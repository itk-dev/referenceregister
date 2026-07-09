<?php

namespace App;

use App\Entity\Department;
use App\Entity\Role;
use App\Entity\User;
use App\Repository\DepartmentRepository;
use Symfony\Bundle\SecurityBundle\Security;

class UserManager
{
    public function __construct(
        private readonly Security $security,
        private readonly DepartmentRepository $departmentRepository,
    ) {
    }

    /**
     * Get all departments a user has access to.
     *
     * @param User|null $user if not set, the current user will be used
     *
     * @return Department[]
     */
    public function getUserDepartments(?User $user = null): array
    {
        $user ??= $this->security->getUser();
        assert($user instanceof User);

        // An admin user can use all departments.
        return $this->security->isGranted(Role::ADMIN->value)
            ? $this->departmentRepository->findAll()
            : $user->getDepartments()->toArray();
    }
}
