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
include(modification("libs/db_sql.php"));
include(modification("classes/db_rhpessoal_classe.php"));
include(modification("classes/db_pontofx_classe.php"));
include(modification("classes/db_rhcadregime_classe.php"));
require_once modification("libs/db_stdlib.php");
require_once modification("libs/JSON.php");


$clrhpessoal = new cl_rhpessoal;
$clpontofx = new cl_pontofx;
$clrhcadregime = new cl_rhcadregime;
$clrotulo = new rotulocampo;
$clrotulo->label("rh01_regist");
$clrotulo->label("rh01_admiss");
$clrotulo->label("z01_nome");
$clrotulo->label("rh37_rubric");
$clrotulo->label("rh37_descr");
$clrotulo->label("r70_estrut");
$clrotulo->label("r70_descr");
$clrotulo->label("r70_codigo");

parse_str($_SERVER["QUERY_STRING"], $queryString);

$head5 = "PERÍODO: ".db_formatar($datai,"d")." até ".db_formatar($dataf,"d");

$mes = date ('m',db_getsession("DB_datausu"));
$ano = date ('Y',db_getsession("DB_datausu"));

$camposQuebra = ", '' as quebrar";
$labelFuncaoLotacao = $RLrh37_descr;
$valorImprime = "rh37_descr";
if($lota == "l"){
  $camposQuebra = ", r70_estrut as quebrar, r70_descr as descricao";
  if($ordem == "a"){
    $orderby = " r70_estrut,z01_nome ";
    $head6  = "ORDEM: ALFABÉTICA";
  }else if($ordem == "n"){
    $orderby = " r70_estrut,rh01_regist ";
    $head6  = "ORDEM: MATRÍCULA";
  }else{
    $orderby = " r70_estrut,rh01_admiss ";
    $head6  = "ORDEM: ADMISSÃO";
  }
}else if($lota == "c"){
  $labelFuncaoLotacao = $RLr70_descr;
  $valorImprime = "r70_descr";
  $camposQuebra = ", rh37_funcao as quebrar, rh37_descr as descricao";
  if($ordem == "a"){
    $orderby = " rh37_descr,z01_nome ";
    $head6  = "ORDEM: ALFABÉTICA";
  }else if($ordem == "n"){
    $orderby = " rh37_descr,rh01_regist ";
    $head6  = "ORDEM: MATRÍCULA";
  }else{
    $orderby = " rh37_descr,rh01_admiss ";
    $head6  = "ORDEM: ADMISSÃO";
  }
}else{
  if($ordem == "a"){
    $orderby = " z01_nome ";
    $head6  = "ORDEM: ALFABÉTICA";
  }else if($ordem == "n"){
    $orderby = " rh01_regist ";
    $head6  = "ORDEM: MATRÍCULA";
  }else{
    $orderby = " rh01_admiss ";
    $head6  = "ORDEM: ADMISSÃO";
  }
}

$dbwhere = " rh02_anousu = ".$ano." and rh02_mesusu = ".$mes." and rh02_instit = ".db_getsession("DB_instit")." and ";

if($regime != ""){
  if($tipo=="r"){
     $dbwhere .= " rh30_regime in (".$regime.") and ";
  }else{
     $dbwhere .= " rh30_codreg in (".$regime.") and ";
  }
}

if($adm_dem == 'a'){
  $head3 = "FUNCIONÁRIOS ADMITIDOS";
   
  if(trim($datai) != "" && trim($dataf) != ""){
    $dbwhere.= " rh01_admiss between '".$datai."' and '".$dataf."' ";
  }else if(trim($datai) != ""){
    $dbwhere.= " rh01_admiss >= '".$datai."' ";
  }else if(trim($dataf) != ""){
    $dbwhere.= " rh01_admiss <= '".$dataf."' ";
  }
}else{
  $head3 = "FUNCIONÁRIOS DEMITIDOS";
   
  if(trim($datai) != "" && trim($dataf) != ""){
    $dbwhere.= " rh05_recis between '".$datai."' and '".$dataf."' ";
  }else if(trim($datai) != ""){
    $dbwhere.= " rh05_recis >= '".$datai."' ";
  }else if(trim($dataf) != ""){
    $dbwhere.= " rh05_recis <= '".$dataf."' ";
  }
}
$dbwhereinativo = " and (rh30_vinculo = 'A' ";
if(isset($listainativo)){
  $dbwhereinativo .= " or rh30_vinculo = 'I' ";
}
if(isset($listapens)){
  $dbwhereinativo .= " or rh30_vinculo = 'P' ";
}
$dbwhereinativo.= ") ";

