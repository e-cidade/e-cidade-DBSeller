<?php

class cl_empprestaitemdiaria
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
    public $e446_sequencial = 0;
    public $e446_empprestaitem = 0;
    public $e446_regist = 0;
    public $e446_datainicio_dia = null;
    public $e446_datainicio_mes = null;
    public $e446_datainicio_ano = null;
    public $e446_datainicio = null;
    public $e446_datafim_dia = null;
    public $e446_datafim_mes = null;
    public $e446_datafim_ano = null;
    public $e446_datafim = null;
    public $e446_motivo = null;
    public $e446_destino = null;
    public $e446_quantidade = 0;
    public $e446_movimento = 0;
    public $e446_tipodiaria = null;
    public $e446_estadodestino = null;
    public $e446_paisdestino = null;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 e446_sequencial = int4 = Código Sequencial 
                 e446_empprestaitem = int4 = Código do item da prestação 
                 e446_regist = int4 = Matrícula 
                 e446_datainicio = date = Período Inicial 
                 e446_datafim = date = Período Final 
                 e446_motivo = text = Motivo 
                 e446_destino = text = Destino 
                 e446_quantidade = int4 = Quantidade 
                 e446_movimento = int4 = Código do movimento 
                 e446_tipodiaria = varchar(50) = Tipo de Diária 
                 e446_estadodestino = text = Estado de Destino 
                 e446_paisdestino = varchar(255) = País de destino 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("empprestaitemdiaria");
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
            $this->e446_sequencial = ($this->e446_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["e446_sequencial"]:$this->e446_sequencial);
            $this->e446_empprestaitem = ($this->e446_empprestaitem == ""?@$GLOBALS["HTTP_POST_VARS"]["e446_empprestaitem"]:$this->e446_empprestaitem);
            $this->e446_regist = ($this->e446_regist == ""?@$GLOBALS["HTTP_POST_VARS"]["e446_regist"]:$this->e446_regist);
            if ($this->e446_datainicio == "") {
                $this->e446_datainicio_dia = ($this->e446_datainicio_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["e446_datainicio_dia"]:$this->e446_datainicio_dia);
                $this->e446_datainicio_mes = ($this->e446_datainicio_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["e446_datainicio_mes"]:$this->e446_datainicio_mes);
                $this->e446_datainicio_ano = ($this->e446_datainicio_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["e446_datainicio_ano"]:$this->e446_datainicio_ano);
                if ($this->e446_datainicio_dia != "") {
                    $this->e446_datainicio = $this->e446_datainicio_ano."-".$this->e446_datainicio_mes."-".$this->e446_datainicio_dia;
                }
            }
            if ($this->e446_datafim == "") {
                $this->e446_datafim_dia = ($this->e446_datafim_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["e446_datafim_dia"]:$this->e446_datafim_dia);
                $this->e446_datafim_mes = ($this->e446_datafim_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["e446_datafim_mes"]:$this->e446_datafim_mes);
                $this->e446_datafim_ano = ($this->e446_datafim_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["e446_datafim_ano"]:$this->e446_datafim_ano);
                if ($this->e446_datafim_dia != "") {
                    $this->e446_datafim = $this->e446_datafim_ano."-".$this->e446_datafim_mes."-".$this->e446_datafim_dia;
                }
            }
            $this->e446_motivo = ($this->e446_motivo == ""?@$GLOBALS["HTTP_POST_VARS"]["e446_motivo"]:$this->e446_motivo);
            $this->e446_destino = ($this->e446_destino == ""?@$GLOBALS["HTTP_POST_VARS"]["e446_destino"]:$this->e446_destino);
            $this->e446_quantidade = ($this->e446_quantidade == ""?@$GLOBALS["HTTP_POST_VARS"]["e446_quantidade"]:$this->e446_quantidade);
            $this->e446_movimento = ($this->e446_movimento == ""?@$GLOBALS["HTTP_POST_VARS"]["e446_movimento"]:$this->e446_movimento);
            $this->e446_tipodiaria = ($this->e446_tipodiaria == ""?@$GLOBALS["HTTP_POST_VARS"]["e446_tipodiaria"]:$this->e446_tipodiaria);
            $this->e446_estadodestino = ($this->e446_estadodestino == ""?@$GLOBALS["HTTP_POST_VARS"]["e446_estadodestino"]:$this->e446_estadodestino);
            $this->e446_paisdestino = ($this->e446_paisdestino == ""?@$GLOBALS["HTTP_POST_VARS"]["e446_paisdestino"]:$this->e446_paisdestino);
        } else {
            $this->e446_sequencial = ($this->e446_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["e446_sequencial"]:$this->e446_sequencial);
        }
    }

    public function incluir($e446_sequencial)
    {
        $this->atualizacampos();
        if ($this->e446_empprestaitem == null) {
            $this->erro_sql = " Campo Código do item da prestação não informado.";
            $this->erro_campo = "e446_empprestaitem";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e446_regist == null) {
            $this->erro_sql = " Campo Matrícula não informado.";
            $this->erro_campo = "e446_regist";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e446_datainicio == null) {
            $this->erro_sql = " Campo Período Inicial não informado.";
            $this->erro_campo = "e446_datainicio_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e446_datafim == null) {
            $this->erro_sql = " Campo Período Final não informado.";
            $this->erro_campo = "e446_datafim_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e446_motivo == null) {
            $this->erro_sql = " Campo Motivo não informado.";
            $this->erro_campo = "e446_motivo";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e446_destino == null) {
            $this->erro_sql = " Campo Destino não informado.";
            $this->erro_campo = "e446_destino";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e446_quantidade == null) {
            $this->e446_quantidade = "0";
        }
        if ($this->e446_movimento == null) {
            $this->erro_sql = " Campo Código do movimento não informado.";
            $this->erro_campo = "e446_movimento";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e446_tipodiaria == null) {
            $this->erro_sql = " Campo Tipo de Diária não informado.";
            $this->erro_campo = "e446_tipodiaria";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($e446_sequencial == "" || $e446_sequencial == null) {
            $result = db_query("select nextval('empprestaitemdiaria_e446_sequencial_seq')");
            if ($result==false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql   = "Verifique o cadastro da sequencia: empprestaitemdiaria_e446_sequencial_seq do campo: e446_sequencial";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->e446_sequencial = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from empprestaitemdiaria_e446_sequencial_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $e446_sequencial)) {
                $this->erro_sql = " Campo e446_sequencial maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->e446_sequencial = $e446_sequencial;
            }
        }
        if (($this->e446_sequencial == null) || ($this->e446_sequencial == "")) {
            $this->erro_sql = " Campo e446_sequencial não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into empprestaitemdiaria(
                                       e446_sequencial 
                                      ,e446_empprestaitem 
                                      ,e446_regist 
                                      ,e446_datainicio 
                                      ,e446_datafim 
                                      ,e446_motivo 
                                      ,e446_destino 
                                      ,e446_quantidade 
                                      ,e446_movimento 
                                      ,e446_tipodiaria 
                                      ,e446_estadodestino 
                                      ,e446_paisdestino 
                       )
                values (
                                $this->e446_sequencial 
                               ,$this->e446_empprestaitem 
                               ,$this->e446_regist 
                               ,".($this->e446_datainicio == "null" || $this->e446_datainicio == ""?"null":"'".$this->e446_datainicio."'")." 
                               ,".($this->e446_datafim == "null" || $this->e446_datafim == ""?"null":"'".$this->e446_datafim."'")." 
                               ,'$this->e446_motivo' 
                               ,'$this->e446_destino' 
                               ,$this->e446_quantidade 
                               ,$this->e446_movimento 
                               ,'$this->e446_tipodiaria' 
                               ,'$this->e446_estadodestino' 
                               ,'$this->e446_paisdestino' 
                      )";
        $result = db_query($sql);
        if ($result==false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "empprestaitemdiaria ($this->e446_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "empprestaitemdiaria já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "empprestaitemdiaria ($this->e446_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->e446_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            $resaco = $this->sql_record($this->sql_query_file($this->e446_sequencial));
            if (($resaco!=false)||($this->numrows!=0)) {
                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                $resac = db_query("insert into db_acountkey values($acount,1010020,'$this->e446_sequencial','I')");
                $resac = db_query("insert into db_acount values($acount,1010329,1010020,'','".AddSlashes(pg_result($resaco, 0, 'e446_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1010329,1010021,'','".AddSlashes(pg_result($resaco, 0, 'e446_empprestaitem'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1010329,1010022,'','".AddSlashes(pg_result($resaco, 0, 'e446_regist'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1010329,1010023,'','".AddSlashes(pg_result($resaco, 0, 'e446_datainicio'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1010329,1010024,'','".AddSlashes(pg_result($resaco, 0, 'e446_datafim'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1010329,1010025,'','".AddSlashes(pg_result($resaco, 0, 'e446_motivo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1010329,1010026,'','".AddSlashes(pg_result($resaco, 0, 'e446_destino'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1010329,1010027,'','".AddSlashes(pg_result($resaco, 0, 'e446_quantidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1010329,1010028,'','".AddSlashes(pg_result($resaco, 0, 'e446_movimento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1010329,1010029,'','".AddSlashes(pg_result($resaco, 0, 'e446_tipodiaria'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1010329,1015558,'','".AddSlashes(pg_result($resaco, 0, 'e446_estadodestino'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1010329,1015559,'','".AddSlashes(pg_result($resaco, 0, 'e446_paisdestino'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
            }
        }
        return true;
    }

    public function alterar($e446_sequencial = null)
    {
        $this->atualizacampos();
        $sql = " update empprestaitemdiaria set ";
        $virgula = "";
        if (trim($this->e446_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e446_sequencial"])) {
            $sql  .= $virgula." e446_sequencial = $this->e446_sequencial ";
            $virgula = ",";
            if (trim($this->e446_sequencial) == null) {
                $this->erro_sql = " Campo Código Sequencial não informado.";
                $this->erro_campo = "e446_sequencial";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e446_empprestaitem)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e446_empprestaitem"])) {
            $sql  .= $virgula." e446_empprestaitem = $this->e446_empprestaitem ";
            $virgula = ",";
            if (trim($this->e446_empprestaitem) == null) {
                $this->erro_sql = " Campo Código do item da prestação não informado.";
                $this->erro_campo = "e446_empprestaitem";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e446_regist)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e446_regist"])) {
            $sql  .= $virgula." e446_regist = $this->e446_regist ";
            $virgula = ",";
            if (trim($this->e446_regist) == null) {
                $this->erro_sql = " Campo Matrícula não informado.";
                $this->erro_campo = "e446_regist";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e446_datainicio)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e446_datainicio_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["e446_datainicio_dia"] !="")) {
            $sql  .= $virgula." e446_datainicio = '$this->e446_datainicio' ";
            $virgula = ",";
            if (trim($this->e446_datainicio) == null) {
                $this->erro_sql = " Campo Período Inicial não informado.";
                $this->erro_campo = "e446_datainicio_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["e446_datainicio_dia"])) {
                $sql  .= $virgula." e446_datainicio = null ";
                $virgula = ",";
                if (trim($this->e446_datainicio) == null) {
                      $this->erro_sql = " Campo Período Inicial não informado.";
                      $this->erro_campo = "e446_datainicio_dia";
                      $this->erro_banco = "";
                      $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                      $this->erro_status = "0";
                      return false;
                }
            }
        }
        if (trim($this->e446_datafim)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e446_datafim_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["e446_datafim_dia"] !="")) {
            $sql  .= $virgula." e446_datafim = '$this->e446_datafim' ";
            $virgula = ",";
            if (trim($this->e446_datafim) == null) {
                $this->erro_sql = " Campo Período Final não informado.";
                $this->erro_campo = "e446_datafim_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["e446_datafim_dia"])) {
                $sql  .= $virgula." e446_datafim = null ";
                $virgula = ",";
                if (trim($this->e446_datafim) == null) {
                      $this->erro_sql = " Campo Período Final não informado.";
                      $this->erro_campo = "e446_datafim_dia";
                      $this->erro_banco = "";
                      $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                      $this->erro_status = "0";
                      return false;
                }
            }
        }
        if (trim($this->e446_motivo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e446_motivo"])) {
            $sql  .= $virgula." e446_motivo = '$this->e446_motivo' ";
            $virgula = ",";
            if (trim($this->e446_motivo) == null) {
                $this->erro_sql = " Campo Motivo não informado.";
                $this->erro_campo = "e446_motivo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e446_destino)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e446_destino"])) {
            $sql  .= $virgula." e446_destino = '$this->e446_destino' ";
            $virgula = ",";
            if (trim($this->e446_destino) == null) {
                $this->erro_sql = " Campo Destino não informado.";
                $this->erro_campo = "e446_destino";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e446_quantidade)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e446_quantidade"])) {
            if (trim($this->e446_quantidade)=="" && isset($GLOBALS["HTTP_POST_VARS"]["e446_quantidade"])) {
                $this->e446_quantidade = "0" ;
            }
            $sql  .= $virgula." e446_quantidade = $this->e446_quantidade ";
            $virgula = ",";
        }
        if (trim($this->e446_movimento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e446_movimento"])) {
            $sql  .= $virgula." e446_movimento = $this->e446_movimento ";
            $virgula = ",";
            if (trim($this->e446_movimento) == null) {
                $this->erro_sql = " Campo Código do movimento não informado.";
                $this->erro_campo = "e446_movimento";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e446_tipodiaria)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e446_tipodiaria"])) {
            $sql  .= $virgula." e446_tipodiaria = '$this->e446_tipodiaria' ";
            $virgula = ",";
            if (trim($this->e446_tipodiaria) == null) {
                $this->erro_sql = " Campo Tipo de Diária não informado.";
                $this->erro_campo = "e446_tipodiaria";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e446_estadodestino)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e446_estadodestino"])) {
            $sql  .= $virgula." e446_estadodestino = '$this->e446_estadodestino' ";
            $virgula = ",";
        }
        if (trim($this->e446_paisdestino)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e446_paisdestino"])) {
            $sql  .= $virgula." e446_paisdestino = '$this->e446_paisdestino' ";
            $virgula = ",";
        }
        $sql .= " where ";
        if ($e446_sequencial!=null) {
            $sql .= " e446_sequencial = $this->e446_sequencial";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            $resaco = $this->sql_record($this->sql_query_file($this->e446_sequencial));
            if ($this->numrows > 0) {
                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {
                      $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                      $acount = pg_result($resac, 0, 0);
                      $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                      $resac = db_query("insert into db_acountkey values($acount,1010020,'$this->e446_sequencial','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e446_sequencial"]) || $this->e446_sequencial != "") {
                        $resac = db_query("insert into db_acount values($acount,1010329,1010020,'".AddSlashes(pg_result($resaco, $conresaco, 'e446_sequencial'))."','$this->e446_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e446_empprestaitem"]) || $this->e446_empprestaitem != "") {
                        $resac = db_query("insert into db_acount values($acount,1010329,1010021,'".AddSlashes(pg_result($resaco, $conresaco, 'e446_empprestaitem'))."','$this->e446_empprestaitem',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e446_regist"]) || $this->e446_regist != "") {
                        $resac = db_query("insert into db_acount values($acount,1010329,1010022,'".AddSlashes(pg_result($resaco, $conresaco, 'e446_regist'))."','$this->e446_regist',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e446_datainicio"]) || $this->e446_datainicio != "") {
                        $resac = db_query("insert into db_acount values($acount,1010329,1010023,'".AddSlashes(pg_result($resaco, $conresaco, 'e446_datainicio'))."','$this->e446_datainicio',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e446_datafim"]) || $this->e446_datafim != "") {
                        $resac = db_query("insert into db_acount values($acount,1010329,1010024,'".AddSlashes(pg_result($resaco, $conresaco, 'e446_datafim'))."','$this->e446_datafim',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e446_motivo"]) || $this->e446_motivo != "") {
                        $resac = db_query("insert into db_acount values($acount,1010329,1010025,'".AddSlashes(pg_result($resaco, $conresaco, 'e446_motivo'))."','$this->e446_motivo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e446_destino"]) || $this->e446_destino != "") {
                        $resac = db_query("insert into db_acount values($acount,1010329,1010026,'".AddSlashes(pg_result($resaco, $conresaco, 'e446_destino'))."','$this->e446_destino',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e446_quantidade"]) || $this->e446_quantidade != "") {
                        $resac = db_query("insert into db_acount values($acount,1010329,1010027,'".AddSlashes(pg_result($resaco, $conresaco, 'e446_quantidade'))."','$this->e446_quantidade',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e446_movimento"]) || $this->e446_movimento != "") {
                        $resac = db_query("insert into db_acount values($acount,1010329,1010028,'".AddSlashes(pg_result($resaco, $conresaco, 'e446_movimento'))."','$this->e446_movimento',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e446_tipodiaria"]) || $this->e446_tipodiaria != "") {
                        $resac = db_query("insert into db_acount values($acount,1010329,1010029,'".AddSlashes(pg_result($resaco, $conresaco, 'e446_tipodiaria'))."','$this->e446_tipodiaria',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e446_estadodestino"]) || $this->e446_estadodestino != "") {
                        $resac = db_query("insert into db_acount values($acount,1010329,1015558,'".AddSlashes(pg_result($resaco, $conresaco, 'e446_estadodestino'))."','$this->e446_estadodestino',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e446_paisdestino"]) || $this->e446_paisdestino != "") {
                        $resac = db_query("insert into db_acount values($acount,1010329,1015559,'".AddSlashes(pg_result($resaco, $conresaco, 'e446_paisdestino'))."','$this->e446_paisdestino',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "empprestaitemdiaria não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->e446_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "empprestaitemdiaria não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->e446_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$this->e446_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($e446_sequencial = null, $dbwhere = null)
    {
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            if (empty($dbwhere)) {
                $resaco = $this->sql_record($this->sql_query_file($e446_sequencial));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
            }
            if (($resaco != false) || ($this->numrows!=0)) {
                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {
                    $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                    $resac  = db_query("insert into db_acountkey values($acount,1010020,'$e446_sequencial','E')");
                    $resac  = db_query("insert into db_acount values($acount,1010329,1010020,'','".AddSlashes(pg_result($resaco, $iresaco, 'e446_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1010329,1010021,'','".AddSlashes(pg_result($resaco, $iresaco, 'e446_empprestaitem'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1010329,1010022,'','".AddSlashes(pg_result($resaco, $iresaco, 'e446_regist'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1010329,1010023,'','".AddSlashes(pg_result($resaco, $iresaco, 'e446_datainicio'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1010329,1010024,'','".AddSlashes(pg_result($resaco, $iresaco, 'e446_datafim'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1010329,1010025,'','".AddSlashes(pg_result($resaco, $iresaco, 'e446_motivo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1010329,1010026,'','".AddSlashes(pg_result($resaco, $iresaco, 'e446_destino'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1010329,1010027,'','".AddSlashes(pg_result($resaco, $iresaco, 'e446_quantidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1010329,1010028,'','".AddSlashes(pg_result($resaco, $iresaco, 'e446_movimento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1010329,1010029,'','".AddSlashes(pg_result($resaco, $iresaco, 'e446_tipodiaria'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1010329,1015558,'','".AddSlashes(pg_result($resaco, $iresaco, 'e446_estadodestino'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1010329,1015559,'','".AddSlashes(pg_result($resaco, $iresaco, 'e446_paisdestino'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                }
            }
        }
        $sql = " delete from empprestaitemdiaria
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($e446_sequencial)) {
                if (!empty($sql2)) {
                     $sql2 .= " and ";
                }
                $sql2 .= " e446_sequencial = $e446_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "empprestaitemdiaria não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$e446_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "empprestaitemdiaria não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$e446_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$e446_sequencial;
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
            $this->erro_sql   = "Record Vazio na Tabela:empprestaitemdiaria";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($e446_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos}";
        $sql .= "  from empprestaitemdiaria ";
        $sql .= "      inner join empprestaitem  on  empprestaitem.e46_codigo = empprestaitemdiaria.e446_empprestaitem";
        $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = empprestaitem.e46_id_usuario";
        $sql .= "      inner join emppresta  on  emppresta.e45_sequencial = empprestaitem.e46_numemp and  emppresta.e45_sequencial = empprestaitem.e46_emppresta";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($e446_sequencial)) {
                $sql2 .= " where empprestaitemdiaria.e446_sequencial = $e446_sequencial ";
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

    public function sql_query_file($e446_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos} ";
        $sql .= "  from empprestaitemdiaria ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($e446_sequencial)) {
                $sql2 .= " where empprestaitemdiaria.e446_sequencial = $e446_sequencial ";
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
