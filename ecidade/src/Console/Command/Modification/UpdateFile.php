<?php

namespace ECidade\Console\Command\Modification;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use \ECidade\V3\Extension\ConsoleColor as Color;
use \ECidade\V3\Modification\Manager;
use \ECidade\V3\Extension\Logger;

class UpdateFile extends Command
{
    protected function configure()
    {
        $this
            ->setName('modification:update-file')
            ->setDescription('Update modifications file')
            ->setHelp('Update modifications file');

        $this->addArgument('file', InputArgument::REQUIRED, 'file path');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', '-1');

        $file = realpath($input->getArgument('file'));

        $manager = new Manager();
        $logger = $manager->getLogger();
        $logger->setVerbosity(Logger::DEBUG);
        $logger->addHandler(function ($output, $level) {

            switch ($level) {
                case Logger::DEBUG:
                    $output = Color::set($output, 'light_gray');
                    break;

                case Logger::WARNING:
                    $output = Color::set($output, 'brown');
                    break;

                case Logger::ERROR:
                    $output = Color::set($output, 'red');
                    break;
            }

            return $output;
        });
        $manager->updateFile($file);

        echo "Cache atualizado.";

        $processUser = posix_getpwuid(posix_geteuid());
        $group = posix_getgrgid($processUser['uid']);
        if ($group['name'] != 'www-data') {
            echo Color::set(
                "\n\nUsuário atual não está no grupo www-data\n" .
                " - Apos rodar comando atualize as permissões para o grupo www-data\n" .
                " - Ou execute o commando com 'sudo -H -u www-data COMMAND\n",
                'brown'
            );
        }

        echo "\n memory: " . round((memory_get_peak_usage(true) / 1024) / 1024, 2) . "mb\n\n";
    }
}
