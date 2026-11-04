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

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
include(modification("libs/db_sql.php"));
include(modification("classes/db_prontproced_classe.php"));
parse_str($HTTP_SERVER_VARS['QUERY_STRING']);

$clprontproced = new cl_prontproced;
$clprontproced->rotulo->label();

$unidade = str_replace("X",",",$unidades);
$data1 = str_replace("X","-",$data1);
$data2 = str_replace("X","-",$data2);

$sql = "SELECT  count(*) as quantidade,
                sau_procedimento.sd63_i_codigo,
                sau_procedimento.sd63_c_procedimento,
                rhcbo.rh70_sequencial,
                rhcbo.rh70_descr
        FROM prontproced
         INNER JOIN sau_procedimento ON prontproced.sd29_i_procedimento = sau_procedimento.sd63_i_codigo
         inner join prontuarios    on prontuarios.sd24_i_codigo = prontproced.sd29_i_prontuario
         inner join especmedico    on especmedico.sd27_i_codigo = prontproced.sd29_i_profissional
         INNER JOIN rhcbo ON rhcbo.rh70_sequencial = especmedico.sd27_i_rhcbo
        WHERE prontproced.sd29_d_data BETWEEN '$data1' and '$data2'
        GROUP BY sau_procedimento.sd63_i_codigo,
         sau_procedimento.sd63_c_procedimento,
         rhcbo.rh70_sequencial,
         rhcbo.rh70_descr
        ORDER BY rhcbo.rh70_sequencial,
         rhcbo.rh70_descr
        ";

$result = db_query($sql);
$linhas = pg_num_rows($result);
//db_criatabela($result);
//exit;
if($linhas == 0){
 echo "<table width='100%'>
        <tr>
         <td align='center'><font color='#FF0000' face='arial'><b>Nenhum Registro para o Relatório<br><input type='button' value='Fechar' onclick='window.close()'></b></font></td>
        </tr>
       </table>";
 exit;
}
$pdf = new ECidade\Pdf\Pdf();
$pdf->init(false);
$pdf->AliasNbPages();
$pdf->addTitulo("Relatório de Especialidades");
$pdf->addTitulo("Periodo:".substr($data1,8,2)."/".substr($data1,5,2)."/".substr($data1,0,4)." A ".substr($data2,8,2)."/".substr($data2,5,2)."/".substr($data2,0,4));

$pdf->addpage();
$pri = true;
$g_total = 0;
$s_total = 0;
$unid = "";
$cor1 = 0;
$cor2 = 1;
$cor = "";
for ($i=0;$i<$linhas;$i++){
 db_fieldsmemory($result,$i);
 if($unid!=$rh70_sequencial){
  if ( $unid != "" ){
     $pdf->cell(190,4,"Total da Especialidade: $g_total",1,1,"R",0);
     $g_total = 0;
  }
  $pdf->setfillcolor(180);
  $pdf->setfont('arial','b',8);
  $pdf->cell(190,4,"Especialidade: $rh70_sequencial - $rh70_descr",1,1,"L",1);
  $pdf->cell(190,1,"",0,1,"L",0);
  $pdf->setfont('arial','',7);
  $pdf->setfillcolor(240);
  $unid = $rh70_sequencial;
 }
 $pdf->cell(5,4,"",0,0,"L",0);
 $pdf->cell(160,4,"Procedimento: $sd63_i_codigo - $sd63_c_procedimento","BTL",0,"L",1);
 $pdf->cell(20,4,"Total: $quantidade","BTR",0,"L",1);
 $pdf->cell(5,4,"",0,1,"L",0);
 $s_total += $quantidade;
 $g_total += $quantidade;

}
$pdf->cell(190,4,"Total da Especialidade: $g_total",1,1,"R",0);
$pdf->setfont('arial','b',9);
$pdf->cell(190,6,"Total Geral das Especialidades: $s_total",1,1,"R",0);
$pdf->output('I');
?>
