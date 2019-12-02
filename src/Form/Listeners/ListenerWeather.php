<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-18
 * Time: 12:08
 */

namespace App\Form\Listeners;

use App\Repository\IcaoRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class Listeners
 * @package App\Form
 */
class ListenerWeather extends AbstractType
{
    /**
     * @var IcaoRepository
     */
    private $icao;

    public function __construct(
        IcaoRepository $icaoRepository
    ) {
        $this->icao =   $icaoRepository;
    }

    public function getFieldGroups()
    {
        return [
            'Weather Report Details' =>    [
                'icao',
                'hours',
                'qnh',
                'raw',
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
                'icao',
                ChoiceType::class,
                [
                    'label'         => '',
                    'choice_translation_domain' => false,
                    'choices'       => $this->icao->getMatchingOptions($options['lat'], $options['lon'], $options['limit']),
                    'data'          => $options['icao'],
                ]
            )
            ->add(
                'hours',
                IntegerType::class,
                [
                    'label'         =>  'Hours',
                    'data'          =>  $options['hours'],
                    'empty_data'    =>  '',
                    'attr'          =>  ['min' => 0]

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
                'raw',
                ButtonType::class,
                [
                    'label'         => 'Metar - Raw',
                    'attr'          => [
                        'class' =>      'button small',
                        'onclick' =>    'getMetar(0)'
                    ]
                ]
            )
            ->add(
                'decoded',
                ButtonType::class,
                [
                    'label'         => 'Metar - Decoded',
                    'attr'          => [
                        'class' =>      'button small',
                        'onclick' =>    'getMetar(1)'
                    ]
                ]
            )
        ;

        return $formBuilder->getForm();
    }
}
