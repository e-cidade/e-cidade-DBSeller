<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RGF\AnexoCinco;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Contabilidade\Factories\TemplateFactory;
use App\Domain\Financeiro\Contabilidade\Relatorios\LRF\RGF\XlsAnexoCinco;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\AnexosService;
use App\Domain\Financeiro\Orcamento\Models\Recurso;
use DBDate;
use ECidade\Configuracao\RelatorioLegal\Servico\ParseConfiguracaoXml;
use ECidade\Pdf\Pdf;
use Exception;
use stdClass;

/**
 * Esse relatório diferente dos demais anexos, cada linha tem origem de valores de balancetes diferentes
 * E cada coluna tem o mesmo mapeamento de conta, o que muda é Fonte de Recurso.
 * Exemplo:
 *  A 1ª coluna: DISPONIBILIDADE DE CAIXA BRUTA, todas as linhas somam as contas do estrutural 111 do Bal. Ver.
 *  O valor de cada linha muda, por que cada linha tem um recurso diferente.
 *  E assim todas as demais colunas
 *
 * Dito isso, foi implementado toda uma "regra nova" para buscar e totalizar os dados
 * - como o processamento da linha, calcula de balancetes diferentes cada coluna, foi mapeado qual coluna executa qual
 *      balancete na propriedade $colunasBalancete;
 * - como as linhas não têm mapeamento, foi implementado o mapeamento na propriedade $mapeamentoColunas
 * - RP e Balancete da Despesa todas as linhas são calculadas a conta 3, o que muda o valor é a fonte de recurso.
 */
class AnexoCincoService extends AnexosService
{
    /**
     * Mapeia a busca das contas conforme o mapeamento.
     * Como todas as contas da despesa (RP e Balancete da Despesa) são conta 3 (três) vou fazer fixo.
     * O index do array é a coluna
     * @var array
     */
    protected $mapeamentoColunas = [
        0 => ['contas' => [], 'padrao' => ['111']],
        1 => ['contas' => []],
        2 => ['contas' => []],
        3 => ['contas' => []],
        4 => ['contas' => [], 'padrao' => ['2188', '2288']],
        5 => ['contas' => [], 'padrao' => ['8535']],
        7 => ['contas' => []],
        8 => ['contas' => []]
    ];

    protected $colunasBalancete = [
        'verificacao' => [0, 4, 5],
        'rp' => [1, 3],
        'despesa' => [2, 7, 8],
    ];

    protected $colunasVerificacao = [
        'saldo_final_acumulado' => 'saldo_final_acumulado',
        'financeira' => 'saldo_final_acumulado',
        'insuficiencia_financeira' => 'saldo_final_acumulado',
    ];

    protected $linhasNaoProcessar = [1, 4, 18];

    /**
     * Mapeado as propriedades usadas nesse relatório
     * @var string[]
     */
    protected $mapaPropriedadeColunas = [
        0 => 'saldo_final_acumulado',
        1 => 'saldo_liquidados',
        2 => 'a_pagar_liquidado',
        3 => 'saldo_a_liquidar',
        4 => 'financeira',
        5 => 'insuficiencia_financeira',
        6 => 'disp_caixa_liquida',
        7 => 'a_liquidar',
        8 => 'anulado_acumulado',
        9 => 'disp_caixa'
    ];

    protected $totalizarSoma = [
        1 => [2, 3],
        4 => [5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17],
        18 => [1, 4]
    ];

    protected $sections = ['demonstrativo' => [1, 18]];

    protected $recursosNaoConfigurados = [];

    /**
     * Ordem da linha com os dados simplificados
     * @var integer
     */
    protected $linhaSimplificado = 18;

    /**
     * @param $filtros
     * @throws Exception
     */
    public function __construct($filtros)
    {
        $this->instituicaoSessao = $filtros['DB_instit'];
        $this->exercicio = $filtros['DB_anousu'];
        $this->emissor = \InstituicaoRepository::getInstituicaoByCodigo($filtros['DB_instit']);
        $this->constructAssinaturas($filtros['DB_instit']);
        $this->constructInstituicoes(DBConfig::whereIn('codigo', $filtros['instituicoes'])->get());
        $this->constructPeriodo($filtros['periodo']);

        $this->constructRelatorio($filtros['codigo_relatorio']);
        $this->processaEnteFederativo();

        $this->dataIncio = new \DBDate("$this->exercicio-01-01");

        $template = TemplateFactory::getTemplate(
            $filtros['codigo_relatorio'],
            $filtros['periodo']
        );

        $this->parser = new XlsAnexoCinco($template);
    }

