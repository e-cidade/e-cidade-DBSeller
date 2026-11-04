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
require_once(modification("classes/db_rhfuncao_classe.php"));
require_once(modification("classes/db_rhregime_classe.php"));

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
db_postmemory($HTTP_SERVER_VARS);

$clrhfuncao = new cl_rhfuncao;
$clrhregime = new cl_rhregime;

if(!isset($ano)){
  $ano = db_anofolha();
}
if(!isset($mes)){
  $mes = db_mesfolha();
}

$where = " ";

if(isset($colunas1) && !empty($colunas1)){
   $where .= " and rh30_codreg in ($colunas1) ";
}

$aux_lotacao = false;
$aux_selecao = false;
if (isset($lotacao) && !empty($lotacao)){
  $aux_lotacao = true;
  $where .= " and rhlota.r70_codigo = $lotacao ";
}

//verificamos se foi informada selecao, buscamos a condicao e aplicamos na consulta
if(isset($selecao) && !empty($selecao)) {
  $aux_selecao = true;
  $oSelecao = new Selecao($selecao);  
  $where .= " and rhpessoalmov.rh02_regist in (select rhpessoalmov.rh02_regist 
                                    from rhpessoal 
                                         inner join rhpessoalmov   on rhpessoal.rh01_regist     = rhpessoalmov.rh02_regist 
                                                                  and rhpessoalmov.rh02_anousu  = " . $ano. "
                                                                  and rhpessoalmov.rh02_mesusu  = " . $mes . "
                                                                  and rhpessoalmov.rh02_instit  = " . db_getsession("DB_instit") . "
                                         left join  rhlota         on rhlota.r70_codigo         = rhpessoalmov.rh02_lota
                                                                  and rhlota.r70_instit         = rhpessoalmov.rh02_instit
                                         left join  rhregime       on rhregime.rh30_codreg      = rhpessoalmov.rh02_codreg
                                         left join  rhpescargo     on rhpescargo.rh20_seqpes    = rhpessoalmov.rh02_seqpes
                                         left join  rhpespadrao    on rhpespadrao.rh03_seqpes   = rhpessoalmov.rh02_seqpes             
                                                                  and rhpespadrao.rh03_anousu   = rhpessoalmov.rh02_anousu             
                                                                  and rhpespadrao.rh03_mesusu   = rhpessoalmov.rh02_mesusu
                                         left join  rhpesrescisao  on rhpesrescisao.rh05_seqpes = rhpessoalmov.rh02_seqpes
                                  where " . $oSelecao->getWhere() . ")";
  
}

$titulorel = "TODOS OS CARGOS";

$CamposLotacao = "";
if($aux_selecao || $aux_lotacao) {
  $CamposLotacao = "r70_estrut,r70_descr,";
}

$sql1 = "select $CamposLotacao funcao as rh37_funcao,
                 rh37_descr,
                 rh37_vagas,
                 sum(ocupados)               as ocupados,
                 sum(tot_ativos)             as tot_ativos, 
                 sum(tot_inativos)           as tot_inativos,
                 sum(tot_pensionistas)       as tot_pensionistas,
                 rh37_vagas - sum(ocupados)  as saldo

                 from ( select $CamposLotacao rh37_funcao as funcao,
                                 rh37_descr,
                                 rh37_vagas,
                                 count(rh01_regist) as ocupados,
                                 sum(case when rh30_vinculo = 'A' then 1 else 0 end) as tot_ativos,
                                 sum(case when rh30_vinculo = 'I' then 1 else 0 end) as tot_inativos,
                                 sum(case when rh30_vinculo = 'P' then 1 else 0 end) as tot_pensionistas
                           from rhfuncao 
                           inner join rhpessoalmov  on rhpessoalmov.rh02_funcao  = rhfuncao.rh37_funcao
                                                   and rhpessoalmov.rh02_anousu  = $ano
                                                   and rhpessoalmov.rh02_mesusu  = $mes
                                                   and rhpessoalmov.rh02_instit  = ".db_getsession("DB_instit")."
                           inner join rhpessoal     on rhpessoal.rh01_regist     = rhpessoalmov.rh02_regist 
                           left  join rhpesrescisao on rhpesrescisao.rh05_seqpes = rhpessoalmov.rh02_seqpes 
                           inner join rhregime      on rhregime.rh30_codreg      = rhpessoalmov.rh02_codreg
                                                   and rhregime.rh30_instit      = rhpessoalmov.rh02_instit 
                           inner join cgm           on cgm.z01_numcgm            = rhpessoal.rh01_numcgm 
                           inner join rhlota        on rhlota.r70_codigo         = rhpessoalmov.rh02_lota
                                                   and rhlota.r70_instit         = rhpessoalmov.rh02_instit 
                           where rh37_instit = ".db_getsession("DB_instit")."
                    $where
                and rh05_seqpes is null
              group by rh37_funcao,
                       rh37_descr,
                       rh30_vinculo,
                       $CamposLotacao
                       rh37_vagas
              order by rh37_funcao) as x 
              group by rh37_funcao,
                       rh37_descr,
                       $CamposLotacao
                       rh37_vagas
              order by funcao";	

