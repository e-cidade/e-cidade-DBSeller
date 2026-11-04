<?php

class cl_pcdotac
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
    public $pc13_sequencial = 0;
    public $pc13_anousu = 0;
    public $pc13_coddot = 0;
    public $pc13_codigo = 0;
    public $pc13_depto = 0;
    public $pc13_quant = 0;
    public $pc13_valor = 0;
    public $pc13_codele = 0;
    public $pc13_planoorcamentariolinhapacto = null;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 pc13_sequencial = int4 = Código Sequencial
                 pc13_anousu = int4 = Ano
                 pc13_coddot = int4 = Dotacao orcamentaria
                 pc13_codigo = int8 = Código sequencial do registro
                 pc13_depto = int4 = Departamento
                 pc13_quant = float8 = quantidade solicitada
                 pc13_valor = float8 = Valor
                 pc13_codele = int4 = Código Elemento
                 pc13_planoorcamentariolinhapacto = int4 = Linha de Pacto
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("pcdotac");
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
            $this->pc13_sequencial = ($this->pc13_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["pc13_sequencial"] : $this->pc13_sequencial);
            $this->pc13_anousu = ($this->pc13_anousu == "" ? @$GLOBALS["HTTP_POST_VARS"]["pc13_anousu"] : $this->pc13_anousu);
            $this->pc13_coddot = ($this->pc13_coddot == "" ? @$GLOBALS["HTTP_POST_VARS"]["pc13_coddot"] : $this->pc13_coddot);
            $this->pc13_codigo = ($this->pc13_codigo == "" ? @$GLOBALS["HTTP_POST_VARS"]["pc13_codigo"] : $this->pc13_codigo);
            $this->pc13_depto = ($this->pc13_depto == "" ? @$GLOBALS["HTTP_POST_VARS"]["pc13_depto"] : $this->pc13_depto);
            $this->pc13_quant = ($this->pc13_quant == "" ? @$GLOBALS["HTTP_POST_VARS"]["pc13_quant"] : $this->pc13_quant);
            $this->pc13_valor = ($this->pc13_valor == "" ? @$GLOBALS["HTTP_POST_VARS"]["pc13_valor"] : $this->pc13_valor);
            $this->pc13_codele = ($this->pc13_codele == "" ? @$GLOBALS["HTTP_POST_VARS"]["pc13_codele"] : $this->pc13_codele);
            $this->pc13_planoorcamentariolinhapacto = ($this->pc13_planoorcamentariolinhapacto == "" ? @$GLOBALS["HTTP_POST_VARS"]["pc13_planoorcamentariolinhapacto"] : $this->pc13_planoorcamentariolinhapacto);
        } else {
            $this->pc13_sequencial = ($this->pc13_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["pc13_sequencial"] : $this->pc13_sequencial);
        }
    }

    public function incluir($pc13_sequencial)
    {
        $this->atualizacampos();
        if ($this->pc13_anousu == null) {
            $this->erro_sql = " Campo Ano não informado.";
            $this->erro_campo = "pc13_anousu";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->pc13_coddot == null) {
            $this->erro_sql = " Campo Dotacao orcamentaria não informado.";
            $this->erro_campo = "pc13_coddot";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->pc13_codigo == null) {
            $this->erro_sql = " Campo Código sequencial do registro não informado.";
            $this->erro_campo = "pc13_codigo";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->pc13_depto == null) {
            $this->erro_sql = " Campo Departamento não informado.";
            $this->erro_campo = "pc13_depto";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->pc13_quant == null) {
            $this->erro_sql = " Campo quantidade solicitada não informado.";
            $this->erro_campo = "pc13_quant";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->pc13_valor == null) {
            $this->erro_sql = " Campo Valor não informado.";
            $this->erro_campo = "pc13_valor";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->pc13_codele == null) {
            $this->erro_sql = " Campo Código Elemento não informado.";
            $this->erro_campo = "pc13_codele";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->pc13_planoorcamentariolinhapacto == null) {
            $this->pc13_planoorcamentariolinhapacto = "null";
        }
        if ($pc13_sequencial == "" || $pc13_sequencial == null) {
            $result = db_query("select nextval('pcdotac_pc13_sequencial_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: pcdotac_pc13_sequencial_seq do campo: pc13_sequencial";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->pc13_sequencial = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from pcdotac_pc13_sequencial_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $pc13_sequencial)) {
                $this->erro_sql = " Campo pc13_sequencial maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->pc13_sequencial = $pc13_sequencial;
            }
        }
        if (($this->pc13_sequencial == null) || ($this->pc13_sequencial == "")) {
            $this->erro_sql = " Campo pc13_sequencial não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into pcdotac(
                                       pc13_sequencial
                                      ,pc13_anousu
                                      ,pc13_coddot
                                      ,pc13_codigo
                                      ,pc13_depto
                                      ,pc13_quant
                                      ,pc13_valor
                                      ,pc13_codele
                                      ,pc13_planoorcamentariolinhapacto
                       )
                values (
                                $this->pc13_sequencial
                               ,$this->pc13_anousu
                               ,$this->pc13_coddot
                               ,$this->pc13_codigo
                               ,$this->pc13_depto
                               ,$this->pc13_quant
                               ,$this->pc13_valor
                               ,$this->pc13_codele
                               ,$this->pc13_planoorcamentariolinhapacto
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "dotacoes por item de cada solicitacao ($this->pc13_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "dotacoes por item de cada solicitacao já Cadastrado";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "dotacoes por item de cada solicitacao ($this->pc13_sequencial) não Incluído. Inclusão Abortada.";
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
        $this->erro_sql .= "Valores : " . $this->pc13_sequencial;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "",
            str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file(null, null, null, "*", null,
                "pc13_sequencial={$this->pc13_sequencial}"));
            if (($resaco != false) || ($this->numrows != 0)) {

                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                $resac = db_query("insert into db_acountkey values($acount,11920,'$this->pc13_sequencial','I')");
                $resac = db_query("insert into db_acount values($acount,159,11920,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'pc13_sequencial')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,159,5559,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'pc13_anousu')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,159,5560,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'pc13_coddot')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,159,5561,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'pc13_codigo')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,159,5562,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'pc13_depto')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,159,5563,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'pc13_quant')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,159,6286,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'pc13_valor')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,159,6495,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'pc13_codele')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,159,1010135,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'pc13_planoorcamentariolinhapacto')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            }
        }
        return true;
    }

    public function alterar($pc13_sequencial = null)
    {
        $this->atualizacampos();
        $sql = " update pcdotac set ";
        $virgula = "";
        if (trim($this->pc13_sequencial) != "" || isset($GLOBALS["HTTP_POST_VARS"]["pc13_sequencial"])) {
            $sql .= $virgula . " pc13_sequencial = $this->pc13_sequencial ";
            $virgula = ",";
            if (trim($this->pc13_sequencial) == null) {
                $this->erro_sql = " Campo Código Sequencial não informado.";
                $this->erro_campo = "pc13_sequencial";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->pc13_anousu) != "" || isset($GLOBALS["HTTP_POST_VARS"]["pc13_anousu"])) {
            $sql .= $virgula . " pc13_anousu = $this->pc13_anousu ";
            $virgula = ",";
            if (trim($this->pc13_anousu) == null) {
                $this->erro_sql = " Campo Ano não informado.";
                $this->erro_campo = "pc13_anousu";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->pc13_coddot) != "" || isset($GLOBALS["HTTP_POST_VARS"]["pc13_coddot"])) {
            $sql .= $virgula . " pc13_coddot = $this->pc13_coddot ";
            $virgula = ",";
            if (trim($this->pc13_coddot) == null) {
                $this->erro_sql = " Campo Dotacao orcamentaria não informado.";
                $this->erro_campo = "pc13_coddot";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->pc13_codigo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["pc13_codigo"])) {
            $sql .= $virgula . " pc13_codigo = $this->pc13_codigo ";
            $virgula = ",";
            if (trim($this->pc13_codigo) == null) {
                $this->erro_sql = " Campo Código sequencial do registro não informado.";
                $this->erro_campo = "pc13_codigo";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->pc13_depto) != "" || isset($GLOBALS["HTTP_POST_VARS"]["pc13_depto"])) {
            $sql .= $virgula . " pc13_depto = $this->pc13_depto ";
            $virgula = ",";
            if (trim($this->pc13_depto) == null) {
                $this->erro_sql = " Campo Departamento não informado.";
                $this->erro_campo = "pc13_depto";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->pc13_quant) != "" || isset($GLOBALS["HTTP_POST_VARS"]["pc13_quant"])) {
            $sql .= $virgula . " pc13_quant = $this->pc13_quant ";
            $virgula = ",";
            if (trim($this->pc13_quant) == null) {
                $this->erro_sql = " Campo quantidade solicitada não informado.";
                $this->erro_campo = "pc13_quant";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->pc13_valor) != "" || isset($GLOBALS["HTTP_POST_VARS"]["pc13_valor"])) {
            $sql .= $virgula . " pc13_valor = $this->pc13_valor ";
            $virgula = ",";
            if (trim($this->pc13_valor) == null) {
                $this->erro_sql = " Campo Valor não informado.";
                $this->erro_campo = "pc13_valor";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->pc13_codele) != "" || isset($GLOBALS["HTTP_POST_VARS"]["pc13_codele"])) {
            $sql .= $virgula . " pc13_codele = $this->pc13_codele ";
            $virgula = ",";
            if (trim($this->pc13_codele) == null) {
                $this->erro_sql = " Campo Código Elemento não informado.";
                $this->erro_campo = "pc13_codele";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->pc13_planoorcamentariolinhapacto) != "" || isset($GLOBALS["HTTP_POST_VARS"]["pc13_planoorcamentariolinhapacto"])) {
            if (trim($this->pc13_planoorcamentariolinhapacto) == "" && isset($GLOBALS["HTTP_POST_VARS"]["pc13_planoorcamentariolinhapacto"])) {
                $this->pc13_planoorcamentariolinhapacto = "null";
            }
            $sql .= $virgula . " pc13_planoorcamentariolinhapacto = $this->pc13_planoorcamentariolinhapacto ";
            $virgula = ",";
        }
        $sql .= " where ";
        if ($pc13_sequencial != null) {
            $sql .= " pc13_sequencial = $this->pc13_sequencial";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file(null, null, null, "*", null,
                "pc13_sequencial={$this->pc13_sequencial}"));
            if ($this->numrows > 0) {

                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,11920,'$this->pc13_sequencial','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["pc13_sequencial"]) || $this->pc13_sequencial != "") {
                        $resac = db_query("insert into db_acount values($acount,159,11920,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'pc13_sequencial')) . "','$this->pc13_sequencial'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["pc13_anousu"]) || $this->pc13_anousu != "") {
                        $resac = db_query("insert into db_acount values($acount,159,5559,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'pc13_anousu')) . "','$this->pc13_anousu'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["pc13_coddot"]) || $this->pc13_coddot != "") {
                        $resac = db_query("insert into db_acount values($acount,159,5560,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'pc13_coddot')) . "','$this->pc13_coddot'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["pc13_codigo"]) || $this->pc13_codigo != "") {
                        $resac = db_query("insert into db_acount values($acount,159,5561,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'pc13_codigo')) . "','$this->pc13_codigo'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["pc13_depto"]) || $this->pc13_depto != "") {
                        $resac = db_query("insert into db_acount values($acount,159,5562,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'pc13_depto')) . "','$this->pc13_depto'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["pc13_quant"]) || $this->pc13_quant != "") {
                        $resac = db_query("insert into db_acount values($acount,159,5563,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'pc13_quant')) . "','$this->pc13_quant'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["pc13_valor"]) || $this->pc13_valor != "") {
                        $resac = db_query("insert into db_acount values($acount,159,6286,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'pc13_valor')) . "','$this->pc13_valor'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["pc13_codele"]) || $this->pc13_codele != "") {
                        $resac = db_query("insert into db_acount values($acount,159,6495,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'pc13_codele')) . "','$this->pc13_codele'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["pc13_planoorcamentariolinhapacto"]) || $this->pc13_planoorcamentariolinhapacto != "") {
                        $resac = db_query("insert into db_acount values($acount,159,1010135,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'pc13_planoorcamentariolinhapacto')) . "','$this->pc13_planoorcamentariolinhapacto'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "dotacoes por item de cada solicitacao não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->pc13_sequencial;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "dotacoes por item de cada solicitacao não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->pc13_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $this->pc13_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($pc13_sequencial = null, $dbwhere = null)
    {
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            if (empty($dbwhere)) {

                $resaco = $this->sql_record($this->sql_query_file(null, null, null, "*", null,
                    "pc13_sequencial={$pc13_sequencial}"));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null, null, null, "*", null, $dbwhere));
            }
            if (($resaco != false) || ($this->numrows != 0)) {

                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,11920,'$pc13_sequencial','E')");
                    $resac = db_query("insert into db_acount values($acount,159,11920,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'pc13_sequencial')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,159,5559,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'pc13_anousu')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,159,5560,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'pc13_coddot')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,159,5561,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'pc13_codigo')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,159,5562,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'pc13_depto')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,159,5563,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'pc13_quant')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,159,6286,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'pc13_valor')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,159,6495,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'pc13_codele')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,159,1010135,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'pc13_planoorcamentariolinhapacto')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }
        $sql = " delete from pcdotac
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($pc13_sequencial)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " pc13_sequencial = $pc13_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "dotacoes por item de cada solicitacao não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $pc13_sequencial;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "dotacoes por item de cada solicitacao não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $pc13_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $pc13_sequencial;
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
            $this->erro_sql = "Record Vazio na Tabela:pcdotac";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    function sql_query(
        $pc13_codigo = null,
        $pc13_anousu = null,
        $pc13_coddot = null,
        $campos = "*",
        $ordem = null,
        $dbwhere = ""
    ) {
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
        $sql .= " from pcdotac ";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = pcdotac.pc13_depto";
        $sql .= "      inner join orcelemento  on  orcelemento.o56_codele = pcdotac.pc13_codele and orcelemento.o56_anousu = " . db_getsession("DB_anousu");
        $sql .= "      inner join orcdotacao  on  orcdotacao.o58_anousu = pcdotac.pc13_anousu and  orcdotacao.o58_coddot = pcdotac.pc13_coddot";
        $sql .= "      inner join solicitem  on  solicitem.pc11_codigo = pcdotac.pc13_codigo";
        $sql .= "      inner join db_config  on  db_config.codigo = orcdotacao.o58_instit";
        $sql .= "      inner join orctiporec  on  orctiporec.o15_codigo = orcdotacao.o58_codigo";
        $sql .= "      inner join orcfuncao  on  orcfuncao.o52_funcao = orcdotacao.o58_funcao";
        $sql .= "      inner join orcsubfuncao  on  orcsubfuncao.o53_subfuncao = orcdotacao.o58_subfuncao";
        $sql .= "      inner join orcprograma  on  orcprograma.o54_anousu = orcdotacao.o58_anousu and  orcprograma.o54_programa = orcdotacao.o58_programa";
        $sql .= "      inner join orcelemento  as a on   a.o56_codele = orcdotacao.o58_codele and a.o56_anousu = orcdotacao.o58_anousu";
        $sql .= "      inner join orcprojativ  on  orcprojativ.o55_anousu = orcdotacao.o58_anousu and  orcprojativ.o55_projativ = orcdotacao.o58_projativ";
        $sql .= "      inner join orcorgao  on  orcorgao.o40_anousu = orcdotacao.o58_anousu and  orcorgao.o40_orgao = orcdotacao.o58_orgao";
        $sql .= "      inner join orcunidade  on  orcunidade.o41_anousu = orcdotacao.o58_anousu and  orcunidade.o41_orgao = orcdotacao.o58_orgao and  orcunidade.o41_unidade = orcdotacao.o58_unidade";
        $sql .= "      inner join db_config  as b on   b.codigo = orcdotacao.o58_instit";
        $sql .= "      inner join orctiporec  as c on   c.o15_codigo = orcdotacao.o58_codigo";
        $sql .= "      inner join orcfuncao  as d on   d.o52_funcao = orcdotacao.o58_funcao";
        $sql .= "      inner join orcsubfuncao  as d on   d.o53_subfuncao = orcdotacao.o58_subfuncao";
        $sql .= "      inner join orcprograma  as d on   d.o54_anousu = orcdotacao.o58_anousu and   d.o54_programa = orcdotacao.o58_programa";
        $sql .= "      inner join orcelemento  as d on   d.o56_codele = orcdotacao.o58_codele and d.o56_anousu = orcdotacao.o58_anousu";
        $sql .= "      inner join orcprojativ  as d on   d.o55_anousu = orcdotacao.o58_anousu and   d.o55_projativ = orcdotacao.o58_projativ";
        $sql .= "      inner join orcorgao  as d on   d.o40_anousu = orcdotacao.o58_anousu and   d.o40_orgao = orcdotacao.o58_orgao";
        $sql .= "      inner join orcunidade  as d on   d.o41_anousu = orcdotacao.o58_anousu and   d.o41_orgao = orcdotacao.o58_orgao and   d.o41_unidade = orcdotacao.o58_unidade";
        $sql .= "      inner join solicita  on  solicita.pc10_numero = solicitem.pc11_numero";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($pc13_codigo != null) {
                $sql2 .= " where pcdotac.pc13_codigo = $pc13_codigo ";
            }
            if ($pc13_anousu != null) {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " pcdotac.pc13_anousu = $pc13_anousu ";
            }
            if ($pc13_coddot != null) {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " pcdotac.pc13_coddot = $pc13_coddot ";
            }
        } else {
            if ($dbwhere != "") {
                $sql2 = " where $dbwhere";
            }
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


    function sql_query_depart(
        $pc13_codigo = null,
        $pc13_anousu = null,
        $pc13_coddot = null,
        $campos = "*",
        $ordem = null,
        $dbwhere = ""
    ) {

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
        $sql .= " from pcdotac ";
        $sql .= "      inner join solicitem            on solicitem.pc11_codigo = pcdotac.pc13_codigo ";
        $sql .= "      inner join db_depart            on db_depart.coddepto = pcdotac.pc13_depto ";
        $sql .= "      left  join pcdotaccontrapartida on pc13_sequencial    = pc19_pcdotac ";
        $sql .= "      left  join planoorcamentariolinhapacto on pc13_planoorcamentariolinhapacto = o156_sequencial ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($pc13_codigo != null) {
                $sql2 .= " where pcdotac.pc13_codigo = $pc13_codigo ";
            }
            if ($pc13_anousu != null) {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " pcdotac.pc13_anousu = $pc13_anousu ";
            }
            if ($pc13_coddot != null) {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " pcdotac.pc13_coddot = $pc13_coddot ";
            }
        } else {
            if ($dbwhere != "") {
                $sql2 = " where $dbwhere";
            }
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

    function sql_query_descrdot(
        $pc13_codigo = null,
        $pc13_anousu = null,
        $pc13_coddot = null,
        $campos = "*",
        $ordem = null,
        $dbwhere = ""
    ) {
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
        $sql .= " from pcdotac ";
        $sql .= "      inner join orcdotacao  on  orcdotacao.o58_anousu = pcdotac.pc13_anousu and  orcdotacao.o58_coddot = pcdotac.pc13_coddot";
        $sql .= "      inner join orcelemento  on  orcelemento.o56_codele = orcdotacao.o58_codele and orcelemento.o56_anousu = orcdotacao.o58_anousu";
        $sql .= "      left  join pcdotaccontrapartida     on  pc13_sequencial        = pc19_pcdotac";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($pc13_codigo != null) {
                $sql2 .= " where pcdotac.pc13_codigo = $pc13_codigo ";
            }
            if ($pc13_anousu != null) {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " pcdotac.pc13_anousu = $pc13_anousu ";
            }
            if ($pc13_coddot != null) {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " pcdotac.pc13_coddot = $pc13_coddot ";
            }
        } else {
            if ($dbwhere != "") {
                $sql2 = " where $dbwhere";
            }
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

    function sql_query_dotreserva(
        $pc13_codigo = null,
        $pc13_anousu = null,
        $pc13_coddot = null,
        $campos = "*",
        $ordem = null,
        $dbwhere = ""
    ) {
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
        $sql .= " from pcdotac ";
        $sql .= "      inner join solicitem on solicitem.pc11_codigo = pcdotac.pc13_codigo";
        $sql .= "      left  join pcdotaccontrapartida on pc13_sequencial = pc19_pcdotac";
        $sql .= "      inner join orcdotacao  on  orcdotacao.o58_anousu = pcdotac.pc13_anousu and  orcdotacao.o58_coddot = pcdotac.pc13_coddot";
        $sql .= "      inner join orcprojativ on orcprojativ.o55_anousu = orcdotacao.o58_anousu and orcprojativ.o55_projativ = orcdotacao.o58_projativ";
        $sql .= "      inner join orcelemento  on  orcelemento.o56_codele = orcdotacao.o58_codele and orcelemento.o56_anousu = orcdotacao.o58_anousu";
        $sql .= "      left  join orcreservasol  on  orcreservasol.o82_pcdotac = pcdotac.pc13_sequencial";
        $sql .= "      left  join orcreserva on orcreserva.o80_coddot = pcdotac.pc13_coddot and orcreserva.o80_codres = orcreservasol.o82_codres";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($pc13_codigo != null) {
                $sql2 .= " where pcdotac.pc13_codigo = $pc13_codigo ";
            }
            if ($pc13_anousu != null) {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " pcdotac.pc13_anousu = $pc13_anousu ";
            }
            if ($pc13_coddot != null) {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " pcdotac.pc13_coddot = $pc13_coddot ";
            }
        } else {
            if ($dbwhere != "") {
                $sql2 = " where $dbwhere";
            }
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

    function sql_query_file(
        $pc13_codigo = null,
        $pc13_anousu = null,
        $pc13_coddot = null,
        $campos = "*",
        $ordem = null,
        $dbwhere = ""
    ) {
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
        $sql .= " from pcdotac ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($pc13_codigo != null) {
                $sql2 .= " where pcdotac.pc13_codigo = $pc13_codigo ";
            }
            if ($pc13_anousu != null) {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " pcdotac.pc13_anousu = $pc13_anousu ";
            }
            if ($pc13_coddot != null) {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " pcdotac.pc13_coddot = $pc13_coddot ";
            }
        } else {
            if ($dbwhere != "") {
                $sql2 = " where $dbwhere";
            }
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

    function sql_query_lefdotac(
        $pc13_codigo = null,
        $pc13_anousu = null,
        $pc13_coddot = null,
        $campos = "*",
        $ordem = null,
        $dbwhere = ""
    ) {
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
        $sql .= " from pcdotac ";
        $sql .= "      left join solicitem on solicitem.pc11_codigo = pcdotac.pc13_codigo ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($pc13_codigo != null) {
                $sql2 .= " where pcdotac.pc13_codigo = $pc13_codigo ";
            }
            if ($pc13_anousu != null) {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " pcdotac.pc13_anousu = $pc13_anousu ";
            }
            if ($pc13_coddot != null) {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " pcdotac.pc13_coddot = $pc13_coddot ";
            }
        } else {
            if ($dbwhere != "") {
                $sql2 = " where $dbwhere";
            }
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

    function sql_query_solicita(
        $pc13_codigo = null,
        $pc13_anousu = null,
        $pc13_coddot = null,
        $campos = "*",
        $ordem = null,
        $dbwhere = ""
    ) {
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
        $sql .= " from pcdotac ";
        $sql .= "      inner join solicitem on solicitem.pc11_codigo = pcdotac.pc13_codigo ";
        $sql .= "      inner join solicita on solicita.pc10_numero = solicitem.pc11_numero ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($pc13_codigo != null) {
                $sql2 .= " where pcdotac.pc13_codigo = $pc13_codigo ";
            }
            if ($pc13_anousu != null) {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " pcdotac.pc13_anousu = $pc13_anousu ";
            }
            if ($pc13_coddot != null) {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " pcdotac.pc13_coddot = $pc13_coddot ";
            }
        } else {
            if ($dbwhere != "") {
                $sql2 = " where $dbwhere";
            }
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

    function sql_query_dotacao(
        $pc13_codigo = null,
        $pc13_anousu = null,
        $pc13_coddot = null,
        $campos = "*",
        $ordem = null,
        $dbwhere = ""
    ) {
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
        $sql .= " from pcdotac                                                                               ";
        $sql .= "      inner join orcreservasol   on orcreservasol.o82_pcdotac  = pcdotac.pc13_sequencial    ";
        $sql .= "      inner join orcreserva      on orcreserva.o80_codres      = orcreservasol.o82_codres   ";
        $sql .= "      inner join orcdotacao      on orcdotacao.o58_anousu      = orcreserva.o80_anousu      ";
        $sql .= "                                and orcdotacao.o58_coddot      = orcreserva.o80_coddot      ";
        $sql .= "      inner join orcorgao        on orcorgao.o40_anousu        = orcdotacao.o58_anousu      ";
        $sql .= "                                and orcorgao.o40_orgao         = orcdotacao.o58_orgao       ";
        $sql .= "      inner join orcunidade      on orcunidade.o41_anousu      = orcdotacao.o58_anousu      ";
        $sql .= "                                and orcunidade.o41_orgao       = orcdotacao.o58_orgao       ";
        $sql .= "                                and orcunidade.o41_unidade     = orcdotacao.o58_unidade     ";
        $sql .= "      inner join orcprograma     on orcprograma.o54_anousu     = orcdotacao.o58_anousu      ";
        $sql .= "                                and orcprograma.o54_programa   = orcdotacao.o58_programa    ";
        $sql .= "      inner join orcprojativ     on orcprojativ.o55_anousu     = orcdotacao.o58_anousu      ";
        $sql .= "                                and orcprojativ.o55_projativ   = orcdotacao.o58_projativ    ";
        $sql .= "      inner join orcelemento     on orcelemento.o56_codele     = orcdotacao.o58_codele      ";
        $sql .= "                                and orcelemento.o56_anousu     = orcdotacao.o58_anousu      ";
        $sql .= "      inner join orctiporec      on orctiporec.o15_codigo      = orcdotacao.o58_codigo      ";
        $sql .= "      inner join orcfuncao       on orcfuncao.o52_funcao       = orcdotacao.o58_funcao      ";
        $sql .= "      inner join orcsubfuncao    on orcsubfuncao.o53_subfuncao = orcdotacao.o58_subfuncao   ";


        $sql2 = "";
        if ($dbwhere == "") {
            if ($pc13_codigo != null) {
                $sql2 .= " where pcdotac.pc13_codigo = $pc13_codigo ";
            }
            if ($pc13_anousu != null) {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " pcdotac.pc13_anousu = $pc13_anousu ";
            }
            if ($pc13_coddot != null) {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " pcdotac.pc13_coddot = $pc13_coddot ";
            }
        } else {
            if ($dbwhere != "") {
                $sql2 = " where $dbwhere";
            }
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
}
