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

class cl_conlancamval
{
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
    /**
     * @var int
     */
    public $c69_sequen = 0;
    /**
     * @var int
     */
    public $c69_anousu = 0;
    /**
     * @var int
     */
    public $c69_codlan = 0;
    /**
     * @var int
     */
    public $c69_codhist = 0;
    /**
     * @var int
     */
    public $c69_credito = 0;
    /**
     * @var int
     */
    public $c69_debito = 0;
    /**
     * @var int
     */
    public $c69_valor = 0;
    /**
     * @var string
     */
    public $c69_data = null;
    /**
     * @var int
     */
    public $c69_ordem = null;

    public $campos = "c69_sequen = int4 = Sequen
                      c69_anousu = int4 = Exercício
                      c69_codlan = int4 = Cód Lan
                      c69_codhist = int4 = Histórico
                      c69_credito = int4 = Conta Credito
                      c69_debito = int4 = Conta Debito
                      c69_valor = float8 = Valor
                      c69_data = date = Data Lanc
                      c69_ordem = int4 = Ordem do Lançamento";

    public function __construct()
    {
        $this->rotulo = new rotulo('conlancamval');
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
    }

    public function erro($mostra, $retorna)
    {
        if ($this->erro_status == '0' || ($mostra == true && $this->erro_status != null)) {
            echo "<script>alert('{$this->erro_msg}')</script>";
            if ($retorna == true) {
                echo "<script>location.href='" . $this->pagina_retorno . "'</script>";
            }
        }
    }

    function atualizacampos($exclusao = false)
    {
        if ($exclusao == false) {
            $this->c69_sequen = ($this->c69_sequen == "" ? @$GLOBALS["HTTP_POST_VARS"]["c69_sequen"] : $this->c69_sequen);
            $this->c69_anousu = ($this->c69_anousu == "" ? @$GLOBALS["HTTP_POST_VARS"]["c69_anousu"] : $this->c69_anousu);
            $this->c69_codlan = ($this->c69_codlan == "" ? @$GLOBALS["HTTP_POST_VARS"]["c69_codlan"] : $this->c69_codlan);
            $this->c69_codhist = ($this->c69_codhist == "" ? @$GLOBALS["HTTP_POST_VARS"]["c69_codhist"] : $this->c69_codhist);
            $this->c69_credito = ($this->c69_credito == "" ? @$GLOBALS["HTTP_POST_VARS"]["c69_credito"] : $this->c69_credito);
            $this->c69_debito = ($this->c69_debito == "" ? @$GLOBALS["HTTP_POST_VARS"]["c69_debito"] : $this->c69_debito);
            $this->c69_valor = ($this->c69_valor == "" ? @$GLOBALS["HTTP_POST_VARS"]["c69_valor"] : $this->c69_valor);
            if ($this->c69_data == "") {
                $this->c69_data_dia = ($this->c69_data_dia == "" ? @$GLOBALS["HTTP_POST_VARS"]["c69_data_dia"] : $this->c69_data_dia);
                $this->c69_data_mes = ($this->c69_data_mes == "" ? @$GLOBALS["HTTP_POST_VARS"]["c69_data_mes"] : $this->c69_data_mes);
                $this->c69_data_ano = ($this->c69_data_ano == "" ? @$GLOBALS["HTTP_POST_VARS"]["c69_data_ano"] : $this->c69_data_ano);
                if ($this->c69_data_dia != "") {
                    $this->c69_data = $this->c69_data_ano . "-" . $this->c69_data_mes . "-" . $this->c69_data_dia;
                }
            }
        } else {
            $this->c69_sequen = ($this->c69_sequen == "" ? @$GLOBALS["HTTP_POST_VARS"]["c69_sequen"] : $this->c69_sequen);
        }
    }

