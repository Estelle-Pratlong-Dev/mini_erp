<?php

namespace App\Form;

use App\Entity\Contrat;
use App\Entity\Projet;
use App\Enum\StatutContrat;
use App\Enum\TypeContrat;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContratType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('projet', EntityType::class, [
                'class' => Projet::class,
                'label' => 'Projet',
                'choice_label' => fn (Projet $p) => (string) $p,
            ])
            ->add('type', EnumType::class, [
                'class' => TypeContrat::class,
                'label' => 'Type',
                'choice_label' => fn (TypeContrat $t) => $t->libelle(),
            ])
            ->add('reference', TextType::class, ['label' => 'Référence', 'required' => false])
            ->add('statut', EnumType::class, [
                'class' => StatutContrat::class,
                'label' => 'Statut',
                'choice_label' => fn (StatutContrat $s) => $s->libelle(),
            ])
            ->add('dateEmission', DateType::class, ['label' => 'Date d\'émission', 'widget' => 'single_text'])
            ->add('dateValidite', DateType::class, ['label' => 'Valide jusqu\'au', 'widget' => 'single_text', 'required' => false])
            ->add('notes', TextareaType::class, ['label' => 'Notes', 'required' => false])
            ->add('lignes', CollectionType::class, [
                'entry_type' => LigneArticleType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Contrat::class]);
    }
}
