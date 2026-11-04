<?php

class cl_conplanoconta
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
    public $c63_codcon = 0;
    public $c63_anousu = 0;
    public $c63_banco = null;
    public $c63_agencia = null;
    public $c63_conta = null;
    public $c63_dvconta = null;
    public $c63_dvagencia = null;
    public $c63_identificador = null;
    public $c63_codigooperacao = null;
    public $c63_tipoconta = 0;
    public $c63_reduz = 0;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 c63_codcon = int4 = Reduzido
                 c63_anousu = int4 = Exercício
                 c63_banco = varchar(10) = Banco
                 c63_agencia = varchar(10) = Agência
                 c63_conta = varchar(50) = Conta Bancária
                 c63_dvconta = varchar(2) = DV
                 c63_dvagencia = varchar(2) = DV
                 c63_identificador = char(14) = Identificador (CNPJ )
                 c63_codigooperacao = varchar(4) = Código da Operação
                 c63_tipoconta = int4 = Tipo da Conta
                 c63_reduz = int4 = Reduzido
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("conplanoconta");
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
       $this->c63_codcon = ($this->c63_codcon == ""?@$GLOBALS["HTTP_POST_VARS"]["c63_codcon"]:$this->c63_codcon);
       $this->c63_anousu = ($this->c63_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["c63_anousu"]:$this->c63_anousu);
       $this->c63_banco = ($this->c63_banco == ""?@$GLOBALS["HTTP_POST_VARS"]["c63_banco"]:$this->c63_banco);
       $this->c63_agencia = ($this->c63_agencia == ""?@$GLOBALS["HTTP_POST_VARS"]["c63_agencia"]:$this->c63_agencia);
       $this->c63_conta = ($this->c63_conta == ""?@$GLOBALS["HTTP_POST_VARS"]["c63_conta"]:$this->c63_conta);
       $this->c63_dvconta = ($this->c63_dvconta == ""?@$GLOBALS["HTTP_POST_VARS"]["c63_dvconta"]:$this->c63_dvconta);
       $this->c63_dvagencia = ($this->c63_dvagencia == ""?@$GLOBALS["HTTP_POST_VARS"]["c63_dvagencia"]:$this->c63_dvagencia);
       $this->c63_identificador = ($this->c63_identificador == ""?@$GLOBALS["HTTP_POST_VARS"]["c63_identificador"]:$this->c63_identificador);
       $this->c63_codigooperacao = ($this->c63_codigooperacao == ""?@$GLOBALS["HTTP_POST_VARS"]["c63_codigooperacao"]:$this->c63_codigooperacao);
       $this->c63_tipoconta = ($this->c63_tipoconta == ""?@$GLOBALS["HTTP_POST_VARS"]["c63_tipoconta"]:$this->c63_tipoconta);
       $this->c63_reduz = ($this->c63_reduz == ""?@$GLOBALS["HTTP_POST_VARS"]["c63_reduz"]:$this->c63_reduz);
     }else{
       $this->c63_codcon = ($this->c63_codcon == ""?@$GLOBALS["HTTP_POST_VARS"]["c63_codcon"]:$this->c63_codcon);
       $this->c63_anousu = ($this->c63_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["c63_anousu"]:$this->c63_anousu);
     }
   }

    public function incluir($c63_codcon,$c63_anousu)
    {
      $this->atualizacampos();
     if($this->c63_banco == null ){
       $this->erro_sql = " Campo Banco não informado.";
       $this->erro_campo = "c63_banco";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c63_agencia == null ){
       $this->erro_sql = " Campo Agência não informado.";
       $this->erro_campo = "c63_agencia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c63_conta == null ){
       $this->erro_sql = " Campo Conta Bancária não informado.";
       $this->erro_campo = "c63_conta";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c63_dvconta == null ){
       $this->erro_sql = " Campo DV não informado.";
       $this->erro_campo = "c63_dvconta";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c63_dvagencia == null ){
        $this->c63_dvagencia = "";
     }
     if($this->c63_tipoconta == null ){
       $this->c63_tipoconta = "1";
     }
     if($this->c63_reduz == null ){
       $this->erro_sql = " Campo Reduzido não informado.";
       $this->erro_campo = "c63_reduz";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
       $this->c63_codcon = $c63_codcon;
       $this->c63_anousu = $c63_anousu;
     if(($this->c63_codcon == null) || ($this->c63_codcon == "") ){
       $this->erro_sql = " Campo c63_codcon não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->c63_anousu == null) || ($this->c63_anousu == "") ){
       $this->erro_sql = " Campo c63_anousu não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into conplanoconta(
                                       c63_codcon
                                      ,c63_anousu
                                      ,c63_banco
                                      ,c63_agencia
                                      ,c63_conta
                                      ,c63_dvconta
                                      ,c63_dvagencia
                                      ,c63_identificador
                                      ,c63_codigooperacao
                                      ,c63_tipoconta
                                      ,c63_reduz
                       )
                values (
                                $this->c63_codcon
                               ,$this->c63_anousu
                               ,'$this->c63_banco'
                               ,'$this->c63_agencia'
                               ,'$this->c63_conta'
                               ,'$this->c63_dvconta'
                               ,'$this->c63_dvagencia'
                               ,'$this->c63_identificador'
                               ,'$this->c63_codigooperacao'
                               ,$this->c63_tipoconta
                               ,$this->c63_reduz
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Banco da Conta do Plano ($this->c63_codcon."-".$this->c63_anousu) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Banco da Conta do Plano já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Banco da Conta do Plano ($this->c63_codcon."-".$this->c63_anousu) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->c63_codcon."-".$this->c63_anousu;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);

     return true;
   }

    public function alterar($c63_codcon=null,$c63_anousu=null)
    {
      $this->atualizacampos();
     $sql = " update conplanoconta set ";
     $virgula = "";
     if(trim($this->c63_codcon)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c63_codcon"])){
       $sql  .= $virgula." c63_codcon = $this->c63_codcon ";
       $virgula = ",";
       if(trim($this->c63_codcon) == null ){
         $this->erro_sql = " Campo Reduzido não informado.";
         $this->erro_campo = "c63_codcon";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c63_anousu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c63_anousu"])){
       $sql  .= $virgula." c63_anousu = $this->c63_anousu ";
       $virgula = ",";
       if(trim($this->c63_anousu) == null ){
         $this->erro_sql = " Campo Exercício não informado.";
         $this->erro_campo = "c63_anousu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c63_banco)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c63_banco"])){
       $sql  .= $virgula." c63_banco = '$this->c63_banco' ";
       $virgula = ",";
       if(trim($this->c63_banco) == null ){
         $this->erro_sql = " Campo Banco não informado.";
         $this->erro_campo = "c63_banco";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c63_agencia)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c63_agencia"])){
       $sql  .= $virgula." c63_agencia = '$this->c63_agencia' ";
       $virgula = ",";
       if(trim($this->c63_agencia) == null ){
         $this->erro_sql = " Campo Agência não informado.";
         $this->erro_campo = "c63_agencia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c63_conta)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c63_conta"])){
       $sql  .= $virgula." c63_conta = '$this->c63_conta' ";
       $virgula = ",";
       if(trim($this->c63_conta) == null ){
         $this->erro_sql = " Campo Conta Bancária não informado.";
         $this->erro_campo = "c63_conta";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c63_dvconta)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c63_dvconta"])){
       $sql  .= $virgula." c63_dvconta = '$this->c63_dvconta' ";
       $virgula = ",";
       if(trim($this->c63_dvconta) == null ){
         $this->erro_sql = " Campo DV não informado.";
         $this->erro_campo = "c63_dvconta";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c63_dvagencia)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c63_dvagencia"])){
       $sql  .= $virgula." c63_dvagencia = '$this->c63_dvagencia' ";
       $virgula = ",";
       if(trim($this->c63_dvagencia) == null ){
         $this->erro_sql = " Campo DV não informado.";
         $this->erro_campo = "c63_dvagencia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c63_identificador)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c63_identificador"])){
       $sql  .= $virgula." c63_identificador = '$this->c63_identificador' ";
       $virgula = ",";
     }
     if(trim($this->c63_codigooperacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c63_codigooperacao"])){
       $sql  .= $virgula." c63_codigooperacao = '$this->c63_codigooperacao' ";
       $virgula = ",";
     }
     if(trim($this->c63_tipoconta)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c63_tipoconta"])){
        if(trim($this->c63_tipoconta)=="" && isset($GLOBALS["HTTP_POST_VARS"]["c63_tipoconta"])){
           $this->c63_tipoconta = "0" ;
        }
       $sql  .= $virgula." c63_tipoconta = $this->c63_tipoconta ";
       $virgula = ",";
     }
     if(trim($this->c63_reduz)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c63_reduz"])){
       $sql  .= $virgula." c63_reduz = $this->c63_reduz ";
       $virgula = ",";
       if(trim($this->c63_reduz) == null ){
         $this->erro_sql = " Campo Reduzido não informado.";
         $this->erro_campo = "c63_reduz";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($c63_codcon!=null){
       $sql .= " c63_codcon = $this->c63_codcon";
     }
     if($c63_anousu!=null){
       $sql .= " and  c63_anousu = $this->c63_anousu";
     }

     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Banco da Conta do Plano não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->c63_codcon."-".$this->c63_anousu;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Banco da Conta do Plano não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->c63_codcon."-".$this->c63_anousu;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->c63_codcon."-".$this->c63_anousu;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($c63_codcon=null,$c63_anousu=null, $dbwhere = null)
    {

     $sql = " delete from conplanoconta
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($c63_codcon)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " c63_codcon = $c63_codcon ";
        }
        if (!empty($c63_anousu)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " c63_anousu = $c63_anousu ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Banco da Conta do Plano não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$c63_codcon."-".$c63_anousu;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Banco da Conta do Plano não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$c63_codcon."-".$c63_anousu;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$c63_codcon."-".$c63_anousu;
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
        $this->erro_sql   = "Record Vazio na Tabela:conplanoconta";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    function sql_query ( $c63_codcon=null,$c63_anousu=null,$campos="*",$ordem=null,$dbwhere=""){
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
        $sql .= " from conplanoconta ";
        $sql .= "      inner join conplanoreduz  on conplanoreduz.c61_codcon = conplanoconta.c63_codcon ";
        $sql .= "                               and conplanoreduz.c61_reduz  = conplanoconta.c63_reduz  ";
        $sql .= "                               and conplanoreduz.c61_anousu = conplanoconta.c63_anousu ";
        $sql .= "      inner join conplano  on  conplano.c60_codcon = conplanoreduz.c61_codcon and  conplano.c60_anousu = conplanoreduz.c61_anousu";
        $sql .= "      inner join db_bancos on  db_bancos.db90_codban = conplanoconta.c63_banco";
        $sql .= "      inner join conclass  on  conclass.c51_codcla = conplano.c60_codcla";
        $sql .= "      inner join consistema on  consistema.c52_codsis = conplano.c60_codsis";
        $sql2 = "";
        if($dbwhere==""){
            if($c63_codcon!=null ){
                $sql2 .= " where conplanoconta.c63_codcon = $c63_codcon ";
            }
            if($c63_anousu!=null ){
                if($sql2!=""){
                    $sql2 .= " and ";
                }else{
                    $sql2 .= " where ";
                }
                $sql2 .= " conplanoconta.c63_anousu = $c63_anousu ";
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

    public function sql_query_file($c63_codcon = null,$c63_anousu = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from conplanoconta ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($c63_codcon)){
         $sql2 .= " where conplanoconta.c63_codcon = $c63_codcon ";
       }
       if (!empty($c63_anousu)){
         if ( !empty($sql2) ) {
            $sql2 .= " and ";
         } else {
            $sql2 .= " where ";
         }
         $sql2 .= " conplanoconta.c63_anousu = $c63_anousu ";
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

   function sql_query_razao ( $c63_codcon=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from conplanoconta ";
     $sql .= "      inner join conplano  on  conplano.c60_codcon = conplanoconta.c63_codcon";
     $sql .= "      inner join conclass  on  conclass.c51_codcla = conplano.c60_codcla";
     $sql .= "      inner join consistema  on  consistema.c52_codsis = conplano.c60_codsis";
     $sql .= "      inner join conplanoreduz  on conplanoreduz.c61_codcon = conplanoconta.c63_codcon";
     $sql .= "                               and conplanoreduz.c61_reduz  = conplanoconta.c63_reduz ";
     $sql .= "                               and conplanoreduz.c61_anousu = conplanoconta.c63_anousu ";
     $sql .= "      inner join conlancamval on  conlancamval.c69_credito=conplanoreduz.c61_reduz or conlancamval.c69_debito=conplanoreduz.c61_reduz";
     $sql .= "      inner join conlancam on conlancam.c70_codlan = conlancamval.c69_codlan and conlancam.c70_anousu = conlancamval.c69_anousu  ";
     $sql2 = "";
     if($dbwhere==""){
       if($c63_codcon!=null ){
         $sql2 .= " where conplanoconta.c63_codcon = $c63_codcon ";
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
}
