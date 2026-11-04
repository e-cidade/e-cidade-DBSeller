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

/**
 * Módulo: fiscal
 * Classe da entidade datacienciaandamento
 */
class cl_fis_datacienciaandamento
{
	// Variáveis de erro
	public $rotulo    = null;
    public $query_sql = null;
    public $numrows   = 0;
    public $numrows_incluir = 0;
    public $numrows_alterar = 0;
    public $numrows_excluir = 0;
    public $erro_status = null;
    public $erro_sql    = null;
    public $erro_banco  = null;
    public $erro_msg    = null;
    public $erro_campo  = null;
    public $pagina_retorno = null;

    // Atributos da classe
    public $sequencial       = 0;
    public $codigo_da_peca   = 0;
    public $data_ciencia_dia = null;
    public $data_ciencia_mes = null;
    public $data_ciencia_ano = null;
    public $data_ciencia     = null;
    public $fandam = 0;

    // Método construtor
    public function __construct() {
    	//classes dos rotulos dos campos
        $this->rotulo = new rotulo("fis_datacienciaandamento");
        $this->pagina_retorno =  basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
    } // fim método construtor

    //Método de erro
    public function erro($mostra,$retorna) {
        if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
            echo "<script>alert(\"".$this->erro_msg."\");</script>";
            if($retorna==true){
                echo "<script>location.href='".$this->pagina_retorno."'</script>";
            }
        }
    } // fim método erro

    // Método para atualizar os campos
    public function atualizacampos($exclusao=false){
    	if($exclusao==false){
            $this->codigo_peca = ($this->codigo_peca == ""?@$GLOBALS["HTTP_POST_VARS"]["codigo_peca"]:$this->codigo_peca);
            $this->data_ciencia = explode('/', $this->data_ciencia);
            if (checkdate(intval($this->data_ciencia[2]), intval($this->data_ciencia[1]), intval($this->data_ciencia[0]))) {
              $this->data_ciencia = '\''.$this->data_ciencia[0].'-'.$this->data_ciencia[1].'-'.$this->data_ciencia[2].'\'';
            } else {
              $this->data_ciencia = 'NULL';
            }
        }else{
            $this->sequencial = ($this->sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["sequencial"]:$this->sequencial);
        }
    } // fim método atualizacampos

    public function incluir(){
        $this->atualizacampos();

        //var_dump($this->data_ciencia);die;
    	if($this->codigo_peca == null ){
            $this->erro_sql = " Campo código da peça não informado.";
            $this->erro_campo = "codigo_da_peca";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }

        $dataHoje = date('Y-m-d');

        if (strtotime(str_replace("'", "", $this->data_ciencia)) > strtotime($dataHoje)) {
            $this->erro_sql = "Campo data de ciência não pode ser maior que a data de hoje.";
            $this->erro_campo = "data_ciencia";
            $this->erro_banco = "";
            $this->erro_msg = "\\n ".$this->erro_sql." \\n";
            $this->erro_status = '0';
            return false;
        }

        $sql = " INSERT INTO fiscalizacao.fis_datacienciaandamento (codigo_peca, data_ciencia, fandam) ";
        $sql .= " VALUES ($this->codigo_peca, $this->data_ciencia, $this->fandam) ";
        $result = db_query($sql);

        if($result==false){
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
                $this->erro_sql   = "Data ciência ($this->sequencial) não incluída. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Data ciência já cadastrada";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }else{
                $this->erro_sql   = "Data ciência ($this->sequencial) não incluída. Inclusão abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->sequencial;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        return true;
    }// fim método incluir

    public function alterar($sequencial = null, $where = null){
    	$this->atualizacampos();
        $sql = " UPDATE fiscalizacao.fis_datacienciaandamento SET ";
        $virgula = "";
        if(trim($this->sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["sequencial"])){
            $sql  .= $virgula." sequencial = $this->sequencial ";
            $virgula = ",";
            if(trim($this->sequencial) == null ){
                $this->erro_sql = " Campo sequencial não informado.";
                $this->erro_campo = "sequencial";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->codigo_peca)!="" || isset($GLOBALS["HTTP_POST_VARS"]["codigo_peca"])){
            $sql  .= $virgula." codigo_peca = '$this->codigo_peca' ";
            $virgula = ",";
            if(trim($this->codigo_da_peca) == null ){
                $this->erro_sql = " Campo tipo de peça não informado.";
                $this->erro_campo = "codigo_da_peca";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->data_ciencia)!="" || isset($GLOBALS["HTTP_POST_VARS"]["data_ciencia"])){
            $sql  .= $virgula." data_ciencia = '$this->data_ciencia' ";
            $virgula = ",";
            if(trim($this->data_ciencia) == null ){
                $this->erro_sql = " Campo data ciência não informado.";
                $this->erro_campo = "data_ciencia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }

            $dataHoje = date('Y-m-d');
	    if ($this->data_ciencia > $dataHoje) {
	       $this->erro_sql = "Campo data de ciência não pode ser maior que a data de hoje.";
           $this->erro_campo = "data_ciencia";
	       $this->erro_banco = "";
	       $this->erro_msg = "\\n ".$this->erro_sql." \\n";
	       $this->erro_status = '0';
	       return false;
	    }
        }
        if(trim($this->fandam)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fandam"])){
            $sql  .= $virgula." fandam = $this->fandam ";
            $virgula = ",";
            if(trim($this->fandam) == null ){
                $this->erro_sql = " Campo fandam não informado.";
                $this->erro_campo = "fandam";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " WHERE ";
        if($sequencial != null){
            $sql .= " sequencial = $sequencial";
        } else {
            $sql .= " $where ";
        }
        $result = db_query($sql);
        if($result==false){
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            $this->erro_sql   = "Data de ciência não alterada. Alteracão abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        }else{
            if(pg_affected_rows($result)==0){
                $this->erro_banco = "";
                $this->erro_sql = "Data de ciência não alterada. Alteracão executada.\\n";
                $this->erro_sql .= "Valores : ".$this->sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            }else{
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    } // fim método alterar

    public function excluir($sequencial=null,$dbwhere=null){
    	$sql = " DELETE FROM fiscalizacao.fis_datacienciaandamento
        WHERE ";
        $sql2 = "";
        if($dbwhere==null || $dbwhere ==""){
            if($sequencial != ""){
                if($sql2!=""){
                    $sql2 .= " and ";
                }
                $sql2 .= " sequencial = $sequencial ";
            }
        }else{
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if($result==false){
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            $this->erro_sql   = "Data de ciência não excluída. Exclusão abortada.\\n";
            $this->erro_sql .= "Valores : ".$sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        }else{
            if(pg_affected_rows($result)==0){
                $this->erro_banco = "";
                $this->erro_sql = "Data de ciência não encontrada. Exclusão não efetuada.\\n";
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
    } // fim método excluir

    // Método recordset
    public function sql_record($sql) {
        $result = db_query($sql);
        if($result==false){
            $this->numrows    = 0;
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            $this->erro_sql   = "Erro ao selecionar os registros.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->numrows = pg_numrows($result);
        if($this->numrows==0){
            $this->erro_banco = "";
            $this->erro_sql   = "Record Vazio na Tabela: datacienciaandamento";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    } // fim método sql_record

    // Método sql
    public function sql_query ( $sequencial=null,$campos="*",$ordem=null,$dbwhere="") {
        $sql = "SELECT ";
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
        $sql .= " FROM fiscalizacao.fis_datacienciaandamento ";
        $sql2 = "";
        if($dbwhere==""){
            if($sequencial!=null ){
                $sql2 .= " WHERE fis_datacienciaandamento.sequencial = $sequencial ";
            }
        }else if($dbwhere != ""){
            $sql2 = " WHERE $dbwhere ";
        }
        $sql .= $sql2;
        if($ordem != null ){
            $sql .= " ORDER BY ";
            $campos_sql = explode("#",$ordem);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    } // fim método sql_query
}

?>
