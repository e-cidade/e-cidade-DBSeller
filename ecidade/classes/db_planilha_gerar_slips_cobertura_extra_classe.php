<?php

class cl_planilha_gerar_slips_cobertura_extra
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
    public $placaixarec_id = 0;
    public $credor = 0;
    public $creditar = 0;
    public $debitar = 0;
    public $historico = 0;
    public $cp = null;
    public $valor = 0;
    public $tipo_pagamento = 0;
    public $tipo_operacao = 0;
    public $anulado = 'f';
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 id = int8 = id
                 placaixarec_id = int4 = Item da Planilha
                 credor = int4 = Credor
                 creditar = int4 = Creditar
                 debitar = int4 = Debitar
                 historico = int4 = Histórico
                 cp = char(3) = Característica Peculiar
                 valor = float8 = valor
                 tipo_pagamento = int4 = Tipo de Pagamento
                 tipo_operacao = int4 = Tipo de Operação
                 anulado = bool = Anulado
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("planilha_gerar_slips_cobertura_extra");
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
            $this->placaixarec_id = ($this->placaixarec_id == "" ? @$GLOBALS["HTTP_POST_VARS"]["placaixarec_id"] : $this->placaixarec_id);
            $this->credor = ($this->credor == "" ? @$GLOBALS["HTTP_POST_VARS"]["credor"] : $this->credor);
            $this->creditar = ($this->creditar == "" ? @$GLOBALS["HTTP_POST_VARS"]["creditar"] : $this->creditar);
            $this->debitar = ($this->debitar == "" ? @$GLOBALS["HTTP_POST_VARS"]["debitar"] : $this->debitar);
            $this->historico = ($this->historico == "" ? @$GLOBALS["HTTP_POST_VARS"]["historico"] : $this->historico);
            $this->cp = ($this->cp == "" ? @$GLOBALS["HTTP_POST_VARS"]["cp"] : $this->cp);
            $this->valor = ($this->valor == "" ? @$GLOBALS["HTTP_POST_VARS"]["valor"] : $this->valor);
            $this->tipo_pagamento = ($this->tipo_pagamento == "" ? @$GLOBALS["HTTP_POST_VARS"]["tipo_pagamento"] : $this->tipo_pagamento);
            $this->tipo_operacao = ($this->tipo_operacao == "" ? @$GLOBALS["HTTP_POST_VARS"]["tipo_operacao"] : $this->tipo_operacao);
            $this->anulado = (!empty($GLOBALS["HTTP_POST_VARS"]["anulado"]) ? $GLOBALS["HTTP_POST_VARS"]["anulado"] : $this->anulado);
        } else {
            $this->id = ($this->id == "" ? @$GLOBALS["HTTP_POST_VARS"]["id"] : $this->id);
        }
    }

    public function incluir($id)
    {
        $this->atualizacampos();
        if ($this->placaixarec_id == null) {
            $this->erro_sql = " Campo Item da Planilha não informado.";
            $this->erro_campo = "placaixarec_id";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->credor == null) {
            $this->erro_sql = " Campo Credor não informado.";
            $this->erro_campo = "credor";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->creditar == null) {
            $this->erro_sql = " Campo Creditar não informado.";
            $this->erro_campo = "creditar";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->debitar == null) {
            $this->erro_sql = " Campo Debitar não informado.";
            $this->erro_campo = "debitar";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->historico == null) {
            $this->erro_sql = " Campo Histórico não informado.";
            $this->erro_campo = "historico";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->cp == null) {
            $this->erro_sql = " Campo Característica Peculiar não informado.";
            $this->erro_campo = "cp";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->valor == null) {
            $this->erro_sql = " Campo valor não informado.";
            $this->erro_campo = "valor";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->tipo_pagamento == null) {
            $this->erro_sql = " Campo Tipo de Pagamento não informado.";
            $this->erro_campo = "tipo_pagamento";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->tipo_operacao == null) {
            $this->erro_sql = " Campo Tipo de Operação não informado.";
            $this->erro_campo = "tipo_operacao";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->anulado === null) {
            $this->erro_sql = " Campo Anulado não informado.";
            $this->erro_campo = "anulado";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($id == "" || $id == null) {
            $result = db_query("select nextval('planilha_gerar_slips_cobertura_extra_id_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: planilha_gerar_slips_cobertura_extra_id_seq do campo: id";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->id = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from planilha_gerar_slips_cobertura_extra_id_seq");
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
        $sql = "insert into planilha_gerar_slips_cobertura_extra(
                                       id
                                      ,placaixarec_id
                                      ,credor
                                      ,creditar
                                      ,debitar
                                      ,historico
                                      ,cp
                                      ,valor
                                      ,tipo_pagamento
                                      ,tipo_operacao
                                      ,anulado
                       )
                values (
                                $this->id
                               ,$this->placaixarec_id
                               ,$this->credor
                               ,$this->creditar
                               ,$this->debitar
                               ,$this->historico
                               ,'$this->cp'
                               ,$this->valor
                               ,$this->tipo_pagamento
                               ,$this->tipo_operacao
                               ,'$this->anulado'
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = " ($this->id) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = " já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = " ($this->id) não Incluído. Inclusão Abortada.";
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
                $resac = db_query("insert into db_acount values($acount,1011108,1011345,'','" . AddSlashes(pg_result($resaco, 0, 'id')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1011108,1015210,'','" . AddSlashes(pg_result($resaco, 0, 'placaixarec_id')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1011108,1015211,'','" . AddSlashes(pg_result($resaco, 0, 'credor')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1011108,1015212,'','" . AddSlashes(pg_result($resaco, 0, 'creditar')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1011108,1015213,'','" . AddSlashes(pg_result($resaco, 0, 'debitar')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1011108,1015214,'','" . AddSlashes(pg_result($resaco, 0, 'historico')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1011108,1015215,'','" . AddSlashes(pg_result($resaco, 0, 'cp')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1011108,556,'','" . AddSlashes(pg_result($resaco, 0, 'valor')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1011108,1015216,'','" . AddSlashes(pg_result($resaco, 0, 'tipo_pagamento')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1011108,1015217,'','" . AddSlashes(pg_result($resaco, 0, 'tipo_operacao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1011108,1015218,'','" . AddSlashes(pg_result($resaco, 0, 'anulado')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            }
        }
        return true;
    }

    public function alterar($id = null)
    {
        $this->atualizacampos();
        $sql = " update planilha_gerar_slips_cobertura_extra set ";
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
        if (trim($this->placaixarec_id) != "" || isset($GLOBALS["HTTP_POST_VARS"]["placaixarec_id"])) {
            $sql .= $virgula . " placaixarec_id = $this->placaixarec_id ";
            $virgula = ",";
            if (trim($this->placaixarec_id) == null) {
                $this->erro_sql = " Campo Item da Planilha não informado.";
                $this->erro_campo = "placaixarec_id";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->credor) != "" || isset($GLOBALS["HTTP_POST_VARS"]["credor"])) {
            $sql .= $virgula . " credor = $this->credor ";
            $virgula = ",";
            if (trim($this->credor) == null) {
                $this->erro_sql = " Campo Credor não informado.";
                $this->erro_campo = "credor";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->creditar) != "" || isset($GLOBALS["HTTP_POST_VARS"]["creditar"])) {
            $sql .= $virgula . " creditar = $this->creditar ";
            $virgula = ",";
            if (trim($this->creditar) == null) {
                $this->erro_sql = " Campo Creditar não informado.";
                $this->erro_campo = "creditar";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->debitar) != "" || isset($GLOBALS["HTTP_POST_VARS"]["debitar"])) {
            $sql .= $virgula . " debitar = $this->debitar ";
            $virgula = ",";
            if (trim($this->debitar) == null) {
                $this->erro_sql = " Campo Debitar não informado.";
                $this->erro_campo = "debitar";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->historico) != "" || isset($GLOBALS["HTTP_POST_VARS"]["historico"])) {
            $sql .= $virgula . " historico = $this->historico ";
            $virgula = ",";
            if (trim($this->historico) == null) {
                $this->erro_sql = " Campo Histórico não informado.";
                $this->erro_campo = "historico";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->cp) != "" || isset($GLOBALS["HTTP_POST_VARS"]["cp"])) {
            $sql .= $virgula . " cp = '$this->cp' ";
            $virgula = ",";
            if (trim($this->cp) == null) {
                $this->erro_sql = " Campo Característica Peculiar não informado.";
                $this->erro_campo = "cp";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->valor) != "" || isset($GLOBALS["HTTP_POST_VARS"]["valor"])) {
            $sql .= $virgula . " valor = $this->valor ";
            $virgula = ",";
            if (trim($this->valor) == null) {
                $this->erro_sql = " Campo valor não informado.";
                $this->erro_campo = "valor";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->tipo_pagamento) != "" || isset($GLOBALS["HTTP_POST_VARS"]["tipo_pagamento"])) {
            $sql .= $virgula . " tipo_pagamento = $this->tipo_pagamento ";
            $virgula = ",";
            if (trim($this->tipo_pagamento) == null) {
                $this->erro_sql = " Campo Tipo de Pagamento não informado.";
                $this->erro_campo = "tipo_pagamento";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->tipo_operacao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["tipo_operacao"])) {
            $sql .= $virgula . " tipo_operacao = $this->tipo_operacao ";
            $virgula = ",";
            if (trim($this->tipo_operacao) == null) {
                $this->erro_sql = " Campo Tipo de Operação não informado.";
                $this->erro_campo = "tipo_operacao";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->anulado) !== "" || isset($GLOBALS["HTTP_POST_VARS"]["anulado"])) {
            $sql .= $virgula . " anulado = '$this->anulado' ";
            $virgula = ",";
            if (trim($this->anulado) == null) {
                $this->erro_sql = " Campo Anulado não informado.";
                $this->erro_campo = "anulado";
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
                        $resac = db_query("insert into db_acount values($acount,1011108,1011345,'" . AddSlashes(pg_result($resaco, $conresaco, 'id')) . "','$this->id'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["placaixarec_id"]) || $this->placaixarec_id != "")
                        $resac = db_query("insert into db_acount values($acount,1011108,1015210,'" . AddSlashes(pg_result($resaco, $conresaco, 'placaixarec_id')) . "','$this->placaixarec_id'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["credor"]) || $this->credor != "")
                        $resac = db_query("insert into db_acount values($acount,1011108,1015211,'" . AddSlashes(pg_result($resaco, $conresaco, 'credor')) . "','$this->credor'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["creditar"]) || $this->creditar != "")
                        $resac = db_query("insert into db_acount values($acount,1011108,1015212,'" . AddSlashes(pg_result($resaco, $conresaco, 'creditar')) . "','$this->creditar'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["debitar"]) || $this->debitar != "")
                        $resac = db_query("insert into db_acount values($acount,1011108,1015213,'" . AddSlashes(pg_result($resaco, $conresaco, 'debitar')) . "','$this->debitar'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["historico"]) || $this->historico != "")
                        $resac = db_query("insert into db_acount values($acount,1011108,1015214,'" . AddSlashes(pg_result($resaco, $conresaco, 'historico')) . "','$this->historico'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["cp"]) || $this->cp != "")
                        $resac = db_query("insert into db_acount values($acount,1011108,1015215,'" . AddSlashes(pg_result($resaco, $conresaco, 'cp')) . "','$this->cp'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["valor"]) || $this->valor != "")
                        $resac = db_query("insert into db_acount values($acount,1011108,556,'" . AddSlashes(pg_result($resaco, $conresaco, 'valor')) . "','$this->valor'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["tipo_pagamento"]) || $this->tipo_pagamento != "")
                        $resac = db_query("insert into db_acount values($acount,1011108,1015216,'" . AddSlashes(pg_result($resaco, $conresaco, 'tipo_pagamento')) . "','$this->tipo_pagamento'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["tipo_operacao"]) || $this->tipo_operacao != "")
                        $resac = db_query("insert into db_acount values($acount,1011108,1015217,'" . AddSlashes(pg_result($resaco, $conresaco, 'tipo_operacao')) . "','$this->tipo_operacao'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["anulado"]) || $this->anulado != "")
                        $resac = db_query("insert into db_acount values($acount,1011108,1015218,'" . AddSlashes(pg_result($resaco, $conresaco, 'anulado')) . "','$this->anulado'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = " não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->id;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = " não foi Alterado. Alteração Executada.\\n";
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
                    $resac = db_query("insert into db_acount values($acount,1011108,1011345,'','" . AddSlashes(pg_result($resaco, $iresaco, 'id')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1011108,1015210,'','" . AddSlashes(pg_result($resaco, $iresaco, 'placaixarec_id')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1011108,1015211,'','" . AddSlashes(pg_result($resaco, $iresaco, 'credor')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1011108,1015212,'','" . AddSlashes(pg_result($resaco, $iresaco, 'creditar')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1011108,1015213,'','" . AddSlashes(pg_result($resaco, $iresaco, 'debitar')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1011108,1015214,'','" . AddSlashes(pg_result($resaco, $iresaco, 'historico')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1011108,1015215,'','" . AddSlashes(pg_result($resaco, $iresaco, 'cp')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1011108,556,'','" . AddSlashes(pg_result($resaco, $iresaco, 'valor')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1011108,1015216,'','" . AddSlashes(pg_result($resaco, $iresaco, 'tipo_pagamento')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1011108,1015217,'','" . AddSlashes(pg_result($resaco, $iresaco, 'tipo_operacao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1011108,1015218,'','" . AddSlashes(pg_result($resaco, $iresaco, 'anulado')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }
        $sql = " delete from planilha_gerar_slips_cobertura_extra
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
            $this->erro_sql = " não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $id;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = " não Encontrado. Exclusão não Efetuada.\\n";
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
            $this->erro_sql = "Record Vazio na Tabela:planilha_gerar_slips_cobertura_extra";
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
           from planilha_gerar_slips_cobertura_extra
           join cgm on cgm.z01_numcgm = planilha_gerar_slips_cobertura_extra.credor
           join saltes c on c.k13_conta = planilha_gerar_slips_cobertura_extra.creditar
           join conhist on conhist.c50_codhist = planilha_gerar_slips_cobertura_extra.historico
           join placaixarec on placaixarec.k81_seqpla = planilha_gerar_slips_cobertura_extra.placaixarec_id
           join cgm on cgm.z01_numcgm = placaixarec.k81_numcgm
           join tabrec on tabrec.k02_codigo = placaixarec.k81_receita
           join saltes as d on d.k13_conta = placaixarec.k81_conta
           join orctiporec on orctiporec.o15_codigo = placaixarec.k81_codigo
           join placaixa on placaixa.k80_codpla = placaixarec.k81_codpla
           join concarpeculiar on concarpeculiar.c58_sequencial = placaixarec.k81_concarpeculiar";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($id)) {
                $sql2 .= " where planilha_gerar_slips_cobertura_extra.id = $id ";
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

        $sql = " select {$campos} from planilha_gerar_slips_cobertura_extra ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($id)) {
                $sql2 .= " where planilha_gerar_slips_cobertura_extra.id = $id ";
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
