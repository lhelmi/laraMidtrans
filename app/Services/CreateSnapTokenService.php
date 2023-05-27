<?php
namespace App\Services;
use \Midtrans\Snap;
use PhpParser\Node\Stmt\TryCatch;

class CreateSnapTokenService extends Midtrans{

    public $order;

    public function __construct($order)
    {
        parent::__construct();
        $this->order = $order;
    }

    public function getSnapToken()
    {
        $params = [
            'transaction_details' => [
                'order_id' => $this->order['order_id'],
                'gross_amount' => $this->order['total_amount'],
            ],
            'item_details' => $this->order['detail'],
            'customer_details' => [
                'first_name' => 'test',
                'last_name' => 'pratama',
                'email' => 'test@example.ex',
                'phone' => '0912385192583',
            ]
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return parent::response(true, 'Snap Token Service', $snapToken);
        } catch (\Throwable $th) {
            return parent::response(false, $th->getMessage(), null);
        }


    }

}
