<?php
//MODULO: contabilidade
//CLASSE DA ENTIDADE previsaodespesaplano
class cl_previsaodespesaplano { 
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
   var $c55_sequencial = 0; 
   var $c55_previsaodespesa = 0; 
   var $c55_titulo = null; 
   var $c55_valor = 0; 
   var $c55_codigo = 0; 
   // cria propriedade com as variaveis do arquivo 
   var $campos = "
                 c55_sequencial = int4 = Sequencial 
                 c55_previsaodespesa = int4 = Previsão despesa 
                 c55_titulo = varchar(100) = Título 
                 c55_valor = float8 = Valor 
                 c55_codigo = int4 = Código 
                 ";
   //funcao construtor da classe 
   public function __construct() { 
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("previsaodespesaplano"); 
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
       $this->c55_sequencial = ($this->c55_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["c55_sequencial"]:$this->c55_sequencial);
       $this->c55_previsaodespesa = ($this->c55_previsaodespesa == ""?@$GLOBALS["HTTP_POST_VARS"]["c55_previsaodespesa"]:$this->c55_previsaodespesa);
       $this->c55_titulo = ($this->c55_titulo == ""?@$GLOBALS["HTTP_POST_VARS"]["c55_titulo"]:$this->c55_titulo);
       $this->c55_valor = ($this->c55_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["c55_valor"]:$this->c55_valor);
       $this->c55_codigo = ($this->c55_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["c55_codigo"]:$this->c55_codigo);
     }else{
       $this->c55_sequencial = ($this->c55_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["c55_sequencial"]:$this->c55_sequencial);
     }
   }
   // funcao para Inclusão
   public function incluir ($c55_sequencial){ 
      $this->atualizacampos();
     if($this->c55_previsaodespesa == null ){ 
       $this->erro_sql = " Campo Previsão despesa não informado.";
       $this->erro_campo = "c55_previsaodespesa";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c55_titulo == null ){ 
       $this->erro_sql = " Campo Título não informado.";
       $this->erro_campo = "c55_titulo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c55_valor == null ){ 
       $this->erro_sql = " Campo Valor não informado.";
       $this->erro_campo = "c55_valor";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c55_codigo == null ){ 
       $this->erro_sql = " Campo Código não informado.";
       $this->erro_campo = "c55_codigo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($c55_sequencial == "" || $c55_sequencial == null ){
       $result = db_query("select nextval('previsaodespesaplano_c55_sequencial_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: previsaodespesaplano_c55_sequencial_seq do campo: c55_sequencial"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->c55_sequencial = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from previsaodespesaplano_c55_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $c55_sequencial)){
         $this->erro_sql = " Campo c55_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->c55_sequencial = $c55_sequencial; 
       }
     }
     if(($this->c55_sequencial == null) || ($this->c55_sequencial == "") ){ 
       $this->erro_sql = " Campo c55_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into previsaodespesaplano(
                                       c55_sequencial 
                                      ,c55_previsaodespesa 
                                      ,c55_titulo 
                                      ,c55_valor 
                                      ,c55_codigo 
                       )
                values (
                                $this->c55_sequencial 
                               ,$this->c55_previsaodespesa 
                               ,'$this->c55_titulo' 
                               ,$this->c55_valor 
                               ,$this->c55_codigo 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "previsaodespesaplano ($this->c55_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "previsaodespesaplano já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "previsaodespesaplano ($this->c55_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->c55_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->c55_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1009881,'$this->c55_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,1010303,1009881,'','".AddSlashes(pg_result($resaco,0,'c55_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010303,1009882,'','".AddSlashes(pg_result($resaco,0,'c55_previsaodespesa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010303,1009883,'','".AddSlashes(pg_result($resaco,0,'c55_titulo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010303,1009884,'','".AddSlashes(pg_result($resaco,0,'c55_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010303,1009887,'','".AddSlashes(pg_result($resaco,0,'c55_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 
   // funcao para alteracao
   public function alterar ($c55_sequencial=null) { 
      $this->atualizacampos();
     $sql = " update previsaodespesaplano set ";
     $virgula = "";
     if(trim($this->c55_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c55_sequencial"])){ 
       $sql  .= $virgula." c55_sequencial = $this->c55_sequencial ";
       $virgula = ",";
       if(trim($this->c55_sequencial) == null ){ 
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "c55_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c55_previsaodespesa)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c55_previsaodespesa"])){ 
       $sql  .= $virgula." c55_previsaodespesa = $this->c55_previsaodespesa ";
       $virgula = ",";
       if(trim($this->c55_previsaodespesa) == null ){ 
         $this->erro_sql = " Campo Previsão despesa não informado.";
         $this->erro_campo = "c55_previsaodespesa";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c55_titulo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c55_titulo"])){ 
       $sql  .= $virgula." c55_titulo = '$this->c55_titulo' ";
       $virgula = ",";
       if(trim($this->c55_titulo) == null ){ 
         $this->erro_sql = " Campo Título não informado.";
         $this->erro_campo = "c55_titulo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c55_valor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c55_valor"])){ 
       $sql  .= $virgula." c55_valor = $this->c55_valor ";
       $virgula = ",";
       if(trim($this->c55_valor) == null ){ 
         $this->erro_sql = " Campo Valor não informado.";
         $this->erro_campo = "c55_valor";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c55_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c55_codigo"])){ 
       $sql  .= $virgula." c55_codigo = $this->c55_codigo ";
       $virgula = ",";
       if(trim($this->c55_codigo) == null ){ 
         $this->erro_sql = " Campo Código não informado.";
         $this->erro_campo = "c55_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($c55_sequencial!=null){
       $sql .= " c55_sequencial = $this->c55_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->c55_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1009881,'$this->c55_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c55_sequencial"]) || $this->c55_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,1010303,1009881,'".AddSlashes(pg_result($resaco,$conresaco,'c55_sequencial'))."','$this->c55_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c55_previsaodespesa"]) || $this->c55_previsaodespesa != "")
             $resac = db_query("insert into db_acount values($acount,1010303,1009882,'".AddSlashes(pg_result($resaco,$conresaco,'c55_previsaodespesa'))."','$this->c55_previsaodespesa',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c55_titulo"]) || $this->c55_titulo != "")
             $resac = db_query("insert into db_acount values($acount,1010303,1009883,'".AddSlashes(pg_result($resaco,$conresaco,'c55_titulo'))."','$this->c55_titulo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c55_valor"]) || $this->c55_valor != "")
             $resac = db_query("insert into db_acount values($acount,1010303,1009884,'".AddSlashes(pg_result($resaco,$conresaco,'c55_valor'))."','$this->c55_valor',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c55_codigo"]) || $this->c55_codigo != "")
             $resac = db_query("insert into db_acount values($acount,1010303,1009887,'".AddSlashes(pg_result($resaco,$conresaco,'c55_codigo'))."','$this->c55_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "previsaodespesaplano não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->c55_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "previsaodespesaplano não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->c55_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->c55_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   public function excluir ($c55_sequencial=null,$dbwhere=null) { 

     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($c55_sequencial));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1009881,'$c55_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,1010303,1009881,'','".AddSlashes(pg_result($resaco,$iresaco,'c55_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010303,1009882,'','".AddSlashes(pg_result($resaco,$iresaco,'c55_previsaodespesa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010303,1009883,'','".AddSlashes(pg_result($resaco,$iresaco,'c55_titulo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010303,1009884,'','".AddSlashes(pg_result($resaco,$iresaco,'c55_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010303,1009887,'','".AddSlashes(pg_result($resaco,$iresaco,'c55_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from previsaodespesaplano
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($c55_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " c55_sequencial = $c55_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "previsaodespesaplano não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$c55_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "previsaodespesaplano não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$c55_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$c55_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:previsaodespesaplano";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql 
   public function sql_query ($c55_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from previsaodespesaplano ";
     $sql .= "      inner join previsaodespesa  on  previsaodespesa.c333_sequencial = previsaodespesaplano.c55_previsaodespesa";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($c55_sequencial)) {
         $sql2 .= " where previsaodespesaplano.c55_sequencial = $c55_sequencial "; 
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
   public function sql_query_file ($c55_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from previsaodespesaplano ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($c55_sequencial)){
         $sql2 .= " where previsaodespesaplano.c55_sequencial = $c55_sequencial "; 
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
