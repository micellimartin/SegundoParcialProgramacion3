<?php

//hacer composer dumpautoload -o 
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\Inscripcion;

use \Firebase\JWT\JWT;

class InscripcionController
{
    
    public function registroInscripcion(Request $request, Response $response, $args)
    {       
        //Valido que la materia exista:
        $validacionMateria = MateriaController::validarMateria($args["id"]);

        //Obtengo el id del alumno

        if($validacionMateria == true)
        {
            //Hago la inscripcion
        }
        else
        {
            $response->getBody()->write(json_encode("No existe una materia de id: $args[id]"));
        }
    
        return $response;
    }  
}

?>