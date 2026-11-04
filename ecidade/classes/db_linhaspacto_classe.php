<?php
//MODULO: contabilidade
//CLASSE DA ENTIDADE linhaspacto
class cl_linhaspacto
{
    // cria variaveis de erro
    var $rotulo = null;
    var $query_sql = null;
    var $numrows = 0;
    var $numrows_incluir = 0;
    var $numrows_alterar = 0;
    var $numrows_excluir = 0;
    var $erro_status = null;
    var $erro_sql = null;
    var $erro_banco = null;
    var $erro_msg = null;
    var $erro_campo = null;
    var $pagina_retorno = null;
    // cria variaveis do arquivo
    var $c07_sequencial = 0;
    var $c07_titulo = null;
    var $c07_valor = 0;
    // cria propriedade com as variaveis do arquivo
    var $campos = "
                 c07_sequencial = int4 = Código
                 c07_titulo = varchar(255) = Título
                 c07_valor = float8 = Valor
                 ";

    //funcao construtor da classe
    public function __construct()
    {
        //classes dos rotulos dos campos
        $this->rotulo = new rotulo("linhaspacto");
        $this->pagina_retorno = basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
    }

    //funcao erro
    public function erro($mostra, $retorna)
    {
        if (($this->erro_status == "0") || ($mostra == true && $this->erro_status != null)) {
            echo "<script>alert(\"" . $this->erro_msg . "\");</script>";
            if ($retorna == true) {
                echo "<script>location.href='" . $this->pagina_retorno . "'</script>";
            }
        }
    }

    // funcao para atualizar campos
    public function atualizacampos($exclusao = false)
    {
        if ($exclusao == false) {
            $this->c07_sequencial = ($this->c07_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["c07_sequencial"] : $this->c07_sequencial);
            $this->c07_titulo = ($this->c07_titulo == "" ? @$GLOBALS["HTTP_POST_VARS"]["c07_titulo"] : $this->c07_titulo);
            $this->c07_valor = ($this->c07_valor == "" ? @$GLOBALS["HTTP_POST_VARS"]["c07_valor"] : $this->c07_valor);
        } else {
            $this->c07_sequencial = ($this->c07_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["c07_sequencial"] : $this->c07_sequencial);
        }
    }

