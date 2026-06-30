<?php

namespace App\Controller\Admin;

use App\Entity\TauxTva;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TauxTvaCrudController extends AbstractCrudController
{
    use SoftDeleteCrudTrait;

    public static function getEntityFqcn(): string
    {
        return TauxTva::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Taux de TVA')
            ->setEntityLabelInPlural('Taux de TVA')
            ->setDefaultSort(['taux' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield NumberField::new('taux', 'Taux (%)')->setNumDecimals(2);
        yield TextField::new('libelle', 'Libellé')->setHelp('Ex. Normal, Réduit, Exonéré');
        yield BooleanField::new('actif', 'Actif');
    }
}
