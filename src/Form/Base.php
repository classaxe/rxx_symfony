<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-18
 * Time: 12:08
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ListenerLogs
 * @package App\Form
 */
class Base extends AbstractType
{
    /**
     * @var
     */
    private $options;

    /**
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     * @return FormBuilderInterface
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options)
    {
        $this->addPaging($formBuilder, $options);
        $this->addSorting($formBuilder, $options);
        return $formBuilder->getForm();
    }

    /**
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     */
    public function addPaging(FormBuilderInterface &$formBuilder, array $options)
    {
        $this->options = $options;

        if ($this->options['total'] < $this->options['maxNoPaging']) {
            return $formBuilder;
        }

        $formBuilder
            ->add(
                'limit',
                TextType::class,
                [
                    'attr' =>       ['style' => 'display:none'],
                    'data' =>       $this->options['limit'],
                    'label' =>      'Show',
                ]
            )
            ->add(
                'prev',
                ButtonType::class,
                [
                    'attr' =>       ['class' => 'button tiny', 'style' => 'display:none'],
                    'label' =>      '<',
                ]
            )
            ->add(
                'next',
                ButtonType::class,
                [
                    'attr' =>       ['class' => 'button tiny', 'style' => 'display:none'],
                    'label' =>      '>',
                ]
            )
            ->add(
                'page',
                TextType::class,
                [
                    'attr' =>       ['style' => 'display:none'],
                    'data' =>       0,
                    'label' =>      ' ',
                ]
            );
    }

    /**
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     */
    protected function addSorting(FormBuilderInterface &$formBuilder, array $options)
    {
        $formBuilder
            ->add(
                'sort',
                HiddenType::class,
                [
                    'data' => $this->options['sort']
                ]
            )
            ->add(
                'order',
                HiddenType::class,
                [
                    'data' => $this->options['order']
                ]
            );
    }
}
