<?php

class cl_placaixausu
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
    public $k214_sequencial = 0;
    public $k214_placaixa = 0;
    public $k214_db_usuario = 0;
    public $k214_db_depart = 0;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 k214_sequencial = int4 = Sequencial 
                 k214_placaixa = int4 = Código da Planilha 
                 k214_db_usuario = int4 = Código Usuário 
                 k214_db_depart = int4 = Código Departamento 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("placaixausu");
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
            $this->k214_sequencial = ($this->k214_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["k214_sequencial"]:$this->k214_sequencial);
            $this->k214_placaixa = ($this->k214_placaixa == ""?@$GLOBALS["HTTP_POST_VARS"]["k214_placaixa"]:$this->k214_placaixa);
            $this->k214_db_usuario = ($this->k214_db_usuario == ""?@$GLOBALS["HTTP_POST_VARS"]["k214_db_usuario"]:$this->k214_db_usuario);
            $this->k214_db_depart = ($this->k214_db_depart == ""?@$GLOBALS["HTTP_POST_VARS"]["k214_db_depart"]:$this->k214_db_depart);
        } else {
            $this->k214_sequencial = ($this->k214_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["k214_sequencial"]:$this->k214_sequencial);
        }
    }

    public function incluir($k214_sequencial)
    {
        $this->atualizacampos();
        if ($this->k214_placaixa == null) {
            $this->erro_sql = " Campo Código da Planilha não informado.";
            $this->erro_campo = "k214_placaixa";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k214_db_usuario == null) {
            $this->erro_sql = " Campo Código Usuário não informado.";
            $this->erro_campo = "k214_db_usuario";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k214_db_depart == null) {
            $this->erro_sql = " Campo Código Departamento não informado.";
            $this->erro_campo = "k214_db_depart";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($k214_sequencial == "" || $k214_sequencial == null) {
            $result = db_query("select nextval('placaixausu_k214_sequencial_seq')");
            if ($result==false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql   = "Verifique o cadastro da sequencia: placaixausu_k214_sequencial_seq do campo: k214_sequencial";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->k214_sequencial = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from placaixausu_k214_sequencial_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $k214_sequencial)) {
                $this->erro_sql = " Campo k214_sequencial maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->k214_sequencial = $k214_sequencial;
            }
        }
        if (($this->k214_sequencial == null) || ($this->k214_sequencial == "")) {
            $this->erro_sql = " Campo k214_sequencial não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into placaixausu(
                                       k214_sequencial 
                                      ,k214_placaixa 
                                      ,k214_db_usuario 
                                      ,k214_db_depart 
                       )
                values (
                                $this->k214_sequencial 
                               ,$this->k214_placaixa 
                               ,$this->k214_db_usuario 
                               ,$this->k214_db_depart 
                      )";
        $result = db_query($sql);
        if ($result==false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "placaixausu ($this->k214_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "placaixausu já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "placaixausu ($this->k214_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->k214_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            $resaco = $this->sql_record($this->sql_query_file($this->k214_sequencial));
            if (($resaco!=false)||($this->numrows!=0)) {
                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                $resac = db_query("insert into db_acountkey values($acount,1015171,'$this->k214_sequencial','I')");
                $resac = db_query("insert into db_acount values($acount,1011099,1015171,'','".AddSlashes(pg_result($resaco, 0, 'k214_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1011099,1015172,'','".AddSlashes(pg_result($resaco, 0, 'k214_placaixa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1011099,1015173,'','".AddSlashes(pg_result($resaco, 0, 'k214_db_usuario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1011099,1015174,'','".AddSlashes(pg_result($resaco, 0, 'k214_db_depart'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
            }
        }
        return true;
    }

    public function alterar($k214_sequencial = null)
    {
        $this->atualizacampos();
        $sql = " update placaixausu set ";
        $virgula = "";
        if (trim($this->k214_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k214_sequencial"])) {
            $sql  .= $virgula." k214_sequencial = $this->k214_sequencial ";
            $virgula = ",";
            if (trim($this->k214_sequencial) == null) {
                $this->erro_sql = " Campo Sequencial não informado.";
                $this->erro_campo = "k214_sequencial";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k214_placaixa)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k214_placaixa"])) {
            $sql  .= $virgula." k214_placaixa = $this->k214_placaixa ";
            $virgula = ",";
            if (trim($this->k214_placaixa) == null) {
                $this->erro_sql = " Campo Código da Planilha não informado.";
                $this->erro_campo = "k214_placaixa";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k214_db_usuario)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k214_db_usuario"])) {
            $sql  .= $virgula." k214_db_usuario = $this->k214_db_usuario ";
            $virgula = ",";
            if (trim($this->k214_db_usuario) == null) {
                $this->erro_sql = " Campo Código Usuário não informado.";
                $this->erro_campo = "k214_db_usuario";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k214_db_depart)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k214_db_depart"])) {
            $sql  .= $virgula." k214_db_depart = $this->k214_db_depart ";
            $virgula = ",";
            if (trim($this->k214_db_depart) == null) {
                $this->erro_sql = " Campo Código Departamento não informado.";
                $this->erro_campo = "k214_db_depart";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($k214_sequencial!=null) {
            $sql .= " k214_sequencial = $this->k214_sequencial";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            $resaco = $this->sql_record($this->sql_query_file($this->k214_sequencial));
            if ($this->numrows > 0) {
                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {
                      $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                      $acount = pg_result($resac, 0, 0);
                      $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                      $resac = db_query("insert into db_acountkey values($acount,1015171,'$this->k214_sequencial','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k214_sequencial"]) || $this->k214_sequencial != "") {
                        $resac = db_query("insert into db_acount values($acount,1011099,1015171,'".AddSlashes(pg_result($resaco, $conresaco, 'k214_sequencial'))."','$this->k214_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k214_placaixa"]) || $this->k214_placaixa != "") {
                        $resac = db_query("insert into db_acount values($acount,1011099,1015172,'".AddSlashes(pg_result($resaco, $conresaco, 'k214_placaixa'))."','$this->k214_placaixa',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k214_db_usuario"]) || $this->k214_db_usuario != "") {
                        $resac = db_query("insert into db_acount values($acount,1011099,1015173,'".AddSlashes(pg_result($resaco, $conresaco, 'k214_db_usuario'))."','$this->k214_db_usuario',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k214_db_depart"]) || $this->k214_db_depart != "") {
                        $resac = db_query("insert into db_acount values($acount,1011099,1015174,'".AddSlashes(pg_result($resaco, $conresaco, 'k214_db_depart'))."','$this->k214_db_depart',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "placaixausu não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->k214_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "placaixausu não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->k214_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$this->k214_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($k214_sequencial = null, $dbwhere = null)
    {
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            if (empty($dbwhere)) {
                $resaco = $this->sql_record($this->sql_query_file($k214_sequencial));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
            }
            if (($resaco != false) || ($this->numrows!=0)) {
                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {
                    $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                    $resac  = db_query("insert into db_acountkey values($acount,1015171,'$k214_sequencial','E')");
                    $resac  = db_query("insert into db_acount values($acount,1011099,1015171,'','".AddSlashes(pg_result($resaco, $iresaco, 'k214_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1011099,1015172,'','".AddSlashes(pg_result($resaco, $iresaco, 'k214_placaixa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1011099,1015173,'','".AddSlashes(pg_result($resaco, $iresaco, 'k214_db_usuario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1011099,1015174,'','".AddSlashes(pg_result($resaco, $iresaco, 'k214_db_depart'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                }
            }
        }
        $sql = " delete from placaixausu
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($k214_sequencial)) {
                if (!empty($sql2)) {
                     $sql2 .= " and ";
                }
                $sql2 .= " k214_sequencial = $k214_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "placaixausu não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$k214_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "placaixausu não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$k214_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$k214_sequencial;
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
            $this->erro_sql   = "Record Vazio na Tabela:placaixausu";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($k214_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos}";
        $sql .= "  from placaixausu ";
        $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = placaixausu.k214_db_usuario";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = placaixausu.k214_db_depart";
        $sql .= "      inner join placaixa  on  placaixa.k80_codpla = placaixausu.k214_placaixa";
        $sql .= "      inner join db_config  on  db_config.codigo = db_depart.instit";
        $sql .= "      inner join db_config  as a on   a.codigo = placaixa.k80_instit";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($k214_sequencial)) {
                $sql2 .= " where placaixausu.k214_sequencial = $k214_sequencial ";
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

    public function sql_query_file($k214_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos} ";
        $sql .= "  from placaixausu ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($k214_sequencial)) {
                $sql2 .= " where placaixausu.k214_sequencial = $k214_sequencial ";
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
