<?php
namespace App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\RREO;

use App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\AnexosService;
use App\Domain\Financeiro\Contabilidade\Factories\AnexoDozeFactory;
use stdClass;

class AnexoDozeService extends AnexosService
{
    const SIGLA = "A12";
    /*
     * TABELA 1 - RECEITAS RESULTANTES DE IMPOSTOS E TRANSFERÊNCIAS CONSTITUCIONAIS E LEGAIS
     */
    const RECEITAS_IMPOSTOS_TRANSFERENCIAS = 1;
    
    /*
     * TABELA 2 - DESPESAS COM AÇÕES E SERVIÇOS PÚBLICOS DE SAÚDE (ASPS) - POR SUBFUNÇÃO E CATEGORIA ECONÔMICA
     */
    const DESPESA_ASPS = 2;
    
    /*
     * TABELA 3 - APURAÇÃO DO CUMPRIMENTO DO LIMITE MÍNIMO PARA APLICAÇÃO EM ASPS
     */
    const APURACAO_APLICACAO_ASPS = 3;
    
    /*
     * TABELA 4 - CONTROLE DO VALOR REFERENTE AO PERCENTUAL MÍNIMO NÃO CUMPRIDO EM EXERCÍCIOS ANTERIORES -
     * ARTIGOS 25 E 26 DA LC 141/2012
     */
    const CONTROLE_PERCENTUAL_EXERCICIOS_ANTERIORES = 4;
    
    /*
     * TABELA 5 - CONTROLE DA EXECUÇÃO DOS RESTOS A PAGAR
     */
    const CONTROLE_EXECUCAO_RP = 5;
    
    /*
     * TABELA 6 - CONTROLE DE RESTOS A PAGAR CANCELADOS OU PRESCRITOS
     * CONSIDERADOS PARA FINS DE APLICAÇÃO DA DISPONIBILIDADE DE CAIXA
     * CONFORME ARTIGO 24 §1 o E 2 o DA LC 141/2012
     */
    const CONTROLE_EXECUCAO_RP_CANCELADOS_PRESCRITOS = 6;
    
    /*
     * TABELA 7 - RECEITAS ADICIONAIS PARA O FINANCIAMENTO DA SAÚDE NÃO
     * COMPUTADAS NO CÁLCULO DO MÍNIMO
     */
    const RECEITAS_ADICIONAIS_SAUDE = 7;
    /*
     * TABELA 8 - DESPESAS COM SAÚDE POR SUBFUNÇÕES E CATEGORIA ECONÔMICA NÃO COMPUTADAS NO CÁLCULO DO MÍNIMO
     */
    const DESPESA_SAUDE_NAO_COMPUTADAS = 8;
    
    /*
     * TABELA 9 - DESPESAS TOTAIS COM SAÚDE
     */
    const DESPESA_TOTAIS_SAUDE = 9;
    
    protected $dePara = [
        self::RECEITAS_IMPOSTOS_TRANSFERENCIAS => [
            1 => 1,
            2 => 2,
            3 => 5,
            4 => 8,
            5 => 11,
            6 => 12,
            7 => 13,
            8 => 14,
            9 => 15,
            10 => 16,
            11 => 17,
            12 => 18,
            13 => 21
        ],
        self::DESPESA_ASPS => [
            14 => 22,
            15 => 23,
            16 => 24,
            17 => 25,
            18 => 26,
            19 => 27,
            20 => 28,
            21 => 29,
            22 => 30,
            23 => 31,
            24 => 32,
            25 => 33,
            26 => 34,
            27 => 35,
            28 => 36,
            29 => 37,
            30 => 38,
            31 => 39,
            32 => 40,
            33 => 41,
            34 => 42,
            35 => 43
        ],
        self::APURACAO_APLICACAO_ASPS => [
            36 => 44,
            37 => 45,
            38 => 46,
            39 => 47,
            40 => 48,
            41 => 49,
            42 => 50,
            43 => 51,
            44 => 52,
            45 => 53
        ],
        self::CONTROLE_PERCENTUAL_EXERCICIOS_ANTERIORES => [
            46 => 54,
            47 => 55,
            48 => 56,
            49 => 57
        ],
        self::CONTROLE_EXECUCAO_RP => [
            50 => 58,
            51 => 59,
            52 => 60,
            53 => 61,
            54 => 62,
            55 => 63,
            56 => 64,
            57 => 65
        ],
        self::CONTROLE_EXECUCAO_RP_CANCELADOS_PRESCRITOS => [
            58 => 66,
            59 => 67,
            60 => 68,
            61 => 69
        ],
        self::RECEITAS_ADICIONAIS_SAUDE => [
            62 => 70,
            63 => 71,
            64 => 72,
            65 => 73,
            66 => 74,
            67 => 75,
            68 => 76
        ],
        self::DESPESA_SAUDE_NAO_COMPUTADAS => [
            69 => 77,
            70 => 78,
            71 => 79,
            72 => 80,
            73 => 81,
            74 => 82,
            75 => 83,
            76 => 84,
            77 => 85,
            78 => 86,
            79 => 87,
            80 => 88,
            81 => 89,
            82 => 90,
            83 => 91,
            84 => 92,
            85 => 93,
            86 => 94,
            87 => 95,
            88 => 96,
            89 => 97,
            90 => 98
        ],
        self::DESPESA_TOTAIS_SAUDE =>   [
            91 => 99,
            92 => 100,
            93 => 101,
            94 => 102,
            95 => 103,
            96 => 104,
            97 => 105,
            98 => 106
        ]
    ];

