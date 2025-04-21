<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Jobs\ProcessUpload;
use App\Jobs\RKMProcess;
use Storage;
use App\Models\{
    Outlet,
    Upload,
    User,
    BasePaint,
    Product,
    ColorCard,
    Series,
    MixingBatch,
    Colorant,
    BatchColorant
};

class UploadController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort = !empty($request->sort) ? $request->sort : 'id';
        $sortDir = !empty($request->sortDir) ? $request->sortDir : 'DESC';
        $user = auth()->user();

        $query = Upload::with(['outlet', 'user'])
        ->when($request->q, function ($q, $search) {
            return $q->orWhere('name', 'LIKE', '%' . $search . '%');
        })
        ->when($user->hasAnyRole(['Sales', 'MOS']), function ($q) use($user) {
            return $q->whereIn('outlet_id', $user->outlet()->pluck('id'))
            ->where('user_id', $user->id);
        })
        ->orderBy($sort, $sortDir);

        if($request->limit){
            $data = $query->paginate($request->limit);
        }else{
            $data = $query->get();
        }
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
            'outlet_id' => 'required|exists:outlets,id'
        ]);

        // Cek apakah outlet_id sudah ada di hari ini
        $existingRecord = Upload::where('outlet_id', $request->outlet_id)
            ->whereDate('created_at', now()->toDateString()) // Cek di hari yang sama
            ->exists();

        if ($existingRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Data untuk outlet ini sudah diunggah hari ini.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $user = auth()->user();
            $file = $request->file('file');
            $outlet = Outlet::findOrFail($request->outlet_id);

            // Simpan file
            $path = $file->store('csv_uploads');
            // Simpan data upload
            $upload = Upload::create([
                'user_id' => $user->id,
                'outlet_id' => $outlet->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'status' => 'pending'
            ]);

            // Dispatch job untuk proses import
            ProcessUpload::dispatch($upload, $user);

            DB::commit();
            return response()->json([
                'success' => true,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process file',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function send($id)
    {
        $upload = Upload::where('id', $id)->first();

        
        RKMProcess::dispatch($upload);

        return response()->json([
            'success' => true,
        ], 200);
    }

    
    public function download($id)
    {
        $upload = Upload::where('id', $id)->first();

        if (!$upload) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }

        $path = Storage::path($upload->file_path);

        if (!file_exists($path)) {
            abort(503, 'File not found.');
        }

        return response()->download($path);
    }

    public function delete($id)
    {
        $upload = Upload::where('id', $id)->first();

        if (!$upload) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }

        $path = Storage::path($upload->file_path);

        if (file_exists($path)) {
            unlink($path);
        }

        $upload->delete();

        return response()->json([
            'success' => true,
            'message' => 'File deleted successfully'
        ], 200);
    }
    
}
