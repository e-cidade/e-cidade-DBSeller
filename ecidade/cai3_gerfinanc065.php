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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_cancdebitos_classe.php"));
require_once(modification("classes/db_cancdebitosreg_classe.php"));
require_once(modification("classes/db_cancdebitosprot_classe.php"));
require_once(modification("classes/db_cgm_classe.php"));
require_once(modification("classes/db_arrecad_classe.php"));
require_once(modification("classes/db_cancdebitosconcarpeculiar_classe.php"));
require_once(modification("classes/db_numpref_classe.php"));

parse_str($_SERVER['QUERY_STRING']);
db_postmemory($_POST);

$oPost = db_utils::postMemory($_POST);
$oGet  = db_utils::postMemory($_GET);

$clcgm                       = new cl_cgm;
$clcancdebitos               = new cl_cancdebitos;
$clcancdebitosreg            = new cl_cancdebitosreg;
$clcancdebitosprot           = new cl_cancdebitosprot;
$clarrecad                   = new cl_arrecad;
$clcancdebitosconcarpeculiar = new cl_cancdebitosconcarpeculiar;
$clnumpref                   = new cl_numpref;
$clcancdebitos->rotulo->label("k20_descr");
$clrotulo = new rotulocampo;
$clrotulo->label("k25_codproc");
$clrotulo->label("p58_requer");
$clrotulo->label("k21_obs");
$clrotulo->label("c58_sequencial");
$clrotulo->label("p58_numero");

$db_opcao = 1;

if (isset($oPost->H_DATAUSU)) {
  $sDataVenc = date("Y-m-d",$oPost->H_DATAUSU);
}

