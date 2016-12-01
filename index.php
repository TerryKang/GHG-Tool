<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;


require 'C:/GHG-tool/src/vendor/autoload.php';
include "base.php";
include "GHG-chart.php";
include "GHG-input.php";
include "GHG-base.php";
include "global.php";

//$app = new \Slim\App;
$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);
$container = $app->getContainer();
$container['renderer'] = new PhpRenderer("./");
$app->add(new \RKA\SessionMiddleware(['name' => 'GHG-tool']));

$auth = function ($request, $response, $next) use($app) {
    $session = new \RKA\Session();
    if(!isset($session->userId)){
        return $this->renderer->render($response, "/register.php");
    }else
        $response = $next($request, $response);
    return $response;
};



$app->get('/', function (Request $request, Response $response) {
    $path = $request->getUri()->getPath();
    $session = new \RKA\Session();
    $username = $session->username;
    $userId = $session->userId;
    if(isset($session->userId))
        return $this->renderer->render($response, "/dashboard.php", ['username' => $username , 'path' => $path, 'uid' => $userId]);
    return $this->renderer->render($response, "/register.php");
});

$app->post('/login', function (Request $request, Response $response) use($con) {
    $path = $request->getUri()->getPath();
    $loginemail = $request->getParsedBody()['loginemail'];
    $loginpass = $request->getParsedBody()['loginpass'];
    $userId = 1;
    $firstName = 'abcdefghijklmnopq';
    if(!empty($loginemail) && !empty($loginpass)) {

        $params = array(
                    array(&$loginemail, SQLSRV_PARAM_IN),
                    array(&$loginpass, SQLSRV_PARAM_IN),
                    array(&$userId, SQLSRV_PARAM_INOUT),
                    array(&$firstName, SQLSRV_PARAM_INOUT)
                    );

        $sql = "EXEC dbo.spUserFind @email = ?, @passwd = ?, @userId = ?, @firstName = ?";
        $stmt = sqlsrv_prepare($con, $sql, $params);

        if($stmt && sqlsrv_execute($stmt)) {
            sqlsrv_next_result($stmt);
            if($userId > 1) {
                $session = new \RKA\Session();
                $session->set('userId', $userId);
                $session->set('username', $firstName);
                sqlsrv_free_stmt($stmt);
                return $this->renderer->render($response, "/dashboard.php", ['username' => $firstName , 'path' => $path, 'uid' => $userId]);
            }
        }
        sqlsrv_free_stmt($stmt);
    };
    return $this->renderer->render($response, "/register.php", ['loginError' => 1]);
});

$app->get('/login', function (Request $request, Response $response) {
    $path = $request->getUri()->getPath();
    $session = new \RKA\Session();
    $username = $session->username;
    $userId = $session->userId;
    if(isset($session->userId))
        return $this->renderer->render($response, "/dashboard.php", ['username' => $username , 'path' => $path, 'uid' => $userId]);
    return $this->renderer->render($response, "/register.php");
});

$app->get('/logout', function (Request $request, Response $response) {
    \RKA\Session::destroy();
    return $this->renderer->render($response, "/register.php");
});

$app->post('/register', function (Request $request, Response $response) use($con) {
    $path = $request->getUri()->getPath();
    $email = $request->getParsedBody()['email'];
	$firstName = $request->getParsedBody()['firstName'];
	$lastName = $request->getParsedBody()['lastName'];
	$passwd = $request->getParsedBody()['password'];
	$phone = "6041234567";
	$address = "123";
	$userId = 1;
	$level = 1;
	$isValid = 1;
	$passwd1 = null;
	$joinDate = null;

	if(!empty($email) && !empty($passwd)) {
		$params = 
        array(
            array(&$userId, SQLSRV_PARAM_INOUT),
            array(&$email, SQLSRV_PARAM_IN),
            array(&$passwd, SQLSRV_PARAM_IN),
            array(&$passwd1, SQLSRV_PARAM_IN),
            array(&$firstName, SQLSRV_PARAM_IN),
            array(&$lastName, SQLSRV_PARAM_IN),
            array(&$phone, SQLSRV_PARAM_IN),
            array(&$address, SQLSRV_PARAM_IN),
            array(&$joinDate, SQLSRV_PARAM_IN),
            array(&$level, SQLSRV_PARAM_IN),
            array(&$isValid, SQLSRV_PARAM_IN)
        );

		$sql = "EXEC dbo.spUserUpdate @userId=?, @email=?, @passwd=?, @passwd1=?, @firstName=?, @lastName=?,"
				."@phone=?, @address=?, @joinDate=?, @level=?, @isValid=?";
		$stmt = sqlsrv_prepare($con, $sql, $params);

		if($stmt && sqlsrv_execute($stmt)) {
			sqlsrv_next_result($stmt);
			if($userId > 1) {
                $session = new \RKA\Session();
                $session->set('userId', $userId);
                $session->set('username', $firstName);
                sqlsrv_free_stmt($stmt);
                //return $this->renderer->render($response, "/dashboard.php", ['username' => $firstName , 'path' => $path, 'uid' => $userId]);
			    showMessageRedirect(REGISTER_SUCCESS, 0);
                return;
            }
		}
		sqlsrv_free_stmt($stmt);
	}
    //return $this->renderer->render($response, "/register.php");
    showMessageRedirect(REGISTER_ERROR, 1);
    return;
});