    private $linhasTotalizadorasDetalhe2 = [
        14 => [15,16],
        17 => [18,19],
        20 => [21,22],
        23 => [24,25],
        26 => [27,28],
        29 => [30,31],
        32 => [33,34],
        35 => [14,17,20,23,26,29,32]
    ];

    private $linhasTotalizadorasDetalhe7 = [
        62 => [63,64,65],
        68 => [62,66,67]
    ];

    private $linhasTotalizadorasDetalhe8 = [
        69 => [70,71],
        72 => [73,74],
        75 => [76,77],
        78 => [79,80],
        81 => [82,83],
        84 => [85,86],
        87 => [88,89],
        90 => [69,72,75,78,81,84,87]
    ];

    private $linhasTotalizadorasDetalhe9 = [
        91 => [14,69],
        92 => [17,72],
        93 => [20,75],
        94 => [23,78],
        95 => [26,81],
        96 => [29,84],
        97 => [32,87],
        98 => [91,92,93,94,95,96,97]
    ];
    
    public function __construct($exercicio, $filtros)
    {
        $relatorio = AnexoDozeFactory::getService($exercicio, $filtros);
        $this->linhasProcessadas = $relatorio->getLinhasProcessadas();
        $this->calcularLinhasTotalizadoras();
        $this->exercicio = $exercicio;
        $this->periodo = $relatorio->getPeriodo();
        $this->codigoOrgao = $filtros['codigoOrgaoTCE'];
        $this->nomeOrgao = $filtros['nomeOrgaoTCE'];
        $this->nomeArquivo = self::SIGLA."_{$exercicio}".str_pad($this->periodo->getOrdem(), 2, "0", STR_PAD_LEFT);
        $this->setFilePath("tmp/{$this->nomeArquivo}.TXT");
    }
    
    public function makeHeader()
    {
        $header = new stdClass();
        $header->tipoRegistro = "0";
        $header->brancos1 = " ";
        $header->nomeArquivo = $this->nomeArquivo;
        $header->bimReferencia = explode("_", $this->nomeArquivo)[1];
        $header->tipoArquivo = "O";
        $header->dataGeracaoArq = date("d/m/Y");
        $header->horaGeracaoArq = date("H:i:s");
        $header->codigoOrgao = str_pad($this->codigoOrgao, 4, " ", STR_PAD_LEFT);
        $header->nomeOrgao = str_pad(substr($this->nomeOrgao, 0, 100), 100, " ");
        $header->brancos2 = str_repeat(" ", 27);
        $header->NumRegistroLido = $this->getQtdLinhas();
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
        $this->makeDetalhe8();
        $this->makeDetalhe9();
    }
    
    private function calcularLinhasTotalizadoras()
    {
        $this->criaPropriedadesControleExecucaoRP(55);
        $this->criaPropriedadesControleExecucaoRP(56);
        $this->criaPropriedadesControleExecucaoRP(57);

        $this->calcularLinhasTotalizadorasDetalhe2();
        $this->calcularLinhasTotalizadorasDetalhe3();
        $this->calcularLinhasTotalizadorasDetalhe5();
        $this->calcularLinhasTotalizadorasDetalhe7();
        $this->calcularLinhasTotalizadorasDetalhe8();
        $this->calcularLinhasTotalizadorasDetalhe9();
    }

