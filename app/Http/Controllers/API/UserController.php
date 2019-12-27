<?php

namespace App\Http\Controllers\API;

use App\Events\NewUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUser;
use App\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register(StoreUser $request)
    {
        $validated = $request->validated();

        return response()->json($validated, 200);

//        if ($validator->fails()) {
//            return response()->json(['error'=>$validator->errors()], 401);
//        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('WebChat')-> accessToken;
        $success['name'] =  $user->name;

        event(new NewUser($user->name));

        return response()->json(['success'=>$success], 200);
    }

    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('WebChat')-> accessToken;
            return response()->json(['success' => $success], 200);
        }
        else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }

    public function test(){
        return response()->json('test', 200);
    }
}
