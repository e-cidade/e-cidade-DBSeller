<?php


namespace App\Domain\RecursosHumanos\Pessoal\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TabelaPrevidencia
 * @package App\Domain\RecursosHumanos\Pessoal\Model\Previdencia
 * @property int r44_selec
 * @property string r44_desc1
 * @property string r44_desc2
 * @property string r44_descr
 * @property string r44_obs
 * @property int r44_instit
 * @property string r44_where
 * @property int r44_gruposelecao
*/
class Selecao extends Model
{
    protected $table = 'pessoal.selecao';

    /**
     * @return int
     */
    public function getCodigo()
    {
        return $this->r44_selec;
    }

    /**
     * @param int $r44_selec
     */
    public function setCodigo($r44_selec)
    {
        $this->r44_selec = $r44_selec;
    }

    /**
     * @return string
     */
    public function getDescricao1()
    {
        return $this->r44_desc1;
    }

    /**
     * @param string $r44_desc1
     */
    public function setDescricao1($r44_desc1)
    {
        $this->r44_desc1 = $r44_desc1;
    }

    /**
     * @return string
     */
    public function getDescricao2()
    {
        return $this->r44_desc2;
    }

    /**
     * @param string $r44_desc2
     */
    public function setDescricao2($r44_desc2)
    {
        $this->r44_des21 = $r44_desc2;
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->r44_descr;
    }

    /**
     * @param string $r44_descr
     */
    public function setDescricao($r44_descr)
    {
        $this->r44_descr = $r44_descr;
    }

    /**
     * @return string
     */
    public function getObservacao()
    {
        return $this->r44_obs;
    }

    /**
     * @param int $r44_obs
     */
    public function setObservacao($r44_obs)
    {
        $this->r44_obs = $r44_obs;
    }

    /**
     * @return int
     */
    public function getCodigoInstituicao()
    {
        return $this->r44_instit;
    }

    /**
     * @param int $r44_instit
     */
    public function setCodigoInstituicao($r44_instit)
    {
        $this->r44_instit = $r44_instit;
    }

    /**
     * @return string
     */
    public function getWhere()
    {
        return $this->r44_where;
    }

    /**
     * @param string $r44_where
     */
    public function setWhere($r44_where)
    {
        $this->r44_where = $r44_where;
    }

    /**
     * @return int
     */
    public function getGrupoSelecao()
    {
        return $this->r44_gruposelecao;
    }

    /**
     * @param int $r44_gruposelecao
     */
    public function setGrupoSelecao($r44_gruposelecao)
    {
        $this->r44_gruposelecao = $r44_gruposelecao;
    }
}
