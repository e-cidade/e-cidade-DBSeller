<?php
// ini_set('display_errors',1);
// ini_set('display_startup_errors',1);
// error_reporting(-1);
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
class cl_fis_processoprorrogacaofinalizacao
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
	public $processo_fiscal = null;
	public $data_abertura_dia = null;
	public $data_abertura_mes = null;
	public $data_abertura_ano = null;
	public $data_abertura;
    public $data_prorrogacao_dia = null;
    public $data_prorrogacao_mes = null;
    public $data_prorrogacao_ano = null;
    public $data_prorrogacao;
	public $data_fim_dia = null;
	public $data_fim_mes = null;
	public $data_fim_ano = null;
	public $data_fim;
	public $id_usuario;
	public $situacao;
	public $observacao;


	public function __construct() {
		$this->rotulo = new rotulo("fis_processoprorrogacaofinalizacao");
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

	public function atualizacampos(){
        $this->data_abertura = explode('/', $this->data_abertura);
        $this->data_prorrogacao = explode('/', $this->data_prorrogacao);
        $this->data_fim = explode('/', $this->data_fim);

        if (checkdate(intval($this->data_abertura[1]), intval($this->data_abertura[0]), intval($this->data_abertura[2]))) {
            $this->data_abertura = '\''.$this->data_abertura[2]."-".$this->data_abertura[1]."-".$this->data_abertura[0].'\'';
        } else {
            $this->data_abertura = 'NULL';
        }

        if (checkdate(intval($this->data_prorrogacao[1]), intval($this->data_prorrogacao[0]), intval($this->data_prorrogacao[2]))) {
            $this->data_prorrogacao = '\''.$this->data_prorrogacao[2]."-".$this->data_prorrogacao[1]."-".$this->data_prorrogacao[0].'\'';
        } else {
            $this->data_prorrogacao = 'NULL';
        }

        if (checkdate(intval($this->data_fim[1]), intval($this->data_fim[0]), intval($this->data_fim[2]))) {
            $this->data_fim = '\''.$this->data_fim[2]."-".$this->data_fim[1]."-".$this->data_fim[0].'\'';
        } else {
            $this->data_fim = 'NULL';
        }
	}

	public function incluir() {
		$this->atualizacampos();
		if ($this->processo_fiscal == null) {
			$this->erro_sql = " Campo processo fiscal não informado.";
			$this->erro_campo = "processo_fiscal";
			$this->erro_banco = "";
			$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
			$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
			$this->erro_status = "0";
			return false;
		}
		if ($this->data_abertura == null) {
			$this->erro_sql = " Campo data_abertura não informado.";
			$this->erro_campo = "data_abertura";
			$this->erro_banco = "";
			$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
			$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
			$this->erro_status = "0";
			return false;
		}
		if ($this->id_usuario == null) {
			$this->erro_sql = " Campo id_usuario não informado.";
			$this->erro_campo = "id_usuario";
			$this->erro_banco = "";
			$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
			$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
			$this->erro_status = "0";
			return false;
		}
		if ($this->situacao == null) {
			$this->erro_sql = " Campo situacao não informado.";
			$this->erro_campo = "situacao";
			$this->erro_banco = "";
			$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
			$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
			$this->erro_status = "0";
			return false;
        }
        // dd($this->data_prorrogacao,$this->data_abertura,$this->situacao);

		if ($this->situacao == 1 && $this->data_prorrogacao < $this->data_abertura) {
			$this->erro_sql = " Data de prorrogação inválida para a prorrogação do processo fis_fiscal.";
			$this->erro_campo = "situacao";
			$this->erro_banco = "";
			$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
			$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
			$this->erro_status = "0";
			return false;
        }

        if ($this->situacao == 2 && ($this->data_prorrogacao != null && $this->data_fim > $this->data_prorrogacao)) {
            $this->erro_sql = " Data de finalização inválida para a finalização do processo fis_fiscal.";
            $this->erro_campo = "situacao";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }

		$sql = "INSERT INTO fiscalizacao.fis_processoprorrogacaofinalizacao (
            processo_fiscal,
            data_abertura,
            data_prorrogacao,
            data_fim,
            id_usuario,
            situacao,
            observacao
            ) VALUES (
            $this->processo_fiscal,
            $this->data_abertura,
            $this->data_prorrogacao,
            $this->data_fim,
            $this->id_usuario,
            $this->situacao,
            '$this->observacao'
        )";

        $result = db_query($sql);
      // die($sql);

        if ($result == false) {
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
                $this->erro_sql   = "Falha ao salvar o processo fiscal ($this->sequencial).";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Processo fiscal já cadastrado com o mesmo sequencial.";
                $this->erro_msg  .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }else{
                $this->erro_sql   = "Falha ao salvar o processo fiscal ($this->sequencial).";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg  .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_inclui = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Processo fiscal salvo com sucesso.\\n";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        return true;
	} # end function incluir

	public function alterar() {
		$this->atualizacampos();
		$sql = "UPDATE fiscalizacao.fis_processoprorrogacaofinalizacao SET ";
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
        if(trim($this->data_abertura) != "" || isset($GLOBALS["HTTP_POST_VARS"]["data_abertura"])){
            $sql  .= $virgula." data_abertura = '$this->data_abertura' ";
            $virgula = ",";
            if(trim($this->data_abertura) == null ){
                $this->erro_sql = " Campo data_abertura não informado.";
                $this->erro_campo = "data_abertura";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->data_prorrogacao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["data_prorrogacao"])){
            $sql  .= $virgula." data_prorrogacao = '$this->data_prorrogacao' ";
            $virgula = ",";
        }
        if(trim($this->data_fim) != "" || isset($GLOBALS["HTTP_POST_VARS"]["data_fim"])){
            $sql  .= $virgula." data_fim = '$this->data_fim' ";
        }
        if(trim($this->id_usuario) != "" || isset($GLOBALS["HTTP_POST_VARS"]["id_usuario"])){
            $sql  .= $virgula." id_usuario = $this->id_usuario ";
            if(trim($this->id_usuario) == null ){
                $this->erro_sql = " Campo id_usuario não informado.";
                $this->erro_campo = "id_usuario";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->situacao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["situacao"])){
            $sql  .= $virgula." situacao = '$this->situacao' ";
            if(trim($this->situacao) == null ){
                $this->erro_sql = " Campo situacao não informado.";
                $this->erro_campo = "situacao";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }

        $sql .= " WHERE ";
        if($this->sequencial != null){
            $sql .= " sequencial = $this->sequencial ";
        }
        // echo $sql; die;
        $result = db_query($sql);
        if($result==false) {
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            $this->erro_sql   = "Falha ao salvar o processo fis_fiscal. Alteracão Abortada.\\n";
            $this->erro_sql .= "Código : ".$this->sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if(pg_affected_rows($result) == 0){
                $this->erro_banco = "";
                $this->erro_sql   = "Processo fiscal não alterado. Alteracão Executada.\\n";
                $this->erro_sql  .= "Código : ".$this->sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg  .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso\\n";
                $this->erro_sql .= "Código : ".$this->sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
	} # end function alterar

	public function excluir($sequencial = null, $dbWhere = null) {
		$sql = "DELETE FROM fiscalizacao.fis_processoprorrogacaofinalizacao ";
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
           $this->erro_sql   = "Finalização ou prorrogação do processo fiscal não excluída. Exclusão abortada.\\n";
           $this->erro_sql .= "Valores : ".$sequencial;
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           $this->numrows_excluir = 0;
           return false;
        } else {
            if(pg_affected_rows($result)==0){
                $this->erro_banco = "";
                $this->erro_sql = "Finalização ou prorrogação do processo fiscal não encontrada. Exclusão não efetuada.\\n";
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
        $sql .= " FROM fiscalizacao.fis_processoprorrogacaofinalizacao AS p ";
        $sql2 = "";
        if($dbWhere == ""){
            if($sequencial != null ){
                $sql2 .= " WHERE p.sequencial = $sequencial ";
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
