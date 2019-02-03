<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
# Autocargador
require 'vendor/autoload.php';

# Configuraciones
$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$config['db']['host'] = 'localhost';
$config['db']['user'] = 'juandba';
$config['db']['pass'] = 'admin';
$config['db']['dbname'] = 'db_biblioteca';

$app = new \Slim\App(['settings' => $config]);

# Definimos el contenedos con sus apps
$container = $app->getContainer();

$container['logger'] = function ($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler('app.log');
    $logger->pushHandler($file_handler);    
    return $logger;
};

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO('pgsql:host=' . $db['host'] . ';dbname=' . $db['dbname'], $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};


# Rutas
$app->get('/', function (Request $req, Response $res, array $args ) {
    $this->logger->addInfo('Purete');
    return $res->withStatus(200)->write("Bueno");
});

$app->get('/ciudad', function (Request $req, Response $res) {
    $this->logger->addInfo('Probando el referencial de ciudades');

    $con = $this->db;
    $st = $con->query('SELECT * FROM ciudades');
    //print_r($st->fetchAll());
    $res->getBody()->write(var_dump($st->fetchAll()));
});

$app->get('/ciudad/{id}', function (Request $req, Response $res, $args) {
    $this->logger->addInfo('Probando el referencial de ciudades con parametros');

    $ciudad_id = (int)$args['id'];
    $con = $this->db;
    $st = $con->prepare('SELECT * FROM ciudades WHERE id = :id');
    $st->bindParam(':id', $ciudad_id);
    $st->execute();
    //print_r($st->fetchAll());
    $res->getBody()->write(var_export($st->fetch(), true));
    return $res;
});

$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write("Hola, $name");

    return $response;
});


$app->run();



?>