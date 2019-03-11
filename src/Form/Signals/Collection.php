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
     * @param RegionRepository $region
     * @param TypeRepository $type
     */
    public function __construct(CountryRepository $country, RegionRepository $region, TypeRepository $type)
    {
        $this->country = $country;
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
                    'choices' => [
                        'AND' => 'AND',
                        'OR' =>  'OR',
                    ],
                    'placeholder' => false,
                    'label' => 'Combiner',
                    'required' => false
                ]
            )
            ->add(
                'countries',
                TextType::class,
                [
                    'label' => 'Countries',
                    'required' => false
                ]
            )
            ->add(
                'region',
                ChoiceType::class,
                [
                    'choices' => $this->region->getAllOptions(),
                    'label' => 'Region',
                    'required' =>   false
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
