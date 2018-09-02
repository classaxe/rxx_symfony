<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-18
 * Time: 12:08
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class Listeners
 * @package App\Form
 */
class ListenerSignals extends AbstractType
{
    /**
     * @param FormBuilderInterface $formBuilder
     * @return \Symfony\Component\Form\FormInterface|void
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options)
    {
        $formBuilder
            ->add(
                'sort',
                HiddenType::class,
                [
                    'data' => 'khz'
                ]
            )
            ->add(
                'order',
                HiddenType::class,
                [
                    'data' => 'a'
                ]
            );
        return $formBuilder->getForm();
    }
}
