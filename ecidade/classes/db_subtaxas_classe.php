<?php

class cl_subtaxas
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
    public $ar54_subtaxa = 0; 
    public $ar54_taxa = 0; 
    public $ar54_sequencial = 0; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 ar54_subtaxa = int4 = Subtaxa 
                 ar54_taxa = int4 = Taxa 
                 ar54_sequencial = int4 = Sequencial 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("subtaxas"); 
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
       $this->ar54_subtaxa = ($this->ar54_subtaxa == ""?@$GLOBALS["HTTP_POST_VARS"]["ar54_subtaxa"]:$this->ar54_subtaxa);
       $this->ar54_taxa = ($this->ar54_taxa == ""?@$GLOBALS["HTTP_POST_VARS"]["ar54_taxa"]:$this->ar54_taxa);
       $this->ar54_sequencial = ($this->ar54_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["ar54_sequencial"]:$this->ar54_sequencial);
     }else{
       $this->ar54_sequencial = ($this->ar54_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["ar54_sequencial"]:$this->ar54_sequencial);
     }
   }

    public function incluir()
    {
      $this->atualizacampos();
     if($this->ar54_subtaxa == null ){ 
       $this->erro_sql = " Campo Subtaxa não informado.";
       $this->erro_campo = "ar54_subtaxa";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ar54_taxa == null ){ 
       $this->erro_sql = " Campo Taxa não informado.";
       $this->erro_campo = "ar54_taxa";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into subtaxas(
                                       ar54_subtaxa 
                                      ,ar54_taxa
                       )
                values (
                                $this->ar54_subtaxa 
                               ,$this->ar54_taxa
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Subtaxas ($this->ar54_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Subtaxas já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Subtaxas ($this->ar54_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->ar54_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ar54_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1015370,'$this->ar54_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,1011133,1015372,'','".AddSlashes(pg_result($resaco,0,'ar54_subtaxa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011133,1015371,'','".AddSlashes(pg_result($resaco,0,'ar54_taxa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011133,1015370,'','".AddSlashes(pg_result($resaco,0,'ar54_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($ar54_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update subtaxas set ";
     $virgula = "";
     if(trim($this->ar54_subtaxa)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ar54_subtaxa"])){ 
       $sql  .= $virgula." ar54_subtaxa = $this->ar54_subtaxa ";
       $virgula = ",";
       if(trim($this->ar54_subtaxa) == null ){ 
         $this->erro_sql = " Campo Subtaxa não informado.";
         $this->erro_campo = "ar54_subtaxa";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ar54_taxa)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ar54_taxa"])){ 
       $sql  .= $virgula." ar54_taxa = $this->ar54_taxa ";
       $virgula = ",";
       if(trim($this->ar54_taxa) == null ){ 
         $this->erro_sql = " Campo Taxa não informado.";
         $this->erro_campo = "ar54_taxa";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ar54_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ar54_sequencial"])){ 
       $sql  .= $virgula." ar54_sequencial = $this->ar54_sequencial ";
       $virgula = ",";
       if(trim($this->ar54_sequencial) == null ){ 
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "ar54_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($ar54_sequencial!=null){
       $sql .= " ar54_sequencial = $this->ar54_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ar54_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1015370,'$this->ar54_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ar54_subtaxa"]) || $this->ar54_subtaxa != "")
             $resac = db_query("insert into db_acount values($acount,1011133,1015372,'".AddSlashes(pg_result($resaco,$conresaco,'ar54_subtaxa'))."','$this->ar54_subtaxa',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ar54_taxa"]) || $this->ar54_taxa != "")
             $resac = db_query("insert into db_acount values($acount,1011133,1015371,'".AddSlashes(pg_result($resaco,$conresaco,'ar54_taxa'))."','$this->ar54_taxa',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ar54_sequencial"]) || $this->ar54_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,1011133,1015370,'".AddSlashes(pg_result($resaco,$conresaco,'ar54_sequencial'))."','$this->ar54_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Subtaxas não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->ar54_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Subtaxas não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->ar54_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->ar54_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($ar54_sequencial=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($ar54_sequencial));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1015370,'$ar54_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,1011133,1015372,'','".AddSlashes(pg_result($resaco,$iresaco,'ar54_subtaxa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011133,1015371,'','".AddSlashes(pg_result($resaco,$iresaco,'ar54_taxa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011133,1015370,'','".AddSlashes(pg_result($resaco,$iresaco,'ar54_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from subtaxas
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($ar54_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " ar54_sequencial = $ar54_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Subtaxas não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$ar54_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Subtaxas não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$ar54_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$ar54_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:subtaxas";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($ar54_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from subtaxas ";
     $sql .= "      inner join taxaslancadas on taxaslancadas.ar44_sequencial = subtaxas.ar54_subtaxa";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ar54_sequencial)) {
         $sql2 .= " where subtaxas.ar54_sequencial = $ar54_sequencial "; 
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

    public function sql_query_file($ar54_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from subtaxas ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ar54_sequencial)){
         $sql2 .= " where subtaxas.ar54_sequencial = $ar54_sequencial "; 
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
