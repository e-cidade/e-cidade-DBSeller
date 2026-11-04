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

class cl_conplanoorcamentoanalitica
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
    public $c61_reduz = 0;
    public $c61_codcon = 0;
    public $c61_anousu = 0;
    public $c61_instit = 0;
    public $c61_codigo = 0;
    public $c61_contrapartida = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 c61_reduz = int4 = Reduzido
                 c61_codcon = int4 = Código da Conta
                 c61_anousu = int4 = Exercício
                 c61_instit = int4 = Instituição
                 c61_codigo = int4 = Código do Recurso
                 c61_contrapartida = int4 = Contra Partida
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("conplanoorcamentoanalitica");
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
            $this->c61_reduz = ($this->c61_reduz == "" ? @$GLOBALS["HTTP_POST_VARS"]["c61_reduz"] : $this->c61_reduz);
            $this->c61_codcon = ($this->c61_codcon == "" ? @$GLOBALS["HTTP_POST_VARS"]["c61_codcon"] : $this->c61_codcon);
            $this->c61_anousu = ($this->c61_anousu == "" ? @$GLOBALS["HTTP_POST_VARS"]["c61_anousu"] : $this->c61_anousu);
            $this->c61_instit = ($this->c61_instit == "" ? @$GLOBALS["HTTP_POST_VARS"]["c61_instit"] : $this->c61_instit);
            $this->c61_codigo = ($this->c61_codigo == "" ? @$GLOBALS["HTTP_POST_VARS"]["c61_codigo"] : $this->c61_codigo);
            $this->c61_contrapartida = ($this->c61_contrapartida == "" ? @$GLOBALS["HTTP_POST_VARS"]["c61_contrapartida"] : $this->c61_contrapartida);
        } else {
            $this->c61_reduz = ($this->c61_reduz == "" ? @$GLOBALS["HTTP_POST_VARS"]["c61_reduz"] : $this->c61_reduz);
            $this->c61_anousu = ($this->c61_anousu == "" ? @$GLOBALS["HTTP_POST_VARS"]["c61_anousu"] : $this->c61_anousu);
        }
    }

    public function incluir($c61_reduz, $c61_anousu)
    {
        $this->atualizacampos();
        if ($this->c61_codcon == null) {
            $this->erro_sql = " Campo Código da Conta não informado.";
            $this->erro_campo = "c61_codcon";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->c61_instit == null) {
            $this->erro_sql = " Campo Instituição não informado.";
            $this->erro_campo = "c61_instit";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->c61_codigo == null) {
            $this->erro_sql = " Campo Código do Recurso não informado.";
            $this->erro_campo = "c61_codigo";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->c61_contrapartida == null) {
            $this->c61_contrapartida = "0";
        }
        if ($c61_reduz == "" || $c61_reduz == null) {
            $result = db_query("select nextval('conplanoorcamentoanalitica_c61_reduz_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: conplanoorcamentoanalitica_c61_reduz_seq do campo: c61_reduz";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->c61_reduz = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from conplanoorcamentoanalitica_c61_reduz_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $c61_reduz)) {
                $this->erro_sql = " Campo c61_reduz maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->c61_reduz = $c61_reduz;
            }
        }
        if (($this->c61_reduz == null) || ($this->c61_reduz == "")) {
            $this->erro_sql = " Campo c61_reduz não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if (($this->c61_anousu == null) || ($this->c61_anousu == "")) {
            $this->erro_sql = " Campo c61_anousu não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into conplanoorcamentoanalitica(
                                       c61_reduz
                                      ,c61_codcon
                                      ,c61_anousu
                                      ,c61_instit
                                      ,c61_codigo
                                      ,c61_contrapartida
                       )
                values (
                                $this->c61_reduz
                               ,$this->c61_codcon
                               ,$this->c61_anousu
                               ,$this->c61_instit
                               ,$this->c61_codigo
                               ,$this->c61_contrapartida
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "conplanoorcamentoanalitica ($this->c61_reduz." - ".$this->c61_anousu) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "conplanoorcamentoanalitica já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "conplanoorcamentoanalitica ($this->c61_reduz." - ".$this->c61_anousu) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->c61_reduz . "-" . $this->c61_anousu;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);

        return true;
    }

    public function alterar($c61_reduz = null, $c61_anousu = null)
    {
        $this->atualizacampos();
        $sql = " update conplanoorcamentoanalitica set ";
        $virgula = "";
        if (trim($this->c61_reduz) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c61_reduz"])) {
            $sql .= $virgula . " c61_reduz = $this->c61_reduz ";
            $virgula = ",";
            if (trim($this->c61_reduz) == null) {
                $this->erro_sql = " Campo Reduzido não informado.";
                $this->erro_campo = "c61_reduz";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->c61_codcon) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c61_codcon"])) {
            $sql .= $virgula . " c61_codcon = $this->c61_codcon ";
            $virgula = ",";
            if (trim($this->c61_codcon) == null) {
                $this->erro_sql = " Campo Código da Conta não informado.";
                $this->erro_campo = "c61_codcon";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->c61_anousu) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c61_anousu"])) {
            $sql .= $virgula . " c61_anousu = $this->c61_anousu ";
            $virgula = ",";
            if (trim($this->c61_anousu) == null) {
                $this->erro_sql = " Campo Exercício não informado.";
                $this->erro_campo = "c61_anousu";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->c61_instit) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c61_instit"])) {
            $sql .= $virgula . " c61_instit = $this->c61_instit ";
            $virgula = ",";
            if (trim($this->c61_instit) == null) {
                $this->erro_sql = " Campo Instituição não informado.";
                $this->erro_campo = "c61_instit";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->c61_codigo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c61_codigo"])) {
            $sql .= $virgula . " c61_codigo = $this->c61_codigo ";
            $virgula = ",";
            if (trim($this->c61_codigo) == null) {
                $this->erro_sql = " Campo Código do Recurso não informado.";
                $this->erro_campo = "c61_codigo";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->c61_contrapartida) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c61_contrapartida"])) {
            if (trim($this->c61_contrapartida) == "" && isset($GLOBALS["HTTP_POST_VARS"]["c61_contrapartida"])) {
                $this->c61_contrapartida = "0";
            }
            $sql .= $virgula . " c61_contrapartida = $this->c61_contrapartida ";
            $virgula = ",";
        }
        $sql .= " where ";
        if ($c61_reduz != null) {
            $sql .= " c61_reduz = $this->c61_reduz";
        }
        if ($c61_anousu != null) {
            $sql .= " and  c61_anousu = $this->c61_anousu";
        }

        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "conplanoorcamentoanalitica não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->c61_reduz . "-" . $this->c61_anousu;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "conplanoorcamentoanalitica não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->c61_reduz . "-" . $this->c61_anousu;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $this->c61_reduz . "-" . $this->c61_anousu;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($c61_reduz = null, $c61_anousu = null, $dbwhere = null)
    {

        $sql = " delete from conplanoorcamentoanalitica
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($c61_reduz)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " c61_reduz = $c61_reduz ";
            }
            if (!empty($c61_anousu)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " c61_anousu = $c61_anousu ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "conplanoorcamentoanalitica não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $c61_reduz . "-" . $c61_anousu;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "conplanoorcamentoanalitica não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $c61_reduz . "-" . $c61_anousu;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $c61_reduz . "-" . $c61_anousu;
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
            $this->erro_sql = "Record Vazio na Tabela:conplanoorcamentoanalitica";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($c61_reduz = null, $c61_anousu = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos}";
        $sql .= " from conplanoorcamentoanalitica ";
        $sql .= " join db_config  on  db_config.codigo = conplanoorcamentoanalitica.c61_instit";
        $sql .= " join orctiporec  on  orctiporec.o15_codigo = conplanoorcamentoanalitica.c61_codigo";
        $sql .= " join complementofonterecurso on complementofonterecurso.o200_sequencial = orctiporec.o15_complemento";
        $sql .= " join conplanoorcamento  on  conplanoorcamento.c60_codcon = conplanoorcamentoanalitica.c61_codcon and  conplanoorcamento.c60_anousu = conplanoorcamentoanalitica.c61_anousu";
        $sql .= " join cgm  on  cgm.z01_numcgm = db_config.numcgm";
        $sql .= " join db_tipoinstit  on  db_tipoinstit.db21_codtipo = db_config.db21_tipoinstit";
        $sql .= " join db_estruturavalor  on  db_estruturavalor.db121_sequencial = orctiporec.o15_db_estruturavalor";
        $sql .= " join conclass  on  conclass.c51_codcla = conplanoorcamento.c60_codcla";
        $sql .= " join consistema  on  consistema.c52_codsis = conplanoorcamento.c60_codsis";
        $sql .= " join consistemaconta  on  consistemaconta.c65_sequencial = conplanoorcamento.c60_consistemaconta";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($c61_reduz)) {
                $sql2 .= " where conplanoorcamentoanalitica.c61_reduz = $c61_reduz ";
            }
            if (!empty($c61_anousu)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " conplanoorcamentoanalitica.c61_anousu = $c61_anousu ";
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
    public function sql_query2($c61_reduz = null, $c61_anousu = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "
        select {$campos}
          from conplanoorcamentoanalitica
          join db_config  on  db_config.codigo = conplanoorcamentoanalitica.c61_instit
          join orctiporec  on  orctiporec.o15_codigo = conplanoorcamentoanalitica.c61_codigo
          join fonterecurso on orctiporec_id = orctiporec.o15_codigo
               and exercicio = conplanoorcamentoanalitica.c61_anousu
          join complementofonterecurso on complementofonterecurso.o200_sequencial = orctiporec.o15_complemento
          join conplanoorcamento  on  conplanoorcamento.c60_codcon = conplanoorcamentoanalitica.c61_codcon
               and  conplanoorcamento.c60_anousu = conplanoorcamentoanalitica.c61_anousu
          join cgm  on  cgm.z01_numcgm = db_config.numcgm
          join db_tipoinstit  on  db_tipoinstit.db21_codtipo = db_config.db21_tipoinstit
          join db_estruturavalor  on  db_estruturavalor.db121_sequencial = orctiporec.o15_db_estruturavalor
          join conclass  on  conclass.c51_codcla = conplanoorcamento.c60_codcla
          join consistema  on  consistema.c52_codsis = conplanoorcamento.c60_codsis
          join consistemaconta  on  consistemaconta.c65_sequencial = conplanoorcamento.c60_consistemaconta";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($c61_reduz)) {
                $sql2 .= " where conplanoorcamentoanalitica.c61_reduz = $c61_reduz ";
            }
            if (!empty($c61_anousu)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " conplanoorcamentoanalitica.c61_anousu = $c61_anousu ";
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

    public function sql_query_file($c61_reduz = null, $c61_anousu = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos} ";
        $sql .= "  from conplanoorcamentoanalitica ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($c61_reduz)) {
                $sql2 .= " where conplanoorcamentoanalitica.c61_reduz = $c61_reduz ";
            }
            if (!empty($c61_anousu)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " conplanoorcamentoanalitica.c61_anousu = $c61_anousu ";
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
     *
     * Busca todos os Reduzidos que não possuem registros no ano de destino
     * @param integer $iAnoAtual
     * @param integer $iAnoDestino
     * @param integer $iIntinst
     * @return string
     */
    function sql_query_analiticaProximoExercicio($iAnoAtual, $iAnoDestino, $iIntinst) {

        $sSQl = "select conplanoorcamentoanalitica.*
     from conplanoorcamentoanalitica
          left join conplanoorcamentoanalitica  proximoexercicio
                       on proximoexercicio.c61_reduz  = conplanoorcamentoanalitica.c61_reduz
                      and proximoexercicio.c61_instit = conplanoorcamentoanalitica.c61_instit
                      and proximoexercicio.c61_anousu = {$iAnoDestino}
    where conplanoorcamentoanalitica.c61_anousu = {$iAnoAtual}
      and conplanoorcamentoanalitica.c61_instit = {$iIntinst}
      and proximoexercicio.c61_anousu is null
      and exists(
          select 1 from conplanoorcamento
        where c60_codcon = conplanoorcamentoanalitica.c61_codcon and c60_anousu = {$iAnoDestino})
      ";

        return $sSQl;
    }

    function sql_query_reduzVinculoAnalitica($c61_reduz = null, $c61_anousu = null, $campos = "*", $ordem = null, $dbwhere = "") {

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
        $sql .= " from conplanoorcamentoanalitica ";
        $sql .= "      inner join conplanoorcamento         on conplanoorcamento.c60_codcon                    = conplanoorcamentoanalitica.c61_codcon ";
        $sql .= "                                          and conplanoorcamento.c60_anousu                    = conplanoorcamentoanalitica.c61_anousu";
        $sql .= "      inner join conplanoconplanoorcamento on conplanoconplanoorcamento.c72_conplanoorcamento = conplanoorcamento.c60_codcon ";
        $sql .= "                                          and conplanoconplanoorcamento.c72_anousu            = conplanoorcamento.c60_anousu";
        $sql .= "      inner join conplano                  on conplanoconplanoorcamento.c72_conplano          = conplano.c60_codcon";
        $sql .= "                                          and conplanoconplanoorcamento.c72_anousu            = conplano.c60_anousu";
        $sql .= "      inner join conplanoreduz             on conplano.c60_codcon                             = conplanoreduz.c61_codcon";
        $sql .= "                                          and conplano.c60_anousu                             = conplanoreduz.c61_anousu";
        $sql2 = "";

        if ($dbwhere == "") {
            if ($c61_reduz != null) {
                $sql2 .= " where conplanoorcamentoanalitica.c61_reduz = $c61_reduz ";
            }
            if ($c61_anousu != null) {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " conplanoorcamentoanalitica.c61_anousu = $c61_anousu ";
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

    function sql_query_simples($c61_reduz = null, $c61_anousu = null, $campos = "*", $ordem = null, $dbwhere = "") {

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
        $sql .= " from conplanoorcamentoanalitica ";
        $sql .= "      inner join conplanoorcamento  on  conplanoorcamento.c60_codcon = conplanoorcamentoanalitica.c61_codcon";
        $sql .= "                                   and  conplanoorcamento.c60_anousu = conplanoorcamentoanalitica.c61_anousu";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($c61_reduz != null) {
                $sql2 .= " where conplanoorcamentoanalitica.c61_reduz = $c61_reduz ";
            }
            if ($c61_anousu != null) {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " conplanoorcamentoanalitica.c61_anousu = $c61_anousu ";
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
}
