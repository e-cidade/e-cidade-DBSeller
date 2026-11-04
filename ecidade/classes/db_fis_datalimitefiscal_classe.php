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
class cl_fis_datalimitefiscal
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
	public $data;
    public $fiscal;

	public function __construct() {
		$this->rotulo = new rotulo("fis_datalimitefiscal");
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

    public function atualizaCampos(){
        if(isset($GLOBALS["HTTP_POST_VARS"]["data_dia"])){
                $this->data_dia = ($this->data_dia == "" ? @$GLOBALS["HTTP_POST_VARS"]["data_dia"] : $this->data_dia);
                $this->data_mes = ($this->data_mes == "" ? @$GLOBALS["HTTP_POST_VARS"]["data_mes"] : $this->data_mes);
                $this->data_ano = ($this->data_ano == "" ? @$GLOBALS["HTTP_POST_VARS"]["data_ano"] : $this->data_ano);
                if($this->data_dia != ""){
                    $this->data = "'".$this->data_ano."-".$this->data_mes."-".$this->data_dia."'";
                }else{
		$this->data = "(null)";
		}
            }
    }

	public function incluir() {
        $this->atualizaCampos();
		if ($this->fiscal == null) {
			$this->erro_sql = " Campo fiscal não informado.";
			$this->erro_campo = "fiscal";
			$this->erro_banco = "";
			$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
			$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
			$this->erro_status = "0";
			return false;
		}
		$sql = "INSERT INTO fiscalizacao.fis_datalimitefiscal (data, fiscal) VALUES ($this->data, $this->fiscal)";
        $result = db_query($sql);

        if ($result == false) {
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
                $this->erro_sql   = "Data limite ($this->sequencial) não incluída. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Data limite  já cadastrada";
                $this->erro_msg  .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }else{
                $this->erro_sql   = "Data limite ($this->sequencial) não incluída. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg  .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_inclui = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->sequencial;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        return true;
	} # end function incluir

	public function alterar() {
        $this->atualizaCampos();
		$sql = "UPDATE fiscalizacao.fis_datalimitefiscal SET ";
        $virgula = "";
        if(isset($GLOBALS["HTTP_POST_VARS"]["data"])){
            $sql  .= $virgula." data = $this->data ";
            $virgula = ",";
        }
        if(trim($this->fiscal) != "" || isset($GLOBALS["HTTP_POST_VARS"]["fiscal"])){
            $sql  .= $virgula." fiscal = $this->fiscal ";
            $virgula = ",";
        }
        $sql .= " WHERE ";
        if($this->fiscal != null ){
            $sql .= " fiscal = $this->fiscal ";
        }
        $result = db_query($sql);
        if($result==false) {
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            $this->erro_sql   = "Data limite não alterada. Alteracão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if(pg_affected_rows($result) == 0){
                $this->erro_banco = "";
                $this->erro_sql   = "Data limite não alterada. Alteracão Executada.\\n";
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

	public function excluir($sequencial = null, $dbWhere = null) {
		$sql = "DELETE FROM fiscalizacao.fis_datalimitefiscal ";
        $sql2 = "";
        if($dbwhere == null || $dbwhere == ""){
            if($sequencial != ""){
                if($sql2 != ""){
                    $sql2 .= " AND ";
                }
                $sql2 .= " sequencial = $sequencial ";
            }
        } else {
            $sql2 = $dbWhere;
        }
        $result = db_query($sql.$sql2);
        if($result==false){
           $this->erro_banco = str_replace("\n","",@pg_last_error());
           $this->erro_sql   = "Data limite não excluída. Exclusão abortada.\\n";
           $this->erro_sql .= "Valores : ".$sequencial;
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           $this->numrows_excluir = 0;
           return false;
        } else {
            if(pg_affected_rows($result)==0){
                $this->erro_banco = "";
                $this->erro_sql = "Data limite não encontrada. Exclusão não efetuada.\\n";
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
        $sql .= " FROM fiscalizacao.fis_datalimitefiscal AS d ";
        $sql2 = "";
        if($dbWhere == ""){
            if($sequencial != null ){
                $sql2 .= " WHERE d.sequencial = $sequencial ";
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
