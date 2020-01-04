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
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @var TranslatorInterface
     */
    private $translator;

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
        TranslatorInterface $translator,
        TypeRepository $type
    ) {
        $this->country = $country;
        $this->listener = $listener;
        $this->paper = $paper;
        $this->region = $region;
        $this->translator = $translator;
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
        $this->addPaging($formBuilder, $options);
        $this->addSorting($formBuilder, $options);

        $i18n = $this->translator;

        $formBuilder
            ->setAction($options['url'])
            ->add(
                'show',
                HiddenType::class,
                [
                    'data' =>           $options['show']
                ]
            )
            ->add(
                'paper',
                HiddenType::class,
                [
                    'data' =>           $options['paper']
                ]
            )
            ->add(
                'sortby',
                ChoiceType::class,
                [
                    'choices' =>        [],
                    'label'  =>         'Sort By',
                    'required' =>       false
                ]
            )
            ->add(
                'za',
                CheckboxType::class,
                [
                    'label' =>          'Z-A',
                    'required' =>       false,
                    'value' =>          1
                ]
            )
            ->add(
                'types',
                ChoiceType::class,
                [
                    'attr' =>           [ 'legend' => 'Signal Types' ],
                    'choices' =>        $this->type->getAllChoices(true),
                    'choice_attr' =>    function ($value) { return ['class' => strToLower($value)]; },
                    'data' =>           $options['types'],
                    'expanded' =>       true,
                    'label' =>          false,
                    'multiple' =>       true
                ]
            )
            ->add(
                'call',
                TextType::class,
                [
                    'data' =>           $options['call'],
                    'label' =>          'Call / ID',
                    'required' =>       false
                ]
            )
            ->add(
                'khz_1',
                TextType::class,
                [
                    'data' =>           $options['khz_1'],
                    'label' =>          'Freq.',
                    'required' =>       false
                ]
            )
            ->add(
                'khz_2',
                TextType::class,
                [
                    'data' =>           $options['khz_2'],
                    'label' =>          false,
                    'required' =>       false
                ]
            )
            ->add(
                'channels',
                ChoiceType::class,
                [
                    'choices' =>        [ 'All' => '', 'Only 1 KHz' => '1', 'Not 1 KHz' =>  '2' ],
                    'data' =>           $options['channels'],
                    'label' =>          'Channels',
                    'required' =>       false
                ]
            )
            ->add(
                'active',
                ChoiceType::class,
                [
                    'choices' =>        [ 'All' => '', 'Active' => '1', 'Inactive' =>   '2' ],
                    'data' =>           $options['active'],
                    'label' =>          'Active Status',
                    'required' =>       false
                ]
            )
            ->add(
                'personalise',
                ChoiceType::class,
                [
                    'choices' =>        $this->listener->getAllOptions($system,null, $i18n->trans('(None specified)'), true),
                    'choice_translation_domain' => false,
                    'data' =>           $options['personalise'],
                    'expanded' =>       false,
                    'label' =>          'Personalise for',
                    'required' =>       false
                ]
            )
            ->add(
                'offsets',
                ChoiceType::class,
                [
                    'choices' =>        [ 'Relative' => '', 'Absolute' =>   '1' ],
                    'data' =>           $options['offsets'],
                    'label' =>          'Offsets',
                    'required' =>       false
                ]
            )
            ->add(
                'states',
                TextType::class,
                [
                    'data' =>           $options['states'],
                    'label' =>          'States',
                    'required' =>       false
                ]
            )
            ->add(
                'sp_itu_clause',
                ChoiceType::class,
                [
                    'choices' =>        [ 'AND' => 'AND', 'OR' => 'OR' ],
                    'data' =>           $options['sp_itu_clause'],
                    'label' =>          'Combiner',
                    'placeholder' =>    false,
                    'required' =>       false
                ]
            )
            ->add(
                'countries',
                TextType::class,
                [
                    'data' =>           $options['countries'],
                    'label' =>          'Countries',
                    'required' =>       false
                ]
            )
            ->add(
                'region',
                ChoiceType::class,
                [
                    'choices' =>        $this->region->getAllOptions(),
                    'data' =>           $options['region'],
                    'label' =>          'Region',
                    'required' =>       false
                ]
            )
            ->add(
                'gsq',
                TextType::class,
                [
                    'data' =>           $options['gsq'],
                    'label' =>          'Grid Squares',
                    'required' =>       false
                ]
            )
            ->add(
                'range_gsq',
                TextType::class,
                [
                    'attr' =>           [ 'maxlength' => 6 ],
                    'data' =>           $options['range_gsq'],
                    'label' =>          'From GSQ',
                    'required' =>       false
                ]
            )
            ->add(
                'range_min',
                TextType::class,
                [
                    'attr' =>           [ 'disabled' => 'disabled' ],
                    'data' =>           $options['range_min'],
                    'label' =>          'DX',
                    'required' =>       false
                ]
            )
            ->add(
                'range_max',
                TextType::class,
                [
                    'attr' =>           [ 'disabled' => 'disabled' ],
                    'data' =>           $options['range_max'],
                    'label' =>          false,
                    'required' =>       false,
                ]
            )
            ->add(
                'range_units',
                ChoiceType::class,
                [
                    'attr' =>           [ 'disabled' => 'disabled', 'legend' => 'Units' ],
                    'choices' =>        [ 'km' => 'km', 'miles' => 'mi' ],
                    'data' =>           $options['range_units'],
                    'expanded' =>       true,
                    'placeholder' =>    false,
                    'required' =>       false
                ]
            )
            ->add(
                'listener',
                ChoiceType::class,
                [
                    'attr' =>           [ 'class' => 'multiple' ],
                    'choices' =>        $this->listener->getAllOptions( $system, null, $i18n->trans('Anyone (or enter values in "Heard here" box)'), false ),
                    'choice_translation_domain' => false,
                    'data' =>           $options['listener'],
                    'expanded' =>       false,
                    'label' =>          'Listener(s)',
                    'multiple' =>       true,
                    'required' =>       false,
                ]
            )
            ->add(
                'listener_invert',
                ChoiceType::class,
                [
                    'choices' =>        [ 'Logged by' => 0, 'Not logged by' => 1 ],
                    'data' =>           $options['listener_invert'],
                    'expanded' =>       true,
                    'placeholder' =>    false,
                    'required' =>       false
                ]
            )
            ->add(
                'heard_in',
                TextType::class,
                [
                    'data' =>           $options['heard_in'],
                    'label' =>          'Heard Here',
                    'required' =>       false
                ]
            )
            ->add(
                'heard_in_mod',
                ChoiceType::class,
                [
                    'choices' =>        [ 'Any' => 'any', 'All' => 'all' ],
                    'data' =>           $options['heard_in_mod'],
                    'expanded' =>       true,
                    'placeholder' =>    false,
                    'required' =>       false,
                ]
            )
            ->add(
                'logged_date_1',
                DateType::class,
                [
                    'attr' =>           [ 'class' => 'js-datepicker' ],
                    'data' =>           $options['logged_date_1'],
                    'html5' =>          false,
                    'label' =>          'Logged Between',
                    'required' =>       false,
                    'widget' =>         'single_text'
                ]
            )
            ->add(
                'logged_date_2',
                DateType::class,
                [
                    'attr' =>           [ 'class' => 'js-datepicker' ],
                    'data' =>           $options['logged_date_2'],
                    'html5' =>          false,
                    'label' =>          '',
                    'required' =>       false,
                    'widget' =>         'single_text',
                ]
            )
            ->add(
                'logged_first_1',
                DateType::class,
                [
                    'attr' =>           [ 'class' => 'js-datepicker' ],
                    'data' =>           $options['logged_first_1'],
                    'html5' =>          false,
                    'label' =>          'First Logged',
                    'required' =>       false,
                    'widget' =>         'single_text',
                ]
            )
            ->add(
                'logged_first_2',
                DateType::class,
                [
                    'attr' =>           [ 'class' => 'js-datepicker' ],
                    'data' =>           $options['logged_first_2'],
                    'html5' =>          false,
                    'label' =>          '',
                    'required' =>       false,
                    'widget' =>         'single_text',
                ]
            )
            ->add(
                'logged_last_1',
                DateType::class,
                [
                    'attr' =>           [ 'class' => 'js-datepicker' ],
                    'data' =>           $options['logged_last_1'],
                    'html5' =>          false,
                    'label' =>          'Last Logged',
                    'required' =>       false,
                    'widget' =>         'single_text',
                ]
            )
            ->add(
                'logged_last_2',
                DateType::class,
                [
                    'attr' =>           [ 'class' => 'js-datepicker' ],
                    'data' =>           $options['logged_last_2'],
                    'html5' =>          false,
                    'label' =>          '',
                    'required' =>       false,
                    'widget' =>         'single_text',
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'attr' =>           [ 'class' => 'button small' ],
                    'label' =>          'Go'
                ]
            )
            ->add(
                'clear',
                ResetType::class,
                [
                    'attr' =>           [ 'class' => 'button small' ],
                    'label' =>          'Clear'
                ]
            );

        if ($options['isAdmin']) {
            $formBuilder
                ->add(
                    'admin_mode',
                    ChoiceType::class,
                    [
                        'choices' =>        [
                            '0 - Default View seen by regular visitors' =>   '',
                            '1 - Listing view of unlogged signals for all systems' =>   '1',
                            '2 - Listing view of logged and unlogged from all systems' =>    '2'
                        ],
                        'data' =>           $options['admin_mode'],
                        'label' =>          'Admin Mode',
                        'required' =>       false
                    ]
                );
        }
        $this->addPaging($formBuilder, $options);

        return $formBuilder->getForm();
    }
}
