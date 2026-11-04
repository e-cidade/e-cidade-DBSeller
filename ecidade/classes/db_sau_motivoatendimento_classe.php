<?php

class cl_sau_motivoatendimento
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
    public $s144_i_codigo = 0;
    public $s144_c_descr = null;
    public $s144_situacao = 'f';
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 s144_i_codigo = int4 = Código
                 s144_c_descr = char(30) = Descrição
                 s144_situacao = bool = Ativo
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("sau_motivoatendimento");
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
       $this->s144_i_codigo = ($this->s144_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["s144_i_codigo"]:$this->s144_i_codigo);
       $this->s144_c_descr = ($this->s144_c_descr == ""?@$GLOBALS["HTTP_POST_VARS"]["s144_c_descr"]:$this->s144_c_descr);
       $this->s144_situacao = ($this->s144_situacao == "f"?@$GLOBALS["HTTP_POST_VARS"]["s144_situacao"]:$this->s144_situacao);
     }else{
       $this->s144_i_codigo = ($this->s144_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["s144_i_codigo"]:$this->s144_i_codigo);
     }
   }

    public function incluir($s144_i_codigo)
    {
      $this->atualizacampos();
     if($this->s144_c_descr == null ){
       $this->erro_sql = " Campo Descrição não informado.";
       $this->erro_campo = "s144_c_descr";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->s144_situacao == null ){
       $this->erro_sql = " Campo Ativo não informado.";
       $this->erro_campo = "s144_situacao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($s144_i_codigo == "" || $s144_i_codigo == null ){
       $result = db_query("select nextval('sau_motivoatendimento_s144_i_codigo_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: sau_motivoatendimento_s144_i_codigo_seq do campo: s144_i_codigo";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->s144_i_codigo = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from sau_motivoatendimento_s144_i_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $s144_i_codigo)){
         $this->erro_sql = " Campo s144_i_codigo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->s144_i_codigo = $s144_i_codigo;
       }
     }
     if(($this->s144_i_codigo == null) || ($this->s144_i_codigo == "") ){
       $this->erro_sql = " Campo s144_i_codigo não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into sau_motivoatendimento(
                                       s144_i_codigo
                                      ,s144_c_descr
                                      ,s144_situacao
                       )
                values (
                                $this->s144_i_codigo
                               ,'$this->s144_c_descr'
                               ,'$this->s144_situacao'
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "sau_motivoatendimento ($this->s144_i_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "sau_motivoatendimento já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "sau_motivoatendimento ($this->s144_i_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->s144_i_codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->s144_i_codigo  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,15505,'$this->s144_i_codigo','I')");
         $resac = db_query("insert into db_acount values($acount,2719,15505,'','".AddSlashes(pg_result($resaco,0,'s144_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2719,15506,'','".AddSlashes(pg_result($resaco,0,'s144_c_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2719,1015036,'','".AddSlashes(pg_result($resaco,0,'s144_situacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }

    public function alterar($s144_i_codigo=null)
    {
      $this->atualizacampos();
     $sql = " update sau_motivoatendimento set ";
     $virgula = "";
     if(trim($this->s144_i_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["s144_i_codigo"])){
       $sql  .= $virgula." s144_i_codigo = $this->s144_i_codigo ";
       $virgula = ",";
       if(trim($this->s144_i_codigo) == null ){
         $this->erro_sql = " Campo Código não informado.";
         $this->erro_campo = "s144_i_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->s144_c_descr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["s144_c_descr"])){
       $sql  .= $virgula." s144_c_descr = '$this->s144_c_descr' ";
       $virgula = ",";
       if(trim($this->s144_c_descr) == null ){
         $this->erro_sql = " Campo Descrição não informado.";
         $this->erro_campo = "s144_c_descr";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->s144_situacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["s144_situacao"])){
       $sql  .= $virgula." s144_situacao = '$this->s144_situacao' ";
       $virgula = ",";
       if(trim($this->s144_situacao) == null ){
         $this->erro_sql = " Campo Ativo não informado.";
         $this->erro_campo = "s144_situacao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($s144_i_codigo!=null){
       $sql .= " s144_i_codigo = $this->s144_i_codigo";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->s144_i_codigo));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,15505,'$this->s144_i_codigo','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["s144_i_codigo"]) || $this->s144_i_codigo != "")
             $resac = db_query("insert into db_acount values($acount,2719,15505,'".AddSlashes(pg_result($resaco,$conresaco,'s144_i_codigo'))."','$this->s144_i_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["s144_c_descr"]) || $this->s144_c_descr != "")
             $resac = db_query("insert into db_acount values($acount,2719,15506,'".AddSlashes(pg_result($resaco,$conresaco,'s144_c_descr'))."','$this->s144_c_descr',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["s144_situacao"]) || $this->s144_situacao != "")
             $resac = db_query("insert into db_acount values($acount,2719,1015036,'".AddSlashes(pg_result($resaco,$conresaco,'s144_situacao'))."','$this->s144_situacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "sau_motivoatendimento não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->s144_i_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "sau_motivoatendimento não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->s144_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->s144_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($s144_i_codigo=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($s144_i_codigo));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,15505,'$s144_i_codigo','E')");
           $resac  = db_query("insert into db_acount values($acount,2719,15505,'','".AddSlashes(pg_result($resaco,$iresaco,'s144_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2719,15506,'','".AddSlashes(pg_result($resaco,$iresaco,'s144_c_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2719,1015036,'','".AddSlashes(pg_result($resaco,$iresaco,'s144_situacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from sau_motivoatendimento
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($s144_i_codigo)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " s144_i_codigo = $s144_i_codigo ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "sau_motivoatendimento não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$s144_i_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "sau_motivoatendimento não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$s144_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$s144_i_codigo;
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
        $this->erro_sql   = "Record Vazio na Tabela:sau_motivoatendimento";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($s144_i_codigo = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
     $sql .= "  from sau_motivoatendimento ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($s144_i_codigo)) {
         $sql2 .= " where sau_motivoatendimento.s144_i_codigo = $s144_i_codigo ";
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

    public function sql_query_file($s144_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from sau_motivoatendimento ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($s144_i_codigo)){
         $sql2 .= " where sau_motivoatendimento.s144_i_codigo = $s144_i_codigo ";
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
