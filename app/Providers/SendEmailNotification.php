<?php

namespace App\Providers;

use App\Providers\MidtransReceiver;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\mailMidtrans;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class SendEmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    public $tries = 3;

    public function backoff(): array
    {
        return [1, 5, 10];
    }

    /**
     * Handle the event.
     */
    public function handle(MidtransReceiver $event): void
    {
        try {
            $mail = Mail::to($event->to)->send(new mailMidtrans($event->title, $event->detail));
            $log = [
                'detail' => $event->detail,
                'to' => [
                    $event->to
                ],
                'title' => $event->title,
                'message' => "success"
            ];
            Log::channel('mailLog')->info(json_encode($log));
        } catch (\Exception $th) {
            $log = [
                'detail' => $event->detail,
                'to' => [
                    $event->to
                ],
                'title' => $event->title,
                'message' => $th->getMessage()
            ];
            Log::channel('mailLog')->info(json_encode($log));
            throw $th->getMessage();
        }

    }
}
