<?php

class cl_operadorasaude
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
    public $rh221_sequencial = 0; 
    public $rh221_cgm = 0; 
    public $rh221_ativo = 'f'; 
    public $rh221_ans = 0; 
    public $rh221_instituicao = 0; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 rh221_sequencial = int4 = Sequencial 
                 rh221_cgm = int4 = Operadora 
                 rh221_ativo = bool = Ativo 
                 rh221_ans = int4 = ANS 
                 rh221_instituicao = int4 = Código da Instituição 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("operadorasaude"); 
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
       $this->rh221_sequencial = ($this->rh221_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["rh221_sequencial"]:$this->rh221_sequencial);
       $this->rh221_cgm = ($this->rh221_cgm == ""?@$GLOBALS["HTTP_POST_VARS"]["rh221_cgm"]:$this->rh221_cgm);
       $this->rh221_ativo = ($this->rh221_ativo == "f"?@$GLOBALS["HTTP_POST_VARS"]["rh221_ativo"]:$this->rh221_ativo);
       $this->rh221_ans = ($this->rh221_ans == ""?@$GLOBALS["HTTP_POST_VARS"]["rh221_ans"]:$this->rh221_ans);
       $this->rh221_instituicao = ($this->rh221_instituicao == ""?@$GLOBALS["HTTP_POST_VARS"]["rh221_instituicao"]:$this->rh221_instituicao);
     }else{
       $this->rh221_sequencial = ($this->rh221_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["rh221_sequencial"]:$this->rh221_sequencial);
     }
   }

    public function incluir($rh221_sequencial)
    {
      $this->atualizacampos();
     if($this->rh221_cgm == null ){ 
       $this->erro_sql = " Campo Operadora não informado.";
       $this->erro_campo = "rh221_cgm";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh221_ativo == null ){ 
       $this->erro_sql = " Campo Ativo não informado.";
       $this->erro_campo = "rh221_ativo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh221_ans == null ){ 
       $this->rh221_ans = "0";
     }
     if($this->rh221_instituicao == null ){ 
       $this->erro_sql = " Campo Código da Instituição não informado.";
       $this->erro_campo = "rh221_instituicao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($rh221_sequencial == "" || $rh221_sequencial == null ){
       $result = db_query("select nextval('operadorasaude_rh221_sequencial_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: operadorasaude_rh221_sequencial_seq do campo: rh221_sequencial"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->rh221_sequencial = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from operadorasaude_rh221_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $rh221_sequencial)){
         $this->erro_sql = " Campo rh221_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->rh221_sequencial = $rh221_sequencial; 
       }
     }
     if(($this->rh221_sequencial == null) || ($this->rh221_sequencial == "") ){ 
       $this->erro_sql = " Campo rh221_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into operadorasaude(
                                       rh221_sequencial 
                                      ,rh221_cgm 
                                      ,rh221_ativo 
                                      ,rh221_ans 
                                      ,rh221_instituicao 
                       )
                values (
                                $this->rh221_sequencial 
                               ,$this->rh221_cgm 
                               ,'$this->rh221_ativo' 
                               ,$this->rh221_ans 
                               ,$this->rh221_instituicao 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Operadora de Saúde ($this->rh221_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Operadora de Saúde já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Operadora de Saúde ($this->rh221_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->rh221_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->rh221_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1010048,'$this->rh221_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,1010333,1010048,'','".AddSlashes(pg_result($resaco,0,'rh221_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010333,1010069,'','".AddSlashes(pg_result($resaco,0,'rh221_cgm'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010333,1010052,'','".AddSlashes(pg_result($resaco,0,'rh221_ativo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010333,1010051,'','".AddSlashes(pg_result($resaco,0,'rh221_ans'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010333,1015598,'','".AddSlashes(pg_result($resaco,0,'rh221_instituicao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($rh221_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update operadorasaude set ";
     $virgula = "";
     if(trim($this->rh221_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh221_sequencial"])){ 
       $sql  .= $virgula." rh221_sequencial = $this->rh221_sequencial ";
       $virgula = ",";
       if(trim($this->rh221_sequencial) == null ){ 
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "rh221_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh221_cgm)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh221_cgm"])){ 
       $sql  .= $virgula." rh221_cgm = $this->rh221_cgm ";
       $virgula = ",";
       if(trim($this->rh221_cgm) == null ){ 
         $this->erro_sql = " Campo Operadora não informado.";
         $this->erro_campo = "rh221_cgm";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh221_ativo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh221_ativo"])){ 
       $sql  .= $virgula." rh221_ativo = '$this->rh221_ativo' ";
       $virgula = ",";
       if(trim($this->rh221_ativo) == null ){ 
         $this->erro_sql = " Campo Ativo não informado.";
         $this->erro_campo = "rh221_ativo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh221_ans)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh221_ans"])){ 
        if(trim($this->rh221_ans)=="" && isset($GLOBALS["HTTP_POST_VARS"]["rh221_ans"])){ 
           $this->rh221_ans = "0" ; 
        } 
       $sql  .= $virgula." rh221_ans = $this->rh221_ans ";
       $virgula = ",";
     }
     if(trim($this->rh221_instituicao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh221_instituicao"])){ 
       $sql  .= $virgula." rh221_instituicao = $this->rh221_instituicao ";
       $virgula = ",";
       if(trim($this->rh221_instituicao) == null ){ 
         $this->erro_sql = " Campo Código da Instituição não informado.";
         $this->erro_campo = "rh221_instituicao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($rh221_sequencial!=null){
       $sql .= " rh221_sequencial = $this->rh221_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->rh221_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1010048,'$this->rh221_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh221_sequencial"]) || $this->rh221_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,1010333,1010048,'".AddSlashes(pg_result($resaco,$conresaco,'rh221_sequencial'))."','$this->rh221_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh221_cgm"]) || $this->rh221_cgm != "")
             $resac = db_query("insert into db_acount values($acount,1010333,1010069,'".AddSlashes(pg_result($resaco,$conresaco,'rh221_cgm'))."','$this->rh221_cgm',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh221_ativo"]) || $this->rh221_ativo != "")
             $resac = db_query("insert into db_acount values($acount,1010333,1010052,'".AddSlashes(pg_result($resaco,$conresaco,'rh221_ativo'))."','$this->rh221_ativo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh221_ans"]) || $this->rh221_ans != "")
             $resac = db_query("insert into db_acount values($acount,1010333,1010051,'".AddSlashes(pg_result($resaco,$conresaco,'rh221_ans'))."','$this->rh221_ans',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh221_instituicao"]) || $this->rh221_instituicao != "")
             $resac = db_query("insert into db_acount values($acount,1010333,1015598,'".AddSlashes(pg_result($resaco,$conresaco,'rh221_instituicao'))."','$this->rh221_instituicao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Operadora de Saúde não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->rh221_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Operadora de Saúde não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->rh221_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->rh221_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($rh221_sequencial=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($rh221_sequencial));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1010048,'$rh221_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,1010333,1010048,'','".AddSlashes(pg_result($resaco,$iresaco,'rh221_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010333,1010069,'','".AddSlashes(pg_result($resaco,$iresaco,'rh221_cgm'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010333,1010052,'','".AddSlashes(pg_result($resaco,$iresaco,'rh221_ativo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010333,1010051,'','".AddSlashes(pg_result($resaco,$iresaco,'rh221_ans'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010333,1015598,'','".AddSlashes(pg_result($resaco,$iresaco,'rh221_instituicao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from operadorasaude
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($rh221_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " rh221_sequencial = $rh221_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Operadora de Saúde não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$rh221_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Operadora de Saúde não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$rh221_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$rh221_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:operadorasaude";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($rh221_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from operadorasaude ";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = operadorasaude.rh221_cgm";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($rh221_sequencial)) {
         $sql2 .= " where operadorasaude.rh221_sequencial = $rh221_sequencial "; 
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

    public function sql_query_file($rh221_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from operadorasaude ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($rh221_sequencial)){
         $sql2 .= " where operadorasaude.rh221_sequencial = $rh221_sequencial "; 
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
