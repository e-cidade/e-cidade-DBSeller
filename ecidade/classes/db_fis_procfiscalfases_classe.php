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
//CLASSE DA ENTIDADE procfiscalfases
class cl_fis_procfiscalfases
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
    public $y108_sequencial = 0;
    public $y108_procfiscal = 0;
    public $y108_dtcriacao_dia = null;
    public $y108_dtcriacao_mes = null;
    public $y108_dtcriacao_ano = null;
    public $y108_dtcriacao = null;
    public $y108_dtassinatura_dia = null;
    public $y108_dtassinatura_mes = null;
    public $y108_dtassinatura_ano = null;
    public $y108_dtassinatura = null;
    public $y108_responsavel = 0;
    public $y108_tipo = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 y108_sequencial = int4 = Código
                 y108_procfiscal = int4 = Processo Fiscal
                 y108_dtcriacao = date = Data de Criação
                 y108_dtassinatura = date = Data da assinatura
                 y108_responsavel = int4 = Responsavel
                 y108_tipo = int4 = Tipo
                 ";
    //funcao construtor da classe
    public function __construct()
    {
         //classes dos rotulos dos campos
         $this->rotulo = new rotulo("fis_procfiscalfases");
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
            $this->y108_sequencial = ($this->y108_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["y108_sequencial"]:$this->y108_sequencial);
            $this->y108_procfiscal = ($this->y108_procfiscal == ""?@$GLOBALS["HTTP_POST_VARS"]["y108_procfiscal"]:$this->y108_procfiscal);
            if ($this->y108_dtcriacao == "") {
                $this->y108_dtcriacao_dia = ($this->y108_dtcriacao_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y108_dtcriacao_dia"]:$this->y108_dtcriacao_dia);
                $this->y108_dtcriacao_mes = ($this->y108_dtcriacao_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y108_dtcriacao_mes"]:$this->y108_dtcriacao_mes);
                $this->y108_dtcriacao_ano = ($this->y108_dtcriacao_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y108_dtcriacao_ano"]:$this->y108_dtcriacao_ano);
                if ($this->y108_dtcriacao_dia != "") {
                    $this->y108_dtcriacao = $this->y108_dtcriacao_ano."-".$this->y108_dtcriacao_mes."-".$this->y108_dtcriacao_dia;
                }
            }
            if ($this->y108_dtassinatura == "") {
                $this->y108_dtassinatura_dia = ($this->y108_dtassinatura_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y108_dtassinatura_dia"]:$this->y108_dtassinatura_dia);
                $this->y108_dtassinatura_mes = ($this->y108_dtassinatura_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y108_dtassinatura_mes"]:$this->y108_dtassinatura_mes);
                $this->y108_dtassinatura_ano = ($this->y108_dtassinatura_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y108_dtassinatura_ano"]:$this->y108_dtassinatura_ano);
                if ($this->y108_dtassinatura_dia != "") {
                    $this->y108_dtassinatura = $this->y108_dtassinatura_ano."-".$this->y108_dtassinatura_mes."-".$this->y108_dtassinatura_dia;
                }
            }
            $this->y108_responsavel = ($this->y108_responsavel == ""?@$GLOBALS["HTTP_POST_VARS"]["y108_responsavel"]:$this->y108_responsavel);
            $this->y108_tipo = ($this->y108_tipo == ""?@$GLOBALS["HTTP_POST_VARS"]["y108_tipo"]:$this->y108_tipo);
        } else {
            $this->y108_sequencial = ($this->y108_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["y108_sequencial"]:$this->y108_sequencial);
        }
    }
   // funcao para inclusao
    public function incluir($y108_sequencial = null)
    {
        $this->atualizacampos();
        if ($this->y108_procfiscal == null) {
            $this->erro_sql = " Campo Processo Fiscal nao Informado.";
            $this->erro_campo = "y108_procfiscal";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y108_dtcriacao == null) {
            $this->erro_sql = " Campo Data de Criação nao Informado.";
            $this->erro_campo = "y108_dtcriacao_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y108_dtassinatura == null) {
            $this->erro_sql = " Campo Data da assinatura nao Informado.";
            $this->erro_campo = "y108_dtassinatura_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y108_responsavel == null) {
            $this->erro_sql = " Campo Responsavel nao Informado.";
            $this->erro_campo = "y108_responsavel";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y108_tipo == null) {
            $this->erro_sql = " Campo Tipo nao Informado.";
            $this->erro_campo = "y108_tipo";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into fiscalizacao.fis_procfiscalfases(
                                       y108_procfiscal
                                      ,y108_dtcriacao
                                      ,y108_dtassinatura
                                      ,y108_responsavel
                                      ,y108_tipo
                       )
                values (
                                $this->y108_procfiscal
                               ,".($this->y108_dtcriacao == "null" || $this->y108_dtcriacao == ""?"null":"'".$this->y108_dtcriacao."'")."
                               ,".($this->y108_dtassinatura == "null" || $this->y108_dtassinatura == ""?"null":"'".$this->y108_dtassinatura."'")."
                               ,$this->y108_responsavel
                               ,$this->y108_tipo
                      ) returning y108_sequencial";
        $result = db_query($sql);
        $this->y108_sequencial = db_utils::fieldsmemory($result, 0)->y108_sequencial;

        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "procfiscalfases ($this->y108_sequencial) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "procfiscalfases já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "procfiscalfases ($this->y108_sequencial) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->y108_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);


        return true;
    }
   // funcao para alteracao
    public function alterar($y108_sequencial = null)
    {
          $this->atualizacampos();
         $sql = " update fiscalizacao.fis_procfiscalfases set ";
         $virgula = "";
        if (trim($this->y108_procfiscal)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y108_procfiscal"])) {
            $sql  .= $virgula." y108_procfiscal = $this->y108_procfiscal ";
            $virgula = ",";
            if (trim($this->y108_procfiscal) == null) {
                $this->erro_sql = " Campo Processo Fiscal nao Informado.";
                $this->erro_campo = "y108_procfiscal";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y108_dtcriacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y108_dtcriacao_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y108_dtcriacao_dia"] !="")) {
            $sql  .= $virgula." y108_dtcriacao = '$this->y108_dtcriacao' ";
            $virgula = ",";
            if (trim($this->y108_dtcriacao) == null) {
                $this->erro_sql = " Campo Data de Criação nao Informado.";
                $this->erro_campo = "y108_dtcriacao_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y108_dtcriacao_dia"])) {
                $sql  .= $virgula." y108_dtcriacao = null ";
                $virgula = ",";
                if (trim($this->y108_dtcriacao) == null) {
                    $this->erro_sql = " Campo Data de Criação nao Informado.";
                    $this->erro_campo = "y108_dtcriacao_dia";
                    $this->erro_banco = "";
                    $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        if (trim($this->y108_dtassinatura)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y108_dtassinatura_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y108_dtassinatura_dia"] !="")) {
            $sql  .= $virgula." y108_dtassinatura = '$this->y108_dtassinatura' ";
            $virgula = ",";
            if (trim($this->y108_dtassinatura) == null) {
                $this->erro_sql = " Campo Data da assinatura nao Informado.";
                $this->erro_campo = "y108_dtassinatura_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y108_dtassinatura_dia"])) {
                     $sql  .= $virgula." y108_dtassinatura = null ";
                     $virgula = ",";
                if (trim($this->y108_dtassinatura) == null) {
                    $this->erro_sql = " Campo Data da assinatura nao Informado.";
                    $this->erro_campo = "y108_dtassinatura_dia";
                    $this->erro_banco = "";
                    $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        if (trim($this->y108_responsavel)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y108_responsavel"])) {
            $sql  .= $virgula." y108_responsavel = $this->y108_responsavel ";
            $virgula = ",";
            if (trim($this->y108_responsavel) == null) {
                 $this->erro_sql = " Campo Responsavel nao Informado.";
                 $this->erro_campo = "y108_responsavel";
                 $this->erro_banco = "";
                 $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                 $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                 $this->erro_status = "0";
                 return false;
            }
        }
        if (trim($this->y108_tipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y108_tipo"])) {
            $sql  .= $virgula." y108_tipo = $this->y108_tipo ";
            $virgula = ",";
            if (trim($this->y108_tipo) == null) {
                 $this->erro_sql = " Campo Tipo nao Informado.";
                 $this->erro_campo = "y108_tipo";
                 $this->erro_banco = "";
                 $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                 $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                 $this->erro_status = "0";
                 return false;
            }
        }
        $sql .= " where ";
        if ($y108_sequencial!=null) {
            $sql .= " y108_sequencial = $this->y108_sequencial";
        }


        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "procfiscalfases nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->y108_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                 $this->erro_banco = "";
                 $this->erro_sql = "procfiscalfases nao foi Alterado. Alteracao Executada.\\n";
                 $this->erro_sql .= "Valores : ".$this->y108_sequencial;
                 $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                 $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                 $this->erro_status = "1";
                 $this->numrows_alterar = 0;
                 return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->y108_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao para exclusao
    public function excluir($y108_sequencial = null, $dbwhere = null)
    {
         $sql = " delete from fiscalizacao.fis_procfiscalfases where ";
         $sql2 = "";
        if ($dbwhere==null || $dbwhere =="") {
            if ($y108_sequencial != "") {
                if ($sql2!="") {
                    $sql2 .= " and ";
                }
                $sql2 .= " y108_sequencial = $y108_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "procfiscalfases nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y108_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "procfiscalfases nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y108_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$y108_sequencial;
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
            $this->erro_sql   = "Record Vazio na Tabela:procfiscalfases";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
    public function sql_query($y108_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_procfiscalfases ";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = fis_procfiscalfases.y108_responsavel";
        $sql .= "      inner join fiscalizacao.fis_procfiscal  on  fis_procfiscal.y100_sequencial = fis_procfiscalfases.y108_procfiscal";
        $sql .= "      inner join db_config  on  db_config.codigo = fis_procfiscal.y100_instit";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_procfiscal.y100_coddepto";
        $sql .= "      inner join fiscalizacao.fis_procfiscalcadtipo  on  fis_procfiscalcadtipo.y33_sequencial = fis_procfiscal.y100_procfiscalcadtipo";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y108_sequencial!=null) {
                $sql2 .= " where fis_procfiscalfases.y108_sequencial = $y108_sequencial ";
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
    public function sql_query_file($y108_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_procfiscalfases ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y108_sequencial!=null) {
                $sql2 .= " where fis_procfiscalfases.y108_sequencial = $y108_sequencial ";
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
