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

//MODULO: veiculos
$clveicmotoristas->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("z01_nome");
$clrotulo->label("ve30_descr");
$clrotulo->label("ve33_descr");

$res_param = $clveicparam->sql_record($clveicparam->sql_query_file(null, "ve50_integrapessoal", null, "ve50_instit = " . db_getsession("DB_instit")));
if ($clveicparam->numrows > 0) {
  db_fieldsmemory($res_param, 0);
} else {
  db_msgbox("Parametros nao configurados. Verifique.");
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

<body>
  <div class="container">
    <form name="form1" method="post" action="">
      <fieldset>
        <legend>Dados do Motorista</legend>
        <table border="0">
          <tr>
            <td nowrap title="<?php echo (@$Tve05_codigo) ?>">
              <?php echo (@$Lve05_codigo) ?>
            </td>
            <td>
              <?php
              db_input('ve05_codigo', 10, $Ive05_codigo, true, 'text', 3, "")
              ?>
            </td>
          </tr>
          <tr>
            <td nowrap title="<?php echo (@$Tve05_numcgm) ?>">
              <?php
              db_ancora(@$Lve05_numcgm, "js_pesquisave05_numcgm(true);", $db_opcao);
              ?>
            </td>
            <td>
              <?php
              if (isset($ve50_integrapessoal) && trim(@$ve50_integrapessoal) != "") {
                if ($ve50_integrapessoal == 1) {
                  $pessoal = "true";
                }

                if ($ve50_integrapessoal == 2) {
                  $pessoal = "false";
                }
              }
              db_input('ve05_numcgm', 10, $Ive05_numcgm, true, 'text', $db_opcao, " onchange='js_pesquisave05_numcgm(false);'")
              ?>
              <?php
              db_input('z01_nome', 40, $Iz01_nome, true, 'text', 3, '')
              ?>
            </td>
          </tr>
          <tr>
            <td nowrap title="<?php echo (@$Tve05_habilitacao) ?>">
              <?php echo (@$Lve05_habilitacao) ?>
            </td>
            <td>
              <?php
              db_input('ve05_habilitacao', 20, $Ive05_habilitacao, true, 'text', $db_opcao, "")
              ?>
            </td>
          </tr>
          <tr>
            <td nowrap title="<?php echo (@$Tve05_veiccadcategcnh) ?>">
              <?php
              db_ancora(@$Lve05_veiccadcategcnh, "js_pesquisave05_veiccadcategcnh(true);", $db_opcao);
              ?>
            </td>
            <td>
              <?php
              db_input('ve05_veiccadcategcnh', 10, $Ive05_veiccadcategcnh, true, 'text', $db_opcao, " onchange='js_pesquisave05_veiccadcategcnh(false);'")
              ?>
              <?php
              db_input('ve30_descr', 40, $Ive30_descr, true, 'text', 3, '')
              ?>
            </td>
          </tr>
          <tr>
            <td nowrap title="<?php echo (@$Tve05_dtvenc) ?>">
              <?php echo (@$Lve05_dtvenc) ?>
            </td>
            <td>
              <?php
              db_inputdata('ve05_dtvenc', @$ve05_dtvenc_dia, @$ve05_dtvenc_mes, @$ve05_dtvenc_ano, true, 'text', $db_opcao, "")
              ?>
            </td>
          </tr>
          <tr>
            <td nowrap title="<?php echo (@$Tve05_dtprimcnh) ?>">
              <?php echo (@$Lve05_dtprimcnh) ?>
            </td>
            <td>
              <?php
              db_inputdata('ve05_dtprimcnh', @$ve05_dtprimcnh_dia, @$ve05_dtprimcnh_mes, @$ve05_dtprimcnh_ano, true, 'text', $db_opcao, "")
              ?>
            </td>
          </tr>
          <tr>
            <td nowrap title="<?php echo (@$Tve05_veiccadmotoristasit) ?>">
              <?php
              db_ancora(@$Lve05_veiccadmotoristasit, "js_pesquisave05_veiccadmotoristasit(true);", $db_opcao);
              ?>
            </td>
            <td>
              <?php
              db_input('ve05_veiccadmotoristasit', 10, $Ive05_veiccadmotoristasit, true, 'text', $db_opcao, " onchange='js_pesquisave05_veiccadmotoristasit(false);'")
              ?>
              <?php
              db_input('ve33_descr', 40, $Ive33_descr, true, 'text', 3, '')
              ?>
            </td>
          </tr>
        </table>
      </fieldset>
      <input name="<?php echo ($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir")) ?>" type="submit" id="db_opcao" value="<?php echo ($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>" <?php echo ($db_botao == false ? "disabled" : "") ?>>
      <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();">
    </form>
  </div>
</body>

<script>
  function js_pesquisave05_numcgm(mostra) {
    if (mostra == true) {
      js_OpenJanelaIframe('CurrentWindow.corpo.iframe_veicmotoristas', 'db_iframe_cgm', 'func_veicnomealt.php?pessoal=<?php echo ($pessoal) ?>&funcao_js=parent.js_mostracgm1|z01_numcgm|z01_nome', 'Pesquisa', true, '0');
    } else {
      if (document.form1.ve05_numcgm.value != '') {
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_veicmotoriastas', 'db_iframe_cgm', 'func_veicnomealt.php?pessoal=<?php echo ($pessoal) ?>&pesquisa_chave=' + document.form1.ve05_numcgm.value + '&funcao_js=parent.js_mostracgm', 'Pesquisa', false);
      } else {
        document.form1.z01_nome.value = '';
      }
    }
  }

  function js_mostracgm(chave, erro) {
    document.form1.z01_nome.value = chave;
    if (erro == true) {
      document.form1.ve05_numcgm.focus();
      document.form1.ve05_numcgm.value = '';
    }
  }

  function js_mostracgm1(chave1, chave2) {
    document.form1.ve05_numcgm.value = chave1;
    document.form1.z01_nome.value = chave2;
    db_iframe_cgm.hide();
  }

  function js_pesquisave05_veiccadcategcnh(mostra) {
    if (mostra == true) {
      js_OpenJanelaIframe('CurrentWindow.corpo.iframe_veicmotoristas', 'db_iframe_veiccadcategcnh', 'func_veiccadcategcnh.php?funcao_js=parent.js_mostraveiccadcategcnh1|ve30_codigo|ve30_descr', 'Pesquisa', true, '0');
    } else {
      if (document.form1.ve05_veiccadcategcnh.value != '') {
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_veicmotoristas', 'db_iframe_veiccadcategcnh', 'func_veiccadcategcnh.php?pesquisa_chave=' + document.form1.ve05_veiccadcategcnh.value + '&funcao_js=parent.js_mostraveiccadcategcnh', 'Pesquisa', false);
      } else {
        document.form1.ve30_descr.value = '';
      }
    }
  }

  function js_mostraveiccadcategcnh(chave, erro) {
    document.form1.ve30_descr.value = chave;
    if (erro == true) {
      document.form1.ve05_veiccadcategcnh.focus();
      document.form1.ve05_veiccadcategcnh.value = '';
    }
  }

  function js_mostraveiccadcategcnh1(chave1, chave2) {
    document.form1.ve05_veiccadcategcnh.value = chave1;
    document.form1.ve30_descr.value = chave2;
    db_iframe_veiccadcategcnh.hide();
  }

  function js_pesquisave05_veiccadmotoristasit(mostra) {
    if (mostra == true) {
      js_OpenJanelaIframe('CurrentWindow.corpo.iframe_veicmotoristas', 'db_iframe_veiccadmotoristasit', 'func_veiccadmotoristasit.php?funcao_js=parent.js_mostraveiccadmotoristasit1|ve33_codigo|ve33_descr', 'Pesquisa', true, '0');
    } else {
      if (document.form1.ve05_veiccadmotoristasit.value != '') {
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_veicmotoristas', 'db_iframe_veiccadmotoristasit', 'func_veiccadmotoristasit.php?pesquisa_chave=' + document.form1.ve05_veiccadmotoristasit.value + '&funcao_js=parent.js_mostraveiccadmotoristasit', 'Pesquisa', false);
      } else {
        document.form1.ve33_descr.value = '';
      }
    }
  }

  function js_mostraveiccadmotoristasit(chave, erro) {
    document.form1.ve33_descr.value = chave;
    if (erro == true) {
      document.form1.ve05_veiccadmotoristasit.focus();
      document.form1.ve05_veiccadmotoristasit.value = '';
    }
  }

  function js_mostraveiccadmotoristasit1(chave1, chave2) {
    document.form1.ve05_veiccadmotoristasit.value = chave1;
    document.form1.ve33_descr.value = chave2;
    db_iframe_veiccadmotoristasit.hide();
  }

  function js_pesquisa() {
    js_OpenJanelaIframe('CurrentWindow.corpo.iframe_veicmotoristas', 'db_iframe_veicmotoristas', 'func_veicmotoristas.php?funcao_js=parent.js_preenchepesquisa|ve05_codigo', 'Pesquisa', true, '0');
  }

  function js_preenchepesquisa(chave) {
    db_iframe_veicmotoristas.hide();
    <?php
    if ($db_opcao != 1) {
      echo " location.href = '" . basename($_SERVER["PHP_SELF"]) . "?chavepesquisa='+chave";
    }
    ?>
  }
</script>

</html>
