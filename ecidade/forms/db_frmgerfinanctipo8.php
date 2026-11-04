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


// Débito tipo 11 - AUTO

$iInstituicao = db_getsession('DB_instit');

$sql =  "select
		y50_codauto,
		a.k00_numpre,
		a.k00_numpar,
		a.k00_numtot,
		case when k163_data is not null then to_char(\"k163_data\", 'DD/MM/YYYY')
				else  to_char(\"k00_dtoper\", 'DD/MM/YYYY')
		end as k00_dtoper,
		to_char(\"k163_data\", 'DD/MM/YYYY') as k163_data,
		to_char(\"k00_dtvenc\", 'DD/MM/YYYY') as k00_dtvenc,
		k00_receit,
		k02_drecei,
		k00_valor,
		k00_hist,
		k01_descr,
		y17_codauto,
		(select
				string_agg(y29_codtipo || '-' || y29_descr ,',')
			from
				autotipo
			inner join fiscalproc on
				fiscalproc.y29_codtipo = autotipo.y59_codtipo
			where
				y59_codauto = y17_codauto) as dl_procedência,
		(SELECT coddepto||' - '||descrdepto  FROM configuracoes.db_depart WHERE coddepto = y50_setor) as dl_departamento,
    case when ar51_numpre = " . $numpre . " then (select distinct
      ar51_dtproc
    from
      controleparc_registrosorig
    where
      ar51_numpre = " . $numpre . ")
    end as dl_Data_de_processamento,
    case when ar51_numpre = " . $numpre . " then 
      (select distinct
        ar49_horario
      from
        controleparc_agendamento
      inner join controleparc_registrosorig on
        ar51_id_agendamento = ar49_id
      where ar51_numpre = " . $numpre . ")
    end as dl_Horario_processamento,
		case when ar51_numpre = " . $numpre . " then ar51_dtvenc end as dl_Data_vencimento_original,
		case when ar51_numpre = " . $numpre . " then ar51_novadtvenc end as dl_Nova_data_vencimento,
		case when ar51_numpre = " . $numpre . " then 
      (select ar50_descricao from controleparc_acao where ar50_id = (select distinct
				ar49_acao
			from
				controleparc_agendamento
			inner join controleparc_registrosorig on
				ar51_id_agendamento = ar49_id
			where ar51_numpre = " . $numpre . "))
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
	inner join arreinstit on arreinstit.k00_numpre = a.k00_numpre and arreinstit.k00_instit = " . db_getsession('DB_instit') . "
  left join autonumpre on y17_numpre = a.k00_numpre
  left join auto on y50_codauto = y17_codauto
  left outer join arrematric on arrematric.k00_numpre = a.k00_numpre
  left join controleparc_registrosorig on ar51_numpre = a.k00_numpre and ar51_numpar = a.k00_numpar and ar51_receit = a.k00_receit
  left outer join arreinscr on arreinscr.k00_numpre = a.k00_numpre , tabrec
  inner join tabrecjm on tabrecjm.k02_codjm = tabrec.k02_codjm , histcalc
  left join (
    select divida.v01_coddiv,
      divida.v01_numpre,
      divida.v01_numpar,
      informacaodebito.k163_data
    from divida inner join arreinscr
      on arreinscr.k00_numpre = divida.v01_numpre
    inner join divold  on k10_coddiv = divida.v01_coddiv
    inner join informacaodebito on k163_numpre = divold.k10_numpre and k163_numpar = divold.k10_numpar
    where divida.v01_instit = fc_getsession('DB_instit')::int and divida.v01_numpre = $numpre";
if (!empty($numpar)) {
  $sql .= "  and divida.v01_numpar = $numpar";
}

$sql .= " ) as ver_issvar on ver_issvar.v01_numpre = $numpre";

if (!empty($numpar)) {
  $sql .= "  and ver_issvar.v01_numpar = $numpar";
}

/* FIM ISSVAR */

$sql .= "
  where
    a.k00_numpre = " . $numpre . "
    and k02_codigo = k00_receit
    and k01_codigo = k00_hist";

if ($numpar != 0) {
  $sql .= " and k00_numpar = $numpar";
}
$sql .= "			order by a.k00_numpar ASC";

$dados = db_query($sql);
if (pg_num_rows($dados) == 0) {
  //echo "Código de Arrecadação não cadastrado no diversos.";
  exit;
} else {
?>
  <fieldset>
    <legend>Auto de Infração</legend>
    <?php

    db_fieldsmemory($dados, $x, 0);
    ?>
    <table style="margin: 10;">
      <tr>
        <td width="200"><b>Auto de Infração:</b></td>
        <td width="90%" bgcolor="white">&nbsp;<?= $y50_codauto ?></td>
        <td> </td>
      </tr>
      <tr>
        <td width="200"><b>Numpre:</b></td>
        <td width="90%" bgcolor="white">&nbsp;<?= $k00_numpre ?></td>
        <td> </td>
      </tr>
      <tr>
        <td><b>Dt. Lanc.:</b></td>
        <td bgcolor="white">&nbsp;<?= $k00_dtoper ?></td>
      </tr>
      <tr>
        <td><b>Dt. Venc.:</b></td>
        <td bgcolor="white">&nbsp;<?= $k00_dtvenc ?> </td>
      </tr>
      <tr>
        <td><b>Procedencia:</b></td>
        <td bgcolor="white" title="<?= $dl_procedência ?>">&nbsp;<?= $dl_procedência ?></td>
      </tr>
      <tr>
        <td><b>Receita:</b></td>
        <td bgcolor="white">&nbsp;<?= $k00_receit ?> </td>
      </tr>
      <tr>
        <td><b>Descri&ccedil;&atilde;o:</b></td>
        <td bgcolor="white">&nbsp;<?= $k02_drecei ?></td>
      </tr>
      <tr>
        <td><b>Valor:</b></td>
        <td bgcolor="white">&nbsp; <?= db_formatar(db_formatar($k00_valor, "v") * 1, "f") ?> </td>
      </tr>
      <tr>
        <td><b>Departamento:</b></td>
        <td bgcolor="white" title="<?= $dl_departamento ?>">&nbsp;<?= $dl_departamento ?></td>
      </tr>
      <tr>
        <td><b>Ação de Process.:</b></td>
        <td bgcolor="white" title="<?= $dl_acao_de_processamento ?>">&nbsp;<?= $dl_acao_de_processamento ?></td>
      </tr>
      <tr>
        <td><b>Instituição:</b></td>
        <td bgcolor="white" title="<?= $instit ?>">&nbsp;<?= $instit ?></td>
      </tr>
      <tr>
        <td><b>Usuario:</b></td>
        <td bgcolor="white" title="<?= $usuario ?>">&nbsp;<?= $usuario ?></td>
      </tr>
    </table>
  <?php
}
  ?>
  </fieldset>