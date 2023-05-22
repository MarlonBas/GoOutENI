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
                'attr'=>['class'=>'form-select form-select-lg mb-3']
            ])
            ->add('stringRecherche', TextType::class, [
                'label' => 'Nom de sortie',
                'required' => false
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Entre la date',
                'required' => false,
                'widget' => 'single_text',
                'attr'=>['class'=>'date date-lg mb-3']
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'et la date',
                'required' => false,
                'widget' => 'single_text',
                'attr'=>['class'=>'date date-lg mb-3']
            ])
            ->add('checkOrganisateur', CheckboxType::class, [
                'label' => "Sorties dont je suis l'organisateur/trice",
                'required' => false
            ])
            ->add('checkInscrit', CheckboxType::class, [
                'label' => 'Sorties auquels je suis incrit/e',
                'required' => false
            ])
            ->add('checkNonInscrit', CheckboxType::class, [
                'label' => 'Sorties auquels je ne suis pas incrit/e',
                'required' => false
            ])
            ->add('checkPassee', CheckboxType::class, [
                'label' => 'Sorties passées',
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher',
                'attr'=>['class'=>'btn btn-lg btn-dark']
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
