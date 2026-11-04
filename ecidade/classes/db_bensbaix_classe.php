<?php

class cl_bensbaix
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
    public $t55_codbem = 0;
    public $t55_baixa_dia = null;
    public $t55_baixa_mes = null;
    public $t55_baixa_ano = null;
    public $t55_baixa = null;
    public $t55_motivo = 0;
    public $t55_obs = null;
    public $t55_documento = 0;
    public $t55_estorage = null;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 t55_codbem = int8 = Código do bem
                 t55_baixa = date = Data da baixa
                 t55_motivo = int8 = Motivo da baixa
                 t55_obs = text = Dados Adicionais da Baixa
                 t55_documento = oid = Documento
                 t55_estorage = bool = Estorage
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("bensbaix");
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
       $this->t55_codbem = ($this->t55_codbem == ""?@$GLOBALS["HTTP_POST_VARS"]["t55_codbem"]:$this->t55_codbem);
       if($this->t55_baixa == ""){
         $this->t55_baixa_dia = ($this->t55_baixa_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["t55_baixa_dia"]:$this->t55_baixa_dia);
         $this->t55_baixa_mes = ($this->t55_baixa_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["t55_baixa_mes"]:$this->t55_baixa_mes);
         $this->t55_baixa_ano = ($this->t55_baixa_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["t55_baixa_ano"]:$this->t55_baixa_ano);
         if($this->t55_baixa_dia != ""){
            $this->t55_baixa = $this->t55_baixa_ano."-".$this->t55_baixa_mes."-".$this->t55_baixa_dia;
         }
       }
       $this->t55_motivo = ($this->t55_motivo == ""?@$GLOBALS["HTTP_POST_VARS"]["t55_motivo"]:$this->t55_motivo);
       $this->t55_obs = ($this->t55_obs == ""?@$GLOBALS["HTTP_POST_VARS"]["t55_obs"]:$this->t55_obs);
       $this->t55_documento = ($this->t55_documento == ""?@$GLOBALS["HTTP_POST_VARS"]["t55_documento"]:$this->t55_documento);
     }else{
       $this->t55_codbem = ($this->t55_codbem == ""?@$GLOBALS["HTTP_POST_VARS"]["t55_codbem"]:$this->t55_codbem);
     }
   }

    public function incluir($t55_codbem)
    {
      $this->atualizacampos();
     if($this->t55_baixa == null ){
       $this->erro_sql = " Campo Data da baixa não informado.";
       $this->erro_campo = "t55_baixa_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->t55_motivo == null ){
       $this->erro_sql = " Campo Motivo da baixa não informado.";
       $this->erro_campo = "t55_motivo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->t55_estorage === null ){
       $this->erro_sql = " Campo Estorage não informado.";
       $this->erro_campo = "t55_estorage";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
       $this->t55_codbem = $t55_codbem;
     if(($this->t55_codbem == null) || ($this->t55_codbem == "") ){
       $this->erro_sql = " Campo t55_codbem não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     if (empty($this->t55_documento)) {
        $this->t55_documento = 'null';
     }
     $sql = "insert into bensbaix(
                                       t55_codbem
                                      ,t55_baixa
                                      ,t55_motivo
                                      ,t55_obs
                                      ,t55_documento
                                      ,t55_estorage
                       )
                values (
                                $this->t55_codbem
                               ,".($this->t55_baixa == "null" || $this->t55_baixa == ""?"null":"'".$this->t55_baixa."'")."
                               ,$this->t55_motivo
                               ,'$this->t55_obs'
                               ,$this->t55_documento
                               ,'$this->t55_estorage'
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Baixa de bens ($this->t55_codbem) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Baixa de bens já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Baixa de bens ($this->t55_codbem) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->t55_codbem;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->t55_codbem  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,5784,'$this->t55_codbem','I')");
         $resac = db_query("insert into db_acount values($acount,917,5784,'','".AddSlashes(pg_result($resaco,0,'t55_codbem'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,917,5785,'','".AddSlashes(pg_result($resaco,0,'t55_baixa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,917,5786,'','".AddSlashes(pg_result($resaco,0,'t55_motivo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,917,9579,'','".AddSlashes(pg_result($resaco,0,'t55_obs'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,917,1011319,'','".AddSlashes(pg_result($resaco,0,'t55_documento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,917,1015524,'','".AddSlashes(pg_result($resaco,0,'t55_estorage'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }

    public function alterar($t55_codbem=null)
    {
      $this->atualizacampos();
     $sql = " update bensbaix set ";
     $virgula = "";
     if(trim($this->t55_codbem)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t55_codbem"])){
       $sql  .= $virgula." t55_codbem = $this->t55_codbem ";
       $virgula = ",";
       if(trim($this->t55_codbem) == null ){
         $this->erro_sql = " Campo Código do bem não informado.";
         $this->erro_campo = "t55_codbem";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->t55_baixa)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t55_baixa_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["t55_baixa_dia"] !="") ){
       $sql  .= $virgula." t55_baixa = '$this->t55_baixa' ";
       $virgula = ",";
       if(trim($this->t55_baixa) == null ){
         $this->erro_sql = " Campo Data da baixa não informado.";
         $this->erro_campo = "t55_baixa_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["t55_baixa_dia"])){
         $sql  .= $virgula." t55_baixa = null ";
         $virgula = ",";
         if(trim($this->t55_baixa) == null ){
           $this->erro_sql = " Campo Data da baixa não informado.";
           $this->erro_campo = "t55_baixa_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->t55_motivo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t55_motivo"])){
       $sql  .= $virgula." t55_motivo = $this->t55_motivo ";
       $virgula = ",";
       if(trim($this->t55_motivo) == null ){
         $this->erro_sql = " Campo Motivo da baixa não informado.";
         $this->erro_campo = "t55_motivo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->t55_obs)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t55_obs"])){
       $sql  .= $virgula." t55_obs = '$this->t55_obs' ";
       $virgula = ",";
     }
     if(trim($this->t55_documento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t55_documento"])){
       $sql  .= $virgula." t55_documento = $this->t55_documento ";
       $virgula = ",";
     }
     if(trim($this->t55_estorage)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t55_estorage"])){
       $sql  .= $virgula." t55_estorage = '$this->t55_estorage' ";
       $virgula = ",";
       if(trim($this->t55_estorage) == null ){
         $this->erro_sql = " Campo Estorage não informado.";
         $this->erro_campo = "t55_estorage";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($t55_codbem!=null){
       $sql .= " t55_codbem = $this->t55_codbem";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->t55_codbem));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,5784,'$this->t55_codbem','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["t55_codbem"]) || $this->t55_codbem != "")
             $resac = db_query("insert into db_acount values($acount,917,5784,'".AddSlashes(pg_result($resaco,$conresaco,'t55_codbem'))."','$this->t55_codbem',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["t55_baixa"]) || $this->t55_baixa != "")
             $resac = db_query("insert into db_acount values($acount,917,5785,'".AddSlashes(pg_result($resaco,$conresaco,'t55_baixa'))."','$this->t55_baixa',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["t55_motivo"]) || $this->t55_motivo != "")
             $resac = db_query("insert into db_acount values($acount,917,5786,'".AddSlashes(pg_result($resaco,$conresaco,'t55_motivo'))."','$this->t55_motivo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["t55_obs"]) || $this->t55_obs != "")
             $resac = db_query("insert into db_acount values($acount,917,9579,'".AddSlashes(pg_result($resaco,$conresaco,'t55_obs'))."','$this->t55_obs',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["t55_documento"]) || $this->t55_documento != "")
             $resac = db_query("insert into db_acount values($acount,917,1011319,'".AddSlashes(pg_result($resaco,$conresaco,'t55_documento'))."','$this->t55_documento',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["t55_estorage"]) || $this->t55_estorage != "")
             $resac = db_query("insert into db_acount values($acount,917,1015524,'".AddSlashes(pg_result($resaco,$conresaco,'t55_estorage'))."','$this->t55_estorage',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Baixa de bens não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->t55_codbem;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Baixa de bens não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->t55_codbem;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->t55_codbem;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($t55_codbem=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($t55_codbem));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,5784,'$t55_codbem','E')");
           $resac  = db_query("insert into db_acount values($acount,917,5784,'','".AddSlashes(pg_result($resaco,$iresaco,'t55_codbem'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,917,5785,'','".AddSlashes(pg_result($resaco,$iresaco,'t55_baixa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,917,5786,'','".AddSlashes(pg_result($resaco,$iresaco,'t55_motivo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,917,9579,'','".AddSlashes(pg_result($resaco,$iresaco,'t55_obs'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,917,1011319,'','".AddSlashes(pg_result($resaco,$iresaco,'t55_documento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,917,1015524,'','".AddSlashes(pg_result($resaco,$iresaco,'t55_estorage'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from bensbaix
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($t55_codbem)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " t55_codbem = $t55_codbem ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Baixa de bens não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$t55_codbem;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Baixa de bens não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$t55_codbem;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$t55_codbem;
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
        $this->erro_sql   = "Record Vazio na Tabela:bensbaix";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($t55_codbem = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
     $sql .= "  from bensbaix ";
     $sql .= "      inner join bens  on  bens.t52_bem = bensbaix.t55_codbem";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = bens.t52_numcgm";
     $sql .= "      inner join db_depart  on  db_depart.coddepto = bens.t52_depart";
     $sql .= "      inner join clabens  on  clabens.t64_codcla = bens.t52_codcla";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($t55_codbem)) {
         $sql2 .= " where bensbaix.t55_codbem = $t55_codbem ";
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

    public function sql_query_relatorio_legal($iCodigoBem = null, $sCampos = "*", $sOrdem = null, $sWhere = "")
    {
        $sSql = "select {$sCampos} ";
        $sSql .= " from bensbaix    ";
        $sSql .= "      inner join bens  on  bens.t52_bem = bensbaix.t55_codbem        ";
        $sSql .= "      inner join db_depart  on  db_depart.coddepto = bens.t52_depart ";
        $sSql .= "      inner join clabens  on  clabens.t64_codcla = bens.t52_codcla   ";

        $aConditions = array();

        if (!empty($iCodigoBem)) {
            $aConditions[] = "bensbaix.t55_codbem = {$iCodigoBem}";
        }

        if (!empty($sWhere)) {
            $aConditions[] = $sWhere;
        }

        if (!empty($aConditions)) {
            $sSql .= " where " . implode(" and ", $aConditions);
        }

        if (!empty($sOrdem)) {
            $sSql .= " order by {$sOrdem}";
        }

        return $sSql;
    }

    public function sql_query_relatorio($t55_codbem = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from bensbaix ";
        $sql .= "      inner join bens  on  bens.t52_bem = bensbaix.t55_codbem";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = bens.t52_numcgm";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = bens.t52_depart";
        $sql .= "      inner join clabens  on  clabens.t64_codcla = bens.t52_codcla";
        $sql .= "      inner join db_departorg on db_departorg.db01_coddepto = bens.t52_depart ";
        $sql .= "			inner join orcorgao on orcorgao.o40_orgao = db_departorg.db01_orgao ";
        $sql .= "			and orcorgao.o40_anousu = db_departorg.db01_anousu ";
        $sql .= "			inner join orcunidade on orcunidade.o41_unidade = db_departorg.db01_unidade ";
        $sql .= "			and orcunidade.o41_orgao = db_departorg.db01_orgao";
        $sql .= "			and orcunidade.o41_anousu = db_departorg.db01_anousu";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($t55_codbem != null) {
                $sql2 .= " where bensbaix.t55_codbem = $t55_codbem ";
            }
        } else {
            if ($dbwhere != "") {
                $sql2 = " where $dbwhere";
            }
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        }

        return $sql;
    }

    public function sql_query_file($t55_codbem = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from bensbaix ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($t55_codbem != null) {
                $sql2 .= " where bensbaix.t55_codbem = $t55_codbem ";
            }
        } else {
            if ($dbwhere != "") {
                $sql2 = " where $dbwhere";
            }
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        }

        return $sql;
    }
}
