<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        /*if(empty(!$options["data"])){
            $roles = $options["data"]->getRoles();
            //$roles = $options["data"]->getRoles() -- $roles[0] = $options["data"]->getRoles()[0]
            if($roles[0] == "")
        }*/

        $builder
            ->add('nom', TextType::class, ['label'=>'Nom', 'required'=>'true', 'attr'=>['class'=>'form-control form-control-m']])
            ->add('prenom', TextType::class, ['label'=>'Prenom', 'required'=>'true', 'attr'=>['class'=>'form-control form-control-m']])
            ->add('pseudo', TextType::class, ['label'=>'Pseudo', 'required'=>'true', 'attr'=>['class'=>'form-control form-control-m']])
            ->add('telephone', TextType::class, ['label'=>'Telephone', 'required'=>'true', 'attr'=>['class'=>'form-control form-control-m']])
            ->add('email', TextType::class, ['label'=>'Email', 'required'=>'true', 'attr'=>['class'=>'form-control form-control-m']])
            ->add('campus', EntityType::class, ['label' => 'Campus',
                // classe à afficher
                'class' => Campus::class,
                // quelle propriété utiliser pour les <option> dans la liste déroulante ?
                'choice_label' => 'nom',
                'placeholder' => '--Choississez votre campus--', 'attr'=>['class'=>'form-select form-select-xs mb-3']])

            ->add('motdepasse', RepeatedType::class, ['type'=> PasswordType::class, 'label'=>'Mot de Passe',
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'invalid_message'=>'Les mots de passe doivent correspondre',
                'mapped' => false,
                'options'=> [
                'attr' => ['autocomplete' => 'new-password', 'class'=>'form-control form-control-m']],
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

            ;
            if($options['isGrantedUser']) {

                $builder
                    ->add('image', FileType::class, [
                        'label' => 'Upload Image',
                        'mapped' => false,
                        'required' => false,
                        'constraints' => [
                            new File([
                                'mimeTypes' => [
                                    'image/jpeg',
                                    'image/png'
                                ],
                                'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG).',
                            ]),
                        ],
                    ])
                    ->add('roles', ChoiceType::class, ['choices' => ['ROLE_USER' => 'ROLE_USER', 'ROLE_ORGA' => 'ROLE_ORGA', 'ROLE_ADMIN' => 'ROLE_ADMIN'], 'attr'=>['class'=>'form-select form-select-m mb-3']])
                    ->get('roles')
                    ->addModelTransformer(new CallbackTransformer(
                        function ($rolesArray) {
                            // transform the array to a string
                            return count($rolesArray) ? $rolesArray[0] : null;
                        },
                        function ($rolesString) {
                            // transform the string back to an array
                            return [$rolesString];
                        }

                    ))
                ;
               }
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
            'isGrantedUser' => false
        ]);
    }
}
