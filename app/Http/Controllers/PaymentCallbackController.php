<?php

namespace App\Http\Controllers;

use App\Repositories\MidtransRepository;
use App\Services\Midtrans\CallbackMidtransService;
use Illuminate\Http\Response;
use App\Jobs\SendEmailNotification;
use Illuminate\Support\Facades\Auth;

// use App\Validation\MidtransValidation;

class PaymentCallbackController extends Controller
{
    public function __construct()
    {
        // $this->validation = new MidtransValidation();
        $this->repository = new MidtransRepository();
    }

    // private $validation;
    private $repository;

    public function receive()
    {
        $callback = new CallbackMidtransService();

        if ($callback->isSignatureKeyVerified()) {
            $notification = $callback->getNotification();
            $order = $callback->getOrder();
            $param['status'] = 0;
            if ($callback->isSuccess()) $param['status'] = 1;

            if ($callback->isExpire()) $param['status'] = 2;

            if ($callback->isCancelled()) $param['status'] = 3;

            $save = $this->repository->updateOrderByNumber($order->number, $param);
            if(!$save['res']) return parent::getRespnse(Response::HTTP_INTERNAL_SERVER_ERROR, $save['message'], null);

            dispatch(new SendEmailNotification('Payment Notification', 'your payment has been successful', Auth::user()->email));

            return parent::getRespnse(Response::HTTP_CREATED, "Order's updated has been successful", null);

        } else {
            return parent::getRespnse(Response::HTTP_FORBIDDEN, "Signature key tidak terverifikasi", null);
        }
    }
}
