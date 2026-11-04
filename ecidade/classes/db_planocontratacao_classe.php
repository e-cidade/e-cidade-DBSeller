<?php

class cl_planocontratacao
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
    public $pn05_codigo = 0;
    public $pn05_unidade = 0;
    public $pn05_ano = 0;
    public $pn05_instituicao = 0;
    public $pn05_status = 0;
    public $pn05_pncp = 0;
    public $pn05_itens = 0;
    public $pn05_datacadastro_dia = null;
    public $pn05_datacadastro_mes = null;
    public $pn05_datacadastro_ano = null;
    public $pn05_datacadastro = null;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 pn05_codigo = int4 = Código
                 pn05_unidade = bpchar(100) = Unidade Compradora
                 pn05_ano = int4 = Ano do Plano
                 pn05_instituicao = int4 = Instituicao
                 pn05_status = int4 = Status da Elaboracao
                 pn05_pncp = int4 = Sequencial no PNCP
                 pn05_itens = jsonb = Itens do Plano
                 pn05_datacadastro = date = Data do Cadastro
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("planocontratacao");
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
       $this->pn05_codigo = ($this->pn05_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["pn05_codigo"]:$this->pn05_codigo);
       $this->pn05_unidade = ($this->pn05_unidade == ""?@$GLOBALS["HTTP_POST_VARS"]["pn05_unidade"]:$this->pn05_unidade);
       $this->pn05_ano = ($this->pn05_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["pn05_ano"]:$this->pn05_ano);
       $this->pn05_instituicao = ($this->pn05_instituicao == ""?@$GLOBALS["HTTP_POST_VARS"]["pn05_instituicao"]:$this->pn05_instituicao);
       $this->pn05_status = ($this->pn05_status == ""?@$GLOBALS["HTTP_POST_VARS"]["pn05_status"]:$this->pn05_status);
       $this->pn05_pncp = ($this->pn05_pncp == ""?@$GLOBALS["HTTP_POST_VARS"]["pn05_pncp"]:$this->pn05_pncp);
       $this->pn05_itens = ($this->pn05_itens == ""?@$GLOBALS["HTTP_POST_VARS"]["pn05_itens"]:$this->pn05_itens);
       if($this->pn05_datacadastro == ""){
         $this->pn05_datacadastro_dia = ($this->pn05_datacadastro_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["pn05_datacadastro_dia"]:$this->pn05_datacadastro_dia);
         $this->pn05_datacadastro_mes = ($this->pn05_datacadastro_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["pn05_datacadastro_mes"]:$this->pn05_datacadastro_mes);
         $this->pn05_datacadastro_ano = ($this->pn05_datacadastro_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["pn05_datacadastro_ano"]:$this->pn05_datacadastro_ano);
         if($this->pn05_datacadastro_dia != ""){
            $this->pn05_datacadastro = $this->pn05_datacadastro_ano."-".$this->pn05_datacadastro_mes."-".$this->pn05_datacadastro_dia;
         }
       }
     }else{
       $this->pn05_codigo = ($this->pn05_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["pn05_codigo"]:$this->pn05_codigo);
     }
   }

    public function incluir($pn05_codigo)
    {
      $this->atualizacampos();
     if($this->pn05_unidade == null ){
       $this->erro_sql = " Campo Unidade Compradora não informado.";
       $this->erro_campo = "pn05_unidade";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->pn05_ano == null ){
       $this->erro_sql = " Campo Ano do Plano não informado.";
       $this->erro_campo = "pn05_ano";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->pn05_instituicao == null ){
       $this->erro_sql = " Campo Instituicao não informado.";
       $this->erro_campo = "pn05_instituicao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->pn05_status == null ){
       $this->erro_sql = " Campo Status da Elaboracao não informado.";
       $this->erro_campo = "pn05_status";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->pn05_datacadastro == null ){
       $this->erro_sql = " Campo Data do Cadastro não informado.";
       $this->erro_campo = "pn05_datacadastro_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($pn05_codigo == "" || $pn05_codigo == null ){
       $result = db_query("select nextval('planocontratacao_pn05_codigo_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: planocontratacao_pn05_codigo_seq do campo: pn05_codigo";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->pn05_codigo = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from planocontratacao_pn05_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $pn05_codigo)){
         $this->erro_sql = " Campo pn05_codigo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->pn05_codigo = $pn05_codigo;
       }
     }
     if(($this->pn05_codigo == null) || ($this->pn05_codigo == "") ){
       $this->erro_sql = " Campo pn05_codigo não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into planocontratacao(
                                       pn05_codigo
                                      ,pn05_unidade
                                      ,pn05_ano
                                      ,pn05_instituicao
                                      ,pn05_status
                                      ,pn05_pncp
                                      ,pn05_itens
                                      ,pn05_datacadastro
                       )
                values (
                                $this->pn05_codigo
                               ,$this->pn05_unidade
                               ,$this->pn05_ano
                               ,$this->pn05_instituicao
                               ,$this->pn05_status
                               ,$this->pn05_pncp
                               ,$this->pn05_itens
                               ,".($this->pn05_datacadastro == "null" || $this->pn05_datacadastro == ""?"null":"'".$this->pn05_datacadastro."'")."
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Plano Contratacao ($this->pn05_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Plano Contratacao já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Plano Contratacao ($this->pn05_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->pn05_codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->pn05_codigo  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,69717147,'$this->pn05_codigo','I')");
         $resac = db_query("insert into db_acount values($acount,264770203,69717147,'','".AddSlashes(pg_result($resaco,0,'pn05_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,264770203,124953584,'','".AddSlashes(pg_result($resaco,0,'pn05_unidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,264770203,307934481,'','".AddSlashes(pg_result($resaco,0,'pn05_ano'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,264770203,169404082,'','".AddSlashes(pg_result($resaco,0,'pn05_instituicao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,264770203,289507325,'','".AddSlashes(pg_result($resaco,0,'pn05_status'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,264770203,254174170,'','".AddSlashes(pg_result($resaco,0,'pn05_pncp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,264770203,170728852,'','".AddSlashes(pg_result($resaco,0,'pn05_itens'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,264770203,138413049,'','".AddSlashes(pg_result($resaco,0,'pn05_datacadastro'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }

    public function alterar($pn05_codigo=null)
    {
      $this->atualizacampos();
     $sql = " update planocontratacao set ";
     $virgula = "";
     if(trim($this->pn05_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pn05_codigo"])){
       $sql  .= $virgula." pn05_codigo = $this->pn05_codigo ";
       $virgula = ",";
       if(trim($this->pn05_codigo) == null ){
         $this->erro_sql = " Campo Código não informado.";
         $this->erro_campo = "pn05_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->pn05_unidade)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pn05_unidade"])){
       $sql  .= $virgula." pn05_unidade = $this->pn05_unidade ";
       $virgula = ",";
       if(trim($this->pn05_unidade) == null ){
         $this->erro_sql = " Campo Unidade Compradora não informado.";
         $this->erro_campo = "pn05_unidade";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->pn05_ano)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pn05_ano"])){
       $sql  .= $virgula." pn05_ano = $this->pn05_ano ";
       $virgula = ",";
       if(trim($this->pn05_ano) == null ){
         $this->erro_sql = " Campo Ano do Plano não informado.";
         $this->erro_campo = "pn05_ano";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->pn05_instituicao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pn05_instituicao"])){
       $sql  .= $virgula." pn05_instituicao = $this->pn05_instituicao ";
       $virgula = ",";
       if(trim($this->pn05_instituicao) == null ){
         $this->erro_sql = " Campo Instituicao não informado.";
         $this->erro_campo = "pn05_instituicao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->pn05_status)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pn05_status"])){
       $sql  .= $virgula." pn05_status = $this->pn05_status ";
       $virgula = ",";
       if(trim($this->pn05_status) == null ){
         $this->erro_sql = " Campo Status da Elaboracao não informado.";
         $this->erro_campo = "pn05_status";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->pn05_pncp)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pn05_pncp"])){
        if(trim($this->pn05_pncp)=="" && isset($GLOBALS["HTTP_POST_VARS"]["pn05_pncp"])){
           $this->pn05_pncp = "0" ;
        }
       $sql  .= $virgula." pn05_pncp = $this->pn05_pncp ";
       $virgula = ",";
     }
     if(trim($this->pn05_itens)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pn05_itens"])){
       $sql  .= $virgula." pn05_itens = $this->pn05_itens ";
       $virgula = ",";
     }
     if(trim($this->pn05_datacadastro)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pn05_datacadastro_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["pn05_datacadastro_dia"] !="") ){
       $sql  .= $virgula." pn05_datacadastro = '$this->pn05_datacadastro' ";
       $virgula = ",";
       if(trim($this->pn05_datacadastro) == null ){
         $this->erro_sql = " Campo Data do Cadastro não informado.";
         $this->erro_campo = "pn05_datacadastro_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["pn05_datacadastro_dia"])){
         $sql  .= $virgula." pn05_datacadastro = null ";
         $virgula = ",";
         if(trim($this->pn05_datacadastro) == null ){
           $this->erro_sql = " Campo Data do Cadastro não informado.";
           $this->erro_campo = "pn05_datacadastro_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     $sql .= " where ";
     if($pn05_codigo!=null){
       $sql .= " pn05_codigo = $this->pn05_codigo";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->pn05_codigo));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,69717147,'$this->pn05_codigo','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["pn05_codigo"]) || $this->pn05_codigo != "")
             $resac = db_query("insert into db_acount values($acount,264770203,69717147,'".AddSlashes(pg_result($resaco,$conresaco,'pn05_codigo'))."','$this->pn05_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["pn05_unidade"]) || $this->pn05_unidade != "")
             $resac = db_query("insert into db_acount values($acount,264770203,124953584,'".AddSlashes(pg_result($resaco,$conresaco,'pn05_unidade'))."','$this->pn05_unidade',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["pn05_ano"]) || $this->pn05_ano != "")
             $resac = db_query("insert into db_acount values($acount,264770203,307934481,'".AddSlashes(pg_result($resaco,$conresaco,'pn05_ano'))."','$this->pn05_ano',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["pn05_instituicao"]) || $this->pn05_instituicao != "")
             $resac = db_query("insert into db_acount values($acount,264770203,169404082,'".AddSlashes(pg_result($resaco,$conresaco,'pn05_instituicao'))."','$this->pn05_instituicao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["pn05_status"]) || $this->pn05_status != "")
             $resac = db_query("insert into db_acount values($acount,264770203,289507325,'".AddSlashes(pg_result($resaco,$conresaco,'pn05_status'))."','$this->pn05_status',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["pn05_pncp"]) || $this->pn05_pncp != "")
             $resac = db_query("insert into db_acount values($acount,264770203,254174170,'".AddSlashes(pg_result($resaco,$conresaco,'pn05_pncp'))."','$this->pn05_pncp',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["pn05_itens"]) || $this->pn05_itens != "")
             $resac = db_query("insert into db_acount values($acount,264770203,170728852,'".AddSlashes(pg_result($resaco,$conresaco,'pn05_itens'))."','$this->pn05_itens',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["pn05_datacadastro"]) || $this->pn05_datacadastro != "")
             $resac = db_query("insert into db_acount values($acount,264770203,138413049,'".AddSlashes(pg_result($resaco,$conresaco,'pn05_datacadastro'))."','$this->pn05_datacadastro',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Plano Contratacao não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->pn05_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Plano Contratacao não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->pn05_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->pn05_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($pn05_codigo=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($pn05_codigo));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,69717147,'$pn05_codigo','E')");
           $resac  = db_query("insert into db_acount values($acount,264770203,69717147,'','".AddSlashes(pg_result($resaco,$iresaco,'pn05_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,264770203,124953584,'','".AddSlashes(pg_result($resaco,$iresaco,'pn05_unidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,264770203,307934481,'','".AddSlashes(pg_result($resaco,$iresaco,'pn05_ano'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,264770203,169404082,'','".AddSlashes(pg_result($resaco,$iresaco,'pn05_instituicao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,264770203,289507325,'','".AddSlashes(pg_result($resaco,$iresaco,'pn05_status'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,264770203,254174170,'','".AddSlashes(pg_result($resaco,$iresaco,'pn05_pncp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,264770203,170728852,'','".AddSlashes(pg_result($resaco,$iresaco,'pn05_itens'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,264770203,138413049,'','".AddSlashes(pg_result($resaco,$iresaco,'pn05_datacadastro'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from planocontratacao
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($pn05_codigo)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " pn05_codigo = $pn05_codigo ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Plano Contratacao não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$pn05_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Plano Contratacao não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$pn05_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$pn05_codigo;
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
        $this->erro_sql   = "Record Vazio na Tabela:planocontratacao";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($pn05_codigo = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
     $sql .= "  from planocontratacao ";
     $sql .= "  join unidadespncp on pn05_unidade = pn02_unidade ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($pn05_codigo)) {
         $sql2 .= " where planocontratacao.pn05_codigo = $pn05_codigo ";
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

    public function sql_query_file($pn05_codigo = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from planocontratacao ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($pn05_codigo)){
         $sql2 .= " where planocontratacao.pn05_codigo = $pn05_codigo ";
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
