<?php

namespace App\Domain\Integracoes\EFDReinf\Retencao;

use App\Domain\Financeiro\Empenho\Services\RetencaoNaturezaRendimentoService;
use BusinessException;
use DBDate;
use Exception;
use Illuminate\Support\Facades\DB;

class RetencaoR4020 extends Retencao
{
    public function getRetencoes($filters)
    {
        $query = DB::table('retencaoreceitas')
        ->select(
            "z01_numcgm as cgm",
            "z01_nome as benef",
            "z01_cgccpf as cnpjbenef",
            "k12_data as fato_gerador",
            "e70_vlrliq as valor_bruto",
            "e23_valorbase as valor_base",
            "e23_valorretencao as valor_retencao",
            "e23_sequencial as retencaoreceitas",
            "e21_retencaotipocalc as tipo_calculo",
            "e167_codigo as codnatureza_rendimento",
            "e167_sequencial as naturezarendimento",
            "e69_codnota as nota_liquidacao",
            "e69_numero as nota_fiscal",
            "retencaoreceitas.e23_dtcalculo as data_calculo",
            "e50_codord as ordem_pagamento"
        )
        ->distinct()
        ->selectRaw("
            (select
                jsonb_agg(
                    jsonb_build_object(
                        'nome', cgm.z01_nome,
                        'retencao', retencaotiporec.e21_descricao,
                        'vlrbase', sub.e163_valorbase,
                        'vlrret', sub.e163_valor
                    )
                ) as subcontratado
                from empenho.retencaoreceitassubcontratacao sub
                inner join retencaotiporec on e21_sequencial = e163_retencaotiporec
                inner join cgm on z01_numcgm = e163_numcgm
                where sub.e163_retencaoreceitas = retencaoreceitas.e23_sequencial
            ) as subcontratados
        ")
        ->selectRaw("TO_CHAR(k12_data, 'DD/MM/YYYY') as data_pag")
        ->selectRaw("TO_CHAR(e69_dtnota, 'DD/MM/YYYY') as data_nota")
        ->selectRaw("e23_aliquota || '%' as aliquota")
        ->selectRaw("e21_sequencial || ' - ' || e21_descricao as tipo_retencao")
        ->selectRaw("e60_codemp || '/' || e60_anousu as empenho")
        ->join('retencaopagordem', 'e20_sequencial', 'e23_retencaopagordem')
        ->join('pagordem', 'e50_codord', 'e20_pagordem')
        ->join('pagordemnota', 'e50_codord', 'e71_codord')
        ->join('empnotaele', 'e70_codnota', 'e71_codnota')
        ->join('empnota', 'e69_codnota', 'e70_codnota')
        ->join('empempenho', 'e60_numemp', 'e50_numemp')
        ->join('cgm', 'z01_numcgm', 'e60_numcgm')
        ->join('orcdotacao', 'o58_coddot', DB::Raw('e60_coddot and e60_anousu = o58_anousu'))
        ->join('orcelemento', 'o56_codele', DB::Raw('o58_codele  and o58_anousu = o56_anousu'))
        ->join('retencaotiporec', 'e21_sequencial', 'e23_retencaotiporec')
        ->leftJoin('retencaoreceitassubcontratacao', 'e163_retencaoreceitas', 'e23_sequencial')
        ->leftJoin('retencaocorgrupocorrente', 'e47_retencaoreceita', 'e23_sequencial')
        ->leftJoin('corgrupocorrente', 'k105_sequencial', 'e47_corgrupocorrente')
        ->leftJoin('corrente', 'k105_id', DB::Raw('k12_id AND k105_autent = k12_autent AND k105_data = k12_data'))
        ->leftJoin('retencaonaturezarendimento', 'e168_retencaoreceitas', 'e23_sequencial')
        ->leftJoin('naturezarendimento', 'e167_sequencial', 'e168_naturezarendimento')
        ->where('e23_ativo', true)
        ->where('k12_estorn', false)
        ->whereIn('e21_retencaotipocalc', [2, 8, 9, 10, 11])
        ->where('e21_retencaotiporecgrupo', '<>', 2)
        ->whereRaw("substring(o56_elemento, 3,1) <> '1'")
        ->whereRaw("(e167_codigo not in ('12052', '19001', '19009') or e167_codigo is NULL)")
        ->where('e60_instit', session('DB_instit'));

        if ($filters) {
            if ($filters->cgm) {
                $query->where('z01_numcgm', '=', $filters->cgm);
            }

            if ($filters->periodo) {
                $dataIni = empty($filters->periodo[0]) ? false : DBDate::converter($filters->periodo[0]);
                $dataFim = empty($filters->periodo[1]) ? false : DBDate::converter($filters->periodo[1]);

                if ($dataIni && $dataFim) {
                    $query->whereBetween('k12_data', [$dataIni, $dataFim]);
                } elseif ($dataIni && $dataFim === false) {
                    $query->where('k12_data', '>=', $dataIni);
                } elseif ($dataFim && $dataIni === false) {
                    $query->where('k12_data', '<=', $dataFim);
                }
            }

            // Filtro de orgao unidade
            if (isset($filters->orgaoUnidade)) {
                $orgao   = $filters->orgao;
                $unidade = $filters->unidade;

                $autorizado = $this->checkOrgaoUnidadeUsuario($orgao);
                if (!$autorizado) {
                    throw new BusinessException('Orgão ou Unidade não autorizado para pesquisa.');
                }

                $query->selectRaw(
                    "concat(o40_orgao, ' - ', o40_descr, ' / ', o41_unidade, ' - ', o41_descr) as orgao_unidade"
                )
                ->join(
                    'orcunidade',
                    'o58_unidade',
                    DB::Raw('o41_unidade and o58_anousu = o41_anousu and o58_orgao = o41_orgao')
                )
                ->join(
                    'orcorgao',
                    'o41_orgao',
                    DB::Raw('o40_orgao and o41_anousu = o40_anousu')
                );

                if ($orgao) {
                    $query->where('o58_orgao', '=', $orgao);
                }

                if ($unidade) {
                    $query->where('o58_unidade', '=', $unidade);
                }
            }
        }

        return $query->orderBy('k12_data', 'desc')->get();
    }

    public function saveRetencao($data)
    {
        $this->saveNaturezaRendimento($data);
    }

    private function saveNaturezaRendimento($data)
    {
        $retenNatuRend = new RetencaoNaturezaRendimentoService;
        $retenNatuRend->setRetencaoreceitas($data->retencaoreceitas);
        $retenNatuRend->setNaturezarendimento($data->naturezarendimento);

        $retenNatuRend->save();
    }

    public function procesamento($instit, $ano, $mes, $filtroOrgaoUnidade = false, $unidadeCnpjBase = null)
    {
        $fields = "
        db_config.cgc as contribuinte,

        case when e163_sequencial is not null
            then sub.z01_numcgm
            else emp.z01_numcgm
        end as cgm,

        case when e163_sequencial is not null
            then sub.z01_nome
            else emp.z01_nome
        end as benef,

        case when e163_sequencial is not null
            then sub.z01_cgccpf
            else emp.z01_cgccpf
        end as cnpjbenef,

        case when e163_sequencial is not null
            then e163_valorbase
            else e70_vlrliq
        end as valor_bruto,

        case when e163_sequencial is not null
            then e163_valorbase
            else e23_valorbase
        end as valor_base,

        case when e163_sequencial is not null
            then e163_valor
            else e23_valorretencao
        end as valor_retencao,

        e23_sequencial as retencaoreceitas,
        k12_data as fato_gerador,
        e21_retencaotipocalc as tipo_calculo,
        e167_codigo as codnatureza_rendimento,
        e167_sequencial as naturezarendimento,
        o41_cnpj as unidade";

        $where  = "e23_ativo is true
        and k12_estorn is false
        and e71_anulado is false
        and e21_retencaotipocalc in (2, 8, 9, 10, 11)
        and e21_retencaotiporecgrupo <> 2
        and substring(o56_elemento, 3, 1) <> '1'
        and e167_codigo::int not in (12052, 19001, 19009)
        and e60_instit = $instit
        and extract(year from k12_data) = $ano
        and extract(month from k12_data) = $mes
        and (case when e163_sequencial is not null and sub.z01_numcgm is null then 0 else 1 end) = 1
        ";

        if ($filtroOrgaoUnidade) {
            $where .= " and substr(o41_cnpj, 1, 8) = '{$unidadeCnpjBase}'";
        }

        $sql = "select distinct {$fields} from
                retencaoreceitas
                inner join retencaopagordem on e20_sequencial = e23_retencaopagordem
                inner join pagordem on e50_codord = e20_pagordem
                inner join pagordemnota on e71_codord = e50_codord
                inner join empnotaele on e70_codnota = e71_codnota
                inner join empempenho on e60_numemp = e50_numemp
                inner join cgm as emp on z01_numcgm = e60_numcgm
                inner join db_config on e60_instit = db_config.codigo
                inner join orcdotacao on o58_coddot = e60_coddot and e60_anousu = o58_anousu
                inner join orcelemento on o56_codele = o58_codele and o58_anousu = o56_anousu
                inner join orcunidade on o41_orgao = o58_orgao and
                    o41_unidade = o58_unidade and o41_anousu = o58_anousu
                inner join retencaotiporec on e21_sequencial = e23_retencaotiporec
                inner join retencaonaturezarendimento on e168_retencaoreceitas = e23_sequencial
                inner join naturezarendimento on e167_sequencial = e168_naturezarendimento
                left join retencaoreceitassubcontratacao on e163_retencaoreceitas = e23_sequencial
                left join cgm as sub on sub.z01_numcgm = e163_numcgm and length(sub.z01_cgccpf) = 14
                left join retencaocorgrupocorrente on e47_retencaoreceita = e23_sequencial
                left join corgrupocorrente on k105_sequencial = e47_corgrupocorrente
                left join corrente on k105_id = k12_id and k105_autent = k12_autent and k105_data = k12_data
                where {$where}
        ";

        $rs = \db_query($sql);

        if (!$rs) {
            $error = pg_last_error();
            throw new Exception("Erro ao buscar retencoes do R-4020 {$error}");
        }

        return \db_utils::getCollectionByRecord($rs);
    }
}
