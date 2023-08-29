<?php
namespace App\Form\Donors;


use App\Form\Base;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class Donor
 * @package App\Form
 */
class Donor extends Base
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
                'display',
                TextType::class,
                [
                    'attr' => [
                        'onchange' =>   "try{setCustomValidity('')}catch(e){}",
                        'oninvalid' =>  "setCustomValidity('Please enter this donor\'s display name')"
                    ],
                    'data' =>           $options['display'],
                    'empty_data' =>     '',
                    'label' =>          'Display Name',
                ]
            )
            ->add(
                'callsign',
                TextType::class,
                [
                    'data' =>       $options['callsign'],
                    'empty_data' => '',
                    'label' =>      'Callsign',
                    'required' =>   false
                ]
            )
            ->add(
                'email',
                TextType::class,
                [
                    'data' =>       $options['email'],
                    'empty_data' => '',
                    'label' =>      'Email Address',
                    'required' =>   false
                ]
            )
            ->add(
                'anonymous',
                ChoiceType::class,
                [
                    'choices' => [
                        'No' =>   0,
                        'Yes' =>     1
                    ],
                    'data' =>       $options['anonymous'],
                    'empty_data' => '',
                    'expanded' =>       true,
                    'label' =>      'Anonymous.',
                    'required' =>   true
                ]
            )
            ->add(
                'sp',
                ChoiceType::class,
                [
                    'choices' =>        $this->stateRepository->getMatchingOptions(),
                    'data' =>           $options['sp'],
                    'empty_data' =>     null,
                    'label' =>          'State / Prov',
                    'required' =>       false
                ]
            )
            ->add(
                'itu',
                ChoiceType::class,
                [
                    'choices' =>        $this->countryRepository->getMatchingOptions(),
                    'data' =>           $options['itu'],
                    'label' =>          'Country',
                ]
            )
            ->add(
                'notes',
                TextareaType::class,
                [
                    'data' =>       $options['notes'],
                    'empty_data' => '',
                    'label' =>      'Notes',
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
