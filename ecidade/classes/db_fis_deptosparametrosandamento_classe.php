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

// Módulo: fiscal
// Classe da entidade deptos_parametrosandamento
class cl_fis_deptosparametrosandamento {
// cria variaveis de erro
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
    // cria variaveis do arquivo
    public $sequencial = 0;
    public $parametrosandamento = 1;
    public $db_depart = 1;

    //funcao construtor da classe
    public function __construct() {
    //classes dos rotulos dos campos
        $this->rotulo = new rotulo("fis_deptosparametrosandamento");
        $this->pagina_retorno =  basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
    }

    //funcao erro
    public function erro($mostra,$retorna) {
        if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
            echo "<script>alert(\"".$this->erro_msg."\");</script>";
            if($retorna==true){
                echo "<script>location.href='".$this->pagina_retorno."'</script>";
            }
        }
    }

     // funcao para atualizar campos
     public function atualizacampos($exclusao=false) {
         if($exclusao==false){
             $this->sequencial = ($this->sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["sequencial"]:$this->sequencial);
             $this->parametrosandamento = ($this->parametrosandamento == ""?@$GLOBALS["HTTP_POST_VARS"]["parametrosandamento"]:$this->parametrosandamento);
             $this->db_depart = ($this->db_depart == ""?@$GLOBALS["HTTP_POST_VARS"]["db_depart"]:$this->db_depart);
         }else{
             $this->sequencial = ($this->sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["sequencial"]:$this->sequencial);
         }
     }

    // Método para inclusão
    public function incluir (){
        $this->atualizacampos();
        if($this->parametrosandamento == null ){
            $this->erro_sql = " Campo parâmetros do andamento não informado.";
            $this->erro_campo = "parametrosandamento";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if($this->db_depart == null ){
            $this->erro_sql = " Campo usuário não informado.";
            $this->erro_campo = "db_depart";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = " INSERT INTO fiscalizacao.fis_deptos_parametrosandamento (parametrosandamento, db_depart) ";
        $sql .= " VALUES ($this->parametrosandamento, $this->db_depart) ";
        $result = db_query($sql);
        if($result==false){
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
                $this->erro_sql   = " Parâmetros ($this->sequencial) não incluídos. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Parâmetros já cadastrados";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }else{
                $this->erro_sql   = "Parâmetros ($this->sequencial) não incluídos. Inclusão abortada.";
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
    } // fim método incluir

    // Método para alteração
    public function alterar ($sequencial=null) {
        $this->atualizacampos();
        $sql = " UPDATE fiscalizacao.fis_deptos_parametrosandamento SET ";
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
        if(trim($this->parametrosandamento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["parametrosandamento"])){
            $sql  .= $virgula." parametrosandamento = $this->parametrosandamento ";
            $virgula = ",";
            if(trim($this->parametrosandamento) == null ){
                $this->erro_sql = " Campo parâmetro do andamento não informado.";
                $this->erro_campo = "parametrosandamento";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->db_depart)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db_depart"])){
            $sql  .= $virgula." db_depart = $this->db_depart ";
            $virgula = ",";
            if(trim($this->db_depart) == null ){
                $this->erro_sql = " Campo depto não informado.";
                $this->erro_campo = "db_depart";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " WHERE ";
        if($sequencial!=null){
            $sql .= " sequencial = $sequencial";
        }
        $result = db_query($sql);
        if($result==false){
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            $this->erro_sql   = "Deptos não alterados. Alteracão abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        }else{
            if(pg_affected_rows($result)==0){
                $this->erro_banco = "";
                $this->erro_sql = "Deptos não alterados. Alteracão executada.\\n";
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

    // Método para exclusão
    public function excluir ($sequencial=null,$dbwhere=null) {
        $sql = " DELETE FROM fiscalizacao.fis_deptos_parametrosandamento WHERE ";
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
            $this->erro_sql   = "Usuários parâmetros não excluídos. Exclusão abortada.\\n";
            $this->erro_sql .= "Valores : ".$sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        }else{
            if(pg_affected_rows($result)==0){
                $this->erro_banco = "";
                $this->erro_sql = "Usuários parâmetros não encontrados. Exclusão não efetuada.\\n";
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
            $this->erro_sql   = "Record Vazio na Tabela: parametrosandamento";
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
        $sql .= " FROM fiscalizacao.fis_deptos_parametrosandamento ";
        $sql2 = "";
        if($dbwhere==""){
            if($sequencial!=null ){
                $sql2 .= " WHERE fis_deptos_parametrosandamento.sequencial = $sequencial ";
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

} // fim classe
?>
