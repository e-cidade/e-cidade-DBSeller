<?php

class cl_saltesdepartamento
{
   // cria variaveis de erro 
    public $rotulo = null; 
    public $query_sql = null; 
    public $numrows = 0; 
    public $numrows_incluir = 0; 
    public $numrows_alterar = 0; 
    public $numrows_excluir = 0; 
    public $erro_status = null; 
    public $erro_sql = null; 
    public $erro_banco = null;  
    public $erro_msg = null;  
    public $erro_campo = null;  
    public $pagina_retorno = null; 
    /* Variáveis do Arquivo */
    public $k212_sequencial = 0; 
    public $k212_saltes = 0; 
    public $k212_departamento = 0; 
    public $k212_principal = 'f'; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 k212_sequencial = int4 = Sequencial 
                 k212_saltes = int4 = Conta 
                 k212_departamento = int4 = Departamento 
                 k212_principal = bool = Departamento Principal 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("saltesdepartamento"); 
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
    }

    public function erro($mostra, $retorna)
    {
     if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
        echo "<script>alert(\"".$this->erro_msg."\")</script>";
        if($retorna==true){
           echo "<script>location.href='".$this->pagina_retorno."'</script>";
        }
     }
   }

    public function atualizacampos($exclusao = false)
    {
     if($exclusao==false){
       $this->k212_sequencial = ($this->k212_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["k212_sequencial"]:$this->k212_sequencial);
       $this->k212_saltes = ($this->k212_saltes == ""?@$GLOBALS["HTTP_POST_VARS"]["k212_saltes"]:$this->k212_saltes);
       $this->k212_departamento = ($this->k212_departamento == ""?@$GLOBALS["HTTP_POST_VARS"]["k212_departamento"]:$this->k212_departamento);
       $this->k212_principal = ($this->k212_principal == "f"?@$GLOBALS["HTTP_POST_VARS"]["k212_principal"]:$this->k212_principal);
     }else{
       $this->k212_sequencial = ($this->k212_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["k212_sequencial"]:$this->k212_sequencial);
     }
   }

    public function incluir($k212_sequencial)
    {
      $this->atualizacampos();
     if($this->k212_saltes == null ){ 
       $this->erro_sql = " Campo Conta não informado.";
       $this->erro_campo = "k212_saltes";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->k212_departamento == null ){ 
       $this->erro_sql = " Campo Departamento não informado.";
       $this->erro_campo = "k212_departamento";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->k212_principal == null ){ 
       $this->erro_sql = " Campo Departamento Principal não informado.";
       $this->erro_campo = "k212_principal";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($k212_sequencial == "" || $k212_sequencial == null ){
       $result = db_query("select nextval('saltesdepartamento_k212_sequencial_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: saltesdepartamento_k212_sequencial_seq do campo: k212_sequencial"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->k212_sequencial = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from saltesdepartamento_k212_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $k212_sequencial)){
         $this->erro_sql = " Campo k212_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->k212_sequencial = $k212_sequencial; 
       }
     }
     if(($this->k212_sequencial == null) || ($this->k212_sequencial == "") ){ 
       $this->erro_sql = " Campo k212_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into saltesdepartamento(
                                       k212_sequencial 
                                      ,k212_saltes 
                                      ,k212_departamento 
                                      ,k212_principal 
                       )
                values (
                                $this->k212_sequencial 
                               ,$this->k212_saltes 
                               ,$this->k212_departamento 
                               ,'$this->k212_principal' 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Departamentos das contas da tesouraria ($this->k212_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Departamentos das contas da tesouraria já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Departamentos das contas da tesouraria ($this->k212_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->k212_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->k212_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1014412,'$this->k212_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,1010976,1014412,'','".AddSlashes(pg_result($resaco,0,'k212_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010976,1014413,'','".AddSlashes(pg_result($resaco,0,'k212_saltes'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010976,1014414,'','".AddSlashes(pg_result($resaco,0,'k212_departamento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010976,1014415,'','".AddSlashes(pg_result($resaco,0,'k212_principal'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($k212_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update saltesdepartamento set ";
     $virgula = "";
     if(trim($this->k212_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k212_sequencial"])){ 
       $sql  .= $virgula." k212_sequencial = $this->k212_sequencial ";
       $virgula = ",";
       if(trim($this->k212_sequencial) == null ){ 
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "k212_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->k212_saltes)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k212_saltes"])){ 
       $sql  .= $virgula." k212_saltes = $this->k212_saltes ";
       $virgula = ",";
       if(trim($this->k212_saltes) == null ){ 
         $this->erro_sql = " Campo Conta não informado.";
         $this->erro_campo = "k212_saltes";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->k212_departamento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k212_departamento"])){ 
       $sql  .= $virgula." k212_departamento = $this->k212_departamento ";
       $virgula = ",";
       if(trim($this->k212_departamento) == null ){ 
         $this->erro_sql = " Campo Departamento não informado.";
         $this->erro_campo = "k212_departamento";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->k212_principal)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k212_principal"])){ 
       $sql  .= $virgula." k212_principal = '$this->k212_principal' ";
       $virgula = ",";
       if(trim($this->k212_principal) == null ){ 
         $this->erro_sql = " Campo Departamento Principal não informado.";
         $this->erro_campo = "k212_principal";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($k212_sequencial!=null){
       $sql .= " k212_sequencial = $this->k212_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->k212_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1014412,'$this->k212_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["k212_sequencial"]) || $this->k212_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,1010976,1014412,'".AddSlashes(pg_result($resaco,$conresaco,'k212_sequencial'))."','$this->k212_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["k212_saltes"]) || $this->k212_saltes != "")
             $resac = db_query("insert into db_acount values($acount,1010976,1014413,'".AddSlashes(pg_result($resaco,$conresaco,'k212_saltes'))."','$this->k212_saltes',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["k212_departamento"]) || $this->k212_departamento != "")
             $resac = db_query("insert into db_acount values($acount,1010976,1014414,'".AddSlashes(pg_result($resaco,$conresaco,'k212_departamento'))."','$this->k212_departamento',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["k212_principal"]) || $this->k212_principal != "")
             $resac = db_query("insert into db_acount values($acount,1010976,1014415,'".AddSlashes(pg_result($resaco,$conresaco,'k212_principal'))."','$this->k212_principal',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Departamentos das contas da tesouraria não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->k212_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Departamentos das contas da tesouraria não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->k212_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->k212_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($k212_sequencial=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($k212_sequencial));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1014412,'$k212_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,1010976,1014412,'','".AddSlashes(pg_result($resaco,$iresaco,'k212_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010976,1014413,'','".AddSlashes(pg_result($resaco,$iresaco,'k212_saltes'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010976,1014414,'','".AddSlashes(pg_result($resaco,$iresaco,'k212_departamento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010976,1014415,'','".AddSlashes(pg_result($resaco,$iresaco,'k212_principal'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from saltesdepartamento
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($k212_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " k212_sequencial = $k212_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Departamentos das contas da tesouraria não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$k212_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Departamentos das contas da tesouraria não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$k212_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$k212_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function sql_record($sql)
    {
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
        $this->erro_sql   = "Record Vazio na Tabela:saltesdepartamento";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($k212_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from saltesdepartamento ";
     $sql .= "      inner join db_depart  on  db_depart.coddepto = saltesdepartamento.k212_departamento";
     $sql .= "      inner join saltes  on  saltes.k13_conta = saltesdepartamento.k212_saltes";
     $sql .= "      inner join db_config  on  db_config.codigo = db_depart.instit";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($k212_sequencial)) {
         $sql2 .= " where saltesdepartamento.k212_sequencial = $k212_sequencial "; 
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

    public function sql_query_file($k212_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from saltesdepartamento ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($k212_sequencial)){
         $sql2 .= " where saltesdepartamento.k212_sequencial = $k212_sequencial "; 
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
