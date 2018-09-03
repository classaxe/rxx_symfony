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
 * Class ListenerLogs
 * @package App\Form
 */
class ListenerLogs extends AbstractType
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
                    'data' => 'logDate'
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
