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
include(modification("fpdf151/pdf.php"));
include(modification("libs/db_sql.php"));
include(modification("libs/db_utils.php"));
include(modification("classes/db_inicial_classe.php"));
$clinicial = new cl_inicial;
$clinicial->rotulo->label();

$clrotulo = new rotulocampo;
$clrotulo->label('v51_certidao');
$clrotulo->label('v70_codforo');
$clrotulo->label('v70_vara');
$clrotulo->label('v58_numcgm');
$clrotulo->label('v56_codsit');
$clrotulo->label('z01_nome');
$instit = db_getsession('DB_instit');
$mostra = false;
// parse_str($HTTP_SERVER_VARS['QUERY_STRING']);

db_postmemory($_GET);

//echo "data = $xdata";
$db_where = " v50_instit = ".db_getsession('DB_instit');
$db_ordem = "";
$and = "";
$aTotValOrigem = array();
$aTotalAno = array();
$aIniciais     = array();

$aOrigens = explode("|",$origem);
if (isset($nvalminacao) && $nvalminacao != ""){
  $nValorMinAcao = $nvalminacao;
}
if (isset($nvalmaxacao) && $nvalmaxacao != ""){
  $nValorMaxAcao = $nvalmaxacao;
}
if (isset($nvalminatu) && $nvalminatu != ""){
  $nValorMinAtu  = $nvalminatu;
}
if (isset($nvalmaxatu) && $nvalmaxatu != ""){
  $nValorMaxAtu  = $nvalmaxatu;
}

if($selSituacao == 1){
	$db_where .= " and v50_situacao = 1 ";
}else if($selSituacao == 2){
	$db_where .= " and v50_situacao = 2 ";
}


if (isset($numcgm)&&$numcgm!=""){
  $db_where .= " and inicialnomes.v58_numcgm = $numcgm ";
  $and = " and ";
}

if (isset($codsit)&&$codsit!=""){
  $db_where .= " and inicialmov.v56_codsit = $codsit ";
  $and = " and ";
}

if (isset($advog)&&$advog!=""){
  $db_where .= " and inicial.v50_advog = $advog ";
  $and = " and ";
}

if (isset($codvara)&&$codvara!=""){
  $db_where .= " and processoforo.v70_vara = $codvara ";
  $and = " and ";
}

if ($listar=='p'){
  $db_where .= " and processoforo.v70_codforo is not null ";
  $and = " and ";
}else if ($listar=='n'){
  $db_where .=  " and processoforo.v70_codforo is null ";
  $and = " and ";
}
//echo "<br> dataini = $dataini datafim = $datafim";

if (($dataini != "--") && ($datafim != "--")) {
  $db_where .= " and v50_data  between '$dataini' and '$datafim' ";
  $dataini = db_formatar($dataini, "d");
  $datafim = db_formatar($datafim, "d");
  $info = "De $dataini até $datafim.";
}else if ($dataini != "--") {
  $db_where .= " and v50_data >= '$dataini' ";
  $dataini = db_formatar($dataini, "d");
  $info = "Apartir de $dataini.";
}else if ($datafim != "--") {
  $db_where .= " and v50_data <= '$datafim' ";
  $datafim = db_formatar($datafim, "d");
  $info = "Até $datafim.";
}
//echo "<br> where  = $db_where"; exit;
if ($ordem=='n'){

  $db_ordem .= " nome_exec  ";
  //$db_ordem .= " a.z01_nome ";
  $info2 = "Ordenado por nome";
}else if ($ordem=='i'){
  $db_ordem .= " v50_inicial ";
  $info2 = "Ordenado por inicial";
}else if ($ordem=='p'){
  $db_ordem .= " v70_codforo ";
  $info2 = "Ordenado por processo";
}else if ($ordem=='v'){
  $db_ordem .= " v70_vara ";
  $info2 = "Ordenado por vara";
}

$head2 = "	RELATÓRIO DE INICIAIS ";
$head3 = "";
$head4 = @$info;
$head5 = @$info2;
if((isset($xdata))&&($xdata!="")){
  $head6 = "Valor corrigido em: ".db_formatar($xdata,"d");
}
if ($db_where == ""){
  $db_where = " 1=1 ";
}

