<?php

namespace ECidade\Core\Visualizador\Services;

use ECidade\File\Pdf\PdfToImage;
use ECidade\Lib\File\FileEstorage;
use Exception;

/**
 * Class VisualizadorDocumentosService
 */
class VisualizadorDocumentosService
{
    /**
     * @var FileEstorage
     */
    private $fileEstorage;

    /**
     * VisualizadorDocumentosService constructor.
     */
    public function __construct()
    {
        $this->fileEstorage = new FileEstorage();
    }

    /**
     * @param array $codigos
     * @return array
     * @throws Exception
     */
    public function getImages(array $codigos)
    {
        $imagens = [];
        foreach ($codigos as $key => $fileId) {
            $fileId = trim($fileId);
            if (empty($fileId)) {
                continue;
            }

            $description = 'undefined';
            if (!empty($descricoes[$key])) {
                $description = $descricoes[$key];
            }

            $directory = "tmp";

            $file = $this->fileEstorage->getPath($fileId);
            $fileInfo = pathinfo($file);
            $extensao = $this->verificaExtensao($fileInfo);

            if ($fileInfo['extension'] == "pdf") {
                $prefix = $fileInfo['filename'];
                $path = "{$directory}/{$fileInfo['basename']}";

                $pdf = new PdfToImage($path);
                $pdf->setOutputFormat('png');

                $images = $pdf->saveAllPagesAsImages($directory, $prefix);

                foreach ($images as $image) {
                    $imagens[] = (object)[
                        "id" => $fileId,
                        "download" => $path,
                        "original" => $image['path'],
                        "thumbnail" => $image['path'],
                        "descricao" => utf8_decode($description),
                        "key" => $image['pageNumber']
                    ];
                }

                continue;
            }

            $imagens[] = (object)[
                "id" => $fileId,
                "download" => "{$directory}/{$fileInfo['basename']}",
                "original" => $extensao->original,
                "thumbnail" => $extensao->thumb,
                "descricao" => utf8_decode($description),
            ];
        }

        return $imagens;
    }

    /**
     * @param $fileInfo
     * @return object
     */
    public function verificaExtensao($fileInfo)
    {
        switch ($fileInfo['extension']) {
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                $thumb = 'tmp/' . $fileInfo['basename'];
                $original = 'tmp/' . $fileInfo['basename'];
                break;
            case 'doc':
                $thumb = 'imagens/filetypes/FileTypes26.png';
                $original = 'imagens/filetypes/FileTypes26.png';
                break;
            case 'docx':
                $thumb = 'imagens/filetypes/FileTypes36.png';
                $original = 'imagens/filetypes/FileTypes36.png';
                break;
            case 'odt':
                $thumb = 'imagens/filetypes/FileTypes51.png';
                $original = 'imagens/filetypes/FileTypes51.png';
                break;
            case 'zip':
                $thumb = 'imagens/filetypes/FileTypes29.png';
                $original = 'imagens/filetypes/FileTypes29.png';
                break;
            case 'rar':
                $thumb = 'imagens/filetypes/FileTypes27.png';
                $original = 'imagens/filetypes/FileTypes27.png';
                break;
            case 'xlsx':
                $thumb = 'imagens/filetypes/FileTypes52.png';
                $original = 'imagens/filetypes/FileTypes52.png';
                break;
            default:
                $thumb = 'imagens/filetypes/FileTypesX.png';
                $original = 'imagens/filetypes/FileTypesX.png';
                break;
        }

        return (object)["thumb" => $thumb, "original" => $original];
    }
}
