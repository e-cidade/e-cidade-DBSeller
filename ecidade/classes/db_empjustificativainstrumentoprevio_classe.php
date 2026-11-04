<?php

class cl_empjustificativainstrumentoprevio
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
    public $e176_sequencial = 0; 
    public $e176_empempenho = 0; 
    public $e176_justificativainstrumentoprevio = 0; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 e176_sequencial = int4 = Sequencial 
                 e176_empempenho = int4 = Número Empenho 
                 e176_justificativainstrumentoprevio = int4 = Justificativa Instrumento Prévio 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("empjustificativainstrumentoprevio"); 
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
       $this->e176_sequencial = ($this->e176_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["e176_sequencial"]:$this->e176_sequencial);
       $this->e176_empempenho = ($this->e176_empempenho == ""?@$GLOBALS["HTTP_POST_VARS"]["e176_empempenho"]:$this->e176_empempenho);
       $this->e176_justificativainstrumentoprevio = ($this->e176_justificativainstrumentoprevio == ""?@$GLOBALS["HTTP_POST_VARS"]["e176_justificativainstrumentoprevio"]:$this->e176_justificativainstrumentoprevio);
     }else{
       $this->e176_sequencial = ($this->e176_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["e176_sequencial"]:$this->e176_sequencial);
     }
   }

    public function incluir($e176_sequencial)
    {
      $this->atualizacampos();
     if($this->e176_empempenho == null ){ 
       $this->erro_sql = " Campo Número Empenho não informado.";
       $this->erro_campo = "e176_empempenho";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->e176_justificativainstrumentoprevio == null ){ 
       $this->erro_sql = " Campo Justificativa Instrumento Prévio não informado.";
       $this->erro_campo = "e176_justificativainstrumentoprevio";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($e176_sequencial == "" || $e176_sequencial == null ){
       $result = db_query("select nextval('empjustificativainstrumentoprevio_e176_sequencial_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: empjustificativainstrumentoprevio_e176_sequencial_seq do campo: e176_sequencial"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->e176_sequencial = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from empjustificativainstrumentoprevio_e176_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $e176_sequencial)){
         $this->erro_sql = " Campo e176_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->e176_sequencial = $e176_sequencial; 
       }
     }
     if(($this->e176_sequencial == null) || ($this->e176_sequencial == "") ){ 
       $this->erro_sql = " Campo e176_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into empjustificativainstrumentoprevio(
                                       e176_sequencial 
                                      ,e176_empempenho 
                                      ,e176_justificativainstrumentoprevio 
                       )
                values (
                                $this->e176_sequencial 
                               ,$this->e176_empempenho 
                               ,$this->e176_justificativainstrumentoprevio 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Justificativa Instrumento Prévio Empenho ($this->e176_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Justificativa Instrumento Prévio Empenho já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Justificativa Instrumento Prévio Empenho ($this->e176_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->e176_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     return true;
   } 

    public function alterar($e176_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update empjustificativainstrumentoprevio set ";
     $virgula = "";
     if(trim($this->e176_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e176_sequencial"])){ 
       $sql  .= $virgula." e176_sequencial = $this->e176_sequencial ";
       $virgula = ",";
       if(trim($this->e176_sequencial) == null ){ 
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "e176_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e176_empempenho)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e176_empempenho"])){ 
       $sql  .= $virgula." e176_empempenho = $this->e176_empempenho ";
       $virgula = ",";
       if(trim($this->e176_empempenho) == null ){ 
         $this->erro_sql = " Campo Número Empenho não informado.";
         $this->erro_campo = "e176_empempenho";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e176_justificativainstrumentoprevio)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e176_justificativainstrumentoprevio"])){ 
       $sql  .= $virgula." e176_justificativainstrumentoprevio = $this->e176_justificativainstrumentoprevio ";
       $virgula = ",";
       if(trim($this->e176_justificativainstrumentoprevio) == null ){ 
         $this->erro_sql = " Campo Justificativa Instrumento Prévio não informado.";
         $this->erro_campo = "e176_justificativainstrumentoprevio";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($e176_sequencial!=null){
       $sql .= " e176_sequencial = $this->e176_sequencial";
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Justificativa Instrumento Prévio Empenho não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->e176_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Justificativa Instrumento Prévio Empenho não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->e176_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->e176_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($e176_sequencial=null, $dbwhere = null)
    {
     $sql = " delete from empjustificativainstrumentoprevio
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($e176_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " e176_sequencial = $e176_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Justificativa Instrumento Prévio Empenho não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$e176_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Justificativa Instrumento Prévio Empenho não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$e176_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$e176_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:empjustificativainstrumentoprevio";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($e176_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from empjustificativainstrumentoprevio ";
     $sql .= "      inner join empempenho  on  empempenho.e60_numemp = empjustificativainstrumentoprevio.e176_empempenho";
     $sql .= "      inner join justificativainstrumentoprevio  on  justificativainstrumentoprevio.e174_sequencial = empjustificativainstrumentoprevio.e176_justificativainstrumentoprevio";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = empempenho.e60_numcgm";
     $sql .= "      inner join db_config  on  db_config.codigo = empempenho.e60_instit";
     $sql .= "      inner join orcdotacao  on  orcdotacao.o58_anousu = empempenho.e60_anousu and  orcdotacao.o58_coddot = empempenho.e60_coddot";
     $sql .= "      inner join pctipocompra  on  pctipocompra.pc50_codcom = empempenho.e60_codcom";
     $sql .= "      inner join emptipo  on  emptipo.e41_codtipo = empempenho.e60_codtipo";
     $sql .= "      inner join concarpeculiar  on  concarpeculiar.c58_sequencial = empempenho.e60_concarpeculiar";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($e176_sequencial)) {
         $sql2 .= " where empjustificativainstrumentoprevio.e176_sequencial = $e176_sequencial "; 
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

    public function sql_query_file($e176_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from empjustificativainstrumentoprevio ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($e176_sequencial)){
         $sql2 .= " where empjustificativainstrumentoprevio.e176_sequencial = $e176_sequencial "; 
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
