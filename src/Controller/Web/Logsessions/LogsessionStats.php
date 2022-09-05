<?php
namespace App\Controller\Web\Logsessions;

use App\Controller\Web\Base;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Collection
 * @package App\Controller\Web\Logsessions
 */
class LogsessionStats extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/logsessions/{id}/stats",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="logsession_stats"
     * )
     * @param $_locale
     * @param $system
     * @param $logSessionId
     * @return RedirectResponse
     */
    public function logSession(
        $_locale,
        $system,
        $id
    ) {
        if (!$logsession = $this->logsessionRepository->find($id)) {
            return $this->redirectToRoute('logsession', ['system' => $system]);
        }
        $isAdmin = $this->parameters['isAdmin'];
        $stats = $logsession->getUploadStats() ? unserialize($logsession->getUploadStats()) : false;
        $parameters = [
            '_locale' =>            $_locale,
            'id' =>                 $id,
            'l' =>                  $logsession,
            'mode' =>               "Stats | Log Session $id",
            'stats' =>              $stats,
            'system' =>             $system,
            'tabs' =>               $this->logsessionRepository->getTabs($logsession, $isAdmin),
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('logsession/stats.html.twig', $parameters);
    }

}