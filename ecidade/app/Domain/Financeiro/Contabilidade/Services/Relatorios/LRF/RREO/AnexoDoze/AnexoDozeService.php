<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoDoze;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Contabilidade\Factories\TemplateFactory;
use App\Domain\Financeiro\Contabilidade\Relatorios\LRF\RREO\XlsAnexoDoze;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\AnexosService;
use PhpOffice\PhpWord\Settings;
use NcJoes\OfficeConverter\OfficeConverter;
use DBDate;
use Exception;

class AnexoDozeService extends AnexosService
{
    protected $sections = [
        'receita_1' => [1, 13],
        'despesa_1' => [14, 35],
        'despesa_2' => [36, 40],
        'manuais_1' => [46, 49],
        'rp_1' => [50, 54],
        'manuais_2' => [58, 61],
        'receita_2' => [62, 68],
        'despesa_3' => [69, 90],
        'despesa_4' => [91, 98],
    ];

    protected $totalizarSoma = [
        1 => [2, 3, 4, 5],
        6 => [7, 8, 9, 10, 11, 12],
        13 => [1, 6],
        14 => [15, 16],
        17 => [18, 19],
        20 => [21, 22],
        23 => [24, 25],
        26 => [27, 28],
        29 => [30, 31],
        32 => [33, 34],
        35 => [14, 17, 20, 23, 26, 29, 32],
        36 => [35],
    ];

    protected $totalizarSubtracao = [
        40 => [36, 37, 38, 39]
    ];

    protected $linhasNaoProcessar = [45, 46, 47, 48, 49, 55, 56, 57, 58, 59, 60, 61];

    protected $exerciciosAnteriores = [
        2022 => 51,
        2021 => 52,
        2020 => 53,
        2019 => 54,
    ];

    /**
     * As linhas a baixo devem ser calculadas o exercício atual e os ultmos quatro anos.
     * @var int[]
     */
    protected $linhasRecalcular = [
        1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23,
        24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 51, 52, 53, 54
    ];

    /**
     * @param $filtros
     * @throws Exception
     */
    public function __construct($filtros)
    {
        $this->exercicio = $filtros['DB_anousu'];

        $this->emissor = \InstituicaoRepository::getInstituicaoByCodigo($filtros['DB_instit']);
        $template = TemplateFactory::getTemplate($filtros['codigo_relatorio'], $filtros['periodo']);

        $this->constructAssinaturas($filtros['DB_instit']);
        if (isset($filtros["desvincularDadosInstituicaoCamara"]) && $filtros["desvincularDadosInstituicaoCamara"]) {
            $this->constructInstituicoes(DBConfig::all()->where('db21_tipoinstit', '<>', 2));
        } else {
            $this->constructInstituicoes(DBConfig::all());
        }
        $this->constructPeriodo($filtros['periodo']);
        $this->constructRelatorio($filtros['codigo_relatorio']);
        $this->processaEnteFederativo();
        $this->parser = new XlsAnexoDoze($template);
    }

    public function emitir()
    {
        $this->processar();

        $this->addParserVariaveisFixas();
        $filename = $this->parser->gerar();

        Settings::setTempDir('tmp/');
        $filePdf = basename($filename, ".xlsx").".pdf";
        $converter = new OfficeConverter($filename);
        $converter->convertTo($filePdf);

        return [
            'xls' => $filename,
            'xlsLinkExterno' => ECIDADE_REQUEST_PATH . $filename,
            'pdf' => Settings::getTempDir().$filePdf
        ];
    }

    public function addParserVariaveisFixas()
    {
        $mesesPeriodo = sprintf(
            '%s - %s',
            DBDate::getMesExtenso($this->periodo->getMesInicial()),
            DBDate::getMesExtenso($this->periodo->getMesFinal())
        );
        $this->parser->setEnteFederativo($this->enteFederativo);
        $this->parser->setEmissor($this->emissor);
        $this->parser->setPeriodo($this->periodo->getDescricao());
        $this->parser->setAnoReferencia($this->exercicio);
        $this->parser->setMesesPeriodo($mesesPeriodo);
        $this->parser->setNotaExplicativa($this->getNotaExplicativa());
        $this->parser->setNomePrefeito($this->assinatura->assinaturaPrefeito());
        $this->parser->setNomeContador($this->assinatura->assinaturaContador());
        $this->parser->setNomeOrdenador($this->assinatura->assinaturaSecretarioFazenda());

        foreach ($this->linhasOrganizadas as $section => $linhas) {
            $this->parser->addCollection($section, $linhas);
        }

        $this->addParserVariaveisValores();
    }

