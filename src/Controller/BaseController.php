<?php
namespace App\Controller;

use App\Repository\ModeRepository;
use App\Repository\SystemRepository;
use App\Utils\Rxx;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class BaseController
 * @package App\Controller
 */
class BaseController extends Controller {

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var ModeRepository
     */
    protected $modeRepository;

    /**
     * @var Rxx
     */
    protected $rxx;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var SystemRepository
     */
    protected $systemRepository;

    /**
     * BaseController constructor.
     * @param ModeRepository $modeRepository
     * @param Rxx $rxx
     * @param SystemRepository $systemRepository
     * @param SessionInterface $session
     */
    public function __construct(
        ModeRepository $modeRepository,
        Rxx $rxx,
        SystemRepository $systemRepository,
        SessionInterface $session
    ) {
        $this->modeRepository =     $modeRepository;
        $this->rxx =                $rxx;
        $this->systemRepository =   $systemRepository;
        $this->session =            $session;
        $this->parameters = [
            'isAdmin' =>    $session->get('isAdmin', 0),
            'lastError' =>  $session->get('lastError', ''),
            'modes' =>      $modeRepository->getAll(),
            'systems' =>    $systemRepository->getAll(),
        ];
    }
}