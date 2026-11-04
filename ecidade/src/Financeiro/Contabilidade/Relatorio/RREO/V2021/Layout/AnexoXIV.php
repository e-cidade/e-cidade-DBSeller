<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace ECidade\Financeiro\Contabilidade\Relatorio\RREO\V2021\Layout;

use ECidade\Financeiro\Contabilidade\Relatorio\Layout;
use ECidade\Financeiro\Contabilidade\Relatorio\RREO\V2021\Simplificado;
use Exception;
use ParameterException;
use PDFDocument;

/**
 * Class AnexoXIV
 * @package ECidade\Financeiro\Contabilidade\Relatorio\RREO\V2018\Layout
 * @property Simplificado $relatorio
 */
class AnexoXIV extends Layout
{
    const HEADER_1 = 'DEMONSTRATIVO SIMPLIFICADO DO RELATÓRIO RESUMIDO DA EXECUÇÃO ORÇAMENTÁRIA';

    const HEADER_2 = 'ORÇAMENTOS FISCAL E DA SEGURIDADE SOCIAL';

    const LARGURA_BALANCO_ORCAMENTARIO = 153.5688;

    const LARGURA_DESPESAS_FUNCAO_SUBFUNCAO = 153.5688;

    const LARGURA_RECEITA_CORRENTE_LIQUIDA = 153.5688;

    const LARGURA_RECEITAS_DESPESAS_REGIME_PROPRIO_PREVIDENCIA_SERVIDORES = 153.5688;

    const LARGURA_RESULTADO_NOMINAL_PRIMARIO = 153.5688;

    const LARGURA_RESTOS_PAGAR_PODER_MINISTERIO_PUBLICO = 153.5688;

    const LARGURA_DESPESAS_MANUTENCAO_DESENVOLVIMENTO_ENSINO = 153.5688;

    const LARGURA_RECEITAS_OPERACOES_CREDITO_DESPESAS_CAPITAL = 153.5688;

    const LARGURA_PROJECAO_ATUARIAL_REGIMES_PREVIDENCIA = 153.5688;

    const LARGURA_RECEITA_ALIENACAO_ATIVOS_APLICACAO_RECURSOS = 153.5688;

    const LARGURA_DESPESAS_ACOES_SERVICOS_PUBLICOS_SAUDE = 153.5688;

    const LARGURA_DESPESAS_CARATER_CONTINUADO_DERIVADAS_PPP = 153.5688;

    const LARGURA_ATE_BIMESTRE = 123.4312;

    const LARGURA_META_FIXADA_ANEXO_METAS_FISCAIS_LDO = 29.52;

    const LARGURA_CANCELAMENTO_ATE_BIMESTRE = 38.1;

    const LARGURA_PAGAMENTO_ATE_BIMESTRE = 27.9056;

    const LARGURA_SALDO_PAGAR = 27.9056;

    const LARGURA_RESULTADO_APURADO_ATE_BIMESTRE = 38.1;

    const LARGURA_RELACAO_META = 55.8112;

    const LARGURA_VALOR_APURADO_ATE_BIMESTRE = 29.52;

    const LARGURA_LIMITES_CONSTITUCIONAIS_ANUAIS = 93.9112;

    const LARGURA_VALOR_APURADO_ATE_BIMESTRE_2 = 67.62;

    const LARGURA_EXERCICIO = 29.52;

    const LARGURA_VALOR_APURADO_EXERCICIO_CORRENTE = 123.4312;

    const LARGURA_MINIMO_APLICAR_EXERCICIO = 38.1;

    const LARGURA_APLICADO_ATE_BIMESTRE = 55.8112;

    const LARGURA_LIMITE_CONSTITUCIONAL_ANUAL = 93.9112;

    const LARGURA_SALDO_REALIZAR = 55.8112;

    const LARGURA_35_EXERCICIO = 27.9056;

    const LARGURA_20_EXERCICIO = 27.9056;

    const LARGURA_10_EXERCICIO = 38.1;

    const LARGURA_SALDO_NAO_REALIZADO = 55.8112;

    const LARGURA_INSCRICAO = 29.52;

    /**
     * @var array
     */
    private $quadros;

