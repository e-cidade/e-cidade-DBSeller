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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_movrel_classe.php"));
require_once(modification("classes/db_convenio_classe.php"));
require_once(modification("classes/db_relac_classe.php"));
require_once(modification("classes/db_rhpessoal_classe.php"));
$clmovrel = new cl_movrel;
$clconvenio = new cl_convenio;
$clrelac = new cl_relac;
$clrhpessoal = new cl_rhpessoal;
$clrotulo = new rotulocampo;
$clmovrel->rotulo->label();
$clrotulo->label('z01_nome');
$clrotulo->label('rh05_recis');

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
//db_postmemory($HTTP_SERVER_VARS,2);exit;

//echo "<br> ano -> $ano <br>";

$head2 = "RELATÓRIO DE DADOS IMPORTADOS";
$head4 = "PERÍODO : ".$mes." / ".$ano;

$dbwhere = "and r54_instit = ".db_getsession('DB_instit');
$mais = 5;
if(isset($r54_codrel) && $r54_codrel != ""){
  $dbwhere .= " and r54_codrel = '".$r54_codrel."' ";
  $result_codrel = $clconvenio->sql_record($clconvenio->sql_query_file($r54_codrel,db_getsession('DB_instit'),"r56_descr as descrconv,r56_dirarq as diretorio"));
  if($clconvenio->numrows > 0){
    db_fieldsmemory($result_codrel,0);
    $head6 = "CONVÊNIO: ".$r54_codrel." - ".$descrconv;
    $mais ++;
  }
}

if(isset($r54_regist) && $r54_regist != "" || isset($rh01_regist) && trim($rh01_regist) != "" ){
  
  /**
   * If que verifica o retorno do ajax contido no arquivo pes1_lancapontofs001.php
   */
  if ( isset($rh01_regist) && trim($rh01_regist) != "" ) {
    $dbwhere .= " and r54_regist in ({$rh01_regist}) ";
    $result_codreg = $clrhpessoal->sql_record ($clrhpessoal->sql_query_cgm(null, "z01_nome as nomefunc",null, "rh01_regist in ({$rh01_regist}) "));
  } else {
    
    $dbwhere .= " and r54_regist = ".$r54_regist;
    $result_codreg = $clrhpessoal->sql_record($clrhpessoal->sql_query_cgm($r54_regist,"z01_nome as nomefunc"));
  }
  if($clrhpessoal->numrows > 0){
    db_fieldsmemory($result_codreg,0);
    $HEAD7 = "head".$mais;
    //$$HEAD7 = "MATRÍCULA: ".$r54_regist." - ".$nomefunc;
    $mais ++;
  }
}

if(isset($r54_codeve) && $r54_codeve != ""){
  $dbwhere .= " and r54_codeve = '".$r54_codeve."' ";
  $result_codeve = $clrelac->sql_record($clrelac->sql_query_file($r54_codeve,db_getsession("DB_instit"),"r55_descr as descrrelac"));
  if($clrelac->numrows > 0){
    db_fieldsmemory($result_codeve,0);
    $HEAD8 = "head".$mais;
    $$HEAD8 = "RELACIONAMENTO: ".$r54_codeve." - ".$descrrelac;
    $mais ++;
  }
}
if(isset($nao_lancados)){
  $HEAD9 = "head".$mais;
  $$HEAD9 = "Não lançados na folha";
  $dbwhere .= " and r54_lancad = 'f' ";
}

$sCamposMovRelDados = "r54_codrel,r54_codeve,r54_regist,z01_nome,r54_quant1,r54_quant2,r54_quant3,r54_lancad,rh05_recis";
$sOrdemMovRelDados  = "r54_lancad,z01_nome";
$sWhereMovRelDados  = "r54_anomes = '".$ano.$mes."' $dbwhere";

$sSqlMovRelDados    = $clmovrel->sql_query_dados(null, $sCamposMovRelDados, $sOrdemMovRelDados, $sWhereMovRelDados, $ano, $mes);

