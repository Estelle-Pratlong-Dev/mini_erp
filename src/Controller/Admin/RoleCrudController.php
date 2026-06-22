<?php

namespace App\Controller\Admin;

use App\Entity\Role;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RoleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Role::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Rôle')
            ->setEntityLabelInPlural('Rôles')
            ->setDefaultSort(['code' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('code', 'Code');
        yield TextField::new('libelle', 'Libellé');
        yield AssociationField::new('permissions', 'Permissions')
            ->setFormTypeOption('by_reference', false)
            ->formatValue(fn ($value, $entity) => $entity->getPermissions()->count() . ' permission(s)');
    }
}
