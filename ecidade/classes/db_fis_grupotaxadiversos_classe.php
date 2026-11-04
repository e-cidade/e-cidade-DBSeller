<?php
//MODULO: fiscal
//CLASSE DA ENTIDADE grupotaxadiversos
class cl_fis_grupotaxadiversos
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
    public $y118_sequencial = 0;
    public $y118_descricao = null;
    public $y118_inflator = null;
    public $y118_procedencia = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 y118_sequencial = int8 = Sequencial
                 y118_descricao = varchar(100) = Descrição
                 y118_inflator = varchar(5) = Código Inflator
                 y118_procedencia = int4 = Procedência
                 ";
    //funcao construtor da classe
    public function __construct()
    {
         //classes dos rotulos dos campos
         $this->rotulo = new rotulo("fis_grupotaxadiversos");
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
            $this->y118_sequencial = ($this->y118_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["y118_sequencial"]:$this->y118_sequencial);
            $this->y118_descricao = ($this->y118_descricao == ""?@$GLOBALS["HTTP_POST_VARS"]["y118_descricao"]:$this->y118_descricao);
            $this->y118_inflator = ($this->y118_inflator == ""?@$GLOBALS["HTTP_POST_VARS"]["y118_inflator"]:$this->y118_inflator);
            $this->y118_procedencia = ($this->y118_procedencia == ""?@$GLOBALS["HTTP_POST_VARS"]["y118_procedencia"]:$this->y118_procedencia);
        } else {
            $this->y118_sequencial = ($this->y118_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["y118_sequencial"]:$this->y118_sequencial);
        }
    }
   // funcao para Inclusão
    public function incluir($y118_sequencial = null)
    {
        $this->atualizacampos();
        if ($this->y118_descricao == null) {
            $this->erro_sql = " Campo Descrição não informado.";
            $this->erro_campo = "y118_descricao";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y118_inflator == null) {
            $this->erro_sql = " Campo Código Inflator não informado.";
            $this->erro_campo = "y118_inflator";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y118_procedencia == null) {
            $this->erro_sql = " Campo Procedência não informado.";
            $this->erro_campo = "y118_procedencia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into fiscalizacao.fis_grupotaxadiversos(
                                       y118_descricao
                                      ,y118_inflator
                                      ,y118_procedencia
                       )
                values (
                                '$this->y118_descricao'
                               ,'$this->y118_inflator'
                               ,$this->y118_procedencia
                      ) returning y118_sequencial ";
        $result = db_query($sql);
        $this->y118_sequencial = db_utils::fieldsmemory($result, 0)->y118_sequencial;
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Grupo de Taxas de Diversos ($this->y118_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Grupo de Taxas de Diversos já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "Grupo de Taxas de Diversos ($this->y118_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->y118_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);

        return true;
    }
   // funcao para alteracao
    public function alterar($y118_sequencial = null)
    {
        $this->atualizacampos();
        $sql = " update fiscalizacao.fis_grupotaxadiversos set ";
        $virgula = "";
        if (trim($this->y118_descricao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y118_descricao"])) {
            $sql  .= $virgula." y118_descricao = '$this->y118_descricao' ";
            $virgula = ",";
            if (trim($this->y118_descricao) == null) {
                $this->erro_sql = " Campo Descrição não informado.";
                $this->erro_campo = "y118_descricao";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y118_inflator)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y118_inflator"])) {
            $sql  .= $virgula." y118_inflator = '$this->y118_inflator' ";
            $virgula = ",";
            if (trim($this->y118_inflator) == null) {
                $this->erro_sql = " Campo Código Inflator não informado.";
                $this->erro_campo = "y118_inflator";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y118_procedencia)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y118_procedencia"])) {
            $sql  .= $virgula." y118_procedencia = $this->y118_procedencia ";
            $virgula = ",";
            if (trim($this->y118_procedencia) == null) {
                $this->erro_sql = " Campo Procedência não informado.";
                $this->erro_campo = "y118_procedencia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($y118_sequencial!=null) {
            $sql .= " y118_sequencial = $this->y118_sequencial";
        }


        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Grupo de Taxas de Diversos não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->y118_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Grupo de Taxas de Diversos não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->y118_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->y118_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao para exclusao
    public function excluir($y118_sequencial = null, $dbwhere = null)
    {

        $sql = " delete from fiscalizacao.fis_grupotaxadiversos where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($y118_sequencial)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " y118_sequencial = $y118_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Grupo de Taxas de Diversos não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y118_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Grupo de Taxas de Diversos não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y118_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$y118_sequencial;
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
            $this->erro_sql   = "Record Vazio na Tabela:grupotaxadiversos";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
    // funcao do sql
    public function sql_query($y118_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos}";
        $sql .= "  from fiscalizacao.fis_grupotaxadiversos ";
        $sql .= "      inner join inflan  on  inflan.i01_codigo = fis_grupotaxadiversos.y118_inflator";
        $sql .= "      inner join procdiver  on  procdiver.dv09_procdiver = fis_grupotaxadiversos.y118_procedencia";
        $sql .= "      inner join histcalc  on  histcalc.k01_codigo = procdiver.dv09_hist";
        $sql .= "      inner join tabrec  on  tabrec.k02_codigo = procdiver.dv09_receit";
        $sql .= "      inner join arretipo  on  arretipo.k00_tipo = procdiver.dv09_tipo";
        $sql .= "      inner join db_config  on  db_config.codigo = procdiver.dv09_instit";
        $sql .= "      inner join proced  on  proced.v03_codigo = procdiver.dv09_proced";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($y118_sequencial)) {
                $sql2 .= " where fis_grupotaxadiversos.y118_sequencial = $y118_sequencial ";
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
    public function sql_query_file($y118_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos} ";
        $sql .= "  from fiscalizacao.fis_grupotaxadiversos ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($y118_sequencial)) {
                $sql2 .= " where fis_grupotaxadiversos.y118_sequencial = $y118_sequencial ";
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
