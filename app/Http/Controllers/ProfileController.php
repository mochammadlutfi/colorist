<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function index(Request $request): JsonResponse
    {
        $id = auth()->user()->id;

        $data = User::with('roles')->where('id', $id)->first();

        $data->permissions = $data->getPermissionsViaRoles()->pluck('name');
        
        return response()->json($data,200);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $id = auth()->user()->id;
        $rules = [
            'email' => 'unique:users,email,'.$id,
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return response()->json([
                'success' => false,
                'result' => $validator->errors(),
            ], 422);

        }else{
            
            DB::beginTransaction();
            try{
                $data = User::where('id', $id)->first();
                $data->name = $request->name;
                $data->email = $request->email;

                
                // Path lokasi penyimpanan gambar
                $filePath = 'uploads/users/';

                // Jika request image null tapi user memiliki gambar, hapus gambar lama
                if (is_null($request->image) && !empty($data->image)) {
                    $oldImagePath = public_path($data->image);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath); // Hapus file
                    }
                    $data->image = null; // Kosongkan di database
                }

                // Jika ada file gambar baru diunggah
                if ($request->hasFile('image')) {
                    $file = $request->file('image');

                    // Hapus file lama jika ada
                    if (!empty($data->image)) {
                        $oldImagePath = public_path($data->image);
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath); // Hapus file lama
                        }
                    }

                    // Simpan file baru
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path($filePath), $fileName);
                    $data->image = '/' . $filePath . $fileName;
                }
                $data->save();

            }catch(\QueryException $e){
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'result' => $e,
                ], 422);
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'result' => $data
            ], 200);
        }
    }


    public function device(Request $request): JsonResponse
    {
        $user = $request->user();

        $devices = $user->tokens()
            ->select('id', 'name', 'ip', 'last_used_at')
            ->orderBy('last_used_at', 'DESC')
            ->get();

        $currentToken = $user->currentAccessToken();

        foreach ($devices as $device) {
            $device->hash = Crypt::encryptString($device->id);

            if ($currentToken->id === $device->id) {
                $device->is_current = true;
            }

            unset($device->id);
        }
        
        return response()->json($devices);
    }

}
