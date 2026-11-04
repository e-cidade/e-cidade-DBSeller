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
//CLASSE DA ENTIDADE conplanoreduz
class cl_conplanoreduz
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
    var $c61_codcon = 0;
    var $c61_anousu = 0;
    var $c61_reduz = 0;
    var $c61_instit = 0;
    var $c61_codigo = 0;
    var $c61_contrapartida = 0;
    // cria propriedade com as variaveis do arquivo
    var $campos = "
                 c61_codcon = int4 = Código da Conta
                 c61_anousu = int4 = Exercício
                 c61_reduz = int4 = Reduzido
                 c61_instit = int4 = Instituição
                 c61_codigo = int4 = Codigo do Recurso
                 c61_contrapartida = int4 = Contra Partida
                 ";

    //funcao construtor da classe
    public function __construct()
    {
        //classes dos rotulos dos campos
        $this->rotulo = new rotulo("conplanoreduz");
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
            $this->c61_codcon = ($this->c61_codcon == "" ? @$GLOBALS["HTTP_POST_VARS"]["c61_codcon"] : $this->c61_codcon);
            $this->c61_anousu = ($this->c61_anousu == "" ? @$GLOBALS["HTTP_POST_VARS"]["c61_anousu"] : $this->c61_anousu);
            $this->c61_reduz = ($this->c61_reduz == "" ? @$GLOBALS["HTTP_POST_VARS"]["c61_reduz"] : $this->c61_reduz);
            $this->c61_instit = ($this->c61_instit == "" ? @$GLOBALS["HTTP_POST_VARS"]["c61_instit"] : $this->c61_instit);
            $this->c61_codigo = ($this->c61_codigo == "" ? @$GLOBALS["HTTP_POST_VARS"]["c61_codigo"] : $this->c61_codigo);
            $this->c61_contrapartida = ($this->c61_contrapartida == "" ? @$GLOBALS["HTTP_POST_VARS"]["c61_contrapartida"] : $this->c61_contrapartida);
        } else {
            $this->c61_anousu = ($this->c61_anousu == "" ? @$GLOBALS["HTTP_POST_VARS"]["c61_anousu"] : $this->c61_anousu);
            $this->c61_reduz = ($this->c61_reduz == "" ? @$GLOBALS["HTTP_POST_VARS"]["c61_reduz"] : $this->c61_reduz);
        }
    }

    // funcao para inclusao
    public function incluir($c61_reduz, $c61_anousu)
    {
        $this->atualizacampos();
        if ($this->c61_codcon == null) {
            $this->erro_sql = " Campo Código da Conta nao Informado.";
            $this->erro_campo = "c61_codcon";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
              str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if ($this->c61_instit == null) {
            $this->erro_sql = " Campo Instituição nao Informado.";
            $this->erro_campo = "c61_instit";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
              str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if ($this->c61_codigo == null) {
            $this->erro_sql = " Campo Codigo do Recurso nao Informado.";
            $this->erro_campo = "c61_codigo";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
              str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if ($this->c61_contrapartida == null) {
            $this->c61_contrapartida = "0";
        }
        if ($c61_reduz == "" || $c61_reduz == null) {
            $result = db_query("select nextval('conplanoreduz_c61_reduz_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: conplanoreduz_c61_reduz_seq do campo: c61_reduz";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                  str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            }
            $this->c61_reduz = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from conplanoreduz_c61_reduz_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $c61_reduz)) {
                $this->erro_sql = " Campo c61_reduz maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                  str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            } else {
                $this->c61_reduz = $c61_reduz;
            }
        }
        if (($this->c61_reduz == null) || ($this->c61_reduz == "")) {
            $this->erro_sql = " Campo c61_reduz nao declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
              str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if (($this->c61_anousu == null) || ($this->c61_anousu == "")) {
            $this->erro_sql = " Campo c61_anousu nao declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
              str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        $sql = "insert into conplanoreduz(
                                       c61_codcon
                                      ,c61_anousu
                                      ,c61_reduz
                                      ,c61_instit
                                      ,c61_codigo
                                      ,c61_contrapartida
                       )
                values (
                                $this->c61_codcon
                               ,$this->c61_anousu
                               ,$this->c61_reduz
                               ,$this->c61_instit
                               ,$this->c61_codigo
                               ,$this->c61_contrapartida
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "Código Reduzido do Plano ($this->c61_reduz." - ".$this->c61_anousu) nao Incluído. Inclusao Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "Código Reduzido do Plano já Cadastrado";
                $this->erro_msg .= str_replace('"', "",
                  str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "Código Reduzido do Plano ($this->c61_reduz." - ".$this->c61_anousu) nao Incluído. Inclusao Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                  str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;

            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : " . $this->c61_reduz . "-" . $this->c61_anousu;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "",
          str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        $resaco = $this->sql_record($this->sql_query_file($this->c61_reduz, $this->c61_anousu));

        return true;
    }

    // funcao para alteracao
    public function alterar($c61_reduz = null, $c61_anousu = null)
    {
        $this->atualizacampos();
        $sql = " update conplanoreduz set ";
        $virgula = "";
        if (trim($this->c61_codcon) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c61_codcon"])) {
            $sql .= $virgula . " c61_codcon = $this->c61_codcon ";
            $virgula = ",";
            if (trim($this->c61_codcon) == null) {
                $this->erro_sql = " Campo Código da Conta nao Informado.";
                $this->erro_campo = "c61_codcon";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                  str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            }
        }
        if (trim($this->c61_anousu) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c61_anousu"])) {
            $sql .= $virgula . " c61_anousu = $this->c61_anousu ";
            $virgula = ",";
            if (trim($this->c61_anousu) == null) {
                $this->erro_sql = " Campo Exercício nao Informado.";
                $this->erro_campo = "c61_anousu";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                  str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            }
        }
        if (trim($this->c61_reduz) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c61_reduz"])) {
            $sql .= $virgula . " c61_reduz = $this->c61_reduz ";
            $virgula = ",";
            if (trim($this->c61_reduz) == null) {
                $this->erro_sql = " Campo Reduzido nao Informado.";
                $this->erro_campo = "c61_reduz";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                  str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            }
        }
        if (trim($this->c61_instit) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c61_instit"])) {
            $sql .= $virgula . " c61_instit = $this->c61_instit ";
            $virgula = ",";
            if (trim($this->c61_instit) == null) {
                $this->erro_sql = " Campo Instituição nao Informado.";
                $this->erro_campo = "c61_instit";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                  str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            }
        }
        if (trim($this->c61_codigo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c61_codigo"])) {
            $sql .= $virgula . " c61_codigo = $this->c61_codigo ";
            $virgula = ",";
            if (trim($this->c61_codigo) == null) {
                $this->erro_sql = " Campo Codigo do Recurso nao Informado.";
                $this->erro_campo = "c61_codigo";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                  str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
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
        $resaco = $this->sql_record($this->sql_query_file($this->c61_reduz, $this->c61_anousu));
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Código Reduzido do Plano nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->c61_reduz . "-" . $this->c61_anousu;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
              str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;

            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Código Reduzido do Plano nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->c61_reduz . "-" . $this->c61_anousu;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                  str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;

                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : " . $this->c61_reduz . "-" . $this->c61_anousu;
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
    public function excluir($c61_reduz = null, $c61_anousu = null, $dbwhere = null)
    {
        if ($dbwhere == null || $dbwhere == "") {
            $resaco = $this->sql_record($this->sql_query_file($c61_reduz, $c61_anousu));
        } else {
            $resaco = $this->sql_record($this->sql_query_file(null, null, "*", null, $dbwhere));
        }

        $sql = " delete from conplanoreduz
                    where ";
        $sql2 = "";
        if ($dbwhere == null || $dbwhere == "") {
            if ($c61_reduz != "") {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                }
                $sql2 .= " c61_reduz = $c61_reduz ";
            }
            if ($c61_anousu != "") {
                if ($sql2 != "") {
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
            $this->erro_sql = "Código Reduzido do Plano nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $c61_reduz . "-" . $c61_anousu;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
              str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;

            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Código Reduzido do Plano nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $c61_reduz . "-" . $c61_anousu;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                  str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;

                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : " . $c61_reduz . "-" . $c61_anousu;
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
        if ($result == false) {
            $this->numrows = 0;
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Erro ao selecionar os registros.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
              str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        $this->numrows = pg_numrows($result);
        if ($this->numrows == 0) {
            $this->erro_banco = "";
            $this->erro_sql = "Record Vazio na Tabela:conplanoreduz";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
              str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }

        return $result;
    }

    public function sql_query($c61_reduz = null, $c61_anousu = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from conplanoreduz ";
        $sql .= "      inner join db_config  on  db_config.codigo = conplanoreduz.c61_instit";
        $sql .= "      inner join orctiporec  on  orctiporec.o15_codigo = conplanoreduz.c61_codigo";
        $sql .= "      inner join conplano  on  conplano.c60_codcon = conplanoreduz.c61_codcon and  conplano.c60_anousu = conplanoreduz.c61_anousu";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
        $sql .= "      inner join conclass  on  conclass.c51_codcla = conplano.c60_codcla";
        $sql .= "      inner join consistema  on  consistema.c52_codsis = conplano.c60_codsis";
        $sql .= "      inner join conclass  as a on   a.c51_codcla = conplano.c60_codcla";
        $sql .= "      inner join consistema  as b on   b.c52_codsis = conplano.c60_codsis";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($c61_anousu != null) {
                $sql2 .= " where conplanoreduz.c61_anousu = $c61_anousu ";
            }
            if ($c61_reduz != null) {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " conplanoreduz.c61_reduz = $c61_reduz ";
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

    public function sql_query_file($c61_reduz = null, $c61_anousu = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from conplanoreduz ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($c61_anousu != null) {
                $sql2 .= " where conplanoreduz.c61_anousu = $c61_anousu ";
            }
            if ($c61_reduz != null) {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " conplanoreduz.c61_reduz = $c61_reduz ";
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
     * Metood para retornar a conta bancaria
     * @deprecated
     * @param null $c61_codcon
     * @param null $c61_anousu
     * @param string $campos
     * @param null $ordem
     * @param string $dbwhere
     * @return string
     *
     */
    function sql_query_contabancaria(
      $c61_codcon = null,
      $c61_anousu = null,
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
        $sql .= " from conplanoreduz ";
        $sql .= " inner join conplano              on conplano.c60_codcon = conplanoreduz.c61_codcon";
        $sql .= "                                 and conplano.c60_anousu = conplanoreduz.c61_anousu";
        $sql .= " inner join conplanoconta         on conplanoconta.c63_anousu = conplanoreduz.c61_anousu";
        $sql .= "                                 and conplanoconta.c63_codcon = conplanoreduz.c61_codcon";
        $sql .= " inner join conplanocontabancaria on conplanocontabancaria.c56_codcon = conplanoreduz.c61_codcon";
        $sql .= "                                 and conplanocontabancaria.c56_anousu = conplanoreduz.c61_anousu";
        $sql .= "                                 and conplanocontabancaria.c56_reduz  = conplanoreduz.c61_reduz";
        $sql .= " inner join contabancaria         on contabancaria.db83_sequencial = conplanocontabancaria.c56_contabancaria";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($c61_codcon != null) {
                $sql2 .= " where conplanoreduz.c61_codcon = $c61_codcon ";
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

    public function sql_query_razao($c61_codcon = null, $c61_anousu = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from conplanoreduz ";
        $sql .= "      inner join conlancamval on c69_debito = conplanoreduz.c61_reduz or c69_credito = conplanoreduz.c61_reduz";
        $sql .= "      inner join conplano on c60_codcon = conplanoreduz.c61_codcon";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($c61_codcon != null) {
                $sql2 .= " where conplanoreduz.c61_codcon = $c61_codcon ";
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
     * Busca as contas correntes de um reduzido
     * @return string
     */
    function sql_query_reduz_contacorrente(
      $c61_reduz = null,
      $c61_anousu = null,
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
        $sql .= " from conplanoreduz ";
        $sql .= "      join db_config  on  db_config.codigo = conplanoreduz.c61_instit";
        $sql .= "      join orctiporec on orctiporec.o15_codigo = conplanoreduz.c61_codigo";
        $sql .= "      join fonterecurso on fonterecurso.orctiporec_id = orctiporec.o15_codigo
                            and fonterecurso.exercicio = conplanoreduz.c61_anousu";
        $sql .= "      join complementofonterecurso on complementofonterecurso.o200_sequencial = orctiporec.o15_complemento";
        $sql .= "      inner join conplano  on  conplano.c60_codcon = conplanoreduz.c61_codcon ";
        $sql .= "       									 and  conplano.c60_anousu = conplanoreduz.c61_anousu ";
        $sql .= "       left join conplanocontacorrente  on  conplano.c60_codcon = conplanocontacorrente.c18_codcon ";
        $sql .= "       									              and  conplano.c60_anousu = conplanocontacorrente.c18_anousu ";
        $sql .= "       left join contacorrente  on contacorrente.c17_sequencial = conplanocontacorrente.c18_contacorrente ";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
        $sql .= "      inner join conclass  on  conclass.c51_codcla = conplano.c60_codcla";
        $sql .= "      inner join consistema  on  consistema.c52_codsis = conplano.c60_codsis";
        $sql .= "      inner join conclass  as a on   a.c51_codcla = conplano.c60_codcla";
        $sql .= "      inner join consistema  as b on   b.c52_codsis = conplano.c60_codsis";

        $sql .= "      left join conplanocontabancaria on conplanocontabancaria.c56_codcon = conplanoreduz.c61_codcon ";
        $sql .= "                                     and conplanocontabancaria.c56_anousu = conplanoreduz.c61_anousu ";
        $sql .= "                                     and conplanocontabancaria.c56_reduz  = conplanoreduz.c61_reduz  ";
        $sql .= "      left join contabancaria on contabancaria.db83_sequencial = conplanocontabancaria.c56_contabancaria ";
        $sql .= "      left join bancoagencia on bancoagencia.db89_sequencial = contabancaria.db83_bancoagencia ";

        $sql2 = "";
        if ($dbwhere == "") {
            if ($c61_anousu != null) {
                $sql2 .= " where conplanoreduz.c61_anousu = $c61_anousu ";
            }
            if ($c61_reduz != null) {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " conplanoreduz.c61_reduz = $c61_reduz ";
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

    public function sql_query_analitica($c61_reduz = null, $c61_anousu = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from conplanoreduz ";
        $sql .= "      inner join conplano  on conplano.c60_codcon = conplanoreduz.c61_codcon";
        $sql .= "                          and conplano.c60_anousu = conplanoreduz.c61_anousu";
        $sql .= "      left  join conplanocontacorrente  on conplanocontacorrente.c18_codcon = conplano.c60_codcon ";
        $sql .= "                                       and conplanocontacorrente.c18_anousu = conplano.c60_anousu ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($c61_anousu != null) {
                $sql2 .= " where conplanoreduz.c61_anousu = $c61_anousu ";
            }
            if ($c61_reduz != null) {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " conplanoreduz.c61_reduz = $c61_reduz ";
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

    public function sql_query_plano_reduzido($campos = "*", $where = null)
    {

        $sql = " select {$campos} ";
        $sql .= "   from conplanoreduz ";
        $sql .= "        inner join conplano on conplano.c60_codcon = conplanoreduz.c61_codcon ";
        $sql .= "                           and conplano.c60_anousu = conplanoreduz.c61_anousu";

        if (!empty($where)) {
            $sql .= " where {$where} ";
        }

        return $sql;
    }

    public function sql_query_infocomplementar_obrigatorias($sCampos = "*", $where = null)
    {

        $sql = " select {$sCampos} ";
        $sql .= " from conplanoreduz ";
        $sql .= "      inner join conplano on c60_codcon = c61_codcon ";
        $sql .= "                         and c60_anousu = c61_anousu ";
        $sql .= "      inner join conplanoatributos on c60_codcon = c120_conplano ";
        $sql .= "                                  and c60_anousu = c120_anousu  ";
        $sql .= "      inner join conplanoinfocomplementar on conplanoinfocomplementar.c121_sequencial = conplanoatributos.c120_infocomplementar ";
        $sql .= "      left  join conplanosistema          on conplanosistema.c122_sequencial               = conplanoatributos.c120_conplanosistema ";
        $sql .= "      left  join conplanosistemaatributos on conplanosistemaatributos.c129_conplanosistema = conplanosistema.c122_sequencial ";
        $sql .= "                                         and conplanosistemaatributos.c129_conplanoinfocomplementar = conplanoinfocomplementar.c121_sequencial ";


        if (!empty($where)) {
            $sql .= " where {$where} ";
        }

        return $sql;
    }



    public function sql_query_saldo_disponibilidade_recurso($filtros)
    {
        $sql = " select * ";
        $sql .= "   from conplanoreduz ";
        $sql .= "        inner join conplano on conplano.c60_codcon = conplanoreduz.c61_codcon ";
        $sql .= "                           and conplano.c60_anousu = conplanoreduz.c61_anousu";

        if (!empty($where)) {
            $sql .= " where {$where} ";
        }


        $sql = <<<SQL

select

*

from (



select 1 as id,
       'Ativo Financeiro'::varchar as tipo,

       round(sum(case when fc_planosaldonovo_array[5] = 'C' then fc_planosaldonovo_array[1]::float8 * -1 else fc_planosaldonovo_array[1]::float8 end),2) as saldo_anterior,

       round(sum(case when fc_planosaldonovo_array[6] = 'C' then fc_planosaldonovo_array[4]::float8 * -1 else fc_planosaldonovo_array[4]::float8 end),2) as saldo_atual


       from (select o15_recurso,
	            fc_planosaldonovo_array(c61_anousu, c61_reduz, '$filtros->dataInicial',  '$filtros->dataFinal', false)
	     from conplanoreduz
	          join conplano on c60_anousu = c61_anousu
		               and c60_codcon = c61_codcon
	          inner join orctiporec on o15_codigo = c61_codigo
	     where c61_anousu = $filtros->ano
	       and c61_instit  in ($filtros->instituicoes)
	       and c60_identificadorfinanceiro = 'F'
	       and substr(c60_estrut,1,1) = '1'
	    ) as x
       group by 1

union all




select 2 as id,
       '   (-) Passivo Financeiro'::varchar as tipo,

       round(sum(case when fc_planosaldonovo_array[5] = 'C' then fc_planosaldonovo_array[1]::float8*-1 else fc_planosaldonovo_array[1]::float8 end),2) as saldo_anterior,

       round(sum(case when fc_planosaldonovo_array[6] = 'C' then fc_planosaldonovo_array[4]::float8*-1 else fc_planosaldonovo_array[4]::float8 end),2) as saldo_atual

       from (
           select fc_planosaldonovo_array(c61_anousu, c61_reduz, '$filtros->dataInicial',  '$filtros->dataFinal', false)
	         from conplanoreduz
	         join conplano on c60_anousu = c61_anousu
		                  and c60_codcon = c61_codcon
	   inner join orctiporec on o15_codigo = c61_codigo
	        where c61_anousu = $filtros->ano
	          and c61_instit  in ( $filtros->instituicoes )
	          and c60_identificadorfinanceiro = 'F'
              AND substr(c60_estrut, 1, 2) in ('21', '22')

	    ) as x
       group by 1





union all

select 3 as id,
       '   (-) 622130100 - Credito Empenhado a Liquidar'::varchar as tipo,

       round(sum(case when fc_planosaldonovo_array[5] = 'C' then fc_planosaldonovo_array[1]::float8*-1 else fc_planosaldonovo_array[1]::float8 end),2) as saldo_anterior,

       round(sum(case when fc_planosaldonovo_array[6] = 'C' then fc_planosaldonovo_array[4]::float8*-1 else fc_planosaldonovo_array[4]::float8 end),2) as saldo_atual

       from (select o15_recurso,
	            fc_planosaldonovo_array(c61_anousu, c61_reduz, '$filtros->dataInicial',  '$filtros->dataFinal', false)
	     from conplanoreduz
	          join conplano on c60_anousu = c61_anousu
		               and c60_codcon = c61_codcon
	          inner join orctiporec on o15_codigo = c61_codigo
	     where c61_anousu = $filtros->ano
	       and c61_instit  in ($filtros->instituicoes)
	       and substr(c60_estrut,1,9) = '622130100'
	    ) as x
       group by 1

union all

select 4 as id,
       '   (-) 631710000 - Restos a pagar não processados a liquidar - inscricao no execicio'::varchar as tipo,

       round(sum(case when fc_planosaldonovo_array[5] = 'C' then fc_planosaldonovo_array[1]::float8*-1 else fc_planosaldonovo_array[1]::float8 end),2) as saldo_anterior,

       round(sum(case when fc_planosaldonovo_array[6] = 'C' then fc_planosaldonovo_array[4]::float8*-1 else fc_planosaldonovo_array[4]::float8 end),2) as saldo_atual

       from (select o15_recurso,
	            fc_planosaldonovo_array(c61_anousu, c61_reduz, '$filtros->dataInicial',  '$filtros->dataFinal', false)
	     from conplanoreduz
	          join conplano on c60_anousu = c61_anousu
		               and c60_codcon = c61_codcon
	          inner join orctiporec on o15_codigo = c61_codigo
	     where c61_anousu = $filtros->ano
	       and c61_instit  in ($filtros->instituicoes)
	       and substr(c60_estrut,1,9) = '631710000'
	    ) as x
       group by 1

union all

select 5 as id,
       '   (-) 631100000 - Restos a Pagar não processados a liquidar'::varchar as tipo,

       round(sum(case when fc_planosaldonovo_array[5] = 'C' then fc_planosaldonovo_array[1]::float8*-1 else fc_planosaldonovo_array[1]::float8 end),2) as saldo_anterior,

       round(sum(case when fc_planosaldonovo_array[6] = 'C' then fc_planosaldonovo_array[4]::float8*-1 else fc_planosaldonovo_array[4]::float8 end),2) as saldo_atual

       from (select o15_recurso,
	            fc_planosaldonovo_array(c61_anousu, c61_reduz, '$filtros->dataInicial',  '$filtros->dataFinal', false)
	     from conplanoreduz
	          join conplano on c60_anousu = c61_anousu
		               and c60_codcon = c61_codcon
	          inner join orctiporec on o15_codigo = c61_codigo
	     where c61_anousu = $filtros->ano
	       and c61_instit  in ($filtros->instituicoes)
	       and substr(c60_estrut,1,9) = '631100000'
	    ) as x
       group by 1

union all


select 6 as id,
       '82111 - Disponibilidade por Destinação de Recurso DDR'::varchar as tipo,

       round(sum(case when fc_planosaldonovo_array[5] = 'C' then fc_planosaldonovo_array[1]::float8*-1 else fc_planosaldonovo_array[1]::float8 end),2) as saldo_anterior,

       round(sum(case when fc_planosaldonovo_array[6] = 'C' then fc_planosaldonovo_array[4]::float8*-1 else fc_planosaldonovo_array[4]::float8 end),2) as saldo_atual

       from (select fc_planosaldonovo_array(c61_anousu, c61_reduz, '$filtros->dataInicial',  '$filtros->dataFinal', false)
	     from conplanoreduz
	          join conplano on c60_anousu = c61_anousu
		               and c60_codcon = c61_codcon
	          inner join orctiporec on o15_codigo = c61_codigo
	     where c61_anousu = $filtros->ano
	       and c61_instit  in ($filtros->instituicoes)
	       and substr(c60_estrut,1,5) = '82111'
	    ) as x
       group by 1


) as x




;






SQL;






        return $sql;
    }
}
