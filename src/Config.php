<?php

declare(strict_types=1);

namespace Birke\TraefikSessionAuth;

class Config
{
    /**
     * @readonly
     * @var array<string, string>
     */
    public array $users;

    public function __construct(string $users)
    {
        // TODO Cache this instead of parsing it on every request.
        foreach (preg_split('/\s+/', $users) as $userItem) {
            [$username,$password] = explode(':', $userItem, 2);
            $this->users[$username] = $password;
        }
    }
}
