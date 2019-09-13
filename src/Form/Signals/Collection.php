<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-18
 * Time: 12:08
 */

namespace App\Form\Signals;

use App\Form\Base;
use App\Repository\CountryRepository;
use App\Repository\ListenerRepository;
use App\Repository\PaperRepository;
use App\Repository\RegionRepository;
use App\Repository\TypeRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class Listeners
 * @package App\Form
 */
class Collection extends Base
{
    /**
     * @var CountryRepository
     */
    private $country;

    /**
     * @var ListenerRepository
     */
    private $listener;

    /**
     * @var PaperRepository
     */
    private $paper;

    /**
     * @var RegionRepository
     */
    private $region;

    /**
     * @var TypeRepository
     */
    private $type;

    /**
     * Collection constructor.
     * @param CountryRepository $country
     * @param ListenerRepository $listener
     * @param PaperRepository $paperRepository
     * @param RegionRepository $region
     * @param TypeRepository $type
     * @package ListenerRepository $listener
     */
    public function __construct(
        CountryRepository $country,
        ListenerRepository $listener,
        PaperRepository $paper,
        RegionRepository $region,
        TypeRepository $type
    ) {
        $this->country = $country;
        $this->listener = $listener;
        $this->paper = $paper;
        $this->region = $region;
        $this->type = $type;
    }

