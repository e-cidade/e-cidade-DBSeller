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

use Illuminate\Database\Eloquent\Model;

/**
 * Class Disciplina
 * @package App\Domain\Educacao\Escola\Models
 * @property integer $ed232_i_codigo
 * @property string $ed232_c_descr
 * @property string $ed232_c_abrev
 * @property integer $ed232_areaconhecimento
 * @property string $ed232_c_descrcompleta
 * @method
 */
class Disciplina extends Model
{
    protected $table = 'escola.caddisciplina';
    protected $primaryKey = 'ed232_i_codigo';
    public $timestamps = false;
    public $incrementing = false;

    protected $casts = [
        'ed232_i_codigo' => 'integer',
        'ed232_c_descr' => 'string',
        'ed232_c_abrev' => 'string',
        'ed232_areaconhecimento' => 'integer',
        'ed232_c_descrcompleta' => 'string',
    ];

    /**
     * @var array
     */
    protected $storage = [];

    /**
     * @return integer
     */
    public function getCodigo()
    {
        return $this->getAttribute('ed232_i_codigo');
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->getAttribute('ed232_c_descr');
    }

    /**
     * @return string
     */
    public function getDescricaoCompleta()
    {
        return $this->getAttribute('ed232_c_descrcompleta');
    }

    /**
     * @return string
     */
    public function getAbreviatura()
    {
        return $this->getAttribute('ed232_c_abrev');
    }

    public function areaConhecimento()
    {
        return $this->belongsTo(AreaConhecimento::class, 'ed232_areaconhecimento', 'ed293_sequencial');
    }

    public function disciplnasEquivalentesBNCC()
    {
        return $this->hasMany(DisciplinaEquivalenteBNCC::class, 'ed153_caddisciplina', 'ed232_i_codigo');
    }
}
