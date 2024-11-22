<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * Register new user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegisterRequest $request)
    {
        $user = User::create([
            'email'    => $request->email,
            'name'     => $request->email,
            'password' => $request->password
        ]);

        Auth::login($user);

        return response()->json($user);
    }
}
