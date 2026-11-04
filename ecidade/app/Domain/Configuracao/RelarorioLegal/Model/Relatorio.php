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

namespace App\Domain\Configuracao\RelarorioLegal\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use \App\Domain\Financeiro\Contabilidade\Models\Periodo;

/**
 * Class Relatorio
 * @package App\Domain\Configuracao\RelarorioLegal\Model
 * @property integer $o42_codparrel
 * @property string $o42_descrrel
 * @property integer $o42_orcparamrelgrupo
 * @property string $o42_notapadrao
 */
class Relatorio extends Model
{
    protected $table = 'orcamento.orcparamrel';
    protected $primaryKey = 'o42_codparrel';
    public $timestamps = false;
    public $incrementing = false;

    protected $appends = [
        'codigo',
        'descricao',
    ];

    public function getCodigoAttribute()
    {
        return $this->attributes['o42_codparrel'];
    }

    public function getDescricaoAttribute()
    {
        return $this->attributes['o42_descrrel'];
    }

    public function periodos()
    {
        return $this->belongsToMany(
            Periodo::class,
            'configuracoes.orcparamrelperiodos',
            'o113_orcparamrel',
            'o113_periodo'
        )->withPivot('o113_sequencial');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeRelatoriosLrf(Builder $query)
    {
        return $query->where('o42_orcparamrelgrupo', '=', 1);
    }
}
