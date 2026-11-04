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
include(modification("libs/db_utils.php"));
set_time_limit(0);

//db_postmemory($HTTP_SERVER_VARS,2);exit;

$oGet = db_utils::postMemory($_GET);
$oGet->setor   = str_replace(",","','",$oGet->setor);
$oGet->bairro  = str_replace(",","','",$oGet->bairro);

$pdf = new PDF(); 
$pdf->Open(); 
$pdf->AliasNbPages(); 
$pdf->setfillcolor(235);
$iMatric            = "";
$alt                = 4;
$fonte              = 8;
$andWhere           = "";
$ValorTotal         = 0;
$ValorTotalIsen     = 0;
$Totalizador        = 0;
$SubValorTotal      = 0;
$SubValorTotalIsen  = 0;
$SubTotalizador     = 0;
$Chave              = null;
$int                = 0;
$nomearquivos       = "";
$arqi               = 0;

switch($oGet->selagrupa){
    case "m":
        $NomeTotal  = "DA MATRÍCULA";
        if($oGet->selordem == "m"){    
            $orderby      = " order by j21_matric, j21_anousu, iptucalh.j17_codhis "; 
        } else if($oGet->selordem == "n"){
            $orderby      = " order by z01_nome, j21_matric, j21_anousu, iptucalh.j17_codhis "; 
        } else if($oGet->selordem == "b"){
            $orderby      = " order by j34_bairro, j21_matric, j21_anousu, iptucalh.j17_codhis "; 
        } else if($oGet->selordem == "s"){
            $orderby      = " order by j34_setor, j21_matric, j21_anousu, iptucalh.j17_codhis "; 
        }
        $NomeGrupo  = "MATRÍCULA";
    break;
    case "b":
        $NomeTotal  = "DO BAIRRO";
        $orderby    = " order by j34_bairro, j21_anousu,iptucalh.j17_codhis "; 
        $NomeGrupo  = "BAIRRO";
    break;
    case "s":
        $NomeTotal  = "DO SETOR";
        $orderby    = " order by j34_setor, j21_anousu,iptucalh.j17_codhis "; 
        $NomeGrupo  = "SETOR";
    break;
}

$head2 = "RELATÓRIO DE VALORES POR HISTÓRICO DE CÁLCULO";
$head3 = "TIPO : ".($oGet->seltipo == "a"?"ANALÍTICO":"SINTÉTICO");
$head4 = "EXERCÍCIO DE ".$oGet->anoexei." À ".$oGet->anoexef;
$head5 = "AGRUPADO POR ".$NomeGrupo;

if($oGet->setor){
    $andWhere .= "and j34_setor in ('".$oGet->setor."')";    
}
if($oGet->bairro){
    $andWhere .= "and j34_bairro in ('".$oGet->bairro."')";    
}

