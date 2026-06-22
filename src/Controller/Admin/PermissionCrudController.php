<?php

namespace App\Controller\Admin;

use App\Entity\Permission;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PermissionCrudController extends AbstractCrudController
{
    use SoftDeleteCrudTrait;

    public static function getEntityFqcn(): string
    {
        return Permission::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Permission')
            ->setEntityLabelInPlural('Permissions')
            ->setDefaultSort(['module' => 'ASC', 'code' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('code', 'Code');
        yield TextField::new('libelle', 'Libellé');
        yield TextField::new('module', 'Module');
        yield TextareaField::new('description', 'Description')->hideOnIndex();
    }
}
