<?php
namespace App\Form\Listeners;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class Listeners
 * @package App\Form
 */
class LogUpload extends AbstractType
{
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
            'format',
            TextType::class,
            [
                'data' =>           $options['format'],
                'empty_data' =>     '',
                'label' =>          'Format',
            ]
        )
        ->add(
            'logs',
            TextareaType::class,
            [
                'attr' => [
                    'cols' =>       80,
                    'rows' =>       30
                ],
                'data' =>           '',
                'empty_data' =>     '',
                'label' =>          'Logs',
                'required' =>       true
            ]
        )
        ->add(
            'print',
            ButtonType::class,
            [
                'attr' => [
                    'class' =>      'button small',
                    'onclick' =>    'window.print()'
                ],
                'label' =>          'Print...',
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
        );

        return $formBuilder->getForm();
    }
}
