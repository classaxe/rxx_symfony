<?php
namespace App\Controller\Web\Wwsu;

use App\Controller\Web\Base;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Logs
 * @package App\Controller\Web
 */
class Wwsu extends Base
{
    /**
     * @Route(
     *     "/{_locale}/rww/wwsu/listener/{wwsu_key}/status",
     *     name="wwsu_listener_status"
     * )
     * @param $wwsu_key
     * @return Response
     */
    public function listener_status(
        $wwsu_key
    ) {
        $record = $this->listenerRepository->getListenerForWwsuKey($wwsu_key);
        $out = json_encode($record);
        $textResponse = new Response($out , 200);
        $textResponse->headers->set('Content-Type', 'application/json');

        return $textResponse;
    }
}
