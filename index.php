<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'C:/GHG-tool/src/vendor/autoload.php';
include "base.php";
include "GHG-chart.php";
include "GHG-input.php";

//$app = new \Slim\App;
$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);
//page handlers
$app->get('/{page}', function (Request $request, Response $response) {
    $name = $request->getAttribute('page');
    $response->getBody()->write(file_get_contents("$name"));

    return $response;
});
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write(file_get_contents("register.php"));
    return $response;
});

//input
//post

$app->post('/input/', function (Request $request, Response $response) use ($con)  {
    $data = saveDests($con,json_decode($request->getBody(),true));
    $response->write($data);
    return $response;
});

//get
$app->get('/input/last', function (Request $request, Response $response) use ($con)  {
    $data = getScenario($con,getNewestDest($con),getNewestComp($con));
    $response->write($data);
    return $response;
});

$app->get('/input/{dest}/{comp}', function (Request $request, Response $response) use ($con)  {
    $dest = $request->getAttribute('dest');
    $comp = $request->getAttribute('comp');
    $data = getScenario($con,$dest,$comp);
    $response->write($data);
    return $response;
});

//analysis
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
