<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009 DBSeller Servicos de Informatica
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
 *  02111-1308, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

require_once(modification("fpdf151/pdf.php"));
require_once(modification("libs/db_sql.php"));
require_once(modification("libs/db_utils.php"));

db_postmemory($HTTP_GET_VARS);

if ($mes != '') {
    $mes = $mes;
} else {
    $mes = date('m');
}
if ($ano != '' && strlen($ano) == 4) {
    $ano = $ano;
} else {
    $ano = date('Y');
}


$pdf = new PDF(); // abre a classe
$pdf->Open(); // abre o relatorio
$pdf->SetTextColor(0, 0, 0);
$imprime_header = true;

$total_dias = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

$where = 'where 1 = 1';

if ($regist != '') {
    $where .= " and rh01_regist = $regist";
}

$sql_ano = "select fc_anofolha(".db_getsession('DB_instit').") as anousu, fc_mesfolha(".db_getsession('DB_instit').") as mesusu";
$res_ano = db_query($sql_ano);
db_fieldsmemory($res_ano, 0);

if (isset($localtrab) && $localtrab != '') {
    $where .= " and rh55_estrut = '".$localtrab."'";
}

$where .= " and ( rh05_recis is null or rh05_recis >= ($ano||'-'||$mes||'-01')::date) ";
$head3 = "FOLHA DE PONTO";
$head4 = "MÊS : ".strtoupper(db_mes($mes));
$head5 = "ANO : ".$ano;

    $sql = "
 	select distinct rh02_regist,
        z01_nome,
		case when cgmfisico.z04_nomesocial = '' then ' ' when cgmfisico.z04_nomesocial is null then ' ' else cgmfisico.z04_nomesocial end as z04_nomesocial,
        rh37_descr,
        rh55_estrut ||' - '|| rh55_descr as local,
        r70_estrut ||' - '|| r70_descr as lota,
        rh04_descr
 		from rhpessoal
    	inner join rhpessoalmov on rh02_regist = rh01_regist
                            and rh02_anousu = $anousu
                            and rh02_mesusu = $mesusu
                            and rh02_instit = ".db_getsession('DB_instit')."
    	left join rhfuncao     	on rh37_funcao = rh02_funcao and rh37_instit = ".db_getsession('DB_instit')."
    	inner join cgm         	on rh01_numcgm = z01_numcgm
		left join cgmfisico on cgmfisico.z04_numcgm = cgm.z01_numcgm
    	left join rhpescargo 	 	on rh20_seqpes  = rh02_seqpes
		left join rhcargo      	on rh04_codigo = rh20_cargo and rh04_instit = rh20_instit
    	left join rhpeslocaltrab  on rhpeslocaltrab.rh56_seqpes = rhpessoalmov.rh02_seqpes and rh56_princ is true
    	left join rhlocaltrab  	on rhlocaltrab.rh55_codigo = rhpeslocaltrab.rh56_localtrab and rhlocaltrab.rh55_instit = rhpessoalmov.rh02_instit
    	left join rhlota          on r70_codigo = rh02_lota
    	left join rhpesrescisao   on rh02_seqpes = rh05_seqpes
 	$where
	order by z01_nome";

$result = db_query($sql);
$xxnum = pg_num_rows($result);

if ($xxnum == 0) {
    db_redireciona('db_erros.php?fechar=true&db_erro=Não existem Códigos cadastrados no período de '.$mes.' / '.$ano);
}

// Verificação de feriados
$sqlFeriados = "select distinct rh53_descr, extract( day from r62_data) as dia
			from calendf
			inner join rhcadcalend on r62_calend = rh53_calend
			where rh53_instit = ".db_getsession("DB_instit")."
			and extract( year from r62_data ) = $ano
			and extract( month from r62_data  ) = $mes
			order by 2";

    $mostraFeriados = db_query($sqlFeriados);
    $qtdFeriados = pg_num_rows($mostraFeriados);

    $feriados = array();

for ($y=0; $y < $qtdFeriados; $y++) {
    $retorno = db_fieldsmemory($mostraFeriados, $y);
    $feriados[$dia] = $rh53_descr;
}

