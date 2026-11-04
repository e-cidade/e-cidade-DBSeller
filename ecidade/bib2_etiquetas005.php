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
use ECidade\Pdf\Pdf;

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_utils.php");

$sExemplares         = db_getsession('sListaImpressao');
$iCodigoDepartamento = db_getsession('DB_coddepto');

$oConfig = new stdclass();
$oConfig->iLarguraEtiqueta = 101;
$oConfig->iAlturaEtiqueta  = 25;
$oConfig->iMargemInterna   = 5;
$oConfig->iMargemEtiqueta  = 15;

/**
 * Array contendo os livros que serão impressos
 * @var array
 */
$aLivros = array();

try {
    if (empty($sExemplares)) {
        throw new ParameterException("Informe um exemplar.");
    }

    $sCampos = " trim(bi06_titulo) as titulo, bi06_classcdd, bi06_cutter, bi23_volume, bi23_isbn, bi23_codbarras, bi23_exemplar, bi23_codigo, bi06_seq ";
    $sWhere  = " bi17_coddepto = {$iCodigoDepartamento} ";
    $sWhere .= " and bi23_codigo in ({$sExemplares}) ";
    $sOrdem  = " bi06_titulo, bi23_exemplar ";

    $oDaoExemplar = new cl_exemplar();
    $sSqlExemplar = $oDaoExemplar->sql_query_dados_exemplar(null, $sCampos, $sOrdem, $sWhere);
    $rsExemplar   = db_query($sSqlExemplar);

    if (!$rsExemplar) {
        throw new Exception("Erro ao buscar exemplar.\\n" . pg_last_error());
    }

    $aLivros = db_utils::getCollectionByRecord($rsExemplar);
} catch (Exception $oErro) {
    db_redireciona("db_erros.php?fechar=true&db_erro=" .$oErro->getMessage());
}

$oPdf = new Pdf('P', 'mm', 'Letter');
$oPdf->init(false);
$oPdf->exibeHeader(false);
$oPdf->mostrarRodape(false);
$oPdf->setMargins(3.5, 13, 3.5);
$oPdf->setAutoPageBreak(false, 12.5);
$oPdf->setFont("Arial", '', 8);
$oPdf->setFillColor(0, 0, 0);
$oPdf->addPage();

$oConfig->iMargemEtiquetaEsquerda = $oPdf->getX() + $oConfig->iMargemEtiqueta;
$oConfig->iMargemEtiquetaDireita  = $oPdf->getX() + $oConfig->iLarguraEtiqueta + $oConfig->iMargemInterna + $oConfig->iMargemEtiqueta;
$oConfig->iTamanhoCelula          = $oConfig->iLarguraEtiqueta - $oConfig->iMargemEtiqueta;

$oConfig->iMargemCodBarrasEsquerda = $oConfig->iMargemEtiquetaEsquerda + ($oConfig->iTamanhoCelula / 2) -10;
$oConfig->iMargemCodBarrasDireita  = $oConfig->iMargemEtiquetaDireita + ($oConfig->iTamanhoCelula / 2) -10;

$iYInicial        = null;
$iContadorCelulas = 0;
foreach ($aLivros as $iIndex => $oDados) {
    $oPdf->setFont("Arial", '', 8);
    if ($iContadorCelulas == 20) {
        $oPdf->addPage();
        $iContadorCelulas = 0;
    }

    $sMsg  = substr($oDados->bi06_classcdd, 0, 12). "\n";
    $sMsg .= substr($oDados->bi06_cutter, 0, 12). "\n";
    $sMsg .= (!empty($oDados->bi23_volume)) ? "V. {$oDados->bi23_volume}\n" :  " \n";
    $sMsg .= "Ex. {$oDados->bi23_exemplar}\n";

    $iMargemCodBarras = $oConfig->iMargemCodBarrasEsquerda;

    if ($iIndex % 2 == 0) {
        $iYInicial = $oPdf->getY();
        $oPdf->ln(2.5);
        $oPdf->setX($oConfig->iMargemEtiquetaEsquerda);
    } else {
        $oPdf->setY($iYInicial);
        $oPdf->ln(2.5);
        $oPdf->setX($oConfig->iMargemEtiquetaDireita);
        $iMargemCodBarras = $oConfig->iMargemCodBarrasDireita;
    }

    $oPdf->multiCell($oConfig->iTamanhoCelula, 5, $sMsg, 0);
    $oPdf->text($iMargemCodBarras +9, $oPdf->getY()-17, str_pad($oDados->bi23_codbarras, 13, 0, STR_PAD_LEFT));
    $oPdf->int25($iMargemCodBarras, $oPdf->getY()-16, $oDados->bi23_codbarras, 9, 0.4);//codbarras
    $oPdf->setX($oPdf->getY()-3);
    $oPdf->setFont("Arial", '', 10);
    $oPdf->text($iMargemCodBarras, $oPdf->getY()-2, "Acervo: {$oDados->bi06_seq} Cód.Ex: {$oDados->bi23_codigo}");
    $oPdf->ln(3);
    $iContadorCelulas++;
}

$oPdf->setX($oPdf->getX() + $oConfig->iMargemEtiqueta);
$oPdf->output();
