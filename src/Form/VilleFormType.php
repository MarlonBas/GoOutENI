<?php

namespace App\Form;

use App\Entity\Ville;
use App\Repository\LieuRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;



class VilleFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la Ville : ', 'attr'=>['class'=>'form-control form-control-m']])
            ->add('codepostal', TextType::class, [
                'label' => 'Code Postal : ',
                'attr'=>[
                    'class'=>'form-control form-control-m',
                    'pattern' => '^[0-9]{5}$',
                    'title' => 'Le code postal doit être composé de 5 chiffres.']])
        ;


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ville::class,
        ]);
    }
}