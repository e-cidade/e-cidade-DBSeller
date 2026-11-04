<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009 DBSeller Servicos de Informatica
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

//MODULO: fiscal
//CLASSE DA ENTIDADE lanctipo
class cl_fis_lanctipo
{
   // cria variaveis de erro
    public $rotulo     = null;
    public $query_sql  = null;
    public $numrows    = 0;
    public $numrows_incluir = 0;
    public $numrows_alterar = 0;
    public $numrows_excluir = 0;
    public $erro_status= null;
    public $erro_sql   = null;
    public $erro_banco = null;
    public $erro_msg   = null;
    public $erro_campo = null;
    public $pagina_retorno = null;
   // cria variaveis do arquivo
    public $nl18_codigo = 0;
    public $nl18_codlanc = 0;
    public $nl18_codtipo = 0;
    public $nl18_valor = 0;
    public $nl18_tipo = 0;
    public $nl18_fator = 0;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 nl18_codigo = int4 = Codigo Sequencial
                 nl18_codlanc = int4 = Código da Notificação de Lançamento
                 nl18_codtipo = int8 = Código da Procedência
                 nl18_valor = float8 = Valor
                 nl18_tipo = int4 = Tipo de Correção
                 nl18_fator = int4 = Fator
                 ";
   //funcao construtor da classe
    public function __construct()
    {
      //classes dos rotulos dos campos
        $this->rotulo = new rotulo("fis_lanctipo");
        $this->pagina_retorno =  basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
    }
   //funcao erro
    public function erro($mostra, $retorna)
    {
        if (($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )) {
            echo "<script>alert(\"".$this->erro_msg."\");</script>";
            if ($retorna==true) {
                echo "<script>location.href='".$this->pagina_retorno."'</script>";
            }
        }
    }
   // funcao para atualizar campos
    public function atualizacampos($exclusao = false)
    {
        if ($exclusao==false) {
            $this->nl18_codigo = ($this->nl18_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["nl18_codigo"]:$this->nl18_codigo);
            $this->nl18_codlanc = ($this->nl18_codlanc == ""?@$GLOBALS["HTTP_POST_VARS"]["nl18_codlanc"]:$this->nl18_codlanc);
            $this->nl18_codtipo = ($this->nl18_codtipo == ""?@$GLOBALS["HTTP_POST_VARS"]["nl18_codtipo"]:$this->nl18_codtipo);
            $this->nl18_valor = ($this->nl18_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["nl18_valor"]:$this->nl18_valor);
            $this->nl18_tipo = ($this->nl18_tipo == ""?@$GLOBALS["HTTP_POST_VARS"]["nl18_tipo"]:$this->nl18_tipo);
            $this->nl18_fator = ($this->nl18_fator == ""?@$GLOBALS["HTTP_POST_VARS"]["nl18_fator"]:$this->nl18_fator);
        } else {
            $this->nl18_codigo = ($this->nl18_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["nl18_codigo"]:$this->nl18_codigo);
        }
    }
   // funcao para inclusao
    public function incluir($nl18_codigo)
    {
        $this->atualizacampos();
        if ($this->nl18_codlanc == null) {
            $this->erro_sql = " Campo Código da Notificação de Lançamento nao Informado.";
            $this->erro_campo = "nl18_codlanc";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->nl18_codtipo == null) {
            $this->erro_sql = " Campo Código da Procedência nao Informado.";
            $this->erro_campo = "nl18_codtipo";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->nl18_valor == null) {
            $this->erro_sql = " Campo Valor nao Informado.";
            $this->erro_campo = "nl18_valor";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->nl18_tipo == null) {
            $this->erro_sql = " Campo Tipo de Correção nao Informado.";
            $this->erro_campo = "nl18_tipo";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->nl18_fator == null) {
            $this->erro_sql = " Campo Fator nao Informado.";
            $this->erro_campo = "nl18_fator";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($nl18_codigo == "" || $nl18_codigo == null) {
            $result = db_query("select nextval('fis_lanctipo_nl18_codigo_seq')");
            if ($result==false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql   = "Verifique o cadastro da sequencia: fis_lanctipo_nl18_codigo_seq do campo: nl18_codigo";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->nl18_codigo = pg_fetch_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from fis_lanctipo_nl18_codigo_seq");
            if (($result != false) && (pg_fetch_result($result, 0, 0) < $nl18_codigo)) {
                $this->erro_sql = " Campo nl18_codigo maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->nl18_codigo = $nl18_codigo;
            }
        }
        if (($this->nl18_codigo == null) || ($this->nl18_codigo == "")) {
            $this->erro_sql = " Campo nl18_codigo nao declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into fiscalizacao.fis_lanctipo(
                                       nl18_codigo
                                      ,nl18_codlanc
                                      ,nl18_codtipo
                                      ,nl18_valor
                                      ,nl18_tipo
                                      ,nl18_fator
                       )
                values (
                                $this->nl18_codigo
                               ,$this->nl18_codlanc
                               ,$this->nl18_codtipo
                               ,$this->nl18_valor
                               ,$this->nl18_tipo
                               ,$this->nl18_fator
                      )";
        $result = db_query($sql);
        if ($result==false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "lanctipo ($this->nl18_codigo) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "lanctipo já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "lanctipo ($this->nl18_codigo) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->nl18_codigo;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        return true;
    }
   // funcao para alteracao
    public function alterar($nl18_codigo = null)
    {
        $this->atualizacampos();
        $sql = " update fiscalizacao.fis_lanctipo set ";
        $virgula = "";
        if (trim($this->nl18_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl18_codigo"])) {
            $sql  .= $virgula." nl18_codigo = $this->nl18_codigo ";
            $virgula = ",";
            if (trim($this->nl18_codigo) == null) {
                $this->erro_sql = " Campo Codigo Sequencial nao Informado.";
                $this->erro_campo = "nl18_codigo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->nl18_codlanc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl18_codlanc"])) {
            $sql  .= $virgula." nl18_codlanc = $this->nl18_codlanc ";
            $virgula = ",";
            if (trim($this->nl18_codlanc) == null) {
                $this->erro_sql = " Campo Código da Notificação de Lançamento nao Informado.";
                $this->erro_campo = "nl18_codlanc";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->nl18_codtipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl18_codtipo"])) {
            $sql  .= $virgula." nl18_codtipo = $this->nl18_codtipo ";
            $virgula = ",";
            if (trim($this->nl18_codtipo) == null) {
                $this->erro_sql = " Campo Código da Procedência nao Informado.";
                $this->erro_campo = "nl18_codtipo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->nl18_valor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl18_valor"])) {
            $sql  .= $virgula." nl18_valor = $this->nl18_valor ";
            $virgula = ",";
            if (trim($this->nl18_valor) == null) {
                $this->erro_sql = " Campo Valor nao Informado.";
                $this->erro_campo = "nl18_valor";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->nl18_tipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl18_tipo"])) {
            $sql  .= $virgula." nl18_tipo = $this->nl18_tipo ";
            $virgula = ",";
            if (trim($this->nl18_tipo) == null) {
                $this->erro_sql = " Campo Tipo de Correção nao Informado.";
                $this->erro_campo = "nl18_tipo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->nl18_fator)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl18_fator"])) {
            $sql  .= $virgula." nl18_fator = $this->nl18_fator ";
            $virgula = ",";
            if (trim($this->nl18_fator) == null) {
                $this->erro_sql = " Campo Fator nao Informado.";
                $this->erro_campo = "nl18_fator";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($nl18_codigo!=null) {
            $sql .= " nl18_codigo = $this->nl18_codigo";
        }
        $result = db_query($sql);
        if ($result==false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "lanctipo nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->nl18_codigo;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "lanctipo nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->nl18_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->nl18_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao para exclusao
    public function excluir($nl18_codigo = null, $dbwhere = null)
    {
        $sql = " delete from fiscalizacao.fis_lanctipo
                    where ";
        $sql2 = "";
        if ($dbwhere==null || $dbwhere =="") {
            if ($nl18_codigo != "") {
                if ($sql2!="") {
                    $sql2 .= " and ";
                }
                $sql2 .= " nl18_codigo = $nl18_codigo ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if ($result==false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "lanctipo nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$nl18_codigo;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "lanctipo nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$nl18_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$nl18_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
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
        if ($result==false) {
            $this->numrows    = 0;
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Erro ao selecionar os registros.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->numrows = pg_num_rows($result);
        if ($this->numrows==0) {
            $this->erro_banco = "";
            $this->erro_sql   = "Record Vazio na Tabela:lanctipo";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
    public function sql_query($nl18_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from fiscalizacao.fis_lanctipo ";
        $sql .= "      join fiscalizacao.fis_fiscalproc  on  fis_fiscalproc.y29_codtipo = fis_lanctipo.nl18_codtipo";
        $sql .= "      join fiscalizacao.fis_lancamento  on  fis_lancamento.nl01_codlanc = fis_lanctipo.nl18_codlanc";
        $sql .= "      join db_depart  on  db_depart.coddepto = fis_fiscalproc.y29_coddepto";
        $sql .= "      join fiscalizacao.fis_tipoandam  on  fis_tipoandam.y41_codtipo = fis_fiscalproc.y29_tipoandam";
        $sql .= "      join fiscalizacao.fis_tipofiscaliza on fis_tipofiscaliza.y27_codtipo = fis_fiscalproc.y29_tipofisc";
        $sql .= "      join db_depart  as a on   a.coddepto = fis_lancamento.nl01_setor";
        $sql .= "      join fiscalizacao.fis_tipofiscaliza  as b on   b.y27_codtipo = fis_lancamento.nl01_codtipo";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($nl18_codigo!=null) {
                $sql2 .= " where fis_lanctipo.nl18_codigo = $nl18_codigo ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }
    public function sql_query_baixa($nl18_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }

        $sql .= " from fiscalizacao.fis_lanctipo ";
        $sql .= "      inner join fiscalizacao.fis_fiscalproc            on fis_fiscalproc.y29_codtipo                   = fis_lanctipo.nl18_codtipo                  ";
        $sql .= "      left  join fiscalizacao.fis_lanctipobaixa         on fis_lanctipobaixa.nl23_codlanctipo            = fis_lanctipo.nl18_codigo                   ";
        $sql .= "      left  join fiscalizacao.fis_lanctipobaixaproc     on fis_lanctipobaixaproc.nl24_baixaproc          = fis_lanctipobaixa.nl23_codbaixaproc        ";
        $sql .= "      left  join fiscalizacao.fis_lanctipobaixaprocproc on fis_lanctipobaixaprocproc.nl25_baixaproc     = fis_lanctipobaixaproc.nl24_baixaproc       ";
        $sql .= "      left  join protprocesso          on protprocesso.p58_codproc                 = fis_lanctipobaixaprocproc.nl25_processo   ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($nl18_codigo!=null) {
                $sql2 .= " where fis_lanctipo.nl18_codigo = $nl18_codigo ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }
    public function sql_query_file($nl18_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from fiscalizacao.fis_lanctipo ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($nl18_codigo!=null) {
                $sql2 .= " where fis_lanctipo.nl18_codigo = $nl18_codigo ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }
    public function sql_query_rec($nl18_codlanc = null, $nl18_codtipo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from fiscalizacao.fis_lanctipo ";
        $sql .= "      inner join fiscalizacao.fis_fiscalproc  on  fis_fiscalproc.y29_codtipo = fis_lanctipo.nl18_codtipo";
        $sql .= "      inner join fiscalizacao.fis_lancamento  on  fis_lancamento.nl01_codlanc = fis_lanctipo.nl18_codlanc";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_fiscalproc.y29_coddepto";
        $sql .= "      inner join fiscalizacao.fis_tipoandam  on  fis_tipoandam.y41_codtipo = fis_fiscalproc.y29_tipoandam";
        $sql .= "      inner join db_depart  as a on   a.coddepto = fis_lancamento.nl01_setor";
        $sql .= "      inner join fiscalizacao.fis_fiscalprocrec on fis_fiscalprocrec.y45_codtipo = fis_fiscalproc.y29_codtipo";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($nl18_codlanc!=null) {
                $sql2 .= " where fis_lanctipo.nl18_codlanc = $nl18_codlanc ";
            }
            if ($nl18_codtipo!=null) {
                if ($sql2!="") {
                     $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " fis_lanctipo.nl18_codtipo = $nl18_codtipo ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }
}
