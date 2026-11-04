<?php
require("../../libs/db_stdlib.php");
require("../../libs/db_conecta.php");
include("../../libs/db_sessoes.php");
include("../../libs/db_usuariosonline.php");
include("../../libs/db_utils.php");
include("../../dbforms/db_funcoes.php");

// require_once '../../model/impressao.bematechMP2100TH.php';

// Slip
/*
require_once '../../model/modeloAutentTermicaSlip.model.php';
$oModeloSlip = new modeloAutentTermicaSlip("192.168.0.72","4444");
$oModeloSlip->imprimir(100,38,'2005-08-01',2);
*/

// Empenho
/*
require_once '../../model/modeloAutentTermicaEmpenho.model.php';
$oModeloEmpenho = new modeloAutentTermicaEmpenho("192.168.0.18","4444");
$oModeloEmpenho->imprimir(100,38,'2005-08-01',2);
*/

// Resumo
require_once '../../model/modeloAutentTermicaResumo.php';
$oModeloResumo = new modeloAutentTermicaResumo("192.168.0.18","4444");
$oModeloResumo->imprimir(38,'2005-08-01',2);

echo "<script>parent.db_iframe_teste.hide();</script>";


/*
require_once '../../model/impressao.bematechMP2100TH.php';

$oImpressora = new impressaoMP2100TH('192.168.0.18','4444');
$oImpressora->inicializa();
$oImpressora->setLarguraPadrao();

$sStr  = strToAsc("\n15/04/2009 PREFEITURA MUNICIPAL DE BAGÉ");
$sStr .= "\n".$oImpressora->aplicarNegrito("Conta:")."1236-2 CNPJ:88.073.291/0001-99".$oImpressora->aplicarNegrito("Term:")."999";
$sStr .= "\n".str_pad("",48,'=',STR_PAD_BOTH);
$sStr .= "\n".str_pad("",48,'=',STR_PAD_BOTH);
$sStr .= "\n".$oImpressora->aplicarNegrito(str_pad("Login:",14,' ',STR_PAD_RIGHT))."1-DBSELLER SISTEMAS DE INFORMATICA";
$sStr .= "\n".$oImpressora->aplicarNegrito(str_pad("Departamento:",14,' ',STR_PAD_RIGHT))."100 - TESTES";
$sStr .= "\n".$oImpressora->aplicarNegrito(str_pad("IP Terminal:",14,' ',STR_PAD_RIGHT))."192.168.0.18";
$sStr .= "\n".$oImpressora->aplicarNegrito(str_pad("Data:",14,' ',STR_PAD_RIGHT))."15/04/2009".
                                           str_pad($oImpressora->aplicarNegrito("Hora:")." 00:00",24,' ',STR_PAD_LEFT);

$sStr .= "\n";

$oImpressora->escreverTexto($sStr);
$oImpressora->cortarPapel();
$oImpressora->finaliza();
$oImpressora->rodarComandos();
*/
?>