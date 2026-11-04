<?php

include("/var/www/dbportal2/libs/db_stdlib.php");

db_imprimecheque("JOAO DA SILVA", "001", 100, "01/01/2008", 2, "192.168.0.72", "4444", "PORTO ALEGRE");

function db_imprimecheque ($nome, $codbco, $valor, $data, $modelo = 1, $ip_imprime, $porta, $municipio ){
    
  global $prefeito, $tesoureiro, $municipio, $ip_imprime;
  if($municipio == ''){
    $municipio = '............';
  }

  $ip_imprime = "192.168.0.72";

  echo "ip: $ip_imprime\n";
  echo "porta: $porta\n";
  echo "modelo: $modelo\n";

  $valor = trim(db_formatar($valor, 'p', '', 2));
  $nome = str_pad($nome,40," ", STR_PAD_RIGHT);
  $fd = fsockopen($ip_imprime, $porta);
  if(!$fd) {
		//
		echo "Impossivel conectar com impressora em $ip_imprime:$porta!";
		return;
	}
	
  // modelo 1 - sapiranga CHRONOS
  // modelo 2 - guaiba / alegrete BEMATECH (DP 20)
  if($modelo == 2){
    $data = str_replace("-", "/", $data);
    $imprimir  = chr(27).chr(177);
    $imprimir .= chr(27).chr(162).$codbco.chr(13);
    $imprimir .= chr(27).chr(163).$valor.chr(13);
    $imprimir .= chr(27).chr(160).$nome.chr(13);
    $imprimir .= chr(27).chr(161).$municipio.chr(13);
    $imprimir .= chr(27).chr(164).$data.chr(13);
    $imprimir .= chr(27).chr(176);
    
   
    /*
    if(strtoupper($municipio) == "SAPIRANGA"){ 
      $imprimir .= chr(13).chr(10);
      $imprimir .= chr(13).chr(10);
      $imprimir .= chr(13).chr(10);
      $imprimir .= chr(13).chr(10);
      $imprimir .= chr(13).chr(10);
      $imprimir .= chr(13).chr(10);
      $imprimir .= chr(13).chr(10);
      $imprimir .= chr(13).chr(10);
      $imprimir .= "          Prefeito: $prefeito $tesoureiro".chr(10).chr(13);
    }*/
    fputs($fd, $imprimir);
    
  }elseif($modelo == 1){

    fputs($fd, chr(27).chr(160)." $nome\n");
    fputs($fd, chr(27).chr(161)." $municipio\n");
    fputs($fd, chr(27).chr(162)." $codbco\n");
    fputs($fd, chr(27).chr(163)." $valor\n");
    fputs($fd, chr(27).chr(164)." $data\n");
    fputs($fd, chr(27).chr(176));
/*
    fputs($fd, " \n");
    fputs($fd, " \n");
    fputs($fd, " \n");
    fputs($fd, " \n");
    fputs($fd, " \n");
    fputs($fd, " \n");
    fputs($fd, " \n");
    fputs($fd, "          Prefeito: $prefeito $tesoureiro"."\n");
*/
  }elseif($modelo == 3){

    $data=str_replace("-","",$data);
    $valor=db_formatar($valor, 'p', '0', 15);
    $valor=str_replace(".","",$valor);

    fputs($fd, chr(27).chr(66)." $codbco\n");
    fputs($fd, chr(27).chr(70)." $nome\n");
    fputs($fd, chr(27).chr(67)." $municipio\n");
    fputs($fd, chr(27).chr(68)." $data\n");
    fputs($fd, chr(27).chr(86)." $valor\n");

  }elseif($modelo == 4){

    $valor=db_formatar($valor, 'p', '0', 15);
    $valor=str_replace(".","",$valor);

    $fim="0DH";

    fputs($fd, chr(27) . "119" . "0");

    fputs($fd, chr(27) . "A0H" . $nome       . $fim);
    fputs($fd, chr(27) . "A1H" . $municipio  . $fim);
    fputs($fd, chr(27) . "A2H" . $codbco     . $fim);
    fputs($fd, chr(27) . "A3H" . $valor      . $fim);
    fputs($fd, chr(27) . "A4H" . $data       . $fim);

  }
    
  fclose($fd);
      
}

?>
