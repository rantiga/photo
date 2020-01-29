<?php

use app\httpSenders\ExceptionResponse;

require_once 'vendor/autoload.php';

$httpMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

parse_str(file_get_contents("php://input"), $requestData);

$router = new \app\router\Router();

$routerRegistrar = new \app\router\RouterRegistrar($router);
$routerRegistrar->routerRegistration();

$classData = $router->dispatch($httpMethod, $requestUri);

$method = strtolower($_SERVER['REQUEST_METHOD']);

switch ($httpMethod) {
    case 'GET':
        $classData['values']['requestValues'] = $_GET;
        break;
    case 'POST':
        $classData['values']['requestValues'] = $_POST;
        $classData['values']['files'] = $_FILES;
        break;
    case 'DELETE':
    case 'PUT':
        $classData['values']['requestValues'] = $requestData;
        break;
    default:
        throw new ExceptionResponse('Method not supported', '400');

}

if (!empty($classData['values']['uriValues']['user_id'])) {
    $auth = new \app\models\UserAuthorization();
    $auth->authorization($classData['values']['uriValues']['user_id'], $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
}

$app = new $classData['class']();
$app->$method($classData['values']);