if ((isset ($ver_matric) or isset ($ver_inscr) or isset ($ver_numcgm)) && !isset ($k20_codigo)) {

  if (!isset($inicial)) {

    $vt = $_POST;

    $virgula = "";
    $receit1 = "";
    $numpar1 = "";
    $numpre1 = "";
    for ($i = 0; $i < count($vt); $i ++) {

      if (db_indexOf(key($vt), "CHECK") > 0) {

        $numpres = $vt[key($vt)];
        $mat = explode("N", $numpres);
        if (isset($oPost->marcarvencidas) && isset($oPost->marcartodas)) {

        	if ($oPost->marcarvencidas == 'true' && $oPost->marcartodas == 'false') {

			      for ($iInd = 0; $iInd < count($mat); $iInd++) {

			        if ($mat[$iInd] == "") {
			          continue;
			        }

	            $numpre = explode("P", $mat[$iInd]);
	            $numpar = explode("P", strstr($mat[$iInd], "P"));
	            $numpar = explode("R",$numpar[1]);
	            $receit = @$numpar[1];
	            $numpar = $numpar[0];
	            $numpre = $numpre[0];

			        $sSqlArrecad  = "  select *                               ";
			        $sSqlArrecad .= "    from arrecad                         ";
			        $sSqlArrecad .= "   where k00_numpre   = {$numpre}        ";
			        $sSqlArrecad .= "     and k00_numpar   = {$numpar}        ";
			        $sSqlArrecad .= "     and k00_dtvenc   > '{$sDataVenc}'   ";

			        $rsSqlArrecad = db_query($sSqlArrecad);
			        $iNumRows     = pg_num_rows($rsSqlArrecad);
			        if ($iNumRows == 0) {

                $receit1 .= $virgula.$receit;
                $numpar1 .= $virgula.$numpar;
                $numpre1 .= $virgula.$numpre;
                $virgula = ",";
			        }

			      }
        	} else {

	        	for ($j = 0; $j < count($mat); $j ++) {

	            if ($mat[$j] == "") continue;

	            $numpre = explode("P", $mat[$j]);
	            $numpar = explode("P", strstr($mat[$j], "P"));
	            $numpar = explode("R",$numpar[1]);
	            $receit = @$numpar[1];
	            $numpar = $numpar[0];
	            $numpre = $numpre[0];
	            //db_msgbox("rec=".$receit." numpre = ".$numpre."P=".$numpar);
	            $receit1 .= $virgula.$receit;
	            $numpar1 .= $virgula.$numpar;
	            $numpre1 .= $virgula.$numpre;
	            $virgula = ",";

	          }
        	}
        } else {

	        for ($j = 0; $j < count($mat); $j ++) {

	          if ($mat[$j] == "") continue;

	          $numpre = explode("P", $mat[$j]);
	          $numpar = explode("P", strstr($mat[$j], "P"));
	          $numpar = explode("R",$numpar[1]);
	          $receit = @$numpar[1];
	          $numpar = $numpar[0];
	          $numpre = $numpre[0];
	          //db_msgbox("rec=".$receit." numpre = ".$numpre."P=".$numpar);
	          $receit1 .= $virgula.$receit;
	          $numpar1 .= $virgula.$numpar;
	          $numpre1 .= $virgula.$numpre;
	          $virgula = ",";

	        }
        }

      }
      next($vt);
    }

    $sqlconfplan    = "select * from db_confplan";
    $resultconfplan = db_query($sqlconfplan);
    $linhasconfplan = pg_num_rows($resultconfplan);
    if($linhasconfplan== 0 ){
      db_msgbox("Falta parâmetros na configuração de planilhas.");
      echo "<script>parent.document.formatu.pesquisar.click();</script>";
    }
    db_fieldsmemory($resultconfplan,0);

   $sqlverifica = "
    select * from issvar
    inner join arrecad on k00_numpre = q05_numpre and k00_numpar = q05_numpar
    inner join arretipo on arrecad.k00_tipo = arretipo.k00_tipo
    where     q05_numpre = $numpre
          and q05_numpar = $numpar
          and q05_valor = 0
          and k03_tipo   = 3 ";


    $resultverifica = db_query($sqlverifica);
    $linhasverifica = pg_num_rows($resultverifica);
    if($linhasverifica > 0){
      db_msgbox(" Não é possivel efetuar cancelamento. \\n Deve utilizar a rotina Cancelamento de Issqn Variável.");
      echo "<script>parent.document.formatu.pesquisar.click();</script>";
    }

  } else {

    $vt = $_POST;
    $virgula = "";
    $numpar1 = "";
    $numpre1 = "";
    for ($i = 0; $i < count($vt); $i ++) {

      if (db_indexOf(key($vt), "CHECK") > 0) {

        $v50_inicial = $vt[key($vt)];

        if (isset($oPost->marcarvencidas) && isset($oPost->marcartodas)) {

          if ($oPost->marcarvencidas == 'true' && $oPost->marcartodas == 'false') {

          	$mat = explode("N", $v50_inicial);
            for ($iInd = 0; $iInd < count($mat); $iInd++) {

              if ($mat[$iInd] == "") {
                continue;
              }

              $numpre = explode("P", $mat[$iInd]);
              $numpar = explode("P", strstr($mat[$iInd], "P"));
              $numpar = explode("R",$numpar[1]);
              $receit = @$numpar[1];
              $numpar = $numpar[0];
              $numpre = $numpre[0];

              $sSqlArrecad  = "  select *                               ";
              $sSqlArrecad .= "    from arrecad                         ";
              $sSqlArrecad .= "   where k00_numpre   = {$numpre}        ";
              $sSqlArrecad .= "     and k00_numpar   = {$numpar}        ";
              $sSqlArrecad .= "     and k00_dtvenc   > '{$sDataVenc}'   ";

              $rsSqlArrecad = db_query($sSqlArrecad);
              $iNumRows     = pg_num_rows($rsSqlArrecad);
              if ($iNumRows == 0) {

                $numpar1 .= $virgula.$numpar;
                $numpre1 .= $virgula.$numpre;
                $virgula = ",";
              }

            }
          } else {

	          $sqlinicial = "select distinct v59_numpre from inicialnumpre where v59_inicial = $v50_inicial";
	          $resultinicial = db_query($sqlinicial) or die($sqlinicial);

	          for ($x=0; $x < pg_numrows($resultinicial); $x++) {
	            db_fieldsmemory($resultinicial,$x);

	            $sqlarrecad = "select distinct k00_numpar from arrecad where k00_numpre = $v59_numpre";
	            $resultarrecad = db_query($sqlarrecad) or die($sqlarrecad);

	            for ($y=0; $y < pg_numrows($resultarrecad ); $y++) {
	              db_fieldsmemory($resultarrecad,$y);

	              $numpar1 .= $virgula.$k00_numpar;
	              $numpre1 .= $virgula.$v59_numpre;
	              $virgula = ",";

	            }

	          }
          }
        } else {

	        $sqlinicial = "select distinct v59_numpre from inicialnumpre where v59_inicial = $v50_inicial";
	        $resultinicial = db_query($sqlinicial) or die($sqlinicial);

	        for ($x=0; $x < pg_numrows($resultinicial); $x++) {
	          db_fieldsmemory($resultinicial,$x);

	          $sqlarrecad = "select distinct k00_numpar from arrecad where k00_numpre = $v59_numpre";
	          $resultarrecad = db_query($sqlarrecad) or die($sqlarrecad);

	          for ($y=0; $y < pg_numrows($resultarrecad ); $y++) {
	            db_fieldsmemory($resultarrecad,$y);

	            $numpar1 .= $virgula.$k00_numpar;
	            $numpre1 .= $virgula.$v59_numpre;
	            $virgula = ",";

	          }

	        }
        }
      }
      next($vt);
    }

  }

}

