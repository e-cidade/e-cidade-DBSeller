<?php

class cl_isencaocalc
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
    public $v46_sequencial = 0; 
    public $v46_isencao = 0; 
    public $v46_cadcalc = 0; 
    public $v46_percentual = 0; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 v46_sequencial = int4 = Sequencial 
                 v46_isencao = int4 = Isenção 
                 v46_cadcalc = int4 = Cálculo 
                 v46_percentual = float4 = Percentual 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("isencaocalc"); 
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
       $this->v46_sequencial = ($this->v46_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["v46_sequencial"]:$this->v46_sequencial);
       $this->v46_isencao = ($this->v46_isencao == ""?@$GLOBALS["HTTP_POST_VARS"]["v46_isencao"]:$this->v46_isencao);
       $this->v46_cadcalc = ($this->v46_cadcalc == ""?@$GLOBALS["HTTP_POST_VARS"]["v46_cadcalc"]:$this->v46_cadcalc);
       $this->v46_percentual = ($this->v46_percentual == ""?@$GLOBALS["HTTP_POST_VARS"]["v46_percentual"]:$this->v46_percentual);
     }else{
       $this->v46_sequencial = ($this->v46_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["v46_sequencial"]:$this->v46_sequencial);
     }
   }

    public function incluir($v46_sequencial)
    {
      $this->atualizacampos();
     if($this->v46_isencao == null ){ 
       $this->erro_sql = " Campo Isenção não informado.";
       $this->erro_campo = "v46_isencao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->v46_cadcalc == null ){ 
       $this->erro_sql = " Campo Cálculo não informado.";
       $this->erro_campo = "v46_cadcalc";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->v46_percentual == null ){ 
       $this->erro_sql = " Campo Percentual não informado.";
       $this->erro_campo = "v46_percentual";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
       $this->v46_sequencial = $v46_sequencial; 
     if(($this->v46_sequencial == null) || ($this->v46_sequencial == "") ){ 
       $this->erro_sql = " Campo v46_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into isencaocalc(
                                       v46_sequencial 
                                      ,v46_isencao 
                                      ,v46_cadcalc 
                                      ,v46_percentual 
                       )
                values (
                                $this->v46_sequencial 
                               ,$this->v46_isencao 
                               ,$this->v46_cadcalc 
                               ,$this->v46_percentual 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Cálculos tipo de isenção ($this->v46_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Cálculos tipo de isenção já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Cálculos tipo de isenção ($this->v46_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->v46_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->v46_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1015318,'$this->v46_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,1011129,1015318,'','".AddSlashes(pg_result($resaco,0,'v46_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011129,1015319,'','".AddSlashes(pg_result($resaco,0,'v46_isencao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011129,1015320,'','".AddSlashes(pg_result($resaco,0,'v46_cadcalc'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011129,1015321,'','".AddSlashes(pg_result($resaco,0,'v46_percentual'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($v46_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update isencaocalc set ";
     $virgula = "";
     if(trim($this->v46_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["v46_sequencial"])){ 
       $sql  .= $virgula." v46_sequencial = $this->v46_sequencial ";
       $virgula = ",";
       if(trim($this->v46_sequencial) == null ){ 
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "v46_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->v46_isencao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["v46_isencao"])){ 
       $sql  .= $virgula." v46_isencao = $this->v46_isencao ";
       $virgula = ",";
       if(trim($this->v46_isencao) == null ){ 
         $this->erro_sql = " Campo Isenção não informado.";
         $this->erro_campo = "v46_isencao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->v46_cadcalc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["v46_cadcalc"])){ 
       $sql  .= $virgula." v46_cadcalc = $this->v46_cadcalc ";
       $virgula = ",";
       if(trim($this->v46_cadcalc) == null ){ 
         $this->erro_sql = " Campo Cálculo não informado.";
         $this->erro_campo = "v46_cadcalc";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->v46_percentual)!="" || isset($GLOBALS["HTTP_POST_VARS"]["v46_percentual"])){ 
       $sql  .= $virgula." v46_percentual = $this->v46_percentual ";
       $virgula = ",";
       if(trim($this->v46_percentual) == null ){ 
         $this->erro_sql = " Campo Percentual não informado.";
         $this->erro_campo = "v46_percentual";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($v46_sequencial!=null){
       $sql .= " v46_sequencial = $this->v46_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->v46_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1015318,'$this->v46_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["v46_sequencial"]) || $this->v46_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,1011129,1015318,'".AddSlashes(pg_result($resaco,$conresaco,'v46_sequencial'))."','$this->v46_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["v46_isencao"]) || $this->v46_isencao != "")
             $resac = db_query("insert into db_acount values($acount,1011129,1015319,'".AddSlashes(pg_result($resaco,$conresaco,'v46_isencao'))."','$this->v46_isencao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["v46_cadcalc"]) || $this->v46_cadcalc != "")
             $resac = db_query("insert into db_acount values($acount,1011129,1015320,'".AddSlashes(pg_result($resaco,$conresaco,'v46_cadcalc'))."','$this->v46_cadcalc',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["v46_percentual"]) || $this->v46_percentual != "")
             $resac = db_query("insert into db_acount values($acount,1011129,1015321,'".AddSlashes(pg_result($resaco,$conresaco,'v46_percentual'))."','$this->v46_percentual',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Cálculos tipo de isenção não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->v46_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Cálculos tipo de isenção não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->v46_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->v46_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($v46_sequencial=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($v46_sequencial));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1015318,'$v46_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,1011129,1015318,'','".AddSlashes(pg_result($resaco,$iresaco,'v46_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011129,1015319,'','".AddSlashes(pg_result($resaco,$iresaco,'v46_isencao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011129,1015320,'','".AddSlashes(pg_result($resaco,$iresaco,'v46_cadcalc'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011129,1015321,'','".AddSlashes(pg_result($resaco,$iresaco,'v46_percentual'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from isencaocalc
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($v46_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " v46_sequencial = $v46_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Cálculos tipo de isenção não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$v46_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Cálculos tipo de isenção não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$v46_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$v46_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:isencaocalc";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($v46_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from isencaocalc ";
     $sql .= "      inner join cadcalc  on  cadcalc.q85_codigo = isencaocalc.v46_cadcalc";
     $sql .= "      left join isencaotipo  on  isencaotipo.v11_sequencial = isencaocalc.v46_isencao";
     $sql .= "      left join cadvencdesc  on  cadvencdesc.q92_codigo = cadcalc.q85_codven";
     $sql .= "      left join forcaldesc  on  forcaldesc.q87_codigo = cadcalc.q85_forcal";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($v46_sequencial)) {
         $sql2 .= " where isencaocalc.v46_sequencial = $v46_sequencial "; 
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

    public function sql_query_file($v46_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from isencaocalc ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($v46_sequencial)){
         $sql2 .= " where isencaocalc.v46_sequencial = $v46_sequencial "; 
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
