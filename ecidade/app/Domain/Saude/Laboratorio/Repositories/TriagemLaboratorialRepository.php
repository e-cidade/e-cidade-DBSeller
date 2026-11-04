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

namespace App\Domain\Saude\Laboratorio\Repositories;

use Illuminate\Support\Facades\DB;
use App\Domain\Saude\Laboratorio\Models\LabRequisicao;
use App\Domain\Saude\Laboratorio\Models\LabItemRequisicao;
use App\Domain\Saude\Laboratorio\Models\MotivosRejeicaoAmostra;
use App\Domain\Saude\Laboratorio\Models\LabTriagemItemRequisicao;
use Illuminate\Support\Carbon;

class TriagemLaboratorialRepository
{
    public function buscarTriagens($filtros)
    {
        $campos = [
            'lab_materialcoleta.la15_i_codigo as codigo',
            'lab_laboratorio.la02_c_descr as laboratorio',
            'lab_materialcoleta.la15_c_descr as amostra',
            DB::raw("TO_CHAR(lab_coletaitem.la32_d_data, 'DD/MM/YYYY') as coleta"),
            'lab_coletaitem.la32_c_hora as hora',
            DB::raw("
                jsonb_agg(jsonb_build_object(
                    'codigo', lab_exame.la08_i_codigo, 
                    'descricao', lab_exame.la08_c_descr,
                    'itemRequisicao',lab_requiitem.la21_i_codigo
                )) AS exames                        
            ")
        ];

        $query = LabRequisicao::query();

        if (array_key_exists('setor', $filtros) && !empty($filtros['setor'])) {
            $query->Setor($filtros['setor']);
        }

        if (array_key_exists('exame', $filtros) && !empty($filtros['exame'])) {
            $query->Exame($filtros['exame']);
        }

        $query->select($campos)
        ->join('lab_requiitem', 'la21_i_requisicao', 'lab_requisicao.la22_i_codigo')
        ->join('lab_coletaitem', 'la32_i_requiitem', 'lab_requiitem.la21_i_codigo')
        ->join('lab_setorexame', 'la09_i_codigo', 'lab_requiitem.la21_i_setorexame')
        ->join('lab_labsetor', 'la24_i_codigo', 'lab_setorexame.la09_i_labsetor')
        ->join('lab_laboratorio', 'la02_i_codigo', 'lab_labsetor.la24_i_laboratorio')
        ->join('lab_exame', 'la08_i_codigo', 'lab_setorexame.la09_i_exame')
        ->join('lab_examematerial', 'la19_i_exame', 'lab_exame.la08_i_codigo')
        ->join('lab_materialcoleta', 'la15_i_codigo', 'lab_examematerial.la19_i_materialcoleta')
        ->where('la22_i_codigo', $filtros['codRequisicao'])
        ->where('la21_c_situacao', '30 - Coletado')
        ->groupBy([
            'la15_i_codigo',
            'la02_c_descr',
            'la15_c_descr',
            'la32_d_data',
            'la32_c_hora'
        ]);
        
        $results = $query->get();
        return $results;
    }

    public function buscarMotivosRejeicaoAmostra()
    {
        $campos = [
            'la73_sequencial as codigo',
            'la73_descricao as descricao'
        ];
        $query = MotivosRejeicaoAmostra::query();
        $query->select($campos);
        $results = $query->get();
        return $results;
    }

    public function alterarSituacaoItemAmostra($dados)
    {
        $item = LabItemRequisicao::find($dados['itemRequisicao']);
        if ($item) {
            $item->la21_c_situacao = $dados['situacao'];
            $item->la21_motivosrejeicaoamostra = $dados['motivoRejeicao'];
            $item->la21_outromotivorejeicao = $dados['outroMotivoRejeicao'];
            $item->save();
        }
    }

    public function cadastrarTriagem($dados)
    {
        $triagemItem = new LabTriagemItemRequisicao();

        $triagemItem->la74_usuario = $dados['idUsuario'];
        $triagemItem->la74_requiitem = $dados['itemRequisicao'];
        $triagemItem->created_at = now();
        $triagemItem->updated_at = now();
                
        $triagemItem->save();
    }

    public function getSetoresItemRequisicao($dados)
    {
        $campos = [
            'la21_i_codigo',
            'la23_i_codigo',
            'la23_c_descr'
        ];
        
        $query = LabItemRequisicao::query()
        ->select($campos)
        ->join('lab_setorexame', 'la09_i_codigo', 'la21_i_setorexame')
        ->join('lab_labsetor', 'la24_i_codigo', 'la09_i_labsetor')
        ->join('lab_setor', 'la23_i_codigo', 'la24_i_setor');
        
        if (isset($dados['setores']) && count($dados['setores']) > 0) {
            $query->whereIn('la23_i_codigo', $dados['setores']);
        }

        if (isset($dados['setores_nao_pertinentes']) &&  count($dados['setores_nao_pertinentes']) > 0) {
            $query->whereNotIn('la23_i_codigo', $dados['setores_nao_pertinentes']);
        }

        if (isset($dados['itensRequisicao']) && count($dados['itensRequisicao']) > 0) {
            $query->whereIn('la21_i_codigo', $dados['itensRequisicao']);
        }

        $result = $query->get();
        return $result;
    }

    public function buscarPendenciasTriagem($filtros)
    {
        $campos = [
            DB::raw("distinct lab_requisicao.la22_i_codigo as requisicao"),
            'lab_laboratorio.la02_c_descr as laboratorio',
            'lab_materialcoleta.la15_c_descr as amostra',
            'cgs_und.z01_v_nome as paciente',
            DB::raw("TO_CHAR(lab_coletaitem.la32_d_data, 'DD/MM/YYYY') as data_coleta"),
            DB::raw("
                case when length(COALESCE(la21_outromotivorejeicao, '')) > 0 then 
                    la21_outromotivorejeicao 
                else la73_descricao end as motivo_rejeicao_amostra            
            ")
        ];

        $query = LabRequisicao::query();

        $filtroDataInicial = array_key_exists('dataInicial', $filtros) && !empty($filtros['dataInicial']);
        $filtroDataFinal = array_key_exists('dataFinal', $filtros) && !empty($filtros['dataFinal']);
        
        if ($filtroDataInicial && $filtroDataFinal) {
            $dataInicio = Carbon::createFromFormat('d/m/Y', $filtros['dataInicial'])->startOfDay();
            $dataFim = Carbon::createFromFormat('d/m/Y', $filtros['dataFinal'])->endOfDay();
            $query->whereBetween('lab_triagemitem.created_at', [$dataInicio, $dataFim]);
        } elseif ($filtroDataInicial) {
            $query->where('lab_triagemitem.created_at', '>=', $filtros['dataInicial']);
        }
                
        if (array_key_exists('situacaoAmostra', $filtros) && !empty($filtros['situacaoAmostra'])) {
            $query->where('la21_motivosrejeicaoamostra', $filtros['situacaoAmostra']);
        }

        if (array_key_exists('laboratorios', $filtros) && !empty($filtros['laboratorios'])) {
            $query->whereIn('la02_i_codigo', explode(",", $filtros['laboratorios']));
        }

        $query->select($campos)
        ->join('cgs_und', 'la22_i_cgs', 'z01_i_cgsund')
        ->join('lab_requiitem', 'la21_i_requisicao', 'lab_requisicao.la22_i_codigo')
        ->join('motivosrejeicaoamostra', 'la73_sequencial', 'lab_requiitem.la21_motivosrejeicaoamostra')
        ->join('lab_coletaitem', 'la32_i_requiitem', 'lab_requiitem.la21_i_codigo')
        ->join('lab_triagemitem', 'la74_requiitem', 'lab_requiitem.la21_i_codigo')
        ->join('lab_setorexame', 'la09_i_codigo', 'lab_requiitem.la21_i_setorexame')
        ->join('lab_labsetor', 'la24_i_codigo', 'lab_setorexame.la09_i_labsetor')
        ->join('lab_laboratorio', 'la02_i_codigo', 'lab_labsetor.la24_i_laboratorio')
        ->join('lab_exame', 'la08_i_codigo', 'lab_setorexame.la09_i_exame')
        ->join('lab_examematerial', 'la19_i_exame', 'lab_exame.la08_i_codigo')
        ->join('lab_materialcoleta', 'la15_i_codigo', 'lab_examematerial.la19_i_materialcoleta')
        ->whereNotNull('la21_motivosrejeicaoamostra')
        ->orderBy('laboratorio', 'asc')
        ->orderBy('amostra', 'asc');
        $results = $query->get();
        return $results;
    }

    public function buscarRequisicoes($filtros)
    {
        $campos = [
            'lab_requisicao.la22_i_codigo as codigo_requisicao',
            'lab_laboratorio.la02_c_descr as laboratorio',
            'lab_requiitem.la21_d_data as data_requisicao',
            'cgs_und.z01_v_nome as nome_paciente',
            'cgs_und.z01_d_nasc as data_nascimento_paciente',
            DB::raw("
                jsonb_agg(jsonb_build_object(
                    'codigo', lab_exame.la08_i_codigo, 
                    'descricao', lab_exame.la08_c_descr,
                    'situacao',lab_requiitem.la21_c_situacao
                )) AS exames                    
            ")
        ];

        $query = LabRequisicao::query();

        $filtroDataInicial = array_key_exists('dataInicial', $filtros) && !empty($filtros['dataInicial']);
        $filtroDataFinal = array_key_exists('dataFinal', $filtros) && !empty($filtros['dataFinal']);
        
        if ($filtroDataInicial && $filtroDataFinal) {
            $query->whereBetween('la21_d_data', [$filtros['dataInicial'],$filtros['dataFinal']]);
        } elseif ($filtroDataInicial) {
            $query->where('la21_d_data', '>=', $filtros['dataInicial']);
        }

        if (array_key_exists('paciente', $filtros) && !empty($filtros['paciente'])) {
            $query->where('z01_v_nome', $filtros['paciente']);
        }

        if (array_key_exists('codigoRequisicao', $filtros) && !empty($filtros['codigoRequisicao'])) {
            $query->where('la22_i_codigo', $filtros['codigoRequisicao']);
        }
        
        $query->select($campos)
        ->join('cgs_und', 'z01_i_cgsund', 'la22_i_cgs')
        ->join('lab_requiitem', 'la21_i_requisicao', 'lab_requisicao.la22_i_codigo')
        ->join('lab_setorexame', 'la09_i_codigo', 'lab_requiitem.la21_i_setorexame')
        ->join('lab_labsetor', 'la24_i_codigo', 'lab_setorexame.la09_i_labsetor')
        ->join('lab_laboratorio', 'la02_i_codigo', 'lab_labsetor.la24_i_laboratorio')
        ->join('lab_labdepart', 'la03_i_laboratorio', 'lab_laboratorio.la02_i_codigo')
        ->join('lab_exame', 'la08_i_codigo', 'lab_setorexame.la09_i_exame')
        ->where('la03_i_departamento', $filtros['departamentoUsuario'])
        ->groupBy([
            'la22_i_codigo',
            'la02_c_descr',
            'la21_d_data',
            'z01_v_nome',
            'z01_d_nasc'
        ]);
                        
        $results = $query->get();
        return $results;
    }
}
