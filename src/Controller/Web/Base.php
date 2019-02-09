<?php
namespace App\Controller\Web;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\LanguageRepository;
use App\Repository\ModeRepository;
use App\Repository\SystemRepository;
use App\Utils\Rxx;
use Symfony\Component\HttpKernel\KernelInterface as Kernel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class Base
 * @package App\Controller\Base
 */
class Base extends AbstractController
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
     * @var Kernel
     */
    protected $kernel;

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
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Base constructor.
     * @param ModeRepository $modeRepository
     * @param Rxx $rxx
     * @param SystemRepository $systemRepository
     * @param SessionInterface $session
     */
    public function __construct(
        Kernel $kernel,
        ModeRepository $modeRepository,
        Rxx $rxx,
        SystemRepository $systemRepository,
        SessionInterface $session,
        EntityManagerInterface $em,
        TranslatorInterface $translator,
        LanguageRepository $languageRepository
    ) {
        $this->kernel =             $kernel;
        $this->languageRepository = $languageRepository;
        $this->modeRepository =     $modeRepository;
        $this->rxx =                $rxx;
        $this->systemRepository =   $systemRepository;
        $this->translator =         $translator;
        $this->session =            $session;
        $this->parameters = [
            'isAdmin' =>        $this->session->get('isAdmin', 0),
            'lastError' =>      $this->session->get('lastError', ''),
            'lastMessage' =>    $this->session->get('lastMessage', ''),
            'languages' =>      $this->languageRepository->getAll(),
            'modes' =>          $this->modeRepository->getAll(),
            'systems' =>        $this->systemRepository->getAll(),
        ];
        $dsn = getenv('DATABASE_URL');
        if (in_array($dsn, ['', 'mysql://db_user:db_password@127.0.0.1:3306/db_name'])) {
            print
                (php_sapi_name() == "cli" ? "" : "<pre>")
                ."***************\n"
                ."* RXX SYMFONY *\n"
                ."***************\n"
                ."ERROR: A database connection MUST be defined either in your project .env file or the web server"
                ." configuration.\n"
                ."\n"
                ."There are two solutions available to you - choose one of them.\n"
                ."IDEALLY you should change the credentials given in the DSN connection strings that follow.\n"
                ."\n"
                ."  1) If you are using Apache, and are NOT hosting any other symfony applications on this vhost,\n"
                ."     you may add a line like this to the vhost HTTP / HTTPS sections:\n"
                ."\n"
                ."       SetEnv DATABASE_URL \"mysql://rxx:clamp_rxx_777@localhost:3306/rxx?charset=UTF8\"\n"
                ."\n"
                ."  2) Add a line like this to your project .env file in the doctrine/doctrine-bundle section:\n"
                ."\n"
                ."       DATABASE_URL=\"mysql://rxx:clamp_rxx_777@localhost:3306/rxx?charset=UTF8\"\n"
                ."\n"
                ."     You should also comment out any existing default DATABASE_URL entry by prefixing it with #\n"
                .(php_sapi_name() == "cli" ? "" : "</pre>");
            die();
        }
        try {
            $em->getConnection()->connect();
            $connected = $em->getConnection()->isConnected();
        } catch (\Exception $e) {
            $connected  = false;
        }
        if (!$connected) {
            $b = parse_url($dsn);
            $db = trim($b['path'], '/');
            print
                (php_sapi_name() == "cli" ? "" : "<pre>")
                ."***************\n"
                ."* RXX SYMFONY *\n"
                ."***************\n"
                ."ERROR: Database settings are configured but the database is not connected.\n"
                ."\n"
                ."Please run the following commands ONE AT A TIME to set up the database.\n"
                ."You will be prompted for the mysql root password for each operation:\n"
                ."\n"
                ."    echo \"GRANT DELETE, INSERT, SELECT, UPDATE ON {$db}.* to {$b['user']}@localhost"
                ." identified by '{$b['pass']}'\" | mysql -uroot -p\n"
                ."    echo \"DROP SCHEMA IF EXISTS {$db}; CREATE SCHEMA {$db}\" | mysql -uroot -p\n"
                ."    wget -qO- https://www.classaxe.com/dx/ndb/rxx.sql.gz | gunzip |  mysql -uroot -p {$db}\n"
                ."\n"
                ."If you still see this message after performing these steps, your mysql socket may be incorrect.\n"
                ."Try running this command to create a symlink for your mysql default socket:\n"
                ."\n"
                ."    ln -s /var/run/mysqld/mysqld.sock /tmp/mysql.sock\n"
                .(php_sapi_name() == "cli" ? "" : "</pre>");
            die();
        }
    }

    /**
     * @param array $parameters
     * @return array
     */
    public function getMergedParameters($parameters = [])
    {
        $this->session->set('lastError', '');
        $this->session->set('lastMessage', '');
        return array_merge($parameters, $this->parameters);
    }

    /**
     * @param string      $id         The message id (may also be an object that can be cast to string)
     * @param array       $parameters An array of parameters for the message
     * @param string|null $domain     The domain for the message or null to use the default
     * @param string|null $_locale     The locale or null to use the default
     *
     * @return string The translated string
     *
     * @throws \InvalidArgumentException If the locale contains invalid characters
     */
    public function i18n($id, array $parameters = array(), $domain = null, $_locale = null) {
        return $this->translator->trans($id, $parameters, $domain, $_locale);
    }
}
