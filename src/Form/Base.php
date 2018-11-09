<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-18
 * Time: 12:08
 */

namespace App\Form;

use App\Utils\Rxx;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ListenerLogs
 * @package App\Form
 */
class Base extends AbstractType
{
    private $options;
    private $limitOptions = [10, 20, 50, 100, 200, 500, 1000, 2000, 5000, 100000, 20000, 50000, 100000];

    /**
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     * @return FormBuilderInterface
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
                    'attr' =>       ['class' => 'button', 'style' => 'display:none'],
                    'label' =>      '<',
                ]
            )
            ->add(
                'next',
                ButtonType::class,
                [
                    'attr' =>       ['class' => 'button', 'style' => 'display:none'],
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
}
