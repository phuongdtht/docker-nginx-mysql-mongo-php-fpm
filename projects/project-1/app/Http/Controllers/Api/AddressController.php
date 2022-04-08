<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;


class AddressController extends Controller
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
    public function index()
    {
        $users = Address::with(['user'])->get();
        dd($users->toArray());
        return response()
            ->json([
                'status' => 'success',
                'data' => $users
            ]);
    }

    public function show($id)
    {
        $user = Address::find($id);
        return response()
            ->json([
                'status' => 'success',
                'data' => $user
            ]);
    }

    public function store(Request $request)
    {
        $data = [
            'user_id' => $request->input('user_id') ? $request->input('user_id') : '',
            'address' => $request->input('address') ? $request->input('address') : '',
            'city' => $request->input('city') ? $request->input('city') : '',
            'phone' => $request->input('phone') ? $request->input('phone') : '',
            'is_default' => $request->input('is_default') ? $request->input('is_default') : '',
            'zip-code' => $request->input('zip-code') ? $request->input('zip-code') : '',
        ];
        $user = Address::create($data);
        return response()
            ->json([
                'status' => 'success',
                'data' => $user
            ]);
    }

    public function update($id, Request $request)
    {
        $user = Address::find($id);
        $data = [
            'name' => $request->input('name') ? $request->input('name') : $user->name,
            'email' => $request->input('email') ? $request->input('email') : $user->email,
            'password' => $request->input('password') ? Crypt::encrypt($request->input('password')) : $user->password
        ];
        $user->update($data);
        $user = Address::find($id);
        return response()
            ->json([
                'status' => 'success',
                'data' => $user
            ]);
    }

    public function delete($id)
    {
        $user = Address::find($id);
        $user->delete();
        return response()
            ->json([
                'status' => 'success',
                'data' => $user
            ]);
    }
}
