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

class cl_gruposliberacao
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
    public $la71_sequencial = 0;
    public $la71_descricao = null;
    public $la71_setor = 0;
    public $la71_dbusuarios = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 la71_sequencial = int4 = Código de identificação do grupo de liberação.
                 la71_descricao = varchar(255) = Descrição do grupo de liberação.
                 la71_setor = int4 = Setor do laboratório.
                 la71_dbusuarios = int4 = Usuário do grupo.
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("gruposliberacao");
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
    }

    public function erro($mostra, $retorna)
    {
        if (($this->erro_status == "0") || ($mostra == true && $this->erro_status != null)) {
            echo "<script>alert(\"".$this->erro_msg."\")</script>";
            if ($retorna == true) {
                echo "<script>location.href='".$this->pagina_retorno."'</script>";
            }
        }
    }

    public function atualizacampos($exclusao = false)
    {
        if ($exclusao == false) {
            $this->la71_sequencial = ($this->la71_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["la71_sequencial"] : $this->la71_sequencial);
            $this->la71_descricao = ($this->la71_descricao == "" ? @$GLOBALS["HTTP_POST_VARS"]["la71_descricao"] : $this->la71_descricao);
            $this->la71_setor = ($this->la71_setor == "" ? @$GLOBALS["HTTP_POST_VARS"]["la71_setor"] : $this->la71_setor);
            $this->la71_dbusuarios = ($this->la71_dbusuarios == "" ? @$GLOBALS["HTTP_POST_VARS"]["la71_dbusuarios"] : $this->la71_dbusuarios);
        } else {
            $this->la71_sequencial = ($this->la71_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["la71_sequencial"] : $this->la71_sequencial);
        }
    }

    public function incluir($la71_sequencial)
    {
        $this->atualizacampos();
        if ($this->la71_descricao == null) {
            $this->erro_sql = " Campo Descrição do grupo de liberação. não informado.";
            $this->erro_campo = "la71_descricao";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
            );
            $this->erro_status = "0";
            return false;
        }
        if ($this->la71_dbusuarios == null) {
            $this->erro_sql = " Campo Usuário do grupo. não informado.";
            $this->erro_campo = "la71_dbusuarios";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
            );
            $this->erro_status = "0";
            return false;
        }
        if ($la71_sequencial == "" || $la71_sequencial == null) {
            $result = db_query("select nextval('gruposliberacao_la71_sequencial_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: gruposliberacao_la71_sequencial_seq do campo: la71_sequencial";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "0";
                return false;
            }
            $this->la71_sequencial = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from gruposliberacao_la71_sequencial_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $la71_sequencial)) {
                $this->erro_sql = " Campo la71_sequencial maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "0";
                return false;
            } else {
                $this->la71_sequencial = $la71_sequencial;
            }
        }
        if (($this->la71_sequencial == null) || ($this->la71_sequencial == "")) {
            $this->erro_sql = " Campo la71_sequencial não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
            );
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into gruposliberacao(
                                       la71_sequencial
                                      ,la71_descricao
                                      ,la71_setor
                                      ,la71_dbusuarios
                       )
                values (
                                $this->la71_sequencial
                               ,'$this->la71_descricao'
                               ,$this->la71_setor
                               ,$this->la71_dbusuarios
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "Tabela de grupos de liberação. ($this->la71_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Tabela de grupos de liberação. já Cadastrado";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
            } else {
                $this->erro_sql = "Tabela de grupos de liberação. ($this->la71_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : ".$this->la71_sequencial;
        $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg .= str_replace(
            '"',
            "",
            str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
        );
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        return true;
    }

    public function alterar($la71_sequencial = null)
    {
        $this->atualizacampos();
        $sql = " update gruposliberacao set ";
        $virgula = "";
        if (trim($this->la71_sequencial) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la71_sequencial"])) {
            $sql .= $virgula." la71_sequencial = $this->la71_sequencial ";
            $virgula = ",";
            if (trim($this->la71_sequencial) == null) {
                $this->erro_sql = " Campo Código de identificação do grupo de liberação. não informado.";
                $this->erro_campo = "la71_sequencial";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->la71_descricao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la71_descricao"])) {
            $sql .= $virgula." la71_descricao = '$this->la71_descricao' ";
            $virgula = ",";
            if (trim($this->la71_descricao) == null) {
                $this->erro_sql = " Campo Descrição do grupo de liberação. não informado.";
                $this->erro_campo = "la71_descricao";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->la71_setor) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la71_setor"])) {
            if (trim($this->la71_setor) == "" && isset($GLOBALS["HTTP_POST_VARS"]["la71_setor"])) {
                $this->la71_setor = "0";
            }
            $sql .= $virgula." la71_setor = $this->la71_setor ";
            $virgula = ",";
        }
        if (trim($this->la71_dbusuarios) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la71_dbusuarios"])) {
            $sql .= $virgula." la71_dbusuarios = $this->la71_dbusuarios ";
            $virgula = ",";
            if (trim($this->la71_dbusuarios) == null) {
                $this->erro_sql = " Campo Usuário do grupo. não informado.";
                $this->erro_campo = "la71_dbusuarios";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($la71_sequencial != null) {
            $sql .= " la71_sequencial = $this->la71_sequencial";
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Tabela de grupos de liberação. não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->la71_sequencial;
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
            );
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Tabela de grupos de liberação. não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->la71_sequencial;
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$this->la71_sequencial;
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($la71_sequencial = null, $dbwhere = null)
    {
        $sql = " delete from gruposliberacao
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($la71_sequencial)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " la71_sequencial = $la71_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Tabela de grupos de liberação. não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$la71_sequencial;
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
            );
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Tabela de grupos de liberação. não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$la71_sequencial;
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$la71_sequencial;
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
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
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
            );
            $this->erro_status = "0";
            return false;
        }
        $this->numrows = pg_num_rows($result);
        if ($this->numrows == 0) {
            $this->erro_banco = "";
            $this->erro_sql = "Record Vazio na Tabela:gruposliberacao";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
            );
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($la71_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos}";
        $sql .= "  from gruposliberacao ";
        $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = gruposliberacao.la71_dbusuarios";
        $sql .= "      left  join lab_setor  on  lab_setor.la23_i_codigo = gruposliberacao.la71_setor";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($la71_sequencial)) {
                $sql2 .= " where gruposliberacao.la71_sequencial = $la71_sequencial ";
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

    public function sql_query_file($la71_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos} ";
        $sql .= "  from gruposliberacao ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($la71_sequencial)) {
                $sql2 .= " where gruposliberacao.la71_sequencial = $la71_sequencial ";
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

}
