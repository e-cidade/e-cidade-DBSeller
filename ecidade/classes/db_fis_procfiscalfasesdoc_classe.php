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
//CLASSE DA ENTIDADE procfiscalfasesdoc
class cl_fis_procfiscalfasesdoc
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
    public $y107_sequencial = 0;
    public $y107_procfiscalfases = 0;
    public $y107_dtinc_dia = null;
    public $y107_dtinc_mes = null;
    public $y107_dtinc_ano = null;
    public $y107_dtinc = null;
    public $y107_documento = 0;
    public $y107_tipoinclusao = 0;
    public $y107_nomedoc = null;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 y107_sequencial = int4 = Código
                 y107_procfiscalfases = int4 = Código Fases
                 y107_dtinc = date = Data de inclusão
                 y107_documento = oid = Documento
                 y107_tipoinclusao = int4 = Tipo de Inclusão
                 y107_nomedoc = varchar(100) = Nome do Arquivo
                 ";
    //funcao construtor da classe
    public function __construct()
    {
         //classes dos rotulos dos campos
         $this->rotulo = new rotulo("fis_procfiscalfasesdoc");
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
            $this->y107_sequencial = ($this->y107_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["y107_sequencial"]:$this->y107_sequencial);
            $this->y107_procfiscalfases = ($this->y107_procfiscalfases == ""?@$GLOBALS["HTTP_POST_VARS"]["y107_procfiscalfases"]:$this->y107_procfiscalfases);
            if ($this->y107_dtinc == "") {
                $this->y107_dtinc_dia = ($this->y107_dtinc_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y107_dtinc_dia"]:$this->y107_dtinc_dia);
                $this->y107_dtinc_mes = ($this->y107_dtinc_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y107_dtinc_mes"]:$this->y107_dtinc_mes);
                $this->y107_dtinc_ano = ($this->y107_dtinc_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y107_dtinc_ano"]:$this->y107_dtinc_ano);
                if ($this->y107_dtinc_dia != "") {
                    $this->y107_dtinc = $this->y107_dtinc_ano."-".$this->y107_dtinc_mes."-".$this->y107_dtinc_dia;
                }
            }
            $this->y107_documento = ($this->y107_documento == ""?@$GLOBALS["HTTP_POST_VARS"]["y107_documento"]:$this->y107_documento);
            $this->y107_tipoinclusao = ($this->y107_tipoinclusao == ""?@$GLOBALS["HTTP_POST_VARS"]["y107_tipoinclusao"]:$this->y107_tipoinclusao);
            $this->y107_nomedoc = ($this->y107_nomedoc == ""?@$GLOBALS["HTTP_POST_VARS"]["y107_nomedoc"]:$this->y107_nomedoc);
        } else {
            $this->y107_sequencial = ($this->y107_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["y107_sequencial"]:$this->y107_sequencial);
        }
    }
    // funcao para inclusao
    public function incluir($y107_sequencial = null)
    {
          $this->atualizacampos();
        if ($this->y107_procfiscalfases == null) {
            $this->erro_sql = " Campo Código Fases nao Informado.";
            $this->erro_campo = "y107_procfiscalfases";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y107_dtinc == null) {
            $this->erro_sql = " Campo Data de inclusão nao Informado.";
            $this->erro_campo = "y107_dtinc_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y107_documento == null) {
            $this->erro_sql = " Campo Documento nao Informado.";
            $this->erro_campo = "y107_documento";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y107_tipoinclusao == null) {
            $this->erro_sql = " Campo Tipo de Inclusão nao Informado.";
            $this->erro_campo = "y107_tipoinclusao";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y107_nomedoc == null) {
            $this->erro_sql = " Campo Nome do Arquivo nao Informado.";
            $this->erro_campo = "y107_nomedoc";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into fiscalizacao.fis_procfiscalfasesdoc(
                                       y107_procfiscalfases
                                      ,y107_dtinc
                                      ,y107_documento
                                      ,y107_tipoinclusao
                                      ,y107_nomedoc
                       )
                values (
                                $this->y107_procfiscalfases
                               ,".($this->y107_dtinc == "null" || $this->y107_dtinc == ""?"null":"'".$this->y107_dtinc."'")."
                               ,$this->y107_documento
                               ,$this->y107_tipoinclusao
                               ,'$this->y107_nomedoc'
                      ) returning y107_sequencial ";
        $result = db_query($sql);
        $this->y107_sequencial = db_utils::fieldsmemory($result, 0)->y107_sequencial;

        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "procfiscalfasesdoc ($this->y107_sequencial) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "procfiscalfasesdoc já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "procfiscalfasesdoc ($this->y107_sequencial) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->y107_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);

        return true;
    }
    // funcao para alteracao
    public function alterar($y107_sequencial = null)
    {
          $this->atualizacampos();
         $sql = " update fiscalizacao.fis_procfiscalfasesdoc set ";
         $virgula = "";
        if (trim($this->y107_procfiscalfases)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y107_procfiscalfases"])) {
            $sql  .= $virgula." y107_procfiscalfases = $this->y107_procfiscalfases ";
            $virgula = ",";
            if (trim($this->y107_procfiscalfases) == null) {
                $this->erro_sql = " Campo Código Fases nao Informado.";
                $this->erro_campo = "y107_procfiscalfases";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y107_dtinc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y107_dtinc_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y107_dtinc_dia"] !="")) {
            $sql  .= $virgula." y107_dtinc = '$this->y107_dtinc' ";
            $virgula = ",";
            if (trim($this->y107_dtinc) == null) {
                $this->erro_sql = " Campo Data de inclusão nao Informado.";
                $this->erro_campo = "y107_dtinc_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y107_dtinc_dia"])) {
                $sql  .= $virgula." y107_dtinc = null ";
                $virgula = ",";
                if (trim($this->y107_dtinc) == null) {
                    $this->erro_sql = " Campo Data de inclusão nao Informado.";
                    $this->erro_campo = "y107_dtinc_dia";
                    $this->erro_banco = "";
                    $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        if (trim($this->y107_documento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y107_documento"])) {
            $sql  .= $virgula." y107_documento = $this->y107_documento ";
            $virgula = ",";
            if (trim($this->y107_documento) == null) {
                $this->erro_sql = " Campo Documento nao Informado.";
                $this->erro_campo = "y107_documento";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y107_tipoinclusao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y107_tipoinclusao"])) {
            $sql  .= $virgula." y107_tipoinclusao = $this->y107_tipoinclusao ";
            $virgula = ",";
            if (trim($this->y107_tipoinclusao) == null) {
                $this->erro_sql = " Campo Tipo de Inclusão nao Informado.";
                $this->erro_campo = "y107_tipoinclusao";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y107_nomedoc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y107_nomedoc"])) {
            $sql  .= $virgula." y107_nomedoc = '$this->y107_nomedoc' ";
            $virgula = ",";
            if (trim($this->y107_nomedoc) == null) {
                $this->erro_sql = " Campo Nome do Arquivo nao Informado.";
                $this->erro_campo = "y107_nomedoc";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($y107_sequencial!=null) {
            $sql .= " y107_sequencial = $this->y107_sequencial";
        }


        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "procfiscalfasesdoc nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->y107_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "procfiscalfasesdoc nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->y107_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->y107_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao para exclusao
    public function excluir($y107_sequencial = null, $dbwhere = null)
    {
         $sql = " delete from fiscalizacao.fis_procfiscalfasesdoc where ";
         $sql2 = "";
        if ($dbwhere==null || $dbwhere =="") {
            if ($y107_sequencial != "") {
                if ($sql2!="") {
                    $sql2 .= " and ";
                }
                $sql2 .= " y107_sequencial = $y107_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "procfiscalfasesdoc nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y107_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "procfiscalfasesdoc nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y107_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$y107_sequencial;
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
            $this->erro_sql   = "Record Vazio na Tabela:procfiscalfasesdoc";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
    public function sql_query($y107_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_procfiscalfasesdoc ";
        $sql .= "      inner join fiscalizacao.fis_procfiscalfases  on  fis_procfiscalfases.y108_sequencial = fis_procfiscalfasesdoc.y107_procfiscalfases";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = fis_procfiscalfases.y108_responsavel";
        $sql .= "      inner join fiscalizacao.fis_procfiscal  on  fis_procfiscal.y100_sequencial = fis_procfiscalfases.y108_procfiscal";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y107_sequencial!=null) {
                $sql2 .= " where fis_procfiscalfasesdoc.y107_sequencial = $y107_sequencial ";
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
    public function sql_query_file($y107_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_procfiscalfasesdoc ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y107_sequencial!=null) {
                $sql2 .= " where fis_procfiscalfasesdoc.y107_sequencial = $y107_sequencial ";
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
