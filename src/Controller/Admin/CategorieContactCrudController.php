<?php

namespace App\Controller\Admin;

use App\Entity\CategorieContact;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategorieContactCrudController extends AbstractCrudController
{
    use SoftDeleteCrudTrait;

    public static function getEntityFqcn(): string
    {
        return CategorieContact::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Catégorie de contact')
            ->setEntityLabelInPlural('Catégories de contact')
            ->setDefaultSort(['nom' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nom', 'Nom');
        yield BooleanField::new('actif', 'Active');
    }
}
