<?php
namespace App\Controller\Web\Admin;

use App\Controller\Web\Base;
use DOMDocument;
use DOMElement;
use Symfony\Component\Routing\Annotation\Route;

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
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="admin/info"
     * )
     */
    public function adminInfoController(
        $_locale,
        $system
    ) {
        if (!$this->parameters['isAdmin']) {
            throw $this->createAccessDeniedException('You must be an Administrator to access this resource');
        }

        $parameters = [
            '_locale' =>        $_locale,
            'changelog' =>      "<p>".implode("</p><p></p>", $this->getGitInfo())."</p>",
            'classic' =>        $this->systemRepository->getClassicUrl('admin/info'),
            'mode' =>           'System Info',
            'info' =>           $this->getPhpInfo(),
            'php_version' =>    phpversion(),
            'system' =>         $system,
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('admin/info/index.html.twig', $parameters);
    }

    private function getGitInfo()
    {
        $changelog = explode("\n", `git log master --pretty=format:"%ad %s" --date=short`);
        $entries = [];
        foreach ($changelog as &$entry) {
            $bits =     explode(' ', $entry);
            $date =     array_shift($bits);
            $version =  trim(array_shift($bits), ':');
            $details =  implode(' ', $bits);
            $entries[] =    '<strong>'.$version.'</strong> <em>('.$date.')</em><br />'.$details;
        }
        return $entries;
    }

    private function getPhpInfo()
    {
        $doc = new DOMDocument();
        ob_start();
        phpinfo();
        $doc->loadHtml(ob_get_contents());
        ob_get_clean();
        return $this->innerHTML(
            $doc->getElementsByTagName('div')->item(0)
        );
    }

    private function innerHTML(DOMElement $element)
    {
        $doc = $element->ownerDocument;
        $html = '';
        foreach ($element->childNodes as $node) {
            $html .= $doc->saveHTML($node);
        }
        return $html;
    }

}
