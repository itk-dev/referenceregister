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
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimezoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

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
                $fieldType = $this->getFieldType($instance);
                $templatePath = match ($fieldType) {
                    BooleanField::class => '@EasyAdmin/crud/field/boolean.html.twig',
                    IntegerField::class => '@EasyAdmin/crud/field/integer.html.twig',
                    TimezoneField::class => '@EasyAdmin/crud/field/timezone.html.twig',
                    default => null,
                };
                $field->setFieldFqcn($fieldType);
                if (null !== $templatePath) {
                    $field->setTemplatePath($templatePath);
                }
            }
        }
    }

    public function getFieldType(Setting $setting): string
    {
        [$type, $formType] = [$setting->getType(), $setting->getFormType()];

        return match ($type) {
            Types::BOOLEAN => BooleanField::class,
            Types::INTEGER => IntegerField::class,
            Types::STRING => match ($formType) {
                'url' => UrlField::class,
                'choice' => ChoiceField::class,
                'timezone' => TimezoneField::class,
                default => TextField::class,
            },
            Types::TEXT => match ($formType) {
                'texteditor' => TextEditorField::class,
                'textarea' => TextareaField::class,
                default => throw new \RuntimeException(\sprintf('Unhandled form type: %s', $formType)),
            },
            default => throw new \RuntimeException(\sprintf('Unhandled data type: %s', $type)),
        };
    }
}