    /**
     * @throws ParameterException
     * @throws Exception
     */
    protected function montar()
    {
        $this->montarLinhaIntroducao();

        if ($this->quadros[Simplificado::EMITIR_BALANCO_ORCAMENTARIO]) {
            if (db_getsession("DB_itemmenu_acessado") == 8079) {
                $linhas = $this->relatorio->getBalancoOrcamentario();
                $this->montarCabecalhoBalancoOrcamentario($linhas);
            } else {
                $linhas = $this->relatorio->getBalancoOrcamentarioNovo();
                $this->montarCabecalhoBalancoOrcamentarioNovo($linhas);
            }
        }

        if ($this->quadros[Simplificado::EMITIR_DESPESA_FUNCAO_SUBFUNCAO]) {
            $linhas = $this->relatorio->getDemostrativoDespesaPorFuncaoSubfuncao();
            $this->montarCabecalhoDespesasPorFuncaoSubfuncao($linhas);
        }

        if ($this->quadros[Simplificado::EMITIR_RECEITA_CORRENTE_LIQUIDA]) {
            if (db_getsession("DB_itemmenu_acessado") == 8079) {
                $linhas = $this->relatorio->getReceitaCorrenteLiquida();
            } else {
                $linhas = $this->relatorio->getReceitaCorrenteLiquidaNovo();
            }
            $this->montarCabecalhoReceitaCorrenteLiquida($linhas);
        }

        if ($this->quadros[Simplificado::EMITIR_DESPESAS_RECEITAS_RPPS]) {
            if (db_getsession("DB_itemmenu_acessado") == 8064 || db_getsession("DB_itemmenu_acessado") == 8079) {
                $linhas = $this->relatorio->getRegimeDePrevidencia();
                $this->montarCabecalhoReceitasDespesasRegimeProprioPrevidenciaServidores($linhas);
            } else {
                $linhas = $this->relatorio->getRegimeDePrevidenciaNovo();
                $this->montarCabecalhoReceitasDespesasRegimeProprioPrevidenciaServidoresNovo($linhas);
            }
        }

        if ($this->quadros[Simplificado::EMITIR_RESULTADO_NOMINAL_PRIMARIO]) {
            $this->pdf->addPage();

            if (db_getsession('DB_anousu') < 2022) {
                $linhas = $this->relatorio->getResultadoNominalPrimario();
            } else {
                $linhas = $this->relatorio->getResultadoNominalPrimarioNovo();
            }
            $this->montarCabecalhoResultadosNominalPrimario($linhas);
        }

        if ($this->quadros[Simplificado::EMITIR_RESTOS_A_PAGAR]) {
            $linhas = $this->relatorio->getRestosAPagar();
            $this->montarCabecalhoRestosPagarPoderMinisterioPublico($linhas);
        }

        if ($this->quadros[Simplificado::EMITIR_DESPESAS_MDE]) {
            if (db_getsession("DB_itemmenu_acessado") == 8079) {
                $linhas = $this->relatorio->getDespesasComManutencaoDesenvolvimentoEnsino();
                $this->montarCabecalhoDespesasComManutencaoDesenvolvimentoEnsino($linhas);
            } else {
                $linhas = $this->relatorio->getDespesasComManutencaoDesenvolvimentoEnsinoNovo();
                $this->montarCabecalhoDespesasComManutencaoDesenvolvimentoEnsinoNovo($linhas);
            }
        }

        if ($this->quadros[Simplificado::EMITIR_OPERACAO_DE_CREDITO]) {
            $linhas = $this->relatorio->getReceitasOperacoesCreditoDespesasCapital();
            $this->montarCabecalhoReceitasOperacoesCreditoDespesasCapital($linhas);
        }

        if ($this->quadros[Simplificado::EMITIR_PROJECAO_ATUARIAL_RPPS]) {
            $linhas = $this->relatorio->getProjecaoAtuarialRegimesPrevidencia();
            $this->montarCabecalhoProjecaoAtuarialRegimesPrevidencia($linhas);
        }

        if ($this->quadros[Simplificado::EMITIR_ALIENACAO_ATIVOS]) {
            $linhas = $this->relatorio->getReceitaAlienacaoAtivosAplicacaoRecursos();
            $this->montarCabecalhoReceitaAlienacaoAtivosAplicacaoRecursos($linhas);
        }

        if ($this->quadros[Simplificado::EMITIR_DESPESAS_SAUDE]) {
            $this->pdf->addPage();
            if (db_getsession("DB_itemmenu_acessado") == 8079) {
                $linhas = $this->relatorio->getDespesasAcoesServicosPublicosSaude();
                $this->montarCabecalhoDespesasAcoesServicosPublicosSaude($linhas);
            } else {
                $linhas = $this->relatorio->getDespesasAcoesServicosPublicosSaudeNovo();
                $this->montarCabecalhoDespesasAcoesServicosPublicosSaude($linhas);
            }
        }

        if ($this->quadros[Simplificado::EMITIR_PPP]) {
            $linhas = $this->relatorio->getDespesasCaraterContinuadoDerivadasPPP();
            $this->montarCabecalhoDespesasCaraterContinuadoDerivadasPPP($linhas);
        }

        $this->pdf->Cell(277, 1, '', 'T');
    }

    /**
     * @return $this
     */
    private function montarLinhaIntroducao()
    {
        $this->pdf->Cell(
            138,
            static::ALTURA_LINHA,
            'RREO - Anexo XIV (LRF, art 48)',
            0,
            0,
            PDFDocument::ALIGN_LEFT
        );
        $this->pdf->Cell(
            139,
            static::ALTURA_LINHA,
            'Em reais',
            0,
            1,
            PDFDocument::ALIGN_RIGHT
        );

        return $this;
    }

    /**
     * @param array $linhas
     * @return $this
     */
    private function montarCabecalhoBalancoOrcamentario(array $linhas)
    {
        $this->addPage(count($linhas), static::ALTURA_LINHA * 2);

        $this->pdf->SetFont('arial', 'B', static::TAMANHO_FONTE);

        $x = $this->montarCabecalhoColuna(
            static::LARGURA_BALANCO_ORCAMENTARIO,
            static::ALTURA_LINHA * 2,
            'BALANÇO ORÇAMENTÁRIO'
        );
        $this->montarCabecalhoColuna(
            static::LARGURA_ATE_BIMESTRE,
            static::ALTURA_LINHA * 2,
            'Até o Bimestre',
            $x
        );

        $this->pdf->Ln();

        $this->montarLinhasBalancoOrcamentario($linhas);

        return $this;
    }