    public function emitir()
    {
        $this->processar();

        $mesesPeriodo = sprintf(
            '%s - %s',
            DBDate::getMesExtenso($this->periodo->getMesInicial()),
            DBDate::getMesExtenso($this->periodo->getMesFinal())
        );
        $this->parser->setEnteFederativo($this->enteFederativo);
        $this->parser->setEmissor($this->emissor);
        $this->parser->setPeriodo($this->periodo->getDescricao());
        $this->parser->setAnoReferencia($this->exercicio);
        $this->parser->setExercicioAnterior($this->exercicio - 1);
        $this->parser->setMesesPeriodo($mesesPeriodo);
        $this->parser->setNotaExplicativa($this->getNotaExplicativa());
        $this->parser->setNomePrefeito($this->assinatura->assinaturaPrefeito());
        $this->parser->setNomeContador($this->assinatura->assinaturaContador());
        $this->parser->setNomeOrdenador($this->assinatura->assinaturaSecretarioFazenda());

        foreach ($this->linhasOrganizadas as $section => $linhas) {
            $this->parser->addCollection($section, $linhas);
        }

        $filename = $this->parser->gerar();
        $pdfRecursos = [];
        if (!empty($this->recursosNaoConfigurados)) {
            $pdfRecursos = $this->relatorioRecursosNaoConfigurados();
        }

        return array_merge(
            ['xls' => $filename, 'xlsLinkExterno' => ECIDADE_REQUEST_PATH . $filename],
            $pdfRecursos
        );
    }

    protected function processar()
    {
        $this->executarBalancetes();
        $this->recursosNaoConfiguradosComExecucao();
        $this->buscaContasColunas();
        $this->processaLinhas($this->linhas);
        $this->criaProriedadesValor();
        $this->totalizar();
        $this->organizaLinhas();
    }

    /**
     * busca os estruturais das contas analíticas da configuração da linha e coluna e mapeia na propriedade
     * $mapeamentoColunas
     */
    protected function buscaContasColunas()
    {
        $this->buscarContasBalanceteVerificacao();
        $this->buscarContasDespesa();
    }

    /**
     * Busca os estruturais do balancete de verificação por coluna
     */
    protected function buscarContasBalanceteVerificacao()
    {
        $fake = (object)['origem' => self::ORIGEM_VERIFICACAO];
        $parser = new ParseConfiguracaoXml($fake, $this->exercicio);
        foreach ($this->colunasBalancete['verificacao'] as $coluna) {
            $contas = $this->mapeamentoColunas[$coluna]['padrao'];
            foreach ($contas as $conta) {
                $this->mapeamentoColunas[$coluna]['contas'] = array_merge(
                    $this->mapeamentoColunas[$coluna]['contas'],
                    $parser->buscarContas($conta)
                );
            }
        }
    }

    /**
     * Busca os estruturais do balancete de despesa por coluna
     */
    protected function buscarContasDespesa()
    {
        $fake = (object)['origem' => self::ORIGEM_DESPESA];
        $parser = new ParseConfiguracaoXml($fake, $this->exercicio);
        $contasDespesa = $parser->buscarContas(3);
        foreach ($this->colunasBalancete['rp'] as $coluna) {
            $this->mapeamentoColunas[$coluna]['contas'] = $contasDespesa;
        }

        foreach ($this->colunasBalancete['despesa'] as $coluna) {
            $this->mapeamentoColunas[$coluna]['contas'] = $contasDespesa;
        }
    }

    /**
     * Executa os balancetes necessários para o processamento do relatório
     */
    protected function executarBalancetes()
    {
        $this->getDadosVerificacao();
        $this->getDadosDespesa();
        $this->getDadosRestosPagar();
    }

