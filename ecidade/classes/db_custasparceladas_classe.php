<?php

class cl_custasparceladas
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
    public $ar58_sequencial = 0; 
    public $ar58_processoforo = 0; 
    public $ar58_parcelamento = 0; 
    public $ar58_taxa = 0; 
    public $ar58_valor = 0; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 ar58_sequencial = int4 = Sequencial 
                 ar58_processoforo = int4 = Processo do foro 
                 ar58_parcelamento = int4 = Parcelamento 
                 ar58_taxa = int4 = Taxa 
                 ar58_valor = float4 = Valor 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("custasparceladas"); 
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
       $this->ar58_sequencial = ($this->ar58_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["ar58_sequencial"]:$this->ar58_sequencial);
       $this->ar58_processoforo = ($this->ar58_processoforo == ""?@$GLOBALS["HTTP_POST_VARS"]["ar58_processoforo"]:$this->ar58_processoforo);
       $this->ar58_parcelamento = ($this->ar58_parcelamento == ""?@$GLOBALS["HTTP_POST_VARS"]["ar58_parcelamento"]:$this->ar58_parcelamento);
       $this->ar58_taxa = ($this->ar58_taxa == ""?@$GLOBALS["HTTP_POST_VARS"]["ar58_taxa"]:$this->ar58_taxa);
       $this->ar58_valor = ($this->ar58_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["ar58_valor"]:$this->ar58_valor);
     }else{
       $this->ar58_sequencial = ($this->ar58_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["ar58_sequencial"]:$this->ar58_sequencial);
     }
   }

    public function incluir()
    {
      $this->atualizacampos();
     if($this->ar58_processoforo == null ){ 
       $this->erro_sql = " Campo Processo do foro não informado.";
       $this->erro_campo = "ar58_processoforo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ar58_parcelamento == null ){ 
       $this->erro_sql = " Campo Parcelamento não informado.";
       $this->erro_campo = "ar58_parcelamento";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ar58_taxa == null ){ 
       $this->erro_sql = " Campo Taxa não informado.";
       $this->erro_campo = "ar58_taxa";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ar58_valor == null ){ 
       $this->erro_sql = " Campo Valor não informado.";
       $this->erro_campo = "ar58_valor";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     $sql = "insert into custasparceladas(
                                       ar58_processoforo 
                                      ,ar58_parcelamento 
                                      ,ar58_taxa 
                                      ,ar58_valor 
                       )
                values (
                                $this->ar58_processoforo 
                               ,$this->ar58_parcelamento 
                               ,$this->ar58_taxa 
                               ,$this->ar58_valor 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Custas Parceladas não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Custas Parceladas já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Custas Parceladas não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ";
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     return true;
   } 

    public function alterar($ar58_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update custasparceladas set ";
     $virgula = "";
     if(trim($this->ar58_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ar58_sequencial"])){ 
       $sql  .= $virgula." ar58_sequencial = $this->ar58_sequencial ";
       $virgula = ",";
       if(trim($this->ar58_sequencial) == null ){ 
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "ar58_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ar58_processoforo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ar58_processoforo"])){ 
       $sql  .= $virgula." ar58_processoforo = $this->ar58_processoforo ";
       $virgula = ",";
       if(trim($this->ar58_processoforo) == null ){ 
         $this->erro_sql = " Campo Processo do foro não informado.";
         $this->erro_campo = "ar58_processoforo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ar58_parcelamento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ar58_parcelamento"])){ 
       $sql  .= $virgula." ar58_parcelamento = $this->ar58_parcelamento ";
       $virgula = ",";
       if(trim($this->ar58_parcelamento) == null ){ 
         $this->erro_sql = " Campo Parcelamento não informado.";
         $this->erro_campo = "ar58_parcelamento";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ar58_taxa)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ar58_taxa"])){ 
       $sql  .= $virgula." ar58_taxa = $this->ar58_taxa ";
       $virgula = ",";
       if(trim($this->ar58_taxa) == null ){ 
         $this->erro_sql = " Campo Taxa não informado.";
         $this->erro_campo = "ar58_taxa";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ar58_valor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ar58_valor"])){ 
       $sql  .= $virgula." ar58_valor = $this->ar58_valor ";
       $virgula = ",";
       if(trim($this->ar58_valor) == null ){ 
         $this->erro_sql = " Campo Valor não informado.";
         $this->erro_campo = "ar58_valor";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($ar58_sequencial!=null){
       $sql .= " ar58_sequencial = $this->ar58_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ar58_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1015489,'$this->ar58_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ar58_sequencial"]) || $this->ar58_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,1011150,1015489,'".AddSlashes(pg_result($resaco,$conresaco,'ar58_sequencial'))."','$this->ar58_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ar58_processoforo"]) || $this->ar58_processoforo != "")
             $resac = db_query("insert into db_acount values($acount,1011150,1015490,'".AddSlashes(pg_result($resaco,$conresaco,'ar58_processoforo'))."','$this->ar58_processoforo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ar58_parcelamento"]) || $this->ar58_parcelamento != "")
             $resac = db_query("insert into db_acount values($acount,1011150,1015491,'".AddSlashes(pg_result($resaco,$conresaco,'ar58_parcelamento'))."','$this->ar58_parcelamento',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ar58_taxa"]) || $this->ar58_taxa != "")
             $resac = db_query("insert into db_acount values($acount,1011150,1015492,'".AddSlashes(pg_result($resaco,$conresaco,'ar58_taxa'))."','$this->ar58_taxa',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ar58_valor"]) || $this->ar58_valor != "")
             $resac = db_query("insert into db_acount values($acount,1011150,1015493,'".AddSlashes(pg_result($resaco,$conresaco,'ar58_valor'))."','$this->ar58_valor',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Custas Parceladas não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->ar58_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Custas Parceladas não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->ar58_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->ar58_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($ar58_sequencial=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($ar58_sequencial));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1015489,'$ar58_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,1011150,1015489,'','".AddSlashes(pg_result($resaco,$iresaco,'ar58_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011150,1015490,'','".AddSlashes(pg_result($resaco,$iresaco,'ar58_processoforo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011150,1015491,'','".AddSlashes(pg_result($resaco,$iresaco,'ar58_parcelamento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011150,1015492,'','".AddSlashes(pg_result($resaco,$iresaco,'ar58_taxa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011150,1015493,'','".AddSlashes(pg_result($resaco,$iresaco,'ar58_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from custasparceladas
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($ar58_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " ar58_sequencial = $ar58_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Custas Parceladas não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$ar58_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Custas Parceladas não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$ar58_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$ar58_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:custasparceladas";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($ar58_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from custasparceladas ";
     $sql .= "      inner join termo  on  termo.v07_parcel = custasparceladas.ar58_parcelamento";
     $sql .= "      inner join processoforo  on  processoforo.v70_sequencial = custasparceladas.ar58_processoforo";
     $sql .= "      inner join taxa  on  taxa.ar36_sequencial = custasparceladas.ar58_taxa";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = termo.v07_numcgm";
     $sql .= "      inner join db_config  on  db_config.codigo = termo.v07_instit";
     $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = termo.v07_login";
     $sql .= "      inner join cadtipoparc  on  cadtipoparc.k40_codigo = termo.v07_desconto";
     $sql .= "      inner join db_config  as a on   a.codigo = processoforo.v70_instit";
     $sql .= "      inner join db_usuarios  as b on   b.id_usuario = processoforo.v70_id_usuario";
     $sql .= "      inner join vara  on  vara.v53_codvara = processoforo.v70_vara";
     $sql .= "      left  join processoforomov  on  processoforomov.v73_sequencial = processoforo.v70_processoforomov";
     $sql .= "      inner join cartorio  on  cartorio.v82_sequencial = processoforo.v70_cartorio";
     $sql .= "      inner join tabrec  on  tabrec.k02_codigo = taxa.ar36_receita";
     $sql .= "      inner join grupotaxa  on  grupotaxa.ar37_sequencial = taxa.ar36_grupotaxa";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ar58_sequencial)) {
         $sql2 .= " where custasparceladas.ar58_sequencial = $ar58_sequencial "; 
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

    public function sql_query_file($ar58_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from custasparceladas ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ar58_sequencial)){
         $sql2 .= " where custasparceladas.ar58_sequencial = $ar58_sequencial "; 
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
