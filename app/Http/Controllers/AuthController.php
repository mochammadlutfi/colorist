<?php

namespace App\Http\Controllers;

use App\Models\User;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Validator;
use Hash;
use Carbon\Carbon;
use Browser;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | API Authentication Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating admin users for the application and
    | redirecting them to your admin dashboard.
    |
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Login User.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|exists:users,email',
            'password' => 'required|string'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'result' => $validator->errors(),
            ], 422);
        }

        $user = User::with('roles')->where('email', $request->email)->first();

        if ($user != null) {
            if (Hash::check($request->password, $user->password)) {
                // Dapatkan informasi device
                $browser = Browser::parse($request->userAgent());
                $deviceName = $browser->platformName() . ' / ' . $browser->browserName();
                $deviceIp = $request->ip();

                // Cek apakah token dengan device & IP yang sama sudah ada
                $existingToken = $user->tokens()
                    ->where('name', $deviceName)
                    ->where('ip', $deviceIp)
                    ->first();

                if ($existingToken) {
                    // Cek apakah token sudah expired
                    $tokenExpired = $existingToken->created_at->addDay(7) < now(); // Misalnya 1 hari masa berlaku
                    
                    if ($tokenExpired) {
                        // Hapus token lama yang expired
                        $existingToken->delete();

                        // Buat token baru
                        $sanctumToken = $user->createToken(
                            $deviceName,
                            ['*'],
                            $request->remember ? now()->addMonth() : now()->addDay()
                        );

                        $accessToken = $sanctumToken->plainTextToken;
                        $message = "Expired token recreated";
                    } else {
                        // Gunakan token yang sudah ada jika masih valid
                        $accessToken = $existingToken->token;
                        $message = "Existing token reused";
                    }
                } else {
                    // Jika belum ada token, buat yang baru
                    $sanctumToken = $user->createToken(
                        $deviceName,
                        ['*'],
                        $request->remember ? now()->addMonth() : now()->addDay()
                    );

                    $accessToken = $sanctumToken->plainTextToken;
                    $message = "New token created";
                }

                // Data response
                $data = [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'image' => $user->image,
                        'image_url' => $user->image_url,
                        'roles' => $user->roles,
                    ],
                    'access_token' => $accessToken,
                    'token_type' => 'Bearer',
                    'permissions' => $user->getPermissionsViaRoles()->pluck('name'),
                    'message' => $message
                ];
 
                return response()->json([
                    'success' => true,
                    'result' => $data
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'result' => ['password' => 'Password Salah!']
                ], 401);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Akun Tidak Ditemukan!'
            ], 401);
        }
    }



    /**
     * Logout the admin.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        auth('sanctum')->user()->tokens()->delete();
        return [
            'success' => true,
            'message' => 'Akun Berhasil Keluar!',
        ];
    }
}
