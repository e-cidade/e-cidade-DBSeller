<?php

class cl_conlancamajustesaldoconta
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
    public $c155_codigo = 0;
    public $c155_conlancam = 0;
    public $c155_exercicio = 0;
    public $c155_funcao = 0;
    public $c155_subfuncao = 0;
    public $c155_elemento = 0;
    public $c155_ai = 0;
    public $c155_receita = 0;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 c155_codigo = int4 = Código
                 c155_conlancam = int4 = Lançamento
                 c155_exercicio = int4 = Exercício
                 c155_funcao = int4 = Função
                 c155_subfuncao = int4 = Subfunção
                 c155_elemento = int4 = Elemento
                 c155_ai = int4 = AI
                 c155_receita = int4 = Receita
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("conlancamajustesaldoconta");
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
       $this->c155_codigo = ($this->c155_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["c155_codigo"]:$this->c155_codigo);
       $this->c155_conlancam = ($this->c155_conlancam == ""?@$GLOBALS["HTTP_POST_VARS"]["c155_conlancam"]:$this->c155_conlancam);
       $this->c155_exercicio = ($this->c155_exercicio == ""?@$GLOBALS["HTTP_POST_VARS"]["c155_exercicio"]:$this->c155_exercicio);
       $this->c155_funcao = !empty($this->c155_funcao) ? $this->c155_funcao : 'null';
       $this->c155_subfuncao = !empty($this->c155_subfuncao) ? $this->c155_subfuncao : 'null';
       $this->c155_elemento = !empty($this->c155_elemento) ? $this->c155_elemento : 'null';
       $this->c155_ai = !empty($this->c155_ai) ? $this->c155_ai : 'null';
       $this->c155_receita = !empty($this->c155_receita) ? $this->c155_receita : 'null';
     }else{
       $this->c155_codigo = ($this->c155_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["c155_codigo"]:$this->c155_codigo);
     }
   }

    public function incluir($c155_codigo)
    {
      $this->atualizacampos();
     if($this->c155_conlancam == null ){
       $this->erro_sql = " Campo Lançamento não informado.";
       $this->erro_campo = "c155_conlancam";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($c155_codigo == "" || $c155_codigo == null ){
       $result = db_query("select nextval('conlancamajustesaldoconta_c155_codigo_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: conlancamajustesaldoconta_c155_codigo_seq do campo: c155_codigo";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->c155_codigo = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from conlancamajustesaldoconta_c155_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $c155_codigo)){
         $this->erro_sql = " Campo c155_codigo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->c155_codigo = $c155_codigo;
       }
     }
     if(($this->c155_codigo == null) || ($this->c155_codigo == "") ){
       $this->erro_sql = " Campo c155_codigo não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into conlancamajustesaldoconta(
                                       c155_codigo
                                      ,c155_conlancam
                                      ,c155_exercicio
                                      ,c155_funcao
                                      ,c155_subfuncao
                                      ,c155_elemento
                                      ,c155_ai
                                      ,c155_receita
                       )
                values (
                                $this->c155_codigo
                               ,$this->c155_conlancam
                               ,$this->c155_exercicio
                               ,$this->c155_funcao
                               ,$this->c155_subfuncao
                               ,$this->c155_elemento
                               ,$this->c155_ai
                               ,$this->c155_receita
                      )";

     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Ajuste saldo conta ($this->c155_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Ajuste saldo conta já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Ajuste saldo conta ($this->c155_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->c155_codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->c155_codigo  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,142439925,'$this->c155_codigo','I')");
         $resac = db_query("insert into db_acount values($acount,272441036,142439925,'','".AddSlashes(pg_result($resaco,0,'c155_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,272441036,153098096,'','".AddSlashes(pg_result($resaco,0,'c155_conlancam'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,272441036,207318955,'','".AddSlashes(pg_result($resaco,0,'c155_exercicio'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,272441036,293261377,'','".AddSlashes(pg_result($resaco,0,'c155_funcao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,272441036,237110327,'','".AddSlashes(pg_result($resaco,0,'c155_subfuncao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,272441036,72164190,'','".AddSlashes(pg_result($resaco,0,'c155_elemento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,272441036,142841451,'','".AddSlashes(pg_result($resaco,0,'c155_ai'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,272441036,254497333,'','".AddSlashes(pg_result($resaco,0,'c155_receita'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }

    public function alterar($c155_codigo=null)
    {
      $this->atualizacampos();
     $sql = " update conlancamajustesaldoconta set ";
     $virgula = "";
     if(trim($this->c155_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c155_codigo"])){
       $sql  .= $virgula." c155_codigo = $this->c155_codigo ";
       $virgula = ",";
       if(trim($this->c155_codigo) == null ){
         $this->erro_sql = " Campo Código não informado.";
         $this->erro_campo = "c155_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c155_conlancam)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c155_conlancam"])){
       $sql  .= $virgula." c155_conlancam = $this->c155_conlancam ";
       $virgula = ",";
       if(trim($this->c155_conlancam) == null ){
         $this->erro_sql = " Campo Lançamento não informado.";
         $this->erro_campo = "c155_conlancam";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c155_exercicio)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c155_exercicio"])){
        if(trim($this->c155_exercicio)=="" && isset($GLOBALS["HTTP_POST_VARS"]["c155_exercicio"])){
           $this->c155_exercicio = "0" ;
        }
       $sql  .= $virgula." c155_exercicio = $this->c155_exercicio ";
       $virgula = ",";
     }
     if(trim($this->c155_funcao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c155_funcao"])){
        if(trim($this->c155_funcao)=="" && isset($GLOBALS["HTTP_POST_VARS"]["c155_funcao"])){
           $this->c155_funcao = "0" ;
        }
       $sql  .= $virgula." c155_funcao = $this->c155_funcao ";
       $virgula = ",";
     }
     if(trim($this->c155_subfuncao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c155_subfuncao"])){
        if(trim($this->c155_subfuncao)=="" && isset($GLOBALS["HTTP_POST_VARS"]["c155_subfuncao"])){
           $this->c155_subfuncao = "0" ;
        }
       $sql  .= $virgula." c155_subfuncao = $this->c155_subfuncao ";
       $virgula = ",";
     }
     if(trim($this->c155_elemento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c155_elemento"])){
        if(trim($this->c155_elemento)=="" && isset($GLOBALS["HTTP_POST_VARS"]["c155_elemento"])){
           $this->c155_elemento = "0" ;
        }
       $sql  .= $virgula." c155_elemento = $this->c155_elemento ";
       $virgula = ",";
     }
     if(trim($this->c155_ai)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c155_ai"])){
        if(trim($this->c155_ai)=="" && isset($GLOBALS["HTTP_POST_VARS"]["c155_ai"])){
           $this->c155_ai = "0" ;
        }
       $sql  .= $virgula." c155_ai = $this->c155_ai ";
       $virgula = ",";
     }
     if(trim($this->c155_receita)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c155_receita"])){
        if(trim($this->c155_receita)=="" && isset($GLOBALS["HTTP_POST_VARS"]["c155_receita"])){
           $this->c155_receita = "0" ;
        }
       $sql  .= $virgula." c155_receita = $this->c155_receita ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($c155_codigo!=null){
       $sql .= " c155_codigo = $this->c155_codigo";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->c155_codigo));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,142439925,'$this->c155_codigo','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c155_codigo"]) || $this->c155_codigo != "")
             $resac = db_query("insert into db_acount values($acount,272441036,142439925,'".AddSlashes(pg_result($resaco,$conresaco,'c155_codigo'))."','$this->c155_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c155_conlancam"]) || $this->c155_conlancam != "")
             $resac = db_query("insert into db_acount values($acount,272441036,153098096,'".AddSlashes(pg_result($resaco,$conresaco,'c155_conlancam'))."','$this->c155_conlancam',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c155_exercicio"]) || $this->c155_exercicio != "")
             $resac = db_query("insert into db_acount values($acount,272441036,207318955,'".AddSlashes(pg_result($resaco,$conresaco,'c155_exercicio'))."','$this->c155_exercicio',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c155_funcao"]) || $this->c155_funcao != "")
             $resac = db_query("insert into db_acount values($acount,272441036,293261377,'".AddSlashes(pg_result($resaco,$conresaco,'c155_funcao'))."','$this->c155_funcao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c155_subfuncao"]) || $this->c155_subfuncao != "")
             $resac = db_query("insert into db_acount values($acount,272441036,237110327,'".AddSlashes(pg_result($resaco,$conresaco,'c155_subfuncao'))."','$this->c155_subfuncao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c155_elemento"]) || $this->c155_elemento != "")
             $resac = db_query("insert into db_acount values($acount,272441036,72164190,'".AddSlashes(pg_result($resaco,$conresaco,'c155_elemento'))."','$this->c155_elemento',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c155_ai"]) || $this->c155_ai != "")
             $resac = db_query("insert into db_acount values($acount,272441036,142841451,'".AddSlashes(pg_result($resaco,$conresaco,'c155_ai'))."','$this->c155_ai',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c155_receita"]) || $this->c155_receita != "")
             $resac = db_query("insert into db_acount values($acount,272441036,254497333,'".AddSlashes(pg_result($resaco,$conresaco,'c155_receita'))."','$this->c155_receita',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Ajuste saldo conta não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->c155_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Ajuste saldo conta não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->c155_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->c155_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($c155_codigo=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($c155_codigo));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,142439925,'$c155_codigo','E')");
           $resac  = db_query("insert into db_acount values($acount,272441036,142439925,'','".AddSlashes(pg_result($resaco,$iresaco,'c155_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,272441036,153098096,'','".AddSlashes(pg_result($resaco,$iresaco,'c155_conlancam'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,272441036,207318955,'','".AddSlashes(pg_result($resaco,$iresaco,'c155_exercicio'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,272441036,293261377,'','".AddSlashes(pg_result($resaco,$iresaco,'c155_funcao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,272441036,237110327,'','".AddSlashes(pg_result($resaco,$iresaco,'c155_subfuncao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,272441036,72164190,'','".AddSlashes(pg_result($resaco,$iresaco,'c155_elemento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,272441036,142841451,'','".AddSlashes(pg_result($resaco,$iresaco,'c155_ai'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,272441036,254497333,'','".AddSlashes(pg_result($resaco,$iresaco,'c155_receita'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from conlancamajustesaldoconta
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($c155_codigo)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " c155_codigo = $c155_codigo ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Ajuste saldo conta não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$c155_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Ajuste saldo conta não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$c155_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$c155_codigo;
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
        $this->erro_sql   = "Record Vazio na Tabela:conlancamajustesaldoconta";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($c155_codigo = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
     $sql .= "  from conlancamajustesaldoconta ";
     $sql .= "      left  join orcfuncao  on  orcfuncao.o52_funcao = conlancamajustesaldoconta.c155_funcao";
     $sql .= "      left  join orcsubfuncao  on  orcsubfuncao.o53_subfuncao = conlancamajustesaldoconta.c155_subfuncao";
     $sql .= "      left  join orcelemento  on  orcelemento.o56_codele = conlancamajustesaldoconta.c155_elemento and  orcelemento.o56_anousu = conlancamajustesaldoconta.c155_exercicio";
     $sql .= "      left  join orcfontes  on  orcfontes.o57_codfon = conlancamajustesaldoconta.c155_receita and  orcfontes.o57_anousu = conlancamajustesaldoconta.c155_exercicio";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($c155_codigo)) {
         $sql2 .= " where conlancamajustesaldoconta.c155_codigo = $c155_codigo ";
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

    public function sql_query_file($c155_codigo = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from conlancamajustesaldoconta ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($c155_codigo)){
         $sql2 .= " where conlancamajustesaldoconta.c155_codigo = $c155_codigo ";
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
