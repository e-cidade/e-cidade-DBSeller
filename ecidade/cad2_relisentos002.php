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

include(modification("fpdf151/pdf.php"));
$clrotulo = new rotulocampo;
$clrotulo->label('j46_matric');
$clrotulo->label('j47_anousu');
$clrotulo->label('j45_descr');
$clrotulo->label('j21_valor');
$clrotulo->label('j46_perc');
$clrotulo->label('j46_hist');

db_postmemory($HTTP_SERVER_VARS);

$head1 = "RELATÓRIO DE ISENÇÕES";
$head2 = ($isencoes == "cad"?"SOMENTE CADASTRADAS":"SOMENTE CALCULADAS");
$head3 = "ISENTOS DO ANO ".$anoini." ATÉ O ANO ".$anofin;
$head4 = "ORDEM: " . ($order == "e"?"ENDERECO":($order == "z01_nome"?"NOME":"MATRÍCULA"));

function getRows($sql) {
    $result = db_query($sql);
    while($row = pg_fetch_assoc($result)) {
        yield $row;
    }
}

if ($datai != "--"){

		if($tipodata == "dtinc"){

		   $xdatas    = " and j46_dtinc between '$datai' and '$dataf' ";
		   $tipodata2 = "DATA DE INCLUSÃO DA ISENÇÃO";
		}else if($tipodata == "dtini"){

		   $xdatas    = " and j46_dtini between '$datai' and '$dataf' ";
		   $tipodata2 ="DATA DE INÍCIO DA ISENÇÃO";
		}else if($tipodata == "dtfim"){

		   $xdatas    = " and j46_dtfim between '$datai' and '$dataf' ";
		   $tipodata2 = "DATA DE FIM DA ISENÇÃO";
		}

   $head5 = "$tipodata2";
   $head6 = "PERÍODO ".db_formatar($datai,'d')." A ".db_formatar($dataf,'d');
} else {

   $xdatas = "";
   $head5  = "SEM FILTRO PARA PERIODO DEFINIDO";
}
if($isencoes == "cad"){
  $calv = false;
}elseif($isencoes == "calc"){
  $calv = true;
}

$xtipo = '';

if (isset($campo)){
  $xtipo = " and j46_tipo in (".str_replace('-',', ',$campo).")";
}

if(isset($order)){

   if($order == "e"){
     $order = "nomepri";
   }elseif($order == "matricula"){
     $order = "j46_matric";
   }
}

if($calv == false){
  $iptucalv = "and j21_matric is null";
}else{
  $iptucalv = "";
}

$exercicio = db_getsession("DB_anousu");

