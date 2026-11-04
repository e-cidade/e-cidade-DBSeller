<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\NovaLRF\RGF\AnexoUm;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Contabilidade\Builder\RegraCalculoLinhaLrfBuilder;
use App\Domain\Financeiro\Contabilidade\Factories\AnexoTresFactory;
use App\Domain\Financeiro\Contabilidade\Factories\TemplateFactory;
use App\Domain\Financeiro\Contabilidade\Factories\VersoesRelatoriosLegaisMscFactory;
use App\Domain\Financeiro\Contabilidade\Relatorios\LRF\RGF\XlsAnexoUm;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\NovaLRF\AnexosExecucaoMensalLrfService;
use Exception;
use NcJoes\OfficeConverter\OfficeConverter;
use NcJoes\OfficeConverter\OfficeConverterException;

class Versao279Service extends AnexosExecucaoMensalLrfService
{
    protected $sections = [
        'despesas' => [1, 18],
        'limite_legal' => [19, 28],
    ];

    protected $versaoRCL = 278;

    /**
     * @var XlsAnexoUm
     */
    protected $parser;

    protected $selecionouCamanara = false;

    /**
     * @return void
     * @throws Exception
     */
    public function processar()
    {
        $this->processarFiltros();
        $this->verificaSelecionouCamara();
        $this->processarLinhasMensais();
        $this->processarLinhaRP();
        $this->alteraSinalContas();
        $this->calculaLinhasManuais();
        $this->totalizaMeses();
        $this->totalizarSomaLinhasMensais(['total_meses', 'inscricao_menos_anulacao_rp_nao_processado']);
        $this->totalizarSubtracaoLinhasMensais(['total_meses', 'inscricao_menos_anulacao_rp_nao_processado']);
        $this->processarRCL();
        $this->organizaLinhas();
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws OfficeConverterException
     */
    public function emitir()
    {
        $this->processar();

        foreach ($this->linhasOrganizadas as $section => $linhas) {
            $this->parser->addCollection($section, $linhas);
        }

        $this->parser->setVariavel('exercicio', $this->exercicio);
        $this->parser->setEnteFederativo($this->enteFederativo);
        $this->parser->setEmissor($this->emissor);
        $this->parser->setPeriodo($this->periodo->getDescricao());
        $this->parser->setAnoReferencia($this->exercicio);
        $this->parser->setMesesPeriodo($this->mesesProcessadosPeriodo());
        $this->parser->setNotaExplicativa($this->getNotaExplicativa());
        $this->parser->setNomePrefeito($this->assinatura->assinaturaPrefeito());
        $this->parser->setNomeContador($this->assinatura->assinaturaContador());
        $this->parser->setNomeOrdenador($this->assinatura->assinaturaSecretarioFazenda());

        $filename = $this->parser->gerar();
        $filePdf = basename($filename, ".xlsx") . ".pdf";
        $pathPdf = "tmp/{$filePdf}";

        $converter = new OfficeConverter($filename, 'tmp');
        $converter->convertTo($filePdf);

        return [
            'xls' => $filename,
            'xlsLinkExterno' => url($filename),
            'pdf' => $pathPdf,
            'pdfLinkExterno' => url($pathPdf)
        ];
    }

    public function getNome()
    {
        return 'Anexo 1 - RGF';
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function carregarParserXls()
    {
        $template = $this->carregarTemplate(TemplateFactory::MODELO_MDF);
        $this->parser = new XlsAnexoUm($template);
    }

    protected function criaPropriedadesValores()
    {
        $meses = $this->getMesesProcessar();
        foreach ($this->linhas as $linha) {
            foreach ($meses as $mes) {
                $linha->{$mes->coluna} = 0;
            }

            foreach ($linha->colunas as $coluna) {
                if ($coluna != 'vlr_mes') {
                    $linha->{$coluna->coluna} = 0;
                }
            }
        }
    }

    /**
     * Executa o calculo das colunas mensais do relatório
     * Colunas: DESPESAS EXECUTADAS (Últimos 12 Meses)
     * @return void
     * @throws Exception
     */
    protected function processarLinhasMensais()
    {
        $estruturais = ['6221303', '6221304', '6221307'];
        $meses = $this->getMesesProcessar();

        foreach ($meses as $mes) {
            $dadosMsc = $this->executarMscMes($mes, $estruturais);
            $this->calculaVencimentos($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            $this->calculaObrigacoesPatronais($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            $this->calculaAposentadoriasReservaReformas($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            $this->calculaPensoes($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            $this->calculaIndenizacoesDemissao($dadosMsc, $mes->coluna, $mes->ano, self::CALCULAR_PERIODO);
            $this->calculaDecisaoJudicial($dadosMsc, $mes->coluna, $mes->ano, self::CALCULAR_PERIODO);

            if (in_array($this->periodo->getCodigo(), [11, 13, 16, 28])) {
                $this->calculaDespesasExerciciosAnteriores($dadosMsc, $mes->coluna, $mes->ano, self::CALCULAR_PERIODO);
            }
            $this->calculaInativosPensionistas($dadosMsc, $mes->coluna, $mes->ano, self::CALCULAR_PERIODO);
            $this->calculaAgentesComunitarios($dadosMsc, $mes->coluna, $mes->ano, self::CALCULAR_PERIODO);
        }


        $estruturais = ['86332'];
        foreach ($meses as $mes) {
            if ($this->validaConta($mes, $estruturais)) {
                $dadosMsc = $this->executarMscMes($mes, $estruturais);
                $this->calculaOutrasDespesas($dadosMsc, $mes->coluna, $mes->ano, self::CALCULAR_PERIODO);
            }
        }
        $estruturais = ['86331'];
        foreach ($meses as $mes) {
            if ($this->validaConta($mes, $estruturais)) {
                $dadosMsc = $this->executarMscMes($mes, $estruturais);
                $this->calculaDespesaPessoalNaoExecutada($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            }
        }
    }

    /**
     * Executa o calculo da ultima coluna do relatório
     * Coluna: INSCRITAS EM RESTOS A PAGAR NÃO PROCESSADOS
     * @return void
     * @throws Exception
     */
    protected function processarLinhaRP()
    {
        $estruturais = ['5311'];
        $comEncerramento = false;
        if (in_array($this->periodo->getCodigo(), [11, 13, 16, 28])) {
            $estruturais = ['6221305', '6221306'];
            $comEncerramento = true;
        }
        $dadosMsc = $this->executarMsc($estruturais, $comEncerramento);

        $propriedade = 'inscricao_menos_anulacao_rp_nao_processado';
        $this->calculaVencimentos($dadosMsc, $propriedade, self::CALCULAR_SALDO_FINAL);
        $this->calculaObrigacoesPatronais($dadosMsc, $propriedade, self::CALCULAR_SALDO_FINAL);
        $this->calculaAposentadoriasReservaReformas($dadosMsc, $propriedade, self::CALCULAR_SALDO_FINAL);
        $this->calculaPensoes($dadosMsc, $propriedade, self::CALCULAR_SALDO_FINAL);
        $this->calculaIndenizacoesDemissao($dadosMsc, $propriedade, $this->exercicio, self::CALCULAR_SALDO_FINAL);
        $this->calculaDecisaoJudicial($dadosMsc, $propriedade, $this->exercicio, self::CALCULAR_SALDO_FINAL);

        if (in_array($this->periodo->getCodigo(), [11, 13, 16, 28])) {
            $this->calculaDespesasExerciciosAnteriores(
                $dadosMsc,
                $propriedade,
                $this->exercicio,
                self::CALCULAR_SALDO_FINAL
            );
        }

        $this->calculaInativosPensionistas($dadosMsc, $propriedade, $this->exercicio, self::CALCULAR_SALDO_FINAL);
        $this->calculaAgentesComunitarios($dadosMsc, $propriedade, $this->exercicio, self::CALCULAR_PERIODO);
        $this->calculaOutrasDespesas($dadosMsc, $propriedade, $this->exercicio, self::CALCULAR_PERIODO);
        $this->calculaDespesaPessoalNaoExecutada($dadosMsc, $propriedade, self::CALCULAR_PERIODO);
    }

    protected function montaEstruturalConformeRegra(array $lista, $estrutural)
    {
        $retorno = [];
        foreach ($lista as $value) {
            $retorno[] = str_replace('XX', $value, $estrutural);
        }

        return $retorno;
    }

    /**
     * Linha: 3 - Vencimentos, Vantagens e Outras Despesas Variáveis
     * ND começada por lista:
     * ND (3.1.XX.04.00 exceto 3.1.90.04.15) + 3.1.XX.08.00 + 3.1.XX.11.00 + 3.1.XX.12.00 + 3.1.XX.16.00 +
     * 3.1.XX.17.00 + 3.1.XX.41.00 + 3.1.XX.67.00 + 3.1.90.91.01 + 3.1.90.91.02 + 3.1.90.91.08 + 3.1.90.91.11 +
     * 3.1.90.91.14 + 3.1.90.91.17 + 3.1.90.91.20 + 3.1.90.91.25 + 3.1.90.91.26 + 3.1.90.91.27 + 3.1.90.91.97 +
     * 3.1.90.91.99 + 3.1.90.92.04 + 3.1.90.92.11 + 3.1.90.92.12 + 3.1.90.92.16 + 3.1.90.92.17 + 3.1.90.92.91 +
     * 3.1.90.92.94 + 3.1.90.92.96 + 3.1.90.92.98 + 3.1.90.92.99 + 3.1.90.94.01 + 3.1.90.94.02 + 3.1.90.94.14 +
     * 3.1.90.94.15 + 3.1.90.94.98 + 3.1.90.94.99 + 3.1.XX.96.00 + 3.1.XX.99.00 + 3.1.91.91.99 + 3.1.91.92.04 +
     * 3.1.91.92.91 + 3.1.91.92.96 + 3.1.91.92.98 + 3.1.91.92.99 + 3.1.91.94.98 + 3.1.91.94.99 + 3.1.95.91.00 +
     * 3.1.95.92.00 + 3.1.95.94.00 + 3.1.96.91.00 + 3.1.96.92.00 + 3.1.96.94.00 + 3.1.20.00.00 + 3.1.22.00.00 +
     * 3.1.31.00.00 + 3.1.32.00.00 + 3.1.35.00.00 + 3.1.36.00.00 + 3.1.40.00.00 + 3.1.41.00.00 + 3.1.42.00.00 +
     * 3.1.45.00.00 + 3.1.46.00.00 + 3.1.50.00.00 + 3.1.60.00.00 + 3.1.67.00.00 + 3.1.71.00.00 + 3.1.72.00.00 +
     * 3.1.73.00.00 + 3.1.74.00.00 + 3.1.75.00.00 + 3.1.76.00.00 + 3.1.92.00.00 + 3.1.93.00.00 + 3.1.94.00.00 +
     * 3.1.99.00.00 + 3.3.90.04.00
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula
     * @return void
     * @throws Exception
     */
    protected function calculaVencimentos($dadosMsc, $coluna, $formula)
    {
        $linha = $this->linhas[3];
        $contas = [
            '31909101', '31909102', '31909108', '31909111', '31909114', '31909117', '31909120', '31909125', '31909126',
            '31909127', '31909197', '31909199', '31909204', '31909211', '31909212', '31909216', '31909217', '31909291',
            '31909294', '31909296', '31909298', '31909299', '31909401', '31909402', '31909414', '31909415', '31909498',
            '31909499', '31919199', '31919204', '31919291', '31919296', '31919298', '31919299', '31919498', '31919499',
            '319591', '319592', '319594', '319691', '319692', '319694', '3120', '3122', '3131', '3132', '3135',
            '3136', '3140', '3141', '3142', '3145', '3146', '3150', '3160', '3167', '3171', '3172', '3173',
            '3174', '3175', '3176', '3192', '3193', '3194', '3199', '339004'
        ];
        $lista = [30, 70, 80, 90, 91, 95, 96];
        $contas = array_merge($this->montaEstruturalConformeRegra($lista, '31XX04'), $contas);
        $contas = array_merge($this->montaEstruturalConformeRegra($lista, '31XX08'), $contas);
        $contas = array_merge($this->montaEstruturalConformeRegra($lista, '31XX11'), $contas);
        $contas = array_merge($this->montaEstruturalConformeRegra($lista, '31XX12'), $contas);
        $contas = array_merge($this->montaEstruturalConformeRegra($lista, '31XX16'), $contas);
        $contas = array_merge($this->montaEstruturalConformeRegra($lista, '31XX17'), $contas);
        $contas = array_merge($this->montaEstruturalConformeRegra($lista, '31XX41'), $contas);
        $contas = array_merge($this->montaEstruturalConformeRegra($lista, '31XX67'), $contas);
        $contas = array_merge($this->montaEstruturalConformeRegra($lista, '31XX96'), $contas);
        $contas = array_merge($this->montaEstruturalConformeRegra($lista, '31XX99'), $contas);

        $contasExclusao = ['31900415'];

        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($contas, false)
            ->addContas($contasExclusao, true)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 4 - Obrigaçães Patronais
     * ND começada por lista:
     * 3.1.XX.07.00 + 3.1.XX.13.00 + 3.1.90.92.07 + 3.1.90.92.13 + 3.1.90.04.15 + 3.1.91.91.51 + 3.1.91.91.52 +
     * 3.1.91.91.53 + 3.1.91.91.54 + 3.1.91.92.13 + 3.1.91.94.51 + 3.1.91.92.05 + 3.1.91.92.06 + 3.1.91.92.07 +
     * 3.1.91.92.08 + 3.1.91.92.09 + 3.1.91.92.10 + 3.1.91.92.11 + 3.1.91.92.12 + 3.1.91.92.51
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula
     * @return void
     * @throws Exception
     */
    protected function calculaObrigacoesPatronais($dadosMsc, $coluna, $formula)
    {
        $linha = $this->linhas[4];
        $contas = [
            '31909207', '31909213', '31900415', '31919151', '31919152', '31919153', '31919154', '31919213', '31919451',
            '31919205', '31919206', '31919207', '31919208', '31919209', '31919210', '31919211', '31919212', '31919251'
        ];
        $lista = [90, 91, 95, 96];
        $contas = array_merge($this->montaEstruturalConformeRegra($lista, '31XX07'), $contas);
        $contas = array_merge($this->montaEstruturalConformeRegra($lista, '31XX13'), $contas);

        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($contas)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 6 - Aposentadorias, Reserva e Reformas
     * ND começada por lista: '319001', '319086', '31909109', '31909112', '31909115', '31909118', '31909123',
     * '31909124', '31909128', '31909129', '31909201', '31909403', '31909404'
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula o que deve somar
     * @return void
     * @throws Exception
     */
    protected function calculaAposentadoriasReservaReformas($dadosMsc, $coluna, $formula)
    {
        $linha = $this->linhas[6];
        $contas = [
            '319001', '319086', '31909109', '31909112', '31909115', '31909118', '31909123', '31909124', '31909128',
            '31909129', '31909201', '31909403', '31909404'
        ];

        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($contas)
            ->addComplemento(['1111', '2111', '1151', '2151', '0'])
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 7 - Pensões
     * ND começada por lista: '319003', '31909110', '31909113', '31909116', '31909119', '31909130',
     * '31909131', '31909136', '31909137', '31909203', '31909406', '31909413'
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula o que deve somar
     * @return void
     * @throws Exception
     */
    protected function calculaPensoes($dadosMsc, $coluna, $formula)
    {
        $linha = $this->linhas[7];
        $contas = [
            '319003', '31909110', '31909113', '31909116', '31909119', '31909130', '31909131', '31909136',
            '31909137', '31909203', '31909406', '31909413'
        ];
        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($contas)
            ->addComplemento(['1111', '2111', '1151', '2151'])
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 8 - Outras despesas de pessoal decorrentes de contratos de terceirização ou de contratação de forma
     * indireta
     * ND começada por lista: 33909134, 33909234, 33XX34
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $exercicio exercício
     * @param integer $formula o que deve somar
     * @return void
     * @throws Exception
     */
    protected function calculaOutrasDespesas($dadosMsc, $coluna, $exercicio, $formula)
    {
        $linha = $this->linhas[8];
        $contas = ['33909134', '33909234'];
        $contas = array_merge($contas, $this->getPlanoDespesa($exercicio, ['33__34']));

        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($contas)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 9 - Despesa com Pessoal não Executada Orçamentariamente
     *
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula o que deve somar
     * @return void
     * @throws Exception
     */
    protected function calculaDespesaPessoalNaoExecutada($dadosMsc, $coluna, $formula)
    {
        $linha = $this->linhas[9];
        $regras = (new RegraCalculoLinhaLrfBuilder())->build();
        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 11 - Indenizações por Demissão e Incentivos à Demissão Voluntária
     * ND começada por lista: 31XX94
     * EXCETO FR: X.800 + FR: X.801 + FR: X.803 + FR: X.604 + FR: X.605
     * EXCETO CO: 1111 / 2111 / CO: 1151 / 2151
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $exercicio exercício
     * @param integer $formula o que deve somar
     * @return void
     * @throws Exception
     */
    protected function calculaIndenizacoesDemissao($dadosMsc, $coluna, $exercicio, $formula)
    {
        $linha = $this->linhas[11];

        /**
         * @todo Daiane disse que esses complementos só existem nos recuros '800', '801', '803
         * @todo tem que ver se não terá que implementar de outra forma
         */
        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($this->getPlanoDespesa($exercicio, ['31__94']))
            ->addSiconfi(['800', '801', '803', '604', '605'], true)
            ->addComplemento(['1111', '2111', '1151', '2151'], true)
            ->build();
        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 12 - Decorrentes de Decisão Judicial de Período Anterior ao da Apuração
     * ND começada por lista: 3.1.XX.91.00 + 3.3.90.91.34
     * EXCETO FR: X.800 + FR: X.801 + FR: X.803 + FR: X.604 + FR: X.605
     * EXCETO CO: 1111 / 2111 / 1151 / 2151
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $exercicio exercício
     * @param integer $formula o que deve somar
     * @return void
     * @throws Exception
     */
    protected function calculaDecisaoJudicial($dadosMsc, $coluna, $exercicio, $formula)
    {
        $linha = $this->linhas[12];
        $contas = $this->getPlanoDespesa($exercicio, ['31__91']);


        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($contas)
            ->addSiconfi(['800', '801', '803', '604', '605'], true)
            ->addComplemento(['1111', '2111', '1151', '2151'], true)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);

        $contas = ['33909134'];
        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($contas)
            ->addSiconfi(['604', '605'], true)
            ->addComplemento(['1111', '2111', '1151', '2151'], true)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 13 - Despesas de Exercícios Anteriores de Período Anterior ao da Apuração
     * [(ND: 3.3.90.92.34; EXCETO FR: X.604 + FR: X.605)] +
     * [ND: 3.1.XX.92.00 EXCETO FR: X.800 + X.801 + X.803; + X.604 + X.605 CO: 1111 / 2111 / 1151 / 2151;]
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $exercicio exercício
     * @param integer $formula o que deve somar
     * @return void
     * @throws Exception
     */
    protected function calculaDespesasExerciciosAnteriores($dadosMsc, $coluna, $exercicio, $formula)
    {
        $linha = $this->linhas[13];
        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($this->getPlanoDespesa($exercicio, ['31__92']))
            ->addSiconfi(['800', '801', '803', '604', '605'], true)
            ->addComplemento(['1111', '2111', '1151', '2151'], true)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);

        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas(['33909234'])
            ->addSiconfi(['604', '605'], true)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 14 - Inativos e Pensionistas com Recursos Vinculados
     * [ (ND: 3.1.XX.01.00 + 3.1.XX.03.00 + 3.1.90.86.00 + 3.1.XX.91.00 + 3.1.XX.92.00 + 3.1.XX.94.00)
     *    FR: X.800 + X.801 + X.803;
     *    CO: 1111 / 2111 / 1151 / 2151;
     *   +
     *   (ND: 3.1.XX.01.00 + ND: 3.1.XX.03.00 + FR: X.802; CO: 1111 / 2111 / CO: 1151 / 2151;)
     * ]
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $exercicio exercício
     * @param integer $formula o que deve somar
     * @return void
     * @throws Exception
     */
    protected function calculaInativosPensionistas($dadosMsc, $coluna, $exercicio, $formula)
    {
        $linha = $this->linhas[14];
        $contas = $this->getPlanoDespesa($exercicio, ['31__01', '31__03']);
        $contasOutras = array_merge($contas, $this->getPlanoDespesa($exercicio, ['31__91', '31__92', '31__94']));
        $contasOutras[] = '319086';
        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($contasOutras)
            ->addSiconfi(['800', '801', '803'])
            ->addComplemento(['1111', '2111', '1151', '2151'])
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);

        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($contas)
            ->addSiconfi(['802'])
            ->addComplemento(['1111', '2111', '1151', '2151'])
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 15 - Agentes Comunitários de Saúde e de Combate às Endemias com Recursos Vinculados (CF, art. 198, §11)
     * (ND 3.1.XX.XX.00 + 3.3.XX.34.00 + 3.3.90.91.34 + 3.3.90.92.34; FR 604)
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $exercicio exercício
     * @param integer $formula o que deve somar
     * @return void
     * @throws Exception
     */
    protected function calculaAgentesComunitarios($dadosMsc, $coluna, $exercicio, $formula)
    {
        $linha = $this->linhas[15];
        $contasOutras = array_merge($this->getPlanoDespesa($exercicio, ['31', '31__34']), ['33909134', '33909234']);
        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($contasOutras)
            ->addSiconfi(['604'])
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 19 - RECEITA CORRENTE LÍQUIDA - RCL (IV)
     * Linha: 20 - (-) Transferências obrigatórias da União relativas às emendas individuais (art. 166-A, § 1º, da CF)
     * Linha: 21 - (-) Transferências obrigatórias da União relativas às emendas de bancada (art. 166, § 16 da CF)
     * Linha: 22 - (-) Transferências da União ... remuneração dos agentes comunitários de saúde...
     * Linha: 23 - (-) Outras Deduções Constitucionais ou Legais
     * Linha: 24 - RECEITA CORRENTE LÍQUIDA AJUSTADA PARA CÁLCULO DOS LIMITES DA DESPESA COM PESSOAL (V)
     * Linha: 25 - DESPESA TOTAL COM PESSOAL - DTP (VI) = (III a + III b)
     * Linha: 26 - LIMITE MÁXIMO (VII) (incisos I, II e III, art. 20 da LRF)
     * Linha: 27 - LIMITE PRUDENCIAL (VIII) = (0,95 x IX) (parágrafo único do art. 22 da LRF)
     * Linha: 28 - LIMITE DE ALERTA (IX) = (0,90 x IX) (inciso II do §1º do art. 59 da LRF)
     * @return void
     * @throws Exception
     */
    protected function processarRCL()
    {
        $filtros = $this->filtros;
        $filtros['relatorio'] = $this->versaoRCL;
        $filtros['anexo'] = 3;
        $filtros['tipo'] = 'RREO';
        $filtros['periodo'] = AnexoTresFactory::transformPeriodo($this->filtros['periodo']);

        $service = VersoesRelatoriosLegaisMscFactory::getService($filtros);
        $linhas = $service->processarSimplificado();

        $valorRCLAjustada = $linhas[35]->total_meses;

        $this->linhas[19]->percentual = '-';
        $this->linhas[20]->percentual = '-';
        $this->linhas[24]->percentual = '-';
        $this->linhas[19]->valor = $linhas[29]->total_meses;
        $this->linhas[20]->valor = $linhas[30]->total_meses;
        $this->linhas[21]->valor = $linhas[32]->total_meses;
        $this->linhas[22]->valor = $linhas[33]->total_meses;
        $this->linhas[23]->valor = $linhas[34]->total_meses;
        $this->linhas[24]->valor = $linhas[35]->total_meses;

        $this->linhas[21]->percentual = $this->calculaPercentual($this->linhas[21]->valor, $valorRCLAjustada);
        $this->linhas[22]->percentual = $this->calculaPercentual($this->linhas[22]->valor, $valorRCLAjustada);
        $this->linhas[23]->percentual = $this->calculaPercentual($this->linhas[23]->valor, $valorRCLAjustada);

        // 25 DESPESA TOTAL COM PESSOAL - DTP (VI) = (III a + III b)
        $tresA = $this->linhas[18]->total_meses;
        $tresB = $this->linhas[18]->inscricao_menos_anulacao_rp_nao_processado;
        $this->linhas[25]->valor = $tresA + $tresB;
        $this->linhas[25]->percentual = $this->calculaPercentual($this->linhas[25]->valor, $valorRCLAjustada);

        // 26 LIMITE MÁXIMO (VII)
        // RECEITA CORRENTE LÍQUIDA AJUSTADA PARA CÁLCULO DOS LIMITES DA DESPESA COM PESSOAL
        if ($this->selecionouCamanara) {
            $this->linhas[26]->percentual = 60;
            $this->linhas[26]->valor = round($this->linhas[24]->valor * 0.60, 2);
        } else {
            $this->linhas[26]->percentual = 54;
            $this->linhas[26]->valor = round($this->linhas[24]->valor * 0.54, 2);
        }
        // LIMITE PRUDENCIAL (VIII)
        $this->linhas[27]->valor = round($this->linhas[26]->valor * 0.95, 2);
        $this->linhas[27]->percentual = $this->calculaPercentual($this->linhas[27]->valor, $valorRCLAjustada);
        // LIMITE DE ALERTA (IX)
        $this->linhas[28]->valor = round($this->linhas[26]->valor * 0.90, 2);
        $this->linhas[28]->percentual = $this->calculaPercentual($this->linhas[28]->valor, $valorRCLAjustada);
    }

    protected function alteraSinalContas()
    {
        $mesesProcessar = $this->getMesesProcessar();
        foreach ($this->linhas as $linha) {
            foreach ($mesesProcessar as $mes) {
                $valor = $linha->{$mes->coluna};
                $linha->{$mes->coluna} = $valor * -1;
            }
        }
    }

    protected function calculaLinhasManuais()
    {
        $this->identificaLinhasManuais();
        $valoresManuais = $this->getValoresManuais(array_keys($this->linhasManuais));
        $this->calculaValorManualMensal($valoresManuais);

        // totaliza a ultima coluna
        $valoresManuais->filter(function ($valorManual) {
            return $valorManual['c180_exercicio'] === (int)$this->exercicio
                && $valorManual['c180_mes'] === (int)$this->periodo->getMesFinal()
                && $valorManual['c180_coluna'] === 'inscricao_menos_anulacao_rp_nao_processado';
        })->each(function ($valorManual) {
            $ordem = $valorManual['c180_linha'];
            $this->linhas[$ordem]->inscricao_menos_anulacao_rp_nao_processado += $valorManual['c180_valor'];
        });
    }

    protected function verificaSelecionouCamara()
    {
        $this->instituicoes->each(function (DBConfig $instituicao) {
            if ($instituicao->db21_tipoinstit == 2) {
                $this->selecionouCamanara = true;
            }
        });
    }

    /**
     * Calcula qual é o valor percentual de $valorA em $valorB
     *
     * @param float $valorA
     * @param float $valorB
     * @return float
     */
    protected function calculaPercentual($valorA, $valorB)
    {
        return round((($valorA * 100) / $valorB), 2);
    }
}
