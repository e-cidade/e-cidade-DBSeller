<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoQuatro;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Contabilidade\Factories\TemplateFactory;
use App\Domain\Financeiro\Contabilidade\Relatorios\LRF\RREO\XlsAnexoQuatro;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\AnexosService;
use PhpOffice\PhpWord\Settings;
use NcJoes\OfficeConverter\OfficeConverter;
use DBDate;

class AnexoQuatroService extends AnexosService
{
    /**
     * @var XlsAnexoQuatro
     */
    protected $parser;

    protected $sections = [
        'receita_1' => [1, 22],  // RECEITAS PREVIDENCIÁRIAS - RPPS (FUNDO EM CAPITALIZAÇÃO)
        'despesa_1' => [24, 29], // DESPESAS PREVIDENCIÁRIAS - RPPS (FUNDO EM CAPITALIZAÇÃO)
        'receita_2' => [32, 32], // RECURSOS RPPS ARRECADADOS EM EXERCÍCIOS ANTERIORES
        'despesa_2' => [33, 33], // RESERVA ORÇAMENTÁRIA DO RPPS
        'verificacao_1' => [34, 37], // APORTES DE RECURSOS PARA O FUNDO EM CAPITALIZAÇÃO DO RPPS
        'verificacao_2' => [38, 40], // BENS E DIREITOS DO RPPS (FUNDO EM CAPITALIZAÇÃO)
        'receita_3' => [41, 62], // BENS E DIREITOS DO RPPS (FUNDO EM CAPITALIZAÇÃO)
        'despesa_3' => [63, 69], // DESPESAS PREVIDENCIÁRIAS - RPPS (FUNDO EM REPARTIÇÃO)
        'verificacao_3' => [71, 72], // APORTES DE RECURSOS PARA O FUNDO EM REPARTIÇÃO DO RPPS
        'receita_4' => [73, 73], // RECEITAS DA ADMINISTRAÇÃO - RPPS
        'despesa_4' => [75, 79], // DESPESAS DA ADMINISTRAÇÃO - RPPS
        'despesa_5' => [81, 82], // DESPESAS PREVIDENCIÁRIAS (BENEFÍCIOS MANTIDOS PELO TESOURO)
        'despesa_6' => [84, 87], // DESPESAS PREVIDENCIÁRIAS (BENEFÍCIOS MANTIDOS PELO TESOURO)
    ];

    protected $linhasOrganizadas = [];

