<?php

class cl_fonterecurso
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
    public $orctiporec_id = 0;
    public $exercicio = 0;
    public $codigo_siconfi = null;
    public $gestao = null;
    public $classificacaofr_id = 0;
    public $tipo_detalhamento = null;
    public $descricao = null;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
         id = int8 = id
         orctiporec_id = int4 = Código Recurso
         exercicio = int4 = Exercício
         codigo_siconfi = varchar(15) = Código Siconf
         gestao = varchar(5) = Fonte Recurso
         classificacaofr_id = int4 = Classificação
         tipo_detalhamento = char(2) = Detalhamento
         descricao = text = Descrição
    ";

    public function __construct()
    {
        $this->rotulo = new rotulo("fonterecurso");
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
            $this->orctiporec_id = ($this->orctiporec_id == "" ? @$GLOBALS["HTTP_POST_VARS"]["orctiporec_id"] : $this->orctiporec_id);
            $this->exercicio = ($this->exercicio == "" ? @$GLOBALS["HTTP_POST_VARS"]["exercicio"] : $this->exercicio);
            $this->codigo_siconfi = ($this->codigo_siconfi == "" ? @$GLOBALS["HTTP_POST_VARS"]["codigo_siconfi"] : $this->codigo_siconfi);
            $this->gestao = ($this->gestao == "" ? @$GLOBALS["HTTP_POST_VARS"]["gestao"] : $this->gestao);
            $this->classificacaofr_id = ($this->classificacaofr_id == "" ? @$GLOBALS["HTTP_POST_VARS"]["classificacaofr_id"] : $this->classificacaofr_id);
            $this->tipo_detalhamento = ($this->tipo_detalhamento == "" ? @$GLOBALS["HTTP_POST_VARS"]["tipo_detalhamento"] : $this->tipo_detalhamento);
            $this->descricao = ($this->descricao == "" ? @$GLOBALS["HTTP_POST_VARS"]["descricao"] : $this->descricao);
        } else {
            $this->id = ($this->id == "" ? @$GLOBALS["HTTP_POST_VARS"]["id"] : $this->id);
        }
    }

    public function incluir($id)
    {
        $this->atualizacampos();
        if ($this->orctiporec_id == null) {
            $this->erro_sql = " Campo Código Recurso não informado.";
            $this->erro_campo = "orctiporec_id";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->exercicio == null) {
            $this->exercicio = "0";
        }
        if ($this->codigo_siconfi == null) {
            $this->codigo_siconfi = '';
        }
        if ($this->gestao == null) {
            $this->erro_sql = " Campo Fonte Recurso não informado.";
            $this->erro_campo = "gestao";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->classificacaofr_id == null) {
            $this->erro_sql = " Campo Classificação não informado.";
            $this->erro_campo = "classificacaofr_id";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->tipo_detalhamento == null) {
            $this->erro_sql = " Campo Detalhamento não informado.";
            $this->erro_campo = "tipo_detalhamento";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->descricao == null) {
            $this->erro_sql = " Campo Descrição não informado.";
            $this->erro_campo = "descricao";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($id == "" || $id == null) {
            $result = db_query("select nextval('fonterecurso_id_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: fonterecurso_id_seq do campo: id";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->id = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from fonterecurso_id_seq");
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
        $sql = "insert into fonterecurso(
                       id
                      ,orctiporec_id
                      ,exercicio
                      ,codigo_siconfi
                      ,gestao
                      ,classificacaofr_id
                      ,tipo_detalhamento
                      ,descricao
                )
                values (
                        $this->id
                       ,$this->orctiporec_id
                       ,$this->exercicio
                       ,'$this->codigo_siconfi'
                       ,'$this->gestao'
                       ,$this->classificacaofr_id
                       ,'$this->tipo_detalhamento'
                       ,'$this->descricao'
                )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "Fonte de Recurso ($this->id) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "Fonte de Recurso já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "Fonte de Recurso ($this->id) não Incluído. Inclusão Abortada.";
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

        return true;
    }

    public function alterar($id = null)
    {
        $this->atualizacampos();
        $sql = " update fonterecurso set ";
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
        if (trim($this->orctiporec_id) != "" || isset($GLOBALS["HTTP_POST_VARS"]["orctiporec_id"])) {
            $sql .= $virgula . " orctiporec_id = $this->orctiporec_id ";
            $virgula = ",";
            if (trim($this->orctiporec_id) == null) {
                $this->erro_sql = " Campo Código Recurso não informado.";
                $this->erro_campo = "orctiporec_id";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->exercicio) != "" || isset($GLOBALS["HTTP_POST_VARS"]["exercicio"])) {
            if (trim($this->exercicio) == "" && isset($GLOBALS["HTTP_POST_VARS"]["exercicio"])) {
                $this->exercicio = "0";
            }
            $sql .= $virgula . " exercicio = $this->exercicio ";
            $virgula = ",";
        }
        if (trim($this->codigo_siconfi) != "" || isset($GLOBALS["HTTP_POST_VARS"]["codigo_siconfi"])) {
            $sql .= $virgula . " codigo_siconfi = '$this->codigo_siconfi' ";
            $virgula = ",";
            if (trim($this->codigo_siconfi) == null) {
                $this->erro_sql = " Campo Código Siconf não informado.";
                $this->erro_campo = "codigo_siconfi";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->gestao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["gestao"])) {
            $sql .= $virgula . " gestao = '$this->gestao' ";
            $virgula = ",";
            if (trim($this->gestao) == null) {
                $this->erro_sql = " Campo Fonte Recurso não informado.";
                $this->erro_campo = "gestao";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->classificacaofr_id) != "" || isset($GLOBALS["HTTP_POST_VARS"]["classificacaofr_id"])) {
            $sql .= $virgula . " classificacaofr_id = $this->classificacaofr_id ";
            $virgula = ",";
            if (trim($this->classificacaofr_id) == null) {
                $this->erro_sql = " Campo Classificação não informado.";
                $this->erro_campo = "classificacaofr_id";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->tipo_detalhamento) != "" || isset($GLOBALS["HTTP_POST_VARS"]["tipo_detalhamento"])) {
            $sql .= $virgula . " tipo_detalhamento = '$this->tipo_detalhamento' ";
            $virgula = ",";
            if (trim($this->tipo_detalhamento) == null) {
                $this->erro_sql = " Campo Detalhamento não informado.";
                $this->erro_campo = "tipo_detalhamento";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->descricao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["descricao"])) {
            $sql .= $virgula . " descricao = '$this->descricao' ";
            $virgula = ",";
            if (trim($this->descricao) == null) {
                $this->erro_sql = " Campo Descrição não informado.";
                $this->erro_campo = "descricao";
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

        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Fonte de Recurso não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->id;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Fonte de Recurso não foi Alterado. Alteração Executada.\\n";
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
        $sql = " delete from fonterecurso where ";
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
            $this->erro_sql = "Fonte de Recurso não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $id;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Fonte de Recurso não Encontrado. Exclusão não Efetuada.\\n";
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
            $this->erro_sql = "Record Vazio na Tabela:fonterecurso";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($id = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "
        select {$campos}
          from fonterecurso
          join orctiporec  on  orctiporec.o15_codigo = fonterecurso.orctiporec_id
          join classificacaofr  on  classificacaofr.id = fonterecurso.classificacaofr_id
          join db_estruturavalor  on  db_estruturavalor.db121_sequencial = orctiporec.o15_db_estruturavalor
        ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($id)) {
                $sql2 .= " where fonterecurso.id = $id ";
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
        $sql .= "  from fonterecurso ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($id)) {
                $sql2 .= " where fonterecurso.id = $id ";
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

    public function sqlFonteRecruso($id = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "
        select {$campos}
          from fonterecurso
          join orctiporec  on  orctiporec.o15_codigo = fonterecurso.orctiporec_id
          join complementofonterecurso on o200_sequencial = o15_complemento
          join classificacaofr  on  classificacaofr.id = fonterecurso.classificacaofr_id
          join db_estruturavalor  on  db_estruturavalor.db121_sequencial = orctiporec.o15_db_estruturavalor
        ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($id)) {
                $sql2 .= " where fonterecurso.id = $id ";
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
