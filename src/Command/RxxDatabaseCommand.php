<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

class RxxDatabaseCommand extends Command
{
    /**
     * @var Stopwatch
     */
    private $stopwatch;

    /**
     * @param Stopwatch $stopwatch
     */
    public function __construct(Stopwatch $stopwatch)
    {
        $this->stopwatch = $stopwatch;
        parent::__construct();
    }

    protected function configure() {
        $this
            ->setName('rxx:update')
            ->setDescription('Downloads and installs the latest available database to a local dev instance');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $tmp_gz = sys_get_temp_dir().'/rxx.sql.gz';
        $url = "https://www.classaxe.com/dx/ndb/rxx.sql.gz";
        $newPwd = '777';
        $messages = [
            'Update the RXX database from latest live backup',
            'Not for use on a production server (or you\'d lose recent data)',
            'Beginning download of file    <info>%s</info>',
            'Download complete to          <info>%s</info>',
            'Populating database from      <info>%s</info>',
            'Populated database from       <info>%s</info>',
            'Setting all user passwords to <info>%s</info>',
            'Set all user passwords to     <info>%s</info>',
            'RXX now has the latest data',
        ];

        $io = new SymfonyStyle($input, $output);
        $io->title($messages[0]);

        if (getenv('APP_ENV') !== 'dev') {
            $io->error($messages[1]);
            return 1;
        }

        $this->stopwatch->start('update');

        $io->comment(sprintf($messages[2], $url));
        file_put_contents($tmp_gz, fopen($url, 'rb'));
        $io->comment(sprintf($messages[3], $tmp_gz));

        $io->comment(sprintf($messages[4], $tmp_gz));
        exec('zcat /tmp/rxx.sql.gz | MYSQL_PWD=root mysql -uroot rxx');
        $io->comment(sprintf($messages[5], $tmp_gz));

        $io->comment(sprintf($messages[6], $newPwd));
        exec("echo \"UPDATE rxx.users SET password='\\\$2y\\\$10\\\$WPcwyLosEfHA.tk3LKcBluumFLaLQJGcIfU7eo/i5z5YWzIEy4DGO'\" | MYSQL_PWD=root mysql -uroot");
        $io->comment(sprintf($messages[7], $newPwd));

        $io->success($messages[8]);
        $this->stopwatch($io, $this->stopwatch->stop('update'));

        return 0;
    }

    /**
     * @param SymfonyStyle $io
     * @param StopwatchEvent $event
     */
    private function stopwatch(SymfonyStyle $io, StopwatchEvent $event) {
        $io->writeln([
            sprintf('Time: <info>%.2F</info> s.', $event->getDuration() / 1000),
            sprintf('Memory: <info>%.2F</info> MiB.', $event->getMemory() / 1024 / 1024),
        ]);
    }
}
