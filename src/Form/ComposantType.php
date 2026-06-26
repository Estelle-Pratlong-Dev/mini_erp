<?php

namespace App\Form;

use App\Entity\Composant;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComposantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('composant', EntityType::class, [
                'class' => Produit::class,
                'label' => 'Article',
                'placeholder' => '— Choisir un article —',
                'choice_label' => fn (Produit $p) => (string) $p,
                'query_builder' => fn ($repo) => $repo->createQueryBuilder('p')
                    ->where('p.actif = true')->orderBy('p.designation', 'ASC'),
            ])
            ->add('quantite', NumberType::class, ['label' => 'Quantité', 'scale' => 3]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Composant::class]);
    }
}
