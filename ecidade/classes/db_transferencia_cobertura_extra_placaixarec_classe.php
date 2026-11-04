<?php

class cl_transferencia_cobertura_extra_placaixarec
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
    public $id = 0; 
    public $transferencia_cobertura_extra_id = 0; 
    public $placaixarec_id = 0; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 id = int8 = id 
                 transferencia_cobertura_extra_id = int4 = Transferência de cobertura financeira 
                 placaixarec_id = int4 = Item da Planilha 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("transferencia_cobertura_extra_placaixarec"); 
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
       $this->id = ($this->id == ""?@$GLOBALS["HTTP_POST_VARS"]["id"]:$this->id);
       $this->transferencia_cobertura_extra_id = ($this->transferencia_cobertura_extra_id == ""?@$GLOBALS["HTTP_POST_VARS"]["transferencia_cobertura_extra_id"]:$this->transferencia_cobertura_extra_id);
       $this->placaixarec_id = ($this->placaixarec_id == ""?@$GLOBALS["HTTP_POST_VARS"]["placaixarec_id"]:$this->placaixarec_id);
     }else{
       $this->id = ($this->id == ""?@$GLOBALS["HTTP_POST_VARS"]["id"]:$this->id);
     }
   }

    public function incluir($id)
    {
      $this->atualizacampos();
     if($this->transferencia_cobertura_extra_id == null ){ 
       $this->erro_sql = " Campo Transferência de cobertura financeira não informado.";
       $this->erro_campo = "transferencia_cobertura_extra_id";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->placaixarec_id == null ){ 
       $this->erro_sql = " Campo Item da Planilha não informado.";
       $this->erro_campo = "placaixarec_id";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($id == "" || $id == null ){
       $result = db_query("select nextval('transferencia_cobertura_extra_placaixarec_id_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: transferencia_cobertura_extra_placaixarec_id_seq do campo: id"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->id = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from transferencia_cobertura_extra_placaixarec_id_seq");
       if(($result != false) && (pg_result($result,0,0) < $id)){
         $this->erro_sql = " Campo id maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->id = $id; 
       }
     }
     if(($this->id == null) || ($this->id == "") ){ 
       $this->erro_sql = " Campo id não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into transferencia_cobertura_extra_placaixarec(
                                       id 
                                      ,transferencia_cobertura_extra_id 
                                      ,placaixarec_id 
                       )
                values (
                                $this->id 
                               ,$this->transferencia_cobertura_extra_id 
                               ,$this->placaixarec_id 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Apropriados via Planilhas ($this->id) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Apropriados via Planilhas já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Apropriados via Planilhas ($this->id) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->id;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->id  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1011345,'$this->id','I')");
         $resac = db_query("insert into db_acount values($acount,1011107,1011345,'','".AddSlashes(pg_result($resaco,0,'id'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011107,1015208,'','".AddSlashes(pg_result($resaco,0,'transferencia_cobertura_extra_id'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011107,1015210,'','".AddSlashes(pg_result($resaco,0,'placaixarec_id'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($id=null)
    {
      $this->atualizacampos();
     $sql = " update transferencia_cobertura_extra_placaixarec set ";
     $virgula = "";
     if(trim($this->id)!="" || isset($GLOBALS["HTTP_POST_VARS"]["id"])){ 
       $sql  .= $virgula." id = $this->id ";
       $virgula = ",";
       if(trim($this->id) == null ){ 
         $this->erro_sql = " Campo id não informado.";
         $this->erro_campo = "id";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->transferencia_cobertura_extra_id)!="" || isset($GLOBALS["HTTP_POST_VARS"]["transferencia_cobertura_extra_id"])){ 
       $sql  .= $virgula." transferencia_cobertura_extra_id = $this->transferencia_cobertura_extra_id ";
       $virgula = ",";
       if(trim($this->transferencia_cobertura_extra_id) == null ){ 
         $this->erro_sql = " Campo Transferência de cobertura financeira não informado.";
         $this->erro_campo = "transferencia_cobertura_extra_id";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->placaixarec_id)!="" || isset($GLOBALS["HTTP_POST_VARS"]["placaixarec_id"])){ 
       $sql  .= $virgula." placaixarec_id = $this->placaixarec_id ";
       $virgula = ",";
       if(trim($this->placaixarec_id) == null ){ 
         $this->erro_sql = " Campo Item da Planilha não informado.";
         $this->erro_campo = "placaixarec_id";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($id!=null){
       $sql .= " id = $this->id";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->id));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1011345,'$this->id','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["id"]) || $this->id != "")
             $resac = db_query("insert into db_acount values($acount,1011107,1011345,'".AddSlashes(pg_result($resaco,$conresaco,'id'))."','$this->id',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["transferencia_cobertura_extra_id"]) || $this->transferencia_cobertura_extra_id != "")
             $resac = db_query("insert into db_acount values($acount,1011107,1015208,'".AddSlashes(pg_result($resaco,$conresaco,'transferencia_cobertura_extra_id'))."','$this->transferencia_cobertura_extra_id',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["placaixarec_id"]) || $this->placaixarec_id != "")
             $resac = db_query("insert into db_acount values($acount,1011107,1015210,'".AddSlashes(pg_result($resaco,$conresaco,'placaixarec_id'))."','$this->placaixarec_id',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Apropriados via Planilhas não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->id;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Apropriados via Planilhas não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->id;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->id;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($id=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($id));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1011345,'$id','E')");
           $resac  = db_query("insert into db_acount values($acount,1011107,1011345,'','".AddSlashes(pg_result($resaco,$iresaco,'id'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011107,1015208,'','".AddSlashes(pg_result($resaco,$iresaco,'transferencia_cobertura_extra_id'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011107,1015210,'','".AddSlashes(pg_result($resaco,$iresaco,'placaixarec_id'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from transferencia_cobertura_extra_placaixarec
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($id)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " id = $id ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Apropriados via Planilhas não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$id;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Apropriados via Planilhas não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$id;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$id;
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
        $this->erro_sql   = "Record Vazio na Tabela:transferencia_cobertura_extra_placaixarec";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($id = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from transferencia_cobertura_extra_placaixarec ";
     $sql .= "      inner join transferencia_cobertura_extra  on  transferencia_cobertura_extra.id = transferencia_cobertura_extra_placaixarec.transferencia_cobertura_extra_id";
     $sql .= "      inner join slip  on  slip.k17_codigo = transferencia_cobertura_extra.slip_id";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($id)) {
         $sql2 .= " where transferencia_cobertura_extra_placaixarec.id = $id "; 
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

    public function sql_query_file($id = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from transferencia_cobertura_extra_placaixarec ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($id)){
         $sql2 .= " where transferencia_cobertura_extra_placaixarec.id = $id "; 
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
