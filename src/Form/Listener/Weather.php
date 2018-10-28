<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-18
 * Time: 12:08
 */

namespace App\Form\Listener;

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
class Weather extends AbstractType
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
            '' =>    [
                'name',
                'hours',
                'qnh',
                'metar',
                'decoded'
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
                    'label'         => ' ',
                    'data'          => $options['name'],
                    'empty_data'    => '',
                ]
            )
            ->add(
                'hours',
                TextType::class,
                [
                    'label'         => 'Hours',
                    'data'          => $options['hours'],
                    'empty_data'    => '',
                ]
            )
            ->add(
                'qnh',
                SubmitType::class,
                [
                    'label'         => 'QNH',
                    'attr'          => [
                        'class' =>      'button small'
                    ]
                ]
            )
            ->add(
                'metar',
                SubmitType::class,
                [
                    'label'         => 'Metar',
                    'attr'          => [
                        'class' =>      'button small'
                    ]
                ]
            )
            ->add(
                'decoded',
                SubmitType::class,
                [
                    'label'         => 'Decoded',
                    'attr'          => [
                        'class' =>      'button small'
                    ]
                ]
            )
        ;

        return $formBuilder->getForm();
    }
}
