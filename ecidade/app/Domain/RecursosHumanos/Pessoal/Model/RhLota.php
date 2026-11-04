<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RhLota
 * @property int r70_codigo
 * @property int r70_codestrut
 * @property string r70_estrut
 * @property string r70_descr
 * @property bool r70_analitica
 * @property int r70_instit
 * @property bool r70_ativo
 * @property string r70_concarpeculiar
 * @package App\Domain\RecursosHumanos\Pessoal\Model
 */
class RhLota extends Model
{
    protected $table = 'pessoal.rhlota';
    protected $primaryKey = ['r70_codigo'];
    public $incrementing = false;
    public $timestamps = false;

    /**
     * @return int
     */
    public function getCodigo()
    {
        return $this->r70_codigo;
    }

    /**
     * @param int
     */
    public function setCodigo($r70_codigo)
    {
        $this->r70_codigo = $r70_codigo;
    }

    /**
     * @return string
     */
    public function getCodigoEstrutural()
    {
        return $this->r70_estrut;
    }

    /**
     * @param string
     */
    public function setCodigoEstrutural($r70_estrut)
    {
        $this->r70_estrut = $r70_estrut;
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->r70_descr;
    }

    /**
     * @param string
     */
    public function setDescricao($r70_descr)
    {
        $this->r70_descr = $r70_descr;
    }

    /**
     * @return bool
     */
    public function isAnalitica()
    {
        return $this->r70_analitica;
    }

    /**
     * @param bool
     */
    public function setAnalitica($r70_analitica)
    {
        $this->r70_analitica = $r70_analitica;
    }

    // Refatorar para uso amigavel - por enquanto nao ta sendo utilizado, porem nao sei pra que serve esses campos
    /**
     * @return int
     */
    public function getCodigoEstrut()
    {
        return $this->r70_codestrut;
    }

    /**
     * @param int
     */
    public function setCodigoEstrut($r70_codestrut)
    {
        $this->r70_codestrut = $r70_codestrut;
    }

    /**
     * @return string
     */
    public function getConcarPeculiar()
    {
        return $this->r70_concarpeculiar;
    }

    /**
     * @param string
     */
    public function setConcarPeculiar($r70_concarpeculiar)
    {
        $this->r70_concarpeculiar = $r70_concarpeculiar;
    }
    // Fim Refatorar
}
