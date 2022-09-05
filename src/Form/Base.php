<?php
namespace App\Form;

use App\Repository\CountryRepository;
use App\Repository\IcaoRepository;
use App\Repository\ListenerRepository;
use App\Repository\PaperRepository;
use App\Repository\RegionRepository;
use App\Repository\StateRepository;
use App\Repository\TimeRepository;
use App\Repository\TypeRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class Base
 * @package App\Form
 */
class Base extends AbstractType
{
    protected $translator;

    protected $countryRepository;
    protected $icaoRepository;
    protected $listenerRepository;
    protected $paperRepository;
    protected $regionRepository;
    protected $stateRepository;
    protected $timeRepository;
    protected $typeRepository;

    /**
     * Base constructor.
     * @param TranslatorInterface $translator
     *
     * Auto-wire these repositories:
     * @param CountryRepository $countryRepository
     * @param IcaoRepository $icaoRepository
     * @param ListenerRepository $listenerRepository
     * @param PaperRepository $paperRepository
     * @param RegionRepository $regionRepository
     * @param StateRepository $stateRepository
     * @param TimeRepository $timeRepository
     * @param TypeRepository $typeRepository
     */
    public function __construct(
        TranslatorInterface $translator,

        CountryRepository $countryRepository,
        IcaoRepository $icaoRepository,
        ListenerRepository $listenerRepository,
        PaperRepository $paperRepository,
        RegionRepository $regionRepository,
        StateRepository $stateRepository,
        TimeRepository $timeRepository,
        TypeRepository $typeRepository
    ) {
        $this->translator =         $translator;

        $this->countryRepository =  $countryRepository;
        $this->icaoRepository =     $icaoRepository;
        $this->listenerRepository = $listenerRepository;
        $this->paperRepository =    $paperRepository;
        $this->regionRepository =   $regionRepository;
        $this->stateRepository =    $stateRepository;
        $this->timeRepository =     $timeRepository;
        $this->typeRepository =     $typeRepository;
    }

    /**
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     * @return FormInterface
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options)
    {
        $this->addPaging($formBuilder, $options);
        $this->addSorting($formBuilder, $options);
        return $formBuilder->getForm();
    }

    /**
     * @param FormBuilderInterface $formBuilder
     */
    public function addPaging(FormBuilderInterface $formBuilder, array $options)
    {
        $formBuilder
            ->add(
                'limit',
                TextType::class,
                [
                    'attr' => [
                        'style' => 'display:none'
                    ],
                    'data' =>       $options['limit'],
                    'label' =>      'Show',
                ]
            )
            ->add(
                'page',
                TextType::class,
                [
                    'attr' =>       [
                        'style' => 'display:none'
                    ],
                    'data' =>       $options['page'],
                    'label' =>      ' ',
                ]
            )
            ->add(
                'prev',
                ButtonType::class,
                [
                    'attr' => [
                        'class' => 'button tiny',
                        'style' => 'display:none'
                    ],
                    'label' =>      '<',
                ]
            )
            ->add(
                'next',
                ButtonType::class,
                [
                    'attr' => [
                        'class' => 'button tiny',
                        'style' => 'display:none'
                    ],
                    'label' =>      '>',
                ]
            );
    }

    /**
     * @param FormBuilderInterface $formBuilder
     */
    public function addPagingBottom(FormBuilderInterface $formBuilder, array $options)
    {
        $formBuilder

            ->add(
                'prevbottom',
                ButtonType::class,
                [
                    'attr' => [
                        'class' => 'button tiny',
                        'style' => 'display:none',
                        'onclick' => '$("#form_prev").trigger("click")'
                    ],
                    'label' =>      '<',
                ]
            )
            ->add(
                'nextbottom',
                ButtonType::class,
                [
                    'attr' => [
                        'class' => 'button tiny',
                        'style' => 'display:none',
                        'onclick' => '$("#form_next").trigger("click")'
                    ],
                    'label' =>      '>',
                ]
            );

    }

    /**
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     */
    protected function addSorting(FormBuilderInterface $formBuilder, array $options)
    {
        $formBuilder
            ->add(
                'sort',
                HiddenType::class,
                [
                    'data' => $options['sort']
                ]
            )
            ->add(
                'order',
                HiddenType::class,
                [
                    'data' => $options['order']
                ]
            );
    }
}
