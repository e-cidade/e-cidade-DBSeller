<?php
/*
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

use ECidade\Patrimonial\Material\Helpers\Material;

require_once(modification("libs/db_utils.php"));
require_once(modification("fpdf151/pdf.php"));

define('LARGURA_PAGINA', 192);
define('ALTURA_LINHA', 4);
define('TAMANHO_FONTE_CABECALHO', 6);
define('TAMANHO_FONTE', 6);
define('MARGEM_NOVA_PAGINA', 30);

try {
    $oGet                  = db_utils::postMemory($_GET);
    $iInstituicao          = db_getsession('DB_instit');
    $aCodigosDepartamento = array();
    $aCodigosMateriais     = array();
    $oPeriodoInicial       = null;
    $lMaterialImpresso     = false;
    $iAlmoxarifadoAnterior = null;
    $daoDepositos = new cl_db_almox();

    if (!empty($oGet->periodoInicial)) {
        $oPeriodoInicial = new DBDate($oGet->periodoInicial);
    }

  /**
   * Periodo final, caso nao informado pega data atual
   */
    if (!empty($oGet->periodoFinal)) {
        $oPeriodoFinal = new DBDate($oGet->periodoFinal);
    } else {
        $oPeriodoFinal = new DBDate(date('Y-m-d', db_getsession('DB_datausu')));
    }

    $lQuebraPorDeposito   = $oGet->quebraPorDeposito == 1;
    $somenteItensComMovimento = $oGet->somenteItensComMovimento == 1;
    $sOrdem                   = $oGet->ordem;
    $sTipoImpressao           = $oGet->tipoImpressao;

    if (!empty($oGet->sDepositos)) {
        $aCodigosDepositos = explode(',', $oGet->sDepositos);
        $sqlDepositos = $daoDepositos->sql_query_file(null, 'm91_depto', null, " m91_codigo in ({$oGet->sDepositos})");
        $rs = db_query($sqlDepositos);
        if (!$rs) {
            throw new Exception('Erro ao buscar depósitos.');
        }
        while ($deposito = pg_fetch_object($rs)) {
            $aCodigosDepartamento[] = $deposito->m91_depto;
        }
    }

    if (!empty($oGet->sMateriais)) {
        $aCodigosMateriais = explode(',', $oGet->sMateriais);
    }

  /**
   * Pesquisa almoxarifado
   * - nenhum almoxarifado inforamdo
   *   - caso for informado algum material, pesquisa almoxarifado destes itens
   *   - caso não for inforamdo nenhum material, pesquisa todos os almoxarifados para instituicao atual
   */
    if (empty($aCodigosDepartamento)) {

      /**
       * Nenhum material informado, pesquisa todas os almoxarifados da instituicao atual
       */
        if (empty($aCodigosMateriais)) {
            $oDaoMatestoque      = new cl_matestoque();
            $sSqlAlmoxarifados   = $oDaoMatestoque->sql_query_almoxarifado('distinct m70_coddepto', null, "db_depart.instit = $iInstituicao");
            $rsAlmoxarifados     = $oDaoMatestoque->sql_record($sSqlAlmoxarifados);

            if ($oDaoMatestoque->erro_status == '0') {
                throw new Exception(_M('patrimonial.material.mat2_controleestoque002.nenhum_almoxarifado_encontrado'));
            }

            foreach (db_utils::getCollectionByRecord($rsAlmoxarifados) as $oDadosAlmoxarifado) {
                $aCodigosDepartamento[] = $oDadosAlmoxarifado->m70_coddepto;
            }
        }

      /**
       * Material informado, pesquisa os almoxarifados destes
       */
        if (!empty($aCodigosMateriais)) {
            $oDaoMatestoque      = new cl_matestoque();
            $sWhereAlmoxarifados = 'm70_codmatmater in('. implode(',', $aCodigosMateriais) .')';
            $sSqlAlmoxarifados   = $oDaoMatestoque->sql_query_almoxarifado('distinct m70_coddepto', null, $sWhereAlmoxarifados);
            $rsAlmoxarifados     = $oDaoMatestoque->sql_record($sSqlAlmoxarifados);

            if ($oDaoMatestoque->erro_status == '0') {
                throw new Exception(_M('patrimonial.material.mat2_controleestoque002.nenhum_almoxarifado_encontrado_pelo_item'));
            }

            foreach (db_utils::getCollectionByRecord($rsAlmoxarifados) as $oDadosAlmoxarifado) {
                $aCodigosDepartamento[] = $oDadosAlmoxarifado->m70_coddepto;
            }
        }
    }

  /**
   * Nenhum material informado
   * - busca todos os materias de cada almoxarifado
   */
    if (empty($aCodigosMateriais)) {
        $oDaoMatestoque = new cl_matestoque();
        $sWhereItens    = 'm70_coddepto in('. implode(',', $aCodigosDepartamento) .')';
        $sSqlItens      = $oDaoMatestoque->sql_query_almoxarifado('m70_codmatmater', null, $sWhereItens);
        $rsItens        = $oDaoMatestoque->sql_record($sSqlItens);

        if ($oDaoMatestoque->erro_status == '0') {
            throw new Exception(_M('patrimonial.material.mat2_controleestoque002.nenhum_item_encontrado'));
        }

        foreach (db_utils::getCollectionByRecord($rsItens) as $oDadosItem) {
            $aCodigosMateriais[] = $oDadosItem->m70_codmatmater;
        }
    }

    $oControleEstoque = new ControleEstoque();
    $oControleEstoque->setPeriodo($oPeriodoInicial, $oPeriodoFinal);

    foreach ($aCodigosDepartamento as $iAlmoxarifado) {
        $oControleEstoque->adicionarAlmoxarifado(new Almoxarifado($iAlmoxarifado));
    }

    foreach ($aCodigosMateriais as $iMaterial) {
        $oControleEstoque->adicionarItem(new Item($iMaterial));
    }

    $aMovimentacoes = $oControleEstoque->getMovimentacaoEstoqueSintetica();
    $aDadosMovimentacoes    = array();
    $aOrdenacaoCodigo       = array();
    $aOrdenacaoNome         = array();
    $aOrdenacaoAlmoxarifado = array();

    foreach ($aMovimentacoes as $oMovimentacao) {
        $aDadosMovimentacoes[]    = $oMovimentacao;
        $aOrdenacaoCodigo[]       = $oMovimentacao->getItem()->getCodigo();
        $aOrdenacaoNome[]         = $oMovimentacao->getItem()->getNome();
        $aOrdenacaoAlmoxarifado[] = $oMovimentacao->getAlmoxarifado()->getCodigo();
    }

  /**
   * Ordena de acordo com filtro
   */
    switch ($sOrdem) {
        case 'alfabetica':
            array_multisort($aOrdenacaoNome, SORT_STRING, $aOrdenacaoCodigo, SORT_ASC, $aDadosMovimentacoes);
            break;

        case 'codigo':
            array_multisort($aOrdenacaoCodigo, SORT_ASC, $aOrdenacaoNome, SORT_STRING, $aDadosMovimentacoes);
            break;

        default:
        case 'deposito':
            array_multisort($aOrdenacaoAlmoxarifado, SORT_ASC, $aOrdenacaoNome, SORT_ASC, $aDadosMovimentacoes);
            break;
    }

  /**
   * Nao quebra por almoxarifado
   * - soma itens
   * - quanto tipo de impressao for sintetico
   */
    if (!$lQuebraPorDeposito && $tipoImpressao == 'sintetico') {

      /**
       * Agrupa movimentacoes por material para somalos
       */
        $aMovimentacoesPorMaterial = array();

      /**
       * Agrupa movimentacoes pelo codigo do material
       */
        foreach ($aDadosMovimentacoes as $oMovimentacao) {
            $aMovimentacoesPorMaterial[$oMovimentacao->getItem()->getCodigo()][] = $oMovimentacao;
        }

      /**
       * Percorre as movimentacoes de cada material e soma adicionanando no array de movimentacoes, $aDadosMovimentacoes
       */
        foreach ($aMovimentacoesPorMaterial as $iMaterial => $aDadosMovimentacoesPorMaterial) {
            $nValorAnterior      = 0;
            $nQuantidadeAnterior = 0;
            $nQuantidadeEntrada  = 0;
            $nQuantidadeSaida    = 0;
            $nValorEntrada       = 0;
            $nValorSaida         = 0;

            foreach ($aDadosMovimentacoesPorMaterial as $oMovimentacaoPorMaterial) {
                $nValorAnterior      += $oMovimentacaoPorMaterial->getValorAnterior();
                $nQuantidadeAnterior += $oMovimentacaoPorMaterial->getQuantidadeAnterior();
                $nQuantidadeEntrada  += $oMovimentacaoPorMaterial->getQuantidadeEntrada();
                $nQuantidadeSaida    += $oMovimentacaoPorMaterial->getQuantidadeSaida();
                $nValorEntrada       += $oMovimentacaoPorMaterial->getValorEntrada();
                $nValorSaida         += $oMovimentacaoPorMaterial->getValorSaida();
            }

            $oMovimentacaoPorMaterial->setValorAnterior($nValorAnterior);
            $oMovimentacaoPorMaterial->setValorSaida($nValorSaida);
            $oMovimentacaoPorMaterial->setValorEntrada($nValorEntrada);
            $oMovimentacaoPorMaterial->setQuantidadeAnterior($nQuantidadeAnterior);
            $oMovimentacaoPorMaterial->setQuantidadeEntrada($nQuantidadeEntrada);
            $oMovimentacaoPorMaterial->setQuantidadeSaida($nQuantidadeSaida);

            $aDadosMovimentacoes[] = $oMovimentacaoPorMaterial;
        }
    }

    if (empty($aDadosMovimentacoes)) {
        throw new Exception(_M('patrimonial.material.mat2_controleestoque002.nenhuma_movimentacao_encontrada'));
    }

    $aDescricaoOrdem         = array();
    $aDescricaoTipoImpressao = array();

    $aDescricaoOrdem['alfabetica']   = 'Alfabética';
    $aDescricaoOrdem['codigo']       = 'Código';
    $aDescricaoOrdem['deposito'] = 'Depósito';

    $aDescricaoTipoImpressao['sintetico']   = 'Sintético';
    $aDescricaoTipoImpressao['conferencia'] = 'Conferência';

    $head2 = "Controle de estoque";

    if (!empty($oGet->periodoInicial)) {
        $head4 = "Período: " .$oPeriodoInicial->getDate(DBDate::DATA_PTBR) . ' a ' . $oPeriodoFinal->getDate(DBDate::DATA_PTBR);
    } else {
        $head4 = "Período final: " . $oPeriodoFinal->getDate(DBDate::DATA_PTBR);
    }

