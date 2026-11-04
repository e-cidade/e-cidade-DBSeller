<?php

namespace ECidade\V3\Modification;

use ArrayObject;
use ECidade\V3\Extension\AbstractManager;
use ECidade\V3\Extension\Container;
use ECidade\V3\Modification\Data\File as FileData;
use ECidade\V3\Modification\Data\FileSync;
use ECidade\V3\Modification\Data\FileTypeModification;
use ECidade\V3\Modification\Data\Modification as ModificationData;
use ECidade\V3\Modification\Parse\Modification as ModificationParse;

/**
 * @package Modification
 */
class Manager extends AbstractManager
{
    /**
     * @param Container $container
     */
    public function __construct(Container $container = null)
    {
        // cria o container, caso nao for informado, e registra logger
        parent::__construct($container);

        if (!$this->container->has('group')) {
            $this->container->register('group', function ($container) {
                return Data\Group::restore();
            });
        }

        // cache de \ECidade\V3\Modification\Data\Modification
        if (!$this->container->has('cacheDataModifications')) {
            $this->container->register('cacheDataModifications', function ($container) {

                $cacheDataModifications = new ArrayObject();
                return function ($id) use ($cacheDataModifications) {

                    if (!isset($cacheDataModifications[$id])) {
                        $cacheDataModifications[$id] = ModificationData::restore($id);
                    }

                    return $cacheDataModifications[$id];
                };
            });
        }

        // cache de \ECidade\V3\Modification\Logger
        if (!$this->container->has('cacheLoggerModifications')) {
            $this->container->register('cacheLoggerModifications', function ($container) {

                $cacheLoggerModifications = new ArrayObject();
                return function ($id) use ($cacheLoggerModifications) {

                    if (!isset($cacheLoggerModifications[$id])) {
                        $cacheLoggerModifications[$id] = new Logger($id);
                    }

                    return $cacheLoggerModifications[$id];
                };
            });
        }
    }

    /**
     * @param string $path
     * @param bool $force
     * @return \ECidade\V3\Modification\Data\Modification
     */
    public function unpack($path, $force = false)
    {
        // cria diretorios caso nao existam
        $this->setup();

        $parseModification = $this->parse($path);

        // cache das modificacoes
        $dataModification = ModificationData::restore($parseModification->getId());

        if ($force === false && $dataModification->exists()) {
            throw new \Exception("Modificação já descompactada: " . $parseModification->getId());
        }

        $dataModification->setId($parseModification->getId());
        $dataModification->setLabel($parseModification->getLabel());
        $dataModification->setGroup($parseModification->getGroup());
        $dataModification->setOperations($parseModification->getOperations());
        $dataModification->setFilesOperations($parseModification->getFilesOperations());

        // nao altera status e type de modificacoes ja instaladas
        // @todo - buscar um metodo melhor
        if ($force === false || !$dataModification->exists()) {
            $dataModification->setStatus(ModificationData::STATUS_DISABLED);
            $dataModification->setType($parseModification->getType());
        }

        $dataModification->save();

        // @todo - verificar utilidade dessa copia, guardar .data com modificacoes instaladas
        copy($path, ECIDADE_MODIFICATION_XML_PATH . basename($path));

        return $dataModification;
    }

    /**
     * @param string|array $modifications
     * @return boolean
     */
    public function install($modifications)
    {
        $data = $this->prepare($modifications, 'install', 'add');
        $modifications = $data->modifications;
        $filesReparse = $data->filesReparse;
        $dataFileTypeModification = $data->dataFileTypeModification;
        $fileTypeModification = $data->fileTypeModification;
        $group = $this->container->get('group');
        $logger = $this->container->get('logger');
        $groupUpdated = false;

        $logger->debug("Instalando modificações: " . implode(', ', $modifications));

        // cache-data das modificacoes
        $cacheDataModifications = $this->container->get('cacheDataModifications');

        // cache-data dos loggers de modficacoes
        $cacheLoggerModifications = $this->container->get('cacheLoggerModifications');

        foreach ($modifications as $id) {
            $cacheLoggerModifications($id)->info('Instalando modificação');
        }

        foreach ($modifications as $id) {
            $data = $cacheDataModifications($id);
            $data->setFilesErrors(array());

            // adiciona modificaton ao grupo
            if ($data->hasGroup()) {
                $groupUpdated = true;
                $group->add($data->getGroup(), $id);
            }

            $data->setStatus(ModificationData::STATUS_ENABLED, null);
        }

        $this->parseFiles($filesReparse);

        $filesReparse = null;

        // persist
        foreach ($modifications as $id) {
            $cacheDataModifications($id)->save();
        }

        if ($groupUpdated) {
            $group->save();
        }

        // atualiza e salva cache dos arquivos
        $dataFileTypeModification->setData($fileTypeModification);
        $dataFileTypeModification->save();

        return true;
    }

