<?php

class cl_suspensaoorcamentaria
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
    public $o153_sequencial = 0;
    public $o153_orgao = 0;
    public $o153_unidade = 0;
    public $o153_recurso = 0;
    public $o153_localizadorgastos = 0;
    public $o153_exercicio = 0;
    public $o153_suspenderempenho = 'f';
    public $o153_suspenderliquidacao = 'f';
    public $o153_suspenderpagamento = 'f';
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 o153_sequencial = int4 = Sequencial 
                 o153_orgao = int4 = Orgão 
                 o153_unidade = int4 = Unidade 
                 o153_recurso = int4 = Recurso 
                 o153_localizadorgastos = int4 = Anexo 
                 o153_exercicio = int4 = Exercício 
                 o153_suspenderempenho = bool = Suspender Empenho 
                 o153_suspenderliquidacao = bool = Suspender Empenho 
                 o153_suspenderpagamento = bool = Suspender Pagamento 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("suspensaoorcamentaria");
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
        if ($exclusao==false) {
            $this->o153_sequencial = ($this->o153_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["o153_sequencial"]:$this->o153_sequencial);
            $this->o153_orgao = ($this->o153_orgao == ""?@$GLOBALS["HTTP_POST_VARS"]["o153_orgao"]:$this->o153_orgao);
            $this->o153_unidade = ($this->o153_unidade == ""?@$GLOBALS["HTTP_POST_VARS"]["o153_unidade"]:$this->o153_unidade);
            $this->o153_recurso = ($this->o153_recurso == ""?@$GLOBALS["HTTP_POST_VARS"]["o153_recurso"]:$this->o153_recurso);
            $this->o153_localizadorgastos = ($this->o153_localizadorgastos == ""?@$GLOBALS["HTTP_POST_VARS"]["o153_localizadorgastos"]:$this->o153_localizadorgastos);
            $this->o153_exercicio = ($this->o153_exercicio == ""?@$GLOBALS["HTTP_POST_VARS"]["o153_exercicio"]:$this->o153_exercicio);
            $this->o153_suspenderempenho = ($this->o153_suspenderempenho == "f"?@$GLOBALS["HTTP_POST_VARS"]["o153_suspenderempenho"]:$this->o153_suspenderempenho);
            $this->o153_suspenderliquidacao = ($this->o153_suspenderliquidacao == "f"?@$GLOBALS["HTTP_POST_VARS"]["o153_suspenderliquidacao"]:$this->o153_suspenderliquidacao);
            $this->o153_suspenderpagamento = ($this->o153_suspenderpagamento == "f"?@$GLOBALS["HTTP_POST_VARS"]["o153_suspenderpagamento"]:$this->o153_suspenderpagamento);
        } else {
            $this->o153_sequencial = ($this->o153_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["o153_sequencial"]:$this->o153_sequencial);
        }
    }

    public function incluir($o153_sequencial)
    {
        $this->atualizacampos();
        if ($this->o153_orgao == null) {
            $this->erro_sql = " Campo Orgão não informado.";
            $this->erro_campo = "o153_orgao";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o153_unidade == null) {
            $this->erro_sql = " Campo Unidade não informado.";
            $this->erro_campo = "o153_unidade";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o153_recurso == null) {
            $this->erro_sql = " Campo Recurso não informado.";
            $this->erro_campo = "o153_recurso";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o153_localizadorgastos == null) {
            $this->erro_sql = " Campo Anexo não informado.";
            $this->erro_campo = "o153_localizadorgastos";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o153_exercicio == null) {
            $this->erro_sql = " Campo Exercício não informado.";
            $this->erro_campo = "o153_exercicio";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o153_suspenderempenho == null) {
            $this->erro_sql = " Campo Suspender Empenho não informado.";
            $this->erro_campo = "o153_suspenderempenho";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o153_suspenderliquidacao == null) {
            $this->erro_sql = " Campo Suspender Empenho não informado.";
            $this->erro_campo = "o153_suspenderliquidacao";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o153_suspenderpagamento == null) {
            $this->erro_sql = " Campo Suspender Pagamento não informado.";
            $this->erro_campo = "o153_suspenderpagamento";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($o153_sequencial == "" || $o153_sequencial == null) {
            $result = db_query("select nextval('suspensaoorcamentaria_o153_sequencial_seq')");
            if ($result==false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql   = "Verifique o cadastro da sequencia: suspensaoorcamentaria_o153_sequencial_seq do campo: o153_sequencial";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->o153_sequencial = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from suspensaoorcamentaria_o153_sequencial_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $o153_sequencial)) {
                $this->erro_sql = " Campo o153_sequencial maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->o153_sequencial = $o153_sequencial;
            }
        }
        if (($this->o153_sequencial == null) || ($this->o153_sequencial == "")) {
            $this->erro_sql = " Campo o153_sequencial não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into suspensaoorcamentaria(
                                       o153_sequencial 
                                      ,o153_orgao 
                                      ,o153_unidade 
                                      ,o153_recurso 
                                      ,o153_localizadorgastos 
                                      ,o153_exercicio 
                                      ,o153_suspenderempenho 
                                      ,o153_suspenderliquidacao 
                                      ,o153_suspenderpagamento 
                       )
                values (
                                $this->o153_sequencial 
                               ,$this->o153_orgao 
                               ,$this->o153_unidade 
                               ,$this->o153_recurso 
                               ,$this->o153_localizadorgastos 
                               ,$this->o153_exercicio 
                               ,'$this->o153_suspenderempenho' 
                               ,'$this->o153_suspenderliquidacao' 
                               ,'$this->o153_suspenderpagamento' 
                      )";
        $result = db_query($sql);
        if ($result==false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Suspensão Orçamentária ($this->o153_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Suspensão Orçamentária já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "Suspensão Orçamentária ($this->o153_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->o153_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            $resaco = $this->sql_record($this->sql_query_file($this->o153_sequencial));
            if (($resaco!=false)||($this->numrows!=0)) {
                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                $resac = db_query("insert into db_acountkey values($acount,1015110,'$this->o153_sequencial','I')");
                $resac = db_query("insert into db_acount values($acount,1011089,1015110,'','".AddSlashes(pg_result($resaco, 0, 'o153_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1011089,1015111,'','".AddSlashes(pg_result($resaco, 0, 'o153_orgao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1011089,1015112,'','".AddSlashes(pg_result($resaco, 0, 'o153_unidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1011089,1015114,'','".AddSlashes(pg_result($resaco, 0, 'o153_recurso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1011089,1015113,'','".AddSlashes(pg_result($resaco, 0, 'o153_localizadorgastos'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1011089,1015115,'','".AddSlashes(pg_result($resaco, 0, 'o153_exercicio'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1011089,1015116,'','".AddSlashes(pg_result($resaco, 0, 'o153_suspenderempenho'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1011089,1015117,'','".AddSlashes(pg_result($resaco, 0, 'o153_suspenderliquidacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1011089,1015118,'','".AddSlashes(pg_result($resaco, 0, 'o153_suspenderpagamento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
            }
        }
        return true;
    }

    public function alterar($o153_sequencial = null)
    {
        $this->atualizacampos();
        $sql = " update suspensaoorcamentaria set ";
        $virgula = "";
        if (trim($this->o153_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o153_sequencial"])) {
            $sql  .= $virgula." o153_sequencial = $this->o153_sequencial ";
            $virgula = ",";
            if (trim($this->o153_sequencial) == null) {
                $this->erro_sql = " Campo Sequencial não informado.";
                $this->erro_campo = "o153_sequencial";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o153_orgao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o153_orgao"])) {
            $sql  .= $virgula." o153_orgao = $this->o153_orgao ";
            $virgula = ",";
            if (trim($this->o153_orgao) == null) {
                $this->erro_sql = " Campo Orgão não informado.";
                $this->erro_campo = "o153_orgao";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o153_unidade)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o153_unidade"])) {
            $sql  .= $virgula." o153_unidade = $this->o153_unidade ";
            $virgula = ",";
            if (trim($this->o153_unidade) == null) {
                $this->erro_sql = " Campo Unidade não informado.";
                $this->erro_campo = "o153_unidade";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o153_recurso)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o153_recurso"])) {
            $sql  .= $virgula." o153_recurso = $this->o153_recurso ";
            $virgula = ",";
            if (trim($this->o153_recurso) == null) {
                $this->erro_sql = " Campo Recurso não informado.";
                $this->erro_campo = "o153_recurso";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o153_localizadorgastos)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o153_localizadorgastos"])) {
            $sql  .= $virgula." o153_localizadorgastos = $this->o153_localizadorgastos ";
            $virgula = ",";
            if (trim($this->o153_localizadorgastos) == null) {
                $this->erro_sql = " Campo Anexo não informado.";
                $this->erro_campo = "o153_localizadorgastos";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o153_exercicio)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o153_exercicio"])) {
            $sql  .= $virgula." o153_exercicio = $this->o153_exercicio ";
            $virgula = ",";
            if (trim($this->o153_exercicio) == null) {
                $this->erro_sql = " Campo Exercício não informado.";
                $this->erro_campo = "o153_exercicio";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o153_suspenderempenho)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o153_suspenderempenho"])) {
            $sql  .= $virgula." o153_suspenderempenho = '$this->o153_suspenderempenho' ";
            $virgula = ",";
            if (trim($this->o153_suspenderempenho) == null) {
                $this->erro_sql = " Campo Suspender Empenho não informado.";
                $this->erro_campo = "o153_suspenderempenho";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o153_suspenderliquidacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o153_suspenderliquidacao"])) {
            $sql  .= $virgula." o153_suspenderliquidacao = '$this->o153_suspenderliquidacao' ";
            $virgula = ",";
            if (trim($this->o153_suspenderliquidacao) == null) {
                $this->erro_sql = " Campo Suspender Empenho não informado.";
                $this->erro_campo = "o153_suspenderliquidacao";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o153_suspenderpagamento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o153_suspenderpagamento"])) {
            $sql  .= $virgula." o153_suspenderpagamento = '$this->o153_suspenderpagamento' ";
            $virgula = ",";
            if (trim($this->o153_suspenderpagamento) == null) {
                $this->erro_sql = " Campo Suspender Pagamento não informado.";
                $this->erro_campo = "o153_suspenderpagamento";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($o153_sequencial!=null) {
            $sql .= " o153_sequencial = $this->o153_sequencial";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            $resaco = $this->sql_record($this->sql_query_file($this->o153_sequencial));
            if ($this->numrows > 0) {
                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {
                      $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                      $acount = pg_result($resac, 0, 0);
                      $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                      $resac = db_query("insert into db_acountkey values($acount,1015110,'$this->o153_sequencial','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["o153_sequencial"]) || $this->o153_sequencial != "") {
                        $resac = db_query("insert into db_acount values($acount,1011089,1015110,'".AddSlashes(pg_result($resaco, $conresaco, 'o153_sequencial'))."','$this->o153_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["o153_orgao"]) || $this->o153_orgao != "") {
                        $resac = db_query("insert into db_acount values($acount,1011089,1015111,'".AddSlashes(pg_result($resaco, $conresaco, 'o153_orgao'))."','$this->o153_orgao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["o153_unidade"]) || $this->o153_unidade != "") {
                        $resac = db_query("insert into db_acount values($acount,1011089,1015112,'".AddSlashes(pg_result($resaco, $conresaco, 'o153_unidade'))."','$this->o153_unidade',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["o153_recurso"]) || $this->o153_recurso != "") {
                        $resac = db_query("insert into db_acount values($acount,1011089,1015114,'".AddSlashes(pg_result($resaco, $conresaco, 'o153_recurso'))."','$this->o153_recurso',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["o153_localizadorgastos"]) || $this->o153_localizadorgastos != "") {
                        $resac = db_query("insert into db_acount values($acount,1011089,1015113,'".AddSlashes(pg_result($resaco, $conresaco, 'o153_localizadorgastos'))."','$this->o153_localizadorgastos',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["o153_exercicio"]) || $this->o153_exercicio != "") {
                        $resac = db_query("insert into db_acount values($acount,1011089,1015115,'".AddSlashes(pg_result($resaco, $conresaco, 'o153_exercicio'))."','$this->o153_exercicio',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["o153_suspenderempenho"]) || $this->o153_suspenderempenho != "") {
                        $resac = db_query("insert into db_acount values($acount,1011089,1015116,'".AddSlashes(pg_result($resaco, $conresaco, 'o153_suspenderempenho'))."','$this->o153_suspenderempenho',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["o153_suspenderliquidacao"]) || $this->o153_suspenderliquidacao != "") {
                        $resac = db_query("insert into db_acount values($acount,1011089,1015117,'".AddSlashes(pg_result($resaco, $conresaco, 'o153_suspenderliquidacao'))."','$this->o153_suspenderliquidacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["o153_suspenderpagamento"]) || $this->o153_suspenderpagamento != "") {
                        $resac = db_query("insert into db_acount values($acount,1011089,1015118,'".AddSlashes(pg_result($resaco, $conresaco, 'o153_suspenderpagamento'))."','$this->o153_suspenderpagamento',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Suspensão Orçamentária não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->o153_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Suspensão Orçamentária não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->o153_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$this->o153_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($o153_sequencial = null, $dbwhere = null)
    {
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            if (empty($dbwhere)) {
                $resaco = $this->sql_record($this->sql_query_file($o153_sequencial));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
            }
            if (($resaco != false) || ($this->numrows!=0)) {
                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {
                    $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                    $resac  = db_query("insert into db_acountkey values($acount,1015110,'$o153_sequencial','E')");
                    $resac  = db_query("insert into db_acount values($acount,1011089,1015110,'','".AddSlashes(pg_result($resaco, $iresaco, 'o153_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1011089,1015111,'','".AddSlashes(pg_result($resaco, $iresaco, 'o153_orgao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1011089,1015112,'','".AddSlashes(pg_result($resaco, $iresaco, 'o153_unidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1011089,1015114,'','".AddSlashes(pg_result($resaco, $iresaco, 'o153_recurso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1011089,1015113,'','".AddSlashes(pg_result($resaco, $iresaco, 'o153_localizadorgastos'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1011089,1015115,'','".AddSlashes(pg_result($resaco, $iresaco, 'o153_exercicio'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1011089,1015116,'','".AddSlashes(pg_result($resaco, $iresaco, 'o153_suspenderempenho'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1011089,1015117,'','".AddSlashes(pg_result($resaco, $iresaco, 'o153_suspenderliquidacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1011089,1015118,'','".AddSlashes(pg_result($resaco, $iresaco, 'o153_suspenderpagamento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                }
            }
        }
        $sql = " delete from suspensaoorcamentaria
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($o153_sequencial)) {
                if (!empty($sql2)) {
                     $sql2 .= " and ";
                }
                $sql2 .= " o153_sequencial = $o153_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Suspensão Orçamentária não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$o153_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Suspensão Orçamentária não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$o153_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$o153_sequencial;
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
            $this->erro_sql   = "Record Vazio na Tabela:suspensaoorcamentaria";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($o153_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos}";
        $sql .= "  from suspensaoorcamentaria ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($o153_sequencial)) {
                $sql2 .= " where suspensaoorcamentaria.o153_sequencial = $o153_sequencial ";
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

    public function sql_query_file($o153_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos} ";
        $sql .= "  from suspensaoorcamentaria ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($o153_sequencial)) {
                $sql2 .= " where suspensaoorcamentaria.o153_sequencial = $o153_sequencial ";
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

    public function getDadosOrcamento()
    {
        $sql = "select distinct 
                       o58_anousu, 
                       o58_orgao, 
                       o58_unidade,
                       o41_descr, 
                       o58_codigo,
                       gestao,
                       o58_localizadorgastos,
                       coalesce(o153_suspenderempenho, false) as suspenderempenho, 
                       coalesce(o153_suspenderliquidacao, false) as suspenderliquidacao,
                       coalesce(o153_suspenderpagamento, false) as suspenderpagamento
                  from orcdotacao 
                       inner join orctiporec on orctiporec.o15_codigo = orcdotacao.o58_codigo
                       inner join fonterecurso on orctiporec_id = orctiporec.o15_codigo
                                              and fonterecurso.exercicio = orcdotacao.o58_anousu
                       inner join orcunidade on orcunidade.o41_unidade = orcdotacao.o58_unidade
                                            and orcunidade.o41_orgao = orcdotacao.o58_orgao
                                            and orcunidade.o41_anousu = orcdotacao.o58_anousu
                       left join suspensaoorcamentaria on o58_unidade = suspensaoorcamentaria.o153_unidade
                                                      and o58_orgao = suspensaoorcamentaria.o153_orgao
                                                      and o58_anousu = suspensaoorcamentaria.o153_exercicio
                                                      and o58_codigo = suspensaoorcamentaria.o153_recurso
                                                      and o58_localizadorgastos = suspensaoorcamentaria.o153_localizadorgastos
                 where o58_instit = ".DB_getsession("DB_instit")." 
                   and o58_anousu = ".db_getsession("DB_anousu")."
                 order by o58_orgao, o58_unidade, gestao, o58_localizadorgastos";
        return db_utils::getCollectionByRecord($this->sql_record($sql));
    }

    public function verificaSuspensaoDotacao($dotacao, $anousu = null)
    {
        try {
            $clDotacao = new cl_orcdotacao();

            if (empty($anousu)) {
                $anousu = db_getsession("DB_anousu");
            }

            $sql = $clDotacao->sql_query_file($anousu, $dotacao);
            $rsDotacao = $clDotacao->sql_record($sql);
            if ($clDotacao->numrows == 0) {
                throw new Exception("Erro buscando dados da dotação");
            }

            $dadosDotacao = db_utils::fieldsMemory($rsDotacao, 0);

            return $this->verificaSuspensaoEstruturaOrcamentaria(
                $dadosDotacao->o58_anousu,
                $dadosDotacao->o58_orgao,
                $dadosDotacao->o58_unidade,
                $dadosDotacao->o58_localizadorgastos,
                $dadosDotacao->o58_codigo
            );
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function verificaSuspensaoEstruturaOrcamentaria(
        $exercicio,
        $orgao,
        $unidade,
        $localizadorgastos,
        $recurso
    ) {
        $retorno = new stdClass();
        $retorno->suspendeEmpenho = false;
        $retorno->suspendeLiquiquidacao = false;
        $retorno->suspendePagamento = false;

        $where = [];
        $where[] = "suspensaoorcamentaria.o153_exercicio = {$exercicio} ";
        $where[] = "suspensaoorcamentaria.o153_orgao = {$orgao} ";
        $where[] = "suspensaoorcamentaria.o153_unidade = {$unidade} ";
        $where[] = "suspensaoorcamentaria.o153_localizadorgastos = {$localizadorgastos} ";
        $where[] = "suspensaoorcamentaria.o153_recurso = {$recurso}";
        
        $sql = $this->sql_query_file(
            null,
            "o153_suspenderempenho, o153_suspenderliquidacao, o153_suspenderpagamento",
            null,
            implode(" and ", $where)
        );
        $rsSuspensao = $this->sql_record($sql);
        if ($this->numrows > 0) {
            $dadosSuspensao = db_utils::fieldsMemory($rsSuspensao, 0);

            $retorno->suspendeEmpenho = ($dadosSuspensao->o153_suspenderempenho == "t"?true:false);
            $retorno->suspendeLiquiquidacao = ($dadosSuspensao->o153_suspenderliquidacao=="t"?true:false);
            $retorno->suspendePagamento = ($dadosSuspensao->o153_suspenderpagamento=="t"?true:false);
        }

        return $retorno;
    }

    public function verificaSuspensaoEmpenhoDotacao($dotacao, $anousu = null)
    {
        return $this->verificaSuspensaoDotacao($dotacao, $anousu)->suspendeEmpenho;
    }

    public function verificaSuspensaoLiquidacaoDotacao($dotacao, $anousu = null)
    {
        return $this->verificaSuspensaoDotacao($dotacao, $anousu)->suspendeLiquiquidacao;
    }

    public function verificaSuspensaoPagamentoDotacao($dotacao, $anousu = null)
    {
        return $this->verificaSuspensaoDotacao($dotacao, $anousu)->suspendePagamento;
    }
}
