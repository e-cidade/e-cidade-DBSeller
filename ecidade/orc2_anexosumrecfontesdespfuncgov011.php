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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_liborcamento.php"));
require_once(modification("model/relatorioContabil.model.php"));

$oGet               = db_utils::postMemory($_GET);
$oRelatorioContabil = new relatorioContabil($oGet->codrel);

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
</head>
<body>

<div class="container">

    <form name="form1" id="form1">
        <table >
          <tr>
            <td colspan="3" nowrap="nowrap" class="table_header">
                Sumário Geral da Receita por Fontes e Despesa por Funções de Governo
            </td>
          </tr>
          <tr>
            <td colspan="3">
              <fieldset>
                <legend>Filtro Padrão</legend>
                <table class="form-container">
                  <?php db_selinstit('', 420, 150); ?>
                    <tr>
                        <td width="80px">Origem/Fase:</td>
                        <td>
                          <?php
                            $aOrigemFase = array(0 => "Selecione",
                                                 1 => "Orçamento",
                                                 2 => "Empenhado",
                                                 3 => "Liquidado",
                                                 4 => "Pago");
                            db_select("iOrigemFase", $aOrigemFase, true, 1);
                            ?>
                        </td>
                    </tr>
                    <tr id='trPeriodos'>
                        <td>Período:</td>
                        <td>
                          <?php
                            $aPeriodos         = $oRelatorioContabil->getPeriodos();
                            $aListaPeriodos    = array();
                            $aListaPeriodos[0] = "Selecione";
                            foreach ($aPeriodos as $oPeriodo) {
                                $aListaPeriodos[$oPeriodo->o114_sequencial] = $oPeriodo->o114_descricao;
                            }
                            
                            db_select("o116_periodo", $aListaPeriodos, true, 1);
                            ?>
                        </td>
                    </tr>
                    <tr id='trEsfera'>
                        <td title="Escolha a esfera">Esfera: </td>
                        <td>
                          <?php
                            $m = array("0"=>"Selecione", "1"=>"1 - Fiscal","2"=>"2 - Seguridade", "3"=>"3 - Total");
                            db_select("esfera", $m, true, 1);
                            ?>
                        </td>
                    </tr>                    
                    <tr>
                        <td title="Orientação Página">Orientação:</td>
                        <td>
                          <?php
                             $m = array ("P" => "Retrato","L" => "Paisagem");
                             db_select("orientacao_pagina", $m, true, 1);
                            ?>
                        </td>
                    </tr>  
                    <tr>
                        <td title="Mostrar rodapé">Mostrar rodapé:</td>
                        <td>
                          <?php
                             $m = array ("S"=> "SIM","N" => "NÃO");
                             db_select("mostrar_rodape", $m, true, 1);
                            ?>
                        </td>
                    </tr>                    
                </table>
              </fieldset>
            </td>
          </tr>
        </table>
  
        <input type="button" name="btnImprimir" id="btnImprimir" value="Imprimir" />
    </form>

</div>

<script>
  $("btnImprimir").observe("click", function() {
  
    var iOrigemFase  = $F("iOrigemFase");
    var iPeriodo     = $F("o116_periodo");
    var sInstituicao = document.form1.db_selinstit.value;

    if (sInstituicao == "") {

      alert("Selecione uma instituição.");
      return false;
    }
    var lConsolidado = 0;
    var aCheckbox    = db_selinstit_iframe.$('form1').getInputs('checkbox');
    if (aCheckbox.length == sInstituicao.split('-').length) {
      lConsolidado = 1;
    }
    if (iPeriodo == 0) {

      alert("O período é obrigatório.");
      return false;
    }

    if (iOrigemFase == 0) {

      alert("Selecione a origem/fase.");
      return false;    
    }
    var sUrlWindow  = "orc2_anexosumrecfontesdespfuncaogov002.php?";
        sUrlWindow += "sInstit="+sInstituicao;
        sUrlWindow += "&iPeriodo="+iPeriodo;
        sUrlWindow += "&iOrigemFase="+iOrigemFase;
        sUrlWindow += "&iCodRel=115";
        sUrlWindow += "&lConsolidado="+lConsolidado;
        sUrlWindow += "&esfera="+($F("esfera") == 0 || $F("esfera") == 3?"":$F("esfera"));
        sUrlWindow += "&orientacao_pagina="+$F("orientacao_pagina");
        sUrlWindow += "&mostrar_rodape="+$F("mostrar_rodape");
        
    var oJan = window.open(
            sUrlWindow, 
            '', 
            'width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0'
        );
    oJan.moveTo(0,0);   
  });
  
  $('iOrigemFase').observe("change", function () {
     if (this.value == 1) {
       
       $('trEsfera').style.display   = '';
       $('trPeriodos').style.display   = 'none';
       $('o116_periodo').selectedIndex = 1;
     } else {
       $('trEsfera').style.display = 'none'; 
       $('o116_periodo').selectedIndex = 0;
       $('trPeriodos').style.display   = '';
     }
  });
  
  
</script>

</body>
</html>
