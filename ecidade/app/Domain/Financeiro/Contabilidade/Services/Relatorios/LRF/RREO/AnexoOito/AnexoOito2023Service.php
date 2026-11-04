<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoOito;

use phpDocumentor\Reflection\Types\This;

/**
 * Linhas com formulas no layout
 * 40    9- TOTAL DOS RECURSOS DO FUNDEB DISPONÍVEIS PARA UTILIZAÇÃO (6 +8)
 * 64    15- MÍNIMO DE 70% DO FUNDEB NA REMUNERAÇÃO DOS PROFISSIONAIS DA EDUCAÇÃO BÁSICA
 * 65    16- PERCENTUAL DE 50% DA COMPLEMENTAÇÃO DA UNIÃO AO FUNDEB - VAAT NA EDUCAÇÃO INFANTIL
 * 66    17- MÍNIMO DE 15% DA COMPLEMENTAÇÃO DA UNIÃO AO FUNDEB - VAAT EM DESPESAS DE CAPITAL
 * 67    18- TOTAL DA RECEITA RECEBIDA E NÃO APLICADA NO EXERCÍCIO
 * 84    22- TOTAL DAS DESPESAS DE MDE CUSTEADAS COM RECURSOS DE IMPOSTOS = L20(d ou e)
 * 85    23- TOTAL DAS RECEITAS TRANSFERIDAS AO FUNDEB = (L4)
 * 86    24- (-) RECEITAS DO FUNDEB NÃO UTILIZADAS NO EXERCÍCIO, EM VALOR SUPERIOR A 10% = L18(q)
 * 87    25- (-) SUPERÁVIT PERMITIDO NO EXERCÍCIO IMEDIATAMENTE ANTERIOR NÃO APLICADO NO EXERCÍCIO ATUAL = L19.1(x)
 * 89    27- (-) CANCELAMENTO, NO EXERCÍCIO, DE RESTOS A PAGAR INSCRITOS COM DISPONIBILIDADE FINANCEIRA DE RECURSOS
 *              DE IMPOSTOS VINCULADOS
 * 90    28- TOTAL DAS DESPESAS PARA FINS DE LIMITE (22 + 23) - (24 + 25 + 26 + 27)
 */
class AnexoOito2023Service extends AnexoOitoService
{
    protected $colunasReceita = [
        'previsao_atualizada' => 'previsao_atualizada',
        'arrecadado_acumulado' => 'arrecadado_acumulado',
    ];

    protected $colunasDespesa = [
        '' => 'rpnp_sem_dc', // valor manual, por isso não tem mapeamento
        'a_liquidar' => 'a_liquidar',
        'liquidado_acumulado' => 'liquidado_acumulado',
        'pago_acumulado' => 'pago_acumulado',
        'empenhado_liquido_acumulado' => 'empenhado_liquido_acumulado',
        'total_creditos' => 'total_creditos',
    ];

    /**
     * Mapeia as seções do relatório no excell
     * @var \int[][]
     */
    protected $sections = [
        'receita_1' => [1, 18],  // RECEITA RESULTANTE DE IMPOSTOS
        'receita_2' => [19, 36], // RECEITAS DO FUNDEB RECEBIDAS NO EXERCÍCIO
        'total_superavit' => [37, 39], // RECURSOS RECEBIDOS EM EXERCÍCIOS ANTERIORES E NÃO UTILIZADOS (SUPERÁVIT)
        'despesa_1' => [41, 55], // DESPESAS COM RECURSOS DO FUNDEB (Por Subfunção)
        'despesa_2' => [56, 63], // DESPESAS CUSTEADAS COM RECEITAS DO FUNDEB RECEBIDAS NO EXERCÍCIO
        'manuais' => [68, 70], // INDICADOR (Aplicação do Superávit de Exercício Anterior)
        'despesa_4' => [71, 78], // DESPESAS AÇÕES DE MDE - RECEITAS DE IMPOSTOS - EXCETO FUNDE(Por Subfunção)
        'despesa_5' => [79, 83], // DESPESAS AÇÕES DE MDE - RECEITAS DE IMP. E REC. DO FUNDEB (Por Área de Atuação)
        'rp_1' => [92, 95], // RESTOS A PAGAR INSCRITOS EM EXERCÍCIOS ANTERIORES DE DESPESAS CONSIDERADAS PARA ..
        'receita_3' => [96, 106], // RECEITAS ADICIONAIS PARA FINANCIAMENTO DO ENSINO
        'despesa_6' => [107, 115], // OUTRAS DESPESAS COM EDUCAÇÃO  (Por Subfunção)6
        'despesa_7' => [116, 124], // TOTAL GERAL DAS DESPESAS COM EDUCAÇÃO
        'balver_1' => [125, 131], // CONTROLE DA DISPONIBILIDADE FINANCEIRA E CONCILIAÇÃO BANCÁRIA
    ];

