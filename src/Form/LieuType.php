<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lieu',EntityType::class, [
                'class'=> Lieu::class,
                'label' => 'Lieux :',
                'choice_label'=>function ($lieu) {
                    return $lieu->__toString();
                },
                'mapped'=>false,
                'placeholder' => 'Selectionner le lieu de la sortie',
                'attr'=>['class'=>'form-select form-select-m mb-3'],
                'required' =>false
            ])

        ;
        $builder->get('lieu')->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event){
                $form = $event->getForm();
                $form->getParent()
                    ->
                    add('rue', TextType::class, [
                        'required' => false,
                        'mapped' => false,
                        'label' => 'Rue :',
                        'attr' => ['placeholder' => $form->getData()->getRue(), 'class' => 'form-control form-control-m'],
                        'disabled' => true
                    ])
                    ->add('codepostal', TextType::class, [
                        'required' => false,
                        'mapped' => false,
                        'label' => 'Ville :',
                        'attr' => ['placeholder' => $form->getData()->getVille()->__toString(), 'class' => 'form-control form-control-m'],
                        'disabled' => true
                    ])
                    ->add('longitude', TextType::class, [
                        'required' => false,
                        'mapped' => false,
                        'label' => 'Longitude :',
                        'attr' => ['placeholder' => $form->getData()->getLongitude(), 'class' => 'form-control'],
                        'disabled' => true
                    ])
                    ->add('latitude', TextType::class, [
                        'required' => false,
                        'mapped' => false,
                        'label' => 'Latitude : ',
                        'attr' => ['placeholder' => $form->getData()->getLatitude(), 'class' => 'form-control form-control-s'],
                        'disabled' => true
                    ]);
            }
        );

        $builder->get('lieu')->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                $data = $event->getData();

                /* @var $lieu \App\Entity\Lieu */

                $form = $event->getForm();
                     if ($data) {
                         $form->getParent()
                         ->
                         add('rue', TextType::class, [
                             'required' => false,
                             'mapped' => false,
                             'label' => 'Rue :',
                             'attr' => ['placeholder' => $form->getData()->getRue(), 'class' => 'form-control form-control-m'],
                             'disabled' => true
                         ])
                             ->add('codepostal', TextType::class, [
                                 'required' => false,
                                 'mapped' => false,
                                 'label' => 'Ville :',
                                 'attr' => ['placeholder' => $form->getData()->getVille()->__toString(), 'class' => 'form-control form-control-m'],
                                 'disabled' => true
                             ])
                             ->add('longitude', TextType::class, [
                                 'required' => false,
                                 'mapped' => false,
                                 'label' => 'Longitude :',
                                 'attr' => ['placeholder' => $form->getData()->getLongitude(), 'class' => 'form-control'],
                                 'disabled' => true
                             ])
                             ->add('latitude', TextType::class, [
                                 'required' => false,
                                 'mapped' => false,
                                 'label' => 'Latitude : ',
                                 'attr' => ['placeholder' => $form->getData()->getLatitude(), 'class' => 'form-control form-control-s'],
                                 'disabled' => true
                             ]);
                     }
                     else{
                         $form->getParent()

                         ->add('rue', TextType::class, [
                             'required' => false,
                             'mapped' => false,
                             'label' => 'Rue :',
                             'attr' => ['class' => 'form-control form-control-m'],
                             'disabled' => true
                         ])
                             ->add('codepostal', TextType::class, [
                                 'required' => false,
                                 'mapped' => false,
                                 'label' => 'Ville :',
                                 'attr' => ['class' => 'form-control form-control-m'],
                                 'disabled' => true
                             ])
                             ->add('longitude', TextType::class, [
                                 'required' => false,
                                 'mapped' => false,
                                 'label' => 'Longitude :',
                                 'attr' => ['class' => 'form-control'],
                                 'disabled' => true
                             ])
                             ->add('latitude', TextType::class, [
                                 'required' => false,
                                 'mapped' => false,
                                 'label' => 'Latitude : ',
                                 'attr' => ['class' => 'form-control form-control-s'],
                                 'disabled' => true
                             ]);

                     }
                     }

        );


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }

}