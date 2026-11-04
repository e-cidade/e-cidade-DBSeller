<?php


namespace App\Domain\Financeiro\Contabilidade\Services;

use App\Domain\Financeiro\Contabilidade\Relatorios\ConferenciaPorRecursosCSV;
use App\Domain\Financeiro\Contabilidade\Relatorios\ConferenciaPorRecursosPDF;
use App\Domain\Financeiro\Contabilidade\Relatorios\DisponibilidadeRecursosPDF;
use cl_conlancam;
use cl_conplanoreduz;
use db_utils;
use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * Class DisponibilidadeRecursoService
 * @package App\Domain\Financeiro\Contabilidade\Services
 */
class DisponibilidadeRecursoService
{
    public function relatorioSaldoDisponibilidadeRecurso($filtros)
    {
        $aDadosRelatorio = array();
        $oDao = new cl_conplanoreduz;

        $sql = $oDao->sql_query_saldo_disponibilidade_recurso($filtros);
        $rs = $oDao->sql_record($sql);

        $totalSaldoAnterior = 0;
        $totalSalsoAtual = 0;

        for ($i = 0; $i < $oDao->numrows; $i++) {
            $oDadosConsulta = db_utils::fieldsMemory($rs, $i);

            $oDados = new stdClass();
            $oDados->id = $oDadosConsulta->id;
            $oDados->tipo = $oDadosConsulta->tipo;
            $oDados->saldo_anterior = db_formatar($oDadosConsulta->saldo_anterior, "f");
            $oDados->saldo_atual = db_formatar($oDadosConsulta->saldo_atual, "f");

            $aDadosRelatorio[] = $oDados;

            if ($i < 5) {
                $totalSaldoAnterior += $oDadosConsulta->saldo_anterior;
                $totalSalsoAtual += $oDadosConsulta->saldo_atual;
            }
        }

        $oTotais = new stdClass();
        $oTotais->total_saldo_atual = db_formatar($totalSalsoAtual, "f");
        $oTotais->diferenca_saldo_atual = db_formatar($totalSalsoAtual +
            db_utils::fieldsMemory($rs, 5)->saldo_atual, "f");

        $oTotais->total_saldo_anterior = db_formatar($totalSaldoAnterior, "f");
        $oTotais->diferenca_saldo_anterior = db_formatar($totalSaldoAnterior +
            db_utils::fieldsMemory($rs, 5)->saldo_anterior, "f");

        $aDadosRelatorio[] = $oTotais;

        $pdf = new DisponibilidadeRecursosPDF($filtros);
        return $pdf->emitir($aDadosRelatorio);
    }

