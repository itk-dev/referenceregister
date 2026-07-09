<?php

namespace App\Validator;

use ItkDev\CprValidator\CprValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class IdentifierValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        /** @var Identifier $constraint */
        $value = (string) $value;
        $validator = new CprValidator();
        if (!$validator->isCpr($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
