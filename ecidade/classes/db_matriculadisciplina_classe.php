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

//MODULO: escola
//CLASSE DA ENTIDADE matriculadisciplina
class cl_matriculadisciplina {
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
   var $ed298_sequencial = 0;
   var $ed298_matriculadependencia = 0;
   var $ed298_disciplina = 0;
   // cria propriedade com as variaveis do arquivo
   var $campos = "
                 ed298_sequencial = int4 = Sequencial
                 ed298_matriculadependencia = int4 = Matricula Dependencia
                 ed298_disciplina = int4 = Disciplina
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("matriculadisciplina");
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
       $this->ed298_sequencial = ($this->ed298_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["ed298_sequencial"]:$this->ed298_sequencial);
       $this->ed298_matriculadependencia = ($this->ed298_matriculadependencia == ""?@$GLOBALS["HTTP_POST_VARS"]["ed298_matriculadependencia"]:$this->ed298_matriculadependencia);
       $this->ed298_disciplina = ($this->ed298_disciplina == ""?@$GLOBALS["HTTP_POST_VARS"]["ed298_disciplina"]:$this->ed298_disciplina);
     }else{
       $this->ed298_sequencial = ($this->ed298_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["ed298_sequencial"]:$this->ed298_sequencial);
     }
   }
   // funcao para inclusao
   public function incluir ($ed298_sequencial){
      $this->atualizacampos();
     if($this->ed298_matriculadependencia == null ){
       $this->erro_sql = " Campo Matricula Dependencia nao Informado.";
       $this->erro_campo = "ed298_matriculadependencia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed298_disciplina == null ){
       $this->erro_sql = " Campo Disciplina nao Informado.";
       $this->erro_campo = "ed298_disciplina";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($ed298_sequencial == "" || $ed298_sequencial == null ){
       $result = db_query("select nextval('matriculadisciplina_ed298_sequencial_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: matriculadisciplina_ed298_sequencial_seq do campo: ed298_sequencial";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->ed298_sequencial = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from matriculadisciplina_ed298_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $ed298_sequencial)){
         $this->erro_sql = " Campo ed298_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->ed298_sequencial = $ed298_sequencial;
       }
     }
     if(($this->ed298_sequencial == null) || ($this->ed298_sequencial == "") ){
       $this->erro_sql = " Campo ed298_sequencial nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into matriculadisciplina(
                                       ed298_sequencial
                                      ,ed298_matriculadependencia
                                      ,ed298_disciplina
                       )
                values (
                                $this->ed298_sequencial
                               ,$this->ed298_matriculadependencia
                               ,$this->ed298_disciplina
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "matriculadisciplina ($this->ed298_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "matriculadisciplina já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "matriculadisciplina ($this->ed298_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->ed298_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $resaco = $this->sql_record($this->sql_query_file($this->ed298_sequencial));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
       $resac = db_query("insert into db_acountkey values($acount,18530,'$this->ed298_sequencial','I')");
       $resac = db_query("insert into db_acount values($acount,3278,18530,'','".AddSlashes(pg_result($resaco,0,'ed298_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3278,18531,'','".AddSlashes(pg_result($resaco,0,'ed298_matriculadependencia'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3278,18532,'','".AddSlashes(pg_result($resaco,0,'ed298_disciplina'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   }
   // funcao para alteracao
   public function alterar ($ed298_sequencial=null) {
      $this->atualizacampos();
     $sql = " update matriculadisciplina set ";
     $virgula = "";
     if(trim($this->ed298_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed298_sequencial"])){
       $sql  .= $virgula." ed298_sequencial = $this->ed298_sequencial ";
       $virgula = ",";
       if(trim($this->ed298_sequencial) == null ){
         $this->erro_sql = " Campo Sequencial nao Informado.";
         $this->erro_campo = "ed298_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed298_matriculadependencia)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed298_matriculadependencia"])){
       $sql  .= $virgula." ed298_matriculadependencia = $this->ed298_matriculadependencia ";
       $virgula = ",";
       if(trim($this->ed298_matriculadependencia) == null ){
         $this->erro_sql = " Campo Matricula Dependencia nao Informado.";
         $this->erro_campo = "ed298_matriculadependencia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed298_disciplina)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed298_disciplina"])){
       $sql  .= $virgula." ed298_disciplina = $this->ed298_disciplina ";
       $virgula = ",";
       if(trim($this->ed298_disciplina) == null ){
         $this->erro_sql = " Campo Disciplina nao Informado.";
         $this->erro_campo = "ed298_disciplina";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($ed298_sequencial!=null){
       $sql .= " ed298_sequencial = $this->ed298_sequencial";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->ed298_sequencial));
     if($this->numrows>0){
       for($conresaco=0;$conresaco<$this->numrows;$conresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,18530,'$this->ed298_sequencial','A')");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ed298_sequencial"]) || $this->ed298_sequencial != "")
           $resac = db_query("insert into db_acount values($acount,3278,18530,'".AddSlashes(pg_result($resaco,$conresaco,'ed298_sequencial'))."','$this->ed298_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ed298_matriculadependencia"]) || $this->ed298_matriculadependencia != "")
           $resac = db_query("insert into db_acount values($acount,3278,18531,'".AddSlashes(pg_result($resaco,$conresaco,'ed298_matriculadependencia'))."','$this->ed298_matriculadependencia',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ed298_disciplina"]) || $this->ed298_disciplina != "")
           $resac = db_query("insert into db_acount values($acount,3278,18532,'".AddSlashes(pg_result($resaco,$conresaco,'ed298_disciplina'))."','$this->ed298_disciplina',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "matriculadisciplina nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->ed298_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "matriculadisciplina nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->ed298_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->ed298_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($ed298_sequencial=null,$dbwhere=null) {
     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($ed298_sequencial));
     }else{
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       for($iresaco=0;$iresaco<$this->numrows;$iresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,18530,'$ed298_sequencial','E')");
         $resac = db_query("insert into db_acount values($acount,3278,18530,'','".AddSlashes(pg_result($resaco,$iresaco,'ed298_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3278,18531,'','".AddSlashes(pg_result($resaco,$iresaco,'ed298_matriculadependencia'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3278,18532,'','".AddSlashes(pg_result($resaco,$iresaco,'ed298_disciplina'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from matriculadisciplina
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($ed298_sequencial != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " ed298_sequencial = $ed298_sequencial ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "matriculadisciplina nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$ed298_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "matriculadisciplina nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$ed298_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$ed298_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:matriculadisciplina";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql
   public function sql_query ( $ed298_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from matriculadisciplina ";
     $sql .= "      inner join matriculadependecia  on  matriculadependecia.ed297_sequencial = matriculadisciplina.ed298_matriculadependencia";
     $sql .= "      inner join disciplina  on  disciplina.ed12_i_codigo = matriculadisciplina.ed298_disciplina";
     $sql .= "      inner join turma  on  turma.ed57_i_codigo = matriculadependecia.ed297_turma";
     $sql .= "      inner join matricula  on  matricula.ed60_i_codigo = matriculadependecia.ed297_matricula";
     $sql .= "      inner join caddisciplina  on  caddisciplina.ed232_i_codigo = disciplina.ed12_i_caddisciplina";
     $sql .= "      inner join ensino  on  ensino.ed10_i_codigo = disciplina.ed12_i_ensino";
     $sql2 = "";
     if($dbwhere==""){
       if($ed298_sequencial!=null ){
         $sql2 .= " where matriculadisciplina.ed298_sequencial = $ed298_sequencial ";
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
   public function sql_query_file ( $ed298_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from matriculadisciplina ";
     $sql2 = "";
     if($dbwhere==""){
       if($ed298_sequencial!=null ){
         $sql2 .= " where matriculadisciplina.ed298_sequencial = $ed298_sequencial ";
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
