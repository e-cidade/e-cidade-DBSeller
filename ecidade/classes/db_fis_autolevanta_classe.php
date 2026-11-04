<?php
/**
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
//CLASSE DA ENTIDADE autolevanta
class cl_fis_autolevanta
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
    public $y117_sequencial = 0;
    public $y117_auto = 0;
    public $y117_levanta = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 y117_sequencial = int4 = Auto/Levanta
                 y117_auto = int4 = Auto de Infração
                 y117_levanta = int4 = Levantamento
                 ";
    //funcao construtor da classe
    public function __construct()
    {
      //classes dos rotulos dos campos
        $this->rotulo = new rotulo("fis_autolevanta");
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
            $this->y117_sequencial = ($this->y117_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["y117_sequencial"]:$this->y117_sequencial);
            $this->y117_auto = ($this->y117_auto == ""?@$GLOBALS["HTTP_POST_VARS"]["y117_auto"]:$this->y117_auto);
            $this->y117_levanta = ($this->y117_levanta == ""?@$GLOBALS["HTTP_POST_VARS"]["y117_levanta"]:$this->y117_levanta);
        } else {
            $this->y117_sequencial = ($this->y117_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["y117_sequencial"]:$this->y117_sequencial);
        }
    }
    // funcao para inclusao
    public function incluir($y117_sequencial = null)
    {
        $this->atualizacampos();
        if ($this->y117_auto == null) {
            $this->erro_sql = " Campo Auto de Infração não informado.";
            $this->erro_campo = "y117_auto";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y117_levanta == null) {
            $this->erro_sql = " Campo Levantamento não informado.";
            $this->erro_campo = "y117_levanta";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into fiscalizacao.fis_autolevanta(
                                       y117_auto
                                      ,y117_levanta
                       )
                values (
                                $this->y117_auto
                               ,$this->y117_levanta
                      )";
        $result = db_query($sql);
        $this->y117_sequencial = db_utils::fieldsmemory($result, 0)->y117_sequencial;
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Auto/Levanta ($this->y117_sequencial) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Auto/Levanta já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "Auto/Levanta ($this->y117_sequencial) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->y117_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);


        return true;
    }
   // funcao para alteracao
    public function alterar($y117_sequencial = null)
    {
        $this->atualizacampos();
        $sql = " update fiscalizacao.fis_autolevanta set ";
        $virgula = "";
        if (trim($this->y117_auto)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y117_auto"])) {
            $sql  .= $virgula." y117_auto = $this->y117_auto ";
            $virgula = ",";
            if (trim($this->y117_auto) == null) {
                $this->erro_sql = " Campo Auto de Infração não informado.";
                $this->erro_campo = "y117_auto";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y117_levanta)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y117_levanta"])) {
            $sql  .= $virgula." y117_levanta = $this->y117_levanta ";
            $virgula = ",";
            if (trim($this->y117_levanta) == null) {
                $this->erro_sql = " Campo Levantamento não informado.";
                $this->erro_campo = "y117_levanta";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($y117_sequencial!=null) {
            $sql .= " y117_sequencial = $this->y117_sequencial";
        }


        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Auto/Levanta nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->y117_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Auto/Levanta nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->y117_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->y117_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao para exclusao
    public function excluir($y117_sequencial = null, $dbwhere = null)
    {
        $sql = " delete from fiscalizacao.fis_autolevanta where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($y117_sequencial)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " y117_sequencial = $y117_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Auto/Levanta nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y117_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Auto/Levanta nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y117_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$y117_sequencial;
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
        if ($this->numrows == 0) {
            $this->erro_banco = "";
            $this->erro_sql   = "Record Vazio na Tabela:autolevanta";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
   // funcao do sql
    public function sql_query($y117_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos}";
        $sql .= "  from fiscalizacao.fis_autolevanta ";
        $sql .= "      inner join fiscalizacao.fis_auto  on  fis_auto.y50_codauto = fis_autolevanta.y117_auto";
        $sql .= "      inner join fiscalizacao.fis_levanta  on  fis_levanta.y60_codlev = fis_autolevanta.y117_levanta";
        $sql .= "      inner join db_config  on  db_config.codigo = fis_auto.y50_instit";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_auto.y50_setor";
        $sql .= "      inner join fiscalizacao.fis_tipofiscaliza  on  fis_tipofiscaliza.y27_codtipo = fis_auto.y50_codtipo";
        $sql .= "      left  join protprocesso  on  protprocesso.p58_codproc = fis_levanta.y60_proces";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($y117_sequencial)) {
                $sql2 .= " where fis_autolevanta.y117_sequencial = $y117_sequencial ";
            }
        } elseif (!empty($dbwhere)) {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }
        return $sql;
    }
   // funcao do sql
    public function sql_query_file($y117_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos} ";
        $sql .= "  from fiscalizacao.fis_autolevanta ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($y117_sequencial)) {
                $sql2 .= " where fis_autolevanta.y117_sequencial = $y117_sequencial ";
            }
        } elseif (!empty($dbwhere)) {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }
        return $sql;
    }
}
