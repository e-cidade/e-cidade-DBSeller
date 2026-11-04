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

/**
 * MODULO: Fumam
 */

 $oDaoAssociadotiposservicos->rotulo->label();

if ($db_opcao == 1) {
  $sNameBotaoProcessar = "incluir";
} else if ($db_opcao == 2 || $db_opcao == 22) {
  $sNameBotaoProcessar = "alterar";
} else {
  $sNameBotaoProcessar = "excluir";
}

?>

<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
  </head>
  <body class="body-default" onload="js_carregaTela()">
    <div class="container">
      <form name="form1" method="post" action="">
        <fieldset>
          <legend>Tipo de Serviço</legend>
          <table>
            <tr>
              <td nowrap title="<?php echo $Tfm09_codigo; ?>" >
                <label class="bold" for="fm09_codigo" id="lbl_fm09_codigo"><?php echo $Sfm09_codigo; ?>:</label>
              </td>
              <td>
                <?php db_input('fm09_codigo', 10, $Ifm09_codigo, true, 'text', 3, ""); ?>
              </td>
            </tr>
            <tr>
              <td nowrap title="<?php echo $Tfm09_descricao; ?>" >
                <label class="bold" for="fm09_descricao" id="lbl_fm09_descricao"><?php echo $Sfm09_descricao; ?>:</label>
              </td>
              <td>
                <?php db_input('fm09_descricao', $Mfm09_descricao, $Ifm09_descricao, true, 'text', $db_opcao, ""); ?>
              </td>
            </tr>
            <tr>
              <td>
                <label class="bold" for="fm09_copart_percentual" id="lbl_fm09_copart_percentual">Coparticipação:</label>
              </td>
              <td>
                <?php db_input('fm09_copart_percentual', 50, $Ifm09_copart_percentual, true, 'radio',$db_opcao, " onchange='js_controle_coparticipacao(this);'");?>
                <label class="bold" for="fm09_copart_percentual" id="lbl_fm09_copart_percentual"><?php echo $Sfm09_copart_percentual; ?></label>
                <?php db_input('fm09_copart_financeiro', 50, $Ifm09_copart_financeiro, true, 'radio',$db_opcao, " onchange='js_controle_coparticipacao(this);'");?>
                <label class="bold" for="fm09_copart_financeiro" id="lbl_fm09_copart_financeiro"><?php echo $Sfm09_copart_financeiro; ?></label>
              </td>
            </tr>

            <tr>
              <td nowrap title="<?php echo $Tfm09_valor; ?>" >
                <label class="bold" for="fm09_valor" id="lbl_fm09_valor"><?php echo $Sfm09_valor; ?>:</label>
              </td>
              <td>
                <?php
                  if ( isset($fm09_copart_financeiro) && $fm09_copart_financeiro == 't' ) {
                     $formatter = new NumberFormatter('pt_BR',  NumberFormatter::CURRENCY);
                     $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);
                     $formatter->setAttribute(NumberFormatter::GROUPING_USED, true);
                     $formatter->setSymbol(NumberFormatter::CURRENCY_SYMBOL, 'R$');
                     $fm09_valor = $formatter->formatCurrency($fm09_valor, 'BRL');
                     $fm09_valor = utf8_decode($fm09_valor);
                  }

                  if ( isset($fm09_copart_percentual) && $fm09_copart_percentual == 't' ) {
                     $formatter = new NumberFormatter('pt_BR', NumberFormatter::PERCENT);
                     $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);
                     $formatter->setAttribute(NumberFormatter::GROUPING_USED, true);
                     $formatter->setSymbol(NumberFormatter::PERCENT_SYMBOL, '%');

                     $fm09_valor = $formatter->format($fm09_valor/100);
                  }

                  db_input('fm09_valor', 15, $Ifm09_valor, true, 'text', $db_opcao," onkeyup='js_mascaraCampo(this);' ");
                ?>
              </td>
            </tr>

          </table>
        </fieldset>
        <input name="<?php echo $sNameBotaoProcessar; ?>" type="submit" id="db_opcao" value="<?php echo ucfirst($sNameBotaoProcessar); ?>" <?php echo (!$db_botao ? "disabled" : ""); ?> >
        <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
      </form>
    </div>
    <?php db_menu(); ?>
  </body>
  <script>

    function js_carregaTela() {
      
      if (document.getElementById('fm09_valor').readOnly == true) {
         document.getElementById('fm09_copart_percentual').disabled = true;
         document.getElementById('fm09_copart_financeiro').disabled = true;
      }

      if (document.getElementById('fm09_copart_percentual').value == 't') {
         document.form1.fm09_copart_percentual.checked = true;
         document.form1.fm09_copart_financeiro.checked = false;
      }

      if (document.getElementById('fm09_copart_financeiro').value == 't') {
         document.form1.fm09_copart_financeiro.checked = true;
         document.form1.fm09_copart_percentual.checked = false;
      }

    }

    function js_mascaraCampo(elemento) {

      var valor = elemento.value.replace(/[^0-9]/g, '');

      if (document.form1.fm09_copart_percentual.checked == true) {
         var options = { style: 'percent',
                         currency: 'BRL',
                         minimumFractionDigits: 2,
                         maximumFractionDigits: 2 };

         valor = (valor/10000).toFixed(4);
         document.form1.fm09_copart_percentual.value = true;
         document.form1.fm09_copart_financeiro.value = false;

      } else if (document.form1.fm09_copart_financeiro.checked == true) {
         var options = { style: 'currency',
                         currency: 'BRL',
                         minimumFractionDigits: 2,
                         maximumFractionDigits: 2 };

         valor = (valor/100).toFixed(2);
         document.form1.fm09_copart_percentual.value = false;
         document.form1.fm09_copart_financeiro.value = true;

      }

      const formatNumber = new Intl.NumberFormat('pt-BR', options);

      valor = formatNumber.format(valor);
      elemento.value = valor;

    }

    function js_controle_coparticipacao(oCampo,iOpcao) {

        const aCampos = document.querySelectorAll("input[type='radio']");

        aCampos.forEach(function (oCampo1) {
            if (oCampo.name != oCampo1.name) {
                oCampo1.checked = false;
                document.getElementById('fm09_valor').value = '';
            }
        });
    }

    function js_pesquisa() {
      js_OpenJanelaIframe( 'CurrentWindow.corpo', 
                           'db_iframe_associadotiposservicos', 
                           'func_associadotiposservicos.php?funcao_js=parent.js_preenchepesquisa|fm09_codigo', 
                           'Pesquisa', true);
    }

    function js_preenchepesquisa(sChave) {

      db_iframe_associadotiposservicos.hide();

      <?php
        if ($db_opcao != 1) {
          echo "location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "?chavepesquisa=' + sChave;";
        }
      ?>
    }

    <?php echo (isset($sPosScripts) ? $sPosScripts : ""); ?>
  </script>
</html>
