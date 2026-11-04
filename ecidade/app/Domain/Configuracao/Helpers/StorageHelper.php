<?php
namespace App\Domain\Configuracao\Helpers;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Configuracao\Instituicao\Model\DBConfig as Instituicao;
use App\Domain\Configuracao\Instituicao\Repository\Contracts\InstituicaoRepository as RepositoryInterface;
use ECidade\Lib\Request\Storage\Curl\Autenticacao;
use ECidade\Lib\Request\Storage\Curl\CurrentVersion;
use ECidade\Lib\Request\Storage\Curl\Delete;
use ECidade\Lib\Request\Storage\Curl\Get;
use ECidade\Lib\Request\Storage\Curl\Post;
use ECidade\Lib\Request\Storage\File;
use ECidade\Lib\File\FileEstorage;
use ECidade\V3\Extension\Registry;
use Illuminate\Support\Facades\Log;
use ParameterException;
use Exception;

/**
* Classe helper que reune as fun??es para trabalhar com o storage
*
* @var string
*/
class StorageHelper
{
    /**
     * Indica se o e-Storage remoto est? configurado com credenciais v?lidas.
     *
     * @return bool
     */
    public static function isConfigured()
    {
        $config = self::getStorageConfigOrNull();

        if ($config === null) {
            return false;
        }

        return !empty($config->url)
            && !empty($config->client_id)
            && !empty($config->client_secret);
    }

    /**
     * Retorna a configura??o do e-Storage ou null quando ausente.
     *
     * @return \stdClass|null
     */
    public static function getStorageConfigOrNull()
    {
        if (!Registry::get('app.config')->has('app.api')) {
            return null;
        }

        $configApi = (object) Registry::get('app.config')->get('app.api');

        if (empty($configApi) || empty($configApi->estorage)) {
            return null;
        }

        return (object) $configApi->estorage;
    }

    /**
     * Realiza upload de um arquivo para o E-Storage (ou storage local).
     *
     * @param string $caminho
     * @param boolean $onlyId
     * @param \stdClass $metadata
     * @param  $fileFather
     * @return \stdclass|integer $data|$id9
     */
    public static function uploadArquivo(
        $caminho,
        array $allowed = null,
        $onlyId = false,
        \stdClass $metadata = null,
        $fileFather = null,
        $clientOriginalName = null
    ) {
        if ($clientOriginalName === null) {
            $caminhoPartes = explode("/", $caminho);
            $clientOriginalName = end($caminhoPartes);
        }

        if (!self::isConfigured()) {
            $retorno = (new LocalFileStorage())->upload(
                $caminho,
                $allowed,
                $metadata,
                $fileFather,
                $clientOriginalName
            );
            if ($onlyId) {
                return $retorno->id;
            }
            return $retorno;
        }

        $post = new Post(Autenticacao::getInstance());
        $file = new File();
        $file->realPath($caminho)
            ->clientOriginalName($clientOriginalName);
        if (!empty($metadata)) {
            $file->metadata($metadata);
        }
        $file->visibility("private");
        $file->allowed($allowed);
        if (!empty($fileFather)) {
            $file->fileFather($fileFather);
        }
        $retorno = $post->execute($file);
        if ($onlyId) {
            return $retorno->data->id;
        }
        return $retorno->data;
    }


    public static function atualizarArquivo(
        $id,
        $caminho,
        Array $allowed = null,
        $onlyId = false,
        \stdClass $metadata = null
    ) {
        if (!self::isConfigured()) {
            $retorno = (new LocalFileStorage())->update($id, $caminho, $allowed, $metadata);
            if ($onlyId) {
                return $retorno->id;
            }
            return $retorno;
        }

        $arquivo = explode("/", $caminho);
        $post = new Post(Autenticacao::getInstance());
        $file = new File();
        $file->realPath($caminho)
            ->clientOriginalName($arquivo[count($arquivo) - 1]);
        if (!empty($metadata)) {
            $file->metadata($metadata);
        }
        $file->visibility("private");
        $file->allowed($allowed);
        $retorno = $post->change($id, $file);

        if ($onlyId) {
            return $retorno->data->id;
        }

        return $retorno->data;
    }

    /**
     * Realiza download de um arquivo do E-Storage.
     *
     * @param string $idStorage
     * @return string $caminhoArquivo
     */
    public static function downloadArquivo($idStorage)
    {
        return (new FileEstorage())->getPath($idStorage);
    }

    /**
     * Busca as configura??es do e-storage
     *
     * @return \stdclass
     */
    public static function getStorageConfig()
    {
        $config = self::getStorageConfigOrNull();

        if ($config === null) {
            if (!Registry::get('app.config')->has('app.api')) {
                $msg  = "Erro ao buscar as credencias das api's";
                $msg .= "\nVerifique o arquivo de configura??o (application).";
                throw new ParameterException($msg);
            }

            $msg  = "Erro ao buscar as credencias do e-Storage";
            $msg .= "\nVerifique o arquivo de configura??o (application).";
            throw new ParameterException($msg);
        }

        return $config;
    }


    /**
     * @param $idStorage
     * @return array
     * @throws Exception
     */
    public static function getContentsBase64($idStorage)
    {
        return (new FileEstorage())->getBase64($idStorage);
    }

    /**
     * @param $idStorage
     * @return bool
     * @throws Exception
     */
    public static function deleteArquivo($idStorage)
    {
        if (!self::isConfigured()) {
            return (new LocalFileStorage())->delete($idStorage);
        }

        $delete = new Delete(Autenticacao::getInstance());
        return $delete->delete($idStorage);
    }

    /**
     * @param $idStorage
     * @return mixed
     * @throws Exception
     */
    public static function currentVersion($idStorage)
    {
        if (!self::isConfigured()) {
            return (new LocalFileStorage())->currentVersion($idStorage);
        }

        $currentVersion = new CurrentVersion(Autenticacao::getInstance());
        return $currentVersion->execute($idStorage);
    }
}
