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
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

$clacordo = new cl_acordo;
$clacordocomissaomembro = new cl_acordocomissaomembro;
$clacordoproc = new cl_acordoproc;
$clprotprocesso = new cl_protprocesso;
db_postmemory($HTTP_POST_VARS);
$db_opcao = 22;
$db_botao = false;

if(isset($alterar)) {

  $sqlerro = false;
  db_inicio_transacao();
  $clacordo->alterar($ac16_sequencial);
  if($clacordo->erro_status == 0){
    $sqlerro = true;
  }
  $erro_msg = $clacordo->erro_msg;
  db_fim_transacao($sqlerro);
  $db_opcao = 2;
  $db_botao = true;
} else if(isset($chavepesquisa)) {

   $db_opcao = 2;
   $db_botao = true;
}

if (isset($chavepesquisa)) {

  /**
   * Libera somente alguns campos para alteração caso a situação seja "Homologado"
   */
  $sSqlAcordo = $clacordo->sql_query($chavepesquisa);
  $rsAcordo   = db_query($sSqlAcordo);
  if ($rsAcordo && pg_num_rows($rsAcordo) > 0) {

    $iSituacao = db_utils::fieldsMemory($rsAcordo, 0)->ac17_sequencial;
    db_fieldsmemory($rsAcordo, 0);
    if ($iSituacao == 4) {
      $db_opcao          = 3;
      $db_opcao_editavel = 2;

    }

    $cl_acordoparam = new cl_acordoparam;
    $iInstituicaoSessao = db_getsession('DB_instit');
    $sql = $cl_acordoparam->sql_query_file(null, "*", null, "ac59_instituicao = {$iInstituicaoSessao}");
    $retorno = pg_fetch_assoc($cl_acordoparam->sql_record($sql));
    $parametro_exibe_fiscal = $retorno['ac59_cadastrocomissoesfiscal'] === 't' ? true : false;
    if ($parametro_exibe_fiscal) {
        $sSqlComissaoMembros = $clacordocomissaomembro->sql_query(null, "*", null, "ac07_acordocomissao = $ac16_acordocomissao and ac42_sequencial = 4");
        $rsComissaoMembros = db_query($sSqlComissaoMembros);

        for ($i = 0; $i < pg_num_rows($rsComissaoMembros); $i++) {
            db_fieldsmemory($rsComissaoMembros, $i);
            $fiscalcgm[] = $ac07_numcgm;
        }
        $sSqlComissaoMembros = $clacordocomissaomembro->sql_query(null, "*", null, "ac07_acordocomissao = $ac16_acordocomissao and ac42_sequencial = 1");
        $rsComissaoMembros = db_query($sSqlComissaoMembros);
        db_fieldsmemory($rsComissaoMembros, 0);
        $gestorcgm = $ac07_numcgm;
    }
  }

  $sSqlAcordoProc = $clacordoproc->sql_query_file(null, "ac62_protprocesso", "", "ac62_acordo = {$chavepesquisa}");
  $rsAcordoProc = $clacordoproc->sql_record($sSqlAcordoProc);
  if ($clacordoproc->numrows > 0) {
      $ac62_protprocesso = db_utils::fieldsMemory($rsAcordoProc, 0)->ac62_protprocesso;

      $sSqlProtProcesso = $clprotprocesso->sql_query($ac62_protprocesso, "z01_nome");
      $rsProtProcesso = $clprotprocesso->sql_record($sSqlProtProcesso);
      if ($clprotprocesso->numrows > 0) {
          $ac16_descprocesso = db_utils::fieldsMemory($rsProtProcesso, 0)->z01_nome;
      }
  }
}

unset($_SESSION["oContrato"]);
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<?php
db_app::load("scripts.js, strings.js, datagrid.widget.js, windowAux.widget.js");
db_app::load("dbmessageBoard.widget.js, prototype.js, contratos.classe.js,http/http.js");
db_app::load("estilos.css, grid.style.css");
?>
<script type="text/javascript" src="scripts/prototype.js"></script>
<script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
  <?php
  $sTipoFiltro = '1,4';
  require_once(modification("forms/db_frmacordo.php"));
  ?>
</body>
</html>
<?php
if(isset($alterar)) {

  if($sqlerro == true) {

    db_msgbox($erro_msg);
    if($clacordo->erro_campo != "") {

      echo "<script> document.form1.".$clacordo->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clacordo->erro_campo.".focus();</script>";
    };
  } else {
   db_msgbox($erro_msg);
  }
}

if(isset($chavepesquisa)) {
 echo "
  <script>
      function js_db_libera() {

         parent.document.formaba.acordo.disabled=false;
         parent.document.formaba.acordogarantia.disabled=false;
         (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_acordogarantia.location.href='aco1_acordoacordogarantia001.php?ac12_acordo=". (isset($chavepesquisa) ? $chavepesquisa : null) ."';
         parent.document.formaba.acordopenalidade.disabled=false;
         (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_acordopenalidade.location.href='aco1_acordoacordopenalidade001.php?ac15_acordo=". (isset($chavepesquisa) ? $chavepesquisa : null) ."';
         parent.document.formaba.acordoitem.disabled=false;
         (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_acordoitem.location.href='aco1_acordoitem001.php?ac20_acordo=". (isset($chavepesquisa) ? $chavepesquisa : null) ."';
         parent.document.formaba.acordodocumento.disabled=false;
         (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_acordodocumento.location.href='aco1_acordodocumento001.php?ac40_acordo=". (isset($chavepesquisa) ? $chavepesquisa : null) ."';
     ";
         if(isset($liberaaba)){
         }
 echo"}\n
    js_db_libera();
  </script>\n
 ";
}

if($db_opcao == 22 || $db_opcao == 33){
  echo "<script>document.form1.pesquisar.click();</script>";
}
