<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Jobs\ProcessUpload;

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

        $query = Upload::with(['outlet', 'user'])
        ->when($request->q, function ($q, $search) {
            return $q->orWhere('name', 'LIKE', '%' . $search . '%');
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

    DB::beginTransaction();
    
    try {
        $user = auth()->user();
        $file = $request->file('file');
        $outlet = Outlet::findOrFail($request->outlet_id);

        // Save the file
        $path = $request->file('file')->store('csv_uploads');
        
        // Create upload record
        $upload = Upload::create([
            'user_id' => $user->id,
            'outlet_id' => $outlet->id,
            'file_name' => $request->file('file')->getClientOriginalName(),
            'file_path' => $path,
            'status' => 'pending'
        ]);
        
        // Dispatch import job
        ProcessUpload::dispatch($upload, $user);

        
        // $handle = fopen($file, "r");
        // if ($handle === false) {
        //     throw new \Exception("Failed to open file");
        // }

        // // Skip header row
        // fgetcsv($handle, 0, ",");
        // // dd(fgetcsv($handle, 0, ","));
        // $batchCount = 0;
        // $colorantCount = 0;
        
        // while ($data = fgetcsv($handle, 0, ",")) {
        //     // Skip empty rows
        //     if (empty(array_filter($data))) {
        //         continue;
        //     }
            
        //     // Process base paint if data exists
        //     $basePaint = null;
        //     if (!empty($data[5])) {
        //         $basePaint = BasePaint::firstOrCreate(
        //             ['base_paint_code' => $data[5]],
        //             ['base_paint_name' => $data[6] ?? null]
        //         );
        //     }
            
        //     // Process product if data exists
        //     $product = null;
        //     if (!empty($data[7])) {
        //         $product = Product::firstOrCreate(
        //             ['product_code' => $data[7]],
        //             ['product_name' => $data[8] ?? null]
        //         );
        //     }
            
        //     // Process color card if data exists
        //     $colorCard = null;
        //     if (!empty($data[9])) {
        //         $colorCard = ColorCard::firstOrCreate(
        //             ['color_card_code' => $data[9]],
        //             ['color_card_name' => $data[10] ?? null]
        //         );
        //     }
            
        //     // Process series if data exists
        //     $series = null;
        //     if (!empty($data[11])) {
        //         $series = Series::firstOrCreate(
        //             ['series_code' => $data[11]],
        //             ['series_name' => $data[12] ?? null]
        //         );
        //     }
            
        //     // Parse dates with null checks
        //     $colorMixingTime = !empty($data[1]) ? Carbon::parse($data[1]) : null;
        //     $formulaCreateDate = !empty($data[26]) ? Carbon::parse($data[26]) : null;
            
        //     // Create batch
        //     $batch = MixingBatch::create([
        //         'machine_code' => $data[0] ?? null,
        //         'color_mixing_time' => $colorMixingTime,
        //         'batch_number' => $data[2] ?? null,
        //         'bucket_quantity' => $data[3] ?? null,
        //         'bucket_no' => $data[4] ?? null,
        //         'base_paint_id' => $basePaint?->id,
        //         'product_id' => $product?->id,
        //         'color_card_id' => $colorCard?->id,
        //         'series_id' => $series?->id,
        //         'filling_volume' => $data[21] ?? null,
        //         'unit_name' => $data[22] ?? null,
        //         'original_type' => $data[23] ?? null,
        //         'paint_type' => $data[24] ?? null,
        //         'formula_discount' => $data[25] ?? 0,
        //         'formula_create_date' => $formulaCreateDate,
        //         'remark' => $data[27] ?? null,
        //         'barcode' => $data[28] ?? null,
        //         'result' => $data[29] ?? null,
        //         'result_message' => $data[30] ?? null,
        //         'outlet_id' => $outlet->id,
        //         'profit' => $data[96] ?? 0,
        //         'uploaded_by' => $user->id
        //     ]);
            
        //     $batchCount++;
            
        //     // Process colorants (up to 7)
        //     for ($i = 1; $i <= 7; $i++) {
        //         for ($i = 1; $i <= 7; $i++) {
        //             // Hitung offset untuk colorant ke-i
        //             $offset = 32 + ($i - 1) * 5;
                
        //             $colorantCode = $data[$offset] ?? null;
        //             if (empty($colorantCode)) {
        //                 continue;
        //             }
                    
        //             $colorant = Colorant::firstOrCreate(
        //                 ['colorant_code' => $colorantCode],
        //                 [
        //                     'colorant_name' => $data[$offset + 1] ?? null,
        //                     'rgb' => $data[$offset + 2] ?? null
        //                 ]
        //             );
                    
        //             // Pastikan nilai numerik untuk used_amount dan final_used_amount
        //             $usedAmount = isset($data[$offset + 3]) && is_numeric($data[$offset + 3]) 
        //                             ? (float) $data[$offset + 3] 
        //                             : 0;
        //             $finalUsedAmount = isset($data[$offset + 4]) && is_numeric($data[$offset + 4]) 
        //                             ? (float) $data[$offset + 4] 
        //                             : 0;
                
        //             BatchColorant::create([
        //                 'batch_id' => $batch->id,
        //                 'colorant_id' => $colorant->id,
        //                 'used_amount' => $usedAmount,
        //                 'final_used_amount' => $finalUsedAmount,
        //                 'sequence_number' => $i
        //             ]);
                    
        //             $colorantCount++;
        //         }
                
        //     }
        // }
        
        // fclose($handle);
        
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

    
}
