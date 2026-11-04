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
use App\Domain\Educacao\Escola\Models\NecessidadeEspecial;
use App\Domain\Educacao\Escola\Models\NecessidadeSubdivisaoAluno;

/**
 * Model AlunoNecessidade
 *
 * @property int $ed214_i_codigo
 * @property int $ed214_i_aluno
 * @property int $ed214_i_necessidade
 * @property string|null $ed214_c_principal
 * @property int|null $ed214_i_apoio
 * @property string|null $ed214_d_data - Formato Y-d-m
 * @property int|null $ed214_i_tipo
 * @property int|null $ed214_i_escola
 * @property int|null $ed214_i_anexo_estorage
 */
class AlunoNecessidade extends Model
{
    protected $table      = 'escola.alunonecessidade';
    protected $primaryKey = 'ed214_i_codigo';

    public $timestamps = false;

    public $fillable = [
        'ed214_i_codigo',
        'ed214_i_aluno',
        'ed214_i_necessidade',
        'ed214_c_principal',
        'ed214_i_apoio',
        'ed214_d_data',
        'ed214_i_tipo',
        'ed214_i_escola',
        'ed214_i_anexo_estorage'
    ];

    public function necessidadeSubdivisaoAluno()
    {
        return $this->hasMany(NecessidadeSubdivisaoAluno::class, 'ed187_aluno', 'ed214_i_aluno');
    }

    public function necessidade()
    {
        return $this->hasOne(NecessidadeEspecial::class, 'ed48_i_codigo', 'ed214_i_necessidade');
    }
}
