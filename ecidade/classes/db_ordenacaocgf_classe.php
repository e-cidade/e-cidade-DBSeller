<?php

class cl_ordenacaocgf
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
  public $k241_sequencial = 0;
  public $k241_grupo = 0;
  public $k241_tipo = 0;
  // cria propriedade com as variaveis do arquivo 
  public $campos = "
                 k241_sequencial = int4 = Sequencial 
                 k241_grupo = int4 = Grupo 
                 k241_tipo = int4 =  
                 ";

  public function __construct()
  {
    $this->rotulo = new rotulo("ordenacaocgf");
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
      $this->k241_sequencial = ($this->k241_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["k241_sequencial"] : $this->k241_sequencial);
      $this->k241_grupo = ($this->k241_grupo == "" ? @$GLOBALS["HTTP_POST_VARS"]["k241_grupo"] : $this->k241_grupo);
      $this->k241_tipo = ($this->k241_tipo == "" ? @$GLOBALS["HTTP_POST_VARS"]["k241_tipo"] : $this->k241_tipo);
    } else {
      $this->k241_sequencial = ($this->k241_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["k241_sequencial"] : $this->k241_sequencial);
    }
  }

  public function incluir()
  {
    $this->atualizacampos();
    if ($this->k241_grupo == null) {
      $this->erro_sql = " Campo Grupo não informado.";
      $this->erro_campo = "k241_grupo";
      $this->erro_banco = "";
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      return false;
    }
    if ($this->k241_tipo == null) {
      $this->erro_sql = " Campo  não informado.";
      $this->erro_campo = "k241_tipo";
      $this->erro_banco = "";
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      return false;
    }
    $sql = "insert into ordenacaocgf(
                                       k241_grupo 
                                      ,k241_tipo 
                       )
                values (
                               $this->k241_grupo 
                               ,$this->k241_tipo 
                      )";
    $result = db_query($sql);
    if ($result == false) {
      $this->erro_banco = str_replace("\n", "", @pg_last_error());
      if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
        $this->erro_sql   = "Ordenação CGF ($this->k241_sequencial) não Incluído. Inclusão Abortada.";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_banco = "Ordenação CGF já Cadastrado";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      } else {
        $this->erro_sql   = "Ordenação CGF ($this->k241_sequencial) não Incluído. Inclusão Abortada.";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      }
      $this->erro_status = "0";
      $this->numrows_incluir = 0;
      return false;
    }
    $this->erro_banco = "";
    $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
    $this->erro_sql .= "Valores : " . $this->k241_sequencial;
    $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
    $this->erro_status = "1";
    $this->numrows_incluir = pg_affected_rows($result);
    return true;
  }

  public function alterar($k241_sequencial = null)
  {
    $this->atualizacampos();
    $sql = " update ordenacaocgf set ";
    $virgula = "";
    if (trim($this->k241_sequencial) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k241_sequencial"])) {
      $sql  .= $virgula . " k241_sequencial = $this->k241_sequencial ";
      $virgula = ",";
      if (trim($this->k241_sequencial) == null) {
        $this->erro_sql = " Campo Sequencial não informado.";
        $this->erro_campo = "k241_sequencial";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "0";
        return false;
      }
    }
    if (trim($this->k241_grupo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k241_grupo"])) {
      $sql  .= $virgula . " k241_grupo = $this->k241_grupo ";
      $virgula = ",";
      if (trim($this->k241_grupo) == null) {
        $this->erro_sql = " Campo Grupo não informado.";
        $this->erro_campo = "k241_grupo";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "0";
        return false;
      }
    }
    if (trim($this->k241_tipo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k241_tipo"])) {
      $sql  .= $virgula . " k241_tipo = $this->k241_tipo ";
      $virgula = ",";
      if (trim($this->k241_tipo) == null) {
        $this->erro_sql = " Campo  não informado.";
        $this->erro_campo = "k241_tipo";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "0";
        return false;
      }
    }
    $sql .= " where ";
    if ($k241_sequencial != null) {
      $sql .= " k241_sequencial = $this->k241_sequencial";
    }
    $result = db_query($sql);
    if (!$result) {
      $this->erro_banco = str_replace("\n", "", @pg_last_error());
      $this->erro_sql   = "Ordenação CGF não Alterado. Alteração Abortada.\\n";
      $this->erro_sql .= "Valores : " . $this->k241_sequencial;
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      $this->numrows_alterar = 0;
      return false;
    } else {
      if (pg_affected_rows($result) == 0) {
        $this->erro_banco = "";
        $this->erro_sql = "Ordenação CGF não foi Alterado. Alteração Executada.\\n";
        $this->erro_sql .= "Valores : " . $this->k241_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_alterar = 0;
        return true;
      } else {
        $this->erro_banco = "";
        $this->erro_sql = "Alteração efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->k241_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_alterar = pg_affected_rows($result);
        return true;
      }
    }
  }

  public function excluir($k241_sequencial = null, $dbwhere = null)
  {
    $sql = " delete from ordenacaocgf
                    where ";
    $sql2 = "";
    if (empty($dbwhere)) {
      if (!empty($k241_sequencial)) {
        if (!empty($sql2)) {
          $sql2 .= " and ";
        }
        $sql2 .= " k241_sequencial = $k241_sequencial ";
      }
    } else {
      $sql2 = $dbwhere;
    }
    $result = db_query($sql . $sql2);
    if ($result == false) {
      $this->erro_banco = str_replace("\n", "", @pg_last_error());
      $this->erro_sql   = "Ordenação CGF não Excluído. Exclusão Abortada.\\n";
      $this->erro_sql .= "Valores : " . $k241_sequencial;
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      $this->numrows_excluir = 0;
      return false;
    } else {
      if (pg_affected_rows($result) == 0) {
        $this->erro_banco = "";
        $this->erro_sql = "Ordenação CGF não Encontrado. Exclusão não Efetuada.\\n";
        $this->erro_sql .= "Valores : " . $k241_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_excluir = 0;
        return true;
      } else {
        $this->erro_banco = "";
        $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $k241_sequencial;
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
      $this->erro_sql   = "Record Vazio na Tabela:ordenacaocgf";
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      return false;
    }
    return $result;
  }

  public function sql_query($k241_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
  {

    $sql  = "select {$campos}";
    $sql .= "  from ordenacaocgf ";
    $sql .= "      inner join cadtipo  on  cadtipo.k03_tipo = ordenacaocgf.k241_grupo";
    $sql .= "      inner join tipoordenacaocgf  on  tipoordenacaocgf.k240_sequencial = ordenacaocgf.k241_tipo";
    $sql2 = "";
    if (empty($dbwhere)) {
      if (!empty($k241_sequencial)) {
        $sql2 .= " where ordenacaocgf.k241_sequencial = $k241_sequencial ";
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

  public function sql_query_file($k241_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
  {

    $sql  = "select {$campos} ";
    $sql .= "  from ordenacaocgf ";
    $sql2 = "";
    if (empty($dbwhere)) {
      if (!empty($k241_sequencial)) {
        $sql2 .= " where ordenacaocgf.k241_sequencial = $k241_sequencial ";
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
