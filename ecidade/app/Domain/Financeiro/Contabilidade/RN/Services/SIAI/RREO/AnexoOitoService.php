<?php
namespace App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\RREO;

use App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\AnexosService;
use App\Domain\Financeiro\Contabilidade\Factories\AnexoOitoFactory;
use stdClass;

class AnexoOitoService extends AnexosService
{

    const SIGLA = "A11";
    /*
     * RECEITA RESULTANTE DE IMPOSTO /
     * RECEITAS RECEBIDAS DO FUNDEB /
     * RECEITAS ADICIONAIS PARA FINANCIAMENTO DO ENSINO
     */
    const RECEITAS = 1;

    /*
     * RECURSOS RECEBIDOS EM EXERCÍCIOS ANTERIORES /
     * APURAÇÃO DAS DESPESAS PARA FINS DE LIMITE MÍNIMO CONSTITUCIONAL
     */
    const RECEITAS_ANTERIORES = 2;

    /*
     * DESPESAS COM RECURSOS DO FUNDEB /
     * DESPESAS CUSTEADAS COM RECEITAS DO FUNDEB /
     * DESPESAS COMAÇÕES TÍPICAS DO MDE /
     * DESPESAS CUSTEADAS COM RECEITAS ADICIONAIS /
     * TOTAL GERAL DAS DESPESAS COM EDUCAÇÃO
     */
    const DESPESAS_FUNDEB = 3;

    /*
     * CONTROLE DA DISPONIBILIDADE FINANCEIRA E CONCILIAÇÃO BANCÁRIA
     */
    const DISPONIBILIDADE_FINANCEIRA = 4;

    /*
     * INDICADORES - ART. 2121-A - CONSTITUIÇÃO FEDERAL /
     * INDICADOR - ART.25, § 3º - LEI Nº 14.113, DE 2020 - (MÁXIMO DE 10% DE SUPERÁVIT) /
     * APURAÇÃO DO LIMITE MÍNIMO CONSTITUCIONAL
     */
    const APURACAO_LIMITE_CONSTITUCIONAL = 5;

    /*
     * INDICADOR - ART.25, § 3º - LEI Nº 14.113, DE 2020 - (APLICAÇÃO DO SUPERÁVIT DE EXERCÍCIO ANTERIOR)
     */
    const SUPERAVIT_EXERCICIO_ANTERIOR = 6;

    /*
     * RESTOS A PAGAR INSCRITOS EM EXERCÍCIOS ANTERIORES COM DISPONIBILIDADE FINANCEIRA
     * DE RECURSOS DE IMPOSTOS E DO FUNDEB
     */
    const RESTOS_PAGAR = 7;

