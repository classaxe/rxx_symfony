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
                'status',
                ChoiceType::class,
                [
                    'choices' =>        [
                        'All' => '',
                        'Active' => 'Y',
                        'Inactive' => 'N',
                        'Active (with logs in last 30 days)' => '30d',
                        'Active (with logs in last 3 months)' => '3m',
                        'Active (with logs in last 6 months)' => '6m',
                        'Active (with logs in last year)' => '1y',
                        'Active (with logs in last two years)' => '2y',
                        'Active (with logs in last five years)' => '5y',
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
                            'style' => 'width: 3em;'
                        ],
                        'data' =>           $options['rxx_id'],
                        'label' =>          'RXX ID',
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
                                '' => '',
                                'No' => 'N',
                                'Yes' => 'Y'
                            ],
                            'data' => $options['has_logs'],
                            'label' => 'Has Logs',
                            'required' => false
                        ]
                    );
            }
            $formBuilder
                ->add(
                    'has_map_pos',
                    ChoiceType::class,
                    [
                        'choices' =>        [
                            '' =>      '',
                            'No' =>    'N',
                            'Yes' =>   'Y'
                        ],
                        'data' =>           $options['has_map_pos'],
                        'label' =>          'Has Map Pos',
                        'required' =>       false
                    ]
                );
        }
        $this->addPaging($formBuilder, $options);

        return $formBuilder->getForm();
    }
}
