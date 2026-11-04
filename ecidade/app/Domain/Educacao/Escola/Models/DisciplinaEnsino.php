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

use App\Domain\Educacao\Secretaria\Models\BaseDisciplina;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DisciplinaEnsino
 * @package App\Domain\Educacao\Escola\Models
 * @property integer ed12_i_codigo
 * @property integer ed12_i_ensino
 * @property integer ed12_i_caddisciplina
 */
class DisciplinaEnsino extends Model
{
    protected $table = 'escola.disciplina';
    protected $primaryKey = 'ed12_i_codigo';
    public $timestamps = false;
    public $incrementing = false;

    protected $casts = [
        "ed12_i_codigo" => "integer",
        "ed12_i_ensino" => "integer",
        "ed12_i_caddisciplina" => "integer",
    ];

    /**
     * @var array
     */
    private $storage = [];

    /**
     * @return integer
     */
    public function getCodigo()
    {
        return $this->getAttribute('ed12_i_codigo');
    }

    /**
     * @return Ensino
     */
    public function getEnsino()
    {
        return $this->ensino;
    }

    /**
     * @return Disciplina
     */
    public function getDisciplina()
    {
        if (empty($this->storage['disciplina'])) {
            $this->storage['disciplina'] = $this->disciplina;
        }
        return $this->storage['disciplina'];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ensino()
    {
        return $this->belongsTo(Ensino::class, 'ed12_i_ensino', 'ed10_i_codigo');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function disciplina()
    {
        return $this->belongsTo(Disciplina::class, 'ed12_i_caddisciplina', 'ed232_i_codigo');
    }

    public function scopeMatrizCurricular($query)
    {
        return $query->where('ed12_matrizcurricular', true);
    }
    public function disciplinasBase()
    {
        return $this->hasMany(BaseDisciplina::class, 'ed34_i_disciplina', 'ed12_i_codigo');
    }
}
