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

require_once(modification("fpdf151/pdf.php"));
require_once(modification("libs/db_sql.php"));
require_once(modification("classes/db_cgs_und_classe.php"));

$clcgs_und = new cl_cgs_und ( );
$clcgs_und->rotulo->label ();
$clrotulo = new rotulocampo ( );

parse_str ( $_SERVER ['QUERY_STRING'] );
db_postmemory ($_GET);


$where1 = " where 1=1 ";

$head1 = "SELEÇÃO PARA CONTROLE DE CGSs MÚLTIPLOS POR";

if (isset ( $nome ) && trim ( $nome ) != "") {
	$where1 .= " and trim(z01_v_nome) like '" . $nome . "%' ";
	$head3 = "Nome: ".$nome;
}

if (isset ( $nome_mae )  && trim ( $nome_mae ) != "") {
	$where1 .= " and trim(z01_v_mae) like '" . $nome_mae . "%' ";
	$head4 = "Nome da mãe: ".$nome_mae;
}

if (isset ( $nascimento ) && trim ( $nascimento ) != "") {
	$where1 .= " and z01_d_nasc = " . "'$nascimento'";
	$head5 = "Data de nascimento: ".$nascimento;
}

$sqlMultiplos = "select z01_v_nome,
                        count(z01_i_cgsund) 
                   from  cgs_und  
			    $where1 
			  group by z01_v_nome having count(z01_i_cgsund)>1 
			  order by z01_v_nome";

$result_cgs_und = db_query($sqlMultiplos);
$numrows_cgs_und = pg_num_rows($result_cgs_und);
if ($numrows_cgs_und == 0) {
	db_redireciona ( 'db_erros.php?fechar=true&db_erro=Não existem múltiplos no cgs_und com os dados informados.' );
}

$pdf = new PDF ( );
$pdf->Open ();
$pdf->AliasNbPages ();
$pdf->setfillcolor ( 235 );
$alt = 4;
$troca = 0;
$total = 0;

$z01_cgccpf_setado = isset ( $z01_v_cgccpf );

for($i = 0; $i < $numrows_cgs_und; $i ++) {
	
	db_fieldsmemory ( $result_cgs_und, $i );
	
	$where =  "trim(z01_v_nome)="."'$z01_v_nome'";		
	$where .= " and  case when s115_c_cartaosus <> '' then s115_c_tipo = 'D' else 1=1 end ";
	$campos = "z01_i_cgsund,z01_v_nome,trim(z01_v_cgccpf) as z01_v_cgccpf,z01_v_ender,z01_i_numero,";
	$campos .= "z01_v_cep,z01_d_nasc,z01_v_mae,z01_v_bairro,z01_v_munic,z01_v_uf,s115_c_cartaosus as cns";
	
	$result_dadoscgs_und = db_query($clcgs_und->sql_query ( null, $campos, "z01_i_cgsund", $where ) );
	$numrows_dadoscgs_und = pg_num_rows($result_dadoscgs_und);

	for($iii = 0; $iii < $numrows_dadoscgs_und; $iii ++) {
		db_fieldsmemory ( $result_dadoscgs_und, $iii );
		if (isset ( $z01_v_cgccpf ) && strlen ( $z01_v_cgccpf ) == 11) {
			$z01_v_cgccpf = db_formatar ( $z01_v_cgccpf, "cpf" );
		} else if (isset ( $z01_v_cgccpf ) && strlen ( $z01_v_cgccpf ) == 14) {
			$z01_v_cgccpf = db_formatar ( $z01_v_cgccpf, "cnpj" );
		}
		$z01_v_cep1 = "";
		$z01_v_cep2 = "";
		if (isset ( $z01_v_cep ) && trim ( $z01_v_cep ) != "") {
			$z01_v_cep1 = substr ( $z01_v_cep, 0, 5 );
			$z01_v_cep2 = substr ( $z01_v_cep, 5, 3 );
		}
		$muda_pag = false;
		if ($pdf->gety () > $pdf->h - 32 || $troca == 0) {
			if ($pdf->gety () > $pdf->h - 32) {

				$muda_pag = true;
			}
			$pdf->addpage ( "L" );
			$pdf->setfont ( 'arial', 'b', 8 );

			$pdf->cell ( 15, $alt, $RLz01_i_cgsund, 1, 0, "C", 1 );
			$pdf->cell ( 50, $alt, $RLz01_v_nome, 1, 0, "C", 1 );
			$pdf->cell ( 18, $alt, $RLz01_d_nasc, 1, 0, "C", 1 );
			$pdf->cell ( 20, $alt, $RLz01_v_cgccpf, 1, 0, "C", 1 );
			$pdf->cell ( 22, $alt, "CNS", 1, 0, "C", 1 );
			$pdf->cell ( 50, $alt, $RLz01_v_mae, 1, 0, "C", 1 );
			$pdf->cell ( 50, $alt, $RLz01_v_ender, 1, 0, "C", 1 );					
			$pdf->cell ( 30, $alt, $RLz01_v_bairro, 1, 0, "C", 1 );
			$pdf->cell ( 30, $alt, $RLz01_v_munic, 1, 1, "C", 1 );
			$pdf->ln ( 3 );
			$troca = 1;
		}

		if ($iii == 0 || $muda_pag == true) {

			$pdf->setfont ( 'arial', 'b', 7 );
			$pdf->cell ( 235, $alt,"", "LT", 0, "L", 1 );
			$pdf->cell ( 10, $alt, "", "T", 0, "R", 1 );
			$pdf->cell ( 15, $alt, "Registros:", "LT", 0, "L", 1 );
			$pdf->cell ( 25, $alt, @$count, "TR", 1, "R", 1 );			
		}
		
		$pdf->setfont ( 'arial', '', 7 );
		$pdf->cell ( 15, $alt, $z01_i_cgsund, 1, 0, "C", 0 );
		$pdf->cell ( 50, $alt, substr ( $z01_v_nome, 0, 31 ), 1, 0, "L", 0 );
		$pdf->cell ( 18, $alt, db_formatar($z01_d_nasc,'d'), 1, 0, "L", 0 );
		$pdf->cell ( 20, $alt, $z01_v_cgccpf, 1, 0, "L", 0 );
		$pdf->cell ( 22, $alt, $cns, 1, 0, "L", 0 );
		$pdf->cell ( 50, $alt, strlen($z01_v_mae) > 28  ? substr($z01_v_mae,0,28).'..':$z01_v_mae, 1, 0, "C", 0 );
		$endereco =  strlen($z01_v_ender) > 25  ? substr($z01_v_ender,0,25).'..':$z01_v_ender;
		$endereco .= ", " . $z01_i_numero;
		$pdf->cell ( 50, $alt, $endereco, 1, 0, "L", 0 );
		$pdf->cell ( 30, $alt, substr ( $z01_v_bairro, 0, 19 ), 1, 0, "L", 0 );
		$pdf->cell ( 30, $alt, substr ( $z01_v_munic, 0, 16 ) . "/" . $z01_v_uf, 1, 1, "L", 0 );
		$total ++;
	}
	if ($i + 1 != $numrows_cgs_und) {
		$pdf->ln ( 3 );
	}
}
$pdf->setfont ( 'arial', 'b', 8 );
$pdf->cell ( 275, $alt, 'TOTAL DE REGISTROS ENCONTRADOS  :  ' . $total, "T", 0, "L", 0 );
$pdf->Output ();
?>