    private function calcularLinhasTotalizadorasDetalhe2()
    {
        foreach ($this->linhasTotalizadorasDetalhe2 as $linhaTotalizar => $somarLinhas) {
            $this->linhasProcessadas[$linhaTotalizar]->saldo_inicial = 0;
            $this->linhasProcessadas[$linhaTotalizar]->total_creditos = 0;
            $this->linhasProcessadas[$linhaTotalizar]->empenhado_liquido_acumulado = 0;
            $this->linhasProcessadas[$linhaTotalizar]->liquidado_acumulado = 0;
            $this->linhasProcessadas[$linhaTotalizar]->pago_acumulado = 0;

            foreach ($somarLinhas as $linhaSomar) {
                $this->linhasProcessadas[$linhaTotalizar]->saldo_inicial +=
                $this->linhasProcessadas[$linhaSomar]->saldo_inicial;
                
                $this->linhasProcessadas[$linhaTotalizar]->total_creditos +=
                $this->linhasProcessadas[$linhaSomar]->total_creditos;
                
                $this->linhasProcessadas[$linhaTotalizar]->empenhado_liquido_acumulado +=
                $this->linhasProcessadas[$linhaSomar]->empenhado_liquido_acumulado;
                
                $this->linhasProcessadas[$linhaTotalizar]->liquidado_acumulado +=
                $this->linhasProcessadas[$linhaSomar]->liquidado_acumulado;

                $this->linhasProcessadas[$linhaTotalizar]->pago_acumulado +=
                $this->linhasProcessadas[$linhaSomar]->pago_acumulado;
            }
        }
    }

    private function calcularLinhasTotalizadorasDetalhe3()
    {
        $this->linhasProcessadas[36]->pago_acumulado = $this->linhasProcessadas[35]->pago_acumulado;

        $this->linhasProcessadas[40]->empenhado_liquido_acumulado =
        $this->linhasProcessadas[36]->empenhado_liquido_acumulado
        - $this->linhasProcessadas[37]->empenhado_liquido_acumulado ;
        - $this->linhasProcessadas[38]->empenhado_liquido_acumulado ;
        - $this->linhasProcessadas[39]->empenhado_liquido_acumulado ;

        $this->linhasProcessadas[40]->pago_acumulado = $this->linhasProcessadas[36]->liquidado_acumulado
        - $this->linhasProcessadas[37]->liquidado_acumulado ;
        - $this->linhasProcessadas[38]->liquidado_acumulado ;
        - $this->linhasProcessadas[39]->liquidado_acumulado ;

        $this->linhasProcessadas[40]->pago_acumulado = $this->linhasProcessadas[36]->pago_acumulado
         - $this->linhasProcessadas[37]->pago_acumulado ;
         - $this->linhasProcessadas[38]->pago_acumulado ;
         - $this->linhasProcessadas[39]->pago_acumulado ;

        $this->linhasProcessadas[41]->pago_acumulado = 0;
        $this->linhasProcessadas[41]->vlrDespesaMinima = $this->linhasProcessadas[13]->arrecadado_acumulado*0.15;

        $this->linhasProcessadas[43]->liquidado_acumulado = $this->linhasProcessadas[36]->liquidado_acumulado
        - ($this->linhasProcessadas[13]->arrecadado_acumulado*0.15);

        $this->linhasProcessadas[42]->empenhado_liquido_acumulado = 0;
        $this->linhasProcessadas[42]->liquidado_acumulado = 0;
        $this->linhasProcessadas[42]->pago_acumulado = 0;
        $this->linhasProcessadas[42]->vlrDespesaMinima = 0;
        $this->linhasProcessadas[42]->RPNP = 0;
    }
    
