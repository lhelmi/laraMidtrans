<?php
namespace App\Services\Midtrans;

use App\Models\Order;
use App\Services\Midtrans\Midtrans;
use Midtrans\Notification;

class CallbackMidtransService extends Midtrans{
    protected $notification;
    protected $order;

    public function __construct()
    {
        parent::__construct();
        $this->_handleNotification();
    }

    protected function _handleNotification()
    {
        $notification = new Notification();

        $orderNumber = $notification->order_id;
        $order = Order::where('id', $orderNumber)->first();

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
        $grossAmount = $this->order->total_price;
        $serverKey = $this->serverKey;
        $input = $orderId . $statusCode . $grossAmount . $serverKey;
        $signature = openssl_digest($input, 'sha512');

        return $signature;
    }

    public function isSignatureKeyVerified()
    {
        return $this->_createLocalSignatureKey() == $this->notification->signature_key;
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
