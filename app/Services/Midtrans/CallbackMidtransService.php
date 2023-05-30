<?php
namespace App\Services\Midtrans;

use App\Models\Order;
use App\Services\Midtrans\Midtrans;
use Illuminate\Support\Facades\Log;
use Midtrans\Notification;

class CallbackMidtransService extends Midtrans{
    protected $notification;
    protected $order;
    // protected $params;

    public function __construct()
    {
        parent::__construct();
        // $this->params = $params;
        $this->_handleNotification();
    }

    protected function _handleNotification()
    {
        // $input_source = json_encode($this->params);
        // dd(file_get_contents($input_source), true);
        try {
            $notification = new Notification();
            Log::info($notification);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }

        // dd($notification);
        $orderNumber = $notification->order_id;
        $order = Order::where('number', $orderNumber)->first();

        $this->setNotification($notification);
        $this->setOrder($order);
    }

    public function setNotification($notification)
    {
        $this->notification = $notification;
    }

    public function setOrder($order)
    {
        $this->order = $order;
    }

    public function getNotification()
    {
        return $this->notification;
    }

    public function getOrder()
    {
        return $this->order;
    }

    protected function _createLocalSignatureKey()
    {
        $orderId = $this->order->number;
        $statusCode = $this->notification->status_code;
        $grossAmount = $this->order->gross_amount;
        $serverKey = $this->serverKey;
        $input = $orderId . $statusCode . $grossAmount . $serverKey;
        $signature = openssl_digest($input, 'sha512');

        return $signature;
    }

    public function getSignatureKey(){
        return $this->_createLocalSignatureKey();
    }

    public function isSignatureKeyVerified()
    {
        return ($this->_createLocalSignatureKey() == $this->notification->signature_key);
    }

    public function isSuccess()
    {
        $statusCode = $this->notification->status_code;
        $transactionStatus = $this->notification->transaction_status;
        $fraudStatus = !empty($this->notification->fraud_status) ? ($this->notification->fraud_status == 'accept') : true;

        return ($statusCode == 200 && $fraudStatus && ($transactionStatus == 'capture' || $transactionStatus == 'settlement'));
    }

    public function isExpire()
    {
        return ($this->notification->transaction_status == 'expire');
    }

    public function isCancelled()
    {
        return ($this->notification->transaction_status == 'cancel');
    }
}
