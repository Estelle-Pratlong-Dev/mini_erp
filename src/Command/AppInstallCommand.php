<?php

namespace App\Command;

use App\Entity\CategorieContact;
use App\Entity\Module;
use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\Societe;
use App\Entity\User;
use App\Enum\CodeModule;
use App\Repository\ModuleRepository;
use App\Repository\PermissionRepository;
use App\Repository\RoleRepository;
use App\Repository\SocieteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Initialise / met à jour les données de base de l'ERP :
 * modules, permissions, rôle ADMIN, société, et un premier utilisateur admin.
 * Commande idempotente : peut être relancée sans créer de doublons.
 */
#[AsCommand(name: 'app:install', description: 'Initialise les données de base (modules, permissions, admin).')]
class AppInstallCommand extends Command
{
    /** Actions générées pour chaque module. code => libellé */
    private const ACTIONS = [
        'VOIR' => 'Consulter',
        'CREER' => 'Créer',
        'MODIFIER' => 'Modifier',
        'SUPPRIMER' => 'Supprimer',
    ];

    /** Modules activés par défaut à l'installation. */
    private const DEFAUT_ACTIFS = [
        CodeModule::CONTACTS->value,
        CodeModule::CATALOGUE->value,
        CodeModule::PROJETS->value,
        CodeModule::CONTRATS->value,
        CodeModule::FACTURATION->value,
    ];

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ModuleRepository $moduleRepository,
        private readonly PermissionRepository $permissionRepository,
        private readonly RoleRepository $roleRepository,
        private readonly SocieteRepository $societeRepository,
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'Email du compte admin', 'admin@mini-erp.local')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'Mot de passe du compte admin', 'admin');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->seedModules($io);
        $permissions = $this->seedPermissions($io);
        $adminRole = $this->seedAdminRole($permissions, $io);
        $this->seedSociete($io);
        $this->seedCategoriesContact($io);
        $this->seedAdminUser($adminRole, (string) $input->getOption('email'), (string) $input->getOption('password'), $io);

        $this->em->flush();

        $io->success('Installation terminée.');

        return Command::SUCCESS;
    }

    private function seedModules(SymfonyStyle $io): void
    {
        foreach (CodeModule::cases() as $code) {
            $module = $this->moduleRepository->findOneBy(['code' => $code]);
            if (!$module) {
                $module = (new Module())
                    ->setCode($code)
                    ->setNom($code->libelle())
                    ->setActif(in_array($code->value, self::DEFAUT_ACTIFS, true));
                $this->em->persist($module);
            }
        }
        $io->writeln(' - Modules vérifiés.');
    }

    /**
     * @return array<string, Permission>
     */
    private function seedPermissions(SymfonyStyle $io): array
    {
        $permissions = [];
        foreach (CodeModule::cases() as $module) {
            foreach (self::ACTIONS as $action => $libelleAction) {
                $code = $module->value . '_' . $action;
                $permission = $this->permissionRepository->findOneByCode($code);
                if (!$permission) {
                    $permission = (new Permission())
                        ->setCode($code)
                        ->setLibelle($libelleAction . ' — ' . $module->libelle())
                        ->setModule($module->value);
                    $this->em->persist($permission);
                }
                $permissions[$code] = $permission;
            }
        }
        $io->writeln(' - Permissions vérifiées (' . count($permissions) . ').');

        return $permissions;
    }

    /**
     * @param array<string, Permission> $permissions
     */
    private function seedAdminRole(array $permissions, SymfonyStyle $io): Role
    {
        $role = $this->roleRepository->findOneByCode(Role::CODE_ADMIN);
        if (!$role) {
            $role = (new Role())
                ->setCode(Role::CODE_ADMIN)
                ->setLibelle('Administrateur');
            $this->em->persist($role);
        }

        // L'admin reçoit toutes les permissions (re-synchronisé à chaque exécution).
        foreach ($permissions as $permission) {
            $role->addPermission($permission);
        }
        $io->writeln(' - Rôle ADMIN synchronisé.');

        return $role;
    }

    private function seedSociete(SymfonyStyle $io): void
    {
        if (!$this->societeRepository->getSociete()) {
            $societe = (new Societe())
                ->setRaisonSociale('Ma société')
                ->setVille('')
                ->setPays('France');
            $this->em->persist($societe);
            $io->writeln(' - Société par défaut créée.');
        } else {
            $io->writeln(' - Société déjà présente.');
        }
    }

    private function seedCategoriesContact(SymfonyStyle $io): void
    {
        $repo = $this->em->getRepository(CategorieContact::class);
        foreach (['Client', 'Prospect', 'Fournisseur', 'Partenaire'] as $nom) {
            if (!$repo->findOneBy(['nom' => $nom])) {
                $this->em->persist((new CategorieContact())->setNom($nom));
            }
        }
        $io->writeln(' - Catégories de contact vérifiées.');
    }

    private function seedAdminUser(Role $adminRole, string $email, string $password, SymfonyStyle $io): void
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if ($user) {
            $io->writeln(sprintf(' - Utilisateur admin déjà présent (%s).', $email));

            return;
        }

        $user = (new User())
            ->setEmail($email)
            ->setNomComplet('Administrateur')
            ->setActif(true);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->addRolesEntity($adminRole);
        $this->em->persist($user);

        $io->writeln(sprintf(' - Utilisateur admin créé : %s / %s', $email, $password));
    }
}
