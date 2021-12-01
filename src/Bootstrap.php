<?php

declare(strict_types=1);

namespace Birke\TraefikSessionAuth;

use DI\Container;
use Psr\Container\ContainerInterface;
use Slim\Middleware\Session;

use function DI\env;
use function DI\factory;

class Bootstrap
{
    // 30 minutes
    private const DEFAULT_COOKIE_LIFETIME = 1800;

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
            "cfg.cookieLifetime" => env('SESSION_LIFETIME', self::DEFAULT_COOKIE_LIFETIME),

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
                // Make sure we have a non-zero default for the lifetime
                $cookieLifetime = intval($c->get('cfg.cookieLifetime')) ?: self::DEFAULT_COOKIE_LIFETIME;
                return new Session([
                    'name' => $c->get('cfg.cookieName'),
                    'domain' => $cookieDomain,
                    'autorefresh' => true,
                    'secure' => true,
                    'lifetime' => $cookieLifetime
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
