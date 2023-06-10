<?php
namespace App\Validation;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AuthValidation extends Controller{

    public function register($request)
    {
        $validator = Validator::make($request->all(), [
            "name" => ["required", "string", "max:100"],
            "email" => ["required", "string", "email", "unique:users", "max:100"],
            "password" => ["required", "string", "max:100"]
        ]);

        $validate = null;
        if ($validator->fails()) {
            $validate = $validator->errors();
        }
        return $validate;
    }

    public function login($request)
    {
        $rules = [
            "email" => ["required", "string", "email"],
            "password" => ["required", "string"],
        ];
        $validator = Validator::make($request->all(), $rules);

        $validate = null;
        if ($validator->fails()) {
            $validate = $validator->errors();
        }
        return $validate;
    }
}
