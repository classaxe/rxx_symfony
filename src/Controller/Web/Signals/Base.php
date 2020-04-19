<?php
namespace App\Controller\Web\Signals;

use App\Controller\Web\Base as WebBase;
use App\Entity\Signal;

class Base extends WebBase
{
    /**
     * @param $id
     * @return bool|Signal
     */
    protected function getValidSignal($id)
    {
        if (!(int) $id) {
            $this->session->set('lastError', "Signal cannot be found.");
            return false;
        }
        if (!$signal = $this->signalRepository->find((int) $id)) {
            $this->session->set('lastError', "Signal ".((int) $id)." cannot be found");
            return false;
        }
        return $signal;
    }
}
