<?php

namespace app\domain\Financeiro\Empenho\Relatorios\RetencoesEfdReinf;

use App\Domain\Financeiro\Empenho\Relatorios\RetencoesEfdReinf\RelatorioRetencoesEfdReinfFiltros;
use ECidade\Pdf\Pdf;

class RelatorioRetencoesEfdReinfPdf extends Pdf
{

    /**
     * @var RetencoesEfdReinfFiltros $filtros
     */
    protected $filtros;
    protected $dados;


    public function __construct($orientation = 'L')
    {
        parent::__construct($orientation);
    }

    /**
     * Cabeçalho do relatório
     */
    public function headers()
    {
        $this->addTitulo("Relatório de Retenções da Efd Reinf");
        $this->addTitulo("Data  : " . $this->dados['headerData']);
        $this->addTitulo("Quebra: " . $this->dados['headerQuebra']);
        $this->addTitulo("Evento: " . $this->deParaEvento($this->filtros->filtroEvento));
        $this->addTitulo($this->dados['headerOrgaoUnidade']);

        $this->init(false);
    }
    /**
     * Unidade lógica de processamento
     */


    public function emitir()
    {
        $this->imprimir();

        $fileName = 'tmp/RetencoesEfdReinf' . time() . '.pdf';
        $this->output('F', $fileName);

        return [
            "name" => "Relatório Retenções EFD-Reinf PDF",
            "path" => $fileName,
            'pathExterno' => ECIDADE_REQUEST_PATH . $fileName
        ];
    }

    public function imprimir()
    {
        $sFonte = "Arial";
        $lEscreverHeader = true;
        $nTotalRetencoes = 0;
        $vlrTotalNl = 0;
        $vlrBaseCalc = 0;
        $iTamCell = 0;
        $iTamFonte = 6;
        $textoQuebra = '';

        $this->addPage();
        $aRetencoes = $this->dados['retencoes'];

        $isEvento4000 = false;
        foreach ($aRetencoes as $oQuebra) {
            $this->setFont($sFonte, "b", $iTamFonte + 2);
            $lEscreverHeader = true;

            foreach ($oQuebra->itens as $oRetencaoAtiva) {
                $isEvento4000 = $this->isEventos4000($oRetencaoAtiva->evento);
                if ($this->Gety() > $this->getH() - 27 || $lEscreverHeader) {
                    if ($this->Gety() > $this->getH() - 27) {
                        $this->addPage();
                    }
                    if ($oQuebra->texto != "") {
                        $this->cell(0, 5, $oQuebra->texto, 0, 1);
                        if ($textoQuebra != $oQuebra->texto) {
                            $textoQuebra = $oQuebra->texto;
                            $vlrTotalNl = 0;
                            $vlrBaseCalc = 0;
                        }
                    }

                    $this->imprimeCabecalho($sFonte, $iTamCell, $iTamFonte, $oRetencaoAtiva);
                    $lEscreverHeader = false;
                }
                $vlrTotalNl += $oRetencaoAtiva->valor_nota_liq;
                $vlrBaseCalc += $oRetencaoAtiva->valor_base_calc;
                $this->imprimeDadosRelatorio($sFonte, $iTamCell, $iTamFonte, $oRetencaoAtiva);
            }

            $w = $isEvento4000 ? 203 : 203;
            $this->imprimeTotalizadorQuebra($sFonte, $iTamFonte, $oQuebra, $vlrTotalNl, $vlrBaseCalc, $w);
            $nTotalRetencoes += $oQuebra->total;
        }
        if (count($aRetencoes) > 0) {
            $this->imprimeTotalizadorGeral($sFonte, $iTamFonte, $nTotalRetencoes, $w);
        }
    }

