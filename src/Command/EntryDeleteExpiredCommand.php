<?php

declare(strict_types=1);

namespace App\Command;

use App\EntryManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;

#[AsCommand(
    name: 'app:entry:delete-expired',
    description: 'Delete expired entries',
)]
class EntryDeleteExpiredCommand
{
    public function __invoke(
        EntryManager $manager,
        #[Option(description: 'Show what will be done without doing it.')]
        bool $dryRun = false,
        #[Option]
        string $now = 'now',
    ): int {
        $manager->deleteExpired(new \DateTimeImmutable($now));

        return Command::SUCCESS;
    }
}
