<?php

class cl_fis_lancamento
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
    public $nl01_codlanc = 0;
    public $nl01_data_dia = null;
    public $nl01_data_mes = null;
    public $nl01_data_ano = null;
    public $nl01_data = null;
    public $nl01_hora = 0;
    public $nl01_obs = null;
    public $nl01_setor = 0;
    public $nl01_nome = null;
    public $nl01_dtvenc_dia = null;
    public $nl01_dtvenc_mes = null;
    public $nl01_dtvenc_ano = null;
    public $nl01_dtvenc = null;
    public $nl01_numbloco = null;
    public $nl01_prazorec_dia = null;
    public $nl01_prazorec_mes = null;
    public $nl01_prazorec_ano = null;
    public $nl01_prazorec = null;
    public $nl01_codtipo = 0;
    public $nl01_instit = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 nl01_codlanc = int4 = Código da Notificação de Lançamento 
                 nl01_data = date = Data do Auto de Infração 
                 nl01_hora = bpchar(5) = Hora do Auto 
                 nl01_obs = text = Observações 
                 nl01_setor = int4 = Departamento 
                 nl01_nome = varchar(50) = Nome da Pessoa Autuada 
                 nl01_dtvenc = date = Data de vencimento 
                 nl01_numbloco = varchar(20) = Número do bloco 
                 nl01_prazorec = date = Prazo p/ Recurso 
                 nl01_codtipo = int4 = Tipo de fiscalização 
                 nl01_instit = int4 = Instituição 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("fis_lancamento");
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
            $this->nl01_codlanc = ($this->nl01_codlanc == ""?@$GLOBALS["HTTP_POST_VARS"]["nl01_codlanc"]:$this->nl01_codlanc);
            if ($this->nl01_data == "") {
                $this->nl01_data_dia = ($this->nl01_data_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["nl01_data_dia"]:$this->nl01_data_dia);
                $this->nl01_data_mes = ($this->nl01_data_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["nl01_data_mes"]:$this->nl01_data_mes);
                $this->nl01_data_ano = ($this->nl01_data_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["nl01_data_ano"]:$this->nl01_data_ano);
                if ($this->nl01_data_dia != "") {
                    $this->nl01_data = $this->nl01_data_ano."-".$this->nl01_data_mes."-".$this->nl01_data_dia;
                }
            }
            $this->nl01_hora = ($this->nl01_hora == ""?@$GLOBALS["HTTP_POST_VARS"]["nl01_hora"]:$this->nl01_hora);
            $this->nl01_obs = ($this->nl01_obs == ""?@$GLOBALS["HTTP_POST_VARS"]["nl01_obs"]:$this->nl01_obs);
            $this->nl01_setor = ($this->nl01_setor == ""?@$GLOBALS["HTTP_POST_VARS"]["nl01_setor"]:$this->nl01_setor);
            $this->nl01_nome = ($this->nl01_nome == ""?@$GLOBALS["HTTP_POST_VARS"]["nl01_nome"]:$this->nl01_nome);
            if ($this->nl01_dtvenc == "") {
                $this->nl01_dtvenc_dia = ($this->nl01_dtvenc_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["nl01_dtvenc_dia"]:$this->nl01_dtvenc_dia);
                $this->nl01_dtvenc_mes = ($this->nl01_dtvenc_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["nl01_dtvenc_mes"]:$this->nl01_dtvenc_mes);
                $this->nl01_dtvenc_ano = ($this->nl01_dtvenc_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["nl01_dtvenc_ano"]:$this->nl01_dtvenc_ano);
                if ($this->nl01_dtvenc_dia != "") {
                    $this->nl01_dtvenc = $this->nl01_dtvenc_ano."-".$this->nl01_dtvenc_mes."-".$this->nl01_dtvenc_dia;
                }
            }
            $this->nl01_numbloco = ($this->nl01_numbloco == ""?@$GLOBALS["HTTP_POST_VARS"]["nl01_numbloco"]:$this->nl01_numbloco);
            if ($this->nl01_prazorec == "") {
                $this->nl01_prazorec_dia = ($this->nl01_prazorec_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["nl01_prazorec_dia"]:$this->nl01_prazorec_dia);
                $this->nl01_prazorec_mes = ($this->nl01_prazorec_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["nl01_prazorec_mes"]:$this->nl01_prazorec_mes);
                $this->nl01_prazorec_ano = ($this->nl01_prazorec_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["nl01_prazorec_ano"]:$this->nl01_prazorec_ano);
                if ($this->nl01_prazorec_dia != "") {
                    $this->nl01_prazorec = $this->nl01_prazorec_ano."-".$this->nl01_prazorec_mes."-".$this->nl01_prazorec_dia;
                }
            }
            $this->nl01_codtipo = ($this->nl01_codtipo == ""?@$GLOBALS["HTTP_POST_VARS"]["nl01_codtipo"]:$this->nl01_codtipo);
            $this->nl01_instit = ($this->nl01_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["nl01_instit"]:$this->nl01_instit);
        } else {
            $this->nl01_codlanc = ($this->nl01_codlanc == ""?@$GLOBALS["HTTP_POST_VARS"]["nl01_codlanc"]:$this->nl01_codlanc);
        }
    }

    public function incluir($nl01_codlanc = null)
    {
        $this->atualizacampos();
        if ($this->nl01_setor == null) {
            $this->erro_sql = " Campo Departamento não informado.";
            $this->erro_campo = "nl01_setor";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->nl01_nome == null) {
            $this->erro_sql = " Campo Nome da Pessoa Autuada não informado.";
            $this->erro_campo = "nl01_nome";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->nl01_codtipo == null) {
            $this->erro_sql = " Campo Tipo de fiscalização não informado.";
            $this->erro_campo = "nl01_codtipo";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->nl01_instit == null) {
            $this->erro_sql = " Campo Instituição não informado.";
            $this->erro_campo = "nl01_instit";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into fis_lancamento(
                                       nl01_data 
                                      ,nl01_hora 
                                      ,nl01_obs 
                                      ,nl01_setor 
                                      ,nl01_nome 
                                      ,nl01_dtvenc 
                                      ,nl01_numbloco 
                                      ,nl01_prazorec 
                                      ,nl01_codtipo 
                                      ,nl01_instit 
                       )
                values (
                                ".($this->nl01_data == "null" || $this->nl01_data == ""?"null":"'".$this->nl01_data."'")." 
                               ,$this->nl01_hora 
                               ,'$this->nl01_obs' 
                               ,$this->nl01_setor 
                               ,'$this->nl01_nome' 
                               ,".($this->nl01_dtvenc == "null" || $this->nl01_dtvenc == ""?"null":"'".$this->nl01_dtvenc."'")." 
                               ,'$this->nl01_numbloco' 
                               ,".($this->nl01_prazorec == "null" || $this->nl01_prazorec == ""?"null":"'".$this->nl01_prazorec."'")." 
                               ,$this->nl01_codtipo 
                               ,$this->nl01_instit 
                      ) returning nl01_codlanc ";
        $result = db_query($sql);
        $this->nl01_codlanc = db_utils::fieldsmemory($result, 0)->nl01_codlanc;

        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "lancamento ($this->nl01_codlanc) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "lancamento já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "lancamento ($this->nl01_codlanc) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->nl01_codlanc;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        return true;
    }

    public function alterar($nl01_codlanc = null)
    {
        $this->atualizacampos();
        $sql = " update fis_lancamento set ";
        $virgula = "";
        if (trim($this->nl01_data)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl01_data_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["nl01_data_dia"] !="")) {
            $sql  .= $virgula." nl01_data = '$this->nl01_data' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["nl01_data_dia"])) {
                $sql  .= $virgula." nl01_data = null ";
                $virgula = ",";
            }
        }
        if (trim($this->nl01_hora)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl01_hora"])) {
            $sql  .= $virgula." nl01_hora = $this->nl01_hora ";
            $virgula = ",";
        }
        if (trim($this->nl01_obs)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl01_obs"])) {
            $sql  .= $virgula." nl01_obs = '$this->nl01_obs' ";
            $virgula = ",";
        }
        if (trim($this->nl01_setor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl01_setor"])) {
            $sql  .= $virgula." nl01_setor = $this->nl01_setor ";
            $virgula = ",";
            if (trim($this->nl01_setor) == null) {
                $this->erro_sql = " Campo Departamento não informado.";
                $this->erro_campo = "nl01_setor";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->nl01_nome)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl01_nome"])) {
            $sql  .= $virgula." nl01_nome = '$this->nl01_nome' ";
            $virgula = ",";
            if (trim($this->nl01_nome) == null) {
                $this->erro_sql = " Campo Nome da Pessoa Autuada não informado.";
                $this->erro_campo = "nl01_nome";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->nl01_dtvenc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl01_dtvenc_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["nl01_dtvenc_dia"] !="")) {
            $sql  .= $virgula." nl01_dtvenc = '$this->nl01_dtvenc' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["nl01_dtvenc_dia"])) {
                $sql  .= $virgula." nl01_dtvenc = null ";
                $virgula = ",";
            }
        }
        if (trim($this->nl01_numbloco)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl01_numbloco"])) {
            $sql  .= $virgula." nl01_numbloco = '$this->nl01_numbloco' ";
            $virgula = ",";
        }
        if (trim($this->nl01_prazorec)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl01_prazorec_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["nl01_prazorec_dia"] !="")) {
            $sql  .= $virgula." nl01_prazorec = '$this->nl01_prazorec' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["nl01_prazorec_dia"])) {
                $sql  .= $virgula." nl01_prazorec = null ";
                $virgula = ",";
            }
        }
        if (trim($this->nl01_codtipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl01_codtipo"])) {
            $sql  .= $virgula." nl01_codtipo = $this->nl01_codtipo ";
            $virgula = ",";
            if (trim($this->nl01_codtipo) == null) {
                $this->erro_sql = " Campo Tipo de fiscalização não informado.";
                $this->erro_campo = "nl01_codtipo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->nl01_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl01_instit"])) {
            $sql  .= $virgula." nl01_instit = $this->nl01_instit ";
            $virgula = ",";
            if (trim($this->nl01_instit) == null) {
                $this->erro_sql = " Campo Instituição não informado.";
                $this->erro_campo = "nl01_instit";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($nl01_codlanc!=null) {
            $sql .= " nl01_codlanc = $this->nl01_codlanc";
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "lancamento não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->nl01_codlanc;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "lancamento não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->nl01_codlanc;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$this->nl01_codlanc;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($nl01_codlanc = null, $dbwhere = null)
    {
        $sql = " delete from fis_lancamento where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($nl01_codlanc)) {
                if (!empty($sql2)) {
                     $sql2 .= " and ";
                }
                $sql2 .= " nl01_codlanc = $nl01_codlanc ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "lancamento não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$nl01_codlanc;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "lancamento não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$nl01_codlanc;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$nl01_codlanc;
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
            $this->erro_sql   = "Record Vazio na Tabela:fis_lancamento";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($nl01_codlanc = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos}";
        $sql .= "  from fis_lancamento ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($nl01_codlanc)) {
                $sql2 .= " where fis_lancamento.nl01_codlanc = $nl01_codlanc ";
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

    public function sql_query_file($nl01_codlanc = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos} ";
        $sql .= "  from fis_lancamento ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($nl01_codlanc)) {
                $sql2 .= " where fis_lancamento.nl01_codlanc = $nl01_codlanc ";
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
