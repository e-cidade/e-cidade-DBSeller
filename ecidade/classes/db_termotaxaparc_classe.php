<?php
//MODULO: arrecadacao
//CLASSE DA ENTIDADE termotaxaparc
class cl_termotaxaparc { 
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
   var $ar29_sequencial = 0; 
   var $ar29_numpar = 0; 
   var $ar29_taxa = 0; 
   var $ar29_instit = 0; 
   // cria propriedade com as variaveis do arquivo 
   var $campos = "
                 ar29_sequencial = int4 = Sequencial 
                 ar29_numpar = int4 = Parcela 
                 ar29_taxa = int4 = Taxa 
                 ar29_instit = int4 = Instituição 
                 ";
   //funcao construtor da classe 
   public function __construct() { 
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("termotaxaparc"); 
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
       $this->ar29_sequencial = ($this->ar29_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["ar29_sequencial"]:$this->ar29_sequencial);
       $this->ar29_numpar = ($this->ar29_numpar == ""?@$GLOBALS["HTTP_POST_VARS"]["ar29_numpar"]:$this->ar29_numpar);
       $this->ar29_taxa = ($this->ar29_taxa == ""?@$GLOBALS["HTTP_POST_VARS"]["ar29_taxa"]:$this->ar29_taxa);
       $this->ar29_instit = ($this->ar29_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["ar29_instit"]:$this->ar29_instit);
     }else{
       $this->ar29_sequencial = ($this->ar29_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["ar29_sequencial"]:$this->ar29_sequencial);
     }
   }
   // funcao para Inclusão
   public function incluir ($ar29_sequencial){ 
      $this->atualizacampos();
     if($this->ar29_numpar == null ){ 
       $this->erro_sql = " Campo Parcela não informado.";
       $this->erro_campo = "ar29_numpar";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ar29_taxa == null ){ 
       $this->erro_sql = " Campo Taxa não informado.";
       $this->erro_campo = "ar29_taxa";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ar29_instit == null ){ 
       $this->erro_sql = " Campo Instituição não informado.";
       $this->erro_campo = "ar29_instit";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($ar29_sequencial == "" || $ar29_sequencial == null ){
       $result = db_query("select nextval('termotaxaparc_ar29_sequencial_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: termotaxaparc_ar29_sequencial_seq do campo: ar29_sequencial"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->ar29_sequencial = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from termotaxaparc_ar29_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $ar29_sequencial)){
         $this->erro_sql = " Campo ar29_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->ar29_sequencial = $ar29_sequencial; 
       }
     }
     if(($this->ar29_sequencial == null) || ($this->ar29_sequencial == "") ){ 
       $this->erro_sql = " Campo ar29_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into termotaxaparc(
                                       ar29_sequencial 
                                      ,ar29_numpar 
                                      ,ar29_taxa 
                                      ,ar29_instit 
                       )
                values (
                                $this->ar29_sequencial 
                               ,$this->ar29_numpar 
                               ,$this->ar29_taxa 
                               ,$this->ar29_instit 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Vinculo de parcela com taxas e custas ($this->ar29_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Vinculo de parcela com taxas e custas já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Vinculo de parcela com taxas e custas ($this->ar29_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->ar29_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ar29_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1009592,'$this->ar29_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,1010251,1009592,'','".AddSlashes(pg_result($resaco,0,'ar29_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010251,1009593,'','".AddSlashes(pg_result($resaco,0,'ar29_numpar'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010251,1009594,'','".AddSlashes(pg_result($resaco,0,'ar29_taxa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010251,1009595,'','".AddSlashes(pg_result($resaco,0,'ar29_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 
   // funcao para alteracao
   public function alterar ($ar29_sequencial=null) { 
      $this->atualizacampos();
     $sql = " update termotaxaparc set ";
     $virgula = "";
     if(trim($this->ar29_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ar29_sequencial"])){ 
       $sql  .= $virgula." ar29_sequencial = $this->ar29_sequencial ";
       $virgula = ",";
       if(trim($this->ar29_sequencial) == null ){ 
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "ar29_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ar29_numpar)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ar29_numpar"])){ 
       $sql  .= $virgula." ar29_numpar = $this->ar29_numpar ";
       $virgula = ",";
       if(trim($this->ar29_numpar) == null ){ 
         $this->erro_sql = " Campo Parcela não informado.";
         $this->erro_campo = "ar29_numpar";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ar29_taxa)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ar29_taxa"])){ 
       $sql  .= $virgula." ar29_taxa = $this->ar29_taxa ";
       $virgula = ",";
       if(trim($this->ar29_taxa) == null ){ 
         $this->erro_sql = " Campo Taxa não informado.";
         $this->erro_campo = "ar29_taxa";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ar29_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ar29_instit"])){ 
       $sql  .= $virgula." ar29_instit = $this->ar29_instit ";
       $virgula = ",";
       if(trim($this->ar29_instit) == null ){ 
         $this->erro_sql = " Campo Instituição não informado.";
         $this->erro_campo = "ar29_instit";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($ar29_sequencial!=null){
       $sql .= " ar29_sequencial = $this->ar29_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ar29_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1009592,'$this->ar29_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ar29_sequencial"]) || $this->ar29_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,1010251,1009592,'".AddSlashes(pg_result($resaco,$conresaco,'ar29_sequencial'))."','$this->ar29_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ar29_numpar"]) || $this->ar29_numpar != "")
             $resac = db_query("insert into db_acount values($acount,1010251,1009593,'".AddSlashes(pg_result($resaco,$conresaco,'ar29_numpar'))."','$this->ar29_numpar',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ar29_taxa"]) || $this->ar29_taxa != "")
             $resac = db_query("insert into db_acount values($acount,1010251,1009594,'".AddSlashes(pg_result($resaco,$conresaco,'ar29_taxa'))."','$this->ar29_taxa',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ar29_instit"]) || $this->ar29_instit != "")
             $resac = db_query("insert into db_acount values($acount,1010251,1009595,'".AddSlashes(pg_result($resaco,$conresaco,'ar29_instit'))."','$this->ar29_instit',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Vinculo de parcela com taxas e custas não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->ar29_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Vinculo de parcela com taxas e custas não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->ar29_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->ar29_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   public function excluir ($ar29_sequencial=null,$dbwhere=null) { 

     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($ar29_sequencial));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1009592,'$ar29_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,1010251,1009592,'','".AddSlashes(pg_result($resaco,$iresaco,'ar29_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010251,1009593,'','".AddSlashes(pg_result($resaco,$iresaco,'ar29_numpar'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010251,1009594,'','".AddSlashes(pg_result($resaco,$iresaco,'ar29_taxa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010251,1009595,'','".AddSlashes(pg_result($resaco,$iresaco,'ar29_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from termotaxaparc
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($ar29_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " ar29_sequencial = $ar29_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Vinculo de parcela com taxas e custas não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$ar29_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Vinculo de parcela com taxas e custas não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$ar29_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$ar29_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:termotaxaparc";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql 
   public function sql_query ($ar29_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from termotaxaparc ";
     $sql .= "      inner join taxa  on  taxa.ar36_sequencial = termotaxaparc.ar29_taxa";
     $sql .= "      inner join tabrec  on  tabrec.k02_codigo = taxa.ar36_receita";
     $sql .= "      inner join grupotaxa  on  grupotaxa.ar37_sequencial = taxa.ar36_grupotaxa";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ar29_sequencial)) {
         $sql2 .= " where termotaxaparc.ar29_sequencial = $ar29_sequencial "; 
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
   public function sql_query_file ($ar29_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from termotaxaparc ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ar29_sequencial)){
         $sql2 .= " where termotaxaparc.ar29_sequencial = $ar29_sequencial "; 
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
