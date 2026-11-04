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

//MODULO: Atendimento
//CLASSE DA ENTIDADE tarefaprevisaofaserecurso
class cl_tarefaprevisaofaserecurso {
   // cria variaveis de erro
   var $rotulo     = null;
   var $query_sql  = null;
   var $numrows    = 0;
   var $numrows_incluir = 0;
   var $numrows_alterar = 0;
   var $numrows_excluir = 0;
   var $erro_status= null;
   var $erro_sql   = null;
   var $erro_banco = null;
   var $erro_msg   = null;
   var $erro_campo = null;
   var $pagina_retorno = null;
   // cria variaveis do arquivo
   var $at83_sequencial = 0;
   var $at83_tarefaprevisaofase = 0;
   var $at83_usuario = 0;
   // cria propriedade com as variaveis do arquivo
   var $campos = "
                 at83_sequencial = int4 = Codigo sequencial
                 at83_tarefaprevisaofase = int4 = Codigo sequencial
                 at83_usuario = int4 = Cod. Usuário
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("tarefaprevisaofaserecurso");
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
       $this->at83_sequencial = ($this->at83_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["at83_sequencial"]:$this->at83_sequencial);
       $this->at83_tarefaprevisaofase = ($this->at83_tarefaprevisaofase == ""?@$GLOBALS["HTTP_POST_VARS"]["at83_tarefaprevisaofase"]:$this->at83_tarefaprevisaofase);
       $this->at83_usuario = ($this->at83_usuario == ""?@$GLOBALS["HTTP_POST_VARS"]["at83_usuario"]:$this->at83_usuario);
     }else{
       $this->at83_sequencial = ($this->at83_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["at83_sequencial"]:$this->at83_sequencial);
     }
   }
   // funcao para inclusao
   public function incluir ($at83_sequencial){
      $this->atualizacampos();
     if($this->at83_tarefaprevisaofase == null ){
       $this->erro_sql = " Campo Codigo sequencial nao Informado.";
       $this->erro_campo = "at83_tarefaprevisaofase";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->at83_usuario == null ){
       $this->erro_sql = " Campo Cod. Usuário nao Informado.";
       $this->erro_campo = "at83_usuario";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($at83_sequencial == "" || $at83_sequencial == null ){
       $result = db_query("select nextval('tarefaprevisaofaserecurso_at83_sequencial_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: tarefaprevisaofaserecurso_at83_sequencial_seq do campo: at83_sequencial";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->at83_sequencial = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from tarefaprevisaofaserecurso_at83_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $at83_sequencial)){
         $this->erro_sql = " Campo at83_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->at83_sequencial = $at83_sequencial;
       }
     }
     if(($this->at83_sequencial == null) || ($this->at83_sequencial == "") ){
       $this->erro_sql = " Campo at83_sequencial nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into tarefaprevisaofaserecurso(
                                       at83_sequencial
                                      ,at83_tarefaprevisaofase
                                      ,at83_usuario
                       )
                values (
                                $this->at83_sequencial
                               ,$this->at83_tarefaprevisaofase
                               ,$this->at83_usuario
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Usuarios de cada fase da tarefa ($this->at83_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Usuarios de cada fase da tarefa já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Usuarios de cada fase da tarefa ($this->at83_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->at83_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $resaco = $this->sql_record($this->sql_query_file($this->at83_sequencial));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
       $resac = db_query("insert into db_acountkey values($acount,15038,'$this->at83_sequencial','I')");
       $resac = db_query("insert into db_acount values($acount,2644,15038,'','".AddSlashes(pg_result($resaco,0,'at83_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2644,15040,'','".AddSlashes(pg_result($resaco,0,'at83_tarefaprevisaofase'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2644,15039,'','".AddSlashes(pg_result($resaco,0,'at83_usuario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   }
   // funcao para alteracao
   public function alterar ($at83_sequencial=null) {
      $this->atualizacampos();
     $sql = " update tarefaprevisaofaserecurso set ";
     $virgula = "";
     if(trim($this->at83_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["at83_sequencial"])){
       $sql  .= $virgula." at83_sequencial = $this->at83_sequencial ";
       $virgula = ",";
       if(trim($this->at83_sequencial) == null ){
         $this->erro_sql = " Campo Codigo sequencial nao Informado.";
         $this->erro_campo = "at83_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->at83_tarefaprevisaofase)!="" || isset($GLOBALS["HTTP_POST_VARS"]["at83_tarefaprevisaofase"])){
       $sql  .= $virgula." at83_tarefaprevisaofase = $this->at83_tarefaprevisaofase ";
       $virgula = ",";
       if(trim($this->at83_tarefaprevisaofase) == null ){
         $this->erro_sql = " Campo Codigo sequencial nao Informado.";
         $this->erro_campo = "at83_tarefaprevisaofase";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->at83_usuario)!="" || isset($GLOBALS["HTTP_POST_VARS"]["at83_usuario"])){
       $sql  .= $virgula." at83_usuario = $this->at83_usuario ";
       $virgula = ",";
       if(trim($this->at83_usuario) == null ){
         $this->erro_sql = " Campo Cod. Usuário nao Informado.";
         $this->erro_campo = "at83_usuario";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($at83_sequencial!=null){
       $sql .= " at83_sequencial = $this->at83_sequencial";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->at83_sequencial));
     if($this->numrows>0){
       for($conresaco=0;$conresaco<$this->numrows;$conresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,15038,'$this->at83_sequencial','A')");
         if(isset($GLOBALS["HTTP_POST_VARS"]["at83_sequencial"]) || $this->at83_sequencial != "")
           $resac = db_query("insert into db_acount values($acount,2644,15038,'".AddSlashes(pg_result($resaco,$conresaco,'at83_sequencial'))."','$this->at83_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["at83_tarefaprevisaofase"]) || $this->at83_tarefaprevisaofase != "")
           $resac = db_query("insert into db_acount values($acount,2644,15040,'".AddSlashes(pg_result($resaco,$conresaco,'at83_tarefaprevisaofase'))."','$this->at83_tarefaprevisaofase',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["at83_usuario"]) || $this->at83_usuario != "")
           $resac = db_query("insert into db_acount values($acount,2644,15039,'".AddSlashes(pg_result($resaco,$conresaco,'at83_usuario'))."','$this->at83_usuario',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Usuarios de cada fase da tarefa nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->at83_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Usuarios de cada fase da tarefa nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->at83_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->at83_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($at83_sequencial=null,$dbwhere=null) {
     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($at83_sequencial));
     }else{
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       for($iresaco=0;$iresaco<$this->numrows;$iresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,15038,'$at83_sequencial','E')");
         $resac = db_query("insert into db_acount values($acount,2644,15038,'','".AddSlashes(pg_result($resaco,$iresaco,'at83_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2644,15040,'','".AddSlashes(pg_result($resaco,$iresaco,'at83_tarefaprevisaofase'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2644,15039,'','".AddSlashes(pg_result($resaco,$iresaco,'at83_usuario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from tarefaprevisaofaserecurso
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($at83_sequencial != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " at83_sequencial = $at83_sequencial ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Usuarios de cada fase da tarefa nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$at83_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Usuarios de cada fase da tarefa nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$at83_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$at83_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
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
        $this->erro_sql   = "Record Vazio na Tabela:tarefaprevisaofaserecurso";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql
   public function sql_query ( $at83_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from tarefaprevisaofaserecurso ";
     $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = tarefaprevisaofaserecurso.at83_usuario";
     $sql .= "      inner join tarefaprevisaofase  on  tarefaprevisaofase.at82_sequencial = tarefaprevisaofaserecurso.at83_tarefaprevisaofase";
     $sql .= "      inner join tarefaprevisao  as a on   a.at81_sequencial = tarefaprevisaofase.at82_tarefaprevisao";
     $sql .= "      inner join tarefaprevisaocadfase  on  tarefaprevisaocadfase.at84_sequencial = tarefaprevisaofase.at82_tarefaprevisaocadfase";
     $sql2 = "";
     if($dbwhere==""){
       if($at83_sequencial!=null ){
         $sql2 .= " where tarefaprevisaofaserecurso.at83_sequencial = $at83_sequencial ";
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
   // funcao do sql
   public function sql_query_file ( $at83_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from tarefaprevisaofaserecurso ";
     $sql2 = "";
     if($dbwhere==""){
       if($at83_sequencial!=null ){
         $sql2 .= " where tarefaprevisaofaserecurso.at83_sequencial = $at83_sequencial ";
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
