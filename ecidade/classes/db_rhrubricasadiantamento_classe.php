<?php
//MODULO: pessoal
//CLASSE DA ENTIDADE rhrubricasadiantamento
class cl_rhrubricasadiantamento { 
   // cria variaveis de erro 
   public $rotulo     = null; 
   public $query_sql  = null; 
   public $numrows    = 0; 
   public $erro_status= null; 
   public $erro_sql   = null; 
   public $erro_banco = null;  
   public $erro_msg   = null;  
   public $erro_campo = null;  
   public $pagina_retorno = null; 
   // cria variaveis do arquivo 
   public $rh262_rubrica_principal = null; 
   public $rh262_rubrica_adiantamento = null; 
   public $rh262_instituicao = 0; 
   // cria propriedade com as variaveis do arquivo 
   public $campos = "
                 rh262_rubrica_principal = varchar(4) = Rubrica do Saldo do 13º Salário 
                 rh262_rubrica_adiantamento = varchar(4) = Rubrica de Adiantamento do 13º Salário 
                 rh262_instituicao = int4 = Instituição 
                 ";
   //funcao construtor da classe 
    public function __construct()
    { 
      //classes dos rotulos dos campos
      $this->rotulo = new rotulo("rhrubricasadiantamento"); 
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
       $this->rh262_rubrica_principal = ($this->rh262_rubrica_principal == ""?@$GLOBALS["HTTP_POST_VARS"]["rh262_rubrica_principal"]:$this->rh262_rubrica_principal);
       $this->rh262_rubrica_adiantamento = ($this->rh262_rubrica_adiantamento == ""?@$GLOBALS["HTTP_POST_VARS"]["rh262_rubrica_adiantamento"]:$this->rh262_rubrica_adiantamento);
       $this->rh262_instituicao = ($this->rh262_instituicao == ""?@$GLOBALS["HTTP_POST_VARS"]["rh262_instituicao"]:$this->rh262_instituicao);
     }else{
       $this->rh262_rubrica_principal = ($this->rh262_rubrica_principal == ""?@$GLOBALS["HTTP_POST_VARS"]["rh262_rubrica_principal"]:$this->rh262_rubrica_principal);
       $this->rh262_rubrica_adiantamento = ($this->rh262_rubrica_adiantamento == ""?@$GLOBALS["HTTP_POST_VARS"]["rh262_rubrica_adiantamento"]:$this->rh262_rubrica_adiantamento);
       $this->rh262_instituicao = ($this->rh262_instituicao == ""?@$GLOBALS["HTTP_POST_VARS"]["rh262_instituicao"]:$this->rh262_instituicao);
     }
   }
   // funcao para inclusao
   public function incluir ($rh262_rubrica_principal,$rh262_rubrica_adiantamento,$rh262_instituicao){ 
      $this->atualizacampos();
       $this->rh262_rubrica_principal = $rh262_rubrica_principal; 
       $this->rh262_rubrica_adiantamento = $rh262_rubrica_adiantamento; 
       $this->rh262_instituicao = $rh262_instituicao; 
     if(($this->rh262_rubrica_principal == null) || ($this->rh262_rubrica_principal == "") ){ 
       $this->erro_sql = " Campo rh262_rubrica_principal nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->rh262_rubrica_adiantamento == null) || ($this->rh262_rubrica_adiantamento == "") ){ 
       $this->erro_sql = " Campo rh262_rubrica_adiantamento nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->rh262_instituicao == null) || ($this->rh262_instituicao == "") ){ 
       $this->erro_sql = " Campo rh262_instituicao nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $result = @db_query("insert into rhrubricasadiantamento(
                                       rh262_rubrica_principal 
                                      ,rh262_rubrica_adiantamento 
                                      ,rh262_instituicao 
                       )
                values (
                                '$this->rh262_rubrica_principal' 
                               ,'$this->rh262_rubrica_adiantamento' 
                               ,$this->rh262_instituicao 
                      )");
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Rubrica de Adiantamento de 13º Salário ($this->rh262_rubrica_principal."-".$this->rh262_rubrica_adiantamento."-".$this->rh262_instituicao) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Rubrica de Adiantamento de 13º Salário já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Rubrica de Adiantamento de 13º Salário ($this->rh262_rubrica_principal."-".$this->rh262_rubrica_adiantamento."-".$this->rh262_instituicao) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->rh262_rubrica_principal."-".$this->rh262_rubrica_adiantamento."-".$this->rh262_instituicao;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $resaco = $this->sql_record($this->sql_query_file($this->rh262_rubrica_principal,$this->rh262_rubrica_adiantamento,$this->rh262_instituicao));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountkey values($acount,1014155,'$this->rh262_rubrica_principal','I')");
       $resac = db_query("insert into db_acountkey values($acount,1014156,'$this->rh262_rubrica_adiantamento','I')");
       $resac = db_query("insert into db_acountkey values($acount,1014157,'$this->rh262_instituicao','I')");
       $resac = db_query("insert into db_acount values($acount,1010931,1014155,'','".pg_result($resaco,0,'rh262_rubrica_principal')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010931,1014156,'','".pg_result($resaco,0,'rh262_rubrica_adiantamento')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010931,1014157,'','".pg_result($resaco,0,'rh262_instituicao')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   } 
   // funcao para alteracao
   public function alterar ($rh262_rubrica_principal=null,$rh262_rubrica_adiantamento=null,$rh262_instituicao=null) { 
      $this->atualizacampos();
     $sql = " update rhrubricasadiantamento set ";
     $virgula = "";
     if(trim($this->rh262_rubrica_principal)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh262_rubrica_principal"])){ 
       $sql  .= $virgula." rh262_rubrica_principal = '$this->rh262_rubrica_principal' ";
       $virgula = ",";
     }
     if(trim($this->rh262_rubrica_adiantamento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh262_rubrica_adiantamento"])){ 
       $sql  .= $virgula." rh262_rubrica_adiantamento = '$this->rh262_rubrica_adiantamento' ";
       $virgula = ",";
     }
     if(trim($this->rh262_instituicao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh262_instituicao"])){ 
        if(trim($this->rh262_instituicao)=="" && isset($GLOBALS["HTTP_POST_VARS"]["rh262_instituicao"])){ 
           $this->rh262_instituicao = "0" ; 
        } 
       $sql  .= $virgula." rh262_instituicao = $this->rh262_instituicao ";
       $virgula = ",";
       if(trim($this->rh262_instituicao) == null ){ 
         $this->erro_sql = " Campo Instituição nao Informado.";
         $this->erro_campo = "rh262_instituicao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where rh262_rubrica_principal = '$this->rh262_rubrica_principal' and rh262_instituicao = $this->rh262_instituicao";

     $resaco = $this->sql_record($this->sql_query_file($this->rh262_rubrica_principal,$this->rh262_rubrica_adiantamento,$this->rh262_instituicao));
     if($this->numrows>0){       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountkey values($acount,1014155,'$this->rh262_rubrica_principal','A')");
       $resac = db_query("insert into db_acountkey values($acount,1014156,'$this->rh262_rubrica_adiantamento','A')");
       $resac = db_query("insert into db_acountkey values($acount,1014157,'$this->rh262_instituicao','A')");
       if(isset($GLOBALS["HTTP_POST_VARS"]["rh262_rubrica_principal"]))
         $resac = db_query("insert into db_acount values($acount,1010931,1014155,'".pg_result($resaco,0,'rh262_rubrica_principal')."','$this->rh262_rubrica_principal',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       if(isset($GLOBALS["HTTP_POST_VARS"]["rh262_rubrica_adiantamento"]))
         $resac = db_query("insert into db_acount values($acount,1010931,1014156,'".pg_result($resaco,0,'rh262_rubrica_adiantamento')."','$this->rh262_rubrica_adiantamento',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       if(isset($GLOBALS["HTTP_POST_VARS"]["rh262_instituicao"]))
         $resac = db_query("insert into db_acount values($acount,1010931,1014157,'".pg_result($resaco,0,'rh262_instituicao')."','$this->rh262_instituicao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     $result = @db_query($sql);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Rubrica de Adiantamento de 13º Salário nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->rh262_rubrica_principal."-".$this->rh262_rubrica_adiantamento."-".$this->rh262_instituicao;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Rubrica de Adiantamento de 13º Salário nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->rh262_rubrica_principal."-".$this->rh262_rubrica_adiantamento."-".$this->rh262_instituicao;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->rh262_rubrica_principal."-".$this->rh262_rubrica_adiantamento."-".$this->rh262_instituicao;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   public function excluir ($rh262_rubrica_principal=null,$rh262_rubrica_adiantamento=null,$rh262_instituicao=null) { 
     $this->atualizacampos(true);
     $resaco = $this->sql_record($this->sql_query_file($this->rh262_rubrica_principal,$this->rh262_rubrica_adiantamento,$this->rh262_instituicao));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountkey values($acount,1014155,'$this->rh262_rubrica_principal','E')");
       $resac = db_query("insert into db_acountkey values($acount,1014156,'$this->rh262_rubrica_adiantamento','E')");
       $resac = db_query("insert into db_acountkey values($acount,1014157,'$this->rh262_instituicao','E')");
       $resac = db_query("insert into db_acount values($acount,1010931,1014155,'','".pg_result($resaco,0,'rh262_rubrica_principal')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010931,1014156,'','".pg_result($resaco,0,'rh262_rubrica_adiantamento')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010931,1014157,'','".pg_result($resaco,0,'rh262_instituicao')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     $sql = " delete from rhrubricasadiantamento
                    where ";
     $sql2 = "";
      if($this->rh262_rubrica_principal != ""){
      if($sql2!=""){
        $sql2 .= " and ";
      }
      $sql2 .= " rh262_rubrica_principal = '$this->rh262_rubrica_principal' ";
}
      if($this->rh262_rubrica_adiantamento != ""){
      if($sql2!=""){
        $sql2 .= " and ";
      }
      $sql2 .= " rh262_rubrica_adiantamento = '$this->rh262_rubrica_adiantamento' ";
}
      if($this->rh262_instituicao != ""){
      if($sql2!=""){
        $sql2 .= " and ";
      }
      $sql2 .= " rh262_instituicao = $this->rh262_instituicao ";
}
     $result = @db_query($sql.$sql2);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Rubrica de Adiantamento de 13º Salário nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$this->rh262_rubrica_principal."-".$this->rh262_rubrica_adiantamento."-".$this->rh262_instituicao;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Rubrica de Adiantamento de 13º Salário nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$this->rh262_rubrica_principal."-".$this->rh262_rubrica_adiantamento."-".$this->rh262_instituicao;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->rh262_rubrica_principal."-".$this->rh262_rubrica_adiantamento."-".$this->rh262_instituicao;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       } 
     } 
   } 
   // funcao do recordset 
   public function sql_record($sql) { 
     $result = @db_query($sql);
     if($result==false){
       $this->numrows    = 0;
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Erro ao selecionar os registros.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $this->numrows = pg_numrows($result);
      if($this->numrows==0){
        $this->erro_banco = "";
        $this->erro_sql   = "Dados do Grupo nao Encontrado";
        $this->erro_msg   = "Usuário: \n\n ".$this->erro_sql." \n\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql 
   public function sql_query ( $rh262_rubrica_principal=null,$rh262_rubrica_adiantamento=null,$rh262_instituicao=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from rhrubricasadiantamento ";
     $sql .= "      inner join rhrubricas on rhrubricas.rh27_rubric = rhrubricasadiantamento.rh262_rubrica_adiantamento and rhrubricas.rh27_instit = rhrubricasadiantamento.rh262_instituicao";
     $sql2 = "";
     if($dbwhere==""){
       if($rh262_rubrica_principal!=null ){
         $sql2 .= " where rhrubricasadiantamento.rh262_rubrica_principal = '$rh262_rubrica_principal' "; 
       } 
       if($rh262_rubrica_adiantamento!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         } 
         $sql2 .= " rhrubricasadiantamento.rh262_rubrica_adiantamento = '$rh262_rubrica_adiantamento' "; 
       } 
       if($rh262_instituicao!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         } 
         $sql2 .= " rhrubricasadiantamento.rh262_instituicao = $rh262_instituicao "; 
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
   // funcao do sql 
   public function sql_query_file ( $rh262_rubrica_principal=null,$rh262_rubrica_adiantamento=null,$rh262_instituicao=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from rhrubricasadiantamento ";
     $sql2 = "";
     if($dbwhere==""){
       if($rh262_rubrica_principal!=null ){
         $sql2 .= " where rhrubricasadiantamento.rh262_rubrica_principal = '$rh262_rubrica_principal' "; 
       } 
       if($rh262_rubrica_adiantamento!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         } 
         $sql2 .= " rhrubricasadiantamento.rh262_rubrica_adiantamento = '$rh262_rubrica_adiantamento' "; 
       } 
       if($rh262_instituicao!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         } 
         $sql2 .= " rhrubricasadiantamento.rh262_instituicao = $rh262_instituicao "; 
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
