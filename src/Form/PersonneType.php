<?php

namespace App\Form;

use App\Entity\Personne;
use App\Entity\Profile;
use App\Entity\Hobby;
use App\Entity\Job;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Contracts\Translation\TranslatorInterface;

class PersonneType extends AbstractType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', NULL, [
                'label' => $this->translator->trans('Firstname')
                
            ])
            ->add('name', NULL, [
                'label' => $this->translator->trans('Name')
            ])
            ->add('age', NULL, [
                'label' => $this->translator->trans('Age')
            ])
            ->add('createdAt', NULL, [
                'label' => $this->translator->trans('Created at')
            ])
            ->add('updatedAt', NULL, [
                'label' => $this->translator->trans('Update at')
            ])
            ->add('profile', EntityType::class, [
                'label' => $this->translator->trans('Profil'),
                'expanded' => false,
                'required' => false,
                'class' => Profile::class,
                'multiple' => false,
                'attr' => [
                    'class' => 'select2'
                ]
            ])
            ->add('hobbies', EntityType::class, [
                'label' => $this->translator->trans('Hobbies'),
                'expanded' => false,
                'class' => Hobby::class,
                'multiple' => true,
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('h')
                        ->orderBy('h.designation', 'ASC');
                },
                'choice_label' => 'designation',
                'attr' => [
                    'class' => 'select2'
                ]
            ])
            ->add('job', EntityType::class, [
                'label' => $this->translator->trans('Job'),
                'required' => false,
                'multiple' => false,
                'class' => Job::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('j')
                        ->orderBy('j.designation', 'ASC');
                }
            ])
            ->add('photo', FileType::class, [
                'label' => $this->translator->trans('Your profil image - only image'),
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/gif',
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => $this->translator->trans('Merci de déposer un fichier image valide'),
                    ])
                ],
            ])
            ->add('editer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personne::class,
        ]);
    }
}
