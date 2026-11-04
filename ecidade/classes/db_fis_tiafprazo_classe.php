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
//CLASSE DA ENTIDADE tiafprazo
class cl_fis_tiafprazo
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
    public $y96_codigo = 0;
    public $y96_codtiaf = 0;
    public $y96_prazo_dia = null;
    public $y96_prazo_mes = null;
    public $y96_prazo_ano = null;
    public $y96_prazo = null;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 y96_codigo = int8 = Codigo do prazo
                 y96_codtiaf = int4 = Código Tiaf
                 y96_prazo = date = Data do prazo
                 ";
    //funcao construtor da classe
    public function __construct()
    {
         //classes dos rotulos dos campos
         $this->rotulo = new rotulo("fis_tiafprazo");
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
            $this->y96_codigo = ($this->y96_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["y96_codigo"]:$this->y96_codigo);
            $this->y96_codtiaf = ($this->y96_codtiaf == ""?@$GLOBALS["HTTP_POST_VARS"]["y96_codtiaf"]:$this->y96_codtiaf);
            if ($this->y96_prazo == "") {
                $this->y96_prazo_dia = ($this->y96_prazo_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y96_prazo_dia"]:$this->y96_prazo_dia);
                $this->y96_prazo_mes = ($this->y96_prazo_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y96_prazo_mes"]:$this->y96_prazo_mes);
                $this->y96_prazo_ano = ($this->y96_prazo_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y96_prazo_ano"]:$this->y96_prazo_ano);
                if ($this->y96_prazo_dia != "") {
                    $this->y96_prazo = $this->y96_prazo_ano."-".$this->y96_prazo_mes."-".$this->y96_prazo_dia;
                }
            }
        } else {
            $this->y96_codigo = ($this->y96_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["y96_codigo"]:$this->y96_codigo);
        }
    }
    // funcao para inclusao
    public function incluir($y96_codigo = null)
    {
          $this->atualizacampos();
        if ($this->y96_codtiaf == null) {
            $this->erro_sql = " Campo Código Tiaf nao Informado.";
            $this->erro_campo = "y96_codtiaf";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y96_prazo == null) {
            $this->erro_sql = " Campo Data do prazo nao Informado.";
            $this->erro_campo = "y96_prazo_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into fiscalizacao.fis_tiafprazo(
                                       y96_codtiaf
                                      ,y96_prazo
                       )
                values (
                                $this->y96_codtiaf
                               ,".($this->y96_prazo == "null" || $this->y96_prazo == ""?"null":"'".$this->y96_prazo."'")."
                      ) returning y96_codigo ";
        $result = db_query($sql);
        $this->y96_codigo = db_utils::fieldsmemory($result, 0)->y96_codigo;

        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Prazo do Tiaf ($this->y96_codigo) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Prazo do Tiaf já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "Prazo do Tiaf ($this->y96_codigo) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->y96_codigo;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);

        return true;
    }
   // funcao para alteracao
    public function alterar($y96_codigo = null)
    {
          $this->atualizacampos();
         $sql = " update fiscalizacao.fis_tiafprazo set ";
         $virgula = "";
        if (trim($this->y96_codtiaf)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y96_codtiaf"])) {
            $sql  .= $virgula." y96_codtiaf = $this->y96_codtiaf ";
            $virgula = ",";
            if (trim($this->y96_codtiaf) == null) {
                $this->erro_sql = " Campo Código Tiaf nao Informado.";
                $this->erro_campo = "y96_codtiaf";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y96_prazo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y96_prazo_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y96_prazo_dia"] !="")) {
            $sql  .= $virgula." y96_prazo = '$this->y96_prazo' ";
            $virgula = ",";
            if (trim($this->y96_prazo) == null) {
                $this->erro_sql = " Campo Data do prazo nao Informado.";
                $this->erro_campo = "y96_prazo_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y96_prazo_dia"])) {
                $sql  .= $virgula." y96_prazo = null ";
                $virgula = ",";
                if (trim($this->y96_prazo) == null) {
                    $this->erro_sql = " Campo Data do prazo nao Informado.";
                    $this->erro_campo = "y96_prazo_dia";
                    $this->erro_banco = "";
                    $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        $sql .= " where ";
        if ($y96_codigo!=null) {
            $sql .= " y96_codigo = $this->y96_codigo";
        }

        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Prazo do Tiaf nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->y96_codigo;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "Prazo do Tiaf nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->y96_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->y96_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

   // funcao para exclusao
    public function excluir($y96_codigo = null, $dbwhere = null)
    {
         $sql = " delete from fiscalizacao.fis_tiafprazo where ";
         $sql2 = "";
        if ($dbwhere==null || $dbwhere =="") {
            if ($y96_codigo != "") {
                if ($sql2!="") {
                    $sql2 .= " and ";
                }
                $sql2 .= " y96_codigo = $y96_codigo ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Prazo do Tiaf nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y96_codigo;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "Prazo do Tiaf nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y96_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$y96_codigo;
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
            $this->erro_sql   = "Record Vazio na Tabela:tiafprazo";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($y96_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_tiafprazo ";
        $sql .= "      inner join fiscalizacao.fis_tiaf  on  fis_tiaf.y90_codtiaf = fis_tiafprazo.y96_codtiaf";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y96_codigo!=null) {
                $sql2 .= " where fis_tiafprazo.y96_codigo = $y96_codigo ";
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

    public function sql_query_file($y96_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_tiafprazo ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y96_codigo!=null) {
                $sql2 .= " where fis_tiafprazo.y96_codigo = $y96_codigo ";
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

    public function sql_queryproc($y96_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_tiafprazo ";
        $sql .= "      inner join fiscalizacao.fis_tiaf          on  fis_tiaf.y90_codtiaf = fis_tiafprazo.y96_codtiaf";
        $sql .= "      inner join fiscalizacao.fis_tiafprazoproc on  fis_tiafprazoproc.y97_codprazo = fis_tiafprazo.y96_codigo";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y96_codigo!=null) {
                $sql2 .= " where fis_tiafprazo.y96_codigo = $y96_codigo ";
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