//Novo Grupo

if (isset ($envia) && (@$k20_codigo == 0 || @$k20_codigo == "")) {

  $sqlerro = false;
  db_inicio_transacao();
  if ($k25_codproc != "" && $k20_descr == "") {
    $clcancdebitos->k20_descr = "PROCESSO ".$k25_codproc;
  } else if ($k25_codproc == "" && $k20_descr == "") {
    $clcancdebitos->k20_descr = @ $tipo_filtro." ".@ $cod_filtro;
  } else {
    $clcancdebitos->k20_descr = "$k20_descr";
  }
  $clcancdebitos->k20_cancdebitostipo = $tipoDebito;
  $clcancdebitos->k20_hora = db_hora();
  $clcancdebitos->k20_data = date("Y-m-d", db_getsession("DB_datausu"));
  $clcancdebitos->k20_usuario = db_getsession("DB_id_usuario");
  $clcancdebitos->k20_instit  = db_getsession("DB_instit");
  $clcancdebitos->k20_depart  = db_getsession("DB_coddepto");
  $clcancdebitos->incluir(null);
  if ($clcancdebitos->erro_status == 0) {
    $sqlerro = true;
  }
  $erro_msg = $clcancdebitos->erro_msg;

  if ($sqlerro == false) {
  	if($tipoDebito == 2 ){
  		 if($c58_sequencial!=""){
  		 	 // renuncia -- grava a caracteristica peculiar
		     $clcancdebitosconcarpeculiar->k72_cancdebitos    = $clcancdebitos->k20_codigo;
	    	 $clcancdebitosconcarpeculiar->k72_concarpeculiar = $c58_sequencial;
		     $clcancdebitosconcarpeculiar->incluir(null);
		     if ($clcancdebitosconcarpeculiar->erro_status == 0) {
           $sqlerro = true;
           $erro_msg = $clcancdebitosconcarpeculiar->erro_msg;
         }
  		 }else{
  		 	 $sqlerro = true;
         $erro_msg = "O campo Caracteristica Peculiar não foi informado.";
  		 }

  	}

  }

  if ($sqlerro == false) {
    if ($k25_codproc != "") {
      $clcancdebitosprot->k25_codproc = $k25_codproc;
      $clcancdebitosprot->k25_cancdebitos = $clcancdebitos->k20_codigo;
      $clcancdebitosprot->incluir($clcancdebitos->k20_codigo);
      if ($clcancdebitosprot->erro_status == 0) {
        $sqlerro = true;
        $erro_msg = $clcancdebitosprot->erro_msg;
      }
    }
  }

  if ($sqlerro == false) {
    $mat = explode(",", $numpre);
    $mat1 = explode(",", $numpar);
    $mat2 = explode(",", $receit);
    for ($i = 0; $i < count($mat); $i ++) {
      $numpre = $mat[$i];
      $numpar = $mat1[$i];
      $receit = $mat2[$i];
      //db_msgbox("numpre = $numpre numpar= $numpar rec = $receit");
      // SE RECEITA FOR IGUAL A ZERO
      if ($receit==0) {
        $sqlrec = "select distinct k00_receit from arrecad where k00_numpre =$numpre and k00_numpar = $numpar";
        $resultrec= db_query($sqlrec);
        $linhasrec = pg_num_rows($resultrec);
        if ($resultrec>0) {
          for ($r=0; $r < $linhasrec; $r++) {
            db_fieldsmemory($resultrec,$r);
            if ($sqlerro == false) {
             //db_msgbox("= 0 incluiu....rec=".$k00_receit." numpre = ".$numpre."P=".$numpar);
              $clcancdebitosreg->k21_receit = $k00_receit;
              $clcancdebitosreg->k21_hora = db_hora();
              $clcancdebitosreg->k21_data = date("Y-m-d", db_getsession("DB_datausu"));
              $clcancdebitosreg->k21_obs = "$k21_obs";
              $clcancdebitosreg->k21_codigo = $clcancdebitos->k20_codigo;
              $clcancdebitosreg->k21_numpre = $numpre;
              $clcancdebitosreg->k21_numpar = $numpar;
              $clcancdebitosreg->incluir("");
              if ($clcancdebitosreg->erro_status == 0) {
                $sqlerro = true;
                $erro_msg = $clcancdebitosreg->erro_msg;
                break;
              }
            }
          }// do for $r
        }
      } else {
        // SE RECEITA FOR  <> DE ZERO
        if ($sqlerro == false) {
          //db_msgbox("<> 0 rec=".$receit." numpre = ".$numpre."P=".$numpar);
          $clcancdebitosreg->k21_receit = $receit;
          $clcancdebitosreg->k21_hora = db_hora();
          $clcancdebitosreg->k21_data = date("Y-m-d", db_getsession("DB_datausu"));
          $clcancdebitosreg->k21_obs = "$k21_obs";
          $clcancdebitosreg->k21_codigo = $clcancdebitos->k20_codigo;
          $clcancdebitosreg->k21_numpre = $numpre;
          $clcancdebitosreg->k21_numpar = $numpar;
          $clcancdebitosreg->incluir("");
          if ($clcancdebitosreg->erro_status == 0) {
            $sqlerro = true;
            $erro_msg = $clcancdebitosreg->erro_msg;
            break;
          }
        }
      }
    }
  //$sqlerro = true;
  }

  db_fim_transacao($sqlerro);
  db_msgbox($erro_msg);
  if ($sqlerro == false) {
    echo "<script> parent.document.formatu.pesquisar.click();</script>";
  }
  //Grupo selecionado
} else if (isset ($envia) && $k20_codigo != 0) {
	$sqlerro = false;

	$sqlTipoDeb = "select k20_codigo,k20_cancdebitostipo,k72_concarpeculiar,c58_descr
	                 from cancdebitos
					 left join cancdebitosconcarpeculiar on k72_cancdebitos = k20_codigo
					 left join concarpeculiar            on c58_sequencial  = k72_concarpeculiar
					 where k20_codigo = $k20_codigo";
    $rsTipoDeb = db_query($sqlTipoDeb);
	$linhasDeb = pg_num_rows($rsTipoDeb);
    if($rsTipoDeb>0){
    	db_fieldsmemory($rsTipoDeb,0);

    }


  if(!isset($tipoDebito)){
  	$tipoDebito = $tipoDebitoAux;
  }
	/*
	echo "
	<br> dados da tela
	<br>codigo = $k20_codigo
	<br>tipo   = $tipoDebito
	<br>peculiar = $c58_sequencial
	<br><br> dados do banco
	<br>codigo = $k20_codigo
	<br>tipo   = $k20_cancdebitostipo
	<br>peculiar = $k72_concarpeculiar
	";
	*/
	if($tipoDebito != $k20_cancdebitostipo){
		$erro_msg = "Grupo debitos selecionado com tipo de cancelamento diferente.";
		$sqlerro = true;

	}elseif($c58_sequencial != $k72_concarpeculiar){

        if($k72_concarpeculiar!=""){
        	$erro_msg ="Grupo debitos selecionado com caracteristica peculiar diferente. Este grupo possui caracteristica peculiar:$k72_concarpeculiar - $c58_descr";
        }else{
        	$erro_msg ="Grupo debitos selecionado com caracteristica peculiar diferente.";
        }


		$sqlerro = true;

	}

  if($sqlerro==false){
  db_inicio_transacao();
  if ($k25_codproc != "" && $k20_descr == "") {
    $clcancdebitos->k20_descr = "PROCESSO ".$k25_codproc;
  } else if ($k25_codproc == "" && $k20_descr == "") {
    $clcancdebitos->k20_descr = @ $tipo_filtro." ".@ $cod_filtro;
  } else {
    $clcancdebitos->k20_descr = $k20_descr;
  }
  $clcancdebitos->alterar($k20_codigo);
  if ($clcancdebitos->erro_status == 0) {
    $sqlerro = true;
  }
  $result_prot=$clcancdebitosprot->sql_record($clcancdebitosprot->sql_query_file($k20_codigo));
  if ($k25_codproc!=""){
    if ($clcancdebitosprot->numrows>0){
      $clcancdebitosprot->k25_cancdebitos = $k20_codigo;
      $clcancdebitosprot->k25_codproc = $k25_codproc;
      $clcancdebitosprot->alterar($k20_codigo);
      if ($clcancdebitosprot->erro_status == 0) {
        $sqlerro = true;
      }
    }else{
      $clcancdebitosprot->incluir($k20_codigo);
      if ($clcancdebitosprot->erro_status == 0) {
        $sqlerro = true;
      }
    }
  }else{
    if ($clcancdebitosprot->numrows>0){
      $clcancdebitosprot->excluir($k20_codigo);
      if ($clcancdebitosprot->erro_status == 0) {
        $sqlerro = true;
      }
    }
  }
  //die ($clcancdebitosreg->sql_query_file(null,"*",null,"k21_codigo=$k20_codigo"));
  $result_reg=$clcancdebitosreg->sql_record($clcancdebitosreg->sql_query_file(null,"*",null,"k21_codigo=$k20_codigo"));
  $numrows_reg=$clcancdebitosreg->numrows;
  for($w=0;$w<$numrows_reg;$w++){
    db_fieldsmemory($result_reg,$w);
    $clcancdebitosreg->k21_codigo = $k20_codigo;
    $clcancdebitosreg->k21_obs = "$k21_obs";
    $clcancdebitosreg->k21_sequencia = $k21_sequencia;
    $clcancdebitosreg->alterar($k21_sequencia);
    if ($clcancdebitosreg->erro_status == 0) {
      $sqlerro = true;
    }
  }

  $mat = explode(",", $numpre);
  $mat1 = explode(",", $numpar);
  $mat2 = explode(",", $receit);

  for ($i = 0; $i < count($mat); $i ++) {
    $numpre = $mat[$i];
    $numpar = $mat1[$i];
    $receit = $mat2[$i];


    // SE RECEITA FOR IGUAL A ZERO
    if($receit==0){
      $sqlrec = "select k00_receit from arrecad where k00_numpre =$numpre and k00_numpar = $numpar";

      $resultrec= db_query($sqlrec);
      $linhasrec = pg_num_rows($resultrec);
      if($resultrec>0){
        for ($r=0; $r < $linhasrec; $r++) {
          db_fieldsmemory($resultrec,$r);
          if ($sqlerro == false) {
            //db_msgbox("rec=".$k00_receit." numpre = ".$numpre."P=".$numpar);
            $clcancdebitosreg->k21_receit = $k00_receit;
            $clcancdebitosreg->k21_hora = db_hora();
            $clcancdebitosreg->k21_data = date("Y-m-d", db_getsession("DB_datausu"));
            $clcancdebitosreg->k21_obs = "$k21_obs";
            $clcancdebitosreg->k21_codigo = $clcancdebitos->k20_codigo;
            $clcancdebitosreg->k21_numpre = $numpre;
            $clcancdebitosreg->k21_numpar = $numpar;
            $clcancdebitosreg->incluir("");
            if ($clcancdebitosreg->erro_status == 0) {
              $sqlerro = true;
              $erro_msg = $clcancdebitosreg->erro_msg;
              break;
            }
          }
        }// do for $r
      }
    } else {

      // SE RECEITA FOR  <> DE ZERO
      $sWhere      = "     k21_codigo = {$k20_codigo} and k21_numpre = {$numpre} and k21_numpar = {$numpar} ";
      $sWhere     .= " and k21_receit = {$receit}";
      $result_reg  = $clcancdebitosreg->sql_record($clcancdebitosreg->sql_query_file(null, "*", null, $sWhere));
	    if ($clcancdebitosreg->numrows == 0) {

	      $clcancdebitosreg->k21_codigo = $k20_codigo;
	      $clcancdebitosreg->k21_obs    = $k21_obs;
	      $clcancdebitosreg->k21_receit = $receit;
	      $clcancdebitosreg->k21_hora   = db_hora();
	      $clcancdebitosreg->k21_data   = date("Y-m-d", db_getsession("DB_datausu"));
	      $clcancdebitosreg->k21_numpre = $numpre;
	      $clcancdebitosreg->k21_numpar = $numpar;
	      $clcancdebitosreg->incluir(null);
	      if ($clcancdebitosreg->erro_status == 0) {
	        $sqlerro = true;
	        break;
	      }
	    }
    }
  }
  $erro_msg = $clcancdebitosreg->erro_msg;
  db_fim_transacao($sqlerro);
  }

  db_msgbox($erro_msg);
  if ($clcancdebitos->erro_status != 0) {
    echo "<script>";
    echo "if(confirm('Deseja emitir os registros deste Grupo de Débitos?')){";
    echo " jan = window.open('cai2_cancdebitospendentes002.php?k20_codigo=$k20_codigo','','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');";
    echo " jan.moveTo(0,0);";
    echo "}";
    echo " parent.document.formatu.pesquisar.click();";
    echo "</script>";
  }
} else if (isset ($k20_codigo) && $k20_codigo != 0) {
  $query = $clcancdebitosreg->sql_record($clcancdebitosreg->sql_query("", "k25_codproc,p58_requer,k20_descr,k21_obs", "", "k20_codigo = $k20_codigo"));
  db_fieldsmemory($query, 0);
} else	if (!isset ($k20_codigo)) {
  $query = $clcancdebitos->sql_record($clcancdebitos->sql_pendentes("distinct k20_codigo, k20_descr, k21_obs", "k20_codigo desc", "k20_usuario = ".db_getsession("DB_id_usuario")));
  if ($clcancdebitos->numrows == 1) {
    db_fieldsmemory($query, 0);
    db_redireciona("cai3_gerfinanc065.php?k20_codigo=$k20_codigo&numpre1=$numpre1&numpar1=$numpar1&receit1=$receit1&k03_tipo=$k03_tipo&ver_matric=$ver_matric");
  }
}

