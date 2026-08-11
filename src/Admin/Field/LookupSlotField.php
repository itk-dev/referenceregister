<?php

declare(strict_types=1);

namespace App\Admin\Field;

use App\Form\LookupSlotType;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Contracts\Translation\TranslatableInterface;

final class LookupSlotField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, TranslatableInterface|string|bool|null $label = null): self
    {
        return new self()
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplatePath('admin/field/lookup_slot.html.twig')
            ->setFormType(LookupSlotType::class)
            ->addCssClass('lookup-slot');
    }
}
