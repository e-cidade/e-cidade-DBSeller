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
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("model/patrimonio/BemClassificacao.model.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("classes/db_clabens_classe.php"));
require_once(modification("classes/db_cfpatri_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("dbforms/db_classesgenericas.php"));
db_postmemory($_POST);
$oPost = db_utils::postMemory($_POST);
$clclabens   = new cl_clabens;
$cldb_estrut = new cl_db_estrut;
$clcfpatri   = new cl_cfpatri;
$db_opcao    = 1;
$db_botao    = true;
$t86_conplano_desativado = "";
$t86_conplanodepreciacao_desativado = "";
if(isset($incluir)){

  if ($oPost->t64_bemtipos == "0") {
    $erro_msg = _M("patrimonial.patrimonio.db_frmclabens.informe_tipo_bem");
  } else {

    try {

      db_inicio_transacao();
      $_POST['t64_ativa'] = $oPost->situacao;

      $oClassificacao = new BemClassificacao($oPost->t64_codcla);
      $oClassificacao->setCodigo($oPost->t64_codcla);
      $oClassificacao->setClassificacao($oPost->t64_class);
      $oClassificacao->setDescricao($oPost->t64_descr);
      $oClassificacao->setObservacao($oPost->t64_obs);
      $oClassificacao->setPlanoConta($oPost->t86_conplano);
      $oClassificacao->setCodigoContaDepreciacao($oPost->t86_conplanodepreciacao);
      $oClassificacao->setAnalitica($oPost->t64_analitica);
      $oClassificacao->setTipoBem($oPost->t64_bemtipos);
      $oClassificacao->setTipoDepreciacao($oPost->t64_benstipodepreciacao);
      $oClassificacao->setVidaUtil($oPost->t64_vidautil);
      $oClassificacao->setValorResidual($oPost->t64_valorresidual);
      $oClassificacao->setInstituicao(db_getsession("DB_instit"));
      $oClassificacao->setStatus($oPost->situacao);
      $oClassificacao->salvar();
      $erro_msg = _M("patrimonial.patrimonio.db_frmclabens.classificacao_salva");
      db_fim_transacao(false);

    } catch (Exception $eErro) {

      db_fim_transacao(true);
      $erro_msg = str_replace("\n", "\\n", $eErro->getMessage());
    }
  }
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC>
<div>
	<?php
	include(modification("forms/db_frmclabens.php"));
?>
<?php
  db_menu();
?>
</div>
</body>
</html>
<?php
if(isset($incluir)){

  db_msgbox($erro_msg);
  db_redireciona("pat1_clabens001.php");
}
?>