    protected function processaLinhas(&$linhas)
    {
        foreach ($linhas as $linha) {
            if (in_array($linha->ordem, $this->linhasNaoProcessar)) {
                continue;
            }

            if (!$this->linhaTemRecursoConfigurado($linha)) {
                continue;
            }

            $this->calcularColunasVerificacao($linha, $this->colunasBalancete['verificacao']);
            $this->calcularColunasDespesa($linha, $this->colunasBalancete['despesa']);
            $this->calcularColunasRP($linha, $this->colunasBalancete['rp']);
        }

        $this->processaValoresManuais($linhas);
    }

    /**
     * @param stdClass $linha
     * @param array $colunasCalcular
     */
    protected function calcularColunasVerificacao($linha, array $colunasCalcular)
    {
        foreach ($colunasCalcular as $colunaCalcular) {
            $this->redefineConfiguracaoLinha($linha, $colunaCalcular);

            foreach ($this->balanceteVerificacao[$this->exercicio] as $conta) {
                if (!$this->matchBalanceteVerificacao($conta, $linha)) {
                    continue;
                }

                $this->ajustaSaldosBalanceteVerificacao($conta);

                $coluna = $linha->colunas[$colunaCalcular];
                $colunaVerificacao = $this->colunasVerificacao[$coluna->coluna];
                if (empty($colunaVerificacao)) {
                    continue;
                }

                $coluna->valor += $conta->{$colunaVerificacao};
            }
        }
    }

    /**
     * Como cada coluna tem uma origem diferente, é preciso redefinir a configuração das contas em tempo de execução
     * @param $linha
     * @param $colunaCalcular
     */
    protected function redefineConfiguracaoLinha($linha, $colunaCalcular)
    {
        if (is_null($linha->configuracao)) {
            $linha->configuracao = (object)['contas' => []];
        }
        $linha->configuracao->contas = $this->mapeamentoColunas[$colunaCalcular]['contas'];
    }


    private function calcularColunasRP($linha, array $colunasCalcular)
    {
        foreach ($colunasCalcular as $colunaCalcular) {
            $this->redefineConfiguracaoLinha($linha, $colunaCalcular);

            foreach ($this->restosPagar[$this->exercicio] as $conta) {
                if (!$this->matchResto($conta, $linha)) {
                    continue;
                }

                $coluna = $linha->colunas[$colunaCalcular];
                $colunaRp = array_search($coluna->coluna, $this->colunasRp);
                if (empty($colunaRp)) {
                    continue;
                }

                $coluna->valor += $conta->{$colunaRp};
            }
        }
    }

    /**
     * @param $linha
     * @param array $colunasCalcular
     */
    protected function calcularColunasDespesa($linha, array $colunasCalcular)
    {
        foreach ($colunasCalcular as $colunaCalcular) {
            $this->redefineConfiguracaoLinha($linha, $colunaCalcular);

            foreach ($this->balanceteDespesa[$this->exercicio] as $conta) {
                if (!$this->matchResto($conta, $linha)) {
                    continue;
                }

                $coluna = $linha->colunas[$colunaCalcular];
                $colunaDespesa = array_search($coluna->coluna, $this->colunasDespesa);

                if (empty($colunaDespesa)) {
                    continue;
                }

                $coluna->valor += $conta->{$colunaDespesa};
            }
        }
    }

    private function totalizar()
    {
        $this->totalizarColunas();
        $this->calcularSoma();
    }

    protected function totalizarColunas()
    {
        foreach ($this->linhas as $linha) {
            $this->calculaColunaF($linha);
            $this->calculaColunaI($linha);
        }
    }

    protected function calculaColunaF(stdClass $linha)
    {
        $linha->{$this->mapaPropriedadeColunas[6]} = (
            $linha->{$this->mapaPropriedadeColunas[0]} - (
                $linha->{$this->mapaPropriedadeColunas[1]} +
                $linha->{$this->mapaPropriedadeColunas[2]} +
                $linha->{$this->mapaPropriedadeColunas[3]} +
                $linha->{$this->mapaPropriedadeColunas[4]}
            ) -
            $linha->{$this->mapaPropriedadeColunas[5]}
        );
    }


    protected function calculaColunaI(stdClass $linha)
    {
        $linha->{$this->mapaPropriedadeColunas[9]} = (
            $linha->{$this->mapaPropriedadeColunas[6]} - $linha->{$this->mapaPropriedadeColunas[7]}
        );
    }

