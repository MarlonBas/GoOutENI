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
              ->add('nom')
              ->add('rue')
              ->add('ville',EntityType::class, [
                  'class'=>Ville::class,
                  'choice_label'=>function ($ville) {
                      return $ville->__toString();
                  },
                  'mapped'=>false
              ])
              ->add('latitude')
              ->add('longitude')
              ->add( 'ajouter', SubmitType::class, [
                  'label' =>'Ajouter',
                  'attr'=>['class'=>'btn btn-primary']
              ])
          ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }

}