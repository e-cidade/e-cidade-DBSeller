<?php

class cl_servidoroperadorasaudedependente
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
    public $rh223_sequencial = 0;
    public $rh223_tipo = null;
    public $rh223_valor = 0;
    public $rh223_dependente = 0;
    public $rh223_servidoroperadorasaude = 0;
    public $rh223_rubrica = null;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 rh223_sequencial = int4 = Sequencial
                 rh223_tipo = varchar(2) = Tipo
                 rh223_valor = numeric = Valor
                 rh223_dependente = int4 = Dependente
                 rh223_servidoroperadorasaude = int4 = Operadora de Saúde do Servidor
                 rh223_rubrica = varchar(4) = Rubrica Dependente
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("servidoroperadorasaudedependente");
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
       $this->rh223_sequencial = ($this->rh223_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["rh223_sequencial"]:$this->rh223_sequencial);
       $this->rh223_tipo = ($this->rh223_tipo == ""?@$GLOBALS["HTTP_POST_VARS"]["rh223_tipo"]:$this->rh223_tipo);
       $this->rh223_valor = ($this->rh223_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["rh223_valor"]:$this->rh223_valor);
       $this->rh223_dependente = ($this->rh223_dependente == ""?@$GLOBALS["HTTP_POST_VARS"]["rh223_dependente"]:$this->rh223_dependente);
       $this->rh223_servidoroperadorasaude = ($this->rh223_servidoroperadorasaude == ""?@$GLOBALS["HTTP_POST_VARS"]["rh223_servidoroperadorasaude"]:$this->rh223_servidoroperadorasaude);
       $this->rh223_rubrica = ($this->rh223_rubrica == ""?@$GLOBALS["HTTP_POST_VARS"]["rh223_rubrica"]:$this->rh223_rubrica);
     }else{
       $this->rh223_sequencial = ($this->rh223_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["rh223_sequencial"]:$this->rh223_sequencial);
     }
   }

    public function incluir($rh223_sequencial)
    {
      $this->atualizacampos();
      if ($this->rh223_valor == null) {
            $this->rh223_valor = 0;
      }
      if($this->rh223_tipo == null ){
       $this->erro_sql = " Campo Tipo não informado.";
       $this->erro_campo = "rh223_tipo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh223_valor === null ){
       $this->erro_sql = " Campo Valor não informado.";
       $this->erro_campo = "rh223_valor";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh223_dependente == null ){
       $this->erro_sql = " Campo Dependente não informado.";
       $this->erro_campo = "rh223_dependente";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh223_servidoroperadorasaude == null ){
       $this->erro_sql = " Campo Operadora de Saúde do Servidor não informado.";
       $this->erro_campo = "rh223_servidoroperadorasaude";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh223_rubrica == null ){
       $this->erro_sql = " Campo Rubrica Dependente não informado.";
       $this->erro_campo = "rh223_rubrica";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
        if ($rh223_sequencial == "" || $rh223_sequencial == null) {
            $result = db_query("select nextval('servidoroperadorasaudedependente_rh223_sequencial_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: servidoroperadorasaudedependente_rh223_sequencial_seq do campo: rh223_sequencial";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->rh223_sequencial = pg_result($result, 0, 0);
        } else {
            $result = db_query("SELECT last_value FROM servidoroperadorasaudedependente_rh223_sequencial_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $rh223_sequencial)) {
                $this->erro_sql = " Campo rh223_sequencial maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->rh223_sequencial = $rh223_sequencial;
            }
        }
     if(($this->rh223_sequencial == null) || ($this->rh223_sequencial == "") ){
       $this->erro_sql = " Campo rh223_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into servidoroperadorasaudedependente(
                                       rh223_sequencial
                                      ,rh223_tipo
                                      ,rh223_valor
                                      ,rh223_dependente
                                      ,rh223_servidoroperadorasaude
                                      ,rh223_rubrica
                       )
                values (
                                $this->rh223_sequencial
                               ,'$this->rh223_tipo'
                               ,$this->rh223_valor
                               ,$this->rh223_dependente
                               ,$this->rh223_servidoroperadorasaude
                               ,'$this->rh223_rubrica'
                      )";
     $result = db_query($sql);

     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Dependente do Servidor no Plano de Saúde ($this->rh223_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Dependente do Servidor no Plano de Saúde já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Dependente do Servidor no Plano de Saúde ($this->rh223_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->rh223_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     return true;
   }

    public function alterar($rh223_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update servidoroperadorasaudedependente set ";
     $virgula = "";
     if(trim($this->rh223_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh223_sequencial"])){
       $sql  .= $virgula." rh223_sequencial = $this->rh223_sequencial ";
       $virgula = ",";
       if(trim($this->rh223_sequencial) == null ){
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "rh223_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh223_tipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh223_tipo"])){
       $sql  .= $virgula." rh223_tipo = '$this->rh223_tipo' ";
       $virgula = ",";
       if(trim($this->rh223_tipo) == null ){
         $this->erro_sql = " Campo Tipo não informado.";
         $this->erro_campo = "rh223_tipo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh223_valor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh223_valor"])){
       $sql  .= $virgula." rh223_valor = $this->rh223_valor ";
       $virgula = ",";
       if(trim($this->rh223_valor) === null ){
         $this->erro_sql = " Campo Valor não informado.";
         $this->erro_campo = "rh223_valor";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh223_dependente)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh223_dependente"])){
       $sql  .= $virgula." rh223_dependente = $this->rh223_dependente ";
       $virgula = ",";
       if(trim($this->rh223_dependente) == null ){
         $this->erro_sql = " Campo Dependente não informado.";
         $this->erro_campo = "rh223_dependente";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh223_servidoroperadorasaude)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh223_servidoroperadorasaude"])){
       $sql  .= $virgula." rh223_servidoroperadorasaude = $this->rh223_servidoroperadorasaude ";
       $virgula = ",";
       if(trim($this->rh223_servidoroperadorasaude) == null ){
         $this->erro_sql = " Campo Operadora de Saúde do Servidor não informado.";
         $this->erro_campo = "rh223_servidoroperadorasaude";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh223_rubrica)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh223_rubrica"])){
       $sql  .= $virgula." rh223_rubrica = '$this->rh223_rubrica' ";
       $virgula = ",";
       if(trim($this->rh223_rubrica) == null ){
         $this->erro_sql = " Campo Rubrica Dependente não informado.";
         $this->erro_campo = "rh223_rubrica";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($rh223_sequencial!=null){
       $sql .= " rh223_sequencial = $this->rh223_sequencial";
     }
     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Dependente do Servidor no Plano de Saúde não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->rh223_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Dependente do Servidor no Plano de Saúde não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->rh223_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->rh223_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($rh223_sequencial=null, $dbwhere = null)
    {
     $sql = " delete from servidoroperadorasaudedependente
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($rh223_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " rh223_sequencial = $rh223_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Dependente do Servidor no Plano de Saúde não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$rh223_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Dependente do Servidor no Plano de Saúde não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$rh223_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$rh223_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:servidoroperadorasaudedependente";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($rh223_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
     $sql .= "  from servidoroperadorasaudedependente ";
     $sql .= "      inner join rhdepend  on  rhdepend.rh31_codigo = servidoroperadorasaudedependente.rh223_dependente";
     $sql .= "      inner join servidoroperadorasaude  on  servidoroperadorasaude.rh222_sequencial = servidoroperadorasaudedependente.rh223_servidoroperadorasaude";
     $sql .= "      inner join rhpessoal  on  rhpessoal.rh01_regist = rhdepend.rh31_regist";
     $sql .= "      inner join rhpessoal  as a on   a.rh01_regist = servidoroperadorasaude.rh222_servidor";
     $sql .= "      inner join rhrubricas  on  rhrubricas.rh27_rubric = servidoroperadorasaude.rh222_rubrica and  rhrubricas.rh27_instit = servidoroperadorasaude.rh222_instituicao";
     $sql .= "      inner join operadorasaude  on  operadorasaude.rh221_sequencial = servidoroperadorasaude.rh222_operadorasaude";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($rh223_sequencial)) {
         $sql2 .= " where servidoroperadorasaudedependente.rh223_sequencial = $rh223_sequencial ";
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

    public function sql_query_file($rh223_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from servidoroperadorasaudedependente ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($rh223_sequencial)){
         $sql2 .= " where servidoroperadorasaudedependente.rh223_sequencial = $rh223_sequencial ";
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
