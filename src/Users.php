<?php

declare(strict_types=1);

namespace Birke\TraefikSessionAuth;

class Users
{
    /**
     * @var array<string, string>
     */
    private array $users;

    public function __construct(string $users)
    {
        $this->users = [];
        // TODO Cache this instead of parsing it on every request.
        foreach (preg_split('/\s+/', $users) as $userItem) {
            [$username,$password] = explode(':', $userItem, 2);
            $this->users[$username] = $password;
        }
    }

    public function userExists(string $username): bool
    {
        return !empty($this->users[$username]);
    }

    public function verifyPassword(string $username, string $password): bool
    {
        return password_verify($password, $this->users[$username] ?? '');
    }
}