    private function calcularLinhasTotalizadorasDetalhe5()
    {
        $this->linhasProcessadas[50]->valor_para_aplicar_asps = $this->linhasProcessadas[41]->vlrDespesaMinima;
        $this->linhasProcessadas[50]->total_aplicado_alem_do_limite =
        abs($this->linhasProcessadas[50]->valor_aplicado_asps - $this->linhasProcessadas[50]->valor_para_aplicar_asps);
        $this->linhasProcessadas[50]->valor = ($this->linhasProcessadas[50]->total_aplicado_alem_do_limite
        + $this->linhasProcessadas[50]->rpnp_inscrito_indevidamente)
        - $this->linhasProcessadas[50]->total_anulacoes;


        $this->linhasProcessadas[51]->total_aplicado_alem_do_limite =
        abs($this->linhasProcessadas[51]->valor_aplicado_asps - $this->linhasProcessadas[51]->valor_para_aplicar_asps);
        $this->linhasProcessadas[51]->valor = ($this->linhasProcessadas[51]->total_aplicado_alem_do_limite
        + $this->linhasProcessadas[51]->rpnp_inscrito_indevidamente)
        - $this->linhasProcessadas[51]->total_anulacoes;

        $this->linhasProcessadas[52]->total_aplicado_alem_do_limite =
        abs($this->linhasProcessadas[52]->valor_aplicado_asps - $this->linhasProcessadas[52]->valor_para_aplicar_asps);
        $this->linhasProcessadas[52]->valor = ($this->linhasProcessadas[52]->total_aplicado_alem_do_limite
        + $this->linhasProcessadas[52]->rpnp_inscrito_indevidamente)
        - $this->linhasProcessadas[52]->total_anulacoes;

        $this->linhasProcessadas[53]->total_aplicado_alem_do_limite =
        abs($this->linhasProcessadas[53]->valor_aplicado_asps - $this->linhasProcessadas[53]->valor_para_aplicar_asps);
        $this->linhasProcessadas[53]->valor = ($this->linhasProcessadas[53]->total_aplicado_alem_do_limite
        + $this->linhasProcessadas[53]->rpnp_inscrito_indevidamente)
        - $this->linhasProcessadas[53]->total_anulacoes;

        $this->linhasProcessadas[54]->total_aplicado_alem_do_limite =
        abs($this->linhasProcessadas[54]->valor_aplicado_asps - $this->linhasProcessadas[54]->valor_para_aplicar_asps);
        $this->linhasProcessadas[54]->valor = ($this->linhasProcessadas[54]->total_aplicado_alem_do_limite
        + $this->linhasProcessadas[54]->rpnp_inscrito_indevidamente)
        - $this->linhasProcessadas[54]->total_anulacoes;

        if ($this->linhasProcessadas[55]->valor < 0) {
            $this->linhasProcessadas[55]->valor = 0;
        }

        if ($this->linhasProcessadas[57]->valor < 0) {
            $this->linhasProcessadas[57]->valor = 0;
        }
    }

    private function calculaValorRPControleExecucaoRP($linha)
    {
        $totalRPExercicio = $linha->inscricao_total_rp;
        $valorAplicadoASPS = ($linha->valor_aplicado_asps - $linha->valor_para_aplicar_asps);
        if ($valorAplicadoASPS < 0) {
            $valorAplicadoASPS = 0;
        }
        $RPNPInscritosSemDisponibilidade = $linha->rpnp_inscrito_indevidamente;
        $valorRP = $totalRPExercicio - ($valorAplicadoASPS + $RPNPInscritosSemDisponibilidade);
        if ($valorRP < 0) {
            return 0;
        }
        return $valorRP;
    }

    private function calcularLinhasTotalizadorasDetalhe7()
    {
        foreach ($this->linhasTotalizadorasDetalhe7 as $linhaTotalizar => $somarLinhas) {
            $this->linhasProcessadas[$linhaTotalizar]->valor_inicial = 0;
            $this->linhasProcessadas[$linhaTotalizar]->previsao_atualizada = 0;
            $this->linhasProcessadas[$linhaTotalizar]->arrecadado_acumulado = 0;

            foreach ($somarLinhas as $linhaSomar) {
                $this->linhasProcessadas[$linhaTotalizar]->valor_inicial +=
                $this->linhasProcessadas[$linhaSomar]->valor_inicial;
                
                $this->linhasProcessadas[$linhaTotalizar]->previsao_atualizada +=
                $this->linhasProcessadas[$linhaSomar]->previsao_atualizada;
                
                $this->linhasProcessadas[$linhaTotalizar]->arrecadado_acumulado +=
                $this->linhasProcessadas[$linhaSomar]->arrecadado_acumulado;
            }
        }
    }

