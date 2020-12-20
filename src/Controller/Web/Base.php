<?php
namespace App\Controller\Web;

use App\Repository\AwardRepository;
use App\Repository\BackupRepository;
use App\Repository\CleRepository;
use App\Repository\CountryRepository;
use App\Repository\IcaoRepository;
use App\Repository\LanguageRepository;
use App\Repository\ListenerRepository;
use App\Repository\LogRepository;
use App\Repository\MapRepository;
use App\Repository\ModeRepository;
use App\Repository\PaperRepository;
use App\Repository\RegionRepository;
use App\Repository\SignalRepository;
use App\Repository\StatsRepository;
use App\Repository\SystemRepository;
use App\Repository\ToolRepository;
use App\Repository\TypeRepository;
use App\Repository\UserRepository;
use App\Repository\WeatherRepository;

use App\Utils\Rxx;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
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
    protected $kernel;
    protected $parameters = [];
    protected $rxx;
    protected $session;
    protected $translator;

    protected $awardRepository;
    protected $backupRepository;
    protected $cleRepository;
    protected $countryRepository;
    protected $icaoRepository;
    protected $languageRepository;
    protected $listenerRepository;
    protected $logger;
    protected $logRepository;
    protected $mapRepository;
    protected $modeRepository;
    protected $paperRepository;
    protected $regionRepository;
    protected $signalRepository;
    protected $statsRepository;
    protected $systemRepository;
    protected $toolRepository;
    protected $typeRepository;
    protected $userRepository;
    protected $weatherRepository;

    /**
     * Base constructor.
     * @param EntityManagerInterface $em
     * @param Kernel $kernel
     * @param Rxx $rxx
     * @param SessionInterface $session
     * @param TranslatorInterface $translator
     *
     * Auto-wire these repositories:
     * @param AwardRepository $awardRepository
     * @param BackupRepository $backupRepository
     * @param CleRepository $cleRepository;
     * @param CountryRepository $countryRepository
     * @param IcaoRepository $icaoRepository
     * @param LanguageRepository $languageRepository
     * @param ListenerRepository $listenerRepository
     * @param LoggerInterface $logger
     * @param LogRepository $logRepository
     * @param MapRepository $mapRepository
     * @param ModeRepository $modeRepository
     * @param PaperRepository $paperRepository
     * @param RegionRepository $regionRepository
     * @param SignalRepository $signalRepository
     * @param StatsRepository $statsRepository
     * @param SystemRepository $systemRepository
     * @param ToolRepository $toolRepository
     * @param TypeRepository $typeRepository
     * @param UserRepository $userRepository
     * @param WeatherRepository $weatherRepository

     */
    public function __construct(
        EntityManagerInterface $em,

        Kernel $kernel,
        LoggerInterface $logger,
        Rxx $rxx,
        SessionInterface $session,
        TranslatorInterface $translator,

        AwardRepository $awardRepository,
        BackupRepository $backupRepository,
        CleRepository $cleRepository,
        CountryRepository $countryRepository,
        IcaoRepository $icaoRepository,
        LanguageRepository $languageRepository,
        ListenerRepository $listenerRepository,
        LogRepository $logRepository,
        MapRepository $mapRepository,
        ModeRepository $modeRepository,
        PaperRepository $paperRepository,
        RegionRepository $regionRepository,
        SignalRepository $signalRepository,
        StatsRepository $statsRepository,
        SystemRepository $systemRepository,
        ToolRepository $toolRepository,
        TypeRepository $typeRepository,
        UserRepository $userRepository,
        WeatherRepository $weatherRepository
    ) {
        $this->kernel =             $kernel;
        $this->logger =             $logger;
        $this->rxx =                $rxx;
        $this->session =            $session;
        $this->translator =         $translator;

        $this->awardRepository =    $awardRepository;
        $this->backupRepository =   $backupRepository;
        $this->cleRepository =      $cleRepository;
        $this->countryRepository =  $countryRepository;
        $this->icaoRepository =     $icaoRepository;
        $this->languageRepository = $languageRepository;
        $this->listenerRepository = $listenerRepository;
        $this->logRepository =      $logRepository;
        $this->mapRepository =      $mapRepository;
        $this->modeRepository =     $modeRepository;
        $this->paperRepository =    $paperRepository;
        $this->regionRepository =   $regionRepository;
        $this->signalRepository =   $signalRepository;
        $this->statsRepository =    $statsRepository;
        $this->systemRepository =   $systemRepository;
        $this->toolRepository =     $toolRepository;
        $this->typeRepository =     $typeRepository;
        $this->userRepository =     $userRepository;
        $this->weatherRepository =  $weatherRepository;

        $this->parameters = [
            'gitAge' =>         $this->rxx->getGitAge(),
            'gitNew' =>         $this->systemRepository::NEW_VERSION_AGE,
            'gitTag' =>         $this->rxx->getGitTag(),
            'access' =>         $this->session->get('access', 0),
            'isAdmin' =>        $this->session->get('isAdmin', 0),
            'isDev' =>          getEnv('APP_ENV') === 'dev',
            'isMember' =>       $this->session->get('isMember', 0),
            'lastError' =>      $this->session->get('lastError', ''),
            'lastMessage' =>    $this->session->get('lastMessage', ''),
            'languages' =>      $this->languageRepository->getAll(),
            'modes' =>          $this->modeRepository->getAll(),
            'user_id' =>        $this->session->get('user_id', ''),
            'user_email' =>     $this->session->get('user_email', ''),
            'user_name' =>      $this->session->get('user_name', ''),
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
        } catch (Exception $e) {
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

    public function isAdmin()
    {
        return $this->parameters['isAdmin'];
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
    public function i18n($id, array $parameters = [], $domain = null, $_locale = null) {
        return $this->translator->trans($id, $parameters, $domain, $_locale);
    }

    protected function setValueFromRequest(&$args, $request, $field, $options = false, $letterCase = false)
    {
        if (($value = $request->query->get($field)) && $value !== '') {
            switch ($letterCase) {
                case 'a':
                    $value = strtolower($value);
                    break;
                case 'A':
                    $value = strtoupper($value);
                    break;
            }
            if (false === $options || in_array($value, $options)) {
                $args[$field] = addslashes($value);
            }
        }
    }

    protected function setListenersFromRequest(&$args, $request)
    {
        if ($request->query->get('listeners')) {
            $args['listener'] = [];
            $values = explode(',', $request->query->get('listeners'));
            foreach ($values as $v) {
                if ($this->listenerRepository->find((int) $v)) {
                    $args['listener'][] = $v;
                }
            }
        }
    }

    protected function setPairFromRequest(&$args, $request, $field)
    {
        if ($request->query->get($field)) {
            $values = explode(',', $request->query->get($field));
            switch (count($values)) {
                case 1:
                    $args[$field . '_1'] = addslashes($values[0]);
                    $args[$field . '_2'] = addslashes($values[0]);
                    break;
                case 2:
                    $args[$field . '_1'] = addslashes($values[0]);
                    $args[$field . '_2'] = addslashes($values[1]);
                    break;
            }
        }

    }
    protected function setPagingFromRequest(&$args, $request)
    {
        $limits = [ -1, 10, 20, 50, 100, 200, 500, 1000, 2000, 5000, 100000, 20000, 50000, 100000 ];
        $this->setValueFromRequest($args, $request, 'limit', $limits);

        $orders = [ 'a', 'd' ];
        $this->setValueFromRequest($args, $request, 'order', $orders, 'a');

        if ($page = (int) $request->query->get('page')) {
            if ($page >= 0) {
                $args['page'] = $page;
            }
        }
        $this->setValueFromRequest($args, $request, 'sort');
    }

    protected function setPersonaliseFromRequest(&$args, $request)
    {
        if ($listenerID = (int) $request->query->get('personalise')) {
            if ($this->listenerRepository->find($listenerID)) {
                $args['personalise'] = $listenerID;
            }
        }
    }

    protected function setRegionFromRequest(&$args, $request)
    {
        $regions = ['af', 'an', 'as', 'ca', 'eu', 'iw', 'na', 'oc', 'sa', 'xx'];
        $this->setValueFromRequest($args, $request, 'region', $regions, 'a');
    }

    protected function setRwwFocusFromRequest(&$args, $request)
    {
        if ('rww' === $args['system']) {
            $regions = ['af', 'an', 'as', 'ca', 'eu', 'iw', 'na', 'oc', 'sa', 'xx'];
            $this->setValueFromRequest($args, $request, 'rww_focus', $regions, 'a');
        }
    }

    protected function setTimezoneFromRequest(&$args, $request)
    {
        $value = $request->query->get('timezone', false);
        if ($value !== false) {
            $args['timezone'] = addslashes($value);
        }

    }
    protected function setTypeFromRequest(&$args, $request)
    {
        if ($request->query->get('types')) {
            $types =        strtoupper($request->query->get('types'));
            $types =        'ALL' === $types ? $this->typeRepository->getAllTypesAsCsv() : $types;
            $values =       explode(',', $types);
            $args['type'] = [];
            foreach ($values as $v) {
                if ($this->typeRepository->getSignalTypesSearched([$v])){
                    $args['type'][] = $v;
                }
            }
        }
    }
}
