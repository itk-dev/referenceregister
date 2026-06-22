<?php

namespace App\Controller\Admin;

use App\Entity\Role;
use App\Entity\Setting;
use Doctrine\DBAL\Types\Types;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Symfony\Contracts\Translation\TranslatorInterface;

use function Symfony\Component\Translation\t;

class SettingCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Setting::class;
    }

    #[\Override]
    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityPermission(Role::SETTINGS_ADMIN->value)
            ->setPageTitle(Crud::PAGE_INDEX, t('Settings'))
            ->setPageTitle(Crud::PAGE_EDIT, function () {
                /** @var Setting $setting */
                $setting = $this->getContext()->getEntity()->getInstance();

                return t('Edit setting {name}', ['name' => t($setting->getName())]);
            });
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
            ->formatValue(fn ($value) => $this->getTranslatedName($value))
            ->onlyOnIndex();

        yield TextField::new('category', t('Category'))
            ->formatValue(fn ($value) => $this->getTranslatedName('category.'.$value))
            ->onlyOnIndex();

        if (Crud::PAGE_INDEX === $pageName) {
            // The actual field type will be overridden in SettingConfigurator for non-string values.
            yield TextField::new('value', t('Value'));
        } elseif (Crud::PAGE_EDIT === $pageName) {
            /** @var Setting $setting */
            $setting = $this->getContext()->getEntity()->getInstance();
            if (null !== $setting) {
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
                        default => TextField::class,
                    },
                    Types::INTEGER => IntegerField::class,
                    default => throw new \RuntimeException(\sprintf('Unhandled data type: %s', $type)),
                };
                \assert(is_a($fieldType, FieldInterface::class, true));
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

    private function getTranslatedName(string $name): string
    {
        $t = match ($name) {
            'site_name' => t('Site name'),
            'enable_log_out' => t('Enable log out'),
            'category.user' => t('User'),
            'category.site' => t('Site'),
            'max_loookups_per_day' => t('Max lookups per day'),
            'users_manual_url' => t("User's manual URL"),
            'frontpage_text' => t('Front page text'),
            default => throw new \RuntimeException(\sprintf('Unhandled setting name: %s', $name)),
        };

        return $t->trans($this->translator);
    }
}
