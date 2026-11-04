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

namespace App\Domain\Configuracao\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @property int c30_sequencial
 * @property int c30_anoorigem
 * @property int c30_anodestino
 * @property int c30_usuario
 * @property date c30_data
 * @property char(5) c30_hora
 * @property int c30_situacao
 */
class DBVirada extends Model
{
    public $timestamps = false;

    protected $table = 'configuracoes.db_virada';
    protected $primaryKey = 'c30_sequencial';

    public $fillable = [
        'c30_sequencial',
        'c30_anoorigem',
        'c30_anodestino',
        'c30_usuario',
        'c30_data',
        'c30_hora',
        'c30_situacao'
    ];

    public static function getCodigoNewVirada()
    {
        $data = DB::select("select nextval('db_virada_c30_sequencial_seq') as codigo");

        return $data[0]->codigo;
    }
}
