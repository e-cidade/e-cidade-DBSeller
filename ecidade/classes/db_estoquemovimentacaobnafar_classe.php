<?php

class cl_estoquemovimentacaobnafar
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
    public $fa69_codigo = 0;
    public $fa69_matestoqueini = 0;
    public $fa69_tipomovimentacao = 0;
    public $fa69_cgm = 0;
    public $fa69_unidade = 0;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 fa69_codigo = int4 = Código
                 fa69_matestoqueini = int4 = Estoque Inicial
                 fa69_tipomovimentacao = int4 = Tipo de Movimentação
                 fa69_cgm = int4 = CGM Destino
                 fa69_unidade = int4 = Unidade Destino
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("estoquemovimentacaobnafar");
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
       $this->fa69_codigo = ($this->fa69_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["fa69_codigo"]:$this->fa69_codigo);
       $this->fa69_matestoqueini = ($this->fa69_matestoqueini == ""?@$GLOBALS["HTTP_POST_VARS"]["fa69_matestoqueini"]:$this->fa69_matestoqueini);
       $this->fa69_tipomovimentacao = ($this->fa69_tipomovimentacao == ""?@$GLOBALS["HTTP_POST_VARS"]["fa69_tipomovimentacao"]:$this->fa69_tipomovimentacao);
       $this->fa69_cgm = ($this->fa69_cgm == ""?@$GLOBALS["HTTP_POST_VARS"]["fa69_cgm"]:$this->fa69_cgm);
       $this->fa69_unidade = ($this->fa69_unidade == ""?@$GLOBALS["HTTP_POST_VARS"]["fa69_unidade"]:$this->fa69_unidade);
     }else{
       $this->fa69_codigo = ($this->fa69_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["fa69_codigo"]:$this->fa69_codigo);
     }
   }

    public function incluir($fa69_codigo)
    {
      $this->atualizacampos();
     if($this->fa69_matestoqueini == null ){
       $this->erro_sql = " Campo Estoque Inicial não informado.";
       $this->erro_campo = "fa69_matestoqueini";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fa69_tipomovimentacao == null ){
       $this->erro_sql = " Campo Tipo de Movimentação não informado.";
       $this->erro_campo = "fa69_tipomovimentacao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fa69_cgm == null ){
       $this->fa69_cgm = "null";
     }
     if($this->fa69_unidade == null ){
       $this->fa69_unidade = "null";
     }
     if($fa69_codigo == "" || $fa69_codigo == null ){
       $result = db_query("select nextval('estoquemovimentacaobnafar_fa69_codigo_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: estoquemovimentacaobnafar_fa69_codigo_seq do campo: fa69_codigo";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->fa69_codigo = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from estoquemovimentacaobnafar_fa69_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $fa69_codigo)){
         $this->erro_sql = " Campo fa69_codigo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->fa69_codigo = $fa69_codigo;
       }
     }
     if(($this->fa69_codigo == null) || ($this->fa69_codigo == "") ){
       $this->erro_sql = " Campo fa69_codigo não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into estoquemovimentacaobnafar(
                                       fa69_codigo
                                      ,fa69_matestoqueini
                                      ,fa69_tipomovimentacao
                                      ,fa69_cgm
                                      ,fa69_unidade
                       )
                values (
                                $this->fa69_codigo
                               ,$this->fa69_matestoqueini
                               ,$this->fa69_tipomovimentacao
                               ,$this->fa69_cgm
                               ,$this->fa69_unidade
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Movimentações do Estoque ($this->fa69_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Movimentações do Estoque já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Movimentações do Estoque ($this->fa69_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->fa69_codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->fa69_codigo  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1014043,'$this->fa69_codigo','I')");
         $resac = db_query("insert into db_acount values($acount,1010909,1014043,'','".AddSlashes(pg_result($resaco,0,'fa69_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010909,1014044,'','".AddSlashes(pg_result($resaco,0,'fa69_matestoqueini'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010909,1014045,'','".AddSlashes(pg_result($resaco,0,'fa69_tipomovimentacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010909,1014046,'','".AddSlashes(pg_result($resaco,0,'fa69_cgm'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010909,1014047,'','".AddSlashes(pg_result($resaco,0,'fa69_unidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }

    public function alterar($fa69_codigo=null)
    {
      $this->atualizacampos();
     $sql = " update estoquemovimentacaobnafar set ";
     $virgula = "";
     if(trim($this->fa69_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa69_codigo"])){
       $sql  .= $virgula." fa69_codigo = $this->fa69_codigo ";
       $virgula = ",";
       if(trim($this->fa69_codigo) == null ){
         $this->erro_sql = " Campo Código não informado.";
         $this->erro_campo = "fa69_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fa69_matestoqueini)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa69_matestoqueini"])){
       $sql  .= $virgula." fa69_matestoqueini = $this->fa69_matestoqueini ";
       $virgula = ",";
       if(trim($this->fa69_matestoqueini) == null ){
         $this->erro_sql = " Campo Estoque Inicial não informado.";
         $this->erro_campo = "fa69_matestoqueini";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fa69_tipomovimentacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa69_tipomovimentacao"])){
       $sql  .= $virgula." fa69_tipomovimentacao = $this->fa69_tipomovimentacao ";
       $virgula = ",";
       if(trim($this->fa69_tipomovimentacao) == null ){
         $this->erro_sql = " Campo Tipo de Movimentação não informado.";
         $this->erro_campo = "fa69_tipomovimentacao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fa69_cgm)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa69_cgm"])){
        if(trim($this->fa69_cgm)=="" && isset($GLOBALS["HTTP_POST_VARS"]["fa69_cgm"])){
           $this->fa69_cgm = "null" ;
        }
       $sql  .= $virgula." fa69_cgm = $this->fa69_cgm ";
       $virgula = ",";
     }
     if(trim($this->fa69_unidade)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa69_unidade"])){
        if(trim($this->fa69_unidade)=="" && isset($GLOBALS["HTTP_POST_VARS"]["fa69_unidade"])){
           $this->fa69_unidade = "null" ;
        }
       $sql  .= $virgula." fa69_unidade = $this->fa69_unidade ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($fa69_codigo!=null){
       $sql .= " fa69_codigo = $this->fa69_codigo";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->fa69_codigo));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1014043,'$this->fa69_codigo','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa69_codigo"]) || $this->fa69_codigo != "")
             $resac = db_query("insert into db_acount values($acount,1010909,1014043,'".AddSlashes(pg_result($resaco,$conresaco,'fa69_codigo'))."','$this->fa69_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa69_matestoqueini"]) || $this->fa69_matestoqueini != "")
             $resac = db_query("insert into db_acount values($acount,1010909,1014044,'".AddSlashes(pg_result($resaco,$conresaco,'fa69_matestoqueini'))."','$this->fa69_matestoqueini',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa69_tipomovimentacao"]) || $this->fa69_tipomovimentacao != "")
             $resac = db_query("insert into db_acount values($acount,1010909,1014045,'".AddSlashes(pg_result($resaco,$conresaco,'fa69_tipomovimentacao'))."','$this->fa69_tipomovimentacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa69_cgm"]) || $this->fa69_cgm != "")
             $resac = db_query("insert into db_acount values($acount,1010909,1014046,'".AddSlashes(pg_result($resaco,$conresaco,'fa69_cgm'))."','$this->fa69_cgm',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa69_unidade"]) || $this->fa69_unidade != "")
             $resac = db_query("insert into db_acount values($acount,1010909,1014047,'".AddSlashes(pg_result($resaco,$conresaco,'fa69_unidade'))."','$this->fa69_unidade',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Movimentações do Estoque não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->fa69_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Movimentações do Estoque não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->fa69_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->fa69_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($fa69_codigo=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($fa69_codigo));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1014043,'$fa69_codigo','E')");
           $resac  = db_query("insert into db_acount values($acount,1010909,1014043,'','".AddSlashes(pg_result($resaco,$iresaco,'fa69_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010909,1014044,'','".AddSlashes(pg_result($resaco,$iresaco,'fa69_matestoqueini'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010909,1014045,'','".AddSlashes(pg_result($resaco,$iresaco,'fa69_tipomovimentacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010909,1014046,'','".AddSlashes(pg_result($resaco,$iresaco,'fa69_cgm'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010909,1014047,'','".AddSlashes(pg_result($resaco,$iresaco,'fa69_unidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from estoquemovimentacaobnafar
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($fa69_codigo)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " fa69_codigo = $fa69_codigo ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Movimentações do Estoque não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$fa69_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Movimentações do Estoque não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$fa69_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$fa69_codigo;
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
        $this->erro_sql   = "Record Vazio na Tabela:estoquemovimentacaobnafar";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($fa69_codigo = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
     $sql .= "  from estoquemovimentacaobnafar ";
     $sql .= "      inner join matestoqueini  on  matestoqueini.m80_codigo = estoquemovimentacaobnafar.fa69_matestoqueini";
     $sql .= "      inner join tipomovimentacaobnafar  on  tiposmovimentacaobnafar.fa68_codigo = estoquemovimentacaobnafar.fa69_tipomovimentacao";
     $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = matestoqueini.m80_login";
     $sql .= "      inner join db_depart  on  db_depart.coddepto = matestoqueini.m80_coddepto";
     $sql .= "      inner join matestoquetipo  on  matestoquetipo.m81_codtipo = matestoqueini.m80_codtipo";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($fa69_codigo)) {
         $sql2 .= " where estoquemovimentacaobnafar.fa69_codigo = $fa69_codigo ";
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

    public function sql_query_file($fa69_codigo = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from estoquemovimentacaobnafar ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($fa69_codigo)){
         $sql2 .= " where estoquemovimentacaobnafar.fa69_codigo = $fa69_codigo ";
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

	public function sql_query_estoquemovimentacoesbnafar($iCodigo = null, $sCampos="*", $sOrder, $sWhere)
	{
		$sSql  = "select {$sCampos}";
		$sSql .= " from estoquemovimentacaobnafar";
		$sSql .= "       inner join matestoqueini on m80_codigo = fa69_matestoqueini ";
		$sSql .= "       inner join tipomovimentacaobnafar on fa68_codigo = fa69_tipomovimentacao ";
		$sSql .= "       left join cgm on z01_numcgm =  fa69_cgm";
		$sSql .= "       left join unidades on sd02_i_codigo = fa69_unidade";
		$sSql .= "       left join db_depart on coddepto = sd02_i_codigo";

		if (!empty($iCodigo)) {
			$aWhere[] = "sequencial = {$iCodigo}";
		}

		if (!empty($sWhere)) {
			$aWhere[] = $sWhere;
		}

		if (count($aWhere) > 0) {
			$sSql .= " where ".implode(" and ", $aWhere);
		}
		if (!empty($sOrder)) {
			$sSql .= " order by {$sOrder}";
		}
		return $sSql;
	}
}
