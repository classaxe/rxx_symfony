<?php
namespace App\Controller\Web\Signals;

use App\Form\Signals\SignalLogs as Form;
use App\Repository\SignalRepository;
use App\Repository\TypeRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class SignalLogs extends Base
{
    const defaultlimit =     1000;
    const defaultSorting =  'date';
    const defaultOrder =    'd';

    /**
     * @Route(
     *     "/{_locale}/{system}/signals/{id}/logs",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signal_logs"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param Request $request
     * @param Form $form
     * @param SignalRepository $signalRepository
     * @param TypeRepository $typeRepository
     * @return RedirectResponse|Response
     */
    public function controller(
        $_locale,
        $system,
        $id,
        Request $request,
        Form $form,
        SignalRepository $signalRepository,
        TypeRepository $typeRepository
    ) {
        if (!$signal = $this->getValidSignal($id, $signalRepository)) {
            return $this->redirectToRoute('signals', ['system' => $system]);
        }

        $options = [
            'limit' =>          static::defaultlimit,
            'order' =>          static::defaultOrder,
            'page' =>           0,
            'sort' =>           static::defaultSorting,
            'total' =>          $signal->getLogs()
        ];
        $form = $form->buildForm($this->createFormBuilder(), $options);
        $form->handleRequest($request);
        $args = [
            'limit' =>          static::defaultlimit,
            'order' =>          static::defaultOrder,
            'page' =>           0,
            'sort' =>           static::defaultSorting,
        ];
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();
        }
        $parameters = [
            'args' =>               $args,
            'id' =>                 $id,
            'columns' =>            $signalRepository->getLogsColumns(),
            'form' =>               $form->createView(),
            '_locale' =>            $_locale,
            'matched' =>            sprintf($this->translator->trans('of %s Log Records'), $options['total']),
            'mode' =>               sprintf($this->translator->trans('Logs for %s'), $signal->getFormattedIdent()),
            'records' =>            $signalRepository->getLogsForSignal($id, $args),
            'results' => [
                'limit' =>              isset($args['limit']) ? $args['limit'] : static::defaultlimit,
                'page' =>               isset($args['page']) ? $args['page'] : 0,
                'total' =>              $options['total']
            ],
            'system' =>             $system,
            'tabs' =>               $signalRepository->getTabs($signal),
            'typeRepository' =>     $typeRepository
        ];
        return $this->render('signal/logs.html.twig', $this->getMergedParameters($parameters));
    }
}
