<?php

class cl_tipoordenacaocgf
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
  public $k240_sequencial = 0;
  public $k240_descricao = null;
  // cria propriedade com as variaveis do arquivo 
  public $campos = "
                 k240_sequencial = int4 = Sequencial 
                 k240_descricao = varchar(30) = Descrição 
                 ";

  public function __construct()
  {
    $this->rotulo = new rotulo("tipoordenacaocgf");
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
      $this->k240_sequencial = ($this->k240_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["k240_sequencial"] : $this->k240_sequencial);
      $this->k240_descricao = ($this->k240_descricao == "" ? @$GLOBALS["HTTP_POST_VARS"]["k240_descricao"] : $this->k240_descricao);
    } else {
      $this->k240_sequencial = ($this->k240_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["k240_sequencial"] : $this->k240_sequencial);
    }
  }

  public function incluir()
  {
    $this->atualizacampos();
    if ($this->k240_descricao == null) {
      $this->erro_sql = " Campo Descrição não informado.";
      $this->erro_campo = "k240_descricao";
      $this->erro_banco = "";
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      return false;
    }
    $sql = "insert into tipoordenacaocgf(
                                       k240_descricao 
                       )
                values (
                                '$this->k240_descricao' 
                      )";
    $result = db_query($sql);
    if ($result == false) {
      $this->erro_banco = str_replace("\n", "", @pg_last_error());
      if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
        $this->erro_sql   = "Tipo de ordenação CGF ($this->k240_sequencial) não Incluído. Inclusão Abortada.";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_banco = "Tipo de ordenação CGF já Cadastrado";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      } else {
        $this->erro_sql   = "Tipo de ordenação CGF ($this->k240_sequencial) não Incluído. Inclusão Abortada.";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      }
      $this->erro_status = "0";
      $this->numrows_incluir = 0;
      return false;
    }
    $this->erro_banco = "";
    $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
    $this->erro_sql .= "Valores : " . $this->k240_sequencial;
    $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
    $this->erro_status = "1";
    $this->numrows_incluir = pg_affected_rows($result);
    return true;
  }

  public function alterar($k240_sequencial = null)
  {
    $this->atualizacampos();
    $sql = " update tipoordenacaocgf set ";
    $virgula = "";
    if (trim($this->k240_sequencial) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k240_sequencial"])) {
      $sql  .= $virgula . " k240_sequencial = $this->k240_sequencial ";
      $virgula = ",";
      if (trim($this->k240_sequencial) == null) {
        $this->erro_sql = " Campo Sequencial não informado.";
        $this->erro_campo = "k240_sequencial";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "0";
        return false;
      }
    }
    if (trim($this->k240_descricao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k240_descricao"])) {
      $sql  .= $virgula . " k240_descricao = '$this->k240_descricao' ";
      $virgula = ",";
      if (trim($this->k240_descricao) == null) {
        $this->erro_sql = " Campo Descrição não informado.";
        $this->erro_campo = "k240_descricao";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "0";
        return false;
      }
    }
    $sql .= " where ";
    if ($k240_sequencial != null) {
      $sql .= " k240_sequencial = $this->k240_sequencial";
    }
    $result = db_query($sql);
    if (!$result) {
      $this->erro_banco = str_replace("\n", "", @pg_last_error());
      $this->erro_sql   = "Tipo de ordenação CGF não Alterado. Alteração Abortada.\\n";
      $this->erro_sql .= "Valores : " . $this->k240_sequencial;
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      $this->numrows_alterar = 0;
      return false;
    } else {
      if (pg_affected_rows($result) == 0) {
        $this->erro_banco = "";
        $this->erro_sql = "Tipo de ordenação CGF não foi Alterado. Alteração Executada.\\n";
        $this->erro_sql .= "Valores : " . $this->k240_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_alterar = 0;
        return true;
      } else {
        $this->erro_banco = "";
        $this->erro_sql = "Alteração efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->k240_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_alterar = pg_affected_rows($result);
        return true;
      }
    }
  }

  public function excluir($k240_sequencial = null, $dbwhere = null)
  {
    $sql = " delete from tipoordenacaocgf
                    where ";
    $sql2 = "";
    if (empty($dbwhere)) {
      if (!empty($k240_sequencial)) {
        if (!empty($sql2)) {
          $sql2 .= " and ";
        }
        $sql2 .= " k240_sequencial = $k240_sequencial ";
      }
    } else {
      $sql2 = $dbwhere;
    }
    $result = db_query($sql . $sql2);
    if ($result == false) {
      $this->erro_banco = str_replace("\n", "", @pg_last_error());
      $this->erro_sql   = "Tipo de ordenação CGF não Excluído. Exclusão Abortada.\\n";
      $this->erro_sql .= "Valores : " . $k240_sequencial;
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      $this->numrows_excluir = 0;
      return false;
    } else {
      if (pg_affected_rows($result) == 0) {
        $this->erro_banco = "";
        $this->erro_sql = "Tipo de ordenação CGF não Encontrado. Exclusão não Efetuada.\\n";
        $this->erro_sql .= "Valores : " . $k240_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_excluir = 0;
        return true;
      } else {
        $this->erro_banco = "";
        $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $k240_sequencial;
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
      $this->erro_sql   = "Record Vazio na Tabela:tipoordenacaocgf";
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      return false;
    }
    return $result;
  }

  public function sql_query($k240_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
  {
    $sql  = "select {$campos}";
    $sql .= "  from tipoordenacaocgf ";
    $sql2 = "";
    if (empty($dbwhere)) {
      if (!empty($k240_sequencial)) {
        $sql2 .= " where tipoordenacaocgf.k240_sequencial = $k240_sequencial ";
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

  public function sql_query_file($k240_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
  {
    $sql  = "select {$campos} ";
    $sql .= "  from tipoordenacaocgf ";
    $sql2 = "";
    if (empty($dbwhere)) {
      if (!empty($k240_sequencial)) {
        $sql2 .= " where tipoordenacaocgf.k240_sequencial = $k240_sequencial ";
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