//    $head5 = 'Quebra por depósito: ' .  ($lQuebraPorDeposito ? 'Sim' : 'Não');
    $head5 = 'Somente itens com movimento: ' . ($somenteItensComMovimento ? 'Sim' : 'Não');
    $head6 = 'Ordem: ' . $aDescricaoOrdem[$sOrdem];
    $head7 = 'Tipo de impressão: ' . $aDescricaoTipoImpressao[$sTipoImpressao];

    $oPDF = new PDF('P', 'mm', 'A4');
    $oPDF->Open();
    $oPDF->AliasNbPages();
    $oPDF->setFillColor(235);
    $oPDF->setfont('arial', '', 8);
    $oPDF->addPage();

    $oDados                         = new stdClass();
    $oDados->sPeriodoInicial        = !empty($oPeriodoInicial) ? $oPeriodoInicial->getDate(DBDate::DATA_PTBR) : null;
    $oDados->sPeriodoFinal          = $oPeriodoFinal->getDate(DBDate::DATA_PTBR);
    $oDados->lQuebraPorDeposito = $lQuebraPorDeposito;

    if (!$lQuebraPorDeposito) {
        if ($sTipoImpressao == 'sintetico') {
            cabecalhoSintetico(false, $oDados);
        }

        if ($sTipoImpressao == 'conferencia') {
            cabecalhoConferencia(false, $oDados);
        }
    }


  /*
   *  Criada variaveis para um totalizador, este totalizador, será apenas para o
   *  Tipo de Impressão: SINTETICO
   *
   */

    $nTotalQuantidadeAnterior = 0;
    $nTotalValorAnterior      = 0;
    $nTotalQuantidadeEntrada  = 0;
    $nTotalValorEntrada       = 0;
    $nTotalQuantidadeSaida    = 0;
    $nTotalValorSaida         = 0;
    $nTotalValorFinal         = 0;
    $nTotalQuantidadeFinal    = 0;

    foreach ($aDadosMovimentacoes as $oMovimentacao) {
        $oDados->iMaterial           = $oMovimentacao->getItem()->getCodigo();
        $oDados->sMaterial           = $oMovimentacao->getItem()->getNome();
        $oDados->iAlmoxarifado       = $oMovimentacao->getAlmoxarifado()->getCodigoAlmoxarifado();
        $oDados->sAlmoxarifado       = $oMovimentacao->getAlmoxarifado()->getNomeDepartamento();

  //    $nValorAnterior = ;

        $oDados->nQuantidadeAnterior = (string)Material::arredondarQuantidade($oMovimentacao->getQuantidadeAnterior());
        $oDados->nValorAnterior = formatar(formatarValor($oMovimentacao->getQuantidadeAnterior(), $oMovimentacao->getValorAnterior()));
        $oDados->nQuantidadeEntrada = (string)Material::arredondarQuantidade($oMovimentacao->getQuantidadeEntrada());
        $oDados->nValorEntrada = formatar(formatarValor($oMovimentacao->getQuantidadeEntrada(), $oMovimentacao->getValorEntrada()));
        $oDados->nQuantidadeSaida = (string)Material::arredondarQuantidade($oMovimentacao->getQuantidadeSaida());
        $oDados->nValorSaida = formatar(formatarValor($oMovimentacao->getQuantidadeSaida(), $oMovimentacao->getValorSaida()));
  //    $nValorFinal      = ($oMovimentacao->getValorAnterior() + $oMovimentacao->getValorEntrada()) - $oMovimentacao->getValorSaida();
        $nQuantidadeFinal = $oMovimentacao->getQuantidadeAnterior() + $oMovimentacao->getQuantidadeEntrada() - $oMovimentacao->getQuantidadeSaida();
        $pm = buscaPrecoMedioData($oPeriodoFinal, $oMovimentacao);
        $nValorFinal = $pm * $nQuantidadeFinal;

        $oDados->lImprimirCabecalho = false;

      /**
       * Nao exibe matareiais sem movimentacao no periodo
       */
        if ($somenteItensComMovimento && $oMovimentacao->getValorEntrada() <= 0 && $oMovimentacao->getValorSaida() <= 0) {
            continue;
        }

        $oDados->nValorFinal = formatar($nValorFinal);
        $oDados->nQuantidadeFinal = (string)Material::arredondarQuantidade($nQuantidadeFinal);

      /**
       * Almoxarifado diferente do ultimo impresso, imprime cabecalho
       */
        if ($lQuebraPorDeposito && $oMovimentacao->getAlmoxarifado()->getCodigo() <> $iAlmoxarifadoAnterior) {
            if ($lMaterialImpresso) {
                $oPDF->ln();
            }

            $oDados->lImprimirCabecalho = true;
        }

        if ($tipoImpressao == 'conferencia') {
            if ($oDados->lImprimirCabecalho) {
                cabecalhoConferencia($lQuebraPorDeposito, $oDados);
            }

            linhaConferencia($oDados);
        }

        if ($tipoImpressao == 'sintetico') {
            if ($oDados->lImprimirCabecalho) {
                cabecalhoSintetico($lQuebraPorDeposito, $oDados);
            }

            linhaSintetico($oDados);

          /**
           * somamos os totalizadores, somente quando ele for imprimir
           * dados sintetico
           */
            $nTotalQuantidadeAnterior  += $oMovimentacao->getQuantidadeAnterior();
            $nTotalValorAnterior       += $oMovimentacao->getValorAnterior();
            $nTotalQuantidadeEntrada   += $oMovimentacao->getQuantidadeEntrada();
            $nTotalValorEntrada        += $oMovimentacao->getValorEntrada();
            $nTotalQuantidadeSaida     += $oMovimentacao->getQuantidadeSaida();
            $nTotalValorSaida          += $oMovimentacao->getValorSaida();
            $nTotalValorFinal          += $nValorFinal;
            $nTotalQuantidadeFinal     += $nQuantidadeFinal;
        }

        $lMaterialImpresso     = true;
        $iAlmoxarifadoAnterior = $oMovimentacao->getAlmoxarifado()->getCodigo();
    }

    if (!$lMaterialImpresso) {
        throw new Exception(_M('patrimonial.material.mat2_controleestoque002.nenhuma_movimentacao_encontrada'));
    }

  /**
   * Impressão da linha dos totalizadores
   * Somente para Tipo de Impressão: SINTETICO
   *
   */

    if ($tipoImpressao == 'sintetico') {
        $oPDF->SetY($oPDF->GetY()+4);
        $oPDF->setfont('arial', 'b', TAMANHO_FONTE);
        linha(36, "TOTAL", "TRB", "R");
        linha(8, (string)Material::arredondarQuantidade($nTotalQuantidadeAnterior), "TRB", "R");
        linha(8, "R$ " . formatar(formatarValor($nTotalQuantidadeAnterior, $nTotalValorAnterior)), "TRB", "R");
        linha(14, (string)Material::arredondarQuantidade($nTotalQuantidadeEntrada), "TRB", "R");
  //    linha(8,  formatar(formatarValor($nTotalQuantidadeEntrada, $nTotalValorEntrada)), "TRB", "R");
        linha(14, (string)Material::arredondarQuantidade($nTotalQuantidadeSaida), "TRB", "R");
  //    linha(8,  formatar(formatarValor($nTotalQuantidadeSaida, $nTotalValorSaida)), "TRB", "R");
        linha(10, (string)Material::arredondarQuantidade($nTotalQuantidadeFinal), "TRB", "R");
        linha(10, "R$ " . formatar(formatarValor($nTotalQuantidadeFinal, $nTotalValorFinal)), "TB", "R");
    }

    $oPDF->Output();
} catch (Exception $oErro) {
    $sMensagemErro = nl2br($oErro->getMessage());
    db_redireciona('db_erros.php?fechar=true&db_erro=' . urlEncode($sMensagemErro));
}

