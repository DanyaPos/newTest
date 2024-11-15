<?php

namespace App\Form;

use App\Entity\Developer;
use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeveloperType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $formAttrs = ['class' => 'form-control'];

        $builder
            ->add('fullName', TextType::class, [
                'label' => 'ФИО',
                'attr' => $formAttrs,
                'required' => true, // Пример, если поле обязательно
            ])

            // Должность
            ->add('position', TextType::class, [
                'label' => 'Должность',
                'attr' => $formAttrs,
                'required' => true, // Если нужно обязательное поле
            ])

            // Email
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => $formAttrs,
                'required' => true,
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\Email(), // Валидация на корректный email
                ],
            ])

            // Номер телефона
            ->add('phone', TelType::class, [
                'label' => 'Номер телефона',
                'attr' => $formAttrs,
                'required' => true,
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\Regex([
                        'pattern' => '/^\+?\d{1,4}?[\d\-\(\)\s]{7,20}$/',
                        'message' => 'Неверный формат номера телефона',
                    ]), // Валидация на корректный номер телефона
                ],
            ])
            ->add('project', ChoiceType::class, [
                'choices' => $options['projects'],
                'choice_label' => function (Project $project) {
                    return $project->getName();
                },
                'expanded' => false,
                'multiple' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Developer::class,
            'projects' => []
        ]);
    }
}
