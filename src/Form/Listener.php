<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-18
 * Time: 12:08
 */

namespace App\Form;

use App\Repository\CountryRepository;
use App\Repository\StateRepository;

use App\Repository\RegionRepository;
use App\Repository\TypeRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
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
            'Contact Details' =>    [
                'name',
                'callsign',
                ($isAdmin ? 'email' : ''),
                'website'
            ],
            'Location' =>   [
                'qth',
                'sp',
                'itu',
                'gsq',
//                'timezone',
//                'primary',
//                'mapX',
//                'mapY'
            ],
//            'Other' =>  [
//                'notes',
//                'equipment'
//            ],
            '' => [
                'print',
                'close',
                ($isAdmin ? 'save' : '')
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
        $isAdmin = $options['isAdmin'];
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
                    'label'         => 'Name',
                    'data'          => $options['name'],
                    'block_name'    => 'Contact Details',
                    'disabled'      => !$isAdmin
                ]
            )
            ->add(
                'callsign',
                TextType::class,
                [
                    'label'         => 'Callsign',
                    'data'          => $options['callsign'],
                    'disabled'      => !$isAdmin
                ]
            )
            ->add(
                'email',
                TextType::class,
                [
                    'label' => 'Email Address',
                    'data' => $options['email'],
                    'disabled' => !$isAdmin
                ]
            )
            ->add(
                'website',
                TextType::class,
                [
                    'label'         => 'Website',
                    'data'          => $options['website'],
                    'disabled'      => !$isAdmin
                ]
            )
            ->add(
                'qth',
                TextType::class,
                [
                    'label'         => 'Town / City',
                    'data'          => $options['qth'],
                    'disabled'      => !$isAdmin,
                ]
            )
            ->add(
                'sp',
                ChoiceType::class,
                [
                    'label'         => 'State / Prov',
                    'choices'       => $this->sp->getMatchingOptions(),
                    'data'          => $options['itu'],
                    'disabled'      => !$isAdmin
                ]
            )
            ->add(
                'itu',
                ChoiceType::class,
                [
                    'label'         => 'Country',
                    'choices'       => $this->country->getMatchingOptions(),
                    'data'          => $options['itu'],
                    'disabled'      => !$isAdmin
                ]
            )
            ->add(
                'gsq',
                TextType::class,
                [
                    'label'         => 'Grid Square',
                    'data'          => $options['gsq'],
                    'disabled'      => !$isAdmin,
                    'attr'          => ['size' => '6', 'maxlen' => 6, 'style' => "width: 6em"]
                ]
            )
            ->add(
                'print',
                ButtonType::class,
                [
                    'label'         => 'Print...',
                    'attr'          => [ 'class' => 'button small']
                ]
            )
            ->add(
                'close',
                ButtonType::class,
                [
                    'label'         => 'Close',
                    'attr'          => [ 'class' => 'button small']
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label'         => 'Save',
                    'attr'          => [ 'class' => 'button small']
                ]
            )
        ;

        return $formBuilder->getForm();
    }
}
