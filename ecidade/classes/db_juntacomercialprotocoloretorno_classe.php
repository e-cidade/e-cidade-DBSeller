<?php
//MODULO: issqn
//CLASSE DA ENTIDADE juntacomercialprotocoloretorno
class cl_juntacomercialprotocoloretorno {
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
   var $q149_sequencial = 0;
   var $q149_juntacomercialprotocolo = 0;
   var $q149_xml = null;
   // cria propriedade com as variaveis do arquivo
   var $campos = "
                 q149_sequencial = int4 = Sequencial
                 q149_juntacomercialprotocolo = int4 = Protocolo
                 q149_xml = text = XML
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("juntacomercialprotocoloretorno");
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
       $this->q149_sequencial = ($this->q149_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["q149_sequencial"]:$this->q149_sequencial);
       $this->q149_juntacomercialprotocolo = ($this->q149_juntacomercialprotocolo == ""?@$GLOBALS["HTTP_POST_VARS"]["q149_juntacomercialprotocolo"]:$this->q149_juntacomercialprotocolo);
       $this->q149_xml = ($this->q149_xml == ""?@$GLOBALS["HTTP_POST_VARS"]["q149_xml"]:$this->q149_xml);
     }else{
       $this->q149_sequencial = ($this->q149_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["q149_sequencial"]:$this->q149_sequencial);
     }
   }
   // funcao para inclusao
   public function incluir ($q149_sequencial){
      $this->atualizacampos();
     if($this->q149_juntacomercialprotocolo == null ){
       $this->erro_sql = " Campo Protocolo não informado.";
       $this->erro_campo = "q149_juntacomercialprotocolo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->q149_xml == null ){
       $this->erro_sql = " Campo XML não informado.";
       $this->erro_campo = "q149_xml";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($q149_sequencial == "" || $q149_sequencial == null ){
       $result = db_query("select nextval('juntacomercialprotocoloretorno_q149_sequencial_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: juntacomercialprotocoloretorno_q149_sequencial_seq do campo: q149_sequencial";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->q149_sequencial = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from juntacomercialprotocoloretorno_q149_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $q149_sequencial)){
         $this->erro_sql = " Campo q149_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->q149_sequencial = $q149_sequencial;
       }
     }
     if(($this->q149_sequencial == null) || ($this->q149_sequencial == "") ){
       $this->erro_sql = " Campo q149_sequencial nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into juntacomercialprotocoloretorno(
                                       q149_sequencial
                                      ,q149_juntacomercialprotocolo
                                      ,q149_xml
                       )
                values (
                                $this->q149_sequencial
                               ,$this->q149_juntacomercialprotocolo
                               ,'$this->q149_xml'
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Tabela de retornos de requisicoes do Regin ($this->q149_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Tabela de retornos de requisicoes do Regin já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Tabela de retornos de requisicoes do Regin ($this->q149_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->q149_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->q149_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1009713,'$this->q149_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,1010277,1009713,'','".AddSlashes(pg_result($resaco,0,'q149_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010277,1009714,'','".AddSlashes(pg_result($resaco,0,'q149_juntacomercialprotocolo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010277,1009715,'','".AddSlashes(pg_result($resaco,0,'q149_xml'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }
   // funcao para alteracao
   public function alterar ($q149_sequencial=null) {
      $this->atualizacampos();
     $sql = " update juntacomercialprotocoloretorno set ";
     $virgula = "";
     if(trim($this->q149_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["q149_sequencial"])){
       $sql  .= $virgula." q149_sequencial = $this->q149_sequencial ";
       $virgula = ",";
       if(trim($this->q149_sequencial) == null ){
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "q149_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->q149_juntacomercialprotocolo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["q149_juntacomercialprotocolo"])){
       $sql  .= $virgula." q149_juntacomercialprotocolo = $this->q149_juntacomercialprotocolo ";
       $virgula = ",";
       if(trim($this->q149_juntacomercialprotocolo) == null ){
         $this->erro_sql = " Campo Protocolo não informado.";
         $this->erro_campo = "q149_juntacomercialprotocolo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->q149_xml)!="" || isset($GLOBALS["HTTP_POST_VARS"]["q149_xml"])){
       $sql  .= $virgula." q149_xml = '$this->q149_xml' ";
       $virgula = ",";
       if(trim($this->q149_xml) == null ){
         $this->erro_sql = " Campo XML não informado.";
         $this->erro_campo = "q149_xml";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($q149_sequencial!=null){
       $sql .= " q149_sequencial = $this->q149_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->q149_sequencial));
       if($this->numrows>0){

         for($conresaco=0;$conresaco<$this->numrows;$conresaco++){

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1009713,'$this->q149_sequencial','A')");
           if(isset($GLOBALS["HTTP_POST_VARS"]["q149_sequencial"]) || $this->q149_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,1010277,1009713,'".AddSlashes(pg_result($resaco,$conresaco,'q149_sequencial'))."','$this->q149_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if(isset($GLOBALS["HTTP_POST_VARS"]["q149_juntacomercialprotocolo"]) || $this->q149_juntacomercialprotocolo != "")
             $resac = db_query("insert into db_acount values($acount,1010277,1009714,'".AddSlashes(pg_result($resaco,$conresaco,'q149_juntacomercialprotocolo'))."','$this->q149_juntacomercialprotocolo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if(isset($GLOBALS["HTTP_POST_VARS"]["q149_xml"]) || $this->q149_xml != "")
             $resac = db_query("insert into db_acount values($acount,1010277,1009715,'".AddSlashes(pg_result($resaco,$conresaco,'q149_xml'))."','$this->q149_xml',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Tabela de retornos de requisicoes do Regin nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->q149_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Tabela de retornos de requisicoes do Regin nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->q149_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->q149_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($q149_sequencial=null,$dbwhere=null) {

     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if ($dbwhere==null || $dbwhere=="") {

         $resaco = $this->sql_record($this->sql_query_file($q149_sequencial));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1009713,'$q149_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,1010277,1009713,'','".AddSlashes(pg_result($resaco,$iresaco,'q149_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010277,1009714,'','".AddSlashes(pg_result($resaco,$iresaco,'q149_juntacomercialprotocolo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010277,1009715,'','".AddSlashes(pg_result($resaco,$iresaco,'q149_xml'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from juntacomercialprotocoloretorno
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($q149_sequencial != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " q149_sequencial = $q149_sequencial ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Tabela de retornos de requisicoes do Regin nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$q149_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Tabela de retornos de requisicoes do Regin nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$q149_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$q149_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:juntacomercialprotocoloretorno";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql
   public function sql_query ( $q149_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from juntacomercialprotocoloretorno ";
     $sql .= "      inner join juntacomercialprotocolo  on  juntacomercialprotocolo.q147_sequencial = juntacomercialprotocoloretorno.q149_juntacomercialprotocolo";
     $sql2 = "";
     if($dbwhere==""){
       if($q149_sequencial!=null ){
         $sql2 .= " where juntacomercialprotocoloretorno.q149_sequencial = $q149_sequencial ";
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
   public function sql_query_file ( $q149_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from juntacomercialprotocoloretorno ";
     $sql2 = "";
     if($dbwhere==""){
       if($q149_sequencial!=null ){
         $sql2 .= " where juntacomercialprotocoloretorno.q149_sequencial = $q149_sequencial ";
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
