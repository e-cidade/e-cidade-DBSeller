<?php

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Contabilidade\Models\Pcasp;
use App\Domain\Financeiro\Contabilidade\Models\Conplano;
use App\Domain\Financeiro\Contabilidade\Models\PlanoReceita;
use App\Domain\Financeiro\Orcamento\Models\FonteReceita;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalPcaspPadrao;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceita;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceitaPadrao;
use ECidade\Financeiro\Orcamento\Repository\RecursoRepository as RecursoRepositoryAlias;
use \ECidade\Pdf\Pdf;

/**
 * Retorna o código do recurso com base no número do recurso.
 * Caso exista mais de um recurso com a fonte de recurso,
 * retorna o que tem o complemento 0.
 */
if (!function_exists('obterCodigoRecursoPorFonte')) {
    function obterCodigoRecursoPorFonte($recurso)
    {
        $recursos = RecursoRepositoryAlias::getRecursosValidosPorSubrecurso($recurso);
        /**
         * em breve será utilizado o codigo da gestao, (fonterecurso)
         * quando homologar e efetivar a STN sera alterado
         */
        $retorno = $recursos[0]->o15_codigo;

        // Existindo mais de um, pega o que tem o complemento 0(zero)
        if (count($recursos) > 1) {
            $filtroAdicional = " o15_complemento = 0 ";
            $recursosSearch = RecursoRepositoryAlias::getRecursosValidosPorSubrecurso($recurso, $filtroAdicional);
            if (count($recursosSearch) == 1) {
                return $recursosSearch[0]->o15_codigo;
            }
        }

        return $retorno;
    }
}

/**
 * Retorna uma instância do formater da receita
 * @retrun EstruturalReceita|EstruturalReceitaPadrao
 */
if (!function_exists('estruturalFormatterReceita')) {
    function estruturalFormatterReceita($natureza, $ementario)
    {
        if ($ementario === 'ecidade') {
            return new EstruturalReceita($natureza);
        }
        return new EstruturalReceitaPadrao($natureza);
    }
}

/**
 * Retorna uma instância do formater do PCASP
 * @retrun EstruturalPcaspPadrao
 */
if (!function_exists('estruturalFormatterPcasp')) {
    function estruturalFormatterPcasp($estrutural)
    {
        return new EstruturalPcaspPadrao($estrutural);
    }
}

/**
 * Busca o plano de contas conforme o plano informado retornando um array indexado pelo estrutural
 * @retrun array
 */
if (!function_exists('getPlanoPcasp')) {
    function getPlanoPcasp($tipoPlano, $exercicio)
    {
        $planoPcasp = [];
        $callback = function ($conta) use (&$planoPcasp, $tipoPlano) {
            $estrutural = estruturalFormatterPcasp($conta->conta)->getEstrutural();
            $planoPcasp[$estrutural] = $conta;
        };

        if ($tipoPlano === 'ecidade') {
            Conplano::where('c60_anousu', '=', $exercicio)
                ->orderBy('c60_estrut')
                ->get()
                ->each($callback);

            return $planoPcasp;
        }

        Pcasp::where('uniao', $tipoPlano === 'uniao')
            ->where('exercicio', $exercicio)
            ->orderBy('conta')
            ->get()
            ->each($callback);
        return $planoPcasp;
    }
}


/**
 * Retorna as fontes de receita do exercício e ementário informado.
 * Função geralmente usada para montar a árvore de estrutural
 * @retrun array
 */
if (!function_exists('getFontesEmentario')) {
    function getFontesEmentario($tipoEmentario, $exercicio)
    {
        $fontesEmentario = [];
        $callback = function ($ementario) use (&$fontesEmentario, $tipoEmentario) {
            $estrutural = estruturalFormatterReceita($ementario->natureza, $tipoEmentario)->getEstrutural();
            $fontesEmentario[$estrutural] = $ementario;
        };

        if ($tipoEmentario === 'ecidade') {
            FonteReceita::where('o57_anousu', '=', $exercicio)
                ->orderBy('o57_fonte')
                ->get()
                ->each($callback);
            return $fontesEmentario;
        }

        PlanoReceita::where('uniao', $tipoEmentario === 'uniao')
            ->where('exercicio', $exercicio)
            ->orderBy('conta')
            ->get()
            ->each($callback);

        return $fontesEmentario;
    }
}

if (!function_exists("assinaturasFinanceiro")) {
    /**
     * Assinaturas dos relatórios
     *
     * Os tipos de emissão são os seguintes
     * - LRF = relatórios da LRF execução orçamentaria
     * - GF  = relatórios da LRF gestão fiscal
     * - BG  = relatórios da 4320 Balanço geral
     *
     * @param Pdf $pdf
     * @param DBConfig $instituicaoEmissora
     * @param string $tipo [LRF, GF, BG]
     * @param bool $validaQuebraPagina
     * @return void
     */
    function assinaturasFinanceiro(Pdf &$pdf, DBConfig $instituicaoEmissora, $tipo = 'LRF', $validaQuebraPagina = true)
    {
        $dao = new \cl_assinatura();
        $controle = "______________________________" . "\n" . "Controle Interno";
        $sec = "______________________________" . "\n" . "Secretaria da Fazenda";
        $cont = "______________________________" . "\n" . "Contadoria";
        $pref = "______________________________" . "\n" . "Prefeito";

        $assPrefeito = $dao->assinatura(1000, $pref);
        $assSecretario = $dao->assinatura(1002, $sec);
        $assContador = $dao->assinatura(1005, $cont);
        $assControle = $dao->assinatura(1009, $controle);

        $imprimir = [
            $assPrefeito,
            $assContador,
            $assSecretario,
            $assControle,
        ];

        if ($tipo == 'LRF' || $tipo == 'BG') {
            $imprimir = [
                $assPrefeito,
                $assContador,
            ];
            // não imprime a assinatura do secretario se instituição  6-RPPS(Autarquia), 7-RPPS (Exceto Autarquia )
            if (!in_array($instituicaoEmissora->db21_tipoinstit, [5, 6, 7])) {
                $imprimir[] = $assSecretario;
            }
        }

        if ($validaQuebraPagina && ($pdf->getAvailHeight() < 30)) {
            $pdf->addPage();
            $pdf->ln(14);
        }

        $largura = ($pdf->getW()) / count($imprimir);
        $pos = $pdf->gety();

        $contador = 1;
        foreach ($imprimir as $assinatura) {
            $pdf->multicell($largura, 3, $assinatura, 0, "C", 0);
            $pdf->setxy($largura * $contador, $pos);
            $contador++;
        }
    }
}
