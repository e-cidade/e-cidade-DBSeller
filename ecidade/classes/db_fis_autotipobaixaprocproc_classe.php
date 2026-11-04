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

//MODULO: Fiscal
//CLASSE DA ENTIDADE autotipobaixaprocproc
class cl_fis_autotipobaixaprocproc
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
    public $y114_sequencial = 0;
    public $y114_baixaproc = 0;
    public $y114_processo = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 y114_sequencial = int4 = Sequencial
                 y114_baixaproc = int4 = Codigo da Baixa da Procedência do auto
                 y114_processo = int8 = Processo
                 ";
    //funcao construtor da classe
    public function __construct()
    {
         //classes dos rotulos dos campos
         $this->rotulo = new rotulo("fis_autotipobaixaprocproc");
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
            $this->y114_sequencial = ($this->y114_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["y114_sequencial"]:$this->y114_sequencial);
            $this->y114_baixaproc = ($this->y114_baixaproc == ""?@$GLOBALS["HTTP_POST_VARS"]["y114_baixaproc"]:$this->y114_baixaproc);
            $this->y114_processo = ($this->y114_processo == ""?@$GLOBALS["HTTP_POST_VARS"]["y114_processo"]:$this->y114_processo);
        } else {
            $this->y114_sequencial = ($this->y114_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["y114_sequencial"]:$this->y114_sequencial);
        }
    }
   // funcao para inclusao
    public function incluir($y114_sequencial = null)
    {
          $this->atualizacampos();
        if ($this->y114_baixaproc == null) {
            $this->erro_sql = " Campo Codigo da Baixa da Procedência do auto nao Informado.";
            $this->erro_campo = "y114_baixaproc";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y114_processo == null) {
            $this->erro_sql = " Campo Processo nao Informado.";
            $this->erro_campo = "y114_processo";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into fiscalizacao.fis_autotipobaixaprocproc(
                                       y114_baixaproc
                                      ,y114_processo
                       )
                values (
                                $this->y114_baixaproc
                               ,$this->y114_processo
                      ) returning y114_sequencial ";
        $result = db_query($sql);
        $this->y114_sequencial = db_utils::fieldsmemory($result, 0)->y114_sequencial;
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Processo de protocolo na baixa de auto de infracao ($this->y114_sequencial) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Processo de protocolo na baixa de auto de infracao já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "Processo de protocolo na baixa de auto de infracao ($this->y114_sequencial) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->y114_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);

        return true;
    }
   // funcao para alteracao
    public function alterar($y114_sequencial = null)
    {
          $this->atualizacampos();
         $sql = " update fiscalizacao.fis_autotipobaixaprocproc set ";
         $virgula = "";
        if (trim($this->y114_baixaproc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y114_baixaproc"])) {
            $sql  .= $virgula." y114_baixaproc = $this->y114_baixaproc ";
            $virgula = ",";
            if (trim($this->y114_baixaproc) == null) {
                $this->erro_sql = " Campo Codigo da Baixa da Procedência do auto nao Informado.";
                $this->erro_campo = "y114_baixaproc";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y114_processo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["y114_processo"])) {
            $sql  .= $virgula." y114_processo = $this->y114_processo ";
            $virgula = ",";
            if (trim($this->y114_processo) == null) {
                $this->erro_sql = " Campo Processo nao Informado.";
                $this->erro_campo = "y114_processo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($y114_sequencial!=null) {
            $sql .= " y114_sequencial = $this->y114_sequencial";
        }


        $result = db_query($sql);
        if ($result==false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Processo de protocolo na baixa de auto de infracao nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->y114_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "Processo de protocolo na baixa de auto de infracao nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->y114_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->y114_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao para exclusao
    public function excluir($y114_sequencial = null, $dbwhere = null)
    {
         $sql = " delete from fiscalizacao.fis_autotipobaixaprocproc where ";
         $sql2 = "";
        if ($dbwhere==null || $dbwhere =="") {
            if ($y114_sequencial != "") {
                if ($sql2!="") {
                    $sql2 .= " and ";
                }
                $sql2 .= " y114_sequencial = $y114_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Processo de protocolo na baixa de auto de infracao nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y114_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "Processo de protocolo na baixa de auto de infracao nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y114_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$y114_sequencial;
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
            $this->erro_sql   = "Record Vazio na Tabela:autotipobaixaprocproc";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
   // funcao do sql
    public function sql_query($y114_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_autotipobaixaprocproc ";
        $sql .= "      left  join protprocesso        on  protprocesso.p58_codproc        = fis_autotipobaixaprocproc.y114_processo";
        $sql .= "      left  join fiscalizacao.fis_autotipobaixaproc   on  fis_autotipobaixaproc.y87_baixaproc = fis_autotipobaixaprocproc.y114_baixaproc";
        $sql .= "      inner join cgm                 on  cgm.z01_numcgm                  = protprocesso.p58_numcgm";
        $sql .= "      inner join db_config           on  db_config.codigo                = protprocesso.p58_instit";
        $sql .= "      inner join db_usuarios         on  db_usuarios.id_usuario          = protprocesso.p58_id_usuario";
        $sql .= "      inner join db_depart           on  db_depart.coddepto              = protprocesso.p58_coddepto";
        $sql .= "      inner join tipoproc            on  tipoproc.p51_codigo             = protprocesso.p58_codigo";
        $sql .= "      inner join db_usuarios  as a   on  a.id_usuario                    = fis_autotipobaixaproc.y87_usuario";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y114_sequencial!=null) {
                $sql2 .= " where fis_autotipobaixaprocproc.y114_sequencial = $y114_sequencial ";
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
    public function sql_query_file($y114_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_autotipobaixaprocproc ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y114_sequencial!=null) {
                $sql2 .= " where fis_autotipobaixaprocproc.y114_sequencial = $y114_sequencial ";
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
