<?php

class cl_empanulado
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
    public $e94_codanu = 0;
    public $e94_numemp = 0;
    public $e94_valor = 0;
    public $e94_saldoant = 0;
    public $e94_data_dia = null;
    public $e94_data_mes = null;
    public $e94_data_ano = null;
    public $e94_data = null;
    public $e94_motivo = null;
    public $e94_empanuladotipo = 0;
    public $e94_usuario = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 e94_codanu = int4 = Código anulação
                 e94_numemp = int4 = Número
                 e94_valor = float8 = Valor anulado
                 e94_saldoant = float8 = Saldo anterior
                 e94_data = date = Data anulação
                 e94_motivo = text = Motivo
                 e94_empanuladotipo = int4 = Tipo da Anulacao
                 e94_usuario = int4 = Usuário
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("empanulado");
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
            $this->e94_codanu = ($this->e94_codanu == "" ? @$GLOBALS["HTTP_POST_VARS"]["e94_codanu"] : $this->e94_codanu);
            $this->e94_numemp = ($this->e94_numemp == "" ? @$GLOBALS["HTTP_POST_VARS"]["e94_numemp"] : $this->e94_numemp);
            $this->e94_valor = ($this->e94_valor == "" ? @$GLOBALS["HTTP_POST_VARS"]["e94_valor"] : $this->e94_valor);
            $this->e94_saldoant = ($this->e94_saldoant == "" ? @$GLOBALS["HTTP_POST_VARS"]["e94_saldoant"] : $this->e94_saldoant);
            if ($this->e94_data == "") {
                $this->e94_data_dia = ($this->e94_data_dia == "" ? @$GLOBALS["HTTP_POST_VARS"]["e94_data_dia"] : $this->e94_data_dia);
                $this->e94_data_mes = ($this->e94_data_mes == "" ? @$GLOBALS["HTTP_POST_VARS"]["e94_data_mes"] : $this->e94_data_mes);
                $this->e94_data_ano = ($this->e94_data_ano == "" ? @$GLOBALS["HTTP_POST_VARS"]["e94_data_ano"] : $this->e94_data_ano);
                if ($this->e94_data_dia != "") {
                    $this->e94_data = $this->e94_data_ano . "-" . $this->e94_data_mes . "-" . $this->e94_data_dia;
                }
            }
            $this->e94_motivo = ($this->e94_motivo == "" ? @$GLOBALS["HTTP_POST_VARS"]["e94_motivo"] : $this->e94_motivo);
            $this->e94_empanuladotipo = ($this->e94_empanuladotipo == "" ? @$GLOBALS["HTTP_POST_VARS"]["e94_empanuladotipo"] : $this->e94_empanuladotipo);
            $this->e94_usuario = db_getsession('DB_id_usuario');
        } else {
            $this->e94_codanu = ($this->e94_codanu == "" ? @$GLOBALS["HTTP_POST_VARS"]["e94_codanu"] : $this->e94_codanu);
        }
    }

    public function incluir($e94_codanu)
    {
        $this->atualizacampos();
        if ($this->e94_numemp == null) {
            $this->erro_sql = " Campo Número não informado.";
            $this->erro_campo = "e94_numemp";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e94_valor == null) {
            $this->erro_sql = " Campo Valor anulado nao Informado.";
            $this->erro_campo = "e94_valor";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e94_saldoant == null) {
            $this->erro_sql = " Campo Saldo anterior nao Informado.";
            $this->erro_campo = "e94_saldoant";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e94_data == null) {
            $this->erro_sql = " Campo Data anulação nao Informado.";
            $this->erro_campo = "e94_data_dia";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e94_motivo == null) {
            $this->erro_sql = " Campo Motivo nao Informado.";
            $this->erro_campo = "e94_motivo";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e94_empanuladotipo == null) {
            $this->e94_empanuladotipo = "0";
        }
        if ($e94_codanu == "" || $e94_codanu == null) {
            $result = db_query("select nextval('empanulado_e94_codanu_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: empanulado_e94_codanu_seq do campo: e94_codanu";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->e94_codanu = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from empanulado_e94_codanu_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $e94_codanu)) {
                $this->erro_sql = " Campo e94_codanu maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->e94_codanu = $e94_codanu;
            }
        }
        if (($this->e94_codanu == null) || ($this->e94_codanu == "")) {
            $this->erro_sql = " Campo e94_codanu nao declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into empanulado(
                                       e94_codanu
                                      ,e94_numemp
                                      ,e94_valor
                                      ,e94_saldoant
                                      ,e94_data
                                      ,e94_motivo
                                      ,e94_empanuladotipo
                                      ,e94_usuario
                       )
                values (
                                $this->e94_codanu
                               ,$this->e94_numemp
                               ,$this->e94_valor
                               ,$this->e94_saldoant
                               ," . ($this->e94_data == "null" || $this->e94_data == "" ? "null" : "'" . $this->e94_data . "'") . "
                               ,'$this->e94_motivo'
                               ,$this->e94_empanuladotipo
                               ,$this->e94_usuario
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = " ($this->e94_codanu) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = " já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = " ($this->e94_codanu) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->e94_codanu;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);

        return true;
    }

    public function alterar($e94_codanu = null)
    {
        $this->atualizacampos();
        $sql = " update empanulado set ";
        $virgula = "";
        if (trim($this->e94_codanu) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e94_codanu"])) {
            $sql .= $virgula . " e94_codanu = $this->e94_codanu ";
            $virgula = ",";
            if (trim($this->e94_codanu) == null) {
                $this->erro_sql = " Campo Código anulação não informado.";
                $this->erro_campo = "e94_codanu";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e94_numemp) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e94_numemp"])) {
            $sql .= $virgula . " e94_numemp = $this->e94_numemp ";
            $virgula = ",";
            if (trim($this->e94_numemp) == null) {
                $this->erro_sql = " Campo Número não informado.";
                $this->erro_campo = "e94_numemp";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e94_valor) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e94_valor"])) {
            $sql .= $virgula . " e94_valor = $this->e94_valor ";
            $virgula = ",";
            if (trim($this->e94_valor) == null) {
                $this->erro_sql = " Campo Valor anulado nao Informado.";
                $this->erro_campo = "e94_valor";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e94_saldoant) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e94_saldoant"])) {
            $sql .= $virgula . " e94_saldoant = $this->e94_saldoant ";
            $virgula = ",";
            if (trim($this->e94_saldoant) == null) {
                $this->erro_sql = " Campo Saldo anterior nao Informado.";
                $this->erro_campo = "e94_saldoant";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e94_data) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e94_data_dia"]) && ($GLOBALS["HTTP_POST_VARS"]["e94_data_dia"] != "")) {
            $sql .= $virgula . " e94_data = '$this->e94_data' ";
            $virgula = ",";
            if (trim($this->e94_data) == null) {
                $this->erro_sql = " Campo Data anulação nao Informado.";
                $this->erro_campo = "e94_data_dia";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["e94_data_dia"])) {
                $sql .= $virgula . " e94_data = null ";
                $virgula = ",";
                if (trim($this->e94_data) == null) {
                    $this->erro_sql = " Campo Data anulação nao Informado.";
                    $this->erro_campo = "e94_data_dia";
                    $this->erro_banco = "";
                    $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                    $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        if (trim($this->e94_motivo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e94_motivo"])) {
            $sql .= $virgula . " e94_motivo = '$this->e94_motivo' ";
            $virgula = ",";
            if (trim($this->e94_motivo) == null) {
                $this->erro_sql = " Campo Motivo nao Informado.";
                $this->erro_campo = "e94_motivo";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e94_empanuladotipo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e94_empanuladotipo"])) {
            if (trim($this->e94_empanuladotipo) == "" && isset($GLOBALS["HTTP_POST_VARS"]["e94_empanuladotipo"])) {
                $this->e94_empanuladotipo = "0";
            }
            $sql .= $virgula . " e94_empanuladotipo = $this->e94_empanuladotipo ";
            $virgula = ",";
        }

        if (!empty($this->e94_usuario)) {
            $sql .= $virgula . " e94_usuario = $this->e94_usuario ";
            $virgula = ",";
        }

        $sql .= " where ";
        if ($e94_codanu != null) {
            $sql .= " e94_codanu = $this->e94_codanu";
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = " não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->e94_codanu;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = " não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->e94_codanu;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $this->e94_codanu;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($e94_codanu = null, $dbwhere = null)
    {
        $sql = " delete from empanulado
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($e94_codanu)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " e94_codanu = $e94_codanu ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = " Empenhos anulados não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $e94_codanu;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Empenhos anulados não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $e94_codanu;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $e94_codanu;
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
            $this->erro_sql = "Record Vazio na Tabela:empanulado";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($e94_codanu = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos}";
        $sql .= "  from empanulado ";
        $sql .= "      inner join empempenho  on  empempenho.e60_numemp = empanulado.e94_numemp";
        $sql .= "      inner join empanuladotipo  on  empanuladotipo.e38_sequencial = empanulado.e94_empanuladotipo";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = empempenho.e60_numcgm";
        $sql .= "      inner join db_config  on  db_config.codigo = empempenho.e60_instit";
        $sql .= "      inner join orcdotacao  on  orcdotacao.o58_anousu = empempenho.e60_anousu and  orcdotacao.o58_coddot = empempenho.e60_coddot";
        $sql .= "      inner join pctipocompra  on  pctipocompra.pc50_codcom = empempenho.e60_codcom";
        $sql .= "      inner join emptipo  on  emptipo.e41_codtipo = empempenho.e60_codtipo";
        $sql .= "      inner join concarpeculiar  on  concarpeculiar.c58_sequencial = empempenho.e60_concarpeculiar";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($e94_codanu)) {
                $sql2 .= " where empanulado.e94_codanu = $e94_codanu ";
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

    public function sql_query_file($e94_codanu = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos} ";
        $sql .= "  from empanulado ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($e94_codanu)) {
                $sql2 .= " where empanulado.e94_codanu = $e94_codanu ";
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

    public function sql_query_empenho($campos = "*", $dbwhere = null)
    {
        $sql = " select {$campos} ";
        $sql .= "   from empanulado ";
        $sql .= "        inner join empempenho  on  empempenho.e60_numemp = empanulado.e94_numemp ";
        $sql .= "        inner join empanuladotipo  on  empanuladotipo.e38_sequencial = empanulado.e94_empanuladotipo ";
        $sql .= "        inner join db_config  on  db_config.codigo = empempenho.e60_instit ";
        $sql .= "        inner join orcdotacao  on  orcdotacao.o58_anousu = empempenho.e60_anousu and  orcdotacao.o58_coddot = empempenho.e60_coddot ";
        if (!empty($dbwhere)) {
            $sql .= " where {$dbwhere} ";
        }
        return $sql;
    }
}
