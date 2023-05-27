<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\Product;
use Exception;
use Illuminate\Database\Eloquent\Collection;
// use config\Constant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MidtransRepository extends Repository
{
    public function getProduct($request){

        $productIds = array_map(function($request){
            return $request['product_id'];
        }, $request);

        try {
            $data = Product::whereIn('id', $productIds)->get();
            $res = $this->getAmountOrder($request, $data);
            return parent::response(true, "Product list", $res);
        } catch (Exception $th) {
            return parent::response(false, $th->getMessage(), null);
        }
    }

    private function getAmountOrder($request, $product){
        $total = 0;
        $temp = [];
        foreach ($product as $key => $value) {
            $temp[$key]["id"] = $value->id;
            $temp[$key]["name"] = $value->name;
            $temp[$key]["price"] = $value->price;
            $temp[$key]["quantity"] = $value->quantity;
            $temp[$key]["merchant_id"] = $value->merchant_id;
            $temp[$key]["quantity_amount"] = $request[$key]['quantity'];
            $temp[$key]["total"] = $value->price * $request[$key]['quantity'];
            $total += $temp[$key]["total"];
        }
        $res = [
            'detail' => $temp,
            'total_amount' => $total,
            'order_id' => rand()
        ];
        return $res;
    }

    public function saveOrder($data){
        DB::beginTransaction();

        foreach ($data['detail'] as $key => $value) {
            try {
                $order = new Order();
                $order->user_id = 2;
                $order->product_id = $value['id'];
                $order->merchant_id = $value['merchant_id'];
                $order->quantity = $value['quantity_amount'];
                $order->gross_amount = $value['total'];
                $order->price = $value['price'];
                $order->status = 0;

                $order->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                return parent::response(false, $th->getMessage(), null);
            }
        }

        DB::commit();
        return parent::response(true, "order saved", null);
    }

    public function updateOrderById($id, $param){
        DB::beginTransaction();
        try {
            Order::where('id', $id)->update($param);
            DB::commit();
            return parent::response(true, "order updated", null);
        } catch (\Throwable $th) {
            DB::rollBack();
            return parent::response(false, $th->getMessage(), null);
        }
    }
}
