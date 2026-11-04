<?php

$x = fsockopen("192.168.0.18",4444);

$modelo=9;

$codbco="001";
$valor=123;
$nome="JOAO DA SILVA";
$municipio="TESTE";
$data="";

if($modelo == 2) {

  fputs($x,chr(27).chr(66)."001");
  fputs($x,chr(27).chr(70)."JOAO DA SILVA$");
  fputs($x,chr(27).chr(67)."MUNICIPIO$");
  fputs($x,chr(27).chr(68).'010107');
  fputs($x,chr(27).chr(86)."00000000012299");
  fputs($x,chr(27).chr(12));
	
} elseif ($modelo == 1) {

	fputs($x, chr(27).chr(177));
	fputs($x, chr(27).chr(162).$codbco.chr(13));
	fputs($x, chr(27).chr(163).$valor.chr(13));
	fputs($x, chr(27).chr(160).$nome.chr(13));
	fputs($x, chr(27).chr(161).$municipio.chr(13));
	fputs($x, chr(27).chr(164).$data.chr(13));
	fputs($x, chr(27).chr(176));

} elseif ($modelo == 3) {
	
	fputs($x, chr(27).chr(160)."$nome\n");
	fputs($x, chr(27).chr(161)."$municipio\n");
	fputs($x, chr(27).chr(162)."$codbco\n");
	fputs($x, chr(27).chr(163)."$valor\n");
	fputs($x, chr(27).chr(164)."$data\n");
	fputs($x, chr(27).chr(176));
	
} elseif ($modelo == 8) {

  $nValor = 6800;
  $sStringImpressao  = chr(27).chr(66)." 001".chr(13);
  $sStringImpressao .= chr(27).chr(70)." JOAO DA SILVA".chr(36).chr(13);
  $sStringImpressao .= chr(27).chr(67)." TESTE".chr(36).chr(13);
  $sStringImpressao .= chr(27).chr(68)." 120509";
  $sStringImpressao .= chr(27).chr(86)." ".str_pad($nValor,14,0,STR_PAD_LEFT).chr(13);
  fputs($x, $sStringImpressao);
	
} elseif ($modelo == 4) {
	
	$qtdcheques = 2;

  for($i=1; $i<=$qtdcheques; $i++) {
  	
	  fputs($x, "Cheque: $i\n");
	  fputs($x, "Banco: $codbco\n");
    fputs($x, "Valor: $valor\n");
	  fputs($x, "Favorecido: $nome\n");
    fputs($x, "Municipio: $municipio\n");
	  fputs($x, "Data: $data\n");
	  fputs($x, "\n\n\n\n");
	  
  } 
}elseif ($modelo == 9) {
  /*
  fputs($x, "Cheque: $i\n");
  fputs($x, "Banco: $codbco\n");
  fputs($x, "Valor: $valor\n");
  fputs($x, "Favorecido: $nome\n");
  fputs($x, "Municipio: $municipio\n");
  fputs($x, "Data: $data\n");
  fputs($x, "\n\n\n\n");
  */

  require 'model/impressaoCheque.model.php';
  require 'libs/db_stdlib.php';
  
  $oImpressaoCheque = new impressaoCheque(9);
	
  $qtdcheques = 10;
  
  for($i=1; $i<=$qtdcheques; $i++) {

    $oImpressaoCheque->setIp('192.168.0.18');
    $oImpressaoCheque->setPorta(4444);
    $oImpressaoCheque->setdtDataImpressao('2009-05-22');
    $oImpressaoCheque->setnValor(10000);
    $oImpressaoCheque->setSCredor('Dbseller informatica');

    $oImpressaoCheque->montaImpressao();
    $oImpressaoCheque->imprimir();

  }

  // var_dump($oImpressaoCheque->sStringImpressao);
  // die();

  
  echo "ok \n \n";
  
  

}


fclose($x);

?>
