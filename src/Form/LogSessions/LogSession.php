<?php
namespace App\Form\LogSessions;

use App\Form\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class Listeners
 * @package App\Form
 */
class LogSession extends Base
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
                'reload',
                HiddenType::class,
                [
                    'data' =>       0
                ]
            )
            ->add(
                'listenerId',
                ChoiceType::class,
                [
                    'choices' => $this->listenerRepository->getAllOptions(
                        '',
                        '',
                        $this->translator->trans('(None specified)'),
                        true
                    ),
                    'choice_translation_domain' => false,
                    'data' =>           $options['listenerId'],
                    'expanded' =>       false,
                    'label' =>          'Listener / Loc:',
                    'required' =>       false
                ]
            )
            ->add(
                'operatorId',
                ChoiceType::class,
                [
                    'choices' => $this->listenerRepository->getOperators(
                        '',
                        $this->translator->trans('(None specified)')
                    ),
                    'choice_translation_domain' => false,
                    'data' =>           $options['operatorId'],
                    'expanded' =>       false,
                    'label' =>          'Operator',
                    'required' =>       false
                ]
            )
            ->add(
                'comment',
                TextType::class,
                [
                    'data' =>           $options['comment'],
                    'empty_data' =>     '',
                    'label' =>          'Comment',
                    'required' =>       false
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
