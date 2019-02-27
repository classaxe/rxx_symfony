<?php
namespace App\Controller\Web\Admin;

use App\Controller\Web\Base;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\Rxx;

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class Info extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/admin/info",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="admin/info"
     * )
     */
    public function adminInfoController(
        $_locale,
        $system
    ) {

        $doc = new \DOMDocument();
        ob_start();
        phpinfo();
        $doc->loadHtml(ob_get_contents());
        ob_get_clean();

        $info = $this->innerHTML(
            $doc->getElementsByTagName('div')->item(0)
        );

        $changelog = explode("\n", `git log master --pretty=format:"%ad %s" --date=short`);
        foreach ($changelog as &$entry) {
            $bits =     explode(' ', $entry);
            $date =     array_shift($bits);
            $version =  trim(array_shift($bits), ':');
            $details =  implode(' ', $bits);
            $entry =    '<strong>'.$version.'</strong> <em>('.$date.')</em><br />'.$details;
        }
        $changelog = "<p>".implode("</p><p></p>", $changelog)."</p>";

        $parameters = [
            '_locale' =>        $_locale,
            'changelog' =>      $changelog,
            'mode' =>           'System Info',
            'info' =>           $info,
            'system' =>         $system,
        ];
        $parameters = array_merge($parameters, $this->parameters);

        return $this->render('admin/info/index.html.twig', $parameters);
    }

    function innerHTML(\DOMElement $element)
    {
        $doc = $element->ownerDocument;

        $html = '';

        foreach ($element->childNodes as $node) {
            $html .= $doc->saveHTML($node);
        }

        return $html;
    }

}