    /**
     * linhas 92, 93, 94, 95 -> linhas de RP nº 30, 30.1...
     * demais linhas do quadro: CONTROLE DA DISPONIBILIDADE FINANCEIRA E CONCILIAÇÃO BANCÁRIA
     * @var int[]
     */
    protected $linhasNaoProcessar = [92, 93, 94, 95, 126, 128, 129, 130, 131, 133, 134, 135, 136, 137, 138];
    protected $linhasCalcularRpsMde = [93, 94, 95];

    protected $totalizarSoma = [
        1 => [2, 3, 4, 5],
        7 => [8, 9], // 2.1- Cota-Parte FPM
        6 => [7, 10, 11, 12, 13, 14, 15], // 2- RECEITA DE TRANSFERÊNCIAS CONSTITUCIONAIS E LEGAIS
        16 => [1, 6], // 3- TOTAL DA RECEITA RESULTANTE DE IMPOSTOS (1 + 2)
        17 => [8, 10, 11, 12, 13], // 4- TOTAL DESTINADO AO FUNDEB "depois tem que calcular o percente"
        20 => [21, 22, 23], // 6.1- FUNDEB - Impostos e Transferências de Impostos
        24 => [25, 26, 27], // 6.2- FUNDEB - Complementação da União - VAAF
        28 => [29, 30, 31], // 6.3- FUNDEB - Complementação da União - VAAT
        32 => [33, 34, 35], // 6.4- FUNDEB - Complementação da União - VAAR
        19 => [20, 24, 28, 32], //  6- TOTAL DAS RECEITAS DO FUNDEB RECEBIDAS
        42 => [43, 44, 45, 46, 47], //  10.1- PROFISSIONAIS DA EDUCAÇÃO BÁSICA
        48 => [49, 50, 51, 52, 53, 54, 55], // 10.2- OUTRAS DESPESAS
        41 => [42, 48], // 10- TOTAL DAS DESPESAS COM RECURSOS DO FUNDEB
        56 => [57, 58, 59, 60], //  11- TOTAL DAS DESPESAS CUSTEADAS COM RECURSOS DO FUNDEB RECEBIDAS NO EXERCÍCIO
        68 => [69, 70], // 19- TOTAL DAS DESPESAS CUSTEADAS COM SUPERÁVIT DO FUNDEB
        71 => [72, 73, 74, 75, 76, 77, 78], // 20-TOTAL DAS DESPESAS COM AÇÕES TÍPICAS DE MDE CUSTEADAS COM RECEITAS ...
        80 => [81, 82], // 21.1- EDUCAÇÃO INFANTIL
        79 => [80, 83], // 21- TOTAL DAS DESPESAS COM AÇÕES TÍPICAS DE MDE CUSTEADAS COM RECEITAS DE IMPOSTOS E FUNDEB
        92 => [93, 94, 95], //  30- RESTOS A PAGAR DE DESPESAS COM MDE
        97 => [98, 99, 100, 101, 102], //  31.1- RECEITA DE TRANSFERÊNCIAS DO FNDE ...
        96 => [97, 103, 104, 105, 106], //  31- TOTAL DAS RECEITAS ADICIONAIS PARA FINANCIAMENTO DO ENSINO
        107 => [108, 109, 110, 111, 112, 113, 114, 115], // 32- TOTAL DAS DESPESAS COM AÇÕES TÍPICAS DE MDE CUSTEADAS...
        117 => [118, 119, 120, 121], //  33.1- Despesas Correntes
        122 => [123, 124], // 33.2- Despesas de Capital
    ];

    protected $totalizarSubtracao = [
        36 => [21, 17],
    ];

    /**
     * Mapeamento para cálculo da coluna (j) "DESPESAS EMPENHADAS EM VALOR SUPERIOR AO TOTAL DAS RECEITAS RECEBIDAS
     * NO EXERCÍCIO9" do quadro INDICADORES DO FUNDEB
     * Regra do cáculo:
     *   - L11(d) - L6(b) se > 0
     *   - L11.1(d) - L6.1(b) se > 0
     *   - L11.2(d) - L6.2(b) se > 0
     *   - L11.3(d) - L6.3(b) se > 0
     *   - L11.4(d) - L6.4(b) se > 0
     * @var int[]
     */
    protected $calcularDeficitIndicadoresFundeb = [
        ['despesa' => 56, 'receita' => 19],
        ['despesa' => 57, 'receita' => 20],
        ['despesa' => 58, 'receita' => 24],
        ['despesa' => 59, 'receita' => 28],
        ['despesa' => 60, 'receita' => 32],
    ];

    public function processar()
    {
        $this->processaLinhas($this->linhas);

        $this->processarRPsMDE();
        $this->criaProriedadesValor();
        $this->totalizarLinhas();

        $this->organizaLinhas();
    }

    public function processaLinhasSimplificado()
    {
        $this->processar();
        return $this->linhasSimplificado();
    }


