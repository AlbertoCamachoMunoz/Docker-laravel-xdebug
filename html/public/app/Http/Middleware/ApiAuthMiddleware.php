<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $json = $request->header('Authorization', null);
        $jwtAuth = new \JwtAuth();
        $data = $this->create_error('Error en el middleware', 'datos incorrectos', 404);

        try {

            $userCheck = $jwtAuth->checkToken($json, true);

            // add response to $request
            $request->merge(array("userCheck" => $userCheck));

            if($userCheck) return $next($request);
            if(!$userCheck) return response()->json($data, $data['code']);

        } catch (\Throwable $th) {
            // error
            return response()->json($data, $data['code']);
        }
        
        
        
    }

    public function create_error($msg, $error, $code = 404, $key = null, $value = null){
        return array(
            'status'    =>  'error',
            'code'      =>  $code,
            'message'   =>  $msg,
            'errors'    =>  $error,
            "{$key}"    =>  $value
        );
    }

}
