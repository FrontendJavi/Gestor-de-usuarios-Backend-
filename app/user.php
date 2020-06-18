<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class user extends Model
{
   protected $table = 'users'; 
   protected $fillable = ['id', 'name', 'email', 'password'];

   public function getUsers()
   {
      $user = self::all();
      return $user;
   }
}
