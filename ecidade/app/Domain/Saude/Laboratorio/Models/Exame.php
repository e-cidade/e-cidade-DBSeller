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
 * @property int $la08_i_codigo
 * @property string $la08_c_sigla
 * @property string $la08_c_descr
 * @property int $la08_i_idademax
 * @property int $la08_i_idademin
 * @property int $la08_i_sexo
 * @property \DateTime $la08_d_inicio
 * @property \DateTime $la08_d_fim
 * @property int $la08_i_dias
 * @property string $la08_t_interferencia
 * @property int $la08_i_gerar
 * @property int $la08_i_undidadeini
 * @property int $la08_i_undidadefim
 * @property int $la08_i_ativo
 * @property string $la08_observacao
 * @property bool $la08_obriga_peso
 * @property bool $la08_obriga_altura
 * @property bool $la08_obriga_volumeamostra
 * @property bool $la08_exibehistoricoresultados
 */
class Exame extends Model
{
    protected $table = 'lab_exame';

    protected $primaryKey = 'la08_i_codigo';

    public $timestamps = false;
}