function linhaSintetico(stdClass $oDados)
{

    $oPDF = getPdf();
    $oPDF->setfont('arial', '', TAMANHO_FONTE);

    $sMaterial = $oDados->sMaterial;

    if (strlen($sMaterial) > 37) {
        $sMaterial = substr($oDados->sMaterial, 0, 37) . '...';
    }

    linha(8, $oDados->iMaterial, "TRB", "C");
    linha(28, $sMaterial, "TRB", "L");
    linha(8, $oDados->nQuantidadeAnterior, "TRB", "R");
    linha(8, "R$ ".$oDados->nValorAnterior, "TRB", "R");
    linha(14, $oDados->nQuantidadeEntrada, "TRB", "R");
//  linha(8, $oDados->nValorEntrada, "TRB", "R");
    linha(14, $oDados->nQuantidadeSaida, "TRB", "R");
//  linha(8, $oDados->nValorSaida, "TRB", "R");
    linha(10, $oDados->nQuantidadeFinal, "TRB", "R");

    linha(10, "R$ ".$oDados->nValorFinal, "TB", "R");

    $oPDF->ln();

  /**
   * Quebra de pagina
   */
    if ($oPDF->GetY() > $oPDF->h - MARGEM_NOVA_PAGINA) {
        $oPDF->AddPage();
        cabecalhoSintetico($oDados->lQuebraPorDeposito, $oDados);
    }
}

