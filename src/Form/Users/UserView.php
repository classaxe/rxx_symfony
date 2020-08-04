<?php
namespace App\Form\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class Listeners
 * @package App\Form
 */
class UserView extends AbstractType
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
                'username',
                TextType::class,
                [
                    'attr' => [
                        'onchange' =>   "try{setCustomValidity('')}catch(e){}",
                        'oninvalid' =>  "setCustomValidity('Please enter this user's username')"
                    ],
                    'data' =>           $options['username'],
                    'empty_data' =>     '',
                    'label' =>          'Username',
                ]
            )
            ->add(
                'password',
                TextType::class,
                [
                    'data' =>           '',
                    'empty_data' =>     '',
                    'label' =>          'Password',
                    'required' =>       false
                ]
            )
            ->add(
                'name',
                TextType::class,
                [
                    'attr' => [
                        'onchange' =>   "try{setCustomValidity('')}catch(e){}",
                        'oninvalid' =>  "setCustomValidity('Please enter this user\'s name')"
                    ],
                    'data' =>           $options['name'],
                    'empty_data' =>     '',
                    'label' =>          'Name',
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
                'access',
                TextType::class,
                [
                    'data' =>       $options['access'],
                    'empty_data' => '',
                    'label' =>      'Roles',
                    'required' =>   false
                ]
            )
            ->add(
                'active',
                TextType::class,
                [
                    'data' =>       $options['active'],
                    'empty_data' => '',
                    'label' =>      'Active',
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
