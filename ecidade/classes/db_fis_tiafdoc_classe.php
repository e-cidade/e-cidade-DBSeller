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
//CLASSE DA ENTIDADE tiafdoc
class cl_fis_tiafdoc
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
    public $y99_coddoc = 0;
    public $y99_codtiaf = 0;
    public $y99_tiafdoc = 0;
    public $y99_dtini_dia = null;
    public $y99_dtini_mes = null;
    public $y99_dtini_ano = null;
    public $y99_dtini = null;
    public $y99_dtfim_dia = null;
    public $y99_dtfim_mes = null;
    public $y99_dtfim_ano = null;
    public $y99_dtfim = null;
    public $y99_obs = null;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 y99_coddoc = int4 = Codigo do documento
                 y99_codtiaf = int4 = Código Tiaf
                 y99_tiafdoc = int4 = Codigo do documento
                 y99_dtini = date = data inicio
                 y99_dtfim = date = data final
                 y99_obs = text = Observação
                 ";
    //funcao construtor da classe
    public function __construct()
    {
         //classes dos rotulos dos campos
         $this->rotulo = new rotulo("fis_tiafdoc");
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
            $this->y99_coddoc = ($this->y99_coddoc == ""?@$GLOBALS["HTTP_POST_VARS"]["y99_coddoc"]:$this->y99_coddoc);
            $this->y99_codtiaf = ($this->y99_codtiaf == ""?@$GLOBALS["HTTP_POST_VARS"]["y99_codtiaf"]:$this->y99_codtiaf);
            $this->y99_tiafdoc = ($this->y99_tiafdoc == ""?@$GLOBALS["HTTP_POST_VARS"]["y99_tiafdoc"]:$this->y99_tiafdoc);
            if ($this->y99_dtini == "") {
                $this->y99_dtini_dia = ($this->y99_dtini_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y99_dtini_dia"]:$this->y99_dtini_dia);
                $this->y99_dtini_mes = ($this->y99_dtini_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y99_dtini_mes"]:$this->y99_dtini_mes);
                $this->y99_dtini_ano = ($this->y99_dtini_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y99_dtini_ano"]:$this->y99_dtini_ano);
                if ($this->y99_dtini_dia != "") {
                    $this->y99_dtini = $this->y99_dtini_ano."-".$this->y99_dtini_mes."-".$this->y99_dtini_dia;
                }
            }
            if ($this->y99_dtfim == "") {
                $this->y99_dtfim_dia = ($this->y99_dtfim_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y99_dtfim_dia"]:$this->y99_dtfim_dia);
                $this->y99_dtfim_mes = ($this->y99_dtfim_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y99_dtfim_mes"]:$this->y99_dtfim_mes);
                $this->y99_dtfim_ano = ($this->y99_dtfim_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y99_dtfim_ano"]:$this->y99_dtfim_ano);
                if ($this->y99_dtfim_dia != "") {
                    $this->y99_dtfim = $this->y99_dtfim_ano."-".$this->y99_dtfim_mes."-".$this->y99_dtfim_dia;
                }
            }
            $this->y99_obs = ($this->y99_obs == ""?@$GLOBALS["HTTP_POST_VARS"]["y99_obs"]:$this->y99_obs);
        } else {
            $this->y99_coddoc = ($this->y99_coddoc == ""?@$GLOBALS["HTTP_POST_VARS"]["y99_coddoc"]:$this->y99_coddoc);
        }
    }
    // funcao para inclusao
    public function incluir($y99_coddoc = null)
    {
          $this->atualizacampos();
        if ($this->y99_codtiaf == null) {
            $this->erro_sql = " Campo Código Tiaf nao Informado.";
            $this->erro_campo = "y99_codtiaf";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y99_tiafdoc == null) {
            $this->erro_sql = " Campo Codigo do documento nao Informado.";
            $this->erro_campo = "y99_tiafdoc";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y99_dtini == null) {
            $this->erro_sql = " Campo data inicio nao Informado.";
            $this->erro_campo = "y99_dtini_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y99_dtfim == null) {
            $this->erro_sql = " Campo data final nao Informado.";
            $this->erro_campo = "y99_dtfim_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y99_obs == null) {
            $this->erro_sql = " Campo Observação nao Informado.";
            $this->erro_campo = "y99_obs";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into fiscalizacao.fis_tiafdoc(
                                       y99_codtiaf
                                      ,y99_tiafdoc
                                      ,y99_dtini
                                      ,y99_dtfim
                                      ,y99_obs
                       )
                values (
                                $this->y99_codtiaf
                               ,$this->y99_tiafdoc
                               ,".($this->y99_dtini == "null" || $this->y99_dtini == ""?"null":"'".$this->y99_dtini."'")."
                               ,".($this->y99_dtfim == "null" || $this->y99_dtfim == ""?"null":"'".$this->y99_dtfim."'")."
                               ,'$this->y99_obs'
                      ) returning y99_coddoc ";
        $result = db_query($sql);
        $this->y99_coddoc = db_utils::fieldsmemory($result, 0)->y99_coddoc;

        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Documentos do tiaf ($this->y99_coddoc) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Documentos do tiaf já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "Documentos do tiaf ($this->y99_coddoc) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->y99_coddoc;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);

        return true;
    }
   // funcao para alteracao
    public function alterar($y99_coddoc = null)
    {
          $this->atualizacampos();
         $sql = " update fiscalizacao.fis_tiafdoc set ";
         $virgula = "";
        if (trim($this->y99_codtiaf)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y99_codtiaf"])) {
            $sql  .= $virgula." y99_codtiaf = $this->y99_codtiaf ";
            $virgula = ",";
            if (trim($this->y99_codtiaf) == null) {
                $this->erro_sql = " Campo Código Tiaf nao Informado.";
                $this->erro_campo = "y99_codtiaf";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y99_tiafdoc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y99_tiafdoc"])) {
            $sql  .= $virgula." y99_tiafdoc = $this->y99_tiafdoc ";
            $virgula = ",";
            if (trim($this->y99_tiafdoc) == null) {
                $this->erro_sql = " Campo Codigo do documento nao Informado.";
                $this->erro_campo = "y99_tiafdoc";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y99_dtini)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y99_dtini_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y99_dtini_dia"] !="")) {
            $sql  .= $virgula." y99_dtini = '$this->y99_dtini' ";
            $virgula = ",";
            if (trim($this->y99_dtini) == null) {
                $this->erro_sql = " Campo data inicio nao Informado.";
                $this->erro_campo = "y99_dtini_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y99_dtini_dia"])) {
                $sql  .= $virgula." y99_dtini = null ";
                $virgula = ",";
                if (trim($this->y99_dtini) == null) {
                    $this->erro_sql = " Campo data inicio nao Informado.";
                    $this->erro_campo = "y99_dtini_dia";
                    $this->erro_banco = "";
                    $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        if (trim($this->y99_dtfim)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y99_dtfim_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y99_dtfim_dia"] !="")) {
            $sql  .= $virgula." y99_dtfim = '$this->y99_dtfim' ";
            $virgula = ",";
            if (trim($this->y99_dtfim) == null) {
                $this->erro_sql = " Campo data final nao Informado.";
                $this->erro_campo = "y99_dtfim_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y99_dtfim_dia"])) {
                $sql  .= $virgula." y99_dtfim = null ";
                $virgula = ",";
                if (trim($this->y99_dtfim) == null) {
                    $this->erro_sql = " Campo data final nao Informado.";
                    $this->erro_campo = "y99_dtfim_dia";
                    $this->erro_banco = "";
                    $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        if (trim($this->y99_obs)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y99_obs"])) {
            $sql  .= $virgula." y99_obs = '$this->y99_obs' ";
            $virgula = ",";
            if (trim($this->y99_obs) == null) {
                 $this->erro_sql = " Campo Observação nao Informado.";
                 $this->erro_campo = "y99_obs";
                 $this->erro_banco = "";
                 $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                 $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                 $this->erro_status = "0";
                 return false;
            }
        }
        $sql .= " where ";
        if ($y99_coddoc!=null) {
            $sql .= " y99_coddoc = $this->y99_coddoc";
        }

        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Documentos do tiaf nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->y99_coddoc;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                 $this->erro_banco = "";
                 $this->erro_sql = "Documentos do tiaf nao foi Alterado. Alteracao Executada.\\n";
                 $this->erro_sql .= "Valores : ".$this->y99_coddoc;
                 $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                 $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                 $this->erro_status = "1";
                 $this->numrows_alterar = 0;
                 return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->y99_coddoc;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao para exclusao
    public function excluir($y99_coddoc = null, $dbwhere = null)
    {
         $sql = " delete from fiscalizacao.fis_tiafdoc where ";
         $sql2 = "";
        if ($dbwhere==null || $dbwhere =="") {
            if ($y99_coddoc != "") {
                if ($sql2!="") {
                    $sql2 .= " and ";
                }
                $sql2 .= " y99_coddoc = $y99_coddoc ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Documentos do tiaf nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y99_coddoc;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "Documentos do tiaf nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y99_coddoc;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$y99_coddoc;
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
            $this->erro_sql   = "Record Vazio na Tabela:tiafdoc";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
    public function sql_query($y99_coddoc = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_tiafdoc ";
        $sql .= "      inner join fiscalizacao.fis_tiaf  on  fis_tiaf.y90_codtiaf = fis_tiafdoc.y99_codtiaf";
        $sql .= "      inner join fiscalizacao.fis_tiaftipodoc  on  fis_tiaftipodoc.y98_tiafdoc = fis_tiafdoc.y99_tiafdoc";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y99_coddoc!=null) {
                $sql2 .= " where fis_tiafdoc.y99_coddoc = $y99_coddoc ";
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
    public function sql_query_file($y99_coddoc = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_tiafdoc ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y99_coddoc!=null) {
                $sql2 .= " where fis_tiafdoc.y99_coddoc = $y99_coddoc ";
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
