<?php

class cl_orctiporec
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
    public $o15_codigo = 0;
    public $o15_descr = null;
    public $o15_codtri = null;
    public $o15_finali = null;
    public $o15_tipo = 0;
    public $o15_datalimite_dia = null;
    public $o15_datalimite_mes = null;
    public $o15_datalimite_ano = null;
    public $o15_datalimite = null;
    public $o15_db_estruturavalor = 0;
    public $o15_codigosiconfi = null;
    public $o15_loaidentificadoruso = 0;
    public $o15_loatipo = 0;
    public $o15_loagrupo = 0;
    public $o15_loaespecificacao = null;
    public $o15_complemento = 0;
    public $o15_recurso = null;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 o15_codigo = int4 = Código
                 o15_descr = varchar(60) = Descrição do Recurso
                 o15_codtri = varchar(10) = Código Tribunal
                 o15_finali = text = Finalidade do Recurso
                 o15_tipo = int4 = Tipo do Recurso
                 o15_datalimite = date = Data Limite
                 o15_db_estruturavalor = int4 = Código da Estrutura
                 o15_codigosiconfi = varchar(5) = Código do SICONFI
                 o15_loaidentificadoruso = int4 = Identificador de Uso
                 o15_loatipo = int4 = Tipo
                 o15_loagrupo = int4 = Grupo
                 o15_loaespecificacao = varchar(2) = Especificação
                 o15_complemento = int4 = Complemento
                 o15_recurso = varchar(10) = Recurso
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("orctiporec");
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
            $this->o15_codigo = ($this->o15_codigo == "" ? @$GLOBALS["HTTP_POST_VARS"]["o15_codigo"] : $this->o15_codigo);
            $this->o15_descr = ($this->o15_descr == "" ? @$GLOBALS["HTTP_POST_VARS"]["o15_descr"] : $this->o15_descr);
            $this->o15_codtri = ($this->o15_codtri == "" ? @$GLOBALS["HTTP_POST_VARS"]["o15_codtri"] : $this->o15_codtri);
            $this->o15_finali = ($this->o15_finali == "" ? @$GLOBALS["HTTP_POST_VARS"]["o15_finali"] : $this->o15_finali);
            $this->o15_tipo = ($this->o15_tipo == "" ? @$GLOBALS["HTTP_POST_VARS"]["o15_tipo"] : $this->o15_tipo);
            if ($this->o15_datalimite == "") {
                $this->o15_datalimite_dia = ($this->o15_datalimite_dia == "" ? @$GLOBALS["HTTP_POST_VARS"]["o15_datalimite_dia"] : $this->o15_datalimite_dia);
                $this->o15_datalimite_mes = ($this->o15_datalimite_mes == "" ? @$GLOBALS["HTTP_POST_VARS"]["o15_datalimite_mes"] : $this->o15_datalimite_mes);
                $this->o15_datalimite_ano = ($this->o15_datalimite_ano == "" ? @$GLOBALS["HTTP_POST_VARS"]["o15_datalimite_ano"] : $this->o15_datalimite_ano);
                if ($this->o15_datalimite_dia != "") {
                    $this->o15_datalimite = $this->o15_datalimite_ano . "-" . $this->o15_datalimite_mes . "-" . $this->o15_datalimite_dia;
                }
            }
            $this->o15_db_estruturavalor = ($this->o15_db_estruturavalor == "" ? @$GLOBALS["HTTP_POST_VARS"]["o15_db_estruturavalor"] : $this->o15_db_estruturavalor);
            $this->o15_codigosiconfi = ($this->o15_codigosiconfi == "" ? @$GLOBALS["HTTP_POST_VARS"]["o15_codigosiconfi"] : $this->o15_codigosiconfi);
            $this->o15_loaidentificadoruso = ($this->o15_loaidentificadoruso === "" ? @$GLOBALS["HTTP_POST_VARS"]["o15_loaidentificadoruso"] : $this->o15_loaidentificadoruso);
            $this->o15_loatipo = ($this->o15_loatipo === "" ? @$GLOBALS["HTTP_POST_VARS"]["o15_loatipo"] : $this->o15_loatipo);
            $this->o15_loagrupo = ($this->o15_loagrupo === "" ? @$GLOBALS["HTTP_POST_VARS"]["o15_loagrupo"] : $this->o15_loagrupo);
            $this->o15_loaespecificacao = ($this->o15_loaespecificacao === "" ? @$GLOBALS["HTTP_POST_VARS"]["o15_loaespecificacao"] : $this->o15_loaespecificacao);
            $this->o15_complemento = ($this->o15_complemento === "" ? @$GLOBALS["HTTP_POST_VARS"]["o15_complemento"] : $this->o15_complemento);
            $this->o15_recurso = ($this->o15_recurso == "" ? @$GLOBALS["HTTP_POST_VARS"]["o15_recurso"] : $this->o15_recurso);
        } else {
            $this->o15_codigo = ($this->o15_codigo == "" ? @$GLOBALS["HTTP_POST_VARS"]["o15_codigo"] : $this->o15_codigo);
        }
    }

    public function incluir($o15_codigo)
    {
        $this->atualizacampos();

        if ($this->o15_descr == null) {
            $this->erro_sql = " Campo Descrição do Recurso não informado.";
            $this->erro_campo = "o15_descr";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o15_codtri == null) {
            $this->erro_sql = " Campo Código Tribunal não informado.";
            $this->erro_campo = "o15_codtri";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o15_finali == null) {
            $this->erro_sql = " Campo Finalidade do Recurso não informado.";
            $this->erro_campo = "o15_finali";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o15_tipo == null) {
            $this->erro_sql = " Campo Tipo do Recurso não informado.";
            $this->erro_campo = "o15_tipo";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o15_datalimite == null) {
            $this->o15_datalimite = "null";
        }
        if ($this->o15_db_estruturavalor == null) {
            $this->erro_sql = " Campo Código da Estrutura não informado.";
            $this->erro_campo = "o15_db_estruturavalor";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o15_loaidentificadoruso === '' or is_null($this->o15_loaidentificadoruso)) {
            $this->o15_loaidentificadoruso = "null";
        }
        if ($this->o15_loatipo === '' or is_null($this->o15_loatipo)) {
            $this->o15_loatipo = "null";
        }
        if ($this->o15_loagrupo === '' or is_null($this->o15_loagrupo)) {
            $this->o15_loagrupo = "null";
        }
        if ($this->o15_loaespecificacao === '' or is_null($this->o15_loaespecificacao)) {
            $this->o15_loaespecificacao = "null";
        }

        if ($o15_codigo == "" || $o15_codigo == null) {
            $result = db_query("select nextval('orctiporec_o15_codigo_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: orctiporec_o15_codigo_seq do campo: o15_codigo";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->o15_codigo = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from orctiporec_o15_codigo_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $o15_codigo)) {
                $this->erro_sql = " Campo o15_codigo maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->o15_codigo = $o15_codigo;
            }
        }
        if (($this->o15_codigo == null) || ($this->o15_codigo == "")) {
            $this->erro_sql = " Campo o15_codigo não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }

        $sql = "
        insert into orctiporec(
               o15_codigo
              ,o15_descr
              ,o15_codtri
              ,o15_finali
              ,o15_tipo
              ,o15_datalimite
              ,o15_db_estruturavalor
              ,o15_codigosiconfi
              ,o15_loaidentificadoruso
              ,o15_loatipo
              ,o15_loagrupo
              ,o15_loaespecificacao
              ,o15_complemento
              ,o15_recurso
              )
              values (
                $this->o15_codigo
               ,'$this->o15_descr'
               ,'$this->o15_codtri'
               ,'$this->o15_finali'
               ,$this->o15_tipo
               ," . ($this->o15_datalimite == "null" || $this->o15_datalimite == "" ? "null" : "'" . $this->o15_datalimite . "'") . "
               ,$this->o15_db_estruturavalor
               ,'$this->o15_codigosiconfi'
               ,$this->o15_loaidentificadoruso
               ,$this->o15_loatipo
               ,$this->o15_loagrupo
               ,'$this->o15_loaespecificacao'
               ,$this->o15_complemento
               ,'$this->o15_recurso'
        )";

        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "Tipos de Recursos ($this->o15_codigo) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "Tipos de Recursos já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "Tipos de Recursos ($this->o15_codigo) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->o15_codigo;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);

        return true;
    }

    public function alterar($o15_codigo = null)
    {
        $this->atualizacampos();
        $sql = " update orctiporec set ";
        $virgula = "";
        if (trim($this->o15_codigo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["o15_codigo"])) {
            $sql .= $virgula . " o15_codigo = $this->o15_codigo ";
            $virgula = ",";
            if (trim($this->o15_codigo) == null) {
                $this->erro_sql = " Campo Código não informado.";
                $this->erro_campo = "o15_codigo";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o15_descr) != "" || isset($GLOBALS["HTTP_POST_VARS"]["o15_descr"])) {
            $sql .= $virgula . " o15_descr = '$this->o15_descr' ";
            $virgula = ",";
            if (trim($this->o15_descr) == null) {
                $this->erro_sql = " Campo Descrição do Recurso não informado.";
                $this->erro_campo = "o15_descr";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o15_codtri) != "" || isset($GLOBALS["HTTP_POST_VARS"]["o15_codtri"])) {
            $sql .= $virgula . " o15_codtri = '$this->o15_codtri' ";
            $virgula = ",";
            if (trim($this->o15_codtri) == null) {
                $this->erro_sql = " Campo Código Tribunal não informado.";
                $this->erro_campo = "o15_codtri";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o15_finali) != "" || isset($GLOBALS["HTTP_POST_VARS"]["o15_finali"])) {
            $sql .= $virgula . " o15_finali = '$this->o15_finali' ";
            $virgula = ",";
            if (trim($this->o15_finali) == null) {
                $this->erro_sql = " Campo Finalidade do Recurso não informado.";
                $this->erro_campo = "o15_finali";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o15_tipo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["o15_tipo"])) {
            $sql .= $virgula . " o15_tipo = $this->o15_tipo ";
            $virgula = ",";
            if (trim($this->o15_tipo) == null) {
                $this->erro_sql = " Campo Tipo do Recurso não informado.";
                $this->erro_campo = "o15_tipo";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }

        if (!empty($this->o15_datalimite)) {
            $sql .= $virgula . " o15_datalimite = '$this->o15_datalimite' ";
            $virgula = ",";
        } else {
            $sql .= $virgula . " o15_datalimite = null ";
            $virgula = ",";
        }

        if (trim($this->o15_db_estruturavalor) != "" || isset($GLOBALS["HTTP_POST_VARS"]["o15_db_estruturavalor"])) {
            $sql .= $virgula . " o15_db_estruturavalor = $this->o15_db_estruturavalor ";
            $virgula = ",";
            if (trim($this->o15_db_estruturavalor) == null) {
                $this->erro_sql = " Campo Código da Estrutura não informado.";
                $this->erro_campo = "o15_db_estruturavalor";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o15_codigosiconfi) != "" || isset($GLOBALS["HTTP_POST_VARS"]["o15_codigosiconfi"])) {
            $sql .= $virgula . " o15_codigosiconfi = '$this->o15_codigosiconfi' ";
            $virgula = ",";
        }

        if ($this->o15_loaidentificadoruso === '' or is_null($this->o15_loaidentificadoruso)) {
            $this->o15_loaidentificadoruso = "null";
        }
        $sql .= $virgula . " o15_loaidentificadoruso = $this->o15_loaidentificadoruso ";
        $virgula = ",";

        if ($this->o15_loatipo === '' or is_null($this->o15_loatipo)) {
            $this->o15_loatipo = "null";
        }
        $sql .= $virgula . " o15_loatipo = $this->o15_loatipo ";
        $virgula = ",";

        if ($this->o15_loagrupo === '' or is_null($this->o15_loagrupo)) {
            $this->o15_loagrupo = "null";
        }
        $sql .= $virgula . " o15_loagrupo = $this->o15_loagrupo ";
        $virgula = ",";

        if (trim($this->o15_loaespecificacao) === "" or is_null($this->o15_loaespecificacao)) {
            $this->o15_loaespecificacao = "null";
        }
        $sql .= $virgula . " o15_loaespecificacao = '$this->o15_loaespecificacao' ";
        if (trim($this->o15_complemento) != "" || isset($GLOBALS["HTTP_POST_VARS"]["o15_complemento"])) {
            if (trim($this->o15_complemento) == "" && isset($GLOBALS["HTTP_POST_VARS"]["o15_complemento"])) {
                $this->o15_complemento = "0";
            }
            $sql .= $virgula . " o15_complemento = $this->o15_complemento ";
            $virgula = ",";
        }

        if (trim($this->o15_recurso) != "" || isset($GLOBALS["HTTP_POST_VARS"]["o15_recurso"])) {
            $sql .= $virgula . " o15_recurso = '$this->o15_recurso' ";
            $virgula = ",";
        }
        $sql .= " where ";
        if ($o15_codigo != null) {
            $sql .= " o15_codigo = $this->o15_codigo";
        }

        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Tipos de Recursos não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->o15_codigo;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Tipos de Recursos não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->o15_codigo;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $this->o15_codigo;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($o15_codigo = null, $dbwhere = null)
    {
        $sql = " delete from orctiporec where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($o15_codigo)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " o15_codigo = $o15_codigo ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Tipos de Recursos não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $o15_codigo;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Tipos de Recursos não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $o15_codigo;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $o15_codigo;
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
            $this->erro_sql = "Record Vazio na Tabela:orctiporec";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }

        return $result;
    }

    public function sql_query($o15_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos}";
        $sql .= "  from orctiporec ";
        $sql .= "      left join db_estruturavalor  on  db_estruturavalor.db121_sequencial = orctiporec.o15_db_estruturavalor";
        $sql .= "      left join db_estrutura  on  db_estrutura.db77_codestrut = db_estruturavalor.db121_db_estrutura";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($o15_codigo)) {
                $sql2 .= " where orctiporec.o15_codigo = $o15_codigo ";
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

    public function sql_query_file($o15_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos} ";
        $sql .= "  from orctiporec ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($o15_codigo)) {
                $sql2 .= " where orctiporec.o15_codigo = $o15_codigo ";
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

    public function sql_queryComplemento($o15_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos}";
        $sql .= "  from orctiporec ";
        $sql .= "      left join db_estruturavalor  on  db_estruturavalor.db121_sequencial = orctiporec.o15_db_estruturavalor";
        $sql .= "      left join db_estrutura  on  db_estrutura.db77_codestrut = db_estruturavalor.db121_db_estrutura";
        $sql .= "      left join complementofonterecurso on o15_complemento = o200_sequencial";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($o15_codigo)) {
                $sql2 .= " where orctiporec.o15_codigo = $o15_codigo ";
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

    function sql_query_convenios($sDataini, $sDataFim = null, $campos = "*", $ordem = "o15_codigo", $dbwhere = "")
    {
        if (empty($sDataini)) {
            return false;
        }
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
        $sql .= " from orctiporec ";
        $sql .= "      inner join orctiporecconvenio   on o15_codigo = o16_orctiporec";
        $sql2 = " where ";
        if ($sDataFim == null) {

            $sql2 .= "(('{$sDataini}' between o16_dtvigenciaini and o16_dtvigenciafim) or ";
            $sql2 .= "('{$sDataini}' between o16_dtprorrogacaoini and o16_dtprorrogacaofim))";

        } else {

            $sql2 .= "((o16_dtvigenciaini <= '{$sDataini}' and o16_dtvigenciafim >= '{$sDataFim}') or ";
            $sql2 .= "(o16_dtprorrogacaoini <= '{$sDataini}' and o16_dtprorrogacaofim >= '{$sDataFim}'))";

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

    function sql_query_conveniosGestao($sDataini, $sDataFim = null, $campos = "*", $ordem = "o15_codigo", $dbwhere = "")
    {
        if (empty($sDataini)) {
            return false;
        }
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
        $sql .= " from orctiporec ";
        $sql .= "      inner join orctiporecconvenio   on o15_codigo = o16_orctiporec";
        $sql .= "  join fonterecurso on orctiporec_id = o15_codigo ";
        $sql .= "  join complementofonterecurso on o200_sequencial = o15_complemento ";
        $sql2 = " where ";
        if ($sDataFim == null) {

            $sql2 .= "(('{$sDataini}' between o16_dtvigenciaini and o16_dtvigenciafim) or ";
            $sql2 .= "('{$sDataini}' between o16_dtprorrogacaoini and o16_dtprorrogacaofim))";
            $sql2 .= " and exercicio = " . db_getsession("DB_anousu");

        } else {

            $sql2 .= "((o16_dtvigenciaini <= '{$sDataini}' and o16_dtvigenciafim >= '{$sDataFim}') or ";
            $sql2 .= "(o16_dtprorrogacaoini <= '{$sDataini}' and o16_dtprorrogacaofim >= '{$sDataFim}'))";

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

    function sql_query_emp($o15_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from orctiporec ";
        $sql .= "   inner join orcdotacao on o58_codigo = o15_codigo ";
        $sql .= "   inner join empempenho on e60_coddot = o58_coddot and e60_anousu=o58_anousu";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($o15_codigo != null) {
                $sql2 .= " where orctiporec.o15_codigo = $o15_codigo ";
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

    function sql_query_orcamento($campos = "*", $ordem = null, $dbwhere = "")
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

        $sql_campos = $sql;

        $sql .= " from orctiporec ";
        $sql .= "   inner join orcdotacao on o58_codigo = o15_codigo ";
        $sql .= "   where o58_anousu = " . db_getsession("DB_anousu") . " and o58_instit = " . db_getsession("DB_instit");
        $sql .= " union $sql_campos ";
        $sql .= " from orctiporec ";
        $sql .= "   inner join orcreceita on o70_codigo = o15_codigo ";
        $sql .= "   where o70_anousu = " . db_getsession("DB_anousu") . " and o70_instit = " . db_getsession("DB_instit");

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
     * @param string $sCampos
     * @param null $sWhere
     *
     * @return string
     */
    public function sql_recurso_despesa($sCampos = "*", $sWhere = null)
    {
        $sSql = " select {$sCampos} ";
        $sSql .= "   from orctiporec ";
        $sSql .= "        inner join orcdotacao on orcdotacao.o58_codigo = orctiporec.o15_codigo ";

        if (!empty($sWhere)) {
            $sSql .= " where = {$sWhere} ";
        }

        return $sSql;
    }

    public function sql_query_contacorrentedetalhe($sCampos = "*", $sWhere = null)
    {
        $sSql = " select {$sCampos} ";
        $sSql .= " from orctiporec ";
        $sSql .= "    inner join contacorrentedetalhe on c19_orctiporec = o15_codigo ";

        if (!empty($sWhere)) {
            $sSql .= " where {$sWhere} ";
        }

        return $sSql;
    }

    public function sql_query_complemento($o15_codigo = null, $o15_loaespecificacao = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos}";
        $sql .= "  from orctiporec ";
        $sql .= "      left join complementofonterecurso on o15_complemento = o200_sequencial ";
        $sql .= "      left join orcdotacao on o15_codigo = o58_codigo ";
        $sql2 = "";
        if (empty($dbwhere)) {
            $dbwhere = " where 1=1 ";
            if (!empty($o15_codigo)) {
                $sql2 .= " and orctiporec.o15_codigo = $o15_codigo ";
            }
            if (!empty($o15_loaespecificacao)) {
                $sql2 .= " and orctiporec.o15_loaspecificacao = '{$o15_loaespecificacao}'";
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

    public function sql_query_especificacao_sem_complemento($o15_codigo = null, $o15_loaespecificacao = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos}";
        $sql .= "  from orctiporec ";
        $sql .= "      left join orcdotacao on o15_codigo = o58_codigo ";
        $sql2 = "";
        if (empty($dbwhere)) {
            $dbwhere = " where 1=1 ";
            if (!empty($o15_codigo)) {
                $sql2 .= " and orctiporec.o15_codigo = $o15_codigo ";
            }
            if (!empty($o15_loaespecificacao)) {
                $sql2 .= " and orctiporec.o15_loaspecificacao = '{$o15_loaespecificacao}'";
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

    public function sql_recurso_receita($campos = ["*"], $ordem = null, $where = [])
    {
        $campos = implode(', ', $campos);

        if (empty($where)) {
            $where = ["o70_anousu = " . db_getsession('DB_anousu')];
        }
        $where = implode(' and ', $where);

        $sql = "
            select {$campos}
             from orctiporec
                  join orcreceita on o70_codigo = o15_codigo
            where {$where}
        ";

        if (!empty($ordem)) {
            $sql .= "order by {$ordem}";
        }

        return $sql;
    }

    public function sql_query_fonte_recurso($o15_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos} ";
        $sql .= "  from orctiporec ";
        $sql .= "  join complementofonterecurso on o15_complemento = o200_sequencial ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($ed161_codigo)) {
                $sql2 .= " where orctiporec.o15_codigo = $o15_codigo ";
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

    /**
     * @param $o15_codigo
     * @param $campos
     * @param $ordem
     * @param $dbwhere
     * @return string
     */
    public function sqlNovaFonteRecurso($o15_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos} ";
        $sql .= "  from orctiporec ";
        $sql .= "  join fonterecurso on orctiporec_id = o15_codigo ";
        $sql .= "  join complementofonterecurso on o200_sequencial = o15_complemento ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($ed161_codigo)) {
                $sql2 .= " where orctiporec.o15_codigo = $o15_codigo ";
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