$sSqlMovRelDados    = "select r54_codrel,
	                      r54_codeve,
			      r54_regist,
			      z01_nome  ,
                              z01_cgccpf as cpf,
                              r70_estrut,
                              r70_descr ,
                              r54_quant1,
                              r54_quant2,
                              r54_quant3,
                              r54_lancad,
                              rh05_recis, 
                              r55_rubr01
                       from movrel 
			    inner join convenio     on r56_codrel  = r54_codrel 
                                                   and r56_instit  = ".db_getsession('DB_instit')."
			    inner join relac        on r55_codeve  = r54_codeve 
                                                   and r55_instit  = ".db_getsession('DB_instit')."
                            left join rhpessoalmov  on rh02_regist = r54_regist
                                                   and rh02_anousu = $ano
                                                   and rh02_mesusu = $mes
						   and rh02_instit = ".db_getsession('DB_instit')."
                            left join rhlota        on r70_codigo  = rh02_lota
                                                   and r70_instit  = rh02_instit
                            left join rhpessoal     on rh01_regist = rh02_regist
                            left join cgm           on z01_numcgm  = rh01_numcgm
			    left join rhpesrescisao on rh05_seqpes = rh02_seqpes
		       where $sWhereMovRelDados 
		       order by r54_lancad,
				z01_nome,
                                r54_regist,
                                r54_quant1";

$xRubrica = pg_result($clmovrel->sql_record($sSqlMovRelDados),0,'r55_rubr01');

$sSqlInconsistencia = " select r14_regist, z01_nome, r70_descr, z01_cgccpf as cpf, r14_valor 
		  from gerfsal
                       inner join rhpessoal    on rh01_regist = r14_regist
		       inner join cgm          on rh01_numcgm = z01_numcgm
                       inner join rhpessoalmov on rh02_anousu = r14_anousu 
                                              and rh02_mesusu = r14_mesusu
					      and rh02_regist = r14_regist
                       inner join rhlota       on r70_codigo  = rh02_lota
                                              and r70_instit  = rh02_instit 
          where r14_anousu = $ano
            and r14_mesusu = $mes
            and r14_rubric = '$xRubrica'
            and r14_regist not in (select distinct r54_regist from ($sSqlMovRelDados) as x) ";

// echo $sSqlInconsistencia; exit;
// echo $sSqlMovRelDados; exit;

$erro_msg = '';

$result_inconsistencias = pg_query($sSqlInconsistencia);
$numrows_inconsistencias = pg_numrows($result_inconsistencias);

$result_dados = $clmovrel->sql_record($sSqlMovRelDados);
$numrows_dados = $clmovrel->numrows;
if($numrows_dados == 0){
  db_redireciona('db_erros.php?fechar=true&db_erro=Não existem dados importados no período de '.$mes.' / '.$ano);
  $sqlerro  = true;
  $erro_msg = 'Não foram encontrados registros para o tipo de arquivo selecionado.';
}else{
  $sqlerro = false;
}

$pdf = new PDF(); 
$pdf->Open(); 
$pdf->AliasNbPages(); 
$pdf->setfillcolor(235);
$pdf->setfont('arial','b',8);
$troca = 1;
$alt = 4;
$pre = 1;

$total_reg = 0;
$total_qt1 = 0;
$total_qt2 = 0;
$total_qt3 = 0;
$reg_ant   = 0;
$valor_descontado = 0;
$valor_abater = 0;

$arquivo_impressao = '/tmp/Convenio'.$r54_codrel.'_Relacionamento'.$r54_codeve.'.pdf';
$arquivo_download  = '/tmp/Convenio'.$r54_codrel.'_Relacionamento'.$r54_codeve.'.csv';

 $fp = fopen($arquivo_download,"w");

$sLinha = 'MATRICULA;NOME;LOTACAO;CPF;VLR_ENVIADO;VLR_DESCONTADO;LANCADO;DT_RESCISAO';
fputs($fp,"{$sLinha}\n");

