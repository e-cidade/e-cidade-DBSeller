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

require_once(modification('db_fis_fiscal_classe.php'));

/**
*
*/
class cl_fis_fiscal_estendida extends cl_fis_fiscal
{
	public function sql_query_info($y30_codnoti=null,$campos="*",$dbwhere="", $dbwhere2 = ''){
	    $sql = "select $campos from
	                           (select y30_setor,
	                                   nome,
	                                   descrdepto,
	                                   y30_codnoti,
	                                   y30_numbloco,
	                                   y30_data,
	                                   y30_hora,
	                                   y30_instit,
	                                   y30_prazorec,
	                                   y30_nome,
	                                   y30_obs,
	                                   y30_dtvenc,
	                                   y100_sequencial,
                                       y41_descr,
                                       db_usuarios.id_usuario,
	                                   case when y34_inscr  is not null then 'Inscrição'  else
	                                      (case when y35_matric is not null then 'Matrícula' else
	                                         (case when y37_codsani is not null then 'Sanitário '  else
	                                            (case when y36_numcgm is not null then 'Cgm' else
	                                               (case when y21_codvist is not null then 'Vistorias' else 'Nenhum'
	                                                end)
	                                             end)
	                                           end)
	                                         end)
	                                     end as identifica,
	                                     case when y34_inscr  is not null then   y34_inscr else
	                                       (case when y35_matric is not null then  y35_matric else
	                                         (case when y37_codsani is not null then y37_codsani else
	                                           (case when y36_numcgm is not null then  y36_numcgm else
	                                             (case when y21_codvist is not null then y21_codvist
	                                              end)
	                                           end)
	                                         end)
	                                       end )
	                                     end as codigo,
	                                     case when b.q02_numcgm is not null then b.q02_numcgm else
	                                       (case when a.j01_numcgm is not null then a.j01_numcgm else
	                                         (case when c.y80_numcgm is not null then c.y80_numcgm else
	                                           (case when z01_numcgm is not null then z01_numcgm else
	                                             (case when inscr.q02_numcgm is not null then inscr.q02_numcgm else
	                                               (case when matric.j01_numcgm is not null then matric.j01_numcgm else
	                                                 (case when y73_numcgm is not null then y73_numcgm else
	                                                   (case when sani.y80_numcgm is not null then sani.y80_numcgm
	                                                   end)
	                                                 end)
	                                               end)
	                                             end)
	                                           end)
	                                         end)
										end)
	                                end as z01_numcgm,
                                    in01_intimacao
                                from fiscalizacao.fis_fiscal
                                     inner join fiscalizacao.fis_fiscalintimacao on y30_codnoti = in01_codnoti
                                     left join fiscalizacao.fis_procfiscalnotificacao on y30_codnoti = y110_notificacaofiscal
                                     left join fiscalizacao.fis_procfiscal on y110_procfiscal = y100_sequencial
                					 left join fiscalizacao.fis_fiscalcgm on  y36_codnoti= y30_codnoti
                					 left join fiscalizacao.fis_fiscalinscr on y34_codnoti = y30_codnoti
                					 left join fiscalizacao.fis_fiscalmatric on y35_codnoti = y30_codnoti
                					 left join fiscalizacao.fis_fiscalsanitario on y37_codnoti = y30_codnoti
                					 left join fiscalizacao.fis_fiscalvistorias on  y21_codnoti = y30_codnoti
                					 left join iptubase as a on a.j01_matric = y35_matric
                					 left join issbase as b on y34_inscr = b.q02_inscr
                					 left join cgm on z01_numcgm = y36_numcgm
                					 left join fiscalizacao.fis_sanitario as c on c.y80_codsani = y37_codsani
                					 left join fiscalizacao.fis_vistorias on y70_codvist = y21_codvist
                					 left join fiscalizacao.fis_vistinscr on y71_codvist = y70_codvist
                					 left join fiscalizacao.fis_vistmatric on y72_codvist = y70_codvist
                					 left join fiscalizacao.fis_vistcgm on y73_codvist = y70_codvist
                					 left join fiscalizacao.fis_vistsanitario on y74_codvist = y70_codvist
                					 left join issbase as inscr on y71_inscr = inscr.q02_inscr
                					 left join iptubase as matric on matric.j01_matric = y72_matric
                					 left join fiscalizacao.fis_sanitario as sani on sani.y80_codsani = y74_codsani
                					 left join fiscalizacao.fis_fiscalusuario on y38_codnoti = y30_codnoti
                					 left join fiscalizacao.fis_procfiscalfiscais  on fis_procfiscalfiscais.y106_procfiscal = y100_sequencial
                					 left join fiscalizacao.fis_cadfiscais on fis_procfiscalfiscais.y106_cadfiscais = fis_cadfiscais.id_usuario
                					 left join db_usuarios on db_usuarios.id_usuario = y38_id_usuario
                					 left join fiscalizacao.fis_processofiscalativo on fis_processofiscalativo.processo_fiscal = y100_sequencial
                					 left join fiscalizacao.fis_datalimitefiscal on fis_datalimitefiscal.fiscal = fis_cadfiscais.id_usuario
                					 inner join db_depart on coddepto = y30_setor
                					 left join fiscalizacao.fis_fandam on y39_codandam = (select max(y49_codandam) from fiscalizacao.fis_fiscalandam where y49_codnoti = y30_codnoti)
                					 left join fiscalizacao.fis_tipoandam on y41_codtipo = y39_codtipo
                					 left join fiscalizacao.fis_grupotipoandamento_tipoandam on y41_codtipo = fi30_tipoandam
                					 left join fiscalizacao.fis_grupotipoandamento on fi30_grupo = fis_grupotipoandamento.sequencial
                					 $dbwhere2
	                           ) as x

	                            inner join cgm on cgm.z01_numcgm=x.z01_numcgm";
		$sql2 = "";
		if($dbwhere==""){
			if($y30_codnoti!=null ){
			 $sql2 .= " where y30_codnoti = $y30_codnoti ";
			}
		}else if($dbwhere != ""){
			$sql2 = " where $dbwhere";
		}
		$sql .= $sql2;
		// ORDENAR POR CODIGO
		$sql .= "ORDER BY y30_codnoti DESC";
		return $sql;
 	}