$rs = $clnumpref->sql_record($clnumpref->sql_query(db_getsession("DB_anousu"), db_getsession("DB_instit"), 'k03_apenastiporenuncia,k03_processoparcelamento'));
$parametrosTributario = \db_utils::fieldsMemory($rs, 0);
?>
<html>
<head>
<title>Documento sem t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"
	onLoad="parent.document.getElementById('processando').style.visibility = 'hidden'; if(document.form1.tipoDebito.value == 2){ document.getElementById('renuncia').style.display = '' };">
<form name="form1" method="POST"><?php
echo "<input type='hidden' name='tipo_filtro' value=''>\n";
echo "<input type='hidden' name='cod_filtro' value=''>\n";
echo "<input type='hidden' name='numpre' value='".@$numpre1."'>\n";
echo "<input type='hidden' name='numpar' value='".@$numpar1."'>\n";
echo "<input type='hidden' name='receit' value='".@$receit1."'>\n";
echo "<input type='hidden' name='k03_tipo' value='".@$k03_tipo."'>\n";
?>
<center>
<table border="2" width="100%">
	<input type="hidden" name="matric" value="<?=@$ver_matric?>">
	<tr>
		<td align="center" colspan="2" style='border: 1px outset #cccccc'><b>Cancelamento
		de débito</b></td>
	</tr>
	<tr>
		<td valign="top">
		<table align=center >
      <tr id="linhaProcessoSistemaInterno">
          <td colspan="2">
              <div>
                  <fieldset style='width:fit-content;' >
                      <legend>
                        <strong>
                          Dados do Processo (Preenchimento opcional)
                        </strong>
                      </legend>
                      <table align="center">
                          <tr>
                              <td title="<?= @$Tp58_numero ?>">
                                  <?php
                                  db_ancora($Lp58_numero, 'js_pesquisaProcesso(true)', 1)
                                  ?>
                              </td>
                              <td>
                                  <?php
                                  db_input('p58_numero', 10, '', true, 'text', $db_opcao, "onchange='js_pesquisaProcesso(false)'", '', '', '', 20);
                                  ?>
                              </td>
                              <td>
                                <?php
                                db_input('z01_nome', 40, isset($Iz01_nome) ? $Iz01_nome : '', true, 'text', 3, "", 'z01_nomeprocesso');
                                ?>
                              </td>
                          </tr>
                          <tr>
                              <td title="Ano">Ano</td>
                              <td>
                                  <input type="number" id="anoSessao" name="anoSessao" value="<?=$anoSessao?>" style="width: 82px;" onchange='js_pesquisaProcesso(false)'>
                              </td>
                          </tr>
                          <input type="hidden" id='numeroProcessoConcatenado' name='numeroProcessoConcatenado' value=''>
                          <input type="hidden" id='p58_codproc' name='k25_codproc' value=''>
                      </table>
                  </fieldset>
              </div>
          </td>
      </tr>
			<tr>
				<td colspan="2" ><b>Tipo de cancelamento:</b>
				<?php
        $apenasTipoRenuncia = $parametrosTributario->k03_apenastiporenuncia;

				if($apenasTipoRenuncia == 'f'){
          $resulttipo = db_query("select k73_sequencial,k73_descricao from cancdebitostipo order by k73_sequencial");
          $linhasTipo = pg_num_rows($resulttipo);
          if($linhasTipo > 0 ){
				    for($t=0;$t<$linhasTipo;$t++){
				    	db_fieldsmemory($resulttipo, $t);
						  $tipo[$k73_sequencial] = $k73_descricao;
				    }
          }
				} else {
				  $tipo = array(2 =>"Renuncia");
        }

				db_select("tipoDebito",$tipo,true,1,"onChange='js_mostraRenuncia(document.form1.tipoDebito.value);'","tipoDebito");
				if(isset($tipoDebito)){
					$tipoDebitoAux = $tipoDebito;
				}

				db_input("tipoDebitoAux",4,0,true,"hidden",3);
				?>
				</td>
			</tr>
			<tr id="renuncia" style="display:none" >
				<td colspan="2" >

			    <b><?php db_ancora("Caracteristica peculiar:","js_pesquisac58_sequencial(true);",$db_opcao,"","carac"); ?></b>

				  <?php
                  db_input("c58_sequencial",10,$Ic58_sequencial,true,"text",$db_opcao,"onChange='js_pesquisac58_sequencial(false);'","c58_sequencial");
                  db_input("c58_descr",40,0,true,"text",3);
                ?>

				</td>
			</tr>
			<tr>
				<td><?=$Lk20_descr?><br>
				<?php db_input('k20_descr',58,$Ik20_descr,true,'text',$db_opcao,"","k20_descr") ?></td>
				<td rowspan=2 valign='top'><?php
				$result = $clcancdebitos->sql_record($clcancdebitos->sql_pendentes( "distinct k20_codigo, k20_descr", "k20_codigo desc", "k20_usuario = ".db_getsession("DB_id_usuario")));
				if ($clcancdebitos->numrows > 0) {
				  ?>
				<table>
					<tr>
						<td>Para inserir os registros selecionados em um grupo de débito
						ja existente, basta selecionar um dos grupos abaixo:</td>
					</tr>
					<tr>
						<td><strong>Grupos de Débitos:</strong><br>
						<?php db_selectrecord("k20_codigo",$result,true,1,"","","","0-Gerar novo grupo de Débitos","js_pesquisacancdebitosreg(this.value)")?>
						</td>
					</tr>
				</table>
				<?}?></td>
			</tr>
			<tr>
				<td><strong>Observações:</strong><br>
				<?php db_textarea('k21_obs',3,70,$Ik21_obs,true,'text',$db_opcao,"")?></td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<input type="button" name="envia" value="Confirmar" onClick="js_verifica()">
					<input type="hidden" name="envia" value="Confirmar">
				</td>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</center>