    /**
     * @return void
     */
    public function addParserVariaveisValores()
    {
        $this->parser->setVariavel('valor_xxi', $this->linhas[55]->valor);
        $this->parser->setVariavel('valor_xxii', $this->linhas[56]->valor);
        $this->parser->setVariavel('valor_xxiii', $this->linhas[57]->valor);
    }

    /**
     * @return object
     */
    public function simplificado()
    {
        $linhas = $this->processarSimplificado();

        $vXVI = $linhas[40]->liquidado_acumulado;
        if ($this->periodo->getCodigo() == 11) {
            $vXVI = $linhas[40]->empenhado_liquido_acumulado;
        }

        $percentualAplicado = 0;
        $linhas[13]->arrecadado_acumulado;
        if ($linhas[13]->arrecadado_acumulado > 0) {
            $percentualAplicado = ($vXVI / $linhas[13]->arrecadado_acumulado) * 100;
        }

        return (object)[
            'descricao' => 'Despesas com Ações e Serviços Públicos de Saúde executadas com recursos de impostos',
            'valor_apuracao' => $vXVI,
            'percentual_minimo' => '15 %',
            'percentual_aplicado' => $percentualAplicado
        ];
    }

    /**
     * Para apurar o simplificado, de acordo com as colunas mapeadas pelo Leandro, só precisamos calcular o 1º e 2º
     * qyadro. Que é a mesma lista de linhas que precisamos recalcular para os 5 exercícios.
     * @return array
     */
    protected function processarSimplificado()
    {
        return $this->calculaLinhasEspecificasPorExercicio(
            $this->buscarLinhasPorOrdem($this->linhasRecalcular),
            $this->exercicio
        );
    }

    protected function processar()
    {
        $this->processaLinhas($this->linhas);
        $this->criaProriedadesValor();
        $this->totalizarLinhas();

        $this->organizaLinhas();
    }

    /**
     * @return void
     */
    protected function totalizarLinhas()
    {
        $this->calcularSoma();
        $this->posTotalizar();
    }

    protected function posTotalizar()
    {
        $this->calcularSubtracao();

        $vXVI = $this->linhas[40]->liquidado_acumulado;
        if ($this->periodo->getCodigo() == 11) {
            $vXVI = $this->linhas[40]->empenhado_liquido_acumulado;
        }
        $this->linhas[50]->valor_aplicado_asps = $vXVI;
        $this->linhas[50]->total_pagamentos_rp = '';
        $this->linhas[50]->total_anulacoes = '';
        $o = $this->linhas[50]->valor_aplicado_asps - $this->linhas[50]->valor_para_aplicar_asps;
        $this->linhas[50]->total_aplicado_alem_do_limite = 0;
        if ($o < 0) {
            $this->linhas[50]->total_aplicado_alem_do_limite = $o;
        }
        $this->linhas[50]->total =
            $this->linhas[50]->total_aplicado_alem_do_limite + $this->linhas[50]->rpnp_inscrito_indevidamente;

        foreach ($this->exerciciosAnteriores as $exercicioAnterior => $ordemLinha) {
            $linhas = $this->calculaLinhasEspecificasPorExercicio(
                $this->buscarLinhasPorOrdem($this->linhasRecalcular),
                $exercicioAnterior
            );

            $vIII = $linhas[13]->arrecadado_acumulado;
            $vXVI = $linhas[40]->liquidado_acumulado;
            if ($this->periodo->getCodigo() == 11) {
                $vXVI = $linhas[40]->empenhado_liquido_acumulado;
            }

            // linha que vai ser apresentada no relatório
            $linha = $this->linhas[$ordemLinha];
            $linha->valor_para_aplicar_asps = $vIII * 0.15;
            $linha->valor_aplicado_asps = $vXVI;

            $o = $linha->valor_aplicado_asps - $linha->valor_para_aplicar_asps;
            $linha->total_aplicado_alem_do_limite = 0;
            if ($o < 0) {
                $linha->total_aplicado_alem_do_limite = $o;
            }

            $linha->inscricao_total_rp = $linhas[$ordemLinha]->inscricao_total_rp;
            $linha->rpnp_inscrito_indevidamente = $linhas[$ordemLinha]->rpnp_inscrito_indevidamente;
            $linha->total_pagamentos_rp = $linhas[$ordemLinha]->total_pagamentos_rp;
            $linha->total_rp_a_pagar = $linhas[$ordemLinha]->total_rp_a_pagar;
            $linha->total_anulacoes = $linhas[$ordemLinha]->total_anulacoes;
            $linha->total = ($linha->total_aplicado_alem_do_limite + $linha->rpnp_inscrito_indevidamente)
                - $linha->total_anulacoes;

            $colunasASPS = [
                "valor_para_aplicar_asps",
                "valor_aplicado_asps"
            ];
            foreach ($linha->colunas as $coluna) {
                if (in_array($coluna->coluna, $colunasASPS)) {
                    $linha->{$coluna->coluna} += $coluna->valorManual;
                }
            }

            unset($linhas);
        }

        $somaColunaV = 0;
        foreach ([50, 51, 52, 53, 54] as $ordem) {
            $valor = $this->linhas[$ordem]->total;
            if ($valor < 0) {
                $somaColunaV += $valor;
            }
        }

        $this->linhas[55]->valor = $somaColunaV;
        $this->linhas[57]->valor = $somaColunaV - $this->linhas[56]->valor;
    }