    protected function totalizarLinhas()
    {
        $this->calcularSoma();
        //Calcula 20% da Linha 4
        $this->linhas[17]->previsao_atualizada *= 0.2;
        $this->linhas[17]->arrecadado_acumulado *= 0.2;
        $this->calcularSubtracao();
        $this->posTotalizar();

        /**
         * Zera o valor da coluna a_liquidar quando relatório emitido antes do sexto bimestre
         */
        if ($this->periodo->getCodigo() < 11) {
            foreach ($this->linhas as $linha) {
                if (isset($linha->a_liquidar)) {
                    $linha->a_liquidar = 0;
                }
            }
        }
    }

    public function posTotalizar()
    {
        $this->linhas[37]->valor = $this->linhas[37]->saldo_anterior_acumulado;
        $this->processamentosManuaisLinhasIndicadores();
        $this->calcularDeficitIndicadoresFundeb();
        $this->processaLinhasControleDisponabilidadeFinanceira();
    }

    /**
     * Calcular as linhas:
     * 19- TOTAL DAS DESPESAS CUSTEADAS COM SUPERÁVIT DO FUNDEB
     *   19.1- Total das Despesas custeadas com FUNDEB - Impostos e Transferências de Impostos
     *   19.2- Total das Despesas custeadas com FUNDEB - Complementação da União (VAAF + VAAT + VAAR)
     */
    protected function processamentosManuaisLinhasIndicadores()
    {
        $this->zeraPropriedadesLinhas([68, 69, 70]);

        /**
         * Recalcular as linhas abaixo de acordo com o período
         *  - 20 -> 6.1- FUNDEB - Impostos e Transferências de Impostos
         *  - 24 -> 6.2- FUNDEB - Complementação da União - VAAF
         *  - 28 -> 6.3- FUNDEB - Complementação da União - VAAT
         *  - 32 -> 6.4- FUNDEB - Complementação da União - VAAR
         *  - 57 -> 11.1- Total das Despesas custeadas com FUNDEB - Impostos e Transferências de Impostos
         *  - 58 -> 11.2- Total das Despesas custeadas com FUNDEB - Complementação da União - VAAF
         *  - 59 -> 11.3- Total das Despesas custeadas com FUNDEB - Complementação da União - VAAT
         *  - 60 -> 11.4- Total das Despesas custeadas com FUNDEB - Complementação da União - VAAR
         */
        $ordensLinhas = [20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 57, 58, 59, 60];
        $this->calculaExercicioAnteriorLinhasIndicadores($ordensLinhas);


        $novasLinhas = $this->buscarLinhasPorOrdem($ordensLinhas);
        // separa os estruturais das linhas para usar como filtro e otimizar a execução do balancete
        $estruturaisReceita = $this->filtrarContasConfiguracaoLinha($novasLinhas, self::ORIGEM_RECEITA);
        $estruturaisDespesa = $this->filtrarContasConfiguracaoLinha($novasLinhas, self::ORIGEM_DESPESA);

        if ($this->periodo->getCodigo() > 6) {
            $this->processarPrimeiroQuadrimestre(
                $novasLinhas,
                $estruturaisReceita,
                $estruturaisDespesa
            );
        }

        if ($this->periodo->getCodigo() > 7) {
            $this->processarAposPrimeiroQuadrimestre(
                $this->buscarLinhasPorOrdem($ordensLinhas),
                $estruturaisReceita,
                $estruturaisDespesa
            );
        }

        $this->calculaColunaSuperavitPermitido();
        $this->calculaValoresManuaisColunasIndicadores();
        $this->somarLinha(68, [69, 70]);
    }

    /**
     * Calcula as colunas do exercício anterior para as linhas 23.1 e 23.2
     */
    protected function calculaExercicioAnteriorLinhasIndicadores($ordensLinhas)
    {
        $novasLinhas = $this->buscarLinhasPorOrdem($ordensLinhas);
        foreach ($novasLinhas as $linha) {
            if ((int)$linha->origem === self::ORIGEM_RECEITA) {
                $this->processaReceita($this->getBalanceteReceitaExercicioAnterior(), $linha);
            }
            if ((int)$linha->origem === self::ORIGEM_DESPESA) {
                $this->processaDespesa($this->getBalanceteDespesaExercicioAnterior(), $linha);
            }
        }

        $this->totalizarNovasLinhasIndicadores($novasLinhas);

        $this->calculaColunaAnteriorFundebImpostosTransferencias($novasLinhas);
        $this->calculaColunaAnteriorFundebComplementosUniao($novasLinhas);
    }

    /**
     * Usado para calcular as colunas:
     * - VALOR DE SUPERÁVIT APLICADO ATÉ O PRIMEIRO QUADRIMESTRE
     * - VALOR APLICADO APÓS O PRIMEIRO QUADRIMESTRE
     * @param array $novasLinhas
     * @param $colunaCalcular
     * @return void
     */
    protected function calcularColunaEspecificaLinhaIndicadores(array $novasLinhas, $colunaCalcular)
    {
        $linhasCalcular = [69, 70];
        $receita = [
            69 => [20],
            70 => [24, 28, 32]
        ];

        $despesa = [
            69 => [57],
            70 => [58, 59, 60]
        ];

        foreach ($linhasCalcular as $ordem) {
            $arrecadado = 0;
            $empenhado = 0;

            foreach ($receita[$ordem] as $ordemReceita) {
                $arrecadado += $novasLinhas[$ordemReceita]->arrecadado_acumulado;
            }
            foreach ($despesa[$ordem] as $ordemReceita) {
                $empenhado += $novasLinhas[$ordemReceita]->empenhado_liquido_acumulado;
            }
            $this->linhas[$ordem]->{$colunaCalcular} = $empenhado - $arrecadado;
        }
    }

