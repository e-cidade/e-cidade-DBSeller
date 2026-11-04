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
require_once(modification("fpdf151/assinatura.php"));
require_once(modification("libs/db_sql.php"));
require_once(modification("classes/db_emppresta_classe.php"));
require_once(modification("classes/db_empprestaitem_classe.php"));

$clemppresta = new cl_emppresta;
$clempprestaitem = new cl_empprestaitem;
$classinatura = new cl_assinatura;

$clemppresta->rotulo->label();
$clempprestaitem->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label('e44_descr');
$clrotulo->label('z01_nome');
$clrotulo->label('e60_codemp');

parse_str($_SERVER['QUERY_STRING']);
//db_postmemory($HTTP_SERVER_VARS,2);exit;

$valortotal = 0;

if(isset($e60_codemp)){

  $codemp = explode("/",$e60_codemp);
  if(count($codemp) == 1){
    $ano = db_getsession("DB_anousu");
  }else{
    $ano = $codemp[1];
  }
  $codemp = $codemp[0];
  $where[] = " e60_codemp = '$codemp' and e60_anousu = $ano and e60_instit = ".db_getsession("DB_instit");

}else{

  $where[] = "e45_numemp=$e60_numemp and e60_instit = ".db_getsession("DB_instit");
}
// olhamos o movimento na agenda para ver se a prestção nao foi estornada
//$where[] = "not exists ( select 1 from empagemov where e81_codmov = e45_codmov and e81_cancelado is not null)";

//$where[] = "e45_conferido is not null";
//$where[] = "and exists ( select 1 from empprestaitem where e60_codemp = e82_codemp and e60_numemp = e82_numemp )";
$where = implode(" and " , $where);


$campos = " e45_codmov,
            e45_numemp,
            (
              select k13_conta || ' - ' ||  k13_descr
                from coremp p 
                JOIN corrente r ON r.k12_id = p.k12_id
                               AND r.k12_data = p.k12_data
                               AND r.k12_autent = p.k12_autent
                JOIN saltes ON k13_conta = k12_conta
               where p.k12_codord = e82_codord
               limit 1
            ) as k13_conta, 
            '' as k13_descr, 
            e82_codord ,
            e45_data,
            e45_obs,
            e45_tipo,
            e45_acerta,
            e45_conferido,
            z01_nome,
            e44_descr,
            e60_codemp,
            e60_vlremp,
            e60_vlranu,
            e60_vlrliq,
            e60_coddot,
            fc_estruturaldotacao(e60_anousu, e60_coddot) AS dl_estrutural
         ";

$sql = $clemppresta->sql_query_prest_contas(null, $campos,  null, $where);
$result = db_query($sql);

//echo $sql; die();
if (pg_num_rows($result) == 0){
   db_redireciona('db_erros.php?fechar=true&db_erro=Não existem registros cadastrados.');
}


db_fieldsmemory($result,0);
$e60_numemp = $e45_numemp;

$head3 = "PLANILHA DE PRESTAÇÃO DE CONTAS";
$head4 = "EMPENHO : $e60_codemp ";
$head5 = "NÚMERO : $e60_numemp ";
$head6 = "DATA : ".date("d/m/Y",db_getsession('DB_datausu'));

$pdf = new PDF();
$pdf->Open();
$pdf->AliasNbPages();
$pdf->addpage();
$total = 0;
$pdf->setfillcolor(235);
$pdf->setfont('arial','b',8);
$troca = 1;
$alt = 4;
$total = 0;
$RLe45_acerta = str_replace(['de ', 'da '], '',$RLe45_acerta);
$RLe45_numemp = "Seq. Empenho";

