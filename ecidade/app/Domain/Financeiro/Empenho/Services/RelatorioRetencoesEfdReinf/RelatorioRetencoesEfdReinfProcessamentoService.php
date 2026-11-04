<?php

namespace App\Domain\Financeiro\Empenho\Services\RelatorioRetencoesEfdReinf;

use App\Domain\Financeiro\Empenho\Services\RelatorioRetencoesEfdReinf\RelatorioRetencoesEfdReinfConsultasService;
use App\Domain\Financeiro\Empenho\Relatorios\RetencoesEfdReinf\RelatorioRetencoesEfdReinfFiltros;
use App\Domain\Financeiro\Orcamento\Models\FonteRecurso;
use Illuminate\Support\Facades\DB;

class RelatorioRetencoesEfdReinfProcessamentoService extends RelatorioRetencoesEfdReinfConsultasService
{
    public function processarFiltrosRetencoesEfdReinf(RelatorioRetencoesEfdReinfFiltros $filtros)
    {
        $sWhereSql = " and e60_instit = " . db_getsession("DB_instit");

        $columFilterDate = in_array($filtros->filtroEvento, ['r4010', 'r4020', 'r4040']) ? 'k12_data' : 'e69_dtnota';

        if ($filtros->dataInicial != "" && $filtros->dataFinal == "") {
            $dataInicial = implode("-", array_reverse(explode("/", $filtros->dataInicial)));
            $sWhereSql .= " and {$columFilterDate} = '{$dataInicial}'";
            $headerData = "{$filtros->dataInicial} a {$filtros->dataInicial}";
        } elseif ($filtros->dataFinal != "" && $filtros->dataInicial != "") {
            $dataInicial = implode("-", array_reverse(explode("/", $filtros->dataInicial)));
            $dataFinal = implode("-", array_reverse(explode("/", $filtros->dataFinal)));
            $sWhereSql .= " and {$columFilterDate} between '{$dataInicial}' and '{$dataFinal}'";
            $headerData = "{$filtros->dataInicial} a {$filtros->dataFinal}";
        }

        if (isset($filtros->filtroCredor) && !empty($filtros->filtroCredor)) {
            $sWhereSql .= " and e60_numcgm = " . $filtros->filtroCredor;
        }

        $headerOrgaoUnidade = "";
        if ($filtros->filtroOrgao != "") {
            $headerOrgaoUnidade = "Orgão: {$filtros->filtroOrgao}";
            $sWhereSql .= " and o58_orgao = {$filtros->filtroOrgao}";
        }

        if ($filtros->filtroUnidade != "") {
            $headerOrgaoUnidade = " Orgão/Unidade: {$filtros->filtroOrgao}/{$filtros->filtroUnidade}";
            $sWhereSql .= " and o58_unidade = {$filtros->filtroUnidade}";
        }

        $sCampoQuebrar = "";
        $sCampoQuebrarSql = "e50_codord";
        $sNomeQuebra = "";
        $headerQuebra = "Nenhum";

        if ($filtros->filtroAgrupaPor == 3) {
            //Quebra por credor
            $sCampoQuebrarSql = "identificador_prestador";
            $sCampoQuebrar = "identificador_prestador";
            $sNomeQuebra = "nome_prestador";
            $headerQuebra = "Credor";
        } elseif ($filtros->filtroAgrupaPor == 5) {
            //Quebra por unidade orcamentaria
            $sCampoQuebrarSql = "o41_orgao||'-'||o41_unidade as quebra";
            $sCampoQuebrar = "quebra";
            $sNomeQuebra = "quebra";
            $headerQuebra = "Unidade Orçamentária";
        } elseif ($filtros->filtroAgrupaPor == 6) {
            //Quebra por unidade orcamentaria e credor
            $sCampoQuebrarSql = "o41_orgao||'-'||o41_unidade||'-'||identificador_prestador as quebra";
            $sCampoQuebrar = "quebra";
            $sNomeQuebra = "o41_descr";
            $headerQuebra = "Credor";
        }

        return [
            "sWhereSql" => $sWhereSql,
            "sCampoQuebrarSql" => $sCampoQuebrarSql,
            "sCampoQuebrar" => $sCampoQuebrar,
            "sNomeQuebra" => $sNomeQuebra,
            "headerQuebra" => $headerQuebra,
            "headerData" => $headerData,
            "headerOrgaoUnidade" => $headerOrgaoUnidade
        ];
    }

