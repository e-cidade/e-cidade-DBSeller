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

include(modification("libs/db_sql.php"));
include(modification("fpdf151/pdf.php"));
$clrotulo = new rotulocampo;
$clrotulo->label('j01_matric');
$clrotulo->label('q02_inscr');
$clrotulo->label('z01_numcgm');
$clrotulo->label('z01_nome');
$clrotulo->label('z01_ender');
$clrotulo->label('z01_compl');
$clrotulo->label('z01_bairro');
$clrotulo->label('z01_munic');
$clrotulo->label('v01_exerc');
$clrotulo->label('v01_proced');
$clrotulo->label('v03_descr');

//db_postmemory($HTTP_SERVER_VARS,2);exit;
//db_postmemory($HTTP_POST_VARS,2);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$head2 = 'RELATÓRIO DA DÍVIDA POR EXERCÍCIO';
$head5 = '';
$head6 = '';
$instit = db_getsession("DB_instit");

$agrupa1 = '';
$agrupa2 = '';
$ordem_ano_exercicio = '';
$ordem_procedencia = '';

if ($valor_inicial == "") {
	$valor_inicial = 0;
}

if($agexerc == "S"){
   $head5 = 'AGRUPADO POR EXERCÍCIO : SIM';
   $agrupa1 = ', k22_exerc ';
   $debagrupa1 = ', k22_exerc ';
   $ordem_ano_exercicio = ', k22_exerc asc';
}

if($agproced == "S"){
   $head6 = 'AGRUPADO POR EXERCÍCIO : NÃO';
   $agrupa2 = ', v01_proced,v03_descr ';
   $ordem_procedencia = ' , v01_proced';
}

if($sele == "S"){
  $simnao = ' in ';
}else{
  $simnao = ' not in ';
}

if(isset($proced)){
  $proced = 'and v01_proced '.$simnao.' ('.str_replace("-",",",$proced).') ';
}else{
  $proced = '';
}
$head4 = "";
if(isset($exerc)){
  $exercicios = ' and v01_exerc in ('.str_replace("-",",",$exerc).') ';
  $selexercicios = ' and v01_exerc > '.str_replace("-",",",$exerc).' ';
  $anos=str_replace("-",",",$exerc);
  $head4 = "Exercicíos Selecionados: $anos ";
	$matanos= explode("-",$exerc);
	$menorexerc=0;
	$maiorexerc=0;
	for ($contanos=0; $contanos < sizeof($matanos); $contanos++) {

		if ($matanos[$contanos] < $menorexerc or $menorexerc == 0) {
			$menorexerc = $matanos[$contanos];
		}

		if ($matanos[$contanos] > $maiorexerc) {
			$maiorexerc = $matanos[$contanos];
		}
	}

//  $exercicios = " and v01_exerc between $menorexerc and $maiorexerc ";
//  $selexercicios = " and v01_exerc between $menorexerc and $maiorexerc ";

}else{
  $exercicios = '';
}

if($tipo == 'matric'){
  $head4 .= '(EMISSÃO POR MATRÍCULA DO IMÓVEL)';
  $xtipo = ' inner join proprietario_nome on k22_matric = j01_matric ';
  $matric1 = 'k22_matric';
  $matric = 'j01_matric';
}elseif($tipo == 'inscr'){
  $head4 .= '(EMISSÃO POR INSCRIÇÃO DA EMPRESA)';
  $xtipo  = ' inner join issbase on k22_inscr = q02_inscr ';
//	$xtipo .= ' inner join tabativ on q02_inscr = q07_inscr ';
//	$xtipo .= ' inner join ativprinc on q88_inscr = q02_inscr and q07_seq = q88_seq ';
	$xtipo .= ' inner join cgm on q02_numcgm = z01_numcgm ';
  $matric1 = 'k22_inscr';
  $matric = 'q02_inscr';
}else{
  $head4 .= '(EMISSÃO POR CONTRIBUINTE)';
  $xtipo = ' inner join cgm on k22_numcgm = z01_numcgm ';
  $matric1 = 'k22_numcgm';
  $matric = 'z01_numcgm';
}
  //$numerolista = 1;
