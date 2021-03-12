<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Wallet;

class AuthController extends Controller
{
    /**
     * register
     *
     * @param  mixed $request
     * @return void
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'document' => 'required|string|unique:users',
            'type' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        $user = new User;
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->document = $request->input('document');
        $user->type = $request->input('type');
        $plainPassword = $request->input('password');
        $user->password = app('hash')->make($plainPassword);
        if ($user->save()) {
            $wallet = new Wallet;
            $wallet->user_id = $user->id;
            $wallet->amount = 0;
            $wallet->save();

            return response()->json(['user' => $user, 'message' => 'CREATED'], 201);
        }


        try {
        } catch (\Exception $e) {
            return response()->json(['message' => 'User Registration Failed!'], 409);
        }
    }

    /**
     * login
     *
     * @param  mixed $request
     * @return void
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            $credentials = $request->only(['email', 'password']);

            $token = Auth::attempt($credentials);

            if (!$token) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            return $this->respondWithToken($token);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error!'], 500);
        }
    }
}
