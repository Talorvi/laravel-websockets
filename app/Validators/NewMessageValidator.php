<?php


namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class NewMessageValidator extends Validator
{
    private $data;
    private $rules = [
        'content' => 'required',
        'room_id' => 'required',
        'author_id' => 'required'
    ];
    private $validator;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function validate()
    {
        $this->validator = Validator::make($this->data, $this->rules);
        if($this->validator->fails()) {
            return ["errors" => $this->validator->errors(), "success" => false];
        }
        else {
            return ["success" => true];
        }
    }
}
