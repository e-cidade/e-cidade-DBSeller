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
//CLASSE DA ENTIDADE fiscalproc
class cl_fis_fiscalproc
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
    public $y29_codtipo = 0;
    public $y29_descr = null;
    public $y29_coddepto = 0;
    public $y29_tipoandam = 0;
    public $y29_descr_obs = null;
    public $y29_dias = 0;
    public $y29_tipoproced = null;
    public $y29_tipofisc = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 y29_codtipo = int8 = Código da Procedência
                 y29_descr = varchar(50) = Descrição da Procedência
                 y29_coddepto = int4 = Código do Departamento
                 y29_tipoandam = int8 = Tipo de Andamento
                 y29_descr_obs = text = Descrição
                 y29_dias = int4 = Quantidade de dias para o vencimento
                 y29_tipoproced = varchar(1) = Tipo de Procedimento
                 y29_tipofisc = int4 = Tipo de Fiscalização
                 ";
    //funcao construtor da classe
    public function __construct()
    {
        //classes dos rotulos dos campos
        $this->rotulo = new rotulo("fis_fiscalproc");
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
            $this->y29_codtipo = ($this->y29_codtipo == ""?@$GLOBALS["HTTP_POST_VARS"]["y29_codtipo"]:$this->y29_codtipo);
            $this->y29_descr = ($this->y29_descr == ""?@$GLOBALS["HTTP_POST_VARS"]["y29_descr"]:$this->y29_descr);
            $this->y29_coddepto = ($this->y29_coddepto == ""?@$GLOBALS["HTTP_POST_VARS"]["y29_coddepto"]:$this->y29_coddepto);
            $this->y29_tipoandam = ($this->y29_tipoandam == ""?@$GLOBALS["HTTP_POST_VARS"]["y29_tipoandam"]:$this->y29_tipoandam);
            $this->y29_descr_obs = ($this->y29_descr_obs == ""?@$GLOBALS["HTTP_POST_VARS"]["y29_descr_obs"]:$this->y29_descr_obs);
            $this->y29_dias = ($this->y29_dias == ""?@$GLOBALS["HTTP_POST_VARS"]["y29_dias"]:$this->y29_dias);
            $this->y29_tipoproced = ($this->y29_tipoproced == ""?@$GLOBALS["HTTP_POST_VARS"]["y29_tipoproced"]:$this->y29_tipoproced);
            $this->y29_tipofisc = ($this->y29_tipofisc == ""?@$GLOBALS["HTTP_POST_VARS"]["y29_tipofisc"]:$this->y29_tipofisc);
        } else {
            $this->y29_codtipo = ($this->y29_codtipo == ""?@$GLOBALS["HTTP_POST_VARS"]["y29_codtipo"]:$this->y29_codtipo);
        }
    }
    // funcao para inclusao
    public function incluir($y29_codtipo = null)
    {
        $this->atualizacampos();
        if ($this->y29_descr == null) {
            $this->erro_sql = " Campo Descrição da Procedência nao Informado.";
            $this->erro_campo = "y29_descr";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y29_coddepto == null) {
            $this->erro_sql = " Campo Código do Departamento nao Informado.";
            $this->erro_campo = "y29_coddepto";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y29_tipoandam == null) {
            $this->erro_sql = " Campo Tipo de Andamento nao Informado.";
            $this->erro_campo = "y29_tipoandam";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y29_descr_obs == null) {
            $this->erro_sql = " Campo Descrição nao Informado.";
            $this->erro_campo = "y29_descr_obs";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y29_dias == null) {
            $this->y29_dias = "0";
        }
        if ($this->y29_tipoproced == null) {
            $this->erro_sql = " Campo Tipo de Procedimento nao Informado.";
            $this->erro_campo = "y29_tipoproced";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y29_tipofisc == null) {
            $this->erro_sql = " Campo Tipo de Fiscalização nao Informado.";
            $this->erro_campo = "y29_tipofisc";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into fiscalizacao.fis_fiscalproc(
                                       y29_descr
                                      ,y29_coddepto
                                      ,y29_tipoandam
                                      ,y29_descr_obs
                                      ,y29_dias
                                      ,y29_tipoproced
                                      ,y29_tipofisc
                       )
                values (
                               '$this->y29_descr'
                               ,$this->y29_coddepto
                               ,$this->y29_tipoandam
                               ,'$this->y29_descr_obs'
                               ,$this->y29_dias
                               ,'$this->y29_tipoproced'
                               ,$this->y29_tipofisc
                      ) returning y29_codtipo ";
        $result = db_query($sql);
        $this->y29_codtipo = db_utils::fieldsmemory($result, 0)->y29_codtipo;
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "fiscalproc ($this->y29_codtipo) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "fiscalproc já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "fiscalproc ($this->y29_codtipo) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->y29_codtipo;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);

        return true;
    }
    // funcao para alteracao
    public function alterar($y29_codtipo = null)
    {
        $this->atualizacampos();
        $sql = " update fiscalizacao.fis_fiscalproc set ";
        $virgula = "";
        if (trim($this->y29_descr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y29_descr"])) {
            $sql  .= $virgula." y29_descr = '$this->y29_descr' ";
            $virgula = ",";
            if (trim($this->y29_descr) == null) {
                $this->erro_sql = " Campo Descrição da Procedência nao Informado.";
                $this->erro_campo = "y29_descr";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y29_coddepto)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y29_coddepto"])) {
            $sql  .= $virgula." y29_coddepto = $this->y29_coddepto ";
            $virgula = ",";
            if (trim($this->y29_coddepto) == null) {
                $this->erro_sql = " Campo Código do Departamento nao Informado.";
                $this->erro_campo = "y29_coddepto";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y29_tipoandam)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y29_tipoandam"])) {
            $sql  .= $virgula." y29_tipoandam = $this->y29_tipoandam ";
            $virgula = ",";
            if (trim($this->y29_tipoandam) == null) {
                $this->erro_sql = " Campo Tipo de Andamento nao Informado.";
                $this->erro_campo = "y29_tipoandam";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y29_descr_obs)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y29_descr_obs"])) {
            $sql  .= $virgula." y29_descr_obs = '$this->y29_descr_obs' ";
            $virgula = ",";
            if (trim($this->y29_descr_obs) == null) {
                $this->erro_sql = " Campo Descrição nao Informado.";
                $this->erro_campo = "y29_descr_obs";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y29_dias)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y29_dias"])) {
            if (trim($this->y29_dias)=="" && isset($GLOBALS["HTTP_POST_VARS"]["y29_dias"])) {
                $this->y29_dias = "0" ;
            }
            $sql  .= $virgula." y29_dias = $this->y29_dias ";
            $virgula = ",";
        }
        if (trim($this->y29_tipoproced)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y29_tipoproced"])) {
            $sql  .= $virgula." y29_tipoproced = '$this->y29_tipoproced' ";
            $virgula = ",";
            if (trim($this->y29_tipoproced) == null) {
                $this->erro_sql = " Campo Tipo de Procedimento nao Informado.";
                $this->erro_campo = "y29_tipoproced";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y29_tipofisc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y29_tipofisc"])) {
            $sql  .= $virgula." y29_tipofisc = $this->y29_tipofisc ";
            $virgula = ",";
            if (trim($this->y29_tipofisc) == null) {
                $this->erro_sql = " Campo Tipo de Fiscalização nao Informado.";
                $this->erro_campo = "y29_tipofisc";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($y29_codtipo!=null) {
            $sql .= " y29_codtipo = $this->y29_codtipo";
        }


        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "fiscalproc nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->y29_codtipo;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "fiscalproc nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->y29_codtipo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->y29_codtipo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao para exclusao
    public function excluir($y29_codtipo = null, $dbwhere = null)
    {
        $sql = " delete from fiscalizacao.fis_fiscalproc where ";
        $sql2 = "";
        if ($dbwhere == null || $dbwhere == "") {
            if ($y29_codtipo != "") {
                if ($sql2!="") {
                    $sql2 .= " and ";
                }
                $sql2 .= " y29_codtipo = $y29_codtipo ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "fiscalproc nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y29_codtipo;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "fiscalproc nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y29_codtipo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$y29_codtipo;
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
            $this->erro_sql   = "Record Vazio na Tabela:fiscalproc";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
    public function sql_query($y29_codtipo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_fiscalproc ";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_fiscalproc.y29_coddepto";
        $sql .= "      inner join fiscalizacao.fis_tipoandam  on  fis_tipoandam.y41_codtipo = fis_fiscalproc.y29_tipoandam";
        $sql .= "      inner join fiscalizacao.fis_tipofiscaliza  on  fis_tipofiscaliza.y27_codtipo = fis_fiscalproc.y29_tipofisc";
        $sql .= "      left outer join fiscalizacao.fis_fiscalprocpa on  y29_codtipo = y61_codpa";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y29_codtipo!=null) {
                $sql2 .= " where fis_fiscalproc.y29_codtipo = $y29_codtipo ";
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
    public function sql_query_file($y29_codtipo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_fiscalproc ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y29_codtipo!=null) {
                $sql2 .= " where fis_fiscalproc.y29_codtipo = $y29_codtipo ";
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
    public function sql_query_rec($y29_codtipo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_fiscalproc ";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_fiscalproc.y29_coddepto";
        $sql .= "      inner join fiscalizacao.fis_tipoandam  on  fis_tipoandam.y41_codtipo = fis_fiscalproc.y29_tipoandam";
        $sql .= "      left join fiscalizacao.fis_fiscalprocrec  on  fis_fiscalprocrec.y45_codtipo = fis_fiscalproc.y29_codtipo";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y29_codtipo!=null) {
                $sql2 .= " where fis_fiscalproc.y29_codtipo = $y29_codtipo ";
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
        }    return $sql;
    }
}
