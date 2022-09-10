<?php

namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Exceptions\DataNotFoundException;

class Services
{

    /**
     * Find User by ID
     *
     * @param int $idUser
     * @param array $with
     *
     * @return User
     */
    public function findUserByIdWith($idUser, $with)
    {
        $user = User::where('id', $idUser)->with($with ?? [])->first();
        if (!$user) {
            throw new DataNotFoundException('User is not found');
        }

        return $user;
    }

    /**
     * Find User by ID
     *
     * @param int $idUser
     *
     * @return User
     */
    public function findUserById($idUser)
    {
        $user = User::where('id', $idUser)->first();
        if (!$user) {
            throw new DataNotFoundException('User is not found');
        }

        return $user;
    }

    /**
     * Find Role by ID
     *
     * @param int $idRole
     *
     * @return Role
     */
    public function findRoleById($idRole)
    {
        $role = Role::where('id', $idRole)->first();
        if (!$role) {
            throw new DataNotFoundException('role is not found');
        }

        return $role;
    }
}
