<?php

class cl_setorambulatorial
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
    public $sd91_codigo = 0;
    public $sd91_unidades = 0;
    public $sd91_descricao = null;
    public $sd91_local = 0;
    public $sd91_situacao = 'f';
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 sd91_codigo = int4 = Código
                 sd91_unidades = int4 = Unidade
                 sd91_descricao = varchar(60) = Setor
                 sd91_local = int4 = Local
                 sd91_situacao = bool = Ativo
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("setorambulatorial");
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
            $this->sd91_codigo = ($this->sd91_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["sd91_codigo"]:$this->sd91_codigo);
            $this->sd91_unidades = ($this->sd91_unidades == ""?@$GLOBALS["HTTP_POST_VARS"]["sd91_unidades"]:$this->sd91_unidades);
            $this->sd91_descricao = ($this->sd91_descricao == ""?@$GLOBALS["HTTP_POST_VARS"]["sd91_descricao"]:$this->sd91_descricao);
            $this->sd91_local = ($this->sd91_local == ""?@$GLOBALS["HTTP_POST_VARS"]["sd91_local"]:$this->sd91_local);
            $sd91_situacao = isset($GLOBALS["HTTP_POST_VARS"]["sd91_situacao"]) ? $GLOBALS["HTTP_POST_VARS"]["sd91_situacao"] : 'f';
            $this->sd91_situacao = $sd91_situacao;
        }else{
            $this->sd91_codigo = ($this->sd91_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["sd91_codigo"]:$this->sd91_codigo);
        }
    }

    public function incluir($sd91_codigo)
    {
        $this->atualizacampos();
        if($this->sd91_unidades == null ){
            $this->erro_sql = " Campo Unidade não informado.";
            $this->erro_campo = "sd91_unidades";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if($this->sd91_descricao == null ){
            $this->erro_sql = " Campo Setor não informado.";
            $this->erro_campo = "sd91_descricao";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if($this->sd91_local == null ){
            $this->erro_sql = " Campo Local não informado.";
            $this->erro_campo = "sd91_local";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if($this->sd91_situacao == null ){
            $this->erro_sql = " Campo Ativo não informado.";
            $this->erro_campo = "sd91_situacao";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if($sd91_codigo == "" || $sd91_codigo == null ){
            $result = db_query("select nextval('setorambulatorial_sd91_codigo_seq')");
            if($result==false){
                $this->erro_banco = str_replace("\n","",@pg_last_error());
                $this->erro_sql   = "Verifique o cadastro da sequencia: setorambulatorial_sd91_codigo_seq do campo: sd91_codigo";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->sd91_codigo = pg_result($result,0,0);
        }else{
            $result = db_query("select last_value from setorambulatorial_sd91_codigo_seq");
            if(($result != false) && (pg_result($result,0,0) < $sd91_codigo)){
                $this->erro_sql = " Campo sd91_codigo maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }else{
                $this->sd91_codigo = $sd91_codigo;
            }
        }
        if(($this->sd91_codigo == null) || ($this->sd91_codigo == "") ){
            $this->erro_sql = " Campo sd91_codigo não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into setorambulatorial(
                                       sd91_codigo
                                      ,sd91_unidades
                                      ,sd91_descricao
                                      ,sd91_local
                                      ,sd91_situacao
                       )
                values (
                                $this->sd91_codigo
                               ,$this->sd91_unidades
                               ,'$this->sd91_descricao'
                               ,$this->sd91_local
                               ,'$this->sd91_situacao'
                      )";
        $result = db_query($sql);
        if($result==false){
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
                $this->erro_sql   = "Setor ambulatorial ($this->sd91_codigo) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Setor ambulatorial já Cadastrado";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }else{
                $this->erro_sql   = "Setor ambulatorial ($this->sd91_codigo) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : ".$this->sd91_codigo;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->sd91_codigo  ));
            if(($resaco!=false)||($this->numrows!=0)){

                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac,0,0);
                $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                $resac = db_query("insert into db_acountkey values($acount,20939,'$this->sd91_codigo','I')");
                $resac = db_query("insert into db_acount values($acount,3772,20939,'','".AddSlashes(pg_result($resaco,0,'sd91_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,3772,20940,'','".AddSlashes(pg_result($resaco,0,'sd91_unidades'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,3772,20941,'','".AddSlashes(pg_result($resaco,0,'sd91_descricao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,3772,20942,'','".AddSlashes(pg_result($resaco,0,'sd91_local'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,3772,1015199,'','".AddSlashes(pg_result($resaco,0,'sd91_situacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
            }
        }
        return true;
    }

    public function alterar($sd91_codigo=null)
    {
        $this->atualizacampos();
        $sql = " update setorambulatorial set ";
        $virgula = "";
        if(trim($this->sd91_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["sd91_codigo"])){
            $sql  .= $virgula." sd91_codigo = $this->sd91_codigo ";
            $virgula = ",";
            if(trim($this->sd91_codigo) == null ){
                $this->erro_sql = " Campo Código não informado.";
                $this->erro_campo = "sd91_codigo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->sd91_unidades)!="" || isset($GLOBALS["HTTP_POST_VARS"]["sd91_unidades"])){
            $sql  .= $virgula." sd91_unidades = $this->sd91_unidades ";
            $virgula = ",";
            if(trim($this->sd91_unidades) == null ){
                $this->erro_sql = " Campo Unidade não informado.";
                $this->erro_campo = "sd91_unidades";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->sd91_descricao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["sd91_descricao"])){
            $sql  .= $virgula." sd91_descricao = '$this->sd91_descricao' ";
            $virgula = ",";
            if(trim($this->sd91_descricao) == null ){
                $this->erro_sql = " Campo Setor não informado.";
                $this->erro_campo = "sd91_descricao";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->sd91_local)!="" || isset($GLOBALS["HTTP_POST_VARS"]["sd91_local"])){
            $sql  .= $virgula." sd91_local = $this->sd91_local ";
            $virgula = ",";
            if(trim($this->sd91_local) == null ){
                $this->erro_sql = " Campo Local não informado.";
                $this->erro_campo = "sd91_local";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->sd91_situacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["sd91_situacao"])){
            $sql  .= $virgula." sd91_situacao = '$this->sd91_situacao' ";
            $virgula = ",";
            if(trim($this->sd91_situacao) == null ){
                $this->erro_sql = " Campo Ativo não informado.";
                $this->erro_campo = "sd91_situacao";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if($sd91_codigo!=null){
            $sql .= " sd91_codigo = $this->sd91_codigo";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->sd91_codigo));
            if ($this->numrows > 0) {

                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac,0,0);
                    $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                    $resac = db_query("insert into db_acountkey values($acount,20939,'$this->sd91_codigo','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["sd91_codigo"]) || $this->sd91_codigo != "")
                        $resac = db_query("insert into db_acount values($acount,3772,20939,'".AddSlashes(pg_result($resaco,$conresaco,'sd91_codigo'))."','$this->sd91_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["sd91_unidades"]) || $this->sd91_unidades != "")
                        $resac = db_query("insert into db_acount values($acount,3772,20940,'".AddSlashes(pg_result($resaco,$conresaco,'sd91_unidades'))."','$this->sd91_unidades',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["sd91_descricao"]) || $this->sd91_descricao != "")
                        $resac = db_query("insert into db_acount values($acount,3772,20941,'".AddSlashes(pg_result($resaco,$conresaco,'sd91_descricao'))."','$this->sd91_descricao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["sd91_local"]) || $this->sd91_local != "")
                        $resac = db_query("insert into db_acount values($acount,3772,20942,'".AddSlashes(pg_result($resaco,$conresaco,'sd91_local'))."','$this->sd91_local',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["sd91_situacao"]) || $this->sd91_situacao != "")
                        $resac = db_query("insert into db_acount values($acount,3772,1015199,'".AddSlashes(pg_result($resaco,$conresaco,'sd91_situacao'))."','$this->sd91_situacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            $this->erro_sql   = "Setor ambulatorial não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->sd91_codigo;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Setor ambulatorial não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->sd91_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$this->sd91_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($sd91_codigo=null, $dbwhere = null)
    {
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            if (empty($dbwhere)) {

                $resaco = $this->sql_record($this->sql_query_file($sd91_codigo));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
            }
            if (($resaco != false) || ($this->numrows!=0)) {

                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

                    $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac,0,0);
                    $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                    $resac  = db_query("insert into db_acountkey values($acount,20939,'$sd91_codigo','E')");
                    $resac  = db_query("insert into db_acount values($acount,3772,20939,'','".AddSlashes(pg_result($resaco,$iresaco,'sd91_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,3772,20940,'','".AddSlashes(pg_result($resaco,$iresaco,'sd91_unidades'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,3772,20941,'','".AddSlashes(pg_result($resaco,$iresaco,'sd91_descricao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,3772,20942,'','".AddSlashes(pg_result($resaco,$iresaco,'sd91_local'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,3772,1015199,'','".AddSlashes(pg_result($resaco,$iresaco,'sd91_situacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                }
            }
        }
        $sql = " delete from setorambulatorial
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($sd91_codigo)){
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " sd91_codigo = $sd91_codigo ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            $this->erro_sql   = "Setor ambulatorial não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$sd91_codigo;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Setor ambulatorial não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$sd91_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$sd91_codigo;
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
            $this->erro_sql   = "Record Vazio na Tabela:setorambulatorial";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($sd91_codigo = null,$campos = "*", $ordem = null, $dbwhere = "") {

        $sql  = "select {$campos}";
        $sql .= "  from setorambulatorial ";
        $sql .= "      inner join unidades  on  unidades.sd02_i_codigo = setorambulatorial.sd91_unidades";
        $sql .= "      left  join cgm  on  cgm.z01_numcgm = unidades.sd02_i_diretor and  cgm.z01_numcgm = unidades.sd02_i_numcgm";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = unidades.sd02_i_codigo";
        $sql .= "      left  join sau_esferaadmin  on  sau_esferaadmin.sd37_i_cod_esfadm = unidades.sd02_i_cod_esfadm";
        $sql .= "      left  join sau_atividadeensino  on  sau_atividadeensino.sd38_i_cod_ativid = unidades.sd02_i_cod_ativ";
        $sql .= "      left  join sau_retentributo  on  sau_retentributo.sd39_i_cod_reten = unidades.sd02_i_reten_trib";
        $sql .= "      left  join sau_natorg  on  sau_natorg.sd40_i_cod_natorg = unidades.sd02_i_cod_natorg";
        $sql .= "      left  join sau_fluxocliente  on  sau_fluxocliente.sd41_i_cod_cliente = unidades.sd02_i_cod_client";
        $sql .= "      left  join sau_tipounidade  on  sau_tipounidade.sd42_i_tp_unid_id = unidades.sd02_i_tp_unid_id";
        $sql .= "      left  join sau_turnoatend  on  sau_turnoatend.sd43_cod_turnat = unidades.sd02_i_cod_turnat";
        $sql .= "      left  join sau_nivelhier  on  sau_nivelhier.sd44_i_codnivhier = unidades.sd02_i_codnivhier";
        $sql .= "      left  join sau_distritosanitario  on  sau_distritosanitario.s153_i_codigo = unidades.sd02_i_distrito";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($sd91_codigo)) {
                $sql2 .= " where setorambulatorial.sd91_codigo = $sd91_codigo ";
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

    public function sql_query_file($sd91_codigo = null, $campos = "*", $ordem = null, $dbwhere = "") {

        $sql  = "select {$campos} ";
        $sql .= "  from setorambulatorial ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($sd91_codigo)){
                $sql2 .= " where setorambulatorial.sd91_codigo = $sd91_codigo ";
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
