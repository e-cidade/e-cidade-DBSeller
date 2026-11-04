<?php

namespace ECidade\V3\Modification;

use ArrayObject;
use Exception;
use \ECidade\V3\Extension\Container;
use \ECidade\V3\Modification\Manager;
use \ECidade\V3\Modification\Data\File as FileData;
use \ECidade\V3\Modification\Data\FileSync;
use \ECidade\V3\Modification\Data\Modification as ModificationData;
use \ECidade\V3\Modification\Parse\File as FileParse;
use \ECidade\V3\Modification\Parse\Operation;
use \ECidade\V3\Modification\Exception\Abort as AbortException;

/**
 * @package Modification
 */
class ManagerParseFiles
{
    /**
     * @var \ECidade\Extension\Container
     */
    private $container;

    /**
     * fila de operacoes por arquivo para processar
     *
     * @see ManagerParseFiles::generateOperationsQueue()
     * @example
     * ArrayObject(
     *   global => array(
     *     'modification1' => array(
     *         0 => array(
     *             'arquivo1' => $metadadoArquivo1.1
     *         )
     *     ),
     *     'modification2' => array(
     *         0 => array(
     *             'arquivo2' => $metadadoArquivo2
     *         )
     *     )
     *   )
     * )
     * @var ArrayObject
     */
    private $modificationOperationData;

    /**
     * diretorio temporario para gerar caches
     * @param string
     */
    private $persistPath;

    /**
     * arquivos de caches marcados para remover
     * @var ArrayObject
     */
    private $dataToRemove;

    /**
     * arquivos de caches marcados para salvar
     * @var ArrayObject
     */
    private $dataToPersist;

    /**
     * modifciacoes marcadas para abortar
     * @var ArrayObject
     */
    private $abortModifications;

    /**
     * modifciacoes marcadas para salvar
     * @var ArrayObject
     */
    private $modificationToPersist;

    /**
     * Flag para saber se houveram erros durante o parse de um arquivo
     * @var boolean
     */
    private $hasErrorsOnParse;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param ArrayObject $modificationsFiles
     * @return boolean
     */
    public function generateOperationsQueue(ArrayObject $modificationsFiles)
    {
        $logger = $this->container->get('logger');

        $this->modificationOperationData = new ArrayObject();
        $this->dataToRemove = new ArrayObject();
        $this->dataToPersist = new ArrayObject();
        $this->abortModifications = new ArrayObject();
        $this->modificationToPersist = new ArrayObject();

        // diretorio temporario onde sera gerado arquivos de cache
        $this->persistPath = ECIDADE_MODIFICATION_CACHE_PATH . uniqid('parsing-', true) . '/';

        $logger->debug('Gerando fila de operacoes');
        $logger->debug(' - Definindo diretorio temporario: ' . str_replace(ECIDADE_PATH, null, $this->persistPath));

        // itera o conteudo do arquivo file-type-modification.data
        // (que contem informacoes de arquivos e suas modificacao globais/usuarios)
        $iterator = $modificationsFiles->getIterator();
        for ($iterator->rewind(); $iterator->valid(); $iterator->next()) {
            $path = $iterator->key();
            $types = $iterator->current();

            // se o arquivo original nao existir, erro
            if (!file_exists(ECIDADE_PATH . $path)) {
                $logger->error("Arquivo não existe: '$path'");
                continue;
            }

            // se o arquivo original nao tem permissao de leitura, erro
            if (!is_readable(ECIDADE_PATH . $path)) {
                $logger->error("Arquivo sem permissão de leitura: '$path'");
                continue;
            }

            // se o arquivo original estiver vazio, erro
            if (filesize(ECIDADE_PATH . $path) == 0) {
                $logger->error("Arquivo vazio: '$path'");
                continue;
            }

            // itera os modification globais
            foreach ($types as $type => $modifications) {
                if (!isset($this->modificationOperationData[$type])) {
                    $this->modificationOperationData[$type] = new ArrayObject();
                }

                foreach ($modifications as $modificationId) {
                    if (!isset($this->modificationOperationData[$type][$modificationId])) {
                        $this->modificationOperationData[$type][$modificationId] = new ArrayObject();
                    }

                    $this->modificationOperationData[$type][$modificationId][] = $path;
                }
            }
        }

        return true;
    }