function cabecalhoSintetico($lImprimirCabecalhoAlmoxarifado = false, $oDados = null)
{

    if ($lImprimirCabecalhoAlmoxarifado) {
        cabecalhoAlmoxarifado($oDados);
    }

    $oPDF = getPdf();
    $oPDF->setfont('arial', 'b', TAMANHO_FONTE_CABECALHO);

    linha(36, "Material", "TRB", "C");

    if (!empty($oDados->sPeriodoInicial)) {
        linha(16, "Saldo anterior a " . $oDados->sPeriodoInicial, "TRB", "C");
    } else {
        linha(16, "Saldo anterior", "TRB", "C");
    }

    linha(14, "Entradas no período", "TRB", "C");
    linha(14, "Saídas no período", "TRB", "C");
    linha(20, "Saldo em " . $oDados->sPeriodoFinal, "TB", "C");

    $oPDF->ln();

    linha(8, "Código", "TRB", "C");
    linha(28, "Descrição", "TRB", "C");
    linha(8, "Quantidade", "TRB", "C");
    linha(8, "Valor", "TRB", "C");
    linha(14, "Quantidade", "TRB", "C");
//  linha(8, "Valor", "TRB", "C");
    linha(14, "Quantidade", "TRB", "C");
//  linha(8, "Valor", "TRB", "C");
    linha(10, "Quantidade", "TRB", "C");
    linha(10, "Valor", "TB", "C");

    $oPDF->ln();
    $oPDF->setfont('arial', '', TAMANHO_FONTE);
}

