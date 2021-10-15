<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\RegisterService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(RegisterRequest $request)
    {
        if (RegisterService::create($request->all())) {

            return response()->json([
                'status' => 'Вы успешно зарегистрировались!'
            ]);
        }

        return response()->json([
            'status' => '422'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        $user -> name = $request->get('name');
        $user -> login = $request->get('login');
        $user -> country = $request->get('country');
        $user -> comment = $request->get('comment');

        if ($user -> save()) {

            return response()->json([
                'status' => 'Пользователь был изменена!'
            ]);
        }

        return response()->json([
            'status' => '422'
        ]);
    }
}
