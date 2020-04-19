<?php
namespace App\Controller\Web\Admin;

use App\Controller\Web\Base;
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

    private function icaoImport() {
        $this->session->set('lastError', 'Not yet implemented');
    }

    private function listenersStats() {
        $this->session->set('lastError', 'Not yet implemented');
    }

    private function logsDx() {
        $this->session->set('lastError', 'Not yet implemented');
    }

    private function logsDaytime() {
        $this->session->set('lastError', 'Not yet implemented');
    }

    private function signalsLatLon() {
        $affected = $this->signalRepository->updateSignalLatLonFromGSQ();
        $message = sprintf($this->translator->trans('Operation complete - %d record(s) were updated'), $affected);
        $this->session->set('lastMessage', $message);
    }

    private function signalsStats() {
        $affected = $this->signalRepository->updateSignalStats(false, true);
        $message = sprintf($this->translator->trans('Operation complete - %d record(s) were updated'), $affected);
        $this->session->set('lastMessage', $message);
    }

    private function systemExportDb() {
        $this->session->set('lastError', 'Not yet implemented');
    }

    private function systemEmailTest() {
        $this->session->set('lastError', 'Not yet implemented');
    }
}
