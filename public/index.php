<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// TODO avoid domain hardcoding
//session_set_cookie_params(['domain'=>'.example.com']);
session_start();

$app = AppFactory::create();

// Check Authentication
$app->get('/auth', function (Request $request, Response $response, $args) {
    if (!empty($_SESSION['username'])) {
        $response->getBody()->write("OK");
        return $response;
    }
    return $response->withStatus(302)
        ->withHeader("Location", "/login");
});

// Status page
$app->get('/', function (Request $request, Response $response, $args) {
    $username = $_SESSION['username'] ?? '';
    $response->getBody()->write($username ? "Logged in as $username" : "You are not logged in");
    return $response;
});

// Process login form
$app->post('/login', function (Request $request, Response $response, $args) {
    $params = (array)$request->getParsedBody();
    $username = $params['username'] ?? '';
    $password = $params['password'] ?? '';
    // TODO avoid hardcoding
    if ($username !== 'user' || $password !== 'insecure') {
        $response->getBody()->write("Login failed");
        return $response->withStatus(403);
    }
    $_SESSION['username'] = $username;
    $query = $request->getQueryParams();
    // TODO redirect with query params, to x-forwarded-host or to FQDN
    return $response->withStatus(302)
        ->withHeader("Location", "/");
});

// Show login form
$app->get('/login', function (Request $request, Response $response, $args) {
    $response->getBody()->write(<<<LOGINFORM
		<html>
			<head><title>Login</title></head>
			<body>
				<form action="/login" method="post">
					<div><label for="username">User:</label><input name=username id=username></div>
					<div><label for="password">Password:</label><input name=password id=password type=password></div>
					<div><input type=submit value="Login"></div>
				<form>
			</body>
		</html>
		LOGINFORM
    );
    return $response;
});

$app->get('/logout', function (Request $request, Response $response, $args) {
    unset($_SESSION['username']);
    return $response->withStatus(302)
        ->withHeader("Location", "/");
});


$app->run();
