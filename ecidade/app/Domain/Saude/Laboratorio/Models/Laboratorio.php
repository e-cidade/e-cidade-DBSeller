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

namespace App\Domain\Saude\Laboratorio\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $la02_i_codigo
 * @property int $la02_i_tipo
 * @property string $la02_c_descr
 * @property int $la02_i_alvara
 * @property int $la02_i_cnes
 * @property int $la02_c_endereco
 * @property int $la02_i_telefone
 * @property string $la02_c_numero
 * @property int $la02_i_turnoatend
 * @property bool $la02_interfaceado
 * @property Setor $laboratorioSetores
 */
class Laboratorio extends Model
{
    protected $table = 'lab_laboratorio';

    protected $primaryKey = 'la02_i_codigo';

    public $timestamps = false;

    public function laboratorioSetores()
    {
        return $this->belongsToMany(Setor::class, 'lab_labsetor', 'la24_i_laboratorio', 'la24_i_setor');
    }
}
