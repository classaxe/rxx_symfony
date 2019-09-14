<?php
namespace App\Controller\Web\Signals;

use App\Controller\Web\Base as WebBase;
use App\Repository\SignalRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

class Base extends WebBase
{
    /**
     * @param $id
     * @param $signalRepository
     * @return bool
     */
    protected function getValidSignal($id, $signalRepository)
    {
        if (!(int) $id) {
            $this-> session->set('lastError', "Signal cannot be found.");
            return false;
        }
        if (!$signal = $signalRepository->find((int) $id)) {
            $this->session->set('lastError', "Signal ".((int) $id)." cannot be found");
            return false;
        }
        return $signal;
    }

    /**
     * @param $id
     * @param $signalRepository
     * @return bool
     */
    protected function getValidReportingListener($id, $signalRepository)
    {
        if (!$signal = $this->getValidSignal($id, $signalRepository)) {
            return false;
        }
        if (!$signal->getCountLogs()) {
            $this->session->set('lastError', "Signal <strong>".$signal->getName()."</strong> has never been reported.");
            return false;
        }
        return $signal;
    }
}
