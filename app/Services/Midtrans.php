<?php
namespace App\Services;

use App\Repositories\Repository;
use \Midtrans\Config;

class Midtrans extends Repository{
    protected $serverKey;
    protected $isProduction;
    protected $isSanitized;
    protected $is3ds;
    protected $clientKey;

    public function __construct()
    {
        $this->serverKey = env('MIDTRANS_SERVER_KEY');
        $this->isProduction = config('midtrans.is_production');
        $this->isSanitized = config('midtrans.is_sanitized');
        $this->is3ds = config('midtrans.is_3ds');
        $this->clientKey = env('MIDTRANS_CLIENT_KEY');

        $this->_configureMidtrans();
    }

    public function _configureMidtrans()
    {
        Config::$serverKey = $this->serverKey;
        Config::$isProduction = $this->isProduction;
        Config::$isSanitized = $this->isSanitized;
        Config::$is3ds = $this->is3ds;
        Config::$clientKey = $this->clientKey;
    }

}
