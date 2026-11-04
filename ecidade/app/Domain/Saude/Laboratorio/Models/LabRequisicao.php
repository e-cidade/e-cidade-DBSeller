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
use Illuminate\Database\Eloquent\Builder;

/**
 * @property integer la22_i_codigo
 * @property integer la22_i_departamento
 * @property integer la22_i_usuario
 * @property integer  la22_i_cgs
 * @property string la22_c_responsavel
 * @property date la22_d_data
 * @property string la22_c_hora
 * @property string la22_c_medico
 * @property date la22_d_dum
 * @property text la22_t_medicamento
 * @property text la22_t_diagnostico
 * @property text la22_t_observacao
 * @property integer la22_i_autoriza
 * @property string la22_c_contato
 * @property double la22_peso
 * @property integer la22_altura
 * @property integer la22_volumeamostra
 */
class LabRequisicao extends Model
{
    public $timestamps = false;
    protected $table = 'laboratorio.lab_requisicao';
    protected $primaryKey = 'la22_i_codigo';
    
    /**
     * @param Builder $query
     * @param integer|null $idRequisicao
     */
    public function scopeRequisicao(Builder $query, $idRequisicao = null)
    {
        if ($idRequisicao) {
            $query->where('la22_i_codigo', $idRequisicao);
        }
    }

    /**
     * @param Builder $query
     * @param integer|null $setor
     */
    public function scopeSetor(Builder $query, $setor = null)
    {
        if ($setor) {
            $query->where('la24_i_setor', $setor);
        }
    }
    
    /**
     * @param Builder $query
     * @param integer|null $exame
     */
    public function scopeExame(Builder $query, $exame = null)
    {
        if ($exame) {
            $query->where('la08_i_codigo', $exame);
        }
    }
}
