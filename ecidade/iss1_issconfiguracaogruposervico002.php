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
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("dbforms/db_classesgenericas.php"));

$oPost = db_utils::postMemory($_POST);
$oGet  = db_utils::postMemory($_GET);

$oDaoConfiguracaoGrupo = new cl_issconfiguracaogruposervico();
$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;

$clissgruposervico = new cl_issgruposervico;

$clissgscadanexos = new cl_issgscadanexos;
$clissgscadanexos->rotulo->label();
$clissgscadanexos->rotulo->tlabel();

$clissgsanexos = new cl_issgsanexos;
$clissgsanexos->rotulo->label();
$clissgsanexos->rotulo->tlabel();

$db_opcao = 22;
$db_botao = false;

if ( isset($oPost->salvar) ) {
  	$db_opcao = 2;
	db_inicio_transacao();

    if (!$oPost->q136_retencao){
        $oPost->q136_retencao = 'f';
    }

    if (!$oPost->q136_deducao){
        $oPost->q136_deducao = 'f';
    }

    if (!$oPost->q136_salaoparceiro){
        $oPost->q136_salaoparceiro = 'f';
    }

	$oDaoConfiguracaoGrupo->q136_sequencial     = $oPost->q136_sequencial;
	$oDaoConfiguracaoGrupo->issgruposervico     = $oPost->q136_issgruposervico;
	$oDaoConfiguracaoGrupo->q136_exercicio      = $oPost->q136_exercicio;
	$oDaoConfiguracaoGrupo->q136_tipotributacao = $oPost->q136_tipotributacao;
	$oDaoConfiguracaoGrupo->q136_valor          = $oPost->q136_valor;
	$oDaoConfiguracaoGrupo->q136_deducao        = $oPost->q136_deducao;
	$oDaoConfiguracaoGrupo->q136_retencao       = $oPost->q136_retencao;
    $oDaoConfiguracaoGrupo->q136_salaoparceiro     = $oPost->q136_salaoparceiro;
	if ( !empty($oPost->q136_sequencial) ) {
		$oDaoConfiguracaoGrupo->alterar($q136_sequencial);
	} else {
		$oDaoConfiguracaoGrupo->incluir(null);
	}

	if (!empty($selectanexo)) {
        // // // // // // // // // // // // //
        // Inclusão e Alteração de Vínculos //
        // // // // // // // // // // // // //
        $clissgsanexos->q162_issgruposervico = $q136_issgruposervico;
        $clissgsanexos->q162_issgscadanexos  = $selectanexo;

        // Se $q162_data_fim vier como vazio, atribui vinculo ativo (data vazia) para issgsanexos
        if (empty($q162_data_fim)) {
            $clissgsanexos->q162_data_fim = 'null';
        } else if (!empty($q162_data_fim)) {
            $clissgsanexos->q162_data_fim = date("Y-m-d", strtotime(str_replace('/', '-', $q162_data_fim)));
        }

        // Se sequencial for vazio, inclusão de vinculos
        if (empty($q162_sequencial)) {
            // Se data for vazia (inclusão de vínculo ativo)
            if (empty($q162_data_fim)) {
                $anousu = db_getsession('DB_anousu');
                $sql = "SELECT * FROM issqn.issgsanexos
				INNER JOIN issqn.issgruposervico ON q162_issgruposervico = q126_sequencial
				INNER JOIN issqn.issconfiguracaogruposervico ON q162_issgruposervico = q136_issgruposervico
				WHERE q136_sequencial = $q136_sequencial AND q136_exercicio = $anousu AND q162_data_fim IS NULL
			";
                $result = db_query($sql);
                db_fieldsmemory($result,0);

                // Se existir vínculo com data 'null'(ativo) altera o último registro com data 'null' para receber a data atual(hoje)
                if (pg_numrows($result) > 0) {
                    $dataAtual = db_getsession('DB_datausu');
                    $dataAtualFormatada = str_replace('/', '-', date('Y/m/d',$dataAtual));
                    $clissgsanexos->q162_data_fim = $dataAtualFormatada;

                    // Altera vínculo ativo(com data 'null') existente
                    $clissgsanexos->q162_issgscadanexos = $q162_issgscadanexos;
                    $clissgsanexos->alterar($q162_sequencial);

                    // Depois de alterar atribui 'null' novamente na data para inclusão de um vínculo ativo(com data 'null')
                    $clissgsanexos->q162_sequencial = '';
                    $clissgsanexos->q162_issgscadanexos = $selectanexo;
                    $clissgsanexos->q162_data_fim = 'null';
                    $clissgsanexos->incluir(null);
                    // Se não existir vínculo com data 'null'(ativo), insere novo vínculo ativo
                } else {
                    $clissgsanexos->incluir(null);
                }
                // Se data não for vazia (inclusão de vínculo não ativo)
            } else {
                $sql = "SELECT * FROM issqn.issgsanexos WHERE q162_data_fim = '$clissgsanexos->q162_data_fim'";
                $result = db_query($sql);

                if (pg_numrows($result) > 0 ) {
                    db_msgbox("Vínculo já existente");
                } else {
                    $clissgsanexos->incluir(null);
                }
            }

            // Se sequencial não for vazio, alteração de vinculos
        } else if (!empty($q162_sequencial)) {
            // Se a data for vazio (vinculo ativo)
            if (empty($q162_data_fim)) {
                $sql = "SELECT * FROM issqn.issgsanexos WHERE q162_sequencial = $q162_sequencial AND q162_data_fim IS NULL";
            } else {
                $sql = "SELECT * FROM issqn.issgsanexos WHERE q162_sequencial = $q162_sequencial AND q162_data_fim = '$clissgsanexos->q162_data_fim'";
            }

            $result = db_query($sql);

            if (pg_numrows($result) >0 ) {
                db_msgbox("Vínculo já existente");
            } else {
                $clissgsanexos->alterar($q162_sequencial);
            }
        }
    }

  	db_fim_transacao();

} elseif ( isset($oGet->iCodigoGrupoServico) ) {

	$db_opcao = 2;

	$sCampos  = "db_estruturavalor.db121_estrutural as codigo_grupo,";
	$sCampos .= "db_estruturavalor.db121_descricao  as descricao_grupo,";
	$sCampos .= "issconfiguracaogruposervico.*";
	$sWhere   = "issgruposervico.q126_sequencial = {$oGet->iCodigoGrupoServico} and q136_exercicio = " . db_getsession('DB_anousu');

	$sSqlConfiguracaoGrupo = $oDaoConfiguracaoGrupo->sql_query_grupoServico($sCampos, $sWhere);
	$rsConfiguracaoGrupo   = $oDaoConfiguracaoGrupo->sql_record($sSqlConfiguracaoGrupo);

	if ( $oDaoConfiguracaoGrupo->numrows > 0 ) {

		$oConfiguracaoGrupo = db_utils::fieldsMemory($rsConfiguracaoGrupo, 0);

		$iCodigoGrupoServico    = $oConfiguracaoGrupo->codigo_grupo;
		$sDescricaoGrupoServico = $oConfiguracaoGrupo->descricao_grupo;

		$q136_issgruposervico = $oGet->iCodigoGrupoServico;
		$q136_sequencial      = $oConfiguracaoGrupo->q136_sequencial;
		$q136_exercicio       = $oConfiguracaoGrupo->q136_exercicio;
		$q136_tipotributacao  = $oConfiguracaoGrupo->q136_tipotributacao;
		$q136_valor           = $oConfiguracaoGrupo->q136_valor;
		$q136_localpagamento  = $oConfiguracaoGrupo->q136_localpagamento;
		$q136_deducao         = $oConfiguracaoGrupo->q136_deducao;
		$q136_retencao        = $oConfiguracaoGrupo->q136_retencao;
        $q136_salaoparceiro    = $oConfiguracaoGrupo->q136_salaoparceiro;

		$db_botao = true;
	}

	if ( empty($q136_exercicio) ) {
		$q136_exercicio = db_getsession('DB_anousu');
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
<script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
<?php db_app::load("estilos.css, grid.style.css, scripts.js, strings.js, prototype.js"); ?>
<style type="text/css">
	td {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	}
	input {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	height: 17px;
	border: 1px solid #999999;
	}
	.cores:nth-child(even) {
		background: #FFF;
	}
	.cores:nth-child(odd) {
		background: #efefef;
	}
	table.form-container tr td {
		font-weight: normal !important;
	}
</style>
</head>
<body class="body-default">
  <div class="container">
	 <?php include(modification("forms/db_frmissconfiguracaogruposervico.php")); ?>
  </div>
<?php
db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));

if ( isset($oPost->salvar) ) {

  if ($oDaoConfiguracaoGrupo->erro_status == "0") {

    $oDaoConfiguracaoGrupo->erro(true, false);
    $db_botao = true;

    echo "<script>document.form1.db_opcao.disabled=false;</script>  ";

    if ($oDaoConfiguracaoGrupo->erro_campo != "") {

      echo "<script> document.form1.".$oDaoConfiguracaoGrupo->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$oDaoConfiguracaoGrupo->erro_campo.".focus();</script>";
    }

  } else{
    $oDaoConfiguracaoGrupo->erro(true, true);
  }
}

if ($db_opcao == 22) {
  echo "<script>document.form1.pesquisar.click();</script>";
}

?>

</body>
<html>
