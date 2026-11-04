<?php
namespace ECidade\V3\Modification\Data;

use \ECidade\V3\Extension\Storage;
use \ECidade\V3\Extension\Logger;
use \ECidade\V3\Modification\Manager as ModificationManager;
use \ECidade\V3\Extension\Registry;
use \ECidade\V3\Modification\Data\File;

/**
 * @package modification
 */
class FileSync extends Storage
{
    /**
     * @var \ECidade\V3\Modification\Data\File
     */
    private $fileData;

    /**
     * @param string $path
     */
    public function __construct(File $fileData)
    {
        $this->fileData = $fileData;
        $path = $fileData->getPrefix() . $fileData->getOriginalPath();
        parent::__construct(ECIDADE_MODIFICATION_DATA_PATH . "file/sync/" . $path);
        $this->setSerialize(false);
    }

    /**
     * @return boolean
     */
    public function updated()
    {
        $originalPath = ECIDADE_PATH . $this->fileData->getOriginalPath();

        // arquivo nao sincronizando
        if (!$this->exists()) {
            return false;
        }

        return filemtime($this->path) > filemtime($originalPath);
    }

    /**
     * Mantem arquivo de cache atualizado
     *
     * @param string $path
     * @return \ECidade\V3\Modification\Data\File | false
     */
    public static function update($path)
    {
        $fileData = new File($path);
        $fileSync = new static($fileData);

        // arquivo nao tem modificação
        // @FIXME - remover validacao "!$fileData->exists()"
        // validacao por compatibilidade
        // caches ja instalados nao tem arquivo de sincronizacao criado
        if (!$fileSync->exists() && !$fileData->exists()) {
            return false;
        }

        // arquivo sync atualizado
        // - retorna cache atualizado ou false, caso nao exista
        if ($fileSync->updated()) {
            // cache nao existe
            // acontece quando modificacao foi abortada
            // ou deu erro em todas as operacoes da modificacao
            // mantem arquivo de sync para tentar refazer cache ao atualizar arquivo
            if (!$fileData->exists()) {
                return false;
            }

            // cache atualizado
            return $fileData;
        }

        $manager = new ModificationManager();
        $config = Registry::get('app.config');

        // verifica se tem configurado arquivo para log
        if ($config->has('app.modifications.log.path')) {
            $logger = $manager->getLogger();
            $logger->setFile($config->get('app.modifications.log.path'));
            $logger->setVerbosity($config->get('app.log.verbosity', Logger::QUIET));
            $logger->debug("\n\nmodification() - update file: $path");
        }

        // atualiza arquivo
        $manager->updateFile($path);

        // arquivo nao gerou cache
        // ocorreu algum erro no parse
        if (!$fileData->exists()) {
            return false;
        }

        return $fileData;
    }
}
