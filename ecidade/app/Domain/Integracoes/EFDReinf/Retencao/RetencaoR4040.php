<?php

namespace App\Domain\Integracoes\EFDReinf\Retencao;

use App\Domain\Financeiro\Empenho\Services\RetencaoNaturezaRendimentoService;
use BusinessException;
use DBDate;
use Exception;
use Illuminate\Support\Facades\DB;

class RetencaoR4040 extends Retencao
{
    public function getRetencoes($filters)
    {
        $query = DB::table('retencaoreceitas')
        ->select(
            "z01_nome as benef",
            "z01_cgccpf as cnpjbenef",
            "k12_data as fato_gerador",
            "e60_vlremp as valor_bruto",
            "e23_valorbase as valor_base",
            "e23_valorretencao as valor_retencao",
            "e23_sequencial as retencaoreceitas",
            "e21_retencaotipocalc as tipo_calculo",
            "e167_codigo as codnatureza_rendimento",
            "e167_sequencial as naturezarendimento"
        )
        ->distinct()
        ->selectRaw("e21_sequencial || ' - ' || e21_descricao as tipo_retencao")
        ->selectRaw("e60_codemp || '/' || e60_anousu as empenho")
        ->join('retencaopagordem', 'e20_sequencial', 'e23_retencaopagordem')
        ->join('pagordem', 'e50_codord', 'e20_pagordem')
        ->join('empempenho', 'e60_numemp', 'e50_numemp')
        ->join('cgm', 'z01_numcgm', 'e60_numcgm')
        ->join('orcdotacao', 'o58_coddot', DB::Raw('e60_coddot and e60_anousu = o58_anousu'))
        ->join('orcelemento', 'o56_codele', DB::Raw('o58_codele  and o58_anousu = o56_anousu'))
        ->join('retencaotiporec', 'e21_sequencial', 'e23_retencaotiporec')
        ->leftJoin('retencaoreceitassubcontratacao', 'e163_retencaoreceitas', 'e23_sequencial')
        ->leftJoin('retencaocorgrupocorrente', 'e47_retencaoreceita', 'e23_sequencial')
        ->leftJoin('corgrupocorrente', 'k105_sequencial', 'e47_corgrupocorrente')
        ->leftJoin('corrente', 'k105_id', DB::Raw('k12_id AND k105_autent = k12_autent AND k105_data = k12_data'))
        ->join('retencaonaturezarendimento', 'e168_retencaoreceitas', 'e23_sequencial')
        ->join('naturezarendimento', 'e167_sequencial', 'e168_naturezarendimento')
        ->where('e23_ativo', true)
        ->where('k12_estorn', false)
        ->whereIn('e21_retencaotipocalc', [1, 2, 8, 9, 10, 11])
        ->whereIn('e167_codigo', [12052, 19001, 19009])
        ->whereRaw("substring(o56_elemento, 3,1) <> '1'")
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
        $fields = "db_config.cgc as contribuinte,
        z01_numcgm as cgm,
        z01_nome as benef,
        z01_cgccpf as cnpjbenef,
        k12_data as fato_gerador,
        e70_vlrliq as valor_bruto,
        e23_valorbase as valor_base,
        e23_valorretencao as valor_retencao,
        e23_sequencial as retencaoreceitas,
        e21_retencaotipocalc as tipo_calculo,
        e167_codigo as codnatureza_rendimento,
        e167_sequencial as naturezarendimento,
        o41_cnpj as unidade";

        $where  = "e23_ativo is true
        and k12_estorn is false
        and e71_anulado is false
        and e21_retencaotipocalc in (1, 2, 8, 9, 10, 11)
        and e167_codigo::int in (12052, 19001, 19009)
        and substring(o56_elemento, 3, 1) <> '1'
        and e60_instit = $instit
        and extract(year from k12_data) = $ano
        and extract(month from k12_data) = $mes
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
                inner join cgm on z01_numcgm = e60_numcgm
                inner join db_config on e60_instit = db_config.codigo
                inner join orcdotacao on o58_coddot = e60_coddot and e60_anousu = o58_anousu
                inner join orcelemento on o56_codele = o58_codele and o58_anousu = o56_anousu
                inner join orcunidade on o41_orgao = o58_orgao and
                    o41_unidade = o58_unidade and o41_anousu = o58_anousu
                inner join retencaotiporec on e21_sequencial = e23_retencaotiporec
                inner join retencaonaturezarendimento on e168_retencaoreceitas = e23_sequencial
                inner join naturezarendimento on e167_sequencial = e168_naturezarendimento
                left join retencaoreceitassubcontratacao on e163_retencaoreceitas = e23_sequencial
                left join retencaocorgrupocorrente on e47_retencaoreceita = e23_sequencial
                left join corgrupocorrente on k105_sequencial = e47_corgrupocorrente
                left join corrente on k105_id = k12_id and k105_autent = k12_autent and k105_data = k12_data
                where {$where}
        ";

        $rs = \db_query($sql);

        if (!$rs) {
            $error = pg_last_error();
            throw new Exception("Erro ao buscar retencoes do R-4040 {$error}");
        }

        return \db_utils::getCollectionByRecord($rs);
    }
}
