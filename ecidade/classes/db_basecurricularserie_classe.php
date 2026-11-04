<?php
//MODULO: secretariadeeducacao
//CLASSE DA ENTIDADE basecurricularserie
class cl_basecurricularserie { 
   // cria variaveis de erro 
   var $rotulo     = null; 
   var $query_sql  = null; 
   var $numrows    = 0; 
   var $numrows_incluir = 0; 
   var $numrows_alterar = 0; 
   var $numrows_excluir = 0; 
   var $erro_status= null; 
   var $erro_sql   = null; 
   var $erro_banco = null;  
   var $erro_msg   = null;  
   var $erro_campo = null;  
   var $pagina_retorno = null; 
   // cria variaveis do arquivo 
   var $ed142_sequencial = 0; 
   var $ed142_basecurricular = 0; 
   var $ed142_serie = 0; 
   // cria propriedade com as variaveis do arquivo 
   var $campos = "
                 ed142_sequencial = int4 = Código 
                 ed142_basecurricular = int4 = Base 
                 ed142_serie = int4 = Etapa 
                 ";
                 
   //funcao construtor da classe 
   public function __construct() { 
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("basecurricularserie"); 
     $this->pagina_retorno =  basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
   }
   //funcao erro 
   public function erro($mostra,$retorna) { 
     if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
        echo "<script>alert(\"".$this->erro_msg."\");</script>";
        if($retorna==true){
           echo "<script>location.href='".$this->pagina_retorno."'</script>";
        }
     }
   }
   // funcao para atualizar campos
   public function atualizacampos($exclusao=false) {
     if($exclusao==false){
       $this->ed142_sequencial = ($this->ed142_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["ed142_sequencial"]:$this->ed142_sequencial);
       $this->ed142_basecurricular = ($this->ed142_basecurricular == ""?@$GLOBALS["HTTP_POST_VARS"]["ed142_basecurricular"]:$this->ed142_basecurricular);
       $this->ed142_serie = ($this->ed142_serie == ""?@$GLOBALS["HTTP_POST_VARS"]["ed142_serie"]:$this->ed142_serie);
     }else{
       $this->ed142_sequencial = ($this->ed142_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["ed142_sequencial"]:$this->ed142_sequencial);
     }
   }
   // funcao para Inclusão
   public function incluir ($ed142_sequencial){ 
      $this->atualizacampos();
     if($this->ed142_basecurricular == null ){ 
       $this->erro_sql = " Campo Base não informado.";
       $this->erro_campo = "ed142_basecurricular";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed142_serie == null ){ 
       $this->erro_sql = " Campo Etapa não informado.";
       $this->erro_campo = "ed142_serie";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($ed142_sequencial == "" || $ed142_sequencial == null ){
       $result = db_query("select nextval('basecurricularserie_ed142_sequencial_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: basecurricularserie_ed142_sequencial_seq do campo: ed142_sequencial"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->ed142_sequencial = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from basecurricularserie_ed142_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $ed142_sequencial)){
         $this->erro_sql = " Campo ed142_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->ed142_sequencial = $ed142_sequencial; 
       }
     }
     if(($this->ed142_sequencial == null) || ($this->ed142_sequencial == "") ){ 
       $this->erro_sql = " Campo ed142_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into basecurricularserie(
                                       ed142_sequencial 
                                      ,ed142_basecurricular 
                                      ,ed142_serie 
                       )
                values (
                                $this->ed142_sequencial 
                               ,$this->ed142_basecurricular 
                               ,$this->ed142_serie 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Etapa da Base ($this->ed142_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Etapa da Base já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Etapa da Base ($this->ed142_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->ed142_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ed142_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,22352,'$this->ed142_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,4027,22352,'','".AddSlashes(pg_result($resaco,0,'ed142_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,4027,22353,'','".AddSlashes(pg_result($resaco,0,'ed142_basecurricular'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,4027,22354,'','".AddSlashes(pg_result($resaco,0,'ed142_serie'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 
   // funcao para alteracao
   public function alterar ($ed142_sequencial=null) { 
      $this->atualizacampos();
     $sql = " update basecurricularserie set ";
     $virgula = "";
     if(trim($this->ed142_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed142_sequencial"])){ 
       $sql  .= $virgula." ed142_sequencial = $this->ed142_sequencial ";
       $virgula = ",";
       if(trim($this->ed142_sequencial) == null ){ 
         $this->erro_sql = " Campo Código não informado.";
         $this->erro_campo = "ed142_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed142_basecurricular)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed142_basecurricular"])){ 
       $sql  .= $virgula." ed142_basecurricular = $this->ed142_basecurricular ";
       $virgula = ",";
       if(trim($this->ed142_basecurricular) == null ){ 
         $this->erro_sql = " Campo Base não informado.";
         $this->erro_campo = "ed142_basecurricular";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed142_serie)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed142_serie"])){ 
       $sql  .= $virgula." ed142_serie = $this->ed142_serie ";
       $virgula = ",";
       if(trim($this->ed142_serie) == null ){ 
         $this->erro_sql = " Campo Etapa não informado.";
         $this->erro_campo = "ed142_serie";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($ed142_sequencial!=null){
       $sql .= " ed142_sequencial = $this->ed142_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ed142_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,22352,'$this->ed142_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed142_sequencial"]) || $this->ed142_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,4027,22352,'".AddSlashes(pg_result($resaco,$conresaco,'ed142_sequencial'))."','$this->ed142_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed142_basecurricular"]) || $this->ed142_basecurricular != "")
             $resac = db_query("insert into db_acount values($acount,4027,22353,'".AddSlashes(pg_result($resaco,$conresaco,'ed142_basecurricular'))."','$this->ed142_basecurricular',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed142_serie"]) || $this->ed142_serie != "")
             $resac = db_query("insert into db_acount values($acount,4027,22354,'".AddSlashes(pg_result($resaco,$conresaco,'ed142_serie'))."','$this->ed142_serie',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Etapa da Base não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->ed142_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Etapa da Base não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->ed142_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->ed142_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   public function excluir ($ed142_sequencial=null,$dbwhere=null) { 

     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($ed142_sequencial));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,22352,'$ed142_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,4027,22352,'','".AddSlashes(pg_result($resaco,$iresaco,'ed142_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,4027,22353,'','".AddSlashes(pg_result($resaco,$iresaco,'ed142_basecurricular'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,4027,22354,'','".AddSlashes(pg_result($resaco,$iresaco,'ed142_serie'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from basecurricularserie
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($ed142_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " ed142_sequencial = $ed142_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Etapa da Base não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$ed142_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Etapa da Base não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$ed142_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$ed142_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = pg_affected_rows($result);
         return true;
       } 
     } 
   } 
   // funcao do recordset 
   public function sql_record($sql) { 
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
        $this->erro_sql   = "Record Vazio na Tabela:basecurricularserie";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql 
   public function sql_query ($ed142_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from basecurricularserie ";
     $sql .= "      inner join basecurricular  on  basecurricular.ed141_sequencial = basecurricularserie.ed142_basecurricular";
     $sql .= "      inner join serie  on  serie.ed11_i_codigo = basecurricularserie.ed142_serie";
     $sql .= "      inner join cursoedu  on  cursoedu.ed29_i_codigo = basecurricular.ed141_cursoedu";
     $sql .= "      inner join ensino  on  ensino.ed10_i_codigo = serie.ed11_i_ensino";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ed142_sequencial)) {
         $sql2 .= " where basecurricularserie.ed142_sequencial = $ed142_sequencial "; 
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
   // funcao do sql 
   public function sql_query_file ($ed142_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from basecurricularserie ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ed142_sequencial)){
         $sql2 .= " where basecurricularserie.ed142_sequencial = $ed142_sequencial "; 
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
