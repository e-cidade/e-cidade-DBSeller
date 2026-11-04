<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

class cl_matmatermaterialestoquegrupo
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
    public $m68_sequencial = 0;
    public $m68_matmater = 0;
    public $m68_materialestoquegrupo = 0;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 m68_sequencial = int4 = Código Sequencial 
                 m68_matmater = int4 = Código do Material 
                 m68_materialestoquegrupo = int4 = Código do Grupo 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("matmatermaterialestoquegrupo");
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
            $this->m68_sequencial = ($this->m68_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["m68_sequencial"]:$this->m68_sequencial);
            $this->m68_matmater = ($this->m68_matmater == ""?@$GLOBALS["HTTP_POST_VARS"]["m68_matmater"]:$this->m68_matmater);
            $this->m68_materialestoquegrupo = ($this->m68_materialestoquegrupo == ""?@$GLOBALS["HTTP_POST_VARS"]["m68_materialestoquegrupo"]:$this->m68_materialestoquegrupo);
        } else {
            $this->m68_sequencial = ($this->m68_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["m68_sequencial"]:$this->m68_sequencial);
        }
    }

    public function incluir($m68_sequencial)
    {
        $this->atualizacampos();
        if ($this->m68_matmater == null) {
            $this->erro_sql = " Campo Código do Material não informado.";
            $this->erro_campo = "m68_matmater";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->m68_materialestoquegrupo == null) {
            $this->erro_sql = " Campo Código do Grupo não informado.";
            $this->erro_campo = "m68_materialestoquegrupo";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($m68_sequencial == "" || $m68_sequencial == null) {
            $result = db_query("select nextval('matmatermaterialestoquegrupo_m68_sequencial_seq')");
            if ($result==false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql   = "Verifique o cadastro da sequencia: matmatermaterialestoquegrupo_m68_sequencial_seq do campo: m68_sequencial";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->m68_sequencial = pg_fetch_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from matmatermaterialestoquegrupo_m68_sequencial_seq");
            if (($result != false) && (pg_fetch_result($result, 0, 0) < $m68_sequencial)) {
                $this->erro_sql = " Campo m68_sequencial maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->m68_sequencial = $m68_sequencial;
            }
        }
        if (($this->m68_sequencial == null) || ($this->m68_sequencial == "")) {
            $this->erro_sql = " Campo m68_sequencial não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into matmatermaterialestoquegrupo(
                                       m68_sequencial 
                                      ,m68_matmater 
                                      ,m68_materialestoquegrupo 
                       )
                values (
                                $this->m68_sequencial 
                               ,$this->m68_matmater 
                               ,$this->m68_materialestoquegrupo 
                      )";
        $result = db_query($sql);
        if ($result==false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Grupo de Material ($this->m68_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Grupo de Material já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "Grupo de Material ($this->m68_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->m68_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            $resaco = $this->sql_record($this->sql_query_file($this->m68_sequencial));
            if (($resaco!=false)||($this->numrows!=0)) {
                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_fetch_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                $resac = db_query("insert into db_acountkey values($acount,17976,'$this->m68_sequencial','I')");
                $resac = db_query("insert into db_acount values($acount,3176,17976,'','".AddSlashes(pg_fetch_result($resaco, 0, 'm68_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,3176,17977,'','".AddSlashes(pg_fetch_result($resaco, 0, 'm68_matmater'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,3176,17978,'','".AddSlashes(pg_fetch_result($resaco, 0, 'm68_materialestoquegrupo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
            }
        }
        return true;
    }

    public function alterar($m68_sequencial = null)
    {
        $this->atualizacampos();
        $sql = " update matmatermaterialestoquegrupo set ";
        $virgula = "";
        if (trim($this->m68_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["m68_sequencial"])) {
            $sql  .= $virgula." m68_sequencial = $this->m68_sequencial ";
            $virgula = ",";
            if (trim($this->m68_sequencial) == null) {
                $this->erro_sql = " Campo Código Sequencial não informado.";
                $this->erro_campo = "m68_sequencial";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->m68_matmater)!="" || isset($GLOBALS["HTTP_POST_VARS"]["m68_matmater"])) {
            $sql  .= $virgula." m68_matmater = $this->m68_matmater ";
            $virgula = ",";
            if (trim($this->m68_matmater) == null) {
                $this->erro_sql = " Campo Código do Material não informado.";
                $this->erro_campo = "m68_matmater";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->m68_materialestoquegrupo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["m68_materialestoquegrupo"])) {
            $sql  .= $virgula." m68_materialestoquegrupo = $this->m68_materialestoquegrupo ";
            $virgula = ",";
            if (trim($this->m68_materialestoquegrupo) == null) {
                $this->erro_sql = " Campo Código do Grupo não informado.";
                $this->erro_campo = "m68_materialestoquegrupo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($m68_sequencial!=null) {
            $sql .= " m68_sequencial = $this->m68_sequencial";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            $resaco = $this->sql_record($this->sql_query_file($this->m68_sequencial));
            if ($this->numrows > 0) {
                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {
                      $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                      $acount = pg_fetch_result($resac, 0, 0);
                      $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                      $resac = db_query("insert into db_acountkey values($acount,17976,'$this->m68_sequencial','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["m68_sequencial"]) || $this->m68_sequencial != "") {
                        $resac = db_query("insert into db_acount values($acount,3176,17976,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'm68_sequencial'))."','$this->m68_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["m68_matmater"]) || $this->m68_matmater != "") {
                        $resac = db_query("insert into db_acount values($acount,3176,17977,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'm68_matmater'))."','$this->m68_matmater',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["m68_materialestoquegrupo"]) || $this->m68_materialestoquegrupo != "") {
                        $resac = db_query("insert into db_acount values($acount,3176,17978,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'm68_materialestoquegrupo'))."','$this->m68_materialestoquegrupo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Grupo de Material não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->m68_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Grupo de Material não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->m68_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$this->m68_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($m68_sequencial = null, $dbwhere = null)
    {
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            if (empty($dbwhere)) {
                $resaco = $this->sql_record($this->sql_query_file($m68_sequencial));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
            }
            if (($resaco != false) || ($this->numrows!=0)) {
                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {
                    $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_fetch_result($resac, 0, 0);
                    $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                    $resac  = db_query("insert into db_acountkey values($acount,17976,'$m68_sequencial','E')");
                    $resac  = db_query("insert into db_acount values($acount,3176,17976,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 'm68_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,3176,17977,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 'm68_matmater'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,3176,17978,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 'm68_materialestoquegrupo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                }
            }
        }
        $sql = " delete from matmatermaterialestoquegrupo
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($m68_sequencial)) {
                if (!empty($sql2)) {
                     $sql2 .= " and ";
                }
                $sql2 .= " m68_sequencial = $m68_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Grupo de Material não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$m68_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Grupo de Material não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$m68_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$m68_sequencial;
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
            $this->erro_sql   = "Record Vazio na Tabela:matmatermaterialestoquegrupo";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($m68_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos}";
        $sql .= "  from matmatermaterialestoquegrupo ";
        $sql .= "      inner join matmater  on  matmater.m60_codmater = matmatermaterialestoquegrupo.m68_matmater";
        $sql .= "      inner join materialestoquegrupo  on  materialestoquegrupo.m65_sequencial = matmatermaterialestoquegrupo.m68_materialestoquegrupo";
        $sql .= "      inner join matunid  on  matunid.m61_codmatunid = matmater.m60_codmatunid";
        $sql .= "      inner join db_estruturavalor  on  db_estruturavalor.db121_sequencial = materialestoquegrupo.m65_db_estruturavalor";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($m68_sequencial)) {
                $sql2 .= " where matmatermaterialestoquegrupo.m68_sequencial = $m68_sequencial ";
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

    public function sql_query_file($m68_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos} ";
        $sql .= "  from matmatermaterialestoquegrupo ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($m68_sequencial)) {
                $sql2 .= " where matmatermaterialestoquegrupo.m68_sequencial = $m68_sequencial ";
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

    public function sql_query_grupo_conta($m68_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
    
        $sql = "select {$campos} ";
        $sql .= " from matmatermaterialestoquegrupo ";
        $sql .= "      inner join matmater  on  matmater.m60_codmater = matmatermaterialestoquegrupo.m68_matmater";
        $sql .= "      inner join materialestoquegrupo  on  materialestoquegrupo.m65_sequencial = matmatermaterialestoquegrupo.m68_materialestoquegrupo";
        $sql .= "      inner join materialestoquegrupoconta  on  materialestoquegrupoconta.m66_materialestoquegrupo = materialestoquegrupo.m65_sequencial";
        $sql .= "      inner join matunid  on  matunid.m61_codmatunid = matmater.m60_codmatunid";
        $sql .= "      inner join db_estruturavalor  on  db_estruturavalor.db121_sequencial = materialestoquegrupo.m65_db_estruturavalor";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($m68_sequencial != null) {
                $sql2 .= " where matmatermaterialestoquegrupo.m68_sequencial = $m68_sequencial ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by {$ordem} ";
        }
        return $sql;
    }
}