</form>
<script>
const numeroProcesso = document.getElementById('p58_numero');
const numeroProcessoConcatenado = document.getElementById("numeroProcessoConcatenado");
const nomeProcesso = document.getElementById('z01_nomeprocesso');
const codigoProcesso = document.getElementById('p58_codproc');

document.form1.tipo_filtro.value = parent.document.form2.tipo_filtro.value;
document.form1.cod_filtro.value = parent.document.form2.cod_filtro.value;

function js_pesquisaProcesso(mostra) {
    numeroProcesso.value = numeroProcesso.value.split('/')[0];
    numeroProcessoConcatenado.value = numeroProcesso.value + '/' + anoSessao.value;
    if (mostra == true) {
        js_OpenJanelaIframe('this', 'db_iframe_nomes', 'func_protprocesso_isencao.php?funcao_js=parent.js_mostraProcesso|p58_codproc|z01_nome|p58_numero', 'Pesquisa', true, '0');
    } else {
        if (numeroProcesso.value != '') {
            js_OpenJanelaIframe('this', 'db_iframe_nomes', 'func_protprocesso_isencao.php?pesquisa_chave=' + numeroProcessoConcatenado.value + '&funcao_js=parent.js_mostraProcessoHide&sCampoPesquisa=p58_codproc', 'Pesquisa', false, '0');
        } else {
            nomeProcesso.value = '';
            codigoProcesso.value = '';
        }
    }
}

