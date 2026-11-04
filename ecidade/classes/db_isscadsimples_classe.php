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

//MODULO: issqn
//CLASSE DA ENTIDADE isscadsimples
class cl_isscadsimples {
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
   public $q38_sequencial = 0;
   public $q38_inscr = 0;
   public $q38_dtinicial_dia = null;
   public $q38_dtinicial_mes = null;
   public $q38_dtinicial_ano = null;
   public $q38_dtinicial = null;
   public $q38_categoria = 0;
   public $q38_observacao = null;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 q38_sequencial = int4 = Código Sequencial
                 q38_inscr = int4 = Número da Inscrição
                 q38_dtinicial = date = Data Inicial
                 q38_categoria = int4 = Categoria
                 q38_observacao varchar(60) = Observação
                 ";
   //funcao construtor da classe
   public function cl_isscadsimples() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("isscadsimples");
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
       $this->q38_sequencial = ($this->q38_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["q38_sequencial"]:$this->q38_sequencial);
       $this->q38_inscr = ($this->q38_inscr == ""?@$GLOBALS["HTTP_POST_VARS"]["q38_inscr"]:$this->q38_inscr);
       if($this->q38_dtinicial == ""){
         $this->q38_dtinicial_dia = ($this->q38_dtinicial_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["q38_dtinicial_dia"]:$this->q38_dtinicial_dia);
         $this->q38_dtinicial_mes = ($this->q38_dtinicial_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["q38_dtinicial_mes"]:$this->q38_dtinicial_mes);
         $this->q38_dtinicial_ano = ($this->q38_dtinicial_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["q38_dtinicial_ano"]:$this->q38_dtinicial_ano);
         if($this->q38_dtinicial_dia != ""){
            $this->q38_dtinicial = $this->q38_dtinicial_ano."-".$this->q38_dtinicial_mes."-".$this->q38_dtinicial_dia;
         }
       }
       $this->q38_categoria = ($this->q38_categoria == ""?@$GLOBALS["HTTP_POST_VARS"]["q38_categoria"]:$this->q38_categoria);
       $this->q38_observacao = ($this->q38_observacao == null?@$GLOBALS["HTTP_POST_VARS"]["q38_observacao"]:$this->q38_observacao);
     }else{
       $this->q38_sequencial = ($this->q38_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["q38_sequencial"]:$this->q38_sequencial);
     }
   }
   // funcao para inclusao
   public function incluir ($q38_sequencial){
      $this->atualizacampos();
     if($this->q38_inscr == null ){
       $this->erro_sql = " Campo Número da Inscrição nao Informado.";
       $this->erro_campo = "q38_inscr";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->q38_dtinicial == null ){
       $this->erro_sql = " Campo Data Inicial nao Informado.";
       $this->erro_campo = "q38_dtinicial_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->q38_categoria == null ){
       $this->erro_sql = " Campo Categoria nao Informado.";
       $this->erro_campo = "q38_categoria";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($q38_sequencial == "" || $q38_sequencial == null ){
       $result = db_query("select nextval('isscadsimples_q38_sequencial_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: isscadsimples_q38_sequencial_seq do campo: q38_sequencial";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->q38_sequencial = pg_fetch_result($result,0,0);
     }else{
       $result = db_query("select last_value from isscadsimples_q38_sequencial_seq");
       if(($result != false) && (pg_fetch_result($result,0,0) < $q38_sequencial)){
         $this->erro_sql = " Campo q38_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->q38_sequencial = $q38_sequencial;
       }
     }
     if(($this->q38_sequencial == null) || ($this->q38_sequencial == "") ){
       $this->erro_sql = " Campo q38_sequencial nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into isscadsimples(
                                       q38_sequencial
                                      ,q38_inscr
                                      ,q38_dtinicial
                                      ,q38_categoria
                                      ,q38_observacao
                       )
                values (
                                $this->q38_sequencial
                               ,$this->q38_inscr
                               ,".($this->q38_dtinicial == "null" || $this->q38_dtinicial == ""?"null":"'".$this->q38_dtinicial."'")."
                               ,$this->q38_categoria
                               ,'$this->q38_observacao'
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Cadastro de Optantes pelo Simples ($this->q38_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Cadastro de Optantes pelo Simples já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Cadastro de Optantes pelo Simples ($this->q38_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->q38_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);

     return true;
   }
   // funcao para alteracao
   public function alterar ($q38_sequencial=null) {
      $this->atualizacampos();
     $sql = " update isscadsimples set ";
     $virgula = "";
     if(trim($this->q38_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["q38_sequencial"])){
       $sql  .= $virgula." q38_sequencial = $this->q38_sequencial ";
       $virgula = ",";
       if(trim($this->q38_sequencial) == null ){
         $this->erro_sql = " Campo Código Sequencial nao Informado.";
         $this->erro_campo = "q38_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->q38_inscr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["q38_inscr"])){
       $sql  .= $virgula." q38_inscr = $this->q38_inscr ";
       $virgula = ",";
       if(trim($this->q38_inscr) == null ){
         $this->erro_sql = " Campo Número da Inscrição nao Informado.";
         $this->erro_campo = "q38_inscr";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->q38_dtinicial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["q38_dtinicial_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["q38_dtinicial_dia"] !="") ){
       $sql  .= $virgula." q38_dtinicial = '$this->q38_dtinicial' ";
       $virgula = ",";
       if(trim($this->q38_dtinicial) == null ){
         $this->erro_sql = " Campo Data Inicial nao Informado.";
         $this->erro_campo = "q38_dtinicial_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["q38_dtinicial_dia"])){
         $sql  .= $virgula." q38_dtinicial = null ";
         $virgula = ",";
         if(trim($this->q38_dtinicial) == null ){
           $this->erro_sql = " Campo Data Inicial nao Informado.";
           $this->erro_campo = "q38_dtinicial_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->q38_categoria)!="" || isset($GLOBALS["HTTP_POST_VARS"]["q38_categoria"])){
       $sql  .= $virgula." q38_categoria = $this->q38_categoria ";
       $virgula = ",";
       if(trim($this->q38_categoria) == null ){
         $this->erro_sql = " Campo Categoria nao Informado.";
         $this->erro_campo = "q38_categoria";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->q38_observacao)!=null || isset($GLOBALS["HTTP_POST_VARS"]["q38_observacao"])){
      $sql  .= $virgula." q38_observacao = '$this->q38_observacao' ";
      $virgula = ",";
    }
     $sql .= " where ";
     if($q38_sequencial!=null){
       $sql .= " q38_sequencial = $this->q38_sequencial";
     }

     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Cadastro de Optantes pelo Simples nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->q38_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Cadastro de Optantes pelo Simples nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->q38_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->q38_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($q38_sequencial=null,$dbwhere=null) {
     $sql = " delete from isscadsimples
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($q38_sequencial != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " q38_sequencial = $q38_sequencial ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Cadastro de Optantes pelo Simples nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$q38_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Cadastro de Optantes pelo Simples nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$q38_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$q38_sequencial;
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
     $this->numrows = pg_num_rows($result);
      if($this->numrows==0){
        $this->erro_banco = "";
        $this->erro_sql   = "Record Vazio na Tabela:isscadsimples";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   public function sql_query ( $q38_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from isscadsimples ";
     $sql .= "      inner join issbase  on  issbase.q02_inscr = isscadsimples.q38_inscr";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = issbase.q02_numcgm";
     $sql2 = "";
     if($dbwhere==""){
       if($q38_sequencial!=null ){
         $sql2 .= " where isscadsimples.q38_sequencial = $q38_sequencial ";
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
  public function sql_query_baixa( $q38_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from isscadsimples ";
     $sql .= "      inner join issbase  on  issbase.q02_inscr = isscadsimples.q38_inscr";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = issbase.q02_numcgm";
     $sql .= "      left  join isscadsimplesbaixa  on  q38_sequencial = q39_isscadsimples";
     $sql2 = "";
     if($dbwhere==""){
       if($q38_sequencial!=null ){
         $sql2 .= " where isscadsimples.q38_sequencial = $q38_sequencial ";
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
  public function sql_query_file ( $q38_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from isscadsimples ";
     $sql2 = "";
     if($dbwhere==""){
       if($q38_sequencial!=null ){
         $sql2 .= " where isscadsimples.q38_sequencial = $q38_sequencial ";
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

  public function sql_query_dadosinscr( $q38_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from isscadsimples ";
     $sql .= "      inner join issbase            on issbase.q02_inscr                 = isscadsimples.q38_inscr         ";
     $sql .= "      inner join cgm                on cgm.z01_numcgm                    = issbase.q02_numcgm              ";
     $sql .= "      left  join isscadsimplesbaixa on isscadsimplesbaixa.q39_isscadsimples = isscadsimples.q38_sequencial ";
     $sql .= "      left  join ativprinc          on ativprinc.q88_inscr               = issbase.q02_inscr               ";
     $sql .= "      left  join tabativ            on tabativ.q07_inscr                 = ativprinc.q88_inscr             ";
     $sql .= "                                   and tabativ.q07_seq                   = ativprinc.q88_seq               ";
     $sql .= "      left  join ativid             on ativid.q03_ativ                   = tabativ.q07_ativ                ";
     $sql2 = "";

     if($dbwhere==""){
       if($q38_sequencial!=null ){
         $sql2 .= " where isscadsimples.q38_sequencial = $q38_sequencial ";
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

  /**
   * valida se existe competencia ativa para o cadastro do simples
   * @param  integer $iCodigoInscricao Código da inscrição
   * @param  string  $sCompentencia    Competencia padrao (yyyy-mm)
   * @return string                    Sql
   */
  public function sql_query_valida_competencia($iCodigoInscricao, $sCompentencia) {

    $sSql  = "select *,                                                                                                                                                            ";
    $sSql .= "       case when q39_dtbaixa is null OR '{$sCompentencia}-15'::date between  (extract(year  from q38_dtinicial) || '-' || extract(month from q38_dtinicial) || '-01')::date                 ";
    $sSql .= "                                       and ( coalesce( extract(year  from q39_dtbaixa), extract(year from CURRENT_DATE))  || '-' ||                                  ";
    $sSql .= "                                             coalesce( extract(month from q39_dtbaixa), '12') || '-' ||                                                              ";
    $sSql .= "                                             (SELECT extract(day from CAST(date_trunc('month', q39_dtbaixa) + interval '1 month'- interval '1 day' as date))))::date ";
    $sSql .= "                              then true                                                                                                                              ";
    $sSql .= "                              else false                                                                                                                             ";
    $sSql .= "        end as validade                                                                                                                                              ";
    $sSql .= "  from isscadsimples                                                                                                                                                 ";
    $sSql .= "       left join isscadsimplesbaixa on q39_isscadsimples = q38_sequencial                                                                                            ";
    $sSql .= " where q38_inscr = {$iCodigoInscricao}                                                                                                                               ";

    return $sSql;
  }
}