    public function obterDadosConferenciaPorRecurso($filtros)
    {
        $aDadosRelatorio = array();

        $filtros->dataInicial = implode('-', array_reverse(explode('/', $filtros->dataInicial)));
        $filtros->dataFinal = implode('-', array_reverse(explode('/', $filtros->dataFinal)));

        $oDao = new cl_conlancam;
        $sql = $oDao->sql_conferenciaPorRecurso($filtros);
        $rs = $oDao->sql_record($sql);

        $valoresExtraOrcamentario = $this->valoresSaldoExtraOrcamentario($filtros);

        $total_saldo_ativo_at_f = 0;
        $total_saldo_extra_orcamentario = 0;
        $total_valor_a_liquidar = 0;
        $total_valor_a_pagar = 0;
        $total_valor_a_liquidar_rp = 0;
        $total_valor_a_pagar_rp = 0;
        $total_total = 0;
        $total_valor_disponibilidade = 0;
        $total_diferenca = 0;

        for ($i = 0; $i < $oDao->numrows; $i++) {
            $oDadosConsulta = db_utils::fieldsMemory($rs, $i);

            $oDados = new stdClass();
            $oDados->o15_codigo = $oDadosConsulta->o15_codigo;
            $oDados->gestao = $oDadosConsulta->gestao;
            $oDados->siconfi = $oDadosConsulta->codigo_siconfi;
            $oDados->subrecurso = $oDadosConsulta->subrecurso;
            $oDados->complemento = $oDadosConsulta->o15_complemento;
            $oDados->descricao = $oDadosConsulta->descricao;

            $valorExtra = array_filter($valoresExtraOrcamentario, function ($valorExtra) use ($oDadosConsulta) {
                return $oDadosConsulta->o15_codigo == $valorExtra->o15_codigo;
            });

            $saldo_extra_orcamentario = 0;
            if (!empty($valorExtra)) {
                $saldo_extra_orcamentario = array_shift($valorExtra)->valor;
            }

            $oDados->saldo_ativo_at_f = $oDadosConsulta->saldo_ativo_at_f;
            $oDados->saldo_extra_orcamentario = $saldo_extra_orcamentario;
            $oDados->valor_a_liquidar = $oDadosConsulta->valor_a_liquidar;
            $oDados->valor_a_pagar = $oDadosConsulta->valor_a_pagar;
            $oDados->valor_a_liquidar_rp = $oDadosConsulta->valor_a_liquidar_rp;
            $oDados->valor_a_pagar_rp = $oDadosConsulta->valor_a_pagar_rp;
            $total = $oDadosConsulta->total - $saldo_extra_orcamentario;
            $diferenca = round(($total - $oDadosConsulta->valor_disponibilidade), 2);
            $oDados->total = $total;
            $oDados->valor_disponibilidade = $oDadosConsulta->valor_disponibilidade;
            $oDados->diferenca = $diferenca;

            // totais
            $total_saldo_ativo_at_f += $oDadosConsulta->saldo_ativo_at_f;
            $total_saldo_extra_orcamentario += $saldo_extra_orcamentario;
            $total_valor_a_liquidar += $oDadosConsulta->valor_a_liquidar;
            $total_valor_a_pagar += $oDadosConsulta->valor_a_pagar;
            $total_valor_a_liquidar_rp += $oDadosConsulta->valor_a_liquidar_rp;
            $total_valor_a_pagar_rp += $oDadosConsulta->valor_a_pagar_rp;
            $total_total += $total;
            $total_valor_disponibilidade += $oDadosConsulta->valor_disponibilidade;
            $total_diferenca += $diferenca;

            $aDadosRelatorio[] = $oDados;
        }

        if ($filtros->modeloEmissao != 1) {
            $agruparSiconfi = [];
            foreach ($aDadosRelatorio as $dado) {
                if (empty($dado->siconfi)) {
                    continue;
                }
                $hash = $this->montaHash($filtros, $dado);
                if (!array_key_exists($hash, $agruparSiconfi)) {
                    $agruparSiconfi[$hash] = (object) [
                        "siconfi" => $dado->siconfi,
                        "saldo_ativo_at_f" => 0,
                        "saldo_extra_orcamentario" => 0,
                        "valor_a_liquidar" => 0,
                        "valor_a_pagar" => 0,
                        "valor_a_liquidar_rp" => 0,
                        "valor_a_pagar_rp" => 0,
                        "total" => 0,
                        "valor_disponibilidade" => 0,
                        "diferenca" => 0
                    ];

                    if ($filtros->modeloEmissao == 3) {
                        $agruparSiconfi[$hash]->complemento = $dado->complemento;
                    }
                }
                $agruparSiconfi[$hash]->saldo_ativo_at_f += $dado->saldo_ativo_at_f;
                $agruparSiconfi[$hash]->saldo_extra_orcamentario += $dado->saldo_extra_orcamentario;
                $agruparSiconfi[$hash]->valor_a_liquidar += $dado->valor_a_liquidar;
                $agruparSiconfi[$hash]->valor_a_pagar += $dado->valor_a_pagar;
                $agruparSiconfi[$hash]->valor_a_liquidar_rp += $dado->valor_a_liquidar_rp;
                $agruparSiconfi[$hash]->valor_a_pagar_rp += $dado->valor_a_pagar_rp;
                $agruparSiconfi[$hash]->total += $dado->total;
                $agruparSiconfi[$hash]->valor_disponibilidade += $dado->valor_disponibilidade;
                $agruparSiconfi[$hash]->diferenca += $dado->diferenca;
            }

            sort($agruparSiconfi);
            $aDadosRelatorio = $agruparSiconfi;
        }


        $oTotais = new stdClass();
        $oTotais->saldo_ativo_at_f = $total_saldo_ativo_at_f;
        $oTotais->saldo_extra_orcamentario = $total_saldo_extra_orcamentario;
        $oTotais->valor_a_liquidar = $total_valor_a_liquidar;
        $oTotais->valor_a_pagar = $total_valor_a_pagar;
        $oTotais->valor_a_liquidar_rp = $total_valor_a_liquidar_rp;
        $oTotais->valor_a_pagar_rp = $total_valor_a_pagar_rp;
        $oTotais->total = $total_total;
        $oTotais->valor_disponibilidade = $total_valor_disponibilidade;
        $oTotais->diferenca = $total_diferenca;

        $aDados['registros'] = $aDadosRelatorio;
        $aDados['totais'] = $oTotais;

        return $aDados;
    }

