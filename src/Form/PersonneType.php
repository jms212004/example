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

class PersonneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname')
            ->add('name')
            ->add('age')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('profile', EntityType::class, [
                'expanded' => true,
                'class' => Profile::class,
                'multiple' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.rs', 'ASC');
                }
                ]
            )
            ->add('hobbies', EntityType::class, [
                'expanded' => false,
                'class' => Hobby::class,
                'multiple' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('h')
                        ->orderBy('h.designation', 'ASC');
                }
            ])
            ->add('job', EntityType::class, [
                'required' => false,
                'class' => Job::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('j')
                        ->orderBy('j.designation', 'ASC');
                }
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