$app->get('/profile', function (Request $request, Response $response) use($con) {
    $path = $request->getUri()->getPath();
    $session = new \RKA\Session();
    if(isset($session->userId) && !empty($session->userId)){
            $userId = $session->userId;
            $email = '';
            $firstName = '';
            $lastName = '';
            $phone = '';
            $address = '';
            
            $params = array(
                        array(&$userId, SQLSRV_PARAM_IN, null, SQLSRV_SQLTYPE_INT),
                        array(&$email, SQLSRV_PARAM_INOUT, null, SQLSRV_SQLTYPE_VARCHAR(30)),
                        array(&$firstName, SQLSRV_PARAM_INOUT, null, SQLSRV_SQLTYPE_VARCHAR(30)),
                        array(&$lastName, SQLSRV_PARAM_INOUT, null, SQLSRV_SQLTYPE_VARCHAR(30)),
                        array(&$phone, SQLSRV_PARAM_INOUT, null, SQLSRV_SQLTYPE_VARCHAR(15)),
                        array(&$address, SQLSRV_PARAM_INOUT, null, SQLSRV_SQLTYPE_VARCHAR(70))
                        );

            $sql = "EXEC dbo.spUserInfo @userId=?, @email=?, @firstName=?, @lastName=?, @phone=?, @address=?";
            $stmt = sqlsrv_prepare($con, $sql, $params);

            if($stmt && sqlsrv_execute($stmt)) {
                sqlsrv_next_result($stmt);
                if($userId > 1) {
                    $session = new \RKA\Session();
                    $session->set('userId', $userId);
                    $session->set('username', $firstName);
                    sqlsrv_free_stmt($stmt);
                    return $this->renderer->render($response, "/profile.php", [
                        'username' => $firstName ,
                        'userId' => $userId,
                        'email' => $email,
                        'firstName' => $firstName,
                        'lastName' => $lastName,
                        'phone' => $phone,
                        'address' => $address,
                        'path' => $path
                        ]);
                }
            }  else {
                die( print_r( sqlsrv_errors(), true));
            }
            sqlsrv_free_stmt($stmt);
    }
    header("location:javascript://history.go(-1)");
    return;
});

$app->get('/editProfile', function (Request $request, Response $response) use($con) {
    $path = $request->getUri()->getPath();
    $session = new \RKA\Session();
    if(isset($session->userId) && !empty($session->userId)){
            $userId = $session->userId;
            $email = '';
            $firstName = '';
            $lastName = '';
            $phone = '';
            $address = '';
            
            $params = array(
                        array(&$userId, SQLSRV_PARAM_IN, null, SQLSRV_SQLTYPE_INT),
                        array(&$email, SQLSRV_PARAM_INOUT, null, SQLSRV_SQLTYPE_VARCHAR(30)),
                        array(&$firstName, SQLSRV_PARAM_INOUT, null, SQLSRV_SQLTYPE_VARCHAR(30)),
                        array(&$lastName, SQLSRV_PARAM_INOUT, null, SQLSRV_SQLTYPE_VARCHAR(30)),
                        array(&$phone, SQLSRV_PARAM_INOUT, null, SQLSRV_SQLTYPE_VARCHAR(15)),
                        array(&$address, SQLSRV_PARAM_INOUT, null, SQLSRV_SQLTYPE_VARCHAR(70))
                        );

            $sql = "EXEC dbo.spUserInfo @userId=?, @email=?, @firstName=?, @lastName=?, @phone=?, @address=?";
            $stmt = sqlsrv_prepare($con, $sql, $params);

            if($stmt && sqlsrv_execute($stmt)) {
                sqlsrv_next_result($stmt);
                if($userId > 1) {
                    $session = new \RKA\Session();
                    $session->set('userId', $userId);
                    $session->set('username', $firstName);
                    sqlsrv_free_stmt($stmt);
                    return $this->renderer->render($response, "/editProfile.php", [
                        'username' => $firstName ,
                        'userId' => $userId,
                        'email' => $email,
                        'firstName' => $firstName,
                        'lastName' => $lastName,
                        'phone' => $phone,
                        'address' => $address,
                        'path' => $path
                        ]);
                }
            }  else {
                die( print_r( sqlsrv_errors(), true));
            }
            sqlsrv_free_stmt($stmt);
    }
    header("location:javascript://history.go(-1)");
    return;
});

