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

class cl_saltes
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
    public $k13_conta = 0;
    public $k13_reduz = 0;
    public $k13_descr = null;
    public $k13_saldo = 0;
    public $k13_ident = null;
    public $k13_vlratu = 0;
    public $k13_datvlr_dia = null;
    public $k13_datvlr_mes = null;
    public $k13_datvlr_ano = null;
    public $k13_datvlr = null;
    public $k13_limite_dia = null;
    public $k13_limite_mes = null;
    public $k13_limite_ano = null;
    public $k13_limite = null;
    public $k13_dtimplantacao_dia = null;
    public $k13_dtimplantacao_mes = null;
    public $k13_dtimplantacao_ano = null;
    public $k13_dtimplantacao = null;
    public $k13_outrosdados = null;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 k13_conta = int4 = Código da Conta
                 k13_reduz = int4 = Reduzido
                 k13_descr = varchar(40) = Descrição da Conta
                 k13_saldo = float8 = Saldo da Conta
                 k13_ident = char(15) = Identificacao da conta
                 k13_vlratu = float8 = Valor Atualizado
                 k13_datvlr = date = Data de Atualização
                 k13_limite = date = Data Limite
                 k13_dtimplantacao = date = Data da Implantação da Conta
                 k13_outrosdados = text = outros dados saltes
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("saltes");
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
            $this->k13_conta = ($this->k13_conta == ""?@$GLOBALS["HTTP_POST_VARS"]["k13_conta"]:$this->k13_conta);
            $this->k13_reduz = ($this->k13_reduz == ""?@$GLOBALS["HTTP_POST_VARS"]["k13_reduz"]:$this->k13_reduz);
            $this->k13_descr = ($this->k13_descr == ""?@$GLOBALS["HTTP_POST_VARS"]["k13_descr"]:$this->k13_descr);
            $this->k13_saldo = ($this->k13_saldo == ""?@$GLOBALS["HTTP_POST_VARS"]["k13_saldo"]:$this->k13_saldo);
            $this->k13_ident = ($this->k13_ident == ""?@$GLOBALS["HTTP_POST_VARS"]["k13_ident"]:$this->k13_ident);
            $this->k13_vlratu = ($this->k13_vlratu == ""?@$GLOBALS["HTTP_POST_VARS"]["k13_vlratu"]:$this->k13_vlratu);
            if ($this->k13_datvlr == "") {
                $this->k13_datvlr_dia = ($this->k13_datvlr_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["k13_datvlr_dia"]:$this->k13_datvlr_dia);
                $this->k13_datvlr_mes = ($this->k13_datvlr_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["k13_datvlr_mes"]:$this->k13_datvlr_mes);
                $this->k13_datvlr_ano = ($this->k13_datvlr_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["k13_datvlr_ano"]:$this->k13_datvlr_ano);
                if ($this->k13_datvlr_dia != "") {
                    $this->k13_datvlr = $this->k13_datvlr_ano."-".$this->k13_datvlr_mes."-".$this->k13_datvlr_dia;
                }
            }
            if ($this->k13_limite == "") {
                $this->k13_limite_dia = ($this->k13_limite_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["k13_limite_dia"]:$this->k13_limite_dia);
                $this->k13_limite_mes = ($this->k13_limite_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["k13_limite_mes"]:$this->k13_limite_mes);
                $this->k13_limite_ano = ($this->k13_limite_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["k13_limite_ano"]:$this->k13_limite_ano);
                if ($this->k13_limite_dia != "") {
                    $this->k13_limite = $this->k13_limite_ano."-".$this->k13_limite_mes."-".$this->k13_limite_dia;
                }
            }
            if ($this->k13_dtimplantacao == "") {
                $this->k13_dtimplantacao_dia = ($this->k13_dtimplantacao_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["k13_dtimplantacao_dia"]:$this->k13_dtimplantacao_dia);
                $this->k13_dtimplantacao_mes = ($this->k13_dtimplantacao_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["k13_dtimplantacao_mes"]:$this->k13_dtimplantacao_mes);
                $this->k13_dtimplantacao_ano = ($this->k13_dtimplantacao_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["k13_dtimplantacao_ano"]:$this->k13_dtimplantacao_ano);
                if ($this->k13_dtimplantacao_dia != "") {
                    $this->k13_dtimplantacao = $this->k13_dtimplantacao_ano."-".$this->k13_dtimplantacao_mes."-".$this->k13_dtimplantacao_dia;
                }
            }
            $this->k13_outrosdados = ($this->k13_outrosdados == ""?@$GLOBALS["HTTP_POST_VARS"]["k13_outrosdados"]:$this->k13_outrosdados);
        } else {
            $this->k13_conta = ($this->k13_conta == ""?@$GLOBALS["HTTP_POST_VARS"]["k13_conta"]:$this->k13_conta);
        }
    }

    public function incluir($k13_conta)
    {
        $this->atualizacampos();
        if ($this->k13_reduz == null) {
            $this->erro_sql = " Campo Reduzido não informado.";
            $this->erro_campo = "k13_reduz";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k13_descr == null) {
            $this->erro_sql = " Campo Descrição da Conta não informado.";
            $this->erro_campo = "k13_descr";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k13_saldo == null) {
            $this->erro_sql = " Campo Saldo da Conta não informado.";
            $this->erro_campo = "k13_saldo";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k13_vlratu == null) {
            $this->k13_vlratu = "0";
        }
        if ($this->k13_limite == null) {
            $this->k13_limite = "null";
        }
        if ($this->k13_dtimplantacao == null) {
            $this->erro_sql = " Campo Data da Implantação da Conta não informado.";
            $this->erro_campo = "k13_dtimplantacao_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->k13_conta = $k13_conta;
        if (($this->k13_conta == null) || ($this->k13_conta == "")) {
            $this->erro_sql = " Campo k13_conta não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into saltes(
                                       k13_conta
                                      ,k13_reduz
                                      ,k13_descr
                                      ,k13_saldo
                                      ,k13_ident
                                      ,k13_vlratu
                                      ,k13_datvlr
                                      ,k13_limite
                                      ,k13_dtimplantacao
                                      ,k13_outrosdados
                       )
                values (
                                $this->k13_conta
                               ,$this->k13_reduz
                               ,'$this->k13_descr'
                               ,$this->k13_saldo
                               ,'$this->k13_ident'
                               ,$this->k13_vlratu
                               ," . ($this->k13_datvlr == "null" || $this->k13_datvlr == "" ? "null" : "'" . $this->k13_datvlr . "'") . "
                               ," . ($this->k13_limite == "null" || $this->k13_limite == "" ? "null" : "'" . $this->k13_limite . "'") . "
                               ," . ($this->k13_dtimplantacao == "null" || $this->k13_dtimplantacao == "" ? "null" : "'" . $this->k13_dtimplantacao . "'") . "
                               ," . ($this->k13_outrosdados == "null" || $this->k13_outrosdados == "" ? "null" : "'" . $this->k13_outrosdados . "'") . "
                      )";
        $result = db_query($sql);
        if ($result==false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Saldo Tesuoraria ($this->k13_conta) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Saldo Tesuoraria já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "Saldo Tesuoraria ($this->k13_conta) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->k13_conta;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->k13_conta));
            if (($resaco!=false)||($this->numrows!=0)) {
                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_fetch_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                $resac = db_query("insert into db_acountkey values($acount,1173,'$this->k13_conta','I')");
                $resac = db_query("insert into db_acount values($acount,212,1173,'','".AddSlashes(pg_fetch_result($resaco, 0, 'k13_conta'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,212,6012,'','".AddSlashes(pg_fetch_result($resaco, 0, 'k13_reduz'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,212,1916,'','".AddSlashes(pg_fetch_result($resaco, 0, 'k13_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,212,1174,'','".AddSlashes(pg_fetch_result($resaco, 0, 'k13_saldo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,212,1175,'','".AddSlashes(pg_fetch_result($resaco, 0, 'k13_ident'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,212,1176,'','".AddSlashes(pg_fetch_result($resaco, 0, 'k13_vlratu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,212,1177,'','".AddSlashes(pg_fetch_result($resaco, 0, 'k13_datvlr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,212,6896,'','".AddSlashes(pg_fetch_result($resaco, 0, 'k13_limite'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,212,14209,'','".AddSlashes(pg_fetch_result($resaco, 0, 'k13_dtimplantacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,212,1014514,'','".AddSlashes(pg_fetch_result($resaco, 0, 'k13_outrosdados'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
            }
        }
        return true;
    }

    public function alterar($k13_conta = null)
    {
        $this->atualizacampos();
        $sql = " update saltes set ";
        $virgula = "";
        if (trim($this->k13_conta)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k13_conta"])) {
            $sql  .= $virgula." k13_conta = $this->k13_conta ";
            $virgula = ",";
            if (trim($this->k13_conta) == null) {
                $this->erro_sql = " Campo Código da Conta não informado.";
                $this->erro_campo = "k13_conta";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k13_reduz)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k13_reduz"])) {
            $sql  .= $virgula." k13_reduz = $this->k13_reduz ";
            $virgula = ",";
            if (trim($this->k13_reduz) == null) {
                $this->erro_sql = " Campo Reduzido não informado.";
                $this->erro_campo = "k13_reduz";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k13_descr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k13_descr"])) {
            $sql  .= $virgula." k13_descr = '$this->k13_descr' ";
            $virgula = ",";
            if (trim($this->k13_descr) == null) {
                $this->erro_sql = " Campo Descrição da Conta não informado.";
                $this->erro_campo = "k13_descr";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k13_saldo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k13_saldo"])) {
            $sql  .= $virgula." k13_saldo = $this->k13_saldo ";
            $virgula = ",";
            if (trim($this->k13_saldo) == null) {
                $this->erro_sql = " Campo Saldo da Conta não informado.";
                $this->erro_campo = "k13_saldo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k13_ident)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k13_ident"])) {
            $sql  .= $virgula." k13_ident = '$this->k13_ident' ";
            $virgula = ",";
        }
        if (trim($this->k13_vlratu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k13_vlratu"])) {
            if (trim($this->k13_vlratu)=="" && isset($GLOBALS["HTTP_POST_VARS"]["k13_vlratu"])) {
                $this->k13_vlratu = "0" ;
            }
            $sql  .= $virgula." k13_vlratu = $this->k13_vlratu ";
            $virgula = ",";
        }
        if (trim($this->k13_datvlr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k13_datvlr_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["k13_datvlr_dia"] !="")) {
            $sql  .= $virgula." k13_datvlr = '$this->k13_datvlr' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["k13_datvlr_dia"])) {
                $sql  .= $virgula." k13_datvlr = null ";
                $virgula = ",";
            }
        }
        if (trim($this->k13_limite)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k13_limite_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["k13_limite_dia"] !="")) {
            $sql  .= $virgula." k13_limite = '$this->k13_limite' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["k13_limite_dia"])) {
                $sql  .= $virgula." k13_limite = null ";
                $virgula = ",";
            }
        }
        if (trim($this->k13_dtimplantacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k13_dtimplantacao_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["k13_dtimplantacao_dia"] !="")) {
            $sql  .= $virgula." k13_dtimplantacao = '$this->k13_dtimplantacao' ";
            $virgula = ",";
            if (trim($this->k13_dtimplantacao) == null) {
                $this->erro_sql = " Campo Data da Implantação da Conta não informado.";
                $this->erro_campo = "k13_dtimplantacao_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["k13_dtimplantacao_dia"])) {
                $sql  .= $virgula." k13_dtimplantacao = null ";
                $virgula = ",";
                if (trim($this->k13_dtimplantacao) == null) {
                      $this->erro_sql = " Campo Data da Implantação da Conta não informado.";
                      $this->erro_campo = "k13_dtimplantacao_dia";
                      $this->erro_banco = "";
                      $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                      $this->erro_status = "0";
                      return false;
                }
            }
        }
        if (trim($this->k13_outrosdados)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k13_outrosdados"])) {
            $sql  .= $virgula." k13_outrosdados = '$this->k13_outrosdados' ";
            $virgula = ",";
        }
        $sql .= " where ";
        if ($k13_conta!=null) {
            $sql .= " k13_conta = $this->k13_conta";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            $resaco = $this->sql_record($this->sql_query_file($this->k13_conta));
            if ($this->numrows > 0) {
                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {
                      $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                      $acount = pg_fetch_result($resac, 0, 0);
                      $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                      $resac = db_query("insert into db_acountkey values($acount,1173,'$this->k13_conta','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k13_conta"]) || $this->k13_conta != "") {
                        $resac = db_query("insert into db_acount values($acount,212,1173,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'k13_conta'))."','$this->k13_conta',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k13_reduz"]) || $this->k13_reduz != "") {
                        $resac = db_query("insert into db_acount values($acount,212,6012,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'k13_reduz'))."','$this->k13_reduz',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k13_descr"]) || $this->k13_descr != "") {
                        $resac = db_query("insert into db_acount values($acount,212,1916,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'k13_descr'))."','$this->k13_descr',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k13_saldo"]) || $this->k13_saldo != "") {
                        $resac = db_query("insert into db_acount values($acount,212,1174,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'k13_saldo'))."','$this->k13_saldo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k13_ident"]) || $this->k13_ident != "") {
                        $resac = db_query("insert into db_acount values($acount,212,1175,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'k13_ident'))."','$this->k13_ident',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k13_vlratu"]) || $this->k13_vlratu != "") {
                        $resac = db_query("insert into db_acount values($acount,212,1176,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'k13_vlratu'))."','$this->k13_vlratu',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k13_datvlr"]) || $this->k13_datvlr != "") {
                        $resac = db_query("insert into db_acount values($acount,212,1177,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'k13_datvlr'))."','$this->k13_datvlr',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k13_limite"]) || $this->k13_limite != "") {
                        $resac = db_query("insert into db_acount values($acount,212,6896,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'k13_limite'))."','$this->k13_limite',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k13_dtimplantacao"]) || $this->k13_dtimplantacao != "") {
                        $resac = db_query("insert into db_acount values($acount,212,14209,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'k13_dtimplantacao'))."','$this->k13_dtimplantacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k13_outrosdados"]) || $this->k13_outrosdados != "") {
                        $resac = db_query("insert into db_acount values($acount,212,1014514,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'k13_outrosdados'))."','$this->k13_outrosdados',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Saldo Tesuoraria não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->k13_conta;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Saldo Tesuoraria não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->k13_conta;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$this->k13_conta;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($k13_conta = null, $dbwhere = null)
    {
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {
            if (empty($dbwhere)) {
                $resaco = $this->sql_record($this->sql_query_file($k13_conta));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
            }
            if (($resaco != false) || ($this->numrows != 0)) {
                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {
                    for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {
                        $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                        $acount = pg_fetch_result($resac, 0, 0);
                        $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                        $resac = db_query("insert into db_acountkey values($acount,1173,'$k13_conta','E')");
                        $resac = db_query("insert into db_acount values($acount,212,1173,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'k13_conta')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                        $resac = db_query("insert into db_acount values($acount,212,6012,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'k13_reduz')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                        $resac = db_query("insert into db_acount values($acount,212,1916,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'k13_descr')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                        $resac = db_query("insert into db_acount values($acount,212,1174,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'k13_saldo')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                        $resac = db_query("insert into db_acount values($acount,212,1175,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'k13_ident')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                        $resac = db_query("insert into db_acount values($acount,212,1176,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'k13_vlratu')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                        $resac = db_query("insert into db_acount values($acount,212,1177,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'k13_datvlr')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                        $resac = db_query("insert into db_acount values($acount,212,6896,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'k13_limite')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                        $resac = db_query("insert into db_acount values($acount,212,14209,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'k13_dtimplantacao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                        $resac = db_query("insert into db_acount values($acount,212,1014514,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'k13_outrosdados')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                }
            }
            $sql = " delete from saltes
                    where ";
            $sql2 = "";
            if (empty($dbwhere)) {
                if (!empty($k13_conta)) {
                    if (!empty($sql2)) {
                        $sql2 .= " and ";
                    }
                    $sql2 .= " k13_conta = $k13_conta ";
                }
            } else {
                $sql2 = $dbwhere;
            }
            $result = db_query($sql . $sql2);
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Saldo Tesuoraria não Excluído. Exclusão Abortada.\\n";
                $this->erro_sql .= "Valores : " . $k13_conta;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                $this->numrows_excluir = 0;
                return false;
            } else {
                if (pg_affected_rows($result) == 0) {
                    $this->erro_banco = "";
                    $this->erro_sql = "Saldo Tesuoraria não Encontrado. Exclusão não Efetuada.\\n";
                    $this->erro_sql .= "Valores : " . $k13_conta;
                    $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                    $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                    $this->erro_status = "1";
                    $this->numrows_excluir = 0;
                    return true;
                } else {
                    $this->erro_banco = "";
                    $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                    $this->erro_sql .= "Valores : " . $k13_conta;
                    $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                    $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                    $this->erro_status = "1";
                    $this->numrows_excluir = pg_affected_rows($result);
                    return true;
                }
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
            $this->erro_sql   = "Record Vazio na Tabela:saltes";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($k13_conta = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos}";
     $sql .= " from saltes ";
     $sql .= "      inner join conplanoreduz  on  conplanoreduz.c61_reduz = saltes.k13_reduz and c61_anousu=".db_getsession("DB_anousu");
     $sql .= "      inner join conplanoexe    on  conplanoexe.c62_reduz = conplanoreduz.c61_reduz and c62_anousu=c61_anousu";
     $sql .= "      left  join conplanoconta  on  conplanoconta.c63_codcon = conplanoreduz.c61_codcon  and conplanoconta.c63_anousu=conplanoreduz.c61_anousu and c61_reduz = c63_reduz";
     $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($k13_conta)) {
         $sql2 .= " where saltes.k13_conta = $k13_conta ";
       }
        } elseif (!empty($dbwhere)) {
       $sql2 = " where $dbwhere";
     }
     $sql2 .= ($sql2!=""?" and ":" where ") . " c61_instit = " . db_getsession("DB_instit");
     $sql2 .= ($sql2!=""?" and ":" where ") . " c62_anousu = " . db_getsession("DB_anousu");
     $sql .= $sql2;
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
     }
     return $sql;
  }

    public function sql_query_file ( $k13_conta=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select {$campos
     }";
     $sql .= " from saltes ";
     $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($k13_conta)) {
         $sql2 .= " where saltes.k13_conta = $k13_conta ";
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


    public function sql_query_conciliacontabancaria ( $k13_conta=null,$campos="*",$ordem=null,$dbwhere=""){
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
    $sql .= " from saltes ";
    $sql .= "      inner join conplanoreduz on  conplanoreduz.c61_reduz  = saltes.k13_reduz
                                           and c61_anousu=".db_getsession("DB_anousu");
        $sql .= "      inner join conplanoexe   on  conplanoexe.c62_reduz    = conplanoreduz.c61_reduz
                                           and c61_anousu=c62_anousu";
        $sql .= "      inner join conplano      on  conplanoreduz.c61_codcon = conplano.c60_codcon
                                           and c61_anousu=c60_anousu";
        $sql .= "      left  join conplanoconta on  conplanoconta.c63_codcon = conplanoreduz.c61_codcon ";
        $sql .= "                              and conplanoconta.c63_anousu  = conplanoreduz.c61_anousu ";
        $sql .= "                              and conplanoconta.c63_reduz   = conplanoreduz.c61_reduz  ";
        $sql .= "      left  join empagetipo    on  empagetipo.e83_conta     = saltes.k13_conta ";
        $sql .= "      inner join orctiporec    on o15_codigo = c61_codigo ";
        $sql .= "      inner join conplanocontabancaria on c56_codcon  = c61_codcon ";
        $sql .= "                                  and c56_reduz   =c61_reduz ";
        $sql .= "                                  and c56_anousu  =c61_anousu ";
        $sql .= "      inner join contabancaria ON c56_contabancaria = db83_sequencial ";
        $sql .= "      inner join concilia on k68_contabancaria = db83_sequencial ";


        $sql2 = "";
        if ($dbwhere=="") {
            if ($k13_conta!=null) {
                $sql2 .= " where saltes.k13_conta = $k13_conta ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql2 .= ($sql2!=""?" and ":" where ") . " c61_instit = " . db_getsession("DB_instit");
        $sql2 .= ($sql2!=""?" and ":" where ") . " c62_anousu = " . db_getsession("DB_anousu");
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

  public function sql_query_ListaContasExtras ( $k13_conta=null,$campos="*",$ordem=null,$dbwhere="")
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
     $sql .= " from saltes ";
     $sql .= "      join saltesextra on (k109_saltes) = (k13_conta)";
     $sql .= "      inner join conplanoreduz on  conplanoreduz.c61_reduz  = k109_contaextra
                                            and c61_anousu=".db_getsession("DB_anousu");
     $sql .= "      inner join conplanoexe   on  conplanoexe.c62_reduz    = conplanoreduz.c61_reduz
                                            and c61_anousu=c62_anousu";
     $sql .= "      inner join conplano      on  conplanoreduz.c61_codcon = conplano.c60_codcon
                                            and c61_anousu=c60_anousu";
     $sql .= "      left  join conplanoconta on  conplanoconta.c63_codcon = conplanoreduz.c61_codcon ";
     $sql .= "                              and conplanoconta.c63_anousu  = conplanoreduz.c61_anousu ";
     $sql .= "                              and conplanoconta.c63_reduz   = conplanoreduz.c61_reduz  ";
     $sql .= "      left  join empagetipo    on  empagetipo.e83_conta     = saltes.k13_conta ";
     $sql .= "      inner join orctiporec    on o15_codigo = c61_codigo ";
     $sql .= "      inner join conplanocontabancaria on c56_codcon  = c61_codcon ";
     $sql .= "                                  and c56_reduz   =c61_reduz ";
     $sql .= "                                  and c56_anousu  =c61_anousu ";
     $sql .= "      inner join contabancaria ON c56_contabancaria = db83_sequencial ";
     $sql .= "      inner join concilia on k68_contabancaria = db83_sequencial ";
     $sql .= "      join fonterecurso on orctiporec_id = o15_codigo ";
     $sql .= "                       and exercicio = " . db_getsession("DB_anousu");
     $sql2 = "";
     if($dbwhere==""){
       if($k13_conta!=null ){
         $sql2 .= " where saltes.k13_conta = $k13_conta ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql2 .= ($sql2!=""?" and ":" where ") . " c61_instit = " . db_getsession("DB_instit");
     $sql2 .= ($sql2!=""?" and ":" where ") . " c62_anousu = " . db_getsession("DB_anousu");
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

  public function sql_query_anousu($k13_conta = null, $campos = "*", $ordem = null, $dbwhere = "")
  {
        $anousu = db_getsession("DB_anousu");
        $sql = "
         select {$campos}
           from saltes
           join conplanoreduz on conplanoreduz.c61_reduz = saltes.k13_reduz
                and c61_anousu= {$anousu}
           join conplanoexe on conplanoexe.c62_reduz = conplanoreduz.c61_reduz
                and c61_anousu=c62_anousu
           join conplano on conplanoreduz.c61_codcon = conplano.c60_codcon
                and c61_anousu=c60_anousu
           left  join conplanoconta on  conplanoconta.c63_codcon = conplanoreduz.c61_codcon
                      and conplanoconta.c63_anousu = conplanoreduz.c61_anousu
                      and conplanoconta.c63_reduz = conplanoreduz.c61_reduz
           left  join empagetipo on  empagetipo.e83_conta = saltes.k13_conta
           join orctiporec on o15_codigo = c61_codigo
           join fonterecurso on orctiporec_id = o15_codigo
                and exercicio =  c61_anousu ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($k13_conta != null) {
                $sql2 .= " where saltes.k13_conta = $k13_conta ";
            }
        } else if ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql2 .= ($sql2 != "" ? " and " : " where ") . " c61_instit = " . db_getsession("DB_instit");
        $sql2 .= ($sql2 != "" ? " and " : " where ") . " c62_anousu = " . $anousu;
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
  }

    /**
     *
     * Verifica se o reduzido possui conta na Tesouraria
     * @return string
     */
    public function sql_query_movimentacao_tesouraria ($k13_conta=null,$campos="*",$ordem=null,$dbwhere="") {
    $sql  = "select ";
    if ($campos != "*" ) {

      $campos_sql = explode("#",$campos);
      $virgula = "";
      for ($i=0;$i<sizeof($campos_sql);$i++) {

        $sql .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    } else {
      $sql .= $campos;
    }
    $sql .= "       from saltes                                                                  ";
    $sql .= "            left join corrente    on corrente.k12_conta    = saltes.k13_reduz       ";
    $sql .= "            left join placaixarec on placaixarec.k81_conta = saltes.k13_reduz       ";
    $sql .= "            left join slip        on slip.k17_debito       = saltes.k13_reduz or    ";
    $sql .= "                                      slip.k17_credito      = saltes.k13_reduz      ";
  	$sql2 = "";

        if ($dbwhere == "") {
            if ($k13_conta!=null) {
                $sql2 .= " where saltes.k13_conta = $k13_conta ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;

        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

    public function sql_query_orcamento_recurso($iConta = null,
    $sInstituicoes,
    $iAnoUsu,
    $sCampos = "*",
    $sOrdem = null,
    $sWhere = "",
    $sGroupBy){
        $sql = "select ";
        $sql .= " {$sCampos} ";
        $sql .= " from saltes ";
        $sql .= " inner join conplanoexe on c62_anousu = " . db_getsession("DB_anousu") . " and c62_reduz = k13_conta ";
        $sql .= "					     inner join conplanoreduz on c61_anousu = {$iAnoUsu} and                      ";
        $sql .= "                                          c61_reduz = c62_reduz and                                  ";
        $sql .= "                                          c61_instit in({$sInstituicoes})                            ";
        $sql .= "					     inner join conplano on c61_codcon = c60_codcon and c61_anousu=c60_anousu     ";
        $sql .= "					     inner join orctiporec on o15_codigo = c61_codigo                             ";

        if ($sWhere == "") {
            if ($iConta != null) {
                $sql .= " where saltes.k13_conta = {$iConta} ";
            }
        } elseif ($sWhere != "") {
            $sql .= " where {$sWhere} ";
        }

        if ($sGroupBy != null) {
            $sql .= " group by {$sGroupBy} ";
        }

        if ($sOrdem != null) {
            $sql .= " order by {$sOrdem} ";
        }

        return $sql;
    }






  public function sql_query_contasImplantar($iAno, $iInstituicao)
    {

        $sqlConta    = " select * from ( ";
        $sqlConta   .= " select db83_sequencial, db83_descricao as db83_descricao ,c61_reduz";
        $sqlConta   .= "   from contabancaria ";
        $sqlConta   .= "        inner join conplanocontabancaria on conplanocontabancaria.c56_contabancaria =
                                                            contabancaria.db83_sequencial ";
        $sqlConta   .= "                                        and conplanocontabancaria.c56_anousu = {$iAno}";
        $sqlConta   .= "        inner join conplanoreduz on c61_codcon = c56_codcon  ";
        $sqlConta   .= "                                and c61_anousu = {$iAno}";
        $sqlConta   .= "                                and c61_instit = {$iInstituicao}";
        $sqlConta   .= "        left  join corrente on k12_conta       = c61_reduz ";
        $sqlConta   .= "        left  join concilia on db83_sequencial = k68_contabancaria ";
        $sqlConta   .= "  where k68_contabancaria is null ";

        $sqlConta   .= " union ";

        $sqlConta   .= " select db83_sequencial, db83_descricao as db83_descricao  ,c61_reduz ";
        $sqlConta   .= "   from contabancaria ";
        $sqlConta   .= "        inner join conplanocontabancaria on conplanocontabancaria.c56_contabancaria =
                                                            contabancaria.db83_sequencial ";
        $sqlConta   .= "                                        and conplanocontabancaria.c56_anousu = {$iAno}";
        $sqlConta   .= "        inner join conplanoreduz on c61_codcon = c56_codcon  ";
        $sqlConta   .= "                                and c61_anousu = {$iAno}";
        $sqlConta   .= "                                and c61_instit = {$iInstituicao}";
        $sqlConta   .= "        inner join corlanc on k12_conta       = c61_reduz ";
        $sqlConta   .= "        left  join concilia on db83_sequencial = k68_contabancaria ";
        $sqlConta   .= "  where k68_contabancaria is null ";

        $sqlConta   .= " union ";

        $sqlConta   .= " select  db83_sequencial, db83_descricao as db83_descricao   ,c61_reduz";
        $sqlConta   .= "   from contabancaria ";
        $sqlConta   .= "        inner join conplanocontabancaria on conplanocontabancaria.c56_contabancaria =
                                                            contabancaria.db83_sequencial ";
        $sqlConta   .= "                                        and conplanocontabancaria.c56_anousu = {$iAno}";
        $sqlConta   .= "        inner join conplanoreduz on c61_codcon = c56_codcon  ";
        $sqlConta   .= "                                and c61_anousu = {$iAno}";
        $sqlConta   .= "                                and c61_instit = {$iInstituicao}";
        $sqlConta   .= "        inner join extratolinha on k86_contabancaria = db83_sequencial ";
        $sqlConta   .= "        left  join concilia on db83_sequencial = k68_contabancaria ";
        $sqlConta   .= "  where k68_contabancaria is null ";
        $sqlConta   .= " ) as x order by c61_reduz  ";


        $sqlConta   = " select db83_sequencial, db83_descricao , array_to_string(array_agg(c61_reduz),', ') as c61_reduz";
        $sqlConta   .= "   from contabancaria ";
        $sqlConta   .= "        inner join conplanocontabancaria on conplanocontabancaria.c56_contabancaria = ";
        $sqlConta   .= "                                            contabancaria.db83_sequencial ";
        $sqlConta   .= "                                        and conplanocontabancaria.c56_anousu = {$iAno}";
        $sqlConta   .= "        inner join conplanoreduz on c61_codcon = c56_codcon  ";
        $sqlConta   .= "                                and c61_anousu = {$iAno}";
        $sqlConta   .= "                                and c61_instit = {$iInstituicao}";
        $sqlConta   .= "        inner join conplanoexe on c61_reduz = c62_reduz and c61_anousu = c62_anousu ";
        $sqlConta   .= " where contabancaria.db83_sequencial not in ( select k68_contabancaria from concilia ) ";
        $sqlConta   .= " group by db83_sequencial,db83_descricao";
        $sqlConta   .= " order by c61_reduz  ";



        return $sqlConta;
    }


  public function sql_query_DataContas($iAno, $iInstituicao, $conta, $reduzidos){

        $sql = <<<SQL


SELECT k12_data AS k12_data
FROM
  (

    SELECT DISTINCT k12_data
      FROM corrente
   INNER JOIN conplanoreduz ON conplanoreduz.c61_reduz = corrente.k12_conta
   AND conplanoreduz.c61_anousu = $iAno
   AND conplanoreduz.c61_instit = $iInstituicao
   INNER JOIN conplanocontabancaria ON conplanocontabancaria.c56_codcon = conplanoreduz.c61_codcon
   AND conplanocontabancaria.c56_anousu = conplanoreduz.c61_anousu
   AND conplanocontabancaria.c56_reduz = conplanoreduz.c61_reduz
   WHERE corrente.k12_conta in ($reduzidos)

   UNION ALL

   SELECT DISTINCT corlanc.k12_data
   FROM corlanc
   INNER JOIN slip ON corlanc.k12_codigo = slip.k17_codigo
   INNER JOIN conplanoreduz ON conplanoreduz.c61_reduz = corlanc.k12_conta
   AND conplanoreduz.c61_anousu = $iAno
   AND conplanoreduz.c61_instit = $iInstituicao
   INNER JOIN conplanocontabancaria ON conplanocontabancaria.c56_codcon = conplanoreduz.c61_codcon
   AND conplanocontabancaria.c56_anousu = conplanoreduz.c61_anousu
   AND conplanocontabancaria.c56_reduz = conplanoreduz.c61_reduz
   WHERE (slip.k17_credito in ($reduzidos)
          OR slip.k17_debito in ($reduzidos))


   UNION ALL


   SELECT DISTINCT k86_data
   FROM extratolinha
   LEFT JOIN conciliaextrato ON k87_extratolinha = k86_sequencial
   LEFT JOIN concilia ON k68_data = k86_data
   AND k68_contabancaria = k86_contabancaria
   WHERE k87_extratolinha IS NULL
     AND (k68_data IS NULL
          AND k68_contabancaria IS NULL)
     AND k86_contabancaria = $conta

     ) AS xx

ORDER BY k12_data DESC


SQL;

        return $sql;
    }

    public function sqlContas($campos, $where = [], $order = null)
    {
        $anousu = db_getsession("DB_anousu");
        $sql = "
        select {$campos}
          from saltes
          join conplanoreduz on conplanoreduz.c61_reduz = saltes.k13_reduz
               and conplanoreduz.c61_anousu = {$anousu}
          join conplanoexe on conplanoexe.c62_reduz = conplanoreduz.c61_reduz
               and conplanoexe.c62_anousu = conplanoreduz.c61_anousu
          join conplano on conplano.c60_codcon = conplanoreduz.c61_codcon
               and conplano.c60_anousu = conplanoreduz.c61_anousu
          left  join conplanoconta on  conplanoconta.c63_codcon = conplanoreduz.c61_codcon
                     and conplanoconta.c63_anousu = conplanoreduz.c61_anousu
                     and conplanoconta.c63_reduz = conplanoreduz.c61_reduz
          join orctiporec on orctiporec.o15_codigo = conplanoreduz.c61_codigo
          join fonterecurso on fonterecurso.orctiporec_id = orctiporec.o15_codigo
               and fonterecurso.exercicio = conplanoreduz.c61_anousu
        ";

        if (!empty($where)) {
            $sql .= " where " . implode(' and ', $where);
        }

        if (!empty($order)) {
            $sql .= " order by {$order}";
        }

        return $sql;
    }


  /**
   * Gera sql para buscar as contas nas quais o usuário informado tem permissão.
   * @param int $usuario id do usuário
   * @param int $departamento codigo do departamento
   * @return string
   */
    public function sql_query_contas_usuario($usuario = null, $departamento = null)
    {

        if (empty($usuario)) {
            $usuario = db_getsession("DB_id_usuario");
        }

        if (empty($departamento)) {
            $departamento = db_getsession('DB_coddepto');
        }

        $sql =  " select k13_conta as saltes";
        $sql .= "   from saltes ";
        $sql .= "        inner join saltesdepartamento on saltesdepartamento.k212_saltes = saltes.k13_conta  ";
        $sql .= "        inner join db_depusu on db_depusu.coddepto = saltesdepartamento.k212_departamento";
        $sql .= "  where (db_depusu.id_usuario = {$usuario} ";
        $sql .= "    and db_depusu.coddepto = {$departamento})";

        return $sql;
    }
}
