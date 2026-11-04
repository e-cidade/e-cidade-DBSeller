<?php

namespace ECidade\File\Pdf;

use Exception;
use Imagick;
use ImagickException;
use Illuminate\Support\Collection;

class PdfToImage
{
    /**
     * @var string
     */
    protected $pdfFile;
    /**
     * @var integer
     */
    protected $resolution = 144;
    /**
     * @var string
     */
    protected $outputFormat = 'jpg';
    /**
     * @var integer
     */
    protected $page = 1;
    /**
     * @var
     */
    public $imagick;
    /**
     * @var
     */
    protected $numberOfPages;
    /**
     * @var string[]
     */
    protected $validOutputFormats = ['jpg', 'jpeg', 'png'];
    /**
     * @var integer
     */
    protected $layerMethod = Imagick::LAYERMETHOD_FLATTEN;
    /**
     * @var
     */
    protected $colorspace;
    /**
     * @var
     */
    protected $compressionQuality;

    /**
     * PdfToImage constructor.
     * @param $pdfFile
     * @throws Exception
     */
    public function __construct($pdfFile)
    {
        if (!file_exists($pdfFile)) {
            throw new Exception("Arquivo não existe!");
        }

        $this->imagick = new Imagick();

        $this->imagick->pingImage($pdfFile);

        $this->numberOfPages = $this->imagick->getNumberImages();

        $this->pdfFile = $pdfFile;
    }

    /**
     * @param $resolution
     * @return $this
     */
    public function setResolution($resolution)
    {
        $this->resolution = $resolution;

        return $this;
    }

    /**
     * @param $outputFormat
     * @return $this
     * @throws Exception
     */
    public function setOutputFormat($outputFormat)
    {
        if (!$this->isValidOutputFormat($outputFormat)) {
            throw new Exception("O formato {$outputFormat} não é suportado!");
        }

        $this->outputFormat = $outputFormat;

        return $this;
    }

    /**
     * Sets the layer method for Imagick::mergeImageLayers()
     * If int, should correspond to a predefined LAYERMETHOD constant.
     * If null, Imagick::mergeImageLayers() will not be called.
     *
     * @param int|null
     *
     * @return $this
     * @throws Exception
     * @see Pdf::getImageData()
     * @see https://secure.php.net/manual/en/imagick.constants.php
     *
     * /**
     *
     */
    public function setLayerMethod($layerMethod)
    {
        if (!is_int($layerMethod)) {
            throw new Exception('LayerMethod precisa ser um inteiro');
        }

        $this->layerMethod = $layerMethod;

        return $this;
    }

    /**
     * @param $outputFormat
     * @return bool
     */
    public function isValidOutputFormat($outputFormat)
    {
        return in_array($outputFormat, $this->validOutputFormats);
    }

    /**
     * @param $page
     * @return $this
     * @throws Exception
     */
    public function setPage($page)
    {
        if ($page > $this->getNumberOfPages()) {
            throw new Exception("Página {$page} não existe.");
        }

        $this->page = $page;

        return $this;
    }

    /**
     * @return int
     */
    public function getNumberOfPages()
    {
        return $this->numberOfPages;
    }

    /**
     * @param $pathToImage
     * @return bool
     * @throws ImagickException
     */
    public function saveImage($pathToImage)
    {
        if (is_dir($pathToImage)) {
            $pathToImage = rtrim($pathToImage, '\/') . DIRECTORY_SEPARATOR . $this->page . '.' . $this->outputFormat;
        }

        $imageData = $this->getImageData($pathToImage);

        return file_put_contents($pathToImage, $imageData) !== false;
    }

    /**
     * @param $directory
     * @param string $prefix
     * @return Collection
     * @throws ImagickException
     */
    public function saveAllPagesAsImages($directory, $prefix = '')
    {
        $numberOfPages = $this->getNumberOfPages();

        if ($numberOfPages === 0) {
            return Collection::make([]);
        }

        $files = array_map(function ($pageNumber) use ($directory, $prefix) {
            $this->setPage($pageNumber);

            $destination = "{$directory}/{$prefix}{$pageNumber}.{$this->outputFormat}";

            $this->saveImage($destination);

            return [
                'pageNumber' => $pageNumber,
                'path' => $destination
            ];
        }, range(1, $numberOfPages));

        return Collection::make($files)->sortByDesc('pageNumber');
    }

    /**
     * @param $pathToImage
     * @return Imagick
     * @throws ImagickException
     */
    public function getImageData($pathToImage)
    {
        /*
         * Reinitialize imagick because the target resolution must be set
         * before reading the actual image.
         */
        $this->imagick = new Imagick();

        $this->imagick->setResolution($this->resolution, $this->resolution);

        if ($this->colorspace !== null) {
            $this->imagick->setColorspace($this->colorspace);
        }

        if ($this->compressionQuality !== null) {
            $this->imagick->setCompressionQuality($this->compressionQuality);
        }

        if (filter_var($this->pdfFile, FILTER_VALIDATE_URL)) {
            return $this->getRemoteImageData($pathToImage);
        }

        $this->imagick->readImage(sprintf('%s[%s]', $this->pdfFile, $this->page - 1));

        if (is_int($this->layerMethod)) {
            $this->imagick = $this->imagick->mergeImageLayers($this->layerMethod);
        }

        $this->imagick->setFormat($this->determineOutputFormat($pathToImage));

        return $this->imagick;
    }

    /**
     * @param $colorspace
     * @return $this
     */
    public function setColorspace($colorspace)
    {
        $this->colorspace = $colorspace;

        return $this;
    }

    /**
     * @param $compressionQuality
     * @return $this
     */
    public function setCompressionQuality($compressionQuality)
    {
        $this->compressionQuality = $compressionQuality;

        return $this;
    }

    protected function getRemoteImageData($pathToImage)
    {
        $this->imagick->readImage($this->pdfFile);

        $this->imagick->setIteratorIndex($this->page - 1);

        if (is_int($this->layerMethod)) {
            $this->imagick = $this->imagick->mergeImageLayers($this->layerMethod);
        }

        $this->imagick->setFormat($this->determineOutputFormat($pathToImage));

        return $this->imagick;
    }

    /**
     * @param $pathToImage
     * @return string
     */
    protected function determineOutputFormat($pathToImage)
    {
        $outputFormat = pathinfo($pathToImage, PATHINFO_EXTENSION);

        if ($this->outputFormat != '') {
            $outputFormat = $this->outputFormat;
        }

        $outputFormat = strtolower($outputFormat);

        if (!$this->isValidOutputFormat($outputFormat)) {
            $outputFormat = 'jpg';
        }

        return $outputFormat;
    }
}
