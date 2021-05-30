<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function register(Request $request){

        //Recoger los datos del usuario por post
        $json = $request->input('json', null);

        //Decodificar Json
        $params = json_decode($json); //Objeto
        $params_array = json_decode($json, true); //Array


        if(!empty($params) && !empty($params_array)){
            //Limpiar datos
            $params_array = array_map('trim', $params_array);

            //validar datos
            $validate = \Validator::make($params_array, [
                'name'      => 'required|alpha',
                'surname'   => 'required|alpha',
                'email'     => 'required|email|unique:users', //Comprobar si el usuario existe (duplicado)
                'password'  => 'required',
            ]);
            
            if($validate->fails()){
                $data = array(
                    'status' => 'error',
                    'code'   => 404,
                    'message' => 'El usuario no se ha creado',
                    'errors' => $validate->errors()
                );
                
            }else{
                //Validacion pasada correctamente

                //Cifrar la contraseña
                $pwd = hash('sha256', $params->password);

                //Crear el usuario
                $user = new User();
                $user->name = $params_array['name'];
                $user->surname = $params_array['surname'];
                $user->email = $params_array['email'];
                $user->password = $pwd;
                $user->role = 'ROLE_USER';

                //Guardar el usuario
                $user->save();


                //Crear el usuario
                $data = array(
                    'status' => 'success',
                    'code'   => 200,
                    'message' => 'El usuario  se ha creado',
                    'user' => $user
                );
            }



        }else{
            $data = array(
                'status' => 'error',
                'code'   => 404,
                'message' => 'Datos del usuario no se ha correctos'
            );
        }
        


        return response()->json($data, $data['code']);

    }

    public function login(Request $request){
        $jwtAuth = new \App\Helpers\JwtAuth();

        $email = 'b6@gmail.com';
        $pass  = '123456';
        $pwd   = hash('sha256', $pass);

        


        
        return response()->json($jwtAuth->signup($email, $pwd, 'true'), 200);
    }
}
