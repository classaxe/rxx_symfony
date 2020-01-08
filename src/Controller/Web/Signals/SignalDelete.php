<?php
namespace App\Controller\Web\Signals;

use App\Controller\Web\Base;
use App\Repository\SignalRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations


class SignalDelete extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/signals/{id}/delete",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signal_delete"
     * )
     */
    public function deleteController(
        $_locale,
        $system,
        $id,
        SignalRepository $signalRepository
    ) {
        if (!(int) $id) {
            return $this->redirectToRoute('signals', ['_locale' => $_locale, 'system' => $system]);
        }
        if (!$this->parameters['isAdmin']) {
            return $this->redirectToRoute('signals', ['_locale' => $_locale, 'system' => $system, 'id' => $id]);
        }
        if (!$signal = $this->getValidSignal($id, $signalRepository)) {
            return $this->redirectToRoute('signals', ['system' => $system]);
        }
        if ($signal->getCountLogs() > 0) {
            $this->session->set(
                'lastError',
                "Signal ".$signal->getKhz() . "-" . $signal->getCall() . " has ".$signal->getCountLogs()." logs and cannot be deleted"
            );
            return $this->redirectToRoute('signals', ['_locale' => $_locale, 'system' => $system]);
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($signal);
        $em->flush();

        $this->session->set('lastMessage', "Signal ". $signal->getKhz() . "-" . $signal->getCall() . " has been deleted");
        return $this->redirectToRoute('signals', ['_locale' => $_locale, 'system' => $system]);
    }
}