    private function montarCabecalhoBalancoOrcamentarioNovo(array $linhas)
    {
        $this->addPage(count($linhas), static::ALTURA_LINHA * 2);
        $this->pdf->SetFont('arial', 'B', static::TAMANHO_FONTE);

        $wDescricao = static::LARGURA_BALANCO_ORCAMENTARIO;
        $wValor = static::LARGURA_ATE_BIMESTRE;
        $x = $this->montarCabecalhoColuna(
            $wDescricao,
            static::ALTURA_LINHA * 2,
            'BALANÇO ORÇAMENTÁRIO'
        );
        $this->montarCabecalhoColuna(
            $wValor,
            static::ALTURA_LINHA * 2,
            'Até o Bimestre',
            $x
        );

        $this->pdf->Ln();
        foreach ($linhas as $linha) {
            $nivel = is_null($linha->valor) ? 0 : 3;
            $this->montarLinha($wDescricao, $linha->descricao, false, false, false, $nivel)
                ->montarLinha($wValor, $linha->valor, false, false, true);
        }
    }

    /**
     * @param array $linhas
     * @return $this
     */
    private function montarCabecalhoDespesasPorFuncaoSubfuncao(array $linhas)
    {
        $this->addPage(count($linhas), static::ALTURA_LINHA * 2);

        $this->pdf->SetFont('arial', 'B', static::TAMANHO_FONTE);

        $x = $this->montarCabecalhoColuna(
            static::LARGURA_DESPESAS_FUNCAO_SUBFUNCAO,
            static::ALTURA_LINHA * 2,
            'DESPESAS POR FUNÇÃO/SUBFUNÇÃO'
        );
        $this->montarCabecalhoColuna(
            static::LARGURA_ATE_BIMESTRE,
            static::ALTURA_LINHA * 2,
            'Até o Bimestre',
            $x
        );

        $this->pdf->Ln();

        $this->montarLinhasDespesasPorFuncaoSubfuncao($linhas);

        return $this;
    }

    /**
     * @param array $linhas
     * @return $this
     */
    private function montarCabecalhoReceitaCorrenteLiquida(array $linhas)
    {
        $this->addPage(count($linhas), static::ALTURA_LINHA * 2);

        $this->pdf->SetFont('arial', 'B', static::TAMANHO_FONTE);

        $x = $this->montarCabecalhoColuna(
            static::LARGURA_RECEITA_CORRENTE_LIQUIDA,
            static::ALTURA_LINHA * 2,
            'RECEITA CORRENTE LÍQUIDA - RCL'
        );
        $this->montarCabecalhoColuna(
            static::LARGURA_ATE_BIMESTRE,
            static::ALTURA_LINHA * 2,
            'Até o Bimestre',
            $x
        );

        $this->pdf->Ln();

        $this->montarLinhasReceitaCorrenteLiquida($linhas);

        return $this;
    }

    /**
     * @param array $linhas
     * @return $this
     */
    private function montarCabecalhoReceitasDespesasRegimeProprioPrevidenciaServidores(array $linhas)
    {
        $this->addPage(count($linhas), static::ALTURA_LINHA);

        $this->pdf->SetFont('arial', 'B', static::TAMANHO_FONTE);

        $x = $this->montarCabecalhoColuna(
            static::LARGURA_RECEITAS_DESPESAS_REGIME_PROPRIO_PREVIDENCIA_SERVIDORES,
            static::ALTURA_LINHA,
            'RECEITAS E DESPESAS DO REGIME PRÓPRIO DE PREVIDÊNCIA DOS SERVIDORES'
        );
        $this->montarCabecalhoColuna(
            static::LARGURA_ATE_BIMESTRE,
            static::ALTURA_LINHA,
            'Até o Bimestre',
            $x
        );

        $this->pdf->Ln();

        $this->montarLinhasReceitasDespesasRegimeProprioPrevidenciaServidores($linhas);
        return $this;
    }

    protected function montarCabecalhoReceitasDespesasRegimeProprioPrevidenciaServidoresNovo(array $linhas)
    {
        $this->addPage(count($linhas), static::ALTURA_LINHA);

        $this->pdf->SetFont('arial', 'B', static::TAMANHO_FONTE);

        $x = $this->montarCabecalhoColuna(
            static::LARGURA_RECEITAS_DESPESAS_REGIME_PROPRIO_PREVIDENCIA_SERVIDORES,
            static::ALTURA_LINHA,
            'RECEITAS E DESPESAS DO REGIME PRÓPRIO DE PREVIDÊNCIA DOS SERVIDORES'
        );
        $this->montarCabecalhoColuna(
            static::LARGURA_ATE_BIMESTRE,
            static::ALTURA_LINHA,
            'Até o Bimestre',
            $x
        );

        $this->pdf->Ln();

        foreach ($linhas as $linha) {
            $totalizar = $linha->totalizar;
            $preenche = false;
            $ateBimestre = $linha->ate_bimestre;

            $this->montarLinha(
                static::LARGURA_RECEITAS_DESPESAS_REGIME_PROPRIO_PREVIDENCIA_SERVIDORES,
                $linha->descricao,
                $totalizar,
                $preenche,
                false,
                $linha->nivel
            )
                ->montarLinha(static::LARGURA_ATE_BIMESTRE, $ateBimestre, $totalizar, $preenche, true);
        }


        $iLargura = static::LARGURA_BALANCO_ORCAMENTARIO + static::LARGURA_ATE_BIMESTRE;
        $this->pdf->Cell($iLargura, 1, "", "T", 1, "C", 0);

        return $this;
    }

