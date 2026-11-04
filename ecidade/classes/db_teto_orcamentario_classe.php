<?php
//MODULO: contabilidade
//CLASSE DA ENTIDADE teto_orcamentario
class cl_teto_orcamentario { 
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
   var $c40_sequencial = 0; 
   var $c40_ano = 0; 
   var $c40_orgao = 0; 
   var $c40_unidade = 0;
   var $c40_grupo_natureza_despesa = 0; 
   var $c40_identificador_uso = 0; 
   var $c40_tipo_detalhamento = null; 
   var $c40_grupo_fonte_recursos = null; 
   var $c40_especificacao_fonte = null; 
   var $c40_valor_teto = 0; 
   var $c40_valor_disponivel = 0; 
   // cria propriedade com as variaveis do arquivo 
   var $campos = "
                 c40_sequencial = int4 = Sequencial 
                 c40_ano = int4 = Ano 
                 c40_orgao = int4 = Órgão 
                 c40_unidade = int4 = Unidade 
                 c40_grupo_natureza_despesa = int4 = Grupo da Natureza da Despesa 
                 c40_identificador_uso = int4 = Identificador de Uso 
                 c40_tipo_detalhamento = varchar(2) = Tipo de Detalhamento 
                 c40_grupo_fonte_recursos = varchar(2) = Grupo de Fonte de Recursos 
                 c40_especificacao_fonte = varchar(2) = Especificação da Fonte 
                 c40_valor_teto = float8 = Valor do Teto 
                 c40_valor_disponivel = float8 = Valos Disponível 
                 ";
   //funcao construtor da classe 
   public function __construct() { 
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("teto_orcamentario"); 
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
       $this->c40_sequencial = ($this->c40_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["c40_sequencial"]:$this->c40_sequencial);
       $this->c40_ano = ($this->c40_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["c40_ano"]:$this->c40_ano);
       $this->c40_orgao = ($this->c40_orgao == ""?@$GLOBALS["HTTP_POST_VARS"]["c40_orgao"]:$this->c40_orgao);
       $this->c40_unidade = ($this->c40_unidade == ""?@$GLOBALS["HTTP_POST_VARS"]["c40_unidade"]:$this->c40_unidade);
       $this->c40_grupo_natureza_despesa = ($this->c40_grupo_natureza_despesa == ""?@$GLOBALS["HTTP_POST_VARS"]["c40_grupo_natureza_despesa"]:$this->c40_grupo_natureza_despesa);
       $this->c40_identificador_uso = ($this->c40_identificador_uso == ""?@$GLOBALS["HTTP_POST_VARS"]["c40_identificador_uso"]:$this->c40_identificador_uso);
       $this->c40_tipo_detalhamento = ($this->c40_tipo_detalhamento == ""?@$GLOBALS["HTTP_POST_VARS"]["c40_tipo_detalhamento"]:$this->c40_tipo_detalhamento);
       $this->c40_grupo_fonte_recursos = ($this->c40_grupo_fonte_recursos == ""?@$GLOBALS["HTTP_POST_VARS"]["c40_grupo_fonte_recursos"]:$this->c40_grupo_fonte_recursos);
       $this->c40_especificacao_fonte = ($this->c40_especificacao_fonte == ""?@$GLOBALS["HTTP_POST_VARS"]["c40_especificacao_fonte"]:$this->c40_especificacao_fonte);
       $this->c40_valor_teto = ($this->c40_valor_teto == ""?@$GLOBALS["HTTP_POST_VARS"]["c40_valor_teto"]:$this->c40_valor_teto);
       $this->c40_valor_disponivel = ($this->c40_valor_disponivel == ""?@$GLOBALS["HTTP_POST_VARS"]["c40_valor_disponivel"]:$this->c40_valor_disponivel);
     }else{
       $this->c40_sequencial = ($this->c40_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["c40_sequencial"]:$this->c40_sequencial);
     }
   }
   // funcao para Inclusão
   public function incluir ($c40_sequencial){ 
      $this->atualizacampos();
     if($this->c40_ano == null ){ 
       $this->erro_sql = " Campo Ano não informado.";
       $this->erro_campo = "c40_ano";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c40_orgao == null ){ 
       $this->erro_sql = " Campo Órgão não informado.";
       $this->erro_campo = "c40_orgao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c40_unidade == null ){ 
       $this->erro_sql = " Campo Unidade não informado.";
       $this->erro_campo = "c40_unidade";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c40_grupo_natureza_despesa == null ){ 
       $this->erro_sql = " Campo Grupo da Natureza da Despesa não informado.";
       $this->erro_campo = "c40_grupo_natureza_despesa";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c40_identificador_uso == null ){ 
       $this->erro_sql = " Campo Identificador de Uso não informado.";
       $this->erro_campo = "c40_identificador_uso";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c40_tipo_detalhamento == null ){ 
       $this->erro_sql = " Campo Tipo de Detalhamento não informado.";
       $this->erro_campo = "c40_tipo_detalhamento";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c40_grupo_fonte_recursos == null ){ 
       $this->erro_sql = " Campo Grupo de Fonte de Recursos não informado.";
       $this->erro_campo = "c40_grupo_fonte_recursos";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c40_especificacao_fonte == null ){ 
       $this->erro_sql = " Campo Especificação da Fonte não informado.";
       $this->erro_campo = "c40_especificacao_fonte";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c40_valor_teto == null ){ 
       $this->erro_sql = " Campo Valor do Teto não informado.";
       $this->erro_campo = "c40_valor_teto";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c40_valor_disponivel == null ){ 
       $this->erro_sql = " Campo Valos Disponível não informado.";
       $this->erro_campo = "c40_valor_disponivel";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($c40_sequencial == "" || $c40_sequencial == null ){
       $result = db_query("select nextval('teto_orcamentario_c40_sequencial_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: teto_orcamentario_c40_sequencial_seq do campo: c40_sequencial"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->c40_sequencial = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from teto_orcamentario_c40_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $c40_sequencial)){
         $this->erro_sql = " Campo c40_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->c40_sequencial = $c40_sequencial; 
       }
     }
     if(($this->c40_sequencial == null) || ($this->c40_sequencial == "") ){ 
       $this->erro_sql = " Campo c40_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into teto_orcamentario(
                                       c40_sequencial 
                                      ,c40_ano 
                                      ,c40_orgao 
                                      ,c40_unidade 
                                      ,c40_grupo_natureza_despesa 
                                      ,c40_identificador_uso 
                                      ,c40_tipo_detalhamento 
                                      ,c40_grupo_fonte_recursos 
                                      ,c40_especificacao_fonte 
                                      ,c40_valor_teto 
                                      ,c40_valor_disponivel 
                       )
                values (
                                $this->c40_sequencial 
                               ,$this->c40_ano 
                               ,$this->c40_orgao 
                               ,$this->c40_unidade 
                               ,$this->c40_grupo_natureza_despesa 
                               ,$this->c40_identificador_uso 
                               ,'$this->c40_tipo_detalhamento' 
                               ,'$this->c40_grupo_fonte_recursos' 
                               ,'$this->c40_especificacao_fonte' 
                               ,$this->c40_valor_teto 
                               ,$this->c40_valor_disponivel 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "teto_orcamentario ($this->c40_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "teto_orcamentario já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "teto_orcamentario ($this->c40_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->c40_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->c40_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1009864,'$this->c40_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,1010300,1009864,'','".AddSlashes(pg_result($resaco,0,'c40_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010300,1009865,'','".AddSlashes(pg_result($resaco,0,'c40_ano'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010300,1009866,'','".AddSlashes(pg_result($resaco,0,'c40_orgao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010300,1009867,'','".AddSlashes(pg_result($resaco,0,'c40_unidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010300,1009871,'','".AddSlashes(pg_result($resaco,0,'c40_grupo_natureza_despesa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010300,1009872,'','".AddSlashes(pg_result($resaco,0,'c40_identificador_uso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010300,1009873,'','".AddSlashes(pg_result($resaco,0,'c40_tipo_detalhamento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010300,1009874,'','".AddSlashes(pg_result($resaco,0,'c40_grupo_fonte_recursos'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010300,1009875,'','".AddSlashes(pg_result($resaco,0,'c40_especificacao_fonte'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010300,1009876,'','".AddSlashes(pg_result($resaco,0,'c40_valor_teto'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010300,1009877,'','".AddSlashes(pg_result($resaco,0,'c40_valor_disponivel'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 
   // funcao para alteracao
   public function alterar ($c40_sequencial=null) { 
      $this->atualizacampos();
     $sql = " update teto_orcamentario set ";
     $virgula = "";
     if(trim($this->c40_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c40_sequencial"])){ 
       $sql  .= $virgula." c40_sequencial = $this->c40_sequencial ";
       $virgula = ",";
       if(trim($this->c40_sequencial) == null ){ 
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "c40_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c40_ano)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c40_ano"])){ 
       $sql  .= $virgula." c40_ano = $this->c40_ano ";
       $virgula = ",";
       if(trim($this->c40_ano) == null ){ 
         $this->erro_sql = " Campo Ano não informado.";
         $this->erro_campo = "c40_ano";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c40_orgao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c40_orgao"])){ 
       $sql  .= $virgula." c40_orgao = $this->c40_orgao ";
       $virgula = ",";
       if(trim($this->c40_orgao) == null ){ 
         $this->erro_sql = " Campo Órgão não informado.";
         $this->erro_campo = "c40_orgao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c40_unidade)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c40_unidade"])){ 
       $sql  .= $virgula." c40_unidade = $this->c40_unidade ";
       $virgula = ",";
       if(trim($this->c40_unidade) == null ){ 
         $this->erro_sql = " Campo Unidade não informado.";
         $this->erro_campo = "c40_unidade";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c40_grupo_natureza_despesa)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c40_grupo_natureza_despesa"])){ 
       $sql  .= $virgula." c40_grupo_natureza_despesa = $this->c40_grupo_natureza_despesa ";
       $virgula = ",";
       if(trim($this->c40_grupo_natureza_despesa) == null ){ 
         $this->erro_sql = " Campo Grupo da Natureza da Despesa não informado.";
         $this->erro_campo = "c40_grupo_natureza_despesa";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c40_identificador_uso)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c40_identificador_uso"])){ 
       $sql  .= $virgula." c40_identificador_uso = $this->c40_identificador_uso ";
       $virgula = ",";
       if(trim($this->c40_identificador_uso) == null ){ 
         $this->erro_sql = " Campo Identificador de Uso não informado.";
         $this->erro_campo = "c40_identificador_uso";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c40_tipo_detalhamento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c40_tipo_detalhamento"])){ 
       $sql  .= $virgula." c40_tipo_detalhamento = '$this->c40_tipo_detalhamento' ";
       $virgula = ",";
       if(trim($this->c40_tipo_detalhamento) == null ){ 
         $this->erro_sql = " Campo Tipo de Detalhamento não informado.";
         $this->erro_campo = "c40_tipo_detalhamento";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c40_grupo_fonte_recursos)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c40_grupo_fonte_recursos"])){ 
       $sql  .= $virgula." c40_grupo_fonte_recursos = '$this->c40_grupo_fonte_recursos' ";
       $virgula = ",";
       if(trim($this->c40_grupo_fonte_recursos) == null ){ 
         $this->erro_sql = " Campo Grupo de Fonte de Recursos não informado.";
         $this->erro_campo = "c40_grupo_fonte_recursos";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c40_especificacao_fonte)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c40_especificacao_fonte"])){ 
       $sql  .= $virgula." c40_especificacao_fonte = '$this->c40_especificacao_fonte' ";
       $virgula = ",";
       if(trim($this->c40_especificacao_fonte) == null ){ 
         $this->erro_sql = " Campo Especificação da Fonte não informado.";
         $this->erro_campo = "c40_especificacao_fonte";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c40_valor_teto)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c40_valor_teto"])){ 
       $sql  .= $virgula." c40_valor_teto = $this->c40_valor_teto ";
       $virgula = ",";
       if(trim($this->c40_valor_teto) == null ){ 
         $this->erro_sql = " Campo Valor do Teto não informado.";
         $this->erro_campo = "c40_valor_teto";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c40_valor_disponivel)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c40_valor_disponivel"])){ 
       $sql  .= $virgula." c40_valor_disponivel = $this->c40_valor_disponivel ";
       $virgula = ",";
       if(trim($this->c40_valor_disponivel) == null ){ 
         $this->erro_sql = " Campo Valos Disponível não informado.";
         $this->erro_campo = "c40_valor_disponivel";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($c40_sequencial!=null){
       $sql .= " c40_sequencial = $this->c40_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->c40_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1009864,'$this->c40_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c40_sequencial"]) || $this->c40_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,1010300,1009864,'".AddSlashes(pg_result($resaco,$conresaco,'c40_sequencial'))."','$this->c40_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c40_ano"]) || $this->c40_ano != "")
             $resac = db_query("insert into db_acount values($acount,1010300,1009865,'".AddSlashes(pg_result($resaco,$conresaco,'c40_ano'))."','$this->c40_ano',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c40_orgao"]) || $this->c40_orgao != "")
             $resac = db_query("insert into db_acount values($acount,1010300,1009866,'".AddSlashes(pg_result($resaco,$conresaco,'c40_orgao'))."','$this->c40_orgao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c40_unidade"]) || $this->c40_unidade != "")
             $resac = db_query("insert into db_acount values($acount,1010300,1009867,'".AddSlashes(pg_result($resaco,$conresaco,'c40_unidade'))."','$this->c40_unidade',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c40_grupo_natureza_despesa"]) || $this->c40_grupo_natureza_despesa != "")
             $resac = db_query("insert into db_acount values($acount,1010300,1009871,'".AddSlashes(pg_result($resaco,$conresaco,'c40_grupo_natureza_despesa'))."','$this->c40_grupo_natureza_despesa',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c40_identificador_uso"]) || $this->c40_identificador_uso != "")
             $resac = db_query("insert into db_acount values($acount,1010300,1009872,'".AddSlashes(pg_result($resaco,$conresaco,'c40_identificador_uso'))."','$this->c40_identificador_uso',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c40_tipo_detalhamento"]) || $this->c40_tipo_detalhamento != "")
             $resac = db_query("insert into db_acount values($acount,1010300,1009873,'".AddSlashes(pg_result($resaco,$conresaco,'c40_tipo_detalhamento'))."','$this->c40_tipo_detalhamento',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c40_grupo_fonte_recursos"]) || $this->c40_grupo_fonte_recursos != "")
             $resac = db_query("insert into db_acount values($acount,1010300,1009874,'".AddSlashes(pg_result($resaco,$conresaco,'c40_grupo_fonte_recursos'))."','$this->c40_grupo_fonte_recursos',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c40_especificacao_fonte"]) || $this->c40_especificacao_fonte != "")
             $resac = db_query("insert into db_acount values($acount,1010300,1009875,'".AddSlashes(pg_result($resaco,$conresaco,'c40_especificacao_fonte'))."','$this->c40_especificacao_fonte',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c40_valor_teto"]) || $this->c40_valor_teto != "")
             $resac = db_query("insert into db_acount values($acount,1010300,1009876,'".AddSlashes(pg_result($resaco,$conresaco,'c40_valor_teto'))."','$this->c40_valor_teto',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c40_valor_disponivel"]) || $this->c40_valor_disponivel != "")
             $resac = db_query("insert into db_acount values($acount,1010300,1009877,'".AddSlashes(pg_result($resaco,$conresaco,'c40_valor_disponivel'))."','$this->c40_valor_disponivel',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "teto_orcamentario não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->c40_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "teto_orcamentario não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->c40_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->c40_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   public function excluir ($c40_sequencial=null,$dbwhere=null) { 

     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($c40_sequencial));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1009864,'$c40_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,1010300,1009864,'','".AddSlashes(pg_result($resaco,$iresaco,'c40_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010300,1009865,'','".AddSlashes(pg_result($resaco,$iresaco,'c40_ano'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010300,1009866,'','".AddSlashes(pg_result($resaco,$iresaco,'c40_orgao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010300,1009867,'','".AddSlashes(pg_result($resaco,$iresaco,'c40_unidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010300,1009871,'','".AddSlashes(pg_result($resaco,$iresaco,'c40_grupo_natureza_despesa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010300,1009872,'','".AddSlashes(pg_result($resaco,$iresaco,'c40_identificador_uso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010300,1009873,'','".AddSlashes(pg_result($resaco,$iresaco,'c40_tipo_detalhamento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010300,1009874,'','".AddSlashes(pg_result($resaco,$iresaco,'c40_grupo_fonte_recursos'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010300,1009875,'','".AddSlashes(pg_result($resaco,$iresaco,'c40_especificacao_fonte'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010300,1009876,'','".AddSlashes(pg_result($resaco,$iresaco,'c40_valor_teto'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010300,1009877,'','".AddSlashes(pg_result($resaco,$iresaco,'c40_valor_disponivel'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from teto_orcamentario
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($c40_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " c40_sequencial = $c40_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "teto_orcamentario não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$c40_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "teto_orcamentario não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$c40_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$c40_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:teto_orcamentario";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql 
   public function sql_query ($c40_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from teto_orcamentario ";
     $sql .= "    INNER JOIN orcorgao ON orcorgao.o40_anousu = teto_orcamentario.c40_ano AND orcorgao.o40_orgao = teto_orcamentario.c40_orgao ";
     $sql .= "    INNER JOIN orcunidade ON orcunidade.o41_anousu = teto_orcamentario.c40_ano AND orcunidade.o41_unidade = teto_orcamentario.c40_unidade AND orcunidade.o41_orgao = teto_orcamentario.c40_orgao ";
     $sql .= "    INNER JOIN db_config ON db_config.codigo = orcorgao.o40_instit ";
     $sql .= "    INNER JOIN db_config AS a ON a.codigo = orcunidade.o41_instit ";
     $sql .= "    INNER JOIN orcorgao AS b ON b.o40_anousu = orcunidade.o41_anousu AND b.o40_orgao = orcunidade.o41_orgao ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($c40_sequencial)) {
         $sql2 .= " where teto_orcamentario.c40_sequencial = $c40_sequencial "; 
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
   public function sql_query_file ($c40_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from teto_orcamentario ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($c40_sequencial)){
         $sql2 .= " where teto_orcamentario.c40_sequencial = $c40_sequencial "; 
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

  public function update_valor_disponivel($codigo, $valorNovo)
  {
      $update  = " update teto_orcamentario ";
      $update .= "    set c40_valor_disponivel = {$valorNovo} ";
      $update .= "  where c40_sequencial = {$codigo}; ";

      return $update;
  }

}
