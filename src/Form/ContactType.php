<?php

namespace App\Form;

use App\Entity\CategorieContact;
use App\Entity\Contact;
use App\Enum\TypeContact;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', EnumType::class, [
                'class' => TypeContact::class,
                'label' => 'Type',
                'choice_label' => fn (TypeContact $t) => $t->libelle(),
                'attr' => ['class' => 'contact-type'],
            ])
            ->add('categorie', EntityType::class, [
                'class' => CategorieContact::class,
                'label' => 'Catégorie',
                'required' => false,
                'placeholder' => '— Aucune —',
                'choice_label' => fn (CategorieContact $c) => $c->getNom(),
                'query_builder' => fn ($repo) => $repo->createQueryBuilder('c')
                    ->where('c.actif = true')->orderBy('c.nom', 'ASC'),
                'attr' => ['class' => 'contact-categorie'],
            ])
            ->add('nom', TextType::class, ['label' => 'Nom / Raison sociale'])
            ->add('prenom', TextType::class, ['label' => 'Prénom', 'required' => false, 'attr' => ['class' => 'champ-particulier']])
            ->add('siret', TextType::class, [
                'label' => 'SIRET', 'required' => false,
                'attr' => ['class' => 'champ-pro', 'inputmode' => 'numeric', 'maxlength' => 14, 'pattern' => '[0-9]{14}'],
            ])
            ->add('numTva', TextType::class, ['label' => 'N° TVA', 'required' => false, 'attr' => ['class' => 'champ-pro']])
            ->add('email', EmailType::class, ['label' => 'Email', 'required' => false])
            ->add('telephone', TelType::class, ['label' => 'Téléphone', 'required' => false])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse', 'required' => false,
                'attr' => ['class' => 'adresse-autocomplete', 'autocomplete' => 'off'],
            ])
            ->add('codePostal', TextType::class, ['label' => 'Code postal', 'required' => false, 'attr' => ['class' => 'adresse-cp']])
            ->add('ville', TextType::class, ['label' => 'Ville', 'required' => false, 'attr' => ['class' => 'adresse-ville']])
            ->add('pays', TextType::class, ['label' => 'Pays', 'required' => false])
            ->add('notes', TextareaType::class, ['label' => 'Notes', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Contact::class]);
    }
}