    /**
     * @param array $linhas
     * @return $this
     */
    private function montarCabecalhoResultadosNominalPrimario(array $linhas)
    {
        $this->addPage(count($linhas), static::ALTURA_LINHA * 4);

        $this->pdf->SetFont('arial', 'B', static::TAMANHO_FONTE);

        $x = $this->montarCabecalhoColuna(
            static::LARGURA_RESULTADO_NOMINAL_PRIMARIO,
            static::ALTURA_LINHA * 4,
            'RESULTADOS NOMINAL E PRIMÁRIO'
        );
        $x = $this->montarCabecalhoColuna(
            static::LARGURA_META_FIXADA_ANEXO_METAS_FISCAIS_LDO,
            static::ALTURA_LINHA,
            "Meta Fixada no\nAnexo de Metas\nFiscais da LDO\n(a)",
            $x
        );
        $x = $this->montarCabecalhoColuna(
            static::LARGURA_RESULTADO_APURADO_ATE_BIMESTRE,
            static::ALTURA_LINHA * 1.334,
            "Resultado Apurado\nAté o Bimestre\n(b)",
            $x
        );
        $this->montarCabecalhoColuna(
            static::LARGURA_RELACAO_META,
            static::ALTURA_LINHA * 2,
            "% em Relação à Meta\n(b/a)",
            $x
        );

        $y = $this->pdf->GetY() + static::ALTURA_LINHA * 2;

        $this->pdf->SetY($y);
        $this->pdf->Ln();

        $this->montarLinhasResultadosNominalPrimario($linhas);

        return $this;
    }

    /**
     * @param array $linhas
     * @return $this
     */
    private function montarCabecalhoRestosPagarPoderMinisterioPublico(array $linhas)
    {
        $this->addPage(count($linhas), static::ALTURA_LINHA * 4);

        $this->pdf->SetFont('arial', 'B', static::TAMANHO_FONTE);

        $x = $this->montarCabecalhoColuna(
            static::LARGURA_RESTOS_PAGAR_PODER_MINISTERIO_PUBLICO,
            static::ALTURA_LINHA * 2,
            'RESTOS A PAGAR POR PODER E MINISTÉRIO PÚBLICO'
        );
        $x = $this->montarCabecalhoColuna(
            static::LARGURA_INSCRICAO,
            static::ALTURA_LINHA * 2,
            'Inscrição',
            $x
        );
        $x = $this->montarCabecalhoColuna(
            static::LARGURA_CANCELAMENTO_ATE_BIMESTRE,
            static::ALTURA_LINHA,
            "Cancelamento\nAté o Bimestre",
            $x
        );
        $x = $this->montarCabecalhoColuna(
            static::LARGURA_PAGAMENTO_ATE_BIMESTRE,
            static::ALTURA_LINHA,
            "Pagamento\nAté o Bimestre",
            $x
        );
        $this->montarCabecalhoColuna(
            static::LARGURA_SALDO_PAGAR,
            static::ALTURA_LINHA,
            "Saldo\na Pagar",
            $x
        );

        $y = $this->pdf->GetY() + static::ALTURA_LINHA;

        $this->pdf->SetY($y);

        $this->pdf->Ln();

        $this->montarLinhasRestosPagarPoderMinisterioPublico($linhas);

        return $this;
    }

    /**
     * @param array $linhas
     * @return $this
     */
    private function montarCabecalhoDespesasComManutencaoDesenvolvimentoEnsino(array $linhas)
    {
        $this->addPage(count($linhas), static::ALTURA_LINHA * 3);

        $this->pdf->SetFont('arial', 'B', static::TAMANHO_FONTE);

        $x = $this->montarCabecalhoColuna(
            static::LARGURA_DESPESAS_MANUTENCAO_DESENVOLVIMENTO_ENSINO,
            static::ALTURA_LINHA * 3,
            'DESPESAS COM MANUTENÇÃO E DESENVOLVIMENTO DO ENSINO'
        );
        $x = $this->montarCabecalhoColuna(
            static::LARGURA_VALOR_APURADO_ATE_BIMESTRE,
            static::ALTURA_LINHA * 1.5,
            "Valor Apurado\nAté o Bimestre",
            $x
        );
        $this->montarCabecalhoColuna(
            static::LARGURA_LIMITES_CONSTITUCIONAIS_ANUAIS,
            static::ALTURA_LINHA,
            "Limites Constitucionais Anuais",
            $x
        );

        $y = $this->pdf->GetY() + static::ALTURA_LINHA;

        $this->pdf->SetXY($x, $y);

        $x = $this->montarCabecalhoColuna(
            38.1,
            static::ALTURA_LINHA,
            "% Mínimo a\nAplicar no Exercício",
            $x
        );
        $this->montarCabecalhoColuna(
            55.8112,
            static::ALTURA_LINHA * 2,
            "% Aplicado Até o Bimestre",
            $x
        );

        $this->pdf->Ln();

        $this->montarLinhasDespesasComManutencaoDesenvolvimentoEnsino($linhas);

        return $this;
    }

