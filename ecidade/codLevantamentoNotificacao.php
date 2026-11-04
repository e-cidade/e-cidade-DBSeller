<?php
require_once  modification("libs/db_stdlib.php");
require_once  modification("libs/db_conecta.php");
require_once  modification("libs/db_sessoes.php");
require_once  modification("libs/db_usuariosonline.php");
db_postmemory($HTTP_POST_VARS);
$erro = 0;

	$rsCodLevantamento = db_query("
				      SELECT y60_codlev as code
					    FROM fiscalizacao.fis_lanclevanta
					    JOIN fiscalizacao.fis_lancamento ON fis_lanclevanta.nl15_lancamento = fis_lancamento.nl01_codlanc
					    JOIN fiscalizacao.fis_levanta ON lanclevantanl15_levanta = fis_levanta.y60_codlev
					    WHERE fis_lancamento.nl01_codlanc =".$codigo);
	if(pg_num_rows($rsCodLevantamento) > 0){

	$levantamentos = array();

	for($i=0; $i<pg_num_rows($rsCodLevantamento); $i++){
		db_fieldsmemory($rsCodLevantamento,$i);
 		$levantamentos[] = $code;
	}

	  echo json_encode(array(
	  			'codigoLevantamento' => $levantamentos,
	  			'erro' => 0
	  			));
	}else{
	  echo json_encode(array(
	  			'erro' => true
	  			));
	}
?>
