<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-18
 * Time: 12:08
 */

namespace App\Form\Listeners;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class Listeners
 * @package App\Form\Listeners
 */
class ListenerLocatorMap extends AbstractType
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
                    'data'          => $options['id']
                ]
            )
            ->add(
                'mapX',
                TextType::class,
                [
                    'attr'          => [
                        'maxlength'     => 3,
                        'size'          => 3,
                        'style'         => "margin: 0 0.5em;width: 3em;"
                    ],
                    'data'          => $options['mapX'],
                    'empty_data'    => 0,
                    'label'         => 'X',
                    'required'      => false
                ]
            )
            ->add(
                'mapY',
                TextType::class,
                [
                    'attr'          => [
                        'maxlength'     => 3,
                        'size'          => 3,
                        'style'         => "margin: 0 0.5em;width: 3em;"
                    ],
                    'data'          => $options['mapY'],
                    'empty_data'    => 0,
                    'label'         => 'Y',
                    'required'      => false
                ]
            )
            ->add(
                'reset',
                ResetType::class,
                [
                    'attr' => [
                        'class' => 'button small'
                    ],
                    'label' => 'Reset',
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
