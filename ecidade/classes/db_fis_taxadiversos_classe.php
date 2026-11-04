<?php
//MODULO: fiscal
//CLASSE DA ENTIDADE taxadiversos
class cl_fis_taxadiversos
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
    public $y119_sequencial = 0;
    public $y119_grupotaxadiversos = 0;
    public $y119_natureza = null;
    public $y119_formula = 0;
    public $y119_unidade = null;
    public $y119_tipo_periodo = null;
    public $y119_tipo_calculo = null;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 y119_sequencial = int4 = Sequencial
                 y119_grupotaxadiversos = int4 = Grupo
                 y119_natureza = text = Natureza
                 y119_formula = int4 = Fórmula
                 y119_unidade = varchar(50) = Unidade
                 y119_tipo_periodo = char(1) = Tipo de Período
                 y119_tipo_calculo = char(1) = Tipo de Cálculo
                 ";
   //funcao construtor da classe
    public function __construct()
    {
         //classes dos rotulos dos campos
         $this->rotulo = new rotulo("fis_taxadiversos");
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
            $this->y119_sequencial = ($this->y119_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["y119_sequencial"]:$this->y119_sequencial);
            $this->y119_grupotaxadiversos = ($this->y119_grupotaxadiversos == ""?@$GLOBALS["HTTP_POST_VARS"]["y119_grupotaxadiversos"]:$this->y119_grupotaxadiversos);
            $this->y119_natureza = ($this->y119_natureza == ""?@$GLOBALS["HTTP_POST_VARS"]["y119_natureza"]:$this->y119_natureza);
            $this->y119_formula = ($this->y119_formula == ""?@$GLOBALS["HTTP_POST_VARS"]["y119_formula"]:$this->y119_formula);
            $this->y119_unidade = ($this->y119_unidade == ""?@$GLOBALS["HTTP_POST_VARS"]["y119_unidade"]:$this->y119_unidade);
            $this->y119_tipo_periodo = ($this->y119_tipo_periodo == ""?@$GLOBALS["HTTP_POST_VARS"]["y119_tipo_periodo"]:$this->y119_tipo_periodo);
            $this->y119_tipo_calculo = ($this->y119_tipo_calculo == ""?@$GLOBALS["HTTP_POST_VARS"]["y119_tipo_calculo"]:$this->y119_tipo_calculo);
        } else {
            $this->y119_sequencial = ($this->y119_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["y119_sequencial"]:$this->y119_sequencial);
        }
    }
   // funcao para Inclusão
    public function incluir($y119_sequencial = null)
    {
          $this->atualizacampos();
        if ($this->y119_grupotaxadiversos == null) {
            $this->erro_sql = " Campo Grupo não informado.";
            $this->erro_campo = "y119_grupotaxadiversos";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y119_natureza == null) {
            $this->erro_sql = " Campo Natureza não informado.";
            $this->erro_campo = "y119_natureza";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y119_formula == null) {
            $this->erro_sql = " Campo Fórmula não informado.";
            $this->erro_campo = "y119_formula";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y119_unidade == null) {
            $this->erro_sql = " Campo Unidade não informado.";
            $this->erro_campo = "y119_unidade";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y119_tipo_periodo == null) {
            $this->erro_sql = " Campo Tipo de Período não informado.";
            $this->erro_campo = "y119_tipo_periodo";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y119_tipo_calculo == null) {
            $this->erro_sql = " Campo Tipo de Cálculo não informado.";
            $this->erro_campo = "y119_tipo_calculo";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into fiscalizacao.fis_taxadiversos(
                                       y119_grupotaxadiversos
                                      ,y119_natureza
                                      ,y119_formula
                                      ,y119_unidade
                                      ,y119_tipo_periodo
                                      ,y119_tipo_calculo
                       )
                values (
                                $this->y119_grupotaxadiversos
                               ,'$this->y119_natureza'
                               ,$this->y119_formula
                               ,'$this->y119_unidade'
                               ,'$this->y119_tipo_periodo'
                               ,'$this->y119_tipo_calculo'
                      ) returning y119_sequencial ";
        $result = db_query($sql);
        $this->y119_sequencial = db_utils::fieldsmemory($result, 0)->y119_sequencial;

        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Taxas Diversas ($this->y119_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Taxas Diversas já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "Taxas Diversas ($this->y119_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->y119_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);


        return true;
    }
   // funcao para alteracao
    public function alterar($y119_sequencial = null)
    {
        $this->atualizacampos();
        $sql = " update fiscalizacao.fis_taxadiversos set ";
        $virgula = "";
        if (trim($this->y119_grupotaxadiversos)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y119_grupotaxadiversos"])) {
            $sql  .= $virgula." y119_grupotaxadiversos = $this->y119_grupotaxadiversos ";
            $virgula = ",";
            if (trim($this->y119_grupotaxadiversos) == null) {
                $this->erro_sql = " Campo Grupo não informado.";
                $this->erro_campo = "y119_grupotaxadiversos";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y119_natureza)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y119_natureza"])) {
            $sql  .= $virgula." y119_natureza = '$this->y119_natureza' ";
            $virgula = ",";
            if (trim($this->y119_natureza) == null) {
                $this->erro_sql = " Campo Natureza não informado.";
                $this->erro_campo = "y119_natureza";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y119_formula)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y119_formula"])) {
            $sql  .= $virgula." y119_formula = $this->y119_formula ";
            $virgula = ",";
            if (trim($this->y119_formula) == null) {
                $this->erro_sql = " Campo Fórmula não informado.";
                $this->erro_campo = "y119_formula";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y119_unidade)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y119_unidade"])) {
            $sql  .= $virgula." y119_unidade = '$this->y119_unidade' ";
            $virgula = ",";
            if (trim($this->y119_unidade) == null) {
                $this->erro_sql = " Campo Unidade não informado.";
                $this->erro_campo = "y119_unidade";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y119_tipo_periodo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y119_tipo_periodo"])) {
            $sql  .= $virgula." y119_tipo_periodo = '$this->y119_tipo_periodo' ";
            $virgula = ",";
            if (trim($this->y119_tipo_periodo) == null) {
                $this->erro_sql = " Campo Tipo de Período não informado.";
                $this->erro_campo = "y119_tipo_periodo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y119_tipo_calculo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y119_tipo_calculo"])) {
            $sql  .= $virgula." y119_tipo_calculo = '$this->y119_tipo_calculo' ";
            $virgula = ",";
            if (trim($this->y119_tipo_calculo) == null) {
                $this->erro_sql = " Campo Tipo de Cálculo não informado.";
                $this->erro_campo = "y119_tipo_calculo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($y119_sequencial!=null) {
            $sql .= " y119_sequencial = $this->y119_sequencial";
        }

        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Taxas Diversas não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->y119_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Taxas Diversas não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->y119_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->y119_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao para exclusao
    public function excluir($y119_sequencial = null, $dbwhere = null)
    {
        $sql = " delete from fiscalizacao.fis_taxadiversos where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($y119_sequencial)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " y119_sequencial = $y119_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Taxas Diversas não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y119_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Taxas Diversas não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y119_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$y119_sequencial;
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
            $this->erro_sql   = "Record Vazio na Tabela:taxadiversos";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
   // funcao do sql
    public function sql_query($y119_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos}";
        $sql .= "  from fiscalizacao.fis_taxadiversos ";
        $sql .= "      inner join db_formulas  on  db_formulas.db148_sequencial = fis_taxadiversos.y119_formula";
        $sql .= "      inner join fiscalizacao.fis_grupotaxadiversos  on  fis_grupotaxadiversos.y118_sequencial = fis_taxadiversos.y119_grupotaxadiversos";
        $sql .= "      inner join inflan  on  inflan.i01_codigo = fis_grupotaxadiversos.y118_inflator";
        $sql .= "      inner join procdiver  on  procdiver.dv09_procdiver = fis_grupotaxadiversos.y118_procedencia";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($y119_sequencial)) {
                $sql2 .= " where fis_taxadiversos.y119_sequencial = $y119_sequencial ";
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
    public function sql_query_file($y119_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos} ";
        $sql .= "  from fiscalizacao.fis_taxadiversos ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($y119_sequencial)) {
                $sql2 .= " where fis_taxadiversos.y119_sequencial = $y119_sequencial ";
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