    protected $dePara = [
        self::RECEITAS => [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
            6 => 6,
            7 => 7,
            8 => 8,
            9 => 9,
            10 => 10,
            11 => 11,
            12 => 12,
            13 => 13,
            14 => 14,
            15 => 15,
            16 => 16,
            17 => 17,
            18 => 18,
            19 => 19,
            20 => 20,
            21 => 21,
            22 => 22,
            23 => 23,
            24 => 24,
            25 => 25,
            26 => 26,
            27 => 27,
            28 => 28,
            29 => 29,
            30 => 30,
            31 => 31,
            32 => 32,
            33 => 33,
            34 => 34,
            35 => 35,
            36 => 36,
            96 => 96,
            97 => 97,
            98 => 98,
            99 => 99,
            100 => 100,
            101 => 101,
            102 => 102,
            103 => 103,
            104 => 104,
            105 => 105,
            106 => 106
        ],
        self::RECEITAS_ANTERIORES => [
            37 => 37,
            38 => 38,
            39 => 39,
            40 => 40,
            84 => 84,
            85 => 85,
            86 => 86,
            87 => 87,
            88 => 88,
            89 => 89,
            90 => 90
        ],
        self::DESPESAS_FUNDEB => [
            41 => 41,
            42 => 42,
            43 => 43,
            44 => 44,
            45 => 45,
            46 => 46,
            47 => 47,
            48 => 48,
            49 => 49,
            50 => 50,
            51 => 51,
            52 => 52,
            53 => 53,
            54 => 54,
            55 => 55,
            56 => 56,
            57 => 57,
            58 => 58,
            59 => 59,
            60 => 60,
            61 => 61,
            62 => 62,
            63 => 63,
            71 => 71,
            72 => 72,
            73 => 73,
            74 => 74,
            75 => 75,
            76 => 76,
            77 => 77,
            78 => 78,
            79 => 79,
            80 => 80,
            81 => 81,
            82 => 82,
            83 => 83,
            107 => 107,
            108 => 108,
            109 => 109,
            110 => 110,
            111 => 111,
            112 => 112,
            113 => 113,
            114 => 114,
            115 => 115,
            116 => 116,
            117 => 117,
            118 => 118,
            119 => 119,
            120 => 120,
            121 => 121,
            122 => 122,
            123 => 123,
            124 => 124
        ],
        self::DISPONIBILIDADE_FINANCEIRA => [
            125 => 125,
            126 => 126,
            127 => 127,
            128 => 128,
            129 => 129,
            130 => 130,
            131 => 131
        ],
        self::APURACAO_LIMITE_CONSTITUCIONAL => [
            64 => 64,
            65 => 65,
            66 => 66,
            67 => 67,
            91 => 91
        ],
        self::SUPERAVIT_EXERCICIO_ANTERIOR => [
            68 => 68,
            69 => 69,
            70 => 70
        ],
        self::RESTOS_PAGAR => [
            92 => 92,
            93 => 93,
            94 => 94,
            95 => 95
        ]
    ];

    public function __construct($exercicio, $filtros)
    {
        $relatorio = AnexoOitoFactory::getService($exercicio, $filtros);
        $this->linhasProcessadas = $relatorio->getLinhasProcessadas();
        $this->calcularLinhasEspecificas();
        $this->exercicio = $exercicio;
        $this->periodo = $relatorio->getPeriodo();
        $this->codigoOrgao = $filtros['codigoOrgaoTCE'];
        $this->nomeOrgao = $filtros['nomeOrgaoTCE'];
        $this->nomeArquivo = self::SIGLA."_{$exercicio}" . str_pad($this->periodo->getOrdem(), 2, "0", STR_PAD_LEFT);
        $this->setFilePath("tmp/{$this->nomeArquivo}.TXT");
    }

    public function makeHeader()
    {
        $header = new stdClass();
        $header->tipoRegistro = "0";
        $header->nomeArquivo = $this->nomeArquivo;
        $header->bimReferencia = explode("_", $this->nomeArquivo)[1];
        $header->tipoArquivo = "O";
        $header->dataGeracaoArq = date("d/m/Y");
        $header->horaGeracaoArq = date("H:i:s");
        $header->codigoOrgao = str_pad($this->codigoOrgao, 4, " ", STR_PAD_LEFT);
        $header->nomeOrgao = str_pad(substr($this->nomeOrgao, 0, 100), 100, " ");
        $header->brancos = str_repeat(" ", 19);
        $header->numRegistroLido = $this->getQtdLinhas();
        $this->setHeader($header);
    }

    public function makeDetalhes()
    {
        $this->makeDetalhe1();
        $this->makeDetalhe2();
        $this->makeDetalhe3();
        $this->makeDetalhe4();
        $this->makeDetalhe5();
        $this->makeDetalhe6();
        $this->makeDetalhe7();
    }
    
    public function makeTrailler()
    {
        $trailler = new stdClass();
        $trailler->tipoRegistro = "10";
        $trailler->brancos = str_repeat(" ", 157);
        $trailler->numRegistroLido = $this->getQtdLinhas();
        $this->setTrailler($trailler);
    }
    
