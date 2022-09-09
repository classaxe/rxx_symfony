<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-18
 * Time: 12:08
 */

namespace App\Form\LogSessions;

use App\Form\Base;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class LogSessions
 * @package App\Form\LogSessions
 */
class LogSessions extends Base
{
    /**
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     * @return FormInterface|void
     */
    public function buildForm(
        FormBuilderInterface $formBuilder,
        array $options
    ) {
        $this->addPaging($formBuilder, $options);
        $this->addSorting($formBuilder, $options);
        $formBuilder
            ->add(
                'comment',
                TextType::class,
                [
                    'data' =>           $options['comment'],
                    'label' =>          'Comment'
                ]
            )
            ->add(
                'go',
                ButtonType::class,
                [
                    'attr' => [
                        'onclick' => '$("form").submit()',
                        'class' => 'button tiny'
                    ],
                    'label' =>      'GO',
                ]
            )
            ->add(
                'location',
                TextType::class,
                [
                    'data' =>           $options['location'],
                    'label' =>          'Location'
                ]
            )
            ->add(
                'go2',
                ButtonType::class,
                [
                    'attr' => [
                        'onclick' => '$("form").submit()',
                        'class' => 'button tiny'
                    ],
                    'label' =>      'GO',
                ]
            )
            ->add(
                'type',
                ChoiceType::class,
                [
                    'attr' =>           [ 'legend' => false ],
                    'choices' =>        array_reverse($this->typeRepository->getAllChoices(false)),
                    'choice_attr' =>    function ($value) { return ['class' => strToLower($value)]; },
                    'data' =>           $options['type'],
                    'expanded' =>       true,
                    'label' =>          false,
                    'multiple' =>       true,
                ]
            );
        return $formBuilder->getForm();
    }


}
