<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\NovaLRF\RREO\AnexoQuatro;

use App\Domain\Financeiro\Contabilidade\Builder\RegraCalculoLinhaLrfBuilder;
use App\Domain\Financeiro\Contabilidade\Relatorios\LRF\RREO\XlsAnexoQuatro;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\NovaLRF\AnexosService;
use Exception;
use NcJoes\OfficeConverter\OfficeConverter;
use stdClass;

class Versao281Service extends AnexosService
{
    /**
     * Sessões do template. Uma sessão abrange um quadro de dados no template e deve conter todas linhas daquele quadro
     * @var array[]
     */
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
        'verificacao_4' => [73, 75], // BENS E DIREITOS DO RPPS (FUNDO EM REPARTIÇÃO)
        'receita_4' => [76, 76], // RECEITAS DA ADMINISTRAÇÃO - RPPS
        'despesa_4' => [78, 82], // DESPESAS DA ADMINISTRAÇÃO - RPPS
        'verificacao_5' => [84, 86], // BENS E DIREITOS - ADMINISTRAÇÃO DO RPPS
        'receita_5' => [87, 88], // DESPESAS PREVIDENCIÁRIAS (BENEFÍCIOS MANTIDOS PELO TESOURO)
        'despesa_5' => [90, 93], // DESPESAS PREVIDENCIÁRIAS (BENEFÍCIOS MANTIDOS PELO TESOURO)
    ];

    public function emitir()
    {
        $this->processar();

        foreach ($this->linhasOrganizadas as $section => $linhas) {
            $this->parser->addCollection($section, $linhas);
        }

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
        return 'RREO - Anexo IV';
    }

    protected function processar()
    {
        $this->processarFiltros();
        $this->calcularReceitas();
        $this->calcularDespesas();
        $this->calcularOutrasLinhas();
        $this->alteraSinalContas();
        $this->calculaLinhasManuais();
        $this->totalizarSomaLinhas();
        $this->totalizarReceitasFundoCapitalizacao();

        $this->totalizarFundoCapitalizacaoVI();
        $this->totalizarFundoReparticaoXI();
        $this->totalizarResultadoAdministracaoRppsXVI();
        $this->totalizarResultadoBeneficioTesouroXIX();

        $this->organizaLinhas();
    }


    /**
     *
     * @throws Exception
     */
    protected function carregarParserXls()
    {
        $template = $this->carregarTemplate();
        $this->parser = new XlsAnexoQuatro($template);
    }

    /**
     * Realiza o calculo de ambos quadros RECEITAS PREVIDENCIÁRIAS - RPPS (FUNDO EM CAPITALIZAÇÃO) e
     * RECEITAS PREVIDENCIÁRIAS - RPPS (FUNDO EM REPARTIÇÃO) calculam as mesmas contas alterando apenas o recurso
     * Por isso abaixo, por exemplo, executamos o calculo da linha 3 e 43 e assim por diante
     */
    protected function calcularReceitas()
    {
        $dadosMsc = $this->executarMsc(['5211', '5212', '6212', '6213']);

        // Segurados - Ativo
        $receitas = ['1215011', '1215014', '121503', '7215011', '7215014', '721503'];
        $this->calcularLinhaReceita($this->linhas[3], $dadosMsc, $receitas, ['800']);
        $this->calcularLinhaReceita($this->linhas[43], $dadosMsc, $receitas, ['801']);
        // Segurados - Inativo
        $receitas = ['1215012', '1215015', '7215012', '7215015'];
        $this->calcularLinhaReceita($this->linhas[4], $dadosMsc, $receitas, ['800']);
        $this->calcularLinhaReceita($this->linhas[44], $dadosMsc, $receitas, ['801']);
        // Segurados - Pensionista
        $receitas = ['1215013', '1215016', '7215013', '7215016'];
        $this->calcularLinhaReceita($this->linhas[5], $dadosMsc, $receitas, ['800']);
        $this->calcularLinhaReceita($this->linhas[45], $dadosMsc, $receitas, ['801']);

        // Patronais - Ativo
        $receitas = ['1215021', '1215022', '1215511', '7215021', '7215022', '7215511'];
        $this->calcularLinhaReceita($this->linhas[7], $dadosMsc, $receitas, ['800']);
        $this->calcularLinhaReceita($this->linhas[47], $dadosMsc, $receitas, ['801']);
        // Patronais - Inativo
        $receitas = ['1215501', '1215503', '1215512', '7215501', '7215503', '7215512'];
        $this->calcularLinhaReceita($this->linhas[8], $dadosMsc, $receitas, ['800']);
        $this->calcularLinhaReceita($this->linhas[48], $dadosMsc, $receitas, ['801']);
        // Patronais - Pensionista
        $receitas = ['1215502', '1215504', '1215513', '7215502', '7215504', '7215513'];
        $this->calcularLinhaReceita($this->linhas[9], $dadosMsc, $receitas, ['800']);
        $this->calcularLinhaReceita($this->linhas[49], $dadosMsc, $receitas, ['801']);
        // Receitas Imobiliárias
        $receitas = ['131', '731'];
        $this->calcularLinhaReceita($this->linhas[11], $dadosMsc, $receitas, ['800']);
        $this->calcularLinhaReceita($this->linhas[51], $dadosMsc, $receitas, ['801']);
        // Receitas de Valores Mobiliários
        $receitas = ['132', '732'];
        $this->calcularLinhaReceita($this->linhas[12], $dadosMsc, $receitas, ['800']);
        $this->calcularLinhaReceita($this->linhas[52], $dadosMsc, $receitas, ['801']);
        // Outras Receitas Patrimoniais
        $receitas = ['13', '73'];
        $exclusao = ['131', '132', '731', '732'];
        $this->calcularLinhaReceita($this->linhas[13], $dadosMsc, $receitas, ['800'], $exclusao);
        $this->calcularLinhaReceita($this->linhas[53], $dadosMsc, $receitas, ['801'], $exclusao);
        // Receita de Serviços
        $receitas = ['16', '76'];
        $this->calcularLinhaReceita($this->linhas[14], $dadosMsc, $receitas, ['800']);
        $this->calcularLinhaReceita($this->linhas[54], $dadosMsc, $receitas, ['801']);
        // Compensação Financeira entre os regimes
        $receitas = ['199903', '799903'];
        $this->calcularLinhaReceita($this->linhas[16], $dadosMsc, $receitas, ['800']);
        $this->calcularLinhaReceita($this->linhas[56], $dadosMsc, $receitas, ['801']);
        // Receita de Aportes Periódicos para Amortização de Déficit Atuarial do RPPS
        $receitas = ['199901', '799901'];
        $this->calcularLinhaReceita($this->linhas[17], $dadosMsc, $receitas, ['800']);
        // Demais Receitas Correntes
        $receitas = ['19', '79', '1219509', '7219509'];
        $exclusao = ['199903', '199901', '799901', '799903'];
        $this->calcularLinhaReceita($this->linhas[18], $dadosMsc, $receitas, ['800'], $exclusao);
        $exclusao = ['199903', '799903'];
        $this->calcularLinhaReceita($this->linhas[57], $dadosMsc, $receitas, ['801'], $exclusao);

        // Alienação de Bens, Direitos e Ativos
        $receitas = ['22', '82'];
        $this->calcularLinhaReceita($this->linhas[20], $dadosMsc, $receitas, ['800']);
        $this->calcularLinhaReceita($this->linhas[59], $dadosMsc, $receitas, ['801']);

        // Amortização de Empréstimos
        $receitas = ['23', '83'];
        $this->calcularLinhaReceita($this->linhas[21], $dadosMsc, $receitas, ['800']);
        $this->calcularLinhaReceita($this->linhas[60], $dadosMsc, $receitas, ['801']);

        // Outras Receitas de Capital
        $receitas = ['2', '8'];
        $exclusao = ['22', '23', '82', '83'];
        $this->calcularLinhaReceita($this->linhas[22], $dadosMsc, $receitas, ['800'], $exclusao);
        $this->calcularLinhaReceita($this->linhas[61], $dadosMsc, $receitas, ['801'], $exclusao);

        // RECEITAS DA ADMINISTRAÇÃO - RPPS - Receitas Correntes
        $receitas = ['1', '7'];
        $this->calcularLinhaReceita($this->linhas[76], $dadosMsc, $receitas, ['802']);
    }

    protected function calcularDespesas()
    {
        $dadosMsc = $this->executarMsc(['52211', '52212', '52219', '62213']);
        $this->calularLinhaCapitalizacaoAposentadorias($dadosMsc);
        $this->calularLinhaReparticaoAposentadorias($dadosMsc);

        $this->calcularLinhaCapitalizacaoPensao($dadosMsc);
        $this->calcularLinhaReparticaoPensao($dadosMsc);

        $this->calcularLinhaCapitalizacaoCompensacaoFinanceira($dadosMsc);
        $this->calcularLinhaReparticaoCompensacaoFinanceira($dadosMsc);

        $this->calcularLinhaCapitalizacaoDemaisDespesas($dadosMsc);
        $this->calcularLinhaReparticaoDemaisDespesas($dadosMsc);

        // DESPESAS DA ADMINISTRAÇÃO - RPPS
        $this->calcularLinhaPessoalEncargosSociais($dadosMsc);
        $this->calcularLinhaDemaisDespesasCorrentes($dadosMsc);
        $this->calcularLinhaDespesasCapitalXIV($dadosMsc);
    }

    protected function calcularLinhaReceita(
        stdClass $linha,
        array $dadosMsc,
        array $contasSomar,
        array $siconfi,
        $contasDeduzir = []
    ) {
        $regra = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nr')
            ->addContas($contasSomar);
        if (count($contasDeduzir)) {
            $regra->addContas($contasDeduzir, true);
        }
        if (count($siconfi)) {
            $regra->addSiconfi($siconfi);
        }
        $this->calculaColunasReceita($linha, $regra, $dadosMsc);
    }

    /**
     * @param stdClass $linha
     * @param RegraCalculoLinhaLrfBuilder $regra
     * @param $dadosMsc
     * @return void
     * @throws Exception
     */
    public function calculaColunasReceita(stdClass $linha, RegraCalculoLinhaLrfBuilder $regra, $dadosMsc)
    {
        // PREVISÃO ATUALIZADA (a)
        $this->calculaColunaStrPos(
            $linha,
            $regra->addPcasp(['5211', '5212'])->build(),
            $dadosMsc,
            'previsao_atualizada',
            self::CALCULAR_SALDO_FINAL
        );

        // RECEITAS REALIZADAS Até o Bimestre (c)
        $this->calculaColunaStrPos(
            $linha,
            $regra->addPcasp(['6212', '6213'])->build(),
            $dadosMsc,
            'arrecadado_acumulado',
            self::CALCULAR_SALDO_FINAL
        );
    }

    /**
     * @param stdClass $linha
     * @param RegraCalculoLinhaLrfBuilder $regra
     * @param array $dadosMsc
     * @return void
     * @throws Exception
     */
    protected function calculaColunasDespesa(stdClass $linha, RegraCalculoLinhaLrfBuilder $regra, array $dadosMsc)
    {
        // DOTAÇÃO ATUALIZADA (e)
        $this->calculaColunaStrPos(
            $linha,
            $regra->addPcasp(['52211', '52212', '52219'])->build(),
            $dadosMsc,
            'total_creditos',
            self::CALCULAR_SALDO_FINAL
        );

        // DESPESAS EMPENHADAS - Até o Bimestre (f)
        $this->calculaColunaStrPos(
            $linha,
            $regra->addPcasp(['62213'])->build(),
            $dadosMsc,
            'empenhado_liquido_acumulado',
            self::CALCULAR_SALDO_FINAL
        );

        // DESPESAS LIQUIDADAS - Até o Bimestre (f)
        $this->calculaColunaStrPos(
            $linha,
            $regra->addPcasp(['6221303', '6221304', '6221307'])->build(),
            $dadosMsc,
            'liquidado_acumulado',
            self::CALCULAR_SALDO_FINAL
        );

        // DESPESAS PAGAS ATÉ O BIMESTRE (j)
        $this->calculaColunaStrPos(
            $linha,
            $regra->addPcasp(['6221304'])->build(),
            $dadosMsc,
            'pago_acumulado',
            self::CALCULAR_SALDO_FINAL
        );

        // INSCRITAS EM RESTOS A PAGAR NÃO PROCESSADOS
        $this->calculaColunaStrPos(
            $linha,
            $regra->addPcasp(['6221305', '6221306'])->build(),
            $dadosMsc,
            'a_liquidar',
            self::CALCULAR_SALDO_FINAL
        );
    }

    /**
     * Linha: 25 - CAPITALIZAÇÃO - Aposentadorias
     * Regra 1: FR: X800;
     * ND: 31900101, 31900106, 31900118, 31900151, 31900199, 31909109, 31909115, 31909123, 31909128, 31909201, 31909403
     * Regra 2: E TODAS FR (EXCETO FR: X800 + FR: X801 + FR: X803);
     * CO PREVIDENCIÁRIO: 1111,1121,1122,1123,1124,1125,1131,1132,1141,1151
     * ND: 31900101, 31900106, 31900118, 31900151, 31900199, 31909109, 31909115, 31909123, 31909128, 31909201, 31909403
     * FS XX272
     * @param $dadosMsc
     * @return void
     * @throws Exception
     */
    protected function calularLinhaCapitalizacaoAposentadorias($dadosMsc)
    {
        $contas = [
            '31900101', '31900106', '31900118', '31900151', '31900199', '31909109', '31909115', '31909123', '31909128',
            '31909201', '31909403'
        ];

        $regra1 = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($contas)
            ->addSiconfi(['800']);

        $this->calculaColunasDespesa($this->linhas[25], $regra1, $dadosMsc);

        $regra2 = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($contas)
            ->addSiconfi(['800', '801', '803'], true)
            ->addComplemento(['1111', '1121', '1122', '1123', '1124', '1125', '1131', '1132', '1141', '1151'])
            ->addFuncaoSubfuncao(['09272']);
        $this->calculaColunasDespesa($this->linhas[25], $regra2, $dadosMsc);
    }

    /**
     * Linha: 64 - REPARTIÇÃO - Aposentadorias
     * Regra 1: FR: X801;
     * ND: 31900101, 31900106, 31900118, 31900151, 31900199, 31909109, 31909115, 31909123, 31909128, 31909201, 31909403
     * Regra 2: E TODAS FR (EXCETO FR: X800 + FR: X801 + FR: X803);
     * CO PREVIDENCIÁRIO: 2111,2121,2122,2123,2124,2125,2131,2132,2141,2151
     * ND: 31900101, 31900106, 31900118, 31900151, 31900199, 31909109, 31909115, 31909123, 31909128, 31909201, 31909403
     * FS XX272
     * @param $dadosMsc
     * @return void
     * @throws Exception
     */
    protected function calularLinhaReparticaoAposentadorias($dadosMsc)
    {
        $contas = [
            '31900101', '31900106', '31900118', '31900151', '31900199', '31909109', '31909115', '31909123', '31909128',
            '31909201', '31909403'
        ];

        $regra1 = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($contas)
            ->addSiconfi(['801']);

        $this->calculaColunasDespesa($this->linhas[64], $regra1, $dadosMsc);

        $regra2 = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($contas)
            ->addSiconfi(['800', '801', '803'], true)
            ->addComplemento(['2111', '2121', '2122', '2123', '2124', '2125', '2131', '2132', '2141', '2151'])
            ->addFuncaoSubfuncao(['09272']);
        $this->calculaColunasDespesa($this->linhas[64], $regra2, $dadosMsc);
    }

    /**
     * Linha: 26 - CAPITALIZAÇÃO - Pensões por Morte
     * FR: X800;
     * ND: 31900301, 31900303, 31900305, 31900351, 31900399, 31909110, 31909116, 31909130, 31909136, 31909203, 31909413
     * E TODAS FR (EXCETO FR: X800 + FR: X801 + FR: X803);
     * CO PREVIDENCIÁRIO: 1111,1121,1122,1123,1124,1125,1131,1132,1141,1151;
     * ND: 31900301, 31900303, 31900305, 31900351, 31900399, 31909110, 31909116, 31909130, 31909136, 31909203, 31909413
     * FS XX272
     * @param array $dadosMsc
     * @return void
     * @throws Exception
     */
    protected function calcularLinhaCapitalizacaoPensao(array $dadosMsc)
    {
        $contas = [
            '31900301', '31900303', '31900305', '31900351', '31900399', '31909110', '31909116', '31909130', '31909136',
            '31909203', '31909413'
        ];

        $regra1 = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($contas)
            ->addSiconfi(['800']);

        $this->calculaColunasDespesa($this->linhas[26], $regra1, $dadosMsc);

        $regra2 = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($contas)
            ->addSiconfi(['800', '801', '803'], true)
            ->addComplemento(['1111', '1121', '1122', '1123', '1124', '1125', '1131', '1132', '1141', '1151'])
            ->addFuncaoSubfuncao(['09272']);
        $this->calculaColunasDespesa($this->linhas[26], $regra2, $dadosMsc);
    }

    /**
     * Linha: 65 - REPARTIÇÃO - Pensões por Morte
     * FR: X801;
     * ND: 31900301, 31900303, 31900305, 31900351, 31900399, 31909110, 31909116, 31909130, 31909136, 31909203, 31909413
     * E TODAS FR (EXCETO FR: X800 + FR: X801 + FR: X803);
     * CO PREVIDENCIÁRIO: 1111,1121,1122,1123,1124,1125,1131,1132,1141,1151;
     * ND: 31900301, 31900303, 31900305, 31900351, 31900399, 31909110, 31909116, 31909130, 31909136, 31909203, 31909413
     * FS XX272
     * @param array $dadosMsc
     * @return void
     * @throws Exception
     */
    protected function calcularLinhaReparticaoPensao(array $dadosMsc)
    {
        $contas = [
            '31900301', '31900303', '31900305', '31900351', '31900399', '31909110', '31909116', '31909130', '31909136',
            '31909203', '31909413'
        ];

        $regra1 = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($contas)
            ->addSiconfi(['801']);

        $this->calculaColunasDespesa($this->linhas[65], $regra1, $dadosMsc);

        $regra2 = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($contas)
            ->addSiconfi(['800', '801', '803'], true)
            ->addComplemento(['2111', '2121', '2122', '2123', '2124', '2125', '2131', '2132', '2141', '2151'])
            ->addFuncaoSubfuncao(['09272']);
        $this->calculaColunasDespesa($this->linhas[65], $regra2, $dadosMsc);
    }

    /**
     * Linha: 28 - CAPITALIZAÇÃO - Compensação Financeira entre os regimes
     * Regra 1: FR: X800; ND: 33908600, 33918600, 31908600, 31918600
     * Regra 2: TODAS FR (EXCETO FR: X800 + FR: X801 + FR: X803); +
     * CO FINANCEIRO: 1111,1121,1122,1123,1124,1125,1131,1132,1141,1151
     * ND: 33908600, 33918600, 31908600, 31918600
     * @param array $dadosMsc
     * @return void
     * @throws Exception
     */
    protected function calcularLinhaCapitalizacaoCompensacaoFinanceira(array $dadosMsc)
    {
        $contas = ['33908600', '33918600', '31908600', '31918600'];

        $regra1 = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($contas)
            ->addSiconfi(['800']);

        $this->calculaColunasDespesa($this->linhas[28], $regra1, $dadosMsc);

        $regra2 = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($contas)
            ->addSiconfi(['800', '801', '803'], true)
            ->addComplemento(['1111', '1121', '1122', '1123', '1124', '1125', '1131', '1132', '1141', '1151']);
        $this->calculaColunasDespesa($this->linhas[28], $regra2, $dadosMsc);
    }

    /**
     * Linha: 67 - REPARTIÇÃO - Compensação Financeira entre os regimes
     * Regra 1: FR: X801; ND: 33908600, 33918600, 31908600, 31918600
     * Regra 2: TODAS FR (EXCETO FR: X800 + FR: X801 + FR: X803); +
     * CO FINANCEIRO: 2111,2121,2122,2123,2124,2125,2131,2132,2141,2151 +
     * ND: 33908600, 33918600, 31908600, 31918600
     * @param array $dadosMsc
     * @return void
     * @throws Exception
     */
    protected function calcularLinhaReparticaoCompensacaoFinanceira(array $dadosMsc)
    {
        $contas = ['33908600', '33918600', '31908600', '31918600'];

        $regra1 = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($contas)
            ->addSiconfi(['801']);

        $this->calculaColunasDespesa($this->linhas[67], $regra1, $dadosMsc);

        $regra2 = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($contas)
            ->addSiconfi(['800', '801', '803'], true)
            ->addComplemento(['2111', '2121', '2122', '2123', '2124', '2125', '2131', '2132', '2141', '2151']);
        $this->calculaColunasDespesa($this->linhas[67], $regra2, $dadosMsc);
    }

    /**
     * Linha: 29 - CAPITALIZAÇÃO - Demais Despesas Previdenciárias
     * Regra 1: FR: X800;
     * (ND: 3190 (-) ND: 319001 (-) ND: 319003 (-) ND: 319091 (-) ND: 319092 (-) ND: 319094) +
     * ND: 31909199, 31909291, 31909294, 31909299, 31909499, 3120, 3122, 3130, 3131, 3132, 3135, 3136, 3140, 3141, 3142,
     * 3145, 3146, 3150, 3160, 3167, 3170, 3171, 3172, 3173, 3174, 3175, 3176, 3180, 3191, 3192, 3193, 3194, 3195, 3196,
     * 3199, 339039, 339047, 33909239, 33909247
     * @param array $dadosMsc
     * @return void
     * @throws Exception
     */
    protected function calcularLinhaCapitalizacaoDemaisDespesas(array $dadosMsc)
    {
        /**
         * O calculo foi dividido em 2x porque nessa regra existe uma "exclusão parcial" de um grupo de contas
         * Exemplo.
         * A regra calcula a 3190 mas exclui a conta 319092 porem na regra diz para somar a 31909291 que é filha da
         * conta excluída
         *
         * Para resolver essa regra, somamos todas contas que não conflitam com a exclusão. E depois somamos as contas
         * que geraram conflito.
         */
        $contas = [
            '3190', '3120', '3122', '3130', '3131', '3132', '3135', '3136', '3140', '3141', '3142', '3145', '3146',
            '3150', '3160', '3167', '3170', '3171', '3172', '3173', '3174', '3175', '3176', '3180', '3191', '3192',
            '3193', '3194', '3195', '3196', '3199', '339039', '339047', '33909239', '33909247'
        ];

        $regra1 = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($contas)
            ->addContas(['319001', '319003', '319091', '319092', '319094'], true)
            ->addSiconfi(['800']);

        $this->calculaColunasDespesa($this->linhas[29], $regra1, $dadosMsc);

        $regra2 = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas(['31909199', '31909291', '31909294', '31909299', '31909499'])
            ->addSiconfi(['800']);
        $this->calculaColunasDespesa($this->linhas[29], $regra2, $dadosMsc);
    }

    /**
     * Linha: 68 - CAPITALIZAÇÃO - Demais Despesas Previdenciárias
     *
     * VER DETALHES DESCRITO NO METODO calcularLinhaCapitalizacaoDemaisDespesas
     *
     * @param array $dadosMsc
     * @return void
     * @throws Exception
     */
    protected function calcularLinhaReparticaoDemaisDespesas(array $dadosMsc)
    {
        $contas = [
            '3190', '3120', '3122', '3130', '3131', '3132', '3135', '3136', '3140', '3141', '3142', '3145', '3146',
            '3150', '3160', '3167', '3170', '3171', '3172', '3173', '3174', '3175', '3176', '3180', '3191', '3192',
            '3193', '3194', '3195', '3196', '3199', '339039', '339047', '33909239', '33909247'
        ];

        $regra1 = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($contas)
            ->addContas(['319001', '319003', '319091', '319092', '319094'], true)
            ->addSiconfi(['801']);

        $this->calculaColunasDespesa($this->linhas[68], $regra1, $dadosMsc);

        $regra2 = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas(['31909199', '31909291', '31909294', '31909299', '31909499'])
            ->addSiconfi(['801']);
        $this->calculaColunasDespesa($this->linhas[68], $regra2, $dadosMsc);
    }

    protected function calcularOutrasLinhas()
    {
        $this->calcularLinhaRecursosRppsArrecadadosExerciciosAnteriores();
        $this->calcularLinhaReservaOrcamentariaRpps();

        // APORTES DE RECURSOS PARA O FUNDO EM CAPITALIZAÇÃO DO RPPS
        $dadosMsc = $this->executarMsc(['4513202']);
        $this->calcularLinhaContribuicaoPatronalSuplementar($dadosMsc);
        $this->calcularLinhaAporteValoresPredefinidos($dadosMsc);
        $this->calcularLinhaOutrosAportesRpps($dadosMsc);
        $this->calcularLinhaRecursosCoberturaDeficitFinanceiro($dadosMsc);

        // APORTES DE RECURSOS PARA O FUNDO EM REPARTIÇÃO DO RPPS
        $this->calcularLinhaRecursosCoberturaInsuficienciasFinanceiras();
        $this->calcularLinhaRecursosFormacaoReserva();


        // BENS E DIREITOS DO RPPS (FUNDO EM CAPITALIZAÇÃO)
        // BENS E DIREITOS DO RPPS (FUNDO EM REPARTIÇÃO)
        // BENS E DIREITOS - ADMINISTRAÇÃO DO RPPS
        $this->calcularLinhaCaixaEquivalente();
        $this->calcularLinhaInvestimentosAplicacoes();
        $this->calcularLinhaOutrosBensDireitos();
    }

    /**
     * Linha 32 - RECURSOS RPPS ARRECADADOS EM EXERCÍCIOS ANTERIORES - VALOR
     * Fórmula: CC: 5.2.1.10.00.00  NR: 9.9.9.0.00.0.0
     * @return void
     * @throws Exception
     */
    protected function calcularLinhaRecursosRppsArrecadadosExerciciosAnteriores()
    {
        $dadosMsc = $this->executarMsc(['52110']);

        $regra = (new RegraCalculoLinhaLrfBuilder())
            ->addPcasp(['52110'])
            ->natureza('nr')
            ->addContas(['999']);

        $this->calculaColunaStrPos(
            $this->linhas[32],
            $regra->build(),
            $dadosMsc,
            'valor_inicial',
            self::CALCULAR_SALDO_FINAL
        );
    }

    /**
     * Linha: 33 -  RESERVA ORÇAMENTÁRIA DO RPPS - VALOR
     * Formula: CC: 5.2.2.1.1.00.00  ND: 9.9.99.99.99 + FS: 99.997
     * @return void
     * @throws Exception
     */
    protected function calcularLinhaReservaOrcamentariaRpps()
    {
        $dadosMsc = $this->executarMsc(['52110']);

        $regra = (new RegraCalculoLinhaLrfBuilder())
            ->addPcasp(['52211'])
            ->natureza('nd')
            ->addContas(['99999999'])
            ->addFuncaoSubfuncao(['99997']);

        $this->calculaColunaStrPos(
            $this->linhas[33],
            $regra->build(),
            $dadosMsc,
            'valor_inicial',
            self::CALCULAR_SALDO_FINAL
        );
    }

    /**
     * Linha: 34 - Plano de Amortização - Contribuição Patronal Suplementar
     * Fórmula: CC: 4.5.1.3.2.02.05
     * @return void
     * @throws Exception
     */
    protected function calcularLinhaContribuicaoPatronalSuplementar($dadosMsc)
    {
        $regra = (new RegraCalculoLinhaLrfBuilder())->addPcasp(['451320205']);
        $this->calculaColunaStrPos(
            $this->linhas[34],
            $regra->build(),
            $dadosMsc,
            'saldo_final_acumulado',
            self::CALCULAR_SALDO_FINAL
        );
    }

    /**
     * Linha: 35 - Plano de Amortização - Aporte Periódico de Valores Predefinidos
     * Fórmula: CC: 4.5.1.3.2.02.02
     * @param array $dadosMsc
     * @return void
     * @throws Exception
     */
    protected function calcularLinhaAporteValoresPredefinidos(array $dadosMsc)
    {
        $regra = (new RegraCalculoLinhaLrfBuilder())->addPcasp(['451320202']);
        $this->calculaColunaStrPos(
            $this->linhas[35],
            $regra->build(),
            $dadosMsc,
            'saldo_final_acumulado',
            self::CALCULAR_SALDO_FINAL
        );
    }

    /**
     * Linha: 36 - Outros Aportes para o RPPS
     * Fórmula: CC: 4.5.1.3.2.02.06 + CC: 4.5.1.3.2.02.99 + CC: 4.5.1.3.2.99.00
     * @param array $dadosMsc
     * @return void
     * @throws Exception
     */
    protected function calcularLinhaOutrosAportesRpps(array $dadosMsc)
    {
        $regra = (new RegraCalculoLinhaLrfBuilder())->addPcasp(['451320206', '451320299', '451329900']);
        $this->calculaColunaStrPos(
            $this->linhas[36],
            $regra->build(),
            $dadosMsc,
            'saldo_final_acumulado',
            self::CALCULAR_SALDO_FINAL
        );
    }


    /**
     * Linha: 37 - Recursos para Cobertura de Déficit Financeiro
     * Fórmula: CC 4.5.1.3.2.02.01
     * @param array $dadosMsc
     * @return void
     * @throws Exception
     */
    protected function calcularLinhaRecursosCoberturaDeficitFinanceiro(array $dadosMsc)
    {
        $regra = (new RegraCalculoLinhaLrfBuilder())->addPcasp(['451320201']);
        $this->calculaColunaStrPos(
            $this->linhas[36],
            $regra->build(),
            $dadosMsc,
            'saldo_final_acumulado',
            self::CALCULAR_SALDO_FINAL
        );
    }

    /**
     * BENS E DIREITOS DO RPPS (FUNDO EM CAPITALIZAÇÃO)
     *
     * Linha: 38 - Caixa e Equivalentes de Caixa
     * Fórmula: FR: X.800;  (CC: 1.1.1.1.1.06.03 + CC: 1.1.1.1.1.30.00 + CC: 1.1.1.1.1.53.00)
     *
     * BENS E DIREITOS DO RPPS (FUNDO EM REPARTIÇÃO)
     * Linha: 73 - Caixa e Equivalentes de Caixa
     * Fórmula: FR: X.801;  (CC: 1.1.1.1.1.06.02 + CC: 1.1.1.1.1.30.00 + CC: 1.1.1.1.1.51.00)
     *
     * BENS E DIREITOS - ADMINISTRAÇÃO DO RPPS
     * Linha: 84 - Caixa e Equivalentes de Caixa
     * FR: X.802; (CC: 1.1.1.1.1.06.04 + CC: 1.1.1.1.1.30.00 + CC: 1.1.1.1.1.52.00)
     * @return void
     * @throws Exception
     */
    protected function calcularLinhaCaixaEquivalente()
    {
        $pcasp = ['111110602', '111110603', '111110604', '1111130', '1111151', '1111152', '1111153'];
        $dadosMsc = $this->executarMsc($pcasp);
        $regra = (new RegraCalculoLinhaLrfBuilder())
            ->addPcasp(['111110603', '1111130', '1111153'])
            ->addSiconfi(['800']);
        $this->calculaColunaStrPos(
            $this->linhas[38],
            $regra->build(),
            $dadosMsc,
            'saldo_final_acumulado',
            self::CALCULAR_SALDO_FINAL
        );

        $regra = (new RegraCalculoLinhaLrfBuilder())
            ->addPcasp(['111110602', '1111130', '1111151'])
            ->addSiconfi(['801']);
        $this->calculaColunaStrPos(
            $this->linhas[73],
            $regra->build(),
            $dadosMsc,
            'saldo_final_acumulado',
            self::CALCULAR_SALDO_FINAL
        );

        $dadosMsc = $this->executarMsc();
        $regra = (new RegraCalculoLinhaLrfBuilder())
            ->addPcasp(['111110604', '1111130', '1111152'])
            ->addSiconfi(['802']);
        $this->calculaColunaStrPos(
            $this->linhas[84],
            $regra->build(),
            $dadosMsc,
            'saldo_final_acumulado',
            self::CALCULAR_SALDO_FINAL
        );
    }

    /**
     * BENS E DIREITOS DO RPPS (FUNDO EM CAPITALIZAÇÃO)
     * Linha: 39 - Investimentos e Aplicações
     * Fórmula: CC: 1.1.4.4.1.01.00 + 1.1.4.4.1.02.00 + 1.1.4.4.1.03.00 + 1.1.4.4.1.04.00 + 1.1.4.4.1.05.00
     * + 1.1.4.4.1.06.00 + 1.1.4.4.1.07.00 + 1.1.4.4.1.99.00 + (-) 1.1.4.9.1.05.00 + (-) 1.1.4.9.1.07.00
     * + 1.2.1.3.1.08.00 + 1.2.2.3.1.01.00 + 1.2.2.3.1.02.00 + 1.2.2.3.1.05.00 + (-) 1.2.2.9.1.03.00
     *
     * BENS E DIREITOS DO RPPS (FUNDO EM REPARTIÇÃO)
     * Linha: 74 - REPARTIÇÃO - Investimentos e Aplicações
     * Fórmula: CC: 1.1.4.4.1.11.00 + 1.1.4.4.1.12.00 + 1.1.4.4.1.13.00 + 1.1.4.4.1.14.00 + 1.1.4.4.1.15.00 +
     * 1.1.4.4.1.16.00 + 1.1.4.4.1.17.00 + (-) 1.1.4.9.1.06.00 + (-) 1.1.4.9.1.08.00 + 1.2.2.3.1.03.00 +
     * 1.2.2.3.1.04.00 + (-) 1.2.2.9.1.05.00
     *
     * Linha: 85 - ADMINISTRAÇÃO RPPS - Investimentos e Aplicações
     * Fórmula: CC: 1.1.4.4.1.30.00 + (-) 1.1.4.9.1.09.00
     * Fórmula:
     * @return void
     * @throws Exception
     */
    protected function calcularLinhaInvestimentosAplicacoes()
    {
        $contas = [
            '1144101', '1144102', '1144103', '1144104', '1144105', '1144106', '1144107', '1144199', '1149105',
            '1149107', '1213108', '1223101', '1223102', '1223105', '1229103'
        ];
        $dadosMsc = $this->executarMsc($contas);
        $regra = (new RegraCalculoLinhaLrfBuilder());
        $this->calculaColunaStrPos(
            $this->linhas[38],
            $regra->build(),
            $dadosMsc,
            'saldo_final_acumulado',
            self::CALCULAR_SALDO_FINAL
        );

        $contas = [
            '1144111', '1144112', '1144113', '1144114', '1144115', '1144116', '1144117', '1149106', '1149108',
            '1223103', '1223104', '1229105'
        ];
        $dadosMsc = $this->executarMsc($contas);
        $regra = (new RegraCalculoLinhaLrfBuilder());
        $this->calculaColunaStrPos(
            $this->linhas[74],
            $regra->build(),
            $dadosMsc,
            'saldo_final_acumulado',
            self::CALCULAR_SALDO_FINAL
        );

        $dadosMsc = $this->executarMsc(['1144130', '1149109']);
        $regra = (new RegraCalculoLinhaLrfBuilder());
        $this->calculaColunaStrPos(
            $this->linhas[85],
            $regra->build(),
            $dadosMsc,
            'saldo_final_acumulado',
            self::CALCULAR_SALDO_FINAL
        );
    }

    /**
     * BENS E DIREITOS DO RPPS (FUNDO EM CAPITALIZAÇÃO)
     * Linha: 40 - Outros Bens e Direitos
     * Fórmula: CC: 1.1.2.4.1.07.01 + 1.1.2.4.1.07.02 + 1.1.2.4.1.07.03 + 1.1.2.4.1.07.04 + 1.1.2.4.2.07.00 +
     * 1.1.2.4.3.07.00 + 1.1.2.4.4.07.00 + 1.1.2.4.5.07.00 + (-) 1.1.2.9.1.07.01 + (-) 1.1.2.9.1.07.03
     * + (-) 1.1.2.9.2.07.00 + (-) 1.1.2.9.3.07.00 + (-) 1.1.2.9.4.07.00 + (-) 1.1.2.9.5.07.00  + 1.1.3.6.1.01.01 +
     * 1.1.3.6.1.02.01 + 1.1.3.6.1.99.00 + 1.1.3.6.2.01.01 + 1.1.3.6.2.01.02 + 1.1.3.6.2.02.01 + 1.1.3.6.2.02.02 +
     * 1.1.3.6.2.04 + 1.1.3.6.2.05 + 1.1.3.6.2.06 + 1.1.3.6.2.99 + 1.1.3.6.3.00 + 1.1.3.6.4.00 + 1.1.3.6.5.00 +
     * 1.2.1.1.1.03.03 + 1.2.1.1.1.03.04 + 1.2.1.1.1.03.09 + 1.2.1.1.1.03.10 + 1.2.1.1.1.06 + (-) 1.2.1.1.1.99.06 +
     * 1.2.1.1.2.06.01 + 1.2.1.1.2.06.02 + 1.2.1.1.2.06.03 + 1.2.1.1.2.06.04 + 1.2.1.1.2.06.05 + 1.2.1.1.2.06.98 +
     * 1.2.1.1.2.06.99 + 1.2.1.1.2.08 + 1.2.1.1.3.06 + 1.2.1.1.4.06 + 1.2.1.1.5.06 + 1.2.1.1.4.03.05 + 1.2.1.1.4.03.06 +
     * 1.2.1.1.4.03.07 + 1.2.1.1.4.03.08 + 1.2.1.1.5.03.05 + 1.2.1.1.5.03.06 + 1.2.1.1.5.03.07 + 1.2.1.1.5.03.08
     *
     * BENS E DIREITOS DO RPPS (FUNDO EM REPARTIÇÃO)
     * Linha: 75 - REPARTIÇÃO - Outros Bens e Direitos
     * Fórmula: CC: 1.1.2.4.1.07.05 + 1.1.2.4.1.07.06 + 1.1.2.4.1.07.07 + 1.1.2.4.1.07.08 + (-) 1.1.2.9.1.07.02 + (-)
     * 1.1.2.9.1.07.04 + 1.1.3.6.1.01.02 + 1.1.3.6.1.02.02 + 1.1.3.6.2.01.03 + 1.1.3.6.2.01.04 + 1.1.3.6.2.02.03 +
     * 1.1.3.6.2.02.04 + 1.2.1.1.1.03.05 + 1.2.1.1.1.03.06 + 1.2.1.1.1.03.11 + 1.2.1.1.1.03.12 + (-) 1.2.1.1.1.99.07 +
     * 1.2.1.1.2.06.06 + 1.2.1.1.2.06.07 + 1.2.1.1.2.06.08 + 1.2.1.1.2.06.09 + 1.2.1.1.2.06.10 + 1.2.1.1.2.06.96 +
     * 1.2.1.1.2.06.97 + 1.2.1.1.4.03.11 + 1.2.1.1.4.03.12 + 1.2.1.1.4.03.13 + 1.2.1.1.4.03.14
     *
     * BENS E DIREITOS - ADMINISTRAÇÃO DO RPPS
     * Linha: 86 - ADMINISTRAÇÃO RPPS - Outros Bens e Direitos
     * Não há mapeamento
     * @return void
     * @throws Exception
     */
    protected function calcularLinhaOutrosBensDireitos()
    {
        $contas = [
            '112410701', '112410702', '112410703', '112410704', '1124207', '1124307', '1124407', '1124507', '113610101',
            '113610201', '1136199', '113620101', '113620102', '113620201', '113620202', '1136204', '1136205', '1136206',
            '1136299', '11363', '11364', '11365', '121110303', '121110304', '121110309', '12111031', '1211106',
            '121120601', '121120602', '121120603', '121120604', '121120605', '121120698', '121120699', '1211208',
            '1211306', '1211406', '1211506', '121140305', '121140306', '121140307', '121140308', '121150305',
            '121150306', '121150307', '121150308'
        ];
        $dadosMsc = $this->executarMsc($contas);
        $regra = (new RegraCalculoLinhaLrfBuilder());
        $this->calculaColunaStrPos(
            $this->linhas[40],
            $regra->build(),
            $dadosMsc,
            'saldo_final_acumulado',
            self::CALCULAR_SALDO_FINAL
        );


        $contas = [
            '112410705', '112410706', '112410707', '112410708', '112910702', '112910704', '113610102', '113610202',
            '113620103', '113620104', '113620203', '113620204', '121110305', '121110306', '121110311', '121110312',
            '121119907', '121120606', '121120607', '121120608', '121120609', '121120610', '121120696', '121120697',
            '121140311', '121140312', '121140313', '121140314'
        ];
        $dadosMsc = $this->executarMsc($contas);
        $regra = (new RegraCalculoLinhaLrfBuilder());
        $this->calculaColunaStrPos(
            $this->linhas[75],
            $regra->build(),
            $dadosMsc,
            'saldo_final_acumulado',
            self::CALCULAR_SALDO_FINAL
        );
    }

    /**
     * Linha 23 - TOTAL DAS RECEITAS DO FUNDO EM CAPITALIZAÇÃO - (IV) = (I + III - II)
     * @return void
     */
    protected function totalizarReceitasFundoCapitalizacao()
    {
        $linhaTotalizar = $this->linhas[23];
        $this->somarLinha($linhaTotalizar, [1, 19]);
        $this->subtraiLinha($linhaTotalizar, [17]);
    }

    /**
     * Linha: 31 - RESULTADO PREVIDENCIÁRIO - FUNDO EM CAPITALIZAÇÃO (VI) = (IV - V)²
     * @return void
     */
    protected function totalizarFundoCapitalizacaoVI()
    {
        $iv = $this->linhas[23];
        $v = $this->linhas[30];
        $vi = $this->linhas[31];
        $vi->total_creditos = $v->total_creditos - $iv->previsao_atualizada;
        $vi->empenhado_liquido_acumulado = $v->empenhado_liquido_acumulado - $iv->arrecadado_acumulado;
        $vi->liquidado_acumulado = $v->liquidado_acumulado - $iv->arrecadado_acumulado;
        $vi->pago_acumulado = $v->pago_acumulado - $iv->arrecadado_acumulado;
    }

    /**
     * Linha: 70 - RESULTADO PREVIDENCIÁRIO - FUNDO EM REPARTIÇÃO (XI) = (IX - X)²
     * @return void
     */
    protected function totalizarFundoReparticaoXI()
    {
        $ix = $this->linhas[62];
        $x = $this->linhas[69];
        $xi = $this->linhas[70];
        $xi->total_creditos = $x->total_creditos - $ix->previsao_atualizada;
        $xi->empenhado_liquido_acumulado = $x->empenhado_liquido_acumulado - $ix->arrecadado_acumulado;
        $xi->liquidado_acumulado = $x->liquidado_acumulado - $ix->arrecadado_acumulado;
        $xi->pago_acumulado = $x->pago_acumulado - $ix->arrecadado_acumulado;
    }

    /**
     * Linha: 83 - RESULTADO DA ADMINISTRAÇÃO RPPS (XVI) = (XII - XV)²
     * @return void
     */
    protected function totalizarResultadoAdministracaoRppsXVI()
    {
        $xii = $this->linhas[77];
        $xv = $this->linhas[82];
        $xvi = $this->linhas[83];
        $xvi->total_creditos = $xv->total_creditos - $xii->previsao_atualizada;
        $xvi->empenhado_liquido_acumulado = $xv->empenhado_liquido_acumulado - $xii->arrecadado_acumulado;
        $xvi->liquidado_acumulado = $xv->liquidado_acumulado - $xii->arrecadado_acumulado;
        $xvi->pago_acumulado = $xv->pago_acumulado - $xii->arrecadado_acumulado;

        $xv->a_liquidar = null;
        if ((int)$this->periodo->getCodigo() === 11) {
            $xv->a_liquidar = $xii->arrecadado_acumulado - $xv->a_liquidar;
        }
    }

    /**
     * Linha: 94 - RESULTADO DOS BENEFÍCIOS MANTIDOS PELO TESOURO (XIX) = (XVII - XVIII)²
     * @return void
     */
    protected function totalizarResultadoBeneficioTesouroXIX()
    {
        $xvii = $this->linhas[89];
        $xviii = $this->linhas[93];
        $xix = $this->linhas[94];
        $xix->total_creditos = $xviii->total_creditos - $xvii->previsao_atualizada;
        $xix->empenhado_liquido_acumulado = $xviii->empenhado_liquido_acumulado - $xvii->arrecadado_acumulado;
        $xix->liquidado_acumulado = $xviii->liquidado_acumulado - $xvii->arrecadado_acumulado;
        $xix->pago_acumulado = $xviii->pago_acumulado - $xvii->arrecadado_acumulado;
    }

    /**
     * Linha 71 - Recursos para Cobertura de Insuficiências Financeiras
     * @return void
     * @throws Exception
     */
    protected function calcularLinhaRecursosCoberturaInsuficienciasFinanceiras()
    {
        $dadosMsc = $this->executarMsc(['451320101', '451320199']);
        $regra = (new RegraCalculoLinhaLrfBuilder())->build();
        $this->calculaColunaStrPos(
            $this->linhas[71],
            $regra,
            $dadosMsc,
            'saldo_final_acumulado',
            self::CALCULAR_SALDO_FINAL
        );
    }

    /**
     * Linha 72 - Recursos para Formação de Reserva
     * @return void
     * @throws Exception
     */
    protected function calcularLinhaRecursosFormacaoReserva()
    {
        $dadosMsc = $this->executarMsc(['451320102']);
        $regra = (new RegraCalculoLinhaLrfBuilder())->build();
        $this->calculaColunaStrPos(
            $this->linhas[72],
            $regra,
            $dadosMsc,
            'saldo_final_acumulado',
            self::CALCULAR_SALDO_FINAL
        );
    }

    /**
     * Linha: 79 - Pessoal e Encargos Sociais
     * @param array $dadosMsc
     * @return void
     * @throws Exception
     */
    protected function calcularLinhaPessoalEncargosSociais($dadosMsc)
    {
        $contasExcluir = [
            '319001', '319003', '319086', '31909109', '31909110', '31909112', '31909113', '31909115', '31909116',
            '31909118', '31909119', '31909123', '31909124', '31909128', '31909129', '31909130', '31909131', '31909136',
            '31909137', '31909201', '31909203', '31909403', '31909404', '31909406', '31909413', '319186'
        ];

        $regra1 = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas(['31'])
            ->addContas($contasExcluir, true)
            ->addSiconfi(['802']);
        $this->calculaColunasDespesa($this->linhas[79], $regra1, $dadosMsc);

        $regra2 = (new RegraCalculoLinhaLrfBuilder())
            ->addPo(['10132'])
            ->natureza('nd')
            ->addContas(['31'])
            ->addContas($contasExcluir, true)
            ->addSiconfi(['800', '801', '802', '803'], true);
        $this->calculaColunasDespesa($this->linhas[79], $regra2, $dadosMsc);
    }

    /**
     * Linha: 80 - Demais Despesas Correntes
     * @param array $dadosMsc
     * @return void
     * @throws Exception
     */
    protected function calcularLinhaDemaisDespesasCorrentes(array $dadosMsc)
    {
        $contas = ['32', '33'];
        $contasExcluir = ['339086', '339186'];

        $regra1 = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas($contas)
            ->addContas($contasExcluir, true)
            ->addSiconfi(['802']);
        $this->calculaColunasDespesa($this->linhas[80], $regra1, $dadosMsc);

        $regra2 = (new RegraCalculoLinhaLrfBuilder())
            ->addPo(['10132'])
            ->natureza('nd')
            ->addContas($contas)
            ->addContas($contasExcluir, true)
            ->addSiconfi(['800', '801', '802', '803'], true);
        $this->calculaColunasDespesa($this->linhas[80], $regra2, $dadosMsc);
    }

    /**
     * Linha: 81 - Despesas de Capital (XIV)
     * @param array $dadosMsc
     * @return void
     * @throws Exception
     */
    protected function calcularLinhaDespesasCapitalXIV(array $dadosMsc)
    {
        $regra1 = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nd')
            ->addContas(['4'])
            ->addSiconfi(['802']);
        $this->calculaColunasDespesa($this->linhas[81], $regra1, $dadosMsc);

        $regra2 = (new RegraCalculoLinhaLrfBuilder())
            ->addPo(['10132'])
            ->natureza('nd')
            ->addContas(['4'])
            ->addSiconfi(['800', '801', '802', '803'], true);
        $this->calculaColunasDespesa($this->linhas[81], $regra2, $dadosMsc);
    }
}
