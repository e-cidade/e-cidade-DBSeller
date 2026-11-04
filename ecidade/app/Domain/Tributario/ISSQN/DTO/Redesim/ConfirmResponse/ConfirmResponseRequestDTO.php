<?php

namespace App\Domain\Tributario\ISSQN\DTO\Redesim\ConfirmResponse;

use App\Domain\Tributario\ISSQN\DTO\Redesim\BaseRequestDTO;
use App\Domain\Tributario\ISSQN\Model\Base\IssBase;
use App\Domain\Tributario\ISSQN\Model\Redesim\ProcessedEstablishment;
use App\Domain\Tributario\ISSQN\Reports\Alvara\AlvaraBaseReport;
use Carbon\Carbon;

class ConfirmResponseRequestDTO extends BaseRequestDTO
{
    public $identificador;

    public $cnpj;

    public $codSituacao;

    public $numeroInscricao;

    public $numeroAlvara;

    public $numeroLicenca;

    public $observacao;

    public $arquivoPDF;

    public $dataExpedicao;

    public $dataValidade;

    public function __construct(
        $accessKeyId,
        $secretAccessKey,
        ProcessedEstablishment $processedEstablishment,
        $issBase,
        $alvaraReport,
        $alvaraInfo,
        $isDeferred
    ) {
        parent::__construct($accessKeyId, $secretAccessKey);

        $this->identificador = $processedEstablishment->q190_external_id;
        $this->cnpj = $issBase ? $issBase->cgm->z01_cgccpf : null;
        $this->numeroInscricao = $issBase ? $issBase->q02_inscr : null;
        $this->codSituacao = $isDeferred ? "D" : "I";

        $this->arquivoPDF = $alvaraReport ? $alvaraReport->getBase64() : null;

        if ($alvaraInfo) {
            $alvaraDateCreated = Carbon::createFromFormat("Y-m-d", $alvaraInfo->q123_dtinclusao);
            $this->numeroAlvara = $alvaraInfo->q123_sequencial;
            $this->dataExpedicao = $alvaraDateCreated->format("Ymd");

            if ($alvaraInfo->q120_validadealvara) {
                $this->dataValidade = $alvaraDateCreated->addDay($alvaraInfo->q120_validadealvara)->format("Ymd");
            }
        }
    }
}
