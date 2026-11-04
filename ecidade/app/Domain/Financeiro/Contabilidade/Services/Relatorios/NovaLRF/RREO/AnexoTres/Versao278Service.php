<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\NovaLRF\RREO\AnexoTres;

use App\Domain\Financeiro\Contabilidade\Builder\RegraCalculoLinhaLrfBuilder;
use App\Domain\Financeiro\Contabilidade\Factories\TemplateFactory;
use App\Domain\Financeiro\Contabilidade\Relatorios\LRF\RREO\XlsAnexoTres;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\NovaLRF\AnexosExecucaoMensalLrfService;
use Exception;
use NcJoes\OfficeConverter\OfficeConverter;

class Versao278Service extends AnexosExecucaoMensalLrfService
{
    protected $sections = [
        'receitas' => [1, 35],
    ];

    /**
     * @var XlsAnexoTres
     */
    protected $parser;

    protected $linhasSimplificado = [29, 30, 32, 33, 34, 35];

    /**
     * @return void
     * @throws Exception
     */
    public function processar()
    {
        $this->processaLinhas();
    }

    /**
     * @throws Exception
     */
    public function processarSimplificado()
    {
        $this->processaLinhas(false, false);

        $linhas = [];
        foreach ($this->linhasSimplificado as $ordem) {
            $linhas[$ordem] = $this->linhas[$ordem];
        }

        return $linhas;
    }

    /**
     * @param boolean $calcularPrevisaoAtualizada
     * @param boolean $organizarLinhas
     * @return void
     * @throws Exception
     */
    protected function processaLinhas($calcularPrevisaoAtualizada = true, $organizarLinhas = true)
    {
        $this->processarFiltros();
        $this->processarLinhasMensais();
        if ($calcularPrevisaoAtualizada) {
            $this->processarPrevisaoAtualizada();
        }
        $this->alteraSinalContas();

        $this->alteraSinalLinha28();
        $this->calculaLinhasManuais();
        $this->totalizaMeses();
        $this->totalizarSomaLinhasMensais(['total_meses', 'previsao_atualizada']);
        $this->totalizarSubtracaoLinhasMensais(['total_meses', 'previsao_atualizada']);
        if ($organizarLinhas) {
            $this->organizaLinhas();
        }
    }

