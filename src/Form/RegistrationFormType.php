<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roles', ChoiceType::class, [ 'choices'=> ['ROLE_USER'=> 'ROLE_USER', 'ROLE_ORGA'=>'ROLE_ORGA', 'ROLE_ADMIN'=>'ROLE_ADMIN']])
            ->add('nom', TextType::class, ['label'=>'Nom', 'required'=>'true'])
            ->add('prenom', TextType::class, ['label'=>'Prenom', 'required'=>'true'])
            ->add('pseudo', TextType::class, ['label'=>'Pseudo', 'required'=>'true'])
            ->add('telephone', TextType::class, ['label'=>'Telephone', 'required'=>'true'])
            ->add('email', TextType::class, ['label'=>'Email', 'required'=>'true'])
            ->add('campus', EntityType::class, ['label' => 'Campus',
                // classe à afficher
                'class' => Campus::class,
                // quelle propriété utiliser pour les <option> dans la liste déroulante ?
                'choice_label' => 'nom',
                'placeholder' => '--Choississez votre campus--'])

            ->add('motdepasse', RepeatedType::class, ['type'=> PasswordType::class, 'label'=>'Mot de Passe',
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'invalid_message'=>'Les mots de passe doivent correspondre',
                'mapped' => false,
                'options'=> [
                'attr' => ['autocomplete' => 'new-password']],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),

                ],
                'first_options'=> ['label'=> 'Mot de Passe'],
                'second_options'=>['label'=> 'Confirmation Mot de Passe']
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],

            ])
            ->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // transform the array to a string
                    return count($rolesArray)? $rolesArray[0]: null;
                },
                function ($rolesString) {
                    // transform the string back to an array
                    return [$rolesString];
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
