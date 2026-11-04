<?php

namespace App\Domain\Patrimonial\Protocolo\Model;

use App\Domain\Patrimonial\Protocolo\Model\Processo\ProcessoDocumento;
use Illuminate\Database\Eloquent\Model;

class DocumentoSolicitacaoAssinatura extends Model
{
    protected $fillable = [
        "documento_id",
        "data_assinatura",
        "cgm_assinante",
        "cgm_solicitante",
    ];


    /**
     * @return int
     */
    public function getDocumentoId()
    {
        return $this->documento_id;
    }

    /**
     * @param int $documento_id
     */
    public function setDocumentoId($documento_id)
    {
        $this->documento_id = $documento_id;
    }

    /**
     * @return string
     */
    public function getDataAssinatura()
    {
        return $this->data_assinatura;
    }

    /**
     * @param string $data_assinatura
     */
    public function setDataAssinatura($data_assinatura)
    {
        $this->data_assinatura = $data_assinatura;
    }

    /**
     * @return int
     */
    public function getCgmAssinante()
    {
        return $this->cgm_assinante;
    }

    /**
     * @param int $cgm_assinante
     */
    public function setCgmAssinante($cgm_assinante)
    {
        $this->cgm_assinante = $cgm_assinante;
    }

    /**
     * @return int
     */
    public function getCgmSolicitante()
    {
        return $this->cgm_solicitante;
    }

    /**
     * @param int $cgm_solicitante
     */
    public function setCgmSolicitante($cgm_solicitante)
    {
        $this->cgm_solicitante = $cgm_solicitante;
    }

    public function cgmAssinante()
    {
        return $this->belongsTo(Cgm::class, "cgm_assinante", "z01_numcgm");
    }

    public function cgmSolicitante()
    {
        return $this->belongsTo(Cgm::class, 'cgm_solicitante', 'z01_numcgm');
    }

    public function documento()
    {
        return $this->belongsTo(ProcessoDocumento::class, "documento_id", "p01_sequencial");
    }
}
