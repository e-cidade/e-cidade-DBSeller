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

require_once(modification('db_fis_procfiscal_classe.php'));

/**
 * Módulo: fiscal
 * Classe que estende a cl_procfiscal
 * @version 1.0.0
 */
class cl_fis_procfiscal_estendida extends cl_fis_procfiscal
{
	/**
	 * Sobreescreve o método incluir da classe mãe.
	 *
	 * @param integer $y100_sequencial
	 * @return bool
	 */
	public function incluir($y100_sequencial) {
		$this->atualizacampos();

		if($this->y100_coddepto == null ){
			$this->erro_sql = "Campo Departamento não informado.";
			$this->erro_campo  = "y100_coddepto";
			$this->erro_banco  = "";
			$this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
			$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
			$this->erro_status = "0";
			return false;
     	}

     	if($this->y100_instit == null ){
			$this->erro_sql = "Campo Instituição não Informado.";
			$this->erro_campo = "y100_instit";
			$this->erro_banco = "";
			$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
			$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
			$this->erro_status = "0";
			return false;
     	}

     	if($this->y100_procfiscalcadtipo == null ){
			$this->erro_sql = " Campo tipo não Informado.";
			$this->erro_campo = "y100_procfiscalcadtipo";
			$this->erro_banco = "";
			$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
			$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
			$this->erro_status = "0";
			return false;
     	}

     	if($this->y100_dtinicial == null ){
			$this->y100_dtinicial = "null";
     	}

		if($this->y100_dtfinal == null ){
			$this->y100_dtfinal = "null";
		}

		if($y100_sequencial == "" || $y100_sequencial == null ){
			$result = db_query("select nextval('fis_fis_procfiscal_y100_sequencial_seq')");
			if($result==false){
				$this->erro_banco = str_replace("\n","",@pg_last_error());
				$this->erro_sql   = "Verifique o cadastro da sequência: fis_fis_procfiscal_y100_sequencial_seq do campo: y100_sequencial";
				$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
				$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
				$this->erro_status = "0";
				return false;
			}
			$this->y100_sequencial = pg_result($result,0,0);
		}else{
			$result = db_query("select last_value from fis_fis_procfiscal_y100_sequencial_seq");
			if(($result != false) && (pg_result($result,0,0) < $y100_sequencial)){
				$this->erro_sql = " Campo y100_sequencial maior que o último número da sequência.";
				$this->erro_banco = "Sequencia menor que este número.";
				$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
				$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
				$this->erro_status = "0";
				return false;
			}else{
				$this->y100_sequencial = $y100_sequencial;
			}
		}

		if(($this->y100_sequencial == null) || ($this->y100_sequencial == "") ){
			$this->erro_sql = " Campo y100_sequencial não declarado.";
			$this->erro_banco = "Chave primária zerada.";
			$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
			$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
			$this->erro_status = "0";
			return false;
		}
		$sql = "INSERT INTO fiscalizacao.fis_procfiscal(y100_sequencial,y100_coddepto,y100_instit,y100_procfiscalcadtipo,y100_dtinicial,y100_dtfinal,y100_obs)
                VALUES($this->y100_sequencial,$this->y100_coddepto,$this->y100_instit,$this->y100_procfiscalcadtipo
                ,".($this->y100_dtinicial == "null" || $this->y100_dtinicial == ""?"null":"'".$this->y100_dtinicial."'")."
                ,".($this->y100_dtfinal == "null" || $this->y100_dtfinal == ""?"null":"'".$this->y100_dtfinal."'")."
                ,'$this->y100_obs')";

     	$result = db_query($sql);

		if($result==false){
			$this->erro_banco = str_replace("\n","",@pg_last_error());
			if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
				$this->erro_sql   = "procfiscal ($this->y100_sequencial) nao Incluído. Inclusao Abortada.";
				$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
				$this->erro_banco = "procfiscal já Cadastrado";
				$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
			}else{
				$this->erro_sql   = "procfiscal ($this->y100_sequencial) nao Incluído. Inclusao Abortada.";
				$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
				$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
			}
			$this->erro_status = "0";
			$this->numrows_incluir= 0;
			return false;
		}

		$this->erro_banco = "";
		$this->erro_sql = "Inclusão efetuada com sucesso\\n";
		$this->erro_sql .= "Valores : ".$this->y100_sequencial;
		$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
		$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
		$this->erro_status = "1";
		$this->numrows_incluir= pg_affected_rows($result);

