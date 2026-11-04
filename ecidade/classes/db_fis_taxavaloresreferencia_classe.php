<?php
//MODULO: fiscal
//CLASSE DA ENTIDADE taxavaloresreferencia
class cl_fis_taxavaloresreferencia
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
    public $y121_sequencial = 0;
    public $y121_descricao = null;
    public $y121_valor = 0;
    public $y121_data_base_dia = null;
    public $y121_data_base_mes = null;
    public $y121_data_base_ano = null;
    public $y121_data_base = null;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 y121_sequencial = int4 = Sequencial
                 y121_descricao = varchar(100) = Descrição
                 y121_valor = float8 = Valor Base
                 y121_data_base = date = Data Base
                 ";
    //funcao construtor da classe
    public function __construct()
    {
         //classes dos rotulos dos campos
         $this->rotulo = new rotulo("fis_taxavaloresreferencia");
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
            $this->y121_sequencial = ($this->y121_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["y121_sequencial"]:$this->y121_sequencial);
            $this->y121_descricao = ($this->y121_descricao == ""?@$GLOBALS["HTTP_POST_VARS"]["y121_descricao"]:$this->y121_descricao);
            $this->y121_valor = ($this->y121_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["y121_valor"]:$this->y121_valor);
            if ($this->y121_data_base == "") {
                $this->y121_data_base_dia = ($this->y121_data_base_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y121_data_base_dia"]:$this->y121_data_base_dia);
                $this->y121_data_base_mes = ($this->y121_data_base_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y121_data_base_mes"]:$this->y121_data_base_mes);
                $this->y121_data_base_ano = ($this->y121_data_base_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y121_data_base_ano"]:$this->y121_data_base_ano);
                if ($this->y121_data_base_dia != "") {
                    $this->y121_data_base = $this->y121_data_base_ano."-".$this->y121_data_base_mes."-".$this->y121_data_base_dia;
                }
            }
        } else {
            $this->y121_sequencial = ($this->y121_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["y121_sequencial"]:$this->y121_sequencial);
        }
    }
   // funcao para Inclusão
    public function incluir($y121_sequencial = null)
    {
        $this->atualizacampos();
        if ($this->y121_descricao == null) {
            $this->erro_sql = " Campo Descrição não informado.";
            $this->erro_campo = "y121_descricao";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y121_valor == null) {
            $this->erro_sql = " Campo Valor Base não informado.";
            $this->erro_campo = "y121_valor";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y121_data_base == null) {
            $this->erro_sql = " Campo Data Base não informado.";
            $this->erro_campo = "y121_data_base_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into fiscalizacao.fis_taxavaloresreferencia(
                                       y121_descricao
                                      ,y121_valor
                                      ,y121_data_base
                       )
                values (
                                '$this->y121_descricao'
                               ,$this->y121_valor
                               ,".($this->y121_data_base == "null" || $this->y121_data_base == ""?"null":"'".$this->y121_data_base."'")."
                      ) returning y121_sequencial ";
        $result = db_query($sql);
        $this->y121_sequencial = db_utils::fieldsmemory($result, 0)->y121_sequencial;

        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Valores de Referência das taxas ($this->y121_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Valores de Referência das taxas já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "Valores de Referência das taxas ($this->y121_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->y121_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);

        return true;
    }
   // funcao para alteracao
    public function alterar($y121_sequencial = null)
    {
        $this->atualizacampos();
        $sql = " update fiscalizacao.fis_taxavaloresreferencia set ";
        $virgula = "";
        if (trim($this->y121_descricao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y121_descricao"])) {
            $sql  .= $virgula." y121_descricao = '$this->y121_descricao' ";
            $virgula = ",";
            if (trim($this->y121_descricao) == null) {
                $this->erro_sql = " Campo Descrição não informado.";
                $this->erro_campo = "y121_descricao";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y121_valor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y121_valor"])) {
            $sql  .= $virgula." y121_valor = $this->y121_valor ";
            $virgula = ",";
            if (trim($this->y121_valor) == null) {
                $this->erro_sql = " Campo Valor Base não informado.";
                $this->erro_campo = "y121_valor";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y121_data_base)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y121_data_base_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y121_data_base_dia"] !="")) {
            $sql  .= $virgula." y121_data_base = '$this->y121_data_base' ";
            $virgula = ",";
            if (trim($this->y121_data_base) == null) {
                $this->erro_sql = " Campo Data Base não informado.";
                $this->erro_campo = "y121_data_base_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y121_data_base_dia"])) {
                $sql  .= $virgula." y121_data_base = null ";
                $virgula = ",";
                if (trim($this->y121_data_base) == null) {
                    $this->erro_sql = " Campo Data Base não informado.";
                    $this->erro_campo = "y121_data_base_dia";
                    $this->erro_banco = "";
                    $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        $sql .= " where ";
        if ($y121_sequencial!=null) {
            $sql .= " y121_sequencial = $this->y121_sequencial";
        }

        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Valores de Referência das taxas não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->y121_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Valores de Referência das taxas não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->y121_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->y121_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao para exclusao
    public function excluir($y121_sequencial = null, $dbwhere = null)
    {
        $sql = " delete from fiscalizacao.fis_taxavaloresreferencia where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($y121_sequencial)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " y121_sequencial = $y121_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Valores de Referência das taxas não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y121_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Valores de Referência das taxas não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y121_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$y121_sequencial;
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
            $this->erro_sql   = "Record Vazio na Tabela:taxavaloresreferencia";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
   // funcao do sql
    public function sql_query($y121_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos}";
        $sql .= "  from fiscalizacao.fis_taxavaloresreferencia ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($y121_sequencial)) {
                $sql2 .= " where fis_taxavaloresreferencia.y121_sequencial = $y121_sequencial ";
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
    public function sql_query_file($y121_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos} ";
        $sql .= "  from fiscalizacao.fis_taxavaloresreferencia ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($y121_sequencial)) {
                $sql2 .= " where fis_taxavaloresreferencia.y121_sequencial = $y121_sequencial ";
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
