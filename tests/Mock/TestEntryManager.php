<?php

namespace App\Tests\Mock;

use App\EntryManager;

class TestEntryManager extends EntryManager
{
    public function isValidIdentifier(string $identifier): bool
    {
        return (bool) preg_match('/^[a-z0-9-]+$/', $identifier);
    }

    protected function hashIdentifier(string $identifier): string
    {
        return $identifier;
    }
}
