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
                    'label' =>      'Show',
                    'data' =>       $this->options['limit']
                ]
            )
            ->add(
                'prev',
                ButtonType::class,
                [
                    'label' =>      '<',
                    'attr' =>       ['class' => 'button tiny', 'style' => 'display:none']
                ]
            )
            ->add(
                'next',
                ButtonType::class,
                [
                    'label' =>      '>',
                    'attr' =>       ['class' => 'button tiny', 'style' => 'display:none']
                ]
            )
            ->add(
                'page',
                TextType::class,
                [
                    'label' =>      ' ',
                    'data' =>       0,
                    'attr' =>       ['style' => 'display:none']
                ]
            )
            ->add(
                'page_ctl',
                ChoiceType::class,
                [
                    'label' =>      ' ',
                    'choices' =>    $this->getPageOptions($this->options['total'], $this->options['limit']),
                    'data' =>       $this->options['page'],
                    'attr' =>       ['style' => 'display:none']
                ]
            );
        ;

        $formBuilder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $form =     $event->getForm();
            $data =     $event->getData();
            $form
                ->remove('page_ctl')
                ->add(
                    'page_ctl',
                    ChoiceType::class,
                    [
                        'label' =>      ' ',
                        'choices' =>    $this->getPageOptions($this->options['total'], $data['limit']),
                        'data' =>       $data['page'],
                        'attr' =>       ['style' => 'display:none']
                    ]
                );
        });
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
