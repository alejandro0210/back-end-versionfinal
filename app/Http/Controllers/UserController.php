<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;





class UserController extends Controller
{
   
    
    public function register(Request $request)
    {
        $data = $request->json()->all();
        $itExistsUserName=User::where('email',$data['email'])->first();

        if ($itExistsUserName==null) {
            $user = User::create(
                [
                    'name'=>$data['name'],
                    'email'=>$data['email'],
                    'password'=>Hash::make($data['password'])
                    
                ]
            );
            $token = $user->createToken('web')->plainTextToken;
                return response()->json([
                    'data'=>$user,
                    'token'=> $token

                ],200);// tiempo de respuesta, si excede marca un error
        } else {
               return response()->json([
                'data'=>'User already exists!',
                'status'=> false
            ],200);
       }

   }

     public function login(Request $request){

        if(!Auth::attempt($request->only('email','password')))
        {
            
            return response()->json
            ([
                'message'=> 'Correo o contraseña incorrectos',
                'status'=> false
            ],400);
        }
         $user = User::where('email',$request['email'])->firstOrFail();
         $token = $user->createToken('web')->plainTextToken;
    
         return response()->json
         ([
            'data'=> $user,
            'token'=>$token
         ]);
    
       }

   public function logout(Request $request)
   {
    $request->user()->currentAccessToken()->delete();
    return response()->json
    ([
        'status'=> true,
    ]);

   }

    public function showById($id)
    {
        $user = User::find($id);
        
        return response()->json(["data"=>$user]);
    }

public function updateRandomPassword($email)
    {
        //verificamos que el email si le corresponda a un usuario
        $user = User::where('email', $email)->first();

        //Si no encuentra ningun usuario con ese email nos dira que no existe
        if (!$user) 
        {
            return response()->json(['message' => 'El usuario no existe'], 200);
        }
        else
        {
            // Generar una contraseña aleatoria de 6 caracteres
        $newPassword = Str::random(6);
        
        // Actualizar el campo password de la tabla user
        $user->password = Hash::make($newPassword);
        //guarda los cambios en la bd
        $user->save();
        
        // Enviar respuesta un mensaje, la nueva contraseña y el usuario al que se le hizo el cambio
        return response()->json([
            'message' => 'Contraseña actualizada correctamente',
            'new_password' => $newPassword,
            'user' => $user,
            
        ], 200);
        }

        
    }
    
  
}
