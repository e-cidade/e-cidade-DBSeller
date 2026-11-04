<?php
//MODULO: contabilidade
//CLASSE DA ENTIDADE avaliacaogruporespostaconta
class cl_avaliacaogruporespostaconta
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
    var $c06_sequencial = 0;
    var $c06_conta = 0;
    var $c06_avaliacaogruporesposta = 0;
    var $c06_ano = 0;
    // cria propriedade com as variaveis do arquivo
    var $campos = "
                 c06_sequencial = int4 = Sequencial
                 c06_conta = int4 = Conta
                 c06_avaliacaogruporesposta = int4 = Avaliação Grupo Resposta
                 c06_ano = int4 = Ano
                 ";

    //funcao construtor da classe
    public function __construct()
    {
        //classes dos rotulos dos campos
        $this->rotulo = new rotulo("avaliacaogruporespostaconta");
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
            $this->c06_sequencial = ($this->c06_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["c06_sequencial"] : $this->c06_sequencial);
            $this->c06_conta = ($this->c06_conta == "" ? @$GLOBALS["HTTP_POST_VARS"]["c06_conta"] : $this->c06_conta);
            $this->c06_avaliacaogruporesposta = ($this->c06_avaliacaogruporesposta == "" ? @$GLOBALS["HTTP_POST_VARS"]["c06_avaliacaogruporesposta"] : $this->c06_avaliacaogruporesposta);
            $this->c06_ano = ($this->c06_ano == "" ? @$GLOBALS["HTTP_POST_VARS"]["c06_ano"] : $this->c06_ano);
        } else {
            $this->c06_sequencial = ($this->c06_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["c06_sequencial"] : $this->c06_sequencial);
        }
    }

    // funcao para Inclusão
    public function incluir($c06_sequencial)
    {
        $this->atualizacampos();
        if ($this->c06_conta == null) {
            $this->erro_sql = " Campo Conta não informado.";
            $this->erro_campo = "c06_conta";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->c06_avaliacaogruporesposta == null) {
            $this->erro_sql = " Campo Avaliação Grupo Resposta não informado.";
            $this->erro_campo = "c06_avaliacaogruporesposta";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->c06_ano == null) {
            $this->erro_sql = " Campo Ano não informado.";
            $this->erro_campo = "c06_ano";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($c06_sequencial == "" || $c06_sequencial == null) {
            $result = db_query("select nextval('avaliacaogruporespostaconta_c06_sequencial_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: avaliacaogruporespostaconta_c06_sequencial_seq do campo: c06_sequencial";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->c06_sequencial = pg_result($result, 0, 0);
        } else {
            $result = db_query("SELECT last_value FROM avaliacaogruporespostaconta_c06_sequencial_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $c06_sequencial)) {
                $this->erro_sql = " Campo c06_sequencial maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->c06_sequencial = $c06_sequencial;
            }
        }
        if (($this->c06_sequencial == null) || ($this->c06_sequencial == "")) {
            $this->erro_sql = " Campo c06_sequencial não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into avaliacaogruporespostaconta(
                                       c06_sequencial
                                      ,c06_conta
                                      ,c06_avaliacaogruporesposta
                                      ,c06_ano
                       )
                values (
                                $this->c06_sequencial
                               ,$this->c06_conta
                               ,$this->c06_avaliacaogruporesposta
                               ,$this->c06_ano
                      )";
        $result = db_query($sql);
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->c06_sequencial;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "",
            str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->c06_sequencial));
            if (($resaco != false) || ($this->numrows != 0)) {

                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                $resac = db_query("insert into db_acountkey values($acount,1009812,'$this->c06_sequencial','I')");
                $resac = db_query("insert into db_acount values($acount,1010294,1009812,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'c06_sequencial')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010294,1009814,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'c06_conta')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010294,1009813,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'c06_avaliacaogruporesposta')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010294,1009815,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'c06_ano')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            }
        }
        return true;
    }

    // funcao para alteracao
    public function alterar($c06_sequencial = null)
    {
        $this->atualizacampos();
        $sql = " UPDATE avaliacaogruporespostaconta SET ";
        $virgula = "";
        if (trim($this->c06_sequencial) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c06_sequencial"])) {
            $sql .= $virgula . " c06_sequencial = $this->c06_sequencial ";
            $virgula = ",";
            if (trim($this->c06_sequencial) == null) {
                $this->erro_sql = " Campo Sequencial não informado.";
                $this->erro_campo = "c06_sequencial";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->c06_conta) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c06_conta"])) {
            $sql .= $virgula . " c06_conta = $this->c06_conta ";
            $virgula = ",";
            if (trim($this->c06_conta) == null) {
                $this->erro_sql = " Campo Conta não informado.";
                $this->erro_campo = "c06_conta";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->c06_avaliacaogruporesposta) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c06_avaliacaogruporesposta"])) {
            $sql .= $virgula . " c06_avaliacaogruporesposta = $this->c06_avaliacaogruporesposta ";
            $virgula = ",";
            if (trim($this->c06_avaliacaogruporesposta) == null) {
                $this->erro_sql = " Campo Avaliação Grupo Resposta não informado.";
                $this->erro_campo = "c06_avaliacaogruporesposta";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->c06_ano) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c06_ano"])) {
            $sql .= $virgula . " c06_ano = $this->c06_ano ";
            $virgula = ",";
            if (trim($this->c06_ano) == null) {
                $this->erro_sql = " Campo Ano não informado.";
                $this->erro_campo = "c06_ano";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($c06_sequencial != null) {
            $sql .= " c06_sequencial = $this->c06_sequencial";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Avaliação Grupo Resposta Conta não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->c06_sequencial;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Avaliação Grupo Resposta Conta não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->c06_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $this->c06_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    // funcao para exclusao
    public function excluir($c06_sequencial = null, $dbwhere = null)
    {

        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        $sql = " DELETE FROM avaliacaogruporespostaconta
                    WHERE ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($c06_sequencial)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " c06_sequencial = $c06_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Avaliação Grupo Resposta Conta não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $c06_sequencial;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Avaliação Grupo Resposta Conta não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $c06_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $c06_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
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
            $this->erro_sql = "Record Vazio na Tabela:avaliacaogruporespostaconta";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    // funcao do sql
    public function sql_query($c06_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos}";
        $sql .= "  from avaliacaogruporespostaconta ";
        $sql .= "      inner join avaliacaogruporesposta  on  avaliacaogruporesposta.db107_sequencial = avaliacaogruporespostaconta.c06_avaliacaogruporesposta";
        $sql .= "      inner join conplanoorcamento  on  conplanoorcamento.c60_codcon = avaliacaogruporespostaconta.c06_conta and  conplanoorcamento.c60_codcon = avaliacaogruporespostaconta.c06_ano";
        $sql .= "      inner join conclass  on  conclass.c51_codcla = conplanoorcamento.c60_codcla";
        $sql .= "      inner join consistema  on  consistema.c52_codsis = conplanoorcamento.c60_codsis";
        $sql .= "      inner join consistemaconta  on  consistemaconta.c65_sequencial = conplanoorcamento.c60_consistemaconta";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($c06_sequencial)) {
                $sql2 .= " where avaliacaogruporespostaconta.c06_sequencial = $c06_sequencial ";
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

    // funcao do sql
    public function sql_query_file($c06_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos} ";
        $sql .= "  from avaliacaogruporespostaconta ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($c06_sequencial)) {
                $sql2 .= " where avaliacaogruporespostaconta.c06_sequencial = $c06_sequencial ";
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

    public function buscaRespostasPorPergunta($pergunta, $preenchimento, $campos = "*", $ordem = null)
    {
        $sql = "select {$campos}";
        $sql .= "  from avaliacaogruporespostaconta";
        $sql .= "      inner join avaliacaogruporesposta on db107_sequencial = c06_avaliacaogruporesposta";
        $sql .= "      inner join avaliacaogrupoperguntaresposta on db108_avaliacaogruporesposta = db107_sequencial";
        $sql .= "      inner join avaliacaoresposta on db106_sequencial = db108_avaliacaoresposta";
        $sql .= "      inner join avaliacaoperguntaopcao on db104_sequencial = db106_avaliacaoperguntaopcao";
        $sql .= "      inner join avaliacaopergunta on db103_sequencial = db104_avaliacaopergunta";
        $sql .= " where db103_sequencial = {$pergunta}";
        $sql .= "   and db107_sequencial = {$preenchimento}";
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }
        return $sql;
    }
}
