<?php
namespace App\Form\Listeners;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class Listeners
 * @package App\Form
 */
class ListenerAward extends AbstractType
{
    /**
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface|void
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options)
    {
        $formBuilder
            ->add(
                'id',
                HiddenType::class,
                [
                    'data' =>       $options['id']
                ]
            )
            ->add(
                'awards',
                HiddenType::class
            )
            ->add(
                'filter',
                HiddenType::class
            )
            ->add(
                'name',
                TextType::class,
                [
                    'attr' => [
                        'readonly' => 'readonly',
                    ],
                    'data' =>       $options['name'],
                    'empty_data' => '',
                    'label' =>      'Name:',
                ]
            )
            ->add(
                'email',
                TextType::class,
                [
                    'data' =>       $options['email'],
                    'empty_data' => '',
                    'label' =>      'Email:',
                ]
            )
            ->add(
                'body',
                TextareaType::class,
                [
                    'attr' => [
                        'class' =>  'monospace',
                        'readonly' => 'readonly',
                        'style' =>  'height: 20em'
                    ],
                    'label' =>      'Message:',
                    'required' =>   false
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'attr' => [
                        'class' =>  'button',
                        'disabled' => 'disabled',
                        'style' =>  'padding: 0.25em 1em; width: auto'
                    ],
                    'label' =>      'Order',
                ]
            );

        return $formBuilder->getForm();
    }
}
