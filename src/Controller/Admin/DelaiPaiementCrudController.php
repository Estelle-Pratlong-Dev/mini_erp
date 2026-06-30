<?php

namespace App\Controller\Admin;

use App\Entity\DelaiPaiement;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DelaiPaiementCrudController extends AbstractCrudController
{
    use SoftDeleteCrudTrait;

    public static function getEntityFqcn(): string
    {
        return DelaiPaiement::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Délai de paiement')
            ->setEntityLabelInPlural('Délais de paiement')
            ->setDefaultSort(['jours' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nom', 'Nom');
        yield IntegerField::new('jours', 'Nombre de jours')
            ->setHelp('Jours ajoutés à la date d\'émission pour l\'échéance (0 = à réception).');
        yield BooleanField::new('actif', 'Actif');
    }
}
