<?php
namespace App\Form\Logs;

use App\Form\Base;

use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class Listeners
 * @package App\Form
 */
class Log extends Base
{
    /**
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     * @return FormInterface|void
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
                '_close',
                HiddenType::class,
                [
                    'data' =>       0
                ]
            )
            ->add(
                'reload',
                HiddenType::class,
                [
                    'data' =>       0
                ]
            )
            ->add(
                'signalId',
                HiddenType::class,
                [
                    'data' =>           $options['signalId'],
                    'empty_data' =>     '',
                    'label' =>          'Signal',
                ]
            )
            ->add(
                'listenerId',
                HiddenType::class,
                [
                    'data' =>           $options['listenerId'],
                    'empty_data' =>     '',
                    'label' =>          'Listener',
                ]
            )
            ->add(
                'dxKm',
                TextType::class,
                [
                    'attr' => [
                        'readonly' => 'readonly'
                    ],
                    'data' =>           $options['dxKm'],
                    'empty_data' =>     '',
                    'label' =>          'DX (KM)',
                ]
            )
            ->add(
                'dxMiles',
                TextType::class,
                [
                    'attr' => [
                        'readonly' => 'readonly'
                    ],
                    'data' =>           $options['dxMiles'],
                    'empty_data' =>     '',
                    'label' =>          'DX (Miles)',
                ]
            )
            ->add(
                'date',
                TextType::class,
                [
                    'attr' => [
                        'class' => 'js-datepicker'
                    ],
                    'data' =>           $options['date'],
                    'empty_data' =>     '',
                    'label' =>          'Date',
                ]
            )
            ->add(
                'daytime',
                ChoiceType::class,
                [
                    'attr' => [
                        'readonly' => 'readonly'
                    ],
                    'choices' =>        [
                        'No' => 0,
                        'Yes' => 1
                    ],
                    'data' =>           $options['daytime'],
                    'empty_data' =>     '',
                    'label' =>          'Daytime',
                    'required' =>       false
                ]
            )
            ->add(
                'time',
                TextType::class,
                [
                    'data' =>           $options['time'],
                    'empty_data' =>     '',
                    'label' =>          'UTC',
                    'required' =>       false
                ]
            )
            ->add(
                'format',
                TextType::class,
                [
                    'data' =>           $options['format'],
                    'empty_data' =>     '',
                    'label' =>          'Format',
                    'required' =>       false
                ]
            )
            ->add(
                'sec',
                TextType::class,
                [
                    'data' =>           $options['sec'],
                    'empty_data' =>     '',
                    'label' =>          'Cycle Time',
                    'required' =>       false
                ]
            )
            ->add(
                'lsbApprox',
                CheckboxType::class,
                [
                    'data' =>           $options['lsbApprox'],
                    'empty_data' =>     '',
                    'label' =>          'LSB Approx',
                    'mapped' =>         false,
                    'required' =>       false
                ]
            )
            ->add(
                'lsb',
                TextType::class,
                [
                    'data' =>           $options['lsb'],
                    'empty_data' =>     '',
                    'label' =>          'LSB',
                    'required' =>       false
                ]
            )
            ->add(
                'usbApprox',
                CheckboxType::class,
                [
                    'data' =>           $options['usbApprox'],
                    'empty_data' =>     '',
                    'label' =>          'USB Approx',
                    'mapped' =>         false,
                    'required' =>       false
                ]
            )
            ->add(
                'usb',
                TextType::class,
                [
                    'data' =>           $options['usb'],
                    'empty_data' =>     '',
                    'label' =>          'USB',
                    'required' =>       false
                ]
            )
            ->add(
                'close',
                ButtonType::class,
                [
                    'attr' => [
                        'class' =>      'button small',
                        'onclick' =>    'window.close()'
                    ],
                    'label' =>          'Close',
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'attr' => [
                        'class' => 'button small'
                    ],
                    'label' => 'Save',
                ]
            )
            ->add(
                'saveClose',
                SubmitType::class,
                [
                    'attr' => [
                        'class' => 'button small'
                    ],
                    'label' => 'Save + Close',
                ]
            );
        return $formBuilder->getForm();
    }
}
