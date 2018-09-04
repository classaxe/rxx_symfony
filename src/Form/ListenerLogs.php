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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
        if ($options['total'] >= 100) {
            $formBuilder
                ->add(
                    'limit',
                    ChoiceType::class,
                    [
                        'label' =>      'Show',
                        'choices' =>    $this->getlimitOptions($options['total']),
                        'data' =>       100
                    ]
                )
                ->add(
                    'prev',
                    ButtonType::class,
                    [
                        'label' =>      '<',
                        'attr' =>       ['class' => 'button tiny']
                    ]
                )
                ->add(
                    'next',
                    ButtonType::class,
                    [
                        'label' =>      '>',
                        'attr' =>       ['class' => 'button tiny']
                    ]
                )
            ->add(
                'page',
                ChoiceType::class,
                [
                    'label' =>      ' ',
                    'choices' =>    $this->getPageOptions($options['total'], $options['limit']),
                    'data' =>       0,
                    'help' => '&nbsp;'
                ]
            )
            ->add(
                'page',
                ChoiceType::class,
                [
                    'label' =>      ' ',
                    'choices' =>    $this->getPageOptions($options['total'], $options['limit']),
                    'data' =>       0
                ]
            );
        } else {
            $formBuilder
                ->add(
                    'limit',
                    HiddenType::class,
                    [
                        'data' => '100'
                    ]
                );
        }
//        print '<pre>' . print_r($options, true).'</pre>';
        return $formBuilder->getForm();
    }

    private function getlimitOptions($limit)
    {
        $values = [10, 25, 50, 100, 250, 500, 1000, 2500, 5000, 100000, 25000, 50000, 100000];
        $options = [];
        foreach ($values as $value) {
            if ($value < $limit) {
                $options[$value.' results'] = $value;
            }
        }
        $options['All Results'] = -1;
        return $options;
    }
    private function getPageOptions($total, $limit)
    {
        $options = [];
        $pages = $total/$limit;
        for ($i=0; $i < $pages; $i++) {
            $options[1+($i*$limit).'-'.(($i+1)*$limit)] = $i;
        }
        return $options;
    }
}
