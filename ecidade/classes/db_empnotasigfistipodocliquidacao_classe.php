<?php

//MODULO: empenho
//CLASSE DA ENTIDADE empnotasigfistipodocliquidacao
class cl_empnotasigfistipodocliquidacao {
   // cria variaveis de erro
   public $rotulo     = null;
   public $query_sql  = null;
   public $numrows    = 0;
   public $erro_status= null;
   public $erro_sql   = null;
   public $erro_banco = null;
   public $erro_msg   = null;
   public $erro_campo = null;
   public $pagina_retorno = null;
   // cria variaveis do arquivo
   public $e178_sequencial = 0;
   public $e178_empnota = 0;
   public $e178_sigfistipodocliquidacao = 0;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 e178_sequencial = int4 = Código
                 e178_empnota = int4 = Codigo da nota
                 e178_sigfistipodocliquidacao = int4 = Sequencial do documento
                 ";
   //funcao construtor da classe
   function cl_empnotasigfistipodocliquidacao() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("empnotasigfistipodocliquidacao");
     $this->pagina_retorno =  basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
   }
   //funcao erro
   function erro($mostra,$retorna) {
     if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
        echo "<script>alert(\"".$this->erro_msg."\");</script>";
        if($retorna==true){
           echo "<script>location.href='".$this->pagina_retorno."'</script>";
        }
     }
   }
   // funcao para atualizar campos
   function atualizacampos($exclusao=false) {
     if($exclusao==false){
       $this->e178_sequencial = ($this->e178_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["e178_sequencial"]:$this->e178_sequencial);
       $this->e178_empnota = ($this->e178_empnota == ""?@$GLOBALS["HTTP_POST_VARS"]["e178_empnota"]:$this->e178_empnota);
       $this->e178_sigfistipodocliquidacao = ($this->e178_sigfistipodocliquidacao == ""?@$GLOBALS["HTTP_POST_VARS"]["e178_sigfistipodocliquidacao"]:$this->e178_sigfistipodocliquidacao);
     }else{
       $this->e178_sequencial = ($this->e178_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["e178_sequencial"]:$this->e178_sequencial);
     }
   }
   // funcao para inclusao
   function incluir ($e178_sequencial){
      $this->atualizacampos();
     if($this->e178_empnota == null ){
       $this->erro_sql = " Campo Codigo da nota nao Informado.";
       $this->erro_campo = "e178_empnota";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->e178_sigfistipodocliquidacao == null ){
       $this->erro_sql = " Campo Sequencial do documento nao Informado.";
       $this->erro_campo = "e178_sigfistipodocliquidacao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($e178_sequencial == "" || $e178_sequencial == null ){
       $result = @db_query("select nextval('empnotasigfistipodocliquidacao_e178_sequencial_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: empnotasigfistipodocliquidacao_e178_sequencial_seq do campo: e178_sequencial";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->e178_sequencial = pg_fetch_result($result,0,0);
     }else{
       $result = @db_query("select last_value from empnotasigfistipodocliquidacao_e178_sequencial_seq");
       if(($result != false) && (pg_fetch_result($result,0,0) < $e178_sequencial)){
         $this->erro_sql = " Campo e178_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->e178_sequencial = $e178_sequencial;
       }
     }
     if(($this->e178_sequencial == null) || ($this->e178_sequencial == "") ){
       $this->erro_sql = " Campo e178_sequencial nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $result = @db_query("insert into empnotasigfistipodocliquidacao(
                                       e178_sequencial
                                      ,e178_empnota
                                      ,e178_sigfistipodocliquidacao
                       )
                values (
                                $this->e178_sequencial
                               ,$this->e178_empnota
                               ,$this->e178_sigfistipodocliquidacao
                      )");
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Tabela ($this->e178_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Tabela já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Tabela ($this->e178_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->e178_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";

     return true;
   }
   // funcao para alteracao
   function alterar ($e178_sequencial=null) {
      $this->atualizacampos();
     $sql = " update empnotasigfistipodocliquidacao set ";
     $virgula = "";
     if(trim($this->e178_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e178_sequencial"])){
        if(trim($this->e178_sequencial)=="" && isset($GLOBALS["HTTP_POST_VARS"]["e178_sequencial"])){
           $this->e178_sequencial = "0" ;
        }
       $sql  .= $virgula." e178_sequencial = $this->e178_sequencial ";
       $virgula = ",";
       if(trim($this->e178_sequencial) == null ){
         $this->erro_sql = " Campo Código nao Informado.";
         $this->erro_campo = "e178_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e178_empnota)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e178_empnota"])){
        if(trim($this->e178_empnota)=="" && isset($GLOBALS["HTTP_POST_VARS"]["e178_empnota"])){
           $this->e178_empnota = "0" ;
        }
       $sql  .= $virgula." e178_empnota = $this->e178_empnota ";
       $virgula = ",";
       if(trim($this->e178_empnota) == null ){
         $this->erro_sql = " Campo Codigo da nota nao Informado.";
         $this->erro_campo = "e178_empnota";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e178_sigfistipodocliquidacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e178_sigfistipodocliquidacao"])){
        if(trim($this->e178_sigfistipodocliquidacao)=="" && isset($GLOBALS["HTTP_POST_VARS"]["e178_sigfistipodocliquidacao"])){
           $this->e178_sigfistipodocliquidacao = "0" ;
        }
       $sql  .= $virgula." e178_sigfistipodocliquidacao = $this->e178_sigfistipodocliquidacao ";
       $virgula = ",";
       if(trim($this->e178_sigfistipodocliquidacao) == null ){
         $this->erro_sql = " Campo Sequencial do documento nao Informado.";
         $this->erro_campo = "e178_sigfistipodocliquidacao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where  e178_sequencial = $this->e178_sequencial
";

     $result = @db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Tabela nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->e178_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Tabela nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->e178_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->e178_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       }
     }
   }
   // funcao para exclusao
   function excluir ($e178_sequencial=null) {
     $this->atualizacampos(true);

     $sql = " delete from empnotasigfistipodocliquidacao
                    where ";
     $sql2 = "";
      if($this->e178_sequencial != ""){
      if($sql2!=""){
        $sql2 .= " and ";
      }
      $sql2 .= " e178_sequencial = $this->e178_sequencial ";
}
     $result = @db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Tabela nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$this->e178_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Tabela nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$this->e178_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->e178_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       }
     }
   }
   // funcao do recordset
   function sql_record($sql) {
     $result = @db_query($sql);
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
        $this->erro_sql   = "Dados do Grupo nao Encontrado";
        $this->erro_msg   = "Usuário: \n\n ".$this->erro_sql." \n\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql
   function sql_query ( $e178_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from empnotasigfistipodocliquidacao ";
     $sql .= "      inner join empnota  on  empnota.e69_codnota = empnotasigfistipodocliquidacao.e178_empnota";
     $sql .= "      inner join sigfistipodocliquidacao  on  sigfistipodocliquidacao.e177_sequencial = empnotasigfistipodocliquidacao.e178_sigfistipodocliquidacao";
     $sql .= "      inner join empempenho  on  empempenho.e60_numemp = empnota.e69_numemp";
     $sql2 = "";
     if($dbwhere==""){
       if($e178_sequencial!=null ){
         $sql2 .= " where empnotasigfistipodocliquidacao.e178_sequencial = $e178_sequencial ";
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
   function sql_query_file ( $e178_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from empnotasigfistipodocliquidacao ";
     $sql2 = "";
     if($dbwhere==""){
       if($e178_sequencial!=null ){
         $sql2 .= " where empnotasigfistipodocliquidacao.e178_sequencial = $e178_sequencial ";
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
