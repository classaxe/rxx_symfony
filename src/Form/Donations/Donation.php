<?php
namespace App\Form\Donations;


use App\Form\Base;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class Donation
 * @package App\Form
 */
class Donation extends Base
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
                '_close',
                HiddenType::class,
                [
                    'data' =>       0
                ]
            )
            ->add(
                '_reload_opener',
                HiddenType::class,
                [
                    'data' =>       0
                ]
            )
            ->add(
                'name',
                TextType::class,
                [
                    'attr' => [
                        'onchange' =>   "try{setCustomValidity('')}catch(e){}",
                        'oninvalid' =>  "setCustomValidity('Please enter this donor's name')"
                    ],
                    'data' =>           $options['name'],
                    'empty_data' =>     '',
                    'label' =>          'Name',
                ]
            )
            ->add(
                'amount',
                TextType::class,
                [
                    'attr' => [
                        'onchange' =>   "try{setCustomValidity('')}catch(e){}",
                        'oninvalid' =>  "setCustomValidity('Please enter the donation amount')"
                    ],
                    'data' =>           $options['amount'],
                    'empty_data' =>     '',
                    'label' =>          'Amount',
                ]
            )

            ->add(
                'date',
                TextType::class,
                [
                    'attr' => [
                        'class' => 'js-datepicker'
                    ],
                    'data' =>       $options['date'],
                    'empty_data' => '',
                    'label' =>      'Date',
                    'required' =>   true
                ]
            )
            ->add(
                'message',
                TextareaType::class,
                [
                    'data' =>       $options['message'],
                    'empty_data' => '',
                    'label' =>      'Message',
                    'required' =>   false
                ]
            )
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
                'save',
                SubmitType::class,
                [
                    'attr' => [
                        'class' => 'button small'
                    ],
                    'label' => 'Save',
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
