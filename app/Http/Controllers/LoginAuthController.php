<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;

class LoginAuthController extends Controller
{
    /**
     * Login function
     * 
     * @return \Illuminate\Http\Response 
     */
    public function login(LoginRequest $request)
    {
        if (Auth::attempt(['email'=> $request->email, 'password'=> $request->password]))
    	{
            $user = Auth::user();
            $token = $user->createToken('Test laravel')->accessToken;
            return response()->json(['success' => true, 'token' => $token]);
        }

        return response()->json(['success' => false, 'message' => 'Enter a valid credentials'], Response::HTTP_NOT_FOUND);
    }

    /**
     * Logout function
     *
     * @return \Illuminate\Http\Response
     */
    public function logout() {
        Auth::logout();
        return response()->noContent();
    }
}
