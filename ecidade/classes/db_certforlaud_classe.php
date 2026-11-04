<?php

class cl_certforlaud
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
  public $j178_sequencial = 0;
  public $j178_matricula = 0;
  public $j178_emissao = null;
  public $j178_usuario = 0;
  public $j178_titular = null;
  public $j178_processo = null;
  public $j178_observacao = null;
  // cria propriedade com as variaveis do arquivo 
  public $campos = "
                 j178_sequencial = int4 = Sequencial certforlaud 
                 j178_matricula = int4 = Matrícula 
                 j178_emissao = varchar(30) = Data e hora da emissão 
                 j178_usuario = int4 = Usuário 
                 j178_titular = varchar(100) = Titular 
                 j178_processo = varchar(50) = Processo 
                 j178_observacao = text = Observação 
                 ";

  public function __construct()
  {
    $this->rotulo = new rotulo("certforlaud");
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
      $this->j178_sequencial = ($this->j178_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["j178_sequencial"] : $this->j178_sequencial);
      $this->j178_matricula = ($this->j178_matricula == "" ? @$GLOBALS["HTTP_POST_VARS"]["j178_matricula"] : $this->j178_matricula);
      $this->j178_emissao = ($this->j178_emissao == "" ? @$GLOBALS["HTTP_POST_VARS"]["j178_emissao"] : $this->j178_emissao);
      $this->j178_usuario = ($this->j178_usuario == "" ? @$GLOBALS["HTTP_POST_VARS"]["j178_usuario"] : $this->j178_usuario);
      $this->j178_titular = ($this->j178_titular == "" ? @$GLOBALS["HTTP_POST_VARS"]["j178_titular"] : $this->j178_titular);
      $this->j178_processo = ($this->j178_processo == "" ? @$GLOBALS["HTTP_POST_VARS"]["j178_processo"] : $this->j178_processo);
      $this->j178_observacao = ($this->j178_observacao == "" ? @$GLOBALS["HTTP_POST_VARS"]["j178_observacao"] : $this->j178_observacao);
    } else {
      $this->j178_sequencial = ($this->j178_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["j178_sequencial"] : $this->j178_sequencial);
    }
  }

  public function incluir($j178_sequencial)
  {
    $this->atualizacampos();
    if ($this->j178_matricula == null) {
      $this->erro_sql = " Campo Matrícula não informado.";
      $this->erro_campo = "j178_matricula";
      $this->erro_banco = "";
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      return false;
    }
    if ($this->j178_emissao == null) {
      $this->erro_sql = " Campo Data e hora da emissão não informado.";
      $this->erro_campo = "j178_emissao";
      $this->erro_banco = "";
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      return false;
    }
    if ($this->j178_usuario == null) {
      $this->j178_usuario = "0";
    }
    $this->j178_sequencial = $j178_sequencial;
    if (($this->j178_sequencial == null) || ($this->j178_sequencial == "")) {
      $this->erro_sql = " Campo j178_sequencial não declarado.";
      $this->erro_banco = "Chave Primaria zerada.";
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      return false;
    }
    $sql = "insert into certforlaud(
                                       j178_sequencial 
                                      ,j178_matricula 
                                      ,j178_emissao 
                                      ,j178_usuario 
                                      ,j178_titular 
                                      ,j178_processo 
                                      ,j178_observacao 
                       )
                values (
                                $this->j178_sequencial 
                               ,$this->j178_matricula 
                               ,'$this->j178_emissao' 
                               ,$this->j178_usuario 
                               ,'$this->j178_titular' 
                               ,'$this->j178_processo' 
                               ,'$this->j178_observacao' 
                      )";
    $result = db_query($sql);
    if ($result == false) {
      $this->erro_banco = str_replace("\n", "", @pg_last_error());
      if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
        $this->erro_sql   = "Certidao de Foro e Laudêmio ($this->j178_sequencial) não Incluído. Inclusão Abortada.";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_banco = "Certidao de Foro e Laudêmio já Cadastrado";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      } else {
        $this->erro_sql   = "Certidao de Foro e Laudêmio ($this->j178_sequencial) não Incluído. Inclusão Abortada.";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      }
      $this->erro_status = "0";
      $this->numrows_incluir = 0;
      return false;
    }
    $this->erro_banco = "";
    $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
    $this->erro_sql .= "Valores : " . $this->j178_sequencial;
    $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
    $this->erro_status = "1";
    $this->numrows_incluir = pg_affected_rows($result);
    $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
    if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
      && ($lSessaoDesativarAccount === false))) {

      $resaco = $this->sql_record($this->sql_query_file($this->j178_sequencial));
      if (($resaco != false) || ($this->numrows != 0)) {

        $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
        $acount = pg_result($resac, 0, 0);
        $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
        $resac = db_query("insert into db_acountkey values($acount,1015157,'$this->j178_sequencial','I')");
        $resac = db_query("insert into db_acount values($acount,1011098,1015157,'','" . AddSlashes(pg_result($resaco, 0, 'j178_sequencial')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,1011098,1015159,'','" . AddSlashes(pg_result($resaco, 0, 'j178_matricula')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,1011098,1015161,'','" . AddSlashes(pg_result($resaco, 0, 'j178_emissao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,1011098,1015163,'','" . AddSlashes(pg_result($resaco, 0, 'j178_usuario')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,1011098,1015165,'','" . AddSlashes(pg_result($resaco, 0, 'j178_titular')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,1011098,1015167,'','" . AddSlashes(pg_result($resaco, 0, 'j178_processo')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,1011098,1015169,'','" . AddSlashes(pg_result($resaco, 0, 'j178_observacao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
      }
    }
    return true;
  }

  public function alterar($j178_sequencial = null)
  {
    $this->atualizacampos();
    $sql = " update certforlaud set ";
    $virgula = "";
    if (trim($this->j178_sequencial) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j178_sequencial"])) {
      $sql  .= $virgula . " j178_sequencial = $this->j178_sequencial ";
      $virgula = ",";
      if (trim($this->j178_sequencial) == null) {
        $this->erro_sql = " Campo Sequencial certforlaud não informado.";
        $this->erro_campo = "j178_sequencial";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "0";
        return false;
      }
    }
    if (trim($this->j178_matricula) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j178_matricula"])) {
      $sql  .= $virgula . " j178_matricula = $this->j178_matricula ";
      $virgula = ",";
      if (trim($this->j178_matricula) == null) {
        $this->erro_sql = " Campo Matrícula não informado.";
        $this->erro_campo = "j178_matricula";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "0";
        return false;
      }
    }
    if (trim($this->j178_emissao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j178_emissao"])) {
      $sql  .= $virgula . " j178_emissao = '$this->j178_emissao' ";
      $virgula = ",";
      if (trim($this->j178_emissao) == null) {
        $this->erro_sql = " Campo Data e hora da emissão não informado.";
        $this->erro_campo = "j178_emissao";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "0";
        return false;
      }
    }
    if (trim($this->j178_usuario) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j178_usuario"])) {
      if (trim($this->j178_usuario) == "" && isset($GLOBALS["HTTP_POST_VARS"]["j178_usuario"])) {
        $this->j178_usuario = "0";
      }
      $sql  .= $virgula . " j178_usuario = $this->j178_usuario ";
      $virgula = ",";
    }
    if (trim($this->j178_titular) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j178_titular"])) {
      $sql  .= $virgula . " j178_titular = '$this->j178_titular' ";
      $virgula = ",";
    }
    if (trim($this->j178_processo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j178_processo"])) {
      $sql  .= $virgula . " j178_processo = '$this->j178_processo' ";
      $virgula = ",";
    }
    if (trim($this->j178_observacao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j178_observacao"])) {
      $sql  .= $virgula . " j178_observacao = '$this->j178_observacao' ";
      $virgula = ",";
    }
    $sql .= " where ";
    if ($j178_sequencial != null) {
      $sql .= " j178_sequencial = $this->j178_sequencial";
    }
    $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
    if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
      && ($lSessaoDesativarAccount === false))) {

      $resaco = $this->sql_record($this->sql_query_file($this->j178_sequencial));
      if ($this->numrows > 0) {

        for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

          $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
          $acount = pg_result($resac, 0, 0);
          $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
          $resac = db_query("insert into db_acountkey values($acount,1015157,'$this->j178_sequencial','A')");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j178_sequencial"]) || $this->j178_sequencial != "")
            $resac = db_query("insert into db_acount values($acount,1011098,1015157,'" . AddSlashes(pg_result($resaco, $conresaco, 'j178_sequencial')) . "','$this->j178_sequencial'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j178_matricula"]) || $this->j178_matricula != "")
            $resac = db_query("insert into db_acount values($acount,1011098,1015159,'" . AddSlashes(pg_result($resaco, $conresaco, 'j178_matricula')) . "','$this->j178_matricula'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j178_emissao"]) || $this->j178_emissao != "")
            $resac = db_query("insert into db_acount values($acount,1011098,1015161,'" . AddSlashes(pg_result($resaco, $conresaco, 'j178_emissao')) . "','$this->j178_emissao'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j178_usuario"]) || $this->j178_usuario != "")
            $resac = db_query("insert into db_acount values($acount,1011098,1015163,'" . AddSlashes(pg_result($resaco, $conresaco, 'j178_usuario')) . "','$this->j178_usuario'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j178_titular"]) || $this->j178_titular != "")
            $resac = db_query("insert into db_acount values($acount,1011098,1015165,'" . AddSlashes(pg_result($resaco, $conresaco, 'j178_titular')) . "','$this->j178_titular'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j178_processo"]) || $this->j178_processo != "")
            $resac = db_query("insert into db_acount values($acount,1011098,1015167,'" . AddSlashes(pg_result($resaco, $conresaco, 'j178_processo')) . "','$this->j178_processo'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j178_observacao"]) || $this->j178_observacao != "")
            $resac = db_query("insert into db_acount values($acount,1011098,1015169,'" . AddSlashes(pg_result($resaco, $conresaco, 'j178_observacao')) . "','$this->j178_observacao'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        }
      }
    }
    $result = db_query($sql);
    if (!$result) {
      $this->erro_banco = str_replace("\n", "", @pg_last_error());
      $this->erro_sql   = "Certidao de Foro e Laudêmio não Alterado. Alteração Abortada.\\n";
      $this->erro_sql .= "Valores : " . $this->j178_sequencial;
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      $this->numrows_alterar = 0;
      return false;
    } else {
      if (pg_affected_rows($result) == 0) {
        $this->erro_banco = "";
        $this->erro_sql = "Certidao de Foro e Laudêmio não foi Alterado. Alteração Executada.\\n";
        $this->erro_sql .= "Valores : " . $this->j178_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_alterar = 0;
        return true;
      } else {
        $this->erro_banco = "";
        $this->erro_sql = "Alteração efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->j178_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_alterar = pg_affected_rows($result);
        return true;
      }
    }
  }

  public function excluir($j178_sequencial = null, $dbwhere = null)
  {
    $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
    if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
      && ($lSessaoDesativarAccount === false))) {

      if (empty($dbwhere)) {

        $resaco = $this->sql_record($this->sql_query_file($j178_sequencial));
      } else {
        $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
      }
      if (($resaco != false) || ($this->numrows != 0)) {

        for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

          $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
          $acount = pg_result($resac, 0, 0);
          $resac  = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
          $resac  = db_query("insert into db_acountkey values($acount,1015157,'$j178_sequencial','E')");
          $resac  = db_query("insert into db_acount values($acount,1011098,1015157,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j178_sequencial')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,1011098,1015159,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j178_matricula')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,1011098,1015161,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j178_emissao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,1011098,1015163,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j178_usuario')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,1011098,1015165,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j178_titular')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,1011098,1015167,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j178_processo')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,1011098,1015169,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j178_observacao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        }
      }
    }
    $sql = " delete from certforlaud
                    where ";
    $sql2 = "";
    if (empty($dbwhere)) {
      if (!empty($j178_sequencial)) {
        if (!empty($sql2)) {
          $sql2 .= " and ";
        }
        $sql2 .= " j178_sequencial = $j178_sequencial ";
      }
    } else {
      $sql2 = $dbwhere;
    }
    $result = db_query($sql . $sql2);
    if ($result == false) {
      $this->erro_banco = str_replace("\n", "", @pg_last_error());
      $this->erro_sql   = "Certidao de Foro e Laudêmio não Excluído. Exclusão Abortada.\\n";
      $this->erro_sql .= "Valores : " . $j178_sequencial;
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      $this->numrows_excluir = 0;
      return false;
    } else {
      if (pg_affected_rows($result) == 0) {
        $this->erro_banco = "";
        $this->erro_sql = "Certidao de Foro e Laudêmio não Encontrado. Exclusão não Efetuada.\\n";
        $this->erro_sql .= "Valores : " . $j178_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_excluir = 0;
        return true;
      } else {
        $this->erro_banco = "";
        $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $j178_sequencial;
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
      $this->erro_sql   = "Record Vazio na Tabela:certforlaud";
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      return false;
    }
    return $result;
  }

  public function sql_query($j178_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
  {

    $sql  = "select {$campos}";
    $sql .= "  from certforlaud ";
    $sql .= "      inner join iptubase  on  iptubase.j01_matric = certforlaud.j178_matricula";
    $sql .= "      left  join db_usuarios  on  db_usuarios.id_usuario = certforlaud.j178_usuario";
    $sql .= "      inner join lote  on  lote.j34_idbql = iptubase.j01_idbql";
    $sql .= "      inner join cgm  on  cgm.z01_numcgm = iptubase.j01_numcgm";
    $sql .= "      inner join tipoproprietario  on  tipoproprietario.j163_tipoproprietario = iptubase.j01_tipoproprietario";
    $sql2 = "";
    if (empty($dbwhere)) {
      if (!empty($j178_sequencial)) {
        $sql2 .= " where certforlaud.j178_sequencial = $j178_sequencial ";
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

  public function sql_query_file($j178_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
  {

    $sql  = "select {$campos} ";
    $sql .= "  from certforlaud ";
    $sql2 = "";
    if (empty($dbwhere)) {
      if (!empty($j178_sequencial)) {
        $sql2 .= " where certforlaud.j178_sequencial = $j178_sequencial ";
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

  public function sql_certidoes_matricula($j178_matricula)
  {
    $sql  = " select certforlaud.*, iptubase.*, db_usuarios.nome as usuario             ";
    $sql .= " from                                                                       ";
    $sql .= "     certforlaud                                                           ";
    $sql .= "     join db_usuarios on certforlaud.j178_usuario = db_usuarios.id_usuario ";
    $sql .= "     join iptubase on certforlaud.j178_matricula = iptubase.j01_matric     ";
    $sql .= " where                                                                      ";
    $sql .= "     certforlaud.j178_matricula = {$j178_matricula}                        ";

    return $sql;
  }

  public function nextValTabela()
  {
    $rsNextVal = db_query("select nextval('certforlaud_j178_sequencial_seq')");
    $nextVal = db_utils::fieldsMemory($rsNextVal, 0);

    return $nextVal->nextval;
  }

  public function sql_ultimaCertidao($matricula)
  {
    $sql = "select * from certforlaud where j178_matricula = {$matricula} order by j178_sequencial desc limit 1";

    return $sql;
  }

  public function sql_query_certidaoForoLaudemioTemplate($j178_sequencial)
  {
    $sql  =  "select                                                                                         ";
    $sql .=  "     certforlaud.j178_sequencial as certidao,                                                  ";
    $sql .=  "     certforlaud.j178_matricula as matricula,                                                  ";
    $sql .=  "     certforlaud.j178_observacao as observacao_certidao,                                       ";
    $sql .=  "     iptubaseregimovel.j04_matricregimo as matricula_ri,                                       ";
    $sql .=  "     TO_CHAR(iptubase.j01_datacad, 'DD/MM/YYYY') as data_inclusao,                             ";
    $sql .= "      case when (protprocesso.p58_numero is not null and protprocesso.p58_numero <> '' and protprocesso.p58_ano is not null) ";
    $sql .= "        then concat(protprocesso.p58_numero || '/' || protprocesso.p58_ano::text)                                            ";
    $sql .= "        else coalesce(protprocesso.p58_ano::text, '')                                                                        ";
    $sql .= "      end as processo,                                                                                                       ";
    $sql .=  "     CONCAT(                                                                                   ";
    $sql .=  "         lote.j34_setor || '/' || lote.j34_quadra || '/' || lote.j34_lote                      ";
    $sql .=  "     ) AS sql,                                                                                 ";
    $sql .=  "     CONCAT(                                                                                   ";
    $sql .=  "         loteloc.j06_setorloc || '/' || loteloc.j06_quadraloc || '/' || loteloc.j06_lote       ";
    $sql .=  "     ) as pql,                                                                                 ";
    $sql .=  "     case                                                                                      ";
    $sql .=  "         when (                                                                                ";
    $sql .=  "             select                                                                            ";
    $sql .=  "                 count(*)                                                                      ";
    $sql .=  "             from                                                                              ";
    $sql .=  "                 iptuconstr                                                                    ";
    $sql .=  "             where                                                                             ";
    $sql .=  "                 j39_matric = certforlaud.j178_matricula                                       ";
    $sql .=  "                 and j39_dtdemo is null                                                        ";
    $sql .=  "         ) > 0 then (                                                                          ";
    $sql .=  "             select                                                                            ";
    $sql .=  "                 CONCAT(j88_descricao || ' ' || j14_nome) as rua                               ";
    $sql .=  "             from                                                                              ";
    $sql .=  "                 iptuconstr                                                                    ";
    $sql .=  "                 left join ruas ON ruas.j14_codigo = iptuconstr.j39_codigo                     ";
    $sql .=  "                 left join ruastipo ON ruastipo.j88_codigo = ruas.j14_tipo                     ";
    $sql .=  "             where                                                                             ";
    $sql .=  "                 j39_matric = certforlaud.j178_matricula                                       ";
    $sql .=  "                 and j39_dtdemo is null                                                        ";
    $sql .=  "             order by                                                                          ";
    $sql .=  "                 j39_idprinc                                                                   ";
    $sql .=  "             limit                                                                             ";
    $sql .=  "                 1                                                                             ";
    $sql .=  "         )                                                                                     ";
    $sql .=  "         else (                                                                                ";
    $sql .=  "             select                                                                            ";
    $sql .=  "                 CONCAT(j88_descricao || ' ' || ruas.j14_nome)                                 ";
    $sql .=  "             from                                                                              ";
    $sql .=  "                 iptubase                                                                      ";
    $sql .=  "                 inner join lote on j34_idbql = j01_idbql                                      ";
    $sql .=  "                 left outer join testpri on j49_idbql = j01_idbql                              ";
    $sql .=  "                 left outer join testadanumero on testadanumero.j15_idbql = testpri.j49_idbql  ";
    $sql .=  "                 and testadanumero.j15_face = testpri.j49_face                                 ";
    $sql .=  "                 left join ruas ON ruas.j14_codigo = testpri.j49_codigo                        ";
    $sql .=  "                 left join ruastipo ON ruastipo.j88_codigo = ruas.j14_tipo                     ";
    $sql .=  "             where                                                                             ";
    $sql .=  "                 iptubase.j01_matric = certforlaud.j178_matricula                              ";
    $sql .=  "             limit                                                                             ";
    $sql .=  "                 1                                                                             ";
    $sql .=  "         )                                                                                     ";
    $sql .=  "     end as logradouro,                                                                        ";
    $sql .=  "     case                                                                                      ";
    $sql .=  "         when (                                                                                ";
    $sql .=  "             select                                                                            ";
    $sql .=  "                 count(*)                                                                      ";
    $sql .=  "             from                                                                              ";
    $sql .=  "                 iptuconstr                                                                    ";
    $sql .=  "             where                                                                             ";
    $sql .=  "                 j39_matric = certforlaud.j178_matricula                                       ";
    $sql .=  "                 and j39_dtdemo is null                                                        ";
    $sql .=  "         ) > 0 then (                                                                          ";
    $sql .=  "             select                                                                            ";
    $sql .=  "                 j39_numero                                                                    ";
    $sql .=  "             from                                                                              ";
    $sql .=  "                 iptuconstr                                                                    ";
    $sql .=  "             where                                                                             ";
    $sql .=  "                 j39_matric = certforlaud.j178_matricula                                       ";
    $sql .=  "                 and j39_dtdemo is null                                                        ";
    $sql .=  "             order by                                                                          ";
    $sql .=  "                 j39_idprinc                                                                   ";
    $sql .=  "             limit                                                                             ";
    $sql .=  "                 1                                                                             ";
    $sql .=  "         ) :: text                                                                             ";
    $sql .=  "         else (                                                                                ";
    $sql .=  "             select                                                                            ";
    $sql .=  "                 testadanumero.j15_compl                                                       ";
    $sql .=  "             from                                                                              ";
    $sql .=  "                 iptubase                                                                      ";
    $sql .=  "                 inner join lote on j34_idbql = j01_idbql                                      ";
    $sql .=  "                 left outer join testpri on j49_idbql = j01_idbql                              ";
    $sql .=  "                 left outer join testadanumero on testadanumero.j15_idbql = testpri.j49_idbql  ";
    $sql .=  "                 and testadanumero.j15_face = testpri.j49_face                                 ";
    $sql .=  "             where                                                                             ";
    $sql .=  "                 iptubase.j01_matric = certforlaud.j178_matricula                              ";
    $sql .=  "             limit                                                                             ";
    $sql .=  "                 1                                                                             ";
    $sql .=  "         ) :: text                                                                             ";
    $sql .=  "     end as numero,                                                                            ";
    $sql .=  "     case                                                                                      ";
    $sql .=  "         when (                                                                                ";
    $sql .=  "             select                                                                            ";
    $sql .=  "                 count(*)                                                                      ";
    $sql .=  "             from                                                                              ";
    $sql .=  "                 iptuconstr                                                                    ";
    $sql .=  "             where                                                                             ";
    $sql .=  "                 j39_matric = certforlaud.j178_matricula                                       ";
    $sql .=  "                 and j39_dtdemo is null                                                        ";
    $sql .=  "         ) > 0 then (                                                                          ";
    $sql .=  "             select                                                                            ";
    $sql .=  "                 j39_compl                                                                     ";
    $sql .=  "             from                                                                              ";
    $sql .=  "                 iptuconstr                                                                    ";
    $sql .=  "             where                                                                             ";
    $sql .=  "                 j39_matric = certforlaud.j178_matricula                                       ";
    $sql .=  "                 and j39_dtdemo is null                                                        ";
    $sql .=  "             order by                                                                          ";
    $sql .=  "                 j39_idprinc                                                                   ";
    $sql .=  "             limit                                                                             ";
    $sql .=  "                 1                                                                             ";
    $sql .=  "         ) :: text                                                                             ";
    $sql .=  "         else (                                                                                ";
    $sql .=  "             select                                                                            ";
    $sql .=  "                 testadanumero.j15_compl                                                       ";
    $sql .=  "             from                                                                              ";
    $sql .=  "                 iptubase                                                                      ";
    $sql .=  "                 inner join lote on j34_idbql = j01_idbql                                      ";
    $sql .=  "                 left outer join testpri on j49_idbql = j01_idbql                              ";
    $sql .=  "                 left outer join testadanumero on testadanumero.j15_idbql = testpri.j49_idbql  ";
    $sql .=  "                 and testadanumero.j15_face = testpri.j49_face                                 ";
    $sql .=  "             where                                                                             ";
    $sql .=  "                 iptubase.j01_matric = certforlaud.j178_matricula                              ";
    $sql .=  "             limit                                                                             ";
    $sql .=  "                 1                                                                             ";
    $sql .=  "         ) :: text                                                                             ";
    $sql .=  "     end as complemento,                                                                       ";
    $sql .=  "     bairro.j13_descr as bairro,                                                               ";
    $sql .=  "     cgm.z01_numcgm as cgm_proprietario,                                                       ";
    $sql .=  "     coalesce(                                                                                 ";
    $sql .=  "         cgm.z01_nomecomple,                                                                   ";
    $sql .=  "         cgm.z01_nome                                                                          ";
    $sql .=  "     ) as nome_proprietario,                                                                   ";
    $sql .=  "     cgm.z01_cgccpf as cpf_cnpj_proprietario,                                                  ";
    $sql .=  "     cgm_promitente.z01_numcgm as cgm_promitente,                                              ";
    $sql .=  "     coalesce(cgm_promitente.z01_nomecomple, cgm_promitente.z01_nome) as nome_promitente,      ";
    $sql .=  "     cgm_promitente.z01_cgccpf as cpf_cnpj_promitente,                                         ";
    $sql .=  "     round(                                                                                    ";
    $sql .=  "         (                                                                                     ";
    $sql .=  "             (                                                                                 ";
    $sql .=  "                 round(                                                                        ";
    $sql .=  "                     (                                                                         ";
    $sql .=  "                         select                                                                ";
    $sql .=  "                             rnfracao                                                          ";
    $sql .=  "                         from                                                                  ";
    $sql .=  "                             fc_iptu_fracionalote(                                             ";
    $sql .=  "                                 certforlaud.j178_matricula,                                   ";
    $sql .=  "                                 " . db_getsession('DB_anousu') . ",                           ";
    $sql .=  "                                 true,                                                         ";
    $sql .=  "                                 false,                                                        ";
    $sql .=  "                                 false                                                         ";
    $sql .=  "                             )                                                                 ";
    $sql .=  "                     ),                                                                        ";
    $sql .=  "                     10                                                                        ";
    $sql .=  "                  ) * lote.j34_area                                                            ";
    $sql .=  "             ) / 100                                                                           ";
    $sql .=  "         ),                                                                                    ";
    $sql .=  "         2                                                                                     ";
    $sql .=  "     ) as area_total,                                                                          ";
    $sql .=  "     ROUND((select                                                                             ";
    $sql .=  "         SUM(j39_area)                                                                         ";
    $sql .=  "     from                                                                                      ";
    $sql .=  "         iptuconstr                                                                            ";
    $sql .=  "     where                                                                                     ";
    $sql .=  "         j39_matric = certforlaud.j178_matricula), 2) as area_construcao,                      ";
    $sql .=  "     matricobs.j26_obs as observacoes,                                                         ";
    $sql .=  "     ROUND(                                                                                    ";
    $sql .=  "         (                                                                                     ";
    $sql .=  "             select                                                                            ";
    $sql .=  "                 coalesce(j23_vlrter, 0)                                                       ";
    $sql .=  "             from                                                                              ";
    $sql .=  "                 iptucalc                                                                      ";
    $sql .=  "             where                                                                             ";
    $sql .=  "                 j23_matric = certforlaud.j178_matricula                                       ";
    $sql .=  "             order by                                                                          ";
    $sql .=  "                 j23_anousu desc                                                               ";
    $sql .=  "             limit                                                                             ";
    $sql .=  "                 1                                                                             ";
    $sql .=  "         ), 2                                                                                  ";
    $sql .=  "     ) as vlr_terreno,                                                                         ";
    $sql .=  "     ROUND(                                                                                    ";
    $sql .=  "         (                                                                                     ";
    $sql .=  "             select                                                                            ";
    $sql .=  "                 coalesce(sum(j22_valor), 0)                                                   ";
    $sql .=  "             from                                                                              ";
    $sql .=  "                 iptucale                                                                      ";
    $sql .=  "             where                                                                             ";
    $sql .=  "                 j22_matric = certforlaud.j178_matricula                                       ";
    $sql .=  "             group by j22_matric, j22_anousu                                                   ";
    $sql .=  "             order by                                                                          ";
    $sql .=  "                 j22_anousu desc                                                               ";
    $sql .=  "             limit                                                                             ";
    $sql .=  "                 1                                                                             ";
    $sql .=  "         ), 2                                                                                  ";
    $sql .=  "     ) as vlr_construcao,                                                                      ";

    $sql .= "     (                                                                                          ";
    $sql .= "         select                                                                                 ";
    $sql .= "             case                                                                               ";
    $sql .= "                 when status_vencido = 1 then 'EXISTEM DÉBITO VENCIDOS'                         ";
    $sql .= "                 when status_vencido = 2 then 'EXISTEM DÉBITOS A VENCER'                        ";
    $sql .= "                 else 'NÃO EXISTEM DÉBITOS'                                                     ";
    $sql .= "             end as status_vencido                                                              ";
    $sql .= "         from                                                                                   ";
    $sql .= "             (                                                                                  ";
    $sql .= "                 select                                                                         ";
    $sql .= "                     distinct status_vencido                                                    ";
    $sql .= "                 from                                                                           ";
    $sql .= "                     (                                                                          ";
    $sql .= "                         select                                                                 ";
    $sql .= "                             case                                                               ";
    $sql .= "                                 when ((k00_dtvenc :: date) < CURRENT_DATE) then 1              ";
    $sql .= "                                 when ((k00_dtvenc :: date) >= CURRENT_DATE) then 2             ";
    $sql .= "                             end as status_vencido                                              ";
    $sql .= "                         from                                                                   ";
    $sql .= "                             arrematric                                                         ";
    $sql .= "                             inner join arrecad on arrematric.k00_numpre = arrecad.k00_numpre   ";
    $sql .= "                             inner join arretipo on arretipo.k00_tipo = arrecad.k00_tipo        ";
    $sql .= "                             inner join cadtipo on cadtipo.k03_tipo = arretipo.k03_tipo         ";
    $sql .= "                         where                                                                  ";
    $sql .= "                             arrematric.k00_matric = certforlaud.j178_matricula                 ";
    $sql .= "                     ) as status_debito                                                         ";
    $sql .= "                 union                                                                          ";
    $sql .= "                 select                                                                         ";
    $sql .= "                     3 as status_vencido                                                        ";
    $sql .= "             ) as dv                                                                            ";
    $sql .= "         order by                                                                               ";
    $sql .= "             status_vencido                                                                     ";
    $sql .= "         limit                                                                                  ";
    $sql .= "             1                                                                                  ";
    $sql .= "     ) as debitos,                                                                              ";

    $sql .= "      case when (protprocesso.p58_numero is not null and protprocesso.p58_numero <> '' and protprocesso.p58_ano is not null) ";
    $sql .= "        then concat(protprocesso.p58_numero || '/' || protprocesso.p58_ano::text)                                            ";
    $sql .= "        else coalesce(protprocesso.p58_ano::text, '')                                                                        ";
    $sql .= "      end as protocolo,                                                                                                      ";
    $sql .=  "     (                                                                                         ";
    $sql .=  "         select                                                                                ";
    $sql .=  "             nome                                                                              ";
    $sql .=  "         from                                                                                  ";
    $sql .=  "             db_usuarios                                                                       ";
    $sql .=  "         where                                                                                 ";
    $sql .=  "             db_usuarios.id_usuario = certforlaud.j178_usuario                                 ";
    $sql .=  "     ) as nome_servidor,                                                                       ";
    $sql .=  "     fc_dataextenso(certforlaud.j178_emissao :: date) as data_emissao_extenso,                 ";
    $sql .=  "     (                                                                                         ";
    $sql .=  "         select                                                                                ";
    $sql .=  "             iptucalc.j23_anousu                                                               ";
    $sql .=  "         from                                                                                  ";
    $sql .=  "             iptucalc                                                                          ";
    $sql .=  "         where                                                                                 ";
    $sql .=  "             j23_matric = 1234                                                                 ";
    $sql .=  "         order by                                                                              ";
    $sql .=  "             j23_anousu desc                                                                   ";
    $sql .=  "         limit                                                                                 ";
    $sql .=  "             1                                                                                 ";
    $sql .=  "     ) as exercicio,                                                                           ";
    $sql .=  "     iptubaseregimovel.j04_quadraregimo as quadra_regimo,                                      ";
    $sql .=  "     iptubaseregimovel.j04_loteregimo as  lote_regimo,                                         ";
    $sql .=  "     (                                                                                         ";
    $sql .=  "         select                                                                                ";
    $sql .=  "             j31_descr                                                                         ";
    $sql .=  "         from                                                                                  ";
    $sql .=  "             carconstr                                                                         ";
    $sql .=  "             inner join caracter ON caracter.j31_codigo = carconstr.j48_caract                 ";
    $sql .=  "         where                                                                                 ";
    $sql .=  "             j48_matric = certforlaud.j178_matricula                                           ";
    $sql .=  "             and j31_grupo = 7200                                                              ";
    $sql .=  "         order by j31_descr                                                                    ";
    $sql .=  "         limit 1                                                                               ";
    $sql .=  "     ) as situacao_constr                                                                      ";
    $sql .=  " from                                                                                          ";
    $sql .=  "     certforlaud                                                                               ";
    $sql .=  "     left join protprocesso on certforlaud.j178_processo::integer = protprocesso.p58_codproc::integer ";
    $sql .=  "     left join iptubase ON iptubase.j01_matric = certforlaud.j178_matricula                    ";
    $sql .=  "     left join iptubaseregimovel on j04_matric = j178_matricula                                ";
    $sql .=  "     left join lote ON lote.j34_idbql = iptubase.j01_idbql                                     ";
    $sql .=  "     left join loteloc ON loteloc.j06_idbql = lote.j34_idbql                                   ";
    $sql .=  "     left join bairro ON bairro.j13_codi = lote.j34_bairro                                     ";
    $sql .=  "     left join matricobs on matricobs.j26_matric = iptubase.j01_matric                         ";
    $sql .=  "     left join cgm on cgm.z01_numcgm = iptubase.j01_numcgm                                     ";
    $sql .=  "     left join promitente on j41_matric = certforlaud.j178_matricula and j41_tipopro = true    ";
    $sql .=  "     left join cgm cgm_promitente on cgm_promitente.z01_numcgm = promitente.j41_numcgm         ";
    $sql .=  " where                                                                                         ";
    $sql .=  "     j178_sequencial = {$j178_sequencial}                                                      ";

    return $sql;
  }
}