$sqlMain = "select  distinct *,
	              (select min(j47_anousu) from iptuisen join isenexe on j47_codigo = j46_codigo and j46_matric = y.j46_matric) as min,
	              (select max(j47_anousu) from iptuisen join isenexe on j47_codigo = j46_codigo and j46_matric = y.j46_matric) as max
	      from (select	j46_matric,
											j46_codigo,
											processo,
											z01_nome,
											j47_anousu,
											j45_descr,
											j46_perc,
											j21_valor,
											min(j46_hist) as j46_hist,
											z01_compl,
											codpri,
											nomepri,
											j39_numero,
											tipo,
											min(j46_dtinc) as j46_dtinc,
                                            anoIni,
                                            anoFim,
                                            j46_dtini,
                                            j46_dtfim,
                                            j46_dtinc_dt
							from (
	            select  j46_matric,
											j46_codigo,
 										  cast(p58_numero||'/'||p58_ano as varchar) as processo,
		                  z01_nome,
		                  j47_anousu,
		                  j45_descr,
		                  j46_perc,
		                  abs(coalesce(j21_valor,0)) as j21_valor,
		                  j46_hist,
                      z01_compl,
                      codpri,
                      nomepri,
                      proprietario.j39_numero,
		                  case
		                    when j39_matric is not null then 'PREDIAL'
		                    when j39_matric is null or j39_dtdemo is not null then 'TERRITORIAL'
		                  end as tipo,
		                  j46_dtinc,
                          extract(year from j46_dtini)::integer as anoIni,
                          extract(year from j46_dtfim)::integer as anoFim,
                          to_char(j46_dtini, 'DD/MM/YYYY') as j46_dtini,
                          to_char(j46_dtfim, 'DD/MM/YYYY') as j46_dtfim,
                          to_char(j46_dtinc, 'DD/MM/YYYY') as j46_dtinc_dt
	            from iptuisen
	            inner join tipoisen 	  on j45_tipo = j46_tipo
	            inner join isenexe 	    on j47_codigo = j46_codigo
	            inner join proprietario on j01_matric = j46_matric
							inner join isenproc 	  on j61_codigo = j46_codigo
							inner join protprocesso on isenproc.j61_codproc = protprocesso.p58_codproc
	            left join iptuconstr    on j01_matric = j39_matric
	            ".($calv == true?"inner":"left")." join 
                                                   (
                                                     select 
                                                        j21_anousu,
                                                        j21_valor,
                                                        j21_matric
                                                     from 
                                                        iptucalv
                                                        where j21_anousu between $anoini and $anofin
                                                     UNION
                                                     select 
                                                        j08_anousu as j21_anousu,
                                                        j152_valor as j21_valor,
                                                        j151_matric as j21_matric
                                                     from
                                                        iptutaxacalv
                                                     inner join iptutaxanump on j151_codigo = j152_iptutaxanump
                                                     inner join iptucadtaxaexe on j08_iptucadtaxaexe = j151_iptucadtaxaexe
                                                     where j08_anousu between $anoini and $anofin
                                                  ) iptucalv on j21_matric = j01_matric
                                                   and j21_anousu = j47_anousu
                                                   and j21_valor < 0 ";
$sqlMain .= "     where j47_anousu between $anoini and $anofin $xdatas $xtipo $iptucalv) as x ";

$sqlMain .= "     group by	j46_matric,
  											j46_codigo,
												processo,
												z01_nome,
												j47_anousu,
												j45_descr,
												j46_perc,
												j21_valor,
												z01_compl,
												codpri,
												nomepri,
												j39_numero,
												tipo,
                                                anoIni,
                                                anoFim,
                                                j46_dtini,
                                                j46_dtfim,
                                                j46_dtinc_dt";
$sqlMain .= "      ) as y order by $order";

$sqltaxaSeparada = "SELECT j18_taxaseparada
                      FROM cfiptu
                     WHERE j18_anousu = 2019";

$result1 = db_query($sqltaxaSeparada);

// Verifica se a taxa esta separada do iptu
$sqlSeparaTaxa = '
    SELECT j18_taxaseparada as habilitataxa
    FROM cadastro.cfiptu
    WHERE j18_anousu = '. db_getsession('DB_anousu')  .'
    LIMIT 1
';
$rsSeparaTaxa = db_query($sqlSeparaTaxa);

// Variavel para controle de taxa separada
$separaTaxa = db_utils::fieldsMemory($rsSeparaTaxa, 0)->habilitataxa;

$anoSession = db_getsession('DB_anousu');

$sql = "
    SELECT
        tabrec.k02_codigo,
        tabrec.k02_descr,
        iptucadtaxa.j07_iptucadtaxa,
        iptucadtaxa.j07_descr,
        iptuisen.j46_matric,
        iptuisen.j46_dtinc,
        isentaxa.j56_perc,
        iptuisen.j46_perc,
        iptuisen.j46_codigo,
        iptuisen.j46_dtini,
        iptuisen.j46_dtfim,
        iptuisen.j46_hist,
        tipoisen.j45_descr,
        CASE WHEN p58_numero != '0' THEN cast(p58_numero||'/'||p58_ano as varchar)
             ELSE '0'
        END AS processo
    FROM iptuisen
    LEFT JOIN isentaxa
        ON isentaxa.j56_codigo = iptuisen.j46_codigo
    LEFT JOIN tabrec
        ON tabrec.k02_codigo = isentaxa.j56_receit
    LEFT JOIN iptucadtaxaexe
        ON iptucadtaxaexe.j08_tabrec = isentaxa.j56_receit AND iptucadtaxaexe.j08_iptucadtaxaexe = isentaxa.j56_iptucadtaxaexe
    LEFT JOIN iptucadtaxa
        ON iptucadtaxa.j07_iptucadtaxa = iptucadtaxaexe.j08_iptucadtaxa
    LEFT JOIN tipoisen
        ON iptuisen.j46_tipo = tipoisen.j45_tipo
    LEFT JOIN isenproc
        ON iptuisen.j46_codigo = isenproc.j61_codigo
    LEFT JOIN protprocesso 
        ON isenproc.j61_codproc = protprocesso.p58_codproc
    WHERE
        true $xdatas
    ORDER BY iptuisen.j46_dtini DESC;
";

$taxasMatricula = array();
$isencoes = array();

foreach(getRows($sql) as $taxa) {
    $taxasMatricula[$taxa['j46_matric']][$taxa['j46_codigo']][] = $taxa;
    $isencoes[$taxa['j46_codigo']] = [
        'tipo' => $taxa['j45_descr'],
        'dataInicio' => $taxa['j46_dtini'],
        'dataFinal' => $taxa['j46_dtfim'],
        'dataInclusao' => $taxa['j46_dtinc'],
        'porcentagem' => $taxa['j46_perc'],
        'historico' => $taxa['j46_hist'],
        'processo' => $taxa['processo']
    ];
}

$pdf = new PDF();
$pdf->Open();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(220);
$pdf->SetFont('Arial','',11);

$total     = 0;
$totvalor  = 0;
$matricula = 0;
$totalportipo = array();

$pdf->SetFont('Arial','B',7);

// títulos das coluna no relatório
$pdf->Cell(15,5,"Matrícula",1,0,"C",1);
$pdf->Cell(63,5,"Proprietario",1,0,"C",1);
$pdf->Cell(114,5,"Endereço da Matrícula",1,1,"C",1);
//$pdf->Cell(64,5,"",1,1,"C",1);

$pdf->Cell(15,5,"Exercício",1,0,"C",1);
$pdf->Cell(63,5,"Período",1,0,"C",1);
$pdf->Cell(42,5,"Tipo de Isenção",1,0,"C",1);
$pdf->Cell(28,5,"Código da Isenção",1,0,"C",1);
$pdf->Cell(25,5,"Data de Inclusão",1,0,"C",1);
$pdf->Cell(19,5,"Processo",1,1,"C",1);

$pdf->Setxy(10,45);

$pdf->Cell(15,5,"Imposto",1,0,"C",1);
$pdf->Cell(63,5,"Percentual",1,0,"C",1);
$pdf->Cell(114,5,"",1,0,"C",1);

$pdf->Setxy(10,50);

$pdf->cell(192,5,"Histórico",1,1,"C",1);

$altura = 43;
// quebra linha
$pdf->Ln(5);

$codigoIsencaoAnterior = 0;
 $semIsencoes = true;
 $matriculasPercorridas = array();
 foreach(getRows($sqlMain) as $row) {
   $semIsencoes = false;
   $matricula = $row['j46_matric'];

   if (!empty($matriculasPercorridas[$matricula])) {
    continue;
   }

   $matriculasPercorridas[$matricula] = true;

   $nome = $row['z01_nome'];
   $nomepri = $row['nomepri'];
   $numero = $row['j39_numero'];
   $complemento = $row['z01_compl'];
   $codigoIsencao = $row['j46_codigo'];
   $descricao = $row['j45_descr'];
   $valor = $row['j21_valor'];

   $pdf->SetFont('Arial','B',7);
   $pdf->cell(15,6,$matricula,"T",0,"L",0);
   $pdf->cell(92,6,substr($nome, 0, 45),"T",0,"L",0);
   $pdf->cell(85,6,substr(($nomepri!=""? $nomepri:"").($numero!=""?" - ".$numero:"").($complemento!=""?"/".$complemento:""),0,45),"T",1,"L",0);

   if ($codigoIsencaoAnterior != $codigoIsencao) {
       foreach ($taxasMatricula[$matricula] as $keyCodigoIsencao => $isencao) {
           $dataInclusao = date("d/m/Y", strtotime($isencoes[$keyCodigoIsencao]['dataInclusao']));
           $processo = $isencoes[$keyCodigoIsencao]['processo'];

           $pdf->SetFont('Arial', '', 7);

           $pdf->cell(15, 6, date("Y", strtotime($isencoes[$keyCodigoIsencao]['dataInicio'])) . ' - ' . date("Y", strtotime($isencoes[$keyCodigoIsencao]['dataFinal'])), "T", 0, "C", 0);
           $pdf->cell(68, 6, date("d/m/Y", strtotime($isencoes[$keyCodigoIsencao]['dataInicio'])) . ' - ' . date("d/m/Y", strtotime($isencoes[$keyCodigoIsencao]['dataFinal'])), "T", 0, "C", 0);
           $pdf->cell(42, 6, substr($descricao, 0, 20), "T", 0, "L", 0);
           $pdf->cell(20, 6, $keyCodigoIsencao, "T", 0, "C", 0);
           $pdf->cell(27,6,$dataInclusao,"T",0,"C",0);
           $pdf->cell(20,6,$processo,"T",1,"C",0);

           $pdf->cell(50.4, 6, "IPTU", "T", 0, "L", 0);
           $pdf->cell(63, 6, trim($isencoes[$keyCodigoIsencao]['porcentagem']), "T", 0, "L", 0);
           $pdf->cell(55, 6, "", "T", 0, "L", 0);
           $pdf->cell(24, 6, "", "T", 1, "C", 0);

           foreach ($isencao as $t) {
               if(empty($t['k02_codigo'])) continue;

               if (is_null($t['j07_iptucadtaxa'])) {
                   $descricao = $t['k02_descr'];
               } else {
                   $descricao = $t['j07_descr'];
               }

               $pdf->cell(50.4, 6, substr($descricao, 0 , 31), "T", 0, "L", 0);
               $pdf->cell(63, 6, $t['j56_perc'], "T", 0, "L", 0);
               $pdf->cell(55, 6, "", "T", 0, "L", 0);
               $pdf->cell(24, 6, "", "T", 1, "C", 0);
           }
           $pdf->MultiCell(192.3, 6, $isencoes[$keyCodigoIsencao]['historico'], "T", 1, "C", 0);
           $pdf->Ln(3);
       }

   }

   $total    += 1;
   $totvalor += $valor;

   if (!isset($totalportipo[$descricao][0])) {
       $totalportipo[$descricao][0]  = $valor;
   } else {
       $totalportipo[$descricao][0] += $valor;
   }

   $codigoIsencaoAnterior = $codigoIsencao;
 }

if ($semIsencoes) {
  db_redireciona('db_erros.php?fechar=true&db_erro=Não exitem isenções cadastradas para os parâmetros escolhidos');
  exit;
}

$pdf->AddPage();

$pdf->cell(80,5,"TOTALIZAÇÃO POR TIPO DE ISENÇÃO",1,1,"C",1);
$pdf->Ln(5);

$pdf->cell(60,5,"DESCRIÇÃO",1,0,"L",1);
$pdf->cell(20,5,"VALOR",1,1,"C",1);

$total_quant = 0;
foreach ($totalportipo as $k => $v) {

	$pdf->cell(60,5,$k,0,0,"L",0);
	$pdf->cell(20,5,db_formatar($v[0], 'f'),0,1,"R",0);
	$total_quant += $v[0];
}

$pdf->cell(60,5,"TOTAL",1,0,"L",1);
$pdf->cell(20,5,db_formatar($total_quant, 'f'),1,1,"R",1);

$pdf->Ln(5);
$pdf->Cell(95,6,"Total de Registros: ".$total ,"T",0,"L",0);
$pdf->Cell(90,6,'',"T",1,"R",0);

$pdf->Output();

?>
