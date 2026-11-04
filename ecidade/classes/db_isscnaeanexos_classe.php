<?php

class cl_isscnaeanexos
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
    public $q178_sequencial = 0; 
    public $q178_cnae = 0; 
    public $q178_issgscadanexos = 0; 
    public $q178_data_fim_dia = null; 
    public $q178_data_fim_mes = null; 
    public $q178_data_fim_ano = null; 
    public $q178_data_fim = null; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 q178_sequencial = int4 = Sequencial 
                 q178_cnae = int4 = CNAE 
                 q178_issgscadanexos = int4 = issgscadanexos 
                 q178_data_fim = date = Data limite 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("isscnaeanexos"); 
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
       $this->q178_sequencial = ($this->q178_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["q178_sequencial"]:$this->q178_sequencial);
       $this->q178_cnae = ($this->q178_cnae == ""?@$GLOBALS["HTTP_POST_VARS"]["q178_cnae"]:$this->q178_cnae);
       $this->q178_issgscadanexos = ($this->q178_issgscadanexos == ""?@$GLOBALS["HTTP_POST_VARS"]["q178_issgscadanexos"]:$this->q178_issgscadanexos);
       if($this->q178_data_fim == ""){
         $this->q178_data_fim_dia = ($this->q178_data_fim_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["q178_data_fim_dia"]:$this->q178_data_fim_dia);
         $this->q178_data_fim_mes = ($this->q178_data_fim_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["q178_data_fim_mes"]:$this->q178_data_fim_mes);
         $this->q178_data_fim_ano = ($this->q178_data_fim_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["q178_data_fim_ano"]:$this->q178_data_fim_ano);
         if($this->q178_data_fim_dia != ""){
            $this->q178_data_fim = $this->q178_data_fim_ano."-".$this->q178_data_fim_mes."-".$this->q178_data_fim_dia;
         }
       }
     }else{
       $this->q178_sequencial = ($this->q178_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["q178_sequencial"]:$this->q178_sequencial);
     }
   }

    public function incluir($q178_sequencial=null)
    {
      $this->atualizacampos();
     if($this->q178_cnae == null ){ 
       $this->erro_sql = " Campo CNAE não informado.";
       $this->erro_campo = "q178_cnae";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->q178_issgscadanexos == null ){ 
       $this->erro_sql = " Campo issgscadanexos não informado.";
       $this->erro_campo = "q178_issgscadanexos";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->q178_data_fim == null ){ 
       $this->q178_data_fim = "null";
     }
     if($q178_sequencial == "" || $q178_sequencial == null ){
       $result = db_query("select nextval('isscnaeanexos_q178_sequencial_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: isscnaeanexos_q178_sequencial_seq do campo: q178_sequencial"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->q178_sequencial = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from isscnaeanexos_q178_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $q178_sequencial)){
         $this->erro_sql = " Campo q178_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->q178_sequencial = $q178_sequencial; 
       }
     }
     if(($this->q178_sequencial == null) || ($this->q178_sequencial == "") ){ 
       $this->erro_sql = " Campo q178_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into isscnaeanexos(
                                       q178_sequencial 
                                      ,q178_cnae 
                                      ,q178_issgscadanexos 
                                      ,q178_data_fim 
                       )
                values (
                                $this->q178_sequencial 
                               ,$this->q178_cnae 
                               ,$this->q178_issgscadanexos 
                               ,".($this->q178_data_fim == "null" || $this->q178_data_fim == ""?"null":"'".$this->q178_data_fim."'")." 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = " ($this->q178_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = " já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = " ($this->q178_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->q178_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->q178_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1011894,'$this->q178_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,1010630,1011894,'','".AddSlashes(pg_result($resaco,0,'q178_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010630,1011895,'','".AddSlashes(pg_result($resaco,0,'q178_cnae'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010630,1011896,'','".AddSlashes(pg_result($resaco,0,'q178_issgscadanexos'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010630,1011897,'','".AddSlashes(pg_result($resaco,0,'q178_data_fim'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($q178_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update isscnaeanexos set ";
     $virgula = "";
     if(trim($this->q178_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["q178_sequencial"])){ 
       $sql  .= $virgula." q178_sequencial = $this->q178_sequencial ";
       $virgula = ",";
       if(trim($this->q178_sequencial) == null ){ 
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "q178_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->q178_cnae)!="" || isset($GLOBALS["HTTP_POST_VARS"]["q178_cnae"])){ 
       $sql  .= $virgula." q178_cnae = $this->q178_cnae ";
       $virgula = ",";
       if(trim($this->q178_cnae) == null ){ 
         $this->erro_sql = " Campo CNAE não informado.";
         $this->erro_campo = "q178_cnae";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->q178_issgscadanexos)!="" || isset($GLOBALS["HTTP_POST_VARS"]["q178_issgscadanexos"])){ 
       $sql  .= $virgula." q178_issgscadanexos = $this->q178_issgscadanexos ";
       $virgula = ",";
       if(trim($this->q178_issgscadanexos) == null ){ 
         $this->erro_sql = " Campo issgscadanexos não informado.";
         $this->erro_campo = "q178_issgscadanexos";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->q178_data_fim)!="" || isset($GLOBALS["HTTP_POST_VARS"]["q178_data_fim_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["q178_data_fim_dia"] !="") ){ 
       $sql  .= $virgula." q178_data_fim = '$this->q178_data_fim' ";
       $virgula = ",";
     }     else{ 
       if(isset($GLOBALS["HTTP_POST_VARS"]["q178_data_fim_dia"])){ 
         $sql  .= $virgula." q178_data_fim = null ";
         $virgula = ",";
       }
     }
     $sql .= " where ";
     if($q178_sequencial!=null){
       $sql .= " q178_sequencial = $this->q178_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->q178_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1011894,'$this->q178_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["q178_sequencial"]) || $this->q178_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,1010630,1011894,'".AddSlashes(pg_result($resaco,$conresaco,'q178_sequencial'))."','$this->q178_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["q178_cnae"]) || $this->q178_cnae != "")
             $resac = db_query("insert into db_acount values($acount,1010630,1011895,'".AddSlashes(pg_result($resaco,$conresaco,'q178_cnae'))."','$this->q178_cnae',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["q178_issgscadanexos"]) || $this->q178_issgscadanexos != "")
             $resac = db_query("insert into db_acount values($acount,1010630,1011896,'".AddSlashes(pg_result($resaco,$conresaco,'q178_issgscadanexos'))."','$this->q178_issgscadanexos',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["q178_data_fim"]) || $this->q178_data_fim != "")
             $resac = db_query("insert into db_acount values($acount,1010630,1011897,'".AddSlashes(pg_result($resaco,$conresaco,'q178_data_fim'))."','$this->q178_data_fim',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = " não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->q178_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = " não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->q178_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->q178_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($q178_sequencial=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($q178_sequencial));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1011894,'$q178_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,1010630,1011894,'','".AddSlashes(pg_result($resaco,$iresaco,'q178_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010630,1011895,'','".AddSlashes(pg_result($resaco,$iresaco,'q178_cnae'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010630,1011896,'','".AddSlashes(pg_result($resaco,$iresaco,'q178_issgscadanexos'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010630,1011897,'','".AddSlashes(pg_result($resaco,$iresaco,'q178_data_fim'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from isscnaeanexos
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($q178_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " q178_sequencial = $q178_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = " não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$q178_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = " não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$q178_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$q178_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:isscnaeanexos";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($q178_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from isscnaeanexos ";
     $sql .= "      inner join cnae  on  cnae.q71_sequencial = isscnaeanexos.q178_cnae";
     $sql .= "      inner join issgscadanexos  on  issgscadanexos.q157_sequencial = isscnaeanexos.q178_issgscadanexos";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($q178_sequencial)) {
         $sql2 .= " where isscnaeanexos.q178_sequencial = $q178_sequencial "; 
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

    public function sql_query_file($q178_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from isscnaeanexos ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($q178_sequencial)){
         $sql2 .= " where isscnaeanexos.q178_sequencial = $q178_sequencial "; 
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
