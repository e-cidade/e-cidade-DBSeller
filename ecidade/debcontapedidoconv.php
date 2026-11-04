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

set_time_limit(0);

// corrige formato da data para "AAAA-MM-DD"
function montaDataDaeb($string_data){

	$ano = (int)substr($string_data,1,4);
	$mes = (int)substr($string_data,5,2);
	$dia = (int)substr($string_data,7,2);

	if($ano==20) {
		$ano = 2000;
	} else if($ano>=9700) {
		$ano = 1900 + (int)substr($string_data,1,2);
		$dia = (int)substr($string_data,5,2);
		$mes = (int)substr($string_data,7,2);
	}	else if($ano<1900) {
		$ano += 1900;
	}

	$check = checkdate($mes, $dia, $ano);

	$data = "'{$ano}-{$mes}-{$dia}'";

	if ($check == false) {
		$data = "null";
	}

	return $data;
}



/************************************************/
//$dbname   = "ontem_20060426_0934";
$dbname   = "daeb";
$dbhost   = "localhost";

$adbarq[0] = "/tmp/sib201_001.txt"; // BANCO DO BRASIL
$adbarq[1] = "/tmp/sib201_041.txt"; // BANRISUL
$adbarq[2] = "/tmp/sib201_104.txt"; // CAIXA

$abanco[0] = 1;   // BANCO DO BRASIL
$abanco[1] = 41;  // BANRISUL
$abanco[2] = 104; // CAIXA

/***********************************************/

$conn = pg_connect("dbname=$dbname user=postgres password=postgres host=$dbhost") or die('ERRO AO CONECTAR NA BASE DE DADOS !!');
system("echo 'Aguarde conectando na base de dados...'");

echo "\nInicializando arquivos...\n";

db_query("BEGIN;");

// Inicializa tabelas
$queries = array();

$queries[] = "select setval('debcontapedidotipo_d66_sequencial_seq', 1, false)";

$queries[] = "select setval('debcontapedidotiponumpre_d67_sequencial_seq', 1, false)";
$queries[] = "select setval('debcontaarquivo_d72_codigo_seq', 1, false)";
$queries[] = "select setval('debcontaarquivoreg_d73_sequenci', 1, false)";
$queries[] = "select setval('debcontaarquivoregmov_d75_seque', 1, false)";
$queries[] = "select setval('debcontaarquivoregped_d80_seque', 1, false)";

$queries[] = "delete from debcontaarquivoregped";
$queries[] = "delete from debcontaarquivoregcad";
$queries[] = "delete from debcontaarquivoregmov";
$queries[] = "delete from debcontaarquivoreg";
$queries[] = "delete from debcontaarquivo";

$queries[] = "delete from debcontapedidotiponumpre";
$queries[] = "delete from debcontapedidotipo";
$queries[] = "delete from debcontapedidomatric";
$queries[] = "delete from debcontapedidocgm";
$queries[] = "delete from debcontapedido";

$queries[] = "delete from debcontaparam";
$queries[] = "insert into debcontaparam values (4, 1,   '48749', 87, '')";
$queries[] = "insert into debcontaparam values (4, 41,  '00168', 95, '')";
$queries[] = "insert into debcontaparam values (4, 104, '1075',  30, '')";

$cont = count($queries);
for($x=0; $x<$cont; $x++) {
	$i = $x+1;
	$perc = round(($i/$cont)*100,2);
	echo "> $perc % concluido...\r";
	db_query($queries[$x]) or die($queries[$x]);
}