    /**
     * @return boolean
     */
    public function parse()
    {
        // itera todos os modification encontrados que precisam ser executados
        $iterator = $this->modificationOperationData->getIterator();
        for ($iterator->rewind(); $iterator->valid(); $iterator->next()) {
            $type = $iterator->key();
            $modifications = $iterator->current();

            // itera os operations do modification seguindo a ordem correto que veio do xml
            foreach ($modifications as $modificationId => $files) {
                $this->parseModification($modificationId, $files);
            }
        }

        return true;
    }

    /**
     * @return boolean
     */
    public function persist()
    {
        $logger = $this->container->get('logger');

        // salvar alteracoes nos metadados das modificacoes
        $this->persistModification();

        if (count($this->dataToPersist) == 0) {
            return false;
        }

        $logger->debug('Arquivos marcados para salvar: ' . count($this->dataToPersist));
        $saved = 0;

        $iterator = $this->dataToPersist->getIterator();
        for ($iterator->rewind(); $iterator->valid(); $iterator->next()) {
            $path = $iterator->key();
            $relativePath = str_replace($this->persistPath, null, $path);
            $finalPath = ECIDADE_MODIFICATION_CACHE_PATH . $relativePath;
            $finalPathDir = dirname($finalPath);

            // extrai caminho original do arquivo
            // usado para sincronizar cache com arquivo original
            $parts = explode("/", $relativePath);
            $originalPath = implode('/', $parts);

            $dataFile = new FileData($originalPath);
            $fileSync = new FileSync($dataFile);

            if (!file_exists($path)) {
                $logger->error("Arquivo não encontrado: $path");
                continue;
            }

            if (!is_dir($finalPathDir) && !mkdir($finalPathDir, 0775, true)) {
                $logger->error("Erro ao criar diretorio: $finalPathDir");
                continue;
            }

            if (!rename($path, $finalPath)) {
                $logger->error("Erro ao mover arquivo: '$path' para '$finalPath'");
                continue;
            }

            $fileSync->touch();

            $saved++;
        }

        $logger->debug(' - Arquivos salvos: ' . $saved);

        $this->dataToPersist = new ArrayObject();
        return true;
    }

    /**
     * @return boolean
     */
    private function persistModification()
    {
        if (count($this->modificationToPersist) == 0) {
            return false;
        }

        $iterator = $this->modificationToPersist->getIterator();
        for ($iterator->rewind(); $iterator->valid(); $iterator->next()) {
            $iterator->current()->save();
        }

        $this->modificationToPersist = new ArrayObject();
        return true;
    }

