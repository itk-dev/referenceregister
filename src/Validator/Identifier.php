<?php

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

    // You can use #[HasNamedArguments] to make some constraint options required.
    // All configurable options must be passed to the constructor.
    public function __construct(
        public string $mode = 'strict',
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct([], $groups, $payload);
    }
}
