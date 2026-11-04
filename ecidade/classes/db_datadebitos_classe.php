<?php

class cl_datadebitos
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
    public $k115_sequencial = 0;
    public $k115_data_dia = null;
    public $k115_data_mes = null;
    public $k115_data_ano = null;
    public $k115_data = null;
    public $k115_instit = 0;
    public $k115_completa = 'f';
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 k115_sequencial = int4 = Sequencial 
                 k115_data = date = Data 
                 k115_instit = int4 = Instituição
                 k115_completa = bool = Tabela completa 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("datadebitos");
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
    }

    public function erro($mostra, $retorna)
    {
        if (($this->erro_status == "0") || ($mostra && $this->erro_status != null )) {
            echo "<script>alert(\"".$this->erro_msg."\")</script>";
            if ($retorna) {
                echo "<script>location.href='".$this->pagina_retorno."'</script>";
            }
        }
    }

    public function atualizacampos($exclusao = false)
    {
        if (!$exclusao) {
            $this->k115_sequencial = ($this->k115_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["k115_sequencial"]:$this->k115_sequencial);
            if ($this->k115_data == "") {
                $this->k115_data_dia = ($this->k115_data_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["k115_data_dia"]:$this->k115_data_dia);
                $this->k115_data_mes = ($this->k115_data_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["k115_data_mes"]:$this->k115_data_mes);
                $this->k115_data_ano = ($this->k115_data_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["k115_data_ano"]:$this->k115_data_ano);
                if ($this->k115_data_dia != "") {
                    $this->k115_data = $this->k115_data_ano."-".$this->k115_data_mes."-".$this->k115_data_dia;
                }
            }
            $this->k115_instit = ($this->k115_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["k115_instit"]:$this->k115_instit);
            $this->k115_completa = ($this->k115_completa == "f"?@$GLOBALS["HTTP_POST_VARS"]["k115_completa"]:$this->k115_completa);
        } else {
            $this->k115_sequencial = ($this->k115_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["k115_sequencial"]:$this->k115_sequencial);
        }
    }

    public function incluir($k115_sequencial)
    {
        $this->atualizacampos();
        if ($this->k115_instit == null) {
            $this->erro_sql = " Campo Instituição não informado.";
            $this->erro_campo = "k115_instit";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k115_completa == null) {
            $this->k115_completa = "true";
        }
        $sql = "insert into datadebitos(
                                       k115_data 
                                      ,k115_instit 
                                      ,k115_completa 
                       )
                values (
                                ".($this->k115_data == "null" || $this->k115_data == ""?"null":"'".$this->k115_data."'")." 
                               ,$this->k115_instit 
                               ,'$this->k115_completa' 
                      ) returning k115_sequencial ";
        $result = db_query($sql);
        $this->k115_sequencial = db_utils::fieldsmemory($result, 0)->k115_sequencial;

        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Data Debitos ($this->k115_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Data Debitos já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "Data Debitos ($this->k115_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : ".$this->k115_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        return true;
    }

    public function alterar($k115_sequencial = null)
    {
        $this->atualizacampos();
        $sql = " update datadebitos set ";
        $virgula = "";
        if (trim($this->k115_data)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k115_data_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["k115_data_dia"] !="")) {
            $sql  .= $virgula." k115_data = '$this->k115_data' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["k115_data_dia"])) {
                $sql  .= $virgula." k115_data = null ";
                $virgula = ",";
            }
        }
        if (trim($this->k115_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k115_instit"])) {
            $sql  .= $virgula." k115_instit = $this->k115_instit ";
            $virgula = ",";
            if (trim($this->k115_instit) == null) {
                $this->erro_sql = " Campo Instituição não informado.";
                $this->erro_campo = "k115_instit";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k115_completa)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k115_completa"])) {
            $sql  .= $virgula." k115_completa = '$this->k115_completa' ";
            $virgula = ",";
        }
        $sql .= " where ";
        if ($k115_sequencial!=null) {
            $sql .= " k115_sequencial = $this->k115_sequencial";
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Data Debitos não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->k115_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Data Debitos não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->k115_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$this->k115_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($k115_sequencial = null, $dbwhere = null)
    {
        $sql = " delete from datadebitos where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($k115_sequencial)) {
                if (!empty($sql2)) {
                     $sql2 .= " and ";
                }
                $sql2 .= " k115_sequencial = $k115_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Data Debitos não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$k115_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Data Debitos não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$k115_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$k115_sequencial;
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
            $this->erro_sql   = "Record Vazio na Tabela:datadebitos";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($k115_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos}";
        $sql .= "  from datadebitos ";
        $sql .= "      inner join db_config  on  db_config.codigo = datadebitos.k115_instit";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
        $sql .= "      left  join db_depart  on  db_depart.coddepto = db_config.db21_departamento";
        $sql .= "      inner join db_tipoinstit  on  db_tipoinstit.db21_codtipo = db_config.db21_tipoinstit";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($k115_sequencial)) {
                $sql2 .= " where datadebitos.k115_sequencial = $k115_sequencial ";
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

    public function sql_query_file($k115_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos} ";
        $sql .= "  from datadebitos ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($k115_sequencial)) {
                $sql2 .= " where datadebitos.k115_sequencial = $k115_sequencial ";
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
