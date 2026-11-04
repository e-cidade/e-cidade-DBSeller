<?php

class cl_necessidadesubdivisao
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
    public $ed185_sequencial = 0; 
    public $ed185_necessidade = 0; 
    public $ed185_descricao = null; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 ed185_sequencial = int4 = Sequencial 
                 ed185_necessidade = int4 = Necessidade 
                 ed185_descricao = varchar(100) = Descrição 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("necessidadesubdivisao"); 
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
       $this->ed185_sequencial = ($this->ed185_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["ed185_sequencial"]:$this->ed185_sequencial);
       $this->ed185_necessidade = ($this->ed185_necessidade == ""?@$GLOBALS["HTTP_POST_VARS"]["ed185_necessidade"]:$this->ed185_necessidade);
       $this->ed185_descricao = ($this->ed185_descricao == ""?@$GLOBALS["HTTP_POST_VARS"]["ed185_descricao"]:$this->ed185_descricao);
     }else{
       $this->ed185_sequencial = ($this->ed185_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["ed185_sequencial"]:$this->ed185_sequencial);
     }
   }

    public function incluir($ed185_sequencial)
    {
      $this->atualizacampos();
     if($this->ed185_necessidade == null ){ 
       $this->erro_sql = " Campo Necessidade não informado.";
       $this->erro_campo = "ed185_necessidade";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed185_descricao == null ){ 
       $this->erro_sql = " Campo Descrição não informado.";
       $this->erro_campo = "ed185_descricao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($ed185_sequencial == "" || $ed185_sequencial == null ){
       $result = db_query("select nextval('necessidadesubdivisao_ed185_sequencial_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: necessidadesubdivisao_ed185_sequencial_seq do campo: ed185_sequencial"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->ed185_sequencial = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from necessidadesubdivisao_ed185_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $ed185_sequencial)){
         $this->erro_sql = " Campo ed185_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->ed185_sequencial = $ed185_sequencial; 
       }
     }
     if(($this->ed185_sequencial == null) || ($this->ed185_sequencial == "") ){ 
       $this->erro_sql = " Campo ed185_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into necessidadesubdivisao(
                                       ed185_sequencial 
                                      ,ed185_necessidade 
                                      ,ed185_descricao 
                       )
                values (
                                $this->ed185_sequencial 
                               ,$this->ed185_necessidade 
                               ,'$this->ed185_descricao' 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Subdivisão da necessidade especial ($this->ed185_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Subdivisão da necessidade especial já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Subdivisão da necessidade especial ($this->ed185_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->ed185_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ed185_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1014748,'$this->ed185_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,1011023,1014748,'','".AddSlashes(pg_result($resaco,0,'ed185_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011023,1014749,'','".AddSlashes(pg_result($resaco,0,'ed185_necessidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011023,1014750,'','".AddSlashes(pg_result($resaco,0,'ed185_descricao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($ed185_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update necessidadesubdivisao set ";
     $virgula = "";
     if(trim($this->ed185_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed185_sequencial"])){ 
       $sql  .= $virgula." ed185_sequencial = $this->ed185_sequencial ";
       $virgula = ",";
       if(trim($this->ed185_sequencial) == null ){ 
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "ed185_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed185_necessidade)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed185_necessidade"])){ 
       $sql  .= $virgula." ed185_necessidade = $this->ed185_necessidade ";
       $virgula = ",";
       if(trim($this->ed185_necessidade) == null ){ 
         $this->erro_sql = " Campo Necessidade não informado.";
         $this->erro_campo = "ed185_necessidade";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed185_descricao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed185_descricao"])){ 
       $sql  .= $virgula." ed185_descricao = '$this->ed185_descricao' ";
       $virgula = ",";
       if(trim($this->ed185_descricao) == null ){ 
         $this->erro_sql = " Campo Descrição não informado.";
         $this->erro_campo = "ed185_descricao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($ed185_sequencial!=null){
       $sql .= " ed185_sequencial = $this->ed185_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ed185_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1014748,'$this->ed185_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed185_sequencial"]) || $this->ed185_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,1011023,1014748,'".AddSlashes(pg_result($resaco,$conresaco,'ed185_sequencial'))."','$this->ed185_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed185_necessidade"]) || $this->ed185_necessidade != "")
             $resac = db_query("insert into db_acount values($acount,1011023,1014749,'".AddSlashes(pg_result($resaco,$conresaco,'ed185_necessidade'))."','$this->ed185_necessidade',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed185_descricao"]) || $this->ed185_descricao != "")
             $resac = db_query("insert into db_acount values($acount,1011023,1014750,'".AddSlashes(pg_result($resaco,$conresaco,'ed185_descricao'))."','$this->ed185_descricao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Subdivisão da necessidade especial não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->ed185_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Subdivisão da necessidade especial não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->ed185_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->ed185_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($ed185_sequencial=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($ed185_sequencial));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1014748,'$ed185_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,1011023,1014748,'','".AddSlashes(pg_result($resaco,$iresaco,'ed185_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011023,1014749,'','".AddSlashes(pg_result($resaco,$iresaco,'ed185_necessidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011023,1014750,'','".AddSlashes(pg_result($resaco,$iresaco,'ed185_descricao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from necessidadesubdivisao
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($ed185_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " ed185_sequencial = $ed185_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Subdivisão da necessidade especial não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$ed185_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Subdivisão da necessidade especial não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$ed185_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$ed185_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:necessidadesubdivisao";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($ed185_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from necessidadesubdivisao ";
     $sql .= "      inner join necessidade  on  necessidade.ed48_i_codigo = necessidadesubdivisao.ed185_necessidade";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ed185_sequencial)) {
         $sql2 .= " where necessidadesubdivisao.ed185_sequencial = $ed185_sequencial "; 
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

    public function sql_query_file($ed185_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from necessidadesubdivisao ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ed185_sequencial)){
         $sql2 .= " where necessidadesubdivisao.ed185_sequencial = $ed185_sequencial "; 
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
