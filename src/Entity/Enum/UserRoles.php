<?php

namespace App\Entity\Enum;

enum UserRoles: string
{
    case Admin = "ROLE_ADMIN";
    case User = "ROLE_USER";
}
