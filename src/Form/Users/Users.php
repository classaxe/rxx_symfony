<?php
namespace App\Form\Users;

use App\Form\Base;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class ListenerLogs
 * @package App\Form\Users
 */
class Users extends Base
{
    /**
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     * @return FormInterface|void
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options)
    {
        $this->addPaging($formBuilder, $options);
        $this->addPagingBottom($formBuilder, $options);
        $this->addSorting($formBuilder, $options);

        return $formBuilder->getForm();
    }
}
