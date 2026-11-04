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

//MODULO: Fiscal
//CLASSE DA ENTIDADE lanctipobaixaproc
class cl_fis_lanctipobaixaproc
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
    public $nl24_baixaproc = 0;
    public $nl24_dtbaixa_dia = null;
    public $nl24_dtbaixa_mes = null;
    public $nl24_dtbaixa_ano = null;
    public $nl24_dtbaixa = null;
    public $nl24_usuario = 0;
    public $nl24_data_dia = null;
    public $nl24_data_mes = null;
    public $nl24_data_ano = null;
    public $nl24_data = null;
    public $nl24_hora = null;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 nl24_baixaproc = int4 = Codigo da Baixa da Procedência do auto
                 nl24_dtbaixa = date = Data da Baixa
                 nl24_usuario = int4 = Usuário
                 nl24_data = date = Data
                 nl24_hora = varchar(5) = Hora
                 ";
   //funcao construtor da classe
    public function __construct()
    {
      //classes dos rotulos dos campos
        $this->rotulo = new rotulo("fis_lanctipobaixaproc");
        $this->pagina_retorno =  basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
    }
   //funcao erro
    public function erro($mostra, $retorna)
    {
        if (($this->erro_status == "0") || ($mostra && $this->erro_status != null )) {
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
            $this->nl24_baixaproc = ($this->nl24_baixaproc == ""?@$GLOBALS["HTTP_POST_VARS"]["nl24_baixaproc"]:$this->nl24_baixaproc);
            if ($this->nl24_dtbaixa == "") {
                $this->nl24_dtbaixa_dia = ($this->nl24_dtbaixa_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["nl24_dtbaixa_dia"]:$this->nl24_dtbaixa_dia);
                $this->nl24_dtbaixa_mes = ($this->nl24_dtbaixa_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["nl24_dtbaixa_mes"]:$this->nl24_dtbaixa_mes);
                $this->nl24_dtbaixa_ano = ($this->nl24_dtbaixa_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["nl24_dtbaixa_ano"]:$this->nl24_dtbaixa_ano);
                if ($this->nl24_dtbaixa_dia != "") {
                     $this->nl24_dtbaixa = $this->nl24_dtbaixa_ano."-".$this->nl24_dtbaixa_mes."-".$this->nl24_dtbaixa_dia;
                }
            }
            $this->nl24_usuario = ($this->nl24_usuario == ""?@$GLOBALS["HTTP_POST_VARS"]["nl24_usuario"]:$this->nl24_usuario);
            if ($this->nl24_data == "") {
                $this->nl24_data_dia = ($this->nl24_data_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["nl24_data_dia"]:$this->nl24_data_dia);
                $this->nl24_data_mes = ($this->nl24_data_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["nl24_data_mes"]:$this->nl24_data_mes);
                $this->nl24_data_ano = ($this->nl24_data_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["nl24_data_ano"]:$this->nl24_data_ano);
                if ($this->nl24_data_dia != "") {
                    $this->nl24_data = $this->nl24_data_ano."-".$this->nl24_data_mes."-".$this->nl24_data_dia;
                }
            }
            $this->nl24_hora = ($this->nl24_hora == ""?@$GLOBALS["HTTP_POST_VARS"]["nl24_hora"]:$this->nl24_hora);
        } else {
            $this->nl24_baixaproc = ($this->nl24_baixaproc == ""?@$GLOBALS["HTTP_POST_VARS"]["nl24_baixaproc"]:$this->nl24_baixaproc);
        }
    }
   // funcao para inclusao
    public function incluir($nl24_baixaproc = null)
    {
        $this->atualizacampos();
        if ($this->nl24_dtbaixa == null) {
            $this->erro_sql = " Campo Data da Baixa nao Informado.";
            $this->erro_campo = "nl24_dtbaixa_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->nl24_usuario == null) {
            $this->erro_sql = " Campo Usuário nao Informado.";
            $this->erro_campo = "nl24_usuario";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->nl24_data == null) {
            $this->erro_sql = " Campo Data nao Informado.";
            $this->erro_campo = "nl24_data_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->nl24_hora == null) {
            $this->erro_sql = " Campo Hora nao Informado.";
            $this->erro_campo = "nl24_hora";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into fiscalizacao.fis_lanctipobaixaproc(
                                       nl24_dtbaixa
                                      ,nl24_usuario
                                      ,nl24_data
                                      ,nl24_hora
                       )
                values (
                                ".($this->nl24_dtbaixa == "null" || $this->nl24_dtbaixa == ""?"null":"'".$this->nl24_dtbaixa."'")."
                               ,$this->nl24_usuario
                               ,".($this->nl24_data == "null" || $this->nl24_data == ""?"null":"'".$this->nl24_data."'")."
                               ,'$this->nl24_hora'
                      ) returning nl24_baixaproc ";
        $result = db_query($sql);
        $this->nl24_baixaproc = db_utils::fieldsmemory($result, 0)->nl24_baixaproc;

        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Baixa procedências do auto ($this->nl24_baixaproc) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Baixa procedências do auto já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "Baixa procedências do auto ($this->nl24_baixaproc) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->nl24_baixaproc;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        return true;
    }
   // funcao para alteracao
    public function alterar($nl24_baixaproc = null)
    {
        $this->atualizacampos();
        $sql = " update fiscalizacao.fis_lanctipobaixaproc set ";
        $virgula = "";
        if (trim($this->nl24_dtbaixa)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl24_dtbaixa_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["nl24_dtbaixa_dia"] !="")) {
            $sql  .= $virgula." nl24_dtbaixa = '$this->nl24_dtbaixa' ";
            $virgula = ",";
            if (trim($this->nl24_dtbaixa) == null) {
                $this->erro_sql = " Campo Data da Baixa nao Informado.";
                $this->erro_campo = "nl24_dtbaixa_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["nl24_dtbaixa_dia"])) {
                $sql  .= $virgula." nl24_dtbaixa = null ";
                $virgula = ",";
                if (trim($this->nl24_dtbaixa) == null) {
                    $this->erro_sql = " Campo Data da Baixa nao Informado.";
                    $this->erro_campo = "nl24_dtbaixa_dia";
                    $this->erro_banco = "";
                    $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        if (trim($this->nl24_usuario)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl24_usuario"])) {
            $sql  .= $virgula." nl24_usuario = $this->nl24_usuario ";
            $virgula = ",";
            if (trim($this->nl24_usuario) == null) {
                $this->erro_sql = " Campo Usuário nao Informado.";
                $this->erro_campo = "nl24_usuario";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->nl24_data)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl24_data_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["nl24_data_dia"] !="")) {
            $sql  .= $virgula." nl24_data = '$this->nl24_data' ";
            $virgula = ",";
            if (trim($this->nl24_data) == null) {
                $this->erro_sql = " Campo Data nao Informado.";
                $this->erro_campo = "nl24_data_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["nl24_data_dia"])) {
                $sql  .= $virgula." nl24_data = null ";
                $virgula = ",";
                if (trim($this->nl24_data) == null) {
                    $this->erro_sql = " Campo Data nao Informado.";
                    $this->erro_campo = "nl24_data_dia";
                    $this->erro_banco = "";
                    $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        if (trim($this->nl24_hora)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl24_hora"])) {
            $sql  .= $virgula." nl24_hora = '$this->nl24_hora' ";
            $virgula = ",";
            if (trim($this->nl24_hora) == null) {
                $this->erro_sql = " Campo Hora nao Informado.";
                $this->erro_campo = "nl24_hora";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($nl24_baixaproc!=null) {
            $sql .= " nl24_baixaproc = $this->nl24_baixaproc";
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Baixa procedências do auto nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->nl24_baixaproc;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "Baixa procedências do auto nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->nl24_baixaproc;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->nl24_baixaproc;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao para exclusao
    public function excluir($nl24_baixaproc = null, $dbwhere = null)
    {
        $sql = " delete from fiscalizacao.fis_lanctipobaixaproc where ";
        $sql2 = "";
        if ($dbwhere==null || $dbwhere =="") {
            if ($nl24_baixaproc != "") {
                if ($sql2!="") {
                    $sql2 .= " and ";
                }
                $sql2 .= " nl24_baixaproc = $nl24_baixaproc ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Baixa procedências do auto nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$nl24_baixaproc;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "Baixa procedências do auto nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$nl24_baixaproc;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$nl24_baixaproc;
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
            $this->erro_sql   = "Record Vazio na Tabela:lanctipobaixaproc";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
   // funcao do sql
    public function sql_query($nl24_baixaproc = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_lanctipobaixaproc ";
        $sql .= "      inner join db_usuarios        on db_usuarios.id_usuario = fis_lanctipobaixaproc.nl24_usuario  ";
        $sql .= "      inner join cgm                on cgm.z01_numcgm         = protprocesso.p58_numcgm             ";
        $sql .= "      inner join db_usuarios        on db_usuarios.id_usuario = protprocesso.p58_id_usuario         ";
        $sql .= "      inner join db_depart          on db_depart.coddepto     = protprocesso.p58_coddepto           ";
        $sql .= "      inner join tipoproc           on tipoproc.p51_codigo    = protprocesso.p58_codigo             ";
        $sql .= "      left  join fiscalizacao.fis_lanctipobaixaprocproc                                             ";
        $sql .= "        on fis_lanctipobaixaprocproc.nl25_baixaproc  = fis_lanctipobaixaproc.nl24_baixaproc         ";
        $sql .= "      left  join protprocesso on protprocesso.p58_codproc = fis_lanctipobaixaprocproc.nl25_processo ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($nl24_baixaproc!=null) {
                $sql2 .= " where fis_lanctipobaixaproc.nl24_baixaproc = $nl24_baixaproc ";
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
   // funcao do sql
    public function sql_query_file($nl24_baixaproc = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_lanctipobaixaproc ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($nl24_baixaproc!=null) {
                $sql2 .= " where fis_lanctipobaixaproc.nl24_baixaproc = $nl24_baixaproc ";
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
