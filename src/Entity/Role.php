<?php

namespace App\Entity;

enum Role: string
{
    case Administrator = 'ROLE_ADMINISTRATOR';
    case DepartmentEditor = 'ROLE_DEPARTMENT_EDITOR';
    case Manager = 'ROLE_MANAGER';
    case SettingEditor = 'ROLE_SETTING_EDITOR';
    case User = 'ROLE_USER';
}
