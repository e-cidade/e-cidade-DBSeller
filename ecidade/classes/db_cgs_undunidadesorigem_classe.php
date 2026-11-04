<?php

class cl_cgs_undunidadesorigem
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
    public $sd114_sequencial = 0; 
    public $sd114_cgs_und = 0; 
    public $sd114_unidadesorigem = null; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 sd114_sequencial = int4 = Sequencial 
                 sd114_cgs_und = int4 = Cgs 
                 sd114_unidadesorigem = int4 = Unidade Origem 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("cgs_undunidadesorigem"); 
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
       $this->sd114_sequencial = ($this->sd114_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["sd114_sequencial"]:$this->sd114_sequencial);
       $this->sd114_cgs_und = ($this->sd114_cgs_und == ""?@$GLOBALS["HTTP_POST_VARS"]["sd114_cgs_und"]:$this->sd114_cgs_und);
       $this->sd114_unidadesorigem = ($this->sd114_unidadesorigem == ""?@$GLOBALS["HTTP_POST_VARS"]["sd114_unidadesorigem"]:$this->sd114_unidadesorigem);
     }else{
       $this->sd114_sequencial = ($this->sd114_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["sd114_sequencial"]:$this->sd114_sequencial);
     }
   }

    public function incluir($sd114_sequencial)
    {
      $this->atualizacampos();
     if($sd114_sequencial == "" || $sd114_sequencial == null ){
       $result = db_query("select nextval('cgs_undunidadesorigem_sd114_sequencial_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: cgs_undunidadesorigem_sd114_sequencial_seq do campo: sd114_sequencial"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->sd114_sequencial = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from cgs_undunidadesorigem_sd114_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $sd114_sequencial)){
         $this->erro_sql = " Campo sd114_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->sd114_sequencial = $sd114_sequencial; 
       }
     }
     if(($this->sd114_sequencial == null) || ($this->sd114_sequencial == "") ){ 
       $this->erro_sql = " Campo sd114_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->sd114_unidadesorigem == null || $this->sd114_unidadesorigem == ""){
      $this->sd114_unidadesorigem = 'null';
     }
     $sql = "insert into cgs_undunidadesorigem(
                                       sd114_sequencial 
                                      ,sd114_cgs_und 
                                      ,sd114_unidadesorigem 
                       )
                values (
                                $this->sd114_sequencial 
                               ,$this->sd114_cgs_und 
                               ,$this->sd114_unidadesorigem 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Unidade Origem CGS ($this->sd114_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Unidade Origem CGS já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Unidade Origem CGS ($this->sd114_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->sd114_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     return true;
   } 

    public function alterar($sd114_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update cgs_undunidadesorigem set ";
     $virgula = "";
     if(trim($this->sd114_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["sd114_sequencial"])){ 
       $sql  .= $virgula." sd114_sequencial = $this->sd114_sequencial ";
       $virgula = ",";
       if(trim($this->sd114_sequencial) == null ){ 
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "sd114_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->sd114_cgs_und)!="" || isset($GLOBALS["HTTP_POST_VARS"]["sd114_cgs_und"])){ 
        if(trim($this->sd114_cgs_und)=="" && isset($GLOBALS["HTTP_POST_VARS"]["sd114_cgs_und"])){ 
           $this->sd114_cgs_und = "0" ; 
        } 
       $sql  .= $virgula." sd114_cgs_und = $this->sd114_cgs_und ";
       $virgula = ",";
     }
     if(trim($this->sd114_unidadesorigem)!="" || isset($GLOBALS["HTTP_POST_VARS"]["sd114_unidadesorigem"])){ 
        if(trim($this->sd114_unidadesorigem)=="" && isset($GLOBALS["HTTP_POST_VARS"]["sd114_unidadesorigem"])){ 
           $this->sd114_unidadesorigem = "0" ; 
        } 
       $sql  .= $virgula." sd114_unidadesorigem = $this->sd114_unidadesorigem ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($sd114_sequencial!=null){
       $sql .= " sd114_sequencial = $this->sd114_sequencial";
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Unidade Origem CGS não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->sd114_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Unidade Origem CGS não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->sd114_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->sd114_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($sd114_sequencial=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($sd114_sequencial));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
     }
     $sql = " delete from cgs_undunidadesorigem
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($sd114_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " sd114_sequencial = $sd114_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Unidade Origem CGS não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$sd114_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Unidade Origem CGS não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$sd114_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$sd114_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:cgs_undunidadesorigem";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($sd114_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from cgs_undunidadesorigem ";
     $sql .= "      left  join cgs_und  on  cgs_und.z01_i_cgsund = cgs_undunidadesorigem.sd114_cgs_und";
     $sql .= "      left  join unidadesorigem  on  unidadesorigem.sd113_sequencial = cgs_undunidadesorigem.sd114_unidadesorigem";
     $sql .= "      left  join familiamicroarea  on  familiamicroarea.sd35_i_codigo = cgs_und.z01_i_familiamicroarea";
     $sql .= "      inner join sau_distritosanitario  on  sau_distritosanitario.s153_i_codigo = cgs_und.z01_distritosanitario";
     $sql .= "      inner join cgs  on  cgs.z01_i_numcgs = cgs_und.z01_i_cgsund";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($sd114_sequencial)) {
         $sql2 .= " where cgs_undunidadesorigem.sd114_sequencial = $sd114_sequencial "; 
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

    public function sql_query_file($sd114_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from cgs_undunidadesorigem ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($sd114_sequencial)){
         $sql2 .= " where cgs_undunidadesorigem.sd114_sequencial = $sd114_sequencial "; 
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