    /**
     * @param string|array $modifications
     * @return boolean
     */
    public function uninstall($modifications)
    {
        $data = $this->prepare($modifications, 'uninstall', 'remove');
        $modifications = $data->modifications;
        $filesReparse = $data->filesReparse;
        $dataFileTypeModification = $data->dataFileTypeModification;
        $fileTypeModification = $data->fileTypeModification;
        $group = $this->container->get('group');
        $logger = $this->container->get('logger');
        $groupUpdated = false;

        // cache-data das modificacoes
        $cacheDataModifications = $this->container->get('cacheDataModifications');

        // cache-data dos loggers de modficacoes
        $cacheLoggerModifications = $this->container->get('cacheLoggerModifications');

        $logger->debug("Desinstalando modificações: " . implode(', ', $modifications));

        foreach ($modifications as $id) {
            $cacheLoggerModifications($id)->info('Desinstalando modificação');
        }

        // remove todos os caches criados das modificacoes
        $this->removeDataModificationFiles($modifications);

        foreach ($modifications as $id) {
            $data = $cacheDataModifications($id);
            $data->setFilesErrors(array());

            // remove modificaton do grupo
            if ($data->hasGroup()) {
                $groupUpdated = true;
                $group->removeItem($data->getGroup(), $id);
            }

            $data->setStatus(ModificationData::STATUS_DISABLED, null);
        }

        // recria os caches caso arquivo possua mais modificacoes
        if (count($filesReparse) > 0) {
            $this->parseFiles($filesReparse);
        }

        $filesReparse = null;

        // persist
        foreach ($modifications as $id) {
            $cacheDataModifications($id)->save();
        }

        if ($groupUpdated) {
            $group->save();
        }

        // atualiza e salva cache dos arquivos
        $dataFileTypeModification->setData($fileTypeModification);
        $dataFileTypeModification->save();

        return true;
    }

    public function abort($modifications)
    {
        $data = $this->prepare($modifications, 'uninstall', 'remove');
        $modifications = $data->modifications;
        $filesReparse = $data->filesReparse;
        $logger = $this->container->get('logger');

        // cache-data dos loggers de modficacoes
        $cacheLoggerModifications = $this->container->get('cacheLoggerModifications');

        $logger->debug("Abortando modificações: " . implode(', ', $modifications));

        foreach ($modifications as $id) {
            $cacheLoggerModifications($id)->info('Abortando modificação');
        }

        // remove todos os caches criados das modificacoes
        $this->removeDataModificationFiles($modifications);

        // recria os caches caso arquivo possua mais modificacoes
        if (count($filesReparse) > 0) {
            $this->parseFiles($filesReparse);
        }

        $filesReparse = null;

        return true;
    }

    public function apply($modifications)
    {
        $data = $this->prepare($modifications, 'apply', 'add');
        $modifications = $data->modifications;
        $filesReparse = $data->filesReparse;
        $group = $this->container->get('group');
        $logger = $this->container->get('logger');

        $logger->debug("Aplicando modificações: " . implode(', ', $modifications));

        // cache-data das modificacoes
        $cacheDataModifications = $this->container->get('cacheDataModifications');

        // cache-data dos loggers de modficacoes
        $cacheLoggerModifications = $this->container->get('cacheLoggerModifications');

        foreach ($modifications as $id) {
            $cacheLoggerModifications($id)->info('Aplicando modificação');
        }

        foreach ($modifications as $id) {
            $data = $cacheDataModifications($id);
            $data->setFilesErrors(array());
            $data->setStatus(ModificationData::STATUS_ENABLED, null);
        }

        $this->parseFiles($filesReparse);

        return true;
    }

