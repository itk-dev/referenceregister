<?php

namespace App\Entity;

enum Role: string
{
    case ADMIN = 'ROLE_ADMIN';
    case USER = 'ROLE_USER';
    case SETTINGS_ADMIN = 'ROLE_SETTINGS_ADMIN';
}
