<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\User;
use App\Entity\Tickets;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\File;

class TicketsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('type', ChoiceType::class, [
              'choices' => [
                  'bug' => ('bug'),
                  'task' => ('task'),
              ]

            ])
            ->add('status', ChoiceType::class, [
              'choices' => [
                  'new' => ('new'),
                  'in progress' => ('in progress'),
                  'testing' => ('testing'),
                  'done' => ('done'),
              ]
            ])
            ->add('assigned', EntityType::class, [
                  'class' => User::class,
                  'choice_label' => 'name',
            ])
            ->add('desription')
            ->add('file', FileType::class, [
                'label' => 'File',
                'mapped' => false,
                'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tickets::class,
        ]);
    }
}
