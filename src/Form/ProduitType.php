<?php

namespace App\Form;

use App\Entity\Produit;
use App\Enum\TypeProduit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reference', TextType::class, ['label' => 'Référence'])
            ->add('designation', TextType::class, ['label' => 'Désignation'])
            ->add('description', TextareaType::class, ['label' => 'Description', 'required' => false])
            ->add('type', EnumType::class, [
                'class' => TypeProduit::class,
                'label' => 'Type',
                'choice_label' => fn (TypeProduit $t) => $t->libelle(),
            ])
            ->add('prixHt', MoneyType::class, ['label' => 'Prix HT', 'currency' => 'EUR'])
            ->add('tauxTva', NumberType::class, ['label' => 'Taux TVA (%)'])
            ->add('unite', TextType::class, ['label' => 'Unité', 'required' => false])
            ->add('gereStock', CheckboxType::class, ['label' => 'Suivre le stock', 'required' => false])
            ->add('stockActuel', NumberType::class, ['label' => 'Stock actuel', 'scale' => 3])
            ->add('stockMin', NumberType::class, ['label' => 'Seuil d\'alerte', 'required' => false, 'scale' => 3])
            ->add('actif', CheckboxType::class, ['label' => 'Actif', 'required' => false])
            ->add('composants', CollectionType::class, [
                'entry_type' => ComposantType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Produit::class]);
    }
}
