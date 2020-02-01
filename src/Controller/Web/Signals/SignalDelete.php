<?php
namespace App\Controller\Web\Signals;

use App\Repository\SignalRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

class SignalDelete extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/signals/{id}/delete",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signal_delete"
     * )
     */
    public function controller(
        $_locale,
        $system,
        $id,
        SignalRepository $signalRepository
    ) {
        if (!$this->parameters['isAdmin']) {
            return $this->redirectToRoute('signals', ['_locale' => $_locale, 'system' => $system]);
        }
        $args =  ['_locale' => $_locale, 'system' => $system, 'admin_mode' => $_REQUEST['form']['admin_mode'] ?? ''];
        if (!(int) $id) {
            return $this->redirectToRoute('signals', $args);
        }
        if (!$signal = $this->getValidSignal($id, $signalRepository)) {
            return $this->redirectToRoute('signals', $args);
        }
        if ($signal->getLogs() > 0) {
            $this->session->set(
                'lastError',
                sprintf(
                    $this->translator->trans("Signal %s has %d logs and so cannot be deleted at this time."),
                    $signal->getFormattedIdent(),
                    $signal->getLogs()
                )
            );
            return $this->redirectToRoute('signals', $args);
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($signal);
        $em->flush();

        $this->session->set(
            'lastMessage',
            sprintf(
                $this->translator->trans("Signal %s has been deleted."),
                $signal->getFormattedIdent()
            )
        );
        return $this->redirectToRoute('signals', $args);
    }
}