    private function recursosNaoConfiguradosComExecucao()
    {
        $recursosPresenteNoRelatorio = [];
        foreach ($this->linhas as $linha) {
            if (!is_null($linha->configuracao) && !empty($linha->configuracao->fonteRecurso) &&
                !empty($linha->configuracao->fonteRecurso->valores)) {
                array_map(function ($fonteRecurso) use (&$recursosPresenteNoRelatorio) {
                    $recursosPresenteNoRelatorio[] = $fonteRecurso;
                }, $linha->configuracao->fonteRecurso->valores);
            }
        }

        $recursosPresenteNoRelatorio = array_unique($recursosPresenteNoRelatorio);

        $recursosComExecucao = [];

        foreach ($this->balanceteVerificacao[$this->exercicio] as $execucao) {
            if (!$this->estruturalCalculado($execucao->estrutural)) {
                continue;
            }
            $recursosComExecucao[] = $execucao->fonte_recurso;
        }
        foreach ($this->restosPagar[$this->exercicio] as $conta) {
            $recursosComExecucao[] = $conta->fonte_recurso;
        }
        foreach ($this->balanceteDespesa[$this->exercicio] as $conta) {
            $recursosComExecucao[] = $conta->fonte_recurso;
        }

        $recursosComExecucao = array_unique($recursosComExecucao);
        $this->recursosNaoConfigurados = array_diff($recursosComExecucao, array_unique($recursosPresenteNoRelatorio));
        sort($this->recursosNaoConfigurados);
    }

    /**
     * Valida se o estrutural informado esta na lista de estrutural contabilizado pelo relatório
     * @param string $estrutural
     * @return bool
     */
    protected function estruturalCalculado($estrutural)
    {
        foreach ($this->colunasBalancete['verificacao'] as $coluna) {
            $padroes = $this->mapeamentoColunas[$coluna]['padrao'];
            foreach ($padroes as $padrao) {
                if (strpos($estrutural, $padrao) === 0) {
                    return true;
                }
            }
        }

        return false;
    }

    private function relatorioRecursosNaoConfigurados()
    {
        $recursos = Recurso::query()
            ->select(['o15_recurso', 'o15_descr'])
            ->distinct()
            ->whereIn('o15_recurso', $this->recursosNaoConfigurados)
            ->orderBy('o15_recurso')
            ->get();

        $pdf = new Pdf();
        $pdf->addPage();
        $pdf->exibeHeader(true);
        $pdf->mostrarRodape(true);
        $pdf->mostrarEmissor(true);
        $pdf->mostrarTotalDePaginas(true);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->addTitulo('Lista de Recursos não configurados no Anexo V');
        $pdf->setFont('Arial', 'B', 8);
        $pdf->setFillColor(240);
        $pdf->cell(30, 5, 'Fonte Recurso', 1, 0, 'C');
        $pdf->cell(163, 5, 'Descrição', 'TLB', 1, 'C');

        $pinta = false;
        $pdf->setFont('Arial', '', 8);
        foreach ($recursos as $recurso) {
            $pdf->cell(30, 5, $recurso->o15_recurso, 1, 0, 'C', $pinta);
            $pdf->cellAdapt(8, 163, 5, $recurso->o15_descr, 'TLB', 1, 'F', $pinta);
            $pinta = !$pinta;
        }

        $file = 'tmp/recursos_nao_configurados.pdf';
        $pdf->output($file, 'F');
        return [
            'outros' => [
                [
                    'nome' => 'Lista de Recursos com execução sem configuração no relatório',
                    'documento' => $file,
                    'linkExterno' => ECIDADE_REQUEST_PATH . $file
                ]
            ]
        ];
    }

    /**
     * Toda linha desse relatório só deve ser calculada se tiver configurado fonte de recuro
     * @param $linha
     * @return bool
     */
    protected function linhaTemRecursoConfigurado($linha)
    {
        if (is_null($linha->configuracao)) {
            return false;
        }
        if (empty($linha->configuracao->fonteRecurso->valores)) {
            return false;
        }
        return true;
    }

    public function getSimplificado()
    {
        $this->processar();

        $linha = $this->linhas[$this->linhaSimplificado];
        return (object)array(
            'rp_nao_processado' => $linha->a_liquidar,
            'disponibilidade_caixa_liquida' => $linha->disp_caixa
        );
    }
}
