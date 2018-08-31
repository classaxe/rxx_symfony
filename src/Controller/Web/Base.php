<?php
namespace App\Controller\Web;

use App\Repository\ModeRepository;
use App\Repository\SystemRepository;
use App\Utils\Rxx;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class Base
 * @package App\Controller\Base
 */
class Base extends Controller
{

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
     * Base constructor.
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
            'isAdmin' =>        $this->session->get('isAdmin', 0),
            'lastError' =>      $this->session->get('lastError', ''),
            'lastMessage' =>    $this->session->get('lastMessage', ''),
            'modes' =>          $this->modeRepository->getAll(),
            'systems' =>        $this->systemRepository->getAll(),
        ];
    }

    public function getMergedParameters($parameters = [])
    {
        $this->session->set('lastError', '');
        $this->session->set('lastMessage', '');
        return array_merge($parameters, $this->parameters);
    }
}
