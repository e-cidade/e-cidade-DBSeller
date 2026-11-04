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

/**
*
*/
class cl_fis_processofiscalativo
{
	public $rotulo     = null;
    public $query_sql  = null;
    public $numrows    = 0;
    public $numrows_incluir = 0;
    public $numrows_alterar = 0;
    public $numrows_excluir = 0;
    public $erro_status= null;
    public $erro_sql   = null;
    public $erro_banco = null;
    public $erro_msg   = null;
    public $erro_campo = null;
    public $pagina_retorno = null;

	public $sequencial = 0;
	public $processo_fiscal;
	public $fiscal;
	public $ativo;

	public function __construct() {
		$this->rotulo = new rotulo("fis_processofiscalativo");
        $this->pagina_retorno =  basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
	} # end function construct

	public function erro($mostra, $retorna) {
		if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
            echo "<script>alert(\"".$this->erro_msg."\");</script>";

            if($retorna==true){
                echo "<script>location.href='".$this->pagina_retorno."'</script>";
            }
        }
	} # end function erro

	public function incluir() {
		if ($this->processo_fiscal == null) {
			$this->erro_sql = " Campo processo fiscal não informado.";
			$this->erro_campo = "processo_fiscal";
			$this->erro_banco = "";
			$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
			$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
			$this->erro_status = "0";
			return false;
		}
		if ($this->fiscal == null) {
			$this->erro_sql = " Campo fiscal não informado.";
			$this->erro_campo = "fiscal";
			$this->erro_banco = "";
			$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
			$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
			$this->erro_status = "0";
			return false;
		}
		if ($this->ativo == null) {
			$this->erro_sql = " Campo ativo não informado.";
			$this->erro_campo = "ativo";
			$this->erro_banco = "";
			$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
			$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
			$this->erro_status = "0";
			return false;
		}
		$sql = "INSERT INTO fiscalizacao.fis_processofiscalativo (processo_fiscal, fiscal, ativo) VALUES ($this->processo_fiscal, $this->fiscal, '$this->ativo')";
        $result = db_query($sql);

        if ($result == false) {
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
                $this->erro_sql   = "Ativo no processo fiscal ($this->sequencial) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Posicao da tarifa  já Cadastrada";
                $this->erro_msg  .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }else{
                $this->erro_sql   = "Ativo no processo fiscal ($this->sequencial) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg  .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_inclui = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->sequencial;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        return true;
	} # end function incluir

	public function alterar() {
		$sql = "UPDATE fiscalizacao.fis_processofiscalativo SET ";
        $virgula = "";
        if(trim($this->processo_fiscal) != "" || isset($GLOBALS["HTTP_POST_VARS"]["processo_fiscal"])){
            $sql  .= $virgula." processo_fiscal = $this->processo_fiscal ";
            $virgula = ",";
            if(trim($this->processo_fiscal) == null ){
                $this->erro_sql = " Campo processo fiscal não informado.";
                $this->erro_campo = "processo_fiscal";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->fiscal) != "" || isset($GLOBALS["HTTP_POST_VARS"]["fiscal"])){
            $sql  .= $virgula." fiscal = $this->fiscal ";
            $virgula = ",";
            if(trim($this->fiscal) == null ){
                $this->erro_sql = " Campo fiscal não informado.";
                $this->erro_campo = "fiscal";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->ativo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ativo"])){
            $sql  .= $virgula." ativo = '$this->ativo' ";
            if(trim($this->ativo) == null ){
                $this->erro_sql = " Campo ativo não informado.";
                $this->erro_campo = "ativo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " WHERE ";
	if($this->sequencial != null){
	     $sql.= "sequencial = $this->sequencial";
        }else if($this->processo_fiscal != null and $this->fiscal != null){
            $sql .= " processo_fiscal = $this->processo_fiscal and fiscal = $this->fiscal ";
        }
        $result = db_query($sql);
        if($result==false) {
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            $this->erro_sql   = "Fiscal ativo no processo não alterado. Alteracão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if(pg_affected_rows($result) == 0){
                $this->erro_banco = "";
                $this->erro_sql   = "Fiscal ativo no processo não alterado. Alteracão Executada.\\n";
                $this->erro_sql  .= "Valores : ".$this->sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg  .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
	} # end function alterar

	public function excluir($sequencial = null, $sWhere = "") {
		$sql = "DELETE FROM fiscalizacao.fis_processofiscalativo ";
        $sql2 = "";
        if($sWhere == null || $sWhere == ""){
            if($sequencial != ""){
                if($sql2 != ""){
                    $sql2 .= " AND ";
                }
                $sql2 .= " sequencial = $sequencial ";
            }
        } else {
            $sql2 = $sWhere;
        }
        $result = db_query($sql.$sql2);
        if($result==false){
           $this->erro_banco = str_replace("\n","",@pg_last_error());
           $this->erro_sql   = "Fiscal ativo no processo não excluído. Exclusão abortada.\\n";
           $this->erro_sql .= "Valores : ".$sequencial;
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           $this->numrows_excluir = 0;
           return false;
        } else {
            if(pg_affected_rows($result)==0){
                $this->erro_banco = "";
                $this->erro_sql = "Fiscal ativo no processo não encontrado. Exclusão não efetuada.\\n";
                $this->erro_sql .= "Valores : ".$sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            }else{
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = pg_affected_rows($result);
                return true;
            }
        }
	} # end function excluir

	public function sql_record($sql) {
        $result = db_query($sql);
        if($result==false){
            $this->numrows    = 0;
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            $this->erro_sql   = "Erro ao selecionar os registros.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .= str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->numrows = pg_numrows($result);
        return $result;
    } # end function sql_record

    /**
     * Funcao do sql
     */
    public function sql_query ($sequencial = null, $campos="*", $ordem=null, $dbWhere=""){
        $sql = "SELECT ";
        if($campos != "*" ) {
            $campos_sql = explode("#",$campos);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            } # endfor
        }else{
            $sql .= $campos;
        }
        $sql .= " FROM fiscalizacao.fis_processofiscalativo AS pfa ";
        $sql2 = "";
        if($dbWhere == ""){
            if($sequencial != null ){
                $sql2 .= " WHERE pfa.sequencial = $sequencial ";
            }
        } else if ($dbWhere != ""){
            $sql2 = " WHERE $dbWhere";
        }
        $sql .= $sql2;
        if($ordem != null ){
            $sql .= " ORDER BY ";
            $campos_sql = explode("#",$ordem);
            $virgula = "";
            for($i=0; $i < sizeof($campos_sql); $i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            } # end for
        }
        return $sql;
    } # end function sql_query
} # end class
?>
