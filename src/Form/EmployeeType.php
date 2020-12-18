<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\Title;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'First name'
            ])
            ->add('lastname', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Last name'
            ])
            ->add('birthday', TextType::class, [
                'attr' => [
                    'class' => 'datepicker form-control'
                ],
                'empty_data' => new \DateTime(),
                'required' => false
            ])
            ->add('gender', ChoiceType::class, [
                'attr' => [
                    'class' => 'gender-select form-control'
                ],
                'choices' => [
                    "Muški" => "Muški",
                    "Ženski" => "Ženski"
                    ],
                'label' => 'Gender',
                'required' => false
            ])
            ->add('active', CheckboxType::class, [
                'attr' => [
                    'class' => 'checkbox',
                    'style' => 'margin-top: 10px'
                ],
                'label' => 'Is employee active?',
                'required' => false
            ])->add('titles', EntityType::class, [
                'attr' => [
                    'class' => 'title-select form-control',
                    'multiple' => true
                ],
                'label' => 'Titles',
                'multiple' => true,
                'class' => Title::class,
                'choice_label' => 'name',
                'mapped' => false,
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary btn-lg',
                    'style' => 'margin-top: 20px'
                ],
                'label' => 'Save'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Employee::class
        ]);
    }
}
