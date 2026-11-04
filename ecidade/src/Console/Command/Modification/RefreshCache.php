<?php

namespace ECidade\Console\Command\Modification;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use \ECidade\V3\Extension\Glob;
use \ECidade\V3\Extension\ConsoleColor as Color;

class RefreshCache extends Command
{
    protected function configure()
    {
        $this
            ->setName('modification:refresh-cache')
            ->setDescription('Refresh modifications cache')
            ->setHelp('Touch current files caches to force update on next require');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', '-1');

        $filesLegacy = Glob::find(ECIDADE_PATH . "modification/cache/" . "*.*", null, true);

        $current_mtime = time();
        $old_mtime = $current_mtime - 3600;

        $this->removeDirectory(ECIDADE_MODIFICATION_DATA_PATH . "file/sync/");

        // legacy modifications
        foreach ($filesLegacy as $file) {
            $rel = str_replace(ECIDADE_PATH . "modification/cache/", "", $file);
            $rel = str_replace("-", "/", $rel);

            if (file_exists($rel)) {
                touch($rel, $current_mtime);

                echo Color::set("TOUCH: ", "light_blue") . Color::set($rel . "\n", "white");
                $finalPath = ECIDADE_MODIFICATION_CACHE_PATH . "global/" . $rel;

                if (file_exists($finalPath)) {
                    touch($finalPath, $old_mtime);
                    $this->touchSyncPath($finalPath, $old_mtime);
                }
            }
        }

        // EXTENSION

        $filesExtensionGlobal = Glob::find("*.*", ECIDADE_MODIFICATION_CACHE_PATH . "global/", true);


        // global
        foreach ($filesExtensionGlobal as $file) {
            try {
                touch(str_replace(ECIDADE_MODIFICATION_CACHE_PATH . "global/", "", $file), $current_mtime);
                touch($file, $old_mtime);
                $this->touchSyncPath($file, $old_mtime);

                echo Color::set("TOUCH: ", "light_blue");
                echo Color::set(str_replace(ECIDADE_MODIFICATION_CACHE_PATH . "global/", "", $file), "white") . "\n";
            } catch (\Exception $error) {
                echo Color::set(" - SKIP: ", "yellow") . Color::set($file, "white") . "\n";
            }
        }
    }

    private function notifyError($message)
    {
        echo Color::set(" - SKIP: ", "yellow") . Color::set($message, "white") . "\n";
    }

    private function touchSyncPath($path, $mtime)
    {
        $syncPath = ECIDADE_MODIFICATION_DATA_PATH . "file/sync/";
        $path = str_replace(ECIDADE_MODIFICATION_CACHE_PATH, $syncPath, $path);
        $dir = dirname($path);

        if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
            return $this->notifyError("Erro ao criar diretorio: $dir");
        }

        if (!touch($path, $mtime)) {
            $this->notifyError("Erro ao alterar data de modificação do arquivo: $path");
        }

        return true;
    }

    private function removeDirectory($path)
    {
        try {
            if (!is_dir($path)) {
                return true;
            }

            $directoryIterator = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new \RecursiveIteratorIterator($directoryIterator, \RecursiveIteratorIterator::CHILD_FIRST);

            foreach ($files as $fileinfo) {
                $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                $todo($fileinfo->getRealPath());
            }

            return rmdir($path);
        } catch (\Exception $e) {
            return false;
        }
    }
}