for($x = 0; $x < pg_num_rows($result);$x++){

   db_fieldsmemory($result,$x);
   $pdf->setfont('arial','b',8);
   $pdf->cell(30,$alt,$RLe60_codemp." : ",0,0,"R",0);
   $pdf->setfont('arial','',8);
   $pdf->cell(60,$alt,$e60_codemp,0,0,"L",0);
   $pdf->setfont('arial','b',8);
   $pdf->cell(30,$alt,$RLe45_numemp." : ",0,0,"R",0);
   $pdf->setfont('arial','',8);
   $pdf->cell(60,$alt,$e45_numemp,0,1,"L",0);
   $pdf->setfont('arial','b',8);
   $pdf->cell(30,$alt,$RLz01_nome." : ",0,0,"R",0);
   $pdf->setfont('arial','',8);
   $pdf->cell(60,$alt,$z01_nome,0,1,"L",0);
   $pdf->setfont('arial','b',8);
   $pdf->cell(30,$alt,$RLe45_data.' : ',0,0,"R",0);
   $pdf->setfont('arial','',8);
   $pdf->cell(60,$alt,db_formatar($e45_data,'d'),0,0,"L",0);
   $pdf->setfont('arial','b',8);
   $pdf->cell(30,$alt,'Dotação : ',0,0,"R",0);
   $pdf->setfont('arial','',8);
   $pdf->cell(60,$alt,"$e60_coddot -  $dl_estrutural",0,1,"L",0);
   $pdf->setfont('arial','b',8);
   $pdf->cell(30,$alt,$RLe45_tipo." : ",0,0,"R",0);
   $pdf->setfont('arial','',8);
   $pdf->cell(60,$alt,$e45_tipo,0,0,"L",0);
   $pdf->setfont('arial','b',8);
   $pdf->cell(30,$alt,$RLe44_descr." : ",0,0,"R",0);
   $pdf->setfont('arial','',8);
   $pdf->cell(60,$alt,$e44_descr,0,1,"L",0);
   $pdf->setfont('arial','b',8);
   $pdf->cell(30,$alt,substr($RLe45_acerta,0,25)." : ",0,0,"R",0);
   $pdf->setfont('arial','',8);
   $pdf->cell(60,$alt,db_formatar($e45_acerta,'d'),0,0,"L",0);
   $pdf->setfont('arial','b',8);
   $pdf->cell(30,$alt,$RLe45_conferido." : ",0,0,"R",0);
   $pdf->setfont('arial','',8);
   $pdf->cell(60,$alt,db_formatar($e45_conferido,'d'),0,1,"L",0);
   $pdf->setfont('arial','b',8);
   $pdf->cell(30,$alt,'Conta : ',0,0,"R",0);
   $pdf->setfont('arial','',8);
   $pdf->cell(60,$alt,$k13_conta,0,1,"L",0);
   $pdf->setfont('arial','b',8);
   $pdf->cell(30,$alt,'Ordem de Pagamento : ',0,0,"R",0);
   $pdf->setfont('arial','',8);
   $pdf->cell(60,$alt,$e82_codord,0,1,"L",0);
   $pdf->setfont('arial','b',8);
   $pdf->cell(30,$alt,$RLe45_obs." : ",0,0,"R",0);
   $pdf->setfont('arial','',8);
   $pdf->multicell(180,$alt,$e45_obs,0,"L",0);
   $pdf->ln();

   $sqlItens = $clempprestaitem->sql_query(null,'*',null,"e45_codmov=$e45_codmov");
   $result_itens = db_query($sqlItens );

   $troca = 1;
   for($y = 0; $y < pg_num_rows($result_itens);$y++){
     db_fieldsmemory($result_itens,$y);
     if ($pdf->gety() > $pdf->h - 30 || $troca!=0) {
        if ($troca==0) {
          $pdf->addpage();
	}
  //$pdf->ln(2);
	$pdf->setfont('arial','b',8);
	$pdf->cell(15,$alt,$RLe46_codigo,1,0,"C",1);
	$pdf->cell(20,$alt,$RLe46_nota,1,0,"C",1);
	$pdf->cell(50,$alt,$RLe46_descr,1,0,"C",1);
	$pdf->cell(25,$alt,"$RLe46_cpf/$RLe46_cnpj",1,0,"C",1);
	$pdf->cell(50,$alt,$RLe46_nome,1,0,"C",1);
	$pdf->cell(30,$alt,$RLe46_valor,1,1,"C",1);
	$troca = 0;
     }

	$troca = 0;
     $pdf->setfont('arial','',7);
     $pdf->cell(15,$alt,$e46_codigo,0,0,"C",0);
     $pdf->cell(20,$alt,$e46_nota,0,0,"C",0);
     $pdf->cell(50,$alt,substr($e46_descr,0,27),0,0,"L",0);
     if ($e46_cnpj != ""){
       $pdf->cell(25,$alt,$e46_cnpj,0,0,"R",0);
     }else{
       $pdf->cell(25,$alt,$e46_cpf,0,0,"R",0);
     }
     $pdf->cell(50,$alt,$e46_nome,0,0,"L",0);
     $pdf->cell(30,$alt,db_formatar($e46_valor,'f'),0,1,"R",0);
     $valortotal += $e46_valor;
   }


   $pdf->cell(190,$alt, " " , "B",1,"R",0);
   //$pdf->ln(2);
   
  }

$pdf->setfont('arial','b',8);
$pdf->cell(190,$alt,'TOTAL :'.db_formatar($valortotal,'f'),"TB",1,"R",1);
$pdf->cell(172,$alt,'VALOR DO EMPENHO: ',0,0,"R",0);
$pdf->cell(30,$alt,db_formatar($e60_vlremp,'f'),0,1,"L",0);
if ($e60_vlranu > 0) {
  $pdf->cell(172,$alt,'VALOR DEVOLVIDO: ',0,0,"R",0);
  $pdf->cell(30,$alt,db_formatar($e60_vlranu,'f'),0,1,"L",0);

}
$valor_diferenca = $e60_vlremp-$e60_vlranu-$valortotal;
if ($valor_diferenca < 0 ){
  $pdf->cell(172,$alt,'DESPESA GLOSADA: ',0,0,"R",0);
  $pdf->cell(30,$alt,db_formatar($valor_diferenca,'f'),0,1,"L",0);
} else {

  if ($e60_vlremp != $valor_diferenca) {

    $pdf->cell(172,$alt,'PRESTAÇÃO PENDENTE: ',0,0,"R",0);
    $pdf->cell(30,$alt,db_formatar($valor_diferenca,'f'),0,1,"L",0);
  }
}

$tes =  "______________________________"."\n"."Tesoureiro";
$sec =  "______________________________"."\n"."Secretaria da Fazenda";
$cont =  "______________________________"."\n"."Contador";
$pref =  "______________________________"."\n"."Prefeito";
$credor =  "$z01_nome";
$ass_pref = $classinatura->assinatura(1100,$pref);
//$ass_pref = $classinatura->assinatura_usuario();
$ass_sec  = $classinatura->assinatura(1002,$sec);
$ass_tes  = $classinatura->assinatura(1004,$tes);
$ass_cont = $classinatura->assinatura(1005,$cont);
$ass_func = $classinatura->assinatura_usuario();

$largura = ( $pdf->w ) / 3;
$pdf->ln(10);
$pos = $pdf->gety();

$pdf->multicell($largura,4,ucwords(strtolower($ass_func)),0,"C",0,0);
$pdf->setxy($largura,$pos);
$pdf->multicell($largura,4,ucwords(strtolower($credor)),0,"C",0,0);
$pdf->setxy( ( $largura * 2 ),$pos );
$pdf->multicell($largura,4,$ass_tes,0,"C",0,0);
$pdf->Output();

?>