    private function calcularLinhasTotalizadorasDetalhe8()
    {
        foreach ($this->linhasTotalizadorasDetalhe8 as $linhaTotalizar => $somarLinhas) {
            $this->linhasProcessadas[$linhaTotalizar]->saldo_inicial = 0;
            $this->linhasProcessadas[$linhaTotalizar]->total_creditos = 0;
            $this->linhasProcessadas[$linhaTotalizar]->empenhado_liquido_acumulado = 0;
            $this->linhasProcessadas[$linhaTotalizar]->liquidado_acumulado = 0;
            $this->linhasProcessadas[$linhaTotalizar]->pago_acumulado = 0;
            $this->linhasProcessadas[$linhaTotalizar]->a_liquidar = 0;

            foreach ($somarLinhas as $linhaSomar) {
                $this->linhasProcessadas[$linhaTotalizar]->saldo_inicial +=
                $this->linhasProcessadas[$linhaSomar]->saldo_inicial;
                
                $this->linhasProcessadas[$linhaTotalizar]->total_creditos +=
                $this->linhasProcessadas[$linhaSomar]->total_creditos;
                
                $this->linhasProcessadas[$linhaTotalizar]->empenhado_liquido_acumulado +=
                $this->linhasProcessadas[$linhaSomar]->empenhado_liquido_acumulado;
                
                $this->linhasProcessadas[$linhaTotalizar]->liquidado_acumulado +=
                $this->linhasProcessadas[$linhaSomar]->liquidado_acumulado;

                $this->linhasProcessadas[$linhaTotalizar]->pago_acumulado +=
                $this->linhasProcessadas[$linhaSomar]->pago_acumulado;

                $this->linhasProcessadas[$linhaTotalizar]->a_liquidar +=
                (!isset($this->linhasProcessadas[$linhaSomar]->a_liquidar)?0
                :$this->linhasProcessadas[$linhaSomar]->a_liquidar);
            }
        }
    }

    private function calcularLinhasTotalizadorasDetalhe9()
    {
        foreach ($this->linhasTotalizadorasDetalhe9 as $linhaTotalizar => $somarLinhas) {
            $this->linhasProcessadas[$linhaTotalizar]->saldo_inicial = 0;
            $this->linhasProcessadas[$linhaTotalizar]->total_creditos = 0;
            $this->linhasProcessadas[$linhaTotalizar]->empenhado_liquido_acumulado = 0;
            $this->linhasProcessadas[$linhaTotalizar]->liquidado_acumulado = 0;
            $this->linhasProcessadas[$linhaTotalizar]->pago_acumulado = 0;
            $this->linhasProcessadas[$linhaTotalizar]->a_liquidar = 0;

            foreach ($somarLinhas as $linhaSomar) {
                $this->linhasProcessadas[$linhaTotalizar]->saldo_inicial +=
                $this->linhasProcessadas[$linhaSomar]->saldo_inicial;
                
                $this->linhasProcessadas[$linhaTotalizar]->total_creditos +=
                $this->linhasProcessadas[$linhaSomar]->total_creditos;
                
                $this->linhasProcessadas[$linhaTotalizar]->empenhado_liquido_acumulado +=
                $this->linhasProcessadas[$linhaSomar]->empenhado_liquido_acumulado;
                
                $this->linhasProcessadas[$linhaTotalizar]->liquidado_acumulado +=
                $this->linhasProcessadas[$linhaSomar]->liquidado_acumulado;

                $this->linhasProcessadas[$linhaTotalizar]->pago_acumulado +=
                $this->linhasProcessadas[$linhaSomar]->pago_acumulado;

                $this->linhasProcessadas[$linhaTotalizar]->a_liquidar +=
                (!isset($this->linhasProcessadas[$linhaSomar]->a_liquidar)?0
                :$this->linhasProcessadas[$linhaSomar]->a_liquidar);
            }
        }
    }
    
