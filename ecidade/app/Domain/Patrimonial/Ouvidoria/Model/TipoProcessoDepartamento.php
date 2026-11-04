<?php

namespace App\Domain\Patrimonial\Ouvidoria\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $p41_sequencial
 * @property int $p41_tipoproc
 * @property int $p41_coddepto
 */
class TipoProcessoDepartamento extends Model
{
    protected $table = 'ouvidoria.tipoprocdepto';
    protected $primaryKey = 'p41_sequencial';
    public $timestamps = false;

    /**
     * @param  int  $p41_sequencial
     */
    public function setSequencial($p41_sequencial)
    {
        $this->p41_sequencial = $p41_sequencial;
    }

    /**
     * @return int
     */
    public function getSequencial()
    {
        return $this->p41_sequencial;
    }

    /**
     * @param  int  $p41_tipoproc
     */
    public function setTipoProcessoCodigo($p41_tipoproc)
    {
        $this->p41_tipoproc = $p41_tipoproc;
    }

    /**
     * @return int
     */
    public function getTipoProcessoCodigo()
    {
        return $this->p41_tipoproc;
    }

    /**
     * @param  int  $p41_coddepto
     */
    public function setDepartamentoCodigo($p41_coddepto)
    {
        $this->p41_coddepto = $p41_coddepto;
    }

    /**
     * @return int
     */
    public function getDepartamentoCodigo()
    {
        return $this->p41_coddepto;
    }
}
