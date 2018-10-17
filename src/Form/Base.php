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

    /**
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     * @return FormBuilderInterface
     */
    public function addPaging(FormBuilderInterface &$formBuilder, array $options)
    {
        $this->options = $options;

        if ($options['total'] < $options['maxNoPaging']) {
            return $formBuilder;
        }

        $formBuilder
            ->add(
                'limit',
                ChoiceType::class,
                [
                    'label' =>      'Show',
                    'choices' =>    $this->getlimitOptions($options['total']),
                    'data' =>       $options['limit']
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
                'page_hidden',
                hiddenType::class,
                [
                    'data' =>       0
                ]
            )
            ->add(
                'page',
                ChoiceType::class,
                [
                    'label' =>      ' ',
                    'choices' =>    $this->getPageOptions($this->options['total'], $this->options['limit']),
                    'data' =>       0
                ]
            );

        $formBuilder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $form =     $event->getForm();
            $data =     $event->getData();

            print Rxx::y($data);

            $limit =    $data['limit'];
            $page =     $data['page_hidden'];
            $form
                ->remove('page')
                ->add(
                    'page',
                    ChoiceType::class,
                    [
                        'label' =>      ' ',
                        'choices' =>    $this->getPageOptions($this->options['total'], $limit),
                        'data' =>       $page
                    ]
                );
        });
    }

    /**
     * @param $limit
     * @return array
     */
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

    /**
     * @param $total
     * @param $limit
     * @return array
     */
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