    /**
     * Retorna um array com path dos arquivos emitidos
     * @return array
     * @throws Exception
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
        return 'RREO - Anexo 3';
    }

    public function processarLinhasMensais()
    {
        $estruturais = ['6212', '62132', '62138', '62139'];
        $meses = $this->getMesesProcessar();
        foreach ($meses as $mes) {
            $dadosMsc = $this->executarMscMes($mes, $estruturais);

            $this->calculaIptu($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            $this->calculaIss($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            $this->calculaLinhaItbi($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            $this->calculaLinhaIrrf($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            $this->calculaLinhaOutrosImpostos($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            $this->calculaLinhaContribuicoes($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            $this->calculaLinhaRendimentosAplicacaoFinanceira($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            $this->calculaOutrasReceitasPatrimoniais($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            $this->calculaReceitaAgropecuaria($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            $this->calculaReceitaIndustrial($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            $this->calculaReceitaServico($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            $this->calculaCotaFpm($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            $this->calculaCotaIcms($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            $this->calculaCotaIpvs($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            $this->calculaCotaItr($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            $this->calculaTransferenciaLei61($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            $this->calculaTransferenciaFundeb($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            $this->calculaOutrasTransferenciaCorrentes($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            $this->calculaOutrasReceitasCorrentes($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            $this->calculaContribuicaoServidor($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            $this->calculaCompensacaoFinanceira($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            $this->calculaRendimentosAplicacoes($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);

            //  Dedução de Receita para Formação do FUNDEB
            $mscLinhaDeducao = $this->executarMscMes($mes, ['621310100']);
            $this->calculaDeducaoReceitaFundeb($mscLinhaDeducao, $mes->coluna, self::CALCULAR_PERIODO);

            $this->calculaTransfIndividuais($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            $this->calculaTransfBancarias($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
            $this->calculaTransfUniao($dadosMsc, $mes->coluna, self::CALCULAR_PERIODO);
        }
    }

    protected function processarPrevisaoAtualizada()
    {
        $dadosMsc = $this->executarMsc([52111, 5211202, 5211299, 5212101, 5212102, 5212104, 5212199, 52128, 52129]);
        $previsao = 'previsao_atualizada';
        $this->calculaIptu($dadosMsc, $previsao, self::CALCULAR_SALDO_FINAL);
        $this->calculaIss($dadosMsc, $previsao, self::CALCULAR_SALDO_FINAL);
        $this->calculaLinhaItbi($dadosMsc, $previsao, self::CALCULAR_SALDO_FINAL);
        $this->calculaLinhaIrrf($dadosMsc, $previsao, self::CALCULAR_SALDO_FINAL);
        $this->calculaLinhaOutrosImpostos($dadosMsc, $previsao, self::CALCULAR_SALDO_FINAL);
        $this->calculaLinhaContribuicoes($dadosMsc, $previsao, self::CALCULAR_SALDO_FINAL);
        $this->calculaLinhaRendimentosAplicacaoFinanceira($dadosMsc, $previsao, self::CALCULAR_SALDO_FINAL);
        $this->calculaOutrasReceitasPatrimoniais($dadosMsc, $previsao, self::CALCULAR_SALDO_FINAL);
        $this->calculaReceitaAgropecuaria($dadosMsc, $previsao, self::CALCULAR_SALDO_FINAL);
        $this->calculaReceitaIndustrial($dadosMsc, $previsao, self::CALCULAR_SALDO_FINAL);
        $this->calculaReceitaServico($dadosMsc, $previsao, self::CALCULAR_SALDO_FINAL);
        $this->calculaCotaFpm($dadosMsc, $previsao, self::CALCULAR_SALDO_FINAL);
        $this->calculaCotaIcms($dadosMsc, $previsao, self::CALCULAR_SALDO_FINAL);
        $this->calculaCotaIpvs($dadosMsc, $previsao, self::CALCULAR_SALDO_FINAL);
        $this->calculaCotaItr($dadosMsc, $previsao, self::CALCULAR_SALDO_FINAL);
        $this->calculaTransferenciaLei61($dadosMsc, $previsao, self::CALCULAR_SALDO_FINAL);
        $this->calculaTransferenciaFundeb($dadosMsc, $previsao, self::CALCULAR_SALDO_FINAL);
        $this->calculaOutrasTransferenciaCorrentes($dadosMsc, $previsao, self::CALCULAR_SALDO_FINAL);
        $this->calculaOutrasReceitasCorrentes($dadosMsc, $previsao, self::CALCULAR_SALDO_FINAL);
        $this->calculaContribuicaoServidor($dadosMsc, $previsao, self::CALCULAR_SALDO_FINAL);
        $this->calculaCompensacaoFinanceira($dadosMsc, $previsao, self::CALCULAR_SALDO_FINAL);
        $this->calculaRendimentosAplicacoes($dadosMsc, $previsao, self::CALCULAR_SALDO_FINAL);

        // Dedução de Receita para Formação do FUNDEB
        $mscLinhaDeducao = $this->executarMsc([521120101, 521210301]);
        $this->calculaDeducaoReceitaFundeb($mscLinhaDeducao, $previsao, self::CALCULAR_SALDO_FINAL);

        $this->calculaTransfIndividuais($dadosMsc, $previsao, self::CALCULAR_SALDO_FINAL);
        $this->calculaTransfBancarias($dadosMsc, $previsao, self::CALCULAR_SALDO_FINAL);
        $this->calculaTransfUniao($dadosMsc, $previsao, self::CALCULAR_SALDO_FINAL);
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
                && $valorManual['c180_coluna'] === 'previsao_atualizada';
        })->each(function ($valorManual) {
            $this->linhas[$valorManual['c180_linha']]->previsao_atualizada += $valorManual['c180_valor'];
        });
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
     * Linha: 3 - IPTU
     * NR começada por lista: 111250
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula se deve totalizar a coluna de período ou a previsão atualizada
     * @return void
     * @throws Exception
     */
    protected function calculaIptu(array $dadosMsc, $coluna, $formula)
    {
        $linha = $this->linhas[3];

        $contas = [111250];
        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nr')
            ->addContas($contas, false)
            ->build();


        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 4 - ISS
     * NR começada por lista: 1114511,1114512
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula se deve totalizar a coluna de período ou a previsão atualizada
     * @return void
     */
    protected function calculaIss(array $dadosMsc, $coluna, $formula)
    {

        $linha = $this->linhas[4];

        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nr')
            ->addContas([1114511, 1114512], false)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 5 - Itbi
     * NR começada por lista: 111253
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula se deve totalizar a coluna de período ou a previsão atualizada
     * @return void
     */
    protected function calculaLinhaItbi(array $dadosMsc, $coluna, $formula)
    {

        $linha = $this->linhas[5];


        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nr')
            ->addContas([111253], false)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 6 - Irrf
     * NR começada por lista: 111303
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula se deve totalizar a coluna de período ou a previsão atualizada
     * @return void
     */
    protected function calculaLinhaIrrf(array $dadosMsc, $coluna, $formula)
    {

        $linha = $this->linhas[6];

        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nr')
            ->addContas([111303], false)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 7 - Outros Impostos, Taxas e Contribuições de Melhoria
     * NR começada por lista: 11 ; EXCETO NR começa por lista: 111250,1114511,1114512,111253,111303
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula se deve totalizar a coluna de período ou a previsão atualizada
     * @return void
     */
    protected function calculaLinhaOutrosImpostos(array $dadosMsc, $coluna, $formula)
    {
        $linha = $this->linhas[7];

        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nr')
            ->addContas([11], false)
            ->addContas([111250, 1114511, 1114512, 111253, 111303], true)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 8 - Contribuições
     * NR começada por lista: 12
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula se deve totalizar a coluna de período ou a previsão atualizada
     */
    protected function calculaLinhaContribuicoes(array $dadosMsc, $coluna, $formula)
    {
        $linha = $this->linhas[8];

        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nr')
            ->addContas([12], false)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 10 - Rendimentos de Aplicação Financeira
     * NR começada por lista: 132101,132102,132103,132104,132105,132999
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula se deve totalizar a coluna de período ou a previsão atualizada
     */
    protected function calculaLinhaRendimentosAplicacaoFinanceira(
        array $dadosMsc,
        $coluna,
        $formula
    ) {
        $linha = $this->linhas[10];

        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nr')
            ->addContas([132101, 132102, 132103, 132104, 132105, 132999], false)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }


    /**
     * Linha: 11 - Outras Receitas Patrimoniais
     * NR começada por lista: 13 ; EXCETO NR começa por lista: 132101,132102,132103,132104,132105,132999
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula se deve totalizar a coluna de período ou a previsão atualizada
     */
    protected function calculaOutrasReceitasPatrimoniais(array $dadosMsc, $coluna, $formula)
    {
        $linha = $this->linhas[11];
        $naoContem = ['132101', '132102', '132103', '132104', '132105', '132999'];

        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nr')
            ->addContas([13], false)
            ->addContas($naoContem, true)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 12 - Receita Agropecuária
     * NR começada por lista: 14
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula se deve totalizar a coluna de período ou a previsão atualizada
     * @return void
     */
    protected function calculaReceitaAgropecuaria(array $dadosMsc, $coluna, $formula)
    {
        $linha = $this->linhas[12];

        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nr')
            ->addContas([14], false)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 13 - Receita Industrial
     * NR começada por lista: 15
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula se deve totalizar a coluna de período ou a previsão atualizada
     */
    protected function calculaReceitaIndustrial(array $dadosMsc, $coluna, $formula)
    {
        $linha = $this->linhas[13];

        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nr')
            ->addContas([15], false)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 14 - Receita de Serviços
     * NR começada por lista: 16
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula se deve totalizar a coluna de período ou a previsão atualizada
     */
    protected function calculaReceitaServico(array $dadosMsc, $coluna, $formula)
    {
        $linha = $this->linhas[14];

        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nr')
            ->addContas([16], false)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 16 - Cota-Parte do FPM
     * NR começada por lista: 1711511,1711512
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula se deve totalizar a coluna de período ou a previsão atualizada
     */
    protected function calculaCotaFpm(array $dadosMsc, $coluna, $formula)
    {
        $linha = $this->linhas[16];

        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nr')
            ->addContas([1711511, 1711512], false)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 17 - Cota-Parte do ICMS
     * NR começada por lista: 172150
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula se deve totalizar a coluna de período ou a previsão atualizada
     */
    protected function calculaCotaIcms(array $dadosMsc, $coluna, $formula)
    {
        $linha = $this->linhas[17];

        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nr')
            ->addContas([172150], false)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 18 - Cota-Parte do IPVA
     * NR começada por lista: 172151
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula se deve totalizar a coluna de período ou a previsão atualizada
     */
    protected function calculaCotaIpvs(array $dadosMsc, $coluna, $formula)
    {
        $linha = $this->linhas[18];

        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nr')
            ->addContas([172151], false)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 19 - Cota-Parte do ITR
     * NR começada por lista: 171152
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula se deve totalizar a coluna de período ou a previsão atualizada
     */
    protected function calculaCotaItr(array $dadosMsc, $coluna, $formula)
    {
        $linha = $this->linhas[19];


        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nr')
            ->addContas([171152], false)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 20 - Transferências da LC 61/1989
     * NR começada por lista: 172152
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula se deve totalizar a coluna de período ou a previsão atualizada
     */
    protected function calculaTransferenciaLei61(array $dadosMsc, $coluna, $formula)
    {
        $linha = $this->linhas[20];

        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nr')
            ->addContas([172152], false)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 21 - Transferências do FUNDEB
     * NR começada por lista: 175150,1715
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula se deve totalizar a coluna de período ou a previsão atualizada
     */
    protected function calculaTransferenciaFundeb(array $dadosMsc, $coluna, $formula)
    {
        $linha = $this->linhas[21];

        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nr')
            ->addContas([175150, 1715], false)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 22 - Outras Transferências Correntes
     * NR começada por lista: 17 ; EXCETO NR começa por lista: 1711511,1711512,172150,172151,171152,172152,175150,1715
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula se deve totalizar a coluna de período ou a previsão atualizada
     */
    protected function calculaOutrasTransferenciaCorrentes(array $dadosMsc, $coluna, $formula)
    {
        $linha = $this->linhas[22];

        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nr')
            ->addContas([17], false)
            ->addContas([1711511, 1711512, 172150, 172151, 171152, 172152, 175150, 1715], true)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 23 - Outras Receitas Correntes
     * NR começada por lista: 19
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula se deve totalizar a coluna de período ou a previsão atualizada
     * @return void
     */
    protected function calculaOutrasReceitasCorrentes(array $dadosMsc, $coluna, $formula)
    {
        $linha = $this->linhas[23];

        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nr')
            ->addContas([19], false)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 25 - Contrib. do Servidor para o Plano de Previdência
     * NR começada por lista: 121501,121502,121503,121550,121551
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula se deve totalizar a coluna de período ou a previsão atualizada
     * @return void
     */
    protected function calculaContribuicaoServidor(array $dadosMsc, $coluna, $formula)
    {
        $linha = $this->linhas[25];

        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nr')
            ->addContas([121501, 121502, 121503, 121550, 121551], false)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 26 - Compensação Financ. entre Regimes Previdência
     * NR começada por lista: 199903
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula se deve totalizar a coluna de período ou a previsão atualizada
     * @return void
     */
    protected function calculaCompensacaoFinanceira(array $dadosMsc, $coluna, $formula)
    {
        $linha = $this->linhas[26];

        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nr')
            ->addContas([199903], false)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 27 - Rendimentos de Aplicações de Recursos Previdenciários
     * NR começada por lista: 132104
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula se deve totalizar a coluna de período ou a previsão atualizada
     * @return void
     */
    protected function calculaRendimentosAplicacoes(array $dadosMsc, $coluna, $formula)
    {
        $linha = $this->linhas[27];

        $regras = (new RegraCalculoLinhaLrfBuilder())
            ->natureza('nr')
            ->addContas([132104], false)
            ->build();

        $this->calculaColunaStrPos($linha, $regras, $dadosMsc, $coluna, $formula);
    }

    /**
     * Linha: 28 - Dedução de Receita para Formação do FUNDEB
     * Calcula: TODA Conta contábil 6.2.1.3.1.01.00
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula se deve totalizar a coluna de período ou a previsão atualizada
     * @return void
     */
    protected function calculaDeducaoReceitaFundeb(array $dadosMsc, $coluna, $formula)
    {
        $linha = $this->linhas[28];
        foreach ($dadosMsc as $msc) {
            if ($formula == 4) {
                $linha->{$coluna} += $msc->saldo_debito + $msc->saldo_credito;
            } else {
                $linha->{$coluna} += $msc->saldo_final;
            }
        }
    }

    /**
     * Linha: 30 - (-) Transferências obrigatórias da União relativas às emendas individuais...
     * NR começada por 171; CO=3110
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula se deve totalizar a coluna de período ou a previsão atualizada
     * @return void
     */
    protected function calculaTransfIndividuais(array $dadosMsc, $coluna, $formula)
    {
        $linha = $this->linhas[30];
        $ateNivel = '171';
        foreach ($dadosMsc as $msc) {
            if (strpos($msc->nr, $ateNivel) === 0 && $msc->complemento == 3110) {
                if ($formula == 4) {
                    $linha->{$coluna} += $msc->saldo_debito + $msc->saldo_credito;
                } else {
                    $linha->{$coluna} += $msc->saldo_final;
                }
            }
        }
    }

    /**
     * Linha: 32 - (-) Transferências obrigatórias da União relativas às emendas de bancada
     * NR começada por 171; CO=3120; TODAS AS FR EXCETO FR: 604
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula se deve totalizar a coluna de período ou a previsão atualizada
     * @return void
     */
    protected function calculaTransfBancarias(array $dadosMsc, $coluna, $formula)
    {
        $linha = $this->linhas[32];
        $ateNivel = '171';
        foreach ($dadosMsc as $msc) {
            $siconfi = (int)substr($msc->siconfi, 1);
            if (strpos($msc->nr, $ateNivel) === 0 && $msc->complemento == 3120 && $siconfi !== 604) {
                if ($formula == 4) {
                    $linha->{$coluna} += $msc->saldo_debito + $msc->saldo_credito;
                } else {
                    $linha->{$coluna} += $msc->saldo_final;
                }
            }
        }
    }

    /**
     * Linha: 33 - (-) Transferências da União relativas à remuneração dos agentes comunitários de saúde e de
     * combate às endemias
     * NR começada por 171; FR 604
     * @param array $dadosMsc
     * @param string $coluna nome da coluna que deve somar
     * @param integer $formula se deve totalizar a coluna de período ou a previsão atualizada
     * @return void
     */
    protected function calculaTransfUniao(array $dadosMsc, $coluna, $formula)
    {
        $linha = $this->linhas[33];
        $ateNivel = '171';
        foreach ($dadosMsc as $msc) {
            $siconfi = (int)substr($msc->siconfi, 1);
            if (strpos($msc->nr, $ateNivel) === 0 && $siconfi === 604) {
                if ($formula == 4) {
                    $linha->{$coluna} += $msc->saldo_debito + $msc->saldo_credito;
                } else {
                    $linha->{$coluna} += $msc->saldo_final;
                }
            }
        }
    }

    /**
     * @throws Exception
     */
    protected function carregarParserXls()
    {
        $template = $this->carregarTemplate(TemplateFactory::MODELO_MDF);
        $this->parser = new XlsAnexoTres($template);
    }

    /**
     * Ajusta o sinal das contas credoras para apresentação e calculo dos valores
     * @return void
     */
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

    /**
     * Por se tratar de uma linha que só apresenta deduções
     * a linha 28 Dedução de Receita para Formação do FUNDEB
     * Embora seja uma receita ela estará Devedora e para apresentação NESSE relatório, deve-se apresentar
     * Devedora como positivo e Credora como Negativo.
     *
     * @return void
     */
    protected function alteraSinalLinha28()
    {
        $mesesProcessar = $this->getMesesProcessar();
        $linha = $this->linhas[28];

        foreach ($mesesProcessar as $mes) {
            $valor = $linha->{$mes->coluna};
            $linha->{$mes->coluna} = $valor * -1;
        }

        $linha->total_meses *= -1;
        $linha->previsao_atualizada *= -1;
    }
}
