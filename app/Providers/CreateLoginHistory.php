<?php

namespace App\Providers;

use App\Providers\LoginHistory;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateLoginHistory
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
    public function handle(LoginHistory $event): void
    {
        $current_timestamp = Carbon::now()->toDateTimeString();
        $auth = Auth::user();
        $params = [
            'name' => $auth->name,
            'email' => $auth->email,
            'action' => $event->action,
            'created_at' => $current_timestamp,
            'updated_at' => $current_timestamp
        ];
        try {
            DB::table('login_history')->insert($params);
        } catch (\Throwable $th) {
            $params['message'] = $th->getMessage();
            Log::channel('loginLog')->info(json_encode($params));
        }

    }
}
