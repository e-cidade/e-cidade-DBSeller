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
//CLASSE DA ENTIDADE tipoandam
class cl_fis_tipoandam
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
    public $y41_codtipo = 0;
    public $y41_descr = null;
    public $y41_obs = null;
    public $y41_permcalc = 'f';
    public $y41_instit = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 y41_codtipo = int4 = Tipo de Andamento
                 y41_descr = varchar(50) = Descrição do tipo
                 y41_obs = text = Observação do Tipo
                 y41_permcalc = bool = Permite Cálculo
                 y41_instit = int4 = Cod. Instituição
                 ";
   //funcao construtor da classe
    public function __construct()
    {
      //classes dos rotulos dos campos
        $this->rotulo = new rotulo("fis_tipoandam");
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
            $this->y41_codtipo = ($this->y41_codtipo == ""?@$GLOBALS["HTTP_POST_VARS"]["y41_codtipo"]:$this->y41_codtipo);
            $this->y41_descr = ($this->y41_descr == ""?@$GLOBALS["HTTP_POST_VARS"]["y41_descr"]:$this->y41_descr);
            $this->y41_obs = ($this->y41_obs == ""?@$GLOBALS["HTTP_POST_VARS"]["y41_obs"]:$this->y41_obs);
            $this->y41_permcalc = ($this->y41_permcalc == "f"?@$GLOBALS["HTTP_POST_VARS"]["y41_permcalc"]:$this->y41_permcalc);
            $this->y41_instit = ($this->y41_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["y41_instit"]:$this->y41_instit);
        } else {
            $this->y41_codtipo = ($this->y41_codtipo == ""?@$GLOBALS["HTTP_POST_VARS"]["y41_codtipo"]:$this->y41_codtipo);
        }
    }
   // funcao para inclusao
    public function incluir($y41_codtipo = null)
    {
        $this->atualizacampos();
        if ($this->y41_descr == null) {
            $this->erro_sql = " Campo Descrição do tipo nao Informado.";
            $this->erro_campo = "y41_descr";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y41_permcalc == null) {
            $this->erro_sql = " Campo Permite Cálculo nao Informado.";
            $this->erro_campo = "y41_permcalc";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y41_instit == null) {
            $this->erro_sql = " Campo Cod. Instituição nao Informado.";
            $this->erro_campo = "y41_instit";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into fiscalizacao.fis_tipoandam(
                                       y41_descr
                                      ,y41_obs
                                      ,y41_permcalc
                                      ,y41_instit
                       )
                values (
                                '$this->y41_descr'
                               ,'$this->y41_obs'
                               ,'$this->y41_permcalc'
                               ,$this->y41_instit
                      ) returning y41_codtipo ";
        $result = db_query($sql);
        $this->y41_codtipo = db_utils::fieldsmemory($result, 0)->y41_codtipo;

        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "tipoandam ($this->y41_codtipo) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "tipoandam já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "tipoandam ($this->y41_codtipo) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->y41_codtipo;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);

        return true;
    }
   // funcao para alteracao
    public function alterar($y41_codtipo = null)
    {
        $this->atualizacampos();
        $sql = " update fiscalizacao.fis_tipoandam set ";
        $virgula = "";
        if (trim($this->y41_descr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y41_descr"])) {
            $sql  .= $virgula." y41_descr = '$this->y41_descr' ";
            $virgula = ",";
            if (trim($this->y41_descr) == null) {
                $this->erro_sql = " Campo Descrição do tipo nao Informado.";
                $this->erro_campo = "y41_descr";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y41_obs)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y41_obs"])) {
            $sql  .= $virgula." y41_obs = '$this->y41_obs' ";
            $virgula = ",";
        }
        if (trim($this->y41_permcalc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y41_permcalc"])) {
            $sql  .= $virgula." y41_permcalc = '$this->y41_permcalc' ";
            $virgula = ",";
            if (trim($this->y41_permcalc) == null) {
                $this->erro_sql = " Campo Permite Cálculo nao Informado.";
                $this->erro_campo = "y41_permcalc";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y41_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y41_instit"])) {
            $sql  .= $virgula." y41_instit = $this->y41_instit ";
            $virgula = ",";
            if (trim($this->y41_instit) == null) {
                $this->erro_sql = " Campo Cod. Instituição nao Informado.";
                $this->erro_campo = "y41_instit";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($y41_codtipo!=null) {
            $sql .= " y41_codtipo = $this->y41_codtipo";
        }

        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "tipoandam nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->y41_codtipo;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "tipoandam nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->y41_codtipo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->y41_codtipo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao para exclusao
    public function excluir($y41_codtipo = null, $dbwhere = null)
    {
        $sql = " delete from fiscalizacao.fis_tipoandam where ";
        $sql2 = "";
        if ($dbwhere==null || $dbwhere =="") {
            if ($y41_codtipo != "") {
                if ($sql2!="") {
                    $sql2 .= " and ";
                }
                $sql2 .= " y41_codtipo = $y41_codtipo ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "tipoandam nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y41_codtipo;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "tipoandam nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y41_codtipo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$y41_codtipo;
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
            $this->erro_sql   = "Record Vazio na Tabela:tipoandam";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
    public function sql_query($y41_codtipo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_tipoandam ";
        $sql .= "      inner join db_config  on  db_config.codigo = fis_tipoandam.y41_instit";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y41_codtipo!=null) {
                $sql2 .= " where fis_tipoandam.y41_codtipo = $y41_codtipo ";
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
    public function sql_query_file($y41_codtipo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_tipoandam ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y41_codtipo!=null) {
                $sql2 .= " where fis_tipoandam.y41_codtipo = $y41_codtipo ";
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
