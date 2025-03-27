<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RKMService;

use App\Models\MixingBatch;

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
        $page = $request->page ?? 1;

        $response = $this->rkmService->sellOut([
            'page' => $page
        ]);

        return response()->json($response);
    }

    public function sendData(Request $request)
    {

        $query = MixingBatch::latest()->limit(1)->get();

        $data = TransaksiResource::collection($query);

        $response = $this->rkmService->sendData($data);

        return response()->json($response);
    }
}
