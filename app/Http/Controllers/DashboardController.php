<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RKMService;

use App\Models\Upload;
use App\Models\User;
use App\Models\Outlet;
use App\Models\MixingBatch;
use App\Events\UploadProcessed;

use App\Http\Resources\TransaksiResource;
class DashboardController extends Controller
{

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $user = auth()->user();

        $UploadCount = Upload::when($user->hasRole('Sales'), function ($q) use($user) {
            return $q->whereIn('outlet_id', $user->outlet()->pluck('id'))
            ->where('user_id', $user->id);
        })
        ->get()->count();

        $failedUpload = Upload::whereIn('status', ['failed', 'failed_sent'])
        ->when($user->hasRole('Sales'), function ($q) use($user) {
            return $q->whereIn('outlet_id', $user->outlet()->pluck('id'))
            ->where('user_id', $user->id);
        })
        ->get()->count();

        $outlet = Outlet::when($user->hasRole('Sales'), function ($q) use($user) {
            return $q->whereIn('id', $user->outlet()->pluck('id'));
        })
        ->get()->count();

        $data = Collect([
            'sales' => User::latest()->get()->count(),
            'uploaded' => $UploadCount,
            'failed_upload' => $failedUpload,
            'outlet' => $outlet
        ]);
        
        return response()->json([
            'success' => true,
            'result' => $data
        ]);
    }

    public function sendData(Request $request)
    {
        $query = MixingBatch::latest()->get();
        // dd($query);
        // dd($query);
        $data = TransaksiResource::collection($query)->resolve();
        // dd($data);

        $response = $this->rkmService->sendData($data);

        return response()->json($response);
    }
}
