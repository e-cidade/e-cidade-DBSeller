<?php

class cl_lotelancamentos
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
    public $c160_codigo = 0;
    public $c160_exercicio = 0;
    public $c160_instituicao = 0;
    public $c160_lote = null;
    public $created_at_dia = null;
    public $created_at_mes = null;
    public $created_at_ano = null;
    public $created_at = null;
    public $created_at_hora = '00:00';
    public $updated_at_dia = null;
    public $updated_at_mes = null;
    public $updated_at_ano = null;
    public $updated_at = null;
    public $updated_at_hora = '00:00';
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 c160_codigo = int8 =
                 c160_exercicio = int4 =
                 c160_instituicao = int4 =
                 c160_lote = varchar(20) =
                 created_at = timestamp = Criado em
                 updated_at = timestamp = Alterado em
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("lotelancamentos");
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
            $this->c160_codigo = ($this->c160_codigo == "" ? @$GLOBALS["HTTP_POST_VARS"]["c160_codigo"] : $this->c160_codigo);
            $this->c160_exercicio = ($this->c160_exercicio == "" ? @$GLOBALS["HTTP_POST_VARS"]["c160_exercicio"] : $this->c160_exercicio);
            $this->c160_instituicao = ($this->c160_instituicao == "" ? @$GLOBALS["HTTP_POST_VARS"]["c160_instituicao"] : $this->c160_instituicao);
            $this->c160_lote = ($this->c160_lote == "" ? @$GLOBALS["HTTP_POST_VARS"]["c160_lote"] : $this->c160_lote);
        } else {
            $this->c160_codigo = ($this->c160_codigo == "" ? @$GLOBALS["HTTP_POST_VARS"]["c160_codigo"] : $this->c160_codigo);
        }

        $data = new \DateTime();
        if (empty($this->created_at)) {
            $this->created_at = $data->format('Y-m-d H:i:s');
        }
        if (empty($this->updated_at)) {
            $this->created_at = $data->format('Y-m-d H:i:s');
        }
    }

    public function incluir($c160_codigo)
    {
        $this->atualizacampos();
        if ($this->c160_exercicio == null) {
            $this->erro_sql = " Campo  não informado.";
            $this->erro_campo = "c160_exercicio";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->c160_instituicao == null) {
            $this->erro_sql = " Campo  não informado.";
            $this->erro_campo = "c160_instituicao";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->c160_lote == null) {
            $this->erro_sql = " Campo  não informado.";
            $this->erro_campo = "c160_lote";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($c160_codigo == "" || $c160_codigo == null) {
            $result = db_query("select nextval('lotelancamentos_c160_codigo_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: lotelancamentos_c160_codigo_seq do campo: c160_codigo";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->c160_codigo = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from lotelancamentos_c160_codigo_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $c160_codigo)) {
                $this->erro_sql = " Campo c160_codigo maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->c160_codigo = $c160_codigo;
            }
        }
        if (($this->c160_codigo == null) || ($this->c160_codigo == "")) {
            $this->erro_sql = " Campo c160_codigo não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }

        $sql = sprintf(
            "insert into lotelancamentos(c160_codigo,c160_exercicio,c160_instituicao,c160_lote,created_at,updated_at) values (%s, %s,%s,'%s','%s','%s')",
            $this->c160_codigo
            ,$this->c160_exercicio
            ,$this->c160_instituicao
            ,$this->c160_lote
            ,$this->created_at
            ,$this->updated_at
        );

        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "Lote de lan?amentos ($this->c160_codigo) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "Lote de lan?amentos já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "Lote de lan?amentos ($this->c160_codigo) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->c160_codigo;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);

        return true;
    }

    public function alterar($c160_codigo = null)
    {
        $this->atualizacampos();
        $sql = " update lotelancamentos set ";
        $virgula = "";
        if (trim($this->c160_codigo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c160_codigo"])) {
            $sql .= $virgula . " c160_codigo = $this->c160_codigo ";
            $virgula = ",";
            if (trim($this->c160_codigo) == null) {
                $this->erro_sql = " Campo  não informado.";
                $this->erro_campo = "c160_codigo";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->c160_exercicio) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c160_exercicio"])) {
            $sql .= $virgula . " c160_exercicio = $this->c160_exercicio ";
            $virgula = ",";
            if (trim($this->c160_exercicio) == null) {
                $this->erro_sql = " Campo  não informado.";
                $this->erro_campo = "c160_exercicio";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->c160_instituicao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c160_instituicao"])) {
            $sql .= $virgula . " c160_instituicao = $this->c160_instituicao ";
            $virgula = ",";
            if (trim($this->c160_instituicao) == null) {
                $this->erro_sql = " Campo  não informado.";
                $this->erro_campo = "c160_instituicao";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->c160_lote) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c160_lote"])) {
            $sql .= $virgula . " c160_lote = '$this->c160_lote' ";
            $virgula = ",";
            if (trim($this->c160_lote) == null) {
                $this->erro_sql = " Campo  não informado.";
                $this->erro_campo = "c160_lote";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->created_at) != "" || isset($GLOBALS["HTTP_POST_VARS"]["created_at_dia"]) && ($GLOBALS["HTTP_POST_VARS"]["created_at_dia"] != "")) {
            $sql .= $virgula . " created_at = '$this->created_at' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["created_at_dia"])) {
                $sql .= $virgula . " created_at = null ";
                $virgula = ",";
            }
        }
        if (trim($this->updated_at) != "" || isset($GLOBALS["HTTP_POST_VARS"]["updated_at_dia"]) && ($GLOBALS["HTTP_POST_VARS"]["updated_at_dia"] != "")) {
            $sql .= $virgula . " updated_at = '$this->updated_at' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["updated_at_dia"])) {
                $sql .= $virgula . " updated_at = null ";
                $virgula = ",";
            }
        }
        $sql .= " where ";
        if ($c160_codigo != null) {
            $sql .= " c160_codigo = $this->c160_codigo";
        }

        $result = db_query($sql);

        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Lote de lan?amentos não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->c160_codigo;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Lote de lan?amentos não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->c160_codigo;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $this->c160_codigo;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($c160_codigo = null, $dbwhere = null)
    {
        $sql = " delete from lotelancamentos where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($c160_codigo)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " c160_codigo = $c160_codigo ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Lote de lan?amentos não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $c160_codigo;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Lote de lan?amentos não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $c160_codigo;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $c160_codigo;
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
            $this->erro_sql = "Record Vazio na Tabela:lotelancamentos";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($c160_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos}";
        $sql .= "  from lotelancamentos ";
        $sql .= "      inner join db_config  on  db_config.codigo = lotelancamentos.c160_instituicao";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
        $sql .= "      left  join db_depart  on  db_depart.coddepto = db_config.db21_departamento";
        $sql .= "      inner join db_tipoinstit  on  db_tipoinstit.db21_codtipo = db_config.db21_tipoinstit";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($c160_codigo)) {
                $sql2 .= " where lotelancamentos.c160_codigo = $c160_codigo ";
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

    public function sql_query_file($c160_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos} ";
        $sql .= "  from lotelancamentos ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($c160_codigo)) {
                $sql2 .= " where lotelancamentos.c160_codigo = $c160_codigo ";
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