    public function processarDadosRelatorio(RelatorioRetencoesEfdReinfFiltros $filtros)
    {
        $preProcessamento = $this->processarFiltrosRetencoesEfdReinf($filtros);
        $sqlRetencoesEfdReinf = "";
        if ($filtros->filtroEvento == "r2010") {
            $sqlRetencoesEfdReinf = $this->sqlRetencoesR2010(
                $preProcessamento['sCampoQuebrarSql'],
                $preProcessamento['sWhereSql'],
                ''
            );
        } elseif ($filtros->filtroEvento == "r2055") {
            $sqlRetencoesEfdReinf = $this->sqlRetencoesR2055(
                $preProcessamento['sCampoQuebrarSql'],
                $preProcessamento['sWhereSql'],
                ''
            );
        } elseif ($filtros->filtroEvento == "r4010") {
            $sqlRetencoesEfdReinf = $this->sqlRetencoesR4010(
                $preProcessamento['sCampoQuebrarSql'],
                $preProcessamento['sWhereSql'],
                'r4010'
            );
        } elseif ($filtros->filtroEvento == "r4020") {
            $sqlRetencoesEfdReinf = $this->sqlRetencoesR4020(
                $preProcessamento['sCampoQuebrarSql'],
                $preProcessamento['sWhereSql'],
                'r4020'
            );
        } elseif ($filtros->filtroEvento == "todos1") {
            $sqlRetencoesEfdReinf = $this->sqlTodasRetencoesEfdReinf(
                $preProcessamento['sCampoQuebrarSql'],
                $preProcessamento['sWhereSql'],
                ''
            );
        } elseif ($filtros->filtroEvento == "todos2") {
            $sqlRetencoesEfdReinf = $this->sqlTodasRetencoesEfdReinf2(
                $preProcessamento['sCampoQuebrarSql'],
                $preProcessamento['sWhereSql'],
                'r4010'
            );
        }
        $retencoesEfdReinf = DB::select($sqlRetencoesEfdReinf);

        $sValorCompararQuebra = "";
        $aRetencoes = [];

        $sCampoQuebrar = $preProcessamento['sCampoQuebrar'];
        $sNomeQuebra = $preProcessamento['sNomeQuebra'];

        foreach ($retencoesEfdReinf as $oRetencao) {
            if (strlen(trim($oRetencao->cnpj_prestador)) == 11) {
                $cCnpjCpf = db_formatar($oRetencao->cnpj_prestador, "cpf");
            } elseif (strlen(trim($oRetencao->cnpj_prestador)) == 14) {
                $cCnpjCpf = db_formatar($oRetencao->cnpj_prestador, "cnpj");
            } else {
                $cCnpjCpf = $oRetencao->cnpj_prestador;
            }

            if ($filtros->filtroAgrupaPor != 1) {
                if (empty($aRetencoes[$oRetencao->$sCampoQuebrar])) {
                    $aRetencoes[$oRetencao->$sCampoQuebrar] = new \stdClass();
                }

                if ($sValorCompararQuebra == $oRetencao->$sCampoQuebrar) {
                    $aRetencoes[$oRetencao->$sCampoQuebrar]->total += $oRetencao->valor_retencao;
                    $aRetencoes[$oRetencao->$sCampoQuebrar]->itens[] = $oRetencao;
                } else {
                    if ($filtros->filtroAgrupaPor == 3) {
                        $texto = $oRetencao->$sCampoQuebrar . " - " . $oRetencao->$sNomeQuebra;
                        $texto .= " - CPF/CNPJ: $cCnpjCpf -";
                        $aRetencoes[$oRetencao->$sCampoQuebrar]->texto = $texto;
                    } elseif ($filtros->filtroAgrupaPor == 5) {
                        $texto = $oRetencao->o41_orgao . "/" . str_pad($oRetencao->o41_unidade, 3, "0", STR_PAD_LEFT);
                        $texto .= " - " . str_pad($oRetencao->o41_unidade, 3, "0", STR_PAD_LEFT);
                        $texto .= $oRetencao->o41_descr;
                        $aRetencoes[$oRetencao->$sCampoQuebrar]->texto = $texto;
                    } elseif ($filtros->filtroAgrupaPor == 6) {
                        $texto = $oRetencao->o41_orgao . "/" . str_pad($oRetencao->o41_unidade, 3, "0", STR_PAD_LEFT);
                        $texto .= " - " . $oRetencao->o41_descr;
                        $texto .= " Credor: " . $oRetencao->identificador_prestador . " - "
                            . $oRetencao->nome_prestador;
                        $aRetencoes[$oRetencao->$sCampoQuebrar]->texto = $texto;
                    }

                    if (isset($aRetencoes[$oRetencao->$sCampoQuebrar]->total)) {
                        $aRetencoes[$oRetencao->$sCampoQuebrar]->total += $oRetencao->valor_retencao;
                    } else {
                        $aRetencoes[$oRetencao->$sCampoQuebrar]->total = $oRetencao->valor_retencao;
                    }
                    $aRetencoes[$oRetencao->$sCampoQuebrar]->itens[] = $oRetencao;
                }
                $sValorCompararQuebra = $oRetencao->$sCampoQuebrar;
            } else {
                if (empty($aRetencoes[0])) {
                    $aRetencoes[0] = new \stdClass();
                }
                $aRetencoes[0]->texto = "";
                if (isset($aRetencoes[0]->total)) {
                    $aRetencoes[0]->total += $oRetencao->valor_retencao;
                } else {
                    $aRetencoes[0]->total = $oRetencao->valor_retencao;
                }
                $aRetencoes[0]->itens[] = $oRetencao;
            }
        }

        return [
            "retencoes" => $aRetencoes,
            "headerQuebra" => $preProcessamento['headerQuebra'],
            "headerData" => $preProcessamento['headerData'],
            "headerOrgaoUnidade" => $preProcessamento['headerOrgaoUnidade']
        ];
    }
}
