<?php
namespace App\Form\Logs;

use App\Repository\CountryRepository;
use App\Repository\RegionRepository;
use App\Repository\StateRepository;
use App\Repository\TimeRepository;
use App\Repository\TypeRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class Listeners
 * @package App\Form
 */
class Log extends AbstractType
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
     * @param StateRepository $sp
     * @param TimeRepository $timeRepository
     * @param TypeRepository $type
     */
    public function __construct(
        CountryRepository $country,
        RegionRepository $region,
        StateRepository $sp,
        TimeRepository $timeRepository,
        TypeRepository $type
    ) {
        $this->country =    $country;
        $this->region =     $region;
        $this->sp =         $sp;
        $this->timeRepository = $timeRepository;
        $this->type =       $type;
    }

    /**
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     * @return FormInterface|void
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options)
    {
        $formBuilder
            ->add(
                'id',
                HiddenType::class,
                [
                    'data' =>           $options['id']
                ]
            )
            ->add(
                '_close',
                HiddenType::class,
                [
                    'data' =>       0
                ]
            )
            ->add(
                'signalId',
                HiddenType::class,
                [
                    'data' =>           $options['signalId'],
                    'empty_data' =>     '',
                    'label' =>          'Signal',
                ]
            )
            ->add(
                'listenerId',
                HiddenType::class,
                [
                    'data' =>           $options['listenerId'],
                    'empty_data' =>     '',
                    'label' =>          'Listener',
                ]
            )
            ->add(
                'dxKm',
                TextType::class,
                [
                    'attr' =>   [ 'readonly' => 'readonly' ],
                    'data' =>           $options['dxKm'],
                    'empty_data' =>     '',
                    'label' =>          'DX (KM)',
                ]
            )
            ->add(
                'dxMiles',
                TextType::class,
                [
                    'attr' =>   [ 'readonly' => 'readonly' ],
                    'data' =>           $options['dxMiles'],
                    'empty_data' =>     '',
                    'label' =>          'DX (Miles)',
                ]
            )
            ->add(
                'date',
                TextType::class,
                [
                    'attr' =>   [ 'class' => 'js-datepicker' ],
                    'data' =>           $options['date'],
                    'empty_data' =>     '',
                    'label' =>          'Date',
                ]
            )
            ->add(
                'daytime',
                CheckboxType::class,
                [
                    'data' =>           $options['daytime'],
                    'empty_data' =>     '',
                    'label' =>          'Daytime',
                    'required' =>       false
                ]
            )
            ->add(
                'time',
                TextType::class,
                [
                    'data' =>           $options['time'],
                    'empty_data' =>     '',
                    'label' =>          'UTC',
                    'required' =>       false
                ]
            )
            ->add(
                'format',
                TextType::class,
                [
                    'data' =>           $options['format'],
                    'empty_data' =>     '',
                    'label' =>          'Format',
                    'required' =>       false
                ]
            )
            ->add(
                'sec',
                TextType::class,
                [
                    'data' =>           $options['sec'],
                    'empty_data' =>     '',
                    'label' =>          'Cycle Time',
                    'required' =>       false
                ]
            )
            ->add(
                'lsbApprox',
                CheckboxType::class,
                [
                    'data' =>           $options['lsbApprox'],
                    'empty_data' =>     '',
                    'label' =>          'LSB Approx',
                    'required' =>       false
                ]
            )
            ->add(
                'lsb',
                TextType::class,
                [
                    'data' =>           $options['lsb'],
                    'empty_data' =>     '',
                    'label' =>          'LSB',
                    'required' =>       false
                ]
            )
            ->add(
                'usbApprox',
                CheckboxType::class,
                [
                    'data' =>           $options['usbApprox'],
                    'empty_data' =>     '',
                    'label' =>          'USB Approx',
                    'required' =>       false
                ]
            )
            ->add(
                'usb',
                TextType::class,
                [
                    'data' =>           $options['usb'],
                    'empty_data' =>     '',
                    'label' =>          'USB',
                    'required' =>       false
                ]
            )

/*

            'dxKm' =>       $log->getDxKm(),
            'dxMiles' =>    $log->getDxMiles(),
            'format' =>     $log->getFormat(),
            'heardIn' =>    $log->getHeardIn(),
            'listenerId' => $log->getListenerId(),
            'lsb' =>        $log->getLsb(),
            'lsbApprox' =>  $log->getLsbApprox(),
            'region' =>     $log->getRegion(),
            'sec' =>        $log->getSec(),
            'usb' =>        $log->getUsb(),
            'usbApprox' =>  $log->getUsbApprox(),

*/
/*
            ->add(
                'callsign',
                TextType::class,
                [
                    'data' =>           $options['callsign'],
                    'empty_data' =>     '',
                    'label' =>          'Callsign',
                    'required' =>       false
                ]
            )
            ->add(
                'website',
                TextType::class,
                [
                    'data' =>           $options['website'],
                    'empty_data' =>     '',
                    'label' =>          'Website',
                    'required' =>       false
                ]
            )
            ->add(
                'qth',
                TextType::class,
                [
                    'attr' => [
                        'onchange' =>   "try{setCustomValidity('')}catch(e){}",
                        'oninvalid' =>  "setCustomValidity('Please enter this listener\'s approximate location')"
                    ],
                    'data' =>           $options['qth'],
                    'empty_data' =>     '',
                    'label' =>          'Town / City',
                ]
            )
            ->add(
                'sp',
                ChoiceType::class,
                [
                    'choices' =>        $this->sp->getMatchingOptions(),
                    'data' =>           $options['sp'],
                    'label' =>          'State / Prov',
                    'required' =>       false
                ]
            )
            ->add(
                'itu',
                ChoiceType::class,
                [
                    'choices' =>        $this->country->getMatchingOptions(),
                    'data' =>           $options['itu'],
                    'label' =>          'Country',
                ]
            )
            ->add(
                'gsq',
                TextType::class,
                [
                    'attr' => [
                        'maxlength' =>  6,
                        'onchange' =>   "try{setCustomValidity('')}catch(e){}",
                        'oninvalid' =>  "setCustomValidity('Please provide the grid square so we can calculate distances')",
                        'size' =>       6,
                        'style' =>      "width: 6em"
                    ],
                    'data' =>           $options['gsq'],
                    'empty_data' =>     '',
                    'label' =>          'Grid Square',
                ]
            )
            ->add(
                'primaryQth',
                ChoiceType::class,
                [
                    'attr' => [
                        'style' =>      "width: 6em"
                    ],
                    'choices' => [
                        'Yes' =>        1,
                        'No' =>         0,
                    ],
                    'data' =>           $options['primaryQth'],
                    'label' =>          'Primary Location',
                ]
            )
            ->add(
                'timezone',
                ChoiceType::class,
                [
                    'choices' =>        $this->timeRepository->getAllOptions(),
                    'choice_translation_domain' => false,
                    'data' =>           $options['timezone'],
                    'label' =>          'Timezone',
                    'required' =>       false
                ]
            )
            ->add(
                'equipment',
                TextareaType::class,
                [
                    'attr' => [
                        'cols' =>       80,
                        'rows' =>       5,
                    ],
                    'data' =>           html_entity_decode($options['equipment']),
                    'empty_data' =>     '',
                    'label' =>          'Equipment',
                    'required' =>       false
                ]
            )
            ->add(
                'notes',
                TextareaType::class,
                [
                    'attr' => [
                        'cols' =>       80,
                        'rows' =>       5,
                    ],
                    'data' =>           $options['notes'],
                    'empty_data' =>     '',
                    'label' =>          'Notes',
                    'required' =>       false
                ]
            )
*/
            ->add(
                'close',
                ButtonType::class,
                [
                    'attr' => [
                        'class' =>      'button small',
                        'onclick' =>    'window.close()'
                    ],
                    'label' =>          'Close',
                ]
            )
            ->add(
                'saveClose',
                SubmitType::class,
                [
                    'attr' => [
                        'class' => 'button small'
                    ],
                    'label' => 'Save + Close',
                ]
            );
        return $formBuilder->getForm();
    }
}
