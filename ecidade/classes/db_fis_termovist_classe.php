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
//CLASSE DA ENTIDADE termovist
class cl_fis_termovist
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
    public $y91_termovist = 0;
    public $y91_inscr = 0;
    public $y91_datatermo_dia = null;
    public $y91_datatermo_mes = null;
    public $y91_datatermo_ano = null;
    public $y91_datatermo = null;
    public $y91_exerc = 0;
    public $y91_codigo = 0;
    public $y91_tipo = null;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 y91_termovist = int4 = Código do termo
                 y91_inscr = int4 = Inscrição no ISSQN
                 y91_datatermo = date = Data do termo
                 y91_exerc = int4 = Exercicio
                 y91_codigo = int4 = cód. Logradouro
                 y91_tipo = varchar(3) = Origem do termo
                 ";
    //funcao construtor da classe
    public function __construct()
    {
         //classes dos rotulos dos campos
         $this->rotulo = new rotulo("fis_termovist");
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
            $this->y91_termovist = ($this->y91_termovist == ""?@$GLOBALS["HTTP_POST_VARS"]["y91_termovist"]:$this->y91_termovist);
            $this->y91_inscr = ($this->y91_inscr == ""?@$GLOBALS["HTTP_POST_VARS"]["y91_inscr"]:$this->y91_inscr);
            if ($this->y91_datatermo == "") {
                $this->y91_datatermo_dia = ($this->y91_datatermo_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y91_datatermo_dia"]:$this->y91_datatermo_dia);
                $this->y91_datatermo_mes = ($this->y91_datatermo_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y91_datatermo_mes"]:$this->y91_datatermo_mes);
                $this->y91_datatermo_ano = ($this->y91_datatermo_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y91_datatermo_ano"]:$this->y91_datatermo_ano);
                if ($this->y91_datatermo_dia != "") {
                    $this->y91_datatermo = $this->y91_datatermo_ano."-".$this->y91_datatermo_mes."-".$this->y91_datatermo_dia;
                }
            }
            $this->y91_exerc = ($this->y91_exerc == ""?@$GLOBALS["HTTP_POST_VARS"]["y91_exerc"]:$this->y91_exerc);
            $this->y91_codigo = ($this->y91_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["y91_codigo"]:$this->y91_codigo);
            $this->y91_tipo = ($this->y91_tipo == ""?@$GLOBALS["HTTP_POST_VARS"]["y91_tipo"]:$this->y91_tipo);
        } else {
            $this->y91_termovist = ($this->y91_termovist == ""?@$GLOBALS["HTTP_POST_VARS"]["y91_termovist"]:$this->y91_termovist);
        }
    }
   // funcao para inclusao
    public function incluir($y91_termovist = null)
    {
        $this->atualizacampos();
        if ($this->y91_inscr == null) {
            $this->erro_sql = " Campo Inscrição no ISSQN nao Informado.";
            $this->erro_campo = "y91_inscr";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y91_datatermo == null) {
            $this->erro_sql = " Campo Data do termo nao Informado.";
            $this->erro_campo = "y91_datatermo_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y91_exerc == null) {
            $this->erro_sql = " Campo Exercicio nao Informado.";
            $this->erro_campo = "y91_exerc";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y91_codigo == null) {
            $this->erro_sql = " Campo cód. Logradouro nao Informado.";
            $this->erro_campo = "y91_codigo";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y91_tipo == null) {
            $this->erro_sql = " Campo Origem do termo nao Informado.";
            $this->erro_campo = "y91_tipo";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into fiscalizacao.fis_termovist(
                                       y91_inscr
                                      ,y91_datatermo
                                      ,y91_exerc
                                      ,y91_codigo
                                      ,y91_tipo
                       )
                values (
                                $this->y91_inscr
                               ,".($this->y91_datatermo == "null" || $this->y91_datatermo == ""?"null":"'".$this->y91_datatermo."'")."
                               ,$this->y91_exerc
                               ,$this->y91_codigo
                               ,'$this->y91_tipo'
                      ) returning y91_termovist ";
        $result = db_query($sql);
        $this->y91_termovist = db_utils::fieldsmemory($result, 0)->y91_termovist;

        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Termo de vistorias ($this->y91_termovist) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Termo de vistorias já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "Termo de vistorias ($this->y91_termovist) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->y91_termovist;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);

        return true;
    }
   // funcao para alteracao
    public function alterar($y91_termovist = null)
    {
         $this->atualizacampos();
         $sql = " update fiscalizacao.fis_termovist set ";
         $virgula = "";
        if (trim($this->y91_inscr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y91_inscr"])) {
            $sql  .= $virgula." y91_inscr = $this->y91_inscr ";
            $virgula = ",";
            if (trim($this->y91_inscr) == null) {
                $this->erro_sql = " Campo Inscrição no ISSQN nao Informado.";
                $this->erro_campo = "y91_inscr";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y91_datatermo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y91_datatermo_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y91_datatermo_dia"] !="")) {
            $sql  .= $virgula." y91_datatermo = '$this->y91_datatermo' ";
            $virgula = ",";
            if (trim($this->y91_datatermo) == null) {
                $this->erro_sql = " Campo Data do termo nao Informado.";
                $this->erro_campo = "y91_datatermo_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y91_datatermo_dia"])) {
                $sql  .= $virgula." y91_datatermo = null ";
                $virgula = ",";
                if (trim($this->y91_datatermo) == null) {
                    $this->erro_sql = " Campo Data do termo nao Informado.";
                    $this->erro_campo = "y91_datatermo_dia";
                    $this->erro_banco = "";
                    $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        if (trim($this->y91_exerc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y91_exerc"])) {
            $sql  .= $virgula." y91_exerc = $this->y91_exerc ";
            $virgula = ",";
            if (trim($this->y91_exerc) == null) {
                $this->erro_sql = " Campo Exercicio nao Informado.";
                $this->erro_campo = "y91_exerc";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y91_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y91_codigo"])) {
            $sql  .= $virgula." y91_codigo = $this->y91_codigo ";
            $virgula = ",";
            if (trim($this->y91_codigo) == null) {
                $this->erro_sql = " Campo cód. Logradouro nao Informado.";
                $this->erro_campo = "y91_codigo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y91_tipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y91_tipo"])) {
            $sql  .= $virgula." y91_tipo = '$this->y91_tipo' ";
            $virgula = ",";
            if (trim($this->y91_tipo) == null) {
                $this->erro_sql = " Campo Origem do termo nao Informado.";
                $this->erro_campo = "y91_tipo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($y91_termovist!=null) {
            $sql .= " y91_termovist = $this->y91_termovist";
        }

        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Termo de vistorias nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->y91_termovist;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "Termo de vistorias nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->y91_termovist;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->y91_termovist;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao para exclusao
    public function excluir($y91_termovist = null, $dbwhere = null)
    {
         $sql = " delete from fiscalizacao.fis_termovist where ";
         $sql2 = "";
        if ($dbwhere==null || $dbwhere =="") {
            if ($y91_termovist != "") {
                if ($sql2!="") {
                    $sql2 .= " and ";
                }
                $sql2 .= " y91_termovist = $y91_termovist ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Termo de vistorias nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y91_termovist;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "Termo de vistorias nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y91_termovist;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$y91_termovist;
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
            $this->erro_sql   = "Record Vazio na Tabela:termovist";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
    public function sql_query($y91_termovist = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_termovist ";
        $sql .= "      inner join issbase  on  issbase.q02_inscr = fis_termovist.y91_inscr";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = issbase.q02_numcgm";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y91_termovist!=null) {
                $sql2 .= " where fis_termovist.y91_termovist = $y91_termovist ";
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
    public function sql_query_file($y91_termovist = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_termovist ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y91_termovist!=null) {
                $sql2 .= " where fis_termovist.y91_termovist = $y91_termovist ";
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
