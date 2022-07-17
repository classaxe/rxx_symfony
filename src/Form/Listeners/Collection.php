<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-18
 * Time: 12:08
 */

namespace App\Form\Listeners;

use App\Form\Base;

use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;

/**
 * Class Listeners
 * @package App\Form
 */
class Collection extends Base
{
    /**
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     * @return FormInterface|void
     */
    public function buildForm(
        FormBuilderInterface $formBuilder,
        array $options
    ) {
        $system =   $options['system'];
        $region =   $options['region'];

        $this->addPaging($formBuilder, $options);
        $this->addPagingBottom($formBuilder, $options);
        $this->addSorting($formBuilder, $options);

        $formBuilder
            ->add(
                'show',
                HiddenType::class,
                [
                    'data' =>           $options['show']
                ]
            )
            ->add(
                'q',
                TextType::class,
                [
                    'data' =>           $options['q'],
                    'label' =>          'Search For',
                    'required' =>       false
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
                    'multiple' =>       true,
                ]
            )
            ->add(
                'country',
                ChoiceType::class,
                [
                    'choices' => $this->countryRepository->getMatchingOptions(
                        $system, $region, true, false,true
                    ),
                    'data' =>           $options['country'],
                    'label' =>          'Country',
                    'required' =>       false
                ]
            )
            ->add(
                'timezone',
                ChoiceType::class,
                [
                    'choices' =>        $this->timeRepository->getAllOptions(true),
                    'choice_translation_domain' => false,
                    'data' =>           $options['timezone'],
                    'label' =>          'Timezone',
                    'required' =>       true
                ]
            )
            ->add(
                'loctype',
                ChoiceType::class,
                [
                    'attr' =>           [ 'title' => 'Show Primary Locations, Secondary Locations or all?' ],
                    'choices' =>        [
                        '(All)' => '',
                        'Primary (Home)' => 'Y',
                        'Secondary (Other)' => 'N',
                    ],
                    'data' =>           $options['multiop'],
                    'expanded' =>       true,
                    'label' =>          'Location Type',
                    'required' =>       false
                ]
            )
            ->add(
                'multiop',
                ChoiceType::class,
                [
                    'attr' =>           [ 'title' => 'Does the listening location allow for multiple operators?' ],
                    'choices' =>        [
                        '(All)' => '',
                        'Single Operator' => 'N',
                        'Multi-Operator (e.g. Kiwi)' => 'Y',
                    ],
                    'data' =>           $options['multiop'],
                    'expanded' =>       true,
                    'label' =>          'Operators',
                    'required' =>       false
                ]
            )
            ->add(
                'status',
                ChoiceType::class,
                [
                    'choices' =>        [
                        'All' => '',
                        'Active' => 'Y',
                        'Inactive' => 'N',
                        'Active (with logs in last 30 days)' => '30D',
                        'Active (with logs in last 3 months)' => '3M',
                        'Active (with logs in last 6 months)' => '6M',
                        'Active (with logs in last year)' => '1Y',
                        'Active (with logs in last two years)' => '2Y',
                        'Active (with logs in last five years)' => '5Y',
                    ],
                    'data' =>           $options['status'],
                    'label' =>          'Status',
                    'required' =>       false
                ]
            )
            ->add(
                'equipment',
                TextType::class,
                [
                    'data' =>           $options['equipment'],
                    'label' =>          'Equipment',
                    'required' =>       false
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' =>          'Go',
                    'attr' =>           [ 'class' => 'button small']
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
                    'label' =>          'Clear',
                    'attr' =>           [ 'class' => 'button small' ]
                ]
            );


        if ($system=='rww') {
            $formBuilder
                ->add(
                    'region',
                    ChoiceType::class,
                    [
                        'choices' =>        $this->regionRepository->getAllOptions(),
                        'data' =>           $options['region'],
                        'label' =>          'Region',
                        'required' =>       false
                    ]
                );
        }

        if ($options['isAdmin']) {
            $formBuilder
                ->add(
                    'rxx_id',
                    TextType::class,
                    [
                        'attr' => [
                            'placeholder' => 'id1, id2, id3 ...',
                        ],
                        'data' =>           $options['rxx_id'],
                        'label' =>          'RXX ID(s)',
                        'required' =>       false
                    ]
                );
            $formBuilder
                ->add(
                    'notes',
                    TextType::class,
                    [
                        'data' =>           $options['notes'],
                        'label' =>          'Notes',
                        'required' =>       false
                    ]
                );
            if ($system=='rww') {
                $formBuilder
                    ->add(
                        'has_logs',
                        ChoiceType::class,
                        [
                            'choices' => [
                                '(All)' => '-',
                                'No' => 'N',
                                'Yes (Default)' => 'Y',
                            ],
                            'data' => $options['has_logs'],
                            'expanded' => true,
                            'label' => 'Has Logs (RWW)',
                            'required' => true
                        ]
                    );
            }
            $formBuilder
                ->add(
                    'has_map_pos',
                    ChoiceType::class,
                    [
                        'choices' =>        [
                            '(All)' =>      '',
                            'No' =>         'N',
                            'Yes' =>        'Y'
                        ],
                        'data' =>           $options['has_map_pos'],
                        'expanded' =>       true,
                        'label' =>          'Has Map Pos',
                        'required' =>       true
                    ]
                );
        }
        $this->addPaging($formBuilder, $options);

        return $formBuilder->getForm();
    }
}