// Processa os arquivos
for($w=0; $w<3; $w++) {
	$dbarq = $adbarq[$w];
	$banco = $abanco[$w];

	$arquivo = fopen ("$dbarq", "r");
	$cont=0;
	$cont_sim=0;
	$cont_naum=0;
	$instit=4;//DAEB

	system("> /tmp/erros_$banco.txt");

	while (!feof($arquivo)) {
  	$linha = fgets($arquivo,4096);
    $cont++;

		if($linha==""||$cont==1){
    	continue;
    }

    $colunas     = explode(';', $linha);
    $matricula   = substr($colunas[0],0,6); // pega matricula sem digito
		$debcontapedido = $colunas[0]; // pega matricula com digito
    $nome        = $colunas[1];
    $ident_banco = $colunas[2];//ignorar
    $cod_agencia = $colunas[3];
    $dig_agencia = $colunas[4];
    $num_conta   = $colunas[5];
    $dig_conta   = $colunas[6];

		if($banco == 1) { // Banco do Brasil
			$id_empresa  = str_pad(intval($debcontapedido),18,"0",STR_PAD_LEFT) . "       ";
		} else {
			$id_empresa = $debcontapedido;
		}

		$dt_lanc = montaDataDaeb($colunas[7]);
		$dt_canc = montaDataDaeb($colunas[8]);

//		$sql_arrecad = "
//		  select distinct arrecad.k00_numpre, arrecad.k00_numpar, aguabase.x01_numcgm
//		  from  arrecad
//			inner join arrematric on arrematric.k00_numpre=arrecad.k00_numpre
//			left  join aguabase   on aguabase.x01_matric = arrematric.k00_matric
//			where k00_tipo = 37 and k00_matric = $matricula ";

		$sql_arrecad = "
			select  distinct
			        arrecad.k00_numpre,
			        arrecad.k00_numpar,
			        aguabase.x01_numcgm
			from    aguabase
			inner join arrematric on arrematric.k00_matric = aguabase.x01_matric
			inner join arrecad    on arrecad.k00_numpre    = arrematric.k00_numpre
			where   aguabase.x01_matric = $matricula
			and     arrecad.k00_tipo    = 37
			union
			select  distinct
			        arrecant.k00_numpre,
			        arrecant.k00_numpar,
			        aguabase.x01_numcgm
			from    aguabase
			inner join arrematric on arrematric.k00_matric = aguabase.x01_matric
			inner join arrecant   on arrecant.k00_numpre   = arrematric.k00_numpre
			where   aguabase.x01_matric = $matricula
			and     arrecant.k00_tipo    = 37
			;";

    $result_arrecad = db_query($sql_arrecad) or die("Linha: ".__LINE__."\nSQL: $sql\n");

    if(pg_numrows($result_arrecad)>0){
    	//$d63_codigo_seq = db_query("select nextval('debcontapedido_d63_codigo_seq')");
    	//$d63_codigo = pg_result($d63_codigo_seq,0,0);
			$d63_codigo = $debcontapedido;

			$sql = "insert into debcontapedido values ($d63_codigo,$instit,$banco,'$cod_agencia','$num_conta',$dt_lanc,'00:01',2,'$id_empresa')";
    	$inclui_debcontapedido = db_query($sql) or die("Linha: ".__LINE__."\nSQL: $sql\n");

			$sql = "insert into debcontapedidomatric values ($d63_codigo,$matricula)";
			$inclui_debcontapedidomatric = db_query($sql) or die("Linha: ".__LINE__."\nSQL: $sql\n");

			$cgm = pg_result($result_arrecad, 0, 'x01_numcgm');

			$sql = "insert into debcontapedidocgm    values ($d63_codigo,$cgm)";
			$inclui_debcontapedidocgm = db_query($sql) or die("Linha: ".__LINE__."\nSQL: $sql\n");

			$sql = "select nextval('debcontapedidotipo_d66_sequencial_seq')";
    	$d66_sequencial_seq = db_query($sql) or die("Linha: ".__LINE__."\nSQL: $sql\n");

    	$d66_sequencial = pg_result($d66_sequencial_seq,0,0);

			$sql = "insert into debcontapedidotipo values ($d66_sequencial,$d63_codigo,37)";
    	$inclui_debcontapedidotipo = db_query($sql) or die("Linha: ".__LINE__."\nSQL: $sql\n");

			for($x=0;$x<pg_numrows($result_arrecad);$x++){
    		$k00_numpre = pg_result($result_arrecad,$x,'k00_numpre');
    		$k00_numpar = pg_result($result_arrecad,$x,'k00_numpar');

				$sql = "select nextval('debcontapedidotiponumpre_d67_sequencial_seq')";
    		$d67_sequencial_seq = db_query($sql) or die("Linha: ".__LINE__."\nSQL: $sql\n");

				$d67_sequencial = pg_result($d67_sequencial_seq,0,0);

				$sql = "insert into debcontapedidotiponumpre values ($d67_sequencial,$d63_codigo,$k00_numpre,$k00_numpar)";
    		$inclui_debcontapedidotiponumpre = db_query($sql) or die("Linha: ".__LINE__."\nSQL: $sql\n");
    	}
    	echo "Matricula Nº $matricula Incluida!!\n";
    	$cont_sim++;
    } else {
    	system("echo \"matricula nao cadastrada $matricula\">> /tmp/erros_$banco.txt");
    	echo "Matricula Nº $matricula sem debitos!!\n";
    	$cont_naum++;
    }
	}
	echo "Banco $banco\n";
	echo "Foram incluidas $cont_sim\n";
	echo "Matriculas sem debitos $cont_naum\n\n";
	fclose($arquivo);
}

//db_query("ROLLBACK;");
db_query("COMMIT;");

?>
