<?php
namespace App\Controller\Web\Listeners;

use App\Controller\Web\Base as WebBase;
use App\Entity\Listener;
use App\Repository\ListenerRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

class Base extends WebBase
{
    /**
     * @param $id
     * @param ListenerRepository $listenerRepository
     * @return object|bool
     */
    protected function getValidListener($id, ListenerRepository $listenerRepository)
    {
        if (!(int) $id) {
            $this->session->set('lastError', "Listener cannot be found.");
            return false;
        }
        if (!$listener = $listenerRepository->find((int) $id)) {
            $this->session->set('lastError', "Listener ".((int) $id)." cannot be found");
            return false;
        }
        return $listener;
    }

    /**
     * @param $id
     * @param $listenerRepository
     * @return Listener|bool
     */
    protected function getValidReportingListener($id, $listenerRepository)
    {
        if (!$listener = $this->getValidListener($id, $listenerRepository)) {
            return false;
        }
        if (!$listener->getCountLogs()) {
            $this->session->set('lastError', "Listener <strong>".$listener->getName()."</strong> has submitted no logs.");
            return false;
        }
        return $listener;
    }
}