    /**
     * @param string $id
     * @param ArrayObject $files
     * @return boolean
     */
    private function parseModification($id, ArrayObject $files)
    {
        $logger = $this->container->get('logger');
        $cacheLoggerModifications = $this->container->get('cacheLoggerModifications');
        $cacheDataModifications = $this->container->get('cacheDataModifications');

        $logModification = $cacheLoggerModifications($id);
        $dataModification = $cacheDataModifications($id);

        // modificacao esta em um grupo com modificacao abortada
        if ($this->inAbortGroup($id)) {
            $message = "Modificação em um grupo abortado, abortando processamento";
            $logger->error($message);
            $logModification->error($message);
            return false;
        }

        $iterator = $files->getIterator();

        // flag para saber se o modification possui erros antes do parse
        $hadErrors = false;

        // tipo de erros
        // 0 nenhum erro
        // 1 skip
        // 2 abort
        $error = 0;

        // erros ao executar parse
        $this->hasErrorsOnParse = false;

        // realiza parse de cada arquivo da modificacao
        for ($iterator->rewind(); $iterator->valid(); $iterator->next()) {
            $path = $iterator->current();

            if (!$hadErrors && count($dataModification->getFileErrors($path)) > 0) {
                $hadErrors = true;
            }

            // limpa os erros para o arquivo atual
            $dataModification->setFileError($path, array());

            $error = $this->parseFile($path, $id);

            if ($error !== 0) {
                $this->hasErrorsOnParse = true;
            }

            // se houver algum erro abort no meio do parse dos operations
            // entao aborta processamento atual
            if ($error === Operation::ERROR_ABORT) {
                break;
            }
        }

        // se houveram erros no parse, salvamos os metadados para persistir o array de erros
        // ou
        // se anteriormente possuiam erros e agora não tem mais
        // isso ajudará depois a identificar possiveis erros nos modifications pelo sistema.
        // essa complexidade serve para evitar overhead na hora de salvar os metadados
        if ($this->hasErrorsOnParse || $hadErrors) {
            $this->modificationToPersist[$id] = $dataModification;
        }

        // nao ocorreu nenhum erro
        if ($error !== Operation::ERROR_ABORT) {
            return true;
        }

        // erro ao processar modificacao, aborta
        $this->abortModifications[$id] = $dataModification;

        // marca todos os arquivos da modificacao atual para ser removidos
        for ($iterator->rewind(); $iterator->valid(); $iterator->next()) {
            $path = sprintf("%s%s/%s", $this->persistPath, 'global', $iterator->current());

            // remove arquivo da lista para salvar
            if (isset($this->dataToPersist[$path])) {
                unset($this->dataToPersist[$path]);
            }

            // marca arquivo para se removido
            $this->dataToRemove[$path] = true;
        }

        return false;
    }

    /**
     * @param string $path
     * @param string $modificationId
     * @return integer | error code
     */
    private function parseFile($path, $modificationId)
    {
        $logger = $this->container->get('logger');
        $cacheLoggerModifications = $this->container->get('cacheLoggerModifications');
        $cacheDataModifications = $this->container->get('cacheDataModifications');

        $logModification = $cacheLoggerModifications($modificationId);
        $dataModification = $cacheDataModifications($modificationId);

        $data = new FileData($path);
        $data->type = 'global';
        $data->setPersistPath($this->persistPath);
        $this->loadDataContent($data);

        $originalPath = $data->getOriginalPath();
        $operations = $dataModification->getOperationsFile($path);

        if (empty($operations)) {
            $logger->error('Nenhum operacao para o arquivo: ' . $path);
            return true;
        }

        $parseFile = new FileParse();
        $parseFile->setContent($data->getContent());
        $parseFile->setOperations($operations);

        $messageTemplate = "file: '%s' operation: '%s'";
        $code = 0;

        try {
            // total de operacoes
            $countOperations = count($operations);

            // tipo de operacao
            $type = "global";

            $logger->debug("Realizando parse: $path | operacoes: " . $countOperations . " | $type");

            // aplica as operacoes no conteudo do arquivo
            $parseFile->parse();

            // total de operacoes que foram executadas com sucesso
            $countOperationsParse = $countOperations - count($parseFile->getFailOperations());

            $logOperationsParsed = sprintf(
                "Realizado parse: %s | operacoes executadas: %s/%s | %s",
                $path,
                $countOperationsParse,
                $countOperations,
                $type
            );

            $logger->debug($logOperationsParsed);
            $logModification->info($logOperationsParsed);

            // para cada operacao com falha, nos salvamos no log
            foreach ($parseFile->getFailOperations() as $operation) {
                $code = Operation::ERROR_SKIP;
                $message = sprintf($messageTemplate, $originalPath, $operation->label());
                // log do modification
                $this->logFailOperations($message, $originalPath, $modificationId, $operation->error());
                // log da aplicacao
                $logger->warning($modificationId . ' - ' . $message);
            }

            // troca o conteudo do arquivo pelo conteudo parseado
            $data->setContent($parseFile->getContent());

            // persiste conteudo
            $this->persistData($data);
        } catch (AbortException $error) {
            $code = Operation::ERROR_ABORT;
            $message = "[ABORT] " . sprintf($messageTemplate, $originalPath, $error->getMessage());
            // log do modification
            $this->logFailOperations($message, $originalPath, $modificationId, Operation::ERROR_ABORT);
            // log da aplicacao
            $logger->error($modificationId . ' - ' . $message);
        }

        return $code;
    }

