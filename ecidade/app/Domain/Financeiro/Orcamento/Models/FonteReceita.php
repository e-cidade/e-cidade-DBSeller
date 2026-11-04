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

namespace App\Domain\Financeiro\Orcamento\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class FonteReceita
 * @package App\Domain\Financeiro\Orcamento\Models
 * @property $o57_codfon
 * @property $o57_anousu
 * @property $o57_fonte
 * @property $o57_descr
 * @property $o57_finali
 * @property $nome
 * @property $natureza
 */
class FonteReceita extends Model
{
    protected $table = 'orcamento.orcfontes';

    public static function hasDesdobramento($codFon, $exercicio)
    {
        return collect(DB::select(
            "select 1 from orcamento.orcfontesdes where o60_codfon = {$codFon} and o60_anousu = {$exercicio}"
        ))->count() > 0;
    }

    protected $casts = [
        'o57_codfon' => 'integer',
        'o57_anousu' => 'integer',
        'o57_fonte' => 'string',
        'o57_descr' => 'string',
        'o57_finali' => 'string',
    ];

    /**
     * @return string
     */
    public function getNomeAttribute()
    {
        return $this->o57_descr;
    }

    /**
     * @return string
     */
    public function getNaturezaAttribute()
    {
        return $this->o57_fonte;
    }
}