    protected function calculaColunasPrimerioQuadrimestre(array $novasLinhas)
    {
        $this->totalizarNovasLinhasIndicadores($novasLinhas);
        $this->calcularColunaEspecificaLinhaIndicadores($novasLinhas, 'superavit_aplic_1quadr');
    }

    /**
     * @param $novasLinhas
     * @return void
     */
    protected function calculaColunasAposPrimeiroQuadrimestre($novasLinhas)
    {
        $this->totalizarNovasLinhasIndicadores($novasLinhas);
        $this->calcularColunaEspecificaLinhaIndicadores($novasLinhas, 'aplic_apos_1q');
    }

    /**
     *
     * @param array $novasLinhas
     * @return void
     */
    protected function totalizarNovasLinhasIndicadores(array &$novasLinhas)
    {
        foreach ($novasLinhas as $linha) {
            foreach ($linha->colunas as $coluna) {
                $linha->{$coluna->coluna} = $coluna->valor;
            }
        }
        $totalizarSoma = [
            20 => [21, 22, 23], // 6.1- FUNDEB - Impostos e Transferências de Impostos
            24 => [25, 26, 27], // 6.2- FUNDEB - Complementação da União - VAAF
            28 => [29, 30, 31], // 6.3- FUNDEB - Complementação da União - VAAT
            32 => [33, 34, 35], // 6.4- FUNDEB - Complementação da União - VAAR
        ];
        foreach ($totalizarSoma as $linhaTotalizar => $somarLinhas) {
            $this->somarLinhaEspecifica($novasLinhas, $linhaTotalizar, $somarLinhas);
        }
    }

    protected function somarLinhaEspecifica(array &$linhas, $ordemLinha, array $somar)
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

    /**
     * Calcula linha: 19.1- Total das Despesas custeadas com FUNDEB - Impostos e Transferências de Impostos
     * Coluna:
     *  - (s) VALOR DE SUPERÁVIT PERMITIDO NO EXERCÍCIO ANTERIOR
     *    Somar linhas: (L6.1) * 10 / 100
     *  - (t) VALOR NÃO APLICADO NO EXERCÍCIO ANTERIOR
     *    Somar linhas: (L6.1) - (L11.1(d ou e) - L11.1(i))
     * Receita: ordem 20 -> 6.1- FUNDEB - Impostos e Transferências de Impostos
     * Despesa: ordem 57 -> 11.1- Total das Despesas custeadas com FUNDEB - Impostos e Transferências de Impostos
     * @param array $novasLinhas
     * @return void
     */
    protected function calculaColunaAnteriorFundebImpostosTransferencias(array $novasLinhas)
    {
        $arrecadado = $novasLinhas[20]->arrecadado_acumulado;
        $linhaDespesa = $novasLinhas[57];
        $valorDespesa = $linhaDespesa->liquidado_acumulado;
        if ($this->periodo->getCodigo() == 11) {
            $valorDespesa = $linhaDespesa->empenhado_liquido_acumulado;
        }

        $deficitFundeb = $linhaDespesa->empenhado_liquido_acumulado - $arrecadado;
        $deficitFundeb = $deficitFundeb < 0 ? 0 : $deficitFundeb;

        $this->linhas[69]->vlr_superavit_ex_ant = $arrecadado * 10 / 100;
        $this->linhas[69]->vlr_naplic_ex_ant = $arrecadado - $valorDespesa - $deficitFundeb;
    }

