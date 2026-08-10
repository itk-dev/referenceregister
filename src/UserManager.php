<?php

declare(strict_types=1);

namespace App;

use App\Entity\Department;
use App\Entity\Role;
use App\Entity\User;
use App\Exception\LogicException;
use App\Repository\DepartmentRepository;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class UserManager
{
    public function __construct(
        private Security $security,
        private DepartmentRepository $departmentRepository,
    ) {
    }

    /**
     * Get all departments a user has access to.
     *
     * @param User|null $user if not set, the current user will be used
     *
     * @return Department[]
     *
     * @throws LogicException if user has no departments
     */
    public function getUserDepartments(?User $user = null): array
    {
        $user ??= $this->security->getUser();
        if (!$user instanceof User) {
            throw new LogicException(sprintf(
                'User object must be an instance of %s. Found %s.',
                User::class,
                $user::class,
            ));
        }

        // An admin user can use all departments.
        $departments = $this->security->isGranted(Role::Administrator->value)
            ? $this->departmentRepository->findAll()
            : $user->getDepartments()->toArray();

        if (0 === count($departments)) {
            throw new LogicException('Cannot get user departments');
        }

        return $departments;
    }
}
