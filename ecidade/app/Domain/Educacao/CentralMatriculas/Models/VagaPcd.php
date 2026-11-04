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

namespace App\Domain\Educacao\CentralMatriculas\Models;

use Carbon\Carbon;
use ECidade\Educacao\MatriculaOnline\Model\Escolas;
use ECidade\Educacao\MatriculaOnline\Model\Fase;
use Etapa;
use Illuminate\Database\Eloquent\Model;
use Turno;

/**
 * @package App\Domain\Educacao\CentralMatriculas\Models
 * @property integer $mo64_codigo
 * @property integer $mo64_fase
 * @property integer $mo64_escola
 * @property integer $mo64_etapa
 * @property integer $mo64_turno
 * @property integer $mo64_vagas
 * @property Escola $escola
 */
class VagaPcd extends Model
{
    protected $table = "plugins.vagaspcd";
    protected $primaryKey = 'mo64_codigo';

    protected $fillable = [
        'mo64_fase',
        'mo64_escola',
        'mo64_etapa',
        'mo64_turno',
        'mo64_vagas'
    ];

    public $timestamps = false;

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'mo64_escola', 'mo53_codigo');
    }

    public function scopeFase($query, Fase $fase)
    {
        return $query->where('mo64_fase', $fase->getCodigo());
    }

    public function scopeEscola($query, Escolas $escola)
    {
        return $query->where('mo64_escola', $escola->getCodigo());
    }

    public function scopeEtapa($query, Etapa $etapa)
    {
        return $query->where('mo64_etapa', $etapa->getCodigo());
    }

    public function scopeTurno($query, Turno $turno)
    {
        return $query->where('mo64_turno', $turno->getCodigoTurno());
    }
}
