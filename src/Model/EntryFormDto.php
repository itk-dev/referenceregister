<?php

namespace App\Model;

use App\Entity\Department;

class EntryFormDto
{
    public string $identifier;
    public ?Department $department = null;
}
