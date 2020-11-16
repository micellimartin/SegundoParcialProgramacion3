<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Config\Database;

//Controladores de clases
use App\Controllers\UserController;
use App\Controllers\MateriaController;

//Middlewares
use App\Middlewares\JsonMiddleware;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\ValidarAdminMiddleware;

//Ir a https://laravel.com/docs/8.x/eloquent para ver mas metodos de consulta

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
//Recibe /nombre carpeta proyecto/nombre carpeta que tiene el index
$app->setBasePath('/micelli_segundo_parcial/public');

//Instancio un objeto Config/database llamando a su constructor.
//En este caso nos conectamos a la base de datos : baseparcial
new Database;

$app->get('/users[/]', UserController::class . ':getAll')->add(new JsonMiddleware);

$app->post('/registro[/]', UserController::class . ':registro')->add(new JsonMiddleware);

$app->post('/login[/]', UserController::class . ':login')->add(new JsonMiddleware);

$app->post('/materia[/]', MateriaController::class . ':registroMateria')->add(new ValidarAdminMiddleware())->add(new JsonMiddleware);

$app->get('/materia[/]', MateriaController::class . ':getAll')->add(new AuthMiddleware)->add(new JsonMiddleware);

$app->run();