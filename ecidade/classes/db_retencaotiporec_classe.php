<?php

class cl_retencaotiporec
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
    public $e21_sequencial = 0; 
    public $e21_retencaotipocalc = 0; 
    public $e21_receita = 0; 
    public $e21_descricao = null; 
    public $e21_aliquota = 0; 
    public $e21_instit = 0; 
    public $e21_retencaotiporecgrupo = 0; 
    public $e21_enterecebedor = 'null';
    public $e21_receitaenterecebedor = 'null';
    public $e21_envioremessabancaria = 'f'; 
    public $e21_ativo = 'f'; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 e21_sequencial = int4 = Código Sequencial 
                 e21_retencaotipocalc = int4 = Tipo de Cálculo 
                 e21_receita = int4 = Receita 
                 e21_descricao = varchar(100) = Descrição 
                 e21_aliquota = float8 = Aliquota 
                 e21_instit = int4 = Código da Instituição 
                 e21_retencaotiporecgrupo = int4 = Grupo 
                 e21_enterecebedor = int4 = Ente Recebedor 
                 e21_receitaenterecebedor = int4 = Receita do ente Recebedor 
                 e21_envioremessabancaria = bool = Permite pagamento por remessa bancária 
                 e21_ativo = bool = Ativo 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("retencaotiporec"); 
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
    }

    public function erro($mostra, $retorna)
    {
     if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
        echo "<script>alert(\"".$this->erro_msg."\");</script>";
        if($retorna==true){
           echo "<script>location.href='".$this->pagina_retorno."'</script>";
        }
     }
   }

    public function atualizacampos($exclusao = false)
    {
     if($exclusao==false){
       $this->e21_sequencial = ($this->e21_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["e21_sequencial"]:$this->e21_sequencial);
       $this->e21_retencaotipocalc = ($this->e21_retencaotipocalc == ""?@$GLOBALS["HTTP_POST_VARS"]["e21_retencaotipocalc"]:$this->e21_retencaotipocalc);
       $this->e21_receita = ($this->e21_receita == ""?@$GLOBALS["HTTP_POST_VARS"]["e21_receita"]:$this->e21_receita);
       $this->e21_descricao = ($this->e21_descricao == ""?@$GLOBALS["HTTP_POST_VARS"]["e21_descricao"]:$this->e21_descricao);
       $this->e21_aliquota = ($this->e21_aliquota == ""?@$GLOBALS["HTTP_POST_VARS"]["e21_aliquota"]:$this->e21_aliquota);
       $this->e21_instit = ($this->e21_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["e21_instit"]:$this->e21_instit);
       $this->e21_retencaotiporecgrupo = ($this->e21_retencaotiporecgrupo == ""?@$GLOBALS["HTTP_POST_VARS"]["e21_retencaotiporecgrupo"]:$this->e21_retencaotiporecgrupo);
       $this->e21_enterecebedor = ($this->e21_enterecebedor == ""?@$GLOBALS["HTTP_POST_VARS"]["e21_enterecebedor"]:$this->e21_enterecebedor);
       $this->e21_receitaenterecebedor = ($this->e21_receitaenterecebedor == ""?@$GLOBALS["HTTP_POST_VARS"]["e21_receitaenterecebedor"]:$this->e21_receitaenterecebedor);
       $this->e21_envioremessabancaria = ($this->e21_envioremessabancaria == "f"?@$GLOBALS["HTTP_POST_VARS"]["e21_envioremessabancaria"]:$this->e21_envioremessabancaria);
       $this->e21_ativo = ($this->e21_ativo == "f"?@$GLOBALS["HTTP_POST_VARS"]["e21_ativo"]:$this->e21_ativo);
     }else{
       $this->e21_sequencial = ($this->e21_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["e21_sequencial"]:$this->e21_sequencial);
     }
   }

    public function incluir($e21_sequencial)
    {
      $this->atualizacampos();
     if($this->e21_retencaotipocalc == null ){ 
       $this->erro_sql = " Campo Tipo de Cálculo não informado.";
       $this->erro_campo = "e21_retencaotipocalc";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->e21_receita == null ){ 
       $this->erro_sql = " Campo Receita não informado.";
       $this->erro_campo = "e21_receita";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->e21_descricao == null ){ 
       $this->erro_sql = " Campo Descrição não informado.";
       $this->erro_campo = "e21_descricao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->e21_aliquota == null ){ 
       $this->erro_sql = " Campo Aliquota não informado.";
       $this->erro_campo = "e21_aliquota";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->e21_instit == null ){ 
       $this->erro_sql = " Campo Código da Instituição não informado.";
       $this->erro_campo = "e21_instit";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->e21_retencaotiporecgrupo == null ){ 
       $this->erro_sql = " Campo Grupo não informado.";
       $this->erro_campo = "e21_retencaotiporecgrupo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->e21_enterecebedor == null ){ 
       $this->e21_enterecebedor = "null";
     }
     if($this->e21_receitaenterecebedor == null ){ 
       $this->e21_receitaenterecebedor = "null";
     }
     if($this->e21_envioremessabancaria == null ){ 
       $this->erro_sql = " Campo Permite pagamento por remessa bancária não informado.";
       $this->erro_campo = "e21_envioremessabancaria";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->e21_ativo == null ){ 
       $this->erro_sql = " Campo Ativo não informado.";
       $this->erro_campo = "e21_ativo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($e21_sequencial == "" || $e21_sequencial == null ){
       $result = db_query("select nextval('retencaotiporec_e21_sequencial_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: retencaotiporec_e21_sequencial_seq do campo: e21_sequencial"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->e21_sequencial = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from retencaotiporec_e21_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $e21_sequencial)){
         $this->erro_sql = " Campo e21_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->e21_sequencial = $e21_sequencial; 
       }
     }
     if(($this->e21_sequencial == null) || ($this->e21_sequencial == "") ){ 
       $this->erro_sql = " Campo e21_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into retencaotiporec(
                                       e21_sequencial 
                                      ,e21_retencaotipocalc 
                                      ,e21_receita 
                                      ,e21_descricao 
                                      ,e21_aliquota 
                                      ,e21_instit 
                                      ,e21_retencaotiporecgrupo 
                                      ,e21_enterecebedor 
                                      ,e21_receitaenterecebedor 
                                      ,e21_envioremessabancaria 
                                      ,e21_ativo 
                       )
                values (
                                $this->e21_sequencial 
                               ,$this->e21_retencaotipocalc 
                               ,$this->e21_receita 
                               ,'$this->e21_descricao' 
                               ,$this->e21_aliquota 
                               ,$this->e21_instit 
                               ,$this->e21_retencaotiporecgrupo 
                               ,$this->e21_enterecebedor 
                               ,$this->e21_receitaenterecebedor 
                               ,'$this->e21_envioremessabancaria' 
                               ,'$this->e21_ativo' 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Cadastro de Retenções ($this->e21_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Cadastro de Retenções já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Cadastro de Retenções ($this->e21_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->e21_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->e21_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,12159,'$this->e21_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,2112,12159,'','".AddSlashes(pg_result($resaco,0,'e21_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2112,12160,'','".AddSlashes(pg_result($resaco,0,'e21_retencaotipocalc'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2112,12161,'','".AddSlashes(pg_result($resaco,0,'e21_receita'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2112,12162,'','".AddSlashes(pg_result($resaco,0,'e21_descricao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2112,12163,'','".AddSlashes(pg_result($resaco,0,'e21_aliquota'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2112,12550,'','".AddSlashes(pg_result($resaco,0,'e21_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2112,14267,'','".AddSlashes(pg_result($resaco,0,'e21_retencaotiporecgrupo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2112,1010015,'','".AddSlashes(pg_result($resaco,0,'e21_enterecebedor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2112,1010032,'','".AddSlashes(pg_result($resaco,0,'e21_receitaenterecebedor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2112,1014457,'','".AddSlashes(pg_result($resaco,0,'e21_envioremessabancaria'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2112,1015227,'','".AddSlashes(pg_result($resaco,0,'e21_ativo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($e21_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update retencaotiporec set ";
     $virgula = "";
     if(trim($this->e21_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e21_sequencial"])){ 
       $sql  .= $virgula." e21_sequencial = $this->e21_sequencial ";
       $virgula = ",";
       if(trim($this->e21_sequencial) == null ){ 
         $this->erro_sql = " Campo Código Sequencial não informado.";
         $this->erro_campo = "e21_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e21_retencaotipocalc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e21_retencaotipocalc"])){ 
       $sql  .= $virgula." e21_retencaotipocalc = $this->e21_retencaotipocalc ";
       $virgula = ",";
       if(trim($this->e21_retencaotipocalc) == null ){ 
         $this->erro_sql = " Campo Tipo de Cálculo não informado.";
         $this->erro_campo = "e21_retencaotipocalc";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e21_receita)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e21_receita"])){ 
       $sql  .= $virgula." e21_receita = $this->e21_receita ";
       $virgula = ",";
       if(trim($this->e21_receita) == null ){ 
         $this->erro_sql = " Campo Receita não informado.";
         $this->erro_campo = "e21_receita";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e21_descricao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e21_descricao"])){ 
       $sql  .= $virgula." e21_descricao = '$this->e21_descricao' ";
       $virgula = ",";
       if(trim($this->e21_descricao) == null ){ 
         $this->erro_sql = " Campo Descrição não informado.";
         $this->erro_campo = "e21_descricao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e21_aliquota)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e21_aliquota"])){ 
       $sql  .= $virgula." e21_aliquota = $this->e21_aliquota ";
       $virgula = ",";
       if(trim($this->e21_aliquota) == null ){ 
         $this->erro_sql = " Campo Aliquota não informado.";
         $this->erro_campo = "e21_aliquota";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e21_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e21_instit"])){ 
       $sql  .= $virgula." e21_instit = $this->e21_instit ";
       $virgula = ",";
       if(trim($this->e21_instit) == null ){ 
         $this->erro_sql = " Campo Código da Instituição não informado.";
         $this->erro_campo = "e21_instit";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e21_retencaotiporecgrupo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e21_retencaotiporecgrupo"])){ 
       $sql  .= $virgula." e21_retencaotiporecgrupo = $this->e21_retencaotiporecgrupo ";
       $virgula = ",";
       if(trim($this->e21_retencaotiporecgrupo) == null ){ 
         $this->erro_sql = " Campo Grupo não informado.";
         $this->erro_campo = "e21_retencaotiporecgrupo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e21_enterecebedor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e21_enterecebedor"])){ 
        if(trim($this->e21_enterecebedor)=="" && isset($GLOBALS["HTTP_POST_VARS"]["e21_enterecebedor"])){ 
           $this->e21_enterecebedor = "null" ;
        } 
       $sql  .= $virgula." e21_enterecebedor = $this->e21_enterecebedor ";
       $virgula = ",";
     }
     if(trim($this->e21_receitaenterecebedor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e21_receitaenterecebedor"])){ 
        if(trim($this->e21_receitaenterecebedor)=="" && isset($GLOBALS["HTTP_POST_VARS"]["e21_receitaenterecebedor"])){ 
           $this->e21_receitaenterecebedor = "null" ;
        } 
       $sql  .= $virgula." e21_receitaenterecebedor = $this->e21_receitaenterecebedor ";
       $virgula = ",";
     }
     if(trim($this->e21_envioremessabancaria)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e21_envioremessabancaria"])){ 
       $sql  .= $virgula." e21_envioremessabancaria = '$this->e21_envioremessabancaria' ";
       $virgula = ",";
       if(trim($this->e21_envioremessabancaria) == null ){ 
         $this->erro_sql = " Campo Permite pagamento por remessa bancária não informado.";
         $this->erro_campo = "e21_envioremessabancaria";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e21_ativo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e21_ativo"])){ 
       $sql  .= $virgula." e21_ativo = '$this->e21_ativo' ";
       $virgula = ",";
       if(trim($this->e21_ativo) == null ){ 
         $this->erro_sql = " Campo Ativo não informado.";
         $this->erro_campo = "e21_ativo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($e21_sequencial!=null){
       $sql .= " e21_sequencial = $this->e21_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->e21_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,12159,'$this->e21_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["e21_sequencial"]) || $this->e21_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,2112,12159,'".AddSlashes(pg_result($resaco,$conresaco,'e21_sequencial'))."','$this->e21_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["e21_retencaotipocalc"]) || $this->e21_retencaotipocalc != "")
             $resac = db_query("insert into db_acount values($acount,2112,12160,'".AddSlashes(pg_result($resaco,$conresaco,'e21_retencaotipocalc'))."','$this->e21_retencaotipocalc',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["e21_receita"]) || $this->e21_receita != "")
             $resac = db_query("insert into db_acount values($acount,2112,12161,'".AddSlashes(pg_result($resaco,$conresaco,'e21_receita'))."','$this->e21_receita',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["e21_descricao"]) || $this->e21_descricao != "")
             $resac = db_query("insert into db_acount values($acount,2112,12162,'".AddSlashes(pg_result($resaco,$conresaco,'e21_descricao'))."','$this->e21_descricao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["e21_aliquota"]) || $this->e21_aliquota != "")
             $resac = db_query("insert into db_acount values($acount,2112,12163,'".AddSlashes(pg_result($resaco,$conresaco,'e21_aliquota'))."','$this->e21_aliquota',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["e21_instit"]) || $this->e21_instit != "")
             $resac = db_query("insert into db_acount values($acount,2112,12550,'".AddSlashes(pg_result($resaco,$conresaco,'e21_instit'))."','$this->e21_instit',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["e21_retencaotiporecgrupo"]) || $this->e21_retencaotiporecgrupo != "")
             $resac = db_query("insert into db_acount values($acount,2112,14267,'".AddSlashes(pg_result($resaco,$conresaco,'e21_retencaotiporecgrupo'))."','$this->e21_retencaotiporecgrupo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["e21_enterecebedor"]) || $this->e21_enterecebedor != "")
             $resac = db_query("insert into db_acount values($acount,2112,1010015,'".AddSlashes(pg_result($resaco,$conresaco,'e21_enterecebedor'))."','$this->e21_enterecebedor',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["e21_receitaenterecebedor"]) || $this->e21_receitaenterecebedor != "")
             $resac = db_query("insert into db_acount values($acount,2112,1010032,'".AddSlashes(pg_result($resaco,$conresaco,'e21_receitaenterecebedor'))."','$this->e21_receitaenterecebedor',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["e21_envioremessabancaria"]) || $this->e21_envioremessabancaria != "")
             $resac = db_query("insert into db_acount values($acount,2112,1014457,'".AddSlashes(pg_result($resaco,$conresaco,'e21_envioremessabancaria'))."','$this->e21_envioremessabancaria',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["e21_ativo"]) || $this->e21_ativo != "")
             $resac = db_query("insert into db_acount values($acount,2112,1015227,'".AddSlashes(pg_result($resaco,$conresaco,'e21_ativo'))."','$this->e21_ativo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Cadastro de Retenções não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->e21_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Cadastro de Retenções não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->e21_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->e21_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($e21_sequencial=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($e21_sequencial));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,12159,'$e21_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,2112,12159,'','".AddSlashes(pg_result($resaco,$iresaco,'e21_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2112,12160,'','".AddSlashes(pg_result($resaco,$iresaco,'e21_retencaotipocalc'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2112,12161,'','".AddSlashes(pg_result($resaco,$iresaco,'e21_receita'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2112,12162,'','".AddSlashes(pg_result($resaco,$iresaco,'e21_descricao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2112,12163,'','".AddSlashes(pg_result($resaco,$iresaco,'e21_aliquota'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2112,12550,'','".AddSlashes(pg_result($resaco,$iresaco,'e21_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2112,14267,'','".AddSlashes(pg_result($resaco,$iresaco,'e21_retencaotiporecgrupo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2112,1010015,'','".AddSlashes(pg_result($resaco,$iresaco,'e21_enterecebedor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2112,1010032,'','".AddSlashes(pg_result($resaco,$iresaco,'e21_receitaenterecebedor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2112,1014457,'','".AddSlashes(pg_result($resaco,$iresaco,'e21_envioremessabancaria'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2112,1015227,'','".AddSlashes(pg_result($resaco,$iresaco,'e21_ativo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from retencaotiporec
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($e21_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " e21_sequencial = $e21_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Cadastro de Retenções não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$e21_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Cadastro de Retenções não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$e21_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$e21_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:retencaotiporec";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query ($e21_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") {

        $sql  = "select {$campos}";
        $sql .= "  from retencaotiporec ";
        $sql .= "      inner join tabrec  on  tabrec.k02_codigo = retencaotiporec.e21_receita";
        $sql .= "      inner join db_config  on  db_config.codigo = retencaotiporec.e21_instit";
        $sql .= "      inner join retencaotipocalc  on  retencaotipocalc.e32_sequencial = retencaotiporec.e21_retencaotipocalc";
        $sql .= "      inner join retencaotiporecgrupo  on  retencaotiporecgrupo.e01_sequencial = retencaotiporec.e21_retencaotiporecgrupo";
        $sql .= "      inner join tabrecjm  on  tabrecjm.k02_codjm = tabrec.k02_codjm";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
        $sql2 = "";
        if($dbwhere==""){
            if($e21_sequencial!=null ){
                $sql2 .= " where retencaotiporec.e21_sequencial = $e21_sequencial ";
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

   public function sql_query_file ($e21_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from retencaotiporec ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($e21_sequencial)){
         $sql2 .= " where retencaotiporec.e21_sequencial = $e21_sequencial "; 
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

   public function sql_query_irrf ( $e21_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){

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
     $sql .= " from retencaotiporec ";
     $sql .= "      inner join tabrec  on  tabrec.k02_codigo = retencaotiporec.e21_receita";
     $sql .= "      inner join retencaotipocalc  on  retencaotipocalc.e32_sequencial = retencaotiporec.e21_retencaotipocalc";
     $sql .= "      inner join tabrecjm  on  tabrecjm.k02_codjm = tabrec.k02_codjm";
     $sql .= "      left  join retencaotiporeccgm       on  e48_retencaotiporec   = e21_sequencial";
     $sql .= "      left  join cgm                      on  z01_numcgm            = e48_cgm";
     $sql .= "      left  join retencaonaturezatiporec  on  e31_retencaotiporec   = e21_sequencial";
     $sql .= "      left  join retencaonatureza         on  e31_retencaonatureza  = e30_sequencial";
     $sql .= "      left  join tabrec receitaente on  receitaente.k02_codigo = retencaotiporec.e21_receitaenterecebedor";
     $sql2 = "";
     if($dbwhere==""){
       if($e21_sequencial!=null ){
         $sql2 .= " where retencaotiporec.e21_sequencial = $e21_sequencial "; 
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
