<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Upload;
use App\Http\Resources\TransaksiResource;
use App\Services\RKMService;
use Illuminate\Support\Facades\Log;
use App\Jobs\RKMProcess;
use App\Models\MixingBatch;

use App\Events\UploadProcessed;
class RKMProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $upload;

    /**
     * Create a new job instance.
     *
     * @param Upload $upload
     */
    public function __construct(Upload $upload)
    {
        $this->upload = $upload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(RKMService $rkmService): void
    {
        try {
            // Ambil data berdasarkan upload_id

            $query = MixingBatch::where('upload_id', $this->upload->id)
            ->where('type', 'sales')->get();

            if ($query->isEmpty()) {
                Log::warning("No data found for upload ID: {$this->upload->id}");
                return;
            }

            // Konversi data ke resource
            $data = TransaksiResource::collection($query)->resolve();
            // Kirim data ke vendor
            $response = $rkmService->sendData($data);
            
            if ($response['response_code'] === '2000') {
                $this->upload->update([
                    'status' => 'completed',
                    'sent_records' => $query->count()
                ]);

            } elseif ($response['response_code'] === '4004') {
                $this->upload->update([
                    'status' => 'failed_sent',
                    'error_message' => $response['response_message']
                ]);
                
                Log::warning("Duplicate data for upload ID: {$this->upload->id}", ['response' => $response]);
            
            } else {

                $this->upload->update([
                    'status' => 'failed_sent',
                ]);
                Log::error("Failed to send data for upload ID: {$this->upload->id}", ['response' => $response]);
            }
            broadcast(new UploadProcessed($this->upload));

        } catch (\Exception $e) {
            $this->upload->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            Log::error("SendDataToVendor job failed: " . $e->getMessage(), [
                'upload_id' => $this->upload->id,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
