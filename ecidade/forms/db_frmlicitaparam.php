<?php

/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBselller Servicos de Informatica
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
 * MODULO: licitacao
 */
$oDaoLicitaparam->rotulo->label();

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
  <body class="body-default">
    <div class="container">
      <form name="form1" method="post" action="">
        <fieldset>
          <legend><?php echo ucfirst($sNameBotaoProcessar); ?> Parametros do modulo da licitacao</legend>
          <table>

            <tr>
              <td nowrap title="<?php echo $Tl12_escolherprocesso; ?>" >
                <label class="bold" for="l12_escolherprocesso" id="lbl_l12_escolherprocesso"><?php echo $Sl12_escolherprocesso; ?>:</label>
              </td>
              <td>
                <?php
                  $x = array("f" => "NAO", "t" => "SIM");
                  db_select('l12_escolherprocesso', $x, true, $db_opcao, "");
                ?>
              </td>
            </tr>
            <tr>
              <td nowrap title="<?php echo $Tl12_escolheprotocolo; ?>" >
                <label class="bold" for="l12_escolheprotocolo" id="lbl_l12_escolheprotocolo"><?php echo $Sl12_escolheprotocolo; ?>:</label>
              </td>
              <td>
                <?php
                  $x = array("f" => "NAO", "t" => "SIM");
                  db_select('l12_escolheprotocolo', $x, true, $db_opcao, "");
                ?>
              </td>
            </tr>
            <tr>
              <td nowrap title="<?php echo $Tl12_tipoliberacaoweb; ?>" >
                <label class="bold" for="l12_tipoliberacaoweb" id="lbl_l12_tipoliberacaoweb"><?php echo $Sl12_tipoliberacaoweb; ?>:</label>
              </td>
              <td>
                <?php
                  $x = array('1' => 'Data de Abertura', '2' => 'Até Julgamento');
                  db_select('l12_tipoliberacaoweb', $x, true, $db_opcao, "onChange=js_liberacaoWebDias();");
                ?>
              </td>
            </tr>
            <?php $sDisplay = $l12_tipoliberacaoweb <> 1 ? '' : 'none'; ?>
            <tr id="trLiberacaoWebDias" style="display: <?php echo $sDisplay; ?>;">
              <td nowrap title="<?php echo $Tl12_qtdediasliberacaoweb; ?>" >
                <label class="bold" for="l12_qtdediasliberacaoweb" id="lbl_l12_qtdediasliberacaoweb"><?php echo $Sl12_qtdediasliberacaoweb; ?>:</label>
              </td>
              <td>
                <?php
                  db_input('l12_qtdediasliberacaoweb', 5, 0, true, 'text', $db_opcao);
//                  db_select('l12_qtdediasliberacaoweb', $x, true, $db_opcao, "");
                ?>
              </td>
            </tr>
            <tr>
              <td nowrap title="<?php echo $Tl12_limitetamanhoarquivo; ?>" >
                <label class="bold" for="l12_limitetamanhoarquivo" id="lbl_l12_limitetamanhoarquivo"><?php echo $Sl12_limitetamanhoarquivo; ?>:</label>
              </td>
              <td>
                <?php
                  db_input('l12_limitetamanhoarquivo', 10, $Il12_limitetamanhoarquivo, true, 'text', $db_opcao,"");
                ?>
              </td>
            </tr>
            <tr>
              <td nowrap title="<?php echo $Tl12_validalicitacon; ?>" >
                <label class="bold" for="l12_validalicitacon" id="lbl_l12_validalicitacon"><?php echo $Sl12_validalicitacon; ?>:</label>
              </td>
              <td>
                <?php
                    $aOpcoes = array(1 => 'Permitir sem avisar', 2 => 'Permitir com aviso', 3 => 'Não permitir com aviso');
                    db_select("l12_validalicitacon", $aOpcoes, true, 1);
                ?>
              </td>
            </tr>
          </table>
        </fieldset>
        <input name="<?php echo $sNameBotaoProcessar; ?>" type="submit" id="db_opcao" value="<?php echo ucfirst($sNameBotaoProcessar); ?>" <?php echo (!$db_botao ? "disabled" : ""); ?> >

      </form>
    </div>
    <?php db_menu(); ?>
  </body>
  <script>

    function js_pesquisa() {
      js_OpenJanelaIframe(
          'CurrentWindow.corpo',
          'db_iframe_licitaparam',
          'func_licitaparam.php?funcao_js=parent.js_preenchepesquisa|l12_instit',
          'Pesquisa',
          true
      );
    }

    function js_preenchepesquisa(sChave) {

      db_iframe_licitaparam.hide();
      <?php
        if ($db_opcao != 1) {
          echo "location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "?chavepesquisa=' + sChave;";
        }
      ?>
    }

    function js_liberacaoWebDias() {

      if ($F('l12_tipoliberacaoweb') == 2) {
        $('trLiberacaoWebDias').style.display = '';
      } else {
        $('trLiberacaoWebDias').style.display = 'none';
        $('l12_qtdediasliberacaoweb').value   = 0;
      }
    }

    <?php echo (isset($sPosScripts) ? $sPosScripts : ""); ?>
  </script>
</html>
