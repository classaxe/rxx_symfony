<?php
namespace App\Form\Listeners;

use App\Form\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class Listeners
 * @package App\Form
 */
class ListenerView extends Base
{
    /**
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     * @return FormInterface|void
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options)
    {
        $isAdmin = $options['isAdmin'];
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
            'name',
            TextType::class,
            [
                'attr' => [
                    'onchange' =>   "try{setCustomValidity('')}catch(e){}",
                    'oninvalid' =>  "setCustomValidity('Please enter this listener\'s name')"
                ],
                'data' =>           $options['name'],
                'empty_data' =>     '',
                'label' =>          'Name',
            ]
        )
        ->add(
            'callsign',
            TextType::class,
            [
                'data' =>           $options['callsign'],
                'empty_data' =>     '',
                'label' =>          'Callsign',
                'required' =>       false
            ]
        )
        ->add(
            'website',
            TextType::class,
            [
                'data' =>           $options['website'],
                'empty_data' =>     '',
                'label' =>          'Website',
                'required' =>       false
            ]
        )
        ->add(
            'qth',
            TextType::class,
            [
                'attr' => [
                    'onchange' =>   "try{setCustomValidity('')}catch(e){}",
                    'oninvalid' =>  "setCustomValidity('Please enter this listener\'s approximate location')"
                ],
                'data' =>           $options['qth'],
                'empty_data' =>     '',
                'label' =>          'Town / City',
            ]
        )
        ->add(
            'sp',
            ChoiceType::class,
            [
                'choices' =>        $this->stateRepository->getMatchingOptions(),
                'data' =>           $options['sp'],
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
                    'onchange' =>   "try{setCustomValidity('')}catch(e){}",
                    'oninvalid' =>  "setCustomValidity('Please provide the grid square so we can calculate distances')",
                    'size' =>       6,
                    'style' =>      "width: 6em"
                ],
                'data' =>           $options['gsq'],
                'empty_data' =>     '',
                'label' =>          'Grid Square',
            ]
        )
        ->add(
            'primaryQth',
            ChoiceType::class,
            [
                'attr' => [
                    'style' =>      "width: 6em"
                ],
                'choices' => [
                    'Yes' =>        1,
                    'No' =>         0,
                ],
                'data' =>           $options['primaryQth'],
                'label' =>          'Primary Location',
            ]
        )
        ->add(
            'timezone',
            ChoiceType::class,
            [
                'choices' =>        $this->timeRepository->getAllOptions(),
                'choice_translation_domain' => false,
                'data' =>           $options['timezone'],
                'label' =>          'Timezone',
                'required' =>       false
            ]
        )
        ->add(
            'equipment',
            TextareaType::class,
            [
                'attr' => [
                    'cols' =>       80,
                    'rows' =>       5,
                ],
                'data' =>           html_entity_decode($options['equipment']),
                'empty_data' =>     '',
                'label' =>          'Equipment',
                'required' =>       false
            ]
        )
        ->add(
            'notes',
            TextareaType::class,
            [
                'attr' => [
                    'cols' =>       80,
                    'rows' =>       5,
                ],
                'data' =>           $options['notes'],
                'empty_data' =>     '',
                'label' =>          'Notes',
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
        );

        if ($isAdmin) {
            $formBuilder->add(
                'email',
                TextType::class,
                [
                    'data' =>       $options['email'],
                    'empty_data' => '',
                    'label' =>      'Email Address',
                    'required' =>   false
                ]
            )
            ->add(
                'mapX',
                TextType::class,
                [
                    'attr' => [
                        'maxlength' =>  3,
                        'size' =>       3,
                        'style' =>      "margin: 0 0.5em;width: 3em;"
                    ],
                    'data' =>           $options['mapX'],
                    'empty_data' =>     0,
                    'label' =>          'Locator Map',
                    'required' =>       false
                ]
            )
            ->add(
                'mapY',
                TextType::class,
                [
                    'attr' => [
                        'maxlength' =>  3,
                        'size' =>       3,
                        'style' =>      "margin: 0 0.5em;width: 3em;"
                    ],
                    'data' =>           $options['mapY'],
                    'empty_data' =>     0,
                    'label' =>          '',
                    'required' =>       false
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
        } else {
            $formBuilder->add(
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
            );
        }

        return $formBuilder->getForm();
    }
}
