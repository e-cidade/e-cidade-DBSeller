<?php

namespace App\Domain\Configuracao\Helpers;

use BusinessException;
use Exception;
use stdClass;

/**
 * Persistência local de arquivos quando o e-Storage remoto não está configurado.
 */
class LocalFileStorage
{
    const DEFAULT_PATH = 'storage/estorage';
    const META_FILE = 'meta.json';
    const NEXT_ID_FILE = '.next_id';
    const DATA_FILE_PREFIX = 'file';

    /**
     * @var string
     */
    private $basePath;

    public function __construct($basePath = null)
    {
        $this->basePath = rtrim($basePath ?: self::resolveBasePath(), '/\\');
        $this->ensureDirectory($this->basePath);
    }

    /**
     * @return string
     */
    public static function resolveBasePath()
    {
        try {
            $config = StorageHelper::getStorageConfigOrNull();
            if (!empty($config->local_path)) {
                return $config->local_path;
            }
        } catch (Exception $e) {
            // usa default
        }

        return self::DEFAULT_PATH;
    }

    /**
     * @param string $caminho
     * @param array|null $allowed
     * @param stdClass|null $metadata
     * @param mixed $fileFather
     * @param string|null $clientOriginalName
     * @return stdClass
     * @throws Exception
     */
    public function upload(
        $caminho,
        array $allowed = null,
        stdClass $metadata = null,
        $fileFather = null,
        $clientOriginalName = null
    ) {
        $this->assertReadableFile($caminho);

        $id = $this->nextId();
        $name = $clientOriginalName !== null && $clientOriginalName !== ''
            ? basename($clientOriginalName)
            : basename($caminho);
        $dir = $this->directoryFor($id);
        $this->ensureDirectory($dir);

        $storedName = $this->storedFileName($name);
        $destination = $dir . DIRECTORY_SEPARATOR . $storedName;

        if (!copy($caminho, $destination)) {
            throw new Exception("Não foi possível salvar o arquivo localmente (id {$id}).");
        }

        $meta = array(
            'id' => $id,
            'name' => $name,
            'stored_name' => $storedName,
            'mime' => $this->detectMime($caminho, $name),
            'visibility' => 'private',
            'metadata' => $metadata,
            'file_father' => $fileFather,
            'allowed' => $allowed,
            'created_at' => date('c'),
        );

        $this->writeMeta($dir, $meta);

        return $this->toResponse($meta);
    }

    /**
     * @param int|string $id
     * @param string $caminho
     * @param array|null $allowed
     * @param stdClass|null $metadata
     * @return stdClass
     * @throws Exception
     */
    public function update($id, $caminho, array $allowed = null, stdClass $metadata = null)
    {
        $this->assertReadableFile($caminho);

        $dir = $this->directoryFor($id);
        if (!is_dir($dir)) {
            throw new Exception("Arquivo local não encontrado (id {$id}).");
        }

        $existing = $this->readMeta($dir);
        $name = basename($caminho);
        $storedName = $this->storedFileName($name);
        $destination = $dir . DIRECTORY_SEPARATOR . $storedName;

        if (!empty($existing['stored_name'])) {
            $oldFile = $dir . DIRECTORY_SEPARATOR . $existing['stored_name'];
            if (is_file($oldFile) && $oldFile !== $destination) {
                @unlink($oldFile);
            }
        }

        if (!copy($caminho, $destination)) {
            throw new Exception("Não foi possível atualizar o arquivo localmente (id {$id}).");
        }

        $meta = array_merge($existing, array(
            'id' => (int) $id,
            'name' => $name,
            'stored_name' => $storedName,
            'mime' => $this->detectMime($caminho, $name),
            'visibility' => isset($existing['visibility']) ? $existing['visibility'] : 'private',
            'metadata' => $metadata !== null ? $metadata : (isset($existing['metadata']) ? $existing['metadata'] : null),
            'allowed' => $allowed !== null ? $allowed : (isset($existing['allowed']) ? $existing['allowed'] : null),
            'updated_at' => date('c'),
        ));

        $this->writeMeta($dir, $meta);

        return $this->toResponse($meta);
    }

    /**
     * @param int|string $id
     * @return string path em tmp/ para consumo pelos callers
     * @throws Exception
     */
    public function getPath($id)
    {
        $source = $this->resolveStoredFile($id);
        $meta = $this->readMeta($this->directoryFor($id));
        $extension = pathinfo($meta['name'], PATHINFO_EXTENSION);

        $tmp = tempnam('tmp/', 'estorage_file_');
        if ($tmp === false) {
            throw new Exception("Não foi possível criar arquivo temporário para download local.");
        }

        $path = $tmp;
        if (!empty($extension)) {
            $path = $tmp . '.' . $extension;
            @unlink($tmp);
        }

        if (!copy($source, $path)) {
            throw new Exception("Não foi possível ler o arquivo local (id {$id}).");
        }

        return $path;
    }

    /**
     * @param int|string $id
     * @return array
     * @throws Exception
     */
    public function getBase64($id)
    {
        $source = $this->resolveStoredFile($id);
        $meta = $this->readMeta($this->directoryFor($id));
        $content = file_get_contents($source);

        if ($content === false) {
            throw new Exception("Não foi possível ler o arquivo local (id {$id}).");
        }

        $contentType = !empty($meta['mime']) ? $meta['mime'] : 'application/octet-stream';
        $base64 = base64_encode($content);

        return array(
            'documento' => "data:{$contentType};base64,{$base64}",
            'content_type' => $contentType,
        );
    }

