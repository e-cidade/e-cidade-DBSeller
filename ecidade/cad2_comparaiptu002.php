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
include(modification("classes/db_projmelhorias_classe.php"));
include(modification("classes/db_editalproj_classe.php"));

$clprojmelhorias = new cl_projmelhorias;
$cleditalproj = new cl_editalproj;
$clprojmelhorias->rotulo->label();

$clrotulo = new rotulocampo;
$clrotulo->label('j14_nome');
$clrotulo->label('d01_numero');
$clrotulo->label('nome');
($HTTP_SERVER_VARS['QUERY_STRING']);
parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
$dbwhere="1=1";
$and="";

if ($ordem=="percentual_imposto" or $ordem=="percentual_taxas") {
  $ordem = "percentual";
}

if(isset($valor) && $valor != ""){
  if($perc=="ma"){
    $dbwhere .=" and percentual > $valor ";
  } elseif ($perc=="me"){
    $dbwhere .=" and percentual < $valor ";
  }elseif ($perc=="mame"){
    $dbwhere .=" and abs(percentual::float8) > $valor ";
  }

}  

$inner = " ";
$isen="nao";
if($isen=='nao' or 1==1){
  $dbwhere .= " 
              and j01_matric
	         not in(  select j01_matric as matric from iptubase  
                             inner join iptuisen on j46_matric = j01_matric 
     		             inner join isenexe on j47_anousu = $ano1 and j46_codigo=j47_codigo 
		       ) 	     
		  
		  
    ";
  
}


if(isset($ordem) && $ordem != ""){
  $dbwhere   .= " order by $ordem $order";
}

$sql =  "
  	SELECT *
    FROM (  SELECT
                j01_matric,
                valor1,
                valor2,
                CASE
                    WHEN valor1 = 0 OR valor2 = 0 THEN 0
                    ELSE round(100 - (valor2 / valor1 * 100),5) * -1
                END AS percentual
            FROM(
                    SELECT *
                    FROM(   
                            SELECT
                                j01_matric,
                                sum1 AS valor1,
                                sum2 AS valor2
                            FROM(
                                    SELECT
                                        j01_matric,
                                        sum1,
                                        sum2
                                    FROM(   
                                            SELECT
                                                j01_matric,
                                                (x.sum1 + x.sum2) AS sum1,
                                                (y.sum1 + y.sum2) AS sum2
                                            FROM iptubase
                                            LEFT JOIN   (   SELECT
                                                                j21_matric,
                                                                sum(j21_valor) as sum1,
                                                                sum(coalesce(j152_valor, 0)) as sum2
                                                            FROM iptucalv
                                                            LEFT JOIN ( SELECT
                                                                            *
                                                                        FROM
                                                                            iptutaxanump
                                                                        INNER JOIN 
                                                                            iptucadtaxaexe ON iptucadtaxaexe.j08_iptucadtaxaexe = iptutaxanump.j151_iptucadtaxaexe
                                                                      ) as txn ON txn.j151_matric = j21_matric
                                                                              AND txn.j08_anousu  = j21_anousu
                                                            LEFT JOIN
                                                                iptutaxacalv ON iptutaxacalv.j152_iptutaxanump = txn.j151_codigo
                                                            WHERE j21_anousu = {$ano1}
                                                            GROUP BY iptucalv.j21_matric
                                                        ) AS x ON x.j21_matric = iptubase.j01_matric
                                            LEFT JOIN   (   SELECT
                                                                j21_matric,
                                                                sum(j21_valor) as sum1,
                                                                sum(coalesce(j152_valor, 0)) as sum2
                                                            FROM iptucalv
                                                            LEFT JOIN ( SELECT
                                                                            *
                                                                        FROM
                                                                            iptutaxanump
                                                                        INNER JOIN 
                                                                            iptucadtaxaexe ON iptucadtaxaexe.j08_iptucadtaxaexe = iptutaxanump.j151_iptucadtaxaexe
                                                                      ) as txn ON txn.j151_matric = j21_matric
                                                                              AND txn.j08_anousu  = j21_anousu
                                                            LEFT JOIN
                                                                iptutaxacalv ON iptutaxacalv.j152_iptutaxanump = txn.j151_codigo
                                                            WHERE j21_anousu = {$ano2}
                                                            GROUP BY iptucalv.j21_matric
                                                        ) AS y ON y.j21_matric = iptubase.j01_matric
                                            WHERE j01_baixa IS NULL
                                        ) AS z
                                ) AS a 
                        ) AS f
                ) AS g
        ) AS h
    WHERE $dbwhere ";

$result = db_query($sql);
$numrows = pg_numrows($result); 
$alt="5";
$pdf = new PDF(); 
$pdf->Open(); 
$pdf->AliasNbPages(); 
$head2 = "Comparativo IPTU";
$head3 = "entre $ano1 e $ano2";
$pdf->AddPage("L");
$pdf->setfillcolor(235);
$pdf->setfont('arial','b',10);
$pdf->cell(20,7,"Matrícula",1,0,"C",1);
$pdf->cell(57,7,$ano1,1,0,"C",1);
$pdf->cell(125,7,$ano2,1,0,"C",1);
$pdf->cell(45,7,"Percentual",1,0,"C",1);
$pdf->setfont('arial','',8);
$pdf->ln();

$quant=0;

for ($i = 0;$i < $numrows;$i++){
  db_fieldsmemory($result,$i,true);

  if ($imprimirsemdif == "nao") {
    if ($percentual == 0) {
      continue;
    }
  }
  
  if ($pdf->gety() > $pdf->h -30  ){
      $pdf->addpage("L");
      $pdf->setfillcolor(235);
      $pdf->setfont('arial','b',10);
      $pdf->cell(20,7,"Matrícula",1,0,"C",1);
      $pdf->cell(57,7,$ano1,1,0,"C",1);
      $pdf->cell(125,7,$ano2,1,0,"C",1);
      $pdf->cell(45,7,"Percentual",1,0,"C",1);
      $pdf->setfont('arial','',8);
      $pdf->ln();
  }  
  $pdf->cell(20,7,$j01_matric,1,0,"C",0);
  $pdf->cell(57,7,$valor1,1,0,"C",0);
  $pdf->cell(125,7,$valor2,1,0,"C",0);
  $pdf->cell(45,7,$percentual,1,0,"C",0);
  $pdf->ln();
  $quant++;
}

$pdf->cell(50,6,"Total de registros: $quant",0,0,"C",0);

$pdf->Output();
?>
