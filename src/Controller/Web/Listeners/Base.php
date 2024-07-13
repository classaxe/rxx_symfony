<?php
namespace App\Controller\Web\Listeners;

use App\Controller\Web\Base as WebBase;
use App\Entity\Listener;

class Base extends WebBase
{
    /**
     * @param $id
     * @return object|bool
     */
    protected function getValidListener($id)
    {
        if (!(int) $id) {
            $this->session->set('lastError', "Listener cannot be found.");
            return false;
        }
        if (!$listener = $this->listenerRepository->find((int) $id)) {
            $this->session->set('lastError', "Listener ".((int) $id)." cannot be found");
            return false;
        }
        return $listener;
    }

    /**
     * @param $id
     * @return Listener|bool
     */
    protected function getValidReportingListener($id)
    {
        if (!$listener = $this->getValidListener($id)) {
            return false;
        }
        if (!$listener->getCountLogs() && !$listener->getCountRemoteLogs()) {
            $this->session->set('lastError', "Listener <strong>".$listener->getName()."</strong> has submitted no logs.");
            return false;
        }
        return $listener;
    }
}
