<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'C:/GHG-tool/src/vendor/autoload.php';
include "base.php";
include "GHG-chart.php";
include "GHG-input.php";

$app = new \Slim\App;
//$mysqli = new mysqli("example.com", "user", "password", "database");

$app->get('/{page}', function (Request $request, Response $response) {
    $name = $request->getAttribute('page');
    $response->getBody()->write(file_get_contents("$name"));

    return $response;
});
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write(file_get_contents("dashboard.html"));
    return $response;
});
$app->get('/input/last', function (Request $request, Response $response) use ($con)  {
    $data = getLastScenario($con);
    $response->write($data);
    return $response;
});
$app->get('/analysis/historyList', function (Request $request, Response $response) use ($con)  {
    $uid = 2;
    $data = getHistoryList($uid, $con);
    $response->write($data);
    return $response;
});
$app->get('/analysis/{scenarioName}', function (Request $request, Response $response) use ($con)  {
    $scenarioName = $request->getAttribute('scenarioName');
    $uid = 2;
    $data = getAnalyzedData($uid, $scenarioName, $con);
    $response->write($data);
    return $response;
});

$app->run();
