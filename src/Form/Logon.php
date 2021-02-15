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
class Logon extends \App\Controller\Web\Base
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
                'user',
                TextType::class,
                [
                    'attr'          => [
                        'onchange'      =>  "try{setCustomValidity('')}catch(e){}",
                        'oninvalid'     =>  "setCustomValidity('".$this->i18n('Please provide a valid Username')."')",
                    ],
                    'label'         => 'Username',
                ]
            )
            ->add(
                'password',
                PasswordType::class,
                [
                    'attr'          => [
                        'onchange'      => "try{setCustomValidity('')}catch(e){}",
                        'oninvalid'     => "setCustomValidity('".$this->i18n('Please provide a valid Password')."')",
                    ],
                    'label'         => 'Password',
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'attr'          => [
                        'class' =>      'button small',
                        'disabled' =>   ($options['disableLogon'] ?? false)
                    ],
                    'label'         => 'Log On',
                ]
            );

        return $formBuilder->getForm();
    }
}
