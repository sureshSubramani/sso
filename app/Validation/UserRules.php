<?php

namespace App\Validation;
use App\Models\LoginModel;

class UserRules
{

  public function validateUser(string $str, string $fields, array $data){

    $model = new LoginModel();
    $user = $model->where('username', $data['username'])->first();

    if(!$user)
      return false;

    return password_verify($data['password'], $user['password']);
  }
  
}