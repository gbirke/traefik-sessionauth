<?php

declare(strict_types=1);

namespace Birke\TraefikSessionAuth;

class Config
{
    /**
     * @readonly
     */
    public string $cookieDomain;

    /**
     * @readonly
     */
    public string $cookieName;

    /**
     * @readonly
     * @var array<string, string>
     */
    public array $users;

    public function __construct(string $cookieDomain, string $users, string $cookieName)
    {
        // add dot to make sure we have a cookie for all subdomains
        $this->cookieDomain = $cookieDomain ? '.' . trim($cookieDomain, ".\n\r\t\v\0") : '';
        $this->cookieName = $cookieName;
        // TODO Cache this instead of parsing it on every request.
        foreach (preg_split('/\s+/', $users) as $userItem) {
            [$username,$password] = explode(':', $userItem, 2);
            $this->users[$username] = $password;
        }
    }
}
