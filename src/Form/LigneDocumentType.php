<?php

namespace App\Form;

use App\Entity\LigneDocument;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LigneDocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('produit', EntityType::class, [
                'class' => Produit::class,
                'label' => 'Produit',
                'required' => false,
                'placeholder' => '— Libre —',
                'choice_label' => fn (Produit $p) => (string) $p,
                'attr' => ['class' => 'ligne-produit'],
            ])
            ->add('designation', TextType::class, ['label' => 'Désignation'])
            ->add('quantite', NumberType::class, ['label' => 'Qté', 'scale' => 3, 'attr' => ['class' => 'ligne-qte']])
            ->add('prixUnitaireHt', NumberType::class, ['label' => 'PU HT', 'scale' => 2, 'attr' => ['class' => 'ligne-pu']])
            ->add('tauxTva', NumberType::class, ['label' => 'TVA %', 'scale' => 2, 'attr' => ['class' => 'ligne-tva']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => LigneDocument::class]);
    }
}
