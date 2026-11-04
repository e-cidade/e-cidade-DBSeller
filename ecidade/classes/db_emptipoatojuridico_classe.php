<?php

class cl_emptipoatojuridico
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
    public $e166_sequencial = 0; 
    public $e166_empempenho = 0; 
    public $e166_tipoatojuridico = 0; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 e166_sequencial = int4 = Código 
                 e166_empempenho = int4 = Número Empenho 
                 e166_tipoatojuridico = int4 = Tipo Ato Jurídico 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("emptipoatojuridico"); 
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
       $this->e166_sequencial = ($this->e166_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["e166_sequencial"]:$this->e166_sequencial);
       $this->e166_empempenho = ($this->e166_empempenho == ""?@$GLOBALS["HTTP_POST_VARS"]["e166_empempenho"]:$this->e166_empempenho);
       $this->e166_tipoatojuridico = ($this->e166_tipoatojuridico == ""?@$GLOBALS["HTTP_POST_VARS"]["e166_tipoatojuridico"]:$this->e166_tipoatojuridico);
     }else{
       $this->e166_sequencial = ($this->e166_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["e166_sequencial"]:$this->e166_sequencial);
     }
   }

    public function incluir($e166_sequencial)
    {
      $this->atualizacampos();
     if($this->e166_empempenho == null ){ 
       $this->erro_sql = " Campo Número Empenho não informado.";
       $this->erro_campo = "e166_empempenho";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->e166_tipoatojuridico == null ){ 
       $this->erro_sql = " Campo Tipo Ato Jurídico não informado.";
       $this->erro_campo = "e166_tipoatojuridico";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($e166_sequencial == "" || $e166_sequencial == null ){
       $result = db_query("select nextval('emptipoatojuridico_e166_sequencial_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: emptipoatojuridico_e166_sequencial_seq do campo: e166_sequencial"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->e166_sequencial = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from emptipoatojuridico_e166_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $e166_sequencial)){
         $this->erro_sql = " Campo e166_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->e166_sequencial = $e166_sequencial; 
       }
     }
     if(($this->e166_sequencial == null) || ($this->e166_sequencial == "") ){ 
       $this->erro_sql = " Campo e166_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into emptipoatojuridico(
                                       e166_sequencial 
                                      ,e166_empempenho 
                                      ,e166_tipoatojuridico 
                       )
                values (
                                $this->e166_sequencial 
                               ,$this->e166_empempenho 
                               ,$this->e166_tipoatojuridico 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Tipo Ato Jurídico Empenho ($this->e166_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Tipo Ato Jurídico Empenho já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Tipo Ato Jurídico Empenho ($this->e166_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->e166_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     return true;
   } 

    public function alterar($e166_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update emptipoatojuridico set ";
     $virgula = "";
     if(trim($this->e166_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e166_sequencial"])){ 
       $sql  .= $virgula." e166_sequencial = $this->e166_sequencial ";
       $virgula = ",";
       if(trim($this->e166_sequencial) == null ){ 
         $this->erro_sql = " Campo Código não informado.";
         $this->erro_campo = "e166_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e166_empempenho)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e166_empempenho"])){ 
       $sql  .= $virgula." e166_empempenho = $this->e166_empempenho ";
       $virgula = ",";
       if(trim($this->e166_empempenho) == null ){ 
         $this->erro_sql = " Campo Número Empenho não informado.";
         $this->erro_campo = "e166_empempenho";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e166_tipoatojuridico)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e166_tipoatojuridico"])){ 
       $sql  .= $virgula." e166_tipoatojuridico = $this->e166_tipoatojuridico ";
       $virgula = ",";
       if(trim($this->e166_tipoatojuridico) == null ){ 
         $this->erro_sql = " Campo Tipo Ato Jurídico não informado.";
         $this->erro_campo = "e166_tipoatojuridico";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($e166_sequencial!=null){
       $sql .= " e166_sequencial = $this->e166_sequencial";
     }

     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Tipo Ato Jurídico Empenho não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->e166_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Tipo Ato Jurídico Empenho não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->e166_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->e166_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($e166_sequencial=null, $dbwhere = null)
    {
     $sql = " delete from emptipoatojuridico
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($e166_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " e166_sequencial = $e166_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Tipo Ato Jurídico Empenho não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$e166_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Tipo Ato Jurídico Empenho não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$e166_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$e166_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:emptipoatojuridico";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($e166_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from emptipoatojuridico ";
     $sql .= "      inner join empempenho  on  empempenho.e60_numemp = emptipoatojuridico.e166_empempenho";
     $sql .= "      inner join tipoatojuridico  on  tipoatojuridico.e165_sequencial = emptipoatojuridico.e166_tipoatojuridico";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = empempenho.e60_numcgm";
     $sql .= "      inner join db_config  on  db_config.codigo = empempenho.e60_instit";
     $sql .= "      inner join orcdotacao  on  orcdotacao.o58_anousu = empempenho.e60_anousu and  orcdotacao.o58_coddot = empempenho.e60_coddot";
     $sql .= "      inner join pctipocompra  on  pctipocompra.pc50_codcom = empempenho.e60_codcom";
     $sql .= "      inner join emptipo  on  emptipo.e41_codtipo = empempenho.e60_codtipo";
     $sql .= "      inner join concarpeculiar  on  concarpeculiar.c58_sequencial = empempenho.e60_concarpeculiar";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($e166_sequencial)) {
         $sql2 .= " where emptipoatojuridico.e166_sequencial = $e166_sequencial "; 
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

    public function sql_query_file($e166_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from emptipoatojuridico ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($e166_sequencial)){
         $sql2 .= " where emptipoatojuridico.e166_sequencial = $e166_sequencial "; 
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
