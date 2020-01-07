<?php


namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class RegisterValidator extends Validator
{
    private $data;
    private $rules = [
        'name' => 'required|min:3|max:15',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:5|max:255',
        'c_password' => 'required|same:password',
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
