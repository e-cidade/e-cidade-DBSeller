<?php

class cl_pagordemoutrosdados
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
    public $e172_codigo = 0;
    public $e172_pagordem = 0;
    public $e172_dados = null;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 e172_codigo = int8 = codigo dos outros dados da ord pagamento
                 e172_pagordem = int8 = codigo da ordem de pagamento
                 e172_dados = text = outros dados da ordem de pagamento
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("pagordemoutrosdados");
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
       $this->e172_codigo = ($this->e172_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["e172_codigo"]:$this->e172_codigo);
       $this->e172_pagordem = ($this->e172_pagordem == ""?@$GLOBALS["HTTP_POST_VARS"]["e172_pagordem"]:$this->e172_pagordem);
       $this->e172_dados = ($this->e172_dados == ""?@$GLOBALS["HTTP_POST_VARS"]["e172_dados"]:$this->e172_dados);
     }else{
       $this->e172_codigo = ($this->e172_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["e172_codigo"]:$this->e172_codigo);
     }
   }

    public function incluir($e172_codigo=null)
    {
      $this->atualizacampos();
     if($this->e172_pagordem == null ){
       $this->erro_sql = " Campo codigo da ordem de pagamento não informado.";
       $this->erro_campo = "e172_pagordem";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($e172_codigo == "" || $e172_codigo == null ){
       $result = db_query("select nextval('pagordemoutrosdados_e172_codigo_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: pagordemoutrosdados_e172_codigo_seq do campo: e172_codigo";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->e172_codigo = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from pagordemoutrosdados_e172_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $e172_codigo)){
         $this->erro_sql = " Campo e172_codigo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->e172_codigo = $e172_codigo;
       }
     }
     if(($this->e172_codigo == null) || ($this->e172_codigo == "") ){
       $this->erro_sql = " Campo e172_codigo não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into pagordemoutrosdados(
                                       e172_codigo
                                      ,e172_pagordem
                                      ,e172_dados
                       )
                values (
                                $this->e172_codigo
                               ,$this->e172_pagordem
                               ,'$this->e172_dados'
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Outros dados relacionados a pagordem ($this->e172_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Outros dados relacionados a pagordem já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Outros dados relacionados a pagordem ($this->e172_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->e172_codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->e172_codigo  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1014247,'$this->e172_codigo','I')");
         $resac = db_query("insert into db_acount values($acount,1010950,1014247,'','".AddSlashes(pg_result($resaco,0,'e172_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010950,1014248,'','".AddSlashes(pg_result($resaco,0,'e172_pagordem'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010950,1014249,'','".AddSlashes(pg_result($resaco,0,'e172_dados'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }

    public function alterar($e172_codigo=null,$e172_pagordem)
    {
      $this->atualizacampos();
     $sql = " update pagordemoutrosdados set ";
     $virgula = "";
     if(trim($this->e172_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e172_codigo"])){
       $sql  .= $virgula." e172_codigo = $this->e172_codigo ";
       $virgula = ",";
       if(trim($this->e172_codigo) == null ){
         $this->erro_sql = " Campo codigo dos outros dados da ord pagamento não informado.";
         $this->erro_campo = "e172_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e172_pagordem)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e172_pagordem"])){
       $sql  .= $virgula." e172_pagordem = $this->e172_pagordem ";
       $virgula = ",";
       if(trim($this->e172_pagordem) == null ){
         $this->erro_sql = " Campo codigo da ordem de pagamento não informado.";
         $this->erro_campo = "e172_pagordem";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e172_dados)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e172_dados"])){
       $sql  .= $virgula." e172_dados = '$this->e172_dados' ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($e172_codigo!=null){
       $sql .= " e172_codigo = $this->e172_codigo";
     } else {
         $sql .= " e172_pagordem = $e172_pagordem";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->e172_codigo));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1014247,'$this->e172_codigo','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["e172_codigo"]) || $this->e172_codigo != "")
             $resac = db_query("insert into db_acount values($acount,1010950,1014247,'".AddSlashes(pg_result($resaco,$conresaco,'e172_codigo'))."','$this->e172_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["e172_pagordem"]) || $this->e172_pagordem != "")
             $resac = db_query("insert into db_acount values($acount,1010950,1014248,'".AddSlashes(pg_result($resaco,$conresaco,'e172_pagordem'))."','$this->e172_pagordem',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["e172_dados"]) || $this->e172_dados != "")
             $resac = db_query("insert into db_acount values($acount,1010950,1014249,'".AddSlashes(pg_result($resaco,$conresaco,'e172_dados'))."','$this->e172_dados',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Outros dados relacionados a pagordem não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->e172_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Outros dados relacionados a pagordem não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->e172_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->e172_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($e172_codigo=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($e172_codigo));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1014247,'$e172_codigo','E')");
           $resac  = db_query("insert into db_acount values($acount,1010950,1014247,'','".AddSlashes(pg_result($resaco,$iresaco,'e172_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010950,1014248,'','".AddSlashes(pg_result($resaco,$iresaco,'e172_pagordem'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010950,1014249,'','".AddSlashes(pg_result($resaco,$iresaco,'e172_dados'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from pagordemoutrosdados
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($e172_codigo)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " e172_codigo = $e172_codigo ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Outros dados relacionados a pagordem não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$e172_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Outros dados relacionados a pagordem não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$e172_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$e172_codigo;
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
        $this->erro_sql   = "Record Vazio na Tabela:pagordemoutrosdados";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($e172_codigo = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
     $sql .= "  from pagordemoutrosdados ";
     $sql .= "      inner join pagordem  on  pagordem.e50_codord = pagordemoutrosdados.e172_pagordem";
     $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = pagordem.e50_id_usuario";
     $sql .= "      inner join empempenho  on  empempenho.e60_numemp = pagordem.e50_numemp";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($e172_codigo)) {
         $sql2 .= " where pagordemoutrosdados.e172_codigo = $e172_codigo ";
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

    public function sql_query_file($e172_codigo = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from pagordemoutrosdados ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($e172_codigo)){
         $sql2 .= " where pagordemoutrosdados.e172_codigo = $e172_codigo ";
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

    public function existe($pagordem = null){
        $retorno = false;
        if ($pagordem) {
            $sql = "SELECT * from pagordemoutrosdados where e172_pagordem = {$pagordem}";

            $rsExists = db_query($sql);
            if($rsExists == false){
                Throw new Exception("Erro ao consultar se existe outros dados relacionados ao empenho");
            }
            if (pg_num_rows($rsExists) > 0){
                $retorno = true;
            }
        }
        return $retorno;
    }

}
