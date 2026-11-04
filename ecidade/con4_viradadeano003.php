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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_utils.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("libs/db_libsys.php"));
db_postmemory($HTTP_GET_VARS);
# Include AgataAPI class


//$objGet  = db_utils::postmemory($_GET);

ini_set("error_reporting","E_ALL & ~NOTICE");

//mudar o padrao de saida
//$api->setFormat('pdf'); // 'pdf', 'txt', 'xml', 'html', 'csv', 'sxw'
$sqlcab = "select c30_sequencial,c30_anoorigem,c30_anodestino,c30_data,c30_hora,nome
			from db_virada
			inner join db_usuarios on id_usuario = c30_usuario
			where c30_sequencial = ".$virada;
$resultcab = db_query($sqlcab);
$dadosVirada = db_utils::fieldsmemory($resultcab, 0);
$oPdfTable = new PDFTable();

$oPdf = new PDFDocument(PDFDocument::PRINT_LANDSCAPE);
$oPdf->addHeaderDescription("Lista das Inconsistências Geradas");
$oPdf->addHeaderDescription("Virada: ".$virada);
$oPdf->addHeaderDescription("Usuário: ".$nome);
$oPdf->addHeaderDescription("Data: ".db_formatar($dadosVirada->c30_data,"d")." Hora:".$dadosVirada->c30_hora);
$oPdf->addHeaderDescription("Ano exercício: ".$dadosVirada->c30_anoorigem);
$oPdf->addHeaderDescription("Ano destino: ".$dadosVirada->c30_anodestino);
$oPdf->SetFillColor(245);
$oPdf->open();

$oPdf->SetAutoPageBreak(false, 25);
$oPdf->setFileName("log_virada_{$dadosVirada->c30_anodestino}_" . time());
$aTamanhos = [25, 20, 60, 25    , 148];


$oPdfTable->setHeaders(["Data", "Hora", "Item", "Tabela", "Log"]);
$oPdfTable->setColumnsWidth($aTamanhos);
$oPdfTable->setLineHeigth(5);
$oPdfTable->setColumnsAlign([
    PDFDocument::ALIGN_LEFT,
    PDFDocument::ALIGN_LEFT,
    PDFDocument::ALIGN_LEFT,
    PDFDocument::ALIGN_LEFT,
    PDFDocument::ALIGN_LEFT,
]);
/**
 * Seta as colunas que terão Multicell
 */
$aMulticell = array(1,2, 3, 4, 5);
$oPdfTable->setMulticellColumns($aMulticell);


$consulta = "select c35_data                                              as data,
       c35_hora                                               as hora,
       to_char(c33_sequencial, '000') || '-' || c33_descricao as item,
       nomearq                                                as tabela,
       c35_log                                                as log
from db_viradaitemlog
         INNER JOIN db_sysarquivo on (db_viradaitemlog.c35_codarq = db_sysarquivo.codarq)
         INNER JOIN db_viradaitem on (db_viradaitemlog.c35_db_viradaitem = db_viradaitem.c31_sequencial)
         INNER JOIN db_viradacaditem on (c33_sequencial = c31_db_viradacaditem)
where c31_db_virada = {$virada}";
$rsDadoViradaLog = db_query($consulta);
db_utils::makeCollectionFromRecord($rsDadoViradaLog, function ($dados) use($oPdfTable) {

    $aDadosLinha[] = db_formatar($dados->data, 'd');
    $aDadosLinha[] = $dados->hora;
    $aDadosLinha[] = $dados->item;
    $aDadosLinha[] = $dados->tabela;
    $aDadosLinha[] = $dados->log;
    $oPdfTable->addLineInformation($aDadosLinha);
});

$oPdfTable->printOut($oPdf);
