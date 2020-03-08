<?php
namespace App\Form\Listeners;

use App\Repository\CountryRepository;
use App\Repository\RegionRepository;
use App\Repository\StateRepository;
use App\Repository\TypeRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class Listeners
 * @package App\Form
 */
class ListenerView extends AbstractType
{
    /**
     * @var CountryRepository
     */
    private $country;

    /**
     * @var RegionRepository
     */
    private $region;

    /**
     * @var StateRepository
     */
    private $sp;

    /**
     * @var TypeRepository
     */
    private $type;

    /**
     * Listeners constructor.
     * @param CountryRepository $country
     * @param RegionRepository $region
     */
    public function __construct(
        CountryRepository $country,
        RegionRepository $region,
        StateRepository $sp,
        TypeRepository $type
    ) {
        $this->country =    $country;
        $this->region =     $region;
        $this->sp =         $sp;
        $this->type =       $type;
    }

    public function getFieldGroups($isAdmin)
    {
        return [
            'Contact Details' =>    [
                'name',
                'callsign',
                ($isAdmin ? 'email' : ''),
                'website'
            ],
            'Location' =>   [
                'qth',
                'sp',
                'itu',
                'gsq',
                ($isAdmin ? 'mapX' : ''),
                ($isAdmin ? 'mapY' : ''),
            ],
            'Receiving Station Details' =>  [
                'primaryQth',
                'timezone',
                'equipment',
                'notes',
            ],
            '' => [
                'print',
                'close',
                ($isAdmin ? 'save' : '')
            ]
        ];
    }
    /**
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface|void
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options)
    {
        $isAdmin = $options['isAdmin'];
        $formBuilder
            ->add(
                'id',
                HiddenType::class,
                [
                    'data'          => $options['id']
                ]
            )
            ->add(
                'name',
                TextType::class,
                [
                    'attr'          => [
                        'onchange'      => "try{setCustomValidity('')}catch(e){}",
                        'oninvalid'     => "setCustomValidity('Please enter this listener\'s name')"
                    ],
                    'data'          => $options['name'],
                    'disabled'      => !$isAdmin,
                    'empty_data'    => '',
                    'label'         => 'Name',
                ]
            )
            ->add(
                'callsign',
                TextType::class,
                [
                    'data'          => $options['callsign'],
                    'disabled'      => !$isAdmin,
                    'empty_data'    => '',
                    'label'         => 'Callsign',
                    'required'      => false
                ]
            )
            ->add(
                'website',
                TextType::class,
                [
                    'data'          => $options['website'],
                    'disabled'      => !$isAdmin,
                    'empty_data'    => '',
                    'label'         => 'Website',
                    'required'      => false
                ]
            )
            ->add(
                'qth',
                TextType::class,
                [
                    'attr'          => [
                        'onchange'      => "try{setCustomValidity('')}catch(e){}",
                        'oninvalid'     => "setCustomValidity('Please enter this listener\'s approximate location')"
                    ],
                    'data'          => $options['qth'],
                    'disabled'      => !$isAdmin,
                    'empty_data'    => '',
                    'label'         => 'Town / City',
                ]
            )
            ->add(
                'sp',
                ChoiceType::class,
                [
                    'choices'       => $this->sp->getMatchingOptions(),
                    'data'          => $options['sp'],
                    'disabled'      => !$isAdmin,
                    'label'         => 'State / Prov',
                    'required'      => false
                ]
            )
            ->add(
                'itu',
                ChoiceType::class,
                [
                    'choices'       => $this->country->getMatchingOptions(),
                    'data'          => $options['itu'],
                    'disabled'      => !$isAdmin,
                    'label'         => 'Country',
                ]
            )
            ->add(
                'gsq',
                TextType::class,
                [
                    'attr'          => [
                        'maxlength'     => 6,
                        'onchange'      => "try{setCustomValidity('')}catch(e){}",
                        'oninvalid'     => "setCustomValidity('Please provide the grid square so we can calculate distances')",
                        'size'          => 6,
                        'style'         => "width: 6em"
                    ],
                    'data'          => $options['gsq'],
                    'disabled'      => !$isAdmin,
                    'empty_data'    => '',
                    'label'         => 'Grid Square',
                ]
            )
            ->add(
                'primaryQth',
                ChoiceType::class,
                [
                    'attr'          => [
                        'style'         => "width: 6em"
                    ],
                    'choices'       => [
                        'Yes'           => 1,
                        'No'            => 0,
                    ],
                    'data'          => $options['primaryQth'],
                    'disabled'      => !$isAdmin,
                    'label'         => 'Primary Location',
                ]
            )
            ->add(
                'timezone',
                TextType::class,
                [
                    'attr'          => [
                        'maxlength'     => 3,
                        'onchange'      => "try{setCustomValidity('')}catch(e){}",
                        'oninvalid'     => "setCustomValidity('Please provide the hours ahead or behind UTC (use -n for timezones west of london)')",
                        'size'          => 3,
                        'style'         => "width: 4em"
                    ],
                    'data'          => $options['timezone'],
                    'disabled'      => !$isAdmin,
                    'empty_data'    => '',
                    'label'         => 'Timezone',
                ]
            )
            ->add(
                'equipment',
                TextareaType::class,
                [
                    'attr'          => [
                        'cols'          => 80,
                        'rows'          => 3,
                    ],
                    'data'          => html_entity_decode($options['equipment']),
                    'empty_data'    => '',
                    'disabled'      => !$isAdmin,
                    'label'         => 'Equipment',
                    'required'      => false
                ]
            )
            ->add(
                'notes',
                TextareaType::class,
                [
                    'attr'          => [
                        'cols'          => 80,
                        'rows'          => 3,
                    ],
                    'data'          => $options['notes'],
                    'disabled'      => !$isAdmin,
                    'empty_data'    => '',
                    'label'         => 'Notes',
                    'required'      => false
                ]
            )
            ->add(
                'print',
                ButtonType::class,
                [
                    'attr'          => [
                        'class'         => 'button small',
                        'onclick'       => 'window.print()'
                    ],
                    'label'         => 'Print...',
                ]
            )
            ->add(
                'close',
                ButtonType::class,
                [
                    'attr'          => [
                        'class' =>      'button small',
                        'onclick' =>    'window.close()'
                    ],
                    'label'         => 'Close',
                ]
            );
        ;

        if ($isAdmin) {
            $formBuilder
                ->add(
                    'email',
                    TextType::class,
                    [
                        'data'          => $options['email'],
                        'disabled'      => !$isAdmin,
                        'empty_data'    => '',
                        'label'         => 'Email Address',
                        'required'      => false
                    ]
                )
                ->add(
                    'mapX',
                    TextType::class,
                    [
                        'attr'          => [
                            'maxlength'     => 3,
                            'size'          => 3,
                            'style'         => "margin: 0 0.5em;width: 3em;"
                        ],
                        'data'          => $options['mapX'],
                        'disabled'      => !$isAdmin,
                        'empty_data'    => 0,
                        'label'         => 'Locator Map',
                        'required'      => false
                    ]
                )
                ->add(
                    'mapY',
                    TextType::class,
                    [
                        'attr'          => [
                            'maxlength'     => 3,
                            'size'          => 3,
                            'style'         => "margin: 0 0.5em;width: 3em;"
                        ],
                        'data'          => $options['mapY'],
                        'disabled'      => !$isAdmin,
                        'empty_data'    => 0,
                        'label'         => '',
                        'required'      => false
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
                );
        }

        return $formBuilder->getForm();
    }
}