$sqlpar = "select db21_regracgmiss,db21_regracgmiptu from db_config where codigo = ".db_getsession("DB_instit");
$resultpar = db_query($sqlpar);
$linhaspar = pg_num_rows($resultpar);
if($linhaspar>0){
  db_fieldsmemory($resultpar,0);
}


  $sCampos  = " distinct v50_inicial,		    							   ";
  $sCampos .= " v50_data,	              									   ";
  $sCampos .= " v70_codforo,		            							   ";
  $sCampos .= " v53_descr,									                 ";
  $sCampos .= " a.z01_nome as nome_exec,						         ";
  $sCampos .= " a.z01_numcgm as cgm_exe,						         ";
  $sCampos .= " b.z01_nome as nome_advog,						         ";
  $sCampos .= " v52_codsit,                                  ";
  $sCampos .= " v52_descr                                    ";


$sql = ($clinicial->sql_query_inform(null,$sCampos,$db_ordem,$db_where));
$result= db_query($sql);
$linhas = pg_num_rows($result);
if ($linhas == 0){

  $sMsg = _M('tributario.juridico.jur2_relinicial002.nao_existem_registros');
  db_redireciona("db_erros.php?fechar=true&db_erro={$sMsg}");
}

$pdf = new PDF();
$pdf->Open();
$pdf->AliasNbPages();
$total = 0;
$pdf->setfillcolor(235);
$pdf->setfont('arial','b',8);
$troca = 1;
$alt = 4;
$total = 0;
$totalvalor=0;
$totalvalorcor=0;
$totalvalorpago=0;
$p = 0;

