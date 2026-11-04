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
//CLASSE DA ENTIDADE levantanotas
class cl_fis_levantanotas
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
    public $y79_codigo = 0;
    public $y79_sequencia = 0;
    public $y79_ordem = 0;
    public $y79_documento = null;
    public $y79_valor = 0;
    public $y79_data_dia = null;
    public $y79_data_mes = null;
    public $y79_data_ano = null;
    public $y79_data = null;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 y79_codigo = int8 = Sequencial de notas
                 y79_sequencia = int4 = Sequencial
                 y79_ordem = int8 = Ordem
                 y79_documento = varchar(20) = Documento
                 y79_valor = float8 = Valor
                 y79_data = date = Data
                 ";
    //funcao construtor da classe
    public function __construct()
    {
         //classes dos rotulos dos campos
         $this->rotulo = new rotulo("fis_levantanotas");
         $this->pagina_retorno =  basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
    }
    //funcao erro
    public function erro($mostra, $retorna)
    {
        if (($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )) {
            echo "<script>alert(\"".$this->erro_msg."\");</script>";
            if ($retorna) {
                echo "<script>location.href='".$this->pagina_retorno."'</script>";
            }
        }
    }
    // funcao para atualizar campos
    public function atualizacampos($exclusao = false)
    {
        if (!$exclusao) {
            $this->y79_codigo = ($this->y79_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["y79_codigo"]:$this->y79_codigo);
            $this->y79_sequencia = ($this->y79_sequencia == ""?@$GLOBALS["HTTP_POST_VARS"]["y79_sequencia"]:$this->y79_sequencia);
            $this->y79_ordem = ($this->y79_ordem == ""?@$GLOBALS["HTTP_POST_VARS"]["y79_ordem"]:$this->y79_ordem);
            $this->y79_documento = ($this->y79_documento == ""?@$GLOBALS["HTTP_POST_VARS"]["y79_documento"]:$this->y79_documento);
            $this->y79_valor = ($this->y79_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["y79_valor"]:$this->y79_valor);
            if ($this->y79_data == "") {
                $this->y79_data_dia = ($this->y79_data_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y79_data_dia"]:$this->y79_data_dia);
                $this->y79_data_mes = ($this->y79_data_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y79_data_mes"]:$this->y79_data_mes);
                $this->y79_data_ano = ($this->y79_data_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y79_data_ano"]:$this->y79_data_ano);
                if ($this->y79_data_dia != "") {
                    $this->y79_data = $this->y79_data_ano."-".$this->y79_data_mes."-".$this->y79_data_dia;
                }
            }
        } else {
            $this->y79_codigo = ($this->y79_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["y79_codigo"]:$this->y79_codigo);
        }
    }
   // funcao para inclusao
    public function incluir($y79_codigo = null)
    {
          $this->atualizacampos();
        if ($this->y79_sequencia == null) {
            $this->erro_sql = " Campo Sequencial nao Informado.";
            $this->erro_campo = "y79_sequencia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y79_ordem == null) {
            $this->erro_sql = " Campo Ordem nao Informado.";
            $this->erro_campo = "y79_ordem";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y79_documento == null) {
            $this->erro_sql = " Campo Documento nao Informado.";
            $this->erro_campo = "y79_documento";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y79_valor == null) {
            $this->erro_sql = " Campo Valor nao Informado.";
            $this->erro_campo = "y79_valor";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y79_data == null) {
            $this->erro_sql = " Campo Data nao Informado.";
            $this->erro_campo = "y79_data_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into fiscalizacao.fis_levantanotas(
                                       y79_sequencia
                                      ,y79_ordem
                                      ,y79_documento
                                      ,y79_valor
                                      ,y79_data
                       )
                values (
                                $this->y79_sequencia
                               ,$this->y79_ordem
                               ,'$this->y79_documento'
                               ,$this->y79_valor
                               ,".($this->y79_data == "null" || $this->y79_data == ""?"null":"'".$this->y79_data."'")."
                      ) returning y79_codigo ";
        $result = db_query($sql);
        $this->y79_codigo = db_utils::fieldsmemory($result, 0)->y79_codigo;

        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Notas do levantamento ($this->y79_codigo) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Notas do levantamento já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "Notas do levantamento ($this->y79_codigo) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->y79_codigo;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);


        return true;
    }
   // funcao para alteracao
    public function alterar($y79_codigo = null)
    {
          $this->atualizacampos();
         $sql = " update fiscalizacao.fis_levantanotas set ";
         $virgula = "";
        if (trim($this->y79_sequencia)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y79_sequencia"])) {
            $sql  .= $virgula." y79_sequencia = $this->y79_sequencia ";
            $virgula = ",";
            if (trim($this->y79_sequencia) == null) {
                $this->erro_sql = " Campo Sequencial nao Informado.";
                $this->erro_campo = "y79_sequencia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y79_ordem)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y79_ordem"])) {
            $sql  .= $virgula." y79_ordem = $this->y79_ordem ";
            $virgula = ",";
            if (trim($this->y79_ordem) == null) {
                $this->erro_sql = " Campo Ordem nao Informado.";
                $this->erro_campo = "y79_ordem";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y79_documento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y79_documento"])) {
            $sql  .= $virgula." y79_documento = '$this->y79_documento' ";
            $virgula = ",";
            if (trim($this->y79_documento) == null) {
                $this->erro_sql = " Campo Documento nao Informado.";
                $this->erro_campo = "y79_documento";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y79_valor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y79_valor"])) {
            $sql  .= $virgula." y79_valor = $this->y79_valor ";
            $virgula = ",";
            if (trim($this->y79_valor) == null) {
                $this->erro_sql = " Campo Valor nao Informado.";
                $this->erro_campo = "y79_valor";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y79_data)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y79_data_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y79_data_dia"] !="")) {
            $sql  .= $virgula." y79_data = '$this->y79_data' ";
            $virgula = ",";
            if (trim($this->y79_data) == null) {
                $this->erro_sql = " Campo Data nao Informado.";
                $this->erro_campo = "y79_data_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y79_data_dia"])) {
                $sql  .= $virgula." y79_data = null ";
                $virgula = ",";
                if (trim($this->y79_data) == null) {
                    $this->erro_sql = " Campo Data nao Informado.";
                    $this->erro_campo = "y79_data_dia";
                    $this->erro_banco = "";
                    $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        $sql .= " where ";
        if ($y79_codigo!=null) {
            $sql .= " y79_codigo = $this->y79_codigo";
        }


        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Notas do levantamento nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->y79_codigo;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "Notas do levantamento nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->y79_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->y79_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao para exclusao
    public function excluir($y79_codigo = null, $dbwhere = null)
    {
         $sql = " delete from fiscalizacao.fis_levantanotas where ";
         $sql2 = "";
        if ($dbwhere==null || $dbwhere =="") {
            if ($y79_codigo != "") {
                if ($sql2!="") {
                    $sql2 .= " and ";
                }
                $sql2 .= " y79_codigo = $y79_codigo ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Notas do levantamento nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y79_codigo;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "Notas do levantamento nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y79_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$y79_codigo;
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
            $this->erro_sql   = "Record Vazio na Tabela:levantanotas";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
    public function sql_query($y79_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_levantanotas ";
        $sql .= "      inner join fiscalizacao.fis_levvalor  on  fis_levvalor.y63_sequencia = fis_levantanotas.y79_sequencia";
        $sql .= "      inner join fiscalizacao.fis_levanta  on  fis_levanta.y60_codlev = fis_levvalor.y63_codlev";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y79_codigo!=null) {
                $sql2 .= " where fis_levantanotas.y79_codigo = $y79_codigo ";
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
    public function sql_query_file($y79_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_levantanotas ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y79_codigo!=null) {
                $sql2 .= " where fis_levantanotas.y79_codigo = $y79_codigo ";
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
