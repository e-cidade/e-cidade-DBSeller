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

class cl_bnccensinofundamental
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
    public $ed148_sequencial = 0;
    public $ed148_disciplina = null;
    public $ed148_etapa = null;
    public $ed148_codigo = null;
    public $ed148_unidade_tematica = null;
    public $ed148_objeto_conhecimento = null;
    public $ed148_habilidade = null;
    public $ed148_ano = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 ed148_sequencial = int4 = Código
                 ed148_disciplina = varchar(100) = Disciplina
                 ed148_etapa = varchar(100) = Etapa
                 ed148_codigo = varchar(8) = Código BNCC
                 ed148_unidade_tematica = varchar(100) = Unidade Temática
                 ed148_objeto_conhecimento = varchar(255) = Objeto de Conhecimento
                 ed148_habilidade = varchar(255) = Habilidade
                 ed148_ano = int4 = Ano
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("bnccensinofundamental");
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

    public function incluir($ed148_sequencial)
    {
        if ($this->ed148_disciplina == null) {
            $this->erro_sql = " Campo Disciplina não informado.";
            $this->erro_campo = "ed148_disciplina";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ed148_etapa == null) {
            $this->erro_sql = " Campo Etapa não informado.";
            $this->erro_campo = "ed148_etapa";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ed148_codigo == null) {
            $this->erro_sql = " Campo Código BNCC não informado.";
            $this->erro_campo = "ed148_codigo";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ed148_unidade_tematica == null) {
            $this->erro_sql = " Campo Unidade Temática não informado.";
            $this->erro_campo = "ed148_unidade_tematica";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ed148_objeto_conhecimento == null) {
            $this->erro_sql = " Campo Objeto de Conhecimento não informado.";
            $this->erro_campo = "ed148_objeto_conhecimento";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ed148_habilidade == null) {
            $this->erro_sql = " Campo Habilidade não informado.";
            $this->erro_campo = "ed148_habilidade";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ed148_ano == null) {
            $this->erro_sql = " Campo Ano não informado.";
            $this->erro_campo = "ed148_ano";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
            $this->erro_status = "0";
            return false;
        }
        if ($ed148_sequencial == "" || $ed148_sequencial == null) {
            $result = db_query("select nextval('bnccensinofundamental_ed148_sequencial_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: bnccensinofundamental_ed148_sequencial_seq do campo: ed148_sequencial";
                $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
                $this->erro_status = "0";
                return false;
            }
            $this->ed148_sequencial = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from bnccensinofundamental_ed148_sequencial_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $ed148_sequencial)) {
                $this->erro_sql = " Campo ed148_sequencial maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->ed148_sequencial = $ed148_sequencial;
            }
        }
        if (($this->ed148_sequencial == null) || ($this->ed148_sequencial == "")) {
            $this->erro_sql = " Campo ed148_sequencial não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into bnccensinofundamental(
                                       ed148_sequencial
                                      ,ed148_disciplina
                                      ,ed148_etapa
                                      ,ed148_codigo
                                      ,ed148_unidade_tematica
                                      ,ed148_objeto_conhecimento
                                      ,ed148_habilidade
                                      ,ed148_ano
                       )
                values (
                                $this->ed148_sequencial
                               ,'$this->ed148_disciplina'
                               ,'$this->ed148_etapa'
                               ,'$this->ed148_codigo'
                               ,'$this->ed148_unidade_tematica'
                               ,'$this->ed148_objeto_conhecimento'
                               ,'$this->ed148_habilidade'
                               ,$this->ed148_ano
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = " ($this->ed148_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
                $this->erro_banco = " já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
            } else {
                $this->erro_sql = " ($this->ed148_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\n";
        $this->erro_sql .= "Valores : " . $this->ed148_sequencial;
        $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
        $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->ed148_sequencial));
            if (($resaco != false) || ($this->numrows != 0)) {

                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                $resac = db_query("insert into db_acountkey values($acount,1010915,'$this->ed148_sequencial','I')");
                $resac = db_query("insert into db_acount values($acount,1010503,1010915,'','" . AddSlashes(pg_result($resaco, 0, 'ed148_sequencial')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010503,1010916,'','" . AddSlashes(pg_result($resaco, 0, 'ed148_disciplina')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010503,1010917,'','" . AddSlashes(pg_result($resaco, 0, 'ed148_etapa')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010503,1010918,'','" . AddSlashes(pg_result($resaco, 0, 'ed148_codigo')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010503,1010919,'','" . AddSlashes(pg_result($resaco, 0, 'ed148_unidade_tematica')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010503,1010920,'','" . AddSlashes(pg_result($resaco, 0, 'ed148_objeto_conhecimento')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010503,1010921,'','" . AddSlashes(pg_result($resaco, 0, 'ed148_habilidade')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010503,1011781,'','" . AddSlashes(pg_result($resaco, 0, 'ed148_ano')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            }
        }
        return true;
    }

    public function alterar($ed148_sequencial = null)
    {
        $sql = " update bnccensinofundamental set ";
        $virgula = "";
        if (trim($this->ed148_sequencial) != "" || isset($_POST["ed148_sequencial"])) {
            $sql .= $virgula . " ed148_sequencial = $this->ed148_sequencial ";
            $virgula = ",";
            if (trim($this->ed148_sequencial) == null) {
                $this->erro_sql = " Campo Código não informado.";
                $this->erro_campo = "ed148_sequencial";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->ed148_disciplina) != "" || isset($_POST["ed148_disciplina"])) {
            $sql .= $virgula . " ed148_disciplina = '$this->ed148_disciplina' ";
            $virgula = ",";
            if (trim($this->ed148_disciplina) == null) {
                $this->erro_sql = " Campo Disciplina não informado.";
                $this->erro_campo = "ed148_disciplina";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->ed148_etapa) != "" || isset($_POST["ed148_etapa"])) {
            $sql .= $virgula . " ed148_etapa = '$this->ed148_etapa' ";
            $virgula = ",";
            if (trim($this->ed148_etapa) == null) {
                $this->erro_sql = " Campo Etapa não informado.";
                $this->erro_campo = "ed148_etapa";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->ed148_codigo) != "" || isset($_POST["ed148_codigo"])) {
            $sql .= $virgula . " ed148_codigo = '$this->ed148_codigo' ";
            $virgula = ",";
            if (trim($this->ed148_codigo) == null) {
                $this->erro_sql = " Campo Código BNCC não informado.";
                $this->erro_campo = "ed148_codigo";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->ed148_unidade_tematica) != "" || isset($_POST["ed148_unidade_tematica"])) {
            $sql .= $virgula . " ed148_unidade_tematica = '$this->ed148_unidade_tematica' ";
            $virgula = ",";
            if (trim($this->ed148_unidade_tematica) == null) {
                $this->erro_sql = " Campo Unidade Temática não informado.";
                $this->erro_campo = "ed148_unidade_tematica";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->ed148_objeto_conhecimento) != "" || isset($_POST["ed148_objeto_conhecimento"])) {
            $sql .= $virgula . " ed148_objeto_conhecimento = '$this->ed148_objeto_conhecimento' ";
            $virgula = ",";
            if (trim($this->ed148_objeto_conhecimento) == null) {
                $this->erro_sql = " Campo Objeto de Conhecimento não informado.";
                $this->erro_campo = "ed148_objeto_conhecimento";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->ed148_habilidade) != "" || isset($_POST["ed148_habilidade"])) {
            $sql .= $virgula . " ed148_habilidade = '$this->ed148_habilidade' ";
            $virgula = ",";
            if (trim($this->ed148_habilidade) == null) {
                $this->erro_sql = " Campo Habilidade não informado.";
                $this->erro_campo = "ed148_habilidade";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->ed148_ano) != "" || isset($_POST["ed148_ano"])) {
            $sql .= $virgula . " ed148_ano = $this->ed148_ano ";
            $virgula = ",";
            if (trim($this->ed148_ano) == null) {
                $this->erro_sql = " Campo Ano não informado.";
                $this->erro_campo = "ed148_ano";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($ed148_sequencial != null) {
            $sql .= " ed148_sequencial = $this->ed148_sequencial";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->ed148_sequencial));
            if ($this->numrows > 0) {

                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,1010915,'$this->ed148_sequencial','A')");
                    if (isset($_POST["ed148_sequencial"]) || $this->ed148_sequencial != "")
                        $resac = db_query("insert into db_acount values($acount,1010503,1010915,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed148_sequencial')) . "','$this->ed148_sequencial'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($_POST["ed148_disciplina"]) || $this->ed148_disciplina != "")
                        $resac = db_query("insert into db_acount values($acount,1010503,1010916,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed148_disciplina')) . "','$this->ed148_disciplina'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($_POST["ed148_etapa"]) || $this->ed148_etapa != "")
                        $resac = db_query("insert into db_acount values($acount,1010503,1010917,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed148_etapa')) . "','$this->ed148_etapa'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($_POST["ed148_codigo"]) || $this->ed148_codigo != "")
                        $resac = db_query("insert into db_acount values($acount,1010503,1010918,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed148_codigo')) . "','$this->ed148_codigo'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($_POST["ed148_unidade_tematica"]) || $this->ed148_unidade_tematica != "")
                        $resac = db_query("insert into db_acount values($acount,1010503,1010919,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed148_unidade_tematica')) . "','$this->ed148_unidade_tematica'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($_POST["ed148_objeto_conhecimento"]) || $this->ed148_objeto_conhecimento != "")
                        $resac = db_query("insert into db_acount values($acount,1010503,1010920,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed148_objeto_conhecimento')) . "','$this->ed148_objeto_conhecimento'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($_POST["ed148_habilidade"]) || $this->ed148_habilidade != "")
                        $resac = db_query("insert into db_acount values($acount,1010503,1010921,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed148_habilidade')) . "','$this->ed148_habilidade'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($_POST["ed148_ano"]) || $this->ed148_ano != "")
                        $resac = db_query("insert into db_acount values($acount,1010503,1011781,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed148_ano')) . "','$this->ed148_ano'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = " não Alterado. Alteração Abortada.\n";
            $this->erro_sql .= "Valores : " . $this->ed148_sequencial;
            $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = " não foi Alterado. Alteração Executada.\n";
                $this->erro_sql .= "Valores : " . $this->ed148_sequencial;
                $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\n";
                $this->erro_sql .= "Valores : " . $this->ed148_sequencial;
                $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }


    public function temVinculoDiarioDeClasse($codHabilidades) 
    {
        $codHabilidades = is_array($codHabilidades) ? "in(".implode(",", $codHabilidades).")" : "= '". $codHabilidades ."'";
        $sqlVerificaVinculoHabilidadesDiarioClasse = "  select 1 
                                                            from diario_classe_bncc_habilidade 
                                                        where ed156_habilidade {$codHabilidades}";
    
        if (pg_numrows(db_query($sqlVerificaVinculoHabilidadesDiarioClasse)) > 0) {
            return true;
        } 
        return false;
    }

    public function excluirObjetoConhecimento($objeto) {
        $objeto = pg_escape_string($objeto);
        $sqlBusca = "select distinct ed148_codigo from bnccensinofundamental where ed148_objeto_conhecimento = '{$objeto}'";
        $rsBusca = db_query($sqlBusca);
        $codHabilidades = [];
        while ($result = pg_fetch_assoc($rsBusca)) {
            $codHabilidades[] = "'". $result['ed148_codigo'] ."'";
        }
        if ($this->temVinculoDiarioDeClasse($codHabilidades)) {
            $this->erro_status = "0";
            $this->erro_msg = "Não foi possível excluir objeto pois suas habilidades possuem vínculos com diários de classe.";
            $this->erro_banco = "Não foi possível excluir objeto pois suas habilidades possuem vínculos com diários de classe.";
        }
        $whereExcluir = "ed148_objeto_conhecimento = '{$objeto}' and ed148_codigo in(".implode(",", $codHabilidades).")";
        $this->excluir(null, $whereExcluir);
    }

    public function alterarObjetoConhecimento($ed148_sequencial = null, $where)
    {
       
        $sql = " update bnccensinofundamental set ed148_objeto_conhecimento = '{$this->ed148_objeto_conhecimento}'";
        $sql .= " where";
        if ($ed148_sequencial != null) {
            $sql .= " and ed148_sequencial = {$this->ed148_sequencial}";
        }

        if (isset($where->ed148_disciplina)) {
            $sql .= " ed148_disciplina = '{$where->ed148_disciplina}'";
        }

        if (isset($where->ed148_unidade_tematica)) {
            $sql .= " and ed148_unidade_tematica = '{$where->ed148_unidade_tematica}'";
        }

        if (isset($where->ed148_objeto_conhecimento)) {
            $sql .= " and ed148_objeto_conhecimento = '{$where->ed148_objeto_conhecimento}'";
        }

        if (isset($where->ed148_ano)) {
            $sql .= " and ed148_ano = '{$where->ed148_ano}'";
        }

        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = " não Alterado. Alteração Abortada.\n";
            $this->erro_sql .= "Valores : " . $this->ed148_sequencial;
            $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = " não foi Alterado. Alteração Executada.\n";
                $this->erro_sql .= "Valores : " . $this->ed148_sequencial;
                $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\n";
                $this->erro_sql .= "Valores : " . $this->ed148_sequencial;
                $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
    public function excluir($ed148_sequencial = null, $dbwhere = null)
    {
      
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            if (empty($dbwhere)) {

                $resaco = db_query($this->sql_query_file($ed148_sequencial));
            } else {
                $resaco = db_query($this->sql_query_file(null, "*", null, $dbwhere));
            }
            if (($resaco != false) || ($this->numrows != 0)) {

                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,1010915,'$ed148_sequencial','E')");
                    $resac = db_query("insert into db_acount values($acount,1010503,1010915,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed148_sequencial')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010503,1010916,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed148_disciplina')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010503,1010917,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed148_etapa')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010503,1010918,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed148_codigo')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010503,1010919,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed148_unidade_tematica')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010503,1010920,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed148_objeto_conhecimento')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010503,1010921,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed148_habilidade')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010503,1011781,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed148_ano')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }
        $sql = " delete from bnccensinofundamental
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($ed148_sequencial)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " ed148_sequencial = $ed148_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = " não Excluído. Exclusão Abortada.\n";
            $this->erro_sql .= "Valores : " . $ed148_sequencial;
            $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = " não Encontrado. Exclusão não Efetuada.\n";
                $this->erro_sql .= "Valores : " . $ed148_sequencial;
                $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\n";
                $this->erro_sql .= "Valores : " . $ed148_sequencial;
                $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
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
            $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
            $this->erro_status = "0";
            return false;
        }
        $this->numrows = pg_num_rows($result);
        if ($this->numrows == 0) {
            $this->erro_banco = "";
            $this->erro_sql = "Record Vazio na Tabela:bnccensinofundamental";
            $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \n\n " . $this->erro_banco . " \n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($ed148_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos}";
        $sql .= "  from bnccensinofundamental ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($ed148_sequencial)) {
                $sql2 .= " where bnccensinofundamental.ed148_sequencial = $ed148_sequencial ";
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

    public function sql_query_file($ed148_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos} ";
        $sql .= "  from bnccensinofundamental ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($ed148_sequencial)) {
                $sql2 .= " where bnccensinofundamental.ed148_sequencial = $ed148_sequencial ";
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
