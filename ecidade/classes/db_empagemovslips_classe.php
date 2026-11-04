<?php
//MODULO: caixa
//CLASSE DA ENTIDADE empagemovslips
class cl_empagemovslips
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
    public $k107_sequencial = 0;
    public $k107_empagemov = 0;
    public $k107_data_dia = null;
    public $k107_data_mes = null;
    public $k107_data_ano = null;
    public $k107_data = null;
    public $k107_valor = 0;
    public $k107_ctacredito = 0;
    public $k107_ctadebito = 0;
    public $k107_retencao = null;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 k107_sequencial = int4 = Código Sequencial
                 k107_empagemov = int4 = Movimento
                 k107_data = date = Data do Movimento
                 k107_valor = float8 = Valor do Movimento
                 k107_ctacredito = int4 = Conta a Credito
                 k107_ctadebito = int4 = Conta a Debito
                 k107_retencao = int4 = Retenção
                 ";
   //funcao construtor da classe
    public function __construct()
    {
      //classes dos rotulos dos campos
        $this->rotulo = new rotulo("empagemovslips");
        $this->pagina_retorno =  basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
    }
   //funcao erro
    public function erro($mostra, $retorna)
    {
        if (($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )) {
            echo "<script>alert(\"".$this->erro_msg."\");</script>";
            if ($retorna==true) {
                echo "<script>location.href='".$this->pagina_retorno."'</script>";
            }
        }
    }

   // funcao para atualizar campos
    public function atualizacampos($exclusao = false)
    {
        if ($exclusao==false) {
            $this->k107_sequencial = ($this->k107_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["k107_sequencial"]:$this->k107_sequencial);
            $this->k107_empagemov = ($this->k107_empagemov == ""?@$GLOBALS["HTTP_POST_VARS"]["k107_empagemov"]:$this->k107_empagemov);
            if ($this->k107_data == "") {
                $this->k107_data_dia = ($this->k107_data_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["k107_data_dia"]:$this->k107_data_dia);
                $this->k107_data_mes = ($this->k107_data_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["k107_data_mes"]:$this->k107_data_mes);
                $this->k107_data_ano = ($this->k107_data_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["k107_data_ano"]:$this->k107_data_ano);
                if ($this->k107_data_dia != "") {
                     $this->k107_data = $this->k107_data_ano."-".$this->k107_data_mes."-".$this->k107_data_dia;
                }
            }
            $this->k107_valor = ($this->k107_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["k107_valor"]:$this->k107_valor);
            $this->k107_ctacredito = !empty($_POST["k107_ctacredito"]) ? $_POST["k107_ctacredito"] : $this->k107_ctacredito;
            $this->k107_ctadebito = !empty($_POST["k107_ctadebito"]) ? $_POST["k107_ctadebito"] : $this->k107_ctadebito;
            $this->k107_retencao = !empty($_POST["k107_retencao"]) ? $_POST["k107_retencao"] : $this->k107_retencao;
        } else {
            $this->k107_sequencial = ($this->k107_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["k107_sequencial"]:$this->k107_sequencial);
        }
    }
   // funcao para Inclusão
    public function incluir($k107_sequencial)
    {
        $this->atualizacampos();
        if ($this->k107_empagemov == null) {
            $this->erro_sql = " Campo Movimento não informado.";
            $this->erro_campo = "k107_empagemov";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k107_data == null) {
            $this->erro_sql = " Campo Data do Movimento não informado.";
            $this->erro_campo = "k107_data_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k107_valor == null) {
            $this->erro_sql = " Campo Valor do Movimento não informado.";
            $this->erro_campo = "k107_valor";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if (is_null($this->k107_ctacredito)) {
            $this->erro_sql = " Campo Conta a Credito não informado.";
            $this->erro_campo = "k107_ctacredito";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if (is_null($this->k107_ctadebito)) {
            $this->erro_sql = " Campo Conta a Debito não informado.";
            $this->erro_campo = "k107_ctadebito";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k107_retencao == null) {
            $this->k107_retencao = "null";
        }
        if ($k107_sequencial == "" || $k107_sequencial == null) {
            $result = db_query("select nextval('empagemovslips_k107_sequencial_seq')");
            if ($result==false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql   = "Verifique o cadastro da sequencia: empagemovslips_k107_sequencial_seq do campo: k107_sequencial";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->k107_sequencial = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from empagemovslips_k107_sequencial_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $k107_sequencial)) {
                $this->erro_sql = " Campo k107_sequencial maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->k107_sequencial = $k107_sequencial;
            }
        }
        if (($this->k107_sequencial == null) || ($this->k107_sequencial == "")) {
            $this->erro_sql = " Campo k107_sequencial não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }



        $sql = "insert into empagemovslips(
                                       k107_sequencial
                                      ,k107_empagemov
                                      ,k107_data
                                      ,k107_valor
                                      ,k107_ctacredito
                                      ,k107_ctadebito
                                      ,k107_retencao
                       )
                values (
                                $this->k107_sequencial
                               ,$this->k107_empagemov
                               ,".($this->k107_data == "null" || $this->k107_data == ""?"null":"'".$this->k107_data."'")."
                               ,$this->k107_valor
                               ,$this->k107_ctacredito
                               ,$this->k107_ctadebito
                               ,$this->k107_retencao
                      )";

        $result = db_query($sql);
        if ($result==false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Slips a Realizar ($this->k107_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Slips a Realizar já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "Slips a Realizar ($this->k107_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->k107_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            $resaco = $this->sql_record($this->sql_query_file($this->k107_sequencial));
            if (($resaco!=false)||($this->numrows!=0)) {
                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                $resac = db_query("insert into db_acountkey values($acount,12458,'$this->k107_sequencial','I')");
                $resac = db_query("insert into db_acount values($acount,2174,12458,'','".AddSlashes(pg_result($resaco, 0, 'k107_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,2174,12463,'','".AddSlashes(pg_result($resaco, 0, 'k107_empagemov'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,2174,12459,'','".AddSlashes(pg_result($resaco, 0, 'k107_data'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,2174,12460,'','".AddSlashes(pg_result($resaco, 0, 'k107_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,2174,12461,'','".AddSlashes(pg_result($resaco, 0, 'k107_ctacredito'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,2174,12462,'','".AddSlashes(pg_result($resaco, 0, 'k107_ctadebito'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,2174,1010031,'','".AddSlashes(pg_result($resaco, 0, 'k107_retencao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
            }
        }
        return true;
    }
   // funcao para alteracao
    public function alterar($k107_sequencial = null)
    {
        $this->atualizacampos();
        $sql = " update empagemovslips set ";
        $virgula = "";
        if (trim($this->k107_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k107_sequencial"])) {
            $sql  .= $virgula." k107_sequencial = $this->k107_sequencial ";
            $virgula = ",";
            if (trim($this->k107_sequencial) == null) {
                $this->erro_sql = " Campo Código Sequencial não informado.";
                $this->erro_campo = "k107_sequencial";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k107_empagemov)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k107_empagemov"])) {
            $sql  .= $virgula." k107_empagemov = $this->k107_empagemov ";
            $virgula = ",";
            if (trim($this->k107_empagemov) == null) {
                $this->erro_sql = " Campo Movimento não informado.";
                $this->erro_campo = "k107_empagemov";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k107_data)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k107_data_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["k107_data_dia"] !="")) {
            $sql  .= $virgula." k107_data = '$this->k107_data' ";
            $virgula = ",";
            if (trim($this->k107_data) == null) {
                $this->erro_sql = " Campo Data do Movimento não informado.";
                $this->erro_campo = "k107_data_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["k107_data_dia"])) {
                $sql  .= $virgula." k107_data = null ";
                $virgula = ",";
                if (trim($this->k107_data) == null) {
                    $this->erro_sql = " Campo Data do Movimento não informado.";
                    $this->erro_campo = "k107_data_dia";
                    $this->erro_banco = "";
                    $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        if (trim($this->k107_valor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k107_valor"])) {
            $sql  .= $virgula." k107_valor = $this->k107_valor ";
            $virgula = ",";
            if (trim($this->k107_valor) == null) {
                $this->erro_sql = " Campo Valor do Movimento não informado.";
                $this->erro_campo = "k107_valor";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k107_ctacredito)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k107_ctacredito"])) {
            $sql  .= $virgula." k107_ctacredito = $this->k107_ctacredito ";
            $virgula = ",";
            if (trim($this->k107_ctacredito) == null) {
                $this->erro_sql = " Campo Conta a Credito não informado.";
                $this->erro_campo = "k107_ctacredito";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k107_ctadebito)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k107_ctadebito"])) {
            $sql  .= $virgula." k107_ctadebito = $this->k107_ctadebito ";
            $virgula = ",";
            if (trim($this->k107_ctadebito) == null) {
                $this->erro_sql = " Campo Conta a Debito não informado.";
                $this->erro_campo = "k107_ctadebito";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }

        if (!is_null($this->k107_retencao)) {
            $sql  .= $virgula." k107_retencao = $this->k107_retencao ";
            $virgula = ",";
        }

        $sql .= " where ";
        if ($k107_sequencial!=null) {
            $sql .= " k107_sequencial = $this->k107_sequencial";
        }

        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            $resaco = $this->sql_record($this->sql_query_file($this->k107_sequencial));
            if ($this->numrows > 0) {
                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {
                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                    $resac = db_query("insert into db_acountkey values($acount,12458,'$this->k107_sequencial','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k107_sequencial"]) || $this->k107_sequencial != "") {
                        $resac = db_query("insert into db_acount values($acount,2174,12458,'".AddSlashes(pg_result($resaco, $conresaco, 'k107_sequencial'))."','$this->k107_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k107_empagemov"]) || $this->k107_empagemov != "") {
                        $resac = db_query("insert into db_acount values($acount,2174,12463,'".AddSlashes(pg_result($resaco, $conresaco, 'k107_empagemov'))."','$this->k107_empagemov',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k107_data"]) || $this->k107_data != "") {
                        $resac = db_query("insert into db_acount values($acount,2174,12459,'".AddSlashes(pg_result($resaco, $conresaco, 'k107_data'))."','$this->k107_data',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k107_valor"]) || $this->k107_valor != "") {
                        $resac = db_query("insert into db_acount values($acount,2174,12460,'".AddSlashes(pg_result($resaco, $conresaco, 'k107_valor'))."','$this->k107_valor',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k107_ctacredito"]) || $this->k107_ctacredito != "") {
                        $resac = db_query("insert into db_acount values($acount,2174,12461,'".AddSlashes(pg_result($resaco, $conresaco, 'k107_ctacredito'))."','$this->k107_ctacredito',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k107_ctadebito"]) || $this->k107_ctadebito != "") {
                        $resac = db_query("insert into db_acount values($acount,2174,12462,'".AddSlashes(pg_result($resaco, $conresaco, 'k107_ctadebito'))."','$this->k107_ctadebito',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k107_retencao"]) || $this->k107_retencao != "") {
                        $resac = db_query("insert into db_acount values($acount,2174,1010031,'".AddSlashes(pg_result($resaco, $conresaco, 'k107_retencao'))."','$this->k107_retencao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Slips a Realizar não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->k107_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Slips a Realizar não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->k107_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$this->k107_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao para exclusao
    public function excluir($k107_sequencial = null, $dbwhere = null)
    {

        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            if (empty($dbwhere)) {
                $resaco = $this->sql_record($this->sql_query_file($k107_sequencial));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
            }
            if (($resaco != false) || ($this->numrows!=0)) {
                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {
                    $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                    $resac  = db_query("insert into db_acountkey values($acount,12458,'$k107_sequencial','E')");
                    $resac  = db_query("insert into db_acount values($acount,2174,12458,'','".AddSlashes(pg_result($resaco, $iresaco, 'k107_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,2174,12463,'','".AddSlashes(pg_result($resaco, $iresaco, 'k107_empagemov'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,2174,12459,'','".AddSlashes(pg_result($resaco, $iresaco, 'k107_data'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,2174,12460,'','".AddSlashes(pg_result($resaco, $iresaco, 'k107_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,2174,12461,'','".AddSlashes(pg_result($resaco, $iresaco, 'k107_ctacredito'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,2174,12462,'','".AddSlashes(pg_result($resaco, $iresaco, 'k107_ctadebito'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,2174,1010031,'','".AddSlashes(pg_result($resaco, $iresaco, 'k107_retencao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                }
            }
        }
        $sql = " delete from empagemovslips
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($k107_sequencial)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " k107_sequencial = $k107_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Slips a Realizar não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$k107_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Slips a Realizar não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$k107_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$k107_sequencial;
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
            $this->erro_sql   = "Record Vazio na Tabela:empagemovslips";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
   // funcao do sql
    public function sql_query($k107_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos}";
        $sql .= "  from empagemovslips ";
        $sql .= "      inner join empagemov  on  empagemov.e81_codmov = empagemovslips.k107_empagemov";
        $sql .= "      inner join empage  on  empage.e80_codage = empagemov.e81_codage";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($k107_sequencial)) {
                $sql2 .= " where empagemovslips.k107_sequencial = $k107_sequencial ";
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
    public function sql_query_file($k107_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos} ";
        $sql .= "  from empagemovslips ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($k107_sequencial)) {
                $sql2 .= " where empagemovslips.k107_sequencial = $k107_sequencial ";
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
