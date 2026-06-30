<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\UniteProduit;
use App\Enum\TypeProduit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
            ->add('prixAchatHt', MoneyType::class, [
                'label' => 'Prix d\'achat HT', 'currency' => 'EUR', 'required' => false,
                'attr' => ['class' => 'prix-achat'],
                'help' => 'Pour un produit composé, le coût est calculé à la demande depuis les composants (non saisi ici).',
            ])
            ->add('prixHt', MoneyType::class, ['label' => 'Prix de vente HT', 'currency' => 'EUR'])
            ->add('tauxTva', NumberType::class, ['label' => 'Taux TVA (%)'])
            ->add('unite', EntityType::class, [
                'class' => UniteProduit::class,
                'label' => 'Unité',
                'required' => false,
                'placeholder' => '—',
                'choice_label' => fn (UniteProduit $u) => $u->getNom(),
                'query_builder' => fn ($repo) => $repo->createQueryBuilder('u')
                    ->where('u.actif = true')->orderBy('u.nom', 'ASC'),
            ])
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
