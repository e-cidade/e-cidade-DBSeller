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

use App\Domain\Educacao\Escola\Models\NecessidadeSubdivisao;
use Illuminate\Database\Eloquent\Model;

/**
 * Model NecessidadeSubdivisaoAluno
 *
 * @property int $ed187_sequencial
 * @property int|null $ed187_necessidadesubdivisao
 * @property int|null $ed187_aluno
 */
class NecessidadeSubdivisaoAluno extends Model
{
    protected $table      = 'escola.necessidadesubdivisaoaluno';
    protected $primaryKey = 'ed187_sequencial';

    public $timestamps = false;

    public $fillable = [
        'ed187_sequencial',
        'ed187_necessidadesubdivisao',
        'ed187_aluno'
    ];

    public function necessidadeSubdivisao()
    {
        return $this->hasMany(NecessidadeSubdivisao::class, 'ed185_sequencial', 'ed187_necessidadesubdivisao');
    }
}