$dbwhererescis = " and rh05_recis is null ";
if(isset($listarescis) || $adm_dem == 'd' ) {
  $dbwhererescis = "";
}


$dbwhere .= $dbwhereinativo.$dbwhererescis;

$sql_dados = $clrhpessoal->sql_query_lotafuncres(null,"rh01_regist, z01_nome,rh01_nasc,rh01_sexo,rh01_admiss, r70_estrut, r70_descr, rh37_funcao, rh37_descr,rh03_padrao,rh05_recis, rh01_observacao".$camposQuebra,$orderby,$dbwhere);
$result_dados = $clrhpessoal->sql_record($sql_dados);
// echo $sql_dados;
// dd($result_dados);
$numrows_dados = $clrhpessoal->numrows;

if($numrows_dados == 0){
  db_redireciona("db_erros.php?fechar=true&db_erro=Não existem funcionários admitidos no período de ".db_formatar($datai,"d")." e ".db_formatar($dataf,"d").".");
}

if($regime != 0){
   if($tipo=="r"){
      $head7 = "REGIMES : (".$regime.")";
   }else{
      $head7 = "VINCULOS : (".$regime.")";
   }
}

$pdf = new PDF(); 
$pdf->Open(); 
$pdf->AliasNbPages(); 
$total = 0;
$pdf->setfillcolor(235);
$pdf->setfont("arial","b",8);
$troca = 1;
$alt = 4;
$pre = 0;
$lotacao_antes = 0;

$imprime_dados_quebra = true;
$tipoSaida = isset($tipoSaida) ? $tipoSaida : 'pdf'; // Assume 'pdf' se não estiver definido

