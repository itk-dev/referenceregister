<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Constraint for IdentifierValidator.
 *
 * @see IdentifierValidator
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class Identifier extends Constraint
{
    public string $message = 'Invalid identifier.';
}