    /**
     * @param string|array $modifications
     * @param string $type install|uninstall|apply
     * @param string $mode add|remove
     *                     modo de alteracao no arquivo de modificacoes
     *                     por arquivo(FileTypeModification)
     * @return array
     */
    private function prepare($modifications, $type, $mode)
    {
        if (!is_array($modifications)) {
            $modifications = array($modifications);
        }

        if (empty($modifications)) {
            throw new \Exception("Nenhum ID informado.");
        }

        if (!in_array($type, array('install', 'uninstall', 'apply'))) {
            throw new \InvalidArgumentException(
                sprintf("Tipo inválido: %s", $type)
            );
        }

        if (!in_array($mode, array('add', 'remove'))) {
            throw new \InvalidArgumentException(
                sprintf("Modo inválido: %s", $mode)
            );
        }

        // cache-data
        $cacheDataModifications = $this->container->get('cacheDataModifications');

        // dados para reparse
        $filesReparse = new ArrayObject();

        // [file][type][modification]
        $dataFileTypeModification = new FileTypeModification();
        $fileTypeModification = array();

        if ($type != 'apply' && $dataFileTypeModification->exists()) {
            $dataFileTypeModification->load();
            $fileTypeModification = $dataFileTypeModification->getData();
        }

        foreach ($modifications as $id) {
            $dataModification = $cacheDataModifications($id);

            if (!$dataModification->exists()) {
                throw new \Exception("Modificação sem cache: $id");
            }

            if ($type == 'uninstall' && !$dataModification->isEnabled()) {
                throw new \Exception("Modificação não instalada: $id");
            } elseif ($type == 'install' && $dataModification->isEnabled()) {
                throw new \Exception("Modificação já instalada: $id");
            }

            if ($mode == 'add') {
                $this->addFileTypeModification($dataModification, $fileTypeModification, $filesReparse);
            } elseif ($mode == 'remove') {
                $this->removeFileTypeModification($dataModification, $fileTypeModification, $filesReparse);
            }
        }

        return (object)array(
            // array com ids das modificacoes
            'modifications' => $modifications,

            // arquivos para reparse no formato do arquivo FileTypeModification
            'filesReparse' => $filesReparse,

            // [file][type][modification]
            'fileTypeModification' => $fileTypeModification,
            'dataFileTypeModification' => $dataFileTypeModification,
        );
    }

    /**
     * @param string path
     * @return boolean
     */
    public function updateFile($path)
    {
        if (!file_exists($path)) {
            throw new \Exception('Arquivo não existe: ' . $path);
        }

        // clear absolute path
        $path = str_replace(ECIDADE_PATH, null, realpath($path));

        $dataFileTypeModification = new FileTypeModification();
        $fileTypeModification = array();

        if ($dataFileTypeModification->exists()) {
            $dataFileTypeModification->load();
            $fileTypeModification = $dataFileTypeModification->getData();
        }

        if (!isset($fileTypeModification[$path])) {
            throw new \Exception('Arquivo sem modificacao: ' . $path);
        }

        $filesReparse = new ArrayObject(array($path => $fileTypeModification[$path]));
        return $this->parseFiles($filesReparse);
    }

    /**
     * @param string path
     * @return boolean
     */
    public function updateFileTest($path)
    {
        $logger = $this->container->get('logger');

        if (!file_exists($path)) {
            $logger->error('Arquivo nao existe: ' . $path);
            return false;
        }

        $dataFileTypeModification = new FileTypeModification();
        $fileTypeModification = array();

        // clear absolute path
        $path = str_replace(ECIDADE_PATH, null, realpath($path));

        if ($dataFileTypeModification->exists()) {
            $dataFileTypeModification->load();
            $fileTypeModification = $dataFileTypeModification->getData();
        }

        if (!isset($fileTypeModification[$path])) {
            $logger->debug('Arquivo sem modificacao: ' . $path);
            return true;
        }

        $filesReparse = new ArrayObject(array($path => $fileTypeModification[$path]));
        $managerParseFiles = new ManagerParseFiles($this->container);

        // agrupa operacoes para executar parse na ordem correta
        $managerParseFiles->generateOperationsQueue($filesReparse);

        // desabilita log das modificacoes
        $managerParseFiles->setModificationLogVerbosity(Logger::QUIET);

        // executa o parse
        $managerParseFiles->parse();

        // remove arquivos temporarios
        $managerParseFiles->removePersistDirectory();

        return !$managerParseFiles->hasErrorsOnParse();
    }