if ($tipoSaida == 'pdf'){
  if(isSantanaDoLivramento()){
    for($x=0; $x<$numrows_dados; $x++){
      db_fieldsmemory($result_dados,$x);
    
      if($lota != "n" && $lotacao_antes != $quebrar){
        $lotacao_antes = $quebrar;
        if($quebrapagina == "s"){
          $troca = 1;
        }
        $imprime_dados_quebra = true;
      }
    
      if($pdf->gety() > $pdf->h - 30 || $troca != 0){
        $pdf->addpage();
        $pdf->setfont("arial","b",7);
        $pdf->cell(13,$alt,$RLrh01_regist,1,0,"C",1);
        $pdf->cell(56,$alt,$RLz01_nome,1,0,"C",1);
        $pdf->cell(50,$alt,$labelFuncaoLotacao,1,0,"C",1);
        $pdf->cell(15,$alt,$RLrh01_admiss,1,0,"C",1);
        $pdf->cell(15,$alt,'Rescisão',1,0,"C",1);
        $pdf->cell(13,$alt,'Padão',1,0,"C",1);
        $pdf->cell(30,$alt,'OBS',1,1,"C",1);
    
        if($fixo == "s"){
          $pdf->setfont("arial","b",6);
          $pdf->cell(15,$alt,"RUBRICA",0,0,"C",0);
          $pdf->cell(110,$alt,"DESCRIÇÃO",0,0,"C",0);
          $pdf->cell(30,$alt,"QUANT",0,0,"C",0);
          $pdf->cell(30,$alt,"VALOR",0,1,"C",0);
        }
    
        $imprime_dados_quebra = true;
        $troca = 0;
        $pre = 1;
      }
    
      if($lota != 'n' && $imprime_dados_quebra == true){
        $pdf->setfont("arial","b",6);
        $pdf->ln(2);
        $pdf->cell(0,$alt,$quebrar." - ".$descricao,0,1,"L",0);
        $imprime_dados_quebra = false;
      }
    
      if($pre == 1){
        $pre = 0;
      }else{
        $pre = 1;
      }
    
      $put_b = "";
      $put_t = "0";
      if($fixo == "s"){
        $put_b = "b";
        $put_t = "T";
        $pre = 1;
        $pdf->ln(1);
      }
    
      $pdf->setfont("arial",$put_b,6);
      $pdf->cell(13,$alt,$rh01_regist,$put_t,0,"C",$pre);
      $pdf->cell(56,$alt,$z01_nome,$put_t,0,"L",$pre);
      $pdf->cell(50,$alt,substr($$valorImprime,0,40),$put_t,0,"L",$pre);
      $pdf->cell(15,$alt,db_formatar($rh01_admiss,"d"),$put_t,0,"C",$pre);
      $pdf->cell(15,$alt,db_formatar($rh05_recis,"d"),$put_t,0,"C",$pre);
      $pdf->cell(13,$alt,$rh03_padrao,$put_t,0,"C",$pre);
      $pdf->MultiCell( 30, $alt, $rh01_observacao,$put_t,"L",$pre);

      if($fixo == "s"){
        $sql_fixo = $clpontofx->sql_query_rubrica($ano, $mes, $rh01_regist, null, " * ");
        $result_fixo = $clpontofx->sql_record($sql_fixo);
        $numrows_fixo = $clpontofx->numrows;
        for($xx=0; $xx<$numrows_fixo; $xx++){
          db_fieldsmemory($result_fixo,$xx);
          $pdf->setfont("arial","",6);
          $pdf->cell(15,$alt,$rh27_rubric,0,0,"C",0);
          $pdf->cell(110,$alt,$rh27_descr,0,0,"L",0);
          $pdf->cell(30,$alt,db_formatar($r90_quant,"f"),0,0,"R",0);
          $pdf->cell(30,$alt,db_formatar($r90_valor,"f"),0,1,"R",0);
        }
      }
      $total += 1;
    }
  }else{
    for($x=0; $x<$numrows_dados; $x++){
      db_fieldsmemory($result_dados,$x);

      if($lota != "n" && $lotacao_antes != $quebrar){
        $lotacao_antes = $quebrar;
        if($quebrapagina == "s"){
          $troca = 1;
        }
        $imprime_dados_quebra = true;
      }

      if($pdf->gety() > $pdf->h - 30 || $troca != 0){
        $pdf->addpage();
        $pdf->setfont("arial","b",7);
        $pdf->cell(15,$alt,$RLrh01_regist,1,0,"C",1);
        $pdf->cell(56,$alt,$RLz01_nome,1,0,"C",1);
        $pdf->cell(18,$alt,'Nascimento',1,0,"C",1);
        $pdf->cell(8,$alt,'Sexo',1,0,"C",1);
        $pdf->cell(50,$alt,$labelFuncaoLotacao,1,0,"C",1);
        $pdf->cell(15,$alt,$RLrh01_admiss,1,0,"C",1);
        $pdf->cell(15,$alt,'Rescisão',1,0,"C",1);
        $pdf->cell(13,$alt,'Padrão',1,1,"C",1);

        if($fixo == "s"){
          $pdf->setfont("arial","b",6);
          $pdf->cell(15,$alt,"RUBRICA",0,0,"C",0);
          $pdf->cell(110,$alt,"DESCRIÇÃO",0,0,"C",0);
          $pdf->cell(30,$alt,"QUANT",0,0,"C",0);
          $pdf->cell(30,$alt,"VALOR",0,1,"C",0);
        }

        $imprime_dados_quebra = true;
        $troca = 0;
        $pre = 1;
      }

      if($lota != 'n' && $imprime_dados_quebra == true){
        $pdf->setfont("arial","b",6);
        $pdf->ln(2);
        $pdf->cell(0,$alt,$quebrar." - ".$descricao,0,1,"L",0);
        $imprime_dados_quebra = false;
      }

      if($pre == 1){
        $pre = 0;
      }else{
        $pre = 1;
      }

      $put_b = "";
      $put_t = "0";
      if($fixo == "s"){
        $put_b = "b";
        $put_t = "T";
        $pre = 1;
        $pdf->ln(1);
      }

      $pdf->setfont("arial",$put_b,6);
      $pdf->cell(15,$alt,$rh01_regist,$put_t,0,"C",$pre);
      $pdf->cell(56,$alt,$z01_nome,$put_t,0,"L",$pre);
      $pdf->cell(18,$alt,$rh01_nasc,$put_t,0,"C",$pre);
      $pdf->cell(8,$alt,$rh01_sexo,$put_t,0,"C",$pre);
      $pdf->cell(50,$alt,substr($$valorImprime,0,40),$put_t,0,"L",$pre);
      $pdf->cell(15,$alt,db_formatar($rh01_admiss,"d"),$put_t,0,"C",$pre);
      $pdf->cell(15,$alt,db_formatar($rh05_recis,"d"),$put_t,0,"C",$pre);
      $pdf->cell(13,$alt,$rh03_padrao,$put_t,1,"C",$pre);

      if($fixo == "s"){
        $sql_fixo = $clpontofx->sql_query_rubrica($ano, $mes, $rh01_regist, null, " * ");
        $result_fixo = $clpontofx->sql_record($sql_fixo);
        $numrows_fixo = $clpontofx->numrows;
        for($xx=0; $xx<$numrows_fixo; $xx++){
          db_fieldsmemory($result_fixo,$xx);
          $pdf->setfont("arial","",6);
          $pdf->cell(15,$alt,$rh27_rubric,0,0,"C",0);
          $pdf->cell(110,$alt,$rh27_descr,0,0,"L",0);
          $pdf->cell(30,$alt,db_formatar($r90_quant,"f"),0,0,"R",0);
          $pdf->cell(30,$alt,db_formatar($r90_valor,"f"),0,1,"R",0);
        }
      }
      $total += 1;
    }

  }
    $pdf->setfont("arial","b",7);
    $pdf->cell(190,$alt,"TOTAL :  ".$total."  FUNCIONÁRIOS","T",0,"R",0);

    $pdf->Output();
} else {

  $sNomeArquivo  = "tmp/adimitidodemitido" . time() . ".csv";
	$fileRelFuncao = fopen($sNomeArquivo, "w");
  if(isSantanaDoLivramento()){
    $aCabecalho = [];
    $aCabecalho[] = 'MATRICULA';
    $aCabecalho[] = 'NOME';
    $aCabecalho[] = 'DESCRICAO DO CARGO';
    $aCabecalho[] = 'ADMISSAO';
    $aCabecalho[] = 'RECISAO';
    $aCabecalho[] = 'PADRAO';
    $aCabecalho[] = 'OBS';
  
    fputcsv($fileRelFuncao, $aCabecalho, ";");

    for($x=0; $x<$numrows_dados; $x++){
      db_fieldsmemory($result_dados,$x);

      $aDadosRelatorio = [];
      $aDadosRelatorio[] = $rh01_regist;
      $aDadosRelatorio[] = $z01_nome;
      $aDadosRelatorio[] = $rh37_descr;
      $aDadosRelatorio[] = db_formatar($rh01_admiss, 'd');    
      $aDadosRelatorio[] = ($rh05_recis === null || $rh05_recis === '') ? ' ' : db_formatar($rh05_recis, 'd');
      $aDadosRelatorio[] = $rh03_padrao;
      $aDadosRelatorio[] = $rh01_observacao;
      
      fputcsv($fileRelFuncao, $aDadosRelatorio, ";");

    }
    
  } else {
    $aCabecalho = [];
    $aCabecalho[] = 'MATRICULA';
    $aCabecalho[] = 'NOME';
    $aCabecalho[] = 'NASCIMENTO';
    $aCabecalho[] = 'SEXO';
    $aCabecalho[] = 'DESCRICAO DO CARGO';
    $aCabecalho[] = 'ADMISSAO';
    $aCabecalho[] = 'RECISAO';
    $aCabecalho[] = 'PADRAO';
  
    fputcsv($fileRelFuncao, $aCabecalho, ";");

    for($x=0; $x<$numrows_dados; $x++){
      db_fieldsmemory($result_dados,$x);

      $aDadosRelatorio = [];
      $aDadosRelatorio[] = $rh01_regist;
      $aDadosRelatorio[] = $z01_nome;
      $aDadosRelatorio[] = $rh01_nasc;
      $aDadosRelatorio[] = $rh01_sexo;
      $aDadosRelatorio[] = $rh37_descr;
      $aDadosRelatorio[] = db_formatar($rh01_admiss, 'd');    
      $aDadosRelatorio[] = ($rh05_recis === null || $rh05_recis === '') ? ' ' : db_formatar($rh05_recis, 'd');
      $aDadosRelatorio[] = $rh03_padrao;
      
      fputcsv($fileRelFuncao, $aDadosRelatorio, ";");

    }
	
  }

  echo "
  <script>
    parent.js_detectaarquivo1('$sNomeArquivo');
  </script>
    ";

  exit;
  
}





?>