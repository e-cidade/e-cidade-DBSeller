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
require_once(modification("libs/db_stdlibwebseller.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

$isSecretaria = ((db_getsession("DB_modulo") == 7159)?true:false);
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/EmissaoRelatorio.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<style>
 .select {
    height:18px;
    font-size:10px;
 }
 
</style>
</head>
<body>
<div class="container">
<?php
if (!$isSecretaria) {
    MsgAviso(db_getsession("DB_coddepto"), "escola");
}
?>
<form name="form1" method="post">
<fieldset>
  <legend>Listagem de alunos com numero da matricula da certidão inválido</legend>
  <table class="form-container">
    <?php
    if ($isSecretaria) {
        $escola = new cl_escola();
        $sqlEscola = $escola->sql_query_file("", "ed18_i_codigo, ed18_c_nome", "ed18_c_nome", "");
        $rsEscola = $escola->sql_record($sqlEscola);
        $iLinhas = $escola->numrows;
          
        echo '<tr>';
        echo '  <td>Selecione a escola:</td>';
        echo '  <td>';
        echo '    <select name="escola" id="escola" onchange="pesquisaEtapas()">';
        echo '     <option value="">Selecione a escola</option>';
        echo '     <option value="">Todas as escolas</option>';
  
        for ($iQtd = 0; $iQtd < $iLinhas; $iQtd++) {
            $oDadosEscola = db_utils::fieldsmemory($rsEscola, $iQtd);
            echo " <option value='{$oDadosEscola->ed18_i_codigo}'>{$oDadosEscola->ed18_c_nome}</option>";
        }
  
        echo '    </select>';
        echo '  </td>';
        echo '</tr>';
    } else {
        $iEscola = db_getsession("DB_coddepto");
        echo "<input type= 'hidden' id ='escola' value = '{$iEscola}' >";
    }
    ?>
    <tr>
      <td>
        Ano letivo:
      </td>
      <td>
        <?php
           db_input("ano", 10, 1, true, "text", 1, "onchange='pesquisaEtapas()'");
        ?>
      </td>
    </tr>
    <tr>
      <td>
        Selecione a Etapa:
      </td>
      <td>
        <select name="etapa" id="etapa">
            <option value=""></option>
        </select>
      </td>
    </tr>
    <tr>
      <td>
        Tipo de Preenchimento:
      </td>
      <td>
        <select name="tipoPreenchimento" id="tipoPreenchimento">
            <option value="">Todos os tipos</option>
            <option value="0">Apenas sem matriculas informadas</option>
            <option value="1">Apenas com matriculas informadas</option>
        </select>
      </td>
    </tr>
  </table>
  
</fieldset>
<input type="button" name="emitir" id="emitir" value="Emitir">
</form>
</div>
<?php db_menu();?>
<script>
const isSecretaria = <?=$isSecretaria?>;
const urlRPC = 'edu4_escola.RPC.php'; 

function pesquisaEtapas() {
  if ($F('ano') == "") {
    sHtml = '<option value=""></option>';
    $('etapa').innerHTML = sHtml;
    return false;
  }

  $('etapa').innerHTML = "";
  
  var oParam = new Object();
  oParam.exec = "PesquisaEtapaAno";
  oParam.escola = $F('escola');
  oParam.calendario = $F('ano');
  var oAjax = new Ajax.Request(urlRPC,
                                 {
                                   method    : 'post',
                                   parameters: 'json='+Object.toJSON(oParam), 
                                   onComplete: retornoPesquisaEtapas
                                 });
}

function retornoPesquisaEtapas(oRetorno) {
  var oRetorno = JSON.parse(oRetorno.responseText);

  if (oRetorno.iStatus != 1) {
    alert(oRetorno.sMessage.urlDecode());
    return false;
  } else {
    sHtml = '';
    if (oRetorno.aResult1.length == 0) {
      sHtml += '<option value="">Não há Etapa</option>';
    } else {
      sHtml += '<option value="">Selecione a Etapa</option>';
      sHtml += '<option value="">Todas</option>';

      for (var i = 0;i < oRetorno.aResult1.length; i++) {
         sHtml += '<option value="'+oRetorno.aResult1[i].ed11_i_codigo+'">';
         sHtml += oRetorno.aResult1[i].ed11_c_descr.urlDecode();
         sHtml += '</option>';
      }
    }
    $('etapa').innerHTML = sHtml;
    $('etapa').disabled = false;
  }
}

$('emitir').addEventListener('click', function() {
        var oParametros = {
            escola: $F('escola'),
            ano: $F('ano'),
            etapa: $F('etapa'), 
            tipoPreenchimento: $F('tipoPreenchimento') 
        };

  new EmissaoRelatorio("edu2_alunosmatriculacertidaoinvalida002.php", oParametros).open();
});
</script>
</body>
</html>