    /*
     * Calculo de linhas especificas
     */
    private function calcularLinhasEspecificas()
    {
        $this->calcularLinha18();
        $this->calcularLinha40();
        $this->calcularLinha67();
        $this->calcularLinha68();
        $this->calcularLinha84();
        $this->calcularLinha85();
        $this->calcularLinha86();
        $this->calcularLinha90();
        $this->calcularLinha91();
        $this->calcularLinha116();
        $this->calcularLinha128();
        $this->calcularLinha131();
        $this->calcularSaldoFinalRP();
    }
    
    /*
     * Detalhe 1
     * 5- VALOR MINIMO A SER APLICADO ALEM DO VALOR DESTINADO AO FUNDEB
     * - 5% DE ((2.1.1) + (2.2) + (2.3) + (2.4) + (2.5)) + 25% DE ((1.1)
     */
    private function calcularLinha18()
    {
        /*
         * Previsão atualizada
         */
        $valor1 = ( ( $this->linhasProcessadas[8]->previsao_atualizada
            + $this->linhasProcessadas[10]->previsao_atualizada
            + $this->linhasProcessadas[11]->previsao_atualizada
            + $this->linhasProcessadas[12]->previsao_atualizada
            + $this->linhasProcessadas[13]->previsao_atualizada
            ) * 0.05 );
        $valor2 = ( ( $this->linhasProcessadas[1]->previsao_atualizada
            + $this->linhasProcessadas[9]->previsao_atualizada
            + $this->linhasProcessadas[14]->previsao_atualizada
            + $this->linhasProcessadas[15]->previsao_atualizada
            ) * 0.25 );
        $this->linhasProcessadas[18]->previsao_atualizada = $valor1+$valor2;
        
        /*
         * Arrecadado Acumulado
         */
        $valor1 = ( ( $this->linhasProcessadas[8]->arrecadado_acumulado
            + $this->linhasProcessadas[10]->arrecadado_acumulado
            + $this->linhasProcessadas[11]->arrecadado_acumulado
            + $this->linhasProcessadas[12]->arrecadado_acumulado
            + $this->linhasProcessadas[13]->arrecadado_acumulado
            ) * 0.05 );
        $valor2 = ( ( $this->linhasProcessadas[1]->arrecadado_acumulado
            + $this->linhasProcessadas[9]->arrecadado_acumulado
            + $this->linhasProcessadas[14]->arrecadado_acumulado
            + $this->linhasProcessadas[15]->arrecadado_acumulado
            ) * 0.25 );
        $this->linhasProcessadas[18]->arrecadado_acumulado = $valor1+$valor2;
    }
    
    /*
     * Detalhe 2
     * 9- TOTAL DOS RECURSOS DO FUNDEB DISPONIVEIS PARA UTILIZACAO (6+8)
     */
    private function calcularLinha40()
    {
        $this->linhasProcessadas[40]->valor = $this->linhasProcessadas[19]->arrecadado_acumulado +
        $this->linhasProcessadas[37]->valor;
    }
    
    /*
     * Detalhe 5
     * 18- TOTAL DA RECEITA RECEBIDA E NÃO APLICADA NO EXERCÍCIO
     */
    private function calcularLinha67()
    {
        $vlrNaoAplicado = round($this->linhasProcessadas[19]->arrecadado_acumulado -
        $this->linhasProcessadas[56]->liquidado_acumulado, 2);

        $vlrNaoAplicadoAposAjuste = $vlrNaoAplicado;

        $vlrPercentualNaoAplicado = round(($vlrNaoAplicado/$this->linhasProcessadas[19]->arrecadado_acumulado)*100, 2);

        $vlrNaoAplicadoExcedente = 0;
        if (($vlrNaoAplicadoAposAjuste - $this->linhasProcessadas[67]->valor_maximo) > 0) {
            $vlrNaoAplicadoExcedente = round($vlrNaoAplicadoAposAjuste - $this->linhasProcessadas[67]->valor_maximo, 2);
        }

        $this->linhasProcessadas[67]->valor_exigido = $this->linhasProcessadas[67]->valor_maximo;
        $this->linhasProcessadas[67]->valor_aplicado = $vlrNaoAplicado;
        $this->linhasProcessadas[67]->valor_considerado = $vlrNaoAplicadoAposAjuste;
        $this->linhasProcessadas[67]->valor_nao_aplicado_excedente = $vlrNaoAplicadoExcedente;
        $this->linhasProcessadas[67]->percentual_aplicado = $vlrPercentualNaoAplicado;
    }
    /*
     * Detalhe 6
     * 19- TOTAL DAS DESPESAS CUSTEADAS COM SUPERAVIT DO FUNDEB
     */
    private function calcularLinha68()
    {
        $this->linhasProcessadas[68]->vlr_superavit_ex_ant = $this->linhasProcessadas[69]->vlr_superavit_ex_ant
        + $this->linhasProcessadas[70]->vlr_superavit_ex_ant;
    }
    
