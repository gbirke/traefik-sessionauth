<?php

use Birke\TraefikSessionAuth\Bootstrap;
use Birke\TraefikSessionAuth\Users;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . './../');
$dotenv->load();
$dotenv->required('USERS')->notEmpty();

$container = Bootstrap::createContainer($_ENV['APP_ENV'] ?? 'dev');

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->add($container->get('middlewares.session'));

// Check Authentication
$app->get('/auth', function (Request $request, Response $response, $args) {
    if (!empty($_SESSION['username'])) {
        $response->getBody()->write("OK");
        return $response;
    }
    $queryParams = $request->getQueryParams();
    if (!empty($queryParams['redirect'])) {
        $_SESSION['redirectAfterLogin'] = $queryParams['redirect'];
    }
    return $response->withStatus(302)
        ->withHeader("Location", "/login");
});

// Status page
$app->get('/', function (Request $request, Response $response, $args) {
    $username = $_SESSION['username'] ?? '';
    $response->getBody()->write(
        $this->get('template')->renderToString(
            'index.latte',
            ['username' => $username]
        )
    ) ;
    return $response;
});

// Process login form
$app->post('/login', function (Request $request, Response $response, $args) {
    $params = (array)$request->getParsedBody();
    $username = $params['username'] ?? '';
    $password = $params['password'] ?? '';

    /* @var Users */
    $users = $this->get(Users::class);
    if ($users->userExists($username) && $users->verifyPassword($username, $password)) {
        $_SESSION['username'] = $username;
        $redirectTarget = '/';
        if (!empty($_SESSION['redirectAfterLogin'])) {
            $redirectTarget = $_SESSION['redirectAfterLogin'];
            unset($_SESSION['redirectAfterLogin']);
        }
        return $response->withStatus(302)
            ->withHeader("Location", $redirectTarget);
    }

    $response->getBody()->write(
        $this->get('template')->renderToString(
            'login.latte',
            [
                'username' => $username,
                'message' => 'Login failed'
            ],
        )
    );
    return $response->withStatus(401);
});

// Show login form
$app->get('/login', function (Request $request, Response $response, $args) {
    $response->getBody()->write(
        $this->get('template')->renderToString(
            'login.latte',
            ['username' => '', 'message' => ''],
        )
    );
    return $response;
});

$app->get('/logout', function (Request $request, Response $response, $args) {
    unset($_SESSION['username']);
    return $response->withStatus(302)
        ->withHeader("Location", "/");
});


$app->run();
