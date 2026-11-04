<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace App\Domain\Educacao\Escola\Models;

use ECidade\Enum\Educacao\Escola\TipoEnsinoEnum;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Ensino
 * @package App\Domain\Educacao\Escola\Models
 * @property integer $ed10_i_codigo
 * @property integer $ed10_i_tipoensino
 * @property integer $ed10_i_grauensino
 * @property string  $ed10_c_descr
 * @property string  $ed10_c_abrev
 * @property integer $ed10_mediacaodidaticopedagogica
 * @property integer $ed10_ordem
 * @property integer $ed10_tipo
 */
class Ensino extends Model
{
    protected $table = 'escola.ensino';
    protected $primaryKey = 'ed10_i_codigo';
    public $timestamps = false;
    public $incrementing = false;
    protected $appends = ['tipoEnsino', 'isInfantil'];

    /**
     * @return integer
     */
    public function getCodigo()
    {
        return $this->ed10_i_codigo;
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->ed10_c_descr;
    }

    /**
     * @return string
     */
    public function getAbreviatura()
    {
        return $this->ed10_c_abrev;
    }

    /**
     * @return integer
     */
    public function getOrdem()
    {
        return $this->ed10_ordem;
    }

    /**
     * @return integer
     */
    public function getTipoEnsino()
    {
        return $this->ed10_i_tipoensino;
    }

    /**
     * @return integer
     */
    public function getGrauEnsino()
    {
        return $this->ed10_i_grauensino;
    }

    /**
     * @return integer
     */
    public function getMediacaoDidaticoPedagogica()
    {
        return $this->ed10_mediacaodidaticopedagogica;
    }

    /**
     * @return integer
     */
    public function getTipo()
    {
        return $this->ed10_tipo;
    }

    /**
     * @param int $ed10_i_codigo
     */
    public function setCodigo($ed10_i_codigo)
    {
        $this->ed10_i_codigo = $ed10_i_codigo;
    }

    /**
     * @param int $ed10_i_tipoensino
     */
    public function setTipoEnsino($ed10_i_tipoensino)
    {
        $this->ed10_i_tipoensino = $ed10_i_tipoensino;
    }

    /**
     * @param int $ed10_i_grauensino
     */
    public function setGrauEnsino($ed10_i_grauensino)
    {
        $this->ed10_i_grauensino = $ed10_i_grauensino;
    }

    /**
     * @param string $ed10_c_descr
     */
    public function setDescricao($ed10_c_descr)
    {
        $this->ed10_c_descr = $ed10_c_descr;
    }

    /**
     * @param string $ed10_c_abrev
     */
    public function setAbreviatura($ed10_c_abrev)
    {
        $this->ed10_c_abrev = $ed10_c_abrev;
    }

    /**
     * @param int $ed10_mediacaodidaticopedagogica
     */
    public function setMediacaoDidaticoPedagogica($ed10_mediacaodidaticopedagogica)
    {
        $this->ed10_mediacaodidaticopedagogica = $ed10_mediacaodidaticopedagogica;
    }

    /**
     * @param int $ed10_ordem
     */
    public function setOrdem($ed10_ordem)
    {
        $this->ed10_ordem = $ed10_ordem;
    }

    /**
     * @param int $ed10_tipo
     */
    public function setEd10Tipo($ed10_tipo)
    {
        $this->ed10_tipo = $ed10_tipo;
    }

    public function getTipoEnsinoAttribute()
    {
        return new TipoEnsinoEnum($this->ed10_tipo);
    }

    public function getIsInfantilAttribute()
    {
        return $this->tipoEnsino->value() === 1;
    }

    public function disciplinas()
    {
        return $this->hasMany(DisciplinaEnsino::class, 'ed12_i_ensino', 'ed10_i_codigo')
            ->matrizCurricular();
    }

    public function etapas()
    {
        return $this->hasMany(Etapa::class, 'ed11_i_ensino', 'ed10_i_codigo');
    }
}
