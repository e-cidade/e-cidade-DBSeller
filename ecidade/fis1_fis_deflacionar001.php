<?php 
require_once  modification("libs/db_stdlib.php");
require_once  modification("libs/db_conecta.php");
require_once  modification("libs/db_sessoes.php");
require_once  modification("libs/db_usuariosonline.php");
db_postmemory($HTTP_POST_VARS);
$erro = 0;

function Deflacionar_valores( $dtVencimento , $dtPagamento , $dValor , $sIntit ){
  
  $vCorrigido    = 0; // Valor Corrigido
  $vDescorrigido = $dValor; // Valor Descorrigido

  /* --[       Verifica a receita cadastrada nos parametros do Fiscal    ]--  */
  /* --[      buscando na tabrec para buscar o codigo de inflaçao        ]--  */
  
  $rsGetReceita = db_query("  SELECT k02_corr 
                                  FROM fiscalizacao.fis_parfiscal
                                    INNER JOIN tabrec   ON tabrec.k02_codigo  = fis_parfiscal.y32_receit
                                    INNER JOIN tabrecjm ON tabrecjm.k02_codjm = tabrec.k02_codjm 
                              WHERE fis_parfiscal.y32_instit = {$sIntit}                                 ");

  $cInfla = db_utils::fieldsMemory($rsGetReceita, 0)->k02_corr;

  /* -- [Pega Ano do Vencimento e o Ano do Pagamento] -- */
  $sAnoVenc = substr( $dtVencimento , 6 , 10 );
  $sAnoPag  = substr( $dtPagamento  , 6 , 10 );
  /* -- [Pega Mes do Vencimento e o Mes do Pagamento] -- */
  $sMesVenc = substr( $dtVencimento , 3 , 2 );
  $sMesPag  = substr( $dtPagamento  , 3 , 2 );
  
  /* -- [ Verifica sempre o mes anterior da competencia ] -- */
  $sMesPag = ( $sMesPag - 1 );
  if( $sMesPag == 0){
    $sMesPag = 12;
    $sAnoPag = ( $sAnoPag - 1 );
  }
/*  $sMesVenc = ( $sMesVenc + 1 );
  if( $sMesVenc == 13){
    $sMesVenc = 1;
    $sAnoVenc = ( $sAnoVenc + 1 );
  }
  */
  /* -- [ Executa Loop Entre os anos em ordem Descrescente ] -- */
  $cValor = 0;
  for ( $iAno = (int)$sAnoPag ; $iAno >= (int)$sAnoVenc ; $iAno-- ) { 

    /* -- [ Logica para fazer loop do Mes ] -- */
    if ( $iAno == $sAnoVenc ){
      $iMesFim = $sMesVenc;
    }else{
      $iMesFim = 1;
    }

    if( $sAnoPag != $iAno ){
      $iMesIni = 12;          
    }else{
      $iMesIni = $sMesPag; 
    }
     /*-- [Executa Loop Entre os Meses em ordem Descrescente] --*/
    for ( $i = (int)$iMesIni; $i >= (int)$iMesFim ; $i-- ) { 
        /*-- [Verificar o valor de inflaçao no mes decorrente] --*/
        $sSqlInflator  = "    SELECT i02_valor                                            ";
        $sSqlInflator .= "           FROM infla                                           ";
        $sSqlInflator .= "           WHERE i02_codigo = '{$cInfla}'                       ";
        $sSqlInflator .= "              AND  extract(year from infla.i02_data)  = {$iAno} ";
        $sSqlInflator .= "              AND  extract(month from infla.i02_data) = {$i}    ";
        $rsInflator = db_query( $sSqlInflator );
        $cValor     = db_utils::fieldsMemory($rsInflator, 0)->i02_valor;
        $vDescorrigido = ($vDescorrigido/$cValor);

    }
  }
  /* ---[ Faz a deflaçao do valor de acordo com a competencia ]--- */
  return round($vDescorrigido,2); // Retorna o valor deflacionado

} 

if( $pagts == 'true' ){
  $sValores = explode("HHH", $valor);
  foreach ($sValores as $key) {
    $sSepara = explode("-", $key);
    $rValor += Deflacionar_valores($_POST['venci'],$sSepara[1],$sSepara[0] , db_getsession('DB_instit'));
  }
}else{
  $rValor = Deflacionar_valores($_POST['venci'],$_POST['pago'],$_POST['valor'] , db_getsession('DB_instit')); 
}

echo json_encode(
		array(
			'erro'       => $erro,
			'valor'      => $rValor,
			'saldo'      => ($apagar - $rValor )
			)
		);
?>
