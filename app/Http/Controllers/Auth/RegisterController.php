<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\RegisterService;
use Illuminate\Foundation\Auth\RegistersUsers;


class RegisterController extends Controller
{
    use RegistersUsers;

    protected function create(RegisterRequest $req)
    {
        if (RegisterService::create($req->all())) {

            return response()->json([
                'status' => 'Вы успешно зарегистрировались!'
            ]);
        }

        return response()->json([
            'status' => '422'
        ]);
    }

    public function getUser() {
        return User::get()->all();
    }

    public function getUserRole() {
        if (count(Role::get()->all()) == 0) {
            Role::create([
                'name' => 'user'
            ]);

            Role::create([
                'name' => 'admin'
            ]);
        } elseif (count(Role::get()->all()) == 1) {
            Role::create([
                'name' => 'admin'
            ]);
        } else {
            return Role::get()->all();
        }

        return Role::get()->all();
    }
}
