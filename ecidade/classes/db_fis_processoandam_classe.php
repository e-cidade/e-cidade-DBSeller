<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBselller Servicos de Informatica
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

//MODULO: fiscal
//CLASSE DA ENTIDADE processoandam
class cl_fis_processoandam {
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
   public $pa01_codandam = 0;
   public $pa01_processo = 0;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 pa01_codandam = int8 = Código do Andamento
                 pa01_processo = int4 = Código do Processo
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_processoandam");
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
       $this->pa01_codandam = ($this->pa01_codandam == ""?@$GLOBALS["HTTP_POST_VARS"]["pa01_codandam"]:$this->pa01_codandam);
       $this->pa01_processo = ($this->pa01_processo == ""?@$GLOBALS["HTTP_POST_VARS"]["pa01_processo"]:$this->pa01_processo);
     }else{
       $this->pa01_codandam = ($this->pa01_codandam == ""?@$GLOBALS["HTTP_POST_VARS"]["pa01_codandam"]:$this->pa01_codandam);
       $this->pa01_processo = ($this->pa01_processo == ""?@$GLOBALS["HTTP_POST_VARS"]["pa01_processo"]:$this->pa01_processo);
     }
   }
   // funcao para inclusao
   public function incluir(){
      $this->atualizacampos();
     if(($this->pa01_codandam == null) || ($this->pa01_codandam == "") ){
       $this->erro_sql    = " Campo Codigo do Andamento nao declarado.";
       $this->erro_banco  = " Chave Estrangeira zerada.";
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->pa01_processo == null) || ($this->pa01_processo == "") ){
       $this->erro_sql    = " Campo do Processo nao declarado.";
       $this->erro_banco  = "Chave Primaria zerada.";
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     $sql = "insert into fiscalizacao.fis_processoandam(
                                       pa01_codandam
                                      ,pa01_processo
                       )
                values (
                                      $this->pa01_codandam
                                     ,$this->pa01_processo
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql    = "processoandam ($this->pa01_codandam."-".$this->pa01_processo) nao Incluído. Inclusao Abortada.";
         $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco  = "Processo Fiscal já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql    = "processoandam ($this->pa01_codandam."-".$this->pa01_codandam) nao Incluído. Inclusao Abortada.";
         $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco  = "";
     $this->erro_sql    = "Inclusao efetuada com Sucesso\\n";
     $this->erro_sql   .= "Valores : ".$this->pa01_codandam."-".$this->pa01_codandam;
     $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     return true;
   }
   // funcao para alteracao
   public function alterar ($pa01_codigo=null) {
     $this->atualizacampos();
     $sql = " update fiscalizacao.fis_processoandam set ";
     $virgula = "";
     if(trim($this->pa01_codandam)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pa01_codandam"])){
       $sql  .= $virgula." pa01_codandam = $this->pa01_codandam ";
       $virgula = ",";
       if(trim($this->pa01_codandam) == null ){
         $this->erro_sql    = " Campo Processo Fiscal nao Informado.";
         $this->erro_campo  = "pa01_codandam";
         $this->erro_banco  = "";
         $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->pa01_processo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pa01_processo"])){
       $sql  .= $virgula." pa01_processo = $this->pa01_processo ";
       $virgula = ",";
       if(trim($this->pa01_processo) == null ){
         $this->erro_sql    = " Campo Processo nao Informado.";
         $this->erro_campo  = "pa01_processo";
         $this->erro_banco  = "";
         $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($pa01_codigo!=null){
       $sql .= " pa01_codigo = $pa01_codigo";
     }
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco  = str_replace("\n","",@pg_last_error());
       $this->erro_sql    = "processoandam nao Alterado. Alteracao Abortada.\\n";
       $this->erro_sql   .= "Valores : ".$this->pa01_codandam."-".$this->pa01_processo;
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco  = "";
         $this->erro_sql    = "processoandam nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql   .= "Valores : ".$this->pa01_codandam."-".$this->pa01_processo;
         $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->pa01_codandam."-".$this->pa01_processo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($pa01_codigo,$dbwhere=null) {

     $sql = " delete from fiscalizacao.fis_processoandam
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($pa01_codigo != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " pa01_codigo = $pa01_codigo ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco  = str_replace("\n","",@pg_last_error());
       $this->erro_sql    = "processoandam nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql   .= "Valores : ".$pa01_codigo;
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco  = "";
         $this->erro_sql    = "processoandam nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql   .= "Valores : ".$pa01_codigo;
         $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco  = "";
         $this->erro_sql    = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql   .= "Valores : ".$pa01_codigo;
         $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao do recordset
   public function sql_record($sql) {
     $result = db_query($sql);
     if($result==false){
       $this->numrows     = 0;
       $this->erro_banco  = str_replace("\n","",@pg_last_error());
       $this->erro_sql    = "Erro ao selecionar os registros.";
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $this->numrows = pg_numrows($result);
      if($this->numrows==0){
        $this->erro_banco   = "";
        $this->erro_sql     = "Record Vazio na Tabela: processoandam";
        $this->erro_msg     = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg    .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status  = "0";
        return false;
      }
     return $result;
   }
   public function sql_query_file ( $pa01_codandam=null,$pa01_processo=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_processoandam ";
     $sql2 = "";
     if($dbwhere==""){
       if($pa01_codandam!=null ){
         $sql2 .= " where pa01_codandam = $pa01_codandam ";
       }
       if($pa01_processo!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " pa01_processo = $pa01_processo ";
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
