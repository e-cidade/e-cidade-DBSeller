<?php

/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

class cl_empagetipo
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
    public $e83_codtipo = 0;
    public $e83_descr = null;
    public $e83_conta = 0;
    public $e83_codmod = 0;
    public $e83_convenio = null;
    public $e83_sequencia = 0;
    public $e83_codigocompromisso = null;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 e83_codtipo = int4 = Tipo
                 e83_descr = varchar(60) = Descrição
                 e83_conta = int4 = Código Conta
                 e83_codmod = int4 = Modelo
                 e83_convenio = varchar(20) = Convenio
                 e83_sequencia = int4 = Seq. Cheque
                 e83_codigocompromisso = varchar(4) = Código do Compromisso
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("empagetipo");
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
            $this->e83_codtipo = ($this->e83_codtipo == "" ? @$GLOBALS["HTTP_POST_VARS"]["e83_codtipo"] : $this->e83_codtipo);
            $this->e83_descr = ($this->e83_descr == "" ? @$GLOBALS["HTTP_POST_VARS"]["e83_descr"] : $this->e83_descr);
            $this->e83_conta = ($this->e83_conta == "" ? @$GLOBALS["HTTP_POST_VARS"]["e83_conta"] : $this->e83_conta);
            $this->e83_codmod = ($this->e83_codmod == "" ? @$GLOBALS["HTTP_POST_VARS"]["e83_codmod"] : $this->e83_codmod);
            $this->e83_convenio = ($this->e83_convenio == "" ? @$GLOBALS["HTTP_POST_VARS"]["e83_convenio"] : $this->e83_convenio);
            $this->e83_sequencia = ($this->e83_sequencia == "" ? @$GLOBALS["HTTP_POST_VARS"]["e83_sequencia"] : $this->e83_sequencia);
            $this->e83_codigocompromisso = ($this->e83_codigocompromisso == "" ? @$GLOBALS["HTTP_POST_VARS"]["e83_codigocompromisso"] : $this->e83_codigocompromisso);
        } else {
            $this->e83_codtipo = ($this->e83_codtipo == "" ? @$GLOBALS["HTTP_POST_VARS"]["e83_codtipo"] : $this->e83_codtipo);
        }
    }

    public function incluir($e83_codtipo)
    {
        $this->atualizacampos();
        if ($this->e83_descr == null) {
            $this->erro_sql = " Campo Descrição não informado.";
            $this->erro_campo = "e83_descr";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e83_conta == null) {
            $this->erro_sql = " Campo Código Conta não informado.";
            $this->erro_campo = "e83_conta";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e83_codmod == null) {
            $this->erro_sql = " Campo Modelo não informado.";
            $this->erro_campo = "e83_codmod";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e83_convenio == null) {
            $this->erro_sql = " Campo Convenio não informado.";
            $this->erro_campo = "e83_convenio";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e83_sequencia == null) {
            $this->erro_sql = " Campo Seq. Cheque não informado.";
            $this->erro_campo = "e83_sequencia";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($e83_codtipo == "" || $e83_codtipo == null) {
            $result = db_query("select nextval('empagetipo_e83_codtipo_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: empagetipo_e83_codtipo_seq do campo: e83_codtipo";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->e83_codtipo = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from empagetipo_e83_codtipo_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $e83_codtipo)) {
                $this->erro_sql = " Campo e83_codtipo maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->e83_codtipo = $e83_codtipo;
            }
        }
        if (($this->e83_codtipo == null) || ($this->e83_codtipo == "")) {
            $this->erro_sql = " Campo e83_codtipo não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->setSequenciaCheque($this->e83_conta);
        $sql = "insert into empagetipo(
                                       e83_codtipo
                                      ,e83_descr
                                      ,e83_conta
                                      ,e83_codmod
                                      ,e83_convenio
                                      ,e83_sequencia
                                      ,e83_codigocompromisso
                       )
                values (
                                $this->e83_codtipo
                               ,'$this->e83_descr'
                               ,$this->e83_conta
                               ,$this->e83_codmod
                               ,'$this->e83_convenio'
                               ,$this->e83_sequencia
                               ,'$this->e83_codigocompromisso'
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "Tipo agenda ($this->e83_codtipo) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "Tipo agenda já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "Tipo agenda ($this->e83_codtipo) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->e83_codtipo;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->e83_codtipo));
            if (($resaco != false) || ($this->numrows != 0)) {

                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                $resac = db_query("insert into db_acountkey values($acount,6179,'$this->e83_codtipo','I')");
                $resac = db_query("insert into db_acount values($acount,997,6179,'','" . AddSlashes(pg_result($resaco, 0, 'e83_codtipo')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,997,6180,'','" . AddSlashes(pg_result($resaco, 0, 'e83_descr')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,997,6181,'','" . AddSlashes(pg_result($resaco, 0, 'e83_conta')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,997,6182,'','" . AddSlashes(pg_result($resaco, 0, 'e83_codmod')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,997,6199,'','" . AddSlashes(pg_result($resaco, 0, 'e83_convenio')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,997,6200,'','" . AddSlashes(pg_result($resaco, 0, 'e83_sequencia')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,997,15012,'','" . AddSlashes(pg_result($resaco, 0, 'e83_codigocompromisso')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            }
        }
        return true;
    }

    public function alterar($e83_codtipo = null)
    {
        $this->atualizacampos();
        $sql = " update empagetipo set ";
        $virgula = "";
        if (trim($this->e83_codtipo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e83_codtipo"])) {
            $sql .= $virgula . " e83_codtipo = $this->e83_codtipo ";
            $virgula = ",";
            if (trim($this->e83_codtipo) == null) {
                $this->erro_sql = " Campo Tipo não informado.";
                $this->erro_campo = "e83_codtipo";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e83_descr) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e83_descr"])) {
            $sql .= $virgula . " e83_descr = '$this->e83_descr' ";
            $virgula = ",";
            if (trim($this->e83_descr) == null) {
                $this->erro_sql = " Campo Descrição não informado.";
                $this->erro_campo = "e83_descr";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e83_conta) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e83_conta"])) {
            $sql .= $virgula . " e83_conta = $this->e83_conta ";
            $virgula = ",";
            if (trim($this->e83_conta) == null) {
                $this->erro_sql = " Campo Código Conta não informado.";
                $this->erro_campo = "e83_conta";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e83_codmod) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e83_codmod"])) {
            $sql .= $virgula . " e83_codmod = $this->e83_codmod ";
            $virgula = ",";
            if (trim($this->e83_codmod) == null) {
                $this->erro_sql = " Campo Modelo não informado.";
                $this->erro_campo = "e83_codmod";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e83_convenio) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e83_convenio"])) {
            $sql .= $virgula . " e83_convenio = '$this->e83_convenio' ";
            $virgula = ",";
            if (trim($this->e83_convenio) == null) {
                $this->erro_sql = " Campo Convenio não informado.";
                $this->erro_campo = "e83_convenio";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e83_sequencia) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e83_sequencia"])) {
            $sql .= $virgula . " e83_sequencia = $this->e83_sequencia ";
            $virgula = ",";
            if (trim($this->e83_sequencia) == null) {
                $this->erro_sql = " Campo Seq. Cheque não informado.";
                $this->erro_campo = "e83_sequencia";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e83_codigocompromisso) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e83_codigocompromisso"])) {
            $sql .= $virgula . " e83_codigocompromisso = '$this->e83_codigocompromisso' ";
            $virgula = ",";
        }
        $sql .= " where ";
        if ($e83_codtipo != null) {
            $sql .= " e83_codtipo = $this->e83_codtipo";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->e83_codtipo));
            if ($this->numrows > 0) {

                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,6179,'$this->e83_codtipo','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e83_codtipo"]) || $this->e83_codtipo != "")
                        $resac = db_query("insert into db_acount values($acount,997,6179,'" . AddSlashes(pg_result($resaco, $conresaco, 'e83_codtipo')) . "','$this->e83_codtipo'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e83_descr"]) || $this->e83_descr != "")
                        $resac = db_query("insert into db_acount values($acount,997,6180,'" . AddSlashes(pg_result($resaco, $conresaco, 'e83_descr')) . "','$this->e83_descr'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e83_conta"]) || $this->e83_conta != "")
                        $resac = db_query("insert into db_acount values($acount,997,6181,'" . AddSlashes(pg_result($resaco, $conresaco, 'e83_conta')) . "','$this->e83_conta'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e83_codmod"]) || $this->e83_codmod != "")
                        $resac = db_query("insert into db_acount values($acount,997,6182,'" . AddSlashes(pg_result($resaco, $conresaco, 'e83_codmod')) . "','$this->e83_codmod'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e83_convenio"]) || $this->e83_convenio != "")
                        $resac = db_query("insert into db_acount values($acount,997,6199,'" . AddSlashes(pg_result($resaco, $conresaco, 'e83_convenio')) . "','$this->e83_convenio'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e83_sequencia"]) || $this->e83_sequencia != "")
                        $resac = db_query("insert into db_acount values($acount,997,6200,'" . AddSlashes(pg_result($resaco, $conresaco, 'e83_sequencia')) . "','$this->e83_sequencia'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e83_codigocompromisso"]) || $this->e83_codigocompromisso != "")
                        $resac = db_query("insert into db_acount values($acount,997,15012,'" . AddSlashes(pg_result($resaco, $conresaco, 'e83_codigocompromisso')) . "','$this->e83_codigocompromisso'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }
        $this->setSequenciaCheque($this->e83_conta);
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Tipo agenda não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->e83_codtipo;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Tipo agenda não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->e83_codtipo;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $this->e83_codtipo;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($e83_codtipo = null, $dbwhere = null)
    {
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            if (empty($dbwhere)) {

                $resaco = $this->sql_record($this->sql_query_file($e83_codtipo));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
            }
            if (($resaco != false) || ($this->numrows != 0)) {

                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,6179,'$e83_codtipo','E')");
                    $resac = db_query("insert into db_acount values($acount,997,6179,'','" . AddSlashes(pg_result($resaco, $iresaco, 'e83_codtipo')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,997,6180,'','" . AddSlashes(pg_result($resaco, $iresaco, 'e83_descr')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,997,6181,'','" . AddSlashes(pg_result($resaco, $iresaco, 'e83_conta')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,997,6182,'','" . AddSlashes(pg_result($resaco, $iresaco, 'e83_codmod')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,997,6199,'','" . AddSlashes(pg_result($resaco, $iresaco, 'e83_convenio')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,997,6200,'','" . AddSlashes(pg_result($resaco, $iresaco, 'e83_sequencia')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,997,15012,'','" . AddSlashes(pg_result($resaco, $iresaco, 'e83_codigocompromisso')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }
        $sql = " delete from empagetipo
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($e83_codtipo)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " e83_codtipo = $e83_codtipo ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Tipo agenda não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $e83_codtipo;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Tipo agenda não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $e83_codtipo;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $e83_codtipo;
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
            $this->erro_sql = "Record Vazio na Tabela:empagetipo";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($e83_codtipo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos}";
        $sql .= "  from empagetipo ";
        $sql .= "      inner join saltes  on  saltes.k13_conta = empagetipo.e83_conta";
        $sql .= "      inner join empagemod  on  empagemod.e84_codmod = empagetipo.e83_codmod";
        $sql .= "      inner join conplanoreduz on saltes.k13_reduz = conplanoreduz.c61_reduz and conplanoreduz.c61_anousu = " . db_getsession("DB_anousu") . " and conplanoreduz.c61_instit = " . db_getsession("DB_instit");
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($e83_codtipo)) {
                $sql2 .= " where empagetipo.e83_codtipo = $e83_codtipo ";
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

    public function sql_query_file($e83_codtipo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos} ";
        $sql .= "  from empagetipo ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($e83_codtipo)) {
                $sql2 .= " where empagetipo.e83_codtipo = $e83_codtipo ";
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

    public function getMaxCheque($iConta)
    {

        if (!class_exists("db_utils")) {
            require_once(modification("libs/db_utils.php"));
        }
        if ($iConta == '') {

            return false;
        }
        $iAnoUsu = db_getsession("DB_anousu");
        $iInstit = db_getsession("DB_instit");
        $sql = "SELECT c63_agencia , ";
        $sql .= "       c63_conta,    ";
        $sql .= "       c63_banco     ";
        $sql .= "  from conplanoconta ";
        $sql .= "       inner join conplanoreduz on c61_codcon = c63_codcon ";
        $sql .= "                               and c61_anousu = c63_anousu";
        $sql .= "                               and c61_reduz = c63_reduz";
        $sql .= "       inner join saltes        on k13_reduz  = c61_reduz";
        $sql .= " where c61_anousu = {$iAnoUsu} ";
        $sql .= "   and c61_instit = {$iInstit} ";
        $sql .= "   and k13_conta  = {$iConta}";
        $rsConta = $this->sql_record($sql);
        if ($this->numrows > 0) {

            $oContas = db_utils::fieldsMemory($rsConta, 0);
            $sSqlAgenciasConta = "SELECT max(e83_sequencia) as total ";
            $sSqlAgenciasConta .= "  from conplanoconta ";
            $sSqlAgenciasConta .= "       inner join conplanoreduz on c61_codcon = c63_codcon ";
            $sSqlAgenciasConta .= "                               and c61_anousu = c63_anousu
                                                            and c61_reduz = c63_reduz ";
            $sSqlAgenciasConta .= "       inner join saltes     on c61_reduz = k13_reduz ";
            $sSqlAgenciasConta .= "       inner join empagetipo on k13_conta = e83_conta ";
            $sSqlAgenciasConta .= " where c63_anousu  = {$iAnoUsu}";
            $sSqlAgenciasConta .= "   and trim(c63_agencia) = '{$oContas->c63_agencia}'";
            $sSqlAgenciasConta .= "   and trim(c63_conta)   = '{$oContas->c63_conta}'";
            $sSqlAgenciasConta .= "   and trim(c63_banco)   = '{$oContas->c63_banco}'";
            $sSqlAgenciasConta .= "   and c63_anousu  = {$iAnoUsu}";
            $rsAgencias = $this->sql_record($sSqlAgenciasConta);
            if ($this->numrows > 0) {

                $oCheque = db_utils::fieldsMemory($rsAgencias, 0);
                return $oCheque->total;

            }
        }
    }

    public function setSequenciaCheque($iConta)
    {

        if (!class_exists("db_utils")) {
            require_once(modification("libs/db_utils.php"));
        }
        if ($iConta == '') {

            $sql = "select e83_conta from empagetipo where e83_codtipo = {$this->e83_codtipo}";
            $rs = db_query($sql);
            $iConta = pg_result($rs, 0, "e83_conta");
        }
        $iAnoUsu = db_getsession("DB_anousu");
        $iInstit = db_getsession("DB_instit");
        $sql = "SELECT c63_agencia , ";
        $sql .= "       c63_conta,    ";
        $sql .= "       c63_banco     ";
        $sql .= "  from conplanoconta ";
        $sql .= "       inner join conplanoreduz on c61_codcon = c63_codcon ";
        $sql .= "                                and c61_anousu = c63_anousu";
        $sql .= "                                and c61_reduz = c63_reduz ";
        $sql .= " where c61_anousu = {$iAnoUsu} ";
        $sql .= "   and c61_instit = {$iInstit} ";
        $sql .= "   and c61_reduz  = {$iConta}";
        $rsConta = $this->sql_record($sql);
        if ($this->numrows > 0) {

            $oContas = db_utils::fieldsMemory($rsConta, 0);
            $sSqlAgenciasConta = "SELECT * ";
            $sSqlAgenciasConta .= "  from conplanoconta ";
            $sSqlAgenciasConta .= "       inner join conplanoreduz on c61_codcon = c63_codcon ";
            $sSqlAgenciasConta .= "                               and c61_anousu = c63_anousu
                                                            and c61_reduz = c63_reduz ";
            $sSqlAgenciasConta .= " where c63_anousu  = {$iAnoUsu}";
            $sSqlAgenciasConta .= "   and trim(c63_agencia) = '{$oContas->c63_agencia}'";
            $sSqlAgenciasConta .= "   and trim(c63_conta)   = '{$oContas->c63_conta}'";
            $sSqlAgenciasConta .= "   and trim(c63_banco)   = '{$oContas->c63_banco}'";
            $rsAgencias = $this->sql_record($sSqlAgenciasConta);
            if ($this->numrows > 0) {

                $iTotAgencias = $this->numrows;
                for ($iInd = 0; $iInd < $iTotAgencias; $iInd++) {

                    $oAgencia = db_utils::fieldsMemory($rsAgencias, $iInd);
                    $rsUpdate = db_query("update empagetipo set e83_sequencia = {$this->e83_sequencia} where e83_conta = {$oAgencia->c61_reduz}");

                    if (!$rsUpdate) {

                        $this->erro_msg = "erro ao Atualizar numeracao do cheque";
                        return false;
                    }

                }
            }
        }
    }

    public function sql_query_conplano($e83_codtipo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from empagetipo ";
        $sql .= " 	    inner join saltes on k13_conta = e83_conta ";
        $sql .= " 	    inner join conplanoreduz on c61_reduz = k13_reduz and c61_anousu = " . db_getsession("DB_anousu") . " and c61_instit=" . db_getsession("DB_instit");
        $sql .= " 	    inner join conplano on c60_codcon = c61_codcon and c60_anousu = c61_anousu ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($e83_codtipo != null) {
                $sql2 .= " where empagetipo.e83_codtipo = $e83_codtipo ";
            }
        } else if ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql2 .= ($sql2 != "" ? " and " : " where ") . " c61_instit = " . db_getsession("DB_instit");
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

    public function sql_query_conplanoconta($e83_codtipo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from empagetipo ";
        $sql .= " 	    inner join saltes on k13_conta = e83_conta ";
        $sql .= " 	    inner join conplanoreduz on c61_reduz = k13_reduz and c61_anousu = " . db_getsession("DB_anousu") . " and c61_instit=" . db_getsession("DB_instit");
        $sql .= " 	    inner join conplano on c60_codcon = c61_codcon and c60_anousu = c61_anousu ";
        $sql .= " 	    inner join conplanoconta on c60_codcon = c63_codcon and c60_anousu = c63_anousu and c61_reduz = c63_reduz ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($e83_codtipo != null) {
                $sql2 .= " where empagetipo.e83_codtipo = $e83_codtipo ";
            }
        } else if ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql2 .= ($sql2 != "" ? " and " : " where ") . " c61_instit = " . db_getsession("DB_instit");
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

    public function sql_query_conta($e83_codtipo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from pagordem  ";
        $sql .= "  inner join empagemov  on e81_numemp  = e50_numemp ";
        $sql .= "  inner join empagepag  on e85_codmov  = e81_codmov";
        $sql .= "  inner join empagetipo on e83_codtipo = e85_codtipo";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($e83_codtipo != null) {
                $sql2 .= " where empagetipo.e83_codtipo = $e83_codtipo ";
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

    public function sql_query_contapaga($e83_codtipo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from empagetipo ";
        $sql .= "      inner join saltes     on  saltes.k13_conta = empagetipo.e83_conta";
        $sql .= " 	    inner join conplanoreduz on c61_reduz = saltes.k13_reduz and c61_anousu = " . db_getsession("DB_anousu") . " and c61_instit=" . db_getsession("DB_instit");
        $sql2 = "";
        if ($dbwhere == "") {
            if ($e83_codtipo != null) {
                $sql2 .= " where empagetipo.e83_codtipo = $e83_codtipo ";
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

    public function sql_query_contas_vinculadas($e83_codtipo = null, $campos = "*", $ordem = null, $sWhere, $lVinculadas = false)
    {

        $sSql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sSql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sSql .= $campos;
        }
        $joinRecursoEmpenho = '';
        $joinRecursoConta = '';
        if (!FONTE_RECURSO_UNIAO || VALIDA_FONTE_RECURSO) {
            $joinRecursoEmpenho = '
                 join orctiporec recdot on recdot.o15_codigo = o58_codigo
                 join fonterecurso frdot on frdot.orctiporec_id = recdot.o15_codigo
                     and frdot.exercicio = c61_anousu
             ';
            $joinRecursoConta = '
                join orctiporec recconta on recconta.o15_codigo = c61_codigo
                     and recconta.o15_recurso = recdot.o15_recurso
                join fonterecurso frconta on frconta.orctiporec_id = recconta.o15_codigo
                     and frconta.exercicio = c61_anousu
                     AND SUBSTRING(frconta.codigo_siconfi FROM 2) = SUBSTRING(frdot.codigo_siconfi FROM 2)
            ';
        }
        $sSql .= " from empagetipo ";
        $sSql .= "      left join ( select distinct
                                           c61_reduz,
                                           C61_CODIGO,
                                           c61_anousu,
                                           recconta.o15_recurso,
                                           frconta.gestao
                                      from empempenho
                                           inner join orcdotacao     on e60_coddot   = o58_coddot
                                                                    and e60_anousu   = o58_anousu
                                                                    inner join conplanoreduz  on (    c61_anousu = o58_anousu
                                                                    or c61_anousu = " . db_getsession("DB_anousu") . " )
                                            {$joinRecursoEmpenho}

                                            {$joinRecursoConta}
                                           left join pagordem        on e60_numemp   = e50_numemp
                                           left join saltes          on c61_reduz    = k13_conta
                                     where c61_instit = " . db_getsession("DB_instit");
        if ($sWhere != '') {
            $sSql .= " and {$sWhere}";
        }
        $sSql .= " )";

        $sSql .= " as x on e83_conta = c61_reduz";
        $sSql .= " where c61_reduz is not null ";

        if (USE_PCASP) {
            $sSql .= " and c61_anousu = " . db_getsession("DB_anousu");
        }

        if ($lVinculadas) {

            $sSql .= " or e83_conta in ";
            $sSql .= " (select c61_reduz from conplanoreduz where c61_anousu =" . db_getsession("DB_anousu");
            $sSql .= " and c61_instit = " . db_getsession("DB_instit") . ")";
        }

        /* [Extensão] - Filtro da Despesa */
        return $sSql;
    }

    public function sql_query_contas_vinculadas_fr($e83_codtipo = null, $campos = "*", $ordem = null, $sWhere, $lVinculadas = false)
    {

        $sSql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sSql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sSql .= $campos;
        }
        $joinRecursoEmpenho = '';
        $joinRecursoConta = '';
        if (!FONTE_RECURSO_UNIAO || VALIDA_FONTE_RECURSO) {
            $joinRecursoEmpenho = 'inner join orctiporec on orcdotacao.o58_codigo = orctiporec.o15_codigo';
        }
        $sSql .= " from empagetipo ";
        $sSql .= "      left join ( select distinct
                                           c61_reduz,
                                           C61_codigo,
                                           C61_anousu,
                                           fr.gestao
                                      from empempenho
                                           inner join orcdotacao on empempenho.e60_coddot = orcdotacao.o58_coddot
                                           and empempenho.e60_anousu = orcdotacao.o58_anousu
                                           {$joinRecursoEmpenho}
                                           inner join fonterecurso fr on fr.orctiporec_id = orctiporec.o15_codigo and fr.exercicio = orcdotacao.o58_anousu
                                           inner join conplanoreduz  on (   conplanoreduz.c61_anousu = orcdotacao.o58_anousu
                                                                          or c61_anousu = " . db_getsession("DB_anousu") . " )
                                           left join pagordem        on empempenho.e60_numemp = pagordem.e50_numemp
                                           left join saltes          on conplanoreduz.c61_reduz = saltes.k13_conta
                                     where conplanoreduz.c61_instit = " . db_getsession("DB_instit");
        if ($sWhere != '') {
            $sSql .= " and {$sWhere}";
        }
        $sSql .= " )";
        $sSql .= "and conplanoreduz.c61_codigo in (select orctiporec_id from fonterecurso where SUBSTRING(gestao, 2) = SUBSTRING(fr.gestao, 2)
                  and exercicio = orcdotacao.o58_anousu))";
        $sSql .= " as x on e83_conta = c61_reduz";
        $sSql .= " where c61_reduz is not null ";

        if (USE_PCASP) {
            $sSql .= " and c61_anousu = " . db_getsession("DB_anousu");
        }

        if ($lVinculadas) {

            $sSql .= " or e83_conta in ";
            $sSql .= " (select c61_reduz from conplanoreduz where c61_anousu =" . db_getsession("DB_anousu");
            $sSql .= " and c61_instit = " . db_getsession("DB_instit") . ")";
        }

        /* [Extens?o] - Filtro da Despesa */

        return $sSql;
    }


    public function sql_query_contas_vinculadas_rp($e83_codtipo = null, $campos = "*", $ordem = null, $sWhere, $lVinculadas = false)
    {

        $lVinculadas = false;
        $sSql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sSql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sSql .= $campos;
        }
        $sSql .= " from empagetipo ";
        $sSql .= "      left join ( select distinct
                                           c61_reduz,
                                           C61_CODIGO,
                                           c61_anousu
                                      from empempenho
                                           inner join empresto       on e60_numemp = e91_numemp
                                                                    and e91_anousu = " . db_getsession("DB_anousu") . "
                                           inner join conplanoreduz  on c61_anousu = " . db_getsession("DB_anousu") . "
                                                                    and c61_codigo = e91_recurso
                                           left join pagordem        on e91_numemp = e50_numemp
                                           left join saltes          on c61_reduz  = k13_conta
                                     where c61_instit = " . db_getsession("DB_instit");
        if ($sWhere != '') {
            $sSql .= " and {$sWhere}";
        }
        $sSql .= " )";

        $sSql .= " as x on e83_conta = c61_reduz";
        $sSql .= " where c61_reduz is not null ";

        if (USE_PCASP) {
            $sSql .= " and c61_anousu = " . db_getsession("DB_anousu");
        }

        if ($lVinculadas) {

            $sSql .= " or e83_conta in ";
            $sSql .= " (select c61_reduz from conplanoreduz where c61_anousu =" . db_getsession("DB_anousu");
            $sSql .= " and c61_instit = " . db_getsession("DB_instit") . ")";
        }

        return $sSql;
    }

    public function sql_query_emprec($e83_codtipo = null, $campos = "*", $ordem = null, $dbwhere = "", $dbwhere02 = "")
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
        $sql .= " from empagetipo ";
        $sql .= " 	    inner join conplanoreduz  on c61_reduz  = e83_conta ";
        $sql .= "                               and c61_anousu = " . db_getsession("DB_anousu");
        $sql .= "                               and c61_instit = " . db_getsession("DB_instit");

        $sql .= "      inner join orcdotacao     on o58_codigo = c61_codigo ";
        $sql .= "      inner join empempenho     on e60_coddot = o58_coddot ";
        $sql .= "                               and e60_anousu = o58_anousu "; // Verificar Anousu do Empenho para Buscar ORCDOTACAO

        $sql .= "       left join pagordem       on e50_numemp = e60_numemp ";


        $sql2 = "";
        if ($dbwhere == "") {
            if ($e83_codtipo != null) {
                $sql2 .= " where empagetipo.e83_codtipo = $e83_codtipo ";
            }
        } else if ($dbwhere != "" && $dbwhere02 != "") {
            $sql2 = " where $dbwhere and $dbwhere02";
        } else if ($dbwhere != "" && $dbwhere02 == "") {
            $sql2 = " where $dbwhere ";
        } else if ($dbwhere == "" && $dbwhere02 != "") {
            $sql2 = " where $dbwhere02";
        }
        $sql2 .= ($sql2 != "" ? " and " : " where ") . " c61_instit = " . db_getsession("DB_instit");
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

    public function sql_query_rec($e83_codtipo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from empagetipo ";
        $sql .= " 	    inner join conplanoreduz on c61_reduz = e83_conta and c61_anousu = " . db_getsession("DB_anousu") . " and c61_instit=" . db_getsession("DB_instit");
        $sql .= " 	    inner join saltes        on c61_reduz = k13_reduz ";
        $sql .= " 	    inner join conplanoconta on c63_codcon = c61_codcon and c63_anousu = c61_anousu and c61_reduz = c63_reduz";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($e83_codtipo != null) {
                $sql2 .= " where empagetipo.e83_codtipo = $e83_codtipo ";
            }
        } else if ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql2 .= ($sql2 != "" ? " and " : " where ") . " c61_instit = " . db_getsession("DB_instit");
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

    public function sql_query_reduz($e83_codtipo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $anousu = db_getsession("DB_anousu");
        $instituicao = db_getsession("DB_instit");

        $sql = "
         select $campos
          from empagetipo
          join saltes on k13_conta = e83_conta
          join conplanoreduz on c61_reduz = k13_reduz and c61_anousu = {$anousu} and c61_instit = {$instituicao}
          join fonterecurso on fonterecurso.orctiporec_id = c61_codigo and exercicio = {$anousu}
          left  join conplanoconta on c63_codcon = c61_codcon
                and c63_reduz = c61_reduz
                and c63_anousu = c61_anousu
        ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($e83_codtipo != null) {
                $sql2 .= " where empagetipo.e83_codtipo = $e83_codtipo ";
            }
        } else if ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql2 .= ($sql2 != "" ? " and " : " where ") . " c61_instit = " . $instituicao;
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


    public function sql_query_movimento($sCampo = "*", $sWhere = null, $sOrdem = null)
    {

        $sql = " select {$sCampo}";
        $sql .= "   from empagetipo ";
        $sql .= "        inner join empagepag on empagepag.e85_codtipo = empagetipo .e83_codtipo ";
        $sql .= "        inner join empagemov on empagemov.e81_codmov = empagepag.e85_codmov";

        if (!empty($sWhere)) {
            $sql .= " where {$sWhere} ";
        }

        if (!empty($sOrdem)) {
            $sql .= " order by {$sOrdem} ";
        }
        return $sql;
    }
}
