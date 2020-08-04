<?php
namespace App\Controller\Web\Admin;

use App\Controller\Web\Base as WebBase;

class Base extends WebBase
{
    /**
     * @param $id
     * @return object|bool
     */
    protected function getValidUser($id)
    {
        if (!(int) $id) {
            $this->session->set('lastError', "User cannot be found.");
            return false;
        }
        if (!$user = $this->userRepository->find((int) $id)) {
            $this->session->set('lastError', "User ".((int) $id)." cannot be found");
            return false;
        }
        return $user;
    }
}
