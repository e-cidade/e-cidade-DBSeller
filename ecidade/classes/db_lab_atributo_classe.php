<?php

class cl_lab_atributo
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
    public $la25_i_codigo = 0;
    public $la25_c_estrutural = null;
    public $la25_c_descr = null;
    public $la25_c_tipo = null;
    public $la25_i_nivel = 0;
    public $la25_sigla = null;
    public $la25_formula = null;
    public $la25_preenchimentoobrigatorio = 'f';
    public $la25_outrasinformacoes = null;
    public $la25_unidademedida = null;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 la25_i_codigo = int4 = Código
                 la25_c_estrutural = char(20) = Estrutural
                 la25_c_descr = char(50) = Descrição
                 la25_c_tipo = char(1) = Tipo
                 la25_i_nivel = int4 = Nível
                 la25_sigla = varchar(5) = Sigla
                 la25_formula = varchar(100) = Fórmula
                 la25_preenchimentoobrigatorio = bool = Campo para informar se o campo permite valor vazio
                 la25_outrasinformacoes = varchar(20) = Exibir dado na digitação
                 la25_unidademedida = int4 = Unidade de medida
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("lab_atributo");
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
       $this->la25_i_codigo = ($this->la25_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["la25_i_codigo"]:$this->la25_i_codigo);
       $this->la25_c_estrutural = ($this->la25_c_estrutural == ""?@$GLOBALS["HTTP_POST_VARS"]["la25_c_estrutural"]:$this->la25_c_estrutural);
       $this->la25_c_descr = ($this->la25_c_descr == ""?@$GLOBALS["HTTP_POST_VARS"]["la25_c_descr"]:$this->la25_c_descr);
       $this->la25_c_tipo = ($this->la25_c_tipo == ""?@$GLOBALS["HTTP_POST_VARS"]["la25_c_tipo"]:$this->la25_c_tipo);
       $this->la25_i_nivel = ($this->la25_i_nivel == ""?@$GLOBALS["HTTP_POST_VARS"]["la25_i_nivel"]:$this->la25_i_nivel);
       $this->la25_sigla = ($this->la25_sigla == ""?@$GLOBALS["HTTP_POST_VARS"]["la25_sigla"]:$this->la25_sigla);
       $this->la25_formula = ($this->la25_formula == ""?@$GLOBALS["HTTP_POST_VARS"]["la25_formula"]:$this->la25_formula);
       $this->la25_preenchimentoobrigatorio = ($this->la25_preenchimentoobrigatorio == "f"?@$GLOBALS["HTTP_POST_VARS"]["la25_preenchimentoobrigatorio"]:$this->la25_preenchimentoobrigatorio);
       $this->la25_outrasinformacoes = ($this->la25_outrasinformacoes == ""?@$GLOBALS["HTTP_POST_VARS"]["la25_outrasinformacoes"]:$this->la25_outrasinformacoes);
       $this->la25_unidademedida = ($this->la25_unidademedida == ""?@$GLOBALS["HTTP_POST_VARS"]["la25_unidademedida"]:$this->la25_unidademedida);
     }else{
       $this->la25_i_codigo = ($this->la25_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["la25_i_codigo"]:$this->la25_i_codigo);
     }
   }

    public function incluir($la25_i_codigo)
    {
      $this->atualizacampos();
     if($this->la25_c_estrutural == null ){
       $this->erro_sql = " Campo Estrutural não informado.";
       $this->erro_campo = "la25_c_estrutural";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->la25_c_descr == null ){
       $this->erro_sql = " Campo Descrição não informado.";
       $this->erro_campo = "la25_c_descr";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->la25_c_tipo == null ){
       $this->erro_sql = " Campo Tipo não informado.";
       $this->erro_campo = "la25_c_tipo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->la25_i_nivel == null ){
       $this->erro_sql = " Campo Nível não informado.";
       $this->erro_campo = "la25_i_nivel";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->la25_preenchimentoobrigatorio == null ){
       $this->erro_sql = " Campo Campo para informar se o campo permite valor vazio não informado.";
       $this->erro_campo = "la25_preenchimentoobrigatorio";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($la25_i_codigo == "" || $la25_i_codigo == null ){
       $result = db_query("select nextval('lab_atributo_la25_i_codigo_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: lab_atributo_la25_i_codigo_seq do campo: la25_i_codigo";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->la25_i_codigo = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from lab_atributo_la25_i_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $la25_i_codigo)){
         $this->erro_sql = " Campo la25_i_codigo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->la25_i_codigo = $la25_i_codigo;
       }
     }
     if(($this->la25_i_codigo == null) || ($this->la25_i_codigo == "") ){
       $this->erro_sql = " Campo la25_i_codigo não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->la25_unidademedida == null){
       $this->la25_unidademedida = 'NULL';
     }
     $sql = "insert into lab_atributo(
                                       la25_i_codigo
                                      ,la25_c_estrutural
                                      ,la25_c_descr
                                      ,la25_c_tipo
                                      ,la25_i_nivel
                                      ,la25_sigla
                                      ,la25_formula
                                      ,la25_preenchimentoobrigatorio
                                      ,la25_outrasinformacoes
                                      ,la25_unidademedida

                       )
                values (
                                $this->la25_i_codigo
                               ,'$this->la25_c_estrutural'
                               ,'$this->la25_c_descr'
                               ,'$this->la25_c_tipo'
                               ,$this->la25_i_nivel
                               ,'" . trim($this->la25_sigla) . "'
                               ,'$this->la25_formula'
                               ,'$this->la25_preenchimentoobrigatorio'
                               ,'$this->la25_outrasinformacoes'
                               ,$this->la25_unidademedida
                      )";     
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Atributo ($this->la25_i_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Atributo já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Atributo ($this->la25_i_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->la25_i_codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->la25_i_codigo  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,16489,'$this->la25_i_codigo','I')");
         $resac = db_query("insert into db_acount values($acount,2899,16489,'','".AddSlashes(pg_result($resaco,0,'la25_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2899,16492,'','".AddSlashes(pg_result($resaco,0,'la25_c_estrutural'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2899,16493,'','".AddSlashes(pg_result($resaco,0,'la25_c_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2899,16494,'','".AddSlashes(pg_result($resaco,0,'la25_c_tipo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2899,16495,'','".AddSlashes(pg_result($resaco,0,'la25_i_nivel'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2899,1010782,'','".AddSlashes(pg_result($resaco,0,'la25_sigla'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2899,1010783,'','".AddSlashes(pg_result($resaco,0,'la25_formula'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2899,1010909,'','".AddSlashes(pg_result($resaco,0,'la25_preenchimentoobrigatorio'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2899,1015483,'','".AddSlashes(pg_result($resaco,0,'la25_outrasinformacoes'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2899,1015634,'','".AddSlashes(pg_result($resaco,0,'la25_unidademedida'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }

    public function alterar($la25_i_codigo=null)
    {
      $this->atualizacampos();
     $sql = " update lab_atributo set ";
     $virgula = "";
     if(trim($this->la25_i_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["la25_i_codigo"])){
       $sql  .= $virgula." la25_i_codigo = $this->la25_i_codigo ";
       $virgula = ",";
       if(trim($this->la25_i_codigo) == null ){
         $this->erro_sql = " Campo Código não informado.";
         $this->erro_campo = "la25_i_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->la25_c_estrutural)!="" || isset($GLOBALS["HTTP_POST_VARS"]["la25_c_estrutural"])){
       $sql  .= $virgula." la25_c_estrutural = '$this->la25_c_estrutural' ";
       $virgula = ",";
       if(trim($this->la25_c_estrutural) == null ){
         $this->erro_sql = " Campo Estrutural não informado.";
         $this->erro_campo = "la25_c_estrutural";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->la25_c_descr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["la25_c_descr"])){
       $sql  .= $virgula." la25_c_descr = '$this->la25_c_descr' ";
       $virgula = ",";
       if(trim($this->la25_c_descr) == null ){
         $this->erro_sql = " Campo Descrição não informado.";
         $this->erro_campo = "la25_c_descr";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->la25_c_tipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["la25_c_tipo"])){
       $sql  .= $virgula." la25_c_tipo = '$this->la25_c_tipo' ";
       $virgula = ",";
       if(trim($this->la25_c_tipo) == null ){
         $this->erro_sql = " Campo Tipo não informado.";
         $this->erro_campo = "la25_c_tipo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->la25_i_nivel)!="" || isset($GLOBALS["HTTP_POST_VARS"]["la25_i_nivel"])){
       $sql  .= $virgula." la25_i_nivel = $this->la25_i_nivel ";
       $virgula = ",";
       if(trim($this->la25_i_nivel) == null ){
         $this->erro_sql = " Campo Nível não informado.";
         $this->erro_campo = "la25_i_nivel";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->la25_sigla)!="" || isset($GLOBALS["HTTP_POST_VARS"]["la25_sigla"])){
         $sql .= $virgula . " la25_sigla = '" . trim($this->la25_sigla) . "' ";
         $virgula = ",";
     }
     if(trim($this->la25_formula)!="" || isset($GLOBALS["HTTP_POST_VARS"]["la25_formula"])){
       $sql  .= $virgula." la25_formula = '$this->la25_formula' ";
       $virgula = ",";
     }
     if(trim($this->la25_preenchimentoobrigatorio)!="" || isset($GLOBALS["HTTP_POST_VARS"]["la25_preenchimentoobrigatorio"])){
       $sql  .= $virgula." la25_preenchimentoobrigatorio = '$this->la25_preenchimentoobrigatorio' ";
       $virgula = ",";
       if(trim($this->la25_preenchimentoobrigatorio) == null ){
         $this->erro_sql = " Campo Campo para informar se o campo permite valor vazio não informado.";
         $this->erro_campo = "la25_preenchimentoobrigatorio";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->la25_outrasinformacoes)!="" || (isset($GLOBALS["HTTP_POST_VARS"]["la25_outrasinformacoes"]) && ($GLOBALS["HTTP_POST_VARS"]["la25_outrasinformacoes"] != ""))) {
       $sql  .= $virgula." la25_outrasinformacoes = '$this->la25_outrasinformacoes' ";
       $virgula = ",";
     } else {
        if (isset($GLOBALS["HTTP_POST_VARS"]["la25_outrasinformacoes"])) {
            $sql .= $virgula . " la25_outrasinformacoes = null ";
            $virgula = ",";
        }
    }
    if(trim($this->la25_unidademedida)!="" || (isset($GLOBALS["HTTP_POST_VARS"]["la25_unidademedida"]) && ($GLOBALS["HTTP_POST_VARS"]["la25_unidademedida"] != ""))) {
      $sql  .= $virgula." la25_unidademedida = '$this->la25_unidademedida' ";
      $virgula = ",";
    } else {
      $sql .= $virgula . " la25_unidademedida = null ";
      $virgula = ",";
    }    
     $sql .= " where ";
     if($la25_i_codigo!=null){
       $sql .= " la25_i_codigo = $this->la25_i_codigo";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->la25_i_codigo));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,16489,'$this->la25_i_codigo','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["la25_i_codigo"]) || $this->la25_i_codigo != "")
             $resac = db_query("insert into db_acount values($acount,2899,16489,'".AddSlashes(pg_result($resaco,$conresaco,'la25_i_codigo'))."','$this->la25_i_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["la25_c_estrutural"]) || $this->la25_c_estrutural != "")
             $resac = db_query("insert into db_acount values($acount,2899,16492,'".AddSlashes(pg_result($resaco,$conresaco,'la25_c_estrutural'))."','$this->la25_c_estrutural',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["la25_c_descr"]) || $this->la25_c_descr != "")
             $resac = db_query("insert into db_acount values($acount,2899,16493,'".AddSlashes(pg_result($resaco,$conresaco,'la25_c_descr'))."','$this->la25_c_descr',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["la25_c_tipo"]) || $this->la25_c_tipo != "")
             $resac = db_query("insert into db_acount values($acount,2899,16494,'".AddSlashes(pg_result($resaco,$conresaco,'la25_c_tipo'))."','$this->la25_c_tipo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["la25_i_nivel"]) || $this->la25_i_nivel != "")
             $resac = db_query("insert into db_acount values($acount,2899,16495,'".AddSlashes(pg_result($resaco,$conresaco,'la25_i_nivel'))."','$this->la25_i_nivel',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["la25_sigla"]) || $this->la25_sigla != "")
             $resac = db_query("insert into db_acount values($acount,2899,1010782,'".AddSlashes(pg_result($resaco,$conresaco,'la25_sigla'))."','$this->la25_sigla',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["la25_formula"]) || $this->la25_formula != "")
             $resac = db_query("insert into db_acount values($acount,2899,1010783,'".AddSlashes(pg_result($resaco,$conresaco,'la25_formula'))."','$this->la25_formula',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["la25_preenchimentoobrigatorio"]) || $this->la25_preenchimentoobrigatorio != "")
             $resac = db_query("insert into db_acount values($acount,2899,1010909,'".AddSlashes(pg_result($resaco,$conresaco,'la25_preenchimentoobrigatorio'))."','$this->la25_preenchimentoobrigatorio',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["la25_outrasinformacoes"]) || $this->la25_outrasinformacoes != "")
             $resac = db_query("insert into db_acount values($acount,2899,1015483,'".AddSlashes(pg_result($resaco,$conresaco,'la25_outrasinformacoes'))."','$this->la25_outrasinformacoes',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["la25_unidademedida"]) || $this->la25_unidademedida != "")
             $resac = db_query("insert into db_acount values($acount,2899,1015634,'".AddSlashes(pg_result($resaco,$conresaco,'la25_unidademedida'))."','$this->la25_unidademedida',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Atributo não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->la25_i_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Atributo não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->la25_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->la25_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($la25_i_codigo=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($la25_i_codigo));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,16489,'$la25_i_codigo','E')");
           $resac  = db_query("insert into db_acount values($acount,2899,16489,'','".AddSlashes(pg_result($resaco,$iresaco,'la25_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2899,16492,'','".AddSlashes(pg_result($resaco,$iresaco,'la25_c_estrutural'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2899,16493,'','".AddSlashes(pg_result($resaco,$iresaco,'la25_c_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2899,16494,'','".AddSlashes(pg_result($resaco,$iresaco,'la25_c_tipo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2899,16495,'','".AddSlashes(pg_result($resaco,$iresaco,'la25_i_nivel'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2899,1010782,'','".AddSlashes(pg_result($resaco,$iresaco,'la25_sigla'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2899,1010783,'','".AddSlashes(pg_result($resaco,$iresaco,'la25_formula'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2899,1010909,'','".AddSlashes(pg_result($resaco,$iresaco,'la25_preenchimentoobrigatorio'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2899,1015483,'','".AddSlashes(pg_result($resaco,$iresaco,'la25_outrasinformacoes'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2899,1015634,'','".AddSlashes(pg_result($resaco,$iresaco,'la25_unidademedida'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from lab_atributo
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($la25_i_codigo)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " la25_i_codigo = $la25_i_codigo ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Atributo não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$la25_i_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Atributo não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$la25_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$la25_i_codigo;
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
        $this->erro_sql   = "Record Vazio na Tabela:lab_atributo";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($la25_i_codigo = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
     $sql .= "  from lab_atributo ";
     $sql .= "  left join lab_undmedida  on la25_unidademedida = la13_i_codigo";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($la25_i_codigo)) {
         $sql2 .= " where lab_atributo.la25_i_codigo = $la25_i_codigo ";
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

  public function sql_query_unidademedida($la25_i_codigo = null,$campos = "*", $ordem = null, $dbwhere = "") {

    $sql  = "select {$campos}";
    $sql .= "  from lab_atributo ";
    $sql .= "  left join lab_undmedida  on la25_unidademedida = la13_i_codigo";
    $sql2 = "";
    if (empty($dbwhere)) {
      if (!empty($la25_i_codigo)) {
        $sql2 .= " where lab_atributo.la25_i_codigo = $la25_i_codigo ";
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


    public function sql_query_file($la25_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from lab_atributo ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($la25_i_codigo != null) {
                $sql2 .= " where lab_atributo.la25_i_codigo = $la25_i_codigo ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

    public function sql_query_referencia($la25_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from lab_atributo ";
        $sql .= "      left join lab_valorreferencia on la27_i_atributo = la25_i_codigo";
        $sql .= "      left join lab_undmedida on la13_i_codigo = la25_unidademedida";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($la25_i_codigo != null) {
                $sql2 .= " where lab_atributo.la25_i_codigo = $la25_i_codigo ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

    public function sql_query_recursivo($la25_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $where = $la25_i_codigo != null ? " la25_i_codigo = {$la25_i_codigo} " : '';
        $ordem = $ordem != null ? "ORDER BY {$ordem}" : '';
        $where .= $dbwhere;
        $where = $where != '' ? "WHERE {$where}" : '';

        return <<<SQL
            WITH RECURSIVE atributos_recursivo AS (
                SELECT la42_i_atributo AS codigo, lab_atributo.*
                FROM lab_atributo
                LEFT JOIN lab_exameatributo ON lab_exameatributo.la42_i_atributo = lab_atributo.la25_i_codigo
                {$where}
                UNION ALL
                SELECT la26_i_exameatributofilho AS codigo, lab_atributo.*
                FROM lab_exameatributoligacao
                INNER JOIN atributos_recursivo ON lab_exameatributoligacao.la26_i_exameatributopai = atributos_recursivo.codigo
                INNER JOIN lab_atributo ON lab_atributo.la25_i_codigo = la26_i_exameatributofilho
            ) SELECT {$campos} FROM atributos_recursivo {$ordem};
SQL;
    }
}
