<?php

//hacer composer dumpautoload -o 
namespace App\Controllers;

use \Firebase\JWT\JWT;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\User;

class UserController
{
    public function getAll(Request $request, Response $response, $args) 
    {
        $rta = User::get();
        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public static function getOne(Request $request, Response $response, $args) 
    {
        $rta = User::find($args["id"]);
    
        if($rta == null)
        {
            $response->getBody()->write(json_encode("No existe un usuario de id: $args[id]"));
        }
        else
        {
            $response->getBody()->write(json_encode($rta));
        }
    
        return $response;
    }

    public function registro(Request $request, Response $response, $args)
    {       
        $parsedBody = $request->getParsedBody();
        
        if(!(empty($parsedBody["email"])) && !(empty($parsedBody["tipo"])) && !(empty($parsedBody["clave"])) && !(empty($parsedBody["nombre"])))
        {
            $validacionMail = UserController::validarMail($parsedBody["email"]);
            $validacionTipo = UserController::validarTipo($parsedBody["tipo"]);
            $validacionNombre = UserController::validarNombre($parsedBody["nombre"]);

            if($validacionMail == true)
            {
                if($validacionTipo == true)
                {
                    if($validacionNombre == true)
                    {
                        $email = $parsedBody["email"];
                        $nombre = $parsedBody["nombre"];
                        $tipo = $parsedBody["tipo"];
                        $clave = $parsedBody["clave"];
                
                        $nuevoUsuario = new User();
                        $nuevoUsuario->email = $email;
                        $nuevoUsuario->nombre = $nombre;
                        $nuevoUsuario->tipo = $tipo;
                        $nuevoUsuario->clave = $clave;
            
                        $response->getBody()->write(json_encode($nuevoUsuario->save()));
                        $response->getBody()->write(json_encode(" Registro exitoso!"));
                    }
                    else
                    {
                        $response->getBody()->write(json_encode("Error: nombre repetido"));
                    }
                }
                else
                {
                    $response->getBody()->write(json_encode("Error: tipo solo puede ser admin, alumno o profesor"));
                }
            }
            else
            {
                $response->getBody()->write(json_encode("Error: email repetido"));
            }
        }
        else
        {
            $response->getBody()->write(json_encode("Para registrarse tiene que enviar email, nombre, tipo y clave por body"));
        }
    
        return $response;
    }

    public function login(Request $request, Response $response, $args)
    {
        $parsedBody = $request->getParsedBody();

        if(!(empty($parsedBody["email"])) || !(empty($parsedBody["nombre"])))
        {
            if(!(empty($parsedBody["clave"])))
            {   
                $usuario = "";

                //Si no mando email logeo por nombre
                if(empty($parsedBody["email"]))
                {
                    $usuario = User::where('nombre', "=" , $parsedBody["nombre"])->where('clave', "=" , $parsedBody["clave"])->get();
                }
                else
                {
                    $usuario = User::where('email', "=" , $parsedBody["email"])->where('clave', "=" , $parsedBody["clave"])->get();

                }

                $usuarioDecodifcado = json_decode($usuario, true);
                //Si no decodifico no puedo verificar si esta vacio el array
                //Si el get devolvio un array vacio es porque el usuario no existe
                if(!empty($usuarioDecodifcado))
                {
                    $email = "";
                    $tipo = "";
                
                    foreach($usuarioDecodifcado as $value)
                    {
                        $email = $value["email"];
                        $tipo = $value["tipo"];
                    }
                
                    $token = UserController::generarTokenJWT($email, $tipo);
                    $response->getBody()->write(json_encode($token));
                }
                else
                {
                    $response->getBody()->write(json_encode("ERROR: no existe un usuario con ese mail o nombre y clave"));
                }
            }
            else
            {
                $response->getBody()->write(json_encode("ERROR: para logearse necesita enviar clave por body"));
            }
        }
        else
        {
            $response->getBody()->write(json_encode("ERROR: para logearse necesita enviar email o nombre por body"));
        }

        return $response;
    }

    public static function validarNombre($nombre)
    {
        //nombre no repetido
        $retorno = true;

        $usuarios = User::get();

        foreach($usuarios as $usuario)
        {
            if($nombre == $usuario->nombre)
            {
                //nombre repetido
                $retorno = false;
                break;
            }
        }

        return $retorno;
    }

    public static function validarMail($email)
    {
        //mail no repetido
        $retorno = true;

        $usuarios = User::get();

        foreach($usuarios as $usuario)
        {
            if($email == $usuario->email)
            {
                //mail repetido
                $retorno = false;
                break;
            }
        }

        return $retorno;
    }

    public static function validarTipo($tipo)
    {
        //tipo invalido
        $retorno = false;

        $tipo = strtolower($tipo);

        if($tipo == "alumno" || $tipo == "profesor" || $tipo == "admin")
        {
            //tipo valido
            $retorno = true;
        }

        return $retorno;
    }

    public static function generarTokenJWT($email, $tipo)
    {
        $key = "segundoparcial";

        $payload = array(
            "email" => $email,
            "tipo" => $tipo
        );

        $token = JWT::encode($payload, $key);

        return $token;
    }

}

?>