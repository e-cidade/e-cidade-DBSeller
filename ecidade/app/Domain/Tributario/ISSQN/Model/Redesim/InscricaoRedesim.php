<?php

namespace App\Domain\Tributario\ISSQN\Model\Redesim;

use App\Domain\Tributario\ISSQN\Model\Base\IssBase;
use Illuminate\Database\Eloquent\Model;

/**
 * Domain\Tributario\ISSQN\Model\Redesim\InscricaoRedesim
 *
 * @method static Builder|InscricaoRedesim betweenDataCadastroInscricao($dataInicio,$dataFim)
 */
class InscricaoRedesim extends Model
{
    protected $table = 'inscricaoredesim';
    protected $primaryKey = 'q179_sequencial';
    public $timestamps = false;
    public static $snakeAttributes = false;

    public function issBase()
    {
        return $this->hasOne(IssBase::class, "q02_inscr", "q179_inscricao");
    }

    /**
     * Filtra as inscrições criadas no período se baseando na data de cadastro da inscrição
     * @param $query
     * @param $dataInicio
     * @param $dataFim
     * @return mixed
     */
    public function scopeBetweenDataCadastroInscricao($query, $dataInicio, $dataFim)
    {
        if (empty($dataInicio) || empty($dataFim)) {
            return $query;
        }

        return $query->where('inscricaoredesim.q179_inscricao', function ($query) use ($dataInicio, $dataFim) {
            $query->select('q02_inscr')
                  ->from('issbase')
                  ->whereColumn('q02_inscr', 'inscricaoredesim.q179_inscricao')
                  ->whereRaw("TO_CHAR(q02_dtcada, 'YYYY-MM-DD') BETWEEN ? AND ?", [$dataInicio, $dataFim])
                  ->limit(1);
        });
    }

    /**
     * @return int
     */
    public function getSequencial()
    {
        return $this->q179_sequencial;
    }

    /**
     * @param int $q179_sequencial
     */
    public function setSequencial($q179_sequencial)
    {
        $this->q179_sequencial = $q179_sequencial;
    }

    /**
     * @return int
     */
    public function getInscricao()
    {
        return $this->q179_inscricao;
    }

    /**
     * @param int $q179_inscricao
     */
    public function setInscricao($q179_inscricao)
    {
        $this->q179_inscricao = $q179_inscricao;
    }

    /**
     * @return int
     */
    public function getProcesso()
    {
        return $this->q179_processo;
    }

    /**
     * @param int $q179_processo
     */
    public function setProcesso($q179_processo)
    {
        $this->q179_processo = $q179_processo;
    }

    /**
     * @return string
     */
    public function getIdentificadorRedesim()
    {
        return $this->q179_identificadorredesim;
    }

    /**
     * @param string q179_identificadorredesim
     */
    public function setIdentificadorRedesim($q179_identificadorredesim)
    {
        $this->q179_identificadorredesim = $q179_identificadorredesim;
    }
}