$sSqlCalv  = " SELECT j21_anousu,                                                                                            \n";
$sSqlCalv .= "        j21_matric,                                                                                            \n";
$sSqlCalv .= "        z01_numcgm,                                                                                            \n";
$sSqlCalv .= "        z01_nome,                                                                                              \n";
$sSqlCalv .= "        j34_setor,                                                                                             \n";
$sSqlCalv .= "        j34_bairro,                                                                                            \n";
$sSqlCalv .= "        j13_descr,                                                                                             \n";
$sSqlCalv .= "        k02_codigo,                                                                                            \n";
$sSqlCalv .= "        j17_codhis,                                                                                            \n";
$sSqlCalv .= "        j21_receit,                                                                                            \n";
$sSqlCalv .= "        j17_descr,                                                                                             \n";
$sSqlCalv .= "        j21_valor,                                                                                             \n";
$sSqlCalv .= "        CASE                                                                                                   \n";
$sSqlCalv .= "            WHEN iptucalhconf.j89_codhis IS NOT NULL                                                           \n";
$sSqlCalv .= "            THEN                                                                                               \n";
$sSqlCalv .= "                 (                                                                                             \n";
$sSqlCalv .= "                  SELECT sum(vlr)                                                                              \n";
$sSqlCalv .= "                  FROM (                                                                                       \n";
$sSqlCalv .= "                          SELECT sum(x.j21_valor) as vlr                                                       \n";
$sSqlCalv .= "                            FROM iptucalv x                                                                    \n";
$sSqlCalv .= "                           WHERE x.j21_anousu = iptucalv.j21_anousu                                            \n";
$sSqlCalv .= "                             AND x.j21_matric = iptucalv.j21_matric                                            \n";
$sSqlCalv .= "                             AND x.j21_receit = iptucalv.j21_receit                                            \n";
$sSqlCalv .= "                             AND x.j21_codhis = iptucalhconf.j89_codhis                                        \n";
$sSqlCalv .= "                           UNION                                                                               \n";
$sSqlCalv .= "                           SELECT                                                                              \n";
$sSqlCalv .= "                               sum(j08_valor) as vlr                                                           \n";
$sSqlCalv .= "                           FROM                                                                                \n";
$sSqlCalv .= "                               v_iptubasetaxa as v                                                             \n";
$sSqlCalv .= "                           WHERE v.j08_anousu   = iptucalv.j21_anousu                                          \n";
$sSqlCalv .= "                             AND v.j151_matric  = iptucalv.j21_matric                                          \n";
$sSqlCalv .= "                             AND v.j152_receit  = iptucalv.j21_receit                                          \n";
$sSqlCalv .= "                             AND v.j08_iptucalh = iptucalhconf.j89_codhis                                      \n";
$sSqlCalv .= "                  ) as xx                                                                                      \n";
$sSqlCalv .= "                 )                                                                                             \n";
$sSqlCalv .= "            ELSE 0                                                                                             \n";
$sSqlCalv .= "        END AS j21_valorisen                                                                                   \n";
$sSqlCalv .= " FROM (                                                                                                        \n";
$sSqlCalv .= "         SELECT                                                                                                \n";
$sSqlCalv .= "              j21_anousu                                                                                       \n";
$sSqlCalv .= "             ,j21_matric                                                                                       \n";
$sSqlCalv .= "             ,j21_receit                                                                                       \n";
$sSqlCalv .= "             ,j21_valor                                                                                        \n";
$sSqlCalv .= "             ,j21_codhis                                                                                       \n";
$sSqlCalv .= "           FROM iptucalv x                                                                                     \n";
$sSqlCalv .= "          UNION                                                                                                \n";
$sSqlCalv .= "          SELECT                                                                                               \n";
$sSqlCalv .= "              j08_anousu  as j21_anousu                                                                        \n";
$sSqlCalv .= "             ,j151_matric as j21_matric                                                                        \n";
$sSqlCalv .= "             ,j152_receit as j21_receit                                                                        \n";
$sSqlCalv .= "             ,j152_valor  as j21_valor                                                                         \n";
$sSqlCalv .= "             ,j152_codhis as j21_codhis                                                                        \n";
$sSqlCalv .= "          FROM                                                                                                 \n";
$sSqlCalv .= "              v_iptubasetaxa as v                                                                              \n";
$sSqlCalv .= " ) as iptucalv                                                                                                 \n";
$sSqlCalv .= " INNER JOIN iptucalh ON iptucalh.j17_codhis = j21_codhis                                                       \n";
$sSqlCalv .= " LEFT  JOIN iptucalhconf conf ON conf.j89_codhis = iptucalh.j17_codhis                                         \n";
$sSqlCalv .= " LEFT  JOIN iptucalhconf ON iptucalhconf.j89_codhispai = j21_codhis                                            \n";
$sSqlCalv .= " INNER JOIN tabrec ON tabrec.k02_codigo = j21_receit                                                           \n";
$sSqlCalv .= " INNER JOIN iptubase ON iptubase.j01_matric = j21_matric                                                       \n";
$sSqlCalv .= " INNER JOIN lote ON lote.j34_idbql = j01_idbql                                                                 \n";
$sSqlCalv .= " INNER JOIN bairro ON bairro.j13_codi = j34_bairro                                                             \n";
$sSqlCalv .= " INNER JOIN cgm ON cgm.z01_numcgm = j01_numcgm                                                                 \n";
$sSqlCalv .= " LEFT  JOIN iptucadtaxaexe ON iptucadtaxaexe.j08_tabrec = j21_receit                                           \n";
$sSqlCalv .= "                          AND iptucadtaxaexe.j08_anousu = j21_anousu                                           \n";
$sSqlCalv .= " WHERE 1=1                                                                                                     \n";
$sSqlCalv .= "   AND j21_anousu BETWEEN {$oGet->anoexei} AND {$oGet->anoexef}                                                \n";
$sSqlCalv .= "   AND conf.j89_codhis IS NULL                                                                                 \n";
$sSqlCalv .= "   {$andWhere}                                                                                                 \n";
$sSqlCalv .= "   {$orderby}                                                                                                  \n";

