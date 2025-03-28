<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RKMService;

use App\Models\Upload;
use App\Models\MixingBatch;
use App\Events\UploadProcessed;

use App\Http\Resources\TransaksiResource;
class TestController extends Controller
{
    
    protected $rkmService;

    public function __construct(RKMService $rkmService)
    {
        $this->rkmService = $rkmService;
    }

    public function index(Request $request)
    {

        // event(new UploadProcessed([
        //     'user_id' => 1,
        //     'status' => 'completed'
        // ]));

        // $page = $request->page ?? 1;
        $upload = Upload::find(50);
        broadcast(new UploadProcessed($upload));
        // $response = $this->rkmService->login();
        // $response = $this->rkmService->sellOut([
        //     'page' => $page
        // ]);

        // return response()->json($response);
    }

    public function sendData(Request $request)
    {
        $query = MixingBatch::where('type', 'maintenance')->latest()->get();
        // dd($query);
        // dd($query);
        $data = TransaksiResource::collection($query)->resolve();
        // dd($data);

        $response = $this->rkmService->sendData($data);

        return response()->json($response);
    }
}
