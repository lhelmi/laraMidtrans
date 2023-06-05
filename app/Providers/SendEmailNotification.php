<?php

namespace App\Providers;

use App\Providers\MidtransReceiver;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\mailMidtrans;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
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
                'message' => $mail
            ];
            Log::channel('mailLog')->info(json_encode($log));
        } catch (\Throwable $th) {
            $log = [
                'detail' => $event->detail,
                'to' => [
                    $event->to
                ],
                'title' => $event->title,
                'message' => $th->getMessage()
            ];
            Log::channel('mailLog')->info(json_encode($log));
        }

    }
}
