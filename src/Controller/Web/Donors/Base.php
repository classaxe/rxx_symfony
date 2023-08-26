<?php
namespace App\Controller\Web\Donors;

use App\Controller\Web\Base as WebBase;

class Base extends WebBase
{
    /**
     * @param $id
     * @return object|bool
     */
    protected function getValidDonor($id)
    {
        if (!(int) $id) {
            $this->session->set('lastError', "Donor cannot be found.");
            return false;
        }
        if (!$donor = $this->donorRepository->find((int) $id)) {
            $this->session->set('lastError', "Donor ".((int) $id)." cannot be found");
            return false;
        }
        return $donor;
    }
}
