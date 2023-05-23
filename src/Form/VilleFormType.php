<?php

namespace App\Form;

use App\Repository\LieuRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;



class VilleFormType extends AbstractType
{
    private $lieuRepository;

    public function __construct(LieuRepository $lieuRepository)
    {
        $this->lieuRepository = $lieuRepository;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ville', ChoiceType::class, [
                'placeholder' => 'Choisir une ville',
                'choices' => $options['villes'],
                'required' => true,
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $ville = $event->getData();

                $lieux = $this->lieuRepository->findOneBy($ville);

                $form->getParent()->add('lieu', ChoiceType::class, [
                    'placeholder' => 'Choisir un lieu',
                    'choices' => $lieux,
                    'required' => false,
                ]);
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'villes' => [], // Les villes disponibles (à injecter lors de la création du formulaire)
        ]);
    }
}