     	return true;

	} // end function incluir

	/**
	 * Sobreescreve o método alterar da classe mãe.
	 *
	 * @param integer|null $y100_sequencial
	 * @return bool
	 */
	public function alterar($y100_sequencial=null) {
		$this->atualizacampos();
		$sql = " update fiscalizacao.fis_procfiscal set ";
		$virgula = "";

		if(trim($this->y100_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y100_sequencial"])){
			$sql  .= $virgula." y100_sequencial = $this->y100_sequencial ";
			$virgula = ",";
			if(trim($this->y100_sequencial) == null ){
				$this->erro_sql = " Campo processo fiscal não informado.";
				$this->erro_campo = "y100_sequencial";
				$this->erro_banco = "";
				$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
				$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
				$this->erro_status = "0";
				return false;
			}
		}

		if(trim($this->y100_coddepto)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y100_coddepto"])){
			$sql  .= $virgula." y100_coddepto = $this->y100_coddepto ";
			$virgula = ",";
			if(trim($this->y100_coddepto) == null ){
				$this->erro_sql = " Campo Depart. nao Informado.";
				$this->erro_campo = "y100_coddepto";
				$this->erro_banco = "";
				$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
				$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
				$this->erro_status = "0";
				return false;
			}
		}

		if(trim($this->y100_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y100_instit"])){
			$sql  .= $virgula." y100_instit = $this->y100_instit ";
			$virgula = ",";
			if(trim($this->y100_instit) == null ){
				$this->erro_sql = " Campo instituição não informado.";
				$this->erro_campo = "y100_instit";
				$this->erro_banco = "";
				$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
				$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
				$this->erro_status = "0";
				return false;
			}
		}

		if(trim($this->y100_procfiscalcadtipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y100_procfiscalcadtipo"])){
			$sql  .= $virgula." y100_procfiscalcadtipo = $this->y100_procfiscalcadtipo ";
			$virgula = ",";
			if(trim($this->y100_procfiscalcadtipo) == null ){
				$this->erro_sql = " Campo tipo não informado.";
				$this->erro_campo = "y100_procfiscalcadtipo";
				$this->erro_banco = "";
				$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
				$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
				$this->erro_status = "0";
				return false;
			}
		}

		if(trim($this->y100_dtinicial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y100_dtinicial_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y100_dtinicial_dia"] !="") ){
			$sql  .= $virgula." y100_dtinicial = '$this->y100_dtinicial' ";
			$virgula = ",";
		} else {
			if(isset($GLOBALS["HTTP_POST_VARS"]["y100_dtinicial_dia"])){
				$sql  .= $virgula." y100_dtinicial = null ";
				$virgula = ",";
			}
		}

		if(trim($this->y100_dtfinal)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y100_dtfinal_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y100_dtfinal_dia"] !="") ){
			$sql  .= $virgula." y100_dtfinal = '$this->y100_dtfinal' ";
			$virgula = ",";
		} else {
			if(isset($GLOBALS["HTTP_POST_VARS"]["y100_dtfinal_dia"])){
				$sql  .= $virgula." y100_dtfinal = null ";
				$virgula = ",";
			}
		}

		if(trim($this->y100_obs)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y100_obs"])){
			$sql  .= $virgula." y100_obs = '$this->y100_obs' ";
			$virgula = ",";
		}

		$sql .= " where ";
		if($y100_sequencial!=null){
			$sql .= " y100_sequencial = $this->y100_sequencial";
		}

		$result = db_query($sql);

		if($result==false){
			$this->erro_banco = str_replace("\n","",@pg_last_error());
			$this->erro_sql   = "procfiscal nao alterado. Alteração Abortada.\\n";
			$this->erro_sql .= "Valores : ".$this->y100_sequencial;
			$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
			$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
			$this->erro_status = "0";
			$this->numrows_alterar = 0;
			return false;
		}else{
			if(pg_affected_rows($result)==0){
				$this->erro_banco = "";
				$this->erro_sql = "procfiscal nao alterado. Alteração executada.\\n";
				$this->erro_sql .= "Valores : ".$this->y100_sequencial;
				$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
				$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
				$this->erro_status = "1";
				$this->numrows_alterar = 0;
				return true;
			}else{
				$this->erro_banco = "";
				$this->erro_sql = "Alteração efetuada com sucesso\\n";
				$this->erro_sql .= "Valores : ".$this->y100_sequencial;
				$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
				$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
				$this->erro_status = "1";
				$this->numrows_alterar = pg_affected_rows($result);
				return true;
			}
		}
	} // end function alterar

	public function sql_query($campos = "",$ordem = "",$where = ""){

		if($campos == ""){
			$campos = "y100_sequencial,
				       y100_dtinicial,
				       ( select case when data_prorrogacao is not null then data_prorrogacao else data_fim end as data
				from fiscalizacao.fis_processoprorrogacaofinalizacao
				where processo_fiscal = y100_sequencial order by sequencial desc limit 1
				)  as dl_Data_Fim,
				       y101_numcgm,
				       z01_nome,
				       y103_inscr,
				       y102_matric,
				       y104_codsani,
				       depart_protocolo AS db_depart_protocolo,
				       descr_depart AS db_descr_depart,
				       y100_coddepto AS db_depart_atual,
				       (select case when situacao = 1 then 'Prorrogado' when situacao = 2 then 'Finalizado' else null end as data
				from fiscalizacao.fis_processoprorrogacaofinalizacao
				where processo_fiscal = y100_sequencial order by sequencial desc limit 1) AS dl_situacao,

				  (SELECT p58_numero || '/' || p58_ano AS p58_numero
				   FROM fiscalizacao.fis_procfiscalprot
				   INNER JOIN protprocesso ON y105_protprocesso = p58_codproc
				   WHERE y105_procfiscal = y100_sequencial) AS p58_numero,

				  (SELECT p58_codproc
				   FROM fiscalizacao.fis_procfiscalprot
				   INNER JOIN protprocesso ON y105_protprocesso = p58_codproc
				    WHERE y105_procfiscal = y100_sequencial) AS DB_p58_codproc";
		}

		$sql = " SELECT
		$campos
				FROM
				  (SELECT DISTINCT y100_sequencial,
				                   y100_dtinicial,
				                   y101_numcgm,
				                   z01_nome,
				                   y103_inscr,
				                   y102_matric,
				                   y104_codsani,
				                   y100_coddepto,

				     (SELECT p61_coddepto
				      FROM protprocesso
				      INNER JOIN procandam ON p58_codandam = p61_codandam
				      INNER JOIN fiscalizacao.fis_procfiscalprot ON y105_protprocesso = procandam.p61_codproc
				      WHERE fis_procfiscalprot.y105_procfiscal = fis_procfiscal.y100_sequencial) AS depart_protocolo,

				     (SELECT descrdepto
				      FROM protprocesso
				      INNER JOIN procandam ON p58_codandam = p61_codandam
				      INNER JOIN fiscalizacao.fis_procfiscalprot ON y105_protprocesso = procandam.p61_codproc
				      INNER JOIN db_depart ON coddepto = p61_coddepto
				      WHERE fis_procfiscalprot.y105_procfiscal = fis_procfiscal.y100_sequencial) AS descr_depart,

				     (SELECT count(*)
				      FROM fiscalizacao.fis_procfiscalfases AS qtd
				      WHERE qtd.y108_procfiscal = fis_procfiscalfases.y108_procfiscal
				      GROUP BY y108_procfiscal
				      HAVING count(*) > 1) AS aberto
				   FROM fiscalizacao.fis_procfiscal
				   LEFT JOIN fiscalizacao.fis_procfiscalfases ON y108_procfiscal = y100_sequencial
				   INNER JOIN fiscalizacao.fis_procfiscalcgm ON y101_procfiscal = y100_sequencial
				   INNER JOIN cgm ON y101_numcgm = z01_numcgm
				   LEFT JOIN fiscalizacao.fis_procfiscalfiscais ON y106_procfiscal = y100_sequencial
				   LEFT JOIN fiscalizacao.fis_cadfiscais ON fis_procfiscalfiscais.y106_cadfiscais = id_usuario
				   LEFT JOIN fiscalizacao.fis_processofiscalativo ON fis_processofiscalativo.processo_fiscal = y100_sequencial
				   AND fis_processofiscalativo.fiscal = fis_procfiscalfiscais.y106_cadfiscais
				   LEFT JOIN fiscalizacao.fis_datalimitefiscal ON fis_datalimitefiscal.fiscal = fis_procfiscalfiscais.y106_cadfiscais
				   LEFT JOIN fiscalizacao.fis_procfiscalmatric ON y102_procfiscal = y100_sequencial
				   LEFT JOIN fiscalizacao.fis_procfiscalinscr ON y103_procfiscal = y100_sequencial
				   LEFT JOIN fiscalizacao.fis_procfiscalsani ON y104_procfiscal = y100_sequencial";
   			$sql .= " where 1=1 and $where ";
   			if($ordem != ""){
	   			$sql .= " order by $ordem";
   			}

   			$sql .= ") AS x";
   			return $sql;
	}

} // end class

?>
