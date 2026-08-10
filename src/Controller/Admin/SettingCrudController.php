<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\Configurator\SettingConfigurator;
use App\Entity\Role;
use App\Entity\Setting;
use App\Settings;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

use function Symfony\Component\Translation\t;

final class SettingCrudController extends AbstractCrudController
{
    public function __construct(
        Settings $settings,
        private readonly SettingConfigurator $settingConfigurator,
    ) {
        parent::__construct($settings);
    }

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

                return t('Edit setting {name}', ['name' => $this->settings->getTranslatedName($setting->getName())]);
            });
    }

    #[\Override]
    public function configureAssets(Assets $assets): Assets
    {
        return parent::configureAssets($assets)->addAssetMapperEntry('admin');
    }

    #[\Override]
    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::DELETE)->disable(Action::NEW);
    }

    /**
     * @see SettingConfigurator::configure()
     */
    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', t('Name'))->formatValue(
            fn (string $value) => $this->settings->getTranslatedName($value),
        )->onlyOnIndex();

        yield TextField::new('category', t('Category'))->formatValue(
            fn (string $value) => $this->settings->getTranslatedName('category.'.$value),
        )->onlyOnIndex();

        // @mago-ignore lint:no-else-clause
        if (Crud::PAGE_INDEX === $pageName) {
            // The actual field type will be overridden in SettingConfigurator for non-string values.
            yield TextField::new('value', t('Value'));
        } elseif (Crud::PAGE_EDIT === $pageName) {
            /** @var Setting $setting */
            $setting = $this->getContext()->getEntity()->getInstance();
            $fieldType = $this->settingConfigurator->getFieldType($setting);
            $field = $fieldType::new('value', false);
            $help = $setting->getDescription();
            if (null !== $help) {
                $field->setHelp($help);
            }
            if ($field instanceof BooleanField) {
                $field->setRequired(false);
            }
            $formTypeOptions = $setting->getFormTypeOptions();
            if (null !== $formTypeOptions) {
                $field->setFormTypeOptions($formTypeOptions);
            }

            yield $field;
        }
    }
}