    protected $linhasNaoProcessar = [32];

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
        $this->parser = new XlsAnexoQuatro($template);
    }

    public function emitir()
    {
        $this->processar();

        foreach ($this->linhasOrganizadas as $section => $linhas) {
            $this->parser->addCollection($section, $linhas);
        }

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

    public function processar()
    {
        $this->processaLinhas($this->linhas);
        // processa a linha 32 que busca dados do exercício anterior
        $this->processaReceita($this->getBalanceteReceitaExercicioAnterior(), $this->linhas[32]);
        $this->criaProriedadesValor();
        $this->organizaLinhas();
    }

    /**
     * Retorna um array de objetos com os dados necessários para impressão do quadro simplificado
     * Para isso executa o relatório legal processando totalizando as linhas necessárias do quadro
     * @return array
     */
    public function processaLinhasSimplificado()
    {
        $this->linhasNaoProcessar = [32, 33, 34, 35, 36, 37, 38, 39, 71, 72, 73, 74, 77, 78, 81, 82, 84, 85, 86];
        $this->processaLinhas($this->linhas);
        $this->criaProriedadesValor();

        // realiza o calculo dos totalizadores das linhas
        $this->calculaLinha23Simplificado();
        $this->calculaLinha30Simplificado();
        $this->calculaLinha31Simplificado();
        $this->calculaLinha62Simplificado();
        $this->calculaLinha69Simplificado();
        $this->calculaLinha70Simplificado();

        return $this->linhasSimplificado();
    }

    protected function calculaLinha23Simplificado()
    {
        $linha23 = $this->linhas[23];
        $somar = [3, 4, 5, 7, 8, 9, 11, 12, 13, 14, 16, 18];
        foreach ($somar as $linha) {
            $linha23->arrecadado_acumulado += $this->linhas[$linha]->arrecadado_acumulado;
        }
    }

    protected function calculaLinha30Simplificado()
    {
        $linha30 = $this->linhas[30];
        $somar = [25, 26, 28, 29];
        foreach ($somar as $linha) {
            $linha30->empenhado_liquido_acumulado += $this->linhas[$linha]->empenhado_liquido_acumulado;
            $linha30->liquidado_acumulado += $this->linhas[$linha]->liquidado_acumulado;
            $linha30->pago_acumulado += $this->linhas[$linha]->pago_acumulado;
        }
    }

    protected function calculaLinha31Simplificado()
    {
        $linha23 = $this->linhas[23];
        $linha30 = $this->linhas[30];
        $linha31 = $this->linhas[31];
        $linha31->empenhado_liquido_acumulado = $linha23->arrecadado_acumulado - $linha30->empenhado_liquido_acumulado;
        $linha31->liquidado_acumulado = $linha23->arrecadado_acumulado - $linha30->liquidado_acumulado;
    }

    protected function calculaLinha62Simplificado()
    {
        $linha62 = $this->linhas[62];

        $somar = [43, 44, 45, 47, 48, 49, 51, 52, 53, 54, 56, 57, 59, 60, 61];
        foreach ($somar as $linha) {
            $linha62->arrecadado_acumulado += $this->linhas[$linha]->arrecadado_acumulado;
        }
    }

    protected function calculaLinha69Simplificado()
    {
        $linha69 = $this->linhas[69];
        $somar = [64, 65, 67, 68];
        foreach ($somar as $linha) {
            $linha69->empenhado_liquido_acumulado += $this->linhas[$linha]->empenhado_liquido_acumulado;
            $linha69->liquidado_acumulado += $this->linhas[$linha]->liquidado_acumulado;
            $linha69->pago_acumulado += $this->linhas[$linha]->pago_acumulado;
        }
    }

    protected function calculaLinha70Simplificado()
    {
        $linha62 = $this->linhas[62];
        $linha69 = $this->linhas[69];
        $linha70 = $this->linhas[70];
        $linha70->empenhado_liquido_acumulado = $linha62->arrecadado_acumulado - $linha69->empenhado_liquido_acumulado;
        $linha70->liquidado_acumulado = $linha62->arrecadado_acumulado - $linha69->liquidado_acumulado;
    }

    protected function linhasSimplificado()
    {
        $linhas = [];
        // primeira linha é o cabecalho
        $linhas[] = $this->createObjetoSimplificado('Fundo em Capitalização (PLANO PREVIDENCIÁRIO)', '', 1, true);
        $valor = $this->linhas[23]->arrecadado_acumulado;
        $linhas[] = $this->createObjetoSimplificado('Receitas Previdenciárias Realizadas', $valor, 2);

        $valorEmpenhado = $this->linhas[30]->empenhado_liquido_acumulado;
        $valorLiquidado = $this->linhas[30]->liquidado_acumulado;
        $valorPago = $this->linhas[30]->pago_acumulado;

        $linhas[] = $this->createObjetoSimplificado('Despesas Previdenciárias Empenhadas', $valorEmpenhado, 2);
        $linhas[] = $this->createObjetoSimplificado('Despesas Previdenciárias Liquidadas', $valorLiquidado, 2);
        $linhas[] = $this->createObjetoSimplificado('Despesas Previdenciárias Pagas', $valorPago, 2);

        $valor = $this->linhas[31]->liquidado_acumulado;
        if ($this->isSextoBimestre()) {
            $valor = $this->linhas[31]->empenhado_liquido_acumulado;
        }
        $linhas[] = $this->createObjetoSimplificado('Resultado Previdenciário', $valor, 2);

        // cabeçalho do segundo quadro
        $linhas[] = $this->createObjetoSimplificado('Fundo em Repartição (PLANO Financeiro)', '', 1, true);
        $valor = $this->linhas[62]->arrecadado_acumulado;
        $linhas[] = $this->createObjetoSimplificado('Receitas Previdenciárias Realizadas', $valor, 2);

        $valorEmpenhado = $this->linhas[69]->empenhado_liquido_acumulado;
        $valorLiquidado = $this->linhas[69]->liquidado_acumulado;
        $valorPago = $this->linhas[69]->pago_acumulado;

        $linhas[] = $this->createObjetoSimplificado('Despesas Previdenciárias Empenhadas', $valorEmpenhado, 2);
        $linhas[] = $this->createObjetoSimplificado('Despesas Previdenciárias Liquidadas', $valorLiquidado, 2);
        $linhas[] = $this->createObjetoSimplificado('Despesas Previdenciárias Pagas', $valorPago, 2);

        $valor = $this->linhas[70]->liquidado_acumulado;
        if ($this->isSextoBimestre()) {
            $valor = $this->linhas[70]->empenhado_liquido_acumulado;
        }
        $linhas[] = $this->createObjetoSimplificado('Resultado Previdenciário', $valor, 2);
        return $linhas;
    }

    /**
     * Cria um objeto simplificado para mapear os valores do quadro simplificado
     * @param string$descricao
     * @param mixed $valor
     * @param int $nivel
     * @param boolean $totaliza
     * @return object
     */
    protected function createObjetoSimplificado($descricao, $valor, $nivel = 1, $totaliza = false)
    {
        return (object)[
            'descricao' => $descricao,
            'totalizar' => $totaliza,
            'nivel' => $nivel,
            'ate_bimestre' => $valor
        ];
    }
    
    public function getLinhasProcessadas()
    {
        $this->processar();
        return $this->linhas;
    }
}