    /*
     * Detalhe 2
     * 22- TOTAL DAS DESPESAS DE MDE CUSTEADAS COM RECURSOS DE IMPOSTOS = L20(d ou e)
     */
    private function calcularLinha84()
    {
        $this->linhasProcessadas[84]->valor = $this->linhasProcessadas[71]->liquidado_acumulado;
    }
        
    /*
     * Detalhe 2
     * 23- TOTAL DAS RECEITAS TRANSFERIDAS AO FUNDEB = (L4)
     */
    private function calcularLinha85()
    {
        $this->linhasProcessadas[85]->valor = $this->linhasProcessadas[17]->arrecadado_acumulado;
    }
    
    /* Detalhe 2
     * 24- (-) RECEITAS DO FUNDEB NÃO UTILIZADAS NO EXERCÍCIO, EM V
     */
    private function calcularLinha86()
    {
        $this->calcularLinha67();
        $this->linhasProcessadas[86]->valor = $this->linhasProcessadas[67]->valor_nao_aplicado_excedente;
    }
    
    /*
     * Detalhe 2
     * 28- TOTAL DAS DESPESAS PARA FINS DE LIMITE  (22 + 23) -  (24 + 25 + 26 + 27)
     */
    private function calcularLinha90()
    {
        $valor = ($this->linhasProcessadas[84]->valor + $this->linhasProcessadas[85]->valor)
         -  (   $this->linhasProcessadas[86]->valor
              + $this->linhasProcessadas[87]->valor
              + $this->linhasProcessadas[88]->valor
              + $this->linhasProcessadas[89]->valor);
        $this->linhasProcessadas[90]->valor = $valor;
    }
    /*
     * Detalhe 5
     * 29- APLICAÇÃO EM MDE SOBRE A RECEITA RESULTANTE DE IMPOSTOS
     */
    private function calcularLinha91()
    {
        $this->linhasProcessadas[91]->valor_exigido = round(
            ($this->linhasProcessadas[16]->arrecadado_acumulado * 0.25),
            2
        );
        $this->linhasProcessadas[91]->valor_aplicado = $this->linhasProcessadas[90]->valor;
        $this->linhasProcessadas[91]->valor_considerado = 0;
        $this->linhasProcessadas[91]->percentual_aplicado = round(
            (($this->linhasProcessadas[90]->valor /
            $this->linhasProcessadas[16]->arrecadado_acumulado)*100),
            2
        );
        $this->linhasProcessadas[91]->percentual = 0;
    }
    