    /**
     * @param ArrayObject $modificationsFiles
     * @return boolean
     */
    private function parseFiles(ArrayObject $modificationsFiles)
    {
        $logger = $this->container->get('logger');
        $logger->debug('Total de arquivos para processar: ' . count($modificationsFiles));

        $managerParseFiles = new ManagerParseFiles($this->container);

        // gera fila de operacoes para processar de acordo com modificacoes
        $managerParseFiles->generateOperationsQueue($modificationsFiles);

        // realiza parse das modificacoes, gerando cache temporario
        $managerParseFiles->parse();

        // aborta modificacoes marcadas para remover
        $managerParseFiles->abortModifications($this);

        // salva alteracoes nos ModificationData
        // salva os caches temporarios gerados pelo parse no diretorio final de caches
        $managerParseFiles->persist();

        // remove caches nao utilizados
        $managerParseFiles->removeUselessDataFile();

        // remove diretorio temporario
        $managerParseFiles->removePersistDirectory();

        return true;
    }

    /**
     * Remover arquivos de cache de modificacoes
     *
     * @param array $modifications
     * @return boolean
     */
    private function removeDataModificationFiles(array $modifications)
    {
        $logger = $this->container->get('logger');
        $cacheDataModifications = $this->container->get('cacheDataModifications');

        foreach ($modifications as $id) {
            $dataModification = $cacheDataModifications($id);
            $dataRemoved = 0;

            $filesTotal = count($dataModification->getFiles());
            $logger->debug("Removendo caches da modificação: {$id} ($filesTotal)");

            foreach ($dataModification->getFiles() as $path) {
                $dataFile = new FileData($path);
                $fileSync = new FileSync($dataFile);

                // dessincroniza arquivo
                // usado para verificar a necessidade de atualizar cache
                $fileSync->remove();

                if (!$dataFile->exists()) {
                    continue;
                }

                $dataRemoved++;
                $dataFile->remove();
            }

            $logger->debug('Arquivos removidos: ' . $dataRemoved);
        }

        return true;
    }

    private function addFileTypeModification($dataModification, &$fileTypeModification, &$filesReparse)
    {
        $id = $dataModification->getId();
        $type = 'global';

        foreach ($dataModification->getFiles() as $path) {
            if (!isset($fileTypeModification[$path][$type])) {
                $fileTypeModification[$path][$type] = array();
            }

            if (!in_array($id, $fileTypeModification[$path][$type])) {
                $fileTypeModification[$path][$type][] = $id;
            }

            $filesReparse[$path] = $fileTypeModification[$path];
        }

        return true;
    }

    private function removeFileTypeModification($dataModification, &$fileTypeModification, &$filesReparse)
    {
        $id = $dataModification->getId();
        $type = 'global';

        foreach ($dataModification->getFiles() as $path) {
            if (!isset($fileTypeModification[$path])) {
                continue;
            }

            // remove id da modificacao do cache global de arquivos
            if (!empty($fileTypeModification[$path][$type])) {
                $key = array_search($id, $fileTypeModification[$path][$type]);

                if ($key !== false) {
                    unset($fileTypeModification[$path][$type][$key]);
                }

                // empty
                if (empty($fileTypeModification[$path][$type])) {
                    unset($fileTypeModification[$path][$type]);
                }
            }

            // empty
            if (empty($fileTypeModification[$path])) {
                unset($fileTypeModification[$path]);
                continue;
            }

            // existe arquivo para reparse
            if (!empty($fileTypeModification[$path])) {
                $filesReparse[$path] = $fileTypeModification[$path];
            }
        }

        $removeKeys = array();
        $iterator = $filesReparse->getIterator();
        for ($iterator->rewind(); $iterator->valid(); $iterator->next()) {
            if (!isset($fileTypeModification[$iterator->key()])) {
                $removeKeys[] = $iterator->key();
            }
        }
        foreach ($removeKeys as $key) {
            $filesReparse->offsetUnset($key);
        }
        $removeKeys = null;

        return true;
    }

    /**
     * @param string $path
     */
    public function parse($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("Arquivo não existe: $path");
        }

        // parse no xml
        $parseModification = new ModificationParse($path);
        $parseModification->load();
        $parseModification->parse();

        return $parseModification;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function setup()
    {
        $mode = 0775;
        $directories = array(
            ECIDADE_MODIFICATION_PATH,
            ECIDADE_MODIFICATION_LOG_PATH,
            ECIDADE_MODIFICATION_DATA_PATH,
            ECIDADE_MODIFICATION_XML_PATH,
        );

        foreach ($directories as $path) {
            if (!is_dir($path) && !mkdir($path, $mode, true)) {
                throw new \Exception(sprintf("Nao foi possivel criar diretorio: %s", $path));
            }
        }

        return $this;
    }
}