    /**
     * @param Data\File $data
     * @return boolean
     */
    private function loadDataContent(FileData $data)
    {
        if ($data->exists()) {
            // carrega o conteudo cacheado
            $data->load();
        } else {
            // carrega o conteudo original
            $data->loadContent();
        }

        return true;
    }

    /**
     * @return boolean
     */
    private function persistData(FileData $data)
    {
        $logger = $this->container->get('logger');

        // se o arquivo nao sofreu modificacao, entao segue para o proximo
        if ($data->getContent() == file_get_contents(ECIDADE_PATH . $data->getOriginalPath())) {
            $this->dataToRemove[$data->getPath()] = true;
            return false;
        }

        // arquivo foi modificado e nao podera ser removido no final
        if (isset($this->dataToRemove[$data->getPath()])) {
            unset($this->dataToRemove[$data->getPath()]);
        }

        // registra arquivo para ser salvo no diretorio correto posteriormente
        $this->dataToPersist[$data->getPath()] = true;

        // salvo o arquivo modificado no diretorio temporario
        $data->save();

        $logger->debug(' - cache temporario gerado');

        return true;
    }

    /**
     * @param string $message Conteudo do log a ser gravado
     * @param string $path Arquivo do modification no qual deu o erro
     * @param string $modificationId Id da modification ao qual ocorreu o erro
     * @param integer $errorType Tipo do erro: Operation::ERROR_SKIP ou Operation::ERROR_ABORT
     * @return boolean
     */
    private function logFailOperations($message, $path, $modificationId, $errorType)
    {
        $cacheLoggerModifications = $this->container->get('cacheLoggerModifications');
        $cacheDataModifications = $this->container->get('cacheDataModifications');

        // log do modification
        $logModification = $cacheLoggerModifications($modificationId);
        // metadado do modification
        $dataModification = $cacheDataModifications($modificationId);

        // adiciona mais um registro de erro para o arquivo
        $dataModification->addFileError($path, array(
            'message' => $message,
            'type' => $errorType
        ));

        // loga o error de acordo com o tipo
        switch ($errorType) {
            default:
            case Operation::ERROR_SKIP:
                $logModification->warning($message);
                break;
            case Operation::ERROR_ABORT:
                $logModification->error($message);
                break;
        }

        return true;
    }

    /**
     * Verifica se modificacao esta em um grupo com modificacao abortada
     * @param string $id - id da modificacao
     * @return boolean
     */
    private function inAbortGroup($id)
    {
        if (count($this->abortModifications) == 0) {
            return false;
        }

        $group = $this->container->get('group');

        $iterator = $this->abortModifications->getIterator();
        for ($iterator->rewind(); $iterator->valid(); $iterator->next()) {
            $abortModificationID = $iterator->key();
            $dataModification = $iterator->current();
            $siblings = array();

            if ($dataModification->hasGroup()) {
                $siblings = $group->get($dataModification->getGroup());
            } else {
                $siblings = $group->getSiblings($abortModificationID);
            }

            if (in_array($id, $siblings)) {
                return true;
            }
        }

        return false;
    }

