<?php
namespace App\Form\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class Listeners
 * @package App\Form
 */
class UserProfile extends AbstractType
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
                        'oninvalid' =>  "setCustomValidity('Please enter your name')"
                    ],
                    'data' =>           $options['name'],
                    'empty_data' =>     '',
                    'label' =>          'Your Name',
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
