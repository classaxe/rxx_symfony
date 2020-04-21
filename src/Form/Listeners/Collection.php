<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-18
 * Time: 12:08
 */

namespace App\Form\Listeners;

use App\Form\Base;
use App\Repository\CountryRepository;
use App\Repository\RegionRepository;
use App\Repository\TimeRepository;
use App\Repository\TypeRepository;
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
     * @var CountryRepository
     */
    private $country;

    /**
     * @var RegionRepository
     */
    private $region;

    /**
     * @var TimeRepository
     */
    private $timeRepository;
    /**
     * @var TypeRepository
     */
    private $type;

    /**
     * Listeners constructor.
     * @param CountryRepository $country
     * @param RegionRepository $region
     * @param TimeRepository $timeRepository
     * @param TypeRepository $type
     */
    public function __construct(
        CountryRepository $country,
        RegionRepository $region,
        TimeRepository $timeRepository,
        TypeRepository $type
    ) {
        $this->country = $country;
        $this->region = $region;
        $this->timeRepository = $timeRepository;
        $this->type = $type;
    }

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
                    'choices' =>        $this->type->getAllChoices(true),
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
                    'choices' => $this->country->getMatchingOptions(
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
                'submit',
                SubmitType::class,
                [
                    'label' =>          'Go',
                    'attr' =>           [ 'class' => 'button small']
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
                        'choices' =>        $this->region->getAllOptions(),
                        'data' =>           $options['region'],
                        'label' =>          'Region',
                        'required' =>       false
                    ]
                );
        }

        if ($options['isAdmin']) {
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
