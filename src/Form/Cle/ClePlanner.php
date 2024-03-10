<?php
namespace App\Form\Cle;
use App\Repository\SignalRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

use App\Form\Base;
/**
 * Class Listeners
 * @package App\Form
 */
class ClePlanner extends Base
{
    /**
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     * @return FormInterface|void
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options)
    {

        $this->addPaging($formBuilder, $options);
        $this->addSorting($formBuilder, $options);
        $this->addPagingBottom($formBuilder, $options);

        $formBuilder
            ->add(
                'type',
                ChoiceType::class,
                [
                    'attr' =>           [ 'legend' => 'Signal Types' ],
                    'choices' =>        $this->typeRepository->getAllChoices(true),
                    'choice_attr' =>    function ($value) { return ['class' => strToLower($value)]; },
                    'data' =>           $options['type'],
                    'expanded' =>       true,
                    'label' =>          false,
                    'multiple' =>       true
                ]
            )
            ->add(
                'khz_1',
                TextType::class,
                [
                    'data' =>           $options['khz_1'],
                    'label' =>          'KHz. Range',
                    'required' =>       false
                ]
            )
            ->add(
                'khz_2',
                TextType::class,
                [
                    'data' =>           $options['khz_2'],
                    'label' =>          false,
                    'required' =>       false
                ]
            )
            ->add(
                'date_1',
                DateType::class,
                [
                    'attr' =>           [ 'class' => 'js-datepicker' ],
                    'data' =>           $options['date_1'],
                    'html5' =>          false,
                    'label' =>          'Log Dates',
                    'required' =>       false,
                    'widget' =>         'single_text'
                ]
            )
            ->add(
                'date_2',
                DateType::class,
                [
                    'attr' =>           [ 'class' => 'js-datepicker' ],
                    'data' =>           $options['date_2'],
                    'html5' =>          false,
                    'label' =>          'End Date',
                    'required' =>       false,
                    'widget' =>         'single_text'
                ]
            )
            ->add(
                'channels',
                ChoiceType::class,
                [
                    'choices' =>        [ 'All' => '', 'Only 1 KHz' => '1', 'Not 1 KHz' =>  '2', 'Only 10 KHz' => '3', 'Not 10 KHz' => '4' ],
                    'data' =>           $options['channels'],
                    'label' =>          'Channels',
                    'required' =>       false
                ]
            )
            ->add(
                'status',
                ChoiceType::class,
                [
                    'attr' =>           [ 'legend' => 'Status' ],
                    'choices' =>        [ 'Active' => '1', 'Inactive' => '2', 'Decommissioned' => '3' ],
                    'data' =>           $options['status'],
                    'expanded' =>       true,
                    'label' =>          false,
                    'multiple' =>       true
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' =>          'Go',
                    'attr' =>           [ 'class' => 'button small']
                ]
            )
            ->add(
                'clear',
                ResetType::class,
                [
                    'label' =>          'Clear',
                    'attr' =>           [ 'class' => 'button small' ]
                ]
            );



        return $formBuilder->getForm();
    }
}
