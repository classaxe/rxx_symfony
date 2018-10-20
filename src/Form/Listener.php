<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-18
 * Time: 12:08
 */

namespace App\Form;

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
class Listener extends AbstractType
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
            'Station Details' =>  [
                'primary',
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
                    'label'         => 'Name',
                    'data'          => $options['name'],
                    'disabled'      => !$isAdmin,
                    'empty_data'    => '',
                ]
            )
            ->add(
                'callsign',
                TextType::class,
                [
                    'label'         => 'Callsign',
                    'data'          => $options['callsign'],
                    'disabled'      => !$isAdmin,
                    'empty_data'    => '',
                ]
            )
            ->add(
                'email',
                TextType::class,
                [
                    'label'         => 'Email Address',
                    'data'          => $options['email'],
                    'disabled'      => !$isAdmin,
                    'empty_data'    => '',
                ]
            )
            ->add(
                'website',
                TextType::class,
                [
                    'label'         => 'Website',
                    'data'          => $options['website'],
                    'disabled'      => !$isAdmin,
                    'empty_data'    => '',
                ]
            )
            ->add(
                'qth',
                TextType::class,
                [
                    'label'         => 'Town / City',
                    'data'          => $options['qth'],
                    'disabled'      => !$isAdmin,
                    'empty_data'    => '',
                ]
            )
            ->add(
                'sp',
                ChoiceType::class,
                [
                    'label'         => 'State / Prov',
                    'choices'       => $this->sp->getMatchingOptions(),
                    'data'          => $options['sp'],
                    'disabled'      => !$isAdmin
                ]
            )
            ->add(
                'itu',
                ChoiceType::class,
                [
                    'label'         => 'Country',
                    'choices'       => $this->country->getMatchingOptions(),
                    'data'          => $options['itu'],
                    'disabled'      => !$isAdmin
                ]
            )
            ->add(
                'gsq',
                TextType::class,
                [
                    'label'         => 'Grid Square',
                    'data'          => $options['gsq'],
                    'disabled'      => !$isAdmin,
                    'empty_data'    => '',
                    'attr'          => ['size' => '6', 'maxlength' => 6, 'style' => "width: 6em"]
                ]
            )
            ->add(
                'timezone',
                TextType::class,
                [
                    'label'         => 'Timezone',
                    'attr'          => ['size' => '3', 'maxlength' => 3, 'style' => "width: 4em"],
                    'data'          => $options['timezone'],
                    'disabled'      => !$isAdmin,
                    'empty_data'    => '',
                ]
            )
            ->add(
                'primary',
                ChoiceType::class,
                [
                    'label'         => 'Primary Location',
                    'choices'       => [
                        'Yes' => 1,
                        'No' => 0,
                    ],
                    'data'          => $options['primary'],
                    'disabled'      => !$isAdmin,
                    'attr'          => [ 'style' => "width: 6em"]
                ]
            )
            ->add(
                'mapX',
                TextType::class,
                [
                    'label'         => 'Map X',
                    'attr'          => ['size' => '3', 'maxlength' => 3, 'style' => "width: 4em;"],
                    'data'          => $options['mapX'],
                    'disabled'      => !$isAdmin,
                    'empty_data'    => 0,
                ]
            )
            ->add(
                'mapY',
                TextType::class,
                [
                    'label'         => 'Map Y',
                    'attr'          => ['size' => '3', 'maxlength' => 3, 'style' => "width: 4em;"],
                    'data'          => $options['mapY'],
                    'disabled'      => !$isAdmin,
                    'empty_data'    => 0,
                ]
            )
            ->add(
                'equipment',
                TextareaType::class,
                [
                    'label'         => 'Equipment',
                    'data'          => html_entity_decode($options['equipment']),
                    'empty_data'    => '',
                    'disabled'      => !$isAdmin,
                    'attr'          => ['rows' => '3', 'cols' => '80']
                ]
            )
            ->add(
                'notes',
                TextareaType::class,
                [
                    'label'         => 'Notes',
                    'attr'          => ['rows' => '3', 'cols' => '80'],
                    'data'          => $options['notes'],
                    'disabled'      => !$isAdmin,
                    'empty_data'    => '',
                ]
            )
            ->add(
                'print',
                ButtonType::class,
                [
                    'label'         => 'Print...',
                    'attr'          => [
                        'class' =>      'button small',
                        'onclick' =>    'window.print()'
                    ]
                ]
            )
            ->add(
                'close',
                ButtonType::class,
                [
                    'label'         => 'Close',
                    'attr'          => [
                        'class' =>      'button small',
                        'onclick' =>    'window.close()'
                    ]
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label'         => 'Save',
                    'attr'          => [
                        'class' =>      'button small'
                    ]
                ]
            )
        ;

        return $formBuilder->getForm();
    }
}