$app->post('/editProfile', function (Request $request, Response $response) use($con) {
    $path = $request->getUri()->getPath();
    $session = new \RKA\Session();
    $email = $request->getParsedBody()['email'];
	$firstName = $request->getParsedBody()['firstName'];
	$lastName = $request->getParsedBody()['lastName'];
	$passwd = $request->getParsedBody()['oldPassword'];
    $passwd1 = $request->getParsedBody()['newPassword'];
    $passwd2 = $request->getParsedBody()['confirmPassword'];
	$phone = "6041234567";
	$address = $request->getParsedBody()['address'];
	$userId = $session->userId;
	$level = 1;
	$isValid = 1;
	$joinDate = null;
    if(!empty($passwd1) && $passwd1 != $passwd2){
        showMessageRedirect(EDIT_MSG, 0);
        return;
    }

	if(!empty($email)) {
        
		$params = array(
						array(&$userId, SQLSRV_PARAM_INOUT),
						array(&$email, SQLSRV_PARAM_IN),
						array(&$passwd, SQLSRV_PARAM_IN),
						array(&$passwd1, SQLSRV_PARAM_IN),
						array(&$firstName, SQLSRV_PARAM_IN),
						array(&$lastName, SQLSRV_PARAM_IN),
						array(&$phone, SQLSRV_PARAM_IN),
						array(&$address, SQLSRV_PARAM_IN),
						array(&$joinDate, SQLSRV_PARAM_IN),
						array(&$level, SQLSRV_PARAM_IN),
						array(&$isValid, SQLSRV_PARAM_IN)
						);

		$sql = "EXEC dbo.spUserUpdate @userId=?, @email=?, @passwd=?, @passwd1=?, @firstName=?, @lastName=?,"
				."@phone=?, @address=?, @joinDate=?, @level=?, @isValid=?";
		$stmt = sqlsrv_prepare($con, $sql, $params);
		if($stmt && sqlsrv_execute($stmt)) {
			sqlsrv_next_result($stmt);

			if($userId > 1) {
                $session->set('username', $firstName);
				showMessageRedirect(EDIT_MSG, 0);
				sqlsrv_free_stmt($stmt);
				return;
			}
		}
		else {
			die( print_r( sqlsrv_errors(), true));
		}
		
		sqlsrv_free_stmt($stmt);
	}
	showMessageRedirect(EDIT_MSG1, 2);
    return;
});
$app->get('/findPassword', function (Request $request, Response $response) use($con) {
    return $this->renderer->render($response, "/password.php");
});
$app->post('/findPassword', function (Request $request, Response $response) use($con) {
    $email = $request->getParsedBody()['email'];
	$passwd = substr(uniqid('', true), -5);

	if(!empty($email)) {
		$params = array(
						array(&$email, SQLSRV_PARAM_IN),
						array(&$passwd, SQLSRV_PARAM_IN)
						);

		$sql = "EXEC dbo.spUserFindPassword @email=?, @passwd=?";
		$stmt = sqlsrv_prepare($con, $sql, $params);
		
		if($stmt && sqlsrv_execute($stmt)) {
			sqlsrv_next_result($stmt);
			showMessage($passwd);
		} else {
			showMessageRedirect(RECOVER_MSG2, 0);
		}
		sqlsrv_free_stmt($stmt);
	}
    return;
});

