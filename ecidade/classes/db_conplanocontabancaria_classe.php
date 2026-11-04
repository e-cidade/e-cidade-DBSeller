<?php

class cl_conplanocontabancaria
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
    public $c56_sequencial = 0;
    public $c56_contabancaria = 0;
    public $c56_codcon = 0;
    public $c56_anousu = 0;
    public $c56_reduz = 0;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 c56_sequencial = int4 = Sequencial
                 c56_contabancaria = int4 = Codigo sequencial da conta bancaria
                 c56_codcon = int4 = Código da Conta
                 c56_anousu = int4 = Exercício
                 c56_reduz = int4 = Reduzido
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("conplanocontabancaria");
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
       $this->c56_sequencial = ($this->c56_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["c56_sequencial"]:$this->c56_sequencial);
       $this->c56_contabancaria = ($this->c56_contabancaria == ""?@$GLOBALS["HTTP_POST_VARS"]["c56_contabancaria"]:$this->c56_contabancaria);
       $this->c56_codcon = ($this->c56_codcon == ""?@$GLOBALS["HTTP_POST_VARS"]["c56_codcon"]:$this->c56_codcon);
       $this->c56_anousu = ($this->c56_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["c56_anousu"]:$this->c56_anousu);
       $this->c56_reduz = ($this->c56_reduz == ""?@$GLOBALS["HTTP_POST_VARS"]["c56_reduz"]:$this->c56_reduz);
     }else{
       $this->c56_sequencial = ($this->c56_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["c56_sequencial"]:$this->c56_sequencial);
     }
   }

    public function incluir($c56_sequencial)
    {
      $this->atualizacampos();
     if($this->c56_contabancaria == null ){
       $this->erro_sql = " Campo Codigo sequencial da conta bancaria não informado.";
       $this->erro_campo = "c56_contabancaria";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c56_codcon == null ){
       $this->erro_sql = " Campo Código da Conta não informado.";
       $this->erro_campo = "c56_codcon";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c56_anousu == null ){
       $this->erro_sql = " Campo Exercício não informado.";
       $this->erro_campo = "c56_anousu";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c56_reduz == null ){
       $this->erro_sql = " Campo Reduzido não informado.";
       $this->erro_campo = "c56_reduz";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($c56_sequencial == "" || $c56_sequencial == null ){
       $result = db_query("select nextval('conplanocontabancaria_c56_sequencial_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: conplanocontabancaria_c56_sequencial_seq do campo: c56_sequencial";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->c56_sequencial = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from conplanocontabancaria_c56_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $c56_sequencial)){
         $this->erro_sql = " Campo c56_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->c56_sequencial = $c56_sequencial;
       }
     }
     if(($this->c56_sequencial == null) || ($this->c56_sequencial == "") ){
       $this->erro_sql = " Campo c56_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into conplanocontabancaria(
                                       c56_sequencial
                                      ,c56_contabancaria
                                      ,c56_codcon
                                      ,c56_anousu
                                      ,c56_reduz
                       )
                values (
                                $this->c56_sequencial
                               ,$this->c56_contabancaria
                               ,$this->c56_codcon
                               ,$this->c56_anousu
                               ,$this->c56_reduz
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Ligação conta bancaria com a contabilidade ($this->c56_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Ligação conta bancaria com a contabilidade já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Ligação conta bancaria com a contabilidade ($this->c56_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->c56_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);

     return true;
   }

    public function alterar($c56_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update conplanocontabancaria set ";
     $virgula = "";
     if(trim($this->c56_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c56_sequencial"])){
       $sql  .= $virgula." c56_sequencial = $this->c56_sequencial ";
       $virgula = ",";
       if(trim($this->c56_sequencial) == null ){
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "c56_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c56_contabancaria)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c56_contabancaria"])){
       $sql  .= $virgula." c56_contabancaria = $this->c56_contabancaria ";
       $virgula = ",";
       if(trim($this->c56_contabancaria) == null ){
         $this->erro_sql = " Campo Codigo sequencial da conta bancaria não informado.";
         $this->erro_campo = "c56_contabancaria";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c56_codcon)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c56_codcon"])){
       $sql  .= $virgula." c56_codcon = $this->c56_codcon ";
       $virgula = ",";
       if(trim($this->c56_codcon) == null ){
         $this->erro_sql = " Campo Código da Conta não informado.";
         $this->erro_campo = "c56_codcon";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c56_anousu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c56_anousu"])){
       $sql  .= $virgula." c56_anousu = $this->c56_anousu ";
       $virgula = ",";
       if(trim($this->c56_anousu) == null ){
         $this->erro_sql = " Campo Exercício não informado.";
         $this->erro_campo = "c56_anousu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c56_reduz)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c56_reduz"])){
       $sql  .= $virgula." c56_reduz = $this->c56_reduz ";
       $virgula = ",";
       if(trim($this->c56_reduz) == null ){
         $this->erro_sql = " Campo Reduzido não informado.";
         $this->erro_campo = "c56_reduz";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($c56_sequencial!=null){
       $sql .= " c56_sequencial = $this->c56_sequencial";
     }

     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Ligação conta bancaria com a contabilidade não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->c56_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Ligação conta bancaria com a contabilidade não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->c56_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->c56_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($c56_sequencial=null, $dbwhere = null)
    {
     $sql = " delete from conplanocontabancaria
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($c56_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " c56_sequencial = $c56_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Ligação conta bancaria com a contabilidade não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$c56_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Ligação conta bancaria com a contabilidade não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$c56_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$c56_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:conplanocontabancaria";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    function sql_query ( $c56_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
        $sql = "select ";
        if($campos != "*" ){
            $campos_sql = explode("#",$campos);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }else{
            $sql .= $campos;
        }
        $sql .= " from conplanocontabancaria ";
        $sql .= "      inner join conplano  on  conplano.c60_codcon = conplanocontabancaria.c56_codcon and  conplano.c60_anousu = conplanocontabancaria.c56_anousu";
        $sql .= "      inner join contabancaria  on  contabancaria.db83_sequencial = conplanocontabancaria.c56_contabancaria";
        $sql .= "      inner join conclass  on  conclass.c51_codcla = conplano.c60_codcla";
        $sql .= "      inner join consistema  on  consistema.c52_codsis = conplano.c60_codsis";
        $sql .= "      inner join bancoagencia  on  bancoagencia.db89_sequencial = contabancaria.db83_bancoagencia";
        $sql2 = "";
        if($dbwhere==""){
            if($c56_sequencial!=null ){
                $sql2 .= " where conplanocontabancaria.c56_sequencial = $c56_sequencial ";
            }
        }else if($dbwhere != ""){
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if($ordem != null ){
            $sql .= " order by ";
            $campos_sql = explode("#",$ordem);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

    public function sql_query_file($c56_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from conplanocontabancaria ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($c56_sequencial)){
         $sql2 .= " where conplanocontabancaria.c56_sequencial = $c56_sequencial ";
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