    public function imprimeDadosRelatorio($sFonte, $iTamCell, $iTamFonte, $oRetencaoAtiva)
    {
        $yAtual = $this->getY();
        $this->SetFont($sFonte, "", $iTamFonte);
        $this->cell(10 + $iTamCell, 6, $oRetencaoAtiva->numero_nota, "T", 0, "R", 0);

        if ($this->isEventos4000($oRetencaoAtiva->evento)) {
            $this->cell(10 + $iTamCell, 6, $oRetencaoAtiva->codigo_nota, "T", 0, "R");
            $this->cell(17 + $iTamCell, 6, db_formatar($oRetencaoAtiva->data_pagamento, 'd'), "T", 0, "C");
        } else {
            $this->cell(17 + $iTamCell, 6, db_formatar($oRetencaoAtiva->data_emissao, 'd'), "T", 0, "C");
        }
        if ($this->isEventos2055($oRetencaoAtiva)) {
            $this->cell(53 + $iTamCell, 6, $oRetencaoAtiva->nome_prestador, "T", 0, "L");
        } else {
            $this->cell(58 + $iTamCell, 6, $oRetencaoAtiva->nome_prestador, "T", 0, "L");
        }
        $this->cell(24, 6, $this->formatCnpjCpf($oRetencaoAtiva->cnpj_prestador), "T", 0, "R");
        $this->cell(16, 6, $oRetencaoAtiva->empenho_numero, "T", 0, "R");
        $this->cell(10, 6, $oRetencaoAtiva->e50_codord, "T", 0, "R");
        $this->cell(18 + $iTamCell, 6, $oRetencaoAtiva->retencao_tipo, "T", 0, "C");

        $xAtual = $this->getX();
        if ($this->isEventos4000($oRetencaoAtiva->evento)) {
            $this->multicell(25 + $iTamCell, 6, $oRetencaoAtiva->codnatureza_rendimento, "T", "R", 0);
        } else {
            $this->multicell(25 + $iTamCell, 6, $oRetencaoAtiva->referencia_tipo_servico_desc, "T", "J", 0);
        }
        $yNovo = $this->getY();
        $this->setxy($xAtual + 25, $yAtual);
        if ($this->isEventos4000($oRetencaoAtiva->evento)) {
            $this->cell(15, 6, $oRetencaoAtiva->naturezarendimento, "T", 0, "R");
        } else {
            $this->cell(22, 6, $oRetencaoAtiva->indicativo_obra_cno, "T", 0, "C");
        }

        if ($this->isEventos2055($oRetencaoAtiva)) {
            $this->cell(15, 6, db_formatar($oRetencaoAtiva->valor_nota_liq, "f"), "T", 0, "R");
            $this->cell(18, 6, db_formatar($oRetencaoAtiva->valor_base_calc, "f"), "T", 0, "R");
            $this->cell(10, 6, $oRetencaoAtiva->valor_senar!= "" ? $oRetencaoAtiva->valor_senar  : "", "T", 0, "R");
            $this->cell(10, 6, $oRetencaoAtiva->giralt!= "" ? $oRetencaoAtiva->giralt  : "", "T", 0, "R");
            $this->cell(10, 6, $oRetencaoAtiva->valor_cp!= "" ? $oRetencaoAtiva->valor_cp  : "", "T", 0, "R");
            $this->cell(15, 6, db_formatar($oRetencaoAtiva->valor_retencao, "f"), "T", 1, "R");
        } else {
            $this->cell(20, 6, db_formatar($oRetencaoAtiva->valor_nota_liq, "f"), "T", 0, "R");
            $this->cell(20, 6, db_formatar($oRetencaoAtiva->valor_base_calc, "f"), "T", 0, "R");
            $this->cell(10, 6, $oRetencaoAtiva->aliquota != "" ? $oRetencaoAtiva->aliquota . "%" : "", "T", 0, "R");
            $this->cell(20, 6, db_formatar($oRetencaoAtiva->valor_retencao, "f"), "T", 1, "R");
        }

        $this->setY($yNovo);
    }