function js_mostraProcessoHide(chave, chave1, erro) {
    codigoProcesso.value = chave;
    nomeProcesso.value = chave1;
    if (erro == true) {
        numeroProcesso.focus();
        numeroProcesso.value = '';
        codigoProcesso.value = '';
    }
}

function js_mostraProcesso(chave1, chave2, chave3) {
    codigoProcesso.value = chave1;
    nomeProcesso.value = chave2;
    numeroProcesso.value = chave3.split('/')[0]
    db_iframe_nomes.hide();
}

function js_pesquisacancdebitosreg(value){
  if(value!=0){
    js_OpenJanelaIframe('','db_iframe_cgm','func_cancdebitosregalt.php?pesquisa_chave=&pesquisa_chave2='+document.form1.k20_codigo.value+'&funcao_js=parent.js_mostracancdebitosreg','Pesquisa',false);
  }else{
    document.form1.k20_descr.value   = "";
		document.getElementById("k20_descr").readOnly = false;
	  document.getElementById("k20_descr").style.background="#FFFFFF";
    document.form1.k21_obs.value     = "";
    document.form1.k25_codproc.value = "";
    document.form1.p58_requer.value  = "";
		document.getElementById("renuncia").style.display = 'none';
		document.form1.tipoDebito.value     = 1;
    document.getElementById("tipoDebito").disabled = false;
		document.form1.c58_sequencial.value = "";
		document.getElementById("c58_sequencial").readOnly = false;
	  document.getElementById("c58_sequencial").style.background="#FFFFFF";
	  document.form1.c58_descr.value      = "";
		document.getElementById("carac").onclick = function(){js_pesquisac58_sequencial(true)};
		document.getElementById("carac").style.textDecoration='';
		document.getElementById("carac").style.color='blue';

  }
}

