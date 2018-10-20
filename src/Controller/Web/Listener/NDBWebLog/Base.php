<?php
namespace App\Controller\Web\Listener\Ndbweblog;

use App\Controller\Web\Base as WebBase;
use App\Repository\ListenerRepository;
use App\Repository\LogRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web\Listener\Ndbweblog
 */
class Base extends WebBase
{
    /**
     * @param $id
     * @param $listenerRepository
     * @return bool
     */
    protected function getValidListener($id, $listenerRepository)
    {
        if (!(int) $id) {
            $this->session->set('lastError', "Listener cannot be found.");
            return false;
        }
        $listener = $listenerRepository->find((int) $id);
        if (!$listener) {
            $this->session->set('lastError', "Listener cannot be found");
            return false;
        }
        if (!$listener->getCountLogs()) {
            $this->session->set('lastError', "Listener <strong>".$listener->getName()."</strong> has no logs to view.");
            return false;
        }
        return $listener;
    }
}