function cabecalhoAlmoxarifado(stdClass $oDados)
{

    $oPDF = getPdf();

    if ($oPDF->GetY() > $oPDF->h - MARGEM_NOVA_PAGINA - ALTURA_LINHA) {
        $oPDF->AddPage();
    }

    $sAlmoxarifado = $oDados->iAlmoxarifado . ' - ' . $oDados->sAlmoxarifado;
    $oPDF->setfont('arial', 'b', TAMANHO_FONTE_CABECALHO);
    linha(100, $sAlmoxarifado, "TB", "L");
    $oPDF->setfont('arial', '', TAMANHO_FONTE);
    $oPDF->ln();
}

function linhaConferencia(stdClass $oDados)
{

    $oPDF = getPdf();
    $oPDF->setfont('arial', '', TAMANHO_FONTE);
    $sMaterial = $oDados->iMaterial . ' - ' . $oDados->sMaterial;
    $sAlmoxarifado = $oDados->iAlmoxarifado . ' - ' . $oDados->sAlmoxarifado;

    if (strlen($sMaterial) > 57) {
        $sMaterial = substr($oDados->sMaterial, 0, 57) . '...';
    }

    if ($oDados->lQuebraPorDeposito) {
        linha(64, $sMaterial, "TRB", "L");
        linha(12, $oDados->nQuantidadeFinal, "TRB", "R");
        linha(12, '', "TRB", "C");
        if ($oDados->nQuantidadeFinal == 0 && $oDados->nValorFinal <= 0.99) {
            linha(12, 'R$ 0,00', "TB", "R");
        } else {
            linha(12, 'R$ '. $oDados->nValorFinal, "TB", "R");
        }
    } else {
        linha(38, $sMaterial, "TRB", "L");
        linha(35, $sAlmoxarifado, "TRB", "L");
        linha(12, $oDados->nQuantidadeFinal, "TRB", "R");
        linha(12, '', "TRB", "C");

        if ($oDados->nQuantidadeFinal == 0 && $oDados->nValorFinal <= 0.99) {
            linha(12, 'R$ 0,00', "TB", "R");
        } else {
            linha(12, 'R$ '.$oDados->nValorFinal, "TB", "R");
        }
    }

    $oPDF->ln();

   /**
   * Quebra de pagina
   */
    if ($oPDF->GetY() > $oPDF->h - MARGEM_NOVA_PAGINA) {
        $oPDF->AddPage();
        cabecalhoConferencia($oDados->lQuebraPorDeposito, $oDados);
    }
}

