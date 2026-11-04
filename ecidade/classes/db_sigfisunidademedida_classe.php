<?php

class cl_sigfisunidademedida
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
    public $o220_sequencial = 0; 
    public $o220_codigo = 0; 
    public $o220_abreviacao = null; 
    public $o220_descricao = null; 
    public $o220_ativo = 'f'; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 o220_sequencial = int4 = Sequencial 
                 o220_codigo = int4 = Codigo da unidade de medida 
                 o220_abreviacao = varchar(6) = Abreviação da unidade de medida 
                 o220_descricao = varchar(250) = Descrição da unidade de medida 
                 o220_ativo = bool = Ativo 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("sigfisunidademedida"); 
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
       $this->o220_sequencial = ($this->o220_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["o220_sequencial"]:$this->o220_sequencial);
       $this->o220_codigo = ($this->o220_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["o220_codigo"]:$this->o220_codigo);
       $this->o220_abreviacao = ($this->o220_abreviacao == ""?@$GLOBALS["HTTP_POST_VARS"]["o220_abreviacao"]:$this->o220_abreviacao);
       $this->o220_descricao = ($this->o220_descricao == ""?@$GLOBALS["HTTP_POST_VARS"]["o220_descricao"]:$this->o220_descricao);
       $this->o220_ativo = ($this->o220_ativo == "f"?@$GLOBALS["HTTP_POST_VARS"]["o220_ativo"]:$this->o220_ativo);
     }else{
       $this->o220_sequencial = ($this->o220_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["o220_sequencial"]:$this->o220_sequencial);
     }
   }

    public function incluir($o220_sequencial)
    {
      $this->atualizacampos();
     if($this->o220_codigo == null ){ 
       $this->erro_sql = " Campo Codigo da unidade de medida não informado.";
       $this->erro_campo = "o220_codigo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->o220_abreviacao == null ){ 
       $this->erro_sql = " Campo Abreviação da unidade de medida não informado.";
       $this->erro_campo = "o220_abreviacao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->o220_descricao == null ){ 
       $this->erro_sql = " Campo Descrição da unidade de medida não informado.";
       $this->erro_campo = "o220_descricao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->o220_ativo == null ){ 
       $this->erro_sql = " Campo Ativo não informado.";
       $this->erro_campo = "o220_ativo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($o220_sequencial == "" || $o220_sequencial == null ){
       $result = db_query("select nextval('sigfisunidademedida_o220_sequencial_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: sigfisunidademedida_o220_sequencial_seq do campo: o220_sequencial"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->o220_sequencial = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from sigfisunidademedida_o220_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $o220_sequencial)){
         $this->erro_sql = " Campo o220_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->o220_sequencial = $o220_sequencial; 
       }
     }
     if(($this->o220_sequencial == null) || ($this->o220_sequencial == "") ){ 
       $this->erro_sql = " Campo o220_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into sigfisunidademedida(
                                       o220_sequencial 
                                      ,o220_codigo 
                                      ,o220_abreviacao 
                                      ,o220_descricao 
                                      ,o220_ativo 
                       )
                values (
                                $this->o220_sequencial 
                               ,$this->o220_codigo 
                               ,'$this->o220_abreviacao' 
                               ,'$this->o220_descricao' 
                               ,'$this->o220_ativo' 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Unidade de medida ($this->o220_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Unidade de medida já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Unidade de medida ($this->o220_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->o220_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     return true;
   } 

    public function alterar($o220_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update sigfisunidademedida set ";
     $virgula = "";
     if(trim($this->o220_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o220_sequencial"])){ 
       $sql  .= $virgula." o220_sequencial = $this->o220_sequencial ";
       $virgula = ",";
       if(trim($this->o220_sequencial) == null ){ 
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "o220_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->o220_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o220_codigo"])){ 
       $sql  .= $virgula." o220_codigo = $this->o220_codigo ";
       $virgula = ",";
       if(trim($this->o220_codigo) == null ){ 
         $this->erro_sql = " Campo Codigo da unidade de medida não informado.";
         $this->erro_campo = "o220_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->o220_abreviacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o220_abreviacao"])){ 
       $sql  .= $virgula." o220_abreviacao = '$this->o220_abreviacao' ";
       $virgula = ",";
       if(trim($this->o220_abreviacao) == null ){ 
         $this->erro_sql = " Campo Abreviação da unidade de medida não informado.";
         $this->erro_campo = "o220_abreviacao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->o220_descricao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o220_descricao"])){ 
       $sql  .= $virgula." o220_descricao = '$this->o220_descricao' ";
       $virgula = ",";
       if(trim($this->o220_descricao) == null ){ 
         $this->erro_sql = " Campo Descrição da unidade de medida não informado.";
         $this->erro_campo = "o220_descricao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->o220_ativo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o220_ativo"])){ 
       $sql  .= $virgula." o220_ativo = '$this->o220_ativo' ";
       $virgula = ",";
       if(trim($this->o220_ativo) == null ){ 
         $this->erro_sql = " Campo Ativo não informado.";
         $this->erro_campo = "o220_ativo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($o220_sequencial!=null){
       $sql .= " o220_sequencial = $this->o220_sequencial";
     }
     
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Unidade de medida não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->o220_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Unidade de medida não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->o220_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->o220_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($o220_sequencial=null, $dbwhere = null)
    {
      
     $sql = " delete from sigfisunidademedida
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($o220_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " o220_sequencial = $o220_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Unidade de medida não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$o220_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Unidade de medida não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$o220_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$o220_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:sigfisunidademedida";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($o220_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from sigfisunidademedida ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($o220_sequencial)) {
         $sql2 .= " where sigfisunidademedida.o220_sequencial = $o220_sequencial "; 
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

    public function sql_query_file($o220_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from sigfisunidademedida ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($o220_sequencial)){
         $sql2 .= " where sigfisunidademedida.o220_sequencial = $o220_sequencial "; 
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
