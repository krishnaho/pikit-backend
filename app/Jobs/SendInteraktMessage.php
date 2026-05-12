<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendInteraktMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     *
     * @param array $data
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $token = 'aG9LcVJuRlBuYmYtUWF2XzVJNVc2X2hZbGZ3Q0ZZdjJWU1pmdGxPZmYtMDo=';

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . $token,
            ])->post('https://api.interakt.ai/v1/public/message/', $this->data);

            if ($response->successful()) {
                Log::info('Interakt message sent successfully', ['response' => $response->json()]);
            } else {
                Log::info('Failed to send Interakt message', ['response' => $response->json()]);
            }
        } catch (\Exception $e) {
            Log::error('Exception occurred while sending Interakt message', ['exception' => $e->getMessage()]);
        }
    }
}
