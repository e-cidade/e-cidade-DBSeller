<?php

class cl_fis_fiscalprocrec
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
    public $y45_codtipo = 0;
    public $y45_receit = 0;
    public $y45_valor = 0;
    public $y45_descr = null;
    public $y45_vlrfixo = 'f';
    public $y45_tipo = 0;
    public $y45_percentual = 'f';
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 y45_codtipo = int8 = Código da Procedência
                 y45_receit = int4 = Receita
                 y45_valor = float8 = Valor notificação
                 y45_descr = varchar(50) = Descr tipo notificação
                 y45_vlrfixo = bool = Usa vlr fixo
                 y45_tipo = int4 = Forma de cálculo
                 y45_percentual = bool = Usa percentual
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("fis_fiscalprocrec");
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
    }

    public function erro($mostra, $retorna)
    {
        if (($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )) {
            echo "<script>alert(\"".$this->erro_msg."\")</script>";
            if ($retorna) {
                echo "<script>location.href='".$this->pagina_retorno."'</script>";
            }
        }
    }

    public function atualizacampos($exclusao = false)
    {
        if (!$exclusao) {
            $this->y45_codtipo = ($this->y45_codtipo == ""?@$GLOBALS["HTTP_POST_VARS"]["y45_codtipo"]:$this->y45_codtipo);
            $this->y45_receit = ($this->y45_receit == ""?@$GLOBALS["HTTP_POST_VARS"]["y45_receit"]:$this->y45_receit);
            $this->y45_valor = ($this->y45_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["y45_valor"]:$this->y45_valor);
            $this->y45_descr = ($this->y45_descr == ""?@$GLOBALS["HTTP_POST_VARS"]["y45_descr"]:$this->y45_descr);
            $this->y45_vlrfixo = ($this->y45_vlrfixo == "f"?@$GLOBALS["HTTP_POST_VARS"]["y45_vlrfixo"]:$this->y45_vlrfixo);
            $this->y45_tipo = ($this->y45_tipo == ""?@$GLOBALS["HTTP_POST_VARS"]["y45_tipo"]:$this->y45_tipo);
            $this->y45_percentual = ($this->y45_percentual == "f"?@$GLOBALS["HTTP_POST_VARS"]["y45_percentual"]:$this->y45_percentual);
        } else {
            $this->y45_codtipo = ($this->y45_codtipo == ""?@$GLOBALS["HTTP_POST_VARS"]["y45_codtipo"]:$this->y45_codtipo);
            $this->y45_receit = ($this->y45_receit == ""?@$GLOBALS["HTTP_POST_VARS"]["y45_receit"]:$this->y45_receit);
        }
    }

    public function incluir($y45_codtipo = null, $y45_receit = null)
    {
        $this->atualizacampos();
        if ($this->y45_valor == null) {
            $this->y45_valor = "0";
        }
        if ($this->y45_vlrfixo == null) {
            $this->y45_vlrfixo = "false";
        }
        if ($this->y45_percentual == null) {
            $this->y45_percentual = "f";
        }
        if(empty($this->y45_tipo)) {
            $this->y45_tipo = "null";
        }
        $this->y45_codtipo = $y45_codtipo;
        $this->y45_receit = $y45_receit;
        if (($this->y45_codtipo == null) || ($this->y45_codtipo == "")) {
            $this->erro_sql = " Campo y45_codtipo não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if (($this->y45_receit == null) || ($this->y45_receit == "")) {
            $this->erro_sql = " Campo y45_receit não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into fis_fiscalprocrec(
                                       y45_codtipo
                                      ,y45_receit
                                      ,y45_valor
                                      ,y45_descr
                                      ,y45_vlrfixo
                                      ,y45_tipo
                                      ,y45_percentual
                       )
                values (
                                $this->y45_codtipo
                               ,$this->y45_receit
                               ,$this->y45_valor
                               ,'$this->y45_descr'
                               ,'$this->y45_vlrfixo'
                               ,$this->y45_tipo
                               ,'$this->y45_percentual'
                      )";
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "fiscalprocrec ($this->y45_codtipo."-".$this->y45_receit) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "fiscalprocrec já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "fiscalprocrec ($this->y45_codtipo."-".$this->y45_receit) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->y45_codtipo."-".$this->y45_receit;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        return true;
    }

    public function alterar($y45_codtipo = null, $y45_receit = null)
    {
        $this->atualizacampos();
        $sql = " update fis_fiscalprocrec set ";
        $virgula = "";
        if (trim($this->y45_valor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y45_valor"])) {
            if (trim($this->y45_valor)=="" && isset($GLOBALS["HTTP_POST_VARS"]["y45_valor"])) {
                $this->y45_valor = "0" ;
            }
            $sql  .= $virgula." y45_valor = $this->y45_valor ";
            $virgula = ",";
        }
        if (trim($this->y45_descr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y45_descr"])) {
            $sql  .= $virgula." y45_descr = '$this->y45_descr' ";
            $virgula = ",";
        }
        if (trim($this->y45_vlrfixo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y45_vlrfixo"])) {
            $sql  .= $virgula." y45_vlrfixo = '$this->y45_vlrfixo' ";
            $virgula = ",";
        }
        if (trim($this->y45_tipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y45_tipo"])) {
            if (trim($this->y45_tipo)=="" && isset($GLOBALS["HTTP_POST_VARS"]["y45_tipo"])) {
                $this->y45_tipo = "0" ;
            }
            $sql  .= $virgula." y45_tipo = $this->y45_tipo ";
            $virgula = ",";
        }
        if (trim($this->y45_percentual)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y45_percentual"])) {
            $sql  .= $virgula." y45_percentual = '$this->y45_percentual' ";
            $virgula = ",";
        }
        $sql .= " where ";
        if ($y45_codtipo!=null) {
            $sql .= " y45_codtipo = $this->y45_codtipo";
        }
        if ($y45_receit!=null) {
            $sql .= " and  y45_receit = $this->y45_receit";
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "fiscalprocrec não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->y45_codtipo."-".$this->y45_receit;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "fiscalprocrec não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->y45_codtipo."-".$this->y45_receit;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$this->y45_codtipo."-".$this->y45_receit;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($y45_codtipo = null, $y45_receit = null, $dbwhere = null)
    {
        $sql = " delete from fiscalizacao.fis_fiscalprocrec where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($y45_codtipo)) {
                if (!empty($sql2)) {
                     $sql2 .= " and ";
                }
                $sql2 .= " y45_codtipo = $y45_codtipo ";
            }
            if (!empty($y45_receit)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " y45_receit = $y45_receit ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "fiscalprocrec não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y45_codtipo."-".$y45_receit;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "fiscalprocrec não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y45_codtipo."-".$y45_receit;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$y45_codtipo."-".$y45_receit;
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
            $this->erro_sql   = "Record Vazio na Tabela:fis_fiscalprocrec";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($y45_codtipo = null, $y45_receit = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos}";
        $sql .= "  from fiscalizacao.fis_fiscalprocrec ";
        $sql .= "      inner join tabrec  on  tabrec.k02_codigo = fis_fiscalprocrec.y45_receit";
        $sql .= "      inner join fiscalizacao.fis_fiscalproc  on  fis_fiscalproc.y29_codtipo = fis_fiscalprocrec.y45_codtipo";
        $sql .= "      inner join tabrecjm  on  tabrecjm.k02_codjm = tabrec.k02_codjm";
        $sql .= "      inner join tabrectipo  on  tabrectipo.k116_sequencial = tabrec.k02_tabrectipo";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_fiscalproc.y29_coddepto";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($y45_codtipo)) {
                $sql2 .= " where fis_fiscalprocrec.y45_codtipo = $y45_codtipo ";
            }
            if (!empty($y45_receit)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " fis_fiscalprocrec.y45_receit = $y45_receit ";
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

    public function sql_query_file($y45_codtipo = null, $y45_receit = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos} ";
        $sql .= "  from fiscalizacao.fis_fiscalprocrec ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($y45_codtipo)) {
                $sql2 .= " where fis_fiscalprocrec.y45_codtipo = $y45_codtipo ";
            }
            if (!empty($y45_receit)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " fis_fiscalprocrec.y45_receit = $y45_receit ";
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

    public function sql_query_fiscaltipo($y45_codtipo=null,$y45_receit=null,$campos="*",$ordem=null,$dbwhere="")
    {
        $sql = "select ";
        if($campos != "*" ){
          $campos_sql = explode("#",$campos);
          $virgula = "";
          for($i=0;$i<sizeof($campos_sql);$i++){
            $sql .= $virgula.$campos_sql[$i];
            $virgula = ",";
          }
        }else{
          $sql .= $campos;
        }
        $sql .= " from fiscalizacao.fis_fiscalprocrec ";
        $sql .= "      inner join tabrec  on  tabrec.k02_codigo = fis_fiscalprocrec.y45_receit";
        $sql .= "      inner join fiscalizacao.fis_fiscalproc  on  fis_fiscalproc.y29_codtipo = fis_fiscalprocrec.y45_codtipo";
        $sql .= "      inner join tabrecjm  on  tabrecjm.k02_codjm = tabrec.k02_codjm";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_fiscalproc.y29_coddepto";
        $sql .= "      inner join fiscalizacao.fis_tipoandam  on  fis_tipoandam.y41_codtipo = fis_fiscalproc.y29_tipoandam";
        $sql .= "      inner join fiscalizacao.fis_fiscaltipo  on  fis_fiscalprocrec.y45_codtipo = fis_fiscaltipo.y31_codtipo";
        $sql2 = "";
        if($dbwhere==""){
          if($y45_codtipo!=null ){
            $sql2 .= " where fis_fiscalprocrec.y45_codtipo = $y45_codtipo ";
          }
          if($y45_receit!=null ){
            if($sql2!=""){
               $sql2 .= " and ";
            }else{
               $sql2 .= " where ";
            }
            $sql2 .= " fis_fiscalprocrec.y45_receit = $y45_receit ";
          }
        }else if($dbwhere != ""){
          $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if($ordem != null ){
          $sql .= " order by ";
          $campos_sql = explode("#",$ordem);
          $virgula = "";
          for($i=0;$i<sizeof($campos_sql);$i++){
            $sql .= $virgula.$campos_sql[$i];
            $virgula = ",";
          }
        }
        return $sql;
    }

    public function sql_query_autotipo ( $y45_codtipo=null,$y45_receit=null,$campos="*",$ordem=null,$dbwhere="")
    {
        $sql = "select ";
        if($campos != "*" ){
          $campos_sql = explode("#",$campos);
          $virgula = "";
          for($i=0;$i<sizeof($campos_sql);$i++){
            $sql .= $virgula.$campos_sql[$i];
            $virgula = ",";
          }
        }else{
          $sql .= $campos;
        }
        $sql .= " from fiscalizacao.fis_fiscalprocrec ";
        $sql .= "      inner join tabrec  on  tabrec.k02_codigo = fis_fiscalprocrec.y45_receit";
        $sql .= "      inner join fiscalizacao.fis_fiscalproc  on  fis_fiscalproc.y29_codtipo = fis_fiscalprocrec.y45_codtipo";
        $sql .= "      inner join tabrecjm  on  tabrecjm.k02_codjm = tabrec.k02_codjm";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_fiscalproc.y29_coddepto";
        $sql .= "      inner join fiscalizacao.fis_tipoandam  on  fis_tipoandam.y41_codtipo = fis_fiscalproc.y29_tipoandam";
        $sql .= "      inner join fiscalizacao.fis_autotipo  on  fis_fiscalprocrec.y45_codtipo = fis_autotipo.y59_codtipo";
        $sql2 = "";
        if($dbwhere==""){
          if($y45_codtipo!=null ){
            $sql2 .= " where fis_fiscalprocrec.y45_codtipo = $y45_codtipo ";
          }
          if($y45_receit!=null ){
            if($sql2!=""){
               $sql2 .= " and ";
            }else{
               $sql2 .= " where ";
            }
            $sql2 .= " fis_fiscalprocrec.y45_receit = $y45_receit ";
          }
        }else if($dbwhere != ""){
          $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if($ordem != null ){
          $sql .= " order by ";
          $campos_sql = explode("#",$ordem);
          $virgula = "";
          for($i=0;$i<sizeof($campos_sql);$i++){
            $sql .= $virgula.$campos_sql[$i];
            $virgula = ",";
          }
        }
        return $sql;
    }

    public function sql_query_lancmulta($y45_codtipo=null,$y45_receit=null,$campos="*",$ordem=null,$dbwhere="")
    {
        $sql = "select ";
        if($campos != "*" ){
            $campos_sql = explode("#",$campos);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
            $sql .= $virgula.$campos_sql[$i];
            $virgula = ",";
            }
        }else{
            $sql .= $campos;
        }
        $sql .= " from fiscalizacao.fis_fiscalprocrec ";
        $sql .= "      inner join tabrec  on  tabrec.k02_codigo = fis_fiscalprocrec.y45_receit";
        $sql .= "      inner join fiscalizacao.fis_fiscalproc  on  fis_fiscalproc.y29_codtipo = fis_fiscalprocrec.y45_codtipo";
        $sql .= "      inner join tabrecjm  on  tabrecjm.k02_codjm = tabrec.k02_codjm";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_fiscalproc.y29_coddepto";
        $sql .= "      inner join fiscalizacao.fis_tipoandam  on  fis_tipoandam.y41_codtipo = fis_fiscalproc.y29_tipoandam";
        $sql .= "      inner join fiscalizacao.fis_lancmulta ON fis_fiscalprocrec.y45_codtipo = fis_lancmulta.nl28_codtipo";
        $sql2 = "";
        if($dbwhere==""){
            if($y45_codtipo!=null ){
            $sql2 .= " where fis_fiscalprocrec.y45_codtipo = $y45_codtipo ";
            }
            if($y45_receit!=null ){
            if($sql2!=""){
                $sql2 .= " and ";
            }else{
                $sql2 .= " where ";
            }
            $sql2 .= " fis_fiscalprocrec.y45_receit = $y45_receit ";
            }
        }else if($dbwhere != ""){
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if($ordem != null ){
            $sql .= " order by ";
            $campos_sql = explode("#",$ordem);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
            $sql .= $virgula.$campos_sql[$i];
            $virgula = ",";
            }
        }
        return $sql;
    }
}
