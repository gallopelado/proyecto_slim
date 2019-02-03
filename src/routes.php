<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/', function (Request $req, Response $res, array $args ) {
    //$this->logger->addInfo('Purete');
    return $res->withStatus(200)->write("Bueno");
});

$app->get('/ciudad', function (Request $req, Response $res) {
    $this->logger->addInfo('Probando el referencial de ciudades');

    $con = $this->db;
    $st = $con->query('SELECT * FROM ciudades');
    //print_r($st->fetchAll());
    $res->getBody()->write(var_dump($st->fetchAll()));
});

$app->get('/ciudad/agregar', function(Request $req, Response $res, $args) {
    $formulario = "
    <form action='/ciudad/agregar' method='POST'>
        <span for='ciudad'>Ciudad</span>
        <input type='text' name='ciudad' autofocus>
        <br><br>
        <button type='submit'>Enviar</button>
    </form>
    ";
    $res->getBody()->write($formulario);
    return $res;
});

$app->post('/ciudad/agregar', function (Request $req, Response $res, $args) {
    $res->getBody()->write('RECIBIDO');
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