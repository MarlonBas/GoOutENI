<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;


class LieuFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
          $builder
              ->add('nom', null, [
              'label' => 'Nom du lieu : ', 'attr'=>['class'=>'form-control form-control-m']])
              ->add('rue', null, [
        'label' => 'Rue : ', 'attr'=>['class'=>'form-control form-control-m']])
              ->add('ville',EntityType::class, [
                  'class'=>Ville::class,
                  'placeholder' => 'Selectionner la ville',
                  'attr'=>['class'=>'form-select form-select-m mb-3'],
                  'choice_label'=>function ($ville) {
                      return $ville->__toString();
                  },
              ])
              ->add('latitude', null, [
                  'label' => 'Latitude : ', 'attr'=>['class'=>'form-control form-control-m']])
              ->add('longitude', null, [
                  'label' => 'Longitude : ', 'attr'=>['class'=>'form-control form-control-m']])
//              ->add( 'ajouter', SubmitType::class, [
//                  'label' =>'Ajouter',
//                  'attr'=>['class'=>'btn btn-primary']
//              ])
          ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }

}