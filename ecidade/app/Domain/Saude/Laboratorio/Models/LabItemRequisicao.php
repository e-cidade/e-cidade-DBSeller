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
 * @property integer la21_i_codigo
 * @property integer la21_i_requisicao
 * @property integer la21_d_entrega
 * @property integer la21_d_data
 * @property integer la21_c_hora
 * @property integer la21_i_setorexame
 * @property integer la21_i_emergencia
 * @property integer la21_c_situacao
 * @property integer la21_i_quantidade
 * @property integer la21_observacao
 * @property integer la21_motivonovacoleta
 * @property integer la21_motivosrejeicaoamostra
 * @property text la21_outromotivorejeicao
 *
 */
class LabItemRequisicao extends Model
{
    public $timestamps = false;
    protected $table = 'laboratorio.lab_requiitem';
    protected $primaryKey = 'la21_i_codigo';
}
