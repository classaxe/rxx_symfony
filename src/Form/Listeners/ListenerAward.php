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
                    'data'          => $options['id']
                ]
            )
            ->add(
                'email',
                TextType::class,
                [
                    'data'          => $options['email'],
                    'empty_data'    => '',
                    'label'         => 'Reply To',
                ]
            )
            ->add(
                'body',
                TextareaType::class,
                [
                    'attr'          => [
                        'style'         => "height: 12em;"
                    ],
                    'empty_data'    => '',
                    'label'         => 'Message',
                    'required'      => false
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'attr' => [
                        'class' => 'button',
                        'style' => 'padding: 0.25em 1em; width: auto'
                    ],
                    'label' => 'Place Order',
                ]
            );

        return $formBuilder->getForm();
    }
}
