<?php

namespace App\Tests\Mock;

use App\EntryManager;

class TestEntryManager extends EntryManager
{
    public function isValidIdentifier(string $identifier): bool
    {
        return (bool) preg_match('/^\d+$/', $identifier);
    }

    protected function hashIdentifier(string $identifier): string
    {
        return $identifier;
    }
}
