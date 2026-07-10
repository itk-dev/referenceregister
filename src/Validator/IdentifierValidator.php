<?php

namespace App\Validator;

use ItkDev\CprValidator\CprValidator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class IdentifierValidator extends ConstraintValidator
{
    public function __construct(
        #[Autowire(param: 'app_skip_validation')]
        private readonly bool $skipValidation,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if ($this->skipValidation) {
            return;
        }

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