    /**
     * Calcula linha: 19.2- Total das Despesas custeadas com FUNDEB - Complementação da União (VAAF + VAAT + VAAR)
     * Coluna:
     *  - VALOR DE SUPERÁVIT PERMITIDO NO EXERCÍCIO ANTERIOR
     *    Somar linhas: (L6.2 .6.3 + 6.4) * 10 / 100
     *  - VALOR NÃO APLICADO NO EXERCÍCIO ANTERIOR
     *    Somar linhas:(L6.2 L6.3 + L6.4) - ((L11.2 + L11.3 + L11.4(d ou e)) - (L11.2 + L11.3 + L11.4(i)))
     *  Receitas:
     *  ordem 24 -> 6.2- FUNDEB - Complementação da União - VAAF
     *  ordem 28 -> 6.3- FUNDEB - Complementação da União - VAAT
     *  ordem 32 -> 6.4- FUNDEB - Complementação da União - VAAR
     * Despesas:
     *  ordem 58 -> 11.2- Total das Despesas custeadas com FUNDEB - Complementação da União VAAF
     *  ordem 59 -> 11.3- Total das Despesas custeadas com FUNDEB - Complementação da União VAAT
     *  ordem 60 -> 11.4- Total das Despesas custeadas com FUNDEB - Complementação da União VAAR
     * @param array $novasLinhas
     * @return void
     */
    protected function calculaColunaAnteriorFundebComplementosUniao(array $novasLinhas)
    {
        // calcula receita
        $arrecadado = 0;
        foreach ([24, 28, 32] as $ordem) {
            $arrecadado += $novasLinhas[$ordem]->arrecadado_acumulado;
        }

        // calcula a despesa
        $valorDespesa = 0; // (d ou e)
        $deficitFundeb = 0; // (i)
        foreach ([58, 59, 60] as $ordem) {
            $linhaDespesa = $novasLinhas[$ordem];
            $valor = $linhaDespesa->liquidado_acumulado;
            if ($this->periodo->getCodigo() == 11) {
                $valor = $linhaDespesa->empenhado_liquido_acumulado;
            }
            $valorDespesa += $valor;

            $deficit = $linhaDespesa->empenhado_liquido_acumulado - $arrecadado;
            $deficitFundeb += $deficit < 0 ? 0 : $deficit;
        }

        $this->linhas[69]->vlr_superavit_ex_ant = $arrecadado * 10 / 100;
        $this->linhas[69]->vlr_naplic_ex_ant = $arrecadado - $valorDespesa - $deficitFundeb;
    }

    /**
     * Processa o calculo do quadro: CONTROLE DA DISPONIBILIDADE FINANCEIRA E CONCILIAÇÃO BANCÁRIA
     */
    protected function processaLinhasControleDisponabilidadeFinanceira()
    {
        // como a configuração do quadro é a mesma, mudando apenas o recurso da coluna, eu peguei a configuração apenas
        // de uma linha para cada lado da coluna.
        $valoresFundeb = $this->buscarValoresDisponibilidadeFinanceira(126);
        $valoresSalarioEducacao = $this->buscarValoresDisponibilidadeFinanceira(133);

        // documentos a contabilizar em cada linha
        $documentosLinha52 = [121, 130, 140, 141, 142, 143, 160, 163, 150, 153];
        $documentosLinha53 = [120, 161, 151, 162, 152, 131, 140, 141, 142, 143];

        // Valores movimentados a DÉBITO nas contas EXCETO os valores constantes na linha 52
        $this->linhas[126]->valor_fundeb = 0; // linha 48 - coluna fundeb
        $this->linhas[129]->valor_fundeb = 0; // linha 52 - coluna fundeb
        $this->linhas[126]->valor_salario_educacao = 0; // linha 48 - coluna salário educação
        $this->linhas[129]->valor_salario_educacao = 0; // linha 52 - coluna salário educação

        // Valores movimentados a CRÉDITO nas contas EXCETO os valores constantes na linha 53
        $this->linhas[127]->valor_fundeb = 0; // linha 50 - coluna fundeb
        $this->linhas[130]->valor_fundeb = 0; // linha 53 - coluna fundeb
        $this->linhas[127]->valor_salario_educacao = 0; // linha 50 - coluna salário educação
        $this->linhas[130]->valor_salario_educacao = 0; // linha 53 - coluna salário educação

        foreach ($valoresFundeb as $valorFundeb) {
            if (in_array($valorFundeb->codigo_documento, $documentosLinha52)) {
                $this->linhas[129]->valor_fundeb += $valorFundeb->debito;
            } else {
                $this->linhas[126]->valor_fundeb += $valorFundeb->debito;
            }

            if (in_array($valorFundeb->codigo_documento, $documentosLinha53)) {
                $this->linhas[130]->valor_fundeb += $valorFundeb->credito;
            } else {
                $this->linhas[127]->valor_fundeb += $valorFundeb->credito;
            }
        }


        foreach ($valoresSalarioEducacao as $valorEducacao) {
            if (in_array($valorEducacao->codigo_documento, $documentosLinha52)) {
                $this->linhas[129]->valor_salario_educacao += $valorEducacao->debito;
            } else {
                $this->linhas[126]->valor_salario_educacao += $valorEducacao->debito;
            }

            if (in_array($valorEducacao->codigo_documento, $documentosLinha53)) {
                $this->linhas[130]->valor_salario_educacao += $valorEducacao->credito;
            } else {
                $this->linhas[127]->valor_salario_educacao += $valorEducacao->credito;
            }
        }

        // atualiza os valores da linha 34 - DISPONIBILIDADE FINANCEIRA
        $this->linhas[125]->valor_fundeb = $this->linhas[125]->colunas[0]->valor;
        $this->linhas[125]->valor_salario_educacao = $this->linhas[132]->colunas[0]->valor;

        $this->linhas[128]->valor_fundeb = 0;
        $this->linhas[128]->valor_salario_educacao = 0;

        $linhasFundeb = range(125, 130);
        $linhasSalarioEducacao = range(132, 138);
        // soma o valor manual
        foreach ($linhasFundeb as $index => $ordem) {
            $linhaFundeb = $this->linhas[$ordem];
            $linhaFundeb->valor_fundeb += $linhaFundeb->colunas[0]->valorManual;

            // pega a linha equivalente da coluna salário educação
            $ordemLinhaSalarioEducacao = $linhasSalarioEducacao[$index];
            $linhaSalarioEducacao = $this->linhas[$ordemLinhaSalarioEducacao];
            $linhaFundeb->valor_salario_educacao += $linhaSalarioEducacao->colunas[0]->valorManual;
        }
    }

