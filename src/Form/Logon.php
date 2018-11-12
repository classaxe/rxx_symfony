<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-18
 * Time: 12:08
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class Logon
 * @package App\Form
 */
class Logon extends AbstractType
{
    /**
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface|void
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options)
    {
        $system =   $options['system'];

        $formBuilder
            ->add(
                'user',
                TextType::class,
                [
                    'attr'          => [
                        'onchange'      => "try{setCustomValidity('')}catch(e){}",
                        'oninvalid'     => "setCustomValidity('Please provide a valid Username')",
                    ],
                    'help'          => '&nbsp;',
                    'label'         => 'Username',
                ]
            )
            ->add(
                'password',
                PasswordType::class,
                [
                    'attr'          => [
                        'onchange'      => "try{setCustomValidity('')}catch(e){}",
                        'oninvalid'     => "setCustomValidity('Please provide a valid Password')",
                    ],
                    'help'          => '&nbsp;',
                    'label'         => 'Password',
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'attr'          => [
                        'class'         => 'button small'
                    ],
                    'label'         => 'Logon',
                ]
            );

        return $formBuilder->getForm();
    }
}
