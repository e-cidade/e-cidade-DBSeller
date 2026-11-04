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
//CLASSE DA ENTIDADE procfiscalmatric
class cl_fis_procfiscalmatric
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
    public $y102_sequencial = 0;
    public $y102_procfiscal = 0;
    public $y102_matric = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 y102_sequencial = int4 = Codigo
                 y102_procfiscal = int4 = Processo Fiscal
                 y102_matric = int4 = Matrícula do Imóvel
                 ";
    //funcao construtor da classe
    public function __construct()
    {
         //classes dos rotulos dos campos
         $this->rotulo = new rotulo("fis_procfiscalmatric");
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
            $this->y102_sequencial = ($this->y102_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["y102_sequencial"]:$this->y102_sequencial);
            $this->y102_procfiscal = ($this->y102_procfiscal == ""?@$GLOBALS["HTTP_POST_VARS"]["y102_procfiscal"]:$this->y102_procfiscal);
            $this->y102_matric = ($this->y102_matric == ""?@$GLOBALS["HTTP_POST_VARS"]["y102_matric"]:$this->y102_matric);
        } else {
            $this->y102_sequencial = ($this->y102_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["y102_sequencial"]:$this->y102_sequencial);
        }
    }
   // funcao para inclusao
    public function incluir($y102_sequencial = null)
    {
          $this->atualizacampos();
        if ($this->y102_procfiscal == null) {
            $this->erro_sql = " Campo Processo Fiscal nao Informado.";
            $this->erro_campo = "y102_procfiscal";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y102_matric == null) {
            $this->erro_sql = " Campo Matrícula do Imóvel nao Informado.";
            $this->erro_campo = "y102_matric";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into fiscalizacao.fis_procfiscalmatric(
                                       y102_procfiscal
                                      ,y102_matric
                       )
                values (
                                $this->y102_procfiscal
                               ,$this->y102_matric
                      ) returning y102_sequencial ";
        $result = db_query($sql);
        $this->y102_sequencial = db_utils::fieldsmemory($result, 0)->y102_sequencial;

        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "procfiscalmatric ($this->y102_sequencial) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "procfiscalmatric já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "procfiscalmatric ($this->y102_sequencial) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->y102_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);


        return true;
    }
   // funcao para alteracao
    public function alterar($y102_sequencial = null)
    {
          $this->atualizacampos();
         $sql = " update fiscalizacao.fis_procfiscalmatric set ";
         $virgula = "";
        if (trim($this->y102_procfiscal)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y102_procfiscal"])) {
            $sql  .= $virgula." y102_procfiscal = $this->y102_procfiscal ";
            $virgula = ",";
            if (trim($this->y102_procfiscal) == null) {
                $this->erro_sql = " Campo Processo Fiscal nao Informado.";
                $this->erro_campo = "y102_procfiscal";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y102_matric)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y102_matric"])) {
            $sql  .= $virgula." y102_matric = $this->y102_matric ";
            $virgula = ",";
            if (trim($this->y102_matric) == null) {
                $this->erro_sql = " Campo Matrícula do Imóvel nao Informado.";
                $this->erro_campo = "y102_matric";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($y102_sequencial!=null) {
            $sql .= " y102_sequencial = $this->y102_sequencial";
        }


        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "procfiscalmatric nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->y102_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "procfiscalmatric nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->y102_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->y102_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao para exclusao
    public function excluir($y102_sequencial = null, $dbwhere = null)
    {
         $sql = " delete from fiscalizacao.fis_procfiscalmatric where ";
         $sql2 = "";
        if ($dbwhere==null || $dbwhere =="") {
            if ($y102_sequencial != "") {
                if ($sql2!="") {
                    $sql2 .= " and ";
                }
                $sql2 .= " y102_sequencial = $y102_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "procfiscalmatric nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y102_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "procfiscalmatric nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y102_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$y102_sequencial;
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
            $this->erro_sql   = "Record Vazio na Tabela:procfiscalmatric";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
    public function sql_query($y102_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_procfiscalmatric ";
        $sql .= "      inner join iptubase  on  iptubase.j01_matric = fis_procfiscalmatric.y102_matric";
        $sql .= "      inner join fiscalizacao.fis_procfiscal  on  fis_procfiscal.y100_sequencial = fis_procfiscalmatric.y102_procfiscal";
        $sql .= "      inner join lote  on  lote.j34_idbql = iptubase.j01_idbql";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = iptubase.j01_numcgm";
        $sql .= "      inner join db_config  on  db_config.codigo = fis_procfiscal.y100_instit";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_procfiscal.y100_coddepto";
        $sql .= "      inner join fiscalizacao.fis_procfiscalcadtipo  on  fis_procfiscalcadtipo.y33_sequencial = fis_procfiscal.y100_procfiscalcadtipo";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y102_sequencial!=null) {
                $sql2 .= " where fis_procfiscalmatric.y102_sequencial = $y102_sequencial ";
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
    public function sql_query_file($y102_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_procfiscalmatric ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y102_sequencial!=null) {
                $sql2 .= " where fis_procfiscalmatric.y102_sequencial = $y102_sequencial ";
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
