<?php

class cl_diversostaxa
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
    public $dv15_sequencial = 0; 
    public $dv15_coddiver = 0; 
    public $dv15_codtaxa = 0; 
    public $dv15_valor = 0; 
    public $dv15_taxaprincipal = 'f'; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 dv15_sequencial = int4 = Sequencial 
                 dv15_coddiver = int4 = Código Diversos 
                 dv15_codtaxa = int4 = Código Taxa 
                 dv15_valor = float4 = Valor 
                 dv15_taxaprincipal = bool = Taxa principal 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("diversostaxa"); 
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
       $this->dv15_sequencial = ($this->dv15_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["dv15_sequencial"]:$this->dv15_sequencial);
       $this->dv15_coddiver = ($this->dv15_coddiver == ""?@$GLOBALS["HTTP_POST_VARS"]["dv15_coddiver"]:$this->dv15_coddiver);
       $this->dv15_codtaxa = ($this->dv15_codtaxa == ""?@$GLOBALS["HTTP_POST_VARS"]["dv15_codtaxa"]:$this->dv15_codtaxa);
       $this->dv15_valor = ($this->dv15_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["dv15_valor"]:$this->dv15_valor);
       $this->dv15_taxaprincipal = ($this->dv15_taxaprincipal == "f"?@$GLOBALS["HTTP_POST_VARS"]["dv15_taxaprincipal"]:$this->dv15_taxaprincipal);
     }else{
       $this->dv15_sequencial = ($this->dv15_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["dv15_sequencial"]:$this->dv15_sequencial);
     }
   }

    public function incluir()
    {
      $this->atualizacampos();
     if($this->dv15_coddiver == null ){ 
       $this->erro_sql = " Campo Código Diversos não informado.";
       $this->erro_campo = "dv15_coddiver";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->dv15_codtaxa == null ){ 
       $this->erro_sql = " Campo Código Taxa não informado.";
       $this->erro_campo = "dv15_codtaxa";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->dv15_valor == null ){ 
       $this->erro_sql = " Campo Valor não informado.";
       $this->erro_campo = "dv15_valor";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     if($this->dv15_taxaprincipal === 't' || $this->dv15_taxaprincipal === 'true' || $this->dv15_taxaprincipal === true){ 
      $this->dv15_taxaprincipal = 'true';
     } else {
      $this->dv15_taxaprincipal = 'false';
     }

     $sql = "insert into diversostaxa(
                                      dv15_coddiver 
                                      ,dv15_codtaxa 
                                      ,dv15_valor 
                                      ,dv15_taxaprincipal 
                       )
                values (
                               $this->dv15_coddiver 
                               ,$this->dv15_codtaxa 
                               ,$this->dv15_valor 
                               ,$this->dv15_taxaprincipal 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Diversos Taxa ($this->dv15_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Diversos Taxa já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Diversos Taxa ($this->dv15_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->dv15_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->dv15_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1015376,'$this->dv15_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,1011135,1015376,'','".AddSlashes(pg_result($resaco,0,'dv15_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011135,1015377,'','".AddSlashes(pg_result($resaco,0,'dv15_coddiver'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011135,1015378,'','".AddSlashes(pg_result($resaco,0,'dv15_codtaxa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011135,1015379,'','".AddSlashes(pg_result($resaco,0,'dv15_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011135,1015382,'','".AddSlashes(pg_result($resaco,0,'dv15_taxaprincipal'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($dv15_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update diversostaxa set ";
     $virgula = "";
     if(trim($this->dv15_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["dv15_sequencial"])){ 
       $sql  .= $virgula." dv15_sequencial = $this->dv15_sequencial ";
       $virgula = ",";
       if(trim($this->dv15_sequencial) == null ){ 
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "dv15_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->dv15_coddiver)!="" || isset($GLOBALS["HTTP_POST_VARS"]["dv15_coddiver"])){ 
       $sql  .= $virgula." dv15_coddiver = $this->dv15_coddiver ";
       $virgula = ",";
       if(trim($this->dv15_coddiver) == null ){ 
         $this->erro_sql = " Campo Código Diversos não informado.";
         $this->erro_campo = "dv15_coddiver";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->dv15_codtaxa)!="" || isset($GLOBALS["HTTP_POST_VARS"]["dv15_codtaxa"])){ 
       $sql  .= $virgula." dv15_codtaxa = $this->dv15_codtaxa ";
       $virgula = ",";
       if(trim($this->dv15_codtaxa) == null ){ 
         $this->erro_sql = " Campo Código Taxa não informado.";
         $this->erro_campo = "dv15_codtaxa";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->dv15_valor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["dv15_valor"])){ 
       $sql  .= $virgula." dv15_valor = $this->dv15_valor ";
       $virgula = ",";
       if(trim($this->dv15_valor) == null ){ 
         $this->erro_sql = " Campo Valor não informado.";
         $this->erro_campo = "dv15_valor";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->dv15_taxaprincipal)!="" || isset($GLOBALS["HTTP_POST_VARS"]["dv15_taxaprincipal"])){ 
       $sql  .= $virgula." dv15_taxaprincipal = '$this->dv15_taxaprincipal' ";
       $virgula = ",";
       if(trim($this->dv15_taxaprincipal) == null ){ 
         $this->erro_sql = " Campo Taxa principal não informado.";
         $this->erro_campo = "dv15_taxaprincipal";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($dv15_sequencial!=null){
       $sql .= " dv15_sequencial = $this->dv15_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->dv15_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1015376,'$this->dv15_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["dv15_sequencial"]) || $this->dv15_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,1011135,1015376,'".AddSlashes(pg_result($resaco,$conresaco,'dv15_sequencial'))."','$this->dv15_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["dv15_coddiver"]) || $this->dv15_coddiver != "")
             $resac = db_query("insert into db_acount values($acount,1011135,1015377,'".AddSlashes(pg_result($resaco,$conresaco,'dv15_coddiver'))."','$this->dv15_coddiver',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["dv15_codtaxa"]) || $this->dv15_codtaxa != "")
             $resac = db_query("insert into db_acount values($acount,1011135,1015378,'".AddSlashes(pg_result($resaco,$conresaco,'dv15_codtaxa'))."','$this->dv15_codtaxa',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["dv15_valor"]) || $this->dv15_valor != "")
             $resac = db_query("insert into db_acount values($acount,1011135,1015379,'".AddSlashes(pg_result($resaco,$conresaco,'dv15_valor'))."','$this->dv15_valor',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["dv15_taxaprincipal"]) || $this->dv15_taxaprincipal != "")
             $resac = db_query("insert into db_acount values($acount,1011135,1015382,'".AddSlashes(pg_result($resaco,$conresaco,'dv15_taxaprincipal'))."','$this->dv15_taxaprincipal',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Diversos Taxa não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->dv15_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Diversos Taxa não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->dv15_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->dv15_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($dv15_sequencial=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($dv15_sequencial));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1015376,'$dv15_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,1011135,1015376,'','".AddSlashes(pg_result($resaco,$iresaco,'dv15_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011135,1015377,'','".AddSlashes(pg_result($resaco,$iresaco,'dv15_coddiver'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011135,1015378,'','".AddSlashes(pg_result($resaco,$iresaco,'dv15_codtaxa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011135,1015379,'','".AddSlashes(pg_result($resaco,$iresaco,'dv15_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011135,1015382,'','".AddSlashes(pg_result($resaco,$iresaco,'dv15_taxaprincipal'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from diversostaxa
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($dv15_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " dv15_sequencial = $dv15_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Diversos Taxa não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$dv15_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Diversos Taxa não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$dv15_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$dv15_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:diversostaxa";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($dv15_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from diversostaxa ";
     $sql .= "      inner join diversos  on  diversos.dv05_coddiver = diversostaxa.dv15_coddiver";
     $sql .= "      inner join taxaslancadas  on  taxaslancadas.ar44_sequencial = diversostaxa.dv15_codtaxa";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = diversos.dv05_numcgm";
     $sql .= "      inner join db_config  on  db_config.codigo = diversos.dv05_instit";
     $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = diversos.dv05_login";
     $sql .= "      inner join procdiver  on  procdiver.dv09_procdiver = diversos.dv05_procdiver";
     $sql .= "      left  join tabrec  on  tabrec.k02_codigo = taxaslancadas.ar44_receitaxaexpediente and  tabrec.k02_codigo = taxaslancadas.ar44_receita";
     $sql .= "      inner join inflan  on  inflan.i01_codigo = taxaslancadas.ar44_inflator";
     $sql .= "      left  join procdiver  as a on   a.dv09_procdiver = taxaslancadas.ar44_procedencia";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($dv15_sequencial)) {
         $sql2 .= " where diversostaxa.dv15_sequencial = $dv15_sequencial "; 
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

    public function sql_query_file($dv15_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from diversostaxa ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($dv15_sequencial)){
         $sql2 .= " where diversostaxa.dv15_sequencial = $dv15_sequencial "; 
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
