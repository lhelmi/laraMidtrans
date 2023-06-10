<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthRepository extends Repository
{
    public function register($request){
        try {
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->save();
            return parent::response(true, "Successfully Registered", null);
        } catch (\Throwable $th) {
            return parent::response(false, $th->getMessage(), null);
        }
    }
}
