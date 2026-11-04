<?php

namespace App\Domain\Patrimonial\Protocolo\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class HistoricoVisualizacaoMensageria
 * @package App\Domain\Patrimonial\Ouvidoria\Model
 *
 * @property int p125_id
 * @property int p125_processo
 * @property string p125_cpfcnpj
 * @property string p125_nome
 * @property timestamp p125_data
 */

class HistoricoVisualizacaoMensageria extends Model
{
    protected $table = 'protocolo.historico_visualizacao_mensageria';
    protected $primaryKey = 'p125_id';
    public $timestamps = false;
    protected $fillable = [
        'p125_processo',
        'p125_cpfcnpj',
        'p125_nome',
        'p125_data',
    ];

    /**
     * @return int
     */
    public function getId()
    {
        return $this->p125_id;
    }

    /**
     * @param int $p125_id
     */
    public function setId($p125_id)
    {
        $this->p125_id = $p125_id;
    }

    /**
     * @return int
     */
    public function getProcesso()
    {
        return $this->p125_processo;
    }

    /**
     * @param int $p125_processo
     */
    public function setProcesso($p125_processo)
    {
        $this->p125_processo = $p125_processo;
    }

    /**
     * @return string
     */
    public function getCpfCnpj()
    {
        return $this->p125_cpfcnpj;
    }

    /**
     * @param string $p125_cpfcnpj
     */
    public function setCpfCnpj($p125_cpfcnpj)
    {
        $this->p125_cpfcnpj = $p125_cpfcnpj;
    }

    /**
     * @return string
     */
    public function getNome()
    {
        return $this->p125_nome;
    }

    /**
     * @param string $p125_nome
     */
    public function setNome($p125_nome)
    {
        $this->p125_nome = $p125_nome;
    }

    /**
     * @return timestamp
     */
    public function getData()
    {
        return $this->p125_data;
    }

    /**
     * @param timestamp $p125_data
     */
    public function setData($p125_data)
    {
        $this->p125_data = $p125_data;
    }
}
