<?php
namespace App\Controller\Web\Channels;

use App\Controller\Web\Base;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Logs
 * @package App\Controller\Web
 */
class Channels extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/channels",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="channels"
     * )
     * @param $_locale
     * @param $system
     *
     * @return Response
     */
    public function channels(
        $_locale,
        $system
    ) {
        $i18n = $this->translator;
        $minDate = '2023-01-01';
        $minTimes = 5;
        $regions = $this->regionRepository->getAllOptions(false, false);
        $regionCodes = array_values($regions);
        $groupedSlots = [];
        foreach ($regionCodes as $r) {
            $groupedSlots[$r] = 0;
        }
        $data = $this->statsRepository->getChannels($minDate, $minTimes);
        $grouped = [];
        foreach ($data as $row) {
            if (!isset($grouped[$row['khz']])) {
                $grouped[$row['khz']] = $groupedSlots;
            }
            $grouped[$row['khz']][$row['region']] = $row['stations'];
        }
        $table = "
            <table border='1' class='channels' style='border-collapse: collapse'>
                <thead>
                    <tr>
                        <th>KHz</th>";
        foreach ($regions as $label => $value) {
            $table .= "<th>" . $label . "</th>";
        }
        $table .= "
                    </tr>
                </thead>
                <tbody>";
        foreach ($grouped as $khz => $stations) {
            $table .= "<tr>\n    <th>" . (0 + $khz) . "</th>";
            foreach ($regionCodes as $regionCode) {
                $table .= "<td>" . ($stations[$regionCode] ? $stations[$regionCode] : "") . "</td>";
            }
            $table .= "</tr>";
        }
        $table .= "</tbody>\n</table>";

        $parameters = [
            '_locale' =>    $_locale,
            'mode' =>       $i18n->trans('Channels'),
            'system' =>     $system,
            'minTimes' =>   $minTimes,
            'minDate' =>    $minDate,
            'table' =>      $table
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('channels/channels.html.twig', $parameters);

    }

}
