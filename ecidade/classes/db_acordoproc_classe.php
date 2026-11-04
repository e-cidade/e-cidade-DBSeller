<?php

class cl_acordoproc
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
    public $ac62_sequencial = 0;
    public $ac62_acordo = 0;
    public $ac62_protprocesso = 0;
    public $ac62_numeroprocesso = null;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 ac62_sequencial = int4 = Sequencial
                 ac62_acordo = int4 = Acordo
                 ac62_protprocesso = int4 = Processo do Acordo
                 ac62_numeroprocesso = varchar(60) = Numero do Processo
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("acordoproc");
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
       $this->ac62_sequencial = ($this->ac62_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["ac62_sequencial"]:$this->ac62_sequencial);
       $this->ac62_acordo = ($this->ac62_acordo == ""?@$GLOBALS["HTTP_POST_VARS"]["ac62_acordo"]:$this->ac62_acordo);
       $this->ac62_protprocesso = ($this->ac62_protprocesso == ""?@$GLOBALS["HTTP_POST_VARS"]["ac62_protprocesso"]:$this->ac62_protprocesso);
       $this->ac62_numeroprocesso = ($this->ac62_numeroprocesso == ""?@$GLOBALS["HTTP_POST_VARS"]["ac62_numeroprocesso"]:$this->ac62_numeroprocesso);
     }else{
       $this->ac62_sequencial = ($this->ac62_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["ac62_sequencial"]:$this->ac62_sequencial);
     }
   }

    public function incluir($ac62_sequencial)
    {
      $this->atualizacampos();
     if($this->ac62_acordo == null ){
       $this->erro_sql = " Campo Acordo não informado.";
       $this->erro_campo = "ac62_acordo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ac62_protprocesso == null ){
       $this->erro_sql = " Campo Processo do Acordo não informado.";
       $this->erro_campo = "ac62_protprocesso";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($ac62_sequencial == "" || $ac62_sequencial == null ){
       $result = db_query("select nextval('acordoproc_ac62_sequencial_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: acordoproc_ac62_sequencial_seq do campo: ac62_sequencial";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->ac62_sequencial = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from acordoproc_ac62_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $ac62_sequencial)){
         $this->erro_sql = " Campo ac62_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->ac62_sequencial = $ac62_sequencial;
       }
     }
     if(($this->ac62_sequencial == null) || ($this->ac62_sequencial == "") ){
       $this->erro_sql = " Campo ac62_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into acordoproc(
                                       ac62_sequencial
                                      ,ac62_acordo
                                      ,ac62_protprocesso
                                      ,ac62_numeroprocesso
                       )
                values (
                                $this->ac62_sequencial
                               ,$this->ac62_acordo
                               ,$this->ac62_protprocesso
                               ,'$this->ac62_numeroprocesso'
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Processos dos Acordos ($this->ac62_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Processos dos Acordos já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Processos dos Acordos ($this->ac62_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->ac62_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ac62_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1015104,'$this->ac62_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,1011088,1015104,'','".AddSlashes(pg_result($resaco,0,'ac62_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011088,1015107,'','".AddSlashes(pg_result($resaco,0,'ac62_acordo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011088,1015106,'','".AddSlashes(pg_result($resaco,0,'ac62_protprocesso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011088,1015108,'','".AddSlashes(pg_result($resaco,0,'ac62_numeroprocesso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }

    public function alterar($ac62_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update acordoproc set ";
     $virgula = "";
     if(trim($this->ac62_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ac62_sequencial"])){
       $sql  .= $virgula." ac62_sequencial = $this->ac62_sequencial ";
       $virgula = ",";
       if(trim($this->ac62_sequencial) == null ){
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "ac62_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ac62_acordo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ac62_acordo"])){
       $sql  .= $virgula." ac62_acordo = $this->ac62_acordo ";
       $virgula = ",";
       if(trim($this->ac62_acordo) == null ){
         $this->erro_sql = " Campo Acordo não informado.";
         $this->erro_campo = "ac62_acordo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ac62_protprocesso)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ac62_protprocesso"])){
       $sql  .= $virgula." ac62_protprocesso = $this->ac62_protprocesso ";
       $virgula = ",";
       if(trim($this->ac62_protprocesso) == null ){
         $this->erro_sql = " Campo Processo do Acordo não informado.";
         $this->erro_campo = "ac62_protprocesso";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ac62_numeroprocesso)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ac62_numeroprocesso"])){
       $sql  .= $virgula." ac62_numeroprocesso = '$this->ac62_numeroprocesso' ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($ac62_sequencial!=null){
       $sql .= " ac62_sequencial = $this->ac62_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ac62_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1015104,'$this->ac62_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ac62_sequencial"]) || $this->ac62_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,1011088,1015104,'".AddSlashes(pg_result($resaco,$conresaco,'ac62_sequencial'))."','$this->ac62_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ac62_acordo"]) || $this->ac62_acordo != "")
             $resac = db_query("insert into db_acount values($acount,1011088,1015107,'".AddSlashes(pg_result($resaco,$conresaco,'ac62_acordo'))."','$this->ac62_acordo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ac62_protprocesso"]) || $this->ac62_protprocesso != "")
             $resac = db_query("insert into db_acount values($acount,1011088,1015106,'".AddSlashes(pg_result($resaco,$conresaco,'ac62_protprocesso'))."','$this->ac62_protprocesso',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ac62_numeroprocesso"]) || $this->ac62_numeroprocesso != "")
             $resac = db_query("insert into db_acount values($acount,1011088,1015108,'".AddSlashes(pg_result($resaco,$conresaco,'ac62_numeroprocesso'))."','$this->ac62_numeroprocesso',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Processos dos Acordos não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->ac62_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Processos dos Acordos não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->ac62_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->ac62_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($ac62_sequencial=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($ac62_sequencial));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1015104,'$ac62_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,1011088,1015104,'','".AddSlashes(pg_result($resaco,$iresaco,'ac62_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011088,1015107,'','".AddSlashes(pg_result($resaco,$iresaco,'ac62_acordo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011088,1015106,'','".AddSlashes(pg_result($resaco,$iresaco,'ac62_protprocesso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011088,1015108,'','".AddSlashes(pg_result($resaco,$iresaco,'ac62_numeroprocesso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from acordoproc
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($ac62_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " ac62_sequencial = $ac62_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Processos dos Acordos não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$ac62_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Processos dos Acordos não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$ac62_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$ac62_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:acordoproc";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($ac62_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
     $sql .= "  from acordoproc ";
     $sql .= "      inner join protprocesso  on  protprocesso.p58_codproc = acordoproc.ac62_protprocesso";
     $sql .= "      inner join acordo  on  acordo.ac16_sequencial = acordoproc.ac62_acordo";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = protprocesso.p58_numcgm";
     $sql .= "      inner join db_config  on  db_config.codigo = protprocesso.p58_instit";
     $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = protprocesso.p58_id_usuario";
     $sql .= "      inner join db_depart  on  db_depart.coddepto = protprocesso.p58_coddepto";
     $sql .= "      inner join tipoproc  on  tipoproc.p51_codigo = protprocesso.p58_codigo";
     $sql .= "      inner join tipoprocesso  on  tipoprocesso.p109_sequencial = protprocesso.p58_tipoprocesso";
     $sql .= "      inner join cgm  as a on   a.z01_numcgm = acordo.ac16_contratado";
     $sql .= "      inner join db_depart  as b on   b.coddepto = acordo.ac16_coddepto and   b.coddepto = acordo.ac16_deptoresponsavel";
     $sql .= "      inner join acordogrupo  on  acordogrupo.ac02_sequencial = acordo.ac16_acordogrupo";
     $sql .= "      inner join acordosituacao  on  acordosituacao.ac17_sequencial = acordo.ac16_acordosituacao";
     $sql .= "      inner join acordocomissao  on  acordocomissao.ac08_sequencial = acordo.ac16_acordocomissao";
     $sql .= "      left  join acordocategoria  on  acordocategoria.ac50_sequencial = acordo.ac16_acordocategoria";
     $sql .= "      inner join acordoclassificacao  on  acordoclassificacao.ac46_sequencial = acordo.ac16_acordoclassificacao";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ac62_sequencial)) {
         $sql2 .= " where acordoproc.ac62_sequencial = $ac62_sequencial ";
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

    public function sql_query_file($ac62_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from acordoproc ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ac62_sequencial)){
         $sql2 .= " where acordoproc.ac62_sequencial = $ac62_sequencial ";
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
