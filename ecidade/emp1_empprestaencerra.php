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
require_once(modification("classes/db_emppresta_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);

$clemppresta = new cl_emppresta;
$oGet = db_utils::postMemory($_GET);
$oPost = db_utils::postMemory($_POST);

$db_opcao = 2;
$db_botao = true;
$erro_msg = "Alteração efetuada com sucesso.";
if (isset($atualizar) ) {

  db_inicio_transacao();

  $sqlerro = false;

  $oEmpenhoFinanceiro = EmpenhoFinanceiroRepository::getEmpenhoFinanceiroPorNumero($e60_numemp);
  $oPrestacaoContas = new PrestacaoConta($oEmpenhoFinanceiro, $oGet->e45_sequencial);
  if ( count($oPrestacaoContas->getItens()) == 0) {

    $sqlerro = true;
    $erro_msg = "Nenhum item lançado na prestação de contas.";
  } else {

    
    /**
     * como a prestacao é por OP devemos alterar todos movimentos da OP na emppresta.e45_acerta
     */
    $sqlMovimentos = "

WITH ordem AS

  (

select e82_codord as ordem 
  from emppresta 
  join empord on e82_codmov = e45_codmov 
  where e45_sequencial = {$oGet->e45_sequencial}

  ), movimentos as 

  (

     select e82_codmov ,
            e45_sequencial
       from  ordem 
       join empord on ordem = e82_codord
       join emppresta on e82_codmov = e45_codmov
  )

select * from movimentos;
    ";
    $rsMovimentos = db_query($sqlMovimentos);
    if (pg_num_rows($rsMovimentos) > 0 ) {

      for ($i=0; $i < pg_num_rows($rsMovimentos); $i++) {
        
              $movimento = db_utils::fieldsMemory($rsMovimentos, $i)->e82_codmov;
              $sequencial = db_utils::fieldsMemory($rsMovimentos, $i)->e45_sequencial;

              $altera = "update emppresta set e45_acerta = '{$oPost->e45_acerta}' where e45_sequencial = {$sequencial}";
              if (!db_query($altera )) {
                if($clemppresta->erro_status == 0){
                  $erro_msg = "Erro ao alterar a data da prestação";
                  $sqlerro=true;
                }
              }

      }
    }

  }
  db_fim_transacao($sqlerro);
}

if (isset($oGet->e45_sequencial)) {
  $result = $clemppresta->sql_record( $clemppresta->sql_query_file($oGet->e45_sequencial,"e45_acerta") );

  db_fieldsmemory($result,0);

  if ($e45_acerta == '' ) {
    $db_opcao = 1;
  } else {
    $db_opcao = 2;
  }
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
<body bgcolor=#CCCCCC leftmargin="0" style="margin-top: 30px" marginwidth="0" marginheight="0" onLoad="a=1" >
<center>
	<?php
	require_once(modification("forms/db_frmempprestaencerra.php"));
	?>
</center>
</body>
</html>
<?php
if(isset($atualizar)){
    db_msgbox($erro_msg);
    echo "<script> parent.location.href='emp1_emppresta002.php'</script>";
}
?>