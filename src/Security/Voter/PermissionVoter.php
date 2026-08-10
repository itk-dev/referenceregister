<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Permission;
use App\Entity\Role;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, Permission>
 */
final class PermissionVoter extends Voter
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return null !== Permission::tryFrom($attribute);
    }

    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        #[\SensitiveParameter]
        TokenInterface $token,
        ?Vote $vote = null,
    ): bool {
        $permission = Permission::tryFrom($attribute);

        return match ($permission) {
            Permission::AddEntry, Permission::RemoveEntry => $this->security->isGranted(Role::Manager->value),
            Permission::LookUpEntry => $this->security->isGranted(Role::User->value),
            default => false,
        };
    }
}
