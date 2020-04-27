<?php
namespace App\Controller\Web\Admin;

use App\Controller\Web\Base;
use App\Repository\BackupRepository;
use App\Utils\Rxx;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class Tools extends Base
{
    private $backupRepository;
    private $system;

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
     * @return Response|void
     */
    public function controller(
        $_locale,
        $system,
        $tool,
        BackupRepository $backupRepository
    ) {
        $this->system = $system;
        $this->backupRepository = $backupRepository;
        if (!$this->parameters['isAdmin']) {
            $this->session->set('route', 'admin/tools');
            return $this->redirectToRoute('logon', ['system' => $system]);
        }
        $this->session->set('route', '');
        switch ($tool) {
            case 'icaoImport':
                return $this->icaoImport();
            case 'listenersStats':
                return $this->listenersStats();
            case 'logsDx':
                return $this->logsDx();
            case 'logsDaytime':
                return $this->logsDaytime();
            case 'signalsLatLon':
                return $this->signalsLatLon();
            case 'signalsStats':
                return $this->signalsStats();
            case 'systemExportDb':
                return $this->systemExportDb();
            case 'systemEmailTest':
                return $this->systemEmailTest();
        }
        $this->session->set('lastError', '');
        $this->session->set('lastMessage', '');

        $parameters = [
            '_locale' =>        $_locale,
            'classic' =>        $this->systemRepository->getClassicUrl('admin/tools'),
            'mode' =>           'Administrator Management Tools',
            'system' =>         $system,
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('admin/tools/index.html.twig', $parameters);
    }

    private function setError($mode, $submode) {
        $message = sprintf(
            $this->i18n('<br /><strong>%s / %s</strong> is not yet implemented'),
            $this->i18n($mode),
            $this->i18n($submode)
        );
        $this->session->set('lastError', $message);
    }

    private function setMessage($mode, $submode, $affected, $start) {
        $duration = gmdate("H:i:s", time() - $start);
        $memory = Rxx::formatBytes(memory_get_peak_usage(),1);
        $message = sprintf(
            $this->i18n('<strong>%s / %s</strong><br />Updated %d records in %s (Used %s)'),
            $mode,
            $submode,
            $affected,
            $duration,
            $memory
        );
        $this->session->set('lastMessage', $message);
    }

    private function icaoImport() {
        $this->setError('ICAO Data', 'Get latest data');

        return $this->redirectToRoute('admin/tools', [ 'system' => $this->system ]);
    }

    private function listenersStats() {
        $start =    time();
        $affected = $this->listenerRepository->updateListenerStats();
        $this->setMessage('Listeners', 'Update log counts', $affected, $start);

        return $this->redirectToRoute('admin/tools', [ 'system' => $this->system ]);
    }

    private function logsDx() {
        $start =    time();
        $affected = $this->logRepository->updateDx();
        $this->setMessage('Logs', 'Recalculate all distances', $affected, $start);

        return $this->redirectToRoute('admin/tools', [ 'system' => $this->system ]);
    }

    private function logsDaytime() {
        $start =    time();
        $affected = $this->logRepository->updateDaytime();
        $this->setMessage('Logs', 'Mark daytime loggings', $affected, $start);

        return $this->redirectToRoute('admin/tools', [ 'system' => $this->system ]);
    }

    private function signalsLatLon() {
        $start = time();
        $affected = $this->signalRepository->updateSignalLatLonFromGSQ();
        $this->setMessage('Signals', 'Update Lat and Lon from GSQ', $affected, $start);

        return $this->redirectToRoute('admin/tools', [ 'system' => $this->system ]);
    }

    private function signalsStats() {
        $start = time();
        $mysql = $this->systemRepository->getMySQLVersion();
        $updateStats = (float)substr($mysql,0,3) > 5.5;
        $affected = $this->signalRepository->updateSignalStats(false, $updateStats);
        $this->setMessage('Signals', 'Update info from latest log data', $affected, $start);

        return $this->redirectToRoute('admin/tools', [ 'system' => $this->system ]);
    }

    private function systemExportDb() {
        $this->backupRepository->generate();
    }

    private function systemEmailTest() {
        $this->setError('System', 'Send Test Email');

        return $this->redirectToRoute('admin/tools', [ 'system' => $this->system ]);
    }
}