function js_mostracancdebitosreg(chave1,chave2,chave3,chave4,chave5,chave6,chave7){

	document.form1.k20_descr.value   = chave1;
  document.form1.k21_obs.value     = chave2;
  document.form1.k25_codproc.value = chave3;
  document.form1.p58_requer.value  = chave4;
  document.form1.tipoDebito.value  = chave7;
	document.form1.tipoDebitoAux.value  = chave7;
  document.getElementById("tipoDebito").disabled = true;
	document.getElementById("k20_descr").readOnly = true;
	document.getElementById("k20_descr").style.background="#DEB887";

	if(chave7 == 2){
		document.form1.c58_sequencial.value = chave5;
	  document.getElementById("c58_sequencial").readOnly = true;
	  document.getElementById("c58_sequencial").style.background="#DEB887";
	  document.form1.c58_descr.value      = chave6;
		document.getElementById("carac").onclick=function(){return false};
	  document.getElementById("carac").style.textDecoration='none';
	  document.getElementById("carac").style.color='#000000';
		document.getElementById("renuncia").style.display = '';
	}else{
		document.getElementById("renuncia").style.display = 'nome';
	}

}

function js_pesquisak25_codproc(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_cgm','func_protprocesso.php?funcao_js=parent.js_mostraprotprocesso1|p58_codproc|p58_requer','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('','db_iframe_cgm','func_protprocesso.php?pesquisa_chave='+document.form1.k25_codproc.value+'&funcao_js=parent.js_mostraprotprocesso','Pesquisa',false);
  }
}
function js_mostraprotprocesso(chave,chave1,erro){
  document.form1.p58_requer.value = chave1;
  if(erro==true){
    document.form1.k25_codproc.focus();
    document.form1.k25_codproc.value = '';
  }
}
function js_mostraprotprocesso1(chave1,chave2){
	document.form1.k25_codproc.value = chave1;
  document.form1.p58_requer.value = chave2;
  db_iframe_cgm.hide();
}
function js_verifica(){
  if (document.form1.tipoDebito.value == "2" && document.form1.c58_sequencial.value == "") {
     document.getElementById("renuncia").style.display = '';
     alert("O campo Caracteristica Peculiar não foi informado.");
     return false;
  }

   document.form1.submit();
}