$result_funcoes=  db_query($sql1);
if (pg_numrows($result_funcoes) == 0) {
  db_redireciona("db_erros.php?fechar=true&db_erro=Nenhum cargo encontrado");
}

$where_regime = "rh30_instit = ".db_getsession('DB_instit');
if (isset($colunas1) && !empty($colunas1)){
  $where_regime .= " and rh30_codreg in ($colunas1) ";
}

$result_regime = $clrhregime->sql_record($clrhregime->sql_query_file(null, "rh30_vinculo","", $where_regime));
$colunas = "";    
$virgula = "";
for($x = 0; $x < $clrhregime->numrows; $x ++) {
  db_fieldsmemory($result_regime, $x);
  $colunas .= $virgula.strtolower($rh30_vinculo);
  $virgula = ",";
}

$head3 = "CARGOS";
$head5 = $titulorel;

$pdf = new PDF();

if($aux_lotacao || $aux_selecao){

  $pdf = new PDF('L');
}

$pdf->Open();
$pdf->AliasNbPages();
$pdf->setfillcolor(235);
$totalt = 0;
$valort = 0;
$quantt = 0;
$troca = 1;
$p = 1;
$alt = 4;
$totalvaga = 0;
$totalocup = 0;
$totalativ = 0;
$totalinat = 0;
$totalpens = 0;
$totalsald = 0;
$totalfunc = 0;

$linha = "";
$separador = ";";

if(isset($formato_emissao) && $formato_emissao == "csv" && isset($sNomeArquivo)){

  $sArquivo     = "tmp/".$sNomeArquivo;
  $fArquivo     = fopen($sArquivo, "w+");

  $linha  = "Cargo".$separador;
  $linha .= "Descrição".$separador;

  if($aux_lotacao || $aux_selecao){

    $linha .= "Lotação".$separador;
    $linha .= "Descrição".$separador;
  }

  $linha .= "Vagas".$separador;
  $linha .= "Ativos".$separador;
  $linha .= "Inativos".$separador;
  $linha .= "Pensionistas".$separador;
  $linha .= "Ocupadas".$separador;
  $linha .= "Saldo".$separador;

  fputs($fArquivo,$linha."\n"."\n");

}

