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

class cl_clabens
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
    public $t64_codcla = 0;
    public $t64_class = null;
    public $t64_descr = null;
    public $t64_obs = null;
    public $t64_analitica = 'f';
    public $t64_bemtipos = 0;
    public $t64_benstipodepreciacao = 0;
    public $t64_vidautil = 0;
    public $t64_instit = 0;
    public $t64_valorresidual = 0;
    public $t64_ativa = 'f';
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 t64_codcla = int8 = Código
                 t64_class = varchar(50) = Classificação
                 t64_descr = varchar(50) = Descrição
                 t64_obs = text = Observações
                 t64_analitica = bool = Analitica
                 t64_bemtipos = int4 = Bem Tipo
                 t64_benstipodepreciacao = int4 = Tipo de Depreciação
                 t64_vidautil = float4 = Vida Útil
                 t64_instit = int4 = Instituição
                 t64_valorresidual = int4 = Valor Residual
                 t64_ativa = bool = Status da classificacao
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("clabens");
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
            $this->t64_codcla = ($this->t64_codcla == ""?@$GLOBALS["HTTP_POST_VARS"]["t64_codcla"]:$this->t64_codcla);
            $this->t64_class = ($this->t64_class == ""?@$GLOBALS["HTTP_POST_VARS"]["t64_class"]:$this->t64_class);
            $this->t64_descr = ($this->t64_descr == ""?@$GLOBALS["HTTP_POST_VARS"]["t64_descr"]:$this->t64_descr);
            $this->t64_obs = ($this->t64_obs == ""?@$GLOBALS["HTTP_POST_VARS"]["t64_obs"]:$this->t64_obs);
            $this->t64_analitica = ($this->t64_analitica == "f"?@$GLOBALS["HTTP_POST_VARS"]["t64_analitica"]:$this->t64_analitica);
            $this->t64_bemtipos = ($this->t64_bemtipos == ""?@$GLOBALS["HTTP_POST_VARS"]["t64_bemtipos"]:$this->t64_bemtipos);
            $this->t64_benstipodepreciacao = ($this->t64_benstipodepreciacao == ""?@$GLOBALS["HTTP_POST_VARS"]["t64_benstipodepreciacao"]:$this->t64_benstipodepreciacao);
            $this->t64_vidautil = ($this->t64_vidautil == ""?@$GLOBALS["HTTP_POST_VARS"]["t64_vidautil"]:$this->t64_vidautil);
            $this->t64_instit = ($this->t64_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["t64_instit"]:$this->t64_instit);
            $this->t64_valorresidual = ($this->t64_valorresidual == ""?@$GLOBALS["HTTP_POST_VARS"]["t64_valorresidual"]:$this->t64_valorresidual);
            $this->t64_ativa = ($this->t64_ativa == "f"?@$GLOBALS["HTTP_POST_VARS"]["t64_ativa"]:$this->t64_ativa);
        } else {
            $this->t64_codcla = ($this->t64_codcla == ""?@$GLOBALS["HTTP_POST_VARS"]["t64_codcla"]:$this->t64_codcla);
        }
    }

    public function incluir($t64_codcla)
    {
        $this->atualizacampos();
        if ($this->t64_class == null) {
            $this->erro_sql = " Campo Classificação não informado.";
            $this->erro_campo = "t64_class";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->t64_descr == null) {
            $this->erro_sql = " Campo Descrição não informado.";
            $this->erro_campo = "t64_descr";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->t64_analitica == null) {
            $this->erro_sql = " Campo Analitica não informado.";
            $this->erro_campo = "t64_analitica";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->t64_bemtipos == null) {
            $this->erro_sql = " Campo Bem Tipo não informado.";
            $this->erro_campo = "t64_bemtipos";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->t64_benstipodepreciacao == null) {
            $this->erro_sql = " Campo Tipo de Depreciação não informado.";
            $this->erro_campo = "t64_benstipodepreciacao";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->t64_vidautil == null) {
            $this->erro_sql = " Campo Vida Útil não informado.";
            $this->erro_campo = "t64_vidautil";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->t64_instit == null) {
            $this->erro_sql = " Campo Instituição não informado.";
            $this->erro_campo = "t64_instit";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->t64_valorresidual == null) {
            $this->t64_valorresidual = "0";
        }
        if ($this->t64_ativa == null) {
            $this->erro_sql = " Campo Status da classificacao não informado.";
            $this->erro_campo = "t64_ativa";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($t64_codcla == "" || $t64_codcla == null) {
            $result = db_query("select nextval('clabens_t64_codcla_seq')");
            if (!$result) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql   = "Verifique o cadastro da sequencia: clabens_t64_codcla_seq do campo: t64_codcla";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->t64_codcla = pg_fetch_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from clabens_t64_codcla_seq");
            if ($result && (pg_fetch_result($result, 0, 0) < $t64_codcla)) {
                $this->erro_sql = " Campo t64_codcla maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->t64_codcla = $t64_codcla;
            }
        }
        if (($this->t64_codcla == null) || ($this->t64_codcla == "")) {
            $this->erro_sql = " Campo t64_codcla não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into clabens(
                                       t64_codcla
                                      ,t64_class
                                      ,t64_descr
                                      ,t64_obs
                                      ,t64_analitica
                                      ,t64_bemtipos
                                      ,t64_benstipodepreciacao
                                      ,t64_vidautil
                                      ,t64_instit
                                      ,t64_valorresidual
                                      ,t64_ativa
                       )
                values (
                                $this->t64_codcla
                               ,'$this->t64_class'
                               ,'$this->t64_descr'
                               ,'$this->t64_obs'
                               ,'$this->t64_analitica'
                               ,$this->t64_bemtipos
                               ,$this->t64_benstipodepreciacao
                               ,$this->t64_vidautil
                               ,$this->t64_instit
                               ,$this->t64_valorresidual
                               ,'$this->t64_ativa'
                      )";
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Classificação dos bens ($this->t64_codcla) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Classificação dos bens já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "Classificação dos bens ($this->t64_codcla) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->t64_codcla;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            $resaco = $this->sql_record($this->sql_query_file($this->t64_codcla));
            if ($resaco ||($this->numrows!=0)) {
                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_fetch_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                $resac = db_query("insert into db_acountkey values($acount,5765,'$this->t64_codcla','I')");
                $resac = db_query("insert into db_acount values($acount,925,5765,'','".AddSlashes(pg_fetch_result($resaco, 0, 't64_codcla'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,925,5809,'','".AddSlashes(pg_fetch_result($resaco, 0, 't64_class'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,925,5810,'','".AddSlashes(pg_fetch_result($resaco, 0, 't64_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,925,5811,'','".AddSlashes(pg_fetch_result($resaco, 0, 't64_obs'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,925,5887,'','".AddSlashes(pg_fetch_result($resaco, 0, 't64_analitica'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,925,17205,'','".AddSlashes(pg_fetch_result($resaco, 0, 't64_bemtipos'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,925,18583,'','".AddSlashes(pg_fetch_result($resaco, 0, 't64_benstipodepreciacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,925,18594,'','".AddSlashes(pg_fetch_result($resaco, 0, 't64_vidautil'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,925,19552,'','".AddSlashes(pg_fetch_result($resaco, 0, 't64_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,925,1013636,'','".AddSlashes(pg_fetch_result($resaco, 0, 't64_valorresidual'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,925,1015042,'','".AddSlashes(pg_fetch_result($resaco, 0, 't64_ativa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
            }
        }
        return true;
    }

    public function alterar($t64_codcla = null)
    {
        $this->atualizacampos();
        $sql = " update clabens set ";
        $virgula = "";
        if (trim($this->t64_codcla)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t64_codcla"])) {
            $sql  .= $virgula." t64_codcla = $this->t64_codcla ";
            $virgula = ",";
            if (trim($this->t64_codcla) == null) {
                $this->erro_sql = " Campo Código não informado.";
                $this->erro_campo = "t64_codcla";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->t64_class)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t64_class"])) {
            $sql  .= $virgula." t64_class = '$this->t64_class' ";
            $virgula = ",";
            if (trim($this->t64_class) == null) {
                $this->erro_sql = " Campo Classificação não informado.";
                $this->erro_campo = "t64_class";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->t64_descr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t64_descr"])) {
            $sql  .= $virgula." t64_descr = '$this->t64_descr' ";
            $virgula = ",";
            if (trim($this->t64_descr) == null) {
                $this->erro_sql = " Campo Descrição não informado.";
                $this->erro_campo = "t64_descr";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->t64_obs)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t64_obs"])) {
            $sql  .= $virgula." t64_obs = '$this->t64_obs' ";
            $virgula = ",";
        }
        if (trim($this->t64_analitica)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t64_analitica"])) {
            $sql  .= $virgula." t64_analitica = '$this->t64_analitica' ";
            $virgula = ",";
            if (trim($this->t64_analitica) == null) {
                $this->erro_sql = " Campo Analitica não informado.";
                $this->erro_campo = "t64_analitica";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->t64_bemtipos)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t64_bemtipos"])) {
            $sql  .= $virgula." t64_bemtipos = $this->t64_bemtipos ";
            $virgula = ",";
            if (trim($this->t64_bemtipos) == null) {
                $this->erro_sql = " Campo Bem Tipo não informado.";
                $this->erro_campo = "t64_bemtipos";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->t64_benstipodepreciacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t64_benstipodepreciacao"])) {
            $sql  .= $virgula." t64_benstipodepreciacao = $this->t64_benstipodepreciacao ";
            $virgula = ",";
            if (trim($this->t64_benstipodepreciacao) == null) {
                $this->erro_sql = " Campo Tipo de Depreciação não informado.";
                $this->erro_campo = "t64_benstipodepreciacao";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->t64_vidautil)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t64_vidautil"])) {
            $sql  .= $virgula." t64_vidautil = $this->t64_vidautil ";
            $virgula = ",";
            if (trim($this->t64_vidautil) == null) {
                $this->erro_sql = " Campo Vida Útil não informado.";
                $this->erro_campo = "t64_vidautil";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->t64_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t64_instit"])) {
            $sql  .= $virgula." t64_instit = $this->t64_instit ";
            $virgula = ",";
            if (trim($this->t64_instit) == null) {
                $this->erro_sql = " Campo Instituição não informado.";
                $this->erro_campo = "t64_instit";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->t64_valorresidual)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t64_valorresidual"])) {
            if (trim($this->t64_valorresidual)=="" && isset($GLOBALS["HTTP_POST_VARS"]["t64_valorresidual"])) {
                $this->t64_valorresidual = "0" ;
            }
            $sql  .= $virgula." t64_valorresidual = $this->t64_valorresidual ";
            $virgula = ",";
        }
        if (trim($this->t64_ativa)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t64_ativa"])) {
            $sql  .= $virgula." t64_ativa = '$this->t64_ativa' ";
            $virgula = ",";
            if (trim($this->t64_ativa) == null) {
                $this->erro_sql = " Campo Status da classificacao não informado.";
                $this->erro_campo = "t64_ativa";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($t64_codcla!=null) {
            $sql .= " t64_codcla = $this->t64_codcla";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            $resaco = $this->sql_record($this->sql_query_file($this->t64_codcla));
            if ($this->numrows > 0) {
                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {
                      $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                      $acount = pg_fetch_result($resac, 0, 0);
                      $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                      $resac = db_query("insert into db_acountkey values($acount,5765,'$this->t64_codcla','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["t64_codcla"]) || $this->t64_codcla != "") {
                        $resac = db_query("insert into db_acount values($acount,925,5765,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 't64_codcla'))."','$this->t64_codcla',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["t64_class"]) || $this->t64_class != "") {
                        $resac = db_query("insert into db_acount values($acount,925,5809,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 't64_class'))."','$this->t64_class',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["t64_descr"]) || $this->t64_descr != "") {
                        $resac = db_query("insert into db_acount values($acount,925,5810,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 't64_descr'))."','$this->t64_descr',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["t64_obs"]) || $this->t64_obs != "") {
                        $resac = db_query("insert into db_acount values($acount,925,5811,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 't64_obs'))."','$this->t64_obs',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["t64_analitica"]) || $this->t64_analitica != "") {
                        $resac = db_query("insert into db_acount values($acount,925,5887,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 't64_analitica'))."','$this->t64_analitica',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["t64_bemtipos"]) || $this->t64_bemtipos != "") {
                        $resac = db_query("insert into db_acount values($acount,925,17205,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 't64_bemtipos'))."','$this->t64_bemtipos',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["t64_benstipodepreciacao"]) || $this->t64_benstipodepreciacao != "") {
                        $resac = db_query("insert into db_acount values($acount,925,18583,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 't64_benstipodepreciacao'))."','$this->t64_benstipodepreciacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["t64_vidautil"]) || $this->t64_vidautil != "") {
                        $resac = db_query("insert into db_acount values($acount,925,18594,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 't64_vidautil'))."','$this->t64_vidautil',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["t64_instit"]) || $this->t64_instit != "") {
                        $resac = db_query("insert into db_acount values($acount,925,19552,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 't64_instit'))."','$this->t64_instit',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["t64_valorresidual"]) || $this->t64_valorresidual != "") {
                        $resac = db_query("insert into db_acount values($acount,925,1013636,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 't64_valorresidual'))."','$this->t64_valorresidual',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["t64_ativa"]) || $this->t64_ativa != "") {
                        $resac = db_query("insert into db_acount values($acount,925,1015042,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 't64_ativa'))."','$this->t64_ativa',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Classificação dos bens não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->t64_codcla;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Classificação dos bens não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->t64_codcla;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$this->t64_codcla;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($t64_codcla = null, $dbwhere = null)
    {
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            if (empty($dbwhere)) {
                $resaco = $this->sql_record($this->sql_query_file($t64_codcla));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
            }
            if (($resaco != false) || ($this->numrows!=0)) {
                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {
                    $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_fetch_result($resac, 0, 0);
                    $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                    $resac  = db_query("insert into db_acountkey values($acount,5765,'$t64_codcla','E')");
                    $resac  = db_query("insert into db_acount values($acount,925,5765,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 't64_codcla'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,925,5809,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 't64_class'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,925,5810,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 't64_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,925,5811,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 't64_obs'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,925,5887,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 't64_analitica'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,925,17205,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 't64_bemtipos'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,925,18583,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 't64_benstipodepreciacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,925,18594,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 't64_vidautil'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,925,19552,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 't64_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,925,1013636,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 't64_valorresidual'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,925,1015042,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 't64_ativa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                }
            }
        }

        $sql = " delete from clabens
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($t64_codcla)) {
                if (!empty($sql2)) {
                     $sql2 .= " and ";
                }
                $sql2 .= " t64_codcla = $t64_codcla ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Classificação dos bens não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$t64_codcla;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Classificação dos bens não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$t64_codcla;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$t64_codcla;
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
            $this->erro_sql   = "Record Vazio na Tabela:clabens";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($t64_codcla = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos}";
        $sql .= " from clabens ";
        $sql .= "      inner join db_config  on  db_config.codigo = clabens.t64_instit";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
        $sql .= "      inner join bemtipos  on  bemtipos.t24_sequencial = clabens.t64_bemtipos";
        $sql .= "      inner join clabensconplano on clabensconplano.t86_clabens = clabens.t64_codcla ";
        $sql .= "                                and clabensconplano.t86_anousu  = ".db_getsession("DB_anousu");
        $sql .= "      left  join conplano  on  conplano.c60_codcon = clabensconplano.t86_conplano";
        $sql .= "                           and conplano.c60_anousu = ".db_getsession("DB_anousu");
        $sql .= "      left  join conplanoreduz on  conplanoreduz.c61_codcon = conplano.c60_codcon";
        $sql .= "                               and conplanoreduz.c61_anousu = ".db_getsession("DB_anousu");
        $sql .= "                               and conplanoreduz.c61_instit = ".db_getsession("DB_instit");
        $sql .= "      left join conclass  on  conclass.c51_codcla = conplano.c60_codcla";
        $sql .= "      left join consistema  on  consistema.c52_codsis = conplano.c60_codsis";
        $sql .= "      left join benstipodepreciacao  on clabens.t64_benstipodepreciacao = benstipodepreciacao.t46_sequencial";
        $sql .= "      left  join db_depart  on  db_depart.coddepto = db_config.db21_departamento";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($t64_codcla)) {
                $sql2 .= " where clabens.t64_codcla = $t64_codcla ";
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

    public function sql_query_file($t64_codcla = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos} ";
        $sql .= "  from clabens ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($t64_codcla)) {
                $sql2 .= " where clabens.t64_codcla = $t64_codcla ";
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

    function sql_query_sem_plano($t64_codcla = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from clabens ";
        $sql .= "      inner join bemtipos  on  bemtipos.t24_sequencial = clabens.t64_bemtipos";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($t64_codcla!=null) {
                $sql2 .= " where clabens.t64_codcla = $t64_codcla ";
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
    public function sql_query_itensporclassificacao($t64_codcla = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from clabens ";
        $sql .= "   INNER JOIN bens on bens.t52_codcla = clabens.t64_codcla 																							";
        $sql .= "   INNER JOIN cfpatriinstituicao on t59_instituicao = t52_instit																					";
        $sql .= "    LEFT JOIN bensdepreciacao on bensdepreciacao.t44_bens = bens.t52_bem 															  ";
        $sql .= "    LEFT JOIN benstipodepreciacao on benstipodepreciacao.t46_sequencial = bensdepreciacao.t44_benstipodepreciacao";
        $sql .= "    left join bensbaix        on bensbaix.t55_codbem       = bens.t52_bem        												";
        $sql2 = "";

        if ($dbwhere=="") {
            if ($t64_codcla!=null) {
                $sql2 .= " where clabens.t64_codcla = $t64_codcla ";
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
    public function sql_query_contas($t64_codcla = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from clabens ";
        $sql .= "      inner join bemtipos           on  bemtipos.t24_sequencial     = clabens.t64_bemtipos";
        $sql .= "      left join clabensconplano    on  clabensconplano.t86_clabens = clabens.t64_codcla";
        $sql .= "                                    and clabensconplano.t86_anousu = ".db_getsession("DB_anousu");
        $sql .= "      left join conplano  as conta on  conta.c60_codcon  = clabensconplano.t86_conplano";
        $sql .= "                           and conta.c60_anousu = ".db_getsession("DB_anousu");
        $sql .= "      left join conplano  as contadepreciacao on  contadepreciacao.c60_codcon  = clabensconplano.t86_conplanodepreciacao";
        $sql .= "                           and contadepreciacao.c60_anousu = ".db_getsession("DB_anousu");
        $sql .= "      left join benstipodepreciacao  on clabens.t64_benstipodepreciacao = benstipodepreciacao.t46_sequencial";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($t64_codcla!=null) {
                $sql2 .= " where clabens.t64_codcla = $t64_codcla";
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
}