    /**
     * @return void
     */
    protected function calculaValoresManuaisColunasIndicadores()
    {
        $this->zeraPropriedadesLinhas([68, 69, 70]);

        foreach ($this->linhas[69]->colunas as $coluna) {
            $this->linhas[69]->{$coluna->coluna} += $coluna->valorManual;
        }
        foreach ($this->linhas[70]->colunas as $coluna) {
            $this->linhas[70]->{$coluna->coluna} += $coluna->valorManual;
        }
    }

    /**
     * Calcula a coluna (x) VALOR DE SUPERÁVIT PERMITIDO NO EXERCÍCIO ANTERIOR NÃO APLICADO NO EXERCÍCIO ATUAL
     *  - Se (u) for maior ou igual a (t), então x = 0
     *  - Se (u) for menor que (t) e (t) for menor ou igual a (s), então x = (t) - (u)
     *  - Se (u) for maior que (t) e (t) for maior que (s), então x = (s) - (u)
     * @return void
     */
    private function calculaColunaSuperavitPermitido()
    {
        $linhas = [69, 70];
        foreach ($linhas as $ordem) {
            $s = $this->linhas[$ordem]->vlr_superavit_ex_ant;
            $t = $this->linhas[$ordem]->vlr_naplic_ex_ant;
            $u = $this->linhas[$ordem]->superavit_aplic_1quadr;
            $x = 0;
            if ($u < $t && $t <= $s) {
                $x = $t - $s;
            }
            if ($u > $t && $t > $s) {
                $x = $s - $t;
            }

            $this->linhas[$ordem]->superavit_permitido = $x;
        }
    }

    public function addParserVariaveisValores()
    {
        $this->parser->setVariavel('valor_rp_np', $this->linhas[88]->valor);
    }

    /**
     * Calcula as linhas:
     * 15 - MÍNIMO DE 70% DO FUNDEB NA REMUNERAÇÃO DOS PROFISSIONAIS DA EDUCAÇÃO BÁSICA
     * 16 - PERCENTUAL DE 50% DA COMPLEMENTAÇÃO DA UNIÃO AO FUNDEB - VAAT NA EDUCAÇÃO INFANTIL
     * 17 - MÍNIMO DE 15% DA COMPLEMENTAÇÃO DA UNIÃO AO FUNDEB - VAAT EM DESPESAS DE CAPITAL
     * Fórmula aplicada nas colunas:
     *  Valor Exigido (j)
     *    - linhas 15: (L6(b) - L6.4(b)) * 70 / 100
     *    - linhas 16: L6.3(b) * 50 / 100
     *    - linhas 17: L6.3(b) * 15 / 100
     *
     *  Valor aplicadp (k)
     *    - linhas 15: L12(d ou e)
     *    - linhas 16: L13(d ou e)
     *    - linhas 17: L14(d ou e)
     *
     *  Valor Considerado após deduções (l)
     *   - linhas 15: L12(d ou e) - L12(h)
     *   - linhas 16: L13(d ou e) - L13(h)
     *   - linhas 17: L14(d ou e) - L14(h)
     *
     *  % Aplicado (m)
     *   - linhas 15: [L15(l) / [L6(b) - L6.4(b)] *100
     *   - linhas 16: L16(l) / L6.3(b) * 100
     *   - linhas 17: L17(l) /  L6.3(b) * 100
     * @return void
     */
    private function calculaIndicadores()
    {
        foreach ([64, 65, 66] as $ordem) {
            $this->linhas[$ordem]->valor_exigido = 0;
            $this->linhas[$ordem]->valor_aplicado = 0;
            $this->linhas[$ordem]->valor_considerado = 0;
            $this->linhas[$ordem]->percentual_aplicado = 0;
        }

        $l6 = $this->linhas[19];
        $l6Dot4 = $this->linhas[32];
        $l6Dot3 = $this->linhas[28];

        // calculo Valor Exigido (j)
        $vlrL6Dot4 = $l6Dot4->arrecadado_acumulado;
        $vlrL6 = $l6->arrecadado_acumulado;
        $ReceitaExtetoVAAR = $vlrL6 - $vlrL6Dot4;
        $this->linhas[64]->valor_exigido = ($ReceitaExtetoVAAR) * 70 / 100;
        $vlrL6dot3 = $l6Dot3->arrecadado_acumulado;
        $this->linhas[65]->valor_exigido = $vlrL6dot3 * 50 / 100;
        $this->linhas[66]->valor_exigido = $vlrL6dot3 * 50 / 100;


        $l12 = $this->linhas[61];
        $l13 = $this->linhas[62];
        $l14 = $this->linhas[63];

        $propriedade = 'liquidado_acumulado';
        if ($this->periodo->getCodigo() == 11) {
            $propriedade = 'empenhado_liquido_acumulado';
        }

        // calculo do valor Valor aplicadp (k)
        $this->linhas[64]->valor_aplicado = $l12->{$propriedade};
        $this->linhas[65]->valor_aplicado = $l13->{$propriedade};
        $this->linhas[66]->valor_aplicado = $l14->{$propriedade};

        // calculo dp Valor Considerado após deduções (l)
        $this->linhas[64]->valor_considerado = $l12->{$propriedade} - $l12->rpnp_sem_dc;
        $this->linhas[65]->valor_considerado = $l13->{$propriedade} - $l13->rpnp_sem_dc;
        $this->linhas[66]->valor_considerado = $l14->{$propriedade} - $l14->rpnp_sem_dc;

        // calculo dp % Aplicado (m)
        if ($this->linhas[64]->valor_exigido > 0) {
            $this->linhas[64]->percentual_aplicado = ($this->linhas[64]->valor_considerado / $ReceitaExtetoVAAR) * 100;
        }

        if ($vlrL6dot3 > 0) {
            $this->linhas[65]->percentual_aplicado = $this->linhas[65]->valor_considerado / $vlrL6dot3 * 100;
            $this->linhas[66]->percentual_aplicado = $this->linhas[66]->valor_considerado / $vlrL6dot3 * 100;
        }

        // percentuals das linhas simplificado
        $this->linhas[64]->percentual = 70;
        $this->linhas[65]->percentual = 50;
        $this->linhas[66]->percentual = 15;
    }


