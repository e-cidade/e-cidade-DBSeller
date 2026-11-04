<?php
//MODULO: contabilidade
//CLASSE DA ENTIDADE previsaodespesalinhaspacto
class cl_previsaodespesalinhaspacto { 
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
   var $c41_sequencial = 0; 
   var $c41_previsaodespesa = 0; 
   var $c41_linhaspacto = 0; 
   var $c41_previsaodespesaplano = 0; 
   var $c41_valorlinha = 0; 
   // cria propriedade com as variaveis do arquivo 
   var $campos = "
                 c41_sequencial = int4 = Código 
                 c41_previsaodespesa = int4 = Previsão despesa 
                 c41_linhaspacto = int4 = LInhas pacto 
                 c41_previsaodespesaplano = int4 = Plano orçamentário 
                 c41_valorlinha = float8 = Valor 
                 ";
   //funcao construtor da classe 
   public function __construct() { 
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("previsaodespesalinhaspacto"); 
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
       $this->c41_sequencial = ($this->c41_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["c41_sequencial"]:$this->c41_sequencial);
       $this->c41_previsaodespesa = ($this->c41_previsaodespesa == ""?@$GLOBALS["HTTP_POST_VARS"]["c41_previsaodespesa"]:$this->c41_previsaodespesa);
       $this->c41_linhaspacto = ($this->c41_linhaspacto == ""?@$GLOBALS["HTTP_POST_VARS"]["c41_linhaspacto"]:$this->c41_linhaspacto);
       $this->c41_previsaodespesaplano = ($this->c41_previsaodespesaplano == ""?@$GLOBALS["HTTP_POST_VARS"]["c41_previsaodespesaplano"]:$this->c41_previsaodespesaplano);
       $this->c41_valorlinha = ($this->c41_valorlinha == ""?@$GLOBALS["HTTP_POST_VARS"]["c41_valorlinha"]:$this->c41_valorlinha);
     }else{
       $this->c41_sequencial = ($this->c41_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["c41_sequencial"]:$this->c41_sequencial);
     }
   }
   // funcao para Inclusão
   public function incluir ($c41_sequencial){ 
      $this->atualizacampos();
     if($this->c41_previsaodespesa == null ){ 
       $this->erro_sql = " Campo Previsão despesa não informado.";
       $this->erro_campo = "c41_previsaodespesa";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c41_linhaspacto == null ){ 
       $this->erro_sql = " Campo LInhas pacto não informado.";
       $this->erro_campo = "c41_linhaspacto";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c41_previsaodespesaplano == null ){ 
       $this->erro_sql = " Campo Plano orçamentário não informado.";
       $this->erro_campo = "c41_previsaodespesaplano";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c41_valorlinha == null ){ 
       $this->erro_sql = " Campo Valor não informado.";
       $this->erro_campo = "c41_valorlinha";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($c41_sequencial == "" || $c41_sequencial == null ){
       $result = db_query("select nextval('previsaodespesalinhaspacto_c41_sequencial_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: previsaodespesalinhaspacto_c41_sequencial_seq do campo: c41_sequencial"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->c41_sequencial = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from previsaodespesalinhaspacto_c41_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $c41_sequencial)){
         $this->erro_sql = " Campo c41_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->c41_sequencial = $c41_sequencial; 
       }
     }
     if(($this->c41_sequencial == null) || ($this->c41_sequencial == "") ){ 
       $this->erro_sql = " Campo c41_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into previsaodespesalinhaspacto(
                                       c41_sequencial 
                                      ,c41_previsaodespesa 
                                      ,c41_linhaspacto 
                                      ,c41_previsaodespesaplano 
                                      ,c41_valorlinha 
                       )
                values (
                                $this->c41_sequencial 
                               ,$this->c41_previsaodespesa 
                               ,$this->c41_linhaspacto 
                               ,$this->c41_previsaodespesaplano 
                               ,$this->c41_valorlinha 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "previsaodespesalinhaspacto ($this->c41_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "previsaodespesalinhaspacto já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "previsaodespesalinhaspacto ($this->c41_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->c41_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->c41_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1009878,'$this->c41_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,1010302,1009878,'','".AddSlashes(pg_result($resaco,0,'c41_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010302,1009879,'','".AddSlashes(pg_result($resaco,0,'c41_previsaodespesa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010302,1009880,'','".AddSlashes(pg_result($resaco,0,'c41_linhaspacto'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010302,1009885,'','".AddSlashes(pg_result($resaco,0,'c41_previsaodespesaplano'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010302,1009886,'','".AddSlashes(pg_result($resaco,0,'c41_valorlinha'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 
   // funcao para alteracao
   public function alterar ($c41_sequencial=null) { 
      $this->atualizacampos();
     $sql = " update previsaodespesalinhaspacto set ";
     $virgula = "";
     if(trim($this->c41_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c41_sequencial"])){ 
       $sql  .= $virgula." c41_sequencial = $this->c41_sequencial ";
       $virgula = ",";
       if(trim($this->c41_sequencial) == null ){ 
         $this->erro_sql = " Campo Código não informado.";
         $this->erro_campo = "c41_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c41_previsaodespesa)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c41_previsaodespesa"])){ 
       $sql  .= $virgula." c41_previsaodespesa = $this->c41_previsaodespesa ";
       $virgula = ",";
       if(trim($this->c41_previsaodespesa) == null ){ 
         $this->erro_sql = " Campo Previsão despesa não informado.";
         $this->erro_campo = "c41_previsaodespesa";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c41_linhaspacto)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c41_linhaspacto"])){ 
       $sql  .= $virgula." c41_linhaspacto = $this->c41_linhaspacto ";
       $virgula = ",";
       if(trim($this->c41_linhaspacto) == null ){ 
         $this->erro_sql = " Campo LInhas pacto não informado.";
         $this->erro_campo = "c41_linhaspacto";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c41_previsaodespesaplano)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c41_previsaodespesaplano"])){ 
       $sql  .= $virgula." c41_previsaodespesaplano = $this->c41_previsaodespesaplano ";
       $virgula = ",";
       if(trim($this->c41_previsaodespesaplano) == null ){ 
         $this->erro_sql = " Campo Plano orçamentário não informado.";
         $this->erro_campo = "c41_previsaodespesaplano";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c41_valorlinha)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c41_valorlinha"])){ 
       $sql  .= $virgula." c41_valorlinha = $this->c41_valorlinha ";
       $virgula = ",";
       if(trim($this->c41_valorlinha) == null ){ 
         $this->erro_sql = " Campo Valor não informado.";
         $this->erro_campo = "c41_valorlinha";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($c41_sequencial!=null){
       $sql .= " c41_sequencial = $this->c41_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->c41_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1009878,'$this->c41_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c41_sequencial"]) || $this->c41_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,1010302,1009878,'".AddSlashes(pg_result($resaco,$conresaco,'c41_sequencial'))."','$this->c41_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c41_previsaodespesa"]) || $this->c41_previsaodespesa != "")
             $resac = db_query("insert into db_acount values($acount,1010302,1009879,'".AddSlashes(pg_result($resaco,$conresaco,'c41_previsaodespesa'))."','$this->c41_previsaodespesa',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c41_linhaspacto"]) || $this->c41_linhaspacto != "")
             $resac = db_query("insert into db_acount values($acount,1010302,1009880,'".AddSlashes(pg_result($resaco,$conresaco,'c41_linhaspacto'))."','$this->c41_linhaspacto',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c41_previsaodespesaplano"]) || $this->c41_previsaodespesaplano != "")
             $resac = db_query("insert into db_acount values($acount,1010302,1009885,'".AddSlashes(pg_result($resaco,$conresaco,'c41_previsaodespesaplano'))."','$this->c41_previsaodespesaplano',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c41_valorlinha"]) || $this->c41_valorlinha != "")
             $resac = db_query("insert into db_acount values($acount,1010302,1009886,'".AddSlashes(pg_result($resaco,$conresaco,'c41_valorlinha'))."','$this->c41_valorlinha',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "previsaodespesalinhaspacto não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->c41_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "previsaodespesalinhaspacto não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->c41_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->c41_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   public function excluir ($c41_sequencial=null,$dbwhere=null) { 

     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($c41_sequencial));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1009878,'$c41_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,1010302,1009878,'','".AddSlashes(pg_result($resaco,$iresaco,'c41_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010302,1009879,'','".AddSlashes(pg_result($resaco,$iresaco,'c41_previsaodespesa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010302,1009880,'','".AddSlashes(pg_result($resaco,$iresaco,'c41_linhaspacto'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010302,1009885,'','".AddSlashes(pg_result($resaco,$iresaco,'c41_previsaodespesaplano'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010302,1009886,'','".AddSlashes(pg_result($resaco,$iresaco,'c41_valorlinha'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from previsaodespesalinhaspacto
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($c41_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " c41_sequencial = $c41_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "previsaodespesalinhaspacto não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$c41_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "previsaodespesalinhaspacto não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$c41_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$c41_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:previsaodespesalinhaspacto";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql 
   public function sql_query ($c41_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from previsaodespesalinhaspacto ";
     $sql .= "      inner join previsaodespesa  on  previsaodespesa.c333_sequencial = previsaodespesalinhaspacto.c41_previsaodespesa";
     $sql .= "      inner join linhaspacto  on  linhaspacto.c07_sequencial = previsaodespesalinhaspacto.c41_linhaspacto";
     $sql .= "      inner join previsaodespesaplano  on  previsaodespesaplano.c55_sequencial = previsaodespesalinhaspacto.c41_previsaodespesaplano";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($c41_sequencial)) {
         $sql2 .= " where previsaodespesalinhaspacto.c41_sequencial = $c41_sequencial "; 
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
   public function sql_query_file ($c41_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from previsaodespesalinhaspacto ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($c41_sequencial)){
         $sql2 .= " where previsaodespesalinhaspacto.c41_sequencial = $c41_sequencial "; 
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
