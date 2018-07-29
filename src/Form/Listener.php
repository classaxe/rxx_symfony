<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-18
 * Time: 12:08
 */

namespace App\Form;

use App\Repository\CountryRepository;
use App\Repository\State;

use App\Repository\RegionRepository;
use App\Repository\TypeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class Listeners
 * @package App\Form
 */
class Listener extends AbstractType
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
     * Listeners constructor.
     * @param CountryRepository $country
     * @param RegionRepository $region
     */
    public function __construct(CountryRepository $country, RegionRepository $region, TypeRepository $type)
    {
        $this->country = $country;
        $this->region = $region;
        $this->type = $type;
    }

    public function getFieldGroups()
    {
        return [
            'Contact Details' =>    [
                'name',
                'callsign',
                'email',
                'website'
            ],
            'Location' =>   [
//                'town',
//                'sp',
                'itu',
//                'gsq',
//                'timezone',
//                'primary',
//                'mapX',
//                'mapY'
            ],
//            'Other' =>  [
//                'notes',
//                'equipment'
//            ]
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
                    'data' => $options['id']
                ]
            )
            ->add(
                'name',
                TextType::class,
                [
                    'label'     => 'Name',
                    'data'      => $options['name'],
                    'block_name'     => 'Contact Details'
                ]
            )
            ->add(
                'callsign',
                TextType::class,
                [
                    'label'     => 'Callsign',
                    'data'      => $options['callsign']
                ]
            )
            ->add(
                'email',
                TextType::class,
                [
                    'label'     => 'Email Address',
                    'data'      => $options['email']
                ]
            )
            ->add(
                'website',
                TextType::class,
                [
                    'label'     => 'Website',
                    'data'      => $options['website']
                ]
            )
//            ->add(
//                'sp',
//                ChoiceType::class,
//                [
//                    'label'     => 'State / Province',
//                    'choices'   => $this->sp->getMatchingOptions(),
//                    'data'      => $options['itu']
//                ]
//            )
            ->add(
                'itu',
                ChoiceType::class,
                [
                    'label'     => 'Country',
                    'choices'   => $this->country->getMatchingOptions(),
                    'data'      => $options['itu']
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Save'
                ]
            );

        return $formBuilder->getForm();
    }
}
