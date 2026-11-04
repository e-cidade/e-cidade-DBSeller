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
include(modification("fpdf151/pdf1.php"));
include(modification("classes/db_fis_fiscal_estendida_classe.php"));
include(modification("classes/db_fis_fiscaltipo_classe.php"));
include(modification("classes/db_fis_fiscalusuario_classe.php"));
include(modification("classes/db_fis_fiscalocal_classe.php"));
include(modification("classes/db_fis_fiscalmatric_classe.php"));
	include(modification("classes/db_db_docparag_classe.php"));
	$cldb_docparag   = new cl_db_docparag;
	$clfiscal        = new cl_fis_fiscal_estendida;
	$clfiscaltipo    = new cl_fis_fiscaltipo;
	$clfiscalusuario = new cl_fis_fiscalusuario;
	$clfiscalocal    = new cl_fis_fiscalocal;
	$clfiscalmatric  = new cl_fis_fiscalmatric;
	$clrotulo        = new rotulocampo;

	parse_str($_SERVER['QUERY_STRING']);

	//-----------------------VALIDA A EXISTÊNCIA DA VARIÁVEL CODFISCAL--------------------

	if (isset($codfiscal) and ($codfiscal == '' or is_null($codfiscal))) {
		db_redireciona('db_erros.php?fechar=true&db_erro=Nenhum parâmetro foi enviado para a consulta. Verifique o formulário!');
	}

	//------------------------------------------------------------------------------------

	//-----------------------BUSCA AS INFORMAÇÕES DA NOTIFICAÇÃO--------------------------

	$result = $clfiscal->sql_record($clfiscal->sql_query_info($codfiscal,"*"));
	if ($clfiscal->numrows>0){
		db_fieldsmemory($result,0,true);
	}else{
		db_redireciona('db_erros.php?fechar=true&db_erro=Não existe registro cadastrado.');
		exit;
	}
	$tipoEnd = ((isset($intimacao) && $intimacao == 1) ? 'I'  :  'N');
	//------------------------E DISPONIBILIZA AS VARIAVESI PARA SER USADA NOS PARAGRAFOS COM OS NOMES ABAIXO----
	$identificacao = (trim($z01_nomecomple) != ''?$z01_nomecomple:$z01_nome);
	$cpf           = $z01_cgccpf;
	$hora          = $y30_hora;
	$arr_data      = explode("/",$y30_data);
	$dia           = $arr_data[0];
	$mes           = db_mes($arr_data[1]);
	$ano           = $arr_data[2];
	$prazorec      = $y30_prazorec;
	$prazorec2     = @db_formatar($y30_prazorec,"d");
	$observacao    = $y30_obs;
	//---------------------------------------------------------------------------------------

	//-----------------------BUSCA FISCAIS DA NOTIFICAÇÃO--------------------------
    $sqlFiscal = $clfiscalusuario->sql_query("","","nome",""," y38_codnoti = $codfiscal");
    $rsFiscal = db_query($sqlFiscal);
    $fiscais = db_utils::getCollectionByRecord($rsFiscal);
    $fiscalNome1 = (!empty($fiscais)) ? $fiscais[0]->nome : '';
    $fiscalNome2 = (!empty($fiscais) && isset($fiscais[1])) ? $fiscais[1]->nome : '';
    //---------------------------------------------------------------------------------------

	//-----------------------BUSCA AS PROCEDENCIAS DA NOTIFICAÇÃO--------------------------
	//------------------------E DISPONIBILIZA PARA SER USADA NOS PARAGRAFOS COM OS NOMES ABAIXO----
	$procedencia = "";
	$descrobs = "";
	$descrobscomquebra = "";
	$vir = "";

	$result_proced=$clfiscaltipo->sql_record($clfiscaltipo->sql_query($codfiscal,
									  null,
																	  "*",
																	  null,
																	  " y31_codnoti = ".$codfiscal." and y30_instit = ".db_getsession('DB_instit') ));
	for($w=0;$w<$clfiscaltipo->numrows;$w++){
		db_fieldsmemory($result_proced,$w,true);
		$procedencia .= $vir.$y29_descr;
		$descrobs .= $vir.$y29_descr_obs;
		$descrobscomquebra .= $y29_descr_obs . "\n";
		$vir = ", ";
	}
	//---------------------------------------------------------------------------------------

	$rua = "";
	$ruacodigo = "";
	$bairro = "";
	$complemento = "";
$numero = "";