$app->get('/dashboard', function (Request $request, Response $response) {
    $path = $request->getUri()->getPath();
    $session = new \RKA\Session();
    $username = $session->username;
    $userId = $session->userId;
    return $this->renderer->render($response, "/dashboard.php", ['username' => $username , 'path' => $path, 'uid' => $userId]);
})->add($auth);
$app->get('/input', function (Request $request, Response $response) {
    $path = $request->getUri()->getPath();
    $session = new \RKA\Session();
    $username = $session->username;
    $userId = $session->userId;
    return $this->renderer->render($response, "/input.php", ['username' => $username , 'path' => $path, 'uid' => $userId]);
})->add($auth);
$app->get('/analysis', function (Request $request, Response $response) {
    $path = $request->getUri()->getPath();
    $session = new \RKA\Session();
    $username = $session->username;
    $userId = $session->userId;
    return $this->renderer->render($response, "/analysis.php", ['username' => $username , 'path' => $path, 'uid' => $userId]);
})->add($auth);
$app->get('/base', function (Request $request, Response $response) {
    $path = $request->getUri()->getPath();
    $session = new \RKA\Session();
    $username = $session->username;
    $userId = $session->userId;
    return $this->renderer->render($response, "/basedata.php", ['username' => $username , 'path' => $path, 'uid' => $userId]);
})->add($auth);

//page handlers
$app->get('/{page}', function (Request $request, Response $response) {
    $name = $request->getAttribute('page');
    $response->getBody()->write(file_get_contents("$name"));
    return $response;
});


//base
//post
$app->post('/base/', function (Request $request, Response $response) use ($con)  {
    $session = new \RKA\Session();
    $userId = $session->userId;
    $data = saveComp($con,$userId,json_decode($request->getBody(),true));
    $response->write($data);
    return $response;
});

//get
$app->get('/base/last', function (Request $request, Response $response) use ($con)  {
    $session = new \RKA\Session();
    $userId = $session->userId;
    $data = getBase($con,$userId,getNewestComp($con));
    $response->write($data);
    return $response;
});

$app->get('/base/data', function (Request $request, Response $response) use ($con)  {
    $data = getBaseData($con);
    $response->write($data);
    return $response;
})->add($auth);

$app->get('/base/{comp}', function (Request $request, Response $response) use ($con)  {
    $session = new \RKA\Session();
    $userId = $session->userId;
    $comp = $request->getAttribute('comp');
    $data = getBase($con,$userId,$comp);
    $response->write($data);
    return $response;
});

//input
//post

$app->post('/input/', function (Request $request, Response $response) use ($con)  {
    $session = new \RKA\Session();
    $userId = $session->userId;
    $data = saveDests($con,$userId,json_decode($request->getBody(),true));
    $response->write($data);
    return $response;
});

//get
$app->get('/input/last', function (Request $request, Response $response) use ($con)  {
    $session = new \RKA\Session();
    $userId = $session->userId;
    $data = getScenario($con,$userId,getNewestDest($con,$userId),getNewestComp($con,$userId));
    $response->write($data);
    return $response;
});

//get
$app->get('/input/history/{type}', function (Request $request, Response $response) use ($con)  {
    $session = new \RKA\Session();
    $userId = $session->userId;
    $type = $request->getAttribute('type');
    $data = json_encode(array("message"=>"invalid history type", "error_code"=>404));
    if($type == 'source')
        $data = getSourceHistory($con,$userId);
    if($type == 'destination')
        $data = getDestinationHistory($con,$userId);
    $response->write($data);
    return $response;
});

$app->get('/input/{dest}/{comp}', function (Request $request, Response $response) use ($con)  {
    $session = new \RKA\Session();
    $userId = $session->userId;
    $dest = $request->getAttribute('dest');
    $comp = $request->getAttribute('comp');
    $data = getScenario($con,$userId,$dest,$comp);
    $response->write($data);
    return $response;
});

//analysis
$app->get('/analysis/historyList', function (Request $request, Response $response) use ($con)  {
    $session = new \RKA\Session();
    $userId = $session->userId;
    $data = getHistoryList($userId, $con);
    $response->write($data);
    return $response;
});
$app->get('/analysis/{scenarioName}', function (Request $request, Response $response) use ($con)  {
    $session = new \RKA\Session();
    $userId = $session->userId;
    $scenarioName = $request->getAttribute('scenarioName');
    $data = getAnalyzedData($userId, $scenarioName, $con);
    $response->write($data);
    return $response;
});



$app->run();
