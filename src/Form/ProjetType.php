<?php

namespace App\Form;

use App\Entity\Contact;
use App\Entity\Projet;
use App\Enum\StatutProjet;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du projet',
                'help' => 'Nom du client, du marché, ou la date du jour pour les ventes du jour.',
            ])
            ->add('contact', EntityType::class, [
                'class' => Contact::class,
                'label' => 'Client / Contact',
                'required' => false,
                'placeholder' => '— Aucun —',
                'choice_label' => fn (Contact $c) => (string) $c,
            ])
            ->add('date', DateType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
            ])
            ->add('statut', EnumType::class, [
                'class' => StatutProjet::class,
                'label' => 'Statut',
                'choice_label' => fn (StatutProjet $s) => $s->libelle(),
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Projet::class]);
    }
}
