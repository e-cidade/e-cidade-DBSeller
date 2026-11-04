<?php

class cl_acordolicitacaocompartilhada
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
    public $ac63_numcgm = 0;
    public $ac63_licitacaocompartilhada = 0;
    public $ac63_acordo = 0;
    public $ac63_sequencial = 0;
    public $ac63_numero = 0;
    public $ac63_ano = 0;
    public $ac63_modalidade = 0;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 ac63_numcgm = int4 = Número do Cgm
                 ac63_licitacaocompartilhada = int4 = Licitação Compartilhada
                 ac63_acordo = int4 = Acordo
                 ac63_sequencial = int4 = Sequencial
                 ac63_numero = int4 = Numero da Licitação
                 ac63_ano = int4 = Ano Licitação
                 ac63_modalidade = int4 = Modalidade Licitação
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("acordolicitacaocompartilhada");
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
       $this->ac63_numcgm = ($this->ac63_numcgm == ""?@$GLOBALS["HTTP_POST_VARS"]["ac63_numcgm"]:$this->ac63_numcgm);
       $this->ac63_licitacaocompartilhada = ($this->ac63_licitacaocompartilhada == ""?@$GLOBALS["HTTP_POST_VARS"]["ac63_licitacaocompartilhada"]:$this->ac63_licitacaocompartilhada);
       $this->ac63_acordo = ($this->ac63_acordo == ""?@$GLOBALS["HTTP_POST_VARS"]["ac63_acordo"]:$this->ac63_acordo);
       $this->ac63_sequencial = ($this->ac63_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["ac63_sequencial"]:$this->ac63_sequencial);
       $this->ac63_numero = ($this->ac63_numero == ""?@$GLOBALS["HTTP_POST_VARS"]["ac63_numero"]:$this->ac63_numero);
       $this->ac63_ano = ($this->ac63_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["ac63_ano"]:$this->ac63_ano);
       $this->ac63_modalidade = ($this->ac63_modalidade == ""?@$GLOBALS["HTTP_POST_VARS"]["ac63_modalidade"]:$this->ac63_modalidade);
     }else{
       $this->ac63_sequencial = ($this->ac63_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["ac63_sequencial"]:$this->ac63_sequencial);
     }
   }

    public function incluir($ac63_sequencial)
    {
      $this->atualizacampos();
     if($this->ac63_numcgm == null ){
       $this->erro_sql = " Campo Número do Cgm não informado.";
       $this->erro_campo = "ac63_numcgm";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ac63_licitacaocompartilhada == null ){
       $this->erro_sql = " Campo Licitação Compartilhada não informado.";
       $this->erro_campo = "ac63_licitacaocompartilhada";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ac63_acordo == null ){
       $this->erro_sql = " Campo Acordo não informado.";
       $this->erro_campo = "ac63_acordo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ac63_numero == null ){
       $this->erro_sql = " Campo Numero da Licitação não informado.";
       $this->erro_campo = "ac63_numero";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ac63_ano == null ){
       $this->erro_sql = " Campo Ano Licitação não informado.";
       $this->erro_campo = "ac63_ano";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ac63_modalidade == null ){
       $this->erro_sql = " Campo Modalidade Licitação não informado.";
       $this->erro_campo = "ac63_modalidade";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($ac63_sequencial == "" || $ac63_sequencial == null ){
       $result = db_query("select nextval('acordolicitacaocompartilhada_ac63_sequencial_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: acordolicitacaocompartilhada_ac63_sequencial_seq do campo: ac63_sequencial";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->ac63_sequencial = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from acordolicitacaocompartilhada_ac63_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $ac63_sequencial)){
         $this->erro_sql = " Campo ac63_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->ac63_sequencial = $ac63_sequencial;
       }
     }
     if(($this->ac63_sequencial == null) || ($this->ac63_sequencial == "") ){
       $this->erro_sql = " Campo ac63_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into acordolicitacaocompartilhada(
                                       ac63_numcgm
                                      ,ac63_licitacaocompartilhada
                                      ,ac63_acordo
                                      ,ac63_sequencial
                                      ,ac63_numero
                                      ,ac63_ano
                                      ,ac63_modalidade
                       )
                values (
                                $this->ac63_numcgm
                               ,$this->ac63_licitacaocompartilhada
                               ,$this->ac63_acordo
                               ,$this->ac63_sequencial
                               ,$this->ac63_numero
                               ,$this->ac63_ano
                               ,$this->ac63_modalidade
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Acordo Licitacao Compartilhada ($this->ac63_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Acordo Licitacao Compartilhada já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Acordo Licitacao Compartilhada ($this->ac63_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->ac63_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ac63_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1015223,'$this->ac63_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,1011113,1015226,'','".AddSlashes(pg_result($resaco,0,'ac63_numcgm'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011113,1015225,'','".AddSlashes(pg_result($resaco,0,'ac63_licitacaocompartilhada'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011113,1015224,'','".AddSlashes(pg_result($resaco,0,'ac63_acordo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011113,1015223,'','".AddSlashes(pg_result($resaco,0,'ac63_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011113,1015230,'','".AddSlashes(pg_result($resaco,0,'ac63_numero'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011113,1015229,'','".AddSlashes(pg_result($resaco,0,'ac63_ano'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011113,1015228,'','".AddSlashes(pg_result($resaco,0,'ac63_modalidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }

    public function alterar($ac63_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update acordolicitacaocompartilhada set ";
     $virgula = "";
     if(trim($this->ac63_numcgm)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ac63_numcgm"])){
       $sql  .= $virgula." ac63_numcgm = $this->ac63_numcgm ";
       $virgula = ",";
       if(trim($this->ac63_numcgm) == null ){
         $this->erro_sql = " Campo Número do Cgm não informado.";
         $this->erro_campo = "ac63_numcgm";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ac63_licitacaocompartilhada)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ac63_licitacaocompartilhada"])){
       $sql  .= $virgula." ac63_licitacaocompartilhada = $this->ac63_licitacaocompartilhada ";
       $virgula = ",";
       if(trim($this->ac63_licitacaocompartilhada) == null ){
         $this->erro_sql = " Campo Licitação Compartilhada não informado.";
         $this->erro_campo = "ac63_licitacaocompartilhada";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ac63_acordo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ac63_acordo"])){
       $sql  .= $virgula." ac63_acordo = $this->ac63_acordo ";
       $virgula = ",";
       if(trim($this->ac63_acordo) == null ){
         $this->erro_sql = " Campo Acordo não informado.";
         $this->erro_campo = "ac63_acordo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ac63_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ac63_sequencial"])){
       $sql  .= $virgula." ac63_sequencial = $this->ac63_sequencial ";
       $virgula = ",";
       if(trim($this->ac63_sequencial) == null ){
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "ac63_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ac63_numero)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ac63_numero"])){
       $sql  .= $virgula." ac63_numero = $this->ac63_numero ";
       $virgula = ",";
       if(trim($this->ac63_numero) == null ){
         $this->erro_sql = " Campo Numero da Licitação não informado.";
         $this->erro_campo = "ac63_numero";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ac63_ano)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ac63_ano"])){
       $sql  .= $virgula." ac63_ano = $this->ac63_ano ";
       $virgula = ",";
       if(trim($this->ac63_ano) == null ){
         $this->erro_sql = " Campo Ano Licitação não informado.";
         $this->erro_campo = "ac63_ano";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ac63_modalidade)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ac63_modalidade"])){
       $sql  .= $virgula." ac63_modalidade = $this->ac63_modalidade ";
       $virgula = ",";
       if(trim($this->ac63_modalidade) == null ){
         $this->erro_sql = " Campo Modalidade Licitação não informado.";
         $this->erro_campo = "ac63_modalidade";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($ac63_sequencial!=null){
       $sql .= " ac63_sequencial = $ac63_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ac63_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1015223,'$this->ac63_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ac63_numcgm"]) || $this->ac63_numcgm != "")
             $resac = db_query("insert into db_acount values($acount,1011113,1015226,'".AddSlashes(pg_result($resaco,$conresaco,'ac63_numcgm'))."','$this->ac63_numcgm',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ac63_licitacaocompartilhada"]) || $this->ac63_licitacaocompartilhada != "")
             $resac = db_query("insert into db_acount values($acount,1011113,1015225,'".AddSlashes(pg_result($resaco,$conresaco,'ac63_licitacaocompartilhada'))."','$this->ac63_licitacaocompartilhada',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ac63_acordo"]) || $this->ac63_acordo != "")
             $resac = db_query("insert into db_acount values($acount,1011113,1015224,'".AddSlashes(pg_result($resaco,$conresaco,'ac63_acordo'))."','$this->ac63_acordo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ac63_sequencial"]) || $this->ac63_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,1011113,1015223,'".AddSlashes(pg_result($resaco,$conresaco,'ac63_sequencial'))."','$this->ac63_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ac63_numero"]) || $this->ac63_numero != "")
             $resac = db_query("insert into db_acount values($acount,1011113,1015230,'".AddSlashes(pg_result($resaco,$conresaco,'ac63_numero'))."','$this->ac63_numero',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ac63_ano"]) || $this->ac63_ano != "")
             $resac = db_query("insert into db_acount values($acount,1011113,1015229,'".AddSlashes(pg_result($resaco,$conresaco,'ac63_ano'))."','$this->ac63_ano',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ac63_modalidade"]) || $this->ac63_modalidade != "")
             $resac = db_query("insert into db_acount values($acount,1011113,1015228,'".AddSlashes(pg_result($resaco,$conresaco,'ac63_modalidade'))."','$this->ac63_modalidade',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Acordo Licitacao Compartilhada não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->ac63_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Acordo Licitacao Compartilhada não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->ac63_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->ac63_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($ac63_sequencial=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($ac63_sequencial));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1015223,'$ac63_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,1011113,1015226,'','".AddSlashes(pg_result($resaco,$iresaco,'ac63_numcgm'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011113,1015225,'','".AddSlashes(pg_result($resaco,$iresaco,'ac63_licitacaocompartilhada'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011113,1015224,'','".AddSlashes(pg_result($resaco,$iresaco,'ac63_acordo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011113,1015223,'','".AddSlashes(pg_result($resaco,$iresaco,'ac63_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011113,1015230,'','".AddSlashes(pg_result($resaco,$iresaco,'ac63_numero'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011113,1015229,'','".AddSlashes(pg_result($resaco,$iresaco,'ac63_ano'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011113,1015228,'','".AddSlashes(pg_result($resaco,$iresaco,'ac63_modalidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from acordolicitacaocompartilhada
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($ac63_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " ac63_sequencial = $ac63_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Acordo Licitacao Compartilhada não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$ac63_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Acordo Licitacao Compartilhada não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$ac63_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$ac63_sequencial;
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
     return $result;
   }

    public function sql_query($ac63_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
     $sql .= "  from acordolicitacaocompartilhada ";
     $sql .= "      inner join cflicita  on  cflicita.l03_codigo = acordolicitacaocompartilhada.ac63_modalidade";
     $sql .= "      inner join acordo  on  acordo.ac16_sequencial = acordolicitacaocompartilhada.ac63_acordo";
     $sql .= "      inner join db_config  on  db_config.codigo = cflicita.l03_instit";
     $sql .= "      inner join pctipocompra  on  pctipocompra.pc50_codcom = cflicita.l03_codcom";
     $sql .= "      inner join pctipocompratribunal  on  pctipocompratribunal.l44_sequencial = cflicita.l03_pctipocompratribunal";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = acordo.ac16_contratado";
     $sql .= "      inner join db_depart  on  db_depart.coddepto = acordo.ac16_coddepto and  db_depart.coddepto = acordo.ac16_deptoresponsavel";
     $sql .= "      inner join acordogrupo  on  acordogrupo.ac02_sequencial = acordo.ac16_acordogrupo";
     $sql .= "      inner join acordosituacao  on  acordosituacao.ac17_sequencial = acordo.ac16_acordosituacao";
     $sql .= "      inner join acordocomissao  on  acordocomissao.ac08_sequencial = acordo.ac16_acordocomissao";
     $sql .= "      left  join acordocategoria  on  acordocategoria.ac50_sequencial = acordo.ac16_acordocategoria";
     $sql .= "      inner join acordoclassificacao  on  acordoclassificacao.ac46_sequencial = acordo.ac16_acordoclassificacao";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ac63_sequencial)) {
         $sql2 .= " where acordolicitacaocompartilhada.ac63_sequencial = $ac63_sequencial ";
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

    public function sql_query_file($ac63_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from acordolicitacaocompartilhada ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ac63_sequencial)){
         $sql2 .= " where acordolicitacaocompartilhada.ac63_sequencial = $ac63_sequencial ";
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