    /**
     * @param array $linhas
     * @param integer $exercicio
     * @return array
     */
    public function calculaLinhasEspecificasPorExercicio(array $linhas, $exercicio)
    {
        $this->processaLinhasPorExercicio($linhas, $exercicio);
        $this->criarProriedades($linhas);
        $this->totalizar($linhas);

        return $linhas;
    }

    protected function criarProriedades(array &$linhas)
    {
        foreach ($linhas as $linha) {
            foreach ($linha->colunas as $coluna) {
                $linha->{$coluna->coluna} = $coluna->valor;
            }
        }
    }

    private function totalizar(array &$linhas)
    {
        $this->somar($linhas);
        $this->subtrair($linhas);
    }

    /**
     * Aplica a soma das linhas...
     */
    protected function somar(array &$linhas)
    {
        foreach ($this->totalizarSoma as $linha => $somar) {
            $this->somaLinhaAnterior($linhas, $linha, $somar);
        }
    }

    /**
     * Realiza a soma dos valores presente nas linhas
     *
     * @param integer $ordemLinha Ordem da linha a ser somada
     * @param array $somar Ordens das linhas que tem que somar
     */
    protected function somaLinhaAnterior($linhas, $ordemLinha, array $somar)
    {
        $linhaTotalizar = $linhas[$ordemLinha];
        $colunas = $linhaTotalizar->colunas;

        foreach ($somar as $idLinhaSoma) {
            $linhaSomar = $linhas[$idLinhaSoma];

            foreach ($colunas as $dadoColuna) {
                $linhaTotalizar->{$dadoColuna->coluna} += $linhaSomar->{$dadoColuna->coluna};
            }
        }
    }

    protected function subtrair($linhas)
    {
        foreach ($this->totalizarSubtracao as $linha => $subtrair) {
            $this->subtraiLinhaAnterior($linhas, $linha, $subtrair);
        }
    }

    /**
     * @param array $linhas Linhas
     * @param integer $ordemLinha Ordem da linha a ser subitraída
     * @param array $subtrair Ordens das linhas que tem que subtrair
     */
    protected function subtraiLinhaAnterior($linhas, $ordemLinha, array $subtrair)
    {
        $linhaTotalizar = $linhas[$ordemLinha];
        $colunas = $linhaTotalizar->colunas;
        $ordem = array_shift($subtrair); // extrai a ordem da primeira coluna a ser subtraída
        foreach ($colunas as $dadoColuna) {
            // define o valor inicial da coluna para após aplicar a subtração das demais
            $linhaTotalizar->{$dadoColuna->coluna} = $linhas[$ordem]->{$dadoColuna->coluna};
            foreach ($subtrair as $ordemSubtrai) {
                $linhaTotalizar->{$dadoColuna->coluna} -= $linhas[$ordemSubtrai]->{$dadoColuna->coluna};
            }
        }
    }

    public function getLinhasProcessadas()
    {
        $this->processar();
        $this->simplificado();
        return $this->linhas;
    }
}