for($x = 0; $x < $linhas;$x++){
  $mostra 	= false;
  $lContiua = false;

  db_fieldsmemory($result,$x);


  $sql  = "select distinct                                     ";
  $sql .= "       coalesce(k00_matric,0) as k00_matric,        ";
  $sql .= "       coalesce(k00_inscr,0)  as k00_inscr,         ";
	$sql .= "       case											                   ";
	$sql .= "         when k00_matric is not null then k00_matric";
	$sql .= "         when k00_inscr  is not null then k00_inscr ";
	$sql .= "         else arrenumcgm.k00_numcgm					       ";
	$sql .= "       end as codtipo,								               ";
	$sql .= "       case											                   ";
	$sql .= "         when k00_matric is not null then 'M'		   ";
	$sql .= "         when k00_inscr  is not null then 'I'		   ";
	$sql .= "         else 'C'					    				             ";
	$sql .= "       end as descrtipo					          			   ";
  $sql .= "from inicialnumpre	                 ";
  $sql .= "     left join arrematric on arrematric.k00_numpre = inicialnumpre.v59_numpre ";
  $sql .= "     left join arreinscr  on arreinscr.k00_numpre  = inicialnumpre.v59_numpre ";
  $sql .= "     left join arrenumcgm on arrenumcgm.k00_numpre = inicialnumpre.v59_numpre ";
  $sql .= "where v59_inicial = $v50_inicial ";

  $resultinicial = db_query($sql);
  $linhasinicial = pg_num_rows($resultinicial);
  if ($linhasinicial == 0){
    continue;
  }
  db_fieldsmemory($resultinicial,0);

  foreach ($aOrigens as $sTipoOrigem) {
  	if ($descrtipo == $sTipoOrigem) {
  	   $lContiua = true;
  	}
  }

  if (!$lContiua) {
    continue;
  }

  if($descrtipo == 'M'){
    // inicial é de matricula
    if($db21_regracgmiptu==2){
      // se a regra é 2 ... mostrar somente promitente
      //a matricula tem promitente
      $sqlprom = "select * from promitente where j41_matric = $k00_matric";
      $resultprom = db_query($sqlprom);
      $linhasprom = pg_num_rows($resultprom);
      if($linhasprom > 0){
        db_fieldsmemory($resultprom,0);
        if($j41_numcgm == $cgm_exe){
          //  esse é o promitente
          $mostra = true;
        }

      }else{
        // não tem promitente... vê se tem proprietario
        $sqlprop = "select * from iptubase where j01_matric = $k00_matric";
        $resultprop = db_query($sqlprop);
        $linhasprop = pg_num_rows($resultprop);
        if($linhasprop > 0){
          db_fieldsmemory($resultprop,0);
          //  esse é o proprietario
          $mostra = true;

        }
      }

    }elseif($db21_regracgmiptu==1){
      //Considerar Somente Proprietario
      $sqlprop = "select * from iptubase where j01_matric = $k00_matric";
      $resultprop = db_query($sqlprop);
      $linhasprop = pg_num_rows($resultprop);
      if($linhasprop > 0){
        db_fieldsmemory($resultprop,0);
        //  esse é o proprietario
        $mostra = true;
      }
    }elseif($db21_regracgmiptu==0){
      //Considerar Proprietario e Promitente
      $mostra = true;
    }

  }elseif($descrtipo == 'I'){
      // se é de inscrição
      // inicial é de inscrição
      if ( $db21_regracgmiss==0 ) {
        //vincular sócios
        $mostra = true;
      }elseif($db21_regracgmiss==1){
        //Não vincular socios
        $sqlsoc = "select q02_inscr,q02_numcgm from issbase where q02_inscr = $k00_inscr";
        $resultsoc = db_query($sqlsoc);
        $linhassoc = pg_num_rows($resultsoc);
        if($linhassoc>0){
          db_fieldsmemory($resultsoc,0);
          if($q02_numcgm == $cgm_exe){
            $mostra = true;
          }
        }
      }
  }else{
    $mostra = true;
  }


  $valorcor = 0;


  //Situação parcelada
  if ($v52_codsit == 4){
    // $sSqlValorCor  = " select distinct v07_numpre					    ";
    // $sSqlValorCor .= "   from termoini					 		    	";
    // $sSqlValorCor .= "   	    inner join termo on v07_parcel = parcel ";
    // $sSqlValorCor .= "  where inicial = {$v50_inicial}            ";

    // //Trecho adicionado para pegar apenas termo ativo
    // $sSqlValorCor .= "  and v07_situacao = 1		    	";

    //Modificado sql para buscar numpre das iniciais e nao do termo! (redmine 16311)
    $sSqlValorCor  = " select distinct k00_numpre as v07_numpre           ";
    $sSqlValorCor .= "   from inicialnumpre                               ";
    $sSqlValorCor .= "        inner join arreold on arreold.k00_numpre = inicialnumpre.v59_numpre ";
    $sSqlValorCor .= "  where v59_inicial = {$v50_inicial}                        ";

    $rsValorCor  = db_query($sSqlValorCor) or die($sSqlValorCor);
    $iNroLinha 	 = pg_num_rows($rsValorCor);

    $lDebitoAberto = false;
    for ($i=0; $i < $iNroLinha; $i++) {

	    $oValorCor = db_utils::fieldsMemory($rsValorCor,$i);

	    $rsConsultaNumpre = debitos_numpre($oValorCor->v07_numpre,0,0,db_getsession("DB_datausu"),db_getsession("DB_anousu"),0,"k00_numpre");

	    if (!$rsConsultaNumpre){
	      $rsConsultaNumpre = debitos_numpre_old($oValorCor->v07_numpre,0,0,db_getsession("DB_datausu"),db_getsession("DB_anousu"),0,"k00_numpre");
	    }
      
      if ($rsConsultaNumpre){
	      $oConsultaNumpre  = db_utils::fieldsMemory($rsConsultaNumpre,0);
        /* Verifica parcelamento ativo e com débito aberto */
        $numpre_aberto = "";

        $sSqlDebito    = " select distinct                                                         ";
        $sSqlDebito   .= "        termo.v07_parcel,       									                       ";  
        $sSqlDebito   .= "        termo.v07_situacao,                                              ";
        $sSqlDebito   .= "        arrecad.k00_numpre as numpre_aberto                              ";
        $sSqlDebito   .= "   from termoini      			 								                         	   ";
        $sSqlDebito   .= "   	    inner join termo on termoini.parcel = termo.v07_parcel           ";
        $sSqlDebito   .= "   	    inner join arrecad on termo.v07_numpre = arrecad.k00_numpre      ";
        $sSqlDebito   .= "  where termoini.inicial = {$v50_inicial}					   	    	             ";
        $sSqlDebito   .= "    and termo.v07_parcel not in (select v09_parcel from termoanu where v09_parcel = v07_parcel)";
        $rsDebito      = db_query($sSqlDebito) or die($sSqlDebito);
        $iNroLinhaDeb  = pg_num_rows($rsDebito);                                                 

        for ($iRP=0; $iRP < $iNroLinhaDeb; $iRP++) {
          db_fieldsmemory($rsDebito,$iRP);       
          if (!empty($numpre_aberto)) {
             /* Achou débito aberto sai para permitir atualizar */
             $lDebitoAberto = true;
          }
          if ($v07_situacao == '1' && empty($numpre_aberto)) {
            $sSqlReparc  = "    select termoreparc.v08_parcel,                   ";
            $sSqlReparc .= "           a.k00_numpre numpre_aberto                ";
            $sSqlReparc .= "      from termoreparc                               ";
            $sSqlReparc .= "inner join termo t   on v08_parcel = t.v07_parcel    ";
            $sSqlReparc .= "left  join arrecad a on t.v07_numpre = a.k00_numpre  ";
            $sSqlReparc .= "     where v08_parcelorigem = $v07_parcel            ";
            $rsReparc    = db_query($sSqlReparc) or die($sSqlReparc);            
            db_fieldsmemory($rsReparc,0);       
            if (!empty($numpre_aberto)) {
              $lDebitoAberto = true;
            }
          }
        }
      }
      if ($lDebitoAberto == true) {
          $valorcor += $oConsultaNumpre->total;
      } else {
          $valorcor += 0;
      }
    }
  } else if ($v52_codsit != 8 && $v52_codsit != 9) {

    $sSqlValorCor  = " select distinct k00_numpre												    ";
    $sSqlValorCor .= "   from inicialnumpre													    	";
    $sSqlValorCor .= "   	    inner join arrecad on arrecad.k00_numpre = inicialnumpre.v59_numpre ";
    $sSqlValorCor .= "  where v59_inicial = {$v50_inicial}									    	";

    $rsValorCor  = db_query($sSqlValorCor) or die($sSqlValorCor);
    $iNroLinha 	 = pg_num_rows($rsValorCor);


    for ($i=0; $i < $iNroLinha; $i++) {
	  $oValorCor = db_utils::fieldsMemory($rsValorCor,$i);

	  $rsConsultaNumpre = debitos_numpre($oValorCor->k00_numpre,0,0,db_getsession("DB_datausu"),db_getsession("DB_anousu"),0,"k00_numpre");
	  $oConsultaNumpre  = db_utils::fieldsMemory($rsConsultaNumpre,0);

	  $valorcor += $oConsultaNumpre->total;
    }

  }


  // valor da Ação


  $sqlvalor = " select sum(k00_valor) as valor,

			sum(
				fc_corre( arreforo.k00_receit,
				  arreforo.k00_dtoper,
				  arreforo.k00_valor,
				  v50_data,
				  cast(extract(year from v50_data) as integer),
				  v50_data )
				+
				round(
				fc_corre( arreforo.k00_receit,
				  arreforo.k00_dtoper,
				  arreforo.k00_valor,
				  v50_data,
				  cast(extract(year from v50_data) as integer),
				  v50_data )
				*
				coalesce( fc_juros( arreforo.k00_receit,
					    arreforo.k00_dtvenc,
					    v50_data,
					    v50_data,
					    false,
					    cast( extract(
					    year from v50_data) as integer)),0)
				,2)
				+
				round(
				fc_corre( arreforo.k00_receit,
				  arreforo.k00_dtoper,
				  arreforo.k00_valor,
				  v50_data,
				  cast(extract(year from v50_data) as integer),
				  v50_data )
				*
				coalesce( fc_multa( arreforo.k00_receit,
					    arreforo.k00_dtvenc,
					    v50_data,
					    arreforo.k00_dtoper,
					    cast( extract(
					    year from v50_data) as integer)),0)
				,2)
			) as corrigido
			    from inicialcert
				 inner join inicial on v50_inicial = v51_inicial
			    	 inner join arreforo on k00_certidao = v51_certidao
	   		   where v51_inicial = $v50_inicial" ;

$resultvalor = db_query($sqlvalor);
$linhasvalor = pg_num_rows($resultvalor);

if($linhasvalor > 0){
  db_fieldsmemory($resultvalor,0);

  $valor = $corrigido; // + round( $corrigido * $juros_base,2 ) + round( $corrigido * $multa_base,2 );

}


$valorpago = 0;

$sqlvalorpago = " select sum(k00_valor) as valorpago
			    from inicialcert
				 inner join inicialnumpre on v51_inicial = v59_inicial
			    	 inner join arrepaga on k00_numpre = v59_numpre
	   		   where v51_inicial = $v50_inicial" ;
$resultvalorpago = db_query($sqlvalorpago);
$linhasvalorpago = pg_num_rows($resultvalorpago);

if($linhasvalorpago > 0){
  db_fieldsmemory($resultvalorpago,0);
}

  if($mostra==true){

    if ($descrtipo == "M") {
      $sDescrOrigem  = "Matrícula";
      $sDescrOrigem2 = "{$codtipo} - Matrícula";
    } else if ($descrtipo == "I") {
      $sDescrOrigem  = "Inscrição";
      $sDescrOrigem2 = "{$codtipo} - Inscrição";
  	} else {
      $sDescrOrigem  = "Cgm";
      $sDescrOrigem2 = "{$codtipo} - Cgm";

    }
    // filtros por valores
    if (isset($nValorMinAcao) && (float)$valor < (float)$nValorMinAcao) {
      continue;
    }
    if (isset($nValorMaxAcao) && (float)$valor > (float)$nValorMaxAcao) {
      continue;
    }

    if (isset($nValorMinAtu) && (float)$valorcor < (float)$nValorMinAtu) {
      continue;
    }
    if (isset($nValorMaxAtu) && (float)$valorcor > (float)$nValorMaxAtu) {
      continue;
    }

    $ano = substr($v50_data,0,4);

    if ( ! in_array($v50_inicial,$aIniciais)) {
       if (isset($aTotValOrigem[$sDescrOrigem])){
         $aTotValOrigem[$sDescrOrigem]['valor']    += (float)$valor;
         $aTotValOrigem[$sDescrOrigem]['valorcor'] += (float)$valorcor;
         $aTotValOrigem[$sDescrOrigem]['valorpago'] += (float)$valorpago;
       } else {
         $aTotValOrigem[$sDescrOrigem]['valor']    = (float)$valor;
         $aTotValOrigem[$sDescrOrigem]['valorcor'] = (float)$valorcor;
         $aTotValOrigem[$sDescrOrigem]['valorpago'] = (float)$valorpago;
       }
       if (isset($aTotalAno[$ano])){
         $aTotalAno[$ano]['valor']     += (float)$valor;
         $aTotalAno[$ano]['valorcor']  += (float)$valorcor;
         $aTotalAno[$ano]['valorpago'] += (float)$valorpago;
       } else {
         $aTotalAno[$ano]['valor']     = (float)$valor;
         $aTotalAno[$ano]['valorcor']  = (float)$valorcor;
         $aTotalAno[$ano]['valorpago'] = (float)$valorpago;
       }
    }


    if ( $selTipo == "c" ) {
      if ($pdf->gety() > $pdf->h - 30 || $troca != 0 ){
        $pdf->addpage('L');
        $pdf->setfont('arial','b',8);
        $pdf->cell(20,$alt,"Inicial"	   ,1,0,"C",1);
        $pdf->cell(15,$alt,"Dt Inicial"	   ,1,0,"C",1);
        $pdf->cell(30,$alt,"Cod Processo Foro",1,0,"C",1);
        $pdf->cell(20,$alt,@$RLv70_vara ,1,0,"C",1);
        $pdf->cell(70,$alt,@$RLz01_nome	   ,1,0,"C",1);
        $pdf->cell(25,$alt,@$RLv56_codsit  ,1,0,"C",1);
        $pdf->cell(25,$alt,"Origem"	   	   ,1,0,"C",1);
        $pdf->cell(25,$alt,"Vlr Ação"	     ,1,0,"C",1);
        $pdf->cell(25,$alt,"Vlr Atualizado",  1,0,"C",1);
        $pdf->cell(25,$alt,"Vlr Pago",  1,1,"C",1);


        $troca = 0;
        $p = 0;
      }
      $pdf->setfont('arial','',7);
      $pdf->cell(20,$alt,@$v50_inicial			    ,0,0,"C",$p);
      $pdf->cell(15,$alt,db_formatar(@$v50_data,'d'),0,0,"C",$p);
      $pdf->cell(30,$alt,@$v70_codforo			    ,0,0,"C",$p);
      $pdf->cell(20,$alt,substr(@$v53_descr, 0, 12)				,0,0,"L",$p);
      $pdf->cell(70,$alt,@$nome_exec				,0,0,"L",$p);
      $pdf->cell(25,$alt,@$v52_descr				,0,0,"L",$p);
      $pdf->cell(25,$alt,$sDescrOrigem2      ,0,0,"L",$p);
      if ( ! in_array($v50_inicial,$aIniciais)) {

      	$pdf->cell(25,$alt,db_formatar(@$valor,'f')	  ,0,0,"R",$p);
        $pdf->cell(25,$alt,db_formatar(@$valorcor,'f'),    0,0,"R",$p);
        $pdf->cell(25,$alt,db_formatar(@$valorpago,'f'),    0,1,"R",$p);

        $total++;
        $totalvalor += $valor;
        $totalvalorcor +=$valorcor;
        $totalvalorpago +=$valorpago;
      } else {
        $pdf->cell(25,$alt,"",0,0,"R",$p);
        $pdf->cell(25,$alt,"",0,0,"R",$p);
        $pdf->cell(25,$alt,"",0,1,"R",$p);
      }

      if ($p==0) $p=1;
      else $p=0;

      $aIniciais[] = $v50_inicial;

    }
  }
}
if ($selTipo == "c") {
  $pdf->setfont('arial','b',8);
  $pdf->cell(205,$alt,'TOTAL DE REGISTROS : '.$total,"T",0,"L",0);
  $pdf->cell(25,$alt,db_formatar(@$totalvalor,'f')   ,"T",0,"R",0);
  $pdf->cell(25,$alt,db_formatar(@$totalvalorcor,'f'),"T",0,"R",0);
  $pdf->cell(25,$alt,db_formatar(@$totalvalorpago,'f'),"T",0,"R",0);

  $pdf->ln(10);
} else {

 $pdf->addpage('L');
 $pdf->setfont('arial','b',8);

}