    private function montarCabecalhoDespesasComManutencaoDesenvolvimentoEnsinoNovo(array $linhas)
    {
        $this->addPage(count($linhas), static::ALTURA_LINHA * 3);

        $this->pdf->SetFont('arial', 'B', static::TAMANHO_FONTE);

        $x = $this->montarCabecalhoColuna(
            static::LARGURA_DESPESAS_MANUTENCAO_DESENVOLVIMENTO_ENSINO,
            static::ALTURA_LINHA * 3,
            'DESPESAS COM MANUTENÇÃO E DESENVOLVIMENTO DO ENSINO'
        );
        $x = $this->montarCabecalhoColuna(
            static::LARGURA_VALOR_APURADO_ATE_BIMESTRE,
            static::ALTURA_LINHA * 1.5,
            "Valor Apurado\nAté o Bimestre",
            $x
        );
        $this->montarCabecalhoColuna(
            static::LARGURA_LIMITES_CONSTITUCIONAIS_ANUAIS,
            static::ALTURA_LINHA,
            "Limites Constitucionais Anuais",
            $x
        );

        $y = $this->pdf->GetY() + static::ALTURA_LINHA;

        $this->pdf->SetXY($x, $y);

        $x = $this->montarCabecalhoColuna(
            38.1,
            static::ALTURA_LINHA,
            "% Mínimo a\nAplicar no Exercício",
            $x
        );
        $this->montarCabecalhoColuna(
            55.8112,
            static::ALTURA_LINHA * 2,
            "% Aplicado Até o Bimestre",
            $x
        );

        $this->pdf->Ln();

        foreach ($linhas as $linha) {
            $totalizar = false;
            $preenche = false;
            $nivel = 1;

            $this->montarLinha(
                static::LARGURA_DESPESAS_MANUTENCAO_DESENVOLVIMENTO_ENSINO,
                $linha->descricao,
                $totalizar,
                $preenche,
                false,
                $nivel
            )
                ->montarLinha(
                    static::LARGURA_VALOR_APURADO_ATE_BIMESTRE,
                    $linha->valorAplicado,
                    $totalizar,
                    $preenche
                )
                ->montarLinha(
                    static::LARGURA_MINIMO_APLICAR_EXERCICIO,
                    $linha->percentual,
                    $totalizar,
                    $preenche
                )
                ->montarLinha(
                    static::LARGURA_APLICADO_ATE_BIMESTRE,
                    $linha->percentualAplicado,
                    $totalizar,
                    $preenche,
                    true
                );
        }

        $iLargura = static::LARGURA_BALANCO_ORCAMENTARIO + static::LARGURA_ATE_BIMESTRE;
        $this->pdf->Cell($iLargura, 1, "", "T", 1, "C", 0);

        return $this;
    }

    /**
     * @param array $linhas
     * @return $this
     */
    private function montarCabecalhoReceitasOperacoesCreditoDespesasCapital(array $linhas)
    {
        $this->addPage(count($linhas), static::ALTURA_LINHA * 2);

        $this->pdf->SetFont('arial', 'B', static::TAMANHO_FONTE);

        $x = $this->montarCabecalhoColuna(
            static::LARGURA_RECEITAS_OPERACOES_CREDITO_DESPESAS_CAPITAL,
            static::ALTURA_LINHA * 2,
            'RECEITAS DE OPERAÇÕES DE CRÉDITO E DESPESAS DE CAPITAL'
        );
        $x = $this->montarCabecalhoColuna(
            static::LARGURA_VALOR_APURADO_ATE_BIMESTRE_2,
            static::ALTURA_LINHA * 2,
            'Valor Apurado Até o Bimestre',
            $x
        );
        $this->montarCabecalhoColuna(
            static::LARGURA_SALDO_NAO_REALIZADO,
            static::ALTURA_LINHA * 2,
            'Saldo não realizado',
            $x
        );

        $this->pdf->Ln();

        $this->montarLinhasReceitasOperacoesCreditoDespesasCapital($linhas);

        return $this;
    }

    /**
     * @param array $linhas
     * @return $this
     */
    private function montarCabecalhoProjecaoAtuarialRegimesPrevidencia(array $linhas)
    {
        $this->addPage(count($linhas), static::ALTURA_LINHA * 2);

        $this->pdf->SetFont('arial', 'B', static::TAMANHO_FONTE);

        $x = $this->montarCabecalhoColuna(
            static::LARGURA_PROJECAO_ATUARIAL_REGIMES_PREVIDENCIA,
            static::ALTURA_LINHA * 2,
            'PROJEÇÃO ATUARIAL DOS REGIMES DE PREVIDÊNCIA'
        );
        $x = $this->montarCabecalhoColuna(
            static::LARGURA_EXERCICIO,
            static::ALTURA_LINHA * 2,
            'Exercício',
            $x
        );
        $x = $this->montarCabecalhoColuna(
            static::LARGURA_10_EXERCICIO,
            static::ALTURA_LINHA * 2,
            '10º Exercício',
            $x
        );
        $x = $this->montarCabecalhoColuna(
            static::LARGURA_20_EXERCICIO,
            static::ALTURA_LINHA * 2,
            '20º Exercício',
            $x
        );
        $this->montarCabecalhoColuna(
            static::LARGURA_35_EXERCICIO,
            static::ALTURA_LINHA * 2,
            '35º Exercício',
            $x
        );

        $this->pdf->Ln();

        $this->montarLinhasProjecaoAtuarialRegimesPrevidencia($linhas);

        return $this;
    }

