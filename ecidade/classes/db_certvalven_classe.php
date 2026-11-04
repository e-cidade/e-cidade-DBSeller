<?php

class cl_certvalven
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
  public $j177_sequencial = 0;
  public $j177_matricula = 0;
  public $j177_emissao = null;
  public $j177_usuario = 0;
  public $j177_titular = null;
  public $j177_processo = null;
  public $j177_observacao = null;
  // cria propriedade com as variaveis do arquivo 
  public $campos = "
                 j177_sequencial = int4 = Sequencial certvalven 
                 j177_matricula = int4 = Matrícula 
                 j177_emissao = varchar(30) = Data e hora da emissão 
                 j177_usuario = int4 = Usuário 
                 j177_titular = varchar(100) = Titular 
                 j177_processo = varchar(50) = Processo 
                 j177_observacao = text = Observação 
                 ";

  public function __construct()
  {
    $this->rotulo = new rotulo("certvalven");
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
      $this->j177_sequencial = ($this->j177_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["j177_sequencial"] : $this->j177_sequencial);
      $this->j177_matricula = ($this->j177_matricula == "" ? @$GLOBALS["HTTP_POST_VARS"]["j177_matricula"] : $this->j177_matricula);
      $this->j177_emissao = ($this->j177_emissao == "" ? @$GLOBALS["HTTP_POST_VARS"]["j177_emissao"] : $this->j177_emissao);
      $this->j177_usuario = ($this->j177_usuario == "" ? @$GLOBALS["HTTP_POST_VARS"]["j177_usuario"] : $this->j177_usuario);
      $this->j177_titular = ($this->j177_titular == "" ? @$GLOBALS["HTTP_POST_VARS"]["j177_titular"] : $this->j177_titular);
      $this->j177_processo = ($this->j177_processo == "" ? @$GLOBALS["HTTP_POST_VARS"]["j177_processo"] : $this->j177_processo);
      $this->j177_observacao = ($this->j177_observacao == "" ? @$GLOBALS["HTTP_POST_VARS"]["j177_observacao"] : $this->j177_observacao);
    } else {
      $this->j177_sequencial = ($this->j177_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["j177_sequencial"] : $this->j177_sequencial);
    }
  }

  public function incluir($j177_sequencial)
  {
    $this->atualizacampos();
    if ($this->j177_matricula == null) {
      $this->erro_sql = " Campo Matrícula não informado.";
      $this->erro_campo = "j177_matricula";
      $this->erro_banco = "";
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      return false;
    }
    if ($this->j177_emissao == null) {
      $this->erro_sql = " Campo Data e hora da emissão não informado.";
      $this->erro_campo = "j177_emissao";
      $this->erro_banco = "";
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      return false;
    }
    if ($this->j177_usuario == null) {
      $this->j177_usuario = "0";
    }
    $this->j177_sequencial = $j177_sequencial;
    if (($this->j177_sequencial == null) || ($this->j177_sequencial == "")) {
      $this->erro_sql = " Campo j177_sequencial não declarado.";
      $this->erro_banco = "Chave Primaria zerada.";
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      return false;
    }
    $sql = "insert into certvalven(
                                       j177_sequencial 
                                      ,j177_matricula 
                                      ,j177_emissao 
                                      ,j177_usuario 
                                      ,j177_titular 
                                      ,j177_processo 
                                      ,j177_observacao 
                       )
                values (
                                $this->j177_sequencial 
                               ,$this->j177_matricula 
                               ,'$this->j177_emissao' 
                               ,$this->j177_usuario 
                               ,'$this->j177_titular' 
                               ,'$this->j177_processo' 
                               ,'$this->j177_observacao' 
                      )";
    $result = db_query($sql);
    if ($result == false) {
      $this->erro_banco = str_replace("\n", "", @pg_last_error());
      if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
        $this->erro_sql   = "Certidão de Valor Venal ($this->j177_sequencial) não Incluído. Inclusão Abortada.";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_banco = "Certidão de Valor Venal já Cadastrado";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      } else {
        $this->erro_sql   = "Certidão de Valor Venal ($this->j177_sequencial) não Incluído. Inclusão Abortada.";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      }
      $this->erro_status = "0";
      $this->numrows_incluir = 0;
      return false;
    }
    $this->erro_banco = "";
    $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
    $this->erro_sql .= "Valores : " . $this->j177_sequencial;
    $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
    $this->erro_status = "1";
    $this->numrows_incluir = pg_affected_rows($result);
    $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
    if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
      && ($lSessaoDesativarAccount === false))) {

      $resaco = $this->sql_record($this->sql_query_file($this->j177_sequencial));
      if (($resaco != false) || ($this->numrows != 0)) {

        $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
        $acount = pg_result($resac, 0, 0);
        $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
        $resac = db_query("insert into db_acountkey values($acount,1015156,'$this->j177_sequencial','I')");
        $resac = db_query("insert into db_acount values($acount,1011097,1015156,'','" . AddSlashes(pg_result($resaco, 0, 'j177_sequencial')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,1011097,1015158,'','" . AddSlashes(pg_result($resaco, 0, 'j177_matricula')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,1011097,1015160,'','" . AddSlashes(pg_result($resaco, 0, 'j177_emissao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,1011097,1015162,'','" . AddSlashes(pg_result($resaco, 0, 'j177_usuario')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,1011097,1015164,'','" . AddSlashes(pg_result($resaco, 0, 'j177_titular')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,1011097,1015166,'','" . AddSlashes(pg_result($resaco, 0, 'j177_processo')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,1011097,1015168,'','" . AddSlashes(pg_result($resaco, 0, 'j177_observacao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
      }
    }
    return true;
  }

  public function alterar($j177_sequencial = null)
  {
    $this->atualizacampos();
    $sql = " update certvalven set ";
    $virgula = "";
    if (trim($this->j177_sequencial) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j177_sequencial"])) {
      $sql  .= $virgula . " j177_sequencial = $this->j177_sequencial ";
      $virgula = ",";
      if (trim($this->j177_sequencial) == null) {
        $this->erro_sql = " Campo Sequencial certvalven não informado.";
        $this->erro_campo = "j177_sequencial";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "0";
        return false;
      }
    }
    if (trim($this->j177_matricula) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j177_matricula"])) {
      $sql  .= $virgula . " j177_matricula = $this->j177_matricula ";
      $virgula = ",";
      if (trim($this->j177_matricula) == null) {
        $this->erro_sql = " Campo Matrícula não informado.";
        $this->erro_campo = "j177_matricula";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "0";
        return false;
      }
    }
    if (trim($this->j177_emissao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j177_emissao"])) {
      $sql  .= $virgula . " j177_emissao = '$this->j177_emissao' ";
      $virgula = ",";
      if (trim($this->j177_emissao) == null) {
        $this->erro_sql = " Campo Data e hora da emissão não informado.";
        $this->erro_campo = "j177_emissao";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "0";
        return false;
      }
    }
    if (trim($this->j177_usuario) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j177_usuario"])) {
      if (trim($this->j177_usuario) == "" && isset($GLOBALS["HTTP_POST_VARS"]["j177_usuario"])) {
        $this->j177_usuario = "0";
      }
      $sql  .= $virgula . " j177_usuario = $this->j177_usuario ";
      $virgula = ",";
    }
    if (trim($this->j177_titular) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j177_titular"])) {
      $sql  .= $virgula . " j177_titular = '$this->j177_titular' ";
      $virgula = ",";
    }
    if (trim($this->j177_processo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j177_processo"])) {
      $sql  .= $virgula . " j177_processo = '$this->j177_processo' ";
      $virgula = ",";
    }
    if (trim($this->j177_observacao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j177_observacao"])) {
      $sql  .= $virgula . " j177_observacao = '$this->j177_observacao' ";
      $virgula = ",";
    }
    $sql .= " where ";
    if ($j177_sequencial != null) {
      $sql .= " j177_sequencial = $this->j177_sequencial";
    }
    $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
    if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
      && ($lSessaoDesativarAccount === false))) {

      $resaco = $this->sql_record($this->sql_query_file($this->j177_sequencial));
      if ($this->numrows > 0) {

        for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

          $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
          $acount = pg_result($resac, 0, 0);
          $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
          $resac = db_query("insert into db_acountkey values($acount,1015156,'$this->j177_sequencial','A')");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j177_sequencial"]) || $this->j177_sequencial != "")
            $resac = db_query("insert into db_acount values($acount,1011097,1015156,'" . AddSlashes(pg_result($resaco, $conresaco, 'j177_sequencial')) . "','$this->j177_sequencial'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j177_matricula"]) || $this->j177_matricula != "")
            $resac = db_query("insert into db_acount values($acount,1011097,1015158,'" . AddSlashes(pg_result($resaco, $conresaco, 'j177_matricula')) . "','$this->j177_matricula'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j177_emissao"]) || $this->j177_emissao != "")
            $resac = db_query("insert into db_acount values($acount,1011097,1015160,'" . AddSlashes(pg_result($resaco, $conresaco, 'j177_emissao')) . "','$this->j177_emissao'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j177_usuario"]) || $this->j177_usuario != "")
            $resac = db_query("insert into db_acount values($acount,1011097,1015162,'" . AddSlashes(pg_result($resaco, $conresaco, 'j177_usuario')) . "','$this->j177_usuario'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j177_titular"]) || $this->j177_titular != "")
            $resac = db_query("insert into db_acount values($acount,1011097,1015164,'" . AddSlashes(pg_result($resaco, $conresaco, 'j177_titular')) . "','$this->j177_titular'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j177_processo"]) || $this->j177_processo != "")
            $resac = db_query("insert into db_acount values($acount,1011097,1015166,'" . AddSlashes(pg_result($resaco, $conresaco, 'j177_processo')) . "','$this->j177_processo'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j177_observacao"]) || $this->j177_observacao != "")
            $resac = db_query("insert into db_acount values($acount,1011097,1015168,'" . AddSlashes(pg_result($resaco, $conresaco, 'j177_observacao')) . "','$this->j177_observacao'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        }
      }
    }
    $result = db_query($sql);
    if (!$result) {
      $this->erro_banco = str_replace("\n", "", @pg_last_error());
      $this->erro_sql   = "Certidão de Valor Venal não Alterado. Alteração Abortada.\\n";
      $this->erro_sql .= "Valores : " . $this->j177_sequencial;
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      $this->numrows_alterar = 0;
      return false;
    } else {
      if (pg_affected_rows($result) == 0) {
        $this->erro_banco = "";
        $this->erro_sql = "Certidão de Valor Venal não foi Alterado. Alteração Executada.\\n";
        $this->erro_sql .= "Valores : " . $this->j177_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_alterar = 0;
        return true;
      } else {
        $this->erro_banco = "";
        $this->erro_sql = "Alteração efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->j177_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_alterar = pg_affected_rows($result);
        return true;
      }
    }
  }

  public function excluir($j177_sequencial = null, $dbwhere = null)
  {
    $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
    if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
      && ($lSessaoDesativarAccount === false))) {

      if (empty($dbwhere)) {

        $resaco = $this->sql_record($this->sql_query_file($j177_sequencial));
      } else {
        $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
      }
      if (($resaco != false) || ($this->numrows != 0)) {

        for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

          $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
          $acount = pg_result($resac, 0, 0);
          $resac  = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
          $resac  = db_query("insert into db_acountkey values($acount,1015156,'$j177_sequencial','E')");
          $resac  = db_query("insert into db_acount values($acount,1011097,1015156,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j177_sequencial')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,1011097,1015158,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j177_matricula')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,1011097,1015160,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j177_emissao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,1011097,1015162,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j177_usuario')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,1011097,1015164,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j177_titular')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,1011097,1015166,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j177_processo')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,1011097,1015168,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j177_observacao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        }
      }
    }
    $sql = " delete from certvalven
                    where ";
    $sql2 = "";
    if (empty($dbwhere)) {
      if (!empty($j177_sequencial)) {
        if (!empty($sql2)) {
          $sql2 .= " and ";
        }
        $sql2 .= " j177_sequencial = $j177_sequencial ";
      }
    } else {
      $sql2 = $dbwhere;
    }
    $result = db_query($sql . $sql2);
    if ($result == false) {
      $this->erro_banco = str_replace("\n", "", @pg_last_error());
      $this->erro_sql   = "Certidão de Valor Venal não Excluído. Exclusão Abortada.\\n";
      $this->erro_sql .= "Valores : " . $j177_sequencial;
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      $this->numrows_excluir = 0;
      return false;
    } else {
      if (pg_affected_rows($result) == 0) {
        $this->erro_banco = "";
        $this->erro_sql = "Certidão de Valor Venal não Encontrado. Exclusão não Efetuada.\\n";
        $this->erro_sql .= "Valores : " . $j177_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_excluir = 0;
        return true;
      } else {
        $this->erro_banco = "";
        $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $j177_sequencial;
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
      $this->erro_sql   = "Record Vazio na Tabela:certvalven";
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      return false;
    }
    return $result;
  }

  public function sql_query($j177_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
  {

    $sql  = "select {$campos}";
    $sql .= "  from certvalven ";
    $sql .= "      inner join iptubase  on  iptubase.j01_matric = certvalven.j177_matricula";
    $sql .= "      left  join db_usuarios  on  db_usuarios.id_usuario = certvalven.j177_usuario";
    $sql .= "      inner join lote  on  lote.j34_idbql = iptubase.j01_idbql";
    $sql .= "      inner join cgm  on  cgm.z01_numcgm = iptubase.j01_numcgm";
    $sql .= "      inner join tipoproprietario  on  tipoproprietario.j163_tipoproprietario = iptubase.j01_tipoproprietario";
    $sql2 = "";
    if (empty($dbwhere)) {
      if (!empty($j177_sequencial)) {
        $sql2 .= " where certvalven.j177_sequencial = $j177_sequencial ";
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

  public function sql_query_file($j177_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
  {

    $sql  = "select {$campos} ";
    $sql .= "  from certvalven ";
    $sql2 = "";
    if (empty($dbwhere)) {
      if (!empty($j177_sequencial)) {
        $sql2 .= " where certvalven.j177_sequencial = $j177_sequencial ";
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

  public function sql_certidoes_matricula($j177_matricula)
  {
    $sql  = " select certvalven.*, iptubase.*, db_usuarios.nome as usuario             ";
    $sql .= " from                                                                       ";
    $sql .= "     certvalven                                                           ";
    $sql .= "     join db_usuarios on certvalven.j177_usuario = db_usuarios.id_usuario ";
    $sql .= "     join iptubase on certvalven.j177_matricula = iptubase.j01_matric     ";
    $sql .= " where                                                                      ";
    $sql .= "     certvalven.j177_matricula = {$j177_matricula}                        ";

    return $sql;
  }

  public function nextValTabela()
  {
    $rsNextVal = db_query("select nextval('certvalven_j177_sequencial_seq')");
    $nextVal = db_utils::fieldsMemory($rsNextVal, 0);

    return $nextVal->nextval;
  }

  public function sql_ultimaCertidao($matricula)
  {
    $sql = "select * from certvalven where j177_matricula = {$matricula} order by j177_sequencial desc limit 1";

    return $sql;
  }

  public function sql_query_certidaoValorVenalTemplate($j177_sequencial)
  {
    $sql  =  "select                                                                                         ";
    $sql .=  "     certvalven.j177_sequencial as certidao,                                                   ";
    $sql .=  "     certvalven.j177_matricula as matricula,                                                   ";
    $sql .=  "     certvalven.j177_observacao as observacao_certidao,                                        ";
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
    $sql .=  "                 j39_matric = certvalven.j177_matricula                                        ";
    $sql .=  "                 and j39_dtdemo is null                                                        ";
    $sql .=  "         ) > 0 then (                                                                          ";
    $sql .=  "             select                                                                            ";
    $sql .=  "                 CONCAT(j88_descricao || ' ' || j14_nome) as rua                               ";
    $sql .=  "             from                                                                              ";
    $sql .=  "                 iptuconstr                                                                    ";
    $sql .=  "                 left join ruas ON ruas.j14_codigo = iptuconstr.j39_codigo                     ";
    $sql .=  "                 left join ruastipo ON ruastipo.j88_codigo = ruas.j14_tipo                     ";
    $sql .=  "             where                                                                             ";
    $sql .=  "                 j39_matric = certvalven.j177_matricula                                        ";
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
    $sql .=  "                 iptubase.j01_matric = certvalven.j177_matricula                               ";
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
    $sql .=  "                 j39_matric = certvalven.j177_matricula                                        ";
    $sql .=  "                 and j39_dtdemo is null                                                        ";
    $sql .=  "         ) > 0 then (                                                                          ";
    $sql .=  "             select                                                                            ";
    $sql .=  "                 j39_numero                                                                    ";
    $sql .=  "             from                                                                              ";
    $sql .=  "                 iptuconstr                                                                    ";
    $sql .=  "             where                                                                             ";
    $sql .=  "                 j39_matric = certvalven.j177_matricula                                        ";
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
    $sql .=  "                 iptubase.j01_matric = certvalven.j177_matricula                               ";
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
    $sql .=  "                 j39_matric = certvalven.j177_matricula                                        ";
    $sql .=  "                 and j39_dtdemo is null                                                        ";
    $sql .=  "         ) > 0 then (                                                                          ";
    $sql .=  "             select                                                                            ";
    $sql .=  "                 j39_compl                                                                     ";
    $sql .=  "             from                                                                              ";
    $sql .=  "                 iptuconstr                                                                    ";
    $sql .=  "             where                                                                             ";
    $sql .=  "                 j39_matric = certvalven.j177_matricula                                        ";
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
    $sql .=  "                 iptubase.j01_matric = certvalven.j177_matricula                               ";
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
    $sql .=  "                                 certvalven.j177_matricula,                                    ";
    $sql .=  "                                 " . db_getsession('DB_anousu') . ",                           ";
    $sql .=  "                                 true,                                                         ";
    $sql .=  "                                 false,                                                        ";
    $sql .=  "                                 false                                                         ";
    $sql .=  "                             )                                                                 ";
    $sql .=  "                     ),                                                                        ";
    $sql .=  "                     10                                                                        ";
    $sql .=  "                 ) / 100                                                                       ";
    $sql .=  "             ) * lote.j34_area                                                                 ";
    $sql .=  "         ),                                                                                    ";
    $sql .=  "         2                                                                                     ";
    $sql .=  "     ) as area_total,                                                                          ";
    $sql .=  "     ROUND((select                                                                             ";
    $sql .=  "         SUM(j39_area)                                                                         ";
    $sql .=  "     from                                                                                      ";
    $sql .=  "         iptuconstr                                                                            ";
    $sql .=  "     where                                                                                     ";
    $sql .=  "         j39_matric = certvalven.j177_matricula), 2) as area_construcao,                       ";
    $sql .=  "     matricobs.j26_obs as observacoes,                                                         ";
    $sql .=  "     ROUND(                                                                                    ";
    $sql .=  "         (                                                                                     ";
    $sql .=  "             select                                                                            ";
    $sql .=  "                 coalesce(j23_vlrter, 0)                                                       ";
    $sql .=  "             from                                                                              ";
    $sql .=  "                 iptucalc                                                                      ";
    $sql .=  "             where                                                                             ";
    $sql .=  "                 j23_matric = certvalven.j177_matricula                                        ";
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
    $sql .=  "                 j22_matric = certvalven.j177_matricula                                        ";
    $sql .=  "             group by j22_matric, j22_anousu                                                   ";
    $sql .=  "             order by                                                                          ";
    $sql .=  "                 j22_anousu desc                                                               ";
    $sql .=  "             limit                                                                             ";
    $sql .=  "                 1                                                                             ";
    $sql .=  "         ), 2                                                                                  ";
    $sql .=  "     ) as vlr_construcao,                                                                      ";

    $sql .= "     (                                                                                                                   ";
    $sql .= "         select * FROM (                                                                                                 ";
    $sql .= "             select CASE                                                                                                 ";
    $sql .= "                  WHEN status_vencido = 1 THEN 'EXISTEM DÉBITOS VENCIDOS'                                                ";
    $sql .= "                  WHEN status_vencido = 2 THEN 'EXISTEM DÉBITOS A VENCER'                                                ";
    $sql .= "                  WHEN status_vencido = 3 THEN 'NÃO EXISTEM DÉBITOS'                                                     ";
    $sql .= "             end AS status_vencido                                                                                       ";
    $sql .= "         FROM   (SELECT DISTINCT coalesce(status_vencido,3) as status_vencido                                            ";
    $sql .= "                 FROM   (SELECT CASE                                                                                     ";
    $sql .= "                              WHEN ( ( k00_dtvenc :: date ) < CURRENT_DATE ) THEN 1                                      ";
    $sql .= "                              WHEN ( ( k00_dtvenc :: date ) >= CURRENT_DATE ) THEN 2                                     ";
    $sql .= "                              end AS status_vencido                                                                      ";
    $sql .= "                         FROM   iptubase                                                                                 ";
    $sql .= "                         LEFT JOIN arrematric ON j01_matric = arrematric.k00_matric                                      ";
    $sql .= "                         LEFT JOIN arrecad    ON arrematric.k00_numpre = arrecad.k00_numpre                              ";
    $sql .= "                         LEFT JOIN arretipo   ON arretipo.k00_tipo = arrecad.k00_tipo                                    ";
    $sql .= "                         LEFT JOIN cadtipo	ON cadtipo.k03_tipo = arretipo.k03_tipo                                       ";
    $sql .= "                         WHERE  iptubase.j01_matric = certvalven.j177_matricula order by 1 limit 1 ) AS status_debito    ";
    $sql .= "               ) AS dv ORDER  BY status_vencido) as x                                                                    ";
    $sql .= "         LIMIT  1                                                                                                        ";
    $sql .= "     ) as debitos,                                                                                                       ";

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
    $sql .=  "             db_usuarios.id_usuario = certvalven.j177_usuario                                  ";
    $sql .=  "     ) as nome_servidor,                                                                       ";
    $sql .=  "     fc_dataextenso(certvalven.j177_emissao :: date) as data_emissao_extenso,                  ";
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
    $sql .=  "             j48_matric = certvalven.j177_matricula                                            ";
    $sql .=  "             and j31_grupo = 7200                                                              ";
    $sql .=  "         order by j31_descr                                                                    ";
    $sql .=  "         limit 1                                                                               ";
    $sql .=  "     ) as situacao_constr                                                                      ";
    $sql .=  " from                                                                                          ";
    $sql .=  "     certvalven                                                                                ";
    $sql .=  "     left join protprocesso on certvalven.j177_processo::integer = protprocesso.p58_codproc::integer ";
    $sql .=  "     left join iptubase ON iptubase.j01_matric = certvalven.j177_matricula                     ";
    $sql .=  "     left join iptubaseregimovel on j04_matric = j177_matricula                                ";
    $sql .=  "     left join lote ON lote.j34_idbql = iptubase.j01_idbql                                     ";
    $sql .=  "     left join loteloc ON loteloc.j06_idbql = lote.j34_idbql                                   ";
    $sql .=  "     left join bairro ON bairro.j13_codi = lote.j34_bairro                                     ";
    $sql .=  "     left join matricobs on matricobs.j26_matric = iptubase.j01_matric                         ";
    $sql .=  "     left join cgm on cgm.z01_numcgm = iptubase.j01_numcgm                                     ";
    $sql .=  "     left join promitente on j41_matric = certvalven.j177_matricula and j41_tipopro = true     ";
    $sql .=  "     left join cgm cgm_promitente on cgm_promitente.z01_numcgm = promitente.j41_numcgm         ";
    $sql .=  " where                                                                                         ";
    $sql .=  "     j177_sequencial = {$j177_sequencial}                                                      ";

    return $sql;
  }
}
