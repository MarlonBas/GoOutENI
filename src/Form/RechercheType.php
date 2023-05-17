<?php

namespace App\Form;

use App\Entity\Recherche;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RechercheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus')
            ->add('stringRecherche')
            ->add('dateDebut')
            ->add('dateFin')
            ->add('checkOrganisateur')
            ->add('checkInscrit')
            ->add('checkNonInscrit')
            ->add('checkPassee')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recherche::class,
        ]);
    }
}
