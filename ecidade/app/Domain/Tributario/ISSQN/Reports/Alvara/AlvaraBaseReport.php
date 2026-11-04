<?php

namespace App\Domain\Tributario\ISSQN\Reports\Alvara;

use App\Domain\Tributario\ISSQN\Model\Base\IssBase;
use Carbon\Carbon;

abstract class AlvaraBaseReport
{
    const ALVARA_TAMANHO_A5 = 1;
    const ALVARA_TAMANHO_A4 = 2;
    const ALVARA_PRE_IMPRESSO = 3;
    const ALVARA_A4_FONTE_REDUZIDA = 4;
    const ALVARA_PRE_IMPRESSO_TAMANHO_A4 = 5;
    const ALVARA_PRE_IMPRESSO_TAMANHO_A4_CNAE = 6;
    const ALVARA_A4_FRENTE_VERSO = 7;
    const ALVARA_TAMANHO_A4_PROCESSO_AREA = 8;
    const DOCUMENTO_ALVARA = 9;

    /**
     * @var string
     */
    const FILE_PATH = "tmp/";

    /**
     * @var IssBase
     */
    protected $issBase;

    /**
     * @var string
     */
    protected $fileName;

    public function __construct(IssBase $issBase)
    {
        $this->issBase = $issBase;
        $this->fileName = $this->buildFileName($issBase);
    }

    public function getFilePath($extension = "pdf")
    {
        return AlvaraBaseReport::FILE_PATH."{$this->fileName}.{$extension}";
    }

    public function getFullFilePath($extension = "pdf")
    {
        return ECIDADE_PATH.$this->getFilePath($extension);
    }

    public function getBase64($deleteFile = true)
    {
        $fullFilePath = $this->getFullFilePath();
        $file = base64_encode(file_get_contents($fullFilePath));

        if ($deleteFile) {
            unlink($fullFilePath);
        }

        return $file;
    }

    private function buildFileName(IssBase $issBase)
    {
        $currentTimestamp = Carbon::now()->getTimestamp();
        $rawFileName = "{$issBase->q02_inscr}-{$currentTimestamp}-".uniqid();
        return md5($rawFileName);
    }
}