$pdf->addpage();
    
$rsCalv = db_query($sSqlCalv) or die ($sSqlCalv); 
    
for($i=0;    $i <= pg_num_rows($rsCalv); $i++){
    
    $oCalv  = db_utils::fieldsMemory($rsCalv,$i);

    //---------------------------------- INÍCIO DOS AGRUPAMENTOS --------------------------------------//
            
    if($oGet->selagrupa == "s"){
         $campoPrinc = $oCalv->j34_setor; 
    }else if($oGet->selagrupa == "b"){
         $campoPrinc = $oCalv->j13_descr; 
    }else if($oGet->selagrupa == "m"){
         $campoPrinc = $oCalv->j21_matric; 
    }    
            
    if($int == 2000){                                                                                         //
        $arqi++;                                                                                              //
        $arq            = "tmp/RelHistCalc_parte_".$arqi.".pdf";                                              //
        $nomearquivos  .= "tmp/RelHistCalc_parte_".$arqi.".pdf# Download Relatório_Parte_$arqi.pdf|";         //
        $pdf->Output($arq,false,true);                                                                        //
        unset($pdf);                                                                                          //
                                                                                                              //
        $pdf = new PDF();                                                                                     //
        $pdf->Open();                                                                                         // VERIFICA TAMANHO DOC E QUEBRA ARQUIVO
        $pdf->AliasNbPages();                                                                                 // 
        $pdf->setfillcolor(235);                                                                              //
        $pdf->addpage();                                                                                      //
        $int = 0;                                                                                             //
        $i --;                                                                                                //
        continue;                                                                                             //
    }                                                                                                         //
                                                                                                              //
                                                                                                                     
    if($oGet->seltipo == "a"){                                                                                  
                                                                                                                    
        if($campoPrinc != $Chave && $i !=0){                                                        //

            if($oCalv->j21_matric != $iMatric && $oGet->selagrupa=="m"){                            //
                $pdf->setfont('arial','b',$fonte);                                                  //
                $pdf->ln();                                                                         //
                $pdf->setx(32);                                                                     //
                $pdf->cell(15,$alt,'Matrícula',0,0,"C",1);                                          //
                $pdf->cell(65,$alt,'Nome'     ,0,0,"C",1);                                          //
                $pdf->cell(50,$alt,'Bairro'        ,0,0,"C",1);                                     //
                $pdf->cell(20,$alt,'Setor'        ,0,1,"C",1);                                      //
                $pdf->ln(2);                                                                        // IMPRIME CABEÇALHO DA MATRÍCULA
                                                                                                    //
                $pdf->setfont('arial','',$fonte);                                                   //
                $pdf->setx(32);                                                                     //
                $pdf->cell(15,$alt,$Mat                ,0,0,"C",0);                                 //
                $pdf->cell(65,$alt,$Nome            ,0,0,"L",0);                                    //
                $pdf->cell(50,$alt,$Historico ,0,0,"L",0);                                          //
                $pdf->cell(20,$alt,$Setor     ,0,1,"C",0);                                          //
                $iMatric = $oCalv->j21_matric;                                                      //
            }                                                                                       // 
                                                                                                    //
            $pdf->ln();                                                     
            $pdf->setfont('arial','b',$fonte);                              
            $pdf->setx(32);                                                 
            $pdf->cell(20,$alt,'Exercício',0,0,"C",1);                      
            $pdf->cell(40,$alt,'Histórico',0,0,"C",1);                      
            $pdf->cell(30,$alt,'Valor'        ,0,0,"C",1);                      
            $pdf->cell(30,$alt,'Isenção'    ,0,0,"C",1);                      
            $pdf->cell(30,$alt,'Total'        ,0,1,"C",1);                      
            $pdf->setfont('arial','',$fonte);                               
                                                                        
            foreach( $aAgrupaHist as $campPri => $valor1 ){

                foreach( $valor1 as $exe => $valor2){
                                                                                      
                    $SubValorTotal      = 0;                                  
                    $SubValorTotalIsen  = 0;                                  
                    $SubTotalizador     = 0;                                  
                                                                                  
                    $pdf->ln();                                                                                                                                                                         
                    $pdf->setx(32);                                                                                                                                                                 
                    $pdf->cell(20,$alt,$exe ,0,0,"C",0);                                                                                                                     
                            
                    foreach( $valor2  as $hist => $valor3){

                        $pdf->setx(32);                                                                                
                        $pdf->cell(20,$alt,"",0,0,"C",0);                                                              
                        $pdf->cell(40,$alt,$hist,                                                                        0,0,"L",0);
                        $pdf->cell(30,$alt,db_formatar($valor3['valor'],"f"),                                            0,0,"R",0);
                        $pdf->cell(30,$alt,db_formatar($valor3['valorisen'],"f"),                                        0,0,"R",0);
                        $pdf->cell(30,$alt,db_formatar(($valor3['valor'] + $valor3['valorisen']),"f"),0,1,"R",0);      
                        $SubValorTotal            +=  $valor3['valor'];                                                                                                                  
                        $SubValorTotalIsen  +=  $valor3['valorisen'];
                        $SubTotalizador     +=  ($valor3['valor'] + $valor3['valorisen']);
                    }                                                                                                
                        
                    $pdf->setx(92);
                    $pdf->cell(30,$alt,db_formatar($SubValorTotal,"f"),      0,0,"R",0);                     
                    $pdf->cell(30,$alt,db_formatar($SubValorTotalIsen,"f"),  0,0,"R",0);                     
                    $pdf->cell(30,$alt,db_formatar($SubTotalizador ,"f"),    0,1,"R",0);      
                }                                                                                                   
            }                                                                                                     

            if($campoPrinc != $Chave && $i != 0){
                                                                                                                      //
                $pdf->ln();                                                                                           //
                $pdf->setx(52);                                                                                       //
                $pdf->cell(60,$alt,'TOTAL GERAL '.$NomeTotal.' '.$Chave.' : ' ,0,1,"L",0);                            //
                $pdf->ln();                                                                                           //
                                                                                                                      //
                foreach( $aAgrupaTotal as $campPri  => $valor1 ){                                                     //
                    foreach( $valor1  as $hist => $valor2){                                                           //
                         $pdf->setx(32);                                                                              //
                         $pdf->cell(20,$alt,"",                                                     0,0,"C",0);       //
                         $pdf->cell(40,$alt,$hist,                                                  0,0,"L",0);       // IMPRIME TOTAL GERAL POR ( MATRÍCULA, SETOR OU BAIRRO )
                         $pdf->cell(30,$alt,db_formatar($valor2['valor'],"f"),                      0,0,"R",0);       //
                         $pdf->cell(30,$alt,db_formatar($valor2['valorisen'],"f"),                  0,0,"R",0);       //
                         $pdf->cell(30,$alt,db_formatar(($valor2['valor'] + $valor2['valorisen']),"f"),0,1,"R",0);    //
                    }                                                                                                 //
                }                                                                                                     //
                                                                                                                      //
                $pdf->ln();                                                                                           //
                $pdf->setx(92);                                                                                       //
                $pdf->cell(30,$alt,db_formatar($ValorTotal,"f")            ,0,0,"R",0);                               //
                $pdf->cell(30,$alt,db_formatar($ValorTotalIsen,"f")    ,0,0,"R",0);                                   //
                $pdf->cell(30,$alt,db_formatar($Totalizador,"f")        ,0,1,"R",0);                                  //
                $pdf->ln();                                                                                           //

                $SubValorTotal            = 0;                                                                         
                $SubValorTotalIsen  = 0;                                                                         
                $SubTotalizador     = 0;                                                                         
                $ValorTotal            = 0;                                                                             
                $ValorTotalIsen = 0;                                                                             
                $Totalizador    = 0;                                                                             
                unset($aAgrupaSubTotal);                                                                         
                unset($aAgrupaTotal);                                                                            
                unset($aAgrupaHist);                                                                             
                $int ++;                                                                                           
                                                                                                               
            }                                                                                                 
        }
    }

    if ($i == pg_num_rows($rsCalv)){
        break;    
    }

    $Chave = $campoPrinc;
            
    if(isset($aAgrupaHist [$campoPrinc][$oCalv->j21_anousu][$oCalv->j17_descr])){                                                                 //
        $aAgrupaHist [$campoPrinc][$oCalv->j21_anousu][$oCalv->j17_descr]['valor']       += $oCalv->j21_valor;                                    //
        $aAgrupaHist [$campoPrinc][$oCalv->j21_anousu][$oCalv->j17_descr]['valorisen']   += $oCalv->j21_valorisen;                                //
        $aAgrupaHist [$campoPrinc][$oCalv->j21_anousu][$oCalv->j17_descr]['totalizador'] += ($oCalv->j21_valor + $oCalv->j21_valorisen);          //
    }else{                                                                                                                                        // ARRAY COM AS INFOMAÇÕES POR EXERCÍCIO
        $aAgrupaHist [$campoPrinc][$oCalv->j21_anousu][$oCalv->j17_descr]['valor']        = $oCalv->j21_valor;                                    //
        $aAgrupaHist [$campoPrinc][$oCalv->j21_anousu][$oCalv->j17_descr]['valorisen']    = $oCalv->j21_valorisen;                                //
        $aAgrupaHist [$campoPrinc][$oCalv->j21_anousu][$oCalv->j17_descr]['totalizador']  = ($oCalv->j21_valor + $oCalv->j21_valorisen);          //
    }                                                                                                                                             //

    if(isset($aAgrupaTotal[$campoPrinc][$oCalv->j17_descr])){                                                           //
        $aAgrupaTotal[$campoPrinc][$oCalv->j17_descr]['valor']       += $oCalv->j21_valor;                              //
        $aAgrupaTotal[$campoPrinc][$oCalv->j17_descr]['valorisen']   += $oCalv->j21_valorisen;                          //
        $aAgrupaTotal[$campoPrinc][$oCalv->j17_descr]['totalizador'] += ($oCalv->j21_valor + $oCalv->j21_valorisen);    //
    }else{                                                                                                              // ARRAY COM AS INFORMAÇÔES DO TOTAL POR ( MATRÍCULA, SETOR OU BAIRRO ) 
        $aAgrupaTotal[$campoPrinc][$oCalv->j17_descr]['valor']        = $oCalv->j21_valor;                              //
        $aAgrupaTotal[$campoPrinc][$oCalv->j17_descr]['valorisen']    = $oCalv->j21_valorisen;                          //
        $aAgrupaTotal[$campoPrinc][$oCalv->j17_descr]['totalizador']  = ($oCalv->j21_valor + $oCalv->j21_valorisen);    //
    }                                                                                                                   //
                
    if(isset($aTotalFinal[$oCalv->j17_descr])){                                                                            //
        $aTotalFinal[$oCalv->j17_descr]['valor']       += $oCalv->j21_valor;                                               //
        $aTotalFinal[$oCalv->j17_descr]['valorisen']   += $oCalv->j21_valorisen;                                           //
        $aTotalFinal[$oCalv->j17_descr]['totalizador'] += ($oCalv->j21_valor + $oCalv->j21_valorisen);                     //
    }else{                                                                                                                 // ARRAY COM AS INFORMAÇÕES DO TOTAL GERAL
        $aTotalFinal[$oCalv->j17_descr]['valor']        = $oCalv->j21_valor;                                               //
        $aTotalFinal[$oCalv->j17_descr]['valorisen']    = $oCalv->j21_valorisen;                                           //
        $aTotalFinal[$oCalv->j17_descr]['totalizador']  = ($oCalv->j21_valor + $oCalv->j21_valorisen);                     //
    }                                                                                                                      //
                
    $Mat             = $oCalv->j21_matric;
    $Nome            = $oCalv->z01_nome;
    $Historico       = $oCalv->j13_descr;
    $Setor           = $oCalv->j34_setor;
    $ValorTotal     += $oCalv->j21_valor;
    $ValorTotalIsen += $oCalv->j21_valorisen;
    $Totalizador    += ($oCalv->j21_valor + $oCalv->j21_valorisen);
        
} //----------------------------------- FIM DO FOR ------------------------------------------//
    
