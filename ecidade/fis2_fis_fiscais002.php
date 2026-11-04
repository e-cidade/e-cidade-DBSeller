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
 *  02111-1307, USA.                                                  
 *  
 *  Copia da licenca no diretorio licenca/licenca_en.txt 
 *                                licenca/licenca_pt.txt 
 */

require_once(modification("fpdf151/pdf.php"));
require_once(modification("libs/db_sql.php"));
require_once(modification("classes/db_fis_cadfiscais_classe.php"));

$clcadfiscais = new cl_fis_cadfiscais;

$clrotulo = new rotulocampo;
$clrotulo->label('id_usuario');
$clrotulo->label('nome');
$clrotulo->label('descrdepto');

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
//db_postmemory($HTTP_SERVER_VARS,2);exit;

if($ordem == "a") {
$desc_ordem = "ALFABÉTICA";
$order_by = "nome";
}
else {
$desc_ordem = "NUMÉRICA";
$order_by = "fis_cadfiscais.id_usuario";
}
$head3 = "RELATÓRIO DE FISCAIS ";
$head5 = "ORDEM $desc_ordem";

$sql = " SELECT db_usuarios.id_usuario, db_usuarios.nome, db_depart.descrdepto, fis_datalimitefiscal.data
         FROM fiscalizacao.fis_cadfiscais 
         INNER JOIN db_usuarios ON db_usuarios.id_usuario = fis_cadfiscais.id_usuario
         INNER JOIN db_depusu ON db_depusu.id_usuario = fis_cadfiscais.id_usuario
         INNER JOIN db_depart ON  db_depart.coddepto = db_depusu.coddepto
         LEFT JOIN fiscalizacao.fis_datalimitefiscal ON fis_cadfiscais.id_usuario = fis_datalimitefiscal.fiscal
         WHERE db_depart.instit = ".db_getsession('DB_instit')." ORDER BY ".$order_by;

// Query antiga
//$clcadfiscais->sql_query_descrdepto("","db_usuarios.id_usuario, db_usuarios.nome, db_depart.descrdepto",$order_by," db_depart.instit = ".db_getsession('DB_instit') )

$result = $clcadfiscais->sql_record($clcadfiscais->sql_query_descrdepto("","db_usuarios.id_usuario, db_usuarios.nome, db_depart.descrdepto",$order_by," db_depart.instit = ".db_getsession('DB_instit') ));

if ($clcadfiscais->numrows == 0){
   db_redireciona('db_erros.php?fechar=true&db_erro=Não existem fiscais cadastrados.');
}
$pdf = new PDF(); 
$pdf->Open(); 
$pdf->AliasNbPages(); 
$total = 0;
$pdf->setfillcolor(235);
$pdf->setfont('arial','b',8);
$troca = 1;
$altH = 6;
$alt = 4;
$total = 0;
$id_usuarioaux = ""; 
for($x = 0; $x < $clcadfiscais->numrows;$x++){
   db_fieldsmemory($result,$x);
   if ($pdf->gety() > $pdf->h - 30 || $troca != 0 ){
      $pdf->addpage();
      $pdf->setfont('arial','b',8);
      $pdf->cell(20,$altH,'Código',1,0,"C",1);
      $pdf->cell(70,$altH,'Nome',1,0,"C",1); 
      $pdf->cell(30,$altH,'Data limite', 1,0,'C',1);
      $pdf->cell(72,$altH,'Departamento',1,1,"C",1); 
      $troca = 0;
   }
   $pdf->setfont('arial','',7);
   if($id_usuario!=$id_usuarioaux){
      $pdf->cell(20,$alt,$id_usuario,0,0,"C",0);
      $pdf->cell(70,$alt,$nome,0,0,"L",0);
      $pdf->cell(30,$alt,$data, 0,0,'L',0);
      $id_usuarioaux = $id_usuario; 
   }else{
      $pdf->cell(20,$alt,"' '",0,0,"C",0);
      $pdf->cell(70,$alt,"' '",0,0,"L",0);
      $pdf->cell(30,$alt,"",0,0,"C",0);
   }

   $pdf->cell(72,$alt,$descrdepto,0,1,"L",0);
   $total++;
}
$pdf->setfont('arial','b',8);
$pdf->cell(130,$alt,'TOTAL DE FISCAIS  :  '.$total,"T",0,"L",0);
$pdf->Output();
?>