for($x = 0; $x < pg_numrows($result_funcoes); $x ++) {

  db_fieldsmemory($result_funcoes, $x);
  
  $totalvaga += $rh37_vagas;
  $totalocup += $ocupados;
  $totalativ += $tot_ativos;
  $totalinat += $tot_inativos;
  $totalpens += $tot_pensionistas;
  $totalsald += $saldo;
  $totalfunc += 1;

  if(isset($formato_emissao) && $formato_emissao == "pdf"){

    if($pdf->gety() > $pdf->h - 30 || $troca != 0 ){

      $pdf->addpage();
      $pdf->setfont('arial','b',8);
  
      if($aux_lotacao || $aux_selecao){

        $pdf->cell(20,$alt,"Cargo","TBL",0,"C",1);
        $pdf->cell(50,$alt,"Descrição","TBL",0,"C",1);
        $pdf->cell(14,$alt,"Lotação","TBL",0,"C",1);
        $pdf->cell(80,$alt,"Descrição","TBL",0,"C",1);
        $pdf->cell(18,$alt,"Vagas","TBL",0,"C",1);
        $pdf->cell(18,$alt,"Ativos","TBL",0,"C",1);
        $pdf->cell(18,$alt,"Inativos","TBL",0,"C",1);
        $pdf->cell(18,$alt,"Pensionistas","TBL",0,"C",1);
        $pdf->cell(18,$alt,"Ocupadas","TBL",0,"C",1);
        $pdf->cell(18,$alt,"Saldo"   ,"TBLR",1,"C",1);
      }
      else{

        $pdf->cell(20,$alt,"Cargo","TBL",0,"C",1);
        $pdf->cell(60,$alt,"Descrição","TBL",0,"C",1);
        $pdf->cell(30,$alt,"Vagas","TBL",0,"C",1);
        $pdf->cell(15,$alt,"Ativos","TBL",0,"C",1);
        $pdf->cell(15,$alt,"Inativos","TBL",0,"C",1);
        $pdf->cell(20,$alt,"Pensionistas","TBL",0,"C",1);
        $pdf->cell(15,$alt,"Ocupadas","TBL",0,"C",1);
        $pdf->cell(15,$alt,"Saldo"   ,"TBLR",1,"C",1);
      }

      $troca   = 0;
      $pre     = 1;
    }
    if($pre == 0)
      $pre = 1;
    else
      $pre = 0;
    
    $pdf->setfont('arial','',7);

    if($aux_lotacao || $aux_selecao){
      
      $pdf->cell(20,$alt,$rh37_funcao,0,0,"C",$pre);
      $pdf->cell(50,$alt,$rh37_descr,0,0,"C",$pre);
      $pdf->cell(14,$alt,$r70_estrut,0,0,"C",$pre);
      $pdf->cell(80,$alt,$r70_descr,0,0,"C",$pre);
      $pdf->cell(18,$alt,$rh37_vagas,0,0,"R",$pre);
      $pdf->cell(18,$alt,$tot_ativos,0,0,"R",$pre);
      $pdf->cell(18,$alt,$tot_inativos,0,0,"R",$pre);
      $pdf->cell(18,$alt,$tot_pensionistas,0,0,"R",$pre);
      $pdf->cell(18,$alt,$ocupados,0,0,"R",$pre);
      $pdf->cell(18,$alt,$saldo,0,1,"R",$pre);
    }
    else{

      $pdf->cell(20,$alt,$rh37_funcao,0,0,"C",$pre);
      $pdf->cell(60,$alt,$rh37_descr,0,0,"L",$pre);
      $pdf->cell(30,$alt,$rh37_vagas,0,0,"R",$pre);
      $pdf->cell(15,$alt,$tot_ativos,0,0,"R",$pre);
      $pdf->cell(15,$alt,$tot_inativos,0,0,"R",$pre);
      $pdf->cell(20,$alt,$tot_pensionistas,0,0,"R",$pre);
      $pdf->cell(15,$alt,$ocupados,0,0,"R",$pre);
      $pdf->cell(15,$alt,$saldo,0,1,"R",$pre);
    }
  }
  else {

    $linha  = $rh37_funcao.$separador;
    $linha .= $rh37_descr.$separador;

    if($aux_lotacao || $aux_selecao){

      $linha .= $r70_estrut.$separador;
      $linha .= $r70_descr.$separador;
    }

    $linha .= $rh37_vagas.$separador;
    $linha .= $tot_ativos.$separador;
    $linha .= $tot_inativos.$separador;
    $linha .= $tot_pensionistas.$separador;
    $linha .= $ocupados.$separador;
    $linha .= $saldo.$separador;

    fputs($fArquivo,$linha."\n");
  }

}

if(isset($formato_emissao) && $formato_emissao == "pdf"){

    $pdf->setfont('arial','b',7);

    if($aux_lotacao || $aux_selecao){

      $pdf->cell(40,$alt,"TOTAIS DE REGISTROS :","T",0,"R",0);
      $pdf->cell(10,$alt,$totalfunc,"T",0,"R",0);
      $pdf->cell(132,$alt,$totalvaga,"T",0,"R",0);
      $pdf->cell(18,$alt,$totalativ,"T",0,"R",0);
      $pdf->cell(18,$alt,$totalinat,"T",0,"R",0);
      $pdf->cell(18,$alt,$totalpens,"T",0,"R",0);
      $pdf->cell(18,$alt,$totalocup,"T",0,"R",0);
      $pdf->cell(18,$alt,$totalsald,"T",1,"R",0);
    }
    else {

      $pdf->cell(40,$alt,"TOTAIS DE REGISTROS :","T",0,"R",0);
      $pdf->cell(10,$alt,$totalfunc,"T",0,"R",0);
      $pdf->cell(60,$alt,$totalvaga,"T",0,"R",0);
      $pdf->cell(15,$alt,$totalativ,"T",0,"R",0);
      $pdf->cell(15,$alt,$totalinat,"T",0,"R",0);
      $pdf->cell(20,$alt,$totalpens,"T",0,"R",0);
      $pdf->cell(15,$alt,$totalocup,"T",0,"R",0);
      $pdf->cell(15,$alt,$totalsald,"T",1,"R",0);
    }

    
    $pdf->Output();

}

else  {

    $linha  = "TOTAIS DE REGISTROS".$separador;
    $linha .= $totalfunc.$separador;

    if($aux_lotacao || $aux_selecao){

      $linha .= $separador;
      $linha .= $separador;
    }

    $linha .= $totalvaga.$separador;
    $linha .= $totalativ.$separador;
    $linha .= $totalinat.$separador;
    $linha .= $totalpens.$separador;
    $linha .= $totalocup.$separador;
    $linha .= $totalsald.$separador;

    fputs($fArquivo,$linha."\n");

    fclose($fArquivo);
}
