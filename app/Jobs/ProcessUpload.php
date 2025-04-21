<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\{
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

use App\Events\UploadProcessed;
use App\Jobs\RKMProcess;

class ProcessUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $upload;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @param Upload $upload
     * @param User $user
     */
    public function __construct(Upload $upload, User $user)
    {
        $this->upload = $upload;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->upload->update(['status' => 'processing']);
        
        $file = Storage::path($this->upload->file_path);
        
        $total = 0;
        $processed = 0;
        $errors = [];
        
        try {
            if (($handle = fopen($file, "r")) !== FALSE) {
                // Skip header row
                fgetcsv($handle, 0, ",");
                
                while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                    $total++;
                    
                    try {
                        $this->processRow($data);
                        $processed++;
                    } catch (\Exception $e) {
                        $errors[] = "Row $total: " . $e->getMessage();
                        \Log::error("Error processing row $total: " . $e->getMessage(), [
                            'data' => $data,
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                }
                
                fclose($handle);
            } else {
                throw new \Exception("Failed to open file: " . $this->upload->file_path);
            }
            
            $status = empty($errors) ? 'uploaded' : 'failed';
            
            $this->upload->update([
                'status' => $status,
                'total_records' => $total,
                'processed_records' => $processed,
                'error_records' => count($errors),
                'error_message' => empty($errors) ? null : implode("\n", array_slice($errors, 0, 100))
            ]);

            broadcast(new UploadProcessed($this->upload));
            
        } catch (\Exception $e) {
            $this->upload->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
            \Log::error("ProcessUpload job failed: " . $e->getMessage(), [
                'upload_id' => $this->upload->id,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; // Re-throw exception untuk penanganan queue
        }
    }

    /**
     * Process a single row of CSV data
     *
     * @param array $row
     * @throws \Exception
     */
    protected function processRow(array $row): void
    {
        // Validasi minimal data
        // if (count($row) < 97) {
        //     throw new \Exception("Invalid row format - insufficient columns");
        // }

        // Find or create base paint
        // if(isset($row[5]) || isset($row[6])){
        //     $basePaint = BasePaint::firstOrCreate(
        //         ['code' => $row[5]],
        //         ['name' => $row[6]]
        //     );
        // }else{
        //     $basePaint = null;
        // }

        $basePaint = BasePaint::where('code', $row[5])
        ->where('name', $row[6])
        ->where('size', $row[21])
        ->where('unit', $row[22])
        ->first();
         
        // Find or create product
        if(isset($row[7]) || isset($row[8])){
            $product = Product::firstOrCreate(
                ['code' => $row[7]],
                ['name' => $row[8]]
            );
        }else{
            $product = null;
        }
        
        // Find or create color card
        if(isset($row[9]) || isset($row[10])){
            $colorCard = ColorCard::firstOrCreate(
                ['code' => $row[9]],
                ['name' => $row[10]]
            );
        }else{
            $colorCard = null;
        }
        
        // Find or create series
        if(isset($row[11]) || isset($row[12])){
            $series = Series::firstOrCreate(
                ['code' => $row[11]],
                ['name' => $row[12]]
            );
        }else{
            $colorCard = null;
        }
        
        // Parse tanggal dengan validasi
        try {
            $colorMixingTime = Carbon::parse($row[1]);
            $formulaCreateDate = $row[26] ? Carbon::parse($row[26]) : null;
        } catch (\Exception $e) {
            throw new \Exception("Invalid date format: " . $e->getMessage());
        }
        
        // Create mixing batch
        $batch = MixingBatch::create([
            'machine_code' => $row[0] ?? null,
            'color_mixing_time' => $colorMixingTime,
            'type' => $basePaint ? 'sales' : 'maintenance',
            'batch_number' => $row[2] ?? null,
            'bucket_quantity' => $row[3] ?? null,
            'bucket_no' => $row[4] ?? null,
            'base_paint_id' => $basePaint?->id,
            'base_price' => $basePaint->price ?? 0,
            'product_id' => $product?->id,
            'color_card_id' => $colorCard?->id,
            'series_id' => $series?->id,
            'filling_volume' => !empty($row[21]) ? $row[21] : null,
            'unit_name' => !empty($row[22]) ? $row[22] : null,
            'original_type' => !empty($row[23]) ? $row[23] : null,
            'paint_type' => !empty($row[24]) ? $row[24] : null,
            'formula_discount' => !empty($row[25]) ? $row[25] : null,
            'formula_create_date' => $formulaCreateDate,
            'remark' => !empty($row[27]) ? $row[27] : null,
            'barcode' => !empty($row[28]) ? $row[28] : null,
            'result' => !empty($row[29]) ? $row[29] : null,
            'result_message' => !empty($row[30]) ? $row[30] : null,
            'outlet_id' => $this->upload->outlet_id,
            'profit' =>!empty($row[96]) ? $row[96] : null,
            'uploaded_by' => $this->user->id,
            'upload_id' => $this->upload->id
        ]);
        
        // Process colorants (up to 7 colorants in the CSV)
        $colorant_total = 0;
        for ($i = 1; $i <= 7; $i++) {
            // Hitung offset untuk colorant ke-i
            $offset = 33 + ($i - 1) * 5;
            // dd($offset);
            $colorantCode = $row[$offset] ?? null;
            if (empty($colorantCode)) {
                continue;
            }
            
            $colorant = Colorant::firstOrCreate(
                ['code' => $colorantCode],
                [
                    'name' => $row[$offset + 1] ?? null,
                    'rgb' => $row[$offset + 2] ?? null
                ]
            );

            
            // Pastikan nilai numerik untuk used_amount dan final_used_amount
            $usedAmount = isset($row[$offset + 3]) && is_numeric($row[$offset + 3]) 
                            ? (float) $row[$offset + 3] 
                            : 0;
            $finalUsedAmount = isset($row[$offset + 4]) && is_numeric($row[$offset + 4]) 
                            ? (float) $row[$offset + 4] 
                            : 0;
        
            $priceMili = $colorant->price /1000;
            $priceFix = $finalUsedAmount * $priceMili; 
            BatchColorant::create([
                'batch_id' => $batch->id,
                'colorant_id' => $colorant->id,
                'used_amount' => $usedAmount,
                'final_used_amount' => $finalUsedAmount,
                'price' => $priceFix,
                'sequence_number' => $i
            ]);
            $colorant_total += $priceFix;
        }

        $batch->price = $batch->base_price + $colorant_total;
        $batch->save();
    }
}