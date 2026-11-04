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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_sql.php"));
parse_str($_SERVER['QUERY_STRING']);

?>
<html>
<head>
<title>Descritivo do Parcelamento</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

    <?php
   if(1==1){

    $sql =	"select
					a.k00_numpre,
					a.k00_numpar,
					a.k00_numtot,
                    case when k163_data is not null then
                              k163_data
                         else k00_dtoper
                    end as k00_dtoper,
                    k163_data,
					k00_dtvenc,
					k00_receit,
					k02_drecei,
					k00_valor,
					k00_hist,
					k01_descr,
					case when ar51_numpre = ".$numpre." then (select distinct
																ar51_dtproc
															from
																controleparc_registrosorig
															where
																ar51_numpre = ".$numpre.")
															end as dl_Data_de_processamento,
					case when ar51_numpre = ".$numpre." then (select distinct
																ar49_horario
															from
																controleparc_agendamento
															inner join controleparc_registrosorig on
																ar51_id_agendamento = ar49_id
															where ar51_numpre = ".$numpre.")
															end as dl_Horario_processamento,
					case when ar51_numpre = ".$numpre." then ar51_dtvenc end as dl_Data_vencimento_original,
					case when ar51_numpre = ".$numpre." then ar51_novadtvenc end as dl_Nova_data_vencimento,
					case when ar51_numpre = ".$numpre." then (select ar50_descricao from controleparc_acao where ar50_id = (select distinct
																															ar49_acao
																														from
																															controleparc_agendamento
																														inner join controleparc_registrosorig on
																															ar51_id_agendamento = ar49_id
																														where ar51_numpre = ".$numpre."))
																														end as dl_Acao_de_processamento,
				(
					select
						nomeinst
					from
						db_config
					where
						codigo = arreinstit.k00_instit
				) as instit,
				(
					select
						db_usuarios.nome
					from
						arrehist
						join db_usuarios on arrehist.k00_id_usuario = db_usuarios.id_usuario
					where
						k00_numpre = a.k00_numpre
				) as usuario
				from
					arrecad a
				inner join arreinstit on
					arreinstit.k00_numpre = a.k00_numpre
					and arreinstit.k00_instit = ".db_getsession('DB_instit')."
				left outer join arrematric on
					arrematric.k00_numpre = a.k00_numpre
				left join controleparc_registrosorig on
					ar51_numpre = a.k00_numpre
					and ar51_numpar = a.k00_numpar
					and ar51_receit = a.k00_receit
				left outer join arreinscr on
					arreinscr.k00_numpre = a.k00_numpre ,
					tabrec
				inner join tabrecjm on
					tabrecjm.k02_codjm = tabrec.k02_codjm ,
					histcalc";

                /* ISSVAR - M21631 - SE FOR ISSVAR USAR A DATA DE LANÇAMENTO EM VEZ DA DATA DE OPERAÇÃO */
                $sql .= "
                left join (
                            select divida.v01_coddiv,
                                   divida.v01_numpre,
                                   divida.v01_numpar,
                                   informacaodebito.k163_data
                              from divida inner join arreinscr
                                on arreinscr.k00_numpre = divida.v01_numpre
                        inner join divold
                                on k10_coddiv = divida.v01_coddiv
                        inner join informacaodebito
                                on k163_numpre = divold.k10_numpre
                               and k163_numpar = divold.k10_numpar
                             where divida.v01_instit = fc_getsession('DB_instit')::int
                               and divida.v01_numpre = $numpre";
                if (!empty($numpar)) {
                    $sql .= "  and divida.v01_numpar = $numpar";
                }

                $sql .= " ) as ver_issvar

                         on ver_issvar.v01_numpre = $numpre";

                if (!empty($numpar)) {
                    $sql .= "  and ver_issvar.v01_numpar = $numpar";
                }

                /* FIM ISSVAR */

                $sql .= "
				where
					a.k00_numpre = ".$numpre."
					and k02_codigo = k00_receit
					and k01_codigo = k00_hist";

    if($numpar != 0){
       $sql .= " and k00_numpar = $numpar";
    }
	$sql .= "			order by a.k00_numpar ASC";

    $js_func = "";
    db_lovrot($sql,5,"()","",$js_func);
 }else{
 ?>



  <table width="100%" border="0" cellspacing="0" cellpadding="3">
   <tr bgcolor="#FFCC66">
      <th width="10%" nowrap>Numpre</th>
      <th width="5%" nowrap>Par</th>
      <th width="5%" nowrap>Tot</th>
      <th width="9%" nowrap>Dt. Lanc.</th>
      <th width="10%" nowrap>Dt. Venc.</th>
      <th width="8%" nowrap>Hist</th>
      <th width="12%" nowrap>Descri&ccedil;&atilde;o</th>
      <th width="9%" nowrap>Receita</th>
      <th width="17%" nowrap>Descri&ccedil;&atilde;o</th>
      <th width="15%" nowrap>Valor</th>
    </tr>
    <?php
    $sql = "select a.k00_numpre,
	                k00_numpar,
					k00_numtot,
					k00_dtoper,
					k00_dtvenc,
					k00_hist,
					k00_receit,
					k02_drecei,
					k01_descr,
					k00_valor
	        from arrecad a
               inner join arreinstit on arreinstit.k00_numpre = a.k00_numpre
							                      and arreinstit.k00_instit = ".db_getsession('DB_instit')."
	             left outer join arrematric on arrematric.k00_numpre = a.k00_numpre
	             left outer join arreinscr on arreinscr.k00_numpre = a.k00_numpre
	             ,tabrec inner join tabrecjm on tabrecjm.k02_codjm = tabrec.k02_codjm
		     ,histcalc
	        where a.k00_numpre = ".$numpre." and
				  k02_codigo   = k00_receit and
				  k01_codigo   = k00_hist
			";
    if($numpar != 0){
       $sql .= " and k00_numpar = $numpar";
    }
	$dados = db_query($sql);
    $ConfCor1 = "#EFE029";
    $ConfCor2 = "#E4F471";
	$numpre_cor = "";
	$numpre_par = "";
	$qcor= $ConfCor1;
    if(pg_numrows($dados)>0){
      for($x=0;$x<pg_numrows($dados);$x++){
	    db_fieldsmemory($dados,$x,"1");
        if($numpre_cor==""){
		   $numpre_cor = $k00_numpre;
		   $numpre_par = $k00_numpar;
	    }
	  if($numpre_cor != $k00_numpre || $numpre_par != $k00_numpar ){
         $numpre_cor = $k00_numpre;
		 $numpre_par = $k00_numpar;
         if($qcor == $ConfCor1)
		    $qcor = $ConfCor2;
		 else $qcor = $ConfCor1;
	  }
	  ?>
	   <tr bgcolor="<?=$qcor?>">
         <td width="10%" nowrap align="right" > <?=$k00_numpre?></td>
         <td width="5%" nowrap align="right" ><?=$k00_numpar?></td>
         <td width="5%" nowrap align="right"><?=$k00_numtot?></td>
         <td width="9%" nowrap><?=$k00_dtoper?></td>
         <td width="10%" nowrap><?=$k00_dtvenc?> </td>
         <td width="8%" nowrap align="right"><?=$k00_hist?></td>
         <td width="12%" nowrap><?=$k01_descr?></td>
         <td width="9%" nowrap align="center"> <?=$k00_receit?> </td>
         <td width="17%" nowrap><?=$k02_drecei?></td>
         <td width="15%" nowrap align="right"> <?=db_formatar(db_formatar($k00_valor,"v")*-1,"f")?> </td>
      </tr>
    <?php
  	  }
    }
    ?>
  </table>
 <?php
 }
 ?>
</body>
</html>