    /*
     * 33- TOTAL GERAL DAS DESPESAS COM EDUCAÇÃO (10 + 20 + 32)
     */
    private function calcularLinha116()
    {
        $this->linhasProcessadas[116]->total_creditos = $this->linhasProcessadas[41]->total_creditos
        +$this->linhasProcessadas[71]->total_creditos
        +$this->linhasProcessadas[107]->total_creditos;
        
        $this->linhasProcessadas[116]->empenhado_liquido_acumulado =
        $this->linhasProcessadas[41]->empenhado_liquido_acumulado
        +$this->linhasProcessadas[71]->empenhado_liquido_acumulado
        +$this->linhasProcessadas[107]->empenhado_liquido_acumulado;

        $this->linhasProcessadas[116]->liquidado_acumulado = $this->linhasProcessadas[41]->liquidado_acumulado
        +$this->linhasProcessadas[71]->liquidado_acumulado
        +$this->linhasProcessadas[107]->liquidado_acumulado;

        $this->linhasProcessadas[116]->pago_acumulado = $this->linhasProcessadas[41]->pago_acumulado
        +$this->linhasProcessadas[71]->pago_acumulado
        +$this->linhasProcessadas[107]->pago_acumulado;
        
        $this->linhasProcessadas[116]->a_liquidar = $this->linhasProcessadas[41]->a_liquidar
        + $this->linhasProcessadas[71]->a_liquidar
        + $this->linhasProcessadas[107]->a_liquidar ;
    }

    /*
     * Detalhe 4
     * 37- (=) DISPONIBILIDADE FINANCEIRA ATE O BIMESTRE
     */
    private function calcularLinha128()
    {
        $this->linhasProcessadas[128]->valor_fundeb = $this->linhasProcessadas[125]->valor_fundeb
        + $this->linhasProcessadas[126]->valor_fundeb
        - $this->linhasProcessadas[127]->valor_fundeb;
        
        $this->linhasProcessadas[128]->valor_salario_educacao = $this->linhasProcessadas[125]->valor_salario_educacao
        + $this->linhasProcessadas[126]->valor_salario_educacao
        - $this->linhasProcessadas[127]->valor_salario_educacao;
    }
    
    /*
     * Detalhe 4
     * 40- (=) SALDO FINANCEIRO CONCILIADO (Saldo Bancário)
     */
    private function calcularLinha131()
    {
        $this->linhasProcessadas[131]->valor_fundeb = $this->linhasProcessadas[128]->valor_fundeb
        + $this->linhasProcessadas[129]->valor_fundeb
        - $this->linhasProcessadas[130]->valor_fundeb;
        
        $this->linhasProcessadas[131]->valor_salario_educacao = $this->linhasProcessadas[128]->valor_salario_educacao
        + $this->linhasProcessadas[129]->valor_salario_educacao
        - $this->linhasProcessadas[130]->valor_salario_educacao;
    }
    
    /*
     * Detalhe 6
     * Calculo das colunas do saldo final
     */
    private function calcularSaldoFinalRP()
    {
        $this->linhasProcessadas[92]->rp_saldo_final = $this->linhasProcessadas[92]->inscricao_total_rp
        - $this->linhasProcessadas[92]->total_pagamentos_rp
        - $this->linhasProcessadas[92]->total_anulacoes;
        
        $this->linhasProcessadas[93]->rp_saldo_final = $this->linhasProcessadas[93]->inscricao_total_rp
        - $this->linhasProcessadas[93]->total_pagamentos_rp
        - $this->linhasProcessadas[93]->total_anulacoes;
        
        $this->linhasProcessadas[94]->rp_saldo_final = $this->linhasProcessadas[94]->inscricao_total_rp
        - $this->linhasProcessadas[94]->total_pagamentos_rp
        - $this->linhasProcessadas[94]->total_anulacoes;
        
        $this->linhasProcessadas[95]->rp_saldo_final = $this->linhasProcessadas[95]->inscricao_total_rp
        - $this->linhasProcessadas[95]->total_pagamentos_rp
        - $this->linhasProcessadas[95]->total_anulacoes;
    }
    
