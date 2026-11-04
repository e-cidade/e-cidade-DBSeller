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
//CLASSE DA ENTIDADE lancamentotaxadiversos
class cl_fis_lancamentotaxadiversos
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
    public $y120_sequencial = 0;
    public $y120_cgm = 0;
    public $y120_taxadiversos = 0;
    public $y120_unidade = 0;
    public $y120_periodo = 0;
    public $y120_datainicio_dia = null;
    public $y120_datainicio_mes = null;
    public $y120_datainicio_ano = null;
    public $y120_datainicio = null;
    public $y120_datafim_dia = null;
    public $y120_datafim_mes = null;
    public $y120_datafim_ano = null;
    public $y120_datafim = null;
    public $y120_issbase = null;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 y120_sequencial = int4 = Sequencial
                 y120_cgm = int4 = CGM
                 y120_taxadiversos = int4 = Taxa
                 y120_unidade = float8 = Unidade
                 y120_periodo = float8 = Período
                 y120_datainicio = date = Data de Início
                 y120_datafim = date = Data de fim
                 y120_issbase = int4 = Inscrição Municipal
                 ";
    //funcao construtor da classe
    public function __construct()
    {
        //classes dos rotulos dos campos
        $this->rotulo = new rotulo("fis_lancamentotaxadiversos");
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
            $this->y120_sequencial = ($this->y120_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["y120_sequencial"]:$this->y120_sequencial);
            $this->y120_cgm = ($this->y120_cgm == ""?@$GLOBALS["HTTP_POST_VARS"]["y120_cgm"]:$this->y120_cgm);
            $this->y120_taxadiversos = ($this->y120_taxadiversos == ""?@$GLOBALS["HTTP_POST_VARS"]["y120_taxadiversos"]:$this->y120_taxadiversos);
            $this->y120_unidade = ($this->y120_unidade == ""?@$GLOBALS["HTTP_POST_VARS"]["y120_unidade"]:$this->y120_unidade);
            $this->y120_periodo = ($this->y120_periodo == ""?@$GLOBALS["HTTP_POST_VARS"]["y120_periodo"]:$this->y120_periodo);
            if ($this->y120_datainicio == "") {
                $this->y120_datainicio_dia = ($this->y120_datainicio_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y120_datainicio_dia"]:$this->y120_datainicio_dia);
                $this->y120_datainicio_mes = ($this->y120_datainicio_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y120_datainicio_mes"]:$this->y120_datainicio_mes);
                $this->y120_datainicio_ano = ($this->y120_datainicio_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y120_datainicio_ano"]:$this->y120_datainicio_ano);
                if ($this->y120_datainicio_dia != "") {
                    $this->y120_datainicio = $this->y120_datainicio_ano."-".$this->y120_datainicio_mes."-".$this->y120_datainicio_dia;
                }
            }
            if ($this->y120_datafim == "") {
                $this->y120_datafim_dia = ($this->y120_datafim_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y120_datafim_dia"]:$this->y120_datafim_dia);
                $this->y120_datafim_mes = ($this->y120_datafim_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y120_datafim_mes"]:$this->y120_datafim_mes);
                $this->y120_datafim_ano = ($this->y120_datafim_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y120_datafim_ano"]:$this->y120_datafim_ano);
                if ($this->y120_datafim_dia != "") {
                    $this->y120_datafim = $this->y120_datafim_ano."-".$this->y120_datafim_mes."-".$this->y120_datafim_dia;
                }
            }
            $this->y120_issbase = ($this->y120_issbase == ""?@$GLOBALS["HTTP_POST_VARS"]["y120_issbase"]:$this->y120_issbase);
        } else {
            $this->y120_sequencial = ($this->y120_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["y120_sequencial"]:$this->y120_sequencial);
        }
    }
  // funcao para Inclusão
    public function incluir($y120_sequencial = null)
    {
        $this->atualizacampos();
        if ($this->y120_cgm == null) {
            $this->y120_cgm = "0";
        }
        if ($this->y120_taxadiversos == null) {
            $this->erro_sql = " Campo Taxa não informado.";
            $this->erro_campo = "y120_taxadiversos";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y120_unidade == null) {
            $this->erro_sql = " Campo Unidade não informado.";
            $this->erro_campo = "y120_unidade";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y120_periodo == null) {
            $this->y120_periodo = "0";
        }
        if ($this->y120_datainicio == null) {
            $this->y120_datainicio = "null";
        }
        if ($this->y120_datafim == null) {
            $this->y120_datafim = "null";
        }
        if ($this->y120_issbase == null) {
            $this->y120_issbase = 'null';
        }
        $sql = "insert into fiscalizacao.fis_lancamentotaxadiversos(
                                       y120_cgm
                                      ,y120_taxadiversos
                                      ,y120_unidade
                                      ,y120_periodo
                                      ,y120_datainicio
                                      ,y120_datafim
                                      ,y120_issbase
                       )
                values (
                                $this->y120_cgm
                               ,$this->y120_taxadiversos
                               ,$this->y120_unidade
                               ,$this->y120_periodo
                               ,".($this->y120_datainicio == "null" || $this->y120_datainicio == ""?"null":"'".$this->y120_datainicio."'")."
                               ,".($this->y120_datafim == "null" || $this->y120_datafim == ""?"null":"'".$this->y120_datafim."'")."
                               ,$this->y120_issbase
                      ) returning y120_sequencial ";

        $result = db_query($sql);
        $this->y120_sequencial = db_utils::fieldsmemory($result, 0)->y120_sequencial;

        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Lançamento de Taxas diversas ($this->y120_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Lançamento de Taxas diversas já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "Lançamento de Taxas diversas ($this->y120_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }

        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->y120_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);

        return true;
    }
    // funcao para alteracao
    public function alterar($y120_sequencial = null)
    {
        $this->atualizacampos();
        $sql = " update fiscalizacao.fis_lancamentotaxadiversos set ";
        $virgula = "";
        if (trim($this->y120_cgm)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y120_cgm"])) {
            if (trim($this->y120_cgm)=="" && isset($GLOBALS["HTTP_POST_VARS"]["y120_cgm"])) {
                $this->y120_cgm = "0" ;
            }
            $sql  .= $virgula." y120_cgm = $this->y120_cgm ";
            $virgula = ",";
        }
        if (trim($this->y120_taxadiversos)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y120_taxadiversos"])) {
            $sql  .= $virgula." y120_taxadiversos = $this->y120_taxadiversos ";
            $virgula = ",";
            if (trim($this->y120_taxadiversos) == null) {
                $this->erro_sql = " Campo Taxa não informado.";
                $this->erro_campo = "y120_taxadiversos";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y120_unidade)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y120_unidade"])) {
            $sql  .= $virgula." y120_unidade = $this->y120_unidade ";
            $virgula = ",";
            if (trim($this->y120_unidade) == null) {
                $this->erro_sql = " Campo Unidade não informado.";
                $this->erro_campo = "y120_unidade";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y120_periodo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y120_periodo"])) {
            if (trim($this->y120_periodo)=="" && isset($GLOBALS["HTTP_POST_VARS"]["y120_periodo"])) {
                $this->y120_periodo = "0" ;
            }
            $sql  .= $virgula." y120_periodo = $this->y120_periodo ";
            $virgula = ",";
        }
        if (trim($this->y120_datainicio)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y120_datainicio_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y120_datainicio_dia"] !="")) {
            if (trim($this->y120_datainicio) == 'null') {
                $sql  .= $virgula." y120_datainicio = null ";
            } else {
                $sql  .= $virgula." y120_datainicio = '$this->y120_datainicio' ";
            }

            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y120_datainicio_dia"])) {
                $sql  .= $virgula." y120_datainicio = null ";
                $virgula = ",";
            }
        }
        if (trim($this->y120_datafim)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y120_datafim_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y120_datafim_dia"] !="")) {
            if (trim($this->y120_datafim) == 'null') {
                $sql  .= $virgula." y120_datafim = null ";
            } else {
                $sql  .= $virgula." y120_datafim = '$this->y120_datafim' ";
            }

            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y120_datafim_dia"])) {
                $sql  .= $virgula." y120_datafim = null ";
                $virgula = ",";
            }
        }
        if (trim($this->y120_issbase)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y120_issbase"])) {
            if (trim($this->y120_issbase)=="" && isset($GLOBALS["HTTP_POST_VARS"]["y120_issbase"])) {
                $this->y120_issbase = 'null' ;
            }
            $sql  .= $virgula." y120_issbase = $this->y120_issbase ";
            $virgula = ",";
        }
        $sql .= " where ";
        if ($y120_sequencial!=null) {
            $sql .= " y120_sequencial = $this->y120_sequencial";
        }

        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Lançamento de Taxas diversas não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->y120_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Lançamento de Taxas diversas não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->y120_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->y120_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
  // funcao para exclusao
    public function excluir($y120_sequencial = null, $dbwhere = null)
    {
        $sql = " delete from fiscalizacao.fis_lancamentotaxadiversos where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($y120_sequencial)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " y120_sequencial = $y120_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Lançamento de Taxas diversas não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y120_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Lançamento de Taxas diversas não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y120_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$y120_sequencial;
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
            $this->erro_sql   = "Record Vazio na Tabela:lancamentotaxadiversos";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    // funcao do sql
    public function sql_query($y120_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos}";
        $sql .= "  from fiscalizacao.fis_lancamentotaxadiversos ";
        $sql .= "      left  join issbase  on  issbase.q02_inscr = fis_lancamentotaxadiversos.y120_issbase";
        $sql .= "      left  join cgm  on  cgm.z01_numcgm = fis_lancamentotaxadiversos.y120_cgm";
        $sql .= "      inner join fiscalizacao.fis_taxadiversos  on  fis_taxadiversos.y119_sequencial = fis_lancamentotaxadiversos.y120_taxadiversos";
        $sql .= "      left  join cgm  as a on   a.z01_numcgm = issbase.q02_numcgm";
        $sql .= "      inner join db_formulas  on  db_formulas.db148_sequencial = fis_taxadiversos.y119_formula";
        $sql .= "      inner join fiscalizacao.fis_grupotaxadiversos  on  fis_grupotaxadiversos.y118_sequencial = fis_taxadiversos.y119_grupotaxadiversos";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($y120_sequencial)) {
                $sql2 .= " where fis_lancamentotaxadiversos.y120_sequencial = $y120_sequencial ";
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
    public function sql_query_join_diversos($y120_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos}";
        $sql .= "  from fiscalizacao.fis_lancamentotaxadiversos ";
        $sql .= "      left  join issbase  on  issbase.q02_inscr = fis_lancamentotaxadiversos.y120_issbase";
        $sql .= "      left  join cgm  on  cgm.z01_numcgm = fis_lancamentotaxadiversos.y120_cgm";
        $sql .= "      inner join fiscalizacao.fis_taxadiversos  on  fis_taxadiversos.y119_sequencial = fis_lancamentotaxadiversos.y120_taxadiversos";
        $sql .= "      left  join cgm  as a on   a.z01_numcgm = issbase.q02_numcgm";
        $sql .= "      inner join db_formulas  on  db_formulas.db148_sequencial = fis_taxadiversos.y119_formula";
        $sql .= "      inner join fiscalizacao.fis_grupotaxadiversos  on  fis_grupotaxadiversos.y118_sequencial = fis_taxadiversos.y119_grupotaxadiversos";
        $sql .= "      left  join diversoslancamentotaxa on dv14_lancamentotaxadiversos = fis_lancamentotaxadiversos.y120_sequencial";
        $sql .= "      left  join diversos on dv05_coddiver = diversoslancamentotaxa.dv14_diversos";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($y120_sequencial)) {
                $sql2 .= " where fis_lancamentotaxadiversos.y120_sequencial = $y120_sequencial ";
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
    public function sql_query_file($y120_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos} ";
        $sql .= "  from fiscalizacao.fis_lancamentotaxadiversos ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($y120_sequencial)) {
                $sql2 .= " where fis_lancamentotaxadiversos.y120_sequencial = $y120_sequencial ";
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