//-----------------------BUSCA ENDEREÇO REGISTRADO DA NOTIFICAÇÃO--------------------------
$result_ender=$clfiscalocal->sql_record($clfiscalocal->sql_query($codfiscal));
if ($clfiscalocal->numrows>0){
	db_fieldsmemory($result_ender,0,true);
	$rua         = $j14_nome;
	$ruacodigo   = $j14_codigo . " - " . $j14_nome;
	$bairro      = $j13_descr;
	$complemento = $y12_compl;
	$numero      = $y12_numero;
}else{
	 $rs2EndPecas = db_query( "select end01_rua as j14_nome, end01_numero as y12_numero,end01_compl as  y12_compl,end01_bairro as j13_descr
                                from fiscalizacao.fis_enderecopecas where end01_codpeca = {$codfiscal} and end01_tipopeca = '$tipoEnd' " );
   if( pg_num_rows( $rs2EndPecas ) > 0 ){
      db_fieldsmemory( $rs2EndPecas,0 );
   }
	$rua         = $j14_nome;
	$ruacodigo   = $j14_codigo . " - " . $j14_nome;
	$bairro      = $j13_descr;
	$complemento = $y12_compl;
	$numero      = $y12_numero;
}

$bql   = "";
$setor = "";
$result_sql=$clfiscalmatric->sql_record($clfiscalmatric->sql_query(null,"j34_setor, j34_quadra,j34_lote,j30_descr",null," y35_codnoti= {$codfiscal} and  y30_instit = ".db_getsession('DB_instit') ));
if ($clfiscalmatric->numrows>0){
	db_fieldsmemory($result_sql,0,true);
	$bql   = $j34_setor . "/" . $j34_quadra . "/" . $j34_lote;
	$setor = $j34_setor . " - " . trim($j30_descr);
}
if(isset($intimacao)){
	$paragrafo = db_query("select * from fiscalizacao.fis_paragrafointimacao where pl11_intimacao =".$codfiscal);
}else{
	$paragrafo = db_query("select * from fiscalizacao.fis_paragrafointimacao where pl11_intimacao =".$codfiscal);
}
//---------------------------------------------------------------------------------------
$sqlhead = "select db02_texto
			   from db_documento
			    	inner join db_docparag on db03_docum = db04_docum
        			inner join db_tipodoc on db08_codigo  = db03_tipodoc
		     		inner join db_paragrafo on db04_idparag = db02_idparag
			 where db03_tipodoc = 1017 and db03_instit = " . db_getsession("DB_instit")." order by db04_ordem ";
$reshead = db_query($sqlhead);

if ( pg_num_rows($reshead) == 0 ) {
//     $head1 = 'Departamento de Fazenda';
     $head1 = 'SECRETARIA DE FINANÇAS';
}else{
     db_fieldsmemory( $reshead, 0 , true);
     $head1 = $db02_texto;
}
// Ticket 108335
$getLabel  = ((isset($intimacao) && $intimacao == 1) ? 'Intimação Fiscal' : 'Notificação Fiscal');
$getLabel2 = "Sujeito Passivo";
// -------------

class pdf2 extends pdf1 {

	function Header() {
		$sql = "select nomeinst,
		             cgc,
		             trim(ender)||','||trim(cast(numero as text)) as ender,
		             upper(munic) as munic,
		             uf,
		             telef,
		             email,
		             url,
		             logo,
		             db12_extenso
		      from db_config
		             inner join db_uf on db12_uf = uf
		      where codigo = ".db_getsession("DB_instit");
		$result = db_query($sql);
		global $nomeinst;
		global $ender;
		global $munic;
		global $cgc;
		global $bairro;
		global $uf;
		global $db12_extenso;
		global $logo;

		db_fieldsmemory($result,0);
		$db12_extenso = pg_fetch_result($result,0,"db12_extenso");
		/// seta a margem esquerda que veio do relatorio
		$S = $this->lMargin;
		$this->SetLeftMargin(10);
		$Letra = 'Times';

		$posini = ($this->w/6)-15;

		//$this->Image("imagens/files/logo_boleto.png",$posini,8,20);
		$this->Image('imagens/files/'.$logo,$posini,8,20);
		$this->Ln(1);
		$this->SetFont($Letra,'',10);
		$this->MultiCell(0,4,$db12_extenso,0,"C",0);
		$this->SetFont($Letra,'B',13);
		$this->MultiCell(0,6,$nomeinst,0,"C",0);
		$this->SetFont($Letra,'B',12);
		$this->MultiCell(0,4,@$GLOBALS["head1"],0,"C",0);
		$this->Ln(10);
		$this->SetLeftMargin($S);

		global $codfiscal;
		global $getLabel;
		global $getLabel2;
		global $p58_numero;
		global $descrdepto;
		global $identificacao;
		global $numero;
		global $rua;
		global $bairro;
		global $cpf;
		global $codigo;
		global $intimacao;

		$alt = 4;
		$this->setY(33);
		$this->SetFont('Arial','B',10);
		$this->Cell(0,$alt,mb_strtoupper($getLabel).' NÚMERO: '.$codfiscal,0,1,"C");
		$this->SetFont('Arial','',9);
		$this->Cell(0,$alt,'Órgão: '.$descrdepto,0,1,"C");
		$this->SetFont('Arial','B',11);
		$this->Cell(0,$alt,(($p58_numero != '') ? 'Número do Processo: '.$p58_numero.'  '  : ''),0,1,"C");

		$this->setY(46);
		$this->SetFont('Arial','B',10);
		$this->Cell(0,$alt,mb_strtoupper('Identificação do '.$getLabel2),0,1,"L");

		$this->SetFont('Arial','',8);
		$this->Cell(0,$alt,'Nome / Razão Social: '.$identificacao,0,1,"L");
		$this->Cell(0,$alt,'Endereço: '.$rua.' '.(($numero != '') ? 'Numero: '.$numero : '').' Bairro: '.$bairro,0,1,"L");
		$this->Cell(0,$alt,'CNPJ / CPF: '.$cpf,0,1,"L");
		$this->Cell(0,$alt,'Inscrição Municipal: '.$codigo,0,1,"L");

		$this->ln($alt);
		$this->SetFont('Arial','B',10);
		$this->Cell(0,$alt,mb_strtoupper($getLabel),0,1,"C");

		$rectBreak = ($intimacao == 1) ? 20 : 0;
		if($this->PageNo() == 1){
			$this->SetAutoPageBreak(true,(95+$rectBreak));
			$this->Rect(10,75,190,(130-$rectBreak));
		}else{
			$this->SetAutoPageBreak(true,(35+$rectBreak));
			$this->Rect(10,75,190,(190-$rectBreak));
		}
		$this->setY(77);
	}

	function Footer() {
		$S = $this->lMargin;
		$this->SetLeftMargin(10);
		global $conn;
		global $result;
		global $url;
        global $fiscalNome1;
        global $fiscalNome2;
		//Position at 1.5 cm from bottom

		// municipio para campos das assinaturas
		$municipio     = InstituicaoRepository::getInstituicaoByCodigo(db_getsession("DB_instit"));
        $municipio_ass = ucwords(mb_strtolower($municipio->getMunicipio()));

		$this->SetFont('Arial','',5);
		$this->text(10,289,'Base: '.@$GLOBALS["DB_NBASE"]);
		$this->SetFont('Arial','I',5);
		$this->SetY(-10);
		$nome = @$GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"];
		$nome = substr($nome,strrpos($nome,"/")+1);
		$result_nomeusu = db_query("select nome as nomeusu from db_usuarios where id_usuario =".db_getsession("DB_id_usuario"));
		if (pg_num_rows($result_nomeusu)>0){
		$nomeusu = pg_fetch_result($result_nomeusu,0,0);
		}
		if (isset($nomeusu)&&$nomeusu!=""){
		$emissor = $nomeusu;
		}else{
		$emissor = @$GLOBALS["DB_login"];
		}

		/*
		* Modificação para exibir o caminho do menu
		* na base do relatório
		*/
		$sSqlMenuAcess = "SELECT fc_montamenu(funcao) as menu from db_itensmenu where id_item =".db_getsession("DB_itemmenu_acessado");
		$rsMenuAcess   = db_query($conn,$sSqlMenuAcess);
		$sMenuAcess    = substr(pg_fetch_result($rsMenuAcess,0,"menu"),0,50);

		if ( db_getsession("DB_id_usuario") == 1070 ) {
		$this->Cell(0,10,$url. '  '.$sMenuAcess.'  '.$nome.'  Exercício: '.db_getsession("DB_anousu").
		'   Data: '.date("d-m-Y",db_getsession("DB_datausu"))." - ".date("H:i:s"),"T",0,'L');
		} else {
		$this->Cell(0,10,$url. '  '.$sMenuAcess.'  '.$nome.'  Emissor: '.substr(ucwords(strtolower($emissor)),0,30).'  Exercício: '.db_getsession("DB_anousu").
		'   Data: '.date("d-m-Y",db_getsession("DB_datausu"))." - ".date("H:i:s"),"T",0,'L');
		}

		$this->Cell(0,10,'Página '.$this->PageNo().' de {nb}',0,1,'R');
		$this->SetLeftMargin($S);

		global $textoIntiNoti;
		global $nomefiscal;
		global $matricula;
		global $intimacao;
		$alt = 4;

		if($this->PageNo() == 1){

			if($intimacao == 1){
				$this->SetY(188);
				$this->SetFont('Arial','',7);
				$this->MultiCell(0,$alt-1,trim($textoIntiNoti),0,"L",0);
				$this->SetFont('Arial','',8);
			}else{
				$this->SetY(207);
				$this->SetFont('Arial','',8);
				$this->MultiCell(0,$alt,trim($textoIntiNoti),0,"L",0);
			}

			$this->ln(2);
			$this->Cell(188,$alt,"$municipio_ass,",0,1,"L");
			$this->Cell(188,$alt,'Local de Lavratura: ___________________________________',0,1,"L");

			$this->Rect(10,228,90,30);
			$this->Rect(100,228,90,30);

			$this->SetFont('Arial','',7);
			$this->SetY(228);
			$this->Cell(90,$alt+6,"Em             de                                           de 20",0,0,"L");
			$this->Cell(90,$alt+6,"Em             de                                           de 20",0,1,"L");

            $this->ln(4);
            $this->Cell(90, 27, "Fiscal: $fiscalNome1", 0, 0);
            $this->Cell(90, 27, "Fiscal: $fiscalNome2", 0, 1);

			$this->SetY(258);
			$this->Cell(188,$alt,'CIÊNCIA DO SUJEITO PASSÍVO / RESPONSÁVEL',                                                     0,1,"L");

			if (isset($intimacao) && $intimacao == 1){
				$this->Cell(188,$alt,'Declaro-me ciente desta Intimação e seus anexos dos quais recebi cópia.',               0,1,"L");
			}else{
				$this->Cell(188,$alt,'Declaro-me ciente desta Notificão e seus anexos dos quais recebi cópia.',               0,1,"L");
			}

			$this->Cell(130,($alt+3),'Nome: _______________________________________________________________________________________',0,0,"L");
			$this->Cell(48,($alt+3),'Cargo: ___________________________________',                                                     0,1,"L");

			$this->Cell(50,($alt+3),'CPF: _______________________________',                                                           0,0,"L");
			$this->Cell(85,($alt+3),"   $municipio_ass, _____ de _______________________________ de __________",                              0,0,"L");
			$this->Cell(35,($alt+3),'Hora: ____:____',                                                                               0,1,"L");

			$this->Cell(190,($alt+3),'Assinatura: _________________________________________________________________________________',0,1,"L");
		}else{
			if($intimacao == 1){
				$this->SetY(247);
				$this->SetFont('Arial','',7);
				$this->MultiCell(0,$alt-1,trim($textoIntiNoti),0,"L",0);
				$this->SetFont('Arial','',8);
			}else{
				$this->SetY(267);
				$this->SetFont('Arial','',8);
				$this->MultiCell(0,$alt,trim($textoIntiNoti),0,"L",0);
			}
			$this->SetY(280);
			$this->Cell(190,($alt+3),'Assinatura: _________________________________________________________________________________',0,1,"L");
		}
	}
}

$result  = $cldb_docparag->sql_record($cldb_docparag->sql_query("","","db_docparag.*,db02_texto,db02_espaca,db02_alinha,db02_inicia","db04_ordem","db03_tipodoc=7"));
$numrows = $cldb_docparag->numrows;
if ($numrows==0){
	db_redireciona('db_erros.php?fechar=true&db_erro=Não existe documento cadastrado.');
	exit;
}

$pdf = new PDF2();
$pdf->Open();
$pdf->AliasNbPages();
$pdf->setfillcolor(235);
$pdf->Addpage();
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(220);

$textoIntiNoti = "";

if (isset($intimacao) && $intimacao == 1) {
    $documento = 'FISCAL INTIMAÇÃO';
    $sqlParagrafosIntimacao = $cldb_docparag->sql_query(null, null, 'db02_descr, db02_texto', 'db04_ordem', "db03_descr = '{$documento}' and db03_tipodoc = 7");
    $rsParagrafosIntimacao  = db_query($sqlParagrafosIntimacao);

    if ($rsParagrafosIntimacao) {
        $paragrafoCiencia = pg_fetch_all($rsParagrafosIntimacao);

        foreach ($paragrafoCiencia as $parag) {
            $textoIntiNoti .= $parag['db02_texto'] . "\n";
        }
    }
}

$pdf->SetFont('Arial','',9);

db_fieldsmemory($paragrafo,0);
$pdf->SetLeftMargin(12);
$pdf->SetRightMargin(12);
$pdf->MultiCell(0,5,trim($pl11_texto),0,"J",0);

$pdf->Output();

?>
