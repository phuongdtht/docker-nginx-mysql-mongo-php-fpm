<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    // /**
    //  * Create a new controller instance.
    //  *
    //  * @return void
    //  */
    // public function __construct()
    // {
    //     //
    // }

    // //
    public function login(Request $request)
    {
        //dd($request->toArray());
        $credentials  = $request->only(['email', 'password']);
        $user = User::where('email', $request->email)->first();
        if (!empty($user)) {
            if (\Auth::attempt($credentials)) {
            }
        } else {
            return response()
                ->json([
                    'status' => 'fail',
                    'data' => '',
                    'message' => 'fail'
                ], 401);
        }
        // $users = User::all();
        // return response()
        //     ->json([
        //         'status' => 'success',
        //         'data' => $users
        //     ]);
    }
}
