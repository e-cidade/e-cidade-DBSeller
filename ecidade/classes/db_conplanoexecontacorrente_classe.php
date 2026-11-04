<?php

class cl_conplanoexecontacorrente
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
    public $id = 0;
    public $c143_conplanoreduz = 0;
    public $c143_exercicio = 0;
    public $c143_conplanosistema = 0;
    public $c143_saldo = 0;
    public $c143_natureza = null;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
        id = int8 = id
        c143_conplanoreduz = int4 = Reduzido
        c143_exercicio = int4 = Exercício
        c143_conplanosistema = int4 = Conta Corrente
        c143_saldo = float8 = Saldo da conta
        c143_natureza = char(1) = Natureza
    ";

    public function __construct()
    {
        $this->rotulo = new rotulo("conplanoexecontacorrente");
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
            $this->id = ($this->id == "" ? @$GLOBALS["HTTP_POST_VARS"]["id"] : $this->id);
            $this->c143_conplanoreduz = ($this->c143_conplanoreduz == "" ? @$GLOBALS["HTTP_POST_VARS"]["c143_conplanoreduz"] : $this->c143_conplanoreduz);
            $this->c143_exercicio = ($this->c143_exercicio == "" ? @$GLOBALS["HTTP_POST_VARS"]["c143_exercicio"] : $this->c143_exercicio);
            $this->c143_conplanosistema = ($this->c143_conplanosistema == "" ? @$GLOBALS["HTTP_POST_VARS"]["c143_conplanosistema"] : $this->c143_conplanosistema);
            $this->c143_saldo = is_null($this->c143_saldo) ? @$GLOBALS["HTTP_POST_VARS"]["c143_saldo"] : $this->c143_saldo;
            $this->c143_natureza = ($this->c143_natureza == "" ? @$GLOBALS["HTTP_POST_VARS"]["c143_natureza"] : $this->c143_natureza);
        } else {
            $this->id = ($this->id == "" ? @$GLOBALS["HTTP_POST_VARS"]["id"] : $this->id);
        }
    }

    public function incluir($id)
    {
        $this->atualizacampos();
        if ($this->c143_conplanoreduz == null) {
            $this->erro_sql = " Campo Reduzido não informado.";
            $this->erro_campo = "c143_conplanoreduz";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->c143_exercicio == null) {
            $this->erro_sql = " Campo Exercício não informado.";
            $this->erro_campo = "c143_exercicio";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->c143_conplanosistema == null) {
            $this->erro_sql = " Campo Conta Corrente não informado.";
            $this->erro_campo = "c143_conplanosistema";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if (is_null($this->c143_saldo)) {
            $this->erro_sql = " Campo Saldo da conta não informado.";
            $this->erro_campo = "c143_saldo";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->c143_natureza == null) {
            $this->erro_sql = " Campo Natureza não informado.";
            $this->erro_campo = "c143_natureza";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($id == "" || $id == null) {
            $result = db_query("select nextval('conplanoexecontacorrente_id_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: conplanoexecontacorrente_id_seq do campo: id";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->id = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from conplanoexecontacorrente_id_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $id)) {
                $this->erro_sql = " Campo id maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->id = $id;
            }
        }
        if (($this->id == null) || ($this->id == "")) {
            $this->erro_sql = " Campo id não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into conplanoexecontacorrente(
                                       id
                                      ,c143_conplanoreduz
                                      ,c143_exercicio
                                      ,c143_conplanosistema
                                      ,c143_saldo
                                      ,c143_natureza
                       )
                values (
                                $this->id
                               ,$this->c143_conplanoreduz
                               ,$this->c143_exercicio
                               ,$this->c143_conplanosistema
                               ,$this->c143_saldo
                               ,'$this->c143_natureza'
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "Saldo da conta corrente ($this->id) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "Saldo da conta corrente já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "Saldo da conta corrente ($this->id) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->id;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->id));
            if (($resaco != false) || ($this->numrows != 0)) {

                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                $resac = db_query("insert into db_acountkey values($acount,1011345,'$this->id','I')");
                $resac = db_query("insert into db_acount values($acount,1011021,1011345,'','" . AddSlashes(pg_result($resaco, 0, 'id')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1011021,1014739,'','" . AddSlashes(pg_result($resaco, 0, 'c143_conplanoreduz')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1011021,1014740,'','" . AddSlashes(pg_result($resaco, 0, 'c143_exercicio')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1011021,1014741,'','" . AddSlashes(pg_result($resaco, 0, 'c143_conplanosistema')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1011021,1014742,'','" . AddSlashes(pg_result($resaco, 0, 'c143_saldo')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1011021,1014743,'','" . AddSlashes(pg_result($resaco, 0, 'c143_natureza')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            }
        }
        return true;
    }

    public function alterar($id = null)
    {
        $this->atualizacampos();
        $sql = " update conplanoexecontacorrente set ";
        $virgula = "";
        if (trim($this->id) != "" || isset($GLOBALS["HTTP_POST_VARS"]["id"])) {
            $sql .= $virgula . " id = $this->id ";
            $virgula = ",";
            if (trim($this->id) == null) {
                $this->erro_sql = " Campo id não informado.";
                $this->erro_campo = "id";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->c143_conplanoreduz) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c143_conplanoreduz"])) {
            $sql .= $virgula . " c143_conplanoreduz = $this->c143_conplanoreduz ";
            $virgula = ",";
            if (trim($this->c143_conplanoreduz) == null) {
                $this->erro_sql = " Campo Reduzido não informado.";
                $this->erro_campo = "c143_conplanoreduz";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->c143_exercicio) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c143_exercicio"])) {
            $sql .= $virgula . " c143_exercicio = $this->c143_exercicio ";
            $virgula = ",";
            if (trim($this->c143_exercicio) == null) {
                $this->erro_sql = " Campo Exercício não informado.";
                $this->erro_campo = "c143_exercicio";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->c143_conplanosistema) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c143_conplanosistema"])) {
            $sql .= $virgula . " c143_conplanosistema = $this->c143_conplanosistema ";
            $virgula = ",";
            if (trim($this->c143_conplanosistema) == null) {
                $this->erro_sql = " Campo Conta Corrente não informado.";
                $this->erro_campo = "c143_conplanosistema";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->c143_saldo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c143_saldo"])) {
            $sql .= $virgula . " c143_saldo = $this->c143_saldo ";
            $virgula = ",";
            if (trim($this->c143_saldo) == null) {
                $this->erro_sql = " Campo Saldo da conta não informado.";
                $this->erro_campo = "c143_saldo";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->c143_natureza) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c143_natureza"])) {
            $sql .= $virgula . " c143_natureza = '$this->c143_natureza' ";
            $virgula = ",";
            if (trim($this->c143_natureza) == null) {
                $this->erro_sql = " Campo Natureza não informado.";
                $this->erro_campo = "c143_natureza";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($id != null) {
            $sql .= " id = $this->id";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->id));
            if ($this->numrows > 0) {

                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,1011345,'$this->id','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["id"]) || $this->id != "")
                        $resac = db_query("insert into db_acount values($acount,1011021,1011345,'" . AddSlashes(pg_result($resaco, $conresaco, 'id')) . "','$this->id'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c143_conplanoreduz"]) || $this->c143_conplanoreduz != "")
                        $resac = db_query("insert into db_acount values($acount,1011021,1014739,'" . AddSlashes(pg_result($resaco, $conresaco, 'c143_conplanoreduz')) . "','$this->c143_conplanoreduz'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c143_exercicio"]) || $this->c143_exercicio != "")
                        $resac = db_query("insert into db_acount values($acount,1011021,1014740,'" . AddSlashes(pg_result($resaco, $conresaco, 'c143_exercicio')) . "','$this->c143_exercicio'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c143_conplanosistema"]) || $this->c143_conplanosistema != "")
                        $resac = db_query("insert into db_acount values($acount,1011021,1014741,'" . AddSlashes(pg_result($resaco, $conresaco, 'c143_conplanosistema')) . "','$this->c143_conplanosistema'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c143_saldo"]) || $this->c143_saldo != "")
                        $resac = db_query("insert into db_acount values($acount,1011021,1014742,'" . AddSlashes(pg_result($resaco, $conresaco, 'c143_saldo')) . "','$this->c143_saldo'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c143_natureza"]) || $this->c143_natureza != "")
                        $resac = db_query("insert into db_acount values($acount,1011021,1014743,'" . AddSlashes(pg_result($resaco, $conresaco, 'c143_natureza')) . "','$this->c143_natureza'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Saldo da conta corrente não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->id;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Saldo da conta corrente não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->id;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $this->id;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($id = null, $dbwhere = null)
    {
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            if (empty($dbwhere)) {

                $resaco = $this->sql_record($this->sql_query_file($id));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
            }
            if (($resaco != false) || ($this->numrows != 0)) {

                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,1011345,'$id','E')");
                    $resac = db_query("insert into db_acount values($acount,1011021,1011345,'','" . AddSlashes(pg_result($resaco, $iresaco, 'id')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1011021,1014739,'','" . AddSlashes(pg_result($resaco, $iresaco, 'c143_conplanoreduz')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1011021,1014740,'','" . AddSlashes(pg_result($resaco, $iresaco, 'c143_exercicio')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1011021,1014741,'','" . AddSlashes(pg_result($resaco, $iresaco, 'c143_conplanosistema')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1011021,1014742,'','" . AddSlashes(pg_result($resaco, $iresaco, 'c143_saldo')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1011021,1014743,'','" . AddSlashes(pg_result($resaco, $iresaco, 'c143_natureza')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }
        $sql = " delete from conplanoexecontacorrente
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($id)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " id = $id ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Saldo da conta corrente não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $id;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Saldo da conta corrente não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $id;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $id;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
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
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->numrows = pg_num_rows($result);
        if ($this->numrows == 0) {
            $this->erro_banco = "";
            $this->erro_sql = "Record Vazio na Tabela:conplanoexecontacorrente";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($id = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos}";
        $sql .= "  from conplanoexecontacorrente ";
        $sql .= "      inner join conplanoreduz  on  conplanoreduz.c61_reduz = conplanoexecontacorrente.c143_conplanoreduz and  conplanoreduz.c61_anousu = conplanoexecontacorrente.c143_exercicio";
        $sql .= "      inner join db_config  on  db_config.codigo = conplanoreduz.c61_instit";
        $sql .= "      inner join orctiporec  on  orctiporec.o15_codigo = conplanoreduz.c61_codigo";
        $sql .= "      inner join conplano  on  conplano.c60_codcon = conplanoreduz.c61_codcon and  conplano.c60_anousu = conplanoreduz.c61_anousu";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($id)) {
                $sql2 .= " where conplanoexecontacorrente.id = $id ";
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

    public function sql_query_file($id = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos} ";
        $sql .= "  from conplanoexecontacorrente ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($id)) {
                $sql2 .= " where conplanoexecontacorrente.id = $id ";
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

    public function sql_saldo_recurso($reduzido, $exercicio, $idRecurso)
    {
        $where = [
            "c143_conplanosistema = 100",
            "c144_conplanoinfocomplementar = 100",
        ];
        if (!empty($reduzido)) {
            $where[] = "c143_conplanoreduz = {$reduzido}";
        }
        if (!empty($exercicio)) {
            $where[] = "c143_exercicio = {$exercicio}";
        }
        if (!empty($idRecurso)) {
            $where[] = "c144_valor::int = {$idRecurso}";
        }
        $where = implode(' and ', $where);
        $sql = "
        select
            cc.id,
            c143_conplanoreduz,
            c143_exercicio,
            c143_conplanosistema,
            c143_saldo,
            c143_natureza,
            c144_valor
          from contabilidade.conplanoexecontacorrente cc
          join contabilidade.conplanoexecontacorrenteatributo ca on ca.c144_conplanoexecontacorrente = cc.id
        where {$where}
        ";

        return $sql;
    }

}