$tot_inconsistencias = 0;
for($y = 0; $y < $numrows_inconsistencias;$y++){
  db_fieldsmemory($result_inconsistencias,$y);
  if($pdf->gety() > $pdf->h - 30 || $troca != 0 ){
    $pdf->addpage('L');
    $pdf->setfont('arial','b',11);
    $pdf->cell(0,$alt,'I N C O N S I S T E N C I A S',0,0,"C",0);
    $pdf->ln(5);
    $pdf->setfont('arial','b',8);
 //   $pdf->cell(15,$alt,$RLr54_codrel,1,0,"C",1);
 //   $pdf->cell(15,$alt,$RLr54_codeve,1,0,"C",1);
    $pdf->cell(18,$alt,$RLr54_regist,1,0,"C",1);
    $pdf->cell(80,$alt,$RLz01_nome  ,1,0,"C",1);
    $pdf->cell(60,$alt,'Lotação'    ,1,0,"C",1);
    $pdf->cell(20,$alt,'CPF'    ,1,0,"C",1);
    $pdf->cell(20,$alt,'Enviado',1,0,"C",1);
 //   $pdf->cell(20,$alt,'Desconto',1,0,"C",1);
 //   $pdf->cell(15,$alt,$RLr54_quant2,1,0,"C",1);
    $pdf->cell(20,$alt,'Descontado',1,0,"C",1);
    $pdf->cell(15,$alt,$RLr54_lancad,1,0,"C",1);
    $pdf->cell(25,$alt,$RLrh05_recis,1,1,"C",1);
    $troca = 0;
  }

  if($r54_lancad == 't'){
    $r54_lancad = "Sim";
  }else{
    $r54_lancad = "Não";
  }

  if($pre == 1){$pre = 0;}else{$pre = 1;}

  $pdf->setfont('arial','',7);
  $pdf->cell(18,$alt,$r14_regist,0,0,"C",$pre);
  $pdf->cell(80,$alt,$z01_nome."(".$r14_valor.")"  ,0,0,"L",$pre);
  $pdf->cell(60,$alt,$r70_descr  ,0,0,"L",$pre);
  $pdf->cell(20,$alt,db_formatar($cpf,'cpf'),0,0,"C",$pre);
  $pdf->cell(20,$alt,db_formatar(0,"f"),0,0,"R",$pre);
 // $pdf->cell(15,$alt,db_formatar($r14_valor,"f"),0,0,"R",$pre);
  $pdf->cell(20,$alt,db_formatar($r14_valor,"f"),0,0,"R",$pre);
  $pdf->cell(15,$alt,$r54_lancad,0,0,"C",$pre);
  $pdf->cell(25,$alt,'',0,1,"C",$pre);
  
  $tot_inconsistencias += $r14_valor;
}



$troca = 1;
$alt = 4;
$pre = 1;

