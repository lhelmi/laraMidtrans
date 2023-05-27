<?php
namespace App\Validation;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class MidtransValidation extends Controller{

    public function getTransactionToken($request)
    {

        $validate = null;
        $validate = $this->productValidation($request);
        if($validate != null) return $validate;

        $validator = Validator::make($request->all(),
            [
                "product.*.product_id" => ["required", "numeric", "exists:products,id"],
            ],
            [
                'product.*.product_id.exists' => "The product ID : :input not valid!"
            ]
        );

        if ($validator->fails()) {
            $validate = $validator->errors();
        }
        return $validate;
    }

    public function productValidation($request)
    {
        $validator = Validator::make($request->all(), [
            "product" => ["required", "array"],
        ]);

        $validate = null;
        if ($validator->fails()) {
            $validate = $validator->errors();
        }
        return $validate;
    }

}