    /**
     * sincroniza arquivos de uma modificacao
     * usado para verificar a necessidade de atualizar cache
     * Ao abortar uma modificacao ele dessincroniza arquivo, este metodo ressincroniza
     *
     * @param array $modifications
     * @return boolean
     */
    private function resyncModificationFiles(array $modifications)
    {
        $logger = $this->container->get('logger');
        $cacheDataModifications = $this->container->get('cacheDataModifications');

        foreach ($modifications as $id) {
            $dataModification = $cacheDataModifications($id);

            $filesTotal = count($dataModification->getFiles());
            $logger->debug("Sincronizando arquivos da modificação: {$id} ({$filesTotal})");

            foreach ($dataModification->getFiles() as $path) {
                $dataFile = new FileData($path);
                $fileSync = new FileSync($dataFile);
                $fileSync->touch();
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function getAbortModifications()
    {
        return $this->abortModifications;
    }

    /**
     * @return boolean
     */
    public function hasErrorsOnParse()
    {
        return $this->hasErrorsOnParse;
    }

    /**
     * Remover diretorio temporario usado para gerar os caches
     * @return boolean
     */
    public function removePersistDirectory()
    {
        if (!is_dir($this->persistPath)) {
            return false;
        }

        $logger = $this->container->get('logger');
        $logger->debug(' - removendo diretorio temporario: ' . str_replace(ECIDADE_PATH, null, $this->persistPath));

        $directoryIterator = new \RecursiveDirectoryIterator(
            $this->persistPath,
            \RecursiveDirectoryIterator::SKIP_DOTS
        );
        $files = new \RecursiveIteratorIterator($directoryIterator, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }

        return rmdir($this->persistPath);
    }

    /**
     * remove caches nao utilizados
     * @return boolean
     */
    public function removeUselessDataFile()
    {
        if (count($this->dataToRemove) == 0) {
            return false;
        }

        $logger = $this->container->get('logger');
        $logger->debug("Arquivos marcados para remover: " . count($this->dataToRemove));

        $removed = 0;

        // itera os arquivos que devem ser removidos
        $iterator = $this->dataToRemove->getIterator();
        for ($iterator->rewind(); $iterator->valid(); $iterator->next()) {
            $path = $iterator->key();
            $finalPath = str_replace($this->persistPath, ECIDADE_MODIFICATION_CACHE_PATH, $path);

            if (file_exists($path) && !unlink($path)) {
                $logger->error('Erro ao remover arquivo: ' . $path);
            }

            if (file_exists($finalPath)) {
                if (!unlink($finalPath)) {
                    $logger->error('Erro ao remover arquivo: ' . $finalPath);
                } else {
                    $removed++;
                }
            }

            $finalPath = null;
        }

        $this->dataToRemove = new ArrayObject();
        $logger->debug(" - arquivos removidos: " . $removed);
        return $removed > 0;
    }

    /**
     * @param Manager $manager
     * @return boolean
     */
    public function abortModifications(Manager $manager = null)
    {
        if (count($this->abortModifications) == 0) {
            return false;
        }

        if ($manager === null) {
            $manager = new Manager($this->container);
        }

        $abortModificationsGlobal = array();

        $abortModifications = $this->abortModifications;
        $logger = $this->container->get('logger');
        $group = $this->container->get('group');

        // get modifications group
        foreach ($abortModifications as $modificationId => $dataModification) {
            $modificationsGroup = $group->getSiblings($modificationId);

            foreach ($modificationsGroup as $_modificationId) {
                if (!isset($abortModifications[$_modificationId])) {
                    $abortModifications[$_modificationId] = ModificationData::restore($_modificationId);
                }
            }
        }

        foreach ($abortModifications as $modificationId => $dataModification) {
            if ($dataModification->isEnabled()) {
                $abortModificationsGlobal[] = $modificationId;
            }
        }

        // remove modificacoes do tipo GLOBAL
        if (!empty($abortModificationsGlobal)) {
            $logger->error("Abortando modificações globais: " . implode(', ', $abortModificationsGlobal));

            try {
                $manager->abort($abortModificationsGlobal);
                $this->resyncModificationFiles($abortModificationsGlobal);
            } catch (Exception $error) {
                $logger->error("erro ao abortar modificações glboais: " . $error->getMessage());
            }
        }

        $this->abortModifications = new ArrayObject();
        return true;
    }

    /**
     * desabilita
     * @param integer $verbosity
     * @return boolean
     */
    public function setModificationLogVerbosity($verbosity)
    {
        $cacheLoggerModifications = $this->container->get('cacheLoggerModifications');
        $iterator = $this->modificationOperationData->getIterator();
        for ($iterator->rewind(); $iterator->valid(); $iterator->next()) {
            $type = $iterator->key();
            $modifications = $iterator->current();

            foreach ($modifications as $modificationId => $files) {
                $logModification = $cacheLoggerModifications($modificationId);
                $logModification->setVerbosity($verbosity);
            }
        }

        return true;
    }
}