    /**
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface|void
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options)
    {
        $system =   $options['system'];
        $region =   $options['region'];

        $this->addPaging($formBuilder, $options);
        $this->addSorting($formBuilder, $options);

        $formBuilder
            ->add(
                'show',
                ChoiceType::class,
                [
                    'choices' => [
                        'Show List' =>      'list',
                        'Show Map' =>       'map',
                        'Show SeekList' =>  'seeklist',
                    ],
                    'placeholder' =>    false,
                    'label' =>          'Display Mode',
                    'required' =>       false
                ]
            )
            ->add(
                'paper',
                ChoiceType::class,
                [
                    'choices' =>        $this->paper->getAllChoices(),
                    'placeholder' =>    false,
                    'label' =>          'Paper Size',
                    'required' =>       false
                ]
            )
            ->add(
                'sortby',
                ChoiceType::class,
                [
                    'choices' =>    [],
                    'label'  =>     'Sort By',
                    'required' =>   false
                ]
            )
            ->add(
                'za',
                CheckboxType::class,
                [
                    'label' =>      'Z-A',
                    'required' =>   false,
                    'value' =>      1
                ]
            )
            ->add(
                'types',
                ChoiceType::class,
                [
                    'choices' =>    $this->type->getAllChoices(),
                    'choice_attr' => function ($choiceValue, $key, $value) {
                        return ['class' => strToLower($value)];
                    },
                    'expanded' =>   true,
                    'label' =>      false,
                    'multiple' =>   true,
                    'attr' =>       [ 'legend' => 'Signal Types' ]
                ]
            )
            ->add(
                'call',
                TextType::class,
                [
                    'label' =>      'Call / ID',
                    'required' =>   false
                ]
            )
            ->add(
                'khz_1',
                TextType::class,
                [
                    'label' =>      'Freq.',
                    'required' =>   false
                ]
            )
            ->add(
                'khz_2',
                TextType::class,
                [
                    'label' =>      false,
                    'required' =>   false
                ]
            )
            ->add(
                'channels',
                ChoiceType::class,
                [
                    'choices' => [
                        'All' =>        '',
                        'Only 1 KHz' => '1',
                        'Not 1 KHz' =>  '2',
                    ],
                    'label' => 'Channels',
                    'required' => false
                ]
            )
            ->add(
                'active',
                ChoiceType::class,
                [
                    'choices' => [
                        'All' =>        '',
                        'Active' =>     '1',
                        'Inactive' =>   '2',
                    ],
                    'label' => 'Active Status',
                    'required' => false
                ]
            )
            ->add(
                'personalise',
                ChoiceType::class,
                [
                    'choices'       => $this->listener->getAllOptions(
                        $system,
                        null,
                        ' ',
                        true
                    ),
                    'expanded'      => false,
                    'label'         => 'Personalise for',
                    'required'      => false,
                    'choice_translation_domain' => false
                ]
            )
            ->add(
                'offsets',
                ChoiceType::class,
                [
                    'choices' => [
                        'Relative' =>   '',
                        'Absolute' =>   '1',
                    ],
                    'label' => 'Offsets',
                    'required' => false
                ]
            )
            ->add(
                'states',
                TextType::class,
                [
                    'label' => 'States',
                    'required' => false
                ]
            )
            ->add(
                'sp_itu_clause',
                ChoiceType::class,
                [
                    'choices'       => [
                        'AND'   => 'AND',
                        'OR'    =>  'OR',
                    ],
                    'placeholder'   => false,
                    'label'         => 'Combiner',
                    'required'      => false
                ]
            )
            ->add(
                'countries',
                TextType::class,
                [
                    'label'         => 'Countries',
                    'required'      => false
                ]
            )
            ->add(
                'region',
                ChoiceType::class,
                [
                    'choices'       => $this->region->getAllOptions(),
                    'label'         => 'Region',
                    'required'      =>   false
                ]
            )
            ->add(
                'gsq',
                TextType::class,
                [
                    'label'         => 'Grid Squares',
                    'required'      => false
                ]
            )
            ->add(
                'range_gsq',
                TextType::class,
                [
                    'label'         => 'From GSQ',
                    'required'      => false,
                    'attr'          => [
                        'maxlength' => 6
                    ]
                ]
            )
            ->add(
                'range_min',
                TextType::class,
                [
                    'label'         => 'DX',
                    'required'      => false,
                    'attr'          => [
                        'disabled'  => 'disabled'
                    ]
                ]
            )
            ->add(
                'range_max',
                TextType::class,
                [
                    'label'         => false,
                    'required'      => false,
                    'attr'          => [
                        'disabled'  => 'disabled'
                    ]
                ]
            )
            ->add(
                'range_units',
                ChoiceType::class,
                [
                    'choices' => [
                        'km'    => 'km',
                        'miles' => 'mi'
                    ],
                    'expanded'      => true,
                    'placeholder'   => false,
                    'required'      => false,
                    'attr'          => [
                        'disabled'  => 'disabled',
                        'legend'    => 'Units'
                    ]
                ]
            )
            ->add(
                'listener',
                ChoiceType::class,
                [
                    'choices'       => $this->listener->getAllOptions(
                        $system,
                        null,
                        'Anyone (or enter values in "Heard here" box)',
                        false
                    ),
                    'expanded'      => false,
                    'label'         => 'Listener(s)',
                    'multiple'      => true,
                    'required'      => false,
                    'choice_translation_domain' => false,
                    'attr'          => [ 'class' => 'multiple', 'multiple' => 'multiple']
                ]
            )
            ->add(
                'listener_invert',
                ChoiceType::class,
                [
                    'choices' => [
                        'Logged by'     => 0,
                        'Not logged by' => 1
                    ],
                    'expanded'      => true,
                    'placeholder'   => false,
                    'required'      => false,
                    'attr'          => []
                ]
            )
            ->add(
                'heard_in',
                TextType::class,
                [
                    'label'         => 'Heard Here',
                    'required'      => false
                ]
            )
            ->add(
                'heard_in_mod',
                ChoiceType::class,
                [
                    'choices' => [
                        'Any'       => 'any',
                        'All'       => 'all'
                    ],
                    'expanded'      => true,
                    'placeholder'   => false,
                    'required'      => false,
                    'attr'          => []
                ]
            )
            ->add(
                'logged_date_1',
                DateType::class,
                [
                    'label'         => 'Logged Between',
                    'widget'        => 'single_text',
                    'html5'         => false,
                    'required'      => false,
                    'attr'          => [ 'class' => 'js-datepicker' ]
                ]
            )
            ->add(
                'logged_date_2',
                DateType::class,
                [
                    'label'         => '',
                    'widget'        => 'single_text',
                    'html5'         => false,
                    'required'      => false,
                    'attr'          => [ 'class' => 'js-datepicker' ]
                ]
            )
            ->add(
                'logged_first_1',
                DateType::class,
                [
                    'label'         => 'First Logged',
                    'widget'        => 'single_text',
                    'html5'         => false,
                    'required'      => false,
                    'attr'          => [ 'class' => 'js-datepicker' ]
                ]
            )
            ->add(
                'logged_first_2',
                DateType::class,
                [
                    'label'         => '',
                    'widget'        => 'single_text',
                    'html5'         => false,
                    'required'      => false,
                    'attr'          => [ 'class' => 'js-datepicker' ]
                ]
            )
            ->add(
                'logged_last_1',
                DateType::class,
                [
                    'label'         => 'Last Logged',
                    'widget'        => 'single_text',
                    'html5'         => false,
                    'required'      => false,
                    'attr'          => [ 'class' => 'js-datepicker' ]
                ]
            )
            ->add(
                'logged_last_2',
                DateType::class,
                [
                    'label'         => '',
                    'widget'        => 'single_text',
                    'html5'         => false,
                    'required'      => false,
                    'attr'          => [ 'class' => 'js-datepicker' ]
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label'         => 'Go',
                    'attr'          => [ 'class' => 'button small' ]
                ]
            )
            ->add(
                'clear',
                ResetType::class,
                [
                    'label'         => 'Clear',
                    'attr'          => [ 'class' => 'button small' ]
                ]
            );

        $this->addPaging($formBuilder, $options);

        return $formBuilder->getForm();
    }
}
