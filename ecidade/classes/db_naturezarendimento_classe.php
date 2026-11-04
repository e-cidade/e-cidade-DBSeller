<?php

class cl_naturezarendimento
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
    public $e167_sequencial = 0; 
    public $e167_codigo = null; 
    public $e167_descricao = null; 
    public $e167_tributo = null; 
    public $e167_declarante = null; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 e167_sequencial = int4 = Código 
                 e167_codigo = varchar(5) = Codigo da Natureza de rendimento 
                 e167_descricao = text = Descricao 
                 e167_tributo = varchar(100) = Tributo 
                 e167_declarante = varchar(10) = Declarante 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("naturezarendimento"); 
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
       $this->e167_sequencial = ($this->e167_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["e167_sequencial"]:$this->e167_sequencial);
       $this->e167_codigo = ($this->e167_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["e167_codigo"]:$this->e167_codigo);
       $this->e167_descricao = ($this->e167_descricao == ""?@$GLOBALS["HTTP_POST_VARS"]["e167_descricao"]:$this->e167_descricao);
       $this->e167_tributo = ($this->e167_tributo == ""?@$GLOBALS["HTTP_POST_VARS"]["e167_tributo"]:$this->e167_tributo);
       $this->e167_declarante = ($this->e167_declarante == ""?@$GLOBALS["HTTP_POST_VARS"]["e167_declarante"]:$this->e167_declarante);
     }else{
       $this->e167_sequencial = ($this->e167_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["e167_sequencial"]:$this->e167_sequencial);
     }
   }

    public function incluir($e167_sequencial)
    {
      $this->atualizacampos();
     if($this->e167_codigo == null ){ 
       $this->erro_sql = " Campo Codigo da Natureza de rendimento não informado.";
       $this->erro_campo = "e167_codigo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->e167_descricao == null ){ 
       $this->erro_sql = " Campo Descricao não informado.";
       $this->erro_campo = "e167_descricao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($e167_sequencial == "" || $e167_sequencial == null ){
       $result = db_query("select nextval('naturezarendimento_e167_sequencial_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: naturezarendimento_e167_sequencial_seq do campo: e167_sequencial"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->e167_sequencial = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from naturezarendimento_e167_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $e167_sequencial)){
         $this->erro_sql = " Campo e167_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->e167_sequencial = $e167_sequencial; 
       }
     }
     if(($this->e167_sequencial == null) || ($this->e167_sequencial == "") ){ 
       $this->erro_sql = " Campo e167_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into naturezarendimento(
                                       e167_sequencial 
                                      ,e167_codigo 
                                      ,e167_descricao 
                                      ,e167_tributo 
                                      ,e167_declarante 
                       )
                values (
                                $this->e167_sequencial 
                               ,'$this->e167_codigo' 
                               ,'$this->e167_descricao' 
                               ,'$this->e167_tributo' 
                               ,'$this->e167_declarante' 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Tabela ($this->e167_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Tabela já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Tabela ($this->e167_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->e167_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->e167_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,189724153,'$this->e167_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,209270096,189724153,'','".AddSlashes(pg_result($resaco,0,'e167_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,209270096,169611025,'','".AddSlashes(pg_result($resaco,0,'e167_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,209270096,137099954,'','".AddSlashes(pg_result($resaco,0,'e167_descricao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,209270096,318425237,'','".AddSlashes(pg_result($resaco,0,'e167_tributo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,209270096,267434218,'','".AddSlashes(pg_result($resaco,0,'e167_declarante'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($e167_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update naturezarendimento set ";
     $virgula = "";
     if(trim($this->e167_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e167_sequencial"])){ 
       $sql  .= $virgula." e167_sequencial = $this->e167_sequencial ";
       $virgula = ",";
       if(trim($this->e167_sequencial) == null ){ 
         $this->erro_sql = " Campo Código não informado.";
         $this->erro_campo = "e167_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e167_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e167_codigo"])){ 
       $sql  .= $virgula." e167_codigo = '$this->e167_codigo' ";
       $virgula = ",";
       if(trim($this->e167_codigo) == null ){ 
         $this->erro_sql = " Campo Codigo da Natureza de rendimento não informado.";
         $this->erro_campo = "e167_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e167_descricao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e167_descricao"])){ 
       $sql  .= $virgula." e167_descricao = '$this->e167_descricao' ";
       $virgula = ",";
       if(trim($this->e167_descricao) == null ){ 
         $this->erro_sql = " Campo Descricao não informado.";
         $this->erro_campo = "e167_descricao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e167_tributo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e167_tributo"])){ 
       $sql  .= $virgula." e167_tributo = '$this->e167_tributo' ";
       $virgula = ",";
     }
     if(trim($this->e167_declarante)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e167_declarante"])){ 
       $sql  .= $virgula." e167_declarante = '$this->e167_declarante' ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($e167_sequencial!=null){
       $sql .= " e167_sequencial = $this->e167_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->e167_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,189724153,'$this->e167_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["e167_sequencial"]) || $this->e167_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,209270096,189724153,'".AddSlashes(pg_result($resaco,$conresaco,'e167_sequencial'))."','$this->e167_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["e167_codigo"]) || $this->e167_codigo != "")
             $resac = db_query("insert into db_acount values($acount,209270096,169611025,'".AddSlashes(pg_result($resaco,$conresaco,'e167_codigo'))."','$this->e167_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["e167_descricao"]) || $this->e167_descricao != "")
             $resac = db_query("insert into db_acount values($acount,209270096,137099954,'".AddSlashes(pg_result($resaco,$conresaco,'e167_descricao'))."','$this->e167_descricao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["e167_tributo"]) || $this->e167_tributo != "")
             $resac = db_query("insert into db_acount values($acount,209270096,318425237,'".AddSlashes(pg_result($resaco,$conresaco,'e167_tributo'))."','$this->e167_tributo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["e167_declarante"]) || $this->e167_declarante != "")
             $resac = db_query("insert into db_acount values($acount,209270096,267434218,'".AddSlashes(pg_result($resaco,$conresaco,'e167_declarante'))."','$this->e167_declarante',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Tabela não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->e167_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Tabela não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->e167_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->e167_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($e167_sequencial=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($e167_sequencial));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,189724153,'$e167_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,209270096,189724153,'','".AddSlashes(pg_result($resaco,$iresaco,'e167_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,209270096,169611025,'','".AddSlashes(pg_result($resaco,$iresaco,'e167_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,209270096,137099954,'','".AddSlashes(pg_result($resaco,$iresaco,'e167_descricao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,209270096,318425237,'','".AddSlashes(pg_result($resaco,$iresaco,'e167_tributo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,209270096,267434218,'','".AddSlashes(pg_result($resaco,$iresaco,'e167_declarante'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from naturezarendimento
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($e167_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " e167_sequencial = $e167_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Tabela não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$e167_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Tabela não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$e167_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$e167_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:naturezarendimento";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($e167_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from naturezarendimento ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($e167_sequencial)) {
         $sql2 .= " where naturezarendimento.e167_sequencial = $e167_sequencial "; 
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

    public function sql_query_file($e167_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from naturezarendimento ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($e167_sequencial)){
         $sql2 .= " where naturezarendimento.e167_sequencial = $e167_sequencial "; 
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
