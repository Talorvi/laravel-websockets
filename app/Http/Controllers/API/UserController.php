<?php

namespace App\Http\Controllers\API;

use App\Events\NewUser;
use App\Http\Controllers\Controller;
use App\User;
use App\Validators\RegisterValidator;
use Illuminate\Http\Response;

class UserController extends Controller
{
    private RegisterValidator $registerValidator;
    private array $response;
    private User $user;

    public function register()
    {
        $input = request()->all();
        $this->registerValidator = new RegisterValidator($input);
        $this->response = $this->registerValidator->validate();

        switch ($this->response['success']){
            case false:
                return response()->json($this->response, Response::HTTP_BAD_REQUEST);
        }

        $input['password'] = bcrypt($input['password']);
        $this->user = User::create($input);
        $success['token'] =  $this->user->createToken('WebChat')-> accessToken;
        $success['name'] =  $this->user->name;

        event(new NewUser($this->user->name));

        return response()->json(['success'=> true, 'data' => $success], Response::HTTP_OK);
    }

    public function test(){
        return response()->json('test', 200);
    }
}
