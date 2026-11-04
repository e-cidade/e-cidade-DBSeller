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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_jsplibwebseller.php"));

function getData($dData) {

  $aTemp = explode("/", $dData);
  return $aTemp[2]."-".$aTemp[1]."-".$aTemp[0];

}

if (!isset($ed297_data_dia)) {
	
  $ed297_data_dia = date("d",db_getsession("DB_datausu"));
  $ed297_data_mes = date("m",db_getsession("DB_datausu"));
  $ed297_data_ano = date("Y",db_getsession("DB_datausu"));
 
}

db_postmemory($HTTP_POST_VARS);
$oDaoMatricula            = db_utils::getdao('matricula');
$oDaoTurma                = db_utils::getdao('turma');
$oDaoTurmaSerieRegimeMat  = db_utils::getdao('turmaserieregimemat');
$oDaoEduParametros        = db_utils::getdao('edu_parametros');
$oDaoParamDepend          = db_utils::getdao('parametrodependencia');
$oDaoMatriculaDependencia = db_utils::getdao('matriculadependencia');
$oDaoMatriculaDisciplina  = db_utils::getdao('matriculadisciplina');

$db_opcao                 = 1;
$db_value_botao           = 1;
$db_botao                 = false;
$iEscola                  = db_getsession("DB_coddepto");
$oPost                    = db_utils::postMemory($_POST);

$sWhereParametros         = " ed233_i_escola = $iEscola ";
$sSqlParametros           = $oDaoEduParametros->sql_query("", "*", "", $sWhereParametros);
$rsParametros             = $oDaoEduParametros->sql_record($sSqlParametros);

if ($oDaoEduParametros->numrows > 0) {
	
  db_fieldsmemory($rsParametros, 0);
  	
} else {
  
  echo "Erro! Parâmetros não informados";
  exit;
  	
}

$sWhereParamDepend = " ed295_escola = ".$iEscola;
$sSqlParamDepend   = $oDaoParamDepend->sql_query_file("", "*", "", $sWhereParamDepend);
$rsParamDepend     = $oDaoParamDepend->sql_record($sSqlParamDepend);
$iLinhasParamDep   = $oDaoParamDepend->numrows;

if ($iLinhasParamDep > 0) {

  $oDadosParamDepend = db_utils::fieldsmemory($rsParamDepend, 0);

} else {
?>
  <center>
    <br /><br />
    <fieldset width="50%"><legend><b>Erro encontrado</b></legend>
      <h4>Não foi possível encontrar os parâmetros de dependência da escola.</h4>
    </fieldset>
  </center>
<?php
}

if (isset($iMatricula) && isset($iTurma)) {
  
  $db_botao       = true;
  $db_opcao       = 2;
  $db_value_botao = 2;

  $sCampos        = "turma.*, calendario.*, base.*, cursoedu.*, turno.*, ";
  $sCampos       .= "fc_nomeetapaturma(ed57_i_codigo) as nometapa, fc_codetapaturma(ed57_i_codigo) as codetapa ";
  $sWhereTurma    = " ed57_i_codigo = ".$iTurma;
  $sSql           = $oDaoTurma->sql_query("", $sCampos, "", $sWhereTurma);
  $rsTurma        = $oDaoTurma->sql_record($sSql);
  db_fieldsmemory($rsTurma, 0);
  $ed60_i_turma   = $ed57_i_codigo;

  $ed60_i_turma   = $ed57_i_codigo;
  $sCamposMat     = " count(*) ";
  $sWhereMat      = " ed60_i_turma = ".$iTurma." AND ed60_c_situacao = 'MATRICULADO' ";
  $sSqlMatricula  = $oDaoMatricula->sql_query_file("", $sCamposMat, "", $sWhereMat);
  $rsMatricula    = $oDaoMatricula->sql_record($sSqlMatricula);
  db_fieldsmemory($rsMatricula, 0);
   
  $ed57_i_nummatr = $count;

  $sCamposMat     = " ed60_i_codigo,ed60_i_aluno,ed47_v_nome ";
  $sWhereMat      = " ed60_i_codigo = ".$iMatricula." limit 1 ";
  $sSqlMat        = $oDaoMatricula->sql_query("", $sCamposMat, "", $sWhereMat);
  $rsMatricula2   = $oDaoMatricula->sql_record($sSqlMat);
  db_fieldsmemory($rsMatricula2, 0);

}

