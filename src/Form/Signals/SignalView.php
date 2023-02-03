<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-18
 * Time: 12:08
 */

namespace App\Form\Signals;

use App\Form\Base;

use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface as FormInterfaceAlias;

/**
 * Class Listeners
 * @package App\Form
 */
class SignalView extends Base
{
    /**
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     * @return FormInterfaceAlias|void
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
                '_close',
                HiddenType::class,
                [
                    'data' =>       0
                ]
            )
            ->add(
                '_reload_opener',
                HiddenType::class,
                [
                    'data' =>       0
                ]
            )
            ->add(
                'call',
                TextType::class,
                [
                    'data' =>       $options['call'],
                    'empty_data' => '',
                    'label' =>      'ID',
                    'required' =>   false
                ]
            )
            ->add(
                'khz',
                TextType::class,
                [
                    'attr' => [
                        'onchange' =>   "try{setCustomValidity('')}catch(e){}",
                        'oninvalid' =>  "setCustomValidity('Please enter this signal\'s frequency')"
                    ],
                    'data' =>       $options['khz'],
                    'empty_data' => '',
                    'label' =>      'KHz',
                ]
            )
            ->add(
                'pwr',
                TextType::class,
                [
                    'data' =>       $options['pwr'],
                    'empty_data' => '',
                    'label' =>      'Pwr',
                    'required' =>   false,
                ]
            )
            ->add(
                'type',
                ChoiceType::class,
                [
                    'choices' =>    $this->typeRepository->getAllChoicesForKey(),
                    'data' =>       $options['type'],
                    'empty_data' => '',
                    'label' =>      'Type',
                    'required' =>   true
                ]
            )
            ->add(
                'active',
                ChoiceType::class,
                [
                    'choices' => [
                        'Inactive' =>   0,
                        'Active' =>     1
                    ],
                    'data' =>           $options['active'],
                    'empty_data' =>     '',
                    'label' =>          'Status',
                    'required' =>       true
                ]
            )
            ->add(
                'decommissioned',
                ChoiceType::class,
                [
                    'choices' => [
                        'No' =>   0,
                        'Yes' =>     1
                    ],
                    'data' =>       $options['decommissioned'],
                    'empty_data' => '',
                    'label' =>      'Decomm.',
                    'required' =>   false
                ]
            )
            ->add(
                'notes',
                TextareaType::class,
                [
                    'attr' => [
                        'cols' =>   80,
                        'rows' =>   4,
                    ],
                    'data' =>       $options['notes'],
                    'empty_data' => '',
                    'label' =>      'Notes',
                    'required' =>   false
                ]
            )
            ->add(
                'qth',
                TextType::class,
                [
                    'attr' => [
                        'onchange' =>   "try{setCustomValidity('')}catch(e){}",
                        'oninvalid' =>  "setCustomValidity('Please enter this signals\'s approximate location')"
                    ],
                    'data' =>           $options['qth'],
                    'empty_data' =>     '',
                    'label' =>          "'Name' and QTH",
                ]
            )
            ->add(
                'sp',
                ChoiceType::class,
                [
                    'choices' =>        $this->stateRepository->getMatchingOptions(),
                    'data' =>           $options['sp'],
                    'empty_data' =>     null,
                    'label' =>          'State / Prov',
                    'required' =>       false
                ]
            )
            ->add(
                'itu',
                ChoiceType::class,
                [
                    'choices' =>        $this->countryRepository->getMatchingOptions(),
                    'data' =>           $options['itu'],
                    'label' =>          'Country',
                ]
            )
            ->add(
                'gsq',
                TextType::class,
                [
                    'attr' => [
                        'maxlength' =>  6,
                        'size' =>       6,
                        'style' =>      "width: 4em"
                    ],
                    'data' =>           $options['gsq'],
                    'empty_data' =>     '',
                    'label' =>          'Grid Square',
                    'required' =>       false
                ]
            )
            ->add(
                'lat',
                TextType::class,
                [
                    'data' =>           $options['lat'],
                    'empty_data' =>     '',
                    'label' =>          'Lat',
                    'required' =>       false
                ]
            )
            ->add(
                'lon',
                TextType::class,
                [
                    'data' =>           $options['lon'],
                    'empty_data' =>     '',
                    'label' =>          'Lon',
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
                'sec',
                TextType::class,
                [
                    'data' =>           $options['sec'],
                    'empty_data' =>     '',
                    'label' =>          'Cycle',
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
                'firstHeard',
                TextType::class,
                [
                    'data' =>           $options['firstHeard'],
                    'empty_data' =>     '',
                    'label' =>          'First Logged',
                    'required' =>       false
                ]
            )
            ->add(
                'lastHeard',
                TextType::class,
                [
                    'data' =>           $options['lastHeard'],
                    'empty_data' =>     '',
                    'label' =>          'Last Logged',
                    'required' =>       false
                ]
            )
            ->add(
                'print',
                ButtonType::class,
                [
                    'attr' => [
                        'class' =>      'button small',
                        'onclick' =>    'window.print()'
                    ],
                    'label' =>          'Print...',
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
                        'class' =>      'button small'
                    ],
                    'label' =>          'Save',
                ]
            )
            ->add(
                'save_disabled',
                ButtonType::class,
                [
                    'attr' => [
                        'class' =>      'button small',
                        'disabled' =>   true,
                        'title' =>      'Admins only'
                    ],
                    'label' =>          'Save',
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
