<?php
namespace App\Form\Listeners;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class Listeners
 * @package App\Form
 */
class LogUpload extends AbstractType
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
                    'data' =>           $options['id']
                ]
            )
            ->add(
                'step',
                HiddenType::class,
                [
                    'data' =>       $options['step']
                ]
            )
            ->add(
                'format',
                TextType::class,
                [
                    'attr' => [
                        'style' => 'float: left'
                    ],
                    'data' =>           $options['format'],
                    'empty_data' =>     '',
                    'label' =>          'Format'
                ]
            )
            ->add(
                'saveFormat',
                SubmitType::class,
                [
                    'attr' => [
                        'class' => 'button small',
                        'disabled' => 'disabled'
                    ],
                    'label' =>          'Save',
                ]
            )
            ->add(
                'logs',
                TextareaType::class,
                [
                    'attr' => [
                        'cols' =>       80,
                        'rows' =>       30
                    ],
                    'data' =>           '',
                    'empty_data' =>     '',
                    'label' =>          'Logs',
                    'required' =>       false
                ]
            )
            ->add(
                'tabs2spaces',
                ButtonType::class,
                [
                    'attr' => [
                        'class' =>      'button small'
                    ],
                    'label' =>          'Tabs > Spaces',
                ]
            )
            ->add(
                'lineUp',
                ButtonType::class,
                [
                    'attr' => [
                        'class' =>      'button small'
                    ],
                    'label' =>          'Line Up',
                ]
            )
            ->add(
                'parseLog',
                SubmitType::class,
                [
                    'attr' => [
                        'class' => 'button small'
                    ],
                    'label' => 'Parse Log',
                ]
            );

        return $formBuilder->getForm();
    }
}
