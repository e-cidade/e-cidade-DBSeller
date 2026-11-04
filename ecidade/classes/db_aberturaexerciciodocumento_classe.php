<?php

class cl_aberturaexerciciodocumento
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
    public $c149_id = 0;
    public $c149_anousu = 0;
    public $c149_documento = 0;
    public $c149_instit = 0;
    public $c149_usuario = 0;
    public $created_at = null;
    public $updated_at = null;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 c149_id = int4 = Código
                 c149_anousu = int4 = Exercício
                 c149_documento = int4 = Documento
                 c149_instit = int4 = Instituição
                 c149_usuario = int4 = Usuário
                 created_at = varchar(30) = Criado em
                 updated_at = varchar(10) = Alterado em
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("aberturaexerciciodocumento");
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
       $this->c149_id = ($this->c149_id == ""?@$GLOBALS["HTTP_POST_VARS"]["c149_id"]:$this->c149_id);
       $this->c149_anousu = ($this->c149_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["c149_anousu"]:$this->c149_anousu);
       $this->c149_documento = ($this->c149_documento == ""?@$GLOBALS["HTTP_POST_VARS"]["c149_documento"]:$this->c149_documento);
       $this->c149_instit = ($this->c149_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["c149_instit"]:$this->c149_instit);
       $this->c149_usuario = ($this->c149_usuario == ""?@$GLOBALS["HTTP_POST_VARS"]["c149_usuario"]:$this->c149_usuario);
       $this->created_at = ($this->created_at == ""?@$GLOBALS["HTTP_POST_VARS"]["created_at"]:$this->created_at);
       $this->updated_at = ($this->updated_at == ""?@$GLOBALS["HTTP_POST_VARS"]["updated_at"]:$this->updated_at);
     }else{
       $this->c149_id = ($this->c149_id == ""?@$GLOBALS["HTTP_POST_VARS"]["c149_id"]:$this->c149_id);
     }
   }

    public function incluir($c149_id)
    {
      $this->atualizacampos();
     if($this->c149_anousu == null ){
       $this->erro_sql = " Campo Exercício não informado.";
       $this->erro_campo = "c149_anousu";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c149_documento == null ){
       $this->erro_sql = " Campo Documento não informado.";
       $this->erro_campo = "c149_documento";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c149_instit == null ){
       $this->erro_sql = " Campo Instituição não informado.";
       $this->erro_campo = "c149_instit";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c149_usuario == null ){
       $this->erro_sql = " Campo Usuário não informado.";
       $this->erro_campo = "c149_usuario";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->created_at == null ){
       $this->erro_sql = " Campo Criado em não informado.";
       $this->erro_campo = "created_at";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->updated_at == null ){
       $this->erro_sql = " Campo Alterado em não informado.";
       $this->erro_campo = "updated_at";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($c149_id == "" || $c149_id == null ){
       $result = db_query("select nextval('aberturaexerciciodocumento_c149_id_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: aberturaexerciciodocumento_c149_id_seq do campo: c149_id";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->c149_id = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from aberturaexerciciodocumento_c149_id_seq");
       if(($result != false) && (pg_result($result,0,0) < $c149_id)){
         $this->erro_sql = " Campo c149_id maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->c149_id = $c149_id;
       }
     }
     if(($this->c149_id == null) || ($this->c149_id == "") ){
       $this->erro_sql = " Campo c149_id não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into aberturaexerciciodocumento(
                                       c149_id
                                      ,c149_anousu
                                      ,c149_documento
                                      ,c149_instit
                                      ,c149_usuario
                                      ,created_at
                                      ,updated_at
                       )
                values (
                                $this->c149_id
                               ,$this->c149_anousu
                               ,$this->c149_documento
                               ,$this->c149_instit
                               ,$this->c149_usuario
                               ,'$this->created_at'
                               ,'$this->updated_at'
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Abertura de Exercício por documento ($this->c149_id) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Abertura de Exercício por documento já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Abertura de Exercício por documento ($this->c149_id) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->c149_id;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->c149_id  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1015537,'$this->c149_id','I')");
         $resac = db_query("insert into db_acount values($acount,1011161,1015537,'','".AddSlashes(pg_result($resaco,0,'c149_id'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011161,1015538,'','".AddSlashes(pg_result($resaco,0,'c149_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011161,1015539,'','".AddSlashes(pg_result($resaco,0,'c149_documento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011161,1015540,'','".AddSlashes(pg_result($resaco,0,'c149_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011161,1015541,'','".AddSlashes(pg_result($resaco,0,'c149_usuario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011161,1012583,'','".AddSlashes(pg_result($resaco,0,'created_at'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011161,1012584,'','".AddSlashes(pg_result($resaco,0,'updated_at'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }

    public function alterar($c149_id=null)
    {
      $this->atualizacampos();
     $sql = " update aberturaexerciciodocumento set ";
     $virgula = "";
     if(trim($this->c149_id)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c149_id"])){
       $sql  .= $virgula." c149_id = $this->c149_id ";
       $virgula = ",";
       if(trim($this->c149_id) == null ){
         $this->erro_sql = " Campo Código não informado.";
         $this->erro_campo = "c149_id";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c149_anousu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c149_anousu"])){
       $sql  .= $virgula." c149_anousu = $this->c149_anousu ";
       $virgula = ",";
       if(trim($this->c149_anousu) == null ){
         $this->erro_sql = " Campo Exercício não informado.";
         $this->erro_campo = "c149_anousu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c149_documento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c149_documento"])){
       $sql  .= $virgula." c149_documento = $this->c149_documento ";
       $virgula = ",";
       if(trim($this->c149_documento) == null ){
         $this->erro_sql = " Campo Documento não informado.";
         $this->erro_campo = "c149_documento";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c149_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c149_instit"])){
       $sql  .= $virgula." c149_instit = $this->c149_instit ";
       $virgula = ",";
       if(trim($this->c149_instit) == null ){
         $this->erro_sql = " Campo Instituição não informado.";
         $this->erro_campo = "c149_instit";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c149_usuario)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c149_usuario"])){
       $sql  .= $virgula." c149_usuario = $this->c149_usuario ";
       $virgula = ",";
       if(trim($this->c149_usuario) == null ){
         $this->erro_sql = " Campo Usuário não informado.";
         $this->erro_campo = "c149_usuario";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->created_at)!="" || isset($GLOBALS["HTTP_POST_VARS"]["created_at"])){
       $sql  .= $virgula." created_at = '$this->created_at' ";
       $virgula = ",";
       if(trim($this->created_at) == null ){
         $this->erro_sql = " Campo Criado em não informado.";
         $this->erro_campo = "created_at";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->updated_at)!="" || isset($GLOBALS["HTTP_POST_VARS"]["updated_at"])){
       $sql  .= $virgula." updated_at = '$this->updated_at' ";
       $virgula = ",";
       if(trim($this->updated_at) == null ){
         $this->erro_sql = " Campo Alterado em não informado.";
         $this->erro_campo = "updated_at";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($c149_id!=null){
       $sql .= " c149_id = $this->c149_id";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->c149_id));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1015537,'$this->c149_id','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c149_id"]) || $this->c149_id != "")
             $resac = db_query("insert into db_acount values($acount,1011161,1015537,'".AddSlashes(pg_result($resaco,$conresaco,'c149_id'))."','$this->c149_id',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c149_anousu"]) || $this->c149_anousu != "")
             $resac = db_query("insert into db_acount values($acount,1011161,1015538,'".AddSlashes(pg_result($resaco,$conresaco,'c149_anousu'))."','$this->c149_anousu',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c149_documento"]) || $this->c149_documento != "")
             $resac = db_query("insert into db_acount values($acount,1011161,1015539,'".AddSlashes(pg_result($resaco,$conresaco,'c149_documento'))."','$this->c149_documento',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c149_instit"]) || $this->c149_instit != "")
             $resac = db_query("insert into db_acount values($acount,1011161,1015540,'".AddSlashes(pg_result($resaco,$conresaco,'c149_instit'))."','$this->c149_instit',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c149_usuario"]) || $this->c149_usuario != "")
             $resac = db_query("insert into db_acount values($acount,1011161,1015541,'".AddSlashes(pg_result($resaco,$conresaco,'c149_usuario'))."','$this->c149_usuario',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["created_at"]) || $this->created_at != "")
             $resac = db_query("insert into db_acount values($acount,1011161,1012583,'".AddSlashes(pg_result($resaco,$conresaco,'created_at'))."','$this->created_at',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["updated_at"]) || $this->updated_at != "")
             $resac = db_query("insert into db_acount values($acount,1011161,1012584,'".AddSlashes(pg_result($resaco,$conresaco,'updated_at'))."','$this->updated_at',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Abertura de Exercício por documento não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->c149_id;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Abertura de Exercício por documento não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->c149_id;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->c149_id;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($c149_id=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($c149_id));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1015537,'$c149_id','E')");
           $resac  = db_query("insert into db_acount values($acount,1011161,1015537,'','".AddSlashes(pg_result($resaco,$iresaco,'c149_id'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011161,1015538,'','".AddSlashes(pg_result($resaco,$iresaco,'c149_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011161,1015539,'','".AddSlashes(pg_result($resaco,$iresaco,'c149_documento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011161,1015540,'','".AddSlashes(pg_result($resaco,$iresaco,'c149_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011161,1015541,'','".AddSlashes(pg_result($resaco,$iresaco,'c149_usuario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011161,1012583,'','".AddSlashes(pg_result($resaco,$iresaco,'created_at'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011161,1012584,'','".AddSlashes(pg_result($resaco,$iresaco,'updated_at'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from aberturaexerciciodocumento
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($c149_id)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " c149_id = $c149_id ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Abertura de Exercício por documento não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$c149_id;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Abertura de Exercício por documento não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$c149_id;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$c149_id;
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
        $this->erro_sql   = "Record Vazio na Tabela:aberturaexerciciodocumento";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($c149_id = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
     $sql .= "  from aberturaexerciciodocumento ";
     $sql .= "      inner join db_config  on  db_config.codigo = aberturaexerciciodocumento.c149_instit";
     $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = aberturaexerciciodocumento.c149_usuario";
     $sql .= "      inner join conhistdoc  on  conhistdoc.c53_coddoc = aberturaexerciciodocumento.c149_documento";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
     $sql .= "      left  join db_depart  on  db_depart.coddepto = db_config.db21_departamento";
     $sql .= "      inner join db_tipoinstit  on  db_tipoinstit.db21_codtipo = db_config.db21_tipoinstit";
     $sql .= "      inner join conhistdoctipo  on  conhistdoctipo.c57_sequencial = conhistdoc.c53_conhistdoctipo";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($c149_id)) {
         $sql2 .= " where aberturaexerciciodocumento.c149_id = $c149_id ";
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

    public function sql_query_file($c149_id = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from aberturaexerciciodocumento ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($c149_id)){
         $sql2 .= " where aberturaexerciciodocumento.c149_id = $c149_id ";
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
