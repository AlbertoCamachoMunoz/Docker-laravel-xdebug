<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserController extends Controller
{
    public function pruebas(Request $request){
        return "Accion de Controller";
    }

    public function register(Request $request){

        // recoger los datos del usuario por post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        // limpiar datos
        $params_array = array_map('trim', $params_array);
        
        // validar los datos
        if(!empty($params) && !empty($params_array) ){
            
            $validate = \Validator::make($params_array,[
                'name'      =>  'required|alpha',
                'surname'   =>  'required|alpha',
                'email'     =>  'required|email|unique:users',
                'password'  =>  'required',
            ]);
    
            if($validate->fails()){
    
                $data = $this->create_error( 'El usuario no se ha creado',  $validate->errors(), 404);
            }
            // validacion correcta
            if(!$validate->fails()){

                // cifrar la contraseña
                $pwd = password_hash($params->password, PASSWORD_BCRYPT, ['cost' => 4]);

                // creamos el usuario
                $user = new User();
                $user->name = $params->name;
                $user->surname = $params->surname;
                $user->role = 'ROLE_USER';
                $user->email = $params->email;
                $user->password = $pwd;

                $userDB = User::all();
                // Guardamos el usuario
                // $user->save();

                $data = $this->create_success('El usuario SI se ha creado', $code = 200, 'user', $userDB);
            }
        }

        if(empty($params) && empty($params_array) ){

            $data = $this->create_error( 'Los datos enviados no son correctos', 'error en los datos', 404);
        }
        
        
        // crear usuario

        return response()->json($data, $data['code']);
    }

    public function login(Request $request){

        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        // limpiar datos
        $params_array = array_map('trim', $params_array);

        // validar los datos
        if(!empty($params) && !empty($params_array) ){
            
            $validate = \Validator::make($params_array,[
                'email'     =>  'required|email',
                'password'  =>  'required',
            ]);
            // error en los datos
            if($validate->fails()) $data = $this->create_error('El usuario no se ha Logeado', $validate->errors(), 404);
            
            // datos válidos 
            if(!$validate->fails()){

                $jwtAuth = new \JwtAuth();
                $data = $jwtAuth->signup($params->email, $params->password, $params->getToken);
            }
        }
        // error en los datos
        if(empty($params) || empty($params_array)) $data = $this->create_error('El usuario no se ha Logeado', 'datos vacíos', 404);
        

        return $data;

    }

    public function update(Request $request){

        $json = $request->header('Authorization', null);
        $jwtAuth = new \JwtAuth();

        $userCheck = $jwtAuth->checkToken($json, true);

        if($userCheck){

            // recoger los datos del request
            $json = $request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true);

            
            if( !empty($params) && !empty($params_array) ){
                // limpiar datos
                $params_array = array_map('trim', $params_array);

                // validar datos y  obtenemos el usuario 
                $validate = \Validator::make($params_array,[
                    'name'      =>  'required|alpha',
                    'surname'   =>  'required|alpha',
                    'email'     =>  'required|email|unique:users,'.$userCheck->sub
                ]);

                // quitar campos que no me interesa actualizar
                unset($params_array['id']);
                unset($params_array['role']);
                unset($params_array['password']);
                unset($params_array['created_at']);
                unset($params_array['remember_token']);

                // Actualizar usuario
                $user_result_update = User::where('id', $userCheck->sub)->update($params_array);
                $user = User::find($userCheck->sub);

                // alternativa que devuelve los datos
                // $user = DB::table('users')->where('id', $userCheck->sub)->get();

                $data = $this->create_success('El token SI es correcto', $code = 200, 'user', $user);
            }

            if( empty($params) || empty($params_array) ) $data = $this->create_success('El token SI es correcto', $code = 200, 'checkToken', $userCheck);

        }  

        if(!$userCheck) $data = $this->create_error('El token no es correcto', 'datos vacíos', 404);

        return $data;
        die();
    }

    public function upload(Request $request){
        $data = $this->create_success('Imagen subida correcto', $code = 200);

        return response($data, $data['code'])->header('Content-Type', 'text/plain');
    }


    public function create_error($msg, $error, $code = 404, $key=false, $value=false){
        return array(
            'status'    =>  'error',
            'code'      =>  $code,
            'message'   =>  $msg,
            'errors'    =>  $error,
            "{$key}"    =>  $value
        );
    }

    public function create_success($msg, $code = 200, $key, $value){
        return array(
            'status'    =>  'success',
            'code'      =>  $code,
            'message'   =>  $msg,
            "{$key}"    =>  $value
        );
    }
}

