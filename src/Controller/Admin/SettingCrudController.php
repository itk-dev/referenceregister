<?php

namespace App\Controller\Admin;

use App\Entity\Role;
use App\Entity\Setting;
use Doctrine\DBAL\Types\Types;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

use function Symfony\Component\Translation\t;

class SettingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Setting::class;
    }

    #[\Override]
    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityPermission(Role::SettingEditor->value)
            ->setPageTitle(Crud::PAGE_INDEX, t('Settings'))
            ->setPageTitle(Crud::PAGE_EDIT, function () {
                /** @var Setting $setting */
                $setting = $this->getContext()->getEntity()->getInstance();

                return t('Edit setting {name}', ['name' => t($setting->getName())]);
            });
    }

    #[\Override]
    public function configureAssets(Assets $assets): Assets
    {
        return parent::configureAssets($assets)
            ->addAssetMapperEntry('admin');
    }

    #[\Override]
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::DELETE)
            ->disable(Action::NEW);
    }

    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', t('Name'))
//            ->formatValue(fn (string $value) => $this->settings->getTranslatedName($value))
            ->onlyOnIndex();

        yield TextField::new('category', t('Category'))
//            ->formatValue(fn (string $value) => $this->settings->getTranslatedName('category.'.$value))
            ->onlyOnIndex();

        if (Crud::PAGE_INDEX === $pageName) {
            // The actual field type will be overridden in SettingConfigurator for non-string values.
            yield TextField::new('value', t('Value'));
        } elseif (Crud::PAGE_EDIT === $pageName) {
            /** @var Setting $setting */
            $setting = $this->getContext()->getEntity()->getInstance();
            [$type, $formType] = [$setting->getType(), $setting->getFormType()];
            $fieldType = match ($type) {
                Types::TEXT => match ($formType) {
                    'texteditor' => TextEditorField::class,
                    'textarea' => TextareaField::class,
                    default => throw new \RuntimeException(\sprintf('Unhandled form type: %s', $formType)),
                },
                Types::BOOLEAN => BooleanField::class,
                Types::STRING => match ($formType) {
                    'url' => UrlField::class,
                    'choice' => ChoiceField::class,
                    default => TextField::class,
                },
                Types::INTEGER => IntegerField::class,
                default => throw new \RuntimeException(\sprintf('Unhandled data type: %s', $type)),
            };
            $field = $fieldType::new('value', false);
            \assert(\in_array(FieldTrait::class, class_uses($field), true));
            $field
                ->setHelp($setting->getDescription() ?: '');
            if ($field instanceof BooleanField) {
                $field->setRequired(false);
            }
            if ($formTypeOptions = $setting->getFormTypeOptions()) {
                $field->setFormTypeOptions($formTypeOptions);
            }

            yield $field;
        }
    }
}
