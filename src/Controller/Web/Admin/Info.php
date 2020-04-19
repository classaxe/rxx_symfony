<?php
namespace App\Controller\Web\Admin;

use App\Controller\Web\Base;
use DOMDocument;
use Symfony\Component\HttpFoundation\Response;
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
     * @param $_locale
     * @param $system
     * @return Response
     */
    public function controller(
        $_locale,
        $system
    ) {
        if (!$this->parameters['isAdmin']) {
            $this->session->set('route', 'admin/info');
            return $this->redirectToRoute('logon', ['system' => $system]);
        }
        $entries = $this->getGitInfo();
        $parameters = [
            '_locale' =>        $_locale,
            'changelog' =>      "<p>".implode("</p><p></p>", $entries)."</p>",
            'classic' =>        $this->systemRepository->getClassicUrl('admin/info'),
            'countEntries' =>   count($entries),
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

    private function getPhpInfo() : string
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

    private function innerHTML($element) : string
    {
        $doc = $element->ownerDocument;
        $html = '';
        foreach ($element->childNodes as $node) {
            $html .= $doc->saveHTML($node);
        }
        return $html;
    }

}
