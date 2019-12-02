<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-18
 * Time: 12:08
 */

namespace App\Form\Signals;

use App\Repository\CountryRepository;
use App\Repository\StateRepository;

use App\Repository\RegionRepository;
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
class SignalView extends AbstractType
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
            'Profile' =>    [
                'call',
                'khz',
                'pwr',
                'type',
            ],
            'Location' => [
                'qth',
                'sp',
                'itu',
                'gsq',
                'lat',
                'lon'
            ],
            'Latest Values' =>  [
                'lsb',
                'usb',
//                'cycle',
//                'format',

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
                'call',
                TextType::class,
                [
                    'data'          => $options['call'],
                    'disabled'      => !$isAdmin,
                    'empty_data'    => '',
                    'label'         => 'ID',
                    'required'      => false
                ]
            )
            ->add(
                'khz',
                TextType::class,
                [
                    'attr'          => [
                        'onchange'      => "try{setCustomValidity('')}catch(e){}",
                        'oninvalid'     => "setCustomValidity('Please enter this signal\'s frequency')"
                    ],
                    'data'          => $options['khz'],
                    'disabled'      => !$isAdmin,
                    'empty_data'    => '',
                    'label'         => 'KHz',
                ]
            )
            ->add(
                'pwr',
                TextType::class,
                [
                    'data'          => $options['pwr'],
                    'disabled'      => !$isAdmin,
                    'empty_data'    => '',
                    'label'         => 'Pwr',
                    'required'      => false
                ]
            )
            ->add(
                'type',
                ChoiceType::class,
                [
                    'choices'       => $this->type->getAllChoices(false),
                    'data'          => $options['type'],
                    'disabled'      => !$isAdmin,
                    'empty_data'    => '',
                    'label'         => 'Type',
                    'required'      => true
                ]
            )
            ->add(
                'qth',
                TextType::class,
                [
                    'attr'          => [
                        'onchange'      => "try{setCustomValidity('')}catch(e){}",
                        'oninvalid'     => "setCustomValidity('Please enter this signals\'s approximate location')"
                    ],
                    'data'          => $options['qth'],
                    'disabled'      => !$isAdmin,
                    'empty_data'    => '',
                    'label'         => '\'Name\' and QTH',
                ]
            )
            ->add(
                'sp',
                ChoiceType::class,
                [
                    'choices'       => $this->sp->getMatchingOptions(),
                    'data'          => $options['sp'],
                    'disabled'      => !$isAdmin,
                    'empty_data'    => null,
                    'label'         => 'State / Prov'
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
                'lat',
                TextType::class,
                [
                    'data'          => $options['lat'],
                    'disabled'      => !$isAdmin,
                    'empty_data'    => '',
                    'label'         => 'Lat',
                    'required'      => false
                ]
            )
            ->add(
                'lon',
                TextType::class,
                [
                    'data'          => $options['lon'],
                    'disabled'      => !$isAdmin,
                    'empty_data'    => '',
                    'label'         => 'Lon',
                    'required'      => false
                ]
            )
            ->add(
                'lsb',
                TextType::class,
                [
                    'data'          => $options['lsb'],
                    'disabled'      => !$isAdmin,
                    'empty_data'    => '',
                    'label'         => 'LSB',
                    'required'      => false
                ]
            )
            ->add(
                'usb',
                TextType::class,
                [
                    'data'          => $options['usb'],
                    'disabled'      => !$isAdmin,
                    'empty_data'    => '',
                    'label'         => 'USB',
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
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'attr'          => [
                        'class'         => 'button small'
                    ],
                    'label'         => 'Save',
                ]
            )
        ;

        return $formBuilder->getForm();
    }
}
