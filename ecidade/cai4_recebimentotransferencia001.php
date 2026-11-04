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
require_once(modification("model/configuracao/Instituicao.model.php"));
require_once(modification("model/CgmFactory.model.php"));
require_once(modification("model/contabilidade/EventoContabil.model.php"));
require_once(modification("model/contabilidade/EventoContabilLancamento.model.php"));
require_once(modification("model/contabilidade/RegraLancamentoContabil.model.php"));

$oGet = db_utils::postMemory($_GET);

//Checa parametro e mostra alerta de confirmacao de data
$clconparametro  = new cl_conparametro();
$rsconparametro  = $clconparametro->sql_record($clconparametro->sql_query_file(null, "c90_confirmadata"));
$conparametro    = db_utils::fieldsMemory($rsconparametro, 0);
if($conparametro->c90_confirmadata == 't'){
    $data = date('d/m/y', db_getsession('DB_datausu'));
    echo "<db-alertaconfirmadatafinanceiro data=" . $data . "></db-alertaconfirmadatafinanceiro>";
}
?>
<html>
<head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script type="text/javascript" src="scripts/components/AlertaConfirmaDataFinanceiro.js"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">
  <?php 
    db_app::load("scripts.js, strings.js, estilos.css, prototype.js, datagrid.widget.js, grid.style.css");
    db_app::load("classes/DBViewSlipRecebimento.classe.js, widgets/dbtextField.widget.js, widgets/dbcomboBox.widget.js");
  ?>
</head>
<body style="background-color: #cccccc; margin-top: 25px;">
  <center>
    <div id="ctnSlipRecebimento" align="center">
    </div>
  </center>
  <?php 
    db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
  ?>
</body>
</html>
<script>
    let c90_confirmadata = '<?php echo $conparametro->c90_confirmadata ?>'
    if (c90_confirmadata == 't') {
        const msg = "Antes de realizar a operação confirme a data em que deseja incluir o movimento";
        alert(msg)
    }

    var oDivDestino = $('ctnSlipRecebimento');
    var oDBViewSlipRecebimento = new DBViewSlipRecebimento("oDBViewSlipRecebimento", 3, oDivDestino,false);
    //oDBViewSlipRecebimento.show();
    oDBViewSlipRecebimento.start();


</script>
