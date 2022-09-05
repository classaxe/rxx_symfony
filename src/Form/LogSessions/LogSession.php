<?php
namespace App\Form\LogSessions;

use App\Form\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class Listeners
 * @package App\Form\LogSessions
 */
class LogSession extends Base
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
                'listenerId',
                HiddenType::class,
                [
                    'data' =>           $options['listenerId'],
                    'label' =>          'Listener Location',
                ]
            )
            ->add(
                'operatorId',
                HiddenType::class,
                [
                    'data' =>           $options['operatorId'],
                    'label' =>          'Operator (Only for multi-Op Locations)',
                ]
            )
            ->add(
                'comment',
                TextType::class,
                [
                    'attr' => ($options['isAdmin'] ? [] : [ 'readonly' => 'readonly' ]),
                    'data' =>           $options['comment'],
                    'empty_data' =>     '',
                    'label' =>          'Log Session Comment',
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
            );

        if ($options['isAdmin']) {
            $formBuilder
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
                    'operator',
                    TextType::class,
                    [
                        'attr' => [
                            'readonly' => 'readonly'
                        ],
                        'data' =>           $options['operator'],
                        'empty_data' =>     '',
                        'label' =>          'Operator (if multi-op)',
                    ]
                )
                ->add(
                    'name',
                    TextType::class,
                    [
                        'attr' => [
                            'readonly' => 'readonly'
                        ],
                        'data' =>           $options['name'],
                        'empty_data' =>     '',
                        'label' =>          'Listener Location Name',
                    ]
                )
                ->add(
                    'callsign',
                    TextType::class,
                    [
                        'attr' => [
                            'readonly' => 'readonly'
                        ],
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
                        'attr' => [
                            'readonly' => 'readonly'
                        ],
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
                            'readonly' => 'readonly'
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
                        'attr' => [
                            'background' => 'f00'
                        ],
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
                        'attr' => [
                            'readonly' => 'readonly'
                        ],
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
                            'readonly' => 'readonly'
                        ],
                        'data' =>           $options['gsq'],
                        'empty_data' =>     '',
                        'label' =>          'Grid Square',
                    ]
                )
                ->add(
                    'timezone',
                    ChoiceType::class,
                    [
                        'attr' => [
                            'readonly' => 'readonly'
                        ],
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
                            'readonly' =>   'readonly',
                            'style' =>      'height: 8em'
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
                                'readonly' =>   'readonly',
                                'style' =>      'height: 4em'
                            ],
                            'data' =>           html_entity_decode($options['notes']),
                            'empty_data' =>     '',
                            'label' =>          'Notes',
                            'required' =>       false
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
            );
        }

        return $formBuilder->getForm();
    }
}
