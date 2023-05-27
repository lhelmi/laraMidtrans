<?php

namespace App\Repositories;

class Repository
{
    public static function response($res, $message, $data){
        return [
            "res" => $res,
            "message" => $message,
            "data" => $data
        ];
    }
}
