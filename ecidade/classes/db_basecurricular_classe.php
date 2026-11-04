<?php
//MODULO: secretariadeeducacao
//CLASSE DA ENTIDADE basecurricular
class cl_basecurricular { 
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
   var $ed141_sequencial = 0; 
   var $ed141_cursoedu = 0; 
   var $ed141_tipo = 0; 
   var $ed141_descricao = null; 
   // cria propriedade com as variaveis do arquivo 
   var $campos = "
                 ed141_sequencial = int4 = Codigo 
                 ed141_cursoedu = int4 = Curso 
                 ed141_tipo = int4 = Tipo 
                 ed141_descricao = varchar(40) = Descrição 
                 ";

   //funcao construtor da classe 
   public function __construct() { 
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("basecurricular"); 
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
       $this->ed141_sequencial = ($this->ed141_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["ed141_sequencial"]:$this->ed141_sequencial);
       $this->ed141_cursoedu = ($this->ed141_cursoedu == ""?@$GLOBALS["HTTP_POST_VARS"]["ed141_cursoedu"]:$this->ed141_cursoedu);
       $this->ed141_tipo = ($this->ed141_tipo == ""?@$GLOBALS["HTTP_POST_VARS"]["ed141_tipo"]:$this->ed141_tipo);
       $this->ed141_descricao = ($this->ed141_descricao == ""?@$GLOBALS["HTTP_POST_VARS"]["ed141_descricao"]:$this->ed141_descricao);
     }else{
       $this->ed141_sequencial = ($this->ed141_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["ed141_sequencial"]:$this->ed141_sequencial);
     }
   }
   // funcao para Inclusão
   public function incluir ($ed141_sequencial){ 
      $this->atualizacampos();
     if($this->ed141_cursoedu == null ){ 
       $this->erro_sql = " Campo Curso não informado.";
       $this->erro_campo = "ed141_cursoedu";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed141_tipo == null ){ 
       $this->erro_sql = " Campo Tipo não informado.";
       $this->erro_campo = "ed141_tipo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed141_descricao == null ){ 
       $this->erro_sql = " Campo Descrição não informado.";
       $this->erro_campo = "ed141_descricao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($ed141_sequencial == "" || $ed141_sequencial == null ){
       $result = db_query("select nextval('basecurricular_ed141_sequencial_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: basecurricular_ed141_sequencial_seq do campo: ed141_sequencial"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->ed141_sequencial = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from basecurricular_ed141_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $ed141_sequencial)){
         $this->erro_sql = " Campo ed141_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->ed141_sequencial = $ed141_sequencial; 
       }
     }
     if(($this->ed141_sequencial == null) || ($this->ed141_sequencial == "") ){ 
       $this->erro_sql = " Campo ed141_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into basecurricular(
                                       ed141_sequencial 
                                      ,ed141_cursoedu 
                                      ,ed141_tipo 
                                      ,ed141_descricao 
                       )
                values (
                                $this->ed141_sequencial 
                               ,$this->ed141_cursoedu 
                               ,$this->ed141_tipo 
                               ,'$this->ed141_descricao' 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Base Curricular ($this->ed141_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Base Curricular já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Base Curricular ($this->ed141_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->ed141_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ed141_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,22348,'$this->ed141_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,4026,22348,'','".AddSlashes(pg_result($resaco,0,'ed141_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,4026,22349,'','".AddSlashes(pg_result($resaco,0,'ed141_cursoedu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,4026,22350,'','".AddSlashes(pg_result($resaco,0,'ed141_tipo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,4026,22351,'','".AddSlashes(pg_result($resaco,0,'ed141_descricao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 
   // funcao para alteracao
   public function alterar ($ed141_sequencial=null) { 
      $this->atualizacampos();
     $sql = " update basecurricular set ";
     $virgula = "";
     if(trim($this->ed141_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed141_sequencial"])){ 
       $sql  .= $virgula." ed141_sequencial = $this->ed141_sequencial ";
       $virgula = ",";
       if(trim($this->ed141_sequencial) == null ){ 
         $this->erro_sql = " Campo Codigo não informado.";
         $this->erro_campo = "ed141_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed141_cursoedu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed141_cursoedu"])){ 
       $sql  .= $virgula." ed141_cursoedu = $this->ed141_cursoedu ";
       $virgula = ",";
       if(trim($this->ed141_cursoedu) == null ){ 
         $this->erro_sql = " Campo Curso não informado.";
         $this->erro_campo = "ed141_cursoedu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed141_tipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed141_tipo"])){ 
       $sql  .= $virgula." ed141_tipo = $this->ed141_tipo ";
       $virgula = ",";
       if(trim($this->ed141_tipo) == null ){ 
         $this->erro_sql = " Campo Tipo não informado.";
         $this->erro_campo = "ed141_tipo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed141_descricao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed141_descricao"])){ 
       $sql  .= $virgula." ed141_descricao = '$this->ed141_descricao' ";
       $virgula = ",";
       if(trim($this->ed141_descricao) == null ){ 
         $this->erro_sql = " Campo Descrição não informado.";
         $this->erro_campo = "ed141_descricao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($ed141_sequencial!=null){
       $sql .= " ed141_sequencial = $this->ed141_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ed141_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,22348,'$this->ed141_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed141_sequencial"]) || $this->ed141_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,4026,22348,'".AddSlashes(pg_result($resaco,$conresaco,'ed141_sequencial'))."','$this->ed141_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed141_cursoedu"]) || $this->ed141_cursoedu != "")
             $resac = db_query("insert into db_acount values($acount,4026,22349,'".AddSlashes(pg_result($resaco,$conresaco,'ed141_cursoedu'))."','$this->ed141_cursoedu',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed141_tipo"]) || $this->ed141_tipo != "")
             $resac = db_query("insert into db_acount values($acount,4026,22350,'".AddSlashes(pg_result($resaco,$conresaco,'ed141_tipo'))."','$this->ed141_tipo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed141_descricao"]) || $this->ed141_descricao != "")
             $resac = db_query("insert into db_acount values($acount,4026,22351,'".AddSlashes(pg_result($resaco,$conresaco,'ed141_descricao'))."','$this->ed141_descricao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Base Curricular não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->ed141_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Base Curricular não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->ed141_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->ed141_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   public function excluir ($ed141_sequencial=null,$dbwhere=null) { 

     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($ed141_sequencial));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,22348,'$ed141_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,4026,22348,'','".AddSlashes(pg_result($resaco,$iresaco,'ed141_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,4026,22349,'','".AddSlashes(pg_result($resaco,$iresaco,'ed141_cursoedu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,4026,22350,'','".AddSlashes(pg_result($resaco,$iresaco,'ed141_tipo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,4026,22351,'','".AddSlashes(pg_result($resaco,$iresaco,'ed141_descricao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from basecurricular
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($ed141_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " ed141_sequencial = $ed141_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Base Curricular não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$ed141_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Base Curricular não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$ed141_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$ed141_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:basecurricular";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql 
   public function sql_query ($ed141_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from basecurricular ";
     $sql .= "      inner join cursoedu  on  cursoedu.ed29_i_codigo = basecurricular.ed141_cursoedu";
     $sql .= "      inner join ensino  on  ensino.ed10_i_codigo = cursoedu.ed29_i_ensino";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ed141_sequencial)) {
         $sql2 .= " where basecurricular.ed141_sequencial = $ed141_sequencial "; 
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
   public function sql_query_file ($ed141_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from basecurricular ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ed141_sequencial)){
         $sql2 .= " where basecurricular.ed141_sequencial = $ed141_sequencial "; 
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
