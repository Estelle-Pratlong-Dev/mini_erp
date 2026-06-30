<?php

namespace App\Form;

use App\Entity\Contact;
use App\Entity\DelaiPaiement;
use App\Entity\Facture;
use App\Entity\ModePaiement;
use App\Entity\Projet;
use App\Enum\StatutFacture;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('projet', EntityType::class, [
                'class' => Projet::class,
                'label' => 'Projet',
                'choice_label' => fn (Projet $p) => (string) $p,
            ])
            ->add('contact', EntityType::class, [
                'class' => Contact::class,
                'label' => 'Client',
                'required' => false,
                'placeholder' => '— Comptant / aucun —',
                'choice_label' => fn (Contact $c) => (string) $c,
            ])
            ->add('statut', EnumType::class, [
                'class' => StatutFacture::class,
                'label' => 'Statut',
                'choice_label' => fn (StatutFacture $s) => $s->libelle(),
            ])
            ->add('dateEmission', DateType::class, ['label' => 'Date d\'émission', 'widget' => 'single_text'])
            ->add('delaiPaiement', EntityType::class, [
                'class' => DelaiPaiement::class,
                'label' => 'Délai de paiement',
                'required' => false,
                'placeholder' => '—',
                'choice_label' => fn (DelaiPaiement $d) => $d->getNom(),
                'choice_attr' => fn (DelaiPaiement $d) => ['data-jours' => $d->getJours()],
                'query_builder' => fn ($repo) => $repo->createQueryBuilder('d')
                    ->where('d.actif = true')->orderBy('d.jours', 'ASC'),
                'help' => 'Calcule l\'échéance à partir de la date d\'émission.',
            ])
            ->add('dateEcheance', DateType::class, [
                'label' => 'Date d\'échéance',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('modePaiement', EntityType::class, [
                'class' => ModePaiement::class,
                'label' => 'Mode de paiement',
                'required' => false,
                'placeholder' => '—',
                'choice_label' => fn (ModePaiement $m) => $m->getNom(),
                'query_builder' => fn ($repo) => $repo->createQueryBuilder('m')
                    ->where('m.actif = true')->orderBy('m.nom', 'ASC'),
            ])
            ->add('notes', TextareaType::class, ['label' => 'Notes', 'required' => false])
            ->add('lignes', CollectionType::class, [
                'entry_type' => LigneArticleType::class,
                'label' => false,
                'allow_add' => $options['lignes_modifiables'],
                'allow_delete' => $options['lignes_modifiables'],
                'by_reference' => false,
                'prototype' => true,
                // Si ce n'est pas la dernière facture du contrat, les lignes sont verrouillées.
                'disabled' => !$options['lignes_modifiables'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Facture::class,
            'lignes_modifiables' => true,
        ]);
        $resolver->setAllowedTypes('lignes_modifiables', 'bool');
    }
}