function js_mostraRenuncia(id){

	if (id == 2) {
  	document.getElementById("renuncia").style.display = '';
  }
  else {
  	document.getElementById("renuncia").style.display = 'none';
  	document.form1.c58_sequencial.value = "";
  	document.form1.c58_descr.value = "";
  }

}
function js_pesquisac58_sequencial(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_concarpeculiar','func_concarpeculiar.php?funcao_js=parent.js_mostraconcarpeculiar1|c58_sequencial|c58_descr&filtro=receita','Pesquisa',true,'0','1');
  }else{
     if(document.form1.c58_sequencial.value != ''){
        js_OpenJanelaIframe('','db_iframe_concarpeculiar','func_concarpeculiar.php?pesquisa_chave='+document.form1.c58_sequencial.value+'&funcao_js=parent.js_mostraconcarpeculiar&filtro=receita','Pesquisa',false);
     }else{
       document.form1.c58_descr.value = '';
     }
  }
}
function js_mostraconcarpeculiar(chave,erro){
  document.form1.c58_descr.value = chave;
  if(erro==true){
    document.form1.c58_sequencial.focus();
    document.form1.c58_sequencial.value = '';
  }
}
function js_mostraconcarpeculiar1(chave1,chave2){
  document.form1.c58_sequencial.value = chave1;
  document.form1.c58_descr.value      = chave2;
  db_iframe_concarpeculiar.hide();
}
<?php
if(isset($tipoDebito) and $tipoDebito==2){

	echo "js_mostraRenuncia(2);";
}
?>

<?php
if(isset($k20_codigo) && $k20_codigo!= 0){
	echo " document.getElementById('k20_descr').readOnly = true;";
	echo " document.getElementById('k20_descr').style.background='#DEB887'; ";
}
?>

</script>
</body>
</html>