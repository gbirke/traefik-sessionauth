<?php

declare(strict_types=1);

namespace Birke\TraefikSessionAuth;

use DI\Container;
use Psr\Container\ContainerInterface;

use function DI\env;
use function DI\factory;

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
            "cfg.cookieName" => env('COOKIE_NAME', 'auth-login'),
            "cfg.users" => env('USERS'),
            "cfg.cookieDomain" => env('COOKIE_DOMAIN', ''),
            "config" => factory(function (ContainerInterface $c) {
                return new Config(
                    $c->get('cfg.cookieDomain'),
                    $c->get('cfg.users'),
                    $c->get('cfg.cookieName'),
                );
            }),

        ]);
        return $builder->build();
    }
}
