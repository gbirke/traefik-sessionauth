<?php

declare(strict_types=1);

namespace Birke\TraefikSessionAuth;

use DI\Container;
use Psr\Container\ContainerInterface;
use Slim\Middleware\Session;

use function DI\env;
use function DI\factory;
use function DI\create;

class Bootstrap
{
    public static function createContainer(string $environment): Container
    {
        $builder = new \DI\ContainerBuilder();
        $builder->useAnnotations(false);
        if ($environment === 'production') {
            $builder->enableCompilation(__DIR__ . '/../var/cache');
            $builder->writeProxiesToFile(true, __DIR__ . '/../var/cache');
        }
        $builder->addDefinitions([
            // Values
            "environment.name" => $environment,

            // Environment variables
            "cfg.cookieName" => env('COOKIE_NAME', 'auth-login'),
            "cfg.users" => env('USERS'),
            "cfg.cookieDomain" => env('COOKIE_DOMAIN', ''),

            // Instances
            Users::class => factory(function (ContainerInterface $c) {
                return new Users(
                    $c->get('cfg.users'),
                );
            }),
            "middlewares.session" => factory(function (ContainerInterface $c): Session {
                $cookieDomain = $c->get('cfg.cookieDomain');
                // add dot to make sure we have a cookie for all subdomains
                $cookieDomain = $cookieDomain ? '.' . trim($cookieDomain, ".\n\r\t\v\0") : '';
                return new Session([
                    'name' => $c->get('cfg.cookieName'),
                    'domain' => $cookieDomain
                ]);
            }),
            "template" => factory(function (ContainerInterface $c): \Latte\Engine {
                $latte = new \Latte\Engine();
                $latte->setLoader(new \Latte\Loaders\FileLoader(__DIR__ . '/../templates/'));
                $latte->setTempDirectory(__DIR__ . '/../var/cache');

                if ($c->get('environment.name') === 'production') {
                    $latte->setAutoRefresh(false);
                }
                return $latte;
            })
        ]);
        return $builder->build();
    }
}
