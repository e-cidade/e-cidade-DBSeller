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

require_once(modification('db_fis_levanta_classe.php'));

/**
 * Módulo: fiscal
 * Classe que estende a cl_levanta
 * @version 1.0.0
 */
class cl_fis_levanta_estendida extends cl_fis_levanta
{
	/**
     	* Método sql para trazer os levantamentos
     	*
     	* @param string $columns
     	* @param string $joins
	* @param string $where
     	* @param string $orderBy
     	* @param string $groupBy
     	*
     	* @return string $sql
     	*/
	public function getLevantamentos($columns = '*', $joins = '', $where = '', $orderBy = '', $groupBy = '') {
		$sql = " SELECT {$columns} FROM fiscalizacao.fis_levanta ";

		if ($joins != '') {
			$sql .= " {$joins} ";
		}

        	if ($where != '') {
            		$sql .= " WHERE {$where}";
        	}

        	if ($orderBy != '') {
            		$sql .= " ORDER BY {$orderBy}";
        	}

        	if ($groupBy != '') {
         	   	$sql .= " GROUP BY {$groupBy}";
        	}

        	return $sql;
	}

	public function sql_query_pesquisa ( $y60_codlev=null,$campos="*",$ordem=null,$dbwhere="", $dbWhere2=""){
     		$sql = "select ";
     		if($campos != "*" ){
       			$campos_sql = explode("#",$campos);
       			$virgula = "";
       			for($i=0;$i<sizeof($campos_sql);$i++){
         			$sql .= $virgula.$campos_sql[$i];
         			$virgula = ",";
       			}
     		}else{
         		$campos='';
         		$campos.=" distinct(y60_codlev),";
         		$campos.= " case when y62_inscr is null then 'CGM' else 'INSCRIÇÃO' end as DBtxttipo_origem,
             			    case when y62_inscr is null then y93_numcgm else y62_inscr end as DBtxtcod_origem,
				    case when y62_inscr is null then nome_cgm else nome_empresa end as DBtxtnome_origem,";
         		$campos.= " y60_data,y60_importado, dl_Andamento, dl_Processo_Fiscal,dl_Auto";
     		}
         	$sql.=" $campos
                  from (
                    select
       			fis_levanta.*,y62_inscr, y93_numcgm,empresa.z01_nome as nome_empresa,cgm.z01_nome as nome_cgm, y41_descr as dl_Andamento, y100_sequencial as dl_Processo_Fiscal,y117_auto as dl_Auto
                       	from
                           fiscalizacao.fis_levanta
                             left join fiscalizacao.fis_levinscr on y62_codlev = y60_codlev
                             left join fiscalizacao.fis_levcgm   on y93_codlev = y60_codlev
                             left join issbase  on y62_inscr  = q02_inscr
                             left join cgm empresa on q02_numcgm = empresa.z01_numcgm
                             left join cgm on y93_numcgm = cgm.z01_numcgm
			     left join fiscalizacao.fis_procfiscallevanta on y112_levanta = y60_codlev
			     left join fiscalizacao.fis_procfiscal on y112_procfiscal = y100_sequencial
			     left join fiscalizacao.fis_procfiscalfiscais  on fis_procfiscalfiscais.y106_procfiscal = y100_sequencial
			     left join fiscalizacao.fis_cadfiscais on fis_procfiscalfiscais.y106_cadfiscais = fis_cadfiscais.id_usuario
			     left join db_usuarios on db_usuarios.id_usuario = fis_cadfiscais.id_usuario
			     left join fiscalizacao.fis_processofiscalativo on fis_processofiscalativo.processo_fiscal = y100_sequencial
			     left join fiscalizacao.fis_datalimitefiscal on fis_datalimitefiscal.fiscal = fis_cadfiscais.id_usuario
			     left join fiscalizacao.fis_levandam on y67_codlev = y60_codlev
		             left join fiscalizacao.fis_levusu  on y61_codlev = y60_codlev
			     left join fiscalizacao.fis_fandam on y67_codandam = y39_codandam
			     LEFT JOIN fiscalizacao.fis_autolevanta ON y117_levanta = y60_codlev
			     left join fiscalizacao.fis_tipoandam on y41_codtipo = y39_codtipo
			     left join fiscalizacao.fis_grupotipoandamento_tipoandam on y41_codtipo = fi30_tipoandam
			     left join fiscalizacao.fis_grupotipoandamento on fi30_grupo = fis_grupotipoandamento.sequencial
				$dbWhere2
                       ) as x ";
     		$sql2 = "";
     		if($dbwhere==""){
       			if($y60_codlev!=null ){
         			$sql2 .= " where fis_levanta.y60_codlev = $y60_codlev ";
       			}
     		}else if($dbwhere != ""){
       			$sql2 = " where $dbwhere";
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
		}

     		return $sql;
  	}
}

?>
