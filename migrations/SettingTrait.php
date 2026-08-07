<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Types\Types;

trait SettingTrait
{
    private function addSetting(string $name, string $description, string $category, string $type = Types::STRING, array $config = [], mixed $value = null)
    {
        $this->addSql('INSERT INTO `setting` (`name`, `description`, `category`, `type`, `config`, `value`) VALUES (:name, :description, :category, :type, :config, :value)', [
            'name' => $name,
            'description' => $description,
            'category' => $category,
            'type' => $type,
            'config' => json_encode($config),
            'value' => json_encode($value),
        ]);
    }

    private function removeSetting(string $name)
    {
        $this->addSql('DELETE FROM `setting` WHERE `name` = :name', ['name' => $name]);
    }
}