for ($a=0; $a < $xxnum; $a++) {
    db_fieldsmemory($result, $a);

    $pdf->AddPage('P');

    $pdf->SetXY(10, 30);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(18, 15, 'Funcionário:', 0, 0, "L");
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(77, 15, $z01_nome, 0, 0, "L");
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(17, 15, 'Nome Social:', 0, 0, "L");
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(53, 15, $z04_nomesocial, 0, 0, "L");
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(13, 15, 'Matrícula:', 0, 0, "L");
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(13, 15, $rh02_regist, 0, 1, "L");

    $pdf->SetXY(10, 34);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(14, 15, 'Lotação:', 0, 0, "L");
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(110, 15, $lota, 0, 0, "L");
    $pdf->SetFont('Arial', 'B', 7);

    //if (db_getsession('DB_instit') == 1) {
    //   $cargofunc = 'Função:';
    //   $funccargdes = $rh04_descr;
    //    }else if (db_getsession('DB_instit') == 1){
    //   $cargofunc = 'Função';
    //       $funccargdes = $rh04_descr;
    //}else{
    //       $cargofunc = 'Cargo';
    //       $funccargdes = $rh37_descr;
    //}

    $cargofunc = 'Função:';
    $funccargdes = $rh04_descr;
    $cargofunc = 'Função';

    $funccargdes = $rh04_descr;
    $cargofunc = 'Cargo:';
    $funccargdes = $rh37_descr;

    $pdf->Cell(11, 15, $cargofunc, 0, 0, "L");
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(10, 15, $funccargdes, 0, 1, "L");

    $pdf->SetXY(10, 38);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(27, 15, 'Local de Trabalho:', 0, 0, "L");
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(97, 15, $local, 0, 0, "L");
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(20, 15, 'Competência:', 0, 0, "L");
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(40, 15, strtoupper(db_mes($mes)).' / '.$ano, 0, 1, "L");

    $pdf->SetXY(10, 43);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(13, 15, 'Entrada:', 0, 0, "L");
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(40, 9, '', "B", 0, "L");
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(10, 15, 'Saída:', 0, 0, "L");
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(40, 9, '', "B", 0, "L");
    $pdf->Cell(36, 15, '', 9, 0, "L");

    $pdf->SetXY(10, 53);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetFillColor(180);
    $pdf->Cell(15, 8, "Dia", 1, 0, "C", 1);
    $pdf->Cell(30, 8, "Entrada", 1, 0, "C", 1);
    $pdf->Cell(30, 8, "Saída", 1, 0, "C", 1);
    $pdf->Cell(30, 8, "Entrada", 1, 0, "C", 1);
    $pdf->Cell(30, 8, "Saída", 1, 0, "C", 1);
    $pdf->Cell(55, 8, "", 1, 0, "L", 1);
    $pdf->SetXY(145, 54);
    $pdf->multicell(55, 3, "Obs.: Caso necessário, utilizar quadro ao pé da folha.", "", "L", 0);
    $pdf->ln(1);

    for ($i=1; $i <= $total_dias; $i++) {
        $diasemana = date("w", mktime(0, 0, 0, $mes, $i, $ano));

        switch ($diasemana) {
            case "0":
                $diasemana = "DOMINGO";
                break;
            case "6":
                $diasemana = "SÁBADO" ;
                break;
            default:
                $diasemana = "";
        }

        $pdf->Cell(15, 5, $i, 1, 0, "C");

        if (! isset($feriados[$i])) {
            $pdf->Cell(30, 5, $diasemana, 1, 0, "C");
            $pdf->Cell(30, 5, $diasemana, 1, 0, "C");
            $pdf->Cell(30, 5, $diasemana, 1, 0, "C");
            $pdf->Cell(30, 5, $diasemana, 1, 0, "C");
        } else {
            $pdf->SetFont('Arial', 'B', 6);
            $pdf->Cell(30, 5, ($diasemana!=""?$diasemana:$feriados[$i]), 1, 0, "C");
            $pdf->Cell(30, 5, ($diasemana!=""?$diasemana:$feriados[$i]), 1, 0, "C");
            $pdf->Cell(30, 5, ($diasemana!=""?$diasemana:$feriados[$i]), 1, 0, "C");
            $pdf->Cell(30, 5, ($diasemana!=""?$diasemana:$feriados[$i]), 1, 0, "C");
            $pdf->SetFont('Arial', 'B', 8);
        }

        $pdf->Cell(55, 5, "", 1, 1, "L");
    }

    $pdf->SetXY(10, 220);
    $pdf->Cell(10, 10, "", "LBRT", 0, "C");
    $pdf->Cell(30, 10, " Total de Faltas", 0, 0, "L");
    $pdf->SetX(70);
    $pdf->Cell(35, 9, " Assinatura do Servidor:", 0, 0, "L");
    $pdf->Cell(95, 6, "", "B", 0, "L");
    $pdf->SetXY(70, 227);
    $pdf->Cell(56, 9, " Assinatura do Secretário/Responsável:", 0, 0, "L");
    $pdf->Cell(74, 6, "", "B", 0, "L");
    $pdf->SetXY(70, 234);
    $pdf->Cell(37, 9, " Assinatura do Abonador:", 0, 0, "L");
    $pdf->Cell(93, 6, "", "B", 0, "L");
    $pdf->SetXY(70, 241);
    $pdf->Cell(37, 9, " Visto do Coordenador(ADPP):", 0, 0, "L");

    $pdf->SetXY(10, 235);
    $pdf->Cell(10, 10, "", "LBRT", 0, "C");
    $pdf->Cell(30, 10, " Total de Dias de Frequência", 0, 0, "L");

    $pdf->SetXY(10, 244);
    $pdf->ln();
    $pdf->Cell(190, 15, "", "LBRT", 0, "C");
    $pdf->SetXY(10, 252);
    $pdf->Cell(19, 10, 'OBS./Relato:', "", 0, 'L');
    $pdf->Cell(170, 7, '', "B", 1, 'L');
    $pdf->SetX(12);
    $pdf->Cell(187, 6, '', "B", 1, 'L');

    $pdf->SetY(270);
    $pdf->SetFont('Arial', '', 7);
    $pdf->multicell(190, 3, "Os espaços de entrada e saída de cada turno deverão ser assinados individualmente conforme o horário de pautado de Legislações pertinentes para cada servidor.", "", "L", 0);
}
$pdf->output();