if ($numerolista == ''){
    $limite = '';
}else{
    $limite = ' limit '.$numerolista;
}

if ($ordem == 'numerica'){
    $ordem = $matric;
}


//echo $campos."<br>";
//echo $exercicios;exit;
$sql1 = "select k22_data as xdata
             from debitos
             where k22_instit = $instit
             order by k22_data desc limit 1";
$result1 = db_query($sql1);
db_fieldsmemory($result1,0);

$head5 = "Cáculo na data :".db_formatar($xdata,'d');

if ($proced != "" or $agproced == "S") {
	$sql = "
					select $matric,
			z01_nome,
			z01_cgccpf,
			z01_ender,
			z01_compl,
			z01_bairro,
			z01_munic,
			round(sum(k22_vlrhis),2) as k22_vlrhis,
			round(sum(k22_vlrcor),2) as k22_vlrcor,
			round(sum(k22_juros),2) as k22_juros,
			round(sum(k22_multa),2) as k22_multa,
			round(sum(k22_vlrcor+k22_juros+k22_multa),2) as valor
									$agrupa1
			$agrupa2
					from debitos
					inner join divida  on v01_numpre = k22_numpre
					                  and v01_numpar = k22_numpar and k22_data = '$xdata'
							          and v01_instit = ".db_getsession('DB_instit')."
  				    inner join proced  on v01_proced = v03_codigo
					                  and v03_instit = ".db_getsession('DB_instit')."
					inner join arretipo on arretipo.k00_tipo = k22_tipo
				    $xtipo
		 where k03_tipo = 5 $exercicios $proced and debitos.k22_instit = $instit

		 group by $matric,
			z01_nome,
			z01_cgccpf,
			z01_ender,
			z01_compl,
			z01_bairro,
			z01_munic
					$agrupa1
			$agrupa2
				 ";
		//die($agrupa2);
	if($considera_debitos==1) {
    //die($exercicios);
		$sql .= " union all
					select  $matric,
									z01_nome,
									z01_cgccpf,
									z01_ender,
									z01_compl,
									z01_bairro,
									z01_munic,
									round(sum(k22_vlrhis),2) as k22_vlrhis,
									round(sum(k22_vlrcor),2) as k22_vlrcor,
									round(sum(k22_juros),2) as k22_juros,
									round(sum(k22_multa),2) as k22_multa,
									round(sum(k22_vlrcor+k22_juros+k22_multa),2) as valor
											$agrupa1
									$agrupa2
											from debitos
													 inner join divida on v01_numpre = k22_numpre
													                  and v01_numpar = k22_numpar
																	  and k22_data   = '$xdata'
																	  and v01_instit = ".db_getsession('DB_instit')."
													 inner join proced on v01_proced = v03_codigo
													                  and v03_instit = ".db_getsession('DB_instit')."
													 $xtipo
											         inner join ( select $matric1
																				from debitos
																				 inner join divida on v01_numpre = k22_numpre
																				                  and v01_numpar = k22_numpar
																													and k22_data = '$xdata'
																													and v01_instit = ".db_getsession('DB_instit')."
																				 inner join proced on v01_proced = v03_codigo
																				                  and v03_instit = ".db_getsession('DB_instit')."
																				 inner join arretipo on arretipo.k00_tipo = k22_tipo
																					 $xtipo
																			where k03_tipo = 5 $exercicios $proced
																			group by $matric1
																			) as ver on ver.$matric1 = debitos.$matric1
								 where k22_tipo = 5 and k22_exerc > $maiorexerc $proced and k22_data = '$xdata' and k22_instit = $instit
								 group by $matric,
									z01_nome,
									z01_cgccpf,
									z01_ender,
									z01_compl,
									z01_bairro,
									z01_munic
					$agrupa1
			$agrupa2
					";

	}

	$sql_exe = "

	select *,
		sum(valor) over (partition by $matric $agrupa1) as soma_por_cgm_por_ano,
		sum(valor) over (partition by $matric $agrupa1 order by $matric $ordem_procedencia $agrupa1) as soma_por_cgm_por_ano_linha,
		sum(valor) over (partition by $matric ) as soma_por_cgm,
		round(sum(k22_vlrhis) over (partition by $matric $agrupa1),2) as soma_k22_vlrhis_por_ano,
		round(sum(k22_vlrcor) over (partition by $matric $agrupa1), 2) as soma_k22_vlrcor_por_ano,
		round(sum(k22_juros) over (partition by $matric $agrupa1), 2) as soma_k22_juros_por_ano,
		round(sum(k22_multa) over (partition by $matric $agrupa1), 2) as soma_k22_multa_por_ano
    from (
			select $matric,
			z01_nome,
			z01_cgccpf,
			z01_ender,
			z01_compl,
			z01_bairro,
			z01_munic,
			round(sum(k22_vlrhis),2) as k22_vlrhis,
			round(sum(k22_vlrcor),2) as k22_vlrcor,
			round(sum(k22_juros),2) as k22_juros,
			round(sum(k22_multa),2) as k22_multa,
			round(sum(k22_vlrcor+k22_juros+k22_multa),2) as valor
					$agrupa1
			$agrupa2
					from ($sql) as  x

		 group by $matric,
			z01_nome,
			z01_cgccpf,
			z01_ender,
			z01_compl,
			z01_bairro,
			z01_munic
					$agrupa1
			$agrupa2
		) as x
	where valor >= $valor_inicial::numeric and valor <= $valor_final::numeric

					order by $ordem $ordemtipo ,$matric $ordem_ano_exercicio $ordem_procedencia
					$limite

	";
} else {

	$sql = "
					select $matric,
			z01_nome,
			z01_cgccpf,
			z01_ender,
			z01_compl,
			z01_bairro,
			z01_munic,
			round(sum(k22_vlrhis),2) as k22_vlrhis,
			round(sum(k22_vlrcor),2) as k22_vlrcor,
			round(sum(k22_juros),2) as k22_juros,
			round(sum(k22_multa),2) as k22_multa,
			round(sum(k22_vlrcor+k22_juros+k22_multa),2) as valor
									$agrupa1
			$agrupa2
					from debitos
				$xtipo
		 where k22_tipo = 5 and k22_exerc in (" . str_replace("-",",",$exerc) . ") and k22_data = '$xdata' and k22_instit = $instit
		 group by $matric,
			z01_nome,
			z01_cgccpf,
			z01_ender,
			z01_compl,
			z01_bairro,
			z01_munic
					$agrupa1
				 ";
	if($considera_debitos==1){
    //die("Exerc: $exerc  Menor Exerc: $menorexerc  Maior Exerc: $maiorexerc Agrupa1 $agrupa1  Agrupa2 $agrupa2");
		$sql .= " union all
					select $matric,
			z01_nome,
			z01_cgccpf,
			z01_ender,
			z01_compl,
			z01_bairro,
			z01_munic,
			round(sum(k22_vlrhis),2) as k22_vlrhis,
			round(sum(k22_vlrcor),2) as k22_vlrcor,
			round(sum(k22_juros),2) as k22_juros,
			round(sum(k22_multa),2) as k22_multa,
			round(sum(k22_vlrcor+k22_juros+k22_multa),2) as valor
					$agrupa1
					from debitos
							 $xtipo
					 inner join (
														select $matric1
														from debitos
															 $xtipo
													where  k22_tipo = 5 and k22_exerc in (" . str_replace("-",",",$exerc) . ") and k22_data = '$xdata' and k22_instit = $instit
													group by $matric1
													) as ver on ver.$matric1 = debitos.$matric1
		 where k22_tipo = 5 and k22_exerc > $maiorexerc and k22_data = '$xdata' and k22_instit = $instit
		 group by $matric,
			z01_nome,
			z01_cgccpf,
			z01_ender,
			z01_compl,
			z01_bairro,
			z01_munic
					$agrupa1 		";
//die($sql);
	}

	$sql_exe = "

	select *,
		sum(valor) over (partition by $matric $agrupa1) as soma_por_cgm_por_ano,
		sum(valor) over (partition by $matric $agrupa1 order by $matric $agrupa1) as soma_por_cgm_por_ano_linha,
		sum(valor) over (partition by $matric) as soma_por_cgm,
		round(sum(k22_vlrhis) over (partition by $matric $agrupa1),2) as soma_k22_vlrhis_por_ano,
		round(sum(k22_vlrcor) over (partition by $matric $agrupa1), 2) as soma_k22_vlrcor_por_ano,
		round(sum(k22_juros) over (partition by $matric $agrupa1), 2) as soma_k22_juros_por_ano,
		round(sum(k22_multa) over (partition by $matric $agrupa1), 2) as soma_k22_multa_por_ano
    from  (
			select $matric,
			z01_nome,
			z01_cgccpf,
			z01_ender,
			z01_compl,
			z01_bairro,
			z01_munic,
			round(sum(k22_vlrhis),2) as k22_vlrhis,
			round(sum(k22_vlrcor),2) as k22_vlrcor,
			round(sum(k22_juros),2) as k22_juros,
			round(sum(k22_multa),2) as k22_multa,
			round(sum(k22_vlrcor+k22_juros+k22_multa),2) as valor
					$agrupa1
					from ($sql) as  x

		 group by $matric,
			z01_nome,
			z01_cgccpf,
			z01_ender,
			z01_compl,
			z01_bairro,
			z01_munic
					$agrupa1
		) as x
	where valor >= $valor_inicial::numeric and valor <= $valor_final::numeric
	order by $ordem $ordemtipo ,$matric $ordem_ano_exercicio $limite	";

}
//die($sql_exe);
$result = db_query($sql_exe);
if(pg_numrows($result)==0){
  db_redireciona('db_erros.php?fechar=true&db_erro=Não existem dividas em aberto. ($exercicios).');
}
//die ($sql);
$pdf = new PDF();
$pdf->Open();
$pdf->AliasNbPages();
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(220);
$pdf->SetFont('Arial','B',7);
$xmatric = 'RL'.trim($matric);
$pag = 1;
$totalreg = 0;
$totalhis = 0;
$totalcor = 0;
$totaljur = 0;
$totalmul = 0;
$totalval = 0;
for ($x = 0 ; $x < pg_numrows($result);$x++){
  db_fieldsmemory($result,$x);
  if (($pdf->gety() > $pdf->h - 30) || $pag == 1){
    if($agproced == "S"){
      $pdf->addpage("L");
    }else{
      $pdf->addpage();
    }

     $pdf->Cell(15,5,substr($$xmatric,0,10),1,0,"C",1);
     $pdf->Cell(63,5,$RLz01_nome,1,0,"C",1);
     if($agexerc == "S")
        $pdf->Cell(12,5,$RLv01_exerc,1,0,"C",1);
     if($agproced == "S"){
       $pdf->Cell(20,5,$RLv01_proced,1,0,"C",1);
       $pdf->Cell(55,5,$RLv03_descr,1,0,"C",1);
     }
	 $pdf->cell(26,5,'CPF/CNPJ',1,0,"C",1);
     $pdf->cell(16,5,'Historico',1,0,"L",1);
     $pdf->cell(17,5,'Corrigido',1,0,"L",1);
     $pdf->cell(17,5,'Juros',1,0,"L",1);
     $pdf->cell(16,5,'Multa',1,0,"L",1);
     $pdf->cell(17,5,'Total',1,1,"L",1);
     $pag = 0;
  }
  $pdf->SetFont('Arial','',7);
  $pdf->Cell(15,5,trim($$matric),0,0,"C",0);
  $pdf->Cell(63,5,$z01_nome,0,0,"L",0);
  if($agexerc == "S")
    $pdf->Cell(12,5,$k22_exerc,0,0,"C",0);
  if($agproced == "S"){
    $pdf->Cell(20,5,$v01_proced,0,0,"C",0);
    $pdf->Cell(55,5,$v03_descr,0,0,"L",0);
  }
  $cpfCnpj = strlen($z01_cgccpf) == 11 ? 'cpf' : 'cnpj';
  $pdf->cell(26,5,db_formatar($z01_cgccpf,$cpfCnpj),0,0,"C",0);
  $pdf->cell(16,5,db_formatar($k22_vlrhis,'f'),0,0,"R",0);
  $pdf->cell(17,5,db_formatar($k22_vlrcor,'f'),0,0,"R",0);
  $pdf->cell(17,5,db_formatar($k22_juros,'f'),0,0,"R",0);
  $pdf->cell(16,5,db_formatar($k22_multa,'f'),0,0,"R",0);
  $pdf->cell(17,5,db_formatar($valor,'f'),0,1,"R",0);
  $totalreg += 1;
  $totalhis += $k22_vlrhis;
  $totalcor += $k22_vlrcor;
  $totaljur += $k22_juros;
  $totalmul += $k22_multa;
  $totalval += $valor;


  if($analitico == 1 ){
	if ($soma_por_cgm_por_ano_linha==$soma_por_cgm_por_ano)
		{
			$pdf->cell(70,5,'',0,0,"C",0);
            $pdf->cell(8,5,$k22_exerc,0,0,"L",0);
			if($agexerc == "S")
				$pdf->cell(12,7,'',0,0,"R",0);
			if($agproced == "S"){
				$pdf->cell(20,7,'',0,0,"R",0);
				$pdf->cell(55,7,'',0,0,"R",0);
			}
			$pdf->cell(26,7,'',0,0,"R",0);
			$pdf->cell(16,5,db_formatar($soma_k22_vlrhis_por_ano,'f'),0,0,"R",0);
			$pdf->cell(17,5,db_formatar($soma_k22_vlrcor_por_ano,'f'),0,0,"R",0);
			$pdf->cell(17,5,db_formatar($soma_k22_juros_por_ano,'f'),0,0,"R",0);
			$pdf->cell(16,5,db_formatar($soma_k22_multa_por_ano,'f'),0,0,"R",0);
			$pdf->cell(17,5,db_formatar($soma_por_cgm_por_ano,'f'),0,1,"R",0);
		}
    }
}
$pdf->SetFont('Arial','B',7);
$pdf->Cell(78,7,'Total de Registros : '.$totalreg,'T',0,"L",0);
if($agexerc == "S")
  $pdf->cell(12,7,'','T',0,"R",0);
if($agproced == "S"){
  $pdf->cell(20,7,'','T',0,"R",0);
  $pdf->cell(55,7,'','T',0,"R",0);
}
$pdf->cell(26,7,'','T',0,"R",0);
$pdf->cell(16,7,db_formatar($totalhis,'f'),'T',0,"R",0);
$pdf->cell(17,7,db_formatar($totalcor,'f'),'T',0,"R",0);
$pdf->cell(17,7,db_formatar($totaljur,'f'),'T',0,"R",0);
$pdf->cell(16,7,db_formatar($totalmul,'f'),'T',0,"R",0);
$pdf->cell(17,7,db_formatar($totalval,'f'),'T',1,"R",0);


$pdf->Output();

?>
