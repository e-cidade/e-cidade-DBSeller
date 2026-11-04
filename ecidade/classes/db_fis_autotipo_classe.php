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

//MODULO: fiscal
//CLASSE DA ENTIDADE autotipo
class cl_fis_autotipo
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
    public $y59_codigo = 0;
    public $y59_codauto = 0;
    public $y59_codtipo = 0;
    public $y59_valor = 0;
    public $y59_tipo = 0;
    public $y59_fator = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 y59_codigo = int4 = Codigo Sequencial
                 y59_codauto = int4 = Código do Auto de Infração
                 y59_codtipo = int8 = Código da Procedência
                 y59_valor = float8 = Valor
                 y59_tipo = int4 = Tipo de Correção
                 y59_fator = int4 = Fator
                 ";
    //funcao construtor da classe
    public function __construct()
    {
         //classes dos rotulos dos campos
         $this->rotulo = new rotulo("fis_autotipo");
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
        if (!$exclusao) {
            $this->y59_codigo = ($this->y59_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["y59_codigo"]:$this->y59_codigo);
            $this->y59_codauto = ($this->y59_codauto == ""?@$GLOBALS["HTTP_POST_VARS"]["y59_codauto"]:$this->y59_codauto);
            $this->y59_codtipo = ($this->y59_codtipo == ""?@$GLOBALS["HTTP_POST_VARS"]["y59_codtipo"]:$this->y59_codtipo);
            $this->y59_valor = ($this->y59_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["y59_valor"]:$this->y59_valor);
            $this->y59_tipo = ($this->y59_tipo == ""?@$GLOBALS["HTTP_POST_VARS"]["y59_tipo"]:$this->y59_tipo);
            $this->y59_fator = ($this->y59_fator == ""?@$GLOBALS["HTTP_POST_VARS"]["y59_fator"]:$this->y59_fator);
        } else {
            $this->y59_codigo = ($this->y59_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["y59_codigo"]:$this->y59_codigo);
        }
    }
    // funcao para inclusao
    public function incluir($y59_codigo = null)
    {
          $this->atualizacampos();
        if ($this->y59_codauto == null) {
            $this->erro_sql = " Campo Código do Auto de Infração nao Informado.";
            $this->erro_campo = "y59_codauto";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y59_codtipo == null) {
            $this->erro_sql = " Campo Código da Procedência nao Informado.";
            $this->erro_campo = "y59_codtipo";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y59_valor == null) {
            $this->erro_sql = " Campo Valor nao Informado.";
            $this->erro_campo = "y59_valor";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y59_tipo == null) {
            $this->erro_sql = " Campo Tipo de Correção nao Informado.";
            $this->erro_campo = "y59_tipo";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y59_fator == null) {
            $this->erro_sql = " Campo Fator nao Informado.";
            $this->erro_campo = "y59_fator";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into fiscalizacao.fis_autotipo(
                                       y59_codauto
                                      ,y59_codtipo
                                      ,y59_valor
                                      ,y59_tipo
                                      ,y59_fator
                       )
                values (
                                $this->y59_codauto
                               ,$this->y59_codtipo
                               ,$this->y59_valor
                               ,$this->y59_tipo
                               ,$this->y59_fator
                      ) returning y59_codigo ";
        $result = db_query($sql);
        $this->y59_codigo = db_utils::fieldsmemory($result, 0)->y59_codigo;
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "autotipo ($this->y59_codigo) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "autotipo já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "autotipo ($this->y59_codigo) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->y59_codigo;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);


        return true;
    }
   // funcao para alteracao
    public function alterar($y59_codigo = null)
    {
          $this->atualizacampos();
         $sql = " update fiscalizacao.fis_autotipo set ";
         $virgula = "";
        if (trim($this->y59_codauto)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y59_codauto"])) {
            $sql  .= $virgula." y59_codauto = $this->y59_codauto ";
            $virgula = ",";
            if (trim($this->y59_codauto) == null) {
                $this->erro_sql = " Campo Código do Auto de Infração nao Informado.";
                $this->erro_campo = "y59_codauto";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y59_codtipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y59_codtipo"])) {
            $sql  .= $virgula." y59_codtipo = $this->y59_codtipo ";
            $virgula = ",";
            if (trim($this->y59_codtipo) == null) {
                $this->erro_sql = " Campo Código da Procedência nao Informado.";
                $this->erro_campo = "y59_codtipo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y59_valor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y59_valor"])) {
            $sql  .= $virgula." y59_valor = $this->y59_valor ";
            $virgula = ",";
            if (trim($this->y59_valor) == null) {
                $this->erro_sql = " Campo Valor nao Informado.";
                $this->erro_campo = "y59_valor";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y59_tipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y59_tipo"])) {
            $sql  .= $virgula." y59_tipo = $this->y59_tipo ";
            $virgula = ",";
            if (trim($this->y59_tipo) == null) {
                $this->erro_sql = " Campo Tipo de Correção nao Informado.";
                $this->erro_campo = "y59_tipo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y59_fator)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y59_fator"])) {
            $sql  .= $virgula." y59_fator = $this->y59_fator ";
            $virgula = ",";
            if (trim($this->y59_fator) == null) {
                $this->erro_sql = " Campo Fator nao Informado.";
                $this->erro_campo = "y59_fator";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($y59_codigo!=null) {
            $sql .= " y59_codigo = $this->y59_codigo";
        }


        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "autotipo nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->y59_codigo;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "autotipo nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->y59_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->y59_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao para exclusao
    public function excluir($y59_codigo = null, $dbwhere = null)
    {
         $sql = " delete from fiscalizacao.fis_autotipo where ";
         $sql2 = "";
        if ($dbwhere==null || $dbwhere =="") {
            if ($y59_codigo != "") {
                if ($sql2!="") {
                    $sql2 .= " and ";
                }
                $sql2 .= " y59_codigo = $y59_codigo ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "autotipo nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y59_codigo;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "autotipo nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y59_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$y59_codigo;
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
        if (!$result) {
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
            $this->erro_sql   = "Record Vazio na Tabela:autotipo";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
    public function sql_query($y59_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_autotipo ";
        $sql .= "      inner join fiscalizacao.fis_fiscalproc  on  fis_fiscalproc.y29_codtipo = fis_autotipo.y59_codtipo";
        $sql .= "      inner join fiscalizacao.fis_auto  on  fis_auto.y50_codauto = fis_autotipo.y59_codauto";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_fiscalproc.y29_coddepto";
        $sql .= "      inner join fiscalizacao.fis_tipoandam  on  fis_tipoandam.y41_codtipo = fis_fiscalproc.y29_tipoandam";
        $sql .= "      inner join fiscalizacao.fis_tipofiscaliza  on  fis_tipofiscaliza.y27_codtipo = fis_fiscalproc.y29_tipofisc";
        $sql .= "      inner join db_depart  as a on   a.coddepto = fis_auto.y50_setor";
        $sql .= "      inner join fiscalizacao.fis_tipofiscaliza  as b on   b.y27_codtipo = fis_auto.y50_codtipo";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y59_codigo!=null) {
                $sql2 .= " where fis_autotipo.y59_codigo = $y59_codigo ";
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
    public function sql_query_baixa($y59_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_autotipo ";
        $sql .= "      inner join fiscalizacao.fis_fiscalproc            on fis_fiscalproc.y29_codtipo                   = fis_autotipo.y59_codtipo                  ";
        $sql .= "      left  join fiscalizacao.fis_autotipobaixa         on fis_autotipobaixa.y86_codautotipo            = fis_autotipo.y59_codigo                   ";
        $sql .= "      left  join fiscalizacao.fis_autotipobaixaproc     on fis_autotipobaixaproc.y87_baixaproc          = fis_autotipobaixa.y86_codbaixaproc        ";
        $sql .= "      left  join fiscalizacao.fis_autotipobaixaprocproc on fis_autotipobaixaprocproc.y114_baixaproc     = fis_autotipobaixaproc.y87_baixaproc       ";
        $sql .= "      left  join protprocesso          on protprocesso.p58_codproc                 = fis_autotipobaixaprocproc.y114_processo   ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y59_codigo!=null) {
                $sql2 .= " where fis_autotipo.y59_codigo = $y59_codigo ";
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
    public function sql_query_file($y59_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_autotipo ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y59_codigo!=null) {
                $sql2 .= " where fis_autotipo.y59_codigo = $y59_codigo ";
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
    public function sql_query_rec($y59_codauto = null, $y59_codtipo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_autotipo ";
        $sql .= "      inner join fiscalizacao.fis_fiscalproc  on  fis_fiscalproc.y29_codtipo = fis_autotipo.y59_codtipo";
        $sql .= "      inner join fiscalizacao.fis_auto  on  fis_auto.y50_codauto = fis_autotipo.y59_codauto";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_fiscalproc.y29_coddepto";
        $sql .= "      inner join fiscalizacao.fis_tipoandam  on  fis_tipoandam.y41_codtipo = fis_fiscalproc.y29_tipoandam";
        $sql .= "      inner join db_depart  as a on   a.coddepto = fis_auto.y50_setor";
        $sql .= "      inner join fiscalizacao.fis_fiscalprocrec on fis_fiscalprocrec.y45_codtipo=fis_fiscalproc.y29_codtipo";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y59_codauto!=null) {
                $sql2 .= " where fis_autotipo.y59_codauto = $y59_codauto ";
            }
            if ($y59_codtipo!=null) {
                if ($sql2!="") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " fis_autotipo.y59_codtipo = $y59_codtipo ";
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
