<?php
namespace App\Controller\Web\Admin;

use App\Controller\Web\Base;
use App\Utils\Rxx;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class Tools extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/admin/tools/{tool}",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"tool" : ""},
     *     name="admin/tools"
     * )
     * @param $_locale
     * @param $system
     * @param $tool
     * @return Response
     */
    public function controller(
        $_locale,
        $system,
        $tool
    ) {
        if (!$this->parameters['isAdmin']) {
            $this->session->set('route', 'admin/tools');
            return $this->redirectToRoute('logon', ['system' => $system]);
        }
        $this->session->set('route', '');
        switch ($tool) {
            case 'icaoImport':
            case 'listenersStats':
            case 'logsDx':
            case 'logsDaytime':
            case 'signalsLatLon':
            case 'signalsStats':
            case 'systemExportDb':
            case 'systemEmailTest':
                $this->{$tool}();
                return $this->redirectToRoute('admin/tools', ['system' => $system]);

            break;
            default:
                $this->session->set('lastError', '');
                $this->session->set('lastMessage', '');
                break;
        }

        $parameters = [
            '_locale' =>        $_locale,
            'classic' =>        $this->systemRepository->getClassicUrl('admin/tools'),
            'mode' =>           'Administrator Management Tools',
            'system' =>         $system,
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('admin/tools/index.html.twig', $parameters);
    }

    private function setMessage($affected, $start) {
        $duration = gmdate("H:i:s", time() - $start);
        $memory = Rxx::formatBytes(memory_get_peak_usage(),1);
        $message = sprintf(
            $this->translator->trans('%d records updated in %s (Used %s)'),
            $affected,
            $duration,
            $memory
        );
        $this->session->set('lastMessage', $message);
    }

    private function icaoImport() {
        $this->session->set('lastError', 'Not yet implemented');
    }

    private function listenersStats() {
        $this->session->set('lastError', 'Not yet implemented');
    }

    private function logsDx() {
        $start = time();
        $affected = $this->logRepository->updateDx();
        $this->setMessage($affected, $start);
    }

    private function logsDaytime() {
        $this->session->set('lastError', 'Not yet implemented');
    }

    private function signalsLatLon() {
        $start = time();
        $affected = $this->signalRepository->updateSignalLatLonFromGSQ();
        $this->setMessage($affected, $start);
    }

    private function signalsStats() {
        $start = time();
        $affected = $this->signalRepository->updateSignalStats(false, true);
        $this->setMessage($affected, $start);
    }

    private function systemExportDb() {
        $this->session->set('lastError', 'Not yet implemented');
    }

    private function systemEmailTest() {
        $this->session->set('lastError', 'Not yet implemented');
    }
}
