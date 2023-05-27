<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Validation\MidtransValidation;
use App\Repositories\MidtransRepository;
use App\Services\CreateSnapTokenService;
use Illuminate\Http\Response;

class TransactionController extends Controller
{

    public function __construct()
    {
        $this->validation = new MidtransValidation();
        $this->repository = new MidtransRepository();
    }

    private $validation;
    private $repository;

    function getTransactionToken(Request $request){

        $validation = $this->validation->getTransactionToken($request);
        if($validation != null)  return parent::getRespnse(Response::HTTP_BAD_REQUEST, $validation);

        $products = $this->repository->getProduct($request->product);
        if(!$products['res']) return parent::getRespnse(Response::HTTP_BAD_REQUEST, $products['message'], null);

        $service = new CreateSnapTokenService($products['data']);
        $token = $service->getSnapToken();
        if(!$token['res']) return parent::getRespnse(Response::HTTP_INTERNAL_SERVER_ERROR, $token['message'], null);

        $order = $this->repository->saveOrder($products['data']);
        if(!$order['res']) return parent::getRespnse(Response::HTTP_INTERNAL_SERVER_ERROR, $order['message'], null);

        return parent::getRespnse(Response::HTTP_CREATED, $token['message'], [
            'token' => $token['data'],
            'redirect_url' => "https://app.sandbox.midtrans.com/snap/v2/vtweb/".$token['data']
        ]);
    }

}
