<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Vérifie que User::getRoles() expose bien, pour Symfony, les rôles ET les
 * permissions (préfixés ROLE_) issus des rôles-entités.
 */
class UserRolesTest extends TestCase
{
    public function testRolesEtPermissionsSontExposes(): void
    {
        $permission = (new Permission())->setCode('CONTACTS_VOIR')->setLibelle('Voir contacts');
        $role = (new Role())->setCode('COMMERCIAL')->setLibelle('Commercial');
        $role->addPermission($permission);

        $user = (new User())->setEmail('u@test.fr');
        $user->addRolesEntity($role);

        $roles = $user->getRoles();

        self::assertContains('ROLE_USER', $roles);
        self::assertContains('ROLE_COMMERCIAL', $roles);
        self::assertContains('ROLE_CONTACTS_VOIR', $roles);
        self::assertNotContains('ROLE_ADMIN', $roles);
    }

    public function testRoleAdminDonneRoleAdmin(): void
    {
        $admin = (new Role())->setCode(Role::CODE_ADMIN)->setLibelle('Administrateur');
        $user = (new User())->setEmail('admin@test.fr');
        $user->addRolesEntity($admin);

        self::assertContains('ROLE_ADMIN', $user->getRoles());
        self::assertTrue($user->estAdmin());
    }

    public function testUtilisateurSansRoleEstSimplementUser(): void
    {
        $user = (new User())->setEmail('simple@test.fr');

        self::assertSame(['ROLE_USER'], $user->getRoles());
        self::assertFalse($user->estAdmin());
    }
}
