<?php

namespace Tnapf\JsonMapper\Tests\Fakes;

use Tnapf\JsonMapper\Type\EnumerationType;

class Role
{
    public int $id;
    public string $name;

    #[EnumerationType('permissions', RolePermission::class)]
    public RolePermission $permissions;
}
