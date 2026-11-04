<?php

class cl_custasparcelamento
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
  public $ar53_sequencial = 0;
  public $ar53_processoforo = 0;
  public $ar53_inicial = 0;
  public $ar53_taxa = 0;
  public $ar53_parcelamento = 0;
  public $ar53_parcelas = 0;
  public $ar53_usuario = 0;
  public $ar53_data = null;
  // cria propriedade com as variaveis do arquivo 
  public $campos = "
                 ar53_sequencial = int4 = Sequencial 
                 ar53_processoforo = int4 = Processo do foro 
                 ar53_inicial = int4 = Inicial 
                 ar53_taxa = int4 = Taxa 
                 ar53_parcelamento = int4 = Parcelamento 
                 ar53_parcelas = int4 = Número de parcelas 
                 ar53_usuario = int4 = Usuário 
                 ar53_data = varchar(11) = Data 
                 ";

  public function __construct()
  {
    $this->rotulo = new rotulo("custasparcelamento");
    $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
  }

  public function erro($mostra, $retorna)
  {
    if (($this->erro_status == "0") || ($mostra == true && $this->erro_status != null)) {
      echo "<script>alert(\"" . $this->erro_msg . "\")</script>";
      if ($retorna == true) {
        echo "<script>location.href='" . $this->pagina_retorno . "'</script>";
      }
    }
  }

  public function atualizacampos($exclusao = false)
  {
    if ($exclusao == false) {
      $this->ar53_sequencial = ($this->ar53_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["ar53_sequencial"] : $this->ar53_sequencial);
      $this->ar53_processoforo = ($this->ar53_processoforo == "" ? @$GLOBALS["HTTP_POST_VARS"]["ar53_processoforo"] : $this->ar53_processoforo);
      $this->ar53_inicial = ($this->ar53_inicial == "" ? @$GLOBALS["HTTP_POST_VARS"]["ar53_inicial"] : $this->ar53_inicial);
      $this->ar53_taxa = ($this->ar53_taxa == "" ? @$GLOBALS["HTTP_POST_VARS"]["ar53_taxa"] : $this->ar53_taxa);
      $this->ar53_parcelamento = ($this->ar53_parcelamento == "" ? @$GLOBALS["HTTP_POST_VARS"]["ar53_parcelamento"] : $this->ar53_parcelamento);
      $this->ar53_parcelas = ($this->ar53_parcelas == "" ? @$GLOBALS["HTTP_POST_VARS"]["ar53_parcelas"] : $this->ar53_parcelas);
      $this->ar53_usuario = ($this->ar53_usuario == "" ? @$GLOBALS["HTTP_POST_VARS"]["ar53_usuario"] : $this->ar53_usuario);
      $this->ar53_data = ($this->ar53_data == "" ? @$GLOBALS["HTTP_POST_VARS"]["ar53_data"] : $this->ar53_data);
    } else {
      $this->ar53_sequencial = ($this->ar53_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["ar53_sequencial"] : $this->ar53_sequencial);
    }
  }

  public function incluir($ar53_sequencial)
  {
    $this->atualizacampos();
    if ($this->ar53_processoforo == null) {
      $this->ar53_processoforo = "0";
    }
    if ($this->ar53_inicial == null) {
      $this->ar53_inicial = "0";
    }
    if ($this->ar53_taxa == null) {
      $this->erro_sql = " Campo Taxa não informado.";
      $this->erro_campo = "ar53_taxa";
      $this->erro_banco = "";
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      return false;
    }
    if ($this->ar53_parcelamento == null) {
      $this->ar53_parcelamento = "null";
    }
    if ($this->ar53_parcelas == null) {
      $this->ar53_parcelas = "0";
    }
    if ($this->ar53_usuario == null) {
      $this->erro_sql = " Campo Usuário não informado.";
      $this->erro_campo = "ar53_usuario";
      $this->erro_banco = "";
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      return false;
    }
    if ($this->ar53_data == null) {
      $this->erro_sql = " Campo Data não informado.";
      $this->erro_campo = "ar53_data";
      $this->erro_banco = "";
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      return false;
    }
    $this->ar53_sequencial = $ar53_sequencial;
    if (($this->ar53_sequencial == null) || ($this->ar53_sequencial == "")) {
      $this->erro_sql = " Campo ar53_sequencial não declarado.";
      $this->erro_banco = "Chave Primaria zerada.";
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      return false;
    }
    $sql = "insert into custasparcelamento(
                                       ar53_sequencial 
                                      ,ar53_processoforo 
                                      ,ar53_inicial 
                                      ,ar53_taxa 
                                      ,ar53_parcelamento 
                                      ,ar53_parcelas 
                                      ,ar53_usuario 
                                      ,ar53_data 
                       )
                values (
                                $this->ar53_sequencial 
                               ,$this->ar53_processoforo 
                               ,$this->ar53_inicial 
                               ,$this->ar53_taxa 
                               ,$this->ar53_parcelamento 
                               ,$this->ar53_parcelas 
                               ,$this->ar53_usuario 
                               ,'$this->ar53_data' 
                      )";
    $result = db_query($sql);
    if ($result == false) {
      $this->erro_banco = str_replace("\n", "", @pg_last_error());
      if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
        $this->erro_sql   = "Parcelamento de custas ($this->ar53_sequencial) não Incluído. Inclusão Abortada.";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_banco = "Parcelamento de custas já Cadastrado";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      } else {
        $this->erro_sql   = "Parcelamento de custas ($this->ar53_sequencial) não Incluído. Inclusão Abortada.";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      }
      $this->erro_status = "0";
      $this->numrows_incluir = 0;
      return false;
    }
    $this->erro_banco = "";
    $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
    $this->erro_sql .= "Valores : " . $this->ar53_sequencial;
    $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
    $this->erro_status = "1";
    $this->numrows_incluir = pg_affected_rows($result);
    $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
    if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
      && ($lSessaoDesativarAccount === false))) {

      $resaco = $this->sql_record($this->sql_query_file($this->ar53_sequencial));
      if (($resaco != false) || ($this->numrows != 0)) {

        $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
        $acount = pg_result($resac, 0, 0);
        $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
        $resac = db_query("insert into db_acountkey values($acount,1015241,'$this->ar53_sequencial','I')");
        $resac = db_query("insert into db_acount values($acount,1011115,1015241,'','" . AddSlashes(pg_result($resaco, 0, 'ar53_sequencial')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,1011115,1015242,'','" . AddSlashes(pg_result($resaco, 0, 'ar53_processoforo')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,1011115,1015243,'','" . AddSlashes(pg_result($resaco, 0, 'ar53_inicial')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,1011115,1015244,'','" . AddSlashes(pg_result($resaco, 0, 'ar53_taxa')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,1011115,1015305,'','" . AddSlashes(pg_result($resaco, 0, 'ar53_parcelamento')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,1011115,1015303,'','" . AddSlashes(pg_result($resaco, 0, 'ar53_parcelas')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,1011115,1015304,'','" . AddSlashes(pg_result($resaco, 0, 'ar53_usuario')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,1011115,1015302,'','" . AddSlashes(pg_result($resaco, 0, 'ar53_data')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
      }
    }
    return true;
  }

  public function alterar($ar53_sequencial = null)
  {
    $this->atualizacampos();
    $sql = " update custasparcelamento set ";
    $virgula = "";
    if (trim($this->ar53_sequencial) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ar53_sequencial"])) {
      $sql  .= $virgula . " ar53_sequencial = $this->ar53_sequencial ";
      $virgula = ",";
      if (trim($this->ar53_sequencial) == null) {
        $this->erro_sql = " Campo Sequencial não informado.";
        $this->erro_campo = "ar53_sequencial";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "0";
        return false;
      }
    }
    if (trim($this->ar53_processoforo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ar53_processoforo"])) {
      if (trim($this->ar53_processoforo) == "" && isset($GLOBALS["HTTP_POST_VARS"]["ar53_processoforo"])) {
        $this->ar53_processoforo = "0";
      }
      $sql  .= $virgula . " ar53_processoforo = $this->ar53_processoforo ";
      $virgula = ",";
    }
    if (trim($this->ar53_inicial) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ar53_inicial"])) {
      if (trim($this->ar53_inicial) == "" && isset($GLOBALS["HTTP_POST_VARS"]["ar53_inicial"])) {
        $this->ar53_inicial = "0";
      }
      $sql  .= $virgula . " ar53_inicial = $this->ar53_inicial ";
      $virgula = ",";
    }
    if (trim($this->ar53_taxa) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ar53_taxa"])) {
      $sql  .= $virgula . " ar53_taxa = $this->ar53_taxa ";
      $virgula = ",";
      if (trim($this->ar53_taxa) == null) {
        $this->erro_sql = " Campo Taxa não informado.";
        $this->erro_campo = "ar53_taxa";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "0";
        return false;
      }
    }
    if (trim($this->ar53_parcelamento) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ar53_parcelamento"])) {
      if (trim($this->ar53_parcelamento) == "" && isset($GLOBALS["HTTP_POST_VARS"]["ar53_parcelamento"])) {
        $this->ar53_parcelamento = "0";
      }
      $sql  .= $virgula . " ar53_parcelamento = $this->ar53_parcelamento ";
      $virgula = ",";
    }
    if (trim($this->ar53_parcelas) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ar53_parcelas"])) {
      if (trim($this->ar53_parcelas) == "" && isset($GLOBALS["HTTP_POST_VARS"]["ar53_parcelas"])) {
        $this->ar53_parcelas = "0";
      }
      $sql  .= $virgula . " ar53_parcelas = $this->ar53_parcelas ";
      $virgula = ",";
    }
    if (trim($this->ar53_usuario) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ar53_usuario"])) {
      $sql  .= $virgula . " ar53_usuario = $this->ar53_usuario ";
      $virgula = ",";
      if (trim($this->ar53_usuario) == null) {
        $this->erro_sql = " Campo Usuário não informado.";
        $this->erro_campo = "ar53_usuario";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "0";
        return false;
      }
    }
    if (trim($this->ar53_data) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ar53_data"])) {
      $sql  .= $virgula . " ar53_data = '$this->ar53_data' ";
      $virgula = ",";
      if (trim($this->ar53_data) == null) {
        $this->erro_sql = " Campo Data não informado.";
        $this->erro_campo = "ar53_data";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "0";
        return false;
      }
    }
    $sql .= " where ";
    if ($ar53_sequencial != null) {
      $sql .= " ar53_sequencial = $this->ar53_sequencial";
    }
    $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
    if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
      && ($lSessaoDesativarAccount === false))) {

      $resaco = $this->sql_record($this->sql_query_file($this->ar53_sequencial));
      if ($this->numrows > 0) {

        for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

          $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
          $acount = pg_result($resac, 0, 0);
          $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
          $resac = db_query("insert into db_acountkey values($acount,1015241,'$this->ar53_sequencial','A')");
          if (isset($GLOBALS["HTTP_POST_VARS"]["ar53_sequencial"]) || $this->ar53_sequencial != "")
            $resac = db_query("insert into db_acount values($acount,1011115,1015241,'" . AddSlashes(pg_result($resaco, $conresaco, 'ar53_sequencial')) . "','$this->ar53_sequencial'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["ar53_processoforo"]) || $this->ar53_processoforo != "")
            $resac = db_query("insert into db_acount values($acount,1011115,1015242,'" . AddSlashes(pg_result($resaco, $conresaco, 'ar53_processoforo')) . "','$this->ar53_processoforo'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["ar53_inicial"]) || $this->ar53_inicial != "")
            $resac = db_query("insert into db_acount values($acount,1011115,1015243,'" . AddSlashes(pg_result($resaco, $conresaco, 'ar53_inicial')) . "','$this->ar53_inicial'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["ar53_taxa"]) || $this->ar53_taxa != "")
            $resac = db_query("insert into db_acount values($acount,1011115,1015244,'" . AddSlashes(pg_result($resaco, $conresaco, 'ar53_taxa')) . "','$this->ar53_taxa'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["ar53_parcelamento"]) || $this->ar53_parcelamento != "")
            $resac = db_query("insert into db_acount values($acount,1011115,1015305,'" . AddSlashes(pg_result($resaco, $conresaco, 'ar53_parcelamento')) . "','$this->ar53_parcelamento'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["ar53_parcelas"]) || $this->ar53_parcelas != "")
            $resac = db_query("insert into db_acount values($acount,1011115,1015303,'" . AddSlashes(pg_result($resaco, $conresaco, 'ar53_parcelas')) . "','$this->ar53_parcelas'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["ar53_usuario"]) || $this->ar53_usuario != "")
            $resac = db_query("insert into db_acount values($acount,1011115,1015304,'" . AddSlashes(pg_result($resaco, $conresaco, 'ar53_usuario')) . "','$this->ar53_usuario'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["ar53_data"]) || $this->ar53_data != "")
            $resac = db_query("insert into db_acount values($acount,1011115,1015302,'" . AddSlashes(pg_result($resaco, $conresaco, 'ar53_data')) . "','$this->ar53_data'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        }
      }
    }
    $result = db_query($sql);
    if (!$result) {
      $this->erro_banco = str_replace("\n", "", @pg_last_error());
      $this->erro_sql   = "Parcelamento de custas não Alterado. Alteração Abortada.\\n";
      $this->erro_sql .= "Valores : " . $this->ar53_sequencial;
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      $this->numrows_alterar = 0;
      return false;
    } else {
      if (pg_affected_rows($result) == 0) {
        $this->erro_banco = "";
        $this->erro_sql = "Parcelamento de custas não foi Alterado. Alteração Executada.\\n";
        $this->erro_sql .= "Valores : " . $this->ar53_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_alterar = 0;
        return true;
      } else {
        $this->erro_banco = "";
        $this->erro_sql = "Alteração efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->ar53_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_alterar = pg_affected_rows($result);
        return true;
      }
    }
  }

  public function excluir($ar53_sequencial = null, $dbwhere = null)
  {
    $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
    if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
      && ($lSessaoDesativarAccount === false))) {

      if (empty($dbwhere)) {

        $resaco = $this->sql_record($this->sql_query_file($ar53_sequencial));
      } else {
        $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
      }
      if (($resaco != false) || ($this->numrows != 0)) {

        for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

          $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
          $acount = pg_result($resac, 0, 0);
          $resac  = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
          $resac  = db_query("insert into db_acountkey values($acount,1015241,'$ar53_sequencial','E')");
          $resac  = db_query("insert into db_acount values($acount,1011115,1015241,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ar53_sequencial')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,1011115,1015242,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ar53_processoforo')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,1011115,1015243,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ar53_inicial')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,1011115,1015244,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ar53_taxa')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,1011115,1015305,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ar53_parcelamento')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,1011115,1015303,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ar53_parcelas')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,1011115,1015304,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ar53_usuario')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,1011115,1015302,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ar53_data')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        }
      }
    }
    $sql = " delete from custasparcelamento
                    where ";
    $sql2 = "";
    if (empty($dbwhere)) {
      if (!empty($ar53_sequencial)) {
        if (!empty($sql2)) {
          $sql2 .= " and ";
        }
        $sql2 .= " ar53_sequencial = $ar53_sequencial ";
      }
    } else {
      $sql2 = $dbwhere;
    }
    $result = db_query($sql . $sql2);
    if ($result == false) {
      $this->erro_banco = str_replace("\n", "", @pg_last_error());
      $this->erro_sql   = "Parcelamento de custas não Excluído. Exclusão Abortada.\\n";
      $this->erro_sql .= "Valores : " . $ar53_sequencial;
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      $this->numrows_excluir = 0;
      return false;
    } else {
      if (pg_affected_rows($result) == 0) {
        $this->erro_banco = "";
        $this->erro_sql = "Parcelamento de custas não Encontrado. Exclusão não Efetuada.\\n";
        $this->erro_sql .= "Valores : " . $ar53_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_excluir = 0;
        return true;
      } else {
        $this->erro_banco = "";
        $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $ar53_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
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
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      return false;
    }
    $this->numrows = pg_num_rows($result);
    if ($this->numrows == 0) {
      $this->erro_banco = "";
      $this->erro_sql   = "Record Vazio na Tabela:custasparcelamento";
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      return false;
    }
    return $result;
  }

  public function sql_query($ar53_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
  {

    $sql  = "select {$campos}";
    $sql .= "  from custasparcelamento ";
    $sql .= "      left  join inicial  on  inicial.v50_inicial = custasparcelamento.ar53_inicial";
    $sql .= "      left  join processoforo  on  processoforo.v70_sequencial = custasparcelamento.ar53_processoforo";
    $sql .= "      inner join taxa  on  taxa.ar36_sequencial = custasparcelamento.ar53_taxa";
    $sql2 = "";
    if (empty($dbwhere)) {
      if (!empty($ar53_sequencial)) {
        $sql2 .= " where custasparcelamento.ar53_sequencial = $ar53_sequencial ";
      }
    } else if (!empty($dbwhere)) {
      $sql2 = " where $dbwhere";
    }
    $sql .= $sql2;
    if (!empty($ordem)) {
      $sql .= " order by {$ordem}";
    }
    return $sql;
  }

  public function sql_query_file($ar53_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
  {

    $sql  = "select {$campos} ";
    $sql .= "  from custasparcelamento ";
    $sql2 = "";
    if (empty($dbwhere)) {
      if (!empty($ar53_sequencial)) {
        $sql2 .= " where custasparcelamento.ar53_sequencial = $ar53_sequencial ";
      }
    } else if (!empty($dbwhere)) {
      $sql2 = " where $dbwhere";
    }
    $sql .= $sql2;
    if (!empty($ordem)) {
      $sql .= " order by {$ordem}";
    }
    return $sql;
  }
}