    /**
     * @param array $linhas
     * @return $this
     */
    private function montarCabecalhoReceitaAlienacaoAtivosAplicacaoRecursos(array $linhas)
    {
        $this->addPage(count($linhas), static::ALTURA_LINHA * 2);

        $this->pdf->SetFont('arial', 'B', static::TAMANHO_FONTE);

        $x = $this->montarCabecalhoColuna(
            static::LARGURA_RECEITA_ALIENACAO_ATIVOS_APLICACAO_RECURSOS,
            static::ALTURA_LINHA * 2,
            'RECEITA DA ALIENAÇÃO DE ATIVOS E APLICAÇÃO DOS RECURSOS'
        );
        $x = $this->montarCabecalhoColuna(
            static::LARGURA_VALOR_APURADO_ATE_BIMESTRE_2,
            static::ALTURA_LINHA * 2,
            'Valor Apurado Até o Bimestre',
            $x
        );
        $this->montarCabecalhoColuna(
            static::LARGURA_SALDO_REALIZAR,
            static::ALTURA_LINHA * 2,
            'Saldo a Realizar',
            $x
        );

        $this->pdf->Ln();

        $this->montarLinhasReceitaAlienacaoAtivosAplicacaoRecursos($linhas);

        return $this;
    }

    /**
     * @param array $linhas
     * @return $this
     */
    private function montarCabecalhoDespesasAcoesServicosPublicosSaude(array $linhas)
    {
        $this->addPage(count($linhas), static::ALTURA_LINHA * 3);

        $this->pdf->SetFont('arial', 'B', static::TAMANHO_FONTE);

        $x = $this->montarCabecalhoColuna(
            static::LARGURA_DESPESAS_ACOES_SERVICOS_PUBLICOS_SAUDE,
            static::ALTURA_LINHA * 3,
            'DESPESAS COM AÇÕES E SERVIÇOS PÚBLICOS DE SAÚDE'
        );
        $x = $this->montarCabecalhoColuna(
            static::LARGURA_VALOR_APURADO_ATE_BIMESTRE,
            static::ALTURA_LINHA * 1.5,
            "Valor Apurado\nAté o Bimestre",
            $x
        );
        $this->montarCabecalhoColuna(
            static::LARGURA_LIMITE_CONSTITUCIONAL_ANUAL,
            static::ALTURA_LINHA,
            "Limite Constitucional Anual",
            $x
        );

        $y = $this->pdf->GetY() + static::ALTURA_LINHA;

        $this->pdf->SetXY($x, $y);

        $x = $this->montarCabecalhoColuna(
            static::LARGURA_MINIMO_APLICAR_EXERCICIO,
            static::ALTURA_LINHA,
            "% Mínimo a\nAplicar no Exercício",
            $x
        );
        $this->montarCabecalhoColuna(
            static::LARGURA_APLICADO_ATE_BIMESTRE,
            static::ALTURA_LINHA * 2,
            "% Aplicado Até o Bimestre",
            $x
        );

        $this->pdf->Ln();

        $this->montarLinhasDespesasAcoesServicosPublicosSaude($linhas);

        return $this;
    }

    /**
     * @param array $linhas
     * @return $this
     */
    private function montarCabecalhoDespesasCaraterContinuadoDerivadasPPP(array $linhas)
    {
        $this->addPage(count($linhas), static::ALTURA_LINHA * 2);

        $this->pdf->SetFont('arial', 'B', static::TAMANHO_FONTE);

        $x = $this->montarCabecalhoColuna(
            static::LARGURA_DESPESAS_CARATER_CONTINUADO_DERIVADAS_PPP,
            static::ALTURA_LINHA * 2,
            'DESPESAS DE CARÁTER CONTINUADO DERIVADAS DE PPP'
        );
        $this->montarCabecalhoColuna(
            static::LARGURA_VALOR_APURADO_EXERCICIO_CORRENTE,
            static::ALTURA_LINHA * 2,
            'Valor Apurado no Exercício Corrente',
            $x
        );

        $this->pdf->Ln();

        $this->montarLinhasDespesasCaraterContinuadoDerivadasPPP($linhas);
        return $this;
    }

    /**
     * @param array $linhas
     * @return $this
     */
    private function montarLinhasBalancoOrcamentario(array $linhas)
    {
        foreach ($linhas as $linha) {
            if ($linha->ordem == 9 && $this->relatorio->getAno() >= 2020) {
                continue;
            }
            $totalizar = $linha->totalizar;
            $preenche = false;
            $ateBimestre = $linha->ordem == 1 || $linha->ordem == 7 ? '' : $linha->ate_bimestre;

            $this->montarLinha(
                static::LARGURA_BALANCO_ORCAMENTARIO,
                $linha->descricao,
                $totalizar,
                $preenche,
                false,
                $linha->nivel
            )
                ->montarLinha(static::LARGURA_ATE_BIMESTRE, $ateBimestre, $totalizar, $preenche, true);
        }

        return $this;
    }

    /**
     * @param array $linhas
     * @return $this
     */
    private function montarLinhasDespesasPorFuncaoSubfuncao(array $linhas)
    {
        foreach ($linhas as $linha) {
            $totalizar = $linha->totalizar;
            $preenche = false;

            $this->montarLinha(
                static::LARGURA_DESPESAS_FUNCAO_SUBFUNCAO,
                $linha->descricao,
                $totalizar,
                $preenche,
                false,
                $linha->nivel
            )
                ->montarLinha(static::LARGURA_ATE_BIMESTRE, $linha->ate_bimestre, $totalizar, $preenche, true);
        }

        return $this;
    }

