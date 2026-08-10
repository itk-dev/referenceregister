<?php

declare(strict_types=1);

namespace App\Entity;

enum Permission: string
{
    case AddEntry = 'PERMISSION_ADD_ENTRY';
    case LookUpEntry = 'PERMISSION_LOOK_UP_ENTRY';
    case RemoveEntry = 'PERMISSION_REMOVE_ENTRY';
}
