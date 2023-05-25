<?php


namespace App\Form;

use App\Entity\Campus;
use App\Entity\Recherche;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RechercheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'required' => false,
                'placeholder' => '--Choississez votre campus--',
                'attr'=>['class'=>'form-select form-select-sm mb-3 col']
            ])
            ->add('stringRecherche', TextType::class, [
                'label' => 'Rechercher parmi les sorties',
                'required' => false,
                'attr'=>['class'=>'form-control col']
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Entre la date',
                'required' => false,
                'widget' => 'single_text',
                'attr'=>['class'=>'date date-sm mb-3 form-control col']
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'et la date',
                'required' => false,
                'widget' => 'single_text',
                'attr'=>['class'=>'date date-sm mb-3 form-control col']
            ])
            ->add('checkOrganisateur', CheckboxType::class, [
                'label' => "Sorties dont je suis l'organisateur/trice",
                'required' => false,
                'attr'=>['class'=>'form-check-input']
            ])
            ->add('checkInscrit', CheckboxType::class, [
                'label' => 'Sorties auquels je suis inscrit/e',
                'required' => false,
                'attr'=>['class'=>'form-check-input']
            ])
            ->add('checkNonInscrit', CheckboxType::class, [
                'label' => 'Sorties auquels je ne suis pas inscrit/e',
                'required' => false,
                'attr'=>['class'=>'form-check-input']
            ])
            ->add('checkPassee', CheckboxType::class, [
                'label' => 'Sorties passées',
                'required' => false,
                'attr'=>['class'=>'form-check-input']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher',
                'attr'=>['class'=>'btn btn-lg btn-dark col']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recherche::class,
        ]);
    }
}
