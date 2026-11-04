<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                    www.dbseller.com.br
 *                 e-cidade@dbseller.com.br
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
//MODULO: esocial
//CLASSE DA ENTIDADE avaliacaogruporespostaaltercontratual
class cl_avaliacaogruporespostaaltercontratual
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
    var $eso20_sequencial = 0;
    var $eso20_avaliacaogruporesposta = 0;
    var $eso20_cgm = 0;
    var $eso20_rhpessoal = 0;
    // cria propriedade com as variaveis do arquivo
    var $campos = "
                 eso20_sequencial = int4 = Sequencial 
                 eso20_avaliacaogruporesposta = int4 = Resposta 
                 eso20_cgm = int4 = CGM 
                 eso20_rhpessoal = int4 = Pessoal 
                 ";

    //funcao construtor da classe
    public function __construct()
    {
        //classes dos rotulos dos campos
        $this->rotulo = new rotulo("avaliacaogruporespostaaltercontratual");
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
            $this->eso20_sequencial = ($this->eso20_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["eso20_sequencial"] : $this->eso20_sequencial);
            $this->eso20_avaliacaogruporesposta = ($this->eso20_avaliacaogruporesposta == "" ? @$GLOBALS["HTTP_POST_VARS"]["eso20_avaliacaogruporesposta"] : $this->eso20_avaliacaogruporesposta);
            $this->eso20_cgm = ($this->eso20_cgm == "" ? @$GLOBALS["HTTP_POST_VARS"]["eso20_cgm"] : $this->eso20_cgm);
            $this->eso20_rhpessoal = ($this->eso20_rhpessoal == "" ? @$GLOBALS["HTTP_POST_VARS"]["eso20_rhpessoal"] : $this->eso20_rhpessoal);
        } else {
            $this->eso20_sequencial = ($this->eso20_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["eso20_sequencial"] : $this->eso20_sequencial);
        }
    }

    // funcao para Inclusão
    public function incluir($eso20_sequencial)
    {
        $this->atualizacampos();
        if ($this->eso20_avaliacaogruporesposta == null) {
            $this->erro_sql = " Campo Resposta não informado.";
            $this->erro_campo = "eso20_avaliacaogruporesposta";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->eso20_cgm == null) {
            $this->erro_sql = " Campo CGM não informado.";
            $this->erro_campo = "eso20_cgm";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->eso20_rhpessoal == null) {
            $this->erro_sql = " Campo Pessoal não informado.";
            $this->erro_campo = "eso20_rhpessoal";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($eso20_sequencial == "" || $eso20_sequencial == null) {
            $result = db_query("select nextval('avaliacaogruporespostaaltercontratual_eso20_sequencial_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: avaliacaogruporespostaaltercontratual_eso20_sequencial_seq do campo: eso20_sequencial";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->eso20_sequencial = pg_result($result, 0, 0);
        } else {
            $result = db_query("SELECT last_value FROM avaliacaogruporespostaaltercontratual_eso20_sequencial_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $eso20_sequencial)) {
                $this->erro_sql = " Campo eso20_sequencial maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->eso20_sequencial = $eso20_sequencial;
            }
        }
        if (($this->eso20_sequencial == null) || ($this->eso20_sequencial == "")) {
            $this->erro_sql = " Campo eso20_sequencial não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into avaliacaogruporespostaaltercontratual(
                                       eso20_sequencial 
                                      ,eso20_avaliacaogruporesposta 
                                      ,eso20_cgm 
                                      ,eso20_rhpessoal 
                       )
                values (
                                $this->eso20_sequencial 
                               ,$this->eso20_avaliacaogruporesposta 
                               ,$this->eso20_cgm 
                               ,$this->eso20_rhpessoal 
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "avaliacaogruporespostaaltercontratual ($this->eso20_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "avaliacaogruporespostaaltercontratual já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "avaliacaogruporespostaaltercontratual ($this->eso20_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->eso20_sequencial;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->eso20_sequencial));
            if (($resaco != false) || ($this->numrows != 0)) {

                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                $resac = db_query("insert into db_acountkey values($acount,1009941,'$this->eso20_sequencial','I')");
                $resac = db_query("insert into db_acount values($acount,1010315,1009941,'','" . AddSlashes(pg_result($resaco, 0, 'eso20_sequencial')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010315,1009942,'','" . AddSlashes(pg_result($resaco, 0, 'eso20_avaliacaogruporesposta')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010315,1009943,'','" . AddSlashes(pg_result($resaco, 0, 'eso20_cgm')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010315,1009944,'','" . AddSlashes(pg_result($resaco, 0, 'eso20_rhpessoal')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            }
        }
        return true;
    }

    // funcao para alteracao
    public function alterar($eso20_sequencial = null)
    {
        $this->atualizacampos();
        $sql = " UPDATE avaliacaogruporespostaaltercontratual SET ";
        $virgula = "";
        if (trim($this->eso20_sequencial) != "" || isset($GLOBALS["HTTP_POST_VARS"]["eso20_sequencial"])) {
            $sql .= $virgula . " eso20_sequencial = $this->eso20_sequencial ";
            $virgula = ",";
            if (trim($this->eso20_sequencial) == null) {
                $this->erro_sql = " Campo Sequencial não informado.";
                $this->erro_campo = "eso20_sequencial";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->eso20_avaliacaogruporesposta) != "" || isset($GLOBALS["HTTP_POST_VARS"]["eso20_avaliacaogruporesposta"])) {
            $sql .= $virgula . " eso20_avaliacaogruporesposta = $this->eso20_avaliacaogruporesposta ";
            $virgula = ",";
            if (trim($this->eso20_avaliacaogruporesposta) == null) {
                $this->erro_sql = " Campo Resposta não informado.";
                $this->erro_campo = "eso20_avaliacaogruporesposta";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->eso20_cgm) != "" || isset($GLOBALS["HTTP_POST_VARS"]["eso20_cgm"])) {
            $sql .= $virgula . " eso20_cgm = $this->eso20_cgm ";
            $virgula = ",";
            if (trim($this->eso20_cgm) == null) {
                $this->erro_sql = " Campo CGM não informado.";
                $this->erro_campo = "eso20_cgm";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->eso20_rhpessoal) != "" || isset($GLOBALS["HTTP_POST_VARS"]["eso20_rhpessoal"])) {
            $sql .= $virgula . " eso20_rhpessoal = $this->eso20_rhpessoal ";
            $virgula = ",";
            if (trim($this->eso20_rhpessoal) == null) {
                $this->erro_sql = " Campo Pessoal não informado.";
                $this->erro_campo = "eso20_rhpessoal";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($eso20_sequencial != null) {
            $sql .= " eso20_sequencial = $this->eso20_sequencial";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->eso20_sequencial));
            if ($this->numrows > 0) {

                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,1009941,'$this->eso20_sequencial','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["eso20_sequencial"]) || $this->eso20_sequencial != "")
                        $resac = db_query("insert into db_acount values($acount,1010315,1009941,'" . AddSlashes(pg_result($resaco, $conresaco, 'eso20_sequencial')) . "','$this->eso20_sequencial'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["eso20_avaliacaogruporesposta"]) || $this->eso20_avaliacaogruporesposta != "")
                        $resac = db_query("insert into db_acount values($acount,1010315,1009942,'" . AddSlashes(pg_result($resaco, $conresaco, 'eso20_avaliacaogruporesposta')) . "','$this->eso20_avaliacaogruporesposta'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["eso20_cgm"]) || $this->eso20_cgm != "")
                        $resac = db_query("insert into db_acount values($acount,1010315,1009943,'" . AddSlashes(pg_result($resaco, $conresaco, 'eso20_cgm')) . "','$this->eso20_cgm'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["eso20_rhpessoal"]) || $this->eso20_rhpessoal != "")
                        $resac = db_query("insert into db_acount values($acount,1010315,1009944,'" . AddSlashes(pg_result($resaco, $conresaco, 'eso20_rhpessoal')) . "','$this->eso20_rhpessoal'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "avaliacaogruporespostaaltercontratual não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->eso20_sequencial;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "avaliacaogruporespostaaltercontratual não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->eso20_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $this->eso20_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    // funcao para exclusao
    public function excluir($eso20_sequencial = null, $dbwhere = null)
    {

        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            if (empty($dbwhere)) {

                $resaco = $this->sql_record($this->sql_query_file($eso20_sequencial));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
            }
            if (($resaco != false) || ($this->numrows != 0)) {

                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,1009941,'$eso20_sequencial','E')");
                    $resac = db_query("insert into db_acount values($acount,1010315,1009941,'','" . AddSlashes(pg_result($resaco, $iresaco, 'eso20_sequencial')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010315,1009942,'','" . AddSlashes(pg_result($resaco, $iresaco, 'eso20_avaliacaogruporesposta')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010315,1009943,'','" . AddSlashes(pg_result($resaco, $iresaco, 'eso20_cgm')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010315,1009944,'','" . AddSlashes(pg_result($resaco, $iresaco, 'eso20_rhpessoal')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }
        $sql = " DELETE FROM avaliacaogruporespostaaltercontratual
                    WHERE ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($eso20_sequencial)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " eso20_sequencial = $eso20_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "avaliacaogruporespostaaltercontratual não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $eso20_sequencial;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "avaliacaogruporespostaaltercontratual não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $eso20_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $eso20_sequencial;
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
            $this->erro_sql = "Record Vazio na Tabela:avaliacaogruporespostaaltercontratual";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    // funcao do sql
    public function sql_query($eso20_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos}";
        $sql .= "  from avaliacaogruporespostaaltercontratual ";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = avaliacaogruporespostaaltercontratual.eso20_cgm";
        $sql .= "      inner join rhpessoal  on  rhpessoal.rh01_regist = avaliacaogruporespostaaltercontratual.eso20_rhpessoal";
        $sql .= "      inner join avaliacaogruporesposta  on  avaliacaogruporesposta.db107_sequencial = avaliacaogruporespostaaltercontratual.eso20_avaliacaogruporesposta";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = rhpessoal.rh01_numcgm";
        $sql .= "      inner join rhestcivil  on  rhestcivil.rh08_estciv = rhpessoal.rh01_estciv";
        $sql .= "      inner join rhraca  on  rhraca.rh18_raca = rhpessoal.rh01_raca";
        $sql .= "      left  join rhfuncao  on  rhfuncao.rh37_funcao = rhpessoal.rh01_funcao and  rhfuncao.rh37_instit = rhpessoal.rh01_instit";
        $sql .= "      inner join rhinstrucao  on  rhinstrucao.rh21_instru = rhpessoal.rh01_instru";
        $sql .= "      inner join rhnacionalidade  on  rhnacionalidade.rh06_nacionalidade = rhpessoal.rh01_nacion";
        $sql .= "      left  join rhsindicato  on  rhsindicato.rh116_sequencial = rhpessoal.rh01_rhsindicato";
        $sql .= "      inner join rhreajusteparidade  on  rhreajusteparidade.rh148_sequencial = rhpessoal.rh01_reajusteparidade";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($eso20_sequencial)) {
                $sql2 .= " where avaliacaogruporespostaaltercontratual.eso20_sequencial = $eso20_sequencial ";
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

    // funcao do sql
    public function sql_query_file($eso20_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos} ";
        $sql .= "  from avaliacaogruporespostaaltercontratual ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($eso20_sequencial)) {
                $sql2 .= " where avaliacaogruporespostaaltercontratual.eso20_sequencial = $eso20_sequencial ";
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

    public function buscarRespostasPreenchimento(array $campos = array('*'), array $where = array(), $outrosComandos = null)
    {
        $sql = " SELECT DISTINCT " . implode(', ', $campos);
        $sql .= "  FROM avaliacaogruporespostaaltercontratual";
        $sql .= "  INNER JOIN avaliacaogruporesposta ON db107_sequencial = eso20_avaliacaogruporesposta ";
        $sql .= "  INNER JOIN avaliacaogrupoperguntaresposta ON db108_avaliacaogruporesposta = db107_sequencial";
        $sql .= "  INNER JOIN avaliacaoresposta ON db106_sequencial = db108_avaliacaoresposta ";
        $sql .= "  INNER JOIN avaliacaoperguntaopcao ON db104_sequencial = db106_avaliacaoperguntaopcao ";
        $sql .= "  INNER JOIN avaliacaopergunta ON db103_sequencial = db104_avaliacaopergunta ";
        $sql .= "  INNER JOIN avaliacaogrupopergunta ON db103_avaliacaogrupopergunta = db102_sequencial ";
        $sql .= "  INNER JOIN avaliacao ON db102_avaliacao = db101_sequencial ";
        $sql .= "  INNER JOIN cgm ON z01_numcgm = eso20_cgm";

        if (!empty($where)) {
            $sql .= " where " . implode(' and ', $where);
        }

        if (!empty($outrosComandos)) {
            $sql .= " {$outrosComandos} ";
        }

        return $sql;
    }

    public function sqlDadosAlteracaoCadastral($matricula)
    {
        $sql = "
            SELECT rh01_regist AS matricula,
               rh55_descr AS localtrabgeral_desccomp,
               rh116_cnpj AS cnpjsindcategprof,
               z01_cgccpf AS cpfTrab,
               rh16_pis AS nisTrab,
               z01_nomecomple anmTrab,
               z01_sexo AS sexo,
               rh18_descr AS racaCor,
               rh01_estciv AS estCiv,
               rh01_instru AS grauInstr,
               to_char(rh01_nasc, 'DD/MM/YYYY') AS dtNascto,
               CASE WHEN z01_nacion = 1
                         THEN 'brasil'
                    ELSE z09_pais
                   END AS paisNascto,
               CASE WHEN z01_nacion = 1
                         THEN 'brasil'
                    ELSE z09_pais
                   END AS paisNac,
               z01_pai AS nmPai,
               z01_mae AS nmMae,
            -- nacionalidade
               CASE WHEN z01_nacion = 2
                         THEN z09_documento
                    ELSE NULL END AS nrRne,
               z01_ident AS nrRg,
               z01_identorgao AS orgaoEmissor_RG,
               to_char(z01_identdtexp, 'DD/MM/YYYY') AS dtExped_RG,
               rh16_ctps_n :: TEXT || rh16_ctps_d :: TEXT AS nrCtps,
               rh16_ctps_s AS serieCtps,
               rh16_ctps_uf AS ufCtps,
               rh16_carth_n AS nrRegCnh,
               r16_carth_cat AS categoriaCnh,
               to_char(rh16_carth_val, 'DD/MM/YYYY') AS dtValid_CNH,
               j88_descricao AS tpLograd,
               db74_descricao AS dscLograd,
               db75_numero AS nrLograd,
               db76_complemento AS complemento,
               db73_descricao AS bairro,
               db72_descricao AS municipio,
               db76_cep AS cep,
               db125_codigosistema AS codMunic,
               db71_sigla AS uf,
               db70_descricao AS pais,
               db70_sigla AS sigla_pais,
               z01_telef AS fonePrinc,
               z01_telcel AS foneAlternat,
               z01_email AS emailPrinc,
               rh02_funcao AS codcargo,
               rh20_cargo AS codfuncao,
               CASE WHEN rh15_data IS NULL
                         THEN 2
                    ELSE 1
                   END AS opcFGTS,
               to_char(rh15_data, 'DD/MM/YYYY') AS dtOpcFGTS,
               (SELECT f010
                FROM fc_variaveis_matricula(rh01_regist, fc_anofolha(rh01_instit), fc_mesfolha(rh01_instit),
                                            rh01_instit)) AS vrSalFx,
               rh02_tipsal AS undSalFixo,
               rh30_regime AS tpRegTrab,
               CASE WHEN rh02_tbprev = 1
                         THEN 1
                    ELSE 2
                   END AS tpRegPrev,
               rh02_cedencia,
               rh02_onus AS infOnus,
               rh02_cnpjcedencia AS cnpjCednt
        FROM rhpessoal
               JOIN cgm ON cgm.z01_numcgm = rhpessoal.rh01_numcgm
               LEFT JOIN cgmestrangeiro ON cgmestrangeiro.z09_numcgm = cgm.z01_numcgm
               LEFT JOIN rhpesdoc ON rhpesdoc.rh16_regist = rhpessoal.rh01_regist
               JOIN rhraca ON rhraca.rh18_raca = rhpessoal.rh01_raca
               JOIN patrimonio.cgmendereco ON cgmendereco.z07_numcgm = cgm.z01_numcgm
                                                AND z07_tipo = 'P'
               JOIN configuracoes.endereco ON endereco.db76_sequencial = cgmendereco.z07_endereco
               JOIN configuracoes.cadenderlocal ON cadenderlocal.db75_sequencial = endereco.db76_cadenderlocal
               JOIN configuracoes.cadenderbairrocadenderrua
                 ON cadenderbairrocadenderrua.db87_sequencial = cadenderlocal.db75_cadenderbairrocadenderrua
               JOIN configuracoes.cadenderrua ON cadenderrua.db74_sequencial = cadenderbairrocadenderrua.db87_cadenderrua
               JOIN configuracoes.cadenderbairro
                 ON cadenderbairro.db73_sequencial = cadenderbairrocadenderrua.db87_cadenderbairro
               JOIN configuracoes.cadendermunicipio ON cadendermunicipio.db72_sequencial = cadenderrua.db74_cadendermunicipio
               LEFT JOIN configuracoes.cadendermunicipiosistema
                 ON cadendermunicipiosistema.db125_cadendermunicipio = cadendermunicipio.db72_sequencial
               LEFT JOIN configuracoes.db_sistemaexterno
                 ON db_sistemaexterno.db124_sequencial = cadendermunicipiosistema.db125_db_sistemaexterno
                      AND db_sistemaexterno.db124_descricao = 'IBGE'
               JOIN configuracoes.cadenderestado ON cadenderestado.db71_sequencial = cadendermunicipio.db72_cadenderestado
               JOIN configuracoes.cadenderpais ON cadenderpais.db70_sequencial = cadenderestado.db71_cadenderpais
               JOIN configuracoes.cadenderruaruastipo ON cadenderruaruastipo.db85_cadenderrua = cadenderrua.db74_sequencial
               JOIN cadastro.ruastipo ON ruastipo.j88_codigo = cadenderruaruastipo.db85_ruastipo
               JOIN rhpessoalmov ON rhpessoalmov.rh02_regist = rhpessoal.rh01_regist
                                      AND rh02_anousu = fc_anofolha(rh02_instit)
                                      AND rh02_mesusu = fc_mesfolha(rh02_instit)
               LEFT JOIN rhpescargo ON rhpescargo.rh20_seqpes = rhpessoalmov.rh02_seqpes
               LEFT JOIN rhpesfgts ON rhpesfgts.rh15_regist = rhpessoal.rh01_regist
               JOIN rhregime ON rhregime.rh30_codreg = rhpessoalmov.rh02_codreg
               LEFT JOIN rhsindicato  on  rhsindicato.rh116_sequencial = rhpessoal.rh01_rhsindicato
               LEFT JOIN rhpeslocaltrab on rh56_seqpes = rh02_seqpes
               LEFT JOIN rhlocaltrab    on rh55_codigo = rh56_localtrab
		                                and rh55_instit = rhpessoalmov.rh02_instit
        WHERE rhpessoal.rh01_regist = {$matricula}
        ";

        return $sql;
    }

}
