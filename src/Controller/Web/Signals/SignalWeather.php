<?php
namespace App\Controller\Web\Signals;

use App\Form\Signals\SignalWeather as SignalWeatherForm;
use App\Repository\SignalRepository;
use App\Repository\IcaoRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Listeners
 * @package App\Controller\Web\Signals
 */
class SignalWeather extends Base
{

    /**
     * @Route(
     *     "/{_locale}/{system}/signals/{id}/weather",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signal_weather"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param Request $request
     * @param IcaoRepository $icaoRepository
     * @param SignalWeatherForm $signalWeatherForm
     * @param SignalRepository $signalRepository
     * @return RedirectResponse|Response
     */
    public function controller(
        $_locale,
        $system,
        $id,
        Request $request,
        IcaoRepository $icaoRepository,
        SignalWeatherForm $signalWeatherForm,
        SignalRepository $signalRepository
    ) {
        if (!$signal = $this->getValidSignal($id, $signalRepository)) {
            return $this->redirectToRoute('signals', ['system' => $system]);
        }
        $weather = false;
        $options = [
            'hours'     =>  '12',
            'id'        =>  $id,
            'icao'      =>  '',
            'lat'       =>  $signal->getLat(),
            'lon'       =>  $signal->getLon(),
            'limit'     =>  10
        ];
        $form = $signalWeatherForm->buildForm(
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
                    $signal->getLat(),
                    $signal->getLon(),
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
            'fieldGroups' =>        $signalWeatherForm->getFieldGroups(),
            'form' =>               $form->createView(),
            '_locale' =>            $_locale,
            'mode' =>               sprintf($this->translator->trans('Weather for %s'), $signal->getFormattedIdent()),
            'system' =>             $system,
            'tabs' =>               $signalRepository->getTabs($signal),
            'weather' =>            $weather
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('signal/weather.html.twig', $parameters);
    }
}
