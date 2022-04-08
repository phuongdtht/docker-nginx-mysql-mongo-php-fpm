<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
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
    public function index(Request $request)
    {
        //dd($request->search);
        // sử dụng với with khi định nghĩa lại relation trong model id. user_id phải là tring
        $users = User::with(['addresses'])->when(!empty($request->search), function ($sub) use ($request) {
            $sub->where(function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        })->paginate(1);
        // không dùng được join mà sử raw sử dụng hàm $lookup của mongodb để join dữ liệu 
        // sử dụng DB 
        // $users = DB::collection('users')->raw((function ($collection) {
        //     return $collection->aggregate([
        //         [
        //             '$lookup' => [
        //                 'from' => 'addresses',
        //                 'localField' => '_id',
        //                 'foreignField' => 'user_id',
        //                 'as' => 'address'
        //             ]
        //         ]
        //     ]);
        // }));
        // sử dụng model để lookup
        // $users = User::when(!empty($request->search), function ($sub) use ($request) {
        //     $sub->where(function ($query) use ($request) {
        //         $query->where('name', 'like', "%{$request->search}%")
        //             ->orWhere('email', 'like', "%{$request->search}%");
        //     });
        // })
        // $users = User::raw((function ($collection) {
        //     return $collection->aggregate([
        //         [
        //             '$lookup' => [
        //                 'from' => 'addresses',
        //                 'localField' => '_id',
        //                 'foreignField' => 'user_id',
        //                 'as' => 'address'
        //             ]
        //         ]
        //     ]);
        // }));
        // ->get()
        //;//

        // $users = User::raw(function ($collection) use ($request) {
        //     return $collection->aggregate(array(
        //         array('$lookup' => array(
        //             'from' => 'addresses',
        //             'localField' => '_id',
        //             'foreignField' => 'user_id',
        //             'as' => 'addresses'
        //         )),
        //         // array('$unwind' => array(
        //         //     'path' => '$user', 'preserveNullAndEmptyArrays' => True
        //         // )),
        //         // array('$match' => array(
        //         //     '$or' => array(
        //         //         array('invoice_number' => array('$regex' => $request->search)),
        //         //         array('payment_type' => array('$regex' => $request->search)),
        //         //         array('txid' => array('$regex' => $request->search)),
        //         //         array('user.usrEmail' => array('$regex' => $request->search))
        //         //     )
        //         // )),
        //         // array('$skip' => $request->start),
        //         // array('$limit' => $request->limit)
        //     ));
        // });
        //dd($user);
        return response()
            ->json([
                'status' => 'success',
                'data' => $users
            ]);
    }

    public function show($id)
    {
        $user = User::find($id);
        $address = $user->addresses;
        return response()
            ->json([
                'status' => 'success',
                'data' => $address
            ]);
    }

    public function store(Request $request)
    {
        $data = [
            'role_id' => $request->input('role_id') ? $request->input('role_id') : '',
            'address_id' => $request->input('address_id') ? $request->input('address_id') : '',
            'name' => $request->input('name') ? $request->input('name') : '',
            'email' => $request->input('email') ? $request->input('email') : '',
            'password' => $request->input('password') ? Crypt::encrypt($request->input('password')) : ''
        ];
        $user = User::create($data);
        return response()
            ->json([
                'status' => 'success',
                'data' => $user
            ]);
    }

    public function update($id, Request $request)
    {
        $user = User::find($id);
        $data = [
            'name' => $request->input('name') ? $request->input('name') : $user->name,
            'email' => $request->input('email') ? $request->input('email') : $user->email,
            'password' => $request->input('password') ? Crypt::encrypt($request->input('password')) : $user->password
        ];
        $user->update($data);
        $user = User::find($id);
        return response()
            ->json([
                'status' => 'success',
                'data' => $user
            ]);
    }

    public function delete($id)
    {
        $user = User::find($id);
        $user->delete();
        return response()
            ->json([
                'status' => 'success',
                'data' => $user
            ]);
    }
}
