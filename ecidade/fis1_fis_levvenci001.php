<?php 
require_once  modification("libs/db_stdlib.php");
require_once  modification("libs/db_conecta.php");
require_once  modification("libs/db_sessoes.php");
require_once  modification("libs/db_usuariosonline.php");

$erro = 0;

$resultpar      = db_query("select * from parissqn");
$q60_codvencvar = pg_result($resultpar,0,"q60_codvencvar");

$sqlvenc    = "select q82_venc,q82_hist from cadvenc where q82_codigo = $q60_codvencvar and q82_parc = ".$_POST['mes'];
$resultvenc = db_query($sqlvenc);
$vencimento = pg_result($resultvenc,0,"q82_venc");

//if($_POST['ano'] == db_getsession("DB_anousu")){
//	$vencimento = $vencimento;
//}else{
	$res  = db_query("select * from confvencissqnvariavel where q144_ano = ".$_POST['ano']);
	if(pg_num_rows($res) > 0){
		$q144_codvenc = pg_result($res,0,"q144_codvenc");
	}else{
		$erro = "Tabela confvencissqnvariavel vazia!";
	}
	$sqlvenc    = "select q82_venc,q82_hist from cadvenc where q82_codigo = $q144_codvenc and q82_parc = ".$_POST['mes'];
	
	$resultvenc = db_query($sqlvenc);
	$vencimento = pg_result($resultvenc,0,"q82_venc");
	// $qmes = $_POST['mes'];
	// $qano = $_POST['ano'];
	// $qmes += 1;
	// if($qmes > 12){
	// 	$qmes  = 1;
	// 	$qano += 1;
	// }
	// $qmes         = str_pad($qmes,2,"0",STR_PAD_LEFT);
	// $venc_arrecad = $qano."-".$qmes."-".$w10_dia;
	// $vencimento   = $venc_arrecad;
//}

$retorno = explode('-', $vencimento);
echo json_encode(
		array(
			'erro'       => $erro,
			'vencimento' => $retorno[2].'/'.$retorno[1].'/'.$retorno[0],
			'dia'        => $retorno[2],
			'mes'        => $retorno[1],
			'ano'        => $retorno[0]
			)
		);
?>