if (isset($oPost->incluir)) {
  
  db_inicio_transacao();
  
  if (verificaMatriculados($oPost->ed60_i_turma)) {
  
    $oDaoMatriculaDependencia->ed297_matricula = $oPost->ed60_i_codigo;
    $oDaoMatriculaDependencia->ed297_turma     = $oPost->ed60_i_turma;
    $oDaoMatriculaDependencia->ed297_data      = getData($oPost->ed297_data);
    $oDaoMatriculaDependencia->incluir(null);
  
    if ($oDaoMatriculaDependencia->erro_status != 0) {

      for ($iCont = 0; $iCont < count($oPost->disciplina); $iCont++) {

        $oDaoMatriculaDisciplina  = db_utils::getdao('matriculadisciplina');
        $oDaoMatriculaDisciplina->ed298_matriculadependencia = $oDaoMatriculaDependencia->ed297_sequencial;
        $oDaoMatriculaDisciplina->ed298_disciplina           = $oPost->disciplina[$iCont];
        $oDaoMatriculaDisciplina->incluir(null);

      }

      /* Atualizo as vagas disponíveis na turma que o aluno está sendo matriculado */
      $oDaoTurma                 = db_utils::getdao('turma');
      $sSqlTurma                 = $oDaoTurma->sql_query_file($oPost->ed60_i_turma);
      $rsTurma                   = $oDaoTurma->sql_record($sSqlTurma);
      $oDaoTurma->ed57_i_nummatr = db_utils::fieldsmemory($rsTurma, 0)->ed57_i_nummatr + 1;
      $oDaoTurma->ed57_i_codigo  = $oPost->ed60_i_turma;
      $oDaoTurma->alterar($oPost->ed60_i_turma);

    }

  } else {
    db_msgbox('Turma não possui vagas para realizar mais matrículas.');
  }

  db_fim_transacao();
	
} elseif (isset($oPost->alterar)) {
  
  db_inicio_transacao(); 

  $sWhereMatDep = " ed297_matricula = ".$oPost->ed60_i_codigo;
  $sSqlMatDep   = $oDaoMatriculaDependencia->sql_query_file("", "*", "", $sWhereMatDep);
  $rsMatDep     = $oDaoMatriculaDependencia->sql_record($sSqlMatDep);
  $oDadosMatDep = db_utils::fieldsmemory($rsMatDep, 0);

  $oDaoMatriculaDependencia->ed297_sequencial = $oDadosMatDep->ed297_sequencial;
  $oDaoMatriculaDependencia->ed297_matricula  = $oPost->ed60_i_codigo;
  $oDaoMatriculaDependencia->ed297_turma      = $oPost->ed60_i_turma;
  $oDaoMatriculaDependencia->ed297_data       = getData($oPost->ed297_data);
  $oDaoMatriculaDependencia->alterar($oDadosMatDep->ed297_sequencial);
  
  if ($oDaoMatriculaDependencia->erro_status != 0) {
  
    /* Apago as disciplinas para adicionar novamente (fazendo assim consigo 
       editar caso alguma tenha sido retirada ou alterada */
    $sWhereExcluir = " ed298_matriculadependencia = ".$oDaoMatriculaDependencia->ed297_sequencial;
    $oDaoMatriculaDisciplina->excluir(null, $sWhereExcluir);

    if ($oDaoMatriculaDisciplina->erro_status != 0) {
      
      if (isset($oPost->disciplina)) {
        
        for ($iCont = 0; $iCont < count($oPost->disciplina); $iCont++) {

          $oDaoMatriculaDisciplina  = db_utils::getdao('matriculadisciplina');
          $oDaoMatriculaDisciplina->ed298_matriculadependencia = $oDaoMatriculaDependencia->ed297_sequencial;
          $oDaoMatriculaDisciplina->ed298_disciplina           = $oPost->disciplina[$iCont];
          $oDaoMatriculaDisciplina->incluir(null);

        }
      
      }

    }

  }

  db_fim_transacao();

} elseif (isset($chavepesquisa)) {
	
   $db_botao    = false;
   
   $sCampos     = "turma.*, calendario.*, base.*, cursoedu.*, turno.*, ";
   $sCampos    .= "fc_nomeetapaturma(ed57_i_codigo) as nometapa, fc_codetapaturma(ed57_i_codigo) as codetapa ";
   $sWhereTurma = " ed57_i_codigo = ".$chavepesquisa;
   $sSql        = $oDaoTurma->sql_query("", $sCampos, "", $sWhereTurma);
   $rsTurma     = $oDaoTurma->sql_record($sSql);
   db_fieldsmemory($rsTurma, 0);
   
   $ed60_i_turma   = $ed57_i_codigo;
   $ed57_i_nummatr = verificaMatriculados($ed60_i_turma, null, 2);

   if ($ed57_i_numvagas == $ed57_i_nummatr) {

     $db_opcao       = 3;
     $db_value_botao = 1;

   }
   
}
?>
<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/webseller.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
  </head>
  <body bgcolor="#CCCCCC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="430" align="left" valign="top" bgcolor="#CCCCCC">
          <?php
            MsgAviso(db_getsession("DB_coddepto"),"escola");
          ?>
          <br>
          <center>
            <br><br>
            <fieldset style="width:95%"><legend><b>Vinculo Turma/Aluno</b></legend>
              <?include(modification("forms/db_frmmatriculadependencia.php"));?>
            </fieldset>
          </center>
        </td>
      </tr>
    </table>
    <?php
      db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),
              db_getsession("DB_anousu"),db_getsession("DB_instit")
             );
    ?>
  </body>
</html>

<script>
  js_tabulacaoforms("form1","ed60_i_turma",true,1,"ed60_i_turma",true);
</script>

<?php

  if (isset($oPost->incluir) || isset($oPost->alterar)) {
  	
    if ($oDaoMatriculaDependencia->erro_status == "0") {
      
      $oDaoMatriculaDependencia->erro(true, false);
      $db_botao = true;
      
      echo "<script> $('db_opcao').disabled = false; </script>  ";
      
      if ($oDaoMatriculaDependencia->erro_campo != "") {
      	
        echo "<script> $('".$oDaoMatriculaDependencia->erro_campo."').style.backgroundColor = '#99A9AE'; </script>";
        echo "<script> $('".$oDaoMatriculaDependencia->erro_campo."').focus(); </script>";
        
      }
    
    } else {
      ?>
      <script>
        alert("Aluno matriculado com sucesso na turma.");
        location.href = "edu1_matriculadependencia001.php?chavepesquisa=<?=$oPost->ed60_i_turma?>";
      </script>
      <?php
    }
   
  }
?>