    /**
     * @param array $linhas
     * @return $this
     */
    private function montarLinhasReceitaCorrenteLiquida(array $linhas)
    {
        foreach ($linhas as $linha) {
            $totalizar = $linha->totalizar;
            $preenche = false;

            $this->montarLinha(
                static::LARGURA_RECEITA_CORRENTE_LIQUIDA,
                $linha->descricao,
                $totalizar,
                $preenche,
                false,
                $linha->nivel
            )
                ->montarLinha(
                    static::LARGURA_ATE_BIMESTRE,
                    $linha->ate_bimestre,
                    $totalizar,
                    $preenche,
                    true
                );
        }

        return $this;
    }

    /**
     * @param array $linhas
     * @return $this
     */
    private function montarLinhasReceitasDespesasRegimeProprioPrevidenciaServidores(array $linhas)
    {
        foreach ($linhas as $linha) {
            $totalizar = $linha->totalizar;
            $preenche = false;

            $ateBimestre = $linha->ordem == 18 || $linha->ordem == 22 ? '' : $linha->ate_bimestre;

            $this->montarLinha(
                static::LARGURA_RECEITAS_DESPESAS_REGIME_PROPRIO_PREVIDENCIA_SERVIDORES,
                $linha->descricao,
                $totalizar,
                $preenche,
                false,
                $linha->nivel
            )
                ->montarLinha(static::LARGURA_ATE_BIMESTRE, $ateBimestre, $totalizar, $preenche, true);
        }

        $iLargura = static::LARGURA_BALANCO_ORCAMENTARIO + static::LARGURA_ATE_BIMESTRE;
        $this->pdf->Cell($iLargura, 1, "", "T", 1, "C", 0);

        return $this;
    }

    /**
     * @param array $linhas
     * @return $this
     */
    private function montarLinhasResultadosNominalPrimario(array $linhas)
    {
        foreach ($linhas as $linha) {
            $totalizar = $linha->totalizar;
            $preenche = false;

            $this->montarLinha(
                static::LARGURA_RESULTADO_NOMINAL_PRIMARIO,
                $linha->descricao,
                $totalizar,
                $preenche,
                false,
                $linha->nivel
            )
                ->montarLinha(
                    static::LARGURA_META_FIXADA_ANEXO_METAS_FISCAIS_LDO,
                    $linha->meta_fixada_anexo_metas_fiscais,
                    $totalizar,
                    $preenche
                )
                ->montarLinha(
                    static::LARGURA_RESULTADO_APURADO_ATE_BIMESTRE,
                    $linha->resultado_apurado_ate_bimestre,
                    $totalizar,
                    $preenche
                )
                ->montarLinha(
                    static::LARGURA_RELACAO_META,
                    $linha->relacao_meta,
                    $totalizar,
                    $preenche,
                    true
                );
        }

        return $this;
    }

    /**
     * @param array $linhas
     * @return $this
     */
    private function montarLinhasRestosPagarPoderMinisterioPublico(array $linhas)
    {
        foreach ($linhas as $linha) {
            $totalizar = $linha->totalizar;
            $preenche = false;

            if ($linha == end($linhas)) {
                $this->pdf->Cell(277, 0, '', 'T', 1);
            }

            if ($linha->inscricao || $linha->cancelamento_ate_bimestre ||
                $linha->pagamento_ate_bimestre || $linha->saldo_pagar) {
                $this->montarLinha(
                    static::LARGURA_RESTOS_PAGAR_PODER_MINISTERIO_PUBLICO,
                    $linha->descricao,
                    $totalizar,
                    $preenche,
                    false,
                    $linha->nivel
                )
                    ->montarLinha(static::LARGURA_INSCRICAO, $linha->inscricao, $totalizar, $preenche)
                    ->montarLinha(
                        static::LARGURA_CANCELAMENTO_ATE_BIMESTRE,
                        $linha->cancelamento_ate_bimestre,
                        $totalizar,
                        $preenche
                    )
                    ->montarLinha(
                        static::LARGURA_PAGAMENTO_ATE_BIMESTRE,
                        $linha->pagamento_ate_bimestre,
                        $totalizar,
                        $preenche
                    )
                    ->montarLinha(
                        static::LARGURA_SALDO_PAGAR,
                        $linha->saldo_pagar,
                        $totalizar,
                        $preenche,
                        true
                    );
            }
        }

        return $this;
    }

    /**
     * @param array $linhas
     * @return $this
     */
    private function montarLinhasDespesasComManutencaoDesenvolvimentoEnsino(array $linhas)
    {
        foreach ($linhas as $linha) {
            $totalizar = $linha->totalizar;
            $preenche = false;

            $this->montarLinha(
                static::LARGURA_DESPESAS_MANUTENCAO_DESENVOLVIMENTO_ENSINO,
                $linha->descricao,
                $totalizar,
                $preenche,
                false,
                $linha->nivel
            )
                ->montarLinha(
                    static::LARGURA_VALOR_APURADO_ATE_BIMESTRE,
                    $linha->valor_apurado_ate_bimestre,
                    $totalizar,
                    $preenche
                )
                ->montarLinha(
                    static::LARGURA_MINIMO_APLICAR_EXERCICIO,
                    $linha->minimo_aplicar_exercicio,
                    $totalizar,
                    $preenche
                )
                ->montarLinha(
                    static::LARGURA_APLICADO_ATE_BIMESTRE,
                    $linha->aplicado_ate_bimestre,
                    $totalizar,
                    $preenche,
                    true
                );
        }

        $iLargura = static::LARGURA_BALANCO_ORCAMENTARIO + static::LARGURA_ATE_BIMESTRE;
        $this->pdf->Cell($iLargura, 1, "", "T", 1, "C", 0);
        return $this;
    }

