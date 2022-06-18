<?php
namespace App\Form\Cle;
use App\Repository\SignalRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class Listeners
 * @package App\Form
 */
class Cle extends AbstractType
{
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
                'cle',
                NumberType::class,
                [
                    'data' =>           $options['cle'],
                    'empty_data' =>     '',
                    'label' =>          'CLE #',
                    'required' =>       true
                ]
            )
            ->add(
                'dateStart',
                DateType::class,
                [
                    'attr' =>           [ 'class' => 'js-datepicker' ],
                    'data' =>           $options['dateStart'],
                    'html5' =>          false,
                    'label' =>          'Start Date',
                    'required' =>       true,
                    'widget' =>         'single_text'
                ]
            )
            ->add(
                'dateEnd',
                DateType::class,
                [
                    'attr' =>           [ 'class' => 'js-datepicker' ],
                    'data' =>           $options['dateEnd'],
                    'html5' =>          false,
                    'label' =>          'End Date',
                    'required' =>       true,
                    'widget' =>         'single_text'
                ]
            )
            ->add(
                'dateTimespan',
                TextType::class,
                [
                    'data' =>           $options['dateTimespan'],
                    'empty_data' =>     '',
                    'label' =>          'Timespan',
                    'required' =>       true
                ]
            )
            ->add(
                'scope',
                TextType::class,
                [
                    'data' =>           $options['scope'],
                    'empty_data' =>     '',
                    'label' =>          'Scope',
                    'required' =>       true
                ]
            )
            ->add(
                'additional',
                TextareaType::class,
                [
                    'data' =>           $options['additional'],
                    'empty_data' =>     '',
                    'label' =>          'Additional',
                    'required' =>       false
                ]
            )
            ->add(
                'worldRange1Low',
                TextType::class,
                [
                    'data' =>           $options['worldRange1Low'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       true
                ]
            )
            ->add(
                'europeRange1Low',
                TextType::class,
                [
                    'data' =>           $options['europeRange1Low'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       true
                ]
            )
            ->add(
                'worldRange2Low',
                TextType::class,
                [
                    'data' =>           $options['worldRange2Low'] ? $options['worldRange2Low'] : '',
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'europeRange2Low',
                TextType::class,
                [
                    'data' =>           $options['europeRange2Low'] ? $options['europeRange2Low'] : '',
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'worldRange1High',
                TextType::class,
                [
                    'data' =>           $options['worldRange1High'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       true
                ]
            )
            ->add(
                'europeRange1High',
                TextType::class,
                [
                    'data' =>           $options['europeRange1High'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       true
                ]
            )
            ->add(
                'worldRange2High',
                TextType::class,
                [
                    'data' =>           $options['worldRange2High'] ? $options['worldRange2High'] : '',
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'europeRange2High',
                TextType::class,
                [
                    'data' =>           $options['europeRange2High'] ? $options['europeRange2High'] : '',
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'worldRange1Channels',
                ChoiceType::class,
                [
                    'choices' =>        [ 'All' => '', 'Only 1 KHz' => '1', 'Not 1 KHz' =>  '2', 'Only 10 KHz' => '3', 'Not 10 KHz' => '4' ],
                    'data' =>           $options['worldRange1Channels'],
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'europeRange1Channels',
                ChoiceType::class,
                [
                    'choices' =>        [ 'All' => '', 'Only 1 KHz' => '1', 'Not 1 KHz' =>  '2', 'Only 10 KHz' => '3', 'Not 10 KHz' => '4' ],
                    'data' =>           $options['europeRange1Channels'],
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'worldRange2Channels',
                ChoiceType::class,
                [
                    'choices' =>        [ 'All' => '', 'Only 1 KHz' => '1', 'Not 1 KHz' =>  '2', 'Only 10 KHz' => '3', 'Not 10 KHz' => '4' ],
                    'data' =>           $options['worldRange2Channels'],
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'europeRange2Channels',
                ChoiceType::class,
                [
                    'choices' =>        [ 'All' => '', 'Only 1 KHz' => '1', 'Not 1 KHz' =>  '2', 'Only 10 KHz' => '3', 'Not 10 KHz' => '4' ],
                    'data' =>           $options['europeRange2Channels'],
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'worldRange1Type',
                HiddenType::class,
                [
                    'data' =>           $options['worldRange1Type'],
                ]
            )
            ->add(
                'europeRange1Type',
                HiddenType::class,
                [
                    'data' =>           $options['europeRange1Type'],
                ]
            )
            ->add(
                'worldRange2Type',
                HiddenType::class,
                [
                    'data' =>           $options['worldRange2Type'],
                ]
            )
            ->add(
                'europeRange2Type',
                HiddenType::class,
                [
                    'data' =>           $options['europeRange2Type'],
                ]
            )
            ->add(
                'worldRange1Locator',
                TextType::class,
                [
                    'data' =>           $options['worldRange1Locator'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'europeRange1Locator',
                TextType::class,
                [
                    'data' =>           $options['europeRange1Locator'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'worldRange2Locator',
                TextType::class,
                [
                    'data' =>           $options['worldRange2Locator'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'europeRange2Locator',
                TextType::class,
                [
                    'data' =>           $options['europeRange2Locator'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'worldRange1Sp',
                TextType::class,
                [
                    'data' =>           $options['worldRange1Sp'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'europeRange1Sp',
                TextType::class,
                [
                    'data' =>           $options['europeRange1Sp'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'worldRange2Sp',
                TextType::class,
                [
                    'data' =>           $options['worldRange2Sp'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'europeRange2Sp',
                TextType::class,
                [
                    'data' =>           $options['europeRange2Sp'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'worldRange1SpItuClause',
                ChoiceType::class,
                [
                    'choices' =>        [ 'AND' => 'AND', 'OR' =>  'OR' ],
                    'data' =>           $options['worldRange1SpItuClause'],
                    'label' =>          '',
                    'required' =>       true
                ]
            )
            ->add(
                'europeRange1SpItuClause',
                ChoiceType::class,
                [
                    'choices' =>        [ 'AND' => 'AND', 'OR' =>  'OR' ],
                    'data' =>           $options['europeRange1SpItuClause'],
                    'label' =>          '',
                    'required' =>       true
                ]
            )
            ->add(
                'worldRange2SpItuClause',
                ChoiceType::class,
                [
                    'choices' =>        [ 'AND' => 'AND', 'OR' =>  'OR' ],
                    'data' =>           $options['worldRange2SpItuClause'],
                    'label' =>          '',
                    'required' =>       true
                ]
            )
            ->add(
                'europeRange2SpItuClause',
                ChoiceType::class,
                [
                    'choices' =>        [ 'AND' => 'AND', 'OR' =>  'OR' ],
                    'data' =>           $options['europeRange2SpItuClause'],
                    'label' =>          '',
                    'required' =>       true
                ]
            )
            ->add(
                'worldRange1Itu',
                TextType::class,
                [
                    'data' =>           $options['worldRange1Itu'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'europeRange1Itu',
                TextType::class,
                [
                    'data' =>           $options['europeRange1Itu'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'worldRange2Itu',
                TextType::class,
                [
                    'data' =>           $options['worldRange2Itu'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'europeRange2Itu',
                TextType::class,
                [
                    'data' =>           $options['europeRange2Itu'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'worldRange1Recently',
                ChoiceType::class,
                [
                    'choices' =>        [ 'Logged' => 'logged', 'Not Logged' =>   'unlogged' ],
                    'data' =>           $options['worldRange1Recently'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'europeRange1Recently',
                ChoiceType::class,
                [
                    'choices' =>        [ 'Logged' => 'logged', 'Not Logged' =>   'unlogged' ],
                    'data' =>           $options['europeRange1Recently'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'worldRange2Recently',
                ChoiceType::class,
                [
                    'choices' =>        [ 'Logged' => 'logged', 'Not Logged' =>   'unlogged' ],
                    'data' =>           $options['worldRange2Recently'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'europeRange2Recently',
                ChoiceType::class,
                [
                    'choices' =>        [ 'Logged' => 'logged', 'Not Logged' =>   'unlogged' ],
                    'data' =>           $options['europeRange2Recently'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'worldRange1Within',
                ChoiceType::class,
                [
                    'choices' =>        SignalRepository::withinPeriods,
                    'data' =>           $options['worldRange1Within'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'europeRange1Within',
                ChoiceType::class,
                [
                    'choices' =>        SignalRepository::withinPeriods,
                    'data' =>           $options['europeRange1Within'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'worldRange2Within',
                ChoiceType::class,
                [
                    'choices' =>        SignalRepository::withinPeriods,
                    'data' =>           $options['worldRange2Within'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'europeRange2Within',
                ChoiceType::class,
                [
                    'choices' =>        SignalRepository::withinPeriods,
                    'data' =>           $options['europeRange2Within'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'worldRange1Active',
                ChoiceType::class,
                [
                    'choices' =>        [ 'All' => '', 'Active' => '1', 'Inactive' =>   '2' ],
                    'data' =>           $options['worldRange1Active'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'europeRange1Active',
                ChoiceType::class,
                [
                    'choices' =>        [ 'All' => '', 'Active' => '1', 'Inactive' =>   '2' ],
                    'data' =>           $options['europeRange1Active'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'worldRange2Active',
                ChoiceType::class,
                [
                    'choices' =>        [ 'All' => '', 'Active' => '1', 'Inactive' =>   '2' ],
                    'data' =>           $options['worldRange2Active'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'europeRange2Active',
                ChoiceType::class,
                [
                    'choices' =>        [ 'All' => '', 'Active' => '1', 'Inactive' =>   '2' ],
                    'data' =>           $options['europeRange2Active'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'worldRange1FilterOther',
                TextType::class,
                [
                    'data' =>           $options['worldRange1FilterOther'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'europeRange1FilterOther',
                TextType::class,
                [
                    'data' =>           $options['europeRange1FilterOther'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'worldRange2FilterOther',
                TextType::class,
                [
                    'data' =>           $options['worldRange2FilterOther'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'europeRange2FilterOther',
                TextType::class,
                [
                    'data' =>           $options['europeRange2FilterOther'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'worldRange1TextExtra',
                TextType::class,
                [
                    'data' =>           $options['worldRange1TextExtra'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'europeRange1TextExtra',
                TextType::class,
                [
                    'data' =>           $options['europeRange1TextExtra'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'worldRange2TextExtra',
                TextType::class,
                [
                    'data' =>           $options['worldRange2TextExtra'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )
            ->add(
                'europeRange2TextExtra',
                TextType::class,
                [
                    'data' =>           $options['europeRange2TextExtra'],
                    'empty_data' =>     '',
                    'label' =>          '',
                    'required' =>       false
                ]
            )

            ->add(
                'save',
                SubmitType::class,
                [
                    'attr' => [
                        'class' => 'button small'
                    ],
                    'label' => 'Save',
                ]
            );

        return $formBuilder->getForm();
    }
}