    /**
     * Calcula a linha: 29- APLICAÇÃO EM MDE SOBRE A RECEITA RESULTANTE DE IMPOSTOS
     * @return void
     */
    private function calculaApuracaoLimiteMinimoConstitucional()
    {
        $vlrL3 = $this->linhas[16]->arrecadado_acumulado;
        $this->calculaApuracaoDespesasParaFinsLimiteMInimoConstitucional();
        $this->linhas[91]->percentual = 25;
        $this->linhas[91]->valor_aplicado = $this->linhas[90]->valor;
        $this->linhas[91]->percentual_aplicado = 0;
        if ($vlrL3 > 0) {
            $this->linhas[91]->percentual_aplicado = $this->linhas[91]->valor_aplicado / $vlrL3 * 100;
        }
    }

    /**
     * Calcula as linhas
     * 22- TOTAL DAS DESPESAS DE MDE CUSTEADAS COM RECURSOS DE IMPOSTOS = L20(d ou e)
     * 23- TOTAL DAS RECEITAS TRANSFERIDAS AO FUNDEB = (L4)
     * 24- (-) RECEITAS DO FUNDEB NÃO UTILIZADAS NO EXERCÍCIO, EM VALOR SUPERIOR A 10% = L18(q)
     * 25- (-) SUPERÁVIT PERMITIDO NO EXERCÍCIO IMEDIATAMENTE ANTERIOR NÃO APLICADO NO EXERCÍCIO ATUAL = L19.1(x)
     * 26- (-) RESTOS A PAGAR NÃO PROCESSADOS INSCRITOS NO EXERCÍCIO SEM DISPONIBILIDADE FINANCEIRA
     * 27- (-) CANCELAMENTO, NO EXERCÍCIO, DE RESTOS A PAGAR INSCRITOS COM ...= (L30.1(af) + L30.2(af))
     */
    protected function calculaApuracaoDespesasParaFinsLimiteMInimoConstitucional()
    {
        $propriedade = 'liquidado_acumulado';
        if ($this->periodo->getCodigo() == 11) {
            $propriedade = 'empenhado_liquido_acumulado';
        }
        // L22 = l20 (d ou e)
        $vlrL20 = $this->linhas[71]->{$propriedade};
        // L23 = l4
        $vlrL4 = $this->linhas[17]->arrecadado_acumulado;
        //  24- (-) RECEITAS DO FUNDEB NÃO UTILIZADAS NO EXERCÍCIO, EM VALOR SUPERIOR A 10% = L18(q)
        $l18 = $this->calculaLinha18();
        $vlrL18 = $l18->valor_nao_aplicado_excedente;
        // 25- (-) SUPERÁVIT PERMITIDO NO EXERCÍCIO IMEDIATAMENTE ANTERIOR NÃO APLICADO NO EXERCÍCIO ATUAL = L19.1(x)
        $vlrL19dot1 = $this->linhas[69]->superavit_permitido;
        // 26- (-) RESTOS A PAGAR NÃO PROCESSADOS INSCRITOS NO EXERCÍCIO SEM DISPONIBILIDADE FINANCEIRA...
        $vlrL26 = $this->linhas[88]->valor;
        // 27- (-) CANCELAMENTO, NO EXERCÍCIO, DE RESTOS A PAGAR INSCRITOS COM ...  = (L30.1(af) + L30.2(af))
        $this->linhas[89]->valor = $this->linhas[93]->total_anulacoes + $this->linhas[94]->total_anulacoes;
        $this->linhas[90]->valor = ($vlrL20 + $vlrL4) - ($vlrL18 + $vlrL19dot1 + $vlrL26 + $this->linhas[89]->valor);
    }

