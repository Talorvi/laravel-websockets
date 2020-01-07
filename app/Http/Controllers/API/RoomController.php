<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Room;
use App\Validators\NewRoomValidator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Keygen\Keygen;

class RoomController extends Controller
{
    private NewRoomValidator $newRoomValidator;
    private array $response;

    public function newRoom(Request $request)
    {
        $input = request()->all();
        $this->newRoomValidator = new NewRoomValidator($input);
        $this->response = $this->newRoomValidator->validate();

        switch ($this->response['success']){
            case false:
                return response()->json($this->response, Response::HTTP_BAD_REQUEST);
        }

        $user = Auth::user();

        $room = new Room();
        $room->name = $input['name'];
        $room->code = $this->generateUniqueRoomCode();
        $room->owner_id = $user->id;
        $room->save();

        return response()->json(['success'=> true, 'data' => $room->code], Response::HTTP_OK);
    }

    private function generateUniqueRoomCode()
    {
        $code = Keygen::alphanum(6)->generate();

        while(Room::where('code', '=', $code)->count() > 0){
            $code = Keygen::alphanum(6)->generate();
        }
        return strtoupper($code);
    }
}
