<?php

namespace ECidade\Lib\File;

use App\Domain\Configuracao\Helpers\LocalFileStorage;
use App\Domain\Configuracao\Helpers\StorageHelper;
use ECidade\Lib\Request\Curl;
use ECidade\Lib\Request\Storage\Curl\Autenticacao;
use ECidade\Lib\Request\Storage\Curl\Get;

use \BusinessException;
use \Exception;
use \JSON;

class FileEstorage
{
    private $path;
    private $autenticacao;
    private $localMode = false;

    public function __construct()
    {
        if (!StorageHelper::isConfigured()) {
            $this->localMode = true;
            return;
        }

        $this->autenticacao = Autenticacao::getInstance();
        $this->autenticacao->execute();
    }

    protected function get($idFile, $retornaBase64 = false)
    {
        $file = new Get($this->autenticacao);
        $file->setCodigoArquivo($idFile);
        $file->execute();

        $infoRequest = $file->getInfo();
        $response    = $file->getResponse();

        if ($infoRequest['http_code'] != 200) {
            $response = JSON::create()->parse($response);
            $msg = $response->message;

            if (!empty($response->errors) && !empty($response->errors->permission)) {
                $msg .= "\n";
                $msg .= implode("\n", $response->errors->permission);
            }
            throw new Exception($msg);
        }

        if ($retornaBase64) {
            $base64 = base64_encode($response);
            $data["documento"] = "data:{$infoRequest['content_type']};base64,{$base64}";
            $data["content_type"] = $infoRequest['content_type'];
            return $data;
        }

        $this->path  = tempnam('tmp/', 'estorage_file_');
        $this->path .= '.';
        // file_put_contents('tmp/debug', print_r($infoRequest, true));

        $infoRequest['content_type'] = trim($infoRequest['content_type']);
        switch ($infoRequest['content_type']) {
            case 'application/x-abiword':
                $extension = 'abw';
                break;

            case 'application/octet-stream':
                $extension = 'arc';
                break;

            case 'video/x-msvideo':
                $extension = 'avi';
                break;

            case 'application/vnd.amazon.ebook':
                $extension = 'azw';
                break;

            case 'application/octet-stream':
                $extension = 'bin';
                break;

            case 'application/x-bzip':
                $extension = 'bz';
                break;

            case 'application/x-bzip2':
                $extension = 'bz2';
                break;

            case 'application/x-csh':
                $extension = 'csh';
                break;

            case 'application/vnd.ms-fontobject':
                $extension = 'eot';
                break;

            case 'application/epub+zip':
                $extension = 'epub';
                break;

            case 'image/x-icon':
                $extension = 'ico';
                break;

            case 'application/java-archive':
                $extension = 'jar';
                break;

            case 'application/vnd.apple.installer+xml':
                $extension = 'mpkg';
                break;

            case 'application/vnd.oasis.opendocument.presentation':
                $extension = 'odp';
                break;

            case 'application/vnd.oasis.opendocument.spreadsheet':
                $extension = 'ods';
                break;

            case 'application/vnd.oasis.opendocument.text':
                $extension = 'odt';
                break;

            case 'application/vnd.ms-powerpoint':
                $extension = 'ppt';
                break;

            case 'application/x-rar-compressed':
                $extension = 'rar';
                break;

            case 'application/x-sh':
                $extension = 'sh';
                break;

            case 'image/svg+xml':
                $extension = 'svg';
                break;

            case 'application/x-shockwave-flash':
                $extension = 'swf';
                break;

            case 'application/x-tar':
                $extension = 'tar';
                break;

            case 'application/vnd.visio':
                $extension = 'vsd';
                break;

            case 'audio/x-wav':
                $extension = 'wav';
                break;

            case 'application/xhtml+xml':
                $extension = 'xhtml';
                break;

            case 'application/vnd.ms-excel':
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                $extension = 'xlsx';
                break;
            case 'application/vnd.mozilla.xul+xml':
                $extension = 'xul';
                break;

            case 'audio/3gpp':
                $extension = '3gp';
                break;

            case 'audio/3gpp2':
                $extension = '3g2';
                break;

            case 'application/x-7z-compressed':
                $extension = '7z';
                break;


            case 'application/typescript':
                $extension = 'ts';
                break;

            case 'audio/webm':
                $extension = 'weba';
                break;

            case 'audio/ogg':
                $extension = 'oga';
                break;

            case 'video/ogg':
                $extension = 'ogv';
                break;

            case 'application/ogg':
                $extension = 'ogx';
                break;

            case 'application/javascript':
                $extension = 'js';
                break;

            case 'text/calendar':
                $extension = 'ics';
                break;

            case 'text\/plain':
            case (preg_match('/text\/plain.*/', $infoRequest['content_type'], $searched) && $searched):
                $extension = 'txt';
                break;

            case 'application/msword':
                $extension = 'doc';
                break;
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                $extension = 'docx';
                break;

            default:
                $extension = preg_replace('/\w+\/(\w+)$/', "$1", $infoRequest['content_type']);
                break;
        }

        if (empty($extension)) {
            throw new BusinessException("Não foi possível identificar o tipo de arquivo");
        }

        $this->path .= $extension;
        $this->pathRelative = "tmp/".basename($this->path);
        file_put_contents($this->path, $response);
    }

    public function getPath($idFile)
    {
        if ($this->localMode) {
            return (new LocalFileStorage())->getPath($idFile);
        }

        $this->get($idFile);

        return $this->path;
    }

    public function getPathRelative($idFile)
    {
        $this->get($idFile);
        return $this->pathRelative;
    }

    public function getBase64($idFile)
    {
        if ($this->localMode) {
            return (new LocalFileStorage())->getBase64($idFile);
        }

        return $this->get($idFile, true);
    }
}
