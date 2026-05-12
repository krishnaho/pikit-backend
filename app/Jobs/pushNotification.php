<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\PushNotify;

class pushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user_id, $message, $heading, $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_id, $message, $heading, $type)
    {
        $this->user_id = $user_id;
        $this->message = $message;
        $this->heading = $heading;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $pushNotify = new PushNotify;
        $pushNotify->sendMessage($this->user_id, $this->message, $this->heading, $this->type);
    }
}