$pdf->cell(100,$alt,"Resumo Origens" ,     1,1,"C",1);
$pdf->cell(40 ,$alt,"Origem"		  ,1,0,"C",1);
$pdf->cell(30 ,$alt,"Vlr. Ação"	  ,1,0,"C",1);
$pdf->cell(30 ,$alt,"Vlr. Atualizado",     1,0,"C",1);
$pdf->cell(30 ,$alt,"Vlr. Pago",           1,1,"C",1);

foreach ($aTotValOrigem as $sDescrOrigem => $aValores){
  $pdf->cell(40,$alt,$sDescrOrigem	 		   ,1,0,"L",0);
  $pdf->cell(30,$alt,db_formatar($aValores['valor'],'f')   ,1,0,"R",0);
  $pdf->cell(30,$alt,db_formatar($aValores['valorcor'],'f'),1,0,"R",0);
  $pdf->cell(30,$alt,db_formatar($aValores['valorpago'],'f'),1,1,"R",0);
}

$pdf->ln(10);

$pdf->cell(40 ,$alt,"Ano"		  ,1,0,"C",1);
$pdf->cell(30 ,$alt,"Vlr. Ação"	  ,1,0,"C",1);
$pdf->cell(30 ,$alt,"Vlr. Atualizado",     1,0,"C",1);
$pdf->cell(30 ,$alt,"Vlr. Pago",           1,1,"C",1);

foreach ($aTotalAno as $sDescrOrigem => $aValores){
  $pdf->cell(40,$alt,$sDescrOrigem	 		   ,1,0,"L",0);
  $pdf->cell(30,$alt,db_formatar($aValores['valor'],'f')   ,1,0,"R",0);
  $pdf->cell(30,$alt,db_formatar($aValores['valorcor'],'f'),1,0,"R",0);
  $pdf->cell(30,$alt,db_formatar($aValores['valorpago'],'f'),1,1,"R",0);
}



$pdf->Output();
?>
