<?php

namespace Tnapf\JsonMapper\Tests\Fakes;

use Tnapf\JsonMapper\Type\ObjectArray;

class User
{
    public int $id;
    public string $username;
    public string $password;

    #[ObjectArray(name: 'roles', class: Role::class)]
    public array $roles;

    public Address $address;
}
