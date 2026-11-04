<?php

class cl_lab_labsetor
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
    public $la24_i_codigo = 0; 
    public $la24_i_resp = 0; 
    public $la24_i_setor = 0; 
    public $la24_i_laboratorio = 0; 
    public $la24_o_assinatura = null; 
    public $la24_c_nomearq = null; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 la24_i_codigo = int4 = Código 
                 la24_i_resp = int4 = Responsável 
                 la24_i_setor = int4 = Setor 
                 la24_i_laboratorio = int4 = Laboratório 
                 la24_o_assinatura = oid = Assinatura 
                 la24_c_nomearq = char(50) = Nome do arquivo 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("lab_labsetor"); 
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
       $this->la24_i_codigo = ($this->la24_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["la24_i_codigo"]:$this->la24_i_codigo);
       $this->la24_i_resp = ($this->la24_i_resp == ""?@$GLOBALS["HTTP_POST_VARS"]["la24_i_resp"]:$this->la24_i_resp);
       $this->la24_i_setor = ($this->la24_i_setor == ""?@$GLOBALS["HTTP_POST_VARS"]["la24_i_setor"]:$this->la24_i_setor);
       $this->la24_i_laboratorio = ($this->la24_i_laboratorio == ""?@$GLOBALS["HTTP_POST_VARS"]["la24_i_laboratorio"]:$this->la24_i_laboratorio);
       $this->la24_o_assinatura = ($this->la24_o_assinatura == "" && strlen(@$GLOBALS["HTTP_POST_VARS"]["la24_o_assinatura"]) > 0?@$GLOBALS["HTTP_POST_VARS"]["la24_o_assinatura"]:$this->la24_o_assinatura);
       $this->la24_c_nomearq = ($this->la24_c_nomearq == ""?@$GLOBALS["HTTP_POST_VARS"]["la24_c_nomearq"]:$this->la24_c_nomearq);
     }else{
       $this->la24_i_codigo = ($this->la24_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["la24_i_codigo"]:$this->la24_i_codigo);
     }
   }

    public function incluir($la24_i_codigo)
    {
      $this->atualizacampos();
     if($this->la24_i_resp == null ){ 
       $this->erro_sql = " Campo Responsável não informado.";
       $this->erro_campo = "la24_i_resp";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->la24_i_setor == null ){ 
       $this->erro_sql = " Campo Setor não informado.";
       $this->erro_campo = "la24_i_setor";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->la24_i_laboratorio == null ){ 
       $this->erro_sql = " Campo Laboratório não informado.";
       $this->erro_campo = "la24_i_laboratorio";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($la24_i_codigo == "" || $la24_i_codigo == null ){
       $result = db_query("select nextval('lab_labsetor_la24_i_codigo_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: lab_labsetor_la24_i_codigo_seq do campo: la24_i_codigo"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->la24_i_codigo = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from lab_labsetor_la24_i_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $la24_i_codigo)){
         $this->erro_sql = " Campo la24_i_codigo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->la24_i_codigo = $la24_i_codigo; 
       }
     }
     if(($this->la24_i_codigo == null) || ($this->la24_i_codigo == "") ){ 
       $this->erro_sql = " Campo la24_i_codigo não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->la24_o_assinatura == null){
       $this->la24_o_assinatura = 'NULL';
     }
     $sql = "insert into lab_labsetor(
                                       la24_i_codigo 
                                      ,la24_i_resp 
                                      ,la24_i_setor 
                                      ,la24_i_laboratorio 
                                      ,la24_o_assinatura 
                                      ,la24_c_nomearq 
                       )
                values (
                                $this->la24_i_codigo 
                               ,$this->la24_i_resp 
                               ,$this->la24_i_setor 
                               ,$this->la24_i_laboratorio 
                               ,$this->la24_o_assinatura 
                               ,'$this->la24_c_nomearq' 
                      )";
                      
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "lab_labsetor ($this->la24_i_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "lab_labsetor já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "lab_labsetor ($this->la24_i_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->la24_i_codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->la24_i_codigo  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,15794,'$this->la24_i_codigo','I')");
         $resac = db_query("insert into db_acount values($acount,2775,15794,'','".AddSlashes(pg_result($resaco,0,'la24_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2775,15795,'','".AddSlashes(pg_result($resaco,0,'la24_i_resp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2775,15796,'','".AddSlashes(pg_result($resaco,0,'la24_i_setor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2775,15797,'','".AddSlashes(pg_result($resaco,0,'la24_i_laboratorio'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2775,15798,'','".AddSlashes(pg_result($resaco,0,'la24_o_assinatura'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2775,16198,'','".AddSlashes(pg_result($resaco,0,'la24_c_nomearq'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($la24_i_codigo=null)
    {
      $this->atualizacampos();
     $sql = " update lab_labsetor set ";
     $virgula = "";
     if(trim($this->la24_i_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["la24_i_codigo"])){ 
       $sql  .= $virgula." la24_i_codigo = $this->la24_i_codigo ";
       $virgula = ",";
       if(trim($this->la24_i_codigo) == null ){ 
         $this->erro_sql = " Campo Código não informado.";
         $this->erro_campo = "la24_i_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->la24_i_resp)!="" || isset($GLOBALS["HTTP_POST_VARS"]["la24_i_resp"])){ 
       $sql  .= $virgula." la24_i_resp = $this->la24_i_resp ";
       $virgula = ",";
       if(trim($this->la24_i_resp) == null ){ 
         $this->erro_sql = " Campo Responsável não informado.";
         $this->erro_campo = "la24_i_resp";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->la24_i_setor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["la24_i_setor"])){ 
       $sql  .= $virgula." la24_i_setor = $this->la24_i_setor ";
       $virgula = ",";
       if(trim($this->la24_i_setor) == null ){ 
         $this->erro_sql = " Campo Setor não informado.";
         $this->erro_campo = "la24_i_setor";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->la24_i_laboratorio)!="" || isset($GLOBALS["HTTP_POST_VARS"]["la24_i_laboratorio"])){ 
       $sql  .= $virgula." la24_i_laboratorio = $this->la24_i_laboratorio ";
       $virgula = ",";
       if(trim($this->la24_i_laboratorio) == null ){ 
         $this->erro_sql = " Campo Laboratório não informado.";
         $this->erro_campo = "la24_i_laboratorio";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->la24_o_assinatura)!="" || isset($GLOBALS["HTTP_POST_VARS"]["la24_o_assinatura"])){ 
       $sql  .= $virgula." la24_o_assinatura = $this->la24_o_assinatura ";
       $virgula = ",";
     }
     if(trim($this->la24_c_nomearq)!="" || isset($GLOBALS["HTTP_POST_VARS"]["la24_c_nomearq"])){ 
       $sql  .= $virgula." la24_c_nomearq = '$this->la24_c_nomearq' ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($la24_i_codigo!=null){
       $sql .= " la24_i_codigo = $this->la24_i_codigo";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->la24_i_codigo));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,15794,'$this->la24_i_codigo','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["la24_i_codigo"]) || $this->la24_i_codigo != "")
             $resac = db_query("insert into db_acount values($acount,2775,15794,'".AddSlashes(pg_result($resaco,$conresaco,'la24_i_codigo'))."','$this->la24_i_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["la24_i_resp"]) || $this->la24_i_resp != "")
             $resac = db_query("insert into db_acount values($acount,2775,15795,'".AddSlashes(pg_result($resaco,$conresaco,'la24_i_resp'))."','$this->la24_i_resp',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["la24_i_setor"]) || $this->la24_i_setor != "")
             $resac = db_query("insert into db_acount values($acount,2775,15796,'".AddSlashes(pg_result($resaco,$conresaco,'la24_i_setor'))."','$this->la24_i_setor',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["la24_i_laboratorio"]) || $this->la24_i_laboratorio != "")
             $resac = db_query("insert into db_acount values($acount,2775,15797,'".AddSlashes(pg_result($resaco,$conresaco,'la24_i_laboratorio'))."','$this->la24_i_laboratorio',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["la24_o_assinatura"]) || $this->la24_o_assinatura != "")
             $resac = db_query("insert into db_acount values($acount,2775,15798,'".AddSlashes(pg_result($resaco,$conresaco,'la24_o_assinatura'))."','$this->la24_o_assinatura',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["la24_c_nomearq"]) || $this->la24_c_nomearq != "")
             $resac = db_query("insert into db_acount values($acount,2775,16198,'".AddSlashes(pg_result($resaco,$conresaco,'la24_c_nomearq'))."','$this->la24_c_nomearq',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "lab_labsetor não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->la24_i_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "lab_labsetor não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->la24_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->la24_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($la24_i_codigo=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($la24_i_codigo));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,15794,'$la24_i_codigo','E')");
           $resac  = db_query("insert into db_acount values($acount,2775,15794,'','".AddSlashes(pg_result($resaco,$iresaco,'la24_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2775,15795,'','".AddSlashes(pg_result($resaco,$iresaco,'la24_i_resp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2775,15796,'','".AddSlashes(pg_result($resaco,$iresaco,'la24_i_setor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2775,15797,'','".AddSlashes(pg_result($resaco,$iresaco,'la24_i_laboratorio'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2775,15798,'','".AddSlashes(pg_result($resaco,$iresaco,'la24_o_assinatura'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2775,16198,'','".AddSlashes(pg_result($resaco,$iresaco,'la24_c_nomearq'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from lab_labsetor
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($la24_i_codigo)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " la24_i_codigo = $la24_i_codigo ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "lab_labsetor não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$la24_i_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "lab_labsetor não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$la24_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$la24_i_codigo;
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
        $this->erro_sql   = "Record Vazio na Tabela:lab_labsetor";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($la24_i_codigo = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from lab_labsetor ";
     $sql .= "      inner join lab_laboratorio  on  lab_laboratorio.la02_i_codigo = lab_labsetor.la24_i_laboratorio";
     $sql .= "      inner join lab_labresp  on  lab_labresp.la06_i_codigo = lab_labsetor.la24_i_resp";
     $sql .= "      inner join lab_setor  on  lab_setor.la23_i_codigo = lab_labsetor.la24_i_setor";
     $sql .= "      left  join sau_turnoatend  on  sau_turnoatend.sd43_cod_turnat = lab_laboratorio.la02_i_turnoatend";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = lab_labresp.la06_i_cgm";
     $sql .= "      left  join db_uf  on  db_uf.db12_codigo = lab_labresp.la06_i_uf";
     $sql .= "      inner join rhcbo  on  rhcbo.rh70_sequencial = lab_labresp.la06_i_cbo";
     //$sql .= "      inner join lab_laboratorio  on  lab_laboratorio.la02_i_codigo = lab_labresp.la06_i_laboratorio";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($la24_i_codigo)) {
         $sql2 .= " where lab_labsetor.la24_i_codigo = $la24_i_codigo "; 
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

    public function sql_query_file($la24_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from lab_labsetor ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($la24_i_codigo)){
         $sql2 .= " where lab_labsetor.la24_i_codigo = $la24_i_codigo "; 
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

  public function sql_query2 ( $la24_i_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = explode("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from lab_labsetor ";
     $sql .= "      inner join lab_laboratorio  on  lab_laboratorio.la02_i_codigo = lab_labsetor.la24_i_laboratorio";
     $sql .= "      inner join lab_labresp  on  lab_labresp.la06_i_codigo = lab_labsetor.la24_i_resp";
     $sql .= "      inner join lab_setor  on  lab_setor.la23_i_codigo = lab_labsetor.la24_i_setor";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = lab_labresp.la06_i_cgm";
     $sql .= "      left  join db_uf  on  db_uf.db12_codigo = lab_labresp.la06_i_uf";
     $sql .= "      inner join rhcbo  on  rhcbo.rh70_sequencial = lab_labresp.la06_i_cbo";
     //$sql .= "      inner join lab_laboratorio  on  lab_laboratorio.la02_i_codigo = lab_labresp.la06_i_laboratorio";
     $sql2 = "";
     if($dbwhere==""){
       if($la24_i_codigo!=null ){
         $sql2 .= " where lab_labsetor.la24_i_codigo = $la24_i_codigo ";
       } 
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = explode("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }

  public function sql_query_cgm_lab_setor ( $la24_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "" ) {

    $sql = "select ";
    if( $campos != "*" ) {

      $campos_sql = explode("#",$campos);
      $virgula    = "";

      for( $i = 0; $i < sizeof( $campos_sql ); $i++ ) {

        $sql     .= $virgula.$campos_sql[$i];
        $virgula  = ",";
      }
    } else {
      $sql .= $campos;
    }

    $sql .= " from lab_labsetor ";
    $sql .= "      inner join lab_setorexame  on lab_setorexame.la09_i_labsetor   = lab_labsetor.la24_i_codigo";
    $sql .= "      inner join lab_requiitem   on lab_requiitem.la21_i_setorexame  = lab_setorexame.la09_i_codigo";
    $sql .= "      inner join lab_conferencia on lab_conferencia.la47_i_requiitem = lab_requiitem.la21_i_codigo";
    $sql .= "      inner join lab_labresp     on lab_labresp.la06_i_codigo        = lab_labsetor.la24_i_resp";

    $sql2 = "";

    if( $dbwhere == "" ) {

      if( $la24_i_codigo != null ) {
        $sql2 .= " where lab_labsetor.la24_i_codigo = $la24_i_codigo ";
      }
    } else if( $dbwhere != "" ) {
      $sql2 = " where $dbwhere";
    }

    $sql .= $sql2;

    if( $ordem != null ) {

      $sql        .= " order by ";
      $campos_sql  = explode( "#", $ordem );
      $virgula     = "";

      for( $i = 0; $i < sizeof( $campos_sql ); $i++ ) {

        $sql     .= $virgula.$campos_sql[$i];
        $virgula  = ",";
      }
    }

    return $sql;
  }

  public function sql_query_conferencia ( $la24_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "" ) {

    $sql = "select ";
    if( $campos != "*" ) {

      $campos_sql = explode("#",$campos);
      $virgula    = "";

      for( $i = 0; $i < sizeof( $campos_sql ); $i++ ) {

        $sql     .= $virgula.$campos_sql[$i];
        $virgula  = ",";
      }
    } else {
      $sql .= $campos;
    }

    $sql .= " from lab_labsetor ";
    $sql .= "      inner join lab_labresp     on lab_labresp.la06_i_codigo = lab_labsetor.la24_i_resp ";
    $sql .= "      inner join lab_laboratorio on la02_i_codigo = la24_i_laboratorio ";
    $sql .= "                                and la02_i_codigo = la06_i_laboratorio ";
    $sql .= "      inner join lab_labdepart   on la03_i_laboratorio = la02_i_codigo ";
    $sql .= "      inner join db_usuacgm      on db_usuacgm.cgmlogin = lab_labresp.la06_i_cgm ";


    $sql2 = "";

    if( $dbwhere == "" ) {

      if( $la24_i_codigo != null ) {
        $sql2 .= " where lab_labsetor.la24_i_codigo = $la24_i_codigo ";
      }
    } else if( $dbwhere != "" ) {
      $sql2 = " where $dbwhere";
    }

    $sql .= $sql2;

    if( $ordem != null ) {

      $sql        .= " order by ";
      $campos_sql  = explode( "#", $ordem );
      $virgula     = "";

      for( $i = 0; $i < sizeof( $campos_sql ); $i++ ) {

        $sql     .= $virgula.$campos_sql[$i];
        $virgula  = ",";
      }
    }

    return $sql;
  }

  /**
   * Query contendo ligação com a requisição
   */
  public function sql_query_requisicao ( $la24_i_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = explode("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from lab_labsetor ";
     $sql .= "      inner join lab_laboratorio  on  lab_laboratorio.la02_i_codigo = lab_labsetor.la24_i_laboratorio";
     $sql .= "      inner join lab_labresp  on  lab_labresp.la06_i_codigo = lab_labsetor.la24_i_resp";
     $sql .= "      inner join lab_setor  on  lab_setor.la23_i_codigo = lab_labsetor.la24_i_setor";
     $sql .= "      left  join sau_turnoatend  on  sau_turnoatend.sd43_cod_turnat = lab_laboratorio.la02_i_turnoatend";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = lab_labresp.la06_i_cgm";
     $sql .= "      left  join db_uf  on  db_uf.db12_codigo = lab_labresp.la06_i_uf";
     $sql .= "      inner join rhcbo  on  rhcbo.rh70_sequencial = lab_labresp.la06_i_cbo";
     $sql .= "      inner join lab_setorexame  on lab_setorexame.la09_i_labsetor   = lab_labsetor.la24_i_codigo";
     $sql .= "      inner join lab_requiitem   on lab_requiitem.la21_i_setorexame  = lab_setorexame.la09_i_codigo";
     $sql2 = "";
     if($dbwhere==""){
       if($la24_i_codigo!=null ){
         $sql2 .= " where lab_labsetor.la24_i_codigo = $la24_i_codigo ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = explode("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
}
?>
