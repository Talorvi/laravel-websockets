<?php

namespace App\Http\Controllers\API;

use App\Events\Message\NewMessage;
use App\Http\Controllers\Controller;
use App\Message;
use App\Room;
use App\Validators\NewMessageValidator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    private NewMessageValidator $newMessageValidator;
    private array $response;

    public function index()
    {
        $user = Auth::user();

        $room = request()->input('room_id');

        if ($room == null) {
            return response()->json(['success' => false, 'errors' => ['room' => 'Room with given ID doesn\'t exist']], Response::HTTP_NOT_FOUND);
        }

        $messages = $room->messages()->paginate(25);

        return response()->json($messages);
    }

    public function createNewMessage()
    {
        $input = request()->all();
        $this->newMessageValidator = new NewMessageValidator($input);
        $this->response = $this->newMessageValidator->validate();

        switch ($this->response['success']){
            case false:
                return response()->json($this->response, Response::HTTP_BAD_REQUEST);
        }

        $user = Auth::user();

        $room = Room::find(request()->input['room_id']);
        if ($room) {
            $room->name = $input['name'];
            $room->save();
        } else {
            return response()->json(['success' => false, 'errors' => ['room' => 'Room with given ID doesn\'t exist']], Response::HTTP_NOT_FOUND);
        }

        $message = new Message();
        $message->content = $input['content'];
        $message->author_id = $user->id;

        $room->messages()->save($message);

        event(new NewMessage($user->id, $message));

        return response()->json(['success'=> true, 'data' => ['message' => $message]], Response::HTTP_OK);
    }

    public function editMessage()
    {
        $input = request()->all();
        $this->newMessageValidator = new NewMessageValidator($input);
        $this->response = $this->newMessageValidator->validate();

        switch ($this->response['success']){
            case false:
                return response()->json($this->response, Response::HTTP_BAD_REQUEST);
        }

        $message = Message::find(request()->input['id']);
        if ($message) {
            $message->content = $input['content'];
            $message->modified = 1;
            $message->save();
        } else {
            return response()->json(['success' => false, 'errors' => ['room' => 'Room with given ID doesn\'t exist']], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['success'=> true, 'data' => ['message' => $message]], Response::HTTP_OK);
    }

    public function deleteMessage()
    {
        $input = request()->all();

        $message = Message::find(request()->input['id']);

        if ($message) {
            $message->delete();
        } else {
            return response()->json(['success' => false, 'errors' => ['message' => 'Message with given ID doesn\'t exist']], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['success'=> true], Response::HTTP_OK);
    }
}
