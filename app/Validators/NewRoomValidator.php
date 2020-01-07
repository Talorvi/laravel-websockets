<?php


namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class NewRoomValidator extends Validator
{
    private $data;
    private $rules = [
        'name' => 'required|min:3|max:35'
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