for($i = 0; $i < $numrows_dados;$i++){
  db_fieldsmemory($result_dados,$i);
  if($pdf->gety() > $pdf->h - 30 || $troca != 0 ){
    $pdf->addpage('L');
    $pdf->setfont('arial','b',8);
 //   $pdf->cell(15,$alt,$RLr54_codrel,1,0,"C",1);
 //   $pdf->cell(15,$alt,$RLr54_codeve,1,0,"C",1);
    $pdf->cell(18,$alt,$RLr54_regist,1,0,"C",1);
    $pdf->cell(80,$alt,$RLz01_nome  ,1,0,"C",1);
    $pdf->cell(60,$alt,'Lotação'    ,1,0,"C",1);
    $pdf->cell(20,$alt,'CPF'    ,1,0,"C",1);
    $pdf->cell(20,$alt,'Enviado',1,0,"C",1);
 //   $pdf->cell(20,$alt,'Desconto',1,0,"C",1);
 //   $pdf->cell(15,$alt,$RLr54_quant2,1,0,"C",1);
    $pdf->cell(20,$alt,'Descontado',1,0,"C",1);
    $pdf->cell(15,$alt,$RLr54_lancad,1,0,"C",1);
    $pdf->cell(25,$alt,$RLrh05_recis,1,1,"C",1);
    $troca = 0;
  }

  if($r54_lancad == 't'){
    $r54_lancad = "Sim";
  }else{
    $r54_lancad = "Não";
  }

  if($pre == 1){$pre = 0;}else{$pre = 1;}

  $r14_valor = 0;

  $sql_valor = 
	 "select r14_valor 
	  from gerfsal 
	  where r14_anousu = $ano
            and r14_mesusu = $mes
            and r14_regist = $r54_regist
	    and r14_rubric = '$r55_rubr01'";
   $result_valor = pg_query($sql_valor);
  if(pg_numrows($result_valor) > 0){
    db_fieldsmemory($result_valor,0);
//  }else{
//     db_redireciona('db_erros.php?fechar=true&db_erro=Não encontrato valor para a rubrica '.$r55_rubr01.', para o servidor '.$r54_regist.'existem dados importados no período de '.$mes.' / '.$ano);
  }

//  if($valor_descontado == $valor_abater){
//    $valor_descontado = 0;
//  }
  if($reg_ant <> $r54_regist){
     $valor_descontado = 0;
     $valor_abater     = 0;
     if($r14_valor >= $r54_quant1){
	$valor_descontado = $r54_quant1;
        $valor_abater = $r14_valor - $r54_quant1;
     }else{
        $valor_descontado = $r14_valor;
     }
  }else{
     if($valor_abater >= $r54_quant1){
	$valor_descontado = $r54_quant1;
        $valor_abater = $valor_abater - $r54_quant1;
     }else{
	$valor_descontado = $valor_abater;
        $valor_abater = 0;
     }
  }


  if( pg_result($result_dados,($i+1), 'r54_regist') != $r54_regist ){
     $valor_descontado += $valor_abater;
     $valor_abater = 0;
  }

  $reg_ant = $r54_regist;

  $pdf->setfont('arial','',7);
//  $pdf->cell(15,$alt,$r54_codrel,1,0,"C",0);
//  $pdf->cell(15,$alt,$r54_codeve,1,0,"C",0);
  $pdf->cell(18,$alt,$r54_regist,0,0,"C",$pre);
  $pdf->cell(80,$alt,$z01_nome."(".$r14_valor.")"  ,0,0,"L",$pre);
  $pdf->cell(60,$alt,$r70_descr  ,0,0,"L",$pre);
  $pdf->cell(20,$alt,db_formatar($cpf,'cpf'),0,0,"C",$pre);
  $pdf->cell(20,$alt,db_formatar($r54_quant1,"f"),0,0,"R",$pre);
 // $pdf->cell(15,$alt,db_formatar($r14_valor,"f"),0,0,"R",$pre);
  $pdf->cell(20,$alt,db_formatar($valor_descontado,"f"),0,0,"R",$pre);
  $pdf->cell(15,$alt,$r54_lancad,0,0,"C",$pre);
  $pdf->cell(25,$alt,db_formatar($rh05_recis,"d"),0,1,"C",$pre);

 $sLinha = $r54_regist.';'.$z01_nome.';'.$r70_descr.';'.db_formatar($cpf,'cpf').';'.$r54_quant1.';'.$valor_descontado.';'.$r54_lancad.';'.$rh05_recis;
 fputs($fp,"{$sLinha}\n");


  $total_reg ++;
  $total_qt1 += $r54_quant1;
  $total_qt2 += $valor_descontado;
  $total_qt3 += $r54_quant3;
}
$pdf->ln(1);
$pdf->cell(178,$alt,'Quantidade total  :  ',"TB",0,"L",1);
$pdf->cell(20,$alt,db_formatar($total_qt1,"f"),"TB",0,"R",1);
$pdf->cell(20,$alt,db_formatar($total_qt2,"f"),"TB",0,"R",1);
//$pdf->cell(20,$alt,db_formatar($total_qt3,"f"),"TB",0,"R",1);
$pdf->cell(40,$alt,"","TB",1,"C",1);

if($numrows_inconsistencias > 0){
  $pdf->cell(178,$alt,'Inconsistências   :  ',"TB",0,"L",1);
  $pdf->cell(20,$alt,db_formatar(0,"f"),"TB",0,"R",1);
  $pdf->cell(20,$alt,db_formatar($tot_inconsistencias,"f"),"TB",0,"R",1);
  //$pdf->cell(20,$alt,db_formatar($total_qt3,"f"),"TB",0,"R",1);
  $pdf->cell(40,$alt,"","TB",1,"C",1);
  $pdf->cell(178,$alt,'TOTAL GERAL   :  ',"TB",0,"L",1);
  $pdf->cell(20,$alt,db_formatar($total_qt1,"f"),"TB",0,"R",1);
  $pdf->cell(20,$alt,db_formatar($tot_inconsistencias+$total_qt2,"f"),"TB",0,"R",1);
  //$pdf->cell(20,$alt,db_formatar($total_qt3,"f"),"TB",0,"R",1);
  $pdf->cell(40,$alt,"","TB",1,"C",1);

}

$pdf->ln(1);
$pdf->cell(258,$alt,'Total de registros  :  '.$total_reg,"T",1,"L",0);



 fclose($fp);
  
$pdf->Output($arquivo_impressao,false,true);


if($sqlerro == false){
  echo "
  <script>
    parent.js_detectaarquivo('$arquivo_download','$arquivo_impressao');
  </script>
  ";
}else{
  echo "
  <script>
    parent.js_erro('$erro_msg');
  </script>
  ";
}





?>
