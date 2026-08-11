<?php

declare(strict_types=1);

namespace App\Validator;

use App\EntryManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validator for Identifier.
 *
 * @see Identifier
 */
final class IdentifierValidator extends ConstraintValidator
{
    public function __construct(
        private readonly EntryManager $entryManager,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        /** @var Identifier $constraint */
        $value = (string) $value;

        if (!$this->entryManager->isValidIdentifier($value)) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $value)->addViolation();
        }
    }
}