    /**
     * Calcula linha 18- TOTAL DA RECEITA RECEBIDA E NÃO APLICADA NO EXERCÍCIO
     * Fórmula aplicada nas colunas:
     *   - Valor máximo permitido (n):  L6(b) * 10 / 100
     *   - Valor não aplicado (o): L6(b) -  [L11(d ou e) - L11(i)]
     *   - Valor não aplicado após ajuste (p): L18(o) + [( L11(h) - L11(i)) se > 0)]
     *   - Valor não aplicado excedente ao máximo permitido (q): L18(p) - L18(n) (somente se > 0)
     *   - % não aplicado (r):  L18(p) / L6(b) * 100
     * @return \stdClass
     */
    protected function calculaLinha18()
    {
        $l6 = $this->linhas[19];
        $l11 = $this->linhas[56];
        $l18 = $this->linhas[67];
        $l18->percentual_aplicado = 0;

        $propriedade = 'liquidado_acumulado';
        if ($this->periodo->getCodigo() == 11) {
            $propriedade = 'empenhado_liquido_acumulado';
        }

        $vlrL11 = $l11->{$propriedade};
        // Valor máximo permitido (n)
        $l18->valor_maximo = $l6->arrecadado_acumulado * 10 / 100;
        // Valor não aplicado (o)
        $l18->valor_nao_aplicado = $l6->arrecadado_acumulado - ($vlrL11 - $l11->deficit_fundeb);
        // Valor não aplicado após ajuste (p)
        $vlr = $l11->rpnp_sem_dc - $l11->deficit_fundeb;
        $vlr = $vlr > 0 ? $vlr : 0;
        $l18->valor_nao_aplicado_apos_ajuste = $l18->valor_nao_aplicado + $vlr;

        // Valor não aplicado excedente ao máximo permitido (q)
        $vlr = $l18->valor_nao_aplicado_apos_ajuste - $l18->valor_maximo;
        $vlr = $vlr > 0 ? $vlr : 0;
        $l18->valor_nao_aplicado_excedente = $vlr;
        if ($l6->arrecadado_acumulado > 0) {
            $l18->percentual_aplicado = $l18->valor_nao_aplicado_apos_ajuste / $l6->arrecadado_acumulado * 100;
        }

        return $l18;
    }

    protected function linhasSimplificado()
    {
        $linhas = [];

        $this->calculaIndicadores();
        $this->calculaApuracaoLimiteMinimoConstitucional();
        $descricoes = [
            91 => 'Mínimo Anual de <18% / 25%> das Receitas de Impostos na Manutenção e Desenvolvimento do Ensino',
            64 => 'Mínimo Anual de 70% do FUNDEB na Remuneração dos Profissionais da Educação Básica',
            65 => 'Percentual de 50% da Complementação da União ao FUNDEB (VAAT) na Educação Infantil',
            66 => 'Mínimo de 15% da Complementação da União ao FUNDEB (VAAT) em Despesas de Capital',
        ];

        foreach ($descricoes as $index => $descricao) {
            $linha = $this->linhas[$index];
            $linhas[] = $this->createObjetoSimplificado(
                $descricao,
                $linha->valor_aplicado,
                $linha->percentual,
                round($linha->percentual_aplicado, 2)
            );
        }
        return $linhas;
    }

    /**
     * Realiza o calculo da coluna (j) "DESPESAS EMPENHADAS EM VALOR SUPERIOR AO TOTAL DAS RECEITAS RECEBIDAS
     * NO EXERCÍCIO9" do quadro INDICADORES DO FUNDEB conforme o mapeamento presente na propriedade
     * $calcularDeficitIndicadoresFundeb
     * @return void
     */
    protected function calcularDeficitIndicadoresFundeb()
    {
        foreach ($this->calcularDeficitIndicadoresFundeb as $mapa) {
            $lReceita = $this->linhas[$mapa['receita']];
            $lDespesa = $this->linhas[$mapa['despesa']];
            $deficitFundeb = $lDespesa->empenhado_liquido_acumulado - $lReceita->arrecadado_acumulado;
            if ($deficitFundeb < 0) {
                $deficitFundeb = 0;
            }
            $lDespesa->deficit_fundeb = $deficitFundeb;
        }
    }

    public function getLinhasProcessadas()
    {
        $this->processar();
        $this->calculaIndicadores();
        $this->calculaApuracaoLimiteMinimoConstitucional();
        return $this->linhas;
    }
}
