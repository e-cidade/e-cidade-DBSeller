<?php
namespace ECidade\Console\Command\Modification;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use \ECidade\V3\Extension\Glob;
use \ECidade\V3\Modification\Manager;
use \ECidade\V3\Extension\ConsoleColor as Color;

class RefreshMetaData extends Command
{
    protected function configure()
    {
        $this
            ->setName('modification:refresh-metadata')
            ->setDescription('Refresh modifications metadata')
            ->setHelp('Refresh modifications metadata and install');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', '-1');

        $filesLegacy = Glob::find(ECIDADE_PATH . "modification/xml/" . "*.xml");

        $manager = new Manager();

        foreach ($filesLegacy as $file) {
            try {
                $data = $manager->unpack($file, true);
                $manager->install($data->getId());
                echo Color::set(" - Modificação descompactada: ", "cyan").Color::set($data->getId(), "brown") ."\n";
            } catch (\Exception $error) {
                echo Color::set(" - SKIP: ", "light_blue") . Color::set($file, "white") .
                                " --> " . Color::set($error->getMessage(), "red") . "\n";
            }
        }

        $filesExtension = Glob::find(ECIDADE_MODIFICATION_XML_PATH . "*.xml");

        foreach ($filesExtension as $file) {
            try {
                $data = $manager->unpack($file, true);
                echo Color::set(" - Modificação descompactada: ", "cyan") . Color::set($data->getId(), "brown") ."\n";
            } catch (\Exception $error) {
                echo Color::set(" - SKIP: ", "light_blue") . Color::set($file, "white") .
                                " --> " . Color::set($error->getMessage(), "red") . "\n";
            }
        }

        $processUser = posix_getpwuid(posix_geteuid());
        $group = posix_getgrgid($processUser['uid']);
        if ($group['name'] != 'www-data') {
            echo Color::set(
                "\n\nUsuário atual não está no grupo www-data\n".
                " - Apos rodar comando atualize as permissões para o grupo www-data\n".
                " - Ou execute o commando com 'sudo -H -u www-data COMMAND\n",
                'brown'
            );
        }
    }
}
