<?php

namespace App\Http\Middleware;

use Closure;
use App\user;
use App\Helpers\Token;
class checkAuth 
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $header_token = $request->header('Authentication'); 
        if(isset($header_token))
        {
            $token = new Token();
            $data_token = $token->decode($header_token);
            
            $user = user::where('email',$data_token->email)->first();
            if(isset($user))
            {
                $request->request->add(['data_token'=>$data_token]);
                return $next($request);
            }
        }  
    }
}