function cabecalhoConferencia($lQuebraPorDeposito = false, $oDados = null)
{
    $oPDF = getPdf();
    $oPDF->setfont('arial', 'b', TAMANHO_FONTE_CABECALHO);

    if ($lQuebraPorDeposito) {
        cabecalhoAlmoxarifado($oDados);
        $oPDF->setfont('arial', 'b', TAMANHO_FONTE_CABECALHO);

        linha(64, "Material", "TRB", "C");
        linha(12, "Quantidade", "TRB", "C");
        linha(12, "Contagem", "TRB", "C");
        linha(12, "Valor final", "TB", "C");
    } else {
        linha(35, "Material", "TRB", "C");
        linha(38, "Depósito", "TRB", "C");
        linha(12, "Quantidade", "TRB", "C");
        linha(12, "Contagem", "TRB", "C");
        linha(12, "Valor final", "TB", "C");
    }

    $oPDF->setfont('arial', '', TAMANHO_FONTE);
    $oPDF->ln();
}

/**
 * Imprime linha no relatorio
 *
 * @param integer $iLargura
 * @param string $sValor
 * @param string $sBordas
 * @param string $sAlinhamento
 * @access public
 * @return void
 */
function linha($iLargura, $sValor, $sBordas, $sAlinhamento, $iAlturaLinha = ALTURA_LINHA)
{
    getPdf()->cell(larguraColuna($iLargura), $iAlturaLinha, $sValor, $sBordas, 0, $sAlinhamento);
}

