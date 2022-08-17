<?php
namespace App\Controller\Web\Admin;

use App\Controller\Web\Base;

use App\Service\GeoService;
use App\Service\Visitor;
use App\Utils\Rxx;
use Swift_Mailer;
use Swift_Message;
use Swift_Transport_EsmtpTransport;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class Tools extends Base
{
    private $geoService;
    private $mailer;
    private $request;
    private $system;
    private $visitor;

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
     * @param Request $request
     * @param Swift_Mailer $mailer
     * @param GeoService $GeoService
     * @param Visitor $visitor
     * @return Response|void
     */
    public function controller(
        $_locale,
        $system,
        $tool,
        Request $request,
        Swift_Mailer $mailer,
        GeoService $GeoService,
        Visitor $visitor
    ) {
        $this->system = $system;
        $this->request = $request;
        $this->mailer = $mailer;
        $this->geoService = $GeoService;
        $this->visitor = $visitor;
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
            case 'logSessionStats':
                return $this->logsSessionStats();
            case 'signalsLatLon':
                return $this->signalsLatLon();
            case 'signalsStats':
                return $this->signalsStats();
            case 'systemExportDb':
                return $this->systemExportDb();
            case 'systemEmailTest':
                return $this->systemEmailTest();
            case 'systemGeoIpTest':
                return $this->systemGeoIpTest();
            case 'usersStats':
                return $this->userStats();
        }
        $this->session->set('lastError', '');
        $this->session->set('lastMessage', '');

        $parameters = [
            '_locale' =>        $_locale,
            'ip' =>             $this->visitor->getIpAddress(),
            'mode' =>           'Administrator Management Tools',
            'system' =>         $system,
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('admin/tools/index.html.twig', $parameters);
    }

    private function setMessage($mode, $submode, $affected, $start) {
        $duration = gmdate("H:i:s", time() - $start);
        $memory = $this->rxx::formatBytes(memory_get_peak_usage(),1);
        $message = sprintf(
            $this->i18n('<strong>%s / %s</strong><br />Updated %d records in %s (Used %s)'),
            $this->i18n($mode),
            $this->i18n($submode),
            $affected,
            $duration,
            $memory
        );
        $this->session->set('lastMessage', $message);
    }

    private function icaoImport() {
        $start =    time();
        $result = $this->icaoRepository->updateIcaoList();
        if ($result['error']) {
            $this->session->set('lastError', $result['error']);
        } else {
            $this->setMessage('ICAO Data', 'Get latest data', $result['affected'], $start);
        }
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

    private function logsSessionStats() {
        $start =    time();
        $affected = $this->listenerRepository->updateAllInvalidLogSessions();
        $this->setMessage('Logs', 'Update Log Session Stats', $affected, $start);

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
        $affected = $this->signalRepository->updateSignalStats(false, $updateStats, true);
        $this->setMessage('Signals', 'Update info from latest log data', $affected, $start);

        return $this->redirectToRoute('admin/tools', [ 'system' => $this->system ]);
    }

    private function systemExportDb() {
        $this->backupRepository->generate();
    }

    private function systemEmailTest()
    {
        $email = $this->request->query->get('email') ?? '';
        if ('' === $email) {
            $message = sprintf(
                $this->i18n('<strong>%s / %s</strong><br />No valid email was provided'),
                $this->i18n('System'),
                $this->i18n('Send Test Email')
            );
            $this->session->set('lastError', $message);

            return $this->redirectToRoute('admin/tools', [ 'system' => $this->system ]);
        }

        $admin =    $this->systemRepository::AUTHORS[0];
        $html =  $this->renderView('emails/admin/test.html.twig', [
            'admin' => $admin['name'],
            'system' => $this->system
        ]);
        $text = $this->renderView('emails/admin/test.txt.twig', [
            'admin' => $admin['name'] . ' ' . $admin['role'],
            'system' => $this->system
        ]);
        $message = (new Swift_Message('RNA / REU / RWW Test Message'))
            ->setFrom('rxx@classaxe.com')
            ->setReplyTo( [ $admin['email'] =>  $admin['name'] ] )
            ->setTo($email)
            ->setBody($html,'text/html')
            ->addPart($text,'text/plain');

        $transport = $this->mailer->getTransport();
        if ($transport instanceof Swift_Transport_EsmtpTransport){
            $transport->setStreamOptions([
                'ssl' => ['allow_self_signed' => true, 'verify_peer' => false, 'verify_peer_name' => false]
            ]);
        }
        $this->mailer->send($message);

        $message = sprintf(
            $this->i18n('<strong>%s / %s</strong><br />Sent test email message to %s'),
            $this->i18n('System'),
            $this->i18n('Send Test Email'),
            "&lt;<a href='mailto:$email'>$email</a>&gt;"
        );
        $this->session->set('lastMessage', $message);

        return $this->redirectToRoute('admin/tools', [ 'system' => $this->system ]);
    }

    private function systemGeoIpTest() {
        $IP = $this->request->query->get('ip') ?? '';
        if ('' === $IP) {
            $message = sprintf(
                $this->i18n('<strong>%s / %s</strong><br />No valid IP address was provided'),
                $this->i18n('System'),
                $this->i18n('GeoIP Test')
            );
            $this->session->set('lastError', $message);

            return $this->redirectToRoute('admin/tools', [ 'system' => $this->system ]);
        }

        $result = $this->geoService->getDetailsForIP($IP);
        $message = "<pre style='border: none; background: transparent; margin: 0;'>"
            . "GEOIP LOOKUP RESULT:\n--------------------\n"
            . preg_replace(
                '/(^Array|^\\(\n|^\\)\n|^\s*)/m',
                '', print_r($result, true))."</pre>";
        $this->session->set('lastMessage', $message);

        return $this->redirectToRoute('admin/tools', [ 'system' => $this->system ]);
    }

    private function userStats() {
        $start =    time();
        $affected = $this->userRepository->updateUserStats();
        $this->setMessage('Users', 'Update stats', $affected, $start);

        return $this->redirectToRoute('admin/tools', [ 'system' => $this->system ]);
    }


}
