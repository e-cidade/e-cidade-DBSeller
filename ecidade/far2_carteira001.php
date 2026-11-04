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
include(modification("classes/db_far_controle_classe.php"));
include(modification("classes/db_far_controlemed_classe.php"));
include(modification("classes/db_far_retiradaitens_classe.php"));
include(modification("classes/db_cgs_und_classe.php"));
require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");

use ECidade\Pdf\Pdf;
parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
$clfar_controle = new cl_far_controle;
$clfar_controlemed = new cl_far_controlemed;
$clfar_retiradaitens = new cl_far_retiradaitens;
$clcgs_und = new cl_cgs_und;
$coddepto= db_getsession("DB_coddepto");
$descrdepto= db_getsession("DB_nomedepto");
$nome=db_getsession("DB_instit");
$ano=date("Y");
$hoje=date("Y-m-d",db_getsession("DB_datausu"));
$sqlpref  = "select nomeinst from db_config where codigo = ".db_getsession("DB_instit");
$resultpref = db_query($sqlpref);

db_fieldsmemory($resultpref,0);

$result = $clfar_controlemed->sql_record($clfar_controlemed->sql_query("","*","","fa10_d_dataini<= '$hoje' and (fa10_d_datafim is null or fa10_d_datafim>= '$hoje') and fa11_i_cgsund=$fa11_i_cgsund"));

if($clfar_controlemed->numrows>0){
   	  db_fieldsmemory($result,0);
      
}		

if($clfar_controlemed->numrows==0){?>
<table width='100%'>
    <tr>
        <td align='center'>
            <font color='#FF0000' face='arial'>
                <b>Nenhum registro encontrado.<br>
                    <input type='button' value='Fechar' onclick='window.close()'></b>
            </font>
        </td>
    </tr>
</table>
<?php
 exit;
}

$pdf = new Pdf();
$pdf->init(false);
$pdf->AliasNbPages();
$pdf->addTitulo("Carteirinha de Medicamento") ;
$pdf->ln(5);
$cont=0;


for($s=0; $s < $clfar_controlemed->numrows; $s++){	
   	 db_fieldsmemory($result,$s);
	  
     if($s%8==0){

        if($cont%3==0){
            $pdf->addpage('P');
            $altura=20;
            $largura = $pdf->getX();
        }
        
       $pdf->rect( $largura, $altura+35, 95, 45, "D");
       $pdf->setfont('arial','b',7);
        $pdf->text( $largura+15,$altura+38 , $nomeinst);
        $pdf->text( $largura+25, $altura+42,$descrdepto);

       $pdf->setfont('arial','b',10);
       $pdf->text( $largura+22, $altura+46, "Carteirinha de Medicamento");
       $pdf->setfont('arial','',10);
       $pdf->text( $largura+35, $altura+50, $ano);
       
       $pdf->setfont('arial','b',7);
       $pdf->text( $largura+3, $altura + 55, "CGS:".$fa11_i_cgsund." - ".$nomeCgs);
       $pdf->text( $largura+3, $altura + 64, "Mãe:".$z01_v_mae);
       $pdf->text( $largura+3, $altura + 69, "Nasc:".db_formatar($z01_d_nasc,'d'));
       $pdf->text( $largura+60, $altura + 69, "Sexo:".$z01_v_sexo);
       $pdf->text( $largura+3, $altura + 75, "Cartão SUS:".$s115_c_cartaosus);
       $pdf->text( $largura+3, $altura + 79, "Data de Validade: 31/12/".$ano);

       $pdf->rect( $pdf->getX()+95, $altura + 35, 95, 45, "D");
       $pdf->setfont('arial','b',7);
       $s==0 ? $pdf->text( $largura+125, $altura + 40, "Lista de Medicamento") : $pdf->text( $largura+125, $altura + 40, "Continuação");
       $pdf->text( $largura+98, $altura + 43, "Cód.");
       $pdf->text( $largura+105, $altura + 43, "Descrição");
       $pdf->text( $largura+145, $altura + 43, "Qtde");
       $pdf->text( $largura+152, $altura + 43, "Unid.");
       $pdf->text( $largura+165, $altura + 43, "Freq.");
       $pdf->text( $largura+173, $altura + 43, "Programa");
       $pdf->text( $largura+98, $altura + 74, "Obs:");
       
       $pdf->setFont('arial','',7);
       $pdf->text( $largura+105, $altura+74,substr($fa11_t_obs,0,53));
       $pdf->text( $largura+105, $altura+76,substr($fa11_t_obs,53,55));
	   $altura+= 47;
       $cont++;
	  }  
      
  $pdf->setfont('arial','',7);	  
  $pdf->text( $largura+98,  $altura, $fa10_i_codigo);
  $pdf->text( $largura+105, $altura,substr($m60_descr,0,26));
  $pdf->text( $largura+145, $altura,$fa10_i_quantidade);
  $pdf->text( $largura+152, $altura,substr($m61_descr,0,8));
  $pdf->text( $largura+165, $altura,$fa10_i_prazo);
  $pdf->text( $largura+173, $altura,substr($fa12_c_descricao,0,8));
  $altura+=3;

} 	

$pdf->Output("I");