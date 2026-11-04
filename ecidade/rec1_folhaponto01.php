<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009 DBSeller Servicos de Informatica
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
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_assenta_classe.php"));
include(modification("dbforms/db_classesgenericas.php"));

db_postmemory($HTTP_POST_VARS);

$classenta   = new cl_assenta;
$rotulocampo = new rotulocampo;

$rotulocampo->label("rh01_regist");
$rotulocampo->label("z01_nome");
$rotulocampo->label("rh02_anousu");
$rotulocampo->label("localtrabdesc");
$rotulocampo->label("localtrab");

?>
<html>

<head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" bgcolor="#cccccc">
  <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
    <tr>
      <td width="360" height="18">&nbsp;</td>
      <td width="263">&nbsp;</td>
      <td width="25">&nbsp;</td>
      <td width="140">&nbsp;</td>
    </tr>
  </table>
  <table align="center">
    <form name="form1" method="post" action="">
      <tr>
        <td align="right" title="<?php echo $Trh01_regist ?>">
          <?php
          db_ancora(@$Lrh01_regist, "js_pesquisarh01_regist(true);", 1);
          ?>
        </td>
        <td>
          <?php
          db_input('rh01_regist', 8, $Irh01_regist, true, 'text', 1, " onchange='js_pesquisarh01_regist(false);'")
          ?>
          <?php
          db_input('z01_nome', 30, $Iz01_nome, true, 'text', 3, '');
          ?>
        </td>
      </tr>
      <td align="right" title="<?php echo $Trh01_regist ?>">
        <?php
        db_ancora("<strong>Local de Trabalho:</strong>", "js_pesquisarlocal(true);", 1);
        ?>
      </td>
      <td>
        <?php
        db_input('localtrab', 8, 1, true, 'text', 1, " onchange='js_pesquisarlocal(false);'")
        ?>
        <?php
        db_input('localtrabdesc', 30, 3, true, 'text', 3, '');
        ?>
      </td>
      </tr>
      <tr>
        <td nowrap title="Ano / Mês exercício" align="right">
          <b>Exercício:</b>
        </td>
        <td nowrap>
          <?php
          $arr_mes = array(
            '' => '',
            '01' => 'Janeiro',
            '02' => 'Fevereiro',
            '03' => 'Março',
            '04' => 'Abril',
            '05' => 'Maio',
            '06' => 'Junho',
            '07' => 'Julho',
            '08' => 'Agosto',
            '09' => 'Setembro',
            '10' => 'Outubro',
            '11' => 'Novembro',
            '12' => 'Dezembro'
          );
          db_select("rh02_mesusu", $arr_mes, true, 1, "");
          ?>
        </td>
      </tr>
      <tr>
        <td align="right"> <strong>Ano: </strong>

        <td><?php
            db_input('rh02_anousu', 4, $Irh02_anousu, true, 'text', 1, "", '', '', '', '4')
            ?></td>
        <td></td>
      </tr>
      <tr>
        <td colspan="2" align="center">
          <input name="relatorio" id="relatorio" type="button" value="Relatório" onclick="js_emite();">
        </td>
      </tr>
    </form>
  </table>
  <?php
  db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));
  ?>
</body>

</html>
<script>
  function js_emite() {
    qry = "&localtrab=" + document.form1.localtrab.value;
    qry += "&regist=" + document.form1.rh01_regist.value;
    qry += "&mes=" + document.form1.rh02_mesusu.value;
    qry += "&ano=" + document.form1.rh02_anousu.value;

    jan = window.open('rec1_folhaponto02.php?' + qry, '', 'width=' + (screen.availWidth - 5) + ',height=' + (screen.availHeight - 40) + ',scrollbars=1,location=0 ');
    jan.moveTo(0, 0);

  }

  function js_pesquisarh01_regist(mostra) {
    if (mostra == true) {
      js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_rhpessoal', 'func_rhpessoal.php?funcao_js=parent.js_mostrapessoal1|rh01_regist|z01_nome&instit=<?php echo (db_getsession("DB_instit")) ?>', 'Pesquisa', true);
    } else {
      if (document.form1.rh01_regist.value != '') {
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_rhpessoal', 'func_rhpessoal.php?pesquisa_chave=' + document.form1.rh01_regist.value + '&funcao_js=parent.js_mostrapessoal&instit=<?php echo (db_getsession("DB_instit")) ?>', 'Pesquisa', false);
      } else {
        document.form1.z01_nome.value = '';
        js_seleciona_combo(document.form1.objeto2);
      }
    }
  }

  function js_mostrapessoal(chave, erro) {
    document.form1.z01_nome.value = chave;
    if (erro == true) {
      document.form1.rh01_regist.focus();
      document.form1.rh01_regist.value = '';
    } else {
      js_seleciona_combo(document.form1.objeto2);
    }
  }

  function js_mostrapessoal1(chave1, chave2) {
    document.form1.rh01_regist.value = chave1;
    document.form1.z01_nome.value = chave2;
    db_iframe_rhpessoal.hide();
    js_seleciona_combo(document.form1.objeto2);
  }

  function js_pesquisarlocal(mostra) {
    if (mostra == true) {
      js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_rhloc', 'func_rhlocaltrab.php?funcao_js=parent.js_mostralocal1|rh55_estrut|rh55_descr&instit=<?php echo (db_getsession("DB_instit")) ?>', 'Pesquisa', true);
    } else {
      js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_rhloc', 'func_rhlocaltrab.php?pesquisa_chave=' + document.form1.localtrab.value + '&funcao_js=parent.js_mostralocal&ponto=1?>', 'Pesquisa', false);
    }
  }

  function js_mostralocal(chave, erro) {
    document.form1.localtrabdesc.value = chave;
    if (erro == true) {
      document.form1.localtrab.focus();
      document.form1.localtrab.value = '';
    } else {
      js_seleciona_combo(document.form1.objeto2);
    }
  }

  function js_mostralocal1(chave1, chave2) {
    document.form1.localtrab.value = chave1;
    document.form1.localtrabdesc.value = chave2;
    db_iframe_rhloc.hide();
    js_seleciona_combo(document.form1.objeto2);
  }

  function js_relatorio2() {
    var F = document.form1;
    var datai = "";
    var dataf = "";
    if (F.datai_dia.value != "" && F.datai_mes.value != "" && F.datai_ano.value != "") {
      datai = F.datai_ano.value + '-' + F.datai_mes.value + '-' + F.datai_dia.value;
    }
    if (F.dataf_dia.value != "" && F.dataf_mes.value != "" && F.dataf_ano.value != "") {
      dataf = F.dataf_ano.value + '-' + F.dataf_mes.value + '-' + F.dataf_dia.value;
    }
    if (datai == "" && dataf == "") {
      alert("Informe o período de admissão.");
      F.datai_dia.focus();
    } else {
      qry = "?datai=" + datai;
      qry += "&dataf=" + dataf;
      qry += "&ordem=" + F.ordem.value;
      qry += "&regime=" + F.regime.value;
      if (F.listaponto.checked == true) {
        qry += "&fixo=s";
      } else {
        qry += "&fixo=n";
      }
      qry += "&lota=" + F.lota.value;

    }
  }
</script>
