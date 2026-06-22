<?php

namespace App\Controller\Admin;

use App\Entity\Societe;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SocieteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Societe::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Société')
            ->setEntityLabelInPlural('Société')
            ->setPageTitle(Crud::PAGE_INDEX, 'Informations de la société');
    }

    public function configureActions(Actions $actions): Actions
    {
        // Singleton : pas de suppression. La création initiale est faite à l'installation.
        return $actions
            ->disable(Action::DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addFieldset('Identité');
        yield TextField::new('raisonSociale', 'Raison sociale');
        yield TextField::new('formeJuridique', 'Forme juridique')->hideOnIndex();
        yield TextField::new('siret', 'SIRET')->hideOnIndex();
        yield TextField::new('numTva', 'N° TVA')->hideOnIndex();
        yield TextField::new('capital', 'Capital')->hideOnIndex();
        yield TextField::new('rcs', 'RCS')->hideOnIndex();

        yield FormField::addFieldset('Coordonnées');
        yield TextField::new('adresse', 'Adresse')->hideOnIndex();
        yield TextField::new('codePostal', 'Code postal')->hideOnIndex();
        yield TextField::new('ville', 'Ville');
        yield TextField::new('pays', 'Pays')->hideOnIndex();
        yield TextField::new('telephone', 'Téléphone');
        yield EmailField::new('email', 'Email');
        yield TextField::new('siteWeb', 'Site web')->hideOnIndex();
        yield TextField::new('logo', 'Logo (chemin)')->hideOnIndex();

        yield FormField::addFieldset('Bancaire & facturation');
        yield TextField::new('iban', 'IBAN')->hideOnIndex();
        yield TextField::new('bic', 'BIC')->hideOnIndex();
        yield NumberField::new('tauxTvaDefaut', 'TVA par défaut (%)')->hideOnIndex();
        yield TextField::new('devise', 'Devise')->hideOnIndex();
        yield TextField::new('prefixeFacture', 'Préfixe facture')->hideOnIndex();
        yield IntegerField::new('prochainNumeroFacture', 'Prochain n° facture')->hideOnIndex();
        yield TextareaField::new('conditionsPaiement', 'Conditions de paiement')->hideOnIndex();
        yield TextareaField::new('mentionsLegales', 'Mentions légales')->hideOnIndex();
    }
}
