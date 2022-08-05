<?php
namespace App\Form\Listeners;

use App\Form\Base;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class Listeners
 * @package App\Form
 */
class LogUpload extends Base
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
                'selected',
                HiddenType::class,
                [
                    'data' =>           $options['selected']
                ]
            )
            ->add(
                'format',
                TextType::class,
                [
                    'attr' => [
                        'placeholder' => 'Log format goes here'
                    ],
                    'data' =>           $options['format'],
                    'empty_data' =>     '',
                    'label' =>          'Format',
                    'trim' =>           false
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
                        'placeholder' => 'Paste log entries here',
                        'rows' =>       30
                    ],
                    'data' =>           '',
                    'label' =>          'Logs',
                    'required' =>       false,
                    'trim' =>           false
                ]
            )
            ->add(
                'operatorId',
                HiddenType::class,
                [
                    'data' =>           $options['operatorId'],
                    'empty_data' =>     '',
                    'label' =>          'Operator',
                ]
            )
            ->add(
                'comment',
                TextType::class,
                [
                    'attr' => [
                        'placeholder' => '--- Optional comment (e.g. CLE number or occasion) ---',
                        'maxlength' => 255
                    ],
                    'data' =>           $options['comment'],
                    'label' =>          'Comment:',
                    'required' =>       false,
                    'trim' =>           false
                ]
            )
            ->add(
                'YYYY',
                TextType::class,
                [
                    'attr' => [
                        'maxlength' => 4,
                        'placeholder' => 'YYYY'
                    ],
                    'label' =>          'Year:',
                    'required' =>       false
                ]
            )
            ->add(
                'MM',
                TextType::class,
                [
                    'attr' => [
                        'maxlength' => 2,
                        'placeholder' => 'MM'
                    ],
                    'label' =>          'Month:',
                    'required' =>       false
                ]
            )
            ->add(
                'DD',
                TextType::class,
                [
                    'attr' => [
                        'maxlength' => 2,
                        'placeholder' => 'DD'
                    ],
                    'label' =>          'Date:',
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
            )
            ->add(
                'back',
                SubmitType::class,
                [
                    'attr' => [
                        'class' => 'button small'
                    ],
                    'label' => 'Go Back',
                ]
            )
            ->add(
                'submitLog',
                SubmitType::class,
                [
                    'attr' => [
                        'class' => 'button small'
                    ],
                    'label' => 'Submit Log',
                ]
            );

        return $formBuilder->getForm();
    }
}
