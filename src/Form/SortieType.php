<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\CampusRepository;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Sodium\add;

class SortieType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'label' => 'Nom de la sortie : ', 'attr'=>['class'=>'form-control form-control-m']])
            ->add('dateHeureDebut', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date et heure de la sortie : ', 'attr'=>['class'=>'form-control form-control-m']])
            ->add('duree', null, [
                'label' => 'Durée : ', 'attr'=>['class'=>'form-control form-control-m']
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date limite d\'inscription : ', 'attr'=>['class'=>'form-control form-control-m']])
            ->add('nbInscriptionMax', null, [
                'label' => 'Nombre de places : ', 'attr'=>['class'=>'form-control form-control-m']
            ])
            ->add('infosSortie', null, [
                'label' => 'Description et infos : ', 'attr'=>['class'=>'form-control form-control-m']
            ])
           ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'label' => 'Lieu : ',
                'choice_label' => function ($lieu) {
                    return $lieu->__toString();
                },
                'placeholder' => 'Selectionner le lieu de la sortie',
                'attr'=>['class'=>'form-select form-select-m mb-3'],
                //'disabled'=> $ville === null,
                'query_builder' => fn(LieuRepository $lieuRepository) => $lieuRepository->createQueryBuilder('l')
                    ->orderBy('l.nom', 'ASC')
            ])
            ->add('campus', EntityType::class,[
                'class' =>Campus::class,
                'choice_label' => 'nom',
                'attr'=>['class'=>'form-select form-select-m mb-3'],
                'query_builder' => fn(CampusRepository $campusRepository) => $campusRepository->createQueryBuilder('c')
                    ->orderBy('c.nom', 'ASC'),
                       ])
            ->add( 'enregistrer', SubmitType::class, [
                'label' =>'Enregistrer',
                'attr'=>['class'=>'btn btn-primary']
            ])

            ->add( 'publier', SubmitType::class, [
                'label' =>'Publier la sortie',
                'attr'=>['class'=>'btn btn-primary']
            ]);


//        ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
//            $ville = $event->getData();
//            $form = $event->getForm();
//            dump($form);
//        })
//            ->getForm();
//            dump($builder->getForm());
//
//
//                    $builder->addEventListener(
//                        FormEvents::PRE_SET_DATA,
//                        function (FormEvent $event) {
//                            $data = $event->getData();
//                            dump($data);
//                             $villeID = $data->getVille();
//                            $this->addLieuField($event->getForm(), $villeID);
//
//
//                        }
//                    );
//            $builder->get('ville')->addEventListener(
//                FormEvents::POST_SUBMIT,
//                function (FormEvent $event) {
//                    $villeID = $event->getForm()->getData()->getID();
//                    $this->addLieuField($event->getForm()->getParent(), $villeID);
//                }
//            );
        }

        /*
                              $builder->get('ville')->addEventListener(
                                  FormEvents::POST_SUBMIT,
                                  function (FormEvent $event) {
                                      $form = $event->getForm();
                                      $form->getParent()->add('lieu', EntityType::class, [
                                          'class' => Lieu::class,
                                          'placeholder' => 'select a lieu',
                                          'choices' => $form->getData()->getLieu()
                                      ]);

                                  }
                              );

                              $builder->addEventListener(
                                  FormEvents::POST_SET_DATA,
                                  function (FormEvent $event) {

                                      $form = $event->getForm();
                                      $data = $event->getData();
                                      $lieu= $data->getLieu();

                                     if ($lieu) {
                                          // On récupère la ville
                                          $ville = $lieu->getVille();

                                          // On crée les 1 champs supplémentaires
                                          $this->addLieuField($form, $ville);

                                          // On set les données
                                          $form->get('ville')->setData($ville);

                                      }/* else {

                                          // On crée les 2 champs en les laissant vide (champs utilisé pour le JavaScript)
                                         $form->get('ville')->setData($lieu->getVille());
                                         $form->add('lieu', EntityType::class,[
                                             'class' => Lieu::class,
                                             'placeholder' => 'Choisir un lieu',
                                             'choices' => $lieu->getVille()->getLieu()
                                         ]);

                                     }
                                  }
                              );*/



    private function getAvailableLieuChoices(LieuRepository $lieuRepository, int $villeID) : array
    {
       return $lieuRepository->findByVilleID($villeID);
    }


    private function addLieuField(FormInterface $form, ?int $villeID)
    {
        $lieuChoices = null === $villeID ? [] : $this->getAvailableLieuChoices($villeID);

        $builder = $form->add('Lieu',EntityType::class, [
                'class'           =>  Lieu::class,
                'placeholder'     => $villeID ? 'Sélectionnez votre Lieu' : 'Sélectionnez votre Ville',
                'required'        => false,
                'choices'         => $villeID,
                'disabled'  => null === $villeID,
                'invalide_message' => false,
                'autocomplete' => true, 'attr'=>['class'=>'form-control form-control-m']
            ]

        );

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
