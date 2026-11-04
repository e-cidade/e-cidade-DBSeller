<?php

class cl_unidadesorigem
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
    public $sd112_sequencial = 0; 
    public $sd112_descricao = null; 
    public $sd112_ativo = 'f'; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 sd112_sequencial = int4 = Sequencial da unidade de origem 
                 sd112_descricao = varchar(50) = Descrição 
                 sd112_ativo = bool = Ativo 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("unidadesorigem"); 
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
       $this->sd112_sequencial = ($this->sd112_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["sd112_sequencial"]:$this->sd112_sequencial);
       $this->sd112_descricao = ($this->sd112_descricao == ""?@$GLOBALS["HTTP_POST_VARS"]["sd112_descricao"]:$this->sd112_descricao);
       $this->sd112_ativo = ($this->sd112_ativo == "f"?@$GLOBALS["HTTP_POST_VARS"]["sd112_ativo"]:$this->sd112_ativo);
     }else{
       $this->sd112_sequencial = ($this->sd112_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["sd112_sequencial"]:$this->sd112_sequencial);
     }
   }

    public function incluir($sd112_sequencial)
    {
      $this->atualizacampos();
     if($this->sd112_descricao == null ){ 
       $this->erro_sql = " Campo Descrição não informado.";
       $this->erro_campo = "sd112_descricao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->sd112_ativo == null ){ 
       $this->erro_sql = " Campo Ativo não informado.";
       $this->erro_campo = "sd112_ativo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($sd112_sequencial == "" || $sd112_sequencial == null ){
       $result = db_query("select nextval('unidadesorigem_sd112_sequencial_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: unidadesorigem_sd112_sequencial_seq do campo: sd112_sequencial"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->sd112_sequencial = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from unidadesorigem_sd112_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $sd112_sequencial)){
         $this->erro_sql = " Campo sd112_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->sd112_sequencial = $sd112_sequencial; 
       }
     }
     if(($this->sd112_sequencial == null) || ($this->sd112_sequencial == "") ){ 
       $this->erro_sql = " Campo sd112_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into unidadesorigem(
                                       sd112_sequencial 
                                      ,sd112_descricao 
                                      ,sd112_ativo 
                       )
                values (
                                $this->sd112_sequencial 
                               ,'$this->sd112_descricao' 
                               ,'$this->sd112_ativo' 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Unidades de Origem ($this->sd112_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Unidades de Origem já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Unidades de Origem ($this->sd112_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->sd112_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     return true;
   } 

    public function alterar($sd112_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update unidadesorigem set ";
     $virgula = "";
     if(trim($this->sd112_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["sd112_sequencial"])){ 
       $sql  .= $virgula." sd112_sequencial = $this->sd112_sequencial ";
       $virgula = ",";
       if(trim($this->sd112_sequencial) == null ){ 
         $this->erro_sql = " Campo Sequencial da unidade de origem não informado.";
         $this->erro_campo = "sd112_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->sd112_descricao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["sd112_descricao"])){ 
       $sql  .= $virgula." sd112_descricao = '$this->sd112_descricao' ";
       $virgula = ",";
       if(trim($this->sd112_descricao) == null ){ 
         $this->erro_sql = " Campo Descrição não informado.";
         $this->erro_campo = "sd112_descricao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->sd112_ativo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["sd112_ativo"])){ 
       $sql  .= $virgula." sd112_ativo = '$this->sd112_ativo' ";
       $virgula = ",";
       if(trim($this->sd112_ativo) == null ){ 
         $this->erro_sql = " Campo Ativo não informado.";
         $this->erro_campo = "sd112_ativo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($sd112_sequencial!=null){
       $sql .= " sd112_sequencial = $this->sd112_sequencial";
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Unidades de Origem não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->sd112_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Unidades de Origem não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->sd112_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->sd112_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($sd112_sequencial=null, $dbwhere = null)
    {
     $sql = " delete from unidadesorigem
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($sd112_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " sd112_sequencial = $sd112_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Unidades de Origem não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$sd112_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Unidades de Origem não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$sd112_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$sd112_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:unidadesorigem";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($sd112_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from unidadesorigem ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($sd112_sequencial)) {
         $sql2 .= " where unidadesorigem.sd112_sequencial = $sd112_sequencial "; 
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

    public function sql_query_file($sd112_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from unidadesorigem ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($sd112_sequencial)){
         $sql2 .= " where unidadesorigem.sd112_sequencial = $sd112_sequencial "; 
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
