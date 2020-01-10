<?php

namespace App\Http\Controllers\API;

use App\Events\Room\NewRoom;
use App\Http\Controllers\Controller;
use App\Room;
use App\Validators\NewRoomValidator;
use App\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Keygen\Keygen;

class RoomController extends Controller
{
    private NewRoomValidator $newRoomValidator;
    private array $response;

    public function index()
    {
        $page = request()->input('page');

        $user = Auth::user();

        $rooms = $user->rooms()->paginate(15, ['*'], 'page', $page);

        return response()->json($rooms);
    }

    public function newRoom()
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
        $room->users()->save($user);

        event(new NewRoom($user->id, $room));

        return response()->json(['success'=> true, 'data' => ['code' => $room->code]], Response::HTTP_OK);
    }

    public function updateRoomName()
    {
        $input = request()->all();
        $this->newRoomValidator = new NewRoomValidator($input);
        $this->response = $this->newRoomValidator->validate();

        switch ($this->response['success']){
            case false:
                return response()->json($this->response, Response::HTTP_BAD_REQUEST);
        }

        $room = Room::find($input['id']);
        if ($room) {
            $room->name = $input['name'];
            $room->save();
        } else {
            return response()->json(['success' => false, 'errors' => ['room' => 'Room with given ID doesn\'t exist']], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['success'=> true, 'data' => ['code' => $room->code]], Response::HTTP_OK);
    }

    public function updateRoomOwnership()
    {
        $input = request()->all();

        $user = User::find($input['user_id']);
        $room = Room::find($input['id']);

        if ($user == null) {
            return response()->json(['success' => false, 'errors' => ['room' => 'User with given ID doesn\'t exist']], Response::HTTP_NOT_FOUND);
        }

        if ($room) {
            $room->owner_id = $input['user_id'];
            $room->save();
        } else {
            return response()->json(['success' => false, 'errors' => ['room' => 'Room with given ID doesn\'t exist']], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['success'=> true, 'data' => ['code' => $room->code]], Response::HTTP_OK);
    }

    public function deleteRoom()
    {
        $input = request()->all();

        $room = Room::find($input['id']);

        if ($room) {
            $room->delete();
            $room->messages()->delete();
        } else {
            return response()->json(['success' => false, 'errors' => ['room' => 'Room with given ID doesn\'t exist']], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['success'=> true], Response::HTTP_OK);
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
