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
use App\Repository\TypeRepository;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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

    private $options;
    /**
     * @var RegionRepository
     */
    private $region;

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
        TypeRepository $type
    ) {
        $this->country = $country;
        $this->region = $region;
        $this->type = $type;
    }

    /**
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface|void
     */
    public function buildForm(
        FormBuilderInterface $formBuilder,
        array $options
    ) {
        $this->options = $options;
        $system =   $options['system'];
        $region =   $options['region'];

        $this->addPaging($formBuilder, $options);
        $this->addSorting($formBuilder, $options);

        $formBuilder
            ->add(
                'filter',
                TextType::class,
                [
                    'label' =>      'Search For',
                    'required' =>   false
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
                    'attr' =>       [ 'legend' => 'Show Counts' ],
                ]
            )
            ->add(
                'country',
                ChoiceType::class,
                [
                    'choices' => $this->country->getMatchingOptions(
                        $system,
                        $region,
                        true,
                        false,
                        true
                    ),
                    'label' => 'Country',
                    'required' => false
                ]
            );

        if ($system=='rww') {
            $formBuilder
                ->add(
                    'region',
                    ChoiceType::class,
                    [
                        'choices' => $this->region->getAllOptions(),
                        'label' => 'Region',
                        'required' =>   false
                    ]
                );
        }

        $formBuilder
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label'         => 'Go',
                    'attr'          => [ 'class' => 'button small']
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
