<?php

class cl_db_geradorrelatoriotemplate
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
    public $db15_sequencial = 0;
    public $db15_db_relatorio = 0;
    public $db15_documento = 0;
    public $db15_extensao_arquivo = null;
    public $db15_estorage = 0;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 db15_sequencial = int4 = Sequencial
                 db15_db_relatorio = int4 = Código do relatório
                 db15_documento = oid = Documento
                 db15_extensao_arquivo = varchar(10) = Extensão do Arquivo
                 db15_estorage = int8 = Código e-Storage
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("db_geradorrelatoriotemplate");
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
       $this->db15_sequencial = ($this->db15_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["db15_sequencial"]:$this->db15_sequencial);
       $this->db15_db_relatorio = ($this->db15_db_relatorio == ""?@$GLOBALS["HTTP_POST_VARS"]["db15_db_relatorio"]:$this->db15_db_relatorio);
       $this->db15_documento = ($this->db15_documento == ""?@$GLOBALS["HTTP_POST_VARS"]["db15_documento"]:$this->db15_documento);
       $this->db15_extensao_arquivo = ($this->db15_extensao_arquivo == ""?@$GLOBALS["HTTP_POST_VARS"]["db15_extensao_arquivo"]:$this->db15_extensao_arquivo);
       $this->db15_estorage = ($this->db15_estorage == ""?@$GLOBALS["HTTP_POST_VARS"]["db15_estorage"]:$this->db15_estorage);
     }else{
       $this->db15_sequencial = ($this->db15_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["db15_sequencial"]:$this->db15_sequencial);
     }
   }

    public function incluir($db15_sequencial)
    {
      $this->atualizacampos();
     if($this->db15_db_relatorio == null ){
       $this->erro_sql = " Campo Código do relatório não informado.";
       $this->erro_campo = "db15_db_relatorio";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->db15_documento == null ){
       $this->erro_sql = " Campo Documento não informado.";
       $this->erro_campo = "db15_documento";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->db15_extensao_arquivo == null ){
       $this->erro_sql = " Campo Extensão do Arquivo não informado.";
       $this->erro_campo = "db15_extensao_arquivo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->db15_estorage == null ){
       $this->db15_estorage = "null";
     }
     if($db15_sequencial == "" || $db15_sequencial == null ){
       $result = db_query("select nextval('db_geradorrelatoriotemplate_db15_sequencial_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: db_geradorrelatoriotemplate_db15_sequencial_seq do campo: db15_sequencial";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->db15_sequencial = pg_fetch_result($result,0,0);
     }else{
       $result = db_query("select last_value from db_geradorrelatoriotemplate_db15_sequencial_seq");
       if(($result != false) && (pg_fetch_result($result,0,0) < $db15_sequencial)){
         $this->erro_sql = " Campo db15_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->db15_sequencial = $db15_sequencial;
       }
     }
     if(($this->db15_sequencial == null) || ($this->db15_sequencial == "") ){
       $this->erro_sql = " Campo db15_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into db_geradorrelatoriotemplate(
                                       db15_sequencial
                                      ,db15_db_relatorio
                                      ,db15_documento
                                      ,db15_extensao_arquivo
                                      ,db15_estorage
                       )
                values (
                                $this->db15_sequencial
                               ,$this->db15_db_relatorio
                               ,$this->db15_documento
                               ,'$this->db15_extensao_arquivo'
                               ,$this->db15_estorage
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Gerador do template do relatório ($this->db15_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Gerador do template do relatório já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Gerador do template do relatório ($this->db15_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->db15_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);

     return true;
   }

    public function alterar($db15_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update db_geradorrelatoriotemplate set ";
     $virgula = "";
     if(trim($this->db15_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db15_sequencial"])){
       $sql  .= $virgula." db15_sequencial = $this->db15_sequencial ";
       $virgula = ",";
       if(trim($this->db15_sequencial) == null ){
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "db15_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->db15_db_relatorio)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db15_db_relatorio"])){
       $sql  .= $virgula." db15_db_relatorio = $this->db15_db_relatorio ";
       $virgula = ",";
       if(trim($this->db15_db_relatorio) == null ){
         $this->erro_sql = " Campo Código do relatório não informado.";
         $this->erro_campo = "db15_db_relatorio";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->db15_documento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db15_documento"])){
       $sql  .= $virgula." db15_documento = $this->db15_documento ";
       $virgula = ",";
       if(trim($this->db15_documento) == null ){
         $this->erro_sql = " Campo Documento não informado.";
         $this->erro_campo = "db15_documento";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->db15_extensao_arquivo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db15_extensao_arquivo"])){
       $sql  .= $virgula." db15_extensao_arquivo = '$this->db15_extensao_arquivo' ";
       $virgula = ",";
       if(trim($this->db15_extensao_arquivo) == null ){
         $this->erro_sql = " Campo Extensão do Arquivo não informado.";
         $this->erro_campo = "db15_extensao_arquivo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->db15_estorage)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db15_estorage"])){
        if(trim($this->db15_estorage)=="" && isset($GLOBALS["HTTP_POST_VARS"]["db15_estorage"])){
           $this->db15_estorage = "null" ;
        }
       $sql  .= $virgula." db15_estorage = $this->db15_estorage ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($db15_sequencial!=null){
       $sql .= " db15_sequencial = $this->db15_sequencial";
     }

     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Gerador do template do relatório não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->db15_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Gerador do template do relatório não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->db15_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->db15_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($db15_sequencial=null, $dbwhere = null)
    {
     $sql = " delete from db_geradorrelatoriotemplate
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($db15_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " db15_sequencial = $db15_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Gerador do template do relatório não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$db15_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Gerador do template do relatório não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$db15_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$db15_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:db_geradorrelatoriotemplate";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($db15_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
     $sql .= "  from db_geradorrelatoriotemplate ";
     $sql .= "      inner join db_relatorio  on  db_relatorio.db63_sequencial = db_geradorrelatoriotemplate.db15_db_relatorio";
     $sql .= "      inner join db_gruporelatorio  on  db_gruporelatorio.db13_sequencial = db_relatorio.db63_db_gruporelatorio";
     $sql .= "      inner join db_tiporelatorio  on  db_tiporelatorio.db14_sequencial = db_relatorio.db63_db_tiporelatorio";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($db15_sequencial)) {
         $sql2 .= " where db_geradorrelatoriotemplate.db15_sequencial = $db15_sequencial ";
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

    public function sql_query_file($db15_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from db_geradorrelatoriotemplate ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($db15_sequencial)){
         $sql2 .= " where db_geradorrelatoriotemplate.db15_sequencial = $db15_sequencial ";
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
