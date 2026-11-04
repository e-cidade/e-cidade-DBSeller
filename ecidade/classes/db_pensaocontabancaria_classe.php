<?php

class cl_pensaocontabancaria
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
    public $rh139_sequencial = 0; 
    public $rh139_regist = 0; 
    public $rh139_numcgm = 0; 
    public $rh139_anousu = 0; 
    public $rh139_mesusu = 0; 
    public $rh139_contabancaria = 0; 
    public $rh139_cgmalimentado = 0; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 rh139_sequencial = int4 = Sequencial 
                 rh139_regist = int4 = Regist 
                 rh139_numcgm = int4 = Nmero CGM 
                 rh139_anousu = int4 = Ano 
                 rh139_mesusu = int4 = Ms 
                 rh139_contabancaria = int4 = Conta Bancria 
                 rh139_cgmalimentado = int4 = CGM Alimentado 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("pensaocontabancaria"); 
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
       $this->rh139_sequencial = ($this->rh139_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["rh139_sequencial"]:$this->rh139_sequencial);
       $this->rh139_regist = ($this->rh139_regist == ""?@$GLOBALS["HTTP_POST_VARS"]["rh139_regist"]:$this->rh139_regist);
       $this->rh139_numcgm = ($this->rh139_numcgm == ""?@$GLOBALS["HTTP_POST_VARS"]["rh139_numcgm"]:$this->rh139_numcgm);
       $this->rh139_anousu = ($this->rh139_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["rh139_anousu"]:$this->rh139_anousu);
       $this->rh139_mesusu = ($this->rh139_mesusu == ""?@$GLOBALS["HTTP_POST_VARS"]["rh139_mesusu"]:$this->rh139_mesusu);
       $this->rh139_contabancaria = ($this->rh139_contabancaria == ""?@$GLOBALS["HTTP_POST_VARS"]["rh139_contabancaria"]:$this->rh139_contabancaria);
       $this->rh139_cgmalimentado = ($this->rh139_cgmalimentado == ""?@$GLOBALS["HTTP_POST_VARS"]["rh139_cgmalimentado"]:$this->rh139_cgmalimentado);
     }else{
       $this->rh139_sequencial = ($this->rh139_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["rh139_sequencial"]:$this->rh139_sequencial);
     }
   }

    public function incluir($rh139_sequencial)
    {
      $this->atualizacampos();
     if($this->rh139_regist == null ){ 
       $this->erro_sql = " Campo Regist não informado.";
       $this->erro_campo = "rh139_regist";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh139_numcgm == null ){ 
       $this->erro_sql = " Campo Nmero CGM não informado.";
       $this->erro_campo = "rh139_numcgm";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh139_anousu == null ){ 
       $this->erro_sql = " Campo Ano não informado.";
       $this->erro_campo = "rh139_anousu";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh139_mesusu == null ){ 
       $this->erro_sql = " Campo Ms não informado.";
       $this->erro_campo = "rh139_mesusu";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh139_contabancaria == null ){ 
       $this->rh139_contabancaria = "0";
     }
     if($this->rh139_cgmalimentado == null ){ 
       $this->erro_sql = " Campo CGM Alimentado não informado.";
       $this->erro_campo = "rh139_cgmalimentado";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
       $this->rh139_sequencial = $rh139_sequencial; 
     if(($this->rh139_sequencial == null) || ($this->rh139_sequencial == "") ){ 
       $this->erro_sql = " Campo rh139_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into pensaocontabancaria(
                                       rh139_sequencial 
                                      ,rh139_regist 
                                      ,rh139_numcgm 
                                      ,rh139_anousu 
                                      ,rh139_mesusu 
                                      ,rh139_contabancaria 
                                      ,rh139_cgmalimentado 
                       )
                values (
                                $this->rh139_sequencial 
                               ,$this->rh139_regist 
                               ,$this->rh139_numcgm 
                               ,$this->rh139_anousu 
                               ,$this->rh139_mesusu 
                               ,$this->rh139_contabancaria 
                               ,$this->rh139_cgmalimentado 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "pensaocontabancaria ($this->rh139_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "pensaocontabancaria já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "pensaocontabancaria ($this->rh139_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->rh139_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->rh139_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,20671,'$this->rh139_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,3721,20671,'','".AddSlashes(pg_result($resaco,0,'rh139_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3721,20672,'','".AddSlashes(pg_result($resaco,0,'rh139_regist'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3721,20673,'','".AddSlashes(pg_result($resaco,0,'rh139_numcgm'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3721,20675,'','".AddSlashes(pg_result($resaco,0,'rh139_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3721,20676,'','".AddSlashes(pg_result($resaco,0,'rh139_mesusu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3721,20677,'','".AddSlashes(pg_result($resaco,0,'rh139_contabancaria'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3721,240193912,'','".AddSlashes(pg_result($resaco,0,'rh139_cgmalimentado'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($rh139_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update pensaocontabancaria set ";
     $virgula = "";
     if(trim($this->rh139_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh139_sequencial"])){ 
       $sql  .= $virgula." rh139_sequencial = $this->rh139_sequencial ";
       $virgula = ",";
       if(trim($this->rh139_sequencial) == null ){ 
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "rh139_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh139_regist)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh139_regist"])){ 
       $sql  .= $virgula." rh139_regist = $this->rh139_regist ";
       $virgula = ",";
       if(trim($this->rh139_regist) == null ){ 
         $this->erro_sql = " Campo Regist não informado.";
         $this->erro_campo = "rh139_regist";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh139_numcgm)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh139_numcgm"])){ 
       $sql  .= $virgula." rh139_numcgm = $this->rh139_numcgm ";
       $virgula = ",";
       if(trim($this->rh139_numcgm) == null ){ 
         $this->erro_sql = " Campo Nmero CGM não informado.";
         $this->erro_campo = "rh139_numcgm";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh139_anousu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh139_anousu"])){ 
       $sql  .= $virgula." rh139_anousu = $this->rh139_anousu ";
       $virgula = ",";
       if(trim($this->rh139_anousu) == null ){ 
         $this->erro_sql = " Campo Ano não informado.";
         $this->erro_campo = "rh139_anousu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh139_mesusu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh139_mesusu"])){ 
       $sql  .= $virgula." rh139_mesusu = $this->rh139_mesusu ";
       $virgula = ",";
       if(trim($this->rh139_mesusu) == null ){ 
         $this->erro_sql = " Campo Ms não informado.";
         $this->erro_campo = "rh139_mesusu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh139_contabancaria)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh139_contabancaria"])){ 
        if(trim($this->rh139_contabancaria)=="" && isset($GLOBALS["HTTP_POST_VARS"]["rh139_contabancaria"])){ 
           $this->rh139_contabancaria = "0" ; 
        } 
       $sql  .= $virgula." rh139_contabancaria = $this->rh139_contabancaria ";
       $virgula = ",";
     }
     if(trim($this->rh139_cgmalimentado)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh139_cgmalimentado"])){ 
       $sql  .= $virgula." rh139_cgmalimentado = $this->rh139_cgmalimentado ";
       $virgula = ",";
       if(trim($this->rh139_cgmalimentado) == null ){ 
         $this->erro_sql = " Campo CGM Alimentado não informado.";
         $this->erro_campo = "rh139_cgmalimentado";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($rh139_sequencial!=null){
       $sql .= " rh139_sequencial = $this->rh139_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->rh139_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,20671,'$this->rh139_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh139_sequencial"]) || $this->rh139_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,3721,20671,'".AddSlashes(pg_result($resaco,$conresaco,'rh139_sequencial'))."','$this->rh139_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh139_regist"]) || $this->rh139_regist != "")
             $resac = db_query("insert into db_acount values($acount,3721,20672,'".AddSlashes(pg_result($resaco,$conresaco,'rh139_regist'))."','$this->rh139_regist',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh139_numcgm"]) || $this->rh139_numcgm != "")
             $resac = db_query("insert into db_acount values($acount,3721,20673,'".AddSlashes(pg_result($resaco,$conresaco,'rh139_numcgm'))."','$this->rh139_numcgm',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh139_anousu"]) || $this->rh139_anousu != "")
             $resac = db_query("insert into db_acount values($acount,3721,20675,'".AddSlashes(pg_result($resaco,$conresaco,'rh139_anousu'))."','$this->rh139_anousu',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh139_mesusu"]) || $this->rh139_mesusu != "")
             $resac = db_query("insert into db_acount values($acount,3721,20676,'".AddSlashes(pg_result($resaco,$conresaco,'rh139_mesusu'))."','$this->rh139_mesusu',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh139_contabancaria"]) || $this->rh139_contabancaria != "")
             $resac = db_query("insert into db_acount values($acount,3721,20677,'".AddSlashes(pg_result($resaco,$conresaco,'rh139_contabancaria'))."','$this->rh139_contabancaria',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh139_cgmalimentado"]) || $this->rh139_cgmalimentado != "")
             $resac = db_query("insert into db_acount values($acount,3721,240193912,'".AddSlashes(pg_result($resaco,$conresaco,'rh139_cgmalimentado'))."','$this->rh139_cgmalimentado',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "pensaocontabancaria não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->rh139_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "pensaocontabancaria não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->rh139_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->rh139_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($rh139_sequencial=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($rh139_sequencial));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,20671,'$rh139_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,3721,20671,'','".AddSlashes(pg_result($resaco,$iresaco,'rh139_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,3721,20672,'','".AddSlashes(pg_result($resaco,$iresaco,'rh139_regist'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,3721,20673,'','".AddSlashes(pg_result($resaco,$iresaco,'rh139_numcgm'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,3721,20675,'','".AddSlashes(pg_result($resaco,$iresaco,'rh139_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,3721,20676,'','".AddSlashes(pg_result($resaco,$iresaco,'rh139_mesusu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,3721,20677,'','".AddSlashes(pg_result($resaco,$iresaco,'rh139_contabancaria'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,3721,240193912,'','".AddSlashes(pg_result($resaco,$iresaco,'rh139_cgmalimentado'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from pensaocontabancaria
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($rh139_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " rh139_sequencial = $rh139_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "pensaocontabancaria não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$rh139_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "pensaocontabancaria não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$rh139_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$rh139_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:pensaocontabancaria";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($rh139_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from pensaocontabancaria ";
     $sql .= "      left  join contabancaria  on  contabancaria.db83_sequencial = pensaocontabancaria.rh139_contabancaria";
     $sql .= "      inner join bancoagencia  on  bancoagencia.db89_sequencial = contabancaria.db83_bancoagencia";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($rh139_sequencial)) {
         $sql2 .= " where pensaocontabancaria.rh139_sequencial = $rh139_sequencial "; 
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

    public function sql_query_file($rh139_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from pensaocontabancaria ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($rh139_sequencial)){
         $sql2 .= " where pensaocontabancaria.rh139_sequencial = $rh139_sequencial "; 
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
