<?php
/**
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

namespace App\Domain\Educacao\CentralMatriculas\Models;

use App\Domain\Configuracao\Usuario\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Escolas
 * @package App\Domain\Educacao\CentralMatriculas\Models
 * @property integer $mo62_codigo
 * @property integer $mo62_base
 * @property string $mo62_observacao
 * @property integer $mo62_usuario
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property Usuario $usuario
 */
class ObservacaoInscricao extends Model
{
    use SoftDeletes;

    protected $table = "plugins.observacaoinscricao";
    protected $primaryKey = 'mo62_codigo';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $appends = ['data_criacao'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'mo62_usuario', 'id_usuario');
    }

    public function scopeCandidato($query, $codigoBase)
    {
        return $query->where('mo62_base', $codigoBase);
    }

    public function getDataCriacaoAttribute()
    {
        return $this->created_at->format('d/m/Y H\Hi');
    }
}
