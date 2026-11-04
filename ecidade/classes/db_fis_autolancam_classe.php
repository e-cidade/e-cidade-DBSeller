<?php

class cl_fis_autolancam
{
    // cria variaveis de erro
    public $rotulo = null;
    public $query_sql = null;
    public $numrows = 0;
    public $numrows_incluir = 0;
    public $numrows_alterar = 0;
    public $numrows_excluir = 0;
    public $erro_status = null;
    public $erro_sql = null;
    public $erro_banco = null;
    public $erro_msg = null;
    public $erro_campo = null;
    public $pagina_retorno = null;
    /* Variáveis do Arquivo */
    public $y88_codtermo = 0;
    public $y88_codlev = 0;
    public $y88_data_dia = null;
    public $y88_data_mes = null;
    public $y88_data_ano = null;
    public $y88_data = null;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 y88_codtermo = int4 = Código Termo 
                 y88_codlev = int4 = Levantamento 
                 y88_data = date = Data do auto de lançamento 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("fis_autolancam");
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
    }

    public function erro($mostra, $retorna)
    {
        if (($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )) {
            echo "<script>alert(\"".$this->erro_msg."\")</script>";
            if ($retorna==true) {
                echo "<script>location.href='".$this->pagina_retorno."'</script>";
            }
        }
    }

    public function atualizacampos($exclusao = false)
    {
        if (!$exclusao) {
            $this->y88_codtermo = ($this->y88_codtermo == ""?@$GLOBALS["HTTP_POST_VARS"]["y88_codtermo"]:$this->y88_codtermo);
            $this->y88_codlev = ($this->y88_codlev == ""?@$GLOBALS["HTTP_POST_VARS"]["y88_codlev"]:$this->y88_codlev);
            if ($this->y88_data == "") {
                $this->y88_data_dia = ($this->y88_data_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y88_data_dia"]:$this->y88_data_dia);
                $this->y88_data_mes = ($this->y88_data_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y88_data_mes"]:$this->y88_data_mes);
                $this->y88_data_ano = ($this->y88_data_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y88_data_ano"]:$this->y88_data_ano);
                if ($this->y88_data_dia != "") {
                    $this->y88_data = $this->y88_data_ano."-".$this->y88_data_mes."-".$this->y88_data_dia;
                }
            }
        } else {
            $this->y88_codtermo = ($this->y88_codtermo == ""?@$GLOBALS["HTTP_POST_VARS"]["y88_codtermo"]:$this->y88_codtermo);
        }
    }

    public function incluir($y88_codtermo = null)
    {
        $this->atualizacampos();
        if ($this->y88_codlev == null) {
            $this->erro_sql = " Campo Levantamento não informado.";
            $this->erro_campo = "y88_codlev";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y88_data == null) {
            $this->y88_data = "null";
        }
        $sql = "insert into fis_autolancam(
                                       y88_codlev 
                                      ,y88_data 
                       )
                values (
                                $this->y88_codlev 
                               ,".($this->y88_data == "null" || $this->y88_data == ""?"null":"'".$this->y88_data."'")." 
                      ) returning y88_codtermo ";
        $result = db_query($sql);
        $this->y88_codtermo = db_utils::fieldsmemory($result, 0)->y88_codtermo;
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = " ($this->y88_codtermo) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = " já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = " ($this->y88_codtermo) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->y88_codtermo;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        return true;
    }

    public function alterar($y88_codtermo = null)
    {
        $this->atualizacampos();
        $sql = " update fis_autolancam set ";
        $virgula = "";
        if (trim($this->y88_codlev)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y88_codlev"])) {
            $sql  .= $virgula." y88_codlev = $this->y88_codlev ";
            $virgula = ",";
            if (trim($this->y88_codlev) == null) {
                $this->erro_sql = " Campo Levantamento não informado.";
                $this->erro_campo = "y88_codlev";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y88_data)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y88_data_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y88_data_dia"] !="")) {
            $sql  .= $virgula." y88_data = '$this->y88_data' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y88_data_dia"])) {
                $sql  .= $virgula." y88_data = null ";
                $virgula = ",";
            }
        }
        $sql .= " where ";
        if ($y88_codtermo!=null) {
            $sql .= " y88_codtermo = $this->y88_codtermo";
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = " não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->y88_codtermo;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = " não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->y88_codtermo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$this->y88_codtermo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($y88_codtermo = null, $dbwhere = null)
    {
        $sql = " delete from fis_autolancam where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($y88_codtermo)) {
                if (!empty($sql2)) {
                     $sql2 .= " and ";
                }
                $sql2 .= " y88_codtermo = $y88_codtermo ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = " não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y88_codtermo;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = " não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y88_codtermo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$y88_codtermo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = pg_affected_rows($result);
                return true;
            }
        }
    }

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
            $this->erro_sql   = "Record Vazio na Tabela:fis_autolancam";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($y88_codtermo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos}";
        $sql .= "  from fis_autolancam ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($y88_codtermo)) {
                $sql2 .= " where fis_autolancam.y88_codtermo = $y88_codtermo ";
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

    public function sql_query_file($y88_codtermo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos} ";
        $sql .= "  from fis_autolancam ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($y88_codtermo)) {
                $sql2 .= " where fis_autolancam.y88_codtermo = $y88_codtermo ";
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
