<?php
//MODULO: esocial
//CLASSE DA ENTIDADE esocialenviostatus
class cl_esocialenviostatus {
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
   var $rh214_sequencial = 0;
   var $rh214_esocialenvio = 0;
   var $rh214_data = "";
   var $rh214_descricao = null;
   var $rh214_situacao = 'f';
   // cria propriedade com as variaveis do arquivo
   var $campos = "
                 rh214_sequencial = int4 = Sequencial 
                 rh214_esocialenvio = int4 = Envio eSocial 
                 rh214_data = varchar(100) = Data 
                 rh214_descricao = varchar(200) = Descrição 
                 rh214_situacao = bool = Situação 
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("esocialenviostatus");
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
       $this->rh214_sequencial = ($this->rh214_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["rh214_sequencial"]:$this->rh214_sequencial);
       $this->rh214_esocialenvio = ($this->rh214_esocialenvio == ""?@$GLOBALS["HTTP_POST_VARS"]["rh214_esocialenvio"]:$this->rh214_esocialenvio);
       $this->rh214_data = ($this->rh214_data == ""?@$GLOBALS["HTTP_POST_VARS"]["rh214_data"]:$this->rh214_data);
       $this->rh214_descricao = ($this->rh214_descricao == ""?@$GLOBALS["HTTP_POST_VARS"]["rh214_descricao"]:$this->rh214_descricao);
       $this->rh214_situacao = ($this->rh214_situacao == "f"?@$GLOBALS["HTTP_POST_VARS"]["rh214_situacao"]:$this->rh214_situacao);
     }else{
       $this->rh214_sequencial = ($this->rh214_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["rh214_sequencial"]:$this->rh214_sequencial);
     }
   }
   // funcao para Inclusão
   public function incluir ($rh214_sequencial){
      $this->atualizacampos();
     if($this->rh214_esocialenvio == null ){
       $this->erro_sql = " Campo Envio eSocial não informado.";
       $this->erro_campo = "rh214_esocialenvio";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh214_descricao == null ){
       $this->erro_sql = " Campo Descrição não informado.";
       $this->erro_campo = "rh214_descricao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh214_situacao == null ){
       $this->erro_sql = " Campo Situação não informado.";
       $this->erro_campo = "rh214_situacao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($rh214_sequencial == "" || $rh214_sequencial == null ){
       $result = db_query("select nextval('esocialenviostatus_rh214_sequencial_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: esocialenviostatus_rh214_sequencial_seq do campo: rh214_sequencial";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->rh214_sequencial = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from esocialenviostatus_rh214_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $rh214_sequencial)){
         $this->erro_sql = " Campo rh214_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->rh214_sequencial = $rh214_sequencial;
       }
     }
     if(($this->rh214_sequencial == null) || ($this->rh214_sequencial == "") ){
       $this->erro_sql = " Campo rh214_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into esocialenviostatus(
                                       rh214_sequencial 
                                      ,rh214_esocialenvio 
                                      ,rh214_descricao 
                                      ,rh214_situacao 
                       )
                values (
                                $this->rh214_sequencial 
                               ,$this->rh214_esocialenvio 
                               ,'$this->rh214_descricao' 
                               ,'$this->rh214_situacao' 
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "eSocial Envio Status ($this->rh214_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "eSocial Envio Status já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "eSocial Envio Status ($this->rh214_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->rh214_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->rh214_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1009598,'$this->rh214_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,1010252,1009598,'','".AddSlashes(pg_result($resaco,0,'rh214_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010252,1009599,'','".AddSlashes(pg_result($resaco,0,'rh214_esocialenvio'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010252,1009600,'','".AddSlashes(pg_result($resaco,0,'rh214_data'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010252,1009602,'','".AddSlashes(pg_result($resaco,0,'rh214_descricao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010252,1009603,'','".AddSlashes(pg_result($resaco,0,'rh214_situacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }
   // funcao para alteracao
   public function alterar ($rh214_sequencial=null) {
      $this->atualizacampos();
     $sql = " update esocialenviostatus set ";
     $virgula = "";
     if(trim($this->rh214_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh214_sequencial"])){
       $sql  .= $virgula." rh214_sequencial = $this->rh214_sequencial ";
       $virgula = ",";
       if(trim($this->rh214_sequencial) == null ){
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "rh214_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh214_esocialenvio)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh214_esocialenvio"])){
       $sql  .= $virgula." rh214_esocialenvio = $this->rh214_esocialenvio ";
       $virgula = ",";
       if(trim($this->rh214_esocialenvio) == null ){
         $this->erro_sql = " Campo Envio eSocial não informado.";
         $this->erro_campo = "rh214_esocialenvio";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh214_data)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh214_data"])){
       $sql  .= $virgula." rh214_data = '$this->rh214_data' ";
       $virgula = ",";
     }
     if(trim($this->rh214_descricao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh214_descricao"])){
       $sql  .= $virgula." rh214_descricao = '$this->rh214_descricao' ";
       $virgula = ",";
       if(trim($this->rh214_descricao) == null ){
         $this->erro_sql = " Campo Descrição não informado.";
         $this->erro_campo = "rh214_descricao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh214_situacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh214_situacao"])){
       $sql  .= $virgula." rh214_situacao = '$this->rh214_situacao' ";
       $virgula = ",";
       if(trim($this->rh214_situacao) == null ){
         $this->erro_sql = " Campo Situação não informado.";
         $this->erro_campo = "rh214_situacao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($rh214_sequencial!=null){
       $sql .= " rh214_sequencial = $this->rh214_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->rh214_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1009598,'$this->rh214_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh214_sequencial"]) || $this->rh214_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,1010252,1009598,'".AddSlashes(pg_result($resaco,$conresaco,'rh214_sequencial'))."','$this->rh214_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh214_esocialenvio"]) || $this->rh214_esocialenvio != "")
             $resac = db_query("insert into db_acount values($acount,1010252,1009599,'".AddSlashes(pg_result($resaco,$conresaco,'rh214_esocialenvio'))."','$this->rh214_esocialenvio',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh214_data"]) || $this->rh214_data != "")
             $resac = db_query("insert into db_acount values($acount,1010252,1009600,'".AddSlashes(pg_result($resaco,$conresaco,'rh214_data'))."','$this->rh214_data',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh214_descricao"]) || $this->rh214_descricao != "")
             $resac = db_query("insert into db_acount values($acount,1010252,1009602,'".AddSlashes(pg_result($resaco,$conresaco,'rh214_descricao'))."','$this->rh214_descricao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh214_situacao"]) || $this->rh214_situacao != "")
             $resac = db_query("insert into db_acount values($acount,1010252,1009603,'".AddSlashes(pg_result($resaco,$conresaco,'rh214_situacao'))."','$this->rh214_situacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "eSocial Envio Status não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->rh214_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "eSocial Envio Status não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->rh214_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->rh214_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($rh214_sequencial=null,$dbwhere=null) {

     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($rh214_sequencial));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1009598,'$rh214_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,1010252,1009598,'','".AddSlashes(pg_result($resaco,$iresaco,'rh214_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010252,1009599,'','".AddSlashes(pg_result($resaco,$iresaco,'rh214_esocialenvio'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010252,1009600,'','".AddSlashes(pg_result($resaco,$iresaco,'rh214_data'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010252,1009602,'','".AddSlashes(pg_result($resaco,$iresaco,'rh214_descricao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010252,1009603,'','".AddSlashes(pg_result($resaco,$iresaco,'rh214_situacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from esocialenviostatus
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($rh214_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " rh214_sequencial = $rh214_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "eSocial Envio Status não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$rh214_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "eSocial Envio Status não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$rh214_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$rh214_sequencial;
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
     if (!$result) {
       $this->numrows    = 0;
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Erro ao selecionar os registros.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $this->numrows = pg_num_rows($result);
      if ($this->numrows == 0) {
        $this->erro_banco = "";
        $this->erro_sql   = "Record Vazio na Tabela:esocialenviostatus";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql
   public function sql_query ($rh214_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
     $sql .= "  from esocialenviostatus ";
     $sql .= "      inner join esocialenvio  on  esocialenvio.rh213_sequencial = esocialenviostatus.rh214_esocialenvio";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($rh214_sequencial)) {
         $sql2 .= " where esocialenviostatus.rh214_sequencial = $rh214_sequencial ";
       }
     } else if (!empty($dbwhere)) {
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if (!empty($ordem)) {
       $sql .= " order by {$ordem}";
     }
     return $sql;
  }
   // funcao do sql
   public function sql_query_file ($rh214_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from esocialenviostatus ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($rh214_sequencial)){
         $sql2 .= " where esocialenviostatus.rh214_sequencial = $rh214_sequencial ";
       }
     } else if (!empty($dbwhere)) {
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if (!empty($ordem)) {
       $sql .= " order by {$ordem}";
     }
     return $sql;
  }

}
