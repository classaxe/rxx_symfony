<?php
namespace App\Controller\Web\Listener;

use App\Controller\Web\Listener\Base;
use App\Form\Listener\Weather as ListenerWeatherForm;
use App\Repository\ListenerRepository;
use App\Repository\IcaoRepository;

use App\Utils\Rxx;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Listeners
 * @package App\Controller\Web\Listener
 */
class Weather extends Base
{

    /**
     * @Route(
     *     "/{system}/listeners/{id}/weather",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_weather"
     * )
     */
    public function weatherController(
        $system,
        $id,
        Request $request,
        IcaoRepository $icaoRepository,
        ListenerWeatherForm $listenerWeatherForm,
        ListenerRepository $listenerRepository
    ) {
        if (!$listener = $this->getValidListener($id, $listenerRepository)) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }
        $weather = false;
        $options = [
            'hours'     =>  '12',
            'id'        =>  $id,
            'icao'      =>  '',
            'lat'       =>  $listener->getLat(),
            'lon'       =>  $listener->getLon(),
            'limit'     =>  10
        ];
        $form = $listenerWeatherForm->buildForm(
            $this->createFormBuilder(),
            $options
        );
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $form_data = $form->getData();
            $data['form'] = $form_data;

            $weather = $icaoRepository::getMetar($form_data['icao'], $form_data['hours']);
            if ($weather) {
                $icao = $icaoRepository->getLocalIcaos(
                    $listener->getLat(),
                    $listener->getLon(),
                    1,
                    $form_data['icao']
                )[0];
                array_unshift(
                    $weather,
                    "QNH at ".$icao['icao']." - "
                    . $icao['name']
                    . ($icao['sp'] ? ", " . $icao['sp'] : "")
                    . ", " . $icao['cnt'] . "\n"
                    . "(".$icao['mi']." miles / ".$icao['km']." km from QTH)\n"
                    . "----------------------\n"
                    . "DD UTC  MB     SLP \n"
                    . "----------------------"
                );
                $weather[] =
                    "----------------------\n"
                    . "(Weather via ".strToUpper($system).")";
            } else {
                $weather[] = "(No data available from ".$form_data['icao']." for the last ".$form_data['hours']." hours)";
            }
        }

        $parameters = [
            'id' =>                 $id,
            'fieldGroups' =>        $listenerWeatherForm->getFieldGroups(),
            'form' =>               $form->createView(),
            'mode' =>               $listener->getName().' &gt; Weather',
            'system' =>             $system,
            'tabs' =>               $listenerRepository->getTabs($listener),
            'weather' =>            $weather
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listener/weather.html.twig', $parameters);
    }
}
