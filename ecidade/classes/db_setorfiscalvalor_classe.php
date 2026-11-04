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

class cl_setorfiscalvalor
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
    public $j82_codigo = 0;
    public $j82_setorfiscal = 0;
    public $j82_anousu = 0;
    public $j82_valorterreno = 0;
    public $j82_caract = null;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 j82_codigo = int4 = Codigo sequencial 
                 j82_setorfiscal = int4 = Codigo do setor fiscal 
                 j82_anousu = int4 = Ano 
                 j82_valorterreno = float8 = Alíquota 
                 j82_caract = int4 = Caracteristica do imóvel 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("setorfiscalvalor");
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
    }

    public function erro($mostra, $retorna)
    {
        if (($this->erro_status == "0") || ($mostra && $this->erro_status != null )) {
            echo "<script>alert(\"".$this->erro_msg."\")</script>";
            if ($retorna) {
                echo "<script>location.href='".$this->pagina_retorno."'</script>";
            }
        }
    }

    public function atualizacampos($exclusao = false)
    {
        if (!$exclusao) {
            $this->j82_codigo = ($this->j82_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["j82_codigo"]:$this->j82_codigo);
            $this->j82_setorfiscal = ($this->j82_setorfiscal == ""?@$GLOBALS["HTTP_POST_VARS"]["j82_setorfiscal"]:$this->j82_setorfiscal);
            $this->j82_anousu = ($this->j82_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["j82_anousu"]:$this->j82_anousu);
            $this->j82_valorterreno = ($this->j82_valorterreno == ""?@$GLOBALS["HTTP_POST_VARS"]["j82_valorterreno"]:$this->j82_valorterreno);
            $this->j82_caract = ($this->j82_caract == ""?@$GLOBALS["HTTP_POST_VARS"]["j82_caract"]:$this->j82_caract);
        } else {
            $this->j82_codigo = ($this->j82_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["j82_codigo"]:$this->j82_codigo);
        }
    }

    public function incluir($j82_codigo)
    {
        $this->atualizacampos();
        if ($this->j82_setorfiscal == null) {
            $this->erro_sql = " Campo Codigo do setor fiscal não informado.";
            $this->erro_campo = "j82_setorfiscal";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j82_anousu == null) {
            $this->erro_sql = " Campo Ano nao Informado.";
            $this->erro_campo = "j82_anousu";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j82_valorterreno == null) {
            $this->erro_sql = " Campo Valor m2 terreno nao Informado.";
            $this->erro_campo = "j82_valorterreno";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($j82_codigo == "" || $j82_codigo == null) {
            $result = db_query("select nextval('setorfiscalvalor_j82_codigo_seq')");
            if (!$result) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql   = "Verifique o cadastro da sequencia: setorfiscalvalor_j82_codigo_seq do campo: j82_codigo";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->j82_codigo = pg_fetch_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from setorfiscalvalor_j82_codigo_seq");
            if (($result != false) && (pg_fetch_result($result, 0, 0) < $j82_codigo)) {
                $this->erro_sql = " Campo j82_codigo maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->j82_codigo = $j82_codigo;
            }
        }
        if (($this->j82_codigo == null) || ($this->j82_codigo == "")) {
            $this->erro_sql = " Campo j82_codigo não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if (empty($this->j82_caract)) {
	   $this->j82_caract = 'null';
           /* Ajuste para regra que somente possa existir um registro do setor fiscal no ano com característica nula */
           if ((!empty($this->j82_setorfiscal)) && (!empty($this->j82_anousu))) {
              $sWhere = "j82_setorfiscal = {$this->j82_setorfiscal} and j82_anousu = {$this->j82_anousu} and j82_caract is null";
              $this->excluir(null, $sWhere); 
           }
	}
        $sql = "insert into setorfiscalvalor(
                                       j82_codigo 
                                      ,j82_setorfiscal 
                                      ,j82_anousu 
                                      ,j82_valorterreno 
                                      ,j82_caract 
                       )
                values (
                                $this->j82_codigo 
                               ,$this->j82_setorfiscal 
                               ,$this->j82_anousu 
                               ,$this->j82_valorterreno 
                               ,$this->j82_caract 
                      )";
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = " ($this->j82_codigo) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = " já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = " ($this->j82_codigo) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->j82_codigo;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            $resaco = $this->sql_record($this->sql_query_file($this->j82_codigo));
            if (($resaco!=false)||($this->numrows!=0)) {
                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_fetch_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                $resac = db_query("insert into db_acountkey values($acount,9668,'$this->j82_codigo','I')");
                $resac = db_query("insert into db_acount values($acount,1664,9668,'','".AddSlashes(pg_fetch_result($resaco, 0, 'j82_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1664,9669,'','".AddSlashes(pg_fetch_result($resaco, 0, 'j82_setorfiscal'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1664,9670,'','".AddSlashes(pg_fetch_result($resaco, 0, 'j82_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1664,9671,'','".AddSlashes(pg_fetch_result($resaco, 0, 'j82_valorterreno'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1664,93803973,'','".AddSlashes(pg_fetch_result($resaco, 0, 'j82_caract'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
            }
        }
        return true;
    }

    public function alterar($j82_codigo = null)
    {
        $this->atualizacampos();
        $sql = " update setorfiscalvalor set ";
        $virgula = "";
        if (trim($this->j82_setorfiscal)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j82_setorfiscal"])) {
            $sql  .= $virgula." j82_setorfiscal = $this->j82_setorfiscal ";
            $virgula = ",";
            if (trim($this->j82_setorfiscal) == null) {
                $this->erro_sql = " Campo Codigo do setor fiscal não informado.";
                $this->erro_campo = "j82_setorfiscal";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j82_anousu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j82_anousu"])) {
            if (trim($this->j82_anousu)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j82_anousu"])) {
                $this->j82_anousu = "0" ;
            }
            $sql  .= $virgula." j82_anousu = $this->j82_anousu ";
            $virgula = ",";
        }
        if (trim($this->j82_valorterreno)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j82_valorterreno"])) {
            if (trim($this->j82_valorterreno)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j82_valorterreno"])) {
                $this->j82_valorterreno = "0" ;
            }
            $sql  .= $virgula." j82_valorterreno = $this->j82_valorterreno ";
            $virgula = ",";
        }
        if (trim($this->j82_caract)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j82_caract"])) {
            if (trim($this->j82_caract)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j82_caract"])) {
                $this->j82_caract = 'null' ;
            }
            $sql  .= $virgula." j82_caract = $this->j82_caract ";
            $virgula = ",";
        }
        $sql .= " where ";
        if ($j82_codigo!=null) {
            $sql .= " j82_codigo = $this->j82_codigo";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            $resaco = $this->sql_record($this->sql_query_file($this->j82_codigo));
            if ($this->numrows > 0) {
                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {
                      $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                      $acount = pg_fetch_result($resac, 0, 0);
                      $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                      $resac = db_query("insert into db_acountkey values($acount,9668,'$this->j82_codigo','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["j82_codigo"]) || $this->j82_codigo != "") {
                        $resac = db_query("insert into db_acount values($acount,1664,9668,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'j82_codigo'))."','$this->j82_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["j82_setorfiscal"]) || $this->j82_setorfiscal != "") {
                        $resac = db_query("insert into db_acount values($acount,1664,9669,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'j82_setorfiscal'))."','$this->j82_setorfiscal',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["j82_anousu"]) || $this->j82_anousu != "") {
                        $resac = db_query("insert into db_acount values($acount,1664,9670,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'j82_anousu'))."','$this->j82_anousu',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["j82_valorterreno"]) || $this->j82_valorterreno != "") {
                        $resac = db_query("insert into db_acount values($acount,1664,9671,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'j82_valorterreno'))."','$this->j82_valorterreno',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["j82_caract"]) || $this->j82_caract != "") {
                        $resac = db_query("insert into db_acount values($acount,1664,93803973,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'j82_caract'))."','$this->j82_caract',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = " não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->j82_codigo;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = " não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->j82_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$this->j82_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($j82_codigo = null, $dbwhere = null)
    {
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            if (empty($dbwhere)) {
                $resaco = $this->sql_record($this->sql_query_file($j82_codigo));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
            }
            if (($resaco != false) || ($this->numrows!=0)) {
                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {
                    $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_fetch_result($resac, 0, 0);
                    $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                    $resac  = db_query("insert into db_acountkey values($acount,9668,'$j82_codigo','E')");
                    $resac  = db_query("insert into db_acount values($acount,1664,9668,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 'j82_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1664,9669,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 'j82_setorfiscal'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1664,9670,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 'j82_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1664,9671,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 'j82_valorterreno'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1664,93803973,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 'j82_caract'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                }
            }
        }
        $sql = " delete from setorfiscalvalor where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($j82_codigo)) {
                if (!empty($sql2)) {
                     $sql2 .= " and ";
                }
                $sql2 .= " j82_codigo = $j82_codigo ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = " não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$j82_codigo;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = " não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$j82_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$j82_codigo;
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
            $this->erro_sql   = "Record Vazio na Tabela:setorfiscalvalor";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($j82_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos}";
        $sql .= "  from setorfiscalvalor ";
        $sql .= "      left  join caracter  on  caracter.j31_codigo = setorfiscalvalor.j82_caract";
        $sql .= "      inner join setorfiscal  on  setorfiscal.j90_codigo = setorfiscalvalor.j82_setorfiscal";
        $sql .= "      left  join cargrup  on  cargrup.j32_grupo = caracter.j31_grupo";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($j82_codigo)) {
                $sql2 .= " where setorfiscalvalor.j82_codigo = $j82_codigo ";
            }
        } elseif (!empty($dbwhere)) {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        } else {
            $sql .= " order by j82_setorfiscal, j82_anousu desc, j82_caract ";
	}

        return $sql;
    }

    public function sql_query_file($j82_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos} ";
        $sql .= "  from setorfiscalvalor ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($j82_codigo)) {
                $sql2 .= " where setorfiscalvalor.j82_codigo = $j82_codigo ";
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
