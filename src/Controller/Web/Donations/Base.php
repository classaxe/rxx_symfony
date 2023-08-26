<?php
namespace App\Controller\Web\Donations;

use App\Controller\Web\Base as WebBase;

class Base extends WebBase
{
    /**
     * @param $id
     * @return object|bool
     */
    protected function getValidDonation($id)
    {
        if (!(int) $id) {
            $this->session->set('lastError', "Donor cannot be found.");
            return false;
        }
        if (!$donation = $this->donationRepository->find((int) $id)) {
            $this->session->set('lastError', "Donor ".((int) $id)." cannot be found");
            return false;
        }
        return $donation;
    }
}