/**
 * Retorna instancia do objeto PDF
 *
 * @access public
 * @return PDF
 */
function getPdf()
{
    return $GLOBALS['oPDF'];
}

/**
 * Largura da coluna
 *
 * @param string $sTipo
 * @param float $nPorcentagem - Porcentagem que a coluna ocupara na linha
 * @return float
 */
function larguraColuna($nPorcentagem = 0)
{

    if ($nPorcentagem == 0) {
        return LARGURA_PAGINA;
    }

    return round($nPorcentagem / 100 * LARGURA_PAGINA, 2);
}

/**
 * Formata valor e quantidade
 *
 * @param float $nValor
 * @param int $iCasasDecimais
 * @access public
 * @return float
 */
function formatar($nValor, $iCasasDecimais = 2)
{
    return number_format((float) $nValor, (int) $iCasasDecimais, ',', '.');
}

/**
 * Função que retorna ZERO caso o valor informado no parâmetro seja negativo
 * @param $nValor
 * @return string
 */
function formatarValor($iQuantidade, $nValor)
{

    $iQuantidade = round($iQuantidade, 2);
    return (($iQuantidade == 0 || $iQuantidade == 0.01) && $nValor != 0) ? '0' : $nValor;
}

/**
 * @param DBDate $data
 * @param MovimentacaoItem $movimentacao
 * @return integer
 * @throws Exception
 */
function buscaPrecoMedioData(DBDate $data, MovimentacaoItem $movimentacao)
{
    $iMaterial = $movimentacao->getItem()->getCodigo();
    $iDepartamentoDeposito = $movimentacao->getAlmoxarifado()->getCodigo();

    $where = [];
    $where[] = "m85_matmater = {$iMaterial}";
    $where[] = "m85_coddepto = {$iDepartamentoDeposito}";
    $where[] = "m85_data <= '{$data->getDate()}'";

    $dao = new cl_matmaterprecomedio();
    $sql = $dao->sql_query_file(null, "*", 'm85_data desc limit 1', implode(' AND ', $where));
    $rs = db_query($sql);

    if (!$rs) {
        throw new Exception("Erro ao buscar último preço médio na data");
    }

    return db_utils::fieldsmemory($rs, 0)->m85_precomedio;
}
