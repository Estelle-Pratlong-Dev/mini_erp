<?php

namespace App\Form;

use App\Entity\Contact;
use App\Enum\TypeContact;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
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
            ])
            ->add('nom', TextType::class, ['label' => 'Nom / Raison sociale'])
            ->add('prenom', TextType::class, ['label' => 'Prénom', 'required' => false])
            ->add('siret', TextType::class, ['label' => 'SIRET', 'required' => false])
            ->add('numTva', TextType::class, ['label' => 'N° TVA', 'required' => false])
            ->add('email', TextType::class, ['label' => 'Email', 'required' => false])
            ->add('telephone', TextType::class, ['label' => 'Téléphone', 'required' => false])
            ->add('adresse', TextType::class, ['label' => 'Adresse', 'required' => false])
            ->add('codePostal', TextType::class, ['label' => 'Code postal', 'required' => false])
            ->add('ville', TextType::class, ['label' => 'Ville', 'required' => false])
            ->add('pays', TextType::class, ['label' => 'Pays', 'required' => false])
            ->add('notes', TextareaType::class, ['label' => 'Notes', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Contact::class]);
    }
}
