<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\mailMidtrans;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct($title, $detail, $to)
    {
        $this->title = $title;
        $this->detail = $detail;
        $this->to = $to;
    }

    public string $to;
    public string $title;
    public string $detail;
    public $tries = 3;

    public function backoff(): array
    {
        return [1, 5, 10];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->to)->send(new mailMidtrans($this->title, $this->detail));
        } catch (\Exception $th) {
            $log = [
                'detail' => $this->detail,
                'to' => [
                    $this->to
                ],
                'title' => $this->title,
                'message' => $th->getMessage()
            ];
            Log::channel('mailLog')->info(json_encode($log));
            throw $th->getMessage();
        }
    }
}