$ValorTotal     = 0;                                                                                                //
$ValorTotalIsen = 0;                                                                                                //    
$Totalizador    =    0;                                                                                             //
if($oGet->seltipo == "a"){                                                                                          //
    $pdf->addpage();                                                                                                //
}                                                                                                                   //
$pdf->ln();                                                                                                         //
$pdf->setx(42);                                                                                                     //
$pdf->cell(60,$alt,'TOTAL GERAL POR '.$NomeGrupo.' : ' ,0,1,"L",0);                                                 //
$pdf->ln(2);                                                                                                        //
$pdf->setfont('arial','b',$fonte);                                                                                  //
$pdf->setx(42);                                                                                                     //
$pdf->cell(40,$alt,'Histórico',0,0,"C",1);                                                                          //
$pdf->cell(30,$alt,'Valor'        ,0,0,"C",1);                                                                      //
$pdf->cell(30,$alt,'Isenção'    ,0,0,"C",1);                                                                        //
$pdf->cell(30,$alt,'Total'        ,0,1,"C",1);                                                                      //
$pdf->setfont('arial','',$fonte);                                                                                   //
                                                                                                                    //
foreach( $aTotalFinal as $descr => $descrValor ){                                                                   // IMPRIME TOTALIZADOR GERAL
    $pdf->setx(42);                                                                                                 //
    $pdf->cell(40,$alt,$descr,                                                             0,0,"L",0);              //
    $pdf->cell(30,$alt,db_formatar($descrValor['valor'],"f"),                              0,0,"R",0);              //
    $pdf->cell(30,$alt,db_formatar($descrValor['valorisen'],"f"),                          0,0,"R",0);              //
    $pdf->cell(30,$alt,db_formatar(($descrValor['valor'] + $descrValor['valorisen']),"f"), 0,1,"R",0);              //
                                                                                                                    //
    $ValorTotal     += $descrValor['valor'];                                                                        //
    $ValorTotalIsen += $descrValor['valorisen'];                                                                    //
    $Totalizador    += ($descrValor['valor']+ $descrValor['valorisen']);                                            //
                                                                                                                    //
}                                                                                                                   //
                                                                                                                    //
$pdf->setfont('arial','b',$fonte);                                                                                  //
$pdf->setx(82);                                                                                                     //
$pdf->cell(30,$alt,db_formatar($ValorTotal,"f"),         0,0,"R",0);                                                //
$pdf->cell(30,$alt,db_formatar($ValorTotalIsen,"f"),     0,0,"R",0);                                                //
$pdf->cell(30,$alt,db_formatar($Totalizador,"f"),        0,1,"R",0);                                                //
$pdf->ln();                                                                                                         //

$arqi++;
$arq           = "tmp/RelHistCalc_parte_".$arqi.".pdf";
$nomearquivos .= "tmp/RelHistCalc_parte_".$arqi.".pdf# Download Relatório_Parte_".$arqi.".pdf";
$pdf->Output($arq,false,true);

echo "<script>";
echo "  listagem = '$nomearquivos';";
echo "  parent.js_montarlista(listagem,'form1');";
echo "</script>";
