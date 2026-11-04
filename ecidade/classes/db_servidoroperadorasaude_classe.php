<?php

class cl_servidoroperadorasaude
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
    public $rh222_sequencial = 0;
    public $rh222_valor = 0;
    public $rh222_rubrica = null;
    public $rh222_servidor = 0;
    public $rh222_operadorasaude = 0;
    public $rh222_instituicao = 0;
    public $rh222_mes = 0;
    public $rh222_ano = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 rh222_sequencial = int4 = Sequencial 
                 rh222_valor = float8 = Valor 
                 rh222_rubrica = varchar(4) = Rubrica 
                 rh222_servidor = int4 = Servidor 
                 rh222_operadorasaude = int4 = Operadora de Saúde 
                 rh222_instituicao = int4 = Instituição 
                 rh222_mes = int4 = Mês 
                 rh222_ano = int4 = Ano 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("servidoroperadorasaude");
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
    }

    public function erro($mostra, $retorna)
    {
        if (($this->erro_status == "0") || ($mostra == true && $this->erro_status != null)) {
            echo "<script>alert(\"" . $this->erro_msg . "\")</script>";
            if ($retorna == true) {
                echo "<script>location.href='" . $this->pagina_retorno . "'</script>";
            }
        }
    }

    public function atualizacampos($exclusao = false)
    {
        if ($exclusao == false) {
            $this->rh222_sequencial = ($this->rh222_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["rh222_sequencial"] : $this->rh222_sequencial);
            $this->rh222_valor = ($this->rh222_valor == "" ? @$GLOBALS["HTTP_POST_VARS"]["rh222_valor"] : $this->rh222_valor);
            $this->rh222_rubrica = ($this->rh222_rubrica == "" ? @$GLOBALS["HTTP_POST_VARS"]["rh222_rubrica"] : $this->rh222_rubrica);
            $this->rh222_servidor = ($this->rh222_servidor == "" ? @$GLOBALS["HTTP_POST_VARS"]["rh222_servidor"] : $this->rh222_servidor);
            $this->rh222_operadorasaude = ($this->rh222_operadorasaude == "" ? @$GLOBALS["HTTP_POST_VARS"]["rh222_operadorasaude"] : $this->rh222_operadorasaude);
            $this->rh222_instituicao = ($this->rh222_instituicao == "" ? @$GLOBALS["HTTP_POST_VARS"]["rh222_instituicao"] : $this->rh222_instituicao);
            $this->rh222_mes = ($this->rh222_mes == "" ? @$GLOBALS["HTTP_POST_VARS"]["rh222_mes"] : $this->rh222_mes);
            $this->rh222_ano = ($this->rh222_ano == "" ? @$GLOBALS["HTTP_POST_VARS"]["rh222_ano"] : $this->rh222_ano);
        } else {
            $this->rh222_sequencial = ($this->rh222_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["rh222_sequencial"] : $this->rh222_sequencial);
        }
    }

    public function incluir($rh222_sequencial)
    {
        $this->atualizacampos();
        if ($this->rh222_valor == null) {
            $this->rh222_valor = 0;
        }
        if ($this->rh222_rubrica == null) {
            $this->erro_sql = " Campo Rubrica não informado.";
            $this->erro_campo = "rh222_rubrica";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->rh222_servidor == null) {
            $this->erro_sql = " Campo Servidor não informado.";
            $this->erro_campo = "rh222_servidor";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->rh222_operadorasaude == null) {
            $this->erro_sql = " Campo Operadora de Saúde não informado.";
            $this->erro_campo = "rh222_operadorasaude";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->rh222_instituicao == null) {
            $this->erro_sql = " Campo Instituição não informado.";
            $this->erro_campo = "rh222_instituicao";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->rh222_mes == null) {
            $this->erro_sql = " Campo Mês não informado.";
            $this->erro_campo = "rh222_mes";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->rh222_ano == null) {
            $this->erro_sql = " Campo Ano não informado.";
            $this->erro_campo = "rh222_ano";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($rh222_sequencial == "" || $rh222_sequencial == null) {
            $result = db_query("select nextval('servidoroperadorasaude_rh222_sequencial_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: servidoroperadorasaude_rh222_sequencial_seq do campo: rh222_sequencial";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->rh222_sequencial = pg_result($result, 0, 0);
        } else {
            $result = db_query("SELECT last_value FROM servidoroperadorasaude_rh222_sequencial_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $rh222_sequencial)) {
                $this->erro_sql = " Campo rh222_sequencial maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->rh222_sequencial = $rh222_sequencial;
            }
        }
        if (($this->rh222_sequencial == null) || ($this->rh222_sequencial == "")) {
            $this->erro_sql = " Campo rh222_sequencial não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into servidoroperadorasaude(
                                       rh222_sequencial 
                                      ,rh222_valor 
                                      ,rh222_rubrica 
                                      ,rh222_servidor 
                                      ,rh222_operadorasaude 
                                      ,rh222_instituicao 
                                      ,rh222_mes 
                                      ,rh222_ano 
                       )
                values (
                                $this->rh222_sequencial 
                               ,$this->rh222_valor 
                               ,'$this->rh222_rubrica' 
                               ,$this->rh222_servidor 
                               ,$this->rh222_operadorasaude 
                               ,$this->rh222_instituicao 
                               ,$this->rh222_mes 
                               ,$this->rh222_ano 
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "Operadora de Saúde do Servidor  ($this->rh222_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "Operadora de Saúde do Servidor  já Cadastrado";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "Operadora de Saúde do Servidor  ($this->rh222_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->rh222_sequencial;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "",
            str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->rh222_sequencial));
            if (($resaco != false) || ($this->numrows != 0)) {

                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                $resac = db_query("insert into db_acountkey values($acount,1010053,'$this->rh222_sequencial','I')");
                $resac = db_query("insert into db_acount values($acount,1010334,1010053,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'rh222_sequencial')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010334,1010054,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'rh222_valor')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010334,1010060,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'rh222_rubrica')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010334,1010059,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'rh222_servidor')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010334,1010058,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'rh222_operadorasaude')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010334,1010057,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'rh222_instituicao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010334,1010056,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'rh222_mes')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010334,1010055,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'rh222_ano')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            }
        }
        return true;
    }

    public function alterar($rh222_sequencial = null)
    {
        $this->atualizacampos();
        $sql = " UPDATE servidoroperadorasaude SET ";
        $virgula = "";
        if (trim($this->rh222_sequencial) != "" || isset($GLOBALS["HTTP_POST_VARS"]["rh222_sequencial"])) {
            $sql .= $virgula . " rh222_sequencial = $this->rh222_sequencial ";
            $virgula = ",";
            if (trim($this->rh222_sequencial) == null) {
                $this->rh222_sequencial = 0;
            }
        }
        if (trim($this->rh222_valor) != "" || isset($GLOBALS["HTTP_POST_VARS"]["rh222_valor"])) {
            $sql .= $virgula . " rh222_valor = $this->rh222_valor ";
            $virgula = ",";
            if (trim($this->rh222_valor) == null) {
                $this->erro_sql = " Campo Valor não informado.";
                $this->erro_campo = "rh222_valor";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->rh222_rubrica) != "" || isset($GLOBALS["HTTP_POST_VARS"]["rh222_rubrica"])) {
            $sql .= $virgula . " rh222_rubrica = '$this->rh222_rubrica' ";
            $virgula = ",";
            if (trim($this->rh222_rubrica) == null) {
                $this->erro_sql = " Campo Rubrica não informado.";
                $this->erro_campo = "rh222_rubrica";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->rh222_servidor) != "" || isset($GLOBALS["HTTP_POST_VARS"]["rh222_servidor"])) {
            $sql .= $virgula . " rh222_servidor = $this->rh222_servidor ";
            $virgula = ",";
            if (trim($this->rh222_servidor) == null) {
                $this->erro_sql = " Campo Servidor não informado.";
                $this->erro_campo = "rh222_servidor";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->rh222_operadorasaude) != "" || isset($GLOBALS["HTTP_POST_VARS"]["rh222_operadorasaude"])) {
            $sql .= $virgula . " rh222_operadorasaude = $this->rh222_operadorasaude ";
            $virgula = ",";
            if (trim($this->rh222_operadorasaude) == null) {
                $this->erro_sql = " Campo Operadora de Saúde não informado.";
                $this->erro_campo = "rh222_operadorasaude";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->rh222_instituicao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["rh222_instituicao"])) {
            $sql .= $virgula . " rh222_instituicao = $this->rh222_instituicao ";
            $virgula = ",";
            if (trim($this->rh222_instituicao) == null) {
                $this->erro_sql = " Campo Instituição não informado.";
                $this->erro_campo = "rh222_instituicao";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->rh222_mes) != "" || isset($GLOBALS["HTTP_POST_VARS"]["rh222_mes"])) {
            $sql .= $virgula . " rh222_mes = $this->rh222_mes ";
            $virgula = ",";
            if (trim($this->rh222_mes) == null) {
                $this->erro_sql = " Campo Mês não informado.";
                $this->erro_campo = "rh222_mes";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->rh222_ano) != "" || isset($GLOBALS["HTTP_POST_VARS"]["rh222_ano"])) {
            $sql .= $virgula . " rh222_ano = $this->rh222_ano ";
            $virgula = ",";
            if (trim($this->rh222_ano) == null) {
                $this->erro_sql = " Campo Ano não informado.";
                $this->erro_campo = "rh222_ano";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($rh222_sequencial != null) {
            $sql .= " rh222_sequencial = $this->rh222_sequencial";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->rh222_sequencial));
            if ($this->numrows > 0) {

                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,1010053,'$this->rh222_sequencial','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh222_sequencial"]) || $this->rh222_sequencial != "") {
                        $resac = db_query("insert into db_acount values($acount,1010334,1010053,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'rh222_sequencial')) . "','$this->rh222_sequencial'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh222_valor"]) || $this->rh222_valor != "") {
                        $resac = db_query("insert into db_acount values($acount,1010334,1010054,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'rh222_valor')) . "','$this->rh222_valor'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh222_rubrica"]) || $this->rh222_rubrica != "") {
                        $resac = db_query("insert into db_acount values($acount,1010334,1010060,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'rh222_rubrica')) . "','$this->rh222_rubrica'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh222_servidor"]) || $this->rh222_servidor != "") {
                        $resac = db_query("insert into db_acount values($acount,1010334,1010059,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'rh222_servidor')) . "','$this->rh222_servidor'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh222_operadorasaude"]) || $this->rh222_operadorasaude != "") {
                        $resac = db_query("insert into db_acount values($acount,1010334,1010058,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'rh222_operadorasaude')) . "','$this->rh222_operadorasaude'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh222_instituicao"]) || $this->rh222_instituicao != "") {
                        $resac = db_query("insert into db_acount values($acount,1010334,1010057,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'rh222_instituicao')) . "','$this->rh222_instituicao'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh222_mes"]) || $this->rh222_mes != "") {
                        $resac = db_query("insert into db_acount values($acount,1010334,1010056,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'rh222_mes')) . "','$this->rh222_mes'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh222_ano"]) || $this->rh222_ano != "") {
                        $resac = db_query("insert into db_acount values($acount,1010334,1010055,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'rh222_ano')) . "','$this->rh222_ano'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Operadora de Saúde do Servidor  não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->rh222_sequencial;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Operadora de Saúde do Servidor  não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->rh222_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $this->rh222_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($rh222_sequencial = null, $dbwhere = null)
    {
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            if (empty($dbwhere)) {

                $resaco = $this->sql_record($this->sql_query_file($rh222_sequencial));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
            }
            if (($resaco != false) || ($this->numrows != 0)) {

                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,1010053,'$rh222_sequencial','E')");
                    $resac = db_query("insert into db_acount values($acount,1010334,1010053,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'rh222_sequencial')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010334,1010054,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'rh222_valor')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010334,1010060,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'rh222_rubrica')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010334,1010059,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'rh222_servidor')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010334,1010058,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'rh222_operadorasaude')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010334,1010057,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'rh222_instituicao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010334,1010056,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'rh222_mes')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010334,1010055,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'rh222_ano')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }
        $sql = " DELETE FROM servidoroperadorasaude
                    WHERE ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($rh222_sequencial)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " rh222_sequencial = $rh222_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Operadora de Saúde do Servidor  não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $rh222_sequencial;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Operadora de Saúde do Servidor  não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $rh222_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $rh222_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
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
            $this->numrows = 0;
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Erro ao selecionar os registros.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->numrows = pg_num_rows($result);
        if ($this->numrows == 0) {
            $this->erro_banco = "";
            $this->erro_sql = "Record Vazio na Tabela:servidoroperadorasaude";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($rh222_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos}";
        $sql .= "  from servidoroperadorasaude ";
        $sql .= "      inner join rhpessoal  on  rhpessoal.rh01_regist = servidoroperadorasaude.rh222_servidor";
        $sql .= "      inner join rhrubricas  on  rhrubricas.rh27_rubric = servidoroperadorasaude.rh222_rubrica and  rhrubricas.rh27_instit = servidoroperadorasaude.rh222_instituicao";
        $sql .= "      inner join operadorasaude  on  operadorasaude.rh221_sequencial = servidoroperadorasaude.rh222_operadorasaude";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = rhpessoal.rh01_numcgm";
        $sql .= "      inner join rhestcivil  on  rhestcivil.rh08_estciv = rhpessoal.rh01_estciv";
        $sql .= "      inner join rhraca  on  rhraca.rh18_raca = rhpessoal.rh01_raca";
        $sql .= "      left  join rhfuncao  on  rhfuncao.rh37_funcao = rhpessoal.rh01_funcao and  rhfuncao.rh37_instit = rhpessoal.rh01_instit";
        $sql .= "      inner join rhinstrucao  on  rhinstrucao.rh21_instru = rhpessoal.rh01_instru";
        $sql .= "      inner join rhnacionalidade  on  rhnacionalidade.rh06_nacionalidade = rhpessoal.rh01_nacion";
        $sql .= "      left  join rhsindicato  on  rhsindicato.rh116_sequencial = rhpessoal.rh01_rhsindicato";
        $sql .= "      inner join rhreajusteparidade  on  rhreajusteparidade.rh148_sequencial = rhpessoal.rh01_reajusteparidade";
        $sql .= "      inner join db_config  on  db_config.codigo = rhrubricas.rh27_instit";
        $sql .= "      inner join rhtipomedia  on  rhtipomedia.rh29_tipo = rhrubricas.rh27_calc1";
        $sql .= "      left  join rhfundamentacaolegal  on  rhfundamentacaolegal.rh137_sequencial = rhrubricas.rh27_rhfundamentacaolegal";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($rh222_sequencial)) {
                $sql2 .= " where servidoroperadorasaude.rh222_sequencial = $rh222_sequencial ";
            }
        } else {
            if (!empty($dbwhere)) {
                $sql2 = " where $dbwhere";
            }
        }
        $sql .= $sql2;
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }
        return $sql;
    }

    public function sql_query_file($rh222_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos} ";
        $sql .= "  from servidoroperadorasaude ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($rh222_sequencial)) {
                $sql2 .= " where servidoroperadorasaude.rh222_sequencial = $rh222_sequencial ";
            }
        } else {
            if (!empty($dbwhere)) {
                $sql2 = " where $dbwhere";
            }
        }
        $sql .= $sql2;
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }
        return $sql;
    }

}