    /**
     * @param array $linhas
     * @return $this
     */
    private function montarLinhasReceitasOperacoesCreditoDespesasCapital(array $linhas)
    {
        foreach ($linhas as $linha) {
            $totalizar = $linha->totalizar;
            $preenche = false;

            $this->montarLinha(
                static::LARGURA_RECEITAS_OPERACOES_CREDITO_DESPESAS_CAPITAL,
                $linha->descricao,
                $totalizar,
                $preenche,
                false,
                $linha->nivel
            )
                ->montarLinha(
                    static::LARGURA_VALOR_APURADO_ATE_BIMESTRE_2,
                    $linha->valor_apurado_ate_bimestre,
                    $totalizar,
                    $preenche
                )
                ->montarLinha(
                    static::LARGURA_SALDO_NAO_REALIZADO,
                    $linha->saldo_nao_realizado,
                    $totalizar,
                    $preenche,
                    true
                );
        }
        return $this;
    }

    /**
     * @param array $linhas
     * @return $this
     */
    private function montarLinhasProjecaoAtuarialRegimesPrevidencia(array $linhas)
    {
        foreach ($linhas as $linha) {
            $totalizar = $linha->totalizar;
            $preenche = false;

            $this->montarLinha(
                static::LARGURA_PROJECAO_ATUARIAL_REGIMES_PREVIDENCIA,
                $linha->descricao,
                $totalizar,
                $preenche,
                false,
                $linha->nivel
            )
                ->montarLinha(
                    static::LARGURA_EXERCICIO,
                    $linha->exercicio,
                    $totalizar,
                    $preenche
                )
                ->montarLinha(static::LARGURA_10_EXERCICIO, $linha->exercicio_10, $totalizar, $preenche)
                ->montarLinha(static::LARGURA_20_EXERCICIO, $linha->exercicio_20, $totalizar, $preenche)
                ->montarLinha(
                    static::LARGURA_35_EXERCICIO,
                    $linha->exercicio_35,
                    $totalizar,
                    $preenche,
                    true
                );
        }

        return $this;
    }

    /**
     * @param array $linhas
     * @return $this
     */
    private function montarLinhasReceitaAlienacaoAtivosAplicacaoRecursos(array $linhas)
    {
        foreach ($linhas as $linha) {
            $totalizar = $linha->totalizar;
            $preenche = false;

            $this->montarLinha(
                static::LARGURA_RECEITA_ALIENACAO_ATIVOS_APLICACAO_RECURSOS,
                $linha->descricao,
                $totalizar,
                $preenche,
                false,
                $linha->nivel
            )
                ->montarLinha(
                    static::LARGURA_VALOR_APURADO_ATE_BIMESTRE_2,
                    $linha->valor_apurado_ate_bimestre,
                    $totalizar,
                    $preenche
                )
                ->montarLinha(
                    static::LARGURA_SALDO_REALIZAR,
                    $linha->saldo_realizar,
                    $totalizar,
                    $preenche,
                    true
                );
        }

        return $this;
    }

    /**
     * @param array $linhas
     * @return $this
     */
    private function montarLinhasDespesasAcoesServicosPublicosSaude(array $linhas)
    {
        foreach ($linhas as $linha) {
            $totalizar = $linha->totalizar;
            $preenche = false;

            $this->montarLinha(
                static::LARGURA_DESPESAS_ACOES_SERVICOS_PUBLICOS_SAUDE,
                $linha->descricao,
                $totalizar,
                $preenche,
                false,
                $linha->nivel
            )
                ->montarLinha(
                    static::LARGURA_VALOR_APURADO_ATE_BIMESTRE,
                    $linha->valor_apurado_ate_bimestre,
                    $totalizar,
                    $preenche
                )
                ->montarLinha(
                    static::LARGURA_MINIMO_APLICAR_EXERCICIO,
                    $linha->minimo_aplicar_exercicio,
                    $totalizar,
                    $preenche
                )
                ->montarLinha(
                    static::LARGURA_APLICADO_ATE_BIMESTRE,
                    $linha->aplicado_ate_bimestre,
                    $totalizar,
                    $preenche,
                    true
                );
        }

        return $this;
    }

    /**
     * @param array $linhas
     * @return $this
     */
    private function montarLinhasDespesasCaraterContinuadoDerivadasPPP(array $linhas)
    {
        foreach ($linhas as $linha) {
            $totalizar = $linha->totalizar;
            $preenche = false;

            $this->montarLinha(
                static::LARGURA_DESPESAS_CARATER_CONTINUADO_DERIVADAS_PPP,
                $linha->descricao,
                $totalizar,
                $preenche,
                false,
                $linha->nivel
            )
                ->montarLinha(
                    static::LARGURA_VALOR_APURADO_EXERCICIO_CORRENTE,
                    $linha->valor_apurado_ate_bimestre,
                    $totalizar,
                    $preenche,
                    true
                );
        }

        return $this;
    }

    /**
     * @param array $quadros
     */
    public function definirQuadros(array $quadros)
    {
        $this->quadros = $quadros;
    }

    /**
     * @param int $rows
     * @param int $headerHeight
     */
    private function addPage($rows = 0, $headerHeight = 0)
    {
        if (((static::ALTURA_LINHA * $rows) + $headerHeight) > $this->pdf->getAvailHeight()) {
            $this->pdf->Cell(277, 1, '', 'T');
            $this->pdf->AddPage();

            $this->montarLinhaIntroducao();
        }
    }
}
