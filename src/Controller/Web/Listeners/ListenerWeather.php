<?php
namespace App\Controller\Web\Listeners;

use App\Form\Listeners\ListenerWeather as ListenerWeatherForm;
use App\Repository\ListenerRepository;
use App\Repository\IcaoRepository;

use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Listeners
 * @package App\Controller\Web\Listener
 */
class ListenerWeather extends Base
{

    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/weather",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_weather"
     * )
     */
    public function controller(
        $_locale,
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
        if ($form->isSubmitted() && $form->isValid()) {
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
                $weather[] = "(No data available from station ".$form_data['icao']." for the last ".$form_data['hours']." hours)";
            }
        }

        $parameters = [
            'id' =>                 $id,
            'fieldGroups' =>        $listenerWeatherForm->getFieldGroups(),
            'form' =>               $form->createView(),
            '_locale' =>            $_locale,
            'mode' =>               $listener->getName().' &gt; Weather',
            'system' =>             $system,
            'tabs' =>               $listenerRepository->getTabs($listener),
            'weather' =>            $weather
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listener/weather.html.twig', $parameters);
    }
}