    public function incluir($c69_sequen)
    {
        $this->atualizacampos();

        if ($this->c69_anousu === '' || $this->c69_anousu === null) {
            $this->erro_sql = " Campo Exercício não informado.";
            $this->erro_campo = "c69_anousu";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
              str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if ($this->c69_codlan === '' || $this->c69_codlan === null) {
            $this->erro_sql = " Campo Cód Lan não informado.";
            $this->erro_campo = "c69_codlan";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
              str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if ($this->c69_codhist === '' || $this->c69_codhist === null) {
            $this->erro_sql = " Campo Histórico não informado.";
            $this->erro_campo = "c69_codhist";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
              str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if ($this->c69_credito === '' || $this->c69_credito === null) {
            $this->erro_sql = " Campo Conta Credito não informado.";
            $this->erro_campo = "c69_credito";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
              str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if ($this->c69_debito === '' || $this->c69_debito === null) {
            $this->erro_sql = " Campo Conta Debito não informado.";
            $this->erro_campo = "c69_debito";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
              str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if ($this->c69_valor === '' || $this->c69_valor === null) {
            $this->erro_sql = " Campo Valor não informado.";
            $this->erro_campo = "c69_valor";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
              str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if ($this->c69_data === '' || $this->c69_data === null) {
            $this->erro_sql = " Campo Data Lanc não informado.";
            $this->erro_campo = "c69_data_dia";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
              str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if ($this->c69_ordem === null || $this->c69_ordem === '') {
            $this->c69_ordem = "null";
        }
        if ($c69_sequen === '' || $c69_sequen === null || $c69_sequen === 0) {
            $result = db_query("select nextval('conlancamval_c69_sequen_seq')");
            if (!$result) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: conlancamval_c69_sequen_seq do campo: c69_sequen";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                  str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            }
            $this->c69_sequen = pg_result($result, 0, 0);
        } else {
            $result = db_query("SELECT last_value FROM conlancamval_c69_sequen_seq");
            if ($result && pg_result($result, 0, 0) < $c69_sequen) {
                $this->erro_sql = " Campo c69_sequen maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                  str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            } else {
                $this->c69_sequen = $c69_sequen;
            }
        }
        if ($this->c69_sequen === null || $this->c69_sequen === '' || $this->c69_sequen === 0) {
            $this->erro_sql = " Campo c69_sequen não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
              str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = '0';

            return false;
        }
        $sql = "
            INSERT INTO contabilidade.conlancamval (
                c69_sequen
                ,c69_anousu
                ,c69_codlan
                ,c69_codhist
                ,c69_credito
                ,c69_debito
                ,c69_valor
                ,c69_data
                ,c69_ordem
            ) VALUES (
                 " . ($this->c69_sequen === null || $this->c69_sequen === '' ? 'NULL' : $this->c69_sequen) . "
                ," . ($this->c69_anousu === null || $this->c69_anousu === '' ? 'NULL' : $this->c69_anousu) . "
                ," . ($this->c69_codlan === null || $this->c69_codlan === '' ? 'NULL' : $this->c69_codlan) . "
                ," . ($this->c69_codhist === null || $this->c69_codhist === '' ? 'NULL' : $this->c69_codhist) . "
                ," . ($this->c69_credito === null || $this->c69_credito === '' ? 'NULL' : $this->c69_credito) . "
                ," . ($this->c69_debito === null || $this->c69_debito === '' ? 'NULL' : $this->c69_debito) . "
                ," . ($this->c69_valor === null || $this->c69_valor === '' ? 'NULL' : $this->c69_valor) . "
                ," . ($this->c69_data === null || $this->c69_data === '' ? 'NULL' : "'{$this->c69_data}'") . "
                ," . ($this->c69_ordem === null || $this->c69_ordem === '' ? 'NULL' : $this->c69_ordem) . "
            )
        ";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "Valores lançamentos () não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "Valores lançamentos já cadastrado";
                $this->erro_msg .= str_replace('"', "",
                  str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "Valores lançamentos () não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                  str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;

            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "",
          str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
            && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->c69_sequen));
            if ($resaco != false || $this->numrows != 0) {
                $resac = db_query("SELECT nextval('db_acount_id_acount_seq') AS acount");
                $acount = pg_result($resac, 0, 0);
                $resac = db_query("INSERT INTO db_acountacesso VALUES ($acount, " . db_getsession("DB_acessado") . ")");
                $resac = db_query("INSERT INTO db_acountkey VALUES ($acount,5234,'$this->c69_sequen','I')");
                $resac = db_query("INSERT INTO db_acount VALUES ($acount,790,5234,'','" . AddSlashes(pg_result($resaco,
                    0,
                    'c69_sequen')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("INSERT INTO db_acount VALUES ($acount,790,5235,'','" . AddSlashes(pg_result($resaco,
                    0,
                    'c69_anousu')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("INSERT INTO db_acount VALUES ($acount,790,5236,'','" . AddSlashes(pg_result($resaco,
                    0,
                    'c69_codlan')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("INSERT INTO db_acount VALUES ($acount,790,5237,'','" . AddSlashes(pg_result($resaco,
                    0,
                    'c69_codhist')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("INSERT INTO db_acount VALUES ($acount,790,5238,'','" . AddSlashes(pg_result($resaco,
                    0,
                    'c69_credito')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("INSERT INTO db_acount VALUES ($acount,790,5243,'','" . AddSlashes(pg_result($resaco,
                    0,
                    'c69_debito')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("INSERT INTO db_acount VALUES ($acount,790,5244,'','" . AddSlashes(pg_result($resaco,
                    0, 'c69_valor')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("INSERT INTO db_acount VALUES ($acount,790,5245,'','" . AddSlashes(pg_result($resaco,
                    0, 'c69_data')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("INSERT INTO db_acount VALUES ($acount,790,1010568,'','" . AddSlashes(pg_result($resaco,
                    0, 'c69_ordem')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            }
        }

        return true;
    }

    public function alterar($c69_sequen = null)
    {
        $this->atualizacampos();
        $sql = "UPDATE conlancamval SET ";
        $virgula = '';
        if (trim($this->c69_sequen) !== '' && $this->c69_sequen !== null) {
            $sql .= "{$virgula} c69_sequen = {$this->c69_sequen} ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Sequen" é obrigatório.');
        }
        if (trim($this->c69_anousu) !== '' && $this->c69_anousu !== null) {
            $sql .= "{$virgula} c69_anousu = {$this->c69_anousu} ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Exercício" é obrigatório.');
        }
        if (trim($this->c69_codlan) !== '' && $this->c69_codlan !== null) {
            $sql .= "{$virgula} c69_codlan = {$this->c69_codlan} ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Cód Lan" é obrigatório.');
        }
        if (trim($this->c69_codhist) !== '' && $this->c69_codhist !== null) {
            $sql .= "{$virgula} c69_codhist = {$this->c69_codhist} ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Histórico" é obrigatório.');
        }
        if (trim($this->c69_credito) !== '' && $this->c69_credito !== null) {
            $sql .= "{$virgula} c69_credito = {$this->c69_credito} ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Conta Credito" é obrigatório.');
        }
        if (trim($this->c69_debito) !== '' && $this->c69_debito !== null) {
            $sql .= "{$virgula} c69_debito = {$this->c69_debito} ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Conta Debito" é obrigatório.');
        }
        if (trim($this->c69_valor) !== '' && $this->c69_valor !== null) {
            $sql .= "{$virgula} c69_valor = {$this->c69_valor} ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Valor" é obrigatório.');
        }
        if (trim($this->c69_data) !== '' && $this->c69_data !== null) {
            $sql .= "{$virgula} c69_data = '{$this->c69_data}' ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Data Lanc" é obrigatório.');
        }
        if (trim($this->c69_ordem) !== '' && $this->c69_ordem !== null) {
            $sql .= "{$virgula} c69_ordem = {$this->c69_ordem} ";
        } else {
            $sql .= "{$virgula} c69_ordem = NULL ";
        }

        if ($c69_sequen !== '' && $c69_sequen !== null && $c69_sequen !== 0) {
            $sql .= ' WHERE';
            $sql .= " c69_sequen = {$this->c69_sequen}";
        }

        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
            && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->c69_sequen));
            if ($this->numrows > 0) {

                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,5234,'$this->c69_sequen','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c69_sequen"]) || $this->c69_sequen != "") {
                        $resac = db_query("insert into db_acount values($acount,790,5234,'" . AddSlashes(pg_result($resaco,
                            $conresaco,
                            'c69_sequen')) . "','$this->c69_sequen'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c69_anousu"]) || $this->c69_anousu != "") {
                        $resac = db_query("insert into db_acount values($acount,790,5235,'" . AddSlashes(pg_result($resaco,
                            $conresaco,
                            'c69_anousu')) . "','$this->c69_anousu'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c69_codlan"]) || $this->c69_codlan != "") {
                        $resac = db_query("insert into db_acount values($acount,790,5236,'" . AddSlashes(pg_result($resaco,
                            $conresaco,
                            'c69_codlan')) . "','$this->c69_codlan'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c69_codhist"]) || $this->c69_codhist != "") {
                        $resac = db_query("insert into db_acount values($acount,790,5237,'" . AddSlashes(pg_result($resaco,
                            $conresaco,
                            'c69_codhist')) . "','$this->c69_codhist'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c69_credito"]) || $this->c69_credito != "") {
                        $resac = db_query("insert into db_acount values($acount,790,5238,'" . AddSlashes(pg_result($resaco,
                            $conresaco,
                            'c69_credito')) . "','$this->c69_credito'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c69_debito"]) || $this->c69_debito != "") {
                        $resac = db_query("insert into db_acount values($acount,790,5243,'" . AddSlashes(pg_result($resaco,
                            $conresaco,
                            'c69_debito')) . "','$this->c69_debito'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c69_valor"]) || $this->c69_valor != "") {
                        $resac = db_query("insert into db_acount values($acount,790,5244,'" . AddSlashes(pg_result($resaco,
                            $conresaco,
                            'c69_valor')) . "','$this->c69_valor'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c69_data"]) || $this->c69_data != "") {
                        $resac = db_query("insert into db_acount values($acount,790,5245,'" . AddSlashes(pg_result($resaco,
                            $conresaco,
                            'c69_data')) . "','$this->c69_data'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c69_ordem"]) || $this->c69_ordem != "") {
                        $resac = db_query("insert into db_acount values($acount,790,1010568,'" . AddSlashes(pg_result($resaco,
                            $conresaco,
                            'c69_ordem')) . "','$this->c69_ordem'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Valores lançamentos não Alterado. Alteração Abortada.\\n";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
              str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;

            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Valores lançamentos não foi Alterado. Alteração Executada.\\n";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                  str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;

                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                  str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);

                return true;
            }
        }
    }

    public function excluir($c69_sequen = null, $dbwhere = null)
    {
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
            && ($lSessaoDesativarAccount === false))) {

            if (empty($dbwhere)) {

                $resaco = $this->sql_record($this->sql_query_file($c69_sequen));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
            }
            if (($resaco != false) || ($this->numrows != 0)) {

                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,5234,'$c69_sequen','E')");
                    $resac = db_query("insert into db_acount values($acount,790,5234,'','" . AddSlashes(pg_result($resaco,
                        $iresaco,
                        'c69_sequen')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,790,5235,'','" . AddSlashes(pg_result($resaco,
                        $iresaco,
                        'c69_anousu')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,790,5236,'','" . AddSlashes(pg_result($resaco,
                        $iresaco,
                        'c69_codlan')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,790,5237,'','" . AddSlashes(pg_result($resaco,
                        $iresaco,
                        'c69_codhist')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,790,5238,'','" . AddSlashes(pg_result($resaco,
                        $iresaco,
                        'c69_credito')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,790,5243,'','" . AddSlashes(pg_result($resaco,
                        $iresaco,
                        'c69_debito')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,790,5244,'','" . AddSlashes(pg_result($resaco,
                        $iresaco,
                        'c69_valor')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,790,5245,'','" . AddSlashes(pg_result($resaco,
                        $iresaco,
                        'c69_data')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,790,1010568,'','" . AddSlashes(pg_result($resaco,
                        $iresaco,
                        'c69_ordem')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }
        $sql = " delete from conlancamval
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($c69_sequen)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " c69_sequen = $c69_sequen ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Valores lançamentos não Excluído. Exclusão Abortada.\\n";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
              str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;

            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Valores lançamentos não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                  str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;

                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                  str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
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
            $this->erro_msg .= str_replace('"', "",
              str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        $this->numrows = pg_num_rows($result);
        if ($this->numrows == 0) {
            $this->erro_banco = "";
            $this->erro_sql = "Record Vazio na Tabela:conlancamval";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
              str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }

        return $result;
    }

    function excluir_codlan($codlan)
    {
        // pesquisa todos os sequenc do codlan informado
        $res = db_query($this->sql_query_file(null, "c69_sequen", null, "c69_codlan=$codlan"));
        $rows = pg_num_rows($res);
        if ($rows > 0) {
            for ($x = 0; $x < $rows; $x++) {
                $seq = pg_result($res, $x, 0);
                $this->excluir($seq);
            }
        }

        return true;
    }

    function sql_query($c69_sequen = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from conlancamval ";
        $sql .= "      left join conhist            on c50_codhist = c69_codhist ";
        $sql .= "      inner join conlancam         on c70_codlan  = c69_codlan ";
        $sql .= "      inner join conlancamdoc      on c71_codlan  = c70_codlan ";
        $sql .= "      inner join conhistdoc        on c53_coddoc = c71_coddoc ";
        $sql .= "      left  join conlancaminstit   on c02_codlan  = c70_codlan ";
        $sql .= "      left outer join conlancamdig on c78_codlan  = c70_codlan ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($c69_sequen != null) {
                $sql2 .= " where conlancamval.c69_sequen = $c69_sequen ";
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

    function sql_queryComplemento($c69_sequen = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from conlancamval ";
        $sql .= "      left join conhist            on c50_codhist = c69_codhist ";
        $sql .= "      inner join conlancam         on c70_codlan  = c69_codlan ";
        $sql .= "      inner join conlancamcompl    on c70_codlan  = c72_codlan ";
        $sql .= "      inner join conlancamdoc      on c71_codlan  = c70_codlan ";
        $sql .= "      inner join conhistdoc        on c53_coddoc = c71_coddoc ";
        $sql .= "      left  join conlancaminstit   on c02_codlan  = c70_codlan ";
        $sql .= "      left  join conlancamcorrente on c86_conlancam = c70_codlan ";
        $sql .= "      left outer join conlancamdig on c78_codlan  = c70_codlan ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($c69_sequen != null) {
                $sql2 .= " where conlancamval.c69_sequen = $c69_sequen ";
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


    function sql_query_contacorrentedetalhe($c69_sequen = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from conlancamval ";
        $sql .= " inner join contacorrentedetalheconlancamval on c69_sequen = c28_conlancamval";
        $sql .= " inner join contacorrentedetalhe on c28_contacorrentedetalhe = c19_sequencial";

        $sql2 = "";
        if ($dbwhere == "") {
            if ($c69_sequen != null) {
                $sql2 .= " where conlancamval.c69_sequen = $c69_sequen ";
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

    function sql_query_file($c69_sequen = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from conlancamval ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($c69_sequen != null) {
                $sql2 .= " where conlancamval.c69_sequen = $c69_sequen ";
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
     * @param  string $sCampos
     * @param  string $sOrdem
     * @param  string $sWhere
     * @return string
     */
    public function sql_query_contacorrentedetalhe_tce($sCampos = '*', $sTipo = 'D', $sOrdem = null, $sWhere = null)
    {

        $sCampoComparar = $sTipo == 'D' ? 'c69_debito' : 'c69_credito';

        $sSql = " select {$sCampos}                                                                   \n";
        $sSql .= "   from conlancamval                                                                 \n";
        $sSql .= "        left join contacorrentedetalheconlancamval on c69_sequen = c28_conlancamval \n";
        $sSql .= "                                      and  c28_tipo = '{$sTipo}' \n";
        $sSql .= "        left join contacorrentedetalhe on c28_contacorrentedetalhe = c19_sequencial \n";
        $sSql .= "        inner join conlancaminstit on c02_codlan = c69_codlan                        \n";
        $sSql .= "        inner join conlancamdoc on c71_codlan   = c69_codlan                           \n";
        $sSql .= "        inner join conplanoreduz  on c61_reduz  = {$sCampoComparar}                     \n";
        $sSql .= "                                 and c61_instit = c02_instit                         \n";
        $sSql .= "                                 and c61_anousu = c69_anousu                         \n";
        $sSql .= "        inner join conplano  on c60_codcon = c61_codcon                              \n";
        $sSql .= "                            and c60_anousu = c61_anousu                              \n";
        $sSql .= "        left  join vinculoeventoscontabeis on c115_conhistdocestorno = c71_coddoc    \n";

        $sSql .= "        left  join conlancamemp on c75_codlan = c71_codlan                           \n";
        $sSql .= "        left  join conlancamcorrente on c86_conlancam = c71_codlan                   \n";
        $sSql .= "        left  join conlancamcorgrupocorrente on c23_conlancam = c71_codlan           \n";
        $sSql .= "        left  join corgrupocorrente on k105_sequencial = c23_corgrupocorrente        \n";
        $sSql .= "        left  join corrente  on c86_id = corrente.k12_id                             \n";
        $sSql .= "                            and c86_data = corrente.k12_data                         \n";
        $sSql .= "                            and c86_autent = corrente.k12_autent                     \n";

        $sSql .= "        left  join conlancamslip on c84_conlancam = c71_codlan                       \n";
        $sSql .= "        left  join empageslip on c84_slip = e89_codigo                               \n";
        $sSql .= "        left  join empagemov on e81_codmov = e89_codmov                              \n";
        $sSql .= "        left  join empageconfche on e91_codmov = e89_codmov                          \n";

        $sSql .= "        left  join corplacaixa  on k82_id = corrente.k12_id                          \n";
        $sSql .= "                               and k82_data = corrente.k12_data                      \n";
        $sSql .= "                               and k82_autent = corrente.k12_autent                  \n";
        $sSql .= "        left  join placaixarec on k82_seqpla = k81_seqpla                            \n";

        $sSql .= "        left  join coremp  on coremp.k12_id = k105_id                                \n";
        $sSql .= "                          and coremp.k12_data = k105_data                            \n";
        $sSql .= "                          and coremp.k12_autent = k105_autent                        \n";


        if (!empty($sWhere)) {
            $sSql .= " where {$sWhere} ";
        }

        if (!empty($sOrdem)) {
            $sSql .= " order by {$sOrdem} ";
        }

        return $sSql;
    }


    public function sql_query_conta_documento($sCampos = "*", $sWhere = null, $sOrder = null)
    {

        $sql = " select {$sCampos} ";
        $sql .= "   from conlancamval ";
        $sql .= "        inner join conlancamdoc on c71_codlan = c69_codlan ";

        if (!empty($sWhere)) {
            $sql .= " where {$sWhere} ";
        }

        if (!empty($sOrder)) {
            $sql .= " order by {$sOrder} ";
        }

        return $sql;
    }

    public function sql_query_lancamentos_documento($reduzido, $lista_lancamentos)
    {
        $sqlLancamentos = <<<SQL
            select c69_codlan as codigo_lancamento,
                   c71_coddoc as documento,
                   c53_descr  as descricao_documento,
                   case
                     when c69_debito = {$reduzido}
                       then c69_valor
                       else 0
                   end as valor_debito,
                   case
                     when c69_credito = {$reduzido}
                       then c69_valor
                       else 0
                   end as valor_credito,
                   c69_data as data
              from conlancamval
                   inner join conlancamdoc on c71_codlan = c69_codlan
                   inner join conhistdoc   on c53_coddoc = c71_coddoc
             where c69_codlan in ($lista_lancamentos)
               and ( c69_debito  = {$reduzido} or c69_credito = {$reduzido} )
             order by c69_data, c69_codlan

SQL;

        return $sqlLancamentos;

    }

    /**
     * @param $campos
     * @param $where
     * @param string $order
     * @return string
     */
    function sql_query_lancamentos($campos, $where, $order = '')
    {

        $sql = "select {$campos} ";
        $sql .= " from contabilidade.conlancamval ";
        $sql .= "      inner join contabilidade.conlancam on c70_codlan = c69_codlan";
        $sql .= "      inner join contabilidade.conlancamdoc on c70_codlan = c71_codlan";
        $sql .= "      inner join contabilidade.conlancaminstit on c70_codlan = c02_codlan";
        $sql .= "      inner join contabilidade.conplanoreduz reduzdebito on c69_debito = reduzdebito.c61_reduz";
        $sql .= "                                                        and c69_anousu = reduzdebito.c61_anousu";
        $sql .= "      inner join contabilidade.conplano planodebito     on reduzdebito.c61_codcon = planodebito.c60_codcon";
        $sql .= "                                                        and reduzdebito.c61_anousu = planodebito.c60_anousu";
        $sql .= "      inner join contabilidade.conplanoreduz reduzcredito on c69_credito = reduzcredito.c61_reduz";
        $sql .= "                                                        and c69_anousu = reduzcredito.c61_anousu";
        $sql .= "      inner join contabilidade.conplano planocredito    on reduzcredito.c61_codcon = planocredito.c60_codcon";
        $sql .= "                                                        and reduzcredito.c61_anousu = planocredito.c60_anousu";
        $sql .= " where {$where}";
        if (!empty($order)) {
            $sql .= " order by {$order}";
        }

        return $sql;
    }

        /**
     * @param $campos
     * @param $where
     * @param string $order
     * @return string
     */
    function sql_query_lancamentos_novo($campos, $where, $order = '')
    {

        $sql = "
        select 
            distinct {$campos} 
        from 
            contabilidade.conlancamval
            inner join contabilidade.conlancam on c70_codlan = c69_codlan
            inner join contabilidade.conlancamdoc on c70_codlan = c71_codlan
            inner join contabilidade.conlancaminstit on c70_codlan = c02_codlan
            inner join contabilidade.conplanoreduz reduzdebito on c69_debito = reduzdebito.c61_reduz
                and c69_anousu = reduzdebito.c61_anousu
            inner join contabilidade.conplano planodebito on reduzdebito.c61_codcon = planodebito.c60_codcon
                and reduzdebito.c61_anousu = planodebito.c60_anousu
            inner join contabilidade.conplanoreduz reduzcredito on c69_credito = reduzcredito.c61_reduz
                and c69_anousu = reduzcredito.c61_anousu
            inner join contabilidade.conplano planocredito on reduzcredito.c61_codcon = planocredito.c60_codcon
                and reduzcredito.c61_anousu = planocredito.c60_anousu
            left join contabilidade.conlancamemp on c75_codlan = conlancam.c70_codlan
            left join contabilidade.conlancamrec on c74_codlan = conlancam.c70_codlan
            LEFT JOIN orcamento.orcreceita ON orcreceita.o70_codrec = c74_codrec
            LEFT JOIN contabilidade.conlancamele ON c67_codlan = conlancam.c70_codlan
            LEFT JOIN orcamento.orcelemento conele ON conele.o56_codele = c67_codele
                AND conele.o56_anousu = reduzdebito.c61_anousu
            left join conlancamdot on c73_codlan = c70_codlan
            left join orcdotacao on c73_anousu = o58_anousu
                and c73_coddot = o58_coddot
            left join orcelemento orcele ON orcele.o56_codele = orcdotacao.o58_codele
                    AND orcele.o56_anousu = orcdotacao.o58_anousu
";     
        
        $sql .= " where {$where}";
        if (!empty($order)) {
            $sql .= " order by {$order}";
        }

        return $sql;
    }

}