    private function makeDetalhe1()
    {
        $tipoRegistro = self::RECEITAS;
        foreach ($this->linhasProcessadas as $linha) {
            if (!$this->utilizaLinhaPorDetalhe($linha->linha, $tipoRegistro, $this->dePara)) {
                continue;
            }
            $detalhe = new stdClass();
            $detalhe->TipoRegistro = $tipoRegistro;
            $detalhe->Brancos1 = " ";
            $detalhe->Campo = $this->getCodigoCampo($linha->linha, $this->dePara, 3);
            $detalhe->Brancos2 = " ";
            $detalhe->PrevisaoAtualizada = $this->formataValor($linha->previsao_atualizada);
            $detalhe->ReceitasRealizadasAteoBimestre = $this->formataValor($linha->arrecadado_acumulado);
            $detalhe->Brancos3 = str_repeat(" ", 125);
            $detalhe->NumRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }

    private function makeDetalhe2()
    {
        $tipoRegistro = self::RECEITAS_ANTERIORES;
        foreach ($this->linhasProcessadas as $linha) {
            if (!$this->utilizaLinhaPorDetalhe($linha->linha, $tipoRegistro, $this->dePara)) {
                continue;
            }
            $detalhe = new stdClass();
            $detalhe->TipoRegistro = $tipoRegistro;
            $detalhe->Brancos1 = " ";
            $detalhe->Campo = $this->getCodigoCampo($linha->linha, $this->dePara, 3);
            $detalhe->Brancos2 = " ";
            $detalhe->Valor = $this->formataValor($linha->valor);
            $detalhe->Brancos3 = str_repeat(" ", 139);
            $detalhe->NumRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }

    private function makeDetalhe3()
    {
        /*
         * @todo verificar RPNPSemDisponibilidade - apenas utilizado no sexto bimestre
         * @todo DespesasEmpenhadasSuperiorTotalReceitas - aguardando difinicao: nao existe no relatorio
         */
        $tipoRegistro = self::DESPESAS_FUNDEB;
        foreach ($this->linhasProcessadas as $linha) {
            if (!$this->utilizaLinhaPorDetalhe($linha->linha, $tipoRegistro, $this->dePara)) {
                continue;
            }

            $detalhe = new stdClass();
            $detalhe->TipoRegistro = $tipoRegistro;
            $detalhe->Brancos1 = " ";
            $detalhe->Campo = $this->getCodigoCampo($linha->linha, $this->dePara, 3);
            $detalhe->Brancos2 = " ";
            $detalhe->DotacaoAtualizada = $this->formataValor((isset($linha->total_creditos)?$linha->total_creditos:0));
            $detalhe->DespesasEmpenhadasAteoBimestre = $this->formataValor($linha->empenhado_liquido_acumulado);
            $detalhe->DespesasLiquidadasAteoBimestre = $this->formataValor($linha->liquidado_acumulado);
            $detalhe->DespesasPagasAteoBimestre = $this->formataValor($linha->pago_acumulado);
            $detalhe->RPNP = $this->formataValor($linha->a_liquidar);
            $detalhe->RPNPSemDisponibilidade = $this->formataValor(0);
            $detalhe->DespesasEmpenhadasSuperiorTotalReceitas = $this->formataValor(
                isset($linha->deficit_fundeb)?$linha->deficit_fundeb:0
            );
            $detalhe->Brancos3 = str_repeat(" ", 55);
            $detalhe->NumRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }

    private function makeDetalhe4()
    {
        $tipoRegistro = self::DISPONIBILIDADE_FINANCEIRA;
        foreach ($this->linhasProcessadas as $linha) {
            if (!$this->utilizaLinhaPorDetalhe($linha->linha, $tipoRegistro, $this->dePara)) {
                continue;
            }
            $detalhe = new stdClass();
            $detalhe->TipoRegistro = $tipoRegistro;
            $detalhe->Brancos1 = " ";
            $detalhe->Campo = $this->getCodigoCampo($linha->linha, $this->dePara, 3);
            $detalhe->Brancos2 = " ";
            $detalhe->FUNDEB = $this->formataValor($linha->valor_fundeb);
            $detalhe->SalarioEducacao = $this->formataValor($linha->valor_salario_educacao);
            $detalhe->Brancos3 = str_repeat(" ", 125);
            $detalhe->NumRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }

    private function makeDetalhe5()
    {
        /*
         * @todo ValorNaoAplicadoExcedenteMaximoPermitido aguardando definicao: nao existe no relatorio
         */
        $tipoRegistro = self::APURACAO_LIMITE_CONSTITUCIONAL;
        foreach ($this->linhasProcessadas as $linha) {
            if (!$this->utilizaLinhaPorDetalhe($linha->linha, $tipoRegistro, $this->dePara)) {
                continue;
            }
            $detalhe = new stdClass();
            $detalhe->TipoRegistro = $tipoRegistro;
            $detalhe->Brancos1 = " ";
            $detalhe->Campo = $this->getCodigoCampo($linha->linha, $this->dePara, 3);
            $detalhe->Brancos2 = " ";
            $detalhe->ValorExigidoMaximoPermitido = $this->formataValor($linha->valor_exigido);
            $detalhe->ValorAplicadoNaoAplicado = $this->formataValor($linha->valor_aplicado);
            $detalhe->ValorConsideradoAposDeducoesAjuste = $this->formataValor($linha->valor_considerado);
            $detalhe->PercentualAplicadoNaoAplicado = $this->formataValor($linha->percentual_aplicado);
            $detalhe->ValorNaoAplicadoExcedenteMaximoPermitido = $this->formataValor(
                (isset($linha->valor_nao_aplicado_excedente)?$linha->valor_nao_aplicado_excedente:0)
            );
            $detalhe->Brancos3 = str_repeat(" ", 83);
            $detalhe->NumRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }

    private function makeDetalhe6()
    {
        $tipoRegistro = self::SUPERAVIT_EXERCICIO_ANTERIOR;
        foreach ($this->linhasProcessadas as $linha) {
            if (!$this->utilizaLinhaPorDetalhe($linha->linha, $tipoRegistro, $this->dePara)) {
                continue;
            }

            $detalhe = new stdClass();
            $detalhe->TipoRegistro = $tipoRegistro;
            $detalhe->Brancos1 = " ";
            $detalhe->Campo = $this->getCodigoCampo($linha->linha, $this->dePara, 3);
            $detalhe->Brancos2 = " ";
            $detalhe->ValorSuperavitPermitido = $this->formataValor($linha->vlr_superavit_ex_ant); //5
            $detalhe->ValorNaoAplicadoExercicioAnterior = $this->formataValor($linha->vlr_naplic_ex_ant); //6
            $detalhe->ValorSuperavitAplicado = $this->formataValor($linha->superavit_aplic_1quadr); //7
            $detalhe->ValorAplicadoAposQuadrimestre = $this->formataValor($linha->aplic_apos_1q); //9
            $detalhe->ValorSuperavitNaoAplicado = $this->formataValor($linha->vlr_nao_aplic); //10
            $detalhe->ValorAplicadoAtePrimeiroQuadrimestre = $this->formataValor($linha->superavit_permitido); //11
            $detalhe->Brancos3 = str_repeat(" ", 69);
            $detalhe->NumRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }

    private function makeDetalhe7()
    {
        $tipoRegistro = self::RESTOS_PAGAR;
        foreach ($this->linhasProcessadas as $linha) {
            if (!$this->utilizaLinhaPorDetalhe($linha->linha, $tipoRegistro, $this->dePara)) {
                continue;
            }
            
            $detalhe = new stdClass();
            $detalhe->TipoRegistro = $tipoRegistro;
            $detalhe->Brancos1 = " ";
            $detalhe->Campo = $this->getCodigoCampo($linha->linha, $this->dePara, 3);
            $detalhe->Brancos2 = " ";
            $detalhe->SaldoIncial = $this->formataValor($linha->inscricao_total_rp);
            $detalhe->RPLiquidados = $this->formataValor($linha->liquidacoes_rp);
            $detalhe->RPPagos = $this->formataValor($linha->total_pagamentos_rp);
            $detalhe->RPCancelados = $this->formataValor($linha->total_anulacoes);
            $detalhe->SaldoFinal = $this->formataValor($linha->rp_saldo_final);
            $detalhe->Brancos3 = str_repeat(" ", 83);
            $detalhe->NumRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }
}
