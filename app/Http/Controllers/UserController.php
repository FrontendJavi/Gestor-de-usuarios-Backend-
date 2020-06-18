<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use \Firebase\JWT\JWT;
use App\user;
use App\Illuminate\Support\Facades\DB;
use App\Helpers\Token; 

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function register(Request $request) 
    {  
      $password = $request->password; 
     
      $name = $request->name;

      if (strlen($password) < 8 || !ctype_alpha($name)) 
      {
        return response()->json(['message' => ['Parámetros incorrectos']], 400);
      }

      $user = new user();
      $user->name = $request->name;
      $user->password = md5($password);
      $user->email = $request->email;

      try 
      {
        $user->save();
      } 

      catch (\Throwable $th) 
      {
        return response()->json(['message' => ['El nombre o el correo ya existen']], 400);
      }
       
      $token = new Token(['email' => $user->email]);
      $tokenEncoded = $token->encode();

      return response()->json(['token' => $tokenEncoded], 200);
    }

    public function login(Request $request) 
    {
      $email = ['email'=>$request->email];

      $hashed_password = md5($request->password);
        
      $user = user::where($email)->first();  
       
      if ($user!=null) 
      {       
        if($user->password == $hashed_password)
        {       
          $token = new Token($email);
          $tokenEncoded = $token->encode();
          return response()->json(["token" => $tokenEncoded], 201);
        }   
      }     
        return response()->json(["Error" => "Email o contraseña incorrectos"], 401);
    }

    public function listUsers()
    {
      $user = new user();
      $user = $user->getUsers();
      if(isset($user))
      {
        return response()->json($user);
      }
      else
      {
        return response()->json(["Error" => "No hay usuarios registrados"]);
      }
    }

    public function recoverPassword(Request $request)
    {
      $user = user::where('email', $request->email)->first();
      if ($user == null) 
      {
        return response()->json(['message' => "email no encontrado"], 401);
      } 
      else 
      {
        $new_password =  uniqid();
        $hashed_random_password = md5($new_password);
        user::where('id', $user->id)->update(['password' => $hashed_random_password]);
        return response()->json(['message' => "Aquí tienes tu nueva contraseña: " . $new_password . ""], 200);
      }
    }





    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
      $user = user::where('email', $request->email)->first();
      if (isset($user)) 
      {
        if(isset($request->password))
        {
          $user->password = $request->password;
        } 
           
        if (strlen($request->password) < 8)
        {
          return response()->json(["Error" => "La contraseña debe tener 8 caracteres como mínimo"]);
        }
        else 
        {
          user::where('id', $user->id)->update(['password' => md5($request->password)]);
          return response()->json(["Success" => "Se ha modificado el usuario."]);
        }
      } 
      else
      {
        return response()->json(['message' => "email no encontrado"], 401);
      }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteUser(Request $request)
    {
      $token = $request->header("Authentication");
        
      $email= ['email' => $request->email];
      
      $auth = new Token($email);

      if ($token == $auth->encode()) 
      {
        return response()->json(['message' => 'Acción no permitida'], 401);
      }

      $user = new user();
      $user = $user->getUsers();
      $logged = false; 

      foreach ($user as $userEmail) 
      {
        $auth = new Token(['email' => $userEmail->email]);
        
        if ($token == $auth->encode()) 
        {
          $logged = true; 
        }
      }

      if (!$logged) 
      {
        return response()->json(['message' => 'unauthorized'], 401);
      }
      
      user::where($email)->delete();
      return response()->json(['message' => 'Usuario eliminado correctamente'], 200);
   }
}
