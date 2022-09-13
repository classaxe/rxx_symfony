<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-18
 * Time: 12:08
 */

namespace App\Form\Signals;

use App\Form\Base;

use App\Repository\SignalRepository;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
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
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface|void
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options)
    {
        $system =   $options['system'];
        $this->addPaging($formBuilder, $options);
        $this->addPagingBottom($formBuilder, $options);
        $this->addSorting($formBuilder, $options);

        $i18n = $this->translator;

        // Main Visible Section
        $formBuilder
            ->setAction($options['url'])
            ->add(
                'show',
                HiddenType::class,
                [
                    'data' =>           $options['show']
                ]
            )
            // Used with exports
            ->add(
                'filename',
                HiddenType::class,
                [
                    'data' =>           ''
                ]
            )
            // Used in Seeklist mode
            ->add(
                'paper',
                HiddenType::class,
                [
                    'data' =>           $options['paper']
                ]
            )
            ->add(
                'type',
                ChoiceType::class,
                [
                    'attr' =>           [ 'legend' => 'Signal Types' ],
                    'choices' =>        $this->typeRepository->getAllChoices(true),
                    'choice_attr' =>    function ($value) { return ['class' => strToLower($value)]; },
                    'data' =>           $options['type'],
                    'expanded' =>       true,
                    'label' =>          false,
                    'multiple' =>       true
                ]
            )
            ->add(
                'call',
                TextType::class,
                [
                    'attr' => [
                        'placeholder' => 'XXX-khz',
                        'title' => 'TIP: enter call and frequency as CALL-nnn to set frequency fields as well'
                    ],
                    'data' =>           $options['call'],
                    'label' =>          '&#128712; Call / ID',
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
                    'choices' =>        [ 'All' => '', 'Only 1 KHz' => '1', 'Not 1 KHz' =>  '2', 'Only 10 KHz' => '3', 'Not 10 KHz' => '4' ],
                    'data' =>           $options['channels'],
                    'label' =>          'Chan.',
                    'required' =>       false
                ]
            )
            ->add(
                'states',
                TextType::class,
                [
                    'attr' => [
                        'placeholder' => 'SP code list'
                    ],
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
                    'attr' => [
                        'placeholder' => 'ITU code list'
                    ],
                    'data' =>           $options['countries'],
                    'label' =>          'Countries',
                    'required' =>       false
                ]
            )
            ->add(
                'region',
                ChoiceType::class,
                [
                    'choices' =>        $this->regionRepository->getAllOptions(),
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
                'notes',
                TextType::class,
                [
                    'data' =>           $options['notes'],
                    'label' =>          'Notes',
                    'required' =>       false
                ]
            )
            ->add(
                'recently',
                ChoiceType::class,
                [
                    'choices' =>        [ 'Logged' => 'logged', 'Not Logged' =>   'unlogged' ],
                    'data' =>           $options['recently'],
                    'label' =>          'Most Recently',
                    'required' =>       false
                ]
            )
            ->add(
                'within',
                ChoiceType::class,
                [
                    'choices' =>        SignalRepository::withinPeriods,
                    'data' =>           $options['within'],
                    'label' =>          'In Last',
                    'required' =>       false
                ]
            )
            ->add(
                'active',
                ChoiceType::class,
                [
                    'choices' =>        [ 'All' => '', 'Active' => '1', 'Inactive' =>   '2' ],
                    'data' =>           $options['active'],
                    'label' =>          'Status',
                    'required' =>       false
                ]
            )

            // Loggings Section:
            ->add(
                'heard_in',
                TextType::class,
                [
                    'attr' => [
                        'placeholder' => 'ITU / SP values'
                    ],
                    'data' =>           $options['heard_in'],
                    'label' =>          'Heard in SP / ITU',
                    'required' =>       false
                ]
            )
            ->add(
                'heard_in_mod',
                ChoiceType::class,
                [
                    'choices' =>        [ 'Any' => '', 'All' => 'all' ],
                    'data' =>           $options['heard_in_mod'],
                    'expanded' =>       true,
                    'placeholder' =>    false,
                    'required' =>       false,
                ]
            )
            ->add(
                'rww_focus',
                ChoiceType::class,
                [
                    'choices' =>        $this->regionRepository->getAllOptions(false),
                    'data' =>           $options['rww_focus'],
                    'label' =>          'Heard in Region',
                    'required' =>       false
                ]
            )
            ->add(
                'listener',
                ChoiceType::class,
                [
                    'attr' =>           [ 'class' => 'multiple' ],
                    'choices' =>        $this->listenerRepository->getAllOptions(
                        $system,
                        null,
                        $i18n->trans('Anyone (or enter values in "Heard here" box)'),
                        false
                    ),
                    'choice_translation_domain' => false,
                    'data' =>           $options['listener'],
                    'expanded' =>       false,
                    'label' =>          'Listener(s)',
                    'multiple' =>       true,
                    'required' =>       false,
                ]
            )
            ->add(
                'listener_filter',
                ChoiceType::class,
                [
                    'choices' =>        [
                        'All Locations' => '',
                        'Single Operator' => 'N',
                        'Multi Operator' => 'Y'
                    ],
                    'data' =>           $options['listener_filter'],
                    'expanded' =>       true,
                    'label' =>          'Location Types',
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

            // Customise Section
            ->add(
                'personalise',
                ChoiceType::class,
                [
                    'choices' =>        $this->listenerRepository->getAllOptions($system,null, $i18n->trans('(None specified)'), true),
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
                    'choices' =>        [ 'Rel.' => '', 'Abs.' =>   '1' ],
                    'data' =>           $options['offsets'],
                    'expanded' =>       true,
                    'label' =>          'Display Offsets',
                    'required' =>       false
                ]
            )
            ->add(
                'hidenotes',
                ChoiceType::class,
                [
                    'attr' =>           [ 'legend' => 'Notes' ],
                    'choices' =>        [ 'N' => '1', 'Y' =>   '' ],
                    'data' =>           $options['hidenotes'],
                    'expanded' =>       true,
                    'label' =>          false,
                    'required' =>       false
                ]
            )
            ->add(
                'morse',
                ChoiceType::class,
                [
                    'attr' =>           [ 'legend' => 'Morse' ],
                    'choices' =>        [ 'N' => '', 'Y' =>   '1' ],
                    'data' =>           $options['morse'],
                    'expanded' =>       true,
                    'label' =>          false,
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
                'submit',
                SubmitType::class,
                [
                    'attr' =>           [ 'class' => 'button small' ],
                    'label' =>          'Go'
                ]
            )
            ->add(
                'save',
                ButtonType::class,
                [
                    'attr' =>           [ 'class' => 'button small' ],
                    'label' =>          'Set Default'
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
