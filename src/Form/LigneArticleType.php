<?php

namespace App\Form;

use App\Entity\LigneArticle;
use App\Entity\Produit;
use App\Repository\TauxTvaRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LigneArticleType extends AbstractType
{
    public function __construct(private readonly TauxTvaRepository $tauxTvaRepo)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $tauxChoices = [];
        foreach ($this->tauxTvaRepo->actifs() as $t) {
            $tauxChoices[$t->getLibelleAffiche()] = $t->getTaux();
        }

        $builder
            ->add('produit', EntityType::class, [
                'class' => Produit::class,
                'label' => 'Article',
                'required' => true,
                'placeholder' => '— Choisir un article —',
                'choice_label' => fn (Produit $p) => (string) $p,
                'query_builder' => fn ($repo) => $repo->createQueryBuilder('p')
                    ->where('p.actif = true')->orderBy('p.designation', 'ASC'),
                'attr' => ['class' => 'ligne-produit'],
            ])
            ->add('quantite', NumberType::class, ['label' => 'Qté', 'scale' => 3, 'attr' => ['class' => 'ligne-qte']])
            ->add('prixUnitaireHt', NumberType::class, ['label' => 'PU HT', 'scale' => 2, 'attr' => ['class' => 'ligne-pu']])
            ->add('tauxTva', ChoiceType::class, [
                'label' => 'TVA %',
                'choices' => $tauxChoices,
                'placeholder' => $tauxChoices ? false : '—',
                'attr' => ['class' => 'ligne-tva'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => LigneArticle::class]);
    }
}
