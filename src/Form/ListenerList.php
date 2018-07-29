<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-18
 * Time: 12:08
 */

namespace App\Form;

use App\Repository\CountryRepository;
use App\Repository\RegionRepository;
use App\Repository\TypeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class ListenerList
 * @package App\Form
 */
class ListenerList extends AbstractType
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
     * ListenerList constructor.
     * @param CountryRepository $country
     * @param RegionRepository $region
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

        $formBuilder
            ->add(
                'sort',
                HiddenType::class,
                [
                    'data' => 'name'
                ]
            )
            ->add(
                'order',
                HiddenType::class,
                [
                    'data' => 'a'
                ]
            )
            ->add(
                'filter',
                TextType::class,
                [
                    'label' => 'Search For',
                    'help' => '&nbsp;'
                ]
            )
            ->add(
                'types',
                ChoiceType::class,
                [
                    'label' => 'Show Counts',
                    'expanded' => true,
                    'multiple' => true,
                    'choices' => $this->type->getAll(),
                    'choice_attr' => function ($choiceValue, $key, $value) {
                        return ['class' => strToLower($value)];
                    }
                ]
            )
            ->add(
                'country',
                ChoiceType::class,
                [
                    'label' => 'Country',
                    'choices' =>
                        $this->country->getMatchingOptions($system, $region, true),
                ]
            );

        if ($system=='rww') {
            $formBuilder
                ->add(
                    'region',
                    ChoiceType::class,
                    [
                        'label' => 'Region',
                        'choices' => $this->region->getAllOptions(),
                    ]
                );
        }

        $formBuilder
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Go'
                ]
            );

        return $formBuilder->getForm();
    }
}
