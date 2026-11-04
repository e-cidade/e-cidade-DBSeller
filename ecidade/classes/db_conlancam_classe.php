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

//MODULO: contabilidade
//CLASSE DA ENTIDADE conlancam

class cl_conlancam
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
    // cria variaveis do arquivo
    public $c70_codlan = 0;
    public $c70_anousu = 0;
    public $c70_data_dia = null;
    public $c70_data_mes = null;
    public $c70_data_ano = null;
    public $c70_data = null;
    public $c70_valor = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 c70_codlan = int4 = Código Lançamento
                 c70_anousu = int4 = Exercício
                 c70_data = date = Data
                 c70_valor = float8 = Valor do Lançamento
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("conlancam");
        $this->pagina_retorno = basename($_SERVER["PHP_SELF"]);
    }

    public function erro($mostra, $retorna)
    {

        if (($this->erro_status == "0") || ($mostra == true && $this->erro_status != null)) {
            echo "<script>alert(\"" . $this->erro_msg . "\");</script>";
            if ($retorna == true) {
                echo "<script>location.href='" . $this->pagina_retorno . "'</script>";
            }
        }
    }

    public function atualizacampos($exclusao = false)
    {

        if ($exclusao == false) {
            $this->c70_codlan = ($this->c70_codlan == "" ? @$GLOBALS["HTTP_POST_VARS"]["c70_codlan"] : $this->c70_codlan);
            $this->c70_anousu = ($this->c70_anousu == "" ? @$GLOBALS["HTTP_POST_VARS"]["c70_anousu"] : $this->c70_anousu);
            if ($this->c70_data == "") {
                $this->c70_data_dia = ($this->c70_data_dia == "" ? @$GLOBALS["HTTP_POST_VARS"]["c70_data_dia"] : $this->c70_data_dia);
                $this->c70_data_mes = ($this->c70_data_mes == "" ? @$GLOBALS["HTTP_POST_VARS"]["c70_data_mes"] : $this->c70_data_mes);
                $this->c70_data_ano = ($this->c70_data_ano == "" ? @$GLOBALS["HTTP_POST_VARS"]["c70_data_ano"] : $this->c70_data_ano);
                if ($this->c70_data_dia != "") {
                    $this->c70_data = $this->c70_data_ano . "-" . $this->c70_data_mes . "-" . $this->c70_data_dia;
                }
            }
            $this->c70_valor = ($this->c70_valor == "" ? @$GLOBALS["HTTP_POST_VARS"]["c70_valor"] : $this->c70_valor);
        } else {
            $this->c70_codlan = ($this->c70_codlan == "" ? @$GLOBALS["HTTP_POST_VARS"]["c70_codlan"] : $this->c70_codlan);
        }
    }


    public function incluir($c70_codlan)
    {

        $this->atualizacampos();
        if ($this->c70_anousu == null) {
            $this->erro_sql = " Campo Exercício nao Informado.";
            $this->erro_campo = "c70_anousu";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n")
            );
            $this->erro_status = "0";

            return false;
        }
        if ($this->c70_data == null) {
            $this->erro_sql = " Campo Data nao Informado.";
            $this->erro_campo = "c70_data_dia";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n")
            );
            $this->erro_status = "0";

            return false;
        }
        if ($this->c70_valor == null) {
            $this->erro_sql = " Campo Valor do Lançamento nao Informado.";
            $this->erro_campo = "c70_valor";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n")
            );
            $this->erro_status = "0";

            return false;
        }
        if ($c70_codlan == "" || $c70_codlan == null) {
            $result = db_query("select nextval('conlancam_c70_codlan_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: conlancam_c70_codlan_seq do campo: c70_codlan";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n")
                );
                $this->erro_status = "0";

                return false;
            }
            $this->c70_codlan = pg_fetch_result($result, 0, 0);
        } else {
            $result = db_query("SELECT last_value FROM conlancam_c70_codlan_seq");
            if (($result != false) && (pg_fetch_result($result, 0, 0) < $c70_codlan)) {
                $this->erro_sql = " Campo c70_codlan maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n")
                );
                $this->erro_status = "0";

                return false;
            } else {
                $this->c70_codlan = $c70_codlan;
            }
        }
        if (($this->c70_codlan == null) || ($this->c70_codlan == "")) {
            $this->erro_sql = " Campo c70_codlan nao declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n")
            );
            $this->erro_status = "0";

            return false;
        }
        $sql = "insert into conlancam(
                                       c70_codlan
                                      ,c70_anousu
                                      ,c70_data
                                      ,c70_valor
                       )
                values (
                                $this->c70_codlan
                               ,$this->c70_anousu
                               ," . ($this->c70_data == "null" || $this->c70_data == "" ? "null" : "'" . $this->c70_data . "'") . "
                               ,$this->c70_valor
                      )";

        $this->extrai_c70codlan($sql);
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "Lançamentos Contábeis ($this->c70_codlan) nao Incluído. Inclusao Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "Lançamentos Contábeis já Cadastrado";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n")
                );
            } else {
                $this->erro_sql = "Lançamentos Contábeis ($this->c70_codlan) nao Incluído. Inclusao Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n")
                );
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;

            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : " . $this->c70_codlan;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace(
            '"',
            "",
            str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n")
        );
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (isset($lSessaoDesativarAccount) && $lSessaoDesativarAccount === false) {
            $resaco = $this->sql_record($this->sql_query_file($this->c70_codlan));
            if (($resaco != false) || ($this->numrows != 0)) {
                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_fetch_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                $resac = db_query("insert into db_acountkey values($acount,5217,'$this->c70_codlan','I')");
                $resac = db_query("insert into db_acount values($acount,760,5217,'','" . addSlashes(pg_fetch_result(
                    $resaco,
                    0,
                    'c70_codlan'
                )) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,760,5218,'','" . addSlashes(pg_fetch_result(
                    $resaco,
                    0,
                    'c70_anousu'
                )) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,760,5219,'','" . addSlashes(pg_fetch_result(
                    $resaco,
                    0,
                    'c70_data'
                )) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,760,5839,'','" . addSlashes(pg_fetch_result(
                    $resaco,
                    0,
                    'c70_valor'
                )) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            }
        }

        return true;
    }

    // funcao para alteracao

    public function alterar($c70_codlan = null)
    {

        $this->atualizacampos();
        $sql = " UPDATE conlancam SET ";
        $virgula = "";
        if (trim($this->c70_codlan) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c70_codlan"])) {
            $sql .= $virgula . " c70_codlan = $this->c70_codlan ";
            $virgula = ",";
            if (trim($this->c70_codlan) == null) {
                $this->erro_sql = " Campo Código Lançamento nao Informado.";
                $this->erro_campo = "c70_codlan";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n")
                );
                $this->erro_status = "0";

                return false;
            }
        }
        if (trim($this->c70_anousu) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c70_anousu"])) {
            $sql .= $virgula . " c70_anousu = $this->c70_anousu ";
            $virgula = ",";
            if (trim($this->c70_anousu) == null) {
                $this->erro_sql = " Campo Exercício nao Informado.";
                $this->erro_campo = "c70_anousu";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n")
                );
                $this->erro_status = "0";

                return false;
            }
        }
        if (trim($this->c70_data) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c70_data_dia"]) && ($GLOBALS["HTTP_POST_VARS"]["c70_data_dia"] != "")) {
            $sql .= $virgula . " c70_data = '$this->c70_data' ";
            $virgula = ",";
            if (trim($this->c70_data) == null) {
                $this->erro_sql = " Campo Data nao Informado.";
                $this->erro_campo = "c70_data_dia";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n")
                );
                $this->erro_status = "0";

                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["c70_data_dia"])) {
                $sql .= $virgula . " c70_data = null ";
                $virgula = ",";
                if (trim($this->c70_data) == null) {
                    $this->erro_sql = " Campo Data nao Informado.";
                    $this->erro_campo = "c70_data_dia";
                    $this->erro_banco = "";
                    $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                    $this->erro_msg .= str_replace(
                        '"',
                        "",
                        str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n")
                    );
                    $this->erro_status = "0";

                    return false;
                }
            }
        }
        if (trim($this->c70_valor) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c70_valor"])) {
            $sql .= $virgula . " c70_valor = $this->c70_valor ";
            $virgula = ",";
            if (trim($this->c70_valor) == null) {
                $this->erro_sql = " Campo Valor do Lançamento nao Informado.";
                $this->erro_campo = "c70_valor";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n")
                );
                $this->erro_status = "0";

                return false;
            }
        }
        $sql .= " where ";
        if ($c70_codlan != null) {
            $sql .= " c70_codlan = $this->c70_codlan";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (isset($lSessaoDesativarAccount) && $lSessaoDesativarAccount === false) {
            $resaco = $this->sql_record($this->sql_query_file($this->c70_codlan));
            if ($this->numrows > 0) {
                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {
                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_fetch_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,5217,'$this->c70_codlan','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c70_codlan"])) {
                        $resac = db_query("insert into db_acount values($acount,760,5217,'" . addSlashes(pg_fetch_result(
                            $resaco,
                            $conresaco,
                            'c70_codlan'
                        )) . "','$this->c70_codlan'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c70_anousu"])) {
                        $resac = db_query("insert into db_acount values($acount,760,5218,'" . addSlashes(pg_fetch_result(
                            $resaco,
                            $conresaco,
                            'c70_anousu'
                        )) . "','$this->c70_anousu'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c70_data"])) {
                        $resac = db_query("insert into db_acount values($acount,760,5219,'" . addSlashes(pg_fetch_result(
                            $resaco,
                            $conresaco,
                            'c70_data'
                        )) . "','$this->c70_data'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c70_valor"])) {
                        $resac = db_query("insert into db_acount values($acount,760,5839,'" . addSlashes(pg_fetch_result(
                            $resaco,
                            $conresaco,
                            'c70_valor'
                        )) . "','$this->c70_valor'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                }
            }
        }
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Lançamentos Contábeis nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->c70_codlan;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n")
            );
            $this->erro_status = "0";
            $this->numrows_alterar = 0;

            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Lançamentos Contábeis nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->c70_codlan;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n")
                );
                $this->erro_status = "1";
                $this->numrows_alterar = 0;

                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : " . $this->c70_codlan;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n")
                );
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);

                return true;
            }
        }
    }

    // funcao para exclusao

    public function excluir($c70_codlan = null, $dbwhere = null)
    {

        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (isset($lSessaoDesativarAccount) && $lSessaoDesativarAccount === false) {
            if ($dbwhere == null || $dbwhere == "") {
                $resaco = $this->sql_record($this->sql_query_file($c70_codlan));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
            }
            if (($resaco != false) || ($this->numrows != 0)) {
                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {
                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_fetch_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,5217,'$c70_codlan','E')");
                    $resac = db_query("insert into db_acount values($acount,760,5217,'','" . addSlashes(pg_fetch_result(
                        $resaco,
                        $iresaco,
                        'c70_codlan'
                    )) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,760,5218,'','" . addSlashes(pg_fetch_result(
                        $resaco,
                        $iresaco,
                        'c70_anousu'
                    )) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,760,5219,'','" . addSlashes(pg_fetch_result(
                        $resaco,
                        $iresaco,
                        'c70_data'
                    )) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,760,5839,'','" . addSlashes(pg_fetch_result(
                        $resaco,
                        $iresaco,
                        'c70_valor'
                    )) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }
        $sql = " DELETE FROM conlancam
                    WHERE ";
        $sql2 = "";
        if ($dbwhere == null || $dbwhere == "") {
            if ($c70_codlan != "") {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                }
                $sql2 .= " c70_codlan = $c70_codlan ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Lançamentos Contábeis nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $c70_codlan;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n")
            );
            $this->erro_status = "0";
            $this->numrows_excluir = 0;

            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Lançamentos Contábeis nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $c70_codlan;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n")
                );
                $this->erro_status = "1";
                $this->numrows_excluir = 0;

                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : " . $c70_codlan;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n")
                );
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
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n")
            );
            $this->erro_status = "0";

            return false;
        }
        $this->numrows = pg_num_rows($result);
        if ($this->numrows == 0) {
            $this->erro_banco = "";
            $this->erro_sql = "Record Vazio na Tabela:conlancam";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n")
            );
            $this->erro_status = "0";

            return false;
        }

        return $result;
    }

    public  function sql_query($c70_codlan = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from conlancam ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($c70_codlan != null) {
                $sql2 .= " where conlancam.c70_codlan = $c70_codlan ";
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

    public function sql_query_file($c70_codlan = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from conlancam ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($c70_codlan != null) {
                $sql2 .= " where conlancam.c70_codlan = $c70_codlan ";
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

    public function sql_query_trans($c70_codlan = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from conlancam ";
        $sql .= " 	inner join conlancamdoc on c71_codlan = c70_codlan  ";
        $sql .= " 	inner join conhistdoc   on c71_coddoc = c53_coddoc  ";
        $sql .= " 	inner join conlancaminstit   on c02_codlan = c70_codlan  ";
        $sql .= " 	left join conlancamemp  on c75_codlan = c70_codlan  ";
        $sql .= " 	left join empempenho    on c75_numemp = e60_numemp  ";
        $sql .= " 	left join empelemento   on e64_numemp =  e60_numemp ";
        $sql .= " 	left join conlancamrec  on c70_codlan = c74_codlan ";
        $sql .= " 	left join orcreceita    on c74_anousu = o70_anousu and c74_codrec = o70_codrec";
        $sql .= " 	left join conlancampag  on c82_codlan = c70_codlan";
        $sql .= " 	left join conlancamele  on c67_codlan = c70_codlan";
        $sql .= "   left join conlancamord on (c80_codlan) = (c70_codlan)";

        $sql2 = "";
        if ($dbwhere == "") {
            if ($c70_codlan != null) {
                $sql2 .= " where conlancam.c70_codlan = $c70_codlan ";
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

    /**
     *
     * Retorna os movimentos contábil para o Arquivo TXT fo Sigfis
     *
     * @return string
     */
    public function sql_movimentoContabilSigfis($iAnoUsu, $iInstit, $dtDataInicial, $dtDataFinal)
    {

        $sSql = "select competencia,                                                                                       ";
        $sSql .= "       sum(case when tipo = 'C' then c69_valor else 0 end) as valor_credito,                              ";
        $sSql .= "       sum(case when tipo = 'D' then c69_valor else 0 end) as valor_debito,                               ";
        $sSql .= "       conta,                                                                                             ";
        $sSql .= "       tipo_movimento,                                                                                    ";
        $sSql .= "       estrutural                                                                                         ";
        $sSql .= "  from (SELECT to_char(c70_data,'YYYYmm') as competencia,                                                 ";
        $sSql .= "               (case c71_coddoc when 1000 then 2                                                          ";
        $sSql .= "               when 2000 then 1                                                                           ";
        $sSql .= "               else 3 end ) as tipo_movimento,                                                            ";
        $sSql .= "               planocredito.c60_codcon as conta,                                                          ";
        $sSql .= "               c69_valor,                                                                                 ";
        $sSql .= "               'C' as tipo, 																																							";
        $sSql .= "               planocredito.c60_estrut as estrutural                                                      ";
        $sSql .= "          from conlancamval                                                                               ";
        $sSql .= "               inner join conlancam                  on c69_codlan = c70_codlan                           ";
        $sSql .= "               inner join conlancamdoc               on c71_codlan = c70_codlan                           ";
        $sSql .= "               inner join conplanoreduz reduzcredito on reduzcredito.c61_reduz  = c69_credito             ";
        $sSql .= "                                                    and reduzcredito.c61_anousu = c69_anousu              ";
        $sSql .= "                                                    and reduzcredito.c61_instit = $iInstit                ";
        $sSql .= "               inner join conplano planocredito      on planocredito.c60_codcon = reduzcredito.c61_codcon ";
        $sSql .= "               																			and planocredito.c60_anousu = reduzcredito.c61_anousu ";
        $sSql .= "         where c70_data between cast('{$dtDataInicial}' as date) and  cast('{$dtDataFinal}' as date)      ";
        $sSql .= "           and c70_anousu = {$iAnoUsu}                                                                    ";
        $sSql .= "         union                                                                                            ";
        $sSql .= "        SELECT to_char(c70_data,'YYYYmm') as competencia,                                                 ";
        $sSql .= "               (case c71_coddoc when 1000 then 2                                                          ";
        $sSql .= "               when 2000 then 1                                                                           ";
        $sSql .= "               else 3 end ) as tipo_movimento,                                                            ";
        $sSql .= "               planodebito.c60_codcon as conta,                                                           ";
        $sSql .= "               c69_valor,                                                                                 ";
        $sSql .= "               'D' as tipo,                                                                    					  ";
        $sSql .= "               planodebito.c60_estrut as estrutural                                                       ";
        $sSql .= "          from conlancamval                                                                               ";
        $sSql .= "               inner join conlancam                  on c69_codlan = c70_codlan                           ";
        $sSql .= "               inner join conlancamdoc               on c71_codlan = c70_codlan                           ";
        $sSql .= "               inner join conplanoreduz reduzdebito  on reduzdebito.c61_reduz   = c69_debito              ";
        $sSql .= "               																		  and reduzdebito.c61_anousu  = c69_anousu              ";
        $sSql .= "                                                    and reduzdebito.c61_instit = $iInstit                 ";
        $sSql .= "               inner join conplano planodebito       on planodebito.c60_codcon  = reduzdebito.c61_codcon  ";
        $sSql .= "               																			and planodebito.c60_anousu  = reduzdebito.c61_anousu  ";
        $sSql .= "         where c70_data between cast('{$dtDataInicial}' as date) and  cast('{$dtDataFinal}' as date)      ";
        $sSql .= "           and c70_anousu = {$iAnoUsu}) lanc                                                              ";
        $sSql .= "   group by conta,                                                                                				";
        $sSql .= "            competencia,                                                                                  ";
        $sSql .= "            tipo_movimento,                                                                               ";
        $sSql .= "            estrutural                                                                                    ";
        $sSql .= "   order by estrutural 																																										";

        return $sSql;
    }

    public function sql_query_empenho($c70_codlan = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from conlancam ";
        $sql .= " 	inner join conlancamval            on c69_codlan = c70_codlan";
        $sql .= " 	inner join conlancamdoc            on c71_codlan = c70_codlan  ";
        $sql .= " 	inner join conhistdoc              on c71_coddoc = c53_coddoc  ";
        $sql .= " 	inner join conlancamemp            on c75_codlan = c70_codlan  ";
        $sql .= "   inner join empempenho              on c75_numemp = e60_numemp  ";
        $sql .= "   inner join empelemento             on e64_numemp =  e60_numemp ";
        $sql .= "   left  join conlancamele            on c67_codlan = c70_codlan  ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($c70_codlan != null) {
                $sql2 .= " where conlancam.c70_codlan = $c70_codlan ";
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


    public function sql_query_empenho_cgm($c70_codlan = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from conlancam ";
        $sql .= " 	left join conlancamemp            on c75_codlan = conlancam.c70_codlan  ";
        $sql .= " 	left join empempenho 							on e60_numemp = conlancamemp.c75_numemp ";
        $sql .= " 	left join conlancamcgm            on c76_codlan = conlancam.c70_codlan  ";
        $sql .= " 	left join conlancamconcarpeculiar on c08_codlan = conlancam.c70_codlan ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($c70_codlan != null) {
                $sql2 .= " where conlancam.c70_codlan = $c70_codlan ";
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


    public function sql_query_lancamento_requisicao_material($c70_codlan = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from conlancam ";
        $sql .= "      inner join conlancammatestoqueinimei on conlancammatestoqueinimei.c103_conlancam    = conlancam.c70_codlan";
        $sql .= "      inner join conlancamval              on conlancamval.c69_codlan                     = conlancam.c70_codlan";
        $sql .= "      inner join conlancamdoc              on conlancamdoc.c71_codlan                     = conlancam.c70_codlan";
        $sql .= "      inner join conhistdoc                on conhistdoc.c53_coddoc                       = conlancamdoc.c71_coddoc";
        $sql .= "      inner join matestoqueinimei          on matestoqueinimei.m82_codigo                 = conlancammatestoqueinimei.c103_matestoqueinimei";
        $sql .= "      inner join matestoqueini             on matestoqueini.m80_codigo                    = matestoqueinimei.m82_matestoqueini";
        $sql .= "      inner join matestoqueinimeiari       on matestoqueinimeiari.m49_codmatestoqueinimei = matestoqueinimei.m82_codigo";
        $sql .= "      inner join atendrequiitem            on atendrequiitem.m43_codigo                   = matestoqueinimeiari.m49_codatendrequiitem";
        $sql .= "      inner join matrequiitem              on matrequiitem.m41_codigo                     = atendrequiitem.m43_codmatrequiitem";
        $sql .= "      inner join matrequi                  on matrequi.m40_codigo                         = matrequiitem.m41_codmatrequi";
        $sql .= "      inner join db_depart                 on db_depart.coddepto                          = matrequi.m40_depto";
        $sql .= "      inner join matmater                  on matmater.m60_codmater                       = matrequiitem.m41_codmatmater";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($c70_codlan != null) {
                $sql2 .= " where conlancam.c70_codlan = $c70_codlan ";
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

    public function sql_query_lancamento_saida_manual($c70_codlan = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from conlancam ";
        $sql .= "      inner join conlancamval              on conlancamval.c69_codlan                     = conlancam.c70_codlan";
        $sql .= "      inner join conlancamdoc              on conlancamdoc.c71_codlan                     = conlancam.c70_codlan";
        $sql .= "      inner join conlancammatestoqueinimei on conlancammatestoqueinimei.c103_conlancam    = conlancam.c70_codlan";
        $sql .= "      inner join matestoqueinimei          on matestoqueinimei.m82_codigo                 = conlancammatestoqueinimei.c103_matestoqueinimei";
        $sql .= "      inner join matestoqueini             on matestoqueini.m80_codigo                    = matestoqueinimei.m82_matestoqueini";
        $sql .= "      inner join matestoqueitem            on matestoqueitem.m71_codlanc                  = matestoqueinimei.m82_matestoqueitem";
        $sql .= "      inner join matestoque                on matestoque.m70_codigo                       = matestoqueitem.m71_codmatestoque";
        $sql .= "      inner join matmater                  on matmater.m60_codmater                       = matestoque.m70_codmatmater";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($c70_codlan != null) {
                $sql2 .= " where conlancam.c70_codlan = $c70_codlan ";
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

    public function sql_query_lancamento_estorno_saida_manual($c70_codlan = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from conlancam ";
        $sql .= "      inner join conlancamval              on conlancamval.c69_codlan                     = conlancam.c70_codlan";
        $sql .= "      inner join conlancamdoc              on conlancamdoc.c71_codlan                     = conlancam.c70_codlan";
        $sql .= "      inner join conlancammatestoqueinimei on conlancammatestoqueinimei.c103_conlancam    = conlancam.c70_codlan";
        $sql .= "      inner join matestoqueinimeimdi       on matestoqueinimeimdi.m50_codmatestoqueinimei = conlancammatestoqueinimei.c103_matestoqueinimei";
        $sql .= "      inner join matestoquedevitem         on matestoquedevitem.m46_codigo                = matestoqueinimeimdi.m50_codmatestoquedevitem";
        $sql .= "      inner join matrequiitem              on matrequiitem.m41_codigo                     = matestoquedevitem.m46_codmatrequiitem";
        $sql .= "      inner join matestoqueinimei          on matestoqueinimei.m82_codigo                 = matestoqueinimeimdi.m50_codmatestoqueinimei";
        $sql .= "      inner join matestoqueini             on matestoqueini.m80_codigo                    = matestoqueinimei.m82_matestoqueini";
        $sql .= "      inner join matestoqueitem            on matestoqueitem.m71_codlanc                  = matestoqueinimei.m82_matestoqueitem";
        $sql .= "      inner join matestoque                on matestoque.m70_codigo                       = matestoqueitem.m71_codmatestoque";
        $sql .= "      inner join matmater                  on matmater.m60_codmater                       = matestoque.m70_codmatmater";

        $sql2 = "";
        if ($dbwhere == "") {
            if ($c70_codlan != null) {
                $sql2 .= " where conlancam.c70_codlan = $c70_codlan ";
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

    public function sql_query_reprocessamento($c70_codlan = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= "        from conlancam                                               ";
        $sql .= "  inner join conlancamdoc              on c71_codlan = c70_codlan    ";
        $sql .= " 	left join conlancamemp              on c75_codlan = c70_codlan    ";
        $sql .= " 	left join conlancamacordo           on c70_codlan = c87_codlan    ";
        $sql .= "   left join conlancaminscricaopassivo on c70_codlan = c37_conlancam ";
        //$sql .= "     left join empempenho                on c75_numemp = e60_numemp    ";

        $sql2 = "";
        if ($dbwhere == "") {
            if ($c70_codlan != null) {
                $sql2 .= " where conlancam.c70_codlan = $c70_codlan ";
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


    public function sql_query_reprocessaMovimentacaoPatrimonial(
        $c70_codlan = null,
        $campos = "*",
        $ordem = null,
        $dbwhere = "",
        $innerJoin = true
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

        $sJoin = $innerJoin ? 'inner' : 'left';

        $sql .= "        from conlancamdoc                                                      ";
        $sql .= "  inner join conlancam    on conlancam.c70_codlan    = conlancamdoc.c71_codlan ";
        $sql .= "  inner join conlancamval on conlancamval.c69_codlan = conlancam.c70_codlan    ";

        /**
         * Valida instituicao do lancamento pelas conta credito e debito
         */
        $sql .= "  inner join conplanoreduz a  on a.c61_reduz          = conlancamval.c69_debito  ";
        $sql .= "                             and a.c61_anousu         = " . db_getsession("DB_anousu");
        $sql .= "                             and a.c61_instit         = " . db_getsession("DB_instit");
        $sql .= "  inner join conplanoreduz b  on b.c61_reduz          = conlancamval.c69_credito  ";
        $sql .= "                             and b.c61_anousu         = " . db_getsession("DB_anousu");
        $sql .= "                             and b.c61_instit         = " . db_getsession("DB_instit");
        $sql .= "  inner join conlancamcompl   on conlancam.c70_codlan = conlancamcompl.c72_codlan ";

        $sql .= "  $sJoin join conlancamnota                on conlancamnota.c66_codlan                           = conlancamdoc.c71_codlan                                ";
        $sql .= "  $sJoin join conlancamemp                 on conlancamemp.c75_codlan                            = conlancamnota.c66_codlan                               ";
        $sql .= "  $sJoin join empnota                      on empnota.e69_codnota                                = conlancamnota.c66_codnota                              ";
        $sql .= "  $sJoin join empnotaord                   on empnotaord.m72_codnota                             = empnota.e69_codnota                                    ";
        $sql .= "  $sJoin join matordemitem                 on matordemitem.m52_codordem                          = empnotaord.m72_codordem                                ";
        $sql .= "  $sJoin join matestoqueitemoc             on matestoqueitemoc.m73_codmatordemitem               = matordemitem.m52_codlanc                               ";
        $sql .= "  $sJoin join matestoqueitem               on matestoqueitem.m71_codlanc                         = matestoqueitemoc.m73_codmatestoqueitem                 ";
        $sql .= "  $sJoin join matestoque                   on matestoque.m70_codigo                              = matestoqueitem.m71_codmatestoque                       ";
        $sql .= "  $sJoin join matmater                     on matmater.m60_codmater                              = matestoque.m70_codmatmater                             ";
        $sql .= "  $sJoin join matmatermaterialestoquegrupo on matmatermaterialestoquegrupo.m68_matmater          = matmater.m60_codmater                                  ";
        $sql .= "  $sJoin join materialestoquegrupo         on materialestoquegrupo.m65_sequencial                = matmatermaterialestoquegrupo.m68_materialestoquegrupo  ";
        $sql .= "  $sJoin join materialestoquegrupoconta    on materialestoquegrupoconta.m66_materialestoquegrupo = materialestoquegrupo.m65_sequencial                    ";

        $sql2 = "";
        if ($dbwhere == "") {
            if ($c70_codlan != null) {
                $sql2 .= " where conlancam.c70_codlan = $c70_codlan ";
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

    public function sql_query_reprocessaExtraOrcamentario($c70_codlan = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= "  from conlancamdoc";
        $sql .= "        inner join conlancam          on conlancam.c70_codlan = conlancamdoc.c71_codlan ";
        $sql .= "        inner join conlancamcompl     on conlancam.c70_codlan = conlancamcompl.c72_codlan ";
        $sql .= "        inner join conlancaminstit on conlancaminstit.c02_codlan = conlancam.c70_codlan ";

        $sql .= "        left join conlancamslip      on conlancam.c70_codlan = conlancamslip.c84_conlancam  ";
        $sql .= "        left join slip               on slip.k17_codigo      = conlancamslip.c84_slip       ";


        $sql2 = "";
        if ($dbwhere == "") {
            if ($c70_codlan != null) {
                $sql2 .= " where conlancam.c70_codlan = $c70_codlan ";
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

    /**
     * metodo para query retornar valores de lacamnetos em um periodo
     *
     * @param string $c70_codlan
     * @param string $campos
     * @param string $ordem
     * @param string $dbwhere
     * @return string
     */
    public function sql_query_ValorLancamentoPorDocumentoPeriodo(
        $c70_codlan = null,
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
        $sql .= "        from conlancam   ";
        $sql .= "  inner join conlancamdoc    on conlancam.c70_codlan  = conlancamdoc.c71_codlan    ";
        $sql .= "  inner join conlancaminstit on conlancam.c70_codlan  = conlancaminstit.c02_codlan ";

        $sql2 = "";
        if ($dbwhere == "") {
            if ($c70_codlan != null) {
                $sql2 .= " where conlancam.c70_codlan = $c70_codlan ";
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

    /**
     * @param string $sCampos
     * @param string $sOrdem
     * @param string $sWhere
     * @return string
     */
    public function sql_query_lancamentos_documento($sCampos = '*', $sOrdem = null, $sWhere = null)
    {

        $sSql = " select $sCampos                                                                   ";
        $sSql .= "        from conlancam                                                             ";
        $sSql .= "  inner join conlancamdoc    on conlancamdoc.c71_codlan    = conlancam.c70_codlan  ";
        $sSql .= "  inner join conlancaminstit on conlancaminstit.c02_codlan = conlancam.c70_codlan  ";
        $sSql .= "  left  join conlancamcompl  on conlancamcompl.c72_codlan  = conlancam.c70_codlan  ";
        $sSql .= "  inner join db_config       on db_config.codigo = conlancaminstit.c02_instit      ";

        // para testar se documento eh de estorno
        $sSql .= "  left join vinculoeventoscontabeis on c115_conhistdocestorno = c71_coddoc ";

        // dotacao
        $sSql .= "  left join conlancamdot on conlancamdot.c73_codlan = conlancam.c70_codlan ";
        $sSql .= "  left join orcdotacao   on orcdotacao.o58_coddot = conlancamdot.c73_coddot ";
        $sSql .= "                        and orcdotacao.o58_anousu = conlancamdot.c73_anousu ";

        if (!empty($sWhere)) {
            $sSql .= " where $sWhere ";
        }

        if (!empty($sOrdem)) {
            $sSql .= " order by $sOrdem ";
        }

        return $sSql;
    }

    public function sql_query_empenho_lancamento($sCampos = '*', $sOrdem = null, $sWhere = null)
    {

        $sSql = "select {$sCampos}                                        ";
        $sSql .= " from conlancam                                          ";
        $sSql .= " 	    inner join conlancamemp on c75_codlan = c70_codlan ";

        if (!empty($sWhere)) {
            $sSql .= " where $sWhere ";
        }

        if (!empty($sOrdem)) {
            $sSql .= " order by $sOrdem ";
        }

        return $sSql;
    }


    public function sql_query_despesa($sCampos = '*', $sOrdem = null, $sWhere = null)
    {

        $sSql = "select {$sCampos} ";
        $sSql .= "  from conlancam  ";
        $sSql .= "       inner join conlancamdot on conlancamdot.c73_codlan = conlancam.c70_codlan ";
        $sSql .= "       inner join conlancamdoc on conlancamdoc.c71_codlan = conlancam.c70_codlan ";
        $sSql .= "       inner join conhistdoc   on conhistdoc.c53_coddoc   = conlancamdoc.c71_coddoc ";
        $sSql .= "       inner join orcdotacao   on orcdotacao.o58_coddot   = conlancamdot.c73_coddot ";
        $sSql .= "                              and orcdotacao.o58_anousu   = conlancamdot.c73_anousu ";

        if (!empty($sWhere)) {
            $sSql .= " where $sWhere ";
        }

        if (!empty($sOrdem)) {
            $sSql .= " order by $sOrdem ";
        }

        return $sSql;
    }


    public function sql_query_conta_corrente($sCampos = '*', $sOrdem = null, $sWhere = null)
    {

        $sSql = "select {$sCampos} ";
        $sSql .= "  from conlancam ";
        $sSql .= "        inner join conlancamval  on c69_codlan = c70_codlan";
        $sSql .= "        inner join conlancamdoc  on c71_codlan = c70_codlan";
        $sSql .= "        inner join conplanoreduz on c61_anousu = c69_anousu";
        $sSql .= "                                and ((c61_reduz = c69_credito) or (c61_reduz = c69_debito))";
        $sSql .= "        inner join conplano      on c60_codcon = c61_codcon";
        $sSql .= "                                and c60_anousu = c61_anousu";
        $sSql .= "        inner join conplanocontacorrente on c18_codcon = c60_codcon";
        $sSql .= "                                        and c18_anousu = c60_anousu";

        if (!empty($sWhere)) {
            $sSql .= " where $sWhere ";
        }

        if (!empty($sOrdem)) {
            $sSql .= " order by $sOrdem ";
        }

        return $sSql;
    }

    public function sql_query_conta($sCampos = '*', $sOrdem = null, $sWhere = null)
    {

        $sSql = "select {$sCampos} ";
        $sSql .= "  from conlancam ";
        $sSql .= "        inner join conlancamval  on c69_codlan = c70_codlan";
        $sSql .= "        inner join conlancamdoc  on c71_codlan = c70_codlan";
        $sSql .= "        inner join conplanoreduz on c61_anousu = c69_anousu";
        $sSql .= "                                and ((c61_reduz = c69_credito) or (c61_reduz = c69_debito))";
        $sSql .= "        inner join conplano      on c60_codcon = c61_codcon";
        $sSql .= "                                and c60_anousu = c61_anousu";
        $sSql .= "        inner join conlancaminstit on c02_codlan = c70_codlan";

        if (!empty($sWhere)) {
            $sSql .= " where $sWhere ";
        }

        if (!empty($sOrdem)) {
            $sSql .= " order by $sOrdem ";
        }

        return $sSql;
    }


    public function sql_query_reprocessaMovimentacaoBensPatrimonial(
        $c70_codlan = null,
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

        $sql .= "        from conlancamdoc                                                      ";
        $sql .= "  inner join conlancam    on conlancam.c70_codlan    = conlancamdoc.c71_codlan ";
        $sql .= "  inner join conlancamval on conlancamval.c69_codlan = conlancam.c70_codlan    ";

        /**
         * Valida instituicao do lancamento pelas conta credito e debito
         */
        $sql .= "  inner join conplanoreduz a  on a.c61_reduz          = conlancamval.c69_debito  ";
        $sql .= "                             and a.c61_anousu         = " . db_getsession("DB_anousu");
        $sql .= "                             and a.c61_instit         = " . db_getsession("DB_instit");
        $sql .= "  inner join conplanoreduz b  on b.c61_reduz          = conlancamval.c69_credito  ";
        $sql .= "                             and b.c61_anousu         = " . db_getsession("DB_anousu");
        $sql .= "                             and b.c61_instit         = " . db_getsession("DB_instit");
        $sql .= "  left join conlancamcompl   on conlancam.c70_codlan = conlancamcompl.c72_codlan ";

        $sql .= "  left join conlancamnota                on conlancamnota.c66_codlan                           = conlancamdoc.c71_codlan                                ";
        $sql .= "  left join conlancamemp                 on conlancamemp.c75_codlan                            = conlancamnota.c66_codlan                               ";
        $sql .= "  left join empnota                      on empnota.e69_codnota                                = conlancamnota.c66_codnota                              ";
        $sql .= "  left join empnotaord                   on empnotaord.m72_codnota                             = empnota.e69_codnota                                    ";
        $sql .= "  left join matordemitem                 on matordemitem.m52_codordem                          = empnotaord.m72_codordem                                ";
        $sql .= "  left join matestoqueitemoc             on matestoqueitemoc.m73_codmatordemitem               = matordemitem.m52_codlanc                               ";
        $sql .= "  left join matestoqueitem               on matestoqueitem.m71_codlanc                         = matestoqueitemoc.m73_codmatestoqueitem                 ";
        $sql .= "  left join matestoque                   on matestoque.m70_codigo                              = matestoqueitem.m71_codmatestoque                       ";
        $sql .= "  left join matmater                     on matmater.m60_codmater                              = matestoque.m70_codmatmater                             ";
        $sql .= "  left join matmatermaterialestoquegrupo on matmatermaterialestoquegrupo.m68_matmater          = matmater.m60_codmater                                  ";
        $sql .= "  left join materialestoquegrupo         on materialestoquegrupo.m65_sequencial                = matmatermaterialestoquegrupo.m68_materialestoquegrupo  ";
        $sql .= "  left join materialestoquegrupoconta    on materialestoquegrupoconta.m66_materialestoquegrupo = materialestoquegrupo.m65_sequencial                    ";

        $sql2 = "";
        if ($dbwhere == "") {
            if ($c70_codlan != null) {
                $sql2 .= " where conlancam.c70_codlan = $c70_codlan ";
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

    public function sql_query_despesa_orcamentaria($sCampos = '*', $sWhere = null)
    {

        $sql = "select {$sCampos} ";
        $sql .= "  from conlancam   ";
        $sql .= "       inner join conlancaminstit on conlancaminstit.c02_codlan = conlancam.c70_codlan ";
        $sql .= "       inner join conlancamdoc    on conlancamdoc.c71_codlan = conlancam.c70_codlan    ";
        $sql .= "       inner join conhistdoc      on conhistdoc.c53_coddoc   = conlancamdoc.c71_coddoc ";
        $sql .= "       inner join conlancamemp    on conlancamemp.c75_codlan = conlancam.c70_codlan    ";
        $sql .= "       inner join empempenho      on empempenho.e60_numemp   = conlancamemp.c75_numemp ";
        $sql .= "       inner join empelemento     on empelemento.e64_numemp  = empempenho.e60_numemp   ";
        $sql .= "       inner join orcelemento     on orcelemento.o56_codele  = empelemento.e64_codele  ";
        $sql .= "                                 and orcelemento.o56_anousu  = conlancam.c70_anousu    ";

        if (!empty($sWhere)) {
            $sql .= " where {$sWhere}";
        }

        return $sql;
    }

    public function sql_query_lancamentos_by_competencia($sCampos_1 = '*', $sCampos_2, $sWhere = null, $sOrderBy = null)
    {

        if (empty($sCampos_2)) {
            $sCampos_2 = $sCampos_1;
        }
        $ordem = "order by data_lancamento, estrutura";
        if (!empty($sOrderBy)) {
            $ordem = " order by " . $sOrderBy;
        }

        $sql = " select {$sCampos_1}                                                                                         ";
        $sql .= "    from contabilidade.conlancam                                                                            ";
        $sql .= "         inner join contabilidade.conlancamval              on c69_codlan             = c70_codlan          ";
        $sql .= "         inner join contabilidade.conlancamdoc              on c71_codlan             = c70_codlan          ";
        $sql .= "         inner join contabilidade.conhistdoc                on c53_coddoc             = c71_coddoc          ";
        $sql .= "         inner join contabilidade.conlancaminstit           on c02_codlan             = c70_codlan          ";
        $sql .= "         inner join contabilidade.conplanoreduz as debito   on debito.c61_reduz       = c69_debito          ";
        $sql .= "                                                           and debito.c61_anousu      = c69_anousu          ";
        $sql .= "         inner join contabilidade.conplano as contadebito   on contadebito.c60_codcon = debito.c61_codcon   ";
        $sql .= "                                                           and contadebito.c60_anousu = debito.c61_anousu   ";
        $sql .= "         inner join contabilidade.conplanoatributos         on c120_conplano   = contadebito.c60_codcon     ";
        $sql .= "                                                           and c120_anousu     = contadebito.c60_anousu     ";
        $sql .= "         inner join contabilidade.conplanoinfocomplementar  on c121_sequencial = c120_infocomplementar      ";
        $sql .= "         inner join contabilidade.conplanosistema           on c122_sequencial = c120_conplanosistema       ";
        $sql .= "         left  join contabilidade.conplanosistemaatributos  on c129_conplanoinfocomplementar = c120_infocomplementar ";
        $sql .= "                                                           and c129_conplanosistema          = c120_conplanosistema ";

        if (!empty($sWhere)) {
            $sql .= " where {$sWhere} ";
        }

        $sql .= " union all                                                                                                  ";
        $sql .= " select {$sCampos_2}                                                                                        ";
        $sql .= "    from contabilidade.conlancam                                                                            ";
        $sql .= "         inner join contabilidade.conlancamval              on c69_codlan              = c70_codlan         ";
        $sql .= "         inner join contabilidade.conlancamdoc              on c71_codlan              = c70_codlan         ";
        $sql .= "         inner join contabilidade.conhistdoc                on c53_coddoc              = c71_coddoc         ";
        $sql .= "         inner join contabilidade.conlancaminstit           on c02_codlan              = c70_codlan         ";
        $sql .= "         inner join contabilidade.conplanoreduz as credito  on credito.c61_reduz       = c69_credito        ";
        $sql .= "                                                           and credito.c61_anousu      = c69_anousu         ";
        $sql .= "         inner join contabilidade.conplano as contacredito  on contacredito.c60_codcon = credito.c61_codcon ";
        $sql .= "                                                           and contacredito.c60_anousu = credito.c61_anousu ";
        $sql .= "         inner join contabilidade.conplanoatributos         on c120_conplano   = contacredito.c60_codcon    ";
        $sql .= "                                                           and c120_anousu     = contacredito.c60_anousu    ";
        $sql .= "         inner join contabilidade.conplanoinfocomplementar  on c121_sequencial = c120_infocomplementar      ";
        $sql .= "         inner join contabilidade.conplanosistema           on c120_conplanosistema = c120_conplanosistema  ";
        $sql .= "         left  join contabilidade.conplanosistemaatributos  on c129_conplanoinfocomplementar = c120_infocomplementar ";
        $sql .= "                                                           and c129_conplanosistema          = c120_conplanosistema ";

        if (!empty($sWhere)) {
            $sql .= " where {$sWhere} ";
        }

        $sql .= " {$ordem}";

        return $sql;
    }


    public function sql_query_nota_lancamento($campos = "*", $where = null, $outrasClausulas = null)
    {

        $sql = " select {$campos} ";
        $sql .= "   from conlancam ";
        $sql .= "        inner join conlancamval on conlancamval.c69_codlan = conlancam.c70_codlan ";
        $sql .= "        inner join conplanoreduz reduzdebito on reduzdebito.c61_reduz = conlancamval.c69_debito ";
        $sql .= "                                            and reduzdebito.c61_anousu = conlancamval.c69_anousu ";
        $sql .= "        inner join conplano planodebito on planodebito.c60_codcon = reduzdebito.c61_codcon ";
        $sql .= "                                       and planodebito.c60_anousu = reduzdebito.c61_anousu ";
        $sql .= "        inner join conplanoreduz reduzcredito on reduzcredito.c61_reduz = conlancamval.c69_credito ";
        $sql .= "                                             and reduzcredito.c61_anousu = conlancamval.c69_anousu ";
        $sql .= "        inner join conplano planocredito on planocredito.c60_codcon = reduzcredito.c61_codcon ";
        $sql .= "                                        and planocredito.c60_anousu = reduzcredito.c61_anousu ";
        $sql .= "        inner join conlancamcompl on conlancamcompl.c72_codlan = conlancam.c70_codlan ";
        $sql .= "        inner join conlancamdoc on conlancamdoc.c71_codlan = conlancam.c70_codlan ";
        $sql .= "        inner join conhistdoc on conhistdoc.c53_coddoc = conlancamdoc.c71_coddoc ";
        $sql .= "        inner join conhist on conhist.c50_codhist = conlancamval.c69_codhist";

        if (!empty($where)) {
            if (is_array($where)) {
                $sql .= " where " . implode(' and ', $where);
            }
            if (is_string($where)) {
                $sql .= " where {$where}";
            }
        }

        if (!empty($outrasClausulas)) {
            $sql .= " " . $outrasClausulas;
        }

        return $sql;
    }

    /**
     * Retorna os recurssos envolvidos no lancamento quando ativado o domicilio bancario
     *
     * @param $codigoLancamento
     * @return string
     */
    public function sql_consulta_origem_recursos($codigoLancamento)
    {
        $sql = "
            with dados_lancamento as (
             select c70_codlan,
                    c71_coddoc,
                    c70_valor,
                    c53_tipo,
                    c70_data
                from conlancam
             inner join conlancamdoc on conlancamdoc.c71_codlan = conlancam.c70_codlan
             inner join conhistdoc on c53_coddoc = conlancamdoc.c71_coddoc
             where c70_codlan = {$codigoLancamento}
            ), recurso_conta_pagadora as (
                select c61_codigo, c69_codlan
                  from dados_lancamento
                  join conlancamval on conlancamval.c69_codlan = dados_lancamento.c70_codlan
                  join conplanoreduz on c61_anousu = c69_anousu
                                    and c61_reduz = (case
                                                        when c71_coddoc in (151, 152, 161, 162) then c69_debito
                                                        when c71_coddoc in (150, 153, 160, 163) then c69_credito
                                                     end
                                                    )
                where c69_ordem = 1
                  and c71_coddoc in (160, 161, 162, 163, 150, 151, 152, 153)
             ), recurso_empenho as (
              select o206_recurso, c75_codlan, c75_numemp, e60_coddot, c71_coddoc, c70_data
                from dados_lancamento
                join conlancamemp on conlancamemp.c75_codlan = dados_lancamento.c70_codlan
                join empempenho on empempenho.e60_numemp = conlancamemp.c75_numemp
                join origemcomplementorecurso on origemcomplementorecurso.o206_numero = empempenho.e60_numemp
                                             and origemcomplementorecurso.o206_origem = 1
            ), recursos_suplementacao as (
              select o58_codigo, c70_codlan
                 from dados_lancamento
                 join conlancamsup on conlancamsup.c79_codlan = dados_lancamento.c70_codlan
                 join conlancamdot on conlancamdot.c73_codlan = conlancamsup.c79_codlan
                 join orcdotacao on orcdotacao.o58_coddot = conlancamdot.c73_coddot
                                and orcdotacao.o58_anousu = conlancamdot.c73_anousu

            ), resto_a_pagar as (
              select e91_recurso as recurso_resto, c75_codlan
                from recurso_empenho
                join empresto on empresto.e91_numemp = recurso_empenho.c75_numemp
                             and empresto.e91_anousu = extract(YEAR from c70_data)
            ), recurso_receita as (
               select o70_codigo as recurso_receita, c70_codlan
                 from dados_lancamento
                 join conlancamrec on conlancamrec.c74_codlan = dados_lancamento.c70_codlan
                 join orcreceita on orcreceita.o70_codrec = conlancamrec.c74_codrec
                                and orcreceita.o70_anousu = conlancamrec.c74_anousu

                union

                select c61_codigo as recurso_receita, c70_codlan
                 from dados_lancamento
                 join conlancamval on conlancamval.c69_codlan = dados_lancamento.c70_codlan
                      and conlancamval.c69_ordem = 1
                 join conplanoreduz on conplanoreduz.c61_reduz = conlancamval.c69_credito
                      and conplanoreduz.c61_anousu = conlancamval.c69_anousu
                 where not exists(
                   select 1 from conlancamrec where conlancamrec.c74_codlan = dados_lancamento.c70_codlan)
            )
            select dados_lancamento.c71_coddoc as documento,
                   dados_lancamento.c70_codlan as lancamento,
                   c84_slip as slip,
                   recurso_conta_pagadora.c61_codigo as recurso_extraorcamentario,
                   recurso_empenho.o206_recurso as recurso_empenho,
                   recursos_suplementacao.o58_codigo as recurso_dotacao_lancamento,
                          resto_a_pagar.recurso_resto,
                   recurso_receita.recurso_receita
             from dados_lancamento
             left join recurso_empenho on recurso_empenho.c75_codlan = dados_lancamento.c70_codlan
             left join recurso_conta_pagadora on recurso_conta_pagadora.c69_codlan = dados_lancamento.c70_codlan
             left join recursos_suplementacao on recursos_suplementacao.c70_codlan = dados_lancamento.c70_codlan
             left join resto_a_pagar on resto_a_pagar.c75_codlan = dados_lancamento.c70_codlan
             left join recurso_receita on recurso_receita.c70_codlan = dados_lancamento.c70_codlan
             left join conlancamslip on conlancamslip.c84_conlancam = dados_lancamento.c70_codlan
        ";

        return $sql;
    }

    /**
     * Retorna os recursos envolvidos no lancamento quando
     * Não ativado o domicílio bancario
     *
     * @param $codigoLancamento
     * @return string
     */
    public function sql_consulta_recursos_lancamentos($codigoLancamento)
    {
        $sql = "
        with dados_lancamento as (
         select c70_codlan,
                c71_coddoc,
                c70_valor,
                c53_tipo,
                c70_data
            from conlancam
         inner join conlancamdoc on conlancamdoc.c71_codlan = conlancam.c70_codlan
         inner join conhistdoc on c53_coddoc = conlancamdoc.c71_coddoc
         where c70_codlan = {$codigoLancamento}
        ), receita_com_desdobramento as (
           select 1
             from dados_lancamento
             join conlancamrec on conlancamrec.c74_codlan = c70_codlan
             join orcreceita on (o70_anousu, o70_codrec) = (c74_anousu, c74_codrec)
             join orcfontesdes on  (o60_codfon, o60_anousu) = (o70_codfon, o70_anousu)
        ), recurso_retencao_receita as (
           select o70_codigo, c70_codlan
             from dados_lancamento
             join conlancamrec on conlancamrec.c74_codlan = c70_codlan
             join orcreceita on (o70_anousu, o70_codrec) = (c74_anousu, c74_codrec)
             where c71_coddoc in (6000, 6001, 6006, 6007)
        ), recurso_conta_pagadora as (

            select c61_codigo, c69_codlan
              from dados_lancamento
              left join conlancamrec on conlancamrec.c74_codlan = dados_lancamento.c70_codlan
              join conlancamval on conlancamval.c69_codlan = dados_lancamento.c70_codlan
              join conplanoreduz on c61_anousu = c69_anousu
                   and c61_reduz = (case
                                       when c71_coddoc in (150, 153, 160, 163) then c69_debito
                                       when c71_coddoc in (151, 152, 161, 162) then c69_credito
                                    end
                                   )
            where c69_ordem = 1
              and c71_coddoc in (160, 161, 162, 163, 150, 151, 152, 153)
              and c74_codlan is null
              and not exists(
                select 1
                  from conlancamslip
                  join slipempagemovslips on slipempagemovslips.k108_slip = conlancamslip.c84_slip
                  join empagemovslips on empagemovslips.k107_sequencial = slipempagemovslips.k108_empagemovslips
                  join retencaoempagemov on retencaoempagemov.e27_empagemov = empagemovslips.k107_empagemov
                  where c84_conlancam = dados_lancamento.c70_codlan
              )
          union all
            select c61_codigo, c82_codlan as c69_codlan
              from dados_lancamento
              join conlancampag on conlancampag.c82_codlan = dados_lancamento.c70_codlan
              join conplanoreduz on conplanoreduz.c61_reduz = conlancampag.c82_reduz
                                and conplanoreduz.c61_anousu = conlancampag.c82_anousu
              left join conlancamrec on conlancamrec.c74_codlan = dados_lancamento.c70_codlan
             where c71_coddoc not in (160, 161, 162, 163, 150, 151, 152, 153)
               and c74_codlan is null
        ), dados_empenho as (
          select o206_recurso, c75_codlan, c75_numemp, e60_coddot, c71_coddoc, c70_data, c53_tipo
            from dados_lancamento
            join conlancamemp on conlancamemp.c75_codlan = dados_lancamento.c70_codlan
            join empempenho on empempenho.e60_numemp = conlancamemp.c75_numemp
            join origemcomplementorecurso on origemcomplementorecurso.o206_numero = empempenho.e60_numemp
                                         and origemcomplementorecurso.o206_origem = 1
        ), recurso_empenho as (
          select o206_recurso, c75_codlan, c75_numemp, e60_coddot, c71_coddoc, c70_data
            from dados_empenho
            where c53_tipo NOT IN (30, 31, 100)
        ), recurso_apropriacao as (
           select o58_codigo, c75_codlan
             from dados_empenho
             join orcdotacao ON orcdotacao.o58_coddot = dados_empenho.e60_coddot
            where c71_coddoc IN (6002, 6003, 6004, 6005, 6008, 6009, 6010, 6011)
        ), recursos_suplementacao as (
          select o58_codigo, c70_codlan
             from dados_lancamento
             join conlancamsup on conlancamsup.c79_codlan = dados_lancamento.c70_codlan
             join conlancamdot on conlancamdot.c73_codlan = conlancamsup.c79_codlan
             join orcdotacao on orcdotacao.o58_coddot = conlancamdot.c73_coddot
                            and orcdotacao.o58_anousu = conlancamdot.c73_anousu

        ), recurso_despesa_suplementacao as (
          select *
            from recursos_suplementacao
           where not exists( select 1 from conlancamrec where c74_codlan = c70_codlan)
        ), resto_a_pagar as (
          select e91_recurso as recurso_resto, c75_codlan
            from recurso_empenho
            join empresto on empresto.e91_numemp = recurso_empenho.c75_numemp
                         and empresto.e91_anousu = extract(YEAR from c70_data)
        ), recurso_receita as (
           select o70_codigo as recurso_receita, c70_codlan
             from dados_lancamento
             join conlancamrec on conlancamrec.c74_codlan = dados_lancamento.c70_codlan
             join orcreceita on orcreceita.o70_codrec = conlancamrec.c74_codrec
                            and orcreceita.o70_anousu = conlancamrec.c74_anousu
        ), abertura_despesa as (
          select o58_codigo as recurso,  c70_codlan
            from dados_lancamento
            join conlancamdot on c73_codlan = c70_codlan
            join orcdotacao on (o58_anousu, o58_coddot) = (c73_anousu, c73_coddot)
           where c71_coddoc = 2001
        ), abertura_receita as (
          select o70_codigo as recurso,  c70_codlan
            from dados_lancamento
            join conlancamrec on c74_codlan = c70_codlan
            join orcreceita on (o70_anousu, o70_codrec) = (c74_anousu, c74_codrec)
           where c71_coddoc = 2003
        ), abertura_rp as (
          select o58_codigo as recurso,  c70_codlan
            from dados_lancamento
            join conlancamemp on c75_codlan = c70_codlan
            join empempenho on e60_numemp = c75_numemp
            join orcdotacao on (o58_anousu, o58_coddot) = (e60_anousu, e60_coddot)
           where c71_coddoc in (2032, 2033)
        ), abertura_exercicio as (
          select * from abertura_despesa
          union all
          select * from abertura_receita
          union all
          select * from abertura_rp
        )
        select dados_lancamento.*,
               recurso_conta_pagadora.c61_codigo as recurso_conta_pagadora,
               recurso_empenho.o206_recurso as recurso_empenho,
               recurso_apropriacao.o58_codigo as recurso_apropriacao,
               recurso_retencao_receita.o70_codigo as recurso_retencao_receita,
               recurso_despesa_suplementacao.o58_codigo as dotacao_suplementacao,
               resto_a_pagar.recurso_resto,
               recurso_receita.recurso_receita,
               abertura_exercicio.recurso as abertura_exercicio
         from dados_lancamento
         left join recurso_empenho on recurso_empenho.c75_codlan = dados_lancamento.c70_codlan
         left join recurso_conta_pagadora on recurso_conta_pagadora.c69_codlan = dados_lancamento.c70_codlan
         left join recurso_apropriacao on recurso_apropriacao.c75_codlan = dados_lancamento.c70_codlan
         left join recurso_despesa_suplementacao on recurso_despesa_suplementacao.c70_codlan = dados_lancamento.c70_codlan
         left join resto_a_pagar on resto_a_pagar.c75_codlan = dados_lancamento.c70_codlan
         left join recurso_receita on recurso_receita.c70_codlan = dados_lancamento.c70_codlan
         left join abertura_exercicio  on abertura_exercicio.c70_codlan = dados_lancamento.c70_codlan
         left join recurso_retencao_receita on recurso_retencao_receita.c70_codlan = dados_lancamento.c70_codlan
        ";

        return $sql;
    }

    public function extrai_c70codlan($sql)
    {
        $pattern = '/values\s*\(([^)]+)\)/i';
        preg_match($pattern, $sql, $matches);
        $valuesPart = $matches[1];
        $pattern = '/\b(\d+)\b/';
        preg_match($pattern, $valuesPart, $matches);
        $codigo = $matches[1];
        $GLOBALS["codlan_c70"] = $codigo;
    }

    public function sql_despesaExtraOrcamentaria($instituicoes, $datainicial, $datafinal, $anousu)
    {

        $sql = <<<SQL

        SELECT
        (select c60_descr
          from conplano
          join conplanoreduz on c60_codcon = c61_codcon
                            and c60_anousu = c61_anousu
          join conlancamval on c69_debito = c61_reduz
         where c69_codlan = c71_codlan
           and c61_anousu = $anousu
           and c69_ordem = 1) as descricao_conta,
        c71_codlan,
        c71_data,
        c71_coddoc,
        c53_descr,
        c02_instit,
        nomeinstabrev,

   (SELECT z01_cgccpf
    FROM conlancamemp
    INNER JOIN empempenho ON c75_numemp = e60_numemp
    INNER JOIN cgm ON z01_numcgm = e60_numcgm
    WHERE c75_codlan = c70_codlan ) AS cpf_cnpj,

   (SELECT e60_codemp
    FROM conlancamemp
    INNER JOIN empempenho ON c75_numemp = e60_numemp
    INNER JOIN cgm ON z01_numcgm = e60_numcgm
    WHERE c75_codlan = c70_codlan ) AS empenho,

   (SELECT e60_anousu
    FROM conlancamemp
    INNER JOIN empempenho ON c75_numemp = e60_numemp
    INNER JOIN cgm ON z01_numcgm = e60_numcgm
    WHERE c75_codlan = c70_codlan ) AS ano_empenho,

   (SELECT c84_slip
    FROM conlancamslip
    WHERE c84_conlancam = c71_codlan ) AS slip,
        CASE
            WHEN
                   (SELECT c60_estrut
                    FROM conlancamretencao
                    INNER JOIN retencaotiporec ON e21_sequencial = c127_retencaotiporec
                    INNER JOIN tabrec a ON e21_receita = a.k02_codigo
                    INNER JOIN tabplan b ON b.k02_anousu = c70_anousu
                    AND a.k02_codigo = b.k02_codigo
                    INNER JOIN conplanoreduz ON b.k02_anousu = c61_anousu
                    AND b.k02_reduz = c61_reduz
                    INNER JOIN conplano ON c60_anousu = c61_anousu
                    AND c60_codcon = c61_codcon
                    WHERE c127_conlancam = c71_codlan ) IS NOT NULL THEN
                   (SELECT c60_estrut
                    FROM conlancamretencao
                    INNER JOIN retencaotiporec ON e21_sequencial = c127_retencaotiporec
                    INNER JOIN tabrec a ON e21_receita = a.k02_codigo
                    INNER JOIN tabplan b ON b.k02_anousu = c70_anousu
                    AND a.k02_codigo = b.k02_codigo
                    INNER JOIN conplanoreduz ON b.k02_anousu = c61_anousu
                    AND b.k02_reduz = c61_reduz
                    INNER JOIN conplano ON c60_anousu = c61_anousu
                    AND c60_codcon = c61_codcon
                    WHERE c127_conlancam = c71_codlan )
            ELSE CASE
                     WHEN
                            (SELECT c60_estrut
                             FROM conlancamcorrente
                             INNER JOIN cornump ON c86_data = k12_data
                             AND c86_id = k12_id
                             AND c86_autent = k12_autent
                             INNER JOIN tabplan ON k02_codigo = k12_receit
                             AND k02_anousu = c70_anousu
                             INNER JOIN conplanoreduz ON c61_anousu = c70_anousu
                             AND c61_reduz = k02_reduz
                             INNER JOIN conplano ON c61_anousu = c60_anousu
                             AND c61_codcon = c60_codcon
                             WHERE c86_conlancam = c71_codlan ) IS NOT NULL THEN
                            (SELECT c60_estrut
                             FROM conlancamcorrente
                             INNER JOIN cornump ON c86_data = k12_data
                             AND c86_id = k12_id
                             AND c86_autent = k12_autent
                             INNER JOIN tabplan ON k02_codigo = k12_receit
                             AND k02_anousu = c70_anousu
                             INNER JOIN conplanoreduz ON c61_anousu = c70_anousu
                             AND c61_reduz = k02_reduz
                             INNER JOIN conplano ON c61_anousu = c60_anousu
                             AND c61_codcon = c60_codcon
                             WHERE c86_conlancam = c71_codlan )
                     ELSE
                            (SELECT c60_estrut
                             FROM conlancamslip
                             INNER JOIN slip ON c84_slip = k17_codigo
                             INNER JOIN conplanoreduz ON c61_anousu = c70_anousu
                             AND c61_reduz = k17_debito
                             INNER JOIN conplano ON c61_anousu = c60_anousu
                             AND c61_codcon = c60_codcon
                             WHERE c84_conlancam = c71_codlan )
                 END
        END AS estrutural,

   (SELECT count(*)
    FROM conlancampag
    WHERE c82_codlan = c71_codlan ) AS ctabancaria,
        CASE
            WHEN c53_tipo in (153,
                              163) THEN c70_valor*-1
            ELSE c70_valor
        END AS c70_valor
 FROM conlancamdoc
 INNER JOIN conhistdoc ON c71_coddoc = c53_coddoc
 INNER JOIN conlancam ON c70_codlan = c71_codlan
 INNER JOIN conlancaminstit ON c71_codlan = c02_codlan
 INNER JOIN db_config ON codigo = c02_instit
 WHERE

       c71_coddoc in ( SELECT c53_coddoc
                        FROM conhistdoc
                       WHERE c53_coddoc in (151, 153, 161, 163)   )

   AND c71_data BETWEEN '$datainicial' AND '$datafinal'
   AND c02_instit in ($instituicoes)

 UNION

 SELECT
       (select c60_descr
          from conplano
          join conplanoreduz on c60_codcon = c61_codcon
                            and c60_anousu = c61_anousu
          join conlancamval on c69_debito = c61_reduz
         where c69_codlan = c71_codlan
           and c61_anousu = $anousu
           and c69_ordem = 1) as descricao_conta,
        c71_codlan,
        c71_data,
        c71_coddoc,
        c53_descr,
        c02_instit,
        nomeinstabrev,

   (SELECT z01_cgccpf
    FROM conlancamemp
    INNER JOIN empempenho ON c75_numemp = e60_numemp
    INNER JOIN cgm ON z01_numcgm = e60_numcgm
    WHERE c75_codlan = c70_codlan ) AS cpf_cnpj,

   (SELECT e60_codemp
    FROM conlancamemp
    INNER JOIN empempenho ON c75_numemp = e60_numemp
    INNER JOIN cgm ON z01_numcgm = e60_numcgm
    WHERE c75_codlan = c70_codlan ) AS empenho,

   (SELECT e60_anousu
    FROM conlancamemp
    INNER JOIN empempenho ON c75_numemp = e60_numemp
    INNER JOIN cgm ON z01_numcgm = e60_numcgm
    WHERE c75_codlan = c70_codlan ) AS ano_empenho,
        0 AS slip,
        CASE
            WHEN
                   (SELECT c60_estrut
                    FROM conlancamretencao
                    INNER JOIN retencaotiporec ON e21_sequencial = c127_retencaotiporec
                    INNER JOIN tabrec a ON e21_receita = a.k02_codigo
                    INNER JOIN tabplan b ON b.k02_anousu = c70_anousu
                    AND a.k02_codigo = b.k02_codigo
                    INNER JOIN conplanoreduz ON b.k02_anousu = c61_anousu
                    AND b.k02_reduz = c61_reduz
                    INNER JOIN conplano ON c60_anousu = c61_anousu
                    AND c60_codcon = c61_codcon
                    WHERE c127_conlancam = c71_codlan ) IS NOT NULL THEN
                   (SELECT c60_estrut
                    FROM conlancamretencao
                    INNER JOIN retencaotiporec ON e21_sequencial = c127_retencaotiporec
                    INNER JOIN tabrec a ON e21_receita = a.k02_codigo
                    INNER JOIN tabplan b ON b.k02_anousu = c70_anousu
                    AND a.k02_codigo = b.k02_codigo
                    INNER JOIN conplanoreduz ON b.k02_anousu = c61_anousu
                    AND b.k02_reduz = c61_reduz
                    INNER JOIN conplano ON c60_anousu = c61_anousu
                    AND c60_codcon = c61_codcon
                    WHERE c127_conlancam = c71_codlan )
            ELSE CASE
                     WHEN
                            (SELECT c60_estrut
                             FROM conlancamcorrente
                             INNER JOIN cornump ON c86_data = k12_data
                             AND c86_id = k12_id
                             AND c86_autent = k12_autent
                             INNER JOIN tabplan ON k02_codigo = k12_receit
                             AND k02_anousu = c70_anousu
                             INNER JOIN conplanoreduz ON c61_anousu = c70_anousu
                             AND c61_reduz = k02_reduz
                             INNER JOIN conplano ON c61_anousu = c60_anousu
                             AND c61_codcon = c60_codcon
                             WHERE c86_conlancam = c71_codlan ) IS NOT NULL THEN
                            (SELECT c60_estrut
                             FROM conlancamcorrente
                             INNER JOIN cornump ON c86_data = k12_data
                             AND c86_id = k12_id
                             AND c86_autent = k12_autent
                             INNER JOIN tabplan ON k02_codigo = k12_receit
                             AND k02_anousu = c70_anousu
                             INNER JOIN conplanoreduz ON c61_anousu = c70_anousu
                             AND c61_reduz = k02_reduz
                             INNER JOIN conplano ON c61_anousu = c60_anousu
                             AND c61_codcon = c60_codcon
                             WHERE c86_conlancam = c71_codlan )
                     ELSE
                            (SELECT c60_estrut
                             FROM conlancamslip
                             INNER JOIN slip ON c84_slip = k17_codigo
                             INNER JOIN conplanoreduz ON c61_anousu = c70_anousu
                             AND c61_reduz = k17_debito
                             INNER JOIN conplano ON c61_anousu = c60_anousu
                             AND c61_codcon = c60_codcon
                             WHERE c84_conlancam = c71_codlan )
                 END
        END AS estrutural,

   (SELECT count(*)
    FROM conlancampag
    WHERE c82_codlan = c71_codlan ) AS ctabancaria,
        CASE
            WHEN c53_tipo = 31 THEN c70_valor*-1
            ELSE c70_valor
        END AS c70_valor
 FROM conlancamdoc
 INNER JOIN conhistdoc ON c71_coddoc = c53_coddoc
 INNER JOIN conlancam ON c70_codlan = c71_codlan
 INNER JOIN conlancaminstit ON c71_codlan = c02_codlan
 INNER JOIN db_config ON codigo = c02_instit
 WHERE c71_coddoc in (35, 36, 37, 38)

   AND c71_data BETWEEN '$datainicial' AND '$datafinal'
   AND c02_instit in ($instituicoes)

SQL;

        return $sql;
    }

    public function sql_receitaExtraOrcamentaria($instituicoes, $datainicial, $datafinal, $anousu)
    {
        $sql = <<<SQL
SELECT c71_codlan,
       (select c69_credito from conlancamval where c69_codlan = c71_codlan and c69_ordem = 1) as c69_credito,
       (select c60_descr
          from conplano
          join conplanoreduz on c60_codcon = c61_codcon
                            and c60_anousu = c61_anousu
          join conlancamval on c69_credito = c61_reduz
         where c69_codlan = c71_codlan
           and c61_anousu = $anousu
           and c69_ordem = 1) as descricao_conta,
       c71_data,
       c71_coddoc,
       c53_descr,
       c02_instit,
       nomeinstabrev,

  (SELECT z01_cgccpf
   FROM conlancamemp
   INNER JOIN empempenho ON c75_numemp = e60_numemp
   INNER JOIN cgm ON z01_numcgm = e60_numcgm
   WHERE c75_codlan = c70_codlan ) AS cpf_cnpj,

  (SELECT e60_codemp
   FROM conlancamemp
   INNER JOIN empempenho ON c75_numemp = e60_numemp
   INNER JOIN cgm ON z01_numcgm = e60_numcgm
   WHERE c75_codlan = c70_codlan ) AS empenho,

  (SELECT e60_anousu
   FROM conlancamemp
   INNER JOIN empempenho ON c75_numemp = e60_numemp
   INNER JOIN cgm ON z01_numcgm = e60_numcgm
   WHERE c75_codlan = c70_codlan ) AS ano_empenho,
       CASE
           WHEN
                  (SELECT c60_estrut
                   FROM conlancamretencao
                   INNER JOIN retencaotiporec ON e21_sequencial = c127_retencaotiporec
                   INNER JOIN tabrec a ON e21_receita = a.k02_codigo
                   INNER JOIN tabplan b ON b.k02_anousu = c70_anousu
                   AND a.k02_codigo = b.k02_codigo
                   INNER JOIN conplanoreduz ON b.k02_anousu = c61_anousu
                   AND b.k02_reduz = c61_reduz
                   INNER JOIN conplano ON c60_anousu = c61_anousu
                   AND c60_codcon = c61_codcon
                   WHERE c127_conlancam = c71_codlan ) IS NOT NULL THEN
                  (SELECT c60_estrut
                   FROM conlancamretencao
                   INNER JOIN retencaotiporec ON e21_sequencial = c127_retencaotiporec
                   INNER JOIN tabrec a ON e21_receita = a.k02_codigo
                   INNER JOIN tabplan b ON b.k02_anousu = c70_anousu
                   AND a.k02_codigo = b.k02_codigo
                   INNER JOIN conplanoreduz ON b.k02_anousu = c61_anousu
                   AND b.k02_reduz = c61_reduz
                   INNER JOIN conplano ON c60_anousu = c61_anousu
                   AND c60_codcon = c61_codcon
                   WHERE c127_conlancam = c71_codlan )
           ELSE CASE
                    WHEN
                           (SELECT c60_estrut
                            FROM conlancamcorrente
                            INNER JOIN cornump ON c86_data = k12_data
                            AND c86_id = k12_id
                            AND c86_autent = k12_autent
                            INNER JOIN tabplan ON k02_codigo = k12_receit
                            AND k02_anousu = c70_anousu
                            INNER JOIN conplanoreduz ON c61_anousu = c70_anousu
                            AND c61_reduz = k02_reduz
                            INNER JOIN conplano ON c61_anousu = c60_anousu
                            AND c61_codcon = c60_codcon
                            WHERE c86_conlancam = c71_codlan ) IS NOT NULL THEN
                           (SELECT c60_estrut
                            FROM conlancamcorrente
                            INNER JOIN cornump ON c86_data = k12_data
                            AND c86_id = k12_id
                            AND c86_autent = k12_autent
                            INNER JOIN tabplan ON k02_codigo = k12_receit
                            AND k02_anousu = c70_anousu
                            INNER JOIN conplanoreduz ON c61_anousu = c70_anousu
                            AND c61_reduz = k02_reduz
                            INNER JOIN conplano ON c61_anousu = c60_anousu
                            AND c61_codcon = c60_codcon
                            WHERE c86_conlancam = c71_codlan )
                    ELSE
                           (SELECT c60_estrut
                            FROM conlancamslip
                            INNER JOIN slip ON c84_slip = k17_codigo
                            INNER JOIN conplanoreduz ON c61_anousu = c70_anousu
                            AND c61_reduz = k17_credito
                            INNER JOIN conplano ON c61_anousu = c60_anousu
                            AND c61_codcon = c60_codcon
                            WHERE c84_conlancam = c71_codlan )
                END
       END AS estrutural,

  (SELECT count(*)
   FROM conlancampag
   WHERE c82_codlan = c71_codlan ) AS ctabancaria,
       CASE
           WHEN c53_tipo = 31 THEN c70_valor*-1
           WHEN c71_coddoc in (152,
                               162) THEN c70_valor*-1
           ELSE c70_valor
       END AS c70_valor
FROM conlancamdoc
INNER JOIN conhistdoc ON c71_coddoc = c53_coddoc
INNER JOIN conlancam ON c70_codlan = c71_codlan
INNER JOIN conlancaminstit ON c71_codlan = c02_codlan
INNER JOIN db_config ON codigo = c02_instit
WHERE

c71_coddoc in (
                       SELECT c53_coddoc
                         FROM conhistdoc
                         where (c53_tipo in (30, 31) and c53_coddoc in (6002, 6003, 6008, 6009, 6010, 6011) )
                            OR c53_coddoc in (160, 162, 150, 152)
                    )

  AND c71_data BETWEEN '$datainicial' AND '$datafinal'
  AND c02_instit in ($instituicoes)
ORDER BY c71_data,
         c70_valor




SQL;

        return $sql;
    }


    public function sql_conferenciaPorRecurso(stdClass $filtros, $estrutural = '82111')
    {
        $exercicio = $filtros->ano;

        $sql = <<<SQL

select * from (

    select
           orctiporec.o15_codigo,
           orctiporec.o15_recurso as subrecurso,
           orctiporec.o15_complemento,
           fonterecurso.gestao,
           fonterecurso.codigo_siconfi,
           fonterecurso.descricao,
           saldo_ativo_at_f,

           round(valor_empenho - valor_liquidado ,2) as valor_a_liquidar,
           round(valor_liquidado - valor_pago ,2) as valor_a_pagar,
           round(valor_a_liquidar_rp_inicial - valor_anulado_rp_nao_processado - valor_liquidado_rp ,2) as valor_a_liquidar_rp,
           round(valor_a_pagar_rp_inicial + valor_liquidado_rp - valor_pago_rp - valor_pago_rp_nao - valor_anulado_rp_processado ,2) as valor_a_pagar_rp,
           round(saldo_ativo_at_f
                 -round(valor_empenho - valor_liquidado ,2)
                 -round(valor_liquidado - valor_pago ,2)
                 -round(valor_a_liquidar_rp_inicial - valor_anulado_rp_nao_processado - valor_liquidado_rp ,2)
                 -round(valor_a_pagar_rp_inicial + valor_liquidado_rp - valor_pago_rp - valor_pago_rp_nao - valor_anulado_rp_processado ,2) ,2) as total,
           valor_disponibilidade,
           valor_disponibilidade -
                ( round(saldo_ativo_at_f
                 -round(valor_empenho - valor_liquidado ,2)
                 -round(valor_liquidado - valor_pago ,2)
                 -round(valor_a_liquidar_rp_inicial - valor_anulado_rp_nao_processado - valor_liquidado_rp ,2)
                 -round(valor_a_pagar_rp_inicial + valor_liquidado_rp - valor_pago_rp - valor_pago_rp_nao - valor_anulado_rp_processado ,2) ,2) ) as diferenca
    from (
        select
               o15_codigo,
               round(sum(case when tipo = 'sp' then valor_empenho else 0 end),2) as saldo_ativo_at_f ,
               round(sum(case when tipo = 'e' then valor_empenho else 0 end),2) as valor_empenho ,
               round(sum(case when tipo = 'l' then valor_empenho else 0 end),2) as valor_liquidado,
               round(sum(case when tipo = 'p' then valor_empenho else 0 end),2) as valor_pago,
               round(sum(case when tipo = 'rl' then valor_empenho else 0 end),2) as valor_liquidado_rp,
               round(sum(case when tipo = 'rp' then valor_empenho else 0 end),2) as valor_pago_rp,
               round(sum(case when tipo = 'rnp' then valor_empenho else 0 end),2) as valor_pago_rp_nao,
               round(sum(case when tipo = 'rap' then valor_empenho else 0 end),2) as valor_anulado_rp_processado,
               round(sum(case when tipo = 'ranp' then valor_empenho else 0 end),2) as valor_anulado_rp_nao_processado,
               round(sum(case when tipo = 'restos_a_liquidar' then valor_empenho else 0 end),2) as valor_a_liquidar_rp_inicial,
               round(sum(case when tipo = 'restos_a_pagar' then valor_empenho else 0 end),2) as valor_a_pagar_rp_inicial,
               round(sum(case when tipo = 'disponibilidade' then valor_empenho else 0 end),2) as valor_disponibilidade
        from (
            select o15_codigo,
                   'sp'::varchar as tipo,
                   round(sum(
                       case when fc_planosaldonovo_array[6] = 'C' then fc_planosaldonovo_array[4]::float8*-1
                       else fc_planosaldonovo_array[4]::float8 end), 2
                   ) as valor_empenho
             from (
                select o15_codigo,
                       fc_planosaldonovo_array(c61_anousu, c61_reduz, '$filtros->dataInicial',  '$filtros->dataFinal', false)
                  from conplanoreduz
                  join conplano on c60_anousu = c61_anousu
                       and c60_codcon = c61_codcon
                 inner join orctiporec on o15_codigo = c61_codigo
                where c61_anousu = $filtros->ano
                  and c61_instit in ($filtros->instituicoes)
                  and c60_identificadorfinanceiro = 'F'
                  and substr(c60_estrut,1,1) = '1'
           ) as x
           group by 1,2

           union all

           select *
             from (
                select o15_codigo,
                       'e'::varchar as tipo,
                       (round(case when c53_tipo = 11 then c70_valor * -1 else c70_valor end, 2)) as valor_empenho
                  from conlancam
                       join conlancamemp on c75_codlan = c70_codlan
                       join conlancamdoc on c71_codlan = c70_codlan
                       join conhistdoc on c53_coddoc = c71_coddoc
                       join conlancamcomplementorecurso on o201_codlan = c70_codlan
                       join orctiporec on o15_codigo = o201_orctiporec
                       join empempenho on e60_numemp = c75_numemp
                  where c70_data between  '$filtros->dataInicial' and '$filtros->dataFinal'
                    and e60_instit  in ($filtros->instituicoes)
                    and e60_anousu = $filtros->ano
                    and c53_tipo in (10,11)
           ) as empenhado

          union all

          select *
            from (
               select o15_codigo,
                      'l'::varchar as tipo,
                      (round(case when c53_tipo = 21 then c70_valor * -1 else c70_valor end,2)) as valor_liquidado
                 from conlancam
                 join conlancamemp on c75_codlan = c70_codlan
                 join conlancamdoc on c71_codlan = c70_codlan
                 join conhistdoc on c53_coddoc = c71_coddoc
                 join conlancamcomplementorecurso on o201_codlan = c70_codlan
                 join orctiporec on o15_codigo = o201_orctiporec
                 join empempenho on e60_numemp = c75_numemp
                where c70_data between '$filtros->dataInicial' and '$filtros->dataFinal'
                 and e60_instit  in ($filtros->instituicoes)
                 and e60_anousu = $filtros->ano
                 and c53_tipo in (20,21)
          ) as liquidado

          union all

          select *
           from (
              select o15_codigo,
                     'p'::varchar as tipo,
                     (round(case when c53_tipo = 31 then c70_valor * -1 else c70_valor end,2)) as valor_pago
               from conlancam
               join conlancamemp on c75_codlan = c70_codlan
               join conlancamdoc on c71_codlan = c70_codlan
               join conhistdoc on c53_coddoc = c71_coddoc
               join conlancamcomplementorecurso on o201_codlan = c70_codlan
               join orctiporec on o15_codigo = o201_orctiporec
               join empempenho on e60_numemp = c75_numemp
              where c70_data between '$filtros->dataInicial' and '$filtros->dataFinal'
               and e60_instit  in ($filtros->instituicoes)
               and e60_anousu = $filtros->ano
               and c53_tipo in (30,31)
          ) as pago

          union all

         select *
           from (
                select o15_codigo,
                       'restos_a_liquidar'::varchar as tipo,
                       (e91_vlremp - e91_vlranu - e91_vlrliq) as valor_empenho_rp
                 from empresto
                 join orctiporec on o15_codigo = e91_recurso
                 join empempenho on e60_numemp = e91_numemp
                where e91_anousu = $filtros->ano
                  and e60_instit  in ($filtros->instituicoes)
        ) as valor_a_liquidado_rp

        union all

        select * from (
            select o15_codigo,
                   'restos_a_pagar'::varchar as tipo,
                   ( e91_vlrliq - e91_vlrpag ) as valor_empenho_rp
             from empresto
             join orctiporec on o15_codigo = e91_recurso
             join empempenho on e60_numemp = e91_numemp
            where e91_anousu = $filtros->ano
              and e60_instit  in ($filtros->instituicoes)
        ) as valor_a_pagar_rp

        union all

        select * from (
            select o15_codigo,
                   'rap'::varchar as tipo,
                   (round(case when c53_tipo = 11 then c70_valor else c70_valor end,2)) as valor_liquidado
                from conlancam
                     join conlancamemp on c75_codlan = c70_codlan
                     join conlancamdoc on c71_codlan = c70_codlan
                     join conhistdoc on c53_coddoc = c71_coddoc
                     join conlancamcomplementorecurso on o201_codlan = c70_codlan
                     join orctiporec on o15_codigo = o201_orctiporec
                 join empempenho on e60_numemp = c75_numemp
                where c70_data between '$filtros->dataInicial' and '$filtros->dataFinal'
              and e60_instit  in ($filtros->instituicoes)
              and e60_anousu < $filtros->ano
                  and c53_coddoc in (31)

        ) as estorno_rp_processado

        union all

        select * from (
            select o15_codigo,
                   'ranp'::varchar as tipo,
                   (round(case when c53_tipo = 11 then c70_valor else c70_valor end,2)) as valor_liquidado
                from conlancam
                     join conlancamemp on c75_codlan = c70_codlan
                     join conlancamdoc on c71_codlan = c70_codlan
                     join conhistdoc on c53_coddoc = c71_coddoc
                     join conlancamcomplementorecurso on o201_codlan = c70_codlan
                     join orctiporec on o15_codigo = o201_orctiporec
                 join empempenho on e60_numemp = c75_numemp
                where c70_data between '$filtros->dataInicial' and '$filtros->dataFinal'
              and e60_instit  in ($filtros->instituicoes)
              and e60_anousu < $filtros->ano
                  and c53_coddoc in (32)

        ) as estorno_rp_nao_processado

        union all

        select * from (
            select o15_codigo,
                   'rl'::varchar as tipo,
                       (round(case when c53_tipo = 21 then c70_valor * -1 else c70_valor end,2)) as valor_liquidado
                from conlancam
                     join conlancamemp on c75_codlan = c70_codlan
                     join conlancamdoc on c71_codlan = c70_codlan
                     join conhistdoc on c53_coddoc = c71_coddoc
                     join conlancamcomplementorecurso on o201_codlan = c70_codlan
                     join orctiporec on o15_codigo = o201_orctiporec
                 join empempenho on e60_numemp = c75_numemp
                where c70_data between '$filtros->dataInicial' and '$filtros->dataFinal'
              and e60_instit  in ($filtros->instituicoes)
              and e60_anousu < $filtros->ano
                  and c53_tipo in (20,21)
         ) as restos_liquidados

        union all

        select * from (
               select o15_codigo,
                   'rp'::varchar as tipo,
                       (round(case when c53_tipo = 31 then c70_valor * -1 else c70_valor end,2)) as valor_pago
                from conlancam
                join conlancamemp on c75_codlan = c70_codlan
                join conlancamdoc on c71_codlan = c70_codlan
                join conhistdoc on c53_coddoc = c71_coddoc
                join conlancamcomplementorecurso on o201_codlan = c70_codlan
                join orctiporec on o15_codigo = o201_orctiporec
                join empempenho on e60_numemp = c75_numemp
                where c70_data between '$filtros->dataInicial' and '$filtros->dataFinal'
                  and e60_instit in ($filtros->instituicoes)
                  and e60_anousu < $filtros->ano
                  and c53_coddoc in (35,6008, 36, 6009)
        ) as restos_pagos_processados

        union all

        select * from (
               select o15_codigo,
                   'rnp'::varchar as tipo,
                       (round(case when c53_tipo = 31 then c70_valor * -1 else c70_valor end,2)) as valor_pago
                from conlancam
                     join conlancamemp on c75_codlan = c70_codlan
                     join conlancamdoc on c71_codlan = c70_codlan
                     join conhistdoc on c53_coddoc = c71_coddoc
                     join conlancamcomplementorecurso on o201_codlan = c70_codlan
                     join orctiporec on o15_codigo = o201_orctiporec
                 join empempenho on e60_numemp = c75_numemp
                where c70_data between '$filtros->dataInicial' and '$filtros->dataFinal'
              and e60_instit in ($filtros->instituicoes)
              and e60_anousu < $filtros->ano
                  and c53_coddoc in (37,6010, 38, 6011)
         ) as restos_pagos_nao_processados


        union all

        select o15_codigo,
               'disponibilidade'::varchar as tipo,
               (saldo_inicial + debito - credito) *-1 as saldo
          from (
              select c144_valor::int as o15_codigo,
                     round(sum(case when c143_natureza = 'C' then c143_saldo *- 1 else c143_saldo end),2) as saldo_inicial,
                     0 as credito,
                     0 as debito
                from contabilidade.conplanoexecontacorrente
                join contabilidade.conplanoexecontacorrenteatributo on c144_conplanoexecontacorrente = conplanoexecontacorrente.id
                join contabilidade.conplanoreduz on (c61_reduz, c61_anousu) = (c143_conplanoreduz, c143_exercicio)
                join contabilidade.conplano on (c60_codcon, c60_anousu) = (c61_codcon, c61_anousu)
               where c143_conplanosistema = 100
                 and c143_exercicio = $filtros->ano
                 and c61_instit in ({$filtros->instituicoes})
                 and c60_estrut like '{$estrutural}%'
              group by 1,3,4

              union

              select fc_recurso_conta_lancamento(c69_codlan, c69_credito, 'C') as o15_codigo,
                     0 as saldo_inicial,
                     sum(round(c69_valor, 2)) as credito,
                     0 as debito
                from conlancam
                join conlancamval on c69_codlan = c70_codlan
                join conplanoreduz on c61_reduz = c69_credito
                     and c61_anousu = c69_anousu
                join contabilidade.conplano on (c60_codcon, c60_anousu) = (c61_codcon, c61_anousu)

               where c69_anousu = $filtros->ano
                 and c61_instit in ({$filtros->instituicoes})
                 and c60_estrut like '{$estrutural}%'
                 and c70_data <= '$filtros->dataFinal'
               group by 1,2,4
              union

              select fc_recurso_conta_lancamento(c69_codlan, c69_debito, 'D') ,
                     0 as saldo_inicial,
                     0 as credito,
                     sum(round(c69_valor, 2)) as debito
                 from conlancam
                 join conlancamval on c69_codlan = c70_codlan
                 join conplanoreduz on c61_reduz = c69_debito
                      and c61_anousu = c69_anousu
                 join contabilidade.conplano on (c60_codcon, c60_anousu) = (c61_codcon, c61_anousu)

                where c69_anousu = $filtros->ano
                  and c61_instit in ({$filtros->instituicoes})
                  and c60_estrut like '{$estrutural}%'
                  and c70_data <= '$filtros->dataFinal'
                group by 1,2,3

        ) as saldo

    ) as x
    group by 1
) as x
join orctiporec on orctiporec.o15_codigo = x.o15_codigo
join fonterecurso on orctiporec_id = x.o15_codigo
     and exercicio = $filtros->ano
) as x
order by gestao, subrecurso, o15_complemento
SQL;

        return $sql;
    }


    public function sql_query_receita_planoreceita($campos = "*",$ordem = null, $where = null,$groupBy =null)
    {
        $sql = "         
                select $campos
                  from conlancam 
            inner join conlancamrec                  on c74_codlan = c70_codlan 
            inner join conlancamdoc                  on c71_codlan = c70_codlan 
            inner join conhistdoc                    on c53_coddoc = c71_coddoc 
            inner join orcreceita                    on o70_codrec = c74_codrec 
                                                    and o70_anousu = c74_anousu 
            inner join orcfontes                     on o57_codfon = o70_codfon 
                                                    and o57_anousu = o70_anousu 
            inner join conplanoorcamento             on c60_codcon = o57_codfon
                                                    and c60_anousu = o57_anousu 
            inner join planoreceitaconplanoorcamento on conplanoorcamento_codigo = c60_codigo
            inner join planoreceita                  on planoreceita.id = planoreceita_id
                                                    and planoreceita.exercicio = c70_anousu
                                                    and planoreceita.uniao = 't'            
            inner join orctiporec                    on o15_codigo = o70_codigo 
            inner join fonterecurso                  on orctiporec_id = o15_codigo 
                                                    and fonterecurso.exercicio = c74_anousu 
        ";
        
        if (!empty($where)) {
            $sql .= " where {$where} ";
        }
        if (!empty($ordem)) {
            $sql .= " order by {$ordem} ";
        }
        if (!empty($groupBy)) {
            $sql .= " group by {$groupBy} ";
        }
        return $sql;        
    }

}