	public function getIntimacaoOuNotificacao($y30_codnoti=null,$campos="*",$ordem=null,$dbwhere=""){
     		$sql = "select ";
     		if($campos != "*" ){
       			$campos_sql = explode("#",$campos);
       			$virgula = "";
       			for($i=0;$i<sizeof($campos_sql);$i++){
         			$sql .= $virgula.$campos_sql[$i];
         			$virgula = ",";
       			}
     		}else{
       			$sql .= $campos;
     		}
            $sql .= " from fiscalizacao.fis_fiscal ";
            $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_fiscal.y30_setor";
            $sql .= "      inner join db_config  on  db_config.codigo = db_depart.instit";
            $sql .= "      left join fiscalizacao.fis_procfiscalnotificacao on y110_notificacaofiscal = y30_codnoti";
            $sql .= "      left join fiscalizacao.fis_procfiscal on y110_procfiscal = y100_sequencial";
            $sql .= "      left join fiscalizacao.fis_procfiscalfiscais  on fis_procfiscalfiscais.y106_procfiscal = y100_sequencial";
            $sql .= "      left join fiscalizacao.fis_cadfiscais on fis_procfiscalfiscais.y106_cadfiscais = fis_cadfiscais.id_usuario";
            $sql .= "      left join db_usuarios  on db_usuarios.id_usuario = fis_cadfiscais.id_usuario";
            $sql .= "      left join fiscalizacao.fis_fiscalusuario on y38_codnoti = y30_codnoti";
            $sql .= "      left join fiscalizacao.fis_processofiscalativo on fis_processofiscalativo.processo_fiscal = y100_sequencial ";
            $sql .= "      left join fiscalizacao.fis_processoprorrogacaofinalizacao on fis_processoprorrogacaofinalizacao.processo_fiscal = y100_sequencial ";
            $sql .= "      left join fiscalizacao.fis_datalimitefiscal on fis_datalimitefiscal.fiscal = fis_cadfiscais.id_usuario";
            $sql .= "      left join fiscalizacao.fis_fandam on y39_codandam = (select max(y49_codandam) from fiscalizacao.fis_fiscalandam where y49_codnoti = y30_codnoti) ";
            $sql .= "      left join fiscalizacao.fis_tipoandam on y41_codtipo = y39_codtipo ";
            $sql .= "      left join fiscalizacao.fis_grupotipoandamento_tipoandam on y41_codtipo = fi30_tipoandam ";
            $sql .= "      left join fiscalizacao.fis_grupotipoandamento on fi30_grupo = fis_grupotipoandamento.sequencial ";
     		$sql2 = "";

     		if($dbwhere != ""){
      			if($y30_codnoti!=null ){
         			$sql2 .= " where fis_fiscal.y30_codnoti = $y30_codnoti and $dbwhere";
       			}else{
        			$sql2 .= " where $dbwhere";
       			}
     		}else{
      			if($y30_codnoti!=null ){
         			$sql2 .= " where fis_fiscal.y30_codnoti = $y30_codnoti ";
       			}
     		}

     		$sql .= $sql2;
     		if($ordem != null ){
       			$sql .= " order by ";
       			$campos_sql = explode("#",$ordem);
       			$virgula = "";
       			for($i=0;$i<sizeof($campos_sql);$i++){
         			$sql .= $virgula.$campos_sql[$i];
         			$virgula = ",";
       			}
			$sql .= ' desc ';
     		}
     		return $sql;
  	}


}
?>
