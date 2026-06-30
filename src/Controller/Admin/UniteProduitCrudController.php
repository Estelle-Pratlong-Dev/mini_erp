<?php

namespace App\Controller\Admin;

use App\Entity\UniteProduit;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UniteProduitCrudController extends AbstractCrudController
{
    use SoftDeleteCrudTrait;

    public static function getEntityFqcn(): string
    {
        return UniteProduit::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Unité')
            ->setEntityLabelInPlural('Unités (produits)')
            ->setDefaultSort(['nom' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nom', 'Nom');
        yield BooleanField::new('actif', 'Active');
    }
}
