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

namespace App\Domain\Financeiro\Empenho\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int e91_anousu
 * @property int e91_numemp
 * @property float e91_vlremp
 * @property float e91_vlrliq
 * @property float e91_vlranu
 * @property float e91_vlrpag
 * @property char(20) e91_elemento
 * @property int e91_recurso
 * @property int e91_codtipo
 * @property bool e91_rpcorreto
 */
class EmpenhoResto extends Model
{
    protected $table = 'empenho.empresto';
    protected $primaryKey = 'e91_numemp';
    public $timestamps = false;
   
    public $fillable = [
        'e91_anousu',
        'e91_numemp',
        'e91_vlremp',
        'e91_vlranu',
        'e91_vlrliq',
        'e91_vlrpag',
        'e91_elemento',
        'e91_recurso',
        'e91_codtipo',
        'e91_rpcorreto'
    ];
}
