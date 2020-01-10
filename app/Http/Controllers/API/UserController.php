<?php

namespace App\Http\Controllers\API;

use App\Events\User\NewUser;
use App\Http\Controllers\Controller;
use App\Room;
use App\User;
use App\Validators\RegisterValidator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private RegisterValidator $registerValidator;
    private array $response;

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
        $user = User::create($input);
        $success['token'] =  $user->createToken('WebChat')-> accessToken;
        $success['name'] =  $user->name;

        event(new NewUser($user));

        return response()->json(['success'=> true, 'data' => $success], Response::HTTP_OK);
    }

//    public function test()
//    {
//        $user = Auth::user();
//        $room = Room::find(request()->input('room_id'));
//        dd($user->rooms->contains($room));
//    }
}
