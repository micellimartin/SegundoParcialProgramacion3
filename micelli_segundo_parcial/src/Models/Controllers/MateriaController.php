<?php

//hacer composer dumpautoload -o 
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\Materia;

class MateriaController
{
    public function registroMateria(Request $request, Response $response, $args)
    {       
        $parsedBody = $request->getParsedBody();
        
        if(!(empty($parsedBody["materia"])) && !(empty($parsedBody["cuatrimestre"])) && !(empty($parsedBody["cupos"])))
        {
            $validacionCuatrimestre = MateriaController::validarCuatrimestre($parsedBody["cuatrimestre"]);

            if($validacionCuatrimestre == true)
            {
                $nombre = $parsedBody["materia"];
                $cuatrimestre = $parsedBody["cuatrimestre"];
                $cupos = $parsedBody["cupos"];
        
                $nuevoMateria = new Materia();
                $nuevoMateria->nombre = $nombre;
                $nuevoMateria->cuatrimestre = $cuatrimestre;
                $nuevoMateria->cupos = $cupos;
    
                $response->getBody()->write(json_encode($nuevoMateria->save()));
                $response->getBody()->write(json_encode(" Registro exitoso!"));
            }
            else
            {
                $response->getBody()->write(json_encode("Error: cuatrimestre solo puede ser 1, 2, 3 o 4"));
            }
        }
        else
        {
            $response->getBody()->write(json_encode("Para registrar una materia tiene que enviar materia, cuatrimestre y cupos por body"));
        }
    
        return $response;
    }

    public function getAll(Request $request, Response $response, $args) 
    {
        $rta = Materia::get();
        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public static function getOne(Request $request, Response $response, $args) 
    {
        $rta = Materia::find($args["id"]);
    
        if($rta == null)
        {
            $response->getBody()->write(json_encode("No existe una materia de id: $args[id]"));
        }
        else
        {
            $response->getBody()->write(json_encode($rta));
        }
    
        return $response;
    }

    public static function validarMateria($idMateria)
    {
        //Materia NO existe
        $retorno = false;

        $materias = Materia::get();

        foreach($materias as $materia)
        {
            if($idMateria == $materia->id)
            {
                //Materia existe
                $retorno = true;
                break;
            }
        }

        return $retorno;
    }

    public static function validarCuatrimestre($cuatrimestre)
    {
        //tipo invalido
        $retorno = false;

        if($cuatrimestre == "1" || $cuatrimestre == "2" || $cuatrimestre == "3" || $cuatrimestre == "4")
        {
            //tipo valido
            $retorno = true;
        }

        return $retorno;
    }
}

?>