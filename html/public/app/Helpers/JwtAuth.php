<?php

namespace App\helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\DB; 
use App\Models\User;

class JwtAuth{

    public $key;

    public function __construct(){
        $this->key = '123456_clave';
    }
    
    public function signup( $email, $password, $getToken = null ){

        // buscar si existe el usuario
        $user = User::where([
            'email'     =>  $email
        ])->first();


        
        // comprobar si son correctas
        $signup = false;

        if( is_object($user) && password_verify($password, $user->password) ) $signup = true;
        // generar el token con los datos de usuario
        if( $signup ){
            $token = array(
                'sub'       =>     $user->id,
                'email'     =>     $user->email,
                'name'      =>     $user->name,
                'surname'   =>     $user->surname,
                'iat'       =>     time(),
                'exp'       =>     time() + (7*24*60*60),
            );

            $jwt = JWT::encode($token, $this->key, 'HS256');
            
            $decoded = JWT::decode($jwt, new Key($this->key, 'HS256'));
            
            // devolver los datos decodificados
            if( is_null($getToken) ) $data = $jwt;

            if( $getToken ) $data = $decoded;

        }

        if ( !$signup ){
            $data = array(
                'status'    =>  'error',
                'code'      =>   404,
                'message'   =>  'Login incorrecto'
            );
        }


        return $data;
    }

    public function checkToken($jwt, $getIdentity = false){

        $auth = false;
        try {
            //code...
            $jwt = str_replace('"', '' , $jwt);
            $decoded = JWT::decode($jwt, new Key($this->key, 'HS256'));
        } catch (\UnexpectedValueException $e) {
            //throw $th;
            $auth = false;
        } catch (\DomainException $e) {
            $auth = false;
        }

        if( !empty($decoded) && is_object($decoded) && isset($decoded->sub)) $auth = true;
        if( empty($decoded) || !is_object($decoded) || !isset($decoded->sub) )  $auth = false;

        if($getIdentity) return $decoded;
        
        return $auth;
    }

}