    /**
     * Filtra apenas os recursos que possuem diferença
     * @param $filtros
     * @return mixed
     */
    public function apenasDadosComDiferenca($filtros)
    {
        $dados = $this->obterDadosConferenciaPorRecurso($filtros);
        $registros = $dados['registros'];

        return array_filter($registros, function ($dado) {
            return $dado->diferenca != 0;
        });
    }

    public function relatorioConferenciaPorRecurso($filtros)
    {
        $info = $this->obterDadosConferenciaPorRecurso($filtros);
        $pdf = new ConferenciaPorRecursosPDF($filtros);
        $csv = new ConferenciaPorRecursosCSV($filtros);
        $csv->setDados($info["registros"]);
        $path= ['pdf' => $pdf->emitir($info),
            'csv' => $csv->emitir()];

        return $path;
    }

    private function valoresSaldoExtraOrcamentario($filtros)
    {
        return DB::select("
        with saldos as (
            SELECT
                   orctiporec.o15_codigo,
                   fc_planosaldonovo_array({$filtros->ano},
                     c61_reduz,
                     '{$filtros->dataInicial}',
                     '{$filtros->dataFinal}', FALSE),
                   p.c60_identificadorfinanceiro,
                   c60_consistemaconta
           FROM conplanoexe e
           INNER JOIN conplanoreduz r ON r.c61_anousu = c62_anousu
           AND r.c61_reduz = c62_reduz
           INNER JOIN conplano p ON r.c61_codcon = c60_codcon
           AND r.c61_anousu = c60_anousu
           join orctiporec on orctiporec.o15_codigo = r.c61_codigo
           join fonterecurso on orctiporec_id = orctiporec.o15_codigo and exercicio = c60_anousu
           LEFT OUTER JOIN consistema ON c60_codsis = c52_codsis
           WHERE c62_anousu = {$filtros->ano}
             AND c61_instit IN ($filtros->instituicoes)
             AND c60_identificadorfinanceiro = 'F'
             AND (p.c60_estrut LIKE '2188%' or p.c60_estrut LIKE '2189%')
        ), saldos_por_recurso as (
          select o15_codigo,
                 case when sinal_final = 'D' then saldo_final *-1 else saldo_final end as saldo_final,
                 sinal_final
            from (
            select o15_codigo,
                    round(fc_planosaldonovo_array[4]::float8, 2)::float8 as saldo_final,
                    fc_planosaldonovo_array[6]::varchar(1) AS sinal_final
               from saldos
           ) as x
        ), agrupa_por_recurso as (
          select o15_codigo,
                 sum(saldo_final) as valor
            from saldos_por_recurso
          group by o15_codigo
        ) select * from agrupa_por_recurso
        ");
    }

    private function montaHash($filtros, $dado)
    {
        if ($filtros->modeloEmissao == 2) {
            return $dado->siconfi;
        }

        return "{$dado->siconfi}#{$dado->complemento}";
    }
}
