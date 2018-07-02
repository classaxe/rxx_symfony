<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-18
 * Time: 12:08
 */

namespace App\Form;

use App\Repository\ItuRepository;
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
class Listener extends AbstractType
{
    /**
     * @var ItuRepository
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
     * @param ItuRepository $country
     * @param RegionRepository $region
     */
    public function __construct(ItuRepository $country, RegionRepository $region, TypeRepository $type)
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
        $id =       $options['id'];

        $formBuilder
            ->add(
                'id',
                HiddenType::class,
                [
                    'data' => $id
                ]
            )
            ->add(
                'country',
                ChoiceType::class,
                [
                    'label' => 'Country',
                    'choices' => $this->country->getAllOptionsForSystem(),
                ]
            )
            ->add(
                'submit',
                SubmitType::class, [
                    'label' => 'Go'
                ]
            );

        return $formBuilder->getForm();
    }
}