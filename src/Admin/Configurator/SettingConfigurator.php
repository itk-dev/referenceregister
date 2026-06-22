<?php

namespace App\Admin\Configurator;

use App\Entity\Setting;
use Doctrine\DBAL\Types\Types;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

final class SettingConfigurator implements FieldConfiguratorInterface
{
    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return Setting::class === $entityDto->getFqcn();
    }

    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        if (Crud::PAGE_INDEX !== $context->getCrud()->getCurrentPage()) {
            return;
        }

        if ('value' === $field->getProperty()) {
            $instance = $entityDto->getInstance();
            if ($instance instanceof Setting) {
                if (Types::BOOLEAN === $instance->getType()) {
                    $field->setFieldFqcn(BooleanField::class);
                    $field->setTemplatePath('@EasyAdmin/crud/field/boolean.html.twig');
                } elseif (Types::INTEGER === $instance->getType()) {
                    $field->setFieldFqcn(IntegerField::class);
                    $field->setTemplatePath('@EasyAdmin/crud/field/integer.html.twig');
                }
            }
        }
    }
}
