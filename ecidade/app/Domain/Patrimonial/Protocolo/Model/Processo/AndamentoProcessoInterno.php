<?php

namespace App\Domain\Patrimonial\Protocolo\Model\Processo;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MensageriaProtocolo
 * @package App\Domain\Patrimonial\Protocolo\Model\Processo
 *
 * @property int $p78_sequencial
 * @property int $p78_codandam
 * @property string $p78_data
 * @property string $p78_hora
 * @property int $p78_usuario
 * @property string $p78_despacho
 * @property bool $p78_publico
 * @property bool $p78_transint
 * @property int $p78_tipodespacho
 * @property string $p78_mensageria
 */

class AndamentoProcessoInterno extends Model
{
    protected $table = 'protocolo.procandamint';
    protected $primaryKey = 'p78_sequencial';
    public $timestamps = false;
    protected $fillable = [
        'p78_codandam',
        'p78_data',
        'p78_hora',
        'p78_usuario',
        'p78_despacho',
        'p78_publico',
        'p78_transint',
        'p78_tipodespacho',
        'p78_mensageria',
    ];



    /**
     * @return int
     */
    public function getSequencial()
    {
        return $this->p78_sequencial;
    }

    /**
     * @param  int   $p78_sequencial
     */
    public function setSequencial($p78_sequencial)
    {
        $this->p78_sequencial = $p78_sequencial;
    }

    /**
     * @return int
     */
    public function getCodigoAndamento()
    {
        return $this->p78_codandam;
    }

    /**
     * @param  int  $p78_codandam
     */
    public function setCodigoAndamento($p78_codandam)
    {
        $this->p78_codandam = $p78_codandam;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->p78_data;
    }

    /**
     * @param  string  $p78_data
     */
    public function setData($p78_data)
    {
        $this->p78_data = $p78_data;
    }

    /**
     * @return string
     */
    public function getHora()
    {
        return $this->p78_hora;
    }

    /**
     * @param  string  $p78_hora
     */
    public function setHora($p78_hora)
    {
        $this->p78_hora = $p78_hora;
    }

    /**
     * @return int
     */
    public function getCodigoUsuario()
    {
        return $this->p78_usuario;
    }

    /**
     * @param  int  $p78_usuario
     */
    public function setCodigoUsuario($p78_usuario)
    {
        $this->p78_usuario = $p78_usuario;
    }

    /**
     * @return string
     */
    public function getDespacho()
    {
        return $this->p78_despacho;
    }

    /**
     * @param  string  $p78_despacho
     */
    public function setDespacho($p78_despacho)
    {
        $this->p78_despacho = $p78_despacho;
    }

    /**
     * @return bool
     */
    public function isPublico()
    {
        return $this->p78_publico;
    }

    /**
     * @param  bool  $p78_publico
     */
    public function setPublico($p78_publico)
    {
        $this->p78_publico = $p78_publico;
    }

    /**
     * @return bool
     */
    public function isTransint()
    {
        return $this->p78_transint;
    }

    /**
     * @param  bool  $p78_transint
     */
    public function setTransint($p78_transint)
    {
        $this->p78_transint = $p78_transint;
    }

    /**
     * @return int
     */
    public function getTipoDespacho()
    {
        return $this->p78_tipodespacho;
    }

    /**
     * @param  int  $p78_tipodespacho
     */
    public function setTipoDespacho($p78_tipodespacho)
    {
        $this->p78_tipodespacho = $p78_tipodespacho;
    }

    /**
     * @return string
     */
    public function getMensageria()
    {
        return $this->p78_mensageria;
    }

    /**
     * @param  string  $p78_mensageria
     */
    public function setMensageria($p78_mensageria)
    {
        $this->p78_mensageria = $p78_mensageria;
    }
}