    public function imprimeCabecalho($sFonte, $iTamCell, $iTamFonte, $oRetencaoAtiva)
    {
        $this->setFont($sFonte, "b", $iTamFonte + 1);
        $this->cell(10 + $iTamCell, 5, "NF", 1, 0, "C", 1);

        if ($this->isEventos4000($oRetencaoAtiva->evento)) {
            $this->cell(10, 5, "N. L.", 1, 0, "C", 1);
            $this->cell(17, 5, "DT. PAGTO", 1, 0, "C", 1);
        } else {
            $this->cell(17, 5, "DATA DA NF", 1, 0, "C", 1);
        }

        if ($this->isEventos2055($oRetencaoAtiva)) {
            $this->cell(53 + $iTamCell, 5, "PRESTADOR DE SERVIÇO", 1, 0, "C", 1);
        } else {
            $this->cell(58 + $iTamCell, 5, "PRESTADOR DE SERVIÇO", 1, 0, "C", 1);
        }

        $this->cell(24 + $iTamCell, 5, "CNPJ/CPF", 1, 0, "C", 1);
        $this->cell(16, 5, "EMPENHO", 1, 0, "C", 1);
        $this->cell(10, 5, "OP", 1, 0, "C", 1);
        $this->cell(18 + $iTamCell, 5, "RETENÇÃO", 1, 0, "C", 1);
        if ($this->isEventos4000($oRetencaoAtiva->evento)) {
            $this->cell(25 + $iTamCell, 5, "CÓD NATUREZA", 1, 0, "C", 1);
        } else {
            $this->cell(25 + $iTamCell, 5, "TIPO DE SERVIÇO", 1, 0, "C", 1);
        }

        if ($this->isEventos4000($oRetencaoAtiva->evento)) {
            $this->cell(15, 5, "NAT. REND.", 1, 0, "C", 1);
        } else {
            $this->cell(22, 5, "CNO", 1, 0, "C", 1);
        }

        if ($this->isEventos2055($oRetencaoAtiva)) {
            $this->cell(15, 5, "VLR DA NL", 1, 0, "C", 1);
            $this->cell(18, 5, "BASE DE CALC", 1, 0, "C", 1);
            $this->cell(10, 5, "SENAR", 1, 0, "C", 1);
            $this->cell(10, 5, "GIRALT", 1, 0, "C", 1);
            $this->cell(10, 5, "CP", 1, 0, "C", 1);
            $this->cell(15, 5, "VLR RETIDO", 1, 1, "C", 1);
        } else {
            $this->cell(20, 6, db_formatar($oRetencaoAtiva->valor_nota_liq, "f"), "T", 0, "R");
            $this->cell(20, 6, db_formatar($oRetencaoAtiva->valor_base_calc, "f"), "T", 0, "R");
            $this->cell(10, 6, $oRetencaoAtiva->aliquota != "" ? $oRetencaoAtiva->aliquota . "%" : "", "T", 0, "R");
            $this->cell(20, 6, db_formatar($oRetencaoAtiva->valor_retencao, "f"), "T", 1, "R");
        }
    }

    public function imprimeTotalizadorQuebra($sFonte, $iTamFonte, $oQuebra, $vlrTotalNl, $vlrBaseCalc, $wTotal = 163)
    {
        $this->SetFont($sFonte, "b", $iTamFonte);

        $this->cell($wTotal, 5, "TOTALIZADORES:", "TBR", 0, "R", 1);
        $this->cell(20, 5, db_formatar($vlrTotalNl, "f"), "TBR", 0, "R", 1);
        $this->cell(20, 5, db_formatar($vlrBaseCalc, "f"), "TBR", 0, "R", 1);
        $this->cell(10, 5, '', "TB", 0, "C", 1);
        $this->cell(20, 5, db_formatar($oQuebra->total, "f"), "TBL", 1, "R", 1);
    }

    public function imprimeTotalizadorGeral($sFonte, $iTamFonte, $nTotalRetencoes, $w)
    {
        $this->SetFont($sFonte, "b", $iTamFonte);


        $this->cell($w + 50, 5, "Total Geral:", "TBR", 0, "R", 1);
        $this->cell(20, 5, db_formatar($nTotalRetencoes, "f"), "TBL", 1, "R", 1);
    }

    /**
     * Inicialização do relatorio com informações sobre filtros e dados de processamento;
     */
    public function setDados($dados)
    {
        $this->dados = $dados;
    }

    public function setFiltros(RelatorioRetencoesEfdReinfFiltros $filtros)
    {
        $this->filtros = $filtros;
    }

    public function isEventos4000($evento)
    {
        return in_array($evento, ['r4010', 'r4020', 'r4040']) ? true : false;
    }

    public function isEventos2055($oRetencaoAtiva)
    {
        if (isset($oRetencaoAtiva->valor_senar) && isset($oRetencaoAtiva->giralt) && isset($oRetencaoAtiva->valor_cp)) {
            return true;
        }

        return false;
    }

    private function formatCnpjCpf($value)
    {
        $CPF_LENGTH = 11;
        $cnpj_cpf = preg_replace("/\D/", '', $value);

        if (strlen($cnpj_cpf) === $CPF_LENGTH) {
            return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
        }

        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
    }

    private function deParaEvento($value)
    {
        switch ($value) {
            case 'r2010':
                return 'R-2010';
            case 'r2055':
                return 'R-2055';
            case 'r4010':
                return 'R-4010';
            case 'r4020':
                return 'R-4020';
            case 'todos1':
                return 'TODOS R-2010 e R-2055';
            case 'todos2':
                return 'TODOS R-4010 e R-4020';
            default:
                return '';
        }
    }
}