    private function criaPropriedadesControleExecucaoRP($linha)
    {
        $this->linhasProcessadas[$linha]->valor_para_aplicar_asps = 0;
        $this->linhasProcessadas[$linha]->valor_aplicado_asps = 0;
        $this->linhasProcessadas[$linha]->inscricao_total_rp = 0;
        $this->linhasProcessadas[$linha]->rpnp_inscrito_indevidamente = 0;
        $this->linhasProcessadas[$linha]->total_rp_a_pagar = 0;
        $this->linhasProcessadas[$linha]->total_pagamentos_rp = 0;
        $this->linhasProcessadas[$linha]->total_anulacoes = 0;
        $this->linhasProcessadas[$linha]->total_aplicado_alem_do_limite = 0;
        $this->linhasProcessadas[$linha]->total = 0;
    }

    private function makeDetalhe1()
    {
        $tipoRegistro = self::RECEITAS_IMPOSTOS_TRANSFERENCIAS;
        foreach ($this->linhasProcessadas as $linha) {
            if (!$this->utilizaLinhaPorDetalhe($linha->linha, $tipoRegistro, $this->dePara)) {
                continue;
            }
            
            $detalhe = new stdClass();
            $detalhe->TipRegistro = $tipoRegistro;
            $detalhe->Brancos1 = " ";
            $detalhe->Campo = $this->getCodigoCampo($linha->linha, $this->dePara, 3);
            $detalhe->Brancos2 = str_repeat(" ", 7);
            $detalhe->PrevisaoInicial = $this->formataValor($linha->valor_inicial);
            $detalhe->PrevisaoAtualizada = $this->formataValor($linha->previsao_atualizada);
            $detalhe->ateoBimestre = $this->formataValor($linha->arrecadado_acumulado);
            $detalhe->Brancos3 = str_repeat(" ", 115);
            $detalhe->NumRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }
    
    private function makeDetalhe2()
    {
        /*
         * @todo verificar RPNP essa informação apenas será mostrada no sexto bimestre
         */
        $tipoRegistro = self::DESPESA_ASPS;
        foreach ($this->linhasProcessadas as $linha) {
            if (!$this->utilizaLinhaPorDetalhe($linha->linha, $tipoRegistro, $this->dePara)) {
                continue;
            }
            
            $detalhe = new stdClass();
            $detalhe->TipRegistro = $tipoRegistro;
            $detalhe->Brancos1 = " ";
            $detalhe->Campo = $this->getCodigoCampo($linha->linha, $this->dePara, 3);
            ;
            $detalhe->Brancos2 = str_repeat(" ", 7);
            $detalhe->DotacaoInicial = $this->formataValor($linha->saldo_inicial);
            $detalhe->DotacaoAtualizada = $this->formataValor($linha->total_creditos);
            $detalhe->EmpenhadoAteoBimestre = $this->formataValor($linha->empenhado_liquido_acumulado);
            $detalhe->LiquidadasAteoBimestre = $this->formataValor($linha->liquidado_acumulado);
            $detalhe->PagasAteoBimestre = $this->formataValor($linha->pago_acumulado);
            $detalhe->RPNP = $this->formataValor(0);
            $detalhe->Brancos3 = str_repeat(" ", 73);
            $detalhe->NumRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }
    
    private function makeDetalhe3()
    {
        /*
         * @todo verificar DespesaMinima essa informação apenas será mostrada no sexto bimestre
         */
        $tipoRegistro = self::APURACAO_APLICACAO_ASPS;
        foreach ($this->linhasProcessadas as $linha) {
            if (!$this->utilizaLinhaPorDetalhe($linha->linha, $tipoRegistro, $this->dePara)) {
                continue;
            }
            
            $detalhe = new stdClass();
            $detalhe->TipRegistro = $tipoRegistro;
            $detalhe->Brancos1 = " ";
            $detalhe->Campo = $this->getCodigoCampo($linha->linha, $this->dePara, 3);
            $detalhe->Brancos2 = str_repeat(" ", 7);
            $detalhe->DespesasEmpenhadas = $this->formataValor($linha->empenhado_liquido_acumulado);
            $detalhe->DespesasLiquidadas = $this->formataValor($linha->liquidado_acumulado);
            $detalhe->DespesasPagas = $this->formataValor($linha->pago_acumulado);
            $detalhe->DespesaMinima = $this->formataValor((isset($linha->vlrDespesaMinima)?$linha->vlrDespesaMinima:0));
            $detalhe->Brancos3 = str_repeat(" ", 101);
            $detalhe->NumRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }
    
    private function makeDetalhe4()
    {
        $tipoRegistro = self::CONTROLE_PERCENTUAL_EXERCICIOS_ANTERIORES;
        foreach ($this->linhasProcessadas as $linha) {
            if (!$this->utilizaLinhaPorDetalhe($linha->linha, $tipoRegistro, $this->dePara)) {
                continue;
            }
        
            $detalhe = new stdClass();
            $detalhe->TipRegistro = $tipoRegistro;
            $detalhe->Brancos1 = " ";
            $detalhe->Campo = $this->getCodigoCampo($linha->linha, $this->dePara, 3);
            ;
            $detalhe->Brancos2 = str_repeat(" ", 7);
            $detalhe->SaldoInicial = $this->formataValor($linha->saldo_inicial);
            $detalhe->DespesasEmpenhadas = $this->formataValor($linha->empenhado_liquido_acumulado);
            $detalhe->DespesasLiquidadas = $this->formataValor($linha->liquidado_acumulado);
            $detalhe->DespesasPagas = $this->formataValor($linha->pago_acumulado);
            $detalhe->SaldoFinal = $this->formataValor($linha->total);
            $detalhe->Brancos3 = str_repeat(" ", 87);
            $detalhe->NumRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }
    
    private function makeDetalhe5()
    {
        $tipoRegistro = self::CONTROLE_EXECUCAO_RP;
        foreach ($this->linhasProcessadas as $linha) {
            if (!$this->utilizaLinhaPorDetalhe($linha->linha, $tipoRegistro, $this->dePara)) {
                continue;
            }
            
            $detalhe = new stdClass();
            $detalhe->TipRegistro = $tipoRegistro;
            $detalhe->Brancos1 = " ";
            $detalhe->Campo = $this->getCodigoCampo($linha->linha, $this->dePara, 3);
            $detalhe->Brancos2 = " ";
            $detalhe->ValorMinimoAplicacao = $this->formataValor($linha->valor_para_aplicar_asps);
            $detalhe->ValorAplicadoASPS = $this->formataValor($linha->valor_aplicado_asps);
            $detalhe->ValorAplicadoLimite = $this->formataValor($linha->total_aplicado_alem_do_limite);
            $detalhe->TotalRPExercicio = $this->formataValor($linha->inscricao_total_rp);
            $detalhe->RPNPInscritosSemDisponibilidade = $this->formataValor($linha->rpnp_inscrito_indevidamente);
            $detalhe->ValorRP = $this->formataValor($this->calculaValorRPControleExecucaoRP($linha));
            $detalhe->TotalRPPagos = $this->formataValor($linha->total_pagamentos_rp);
            $detalhe->TotalRPaPagar = $this->formataValor($linha->total_rp_a_pagar);
            $detalhe->TotalRPCancelados = $this->formataValor($linha->total_anulacoes);
            $detalhe->TotalRestosPagarCanceladosPrescritos = $this->formataValor(isset($linha->valor)?$linha->valor:0);
            $detalhe->Brancos3 = str_repeat(" ", 23);
            $detalhe->NumRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }
    
    private function makeDetalhe6()
    {
        $tipoRegistro = self::CONTROLE_EXECUCAO_RP_CANCELADOS_PRESCRITOS;
        foreach ($this->linhasProcessadas as $linha) {
            if (!$this->utilizaLinhaPorDetalhe($linha->linha, $tipoRegistro, $this->dePara)) {
                continue;
            }
            
            $detalhe = new stdClass();
            $detalhe->TipRegistro = $tipoRegistro;
            $detalhe->Brancos1 = " ";
            $detalhe->Campo = $this->getCodigoCampo($linha->linha, $this->dePara, 3);
            ;
            $detalhe->Brancos2 = str_repeat(" ", 7);
            $detalhe->SaldoInicial = $this->formataValor($linha->saldo_inicial);
            $detalhe->DespesasEmpenhadas = $this->formataValor($linha->empenhado_liquido_acumulado);
            $detalhe->DespesasLiquidadas = $this->formataValor($linha->liquidado_acumulado);
            $detalhe->DespesasPagas = $this->formataValor($linha->pago_acumulado);
            $detalhe->SaldoFinal = $this->formataValor($linha->total);
            $detalhe->Brancos3 = str_repeat(" ", 87);
            $detalhe->NumRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }
    
    private function makeDetalhe7()
    {
        $tipoRegistro = self::RECEITAS_ADICIONAIS_SAUDE;
        foreach ($this->linhasProcessadas as $linha) {
            if (!$this->utilizaLinhaPorDetalhe($linha->linha, $tipoRegistro, $this->dePara)) {
                continue;
            }
            
            $detalhe = new stdClass();
            $detalhe->TipRegistro = $tipoRegistro;
            $detalhe->Brancos1 = " ";
            $detalhe->Campo = $this->getCodigoCampo($linha->linha, $this->dePara, 3);
            ;
            $detalhe->Brancos2 = str_repeat(" ", 7);
            $detalhe->PrevisaoInicial = $this->formataValor($linha->valor_inicial);
            $detalhe->PrevisaoAtualizada = $this->formataValor($linha->previsao_atualizada);
            $detalhe->ateoBimestre = $this->formataValor($linha->arrecadado_acumulado);
            $detalhe->Brancos3 = str_repeat(" ", 115);
            $detalhe->NumRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }
    
    private function makeDetalhe8()
    {
        /*
         * @todo verificar RPNP essa informação apenas será mostrada no sexto bimestre
         */
        $tipoRegistro = self::DESPESA_SAUDE_NAO_COMPUTADAS;
        foreach ($this->linhasProcessadas as $linha) {
            if (!$this->utilizaLinhaPorDetalhe($linha->linha, $tipoRegistro, $this->dePara)) {
                continue;
            }
            
            $detalhe = new stdClass();
            $detalhe->TipRegistro = $tipoRegistro;
            $detalhe->Brancos1 = " ";
            $detalhe->Campo = $this->getCodigoCampo($linha->linha, $this->dePara, 3);
            $detalhe->Brancos2 = str_repeat(" ", 7);
            $detalhe->DotacaoInicial = $this->formataValor($linha->saldo_inicial);
            $detalhe->DotacaoAtualizada = $this->formataValor($linha->total_creditos);
            $detalhe->EmpenhadoAteoBimestre = $this->formataValor($linha->empenhado_liquido_acumulado);
            $detalhe->LiquidadasAteoBimestre = $this->formataValor($linha->liquidado_acumulado);
            $detalhe->PagasAteoBimestre = $this->formataValor($linha->pago_acumulado);
            $detalhe->RPNP = $this->formataValor(0);
            $detalhe->Brancos3 = str_repeat(" ", 73);
            $detalhe->NumRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }
    
    private function makeDetalhe9()
    {
        /*
         * @todo verificar RPNP essa informação apenas será mostrada no sexto bimestre
         */
        $tipoRegistro = self::DESPESA_TOTAIS_SAUDE;
        foreach ($this->linhasProcessadas as $linha) {
            if (!$this->utilizaLinhaPorDetalhe($linha->linha, $tipoRegistro, $this->dePara)) {
                continue;
            }
            
            $campo = $this->getCodigoCampo($linha->linha, $this->dePara, 3);

            $detalhe = new stdClass();
            $detalhe->TipRegistro = $tipoRegistro;
            $detalhe->Brancos1 = str_repeat(" ", ($campo > 99)?2:1);
            $detalhe->Campo = $campo;
            $detalhe->Brancos2 = str_repeat(" ", ($campo > 99)?6:7);
            $detalhe->DotacaoInicial = $this->formataValor($linha->saldo_inicial);
            $detalhe->DotacaoAtualizada = $this->formataValor($linha->total_creditos);
            $detalhe->EmpenhadoAteoBimestre = $this->formataValor($linha->empenhado_liquido_acumulado);
            $detalhe->LiquidadasAteoBimestre = $this->formataValor($linha->liquidado_acumulado);
            $detalhe->PagasAteoBimestre = $this->formataValor($linha->pago_acumulado);
            $detalhe->RPNP = $this->formataValor(0);
            $detalhe->Brancos3 = str_repeat(" ", 73);
            $detalhe->NumRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }
    
    public function makeTrailler()
    {
        $trailler = new stdClass();
        $trailler->tipoRegistro = "99";
        $trailler->brancos = str_repeat(" ", 166);
        $trailler->NumRegistroLido = $this->getQtdLinhas();
        $this->setTrailler($trailler);
    }
}
