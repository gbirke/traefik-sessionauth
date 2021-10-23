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

    public function __construct($configValues)
    {
        // add dot to make sure we have a cookie for all subdomains
        $this->cookieDomain = empty($configValues['COOKIE_DOMAIN']) ? ''
            : '.' . trim($configValues['COOKIE_DOMAIN'], ".\n\r\t\v\0");
        $this->cookieName = empty($configValues['COOKIE_NAME']) ? 'session-auth' : $configValues['COOKIE_NAME'];
        foreach (preg_split('/\s+/', $configValues['USERS']) as $userline) {
            [$username,$password] = explode(':', $userline, 2);
            $this->users[$username] = $password;
        }
    }
}
