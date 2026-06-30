<?php

namespace App\Controller\Admin;

use App\Entity\ModePaiement;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ModePaiementCrudController extends AbstractCrudController
{
    use SoftDeleteCrudTrait;

    public static function getEntityFqcn(): string
    {
        return ModePaiement::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Mode de paiement')
            ->setEntityLabelInPlural('Modes de paiement')
            ->setDefaultSort(['nom' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nom', 'Nom');
        yield BooleanField::new('actif', 'Actif');
    }
}