    /**
     * Remove o arquivo e a pasta do ID no storage local.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete($id)
    {
        $dir = $this->directoryFor($id);

        if (!is_dir($dir)) {
            return true;
        }

        foreach (scandir($dir) as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $entry;
            if (is_file($path) && !@unlink($path)) {
                throw new Exception("Não foi possível excluir o arquivo local (id {$id}).");
            }
        }

        if (!@rmdir($dir)) {
            throw new Exception("Não foi possível excluir o diretório do arquivo local (id {$id}).");
        }

        return true;
    }

    /**
     * Em modo local não há versionamento remoto; devolve o próprio ID.
     *
     * @param int|string $id
     * @return stdClass
     * @throws Exception
     */
    public function currentVersion($id)
    {
        $dir = $this->directoryFor($id);
        if (!is_dir($dir)) {
            throw new Exception("Arquivo local não encontrado (id {$id}).");
        }

        $response = new stdClass();
        $response->data = new stdClass();
        $response->data->codigo = (int) $id;
        $response->data->id = (int) $id;

        return $response;
    }

    /**
     * @param array $meta
     * @return stdClass
     */
    private function toResponse(array $meta)
    {
        $response = new stdClass();
        $response->id = (int) $meta['id'];
        $response->name = $meta['name'];
        $response->url = null;
        $response->visibility = isset($meta['visibility']) ? $meta['visibility'] : 'private';

        return $response;
    }

    /**
     * @param int|string $id
     * @return string
     * @throws Exception
     */
    private function resolveStoredFile($id)
    {
        $dir = $this->directoryFor($id);
        if (!is_dir($dir)) {
            throw new Exception("Arquivo local não encontrado (id {$id}).");
        }

        $meta = $this->readMeta($dir);
        if (empty($meta['stored_name'])) {
            throw new BusinessException("Metadata do arquivo local inválida (id {$id}).");
        }

        $path = $dir . DIRECTORY_SEPARATOR . $meta['stored_name'];
        if (!is_file($path)) {
            throw new Exception("Arquivo local não encontrado (id {$id}).");
        }

        return $path;
    }

    /**
     * @param int|string $id
     * @return string
     */
    private function directoryFor($id)
    {
        return $this->basePath . DIRECTORY_SEPARATOR . (int) $id;
    }

    /**
     * @param string $originalName
     * @return string
     */
    private function storedFileName($originalName)
    {
        $safe = preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName);
        if ($safe === '' || $safe === null) {
            $safe = 'file.bin';
        }

        return self::DATA_FILE_PREFIX . '_' . $safe;
    }

    /**
     * @return int
     * @throws Exception
     */
    private function nextId()
    {
        $counterFile = $this->basePath . DIRECTORY_SEPARATOR . self::NEXT_ID_FILE;
        $handle = fopen($counterFile, 'c+');

        if ($handle === false) {
            throw new Exception('Não foi possível abrir o contador de IDs do storage local.');
        }

        try {
            if (!flock($handle, LOCK_EX)) {
                throw new Exception('Não foi possível obter lock do contador de IDs do storage local.');
            }

            $contents = stream_get_contents($handle);
            $current = (int) trim((string) $contents);
            if ($current < 1) {
                $current = 1;
            }

            $next = $current + 1;

            ftruncate($handle, 0);
            rewind($handle);
            fwrite($handle, (string) $next);
            fflush($handle);
            flock($handle, LOCK_UN);

            return $current;
        } finally {
            fclose($handle);
        }
    }

    /**
     * @param string $dir
     * @param array $meta
     * @throws Exception
     */
    private function writeMeta($dir, array $meta)
    {
        $path = $dir . DIRECTORY_SEPARATOR . self::META_FILE;
        $json = json_encode($meta);

        if ($json === false || file_put_contents($path, $json) === false) {
            throw new Exception('Não foi possível gravar a metadata do arquivo local.');
        }
    }

    /**
     * @param string $dir
     * @return array
     * @throws Exception
     */
    private function readMeta($dir)
    {
        $path = $dir . DIRECTORY_SEPARATOR . self::META_FILE;
        if (!is_file($path)) {
            throw new Exception("Metadata do arquivo local não encontrada ({$path}).");
        }

        $data = json_decode(file_get_contents($path), true);
        if (!is_array($data)) {
            throw new Exception("Metadata do arquivo local inválida ({$path}).");
        }

        return $data;
    }

    /**
     * @param string $caminho
     * @param string $name
     * @return string
     */
    private function detectMime($caminho, $name)
    {
        if (function_exists('mime_content_type')) {
            $mime = @mime_content_type($caminho);
            if (!empty($mime)) {
                return $mime;
            }
        }

        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $map = array(
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'txt' => 'text/plain',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'zip' => 'application/zip',
        );

        return isset($map[$extension]) ? $map[$extension] : 'application/octet-stream';
    }

    /**
     * @param string $caminho
     * @throws Exception
     */
    private function assertReadableFile($caminho)
    {
        if (!is_file($caminho) || !is_readable($caminho)) {
            throw new Exception("Arquivo de origem inválido ou inacessível: {$caminho}");
        }
    }

    /**
     * @param string $path
     * @throws Exception
     */
    private function ensureDirectory($path)
    {
        if (is_dir($path)) {
            return;
        }

        if (!mkdir($path, 0775, true) && !is_dir($path)) {
            throw new Exception("Não foi possível criar o diretório de storage local: {$path}");
        }
    }
}
