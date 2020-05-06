<?php
namespace App\Controller\Web\Admin;

use App\Controller\Web\Base;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class Help extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/admin/help",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="admin/help"
     * )
     */
    public function adminHelpController(
        $_locale,
        $system
    ) {
        if (!$this->parameters['isAdmin']) {
            throw $this->createAccessDeniedException('You must be an Administrator to access this resource');
        }
        $parameters = [
            '_locale' =>    $_locale,
            'classic' =>    $this->systemRepository->getClassicUrl('admin/help'),
            'mode' =>       'Admin Help',
            'system' =>     $system,
        ];
        $parameters = array_merge($parameters, $this->parameters);

        return $this->render('admin/help/index.html.twig', $parameters);
    }
}