    // funcao para inclusao
    public function incluir($c07_sequencial)
    {
        $this->atualizacampos();
        if ($this->c07_titulo == null) {
            $this->erro_sql = " Campo Título não informado.";
            $this->erro_campo = "c07_titulo";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->c07_valor == null) {
            $this->erro_sql = " Campo Valor não informado.";
            $this->erro_campo = "c07_valor";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($c07_sequencial == "" || $c07_sequencial == null) {
            $result = db_query("select nextval('linhaspacto_c07_sequencial_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: linhaspacto_c07_sequencial_seq do campo: c07_sequencial";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->c07_sequencial = pg_result($result, 0, 0);
        } else {
            $result = db_query("SELECT last_value FROM linhaspacto_c07_sequencial_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $c07_sequencial)) {
                $this->erro_sql = " Campo c07_sequencial maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->c07_sequencial = $c07_sequencial;
            }
        }
        if (($this->c07_sequencial == null) || ($this->c07_sequencial == "")) {
            $this->erro_sql = " Campo c07_sequencial nao declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into linhaspacto(
                                       c07_sequencial
                                      ,c07_titulo
                                      ,c07_valor
                       )
                values (
                                $this->c07_sequencial
                               ,'$this->c07_titulo'
                               ,$this->c07_valor
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "linhaspacto ($this->c07_sequencial) nao Incluído. Inclusao Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "linhaspacto já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "linhaspacto ($this->c07_sequencial) nao Incluído. Inclusao Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : " . $this->c07_sequencial;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->c07_sequencial));
            if (($resaco != false) || ($this->numrows != 0)) {

                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                $resac = db_query("insert into db_acountkey values($acount,1009861,'$this->c07_sequencial','I')");
                $resac = db_query("insert into db_acount values($acount,1010299,1009861,'','" . AddSlashes(pg_result($resaco, 0, 'c07_sequencial')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010299,1009862,'','" . AddSlashes(pg_result($resaco, 0, 'c07_titulo')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010299,1009863,'','" . AddSlashes(pg_result($resaco, 0, 'c07_valor')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            }
        }
        return true;
    }

    // funcao para alteracao
    public function alterar($c07_sequencial = null)
    {
        $this->atualizacampos();
        $sql = " UPDATE linhaspacto SET ";
        $virgula = "";
        if (trim($this->c07_sequencial) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c07_sequencial"])) {
            $sql .= $virgula . " c07_sequencial = $this->c07_sequencial ";
            $virgula = ",";
            if (trim($this->c07_sequencial) == null) {
                $this->erro_sql = " Campo Código não informado.";
                $this->erro_campo = "c07_sequencial";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->c07_titulo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c07_titulo"])) {
            $sql .= $virgula . " c07_titulo = '$this->c07_titulo' ";
            $virgula = ",";
            if (trim($this->c07_titulo) == null) {
                $this->erro_sql = " Campo Título não informado.";
                $this->erro_campo = "c07_titulo";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->c07_valor) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c07_valor"])) {
            $sql .= $virgula . " c07_valor = $this->c07_valor ";
            $virgula = ",";
            if (trim($this->c07_valor) == null) {
                $this->erro_sql = " Campo Valor não informado.";
                $this->erro_campo = "c07_valor";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($c07_sequencial != null) {
            $sql .= " c07_sequencial = $this->c07_sequencial";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->c07_sequencial));
            if ($this->numrows > 0) {

                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,1009861,'$this->c07_sequencial','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c07_sequencial"]) || $this->c07_sequencial != "")
                        $resac = db_query("insert into db_acount values($acount,1010299,1009861,'" . AddSlashes(pg_result($resaco, $conresaco, 'c07_sequencial')) . "','$this->c07_sequencial'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c07_titulo"]) || $this->c07_titulo != "")
                        $resac = db_query("insert into db_acount values($acount,1010299,1009862,'" . AddSlashes(pg_result($resaco, $conresaco, 'c07_titulo')) . "','$this->c07_titulo'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c07_valor"]) || $this->c07_valor != "")
                        $resac = db_query("insert into db_acount values($acount,1010299,1009863,'" . AddSlashes(pg_result($resaco, $conresaco, 'c07_valor')) . "','$this->c07_valor'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "linhaspacto nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->c07_sequencial;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "linhaspacto nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->c07_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : " . $this->c07_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    // funcao para exclusao
    public function excluir($c07_sequencial = null, $dbwhere = null)
    {

        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            if ($dbwhere == null || $dbwhere == "") {

                $resaco = $this->sql_record($this->sql_query_file($c07_sequencial));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
            }
            if (($resaco != false) || ($this->numrows != 0)) {

                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,1009861,'$c07_sequencial','E')");
                    $resac = db_query("insert into db_acount values($acount,1010299,1009861,'','" . AddSlashes(pg_result($resaco, $iresaco, 'c07_sequencial')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010299,1009862,'','" . AddSlashes(pg_result($resaco, $iresaco, 'c07_titulo')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010299,1009863,'','" . AddSlashes(pg_result($resaco, $iresaco, 'c07_valor')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }
        $sql = " DELETE FROM linhaspacto
                    WHERE ";
        $sql2 = "";
        if ($dbwhere == null || $dbwhere == "") {
            if ($c07_sequencial != "") {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                }
                $sql2 .= " c07_sequencial = $c07_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "linhaspacto nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $c07_sequencial;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "linhaspacto nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $c07_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : " . $c07_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = pg_affected_rows($result);
                return true;
            }
        }
    }

    // funcao do recordset
    public function sql_record($sql)
    {
        $result = db_query($sql);
        if ($result == false) {
            $this->numrows = 0;
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Erro ao selecionar os registros.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->numrows = pg_numrows($result);
        if ($this->numrows == 0) {
            $this->erro_banco = "";
            $this->erro_sql = "Record Vazio na Tabela:linhaspacto";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    // funcao do sql
    public function sql_query($c07_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from linhaspacto ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($c07_sequencial != null) {
                $sql2 .= " where linhaspacto.c07_sequencial = $c07_sequencial ";
            }
        } else if ($dbwhere != "") {
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

    // funcao do sql
    public function sql_query_file($c07_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from linhaspacto ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($c07_sequencial != null) {
                $sql2 .= " where linhaspacto.c07_sequencial = $c07_sequencial ";
            }
        } else if ($dbwhere != "") {
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

    /**
     * @param $campos
     * @param $where
     * @param null $ordem
     * @return string
     */
    public static function getDadosLinhaPacto($campos, $where, $ordem = null)
    {

        $ano = db_getsession('DB_anousu');
        $whereData = " o162_data between '{$ano}-01-01' and '{$ano}-12-31' ";
        $sqlBusca = <<<SQLBUSCA

    select {$campos},
           sum(o156_valor) as previsto,
           sum(coalesce((select sum(o162_valor)
                         from linhapactosaldomovimentacao
                         where o162_linhapacto = o156_sequencial
                           and {$whereData}
                           and o162_tipo in (3, 4)), 0)) as movimentacoes,
           sum(coalesce((select sum(o162_valor)
                     from linhapactosaldomovimentacao
                     where o162_linhapacto = o156_sequencial
                       and {$whereData}
                       and o162_tipo in (1, 2, 5, 6)), 0)) as suplementacoes_reducoes
     from planoorcamentariolinhapacto
          inner join linhaspacto on c07_sequencial = o156_linhaspacto
          inner join orcdotacaoplanoorcamentario on o156_orcdotacaoplanoorcamentario = o155_sequencial
          inner join orcdotacao on o155_coddot = o58_coddot
                               and o155_anousu = o58_anousu
          inner join orcprojativ on o55_projativ = o58_projativ
                                and o55_anousu = o58_anousu
          inner join orcunidade  on o41_orgao   = o58_orgao
                                and o41_unidade = o58_unidade
                                and o41_anousu  = o58_anousu
    where {$where}
    group by {$campos}


SQLBUSCA;

        if (!empty($ordem)) {
            $sqlBusca .= " order by {$ordem} ";
        }


        return $sqlBusca;
    }


    public static function getLinhasDePactoPorFiltro($parametro) {


        $camposObrigatorios = array();
        if (empty($parametro->data_inicial)) {
            $camposObrigatorios[] = 'data inicial';
        }

        if (empty($parametro->data_final)) {
            $camposObrigatorios[] = 'data final';
        }

        if (!empty($camposObrigatorios)) {
            $ligacao = count($camposObrigatorios) === 1 ? 'é' : 'são';
            $mensagem = "Campo(s) ".implode(', ', $camposObrigatorios)." {$ligacao} de preenchimento obrigatório.";
            throw new Exception($mensagem);
        }

        $dataInicial = new DBDate($parametro->data_inicial);
        $dataFinal   = new DBDate($parametro->data_final);
        if ($dataInicial->getTimeStamp() > $dataFinal->getTimeStamp()) {
            throw new Exception("Data inicial é maior que a data final, verifique.");
        }

        $whereData = " o162_data between '{$dataInicial->getDate()}' and '{$dataFinal->getDate()}' ";

        $buscaInformacoes = <<<SQLBUSCA
select o58_coddot as codigo_dotacao,
       fc_estruturaldotacao(o58_anousu, o58_coddot) as estrutural_dotacao,
       o155_titulo as descricao_plano,
       c07_titulo as descricao_linha,
       o156_valor as previsto,
       abs(coalesce((select sum(o162_valor)
                     from linhapactosaldomovimentacao
                     where o162_linhapacto = o156_sequencial
                       and {$whereData}
                       and o162_tipo in (3, 4)), 0)) as movimentacoes,
       coalesce((select sum(o162_valor)
                 from linhapactosaldomovimentacao
                 where o162_linhapacto = o156_sequencial
                   and {$whereData}
                   and o162_tipo in (1, 2, 5, 6)), 0) as suplementacoes_reducoes,
      orcdotacao.o58_projativ as acao,
      linhaspacto.c07_sequencial as linha_pacto
 from planoorcamentariolinhapacto
      inner join linhaspacto on c07_sequencial = o156_linhaspacto
      inner join orcdotacaoplanoorcamentario on o156_orcdotacaoplanoorcamentario = o155_sequencial
      inner join orcdotacao on o155_coddot = o58_coddot
                           and o155_anousu = o58_anousu
where o58_anousu = {$parametro->ano}
  and o58_instit = {$parametro->instituicao}
SQLBUSCA;


        if (!empty($parametro->acao)) {
            $buscaInformacoes .= " and o58_projativ = {$parametro->acao} ";
        }

        if (!empty($parametro->programa)) {
            $buscaInformacoes .= " and o58_programa = {$parametro->programa} ";
        }
        if (!empty($parametro->dotacao)) {
            $buscaInformacoes .= " and o58_coddot = {$parametro->dotacao} ";
        }

        if (!empty($parametro->linha_pacto)) {
            $buscaInformacoes .= " and c07_sequencial = {$parametro->linha_pacto} ";
        }

        $buscaInformacoes .= " order by o58_coddot, o155_sequencial, c07_sequencial limit 10";
        $buscaDotacoes = db_query($buscaInformacoes);
        if (!$buscaDotacoes) {
            throw new Exception("Ocorreu um erro ao executar a consulta das informações de saldo do Plano Orçamentário.");
        }

        $totalRegistros = pg_num_rows($buscaDotacoes);
        if ($totalRegistros === 0) {
            throw new Exception("Nenhum registro encontrado para o filtro informado.");
        }

        $retorno->planos_orcamentarios = array();
        for ($row = 0; $row < $totalRegistros; $row++) {

            $stdInformacao = db_utils::fieldsMemory($buscaDotacoes, $row);

            $dados = (object)array(
                'codigo_dotacao' => $stdInformacao->codigo_dotacao,
                'estrutural_dotacao' => $stdInformacao->estrutural_dotacao,
                'descricao_plano' => $stdInformacao->descricao_plano,
                'descricao_linha' => $stdInformacao->descricao_linha,
                'valor_previsto' => $stdInformacao->previsto,
                'valor_alterado_remanejado' => $stdInformacao->suplementacoes_reducoes,
                'valor_realizado' => $stdInformacao->movimentacoes,
                'saldo_final' => (($stdInformacao->previsto + $stdInformacao->suplementacoes_reducoes) - $stdInformacao->movimentacoes),
                'acao' => $stdInformacao->acao,
                'linha_pacto' => $stdInformacao->linha_pacto,
            );
            $retorno->planos_orcamentarios[] = $dados;
        }

    }
}

?>
