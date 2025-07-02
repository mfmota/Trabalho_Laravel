<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;

class CreateUserController extends Controller
{
    public function store(StoreUserRequest $request){

        $validatedData = $request->validated();

        return response()->json([
            'message' => 'Validação bem-sucedida! O usuário pode ser criado.',
            'data_received' => $validatedData
        ], 201);
    }
}
