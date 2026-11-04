<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009 DBSeller Servicos de Informatica
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

//MODULO: fiscal
//CLASSE DA ENTIDADE fiscal
class cl_fis_fiscal
{
    // cria variaveis de erro
    public $rotulo     = null;
    public $query_sql  = null;
    public $numrows    = 0;
    public $numrows_incluir = 0;
    public $numrows_alterar = 0;
    public $numrows_excluir = 0;
    public $erro_status= null;
    public $erro_sql   = null;
    public $erro_banco = null;
    public $erro_msg   = null;
    public $erro_campo = null;
    public $pagina_retorno = null;
    // cria variaveis do arquivo
    public $y30_codnoti = 0;
    public $y30_data_dia = null;
    public $y30_data_mes = null;
    public $y30_data_ano = null;
    public $y30_data = null;
    public $y30_hora = null;
    public $y30_obs = null;
    public $y30_setor = 0;
    public $y30_nome = null;
    public $y30_dtvenc_dia = null;
    public $y30_dtvenc_mes = null;
    public $y30_dtvenc_ano = null;
    public $y30_dtvenc = null;
    public $y30_numbloco = null;
    public $y30_prazorec_dia = null;
    public $y30_prazorec_mes = null;
    public $y30_prazorec_ano = null;
    public $y30_prazorec = null;
    public $y30_dtlanc_dia = null;
    public $y30_dtlanc_mes = null;
    public $y30_dtlanc_ano = null;
    public $y30_dtlanc = null;
    public $y30_instit = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 y30_codnoti = int8 = Código da Notificação
                 y30_data = date = Data da Notificação
                 y30_hora = char(5) = Hora da Notificação
                 y30_obs = text = Observação da Notificação
                 y30_setor = int4 = Código do Departamento
                 y30_nome = varchar(50) = Nome da Pessoa Notificada
                 y30_dtvenc = date = Vencimento Atualizada
                 y30_numbloco = varchar(20) = Número do Bloco
                 y30_prazorec = date = Prazo p/ Recurso
                 y30_dtlanc = date = Data de lançamento
                 y30_instit = int4 = Cod. Instituição
                 ";
    //funcao construtor da classe
    public function __construct()
    {
      //classes dos rotulos dos campos
        $this->rotulo = new rotulo("fis_fiscal");
        $this->pagina_retorno =  basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
    }
    //funcao erro
    public function erro($mostra, $retorna)
    {
        if (($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )) {
            echo "<script>alert(\"".$this->erro_msg."\");</script>";
            if ($retorna==true) {
                echo "<script>location.href='".$this->pagina_retorno."'</script>";
            }
        }
    }
    // funcao para atualizar campos
    public function atualizacampos($exclusao = false)
    {
        if (!$exclusao) {
            $this->y30_codnoti = ($this->y30_codnoti == ""?@$GLOBALS["HTTP_POST_VARS"]["y30_codnoti"]:$this->y30_codnoti);
            if ($this->y30_data == "") {
                $this->y30_data_dia = ($this->y30_data_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y30_data_dia"]:$this->y30_data_dia);
                $this->y30_data_mes = ($this->y30_data_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y30_data_mes"]:$this->y30_data_mes);
                $this->y30_data_ano = ($this->y30_data_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y30_data_ano"]:$this->y30_data_ano);
                if ($this->y30_data_dia != "") {
                     $this->y30_data = $this->y30_data_ano."-".$this->y30_data_mes."-".$this->y30_data_dia;
                }
            }
            $this->y30_hora = ($this->y30_hora == ""?@$GLOBALS["HTTP_POST_VARS"]["y30_hora"]:$this->y30_hora);
            $this->y30_obs = ($this->y30_obs == ""?@$GLOBALS["HTTP_POST_VARS"]["y30_obs"]:$this->y30_obs);
            $this->y30_setor = ($this->y30_setor == ""?@$GLOBALS["HTTP_POST_VARS"]["y30_setor"]:$this->y30_setor);
            $this->y30_nome = ($this->y30_nome == ""?@$GLOBALS["HTTP_POST_VARS"]["y30_nome"]:$this->y30_nome);
            if ($this->y30_dtvenc == "") {
                $this->y30_dtvenc_dia = ($this->y30_dtvenc_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y30_dtvenc_dia"]:$this->y30_dtvenc_dia);
                $this->y30_dtvenc_mes = ($this->y30_dtvenc_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y30_dtvenc_mes"]:$this->y30_dtvenc_mes);
                $this->y30_dtvenc_ano = ($this->y30_dtvenc_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y30_dtvenc_ano"]:$this->y30_dtvenc_ano);
                if ($this->y30_dtvenc_dia != "") {
                    $this->y30_dtvenc = $this->y30_dtvenc_ano."-".$this->y30_dtvenc_mes."-".$this->y30_dtvenc_dia;
                }
            }
            $this->y30_numbloco = ($this->y30_numbloco == ""?@$GLOBALS["HTTP_POST_VARS"]["y30_numbloco"]:$this->y30_numbloco);
            if ($this->y30_prazorec == "") {
                $this->y30_prazorec_dia = ($this->y30_prazorec_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y30_prazorec_dia"]:$this->y30_prazorec_dia);
                $this->y30_prazorec_mes = ($this->y30_prazorec_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y30_prazorec_mes"]:$this->y30_prazorec_mes);
                $this->y30_prazorec_ano = ($this->y30_prazorec_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y30_prazorec_ano"]:$this->y30_prazorec_ano);
                if ($this->y30_prazorec_dia != "") {
                    $this->y30_prazorec = $this->y30_prazorec_ano."-".$this->y30_prazorec_mes."-".$this->y30_prazorec_dia;
                }
            }
            if ($this->y30_dtlanc == "") {
                $this->y30_dtlanc_dia = ($this->y30_dtlanc_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y30_dtlanc_dia"]:$this->y30_dtlanc_dia);
                $this->y30_dtlanc_mes = ($this->y30_dtlanc_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y30_dtlanc_mes"]:$this->y30_dtlanc_mes);
                $this->y30_dtlanc_ano = ($this->y30_dtlanc_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y30_dtlanc_ano"]:$this->y30_dtlanc_ano);
                if ($this->y30_dtlanc_dia != "") {
                    $this->y30_dtlanc = $this->y30_dtlanc_ano."-".$this->y30_dtlanc_mes."-".$this->y30_dtlanc_dia;
                }
            }
            $this->y30_instit = ($this->y30_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["y30_instit"]:$this->y30_instit);
        } else {
            $this->y30_codnoti = ($this->y30_codnoti == ""?@$GLOBALS["HTTP_POST_VARS"]["y30_codnoti"]:$this->y30_codnoti);
        }
    }
   // funcao para inclusao
    public function incluir($y30_codnoti = null)
    {
        $this->atualizacampos();
        if ($this->y30_data == null) {
            $this->erro_sql = " Campo Data da Notificação nao Informado.";
            $this->erro_campo = "y30_data_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y30_hora == null) {
            $this->erro_sql = " Campo Hora da Notificação nao Informado.";
            $this->erro_campo = "y30_hora";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y30_setor == null) {
            $this->erro_sql = " Campo Código do Departamento nao Informado.";
            $this->erro_campo = "y30_setor";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y30_dtvenc == null) {
            $this->y30_dtvenc = "null";
        }
        if ($this->y30_prazorec == null) {
            $this->y30_prazorec = "null";
        }
        if ($this->y30_dtlanc == null) {
            $this->erro_sql = " Campo Data de lançamento nao Informado.";
            $this->erro_campo = "y30_dtlanc_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y30_instit == null) {
            $this->erro_sql = " Campo Cod. Instituição nao Informado.";
            $this->erro_campo = "y30_instit";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into fiscalizacao.fis_fiscal(
                    ".(empty($this->y30_codnoti) ? '' : 'y30_codnoti,')."
                                      y30_data
                                      ,y30_hora
                                      ,y30_obs
                                      ,y30_setor
                                      ,y30_nome
                                      ,y30_dtvenc
                                      ,y30_numbloco
                                      ,y30_prazorec
                                      ,y30_dtlanc
                                      ,y30_instit
                       )
                values (
                               ".(empty($this->y30_codnoti) ? '' : $this->y30_codnoti . ',')."
                               ".($this->y30_data == "null" || $this->y30_data == ""?"null":"'".$this->y30_data."'")."
                               ,'$this->y30_hora'
                               ,'$this->y30_obs'
                               ,$this->y30_setor
                               ,'$this->y30_nome'
                               ,".($this->y30_dtvenc == "null" || $this->y30_dtvenc == ""?"null":"'".$this->y30_dtvenc."'")."
                               ,'$this->y30_numbloco'
                               ,".($this->y30_prazorec == "null" || $this->y30_prazorec == ""?"null":"'".$this->y30_prazorec."'")."
                               ,".($this->y30_dtlanc == "null" || $this->y30_dtlanc == ""?"null":"'".$this->y30_dtlanc."'")."
                               ,$this->y30_instit
                      ) returning y30_codnoti ";
        $result = db_query($sql);
        $this->y30_codnoti = db_utils::fieldsmemory($result, 0)->y30_codnoti;
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "fiscal ($this->y30_codnoti) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "fiscal já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "fiscal ($this->y30_codnoti) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->y30_codnoti;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);

        return true;
    }
   // funcao para alteracao
    public function alterar($y30_codnoti = null)
    {
        $this->atualizacampos();
        $sql = " update fiscalizacao.fis_fiscal set ";
        $virgula = "";
        if (trim($this->y30_data)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y30_data_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y30_data_dia"] !="")) {
            $sql  .= $virgula." y30_data = '$this->y30_data' ";
            $virgula = ",";
            if (trim($this->y30_data) == null) {
                $this->erro_sql = " Campo Data da Notificação nao Informado.";
                $this->erro_campo = "y30_data_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y30_data_dia"])) {
                $sql  .= $virgula." y30_data = null ";
                $virgula = ",";
                if (trim($this->y30_data) == null) {
                    $this->erro_sql = " Campo Data da Notificação nao Informado.";
                    $this->erro_campo = "y30_data_dia";
                    $this->erro_banco = "";
                    $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        if (trim($this->y30_hora)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y30_hora"])) {
            $sql  .= $virgula." y30_hora = '$this->y30_hora' ";
            $virgula = ",";
            if (trim($this->y30_hora) == null) {
                $this->erro_sql = " Campo Hora da Notificação nao Informado.";
                $this->erro_campo = "y30_hora";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y30_obs)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y30_obs"])) {
            $sql  .= $virgula." y30_obs = '$this->y30_obs' ";
            $virgula = ",";
        }
        if (trim($this->y30_setor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y30_setor"])) {
            $sql  .= $virgula." y30_setor = $this->y30_setor ";
            $virgula = ",";
            if (trim($this->y30_setor) == null) {
                $this->erro_sql = " Campo Código do Departamento nao Informado.";
                $this->erro_campo = "y30_setor";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y30_nome)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y30_nome"])) {
            $sql  .= $virgula." y30_nome = '$this->y30_nome' ";
            $virgula = ",";
        }
        if (trim($this->y30_dtvenc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y30_dtvenc_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y30_dtvenc_dia"] !="")) {
            $sql  .= $virgula." y30_dtvenc = '$this->y30_dtvenc' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y30_dtvenc_dia"])) {
                $sql  .= $virgula." y30_dtvenc = null ";
                $virgula = ",";
            }
        }
        if (trim($this->y30_numbloco)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y30_numbloco"])) {
            $sql  .= $virgula." y30_numbloco = '$this->y30_numbloco' ";
            $virgula = ",";
        }
        if (trim($this->y30_prazorec)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y30_prazorec_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y30_prazorec_dia"] !="")) {
            $sql  .= $virgula." y30_prazorec = '$this->y30_prazorec' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y30_prazorec_dia"])) {
                $sql  .= $virgula." y30_prazorec = null ";
                $virgula = ",";
            }
        }
        if (trim($this->y30_dtlanc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y30_dtlanc_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y30_dtlanc_dia"] !="")) {
            $sql  .= $virgula." y30_dtlanc = '$this->y30_dtlanc' ";
            $virgula = ",";
            if (trim($this->y30_dtlanc) == null) {
                $this->erro_sql = " Campo Data de lançamento nao Informado.";
                $this->erro_campo = "y30_dtlanc_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y30_dtlanc_dia"])) {
                $sql  .= $virgula." y30_dtlanc = null ";
                $virgula = ",";
                if (trim($this->y30_dtlanc) == null) {
                    $this->erro_sql = " Campo Data de lançamento nao Informado.";
                    $this->erro_campo = "y30_dtlanc_dia";
                    $this->erro_banco = "";
                    $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        if (trim($this->y30_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y30_instit"])) {
            $sql  .= $virgula." y30_instit = $this->y30_instit ";
            $virgula = ",";
            if (trim($this->y30_instit) == null) {
                $this->erro_sql = " Campo Cod. Instituição nao Informado.";
                $this->erro_campo = "y30_instit";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($y30_codnoti!=null) {
            $sql .= " y30_codnoti = $this->y30_codnoti";
        }

        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "fiscal nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->y30_codnoti;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "fiscal nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->y30_codnoti;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->y30_codnoti;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao para exclusao
    public function excluir($y30_codnoti = null, $dbwhere = null)
    {
        $sql = " delete from fiscalizacao.fis_fiscal where ";
        $sql2 = "";
        if ($dbwhere == null || $dbwhere == "") {
            if ($y30_codnoti != "") {
                if ($sql2!="") {
                    $sql2 .= " and ";
                }
                $sql2 .= " y30_codnoti = $y30_codnoti ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "fiscal nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y30_codnoti;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "fiscal nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y30_codnoti;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$y30_codnoti;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao do recordset
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
        if ($this->numrows==0) {
            $this->erro_banco = "";
            $this->erro_sql   = "Record Vazio na Tabela:fiscal";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
    public function sql_query($y30_codnoti = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_fiscal ";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_fiscal.y30_setor";
        $sql .= "      inner join db_config  on  db_config.codigo = db_depart.instit";
        $sql2 = "";
        if ($dbwhere != "") {
            if ($y30_codnoti!=null) {
                  $sql2 .= " where fis_fiscal.y30_codnoti = $y30_codnoti and $dbwhere";
            } else {
                $sql2 .= " where $dbwhere";
            }
        } else {
            if ($y30_codnoti!=null) {
                  $sql2 .= " where fis_fiscal.y30_codnoti = $y30_codnoti ";
            }
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
    public function sql_query_busca($y30_codnoti = null, $dbwhere = "")
    {

        $sql = "select dl_noti, dl_identifica, dl_codigo, z01_nome, y30_setor, y30_data, y30_prazorec, y30_dtvenc, nome as fiscal
		         from  (select y30_setor, nome, descrdepto,
                           y30_codnoti as dl_noti, y30_data, y30_prazorec, y30_dtvenc,y30_instit,
                           case when q02_numcgm is not null then 'Inscrição'  else
                                 (case when j01_numcgm is not null then 'Matrícula' else
                                        (case when y80_numcgm is not null then 'Sanitário '  else
                                                (case when z01_numcgm is not null then 'Cgm' else
                                                     (case when y21_codnoti is not null then 'Vistoria' else 'Nenhum'
                                                      end)
                                                end)
                                        end )
                                end )
                        end as dl_identifica,
                        case when y34_inscr is not null then y34_inscr else
                                (case when y35_matric is not null then y35_matric else
                                        (case when y37_codsani is not null then y37_codsani else
                                                (case when z01_numcgm is not null then z01_numcgm else
                                                     (case when y51_codnoti is not null then y51_codnoti
                                                      end)
                                                end)
                                        end )
                                end )
                        end as dl_codigo,
                        case when q02_numcgm is not null then q02_numcgm else
                                (case when j01_numcgm is not null then j01_numcgm else
                                        (case when y80_numcgm is not null then y80_numcgm else
                                                (case when z01_numcgm is not null then z01_numcgm else q02_numcgm
                                                end)
                                        end )
                                end )
                        end as z01_numcgm
                from fiscalizacao.fis_fiscal
                        left join fiscalizacao.fis_fiscalcgm on  y36_codnoti= y30_codnoti
                        left join fiscalizacao.fis_fiscalinscr on y34_codnoti = y30_codnoti
                        left join fiscalizacao.fis_fiscalmatric on y35_codnoti = y30_codnoti
                        left join fiscalizacao.fis_fiscalsanitario on y37_codnoti = y30_codnoti
                        left join fiscalizacao.fis_fiscalvistorias on y21_codnoti = y30_codnoti
                        left join fiscalizacao.fis_autofiscal on y51_codnoti = y30_codnoti
                        left join iptubase on j01_matric = y35_matric
                        left join issbase on y34_inscr = q02_inscr
                        left join cgm on z01_numcgm = y36_numcgm
                        left join fiscalizacao.fis_sanitario on y80_codsani = y37_codsani
                        left join fiscalizacao.fis_fiscalusuario on y38_codnoti = y30_codnoti
                        left join db_usuarios on id_usuario = y38_id_usuario
                        inner join db_depart on coddepto = y30_setor
                 ) as x

                 left join cgm on cgm.z01_numcgm=x.z01_numcgm ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y30_codnoti!=null) {
                $sql2 .= " where dl_noti = $y30_codnoti ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        return $sql;
    }
    public function sql_querycgm($y30_codnoti = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        $sql .= "          case when q02_numcgm is not null then q02_numcgm else
                                (case when j01_numcgm is not null then j01_numcgm else
                                        (case when y80_numcgm is not null then y80_numcgm else q02_numcgm
                                        end )
                                end )
                        end as z01_numcgm ,
                        case when y70_codvist is not null then y70_codvist
                        end as y70_codvist
                        from fiscalizacao.fis_fiscal
                                left join fiscalizacao.fis_fiscalcgm on y36_codnoti = y30_codnoti
                                left join fiscalizacao.fis_fiscalinscr on y34_codnoti = y30_codnoti
                                left join fiscalizacao.fis_fiscalmatric on y35_codnoti = y30_codnoti
                                left join fiscalizacao.fis_fiscalsanitario on y37_codnoti = y30_codnoti
                                left join iptubase on j01_matric = y35_matric
                                left join issbase on y34_inscr = q02_inscr
                                left join fiscalizacao.fis_sanitario on y80_codsani = y37_codsani
                                left join fiscalizacao.fis_fiscalvistorias on y21_codnoti = y30_codnoti
                                left join fiscalizacao.fis_vistorias on y70_codvist = y21_codvist;
     ";
        $sql2 = "";
        if ($y30_codnoti!=null) {
            $sql2 .= " where fis_fiscal.y30_codnoti = $y30_codnoti ";
        }
        $sql .= $sql2;
        return $sql;
    }
    public function sql_query_cons($y30_codnoti = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_fiscal ";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_fiscal.y30_setor";
        $sql .= "      left join fiscalizacao.fis_fiscalcgm on  y36_codnoti= y30_codnoti";
        $sql .= "      left join fiscalizacao.fis_fiscalinscr on y34_codnoti = y30_codnoti";
        $sql .= "      left join issbase on q02_inscr = y34_inscr ";
        $sql .= "      left join fiscalizacao.fis_fiscalmatric on y35_codnoti = y30_codnoti";
        $sql .= "      left join fiscalizacao.fis_fiscalsanitario on y37_codnoti = y30_codnoti";
        $sql .= "      left join fiscalizacao.fis_sanitario on y80_codsani = y37_codsani";
        $sql .= "		  left join fiscalizacao.fis_fiscalvistorias on y21_codnoti = y30_codnoti";
        $sql .= "      left join fiscalizacao.fis_autofiscal on y51_codnoti = y30_codnoti";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y30_codnoti!=null) {
                $sql2 .= " where fis_fiscal.y30_codnoti = $y30_codnoti ";
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
    public function sql_query_ender($y30_codnoti = null, $campos = "*", $dbwhere = "")
    {

        $sql = "select $campos from
			   (select case when y34_inscr  is not null then issruas.j14_codigo  else
           (case when y35_matric is not null then proprietario.j14_codigo  else
               (case when y37_codsani is not null then y80_codrua  else
                   (case when y36_numcgm is not null then  db_cgmruas.j14_codigo else
                       (case when y21_codvist is not null then y10_codigo  else '0'
                        end)
                    end)
                end)
            end)
       end as codrua,
       case when y34_inscr  is not null then ruas.j14_nome  else
           (case when y35_matric is not null then proprietario.j14_nome  else
               (case when y37_codsani is not null then r3.j14_nome  else
                   (case when y36_numcgm is not null then  r2.j14_nome else
                       (case when y21_codvist is not null then r4.j14_nome  else 'NÃO CADASTRADA'
                        end)
                    end)
                end)
            end)
       end as nomerua,
       case when y34_inscr  is not null then issbairro.q13_bairro  else
           (case when y35_matric is not null then proprietario.j34_bairro  else
               (case when y37_codsani is not null then y80_codbairro  else
                   (case when y36_numcgm is not null then  db_cgmbairro.j13_codi else
                       (case when y21_codvist is not null then y10_codi  else '0'
                        end)
                    end)
                end)
            end)
       end as codbairro,
       case when y34_inscr  is not null then bairro.j13_descr  else
           (case when y35_matric is not null then proprietario.j13_descr  else
               (case when y37_codsani is not null then b3.j13_descr  else
                   (case when y36_numcgm is not null then  b2.j13_descr else
                       (case when y21_codvist is not null then b4.j13_descr  else 'NÃO CADASTRADA'
                        end)
                    end)
                end)
            end)
       end as nomebairro,
       case when y34_inscr  is not null then issruas.q02_numero  else
           (case when y35_matric is not null then proprietario.j39_numero  else
               (case when y37_codsani is not null then y80_numero  else
                   (case when y36_numcgm is not null then  cgm.z01_numero else
                       (case when y21_codvist is not null then y10_numero  else '0'
                        end)
                    end)
                end)
            end)
       end as numero,
       case when y34_inscr  is not null then issruas.q02_compl  else
           (case when y35_matric is not null then proprietario.j39_compl  else
               (case when y37_codsani is not null then y80_compl  else
                   (case when y36_numcgm is not null then  cgm.z01_compl else
                       (case when y21_codvist is not null then y10_compl  else '0'
                        end)
                    end)
                end)
            end)
       end as compl,
       fis_fiscal.*
from fiscalizacao.fis_fiscal

    left join fiscalizacao.fis_fiscalmatric on y35_codnoti = y30_codnoti
    left join iptubase as a on a.j01_matric = y35_matric
    left join proprietario on a.j01_matric = proprietario.j01_matric

    left join fiscalizacao.fis_fiscalinscr on y34_codnoti = y30_codnoti
    left join issbase as b on y34_inscr = b.q02_inscr
    left join empresa on b.q02_inscr = empresa.q02_inscr
    left join issruas on b.q02_inscr = issruas.q02_inscr
    left join ruas on issruas.j14_codigo = ruas.j14_codigo
    left join issbairro on b.q02_inscr = issbairro.q13_inscr
    left join bairro on issbairro.q13_bairro = bairro.j13_codi

    left join fiscalizacao.fis_fiscalcgm on  y36_codnoti= y30_codnoti
    left join cgm on cgm.z01_numcgm = y36_numcgm
    left join db_cgmruas on cgm.z01_numcgm = db_cgmruas.z01_numcgm
    left join db_cgmbairro on cgm.z01_numcgm = db_cgmbairro.z01_numcgm
    left join ruas as r2 on db_cgmruas.j14_codigo = r2.j14_codigo
    left join bairro as b2 on  db_cgmbairro.j13_codi = b2.j13_codi


    left join fiscalizacao.fis_fiscalsanitario on y37_codnoti = y30_codnoti
    left join fiscalizacao.fis_sanitario  on y80_codsani = y37_codsani
    left join ruas as r3 on fis_sanitario.y80_codrua = r3.j14_codigo
    left join bairro as b3 on  fis_sanitario.y80_codbairro  = b3.j13_codi

    left join fiscalizacao.fis_fiscalvistorias on  y21_codnoti = y30_codnoti
    left join fiscalizacao.fis_vistorias on y70_codvist = y21_codvist
    left join fiscalizacao.fis_vistlocal on y70_codvist = y10_codvist
    left join ruas as r4 on y10_codigo = r4.j14_codigo
    left join bairro as b4 on y10_codi = b4.j13_codi

    left join fiscalizacao.fis_fiscalusuario on y38_codnoti = y30_codnoti
    left join db_usuarios on id_usuario = y38_id_usuario

    inner join db_depart on coddepto = y30_setor

	    		) as x

		   ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y30_codnoti!=null) {
                $sql2 .= " where y30_codnoti = $y30_codnoti ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        return $sql;
    }
    public function sql_query_file($y30_codnoti = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_fiscal ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y30_codnoti!=null) {
                $sql2 .= " where fis_fiscal.y30_codnoti = $y30_codnoti ";
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
    public function sql_query_info($y30_codnoti = null, $campos = "*", $dbwhere = "")
    {

        $sql = "select $campos from
                           (select y30_setor,
                                   nome,
                                   descrdepto,
                                   y30_codnoti,
                                   y30_numbloco,
                                   y30_data,
                                   y30_hora,
                                   y30_instit,
                                   y30_prazorec,
                                   y30_nome,
                                   y30_obs,
                                   y30_dtvenc,
                                   case when y34_inscr  is not null then 'Inscrição'  else
                                      (case when y35_matric is not null then 'Matrícula' else
                                         (case when y37_codsani is not null then 'Sanitário '  else
                                            (case when y36_numcgm is not null then 'Cgm' else
                                               (case when y21_codvist is not null then 'Vistorias' else 'Nenhum'
                                                end)
                                             end)
                                           end)
                                         end)
                                     end as identifica,
                                     case when y34_inscr  is not null then   y34_inscr else
                                       (case when y35_matric is not null then  y35_matric else
                                         (case when y37_codsani is not null then y37_codsani else
                                           (case when y36_numcgm is not null then  y36_numcgm else
                                             (case when y21_codvist is not null then y21_codvist
                                              end)
                                           end)
                                         end)
                                       end )
                                     end as codigo,
                                     case when b.q02_numcgm is not null then b.q02_numcgm else
                                       (case when a.j01_numcgm is not null then a.j01_numcgm else
                                         (case when c.y80_numcgm is not null then c.y80_numcgm else
                                           (case when z01_numcgm is not null then z01_numcgm else
                                             (case when inscr.q02_numcgm is not null then inscr.q02_numcgm else
                                               (case when matric.j01_numcgm is not null then matric.j01_numcgm else
                                                 (case when y73_numcgm is not null then y73_numcgm else
                                                   (case when sani.y80_numcgm is not null then sani.y80_numcgm
                                                   end)
                                                 end)
                                               end)
                                             end)
                                           end)
                                         end)
end)
                                     end as z01_numcgm
                            from fiscalizacao.fis_fiscal
                                  left join fiscalizacao.fis_fiscalcgm on  y36_codnoti= y30_codnoti
                                  left join fiscalizacao.fis_fiscalinscr on y34_codnoti = y30_codnoti
                                  left join fiscalizacao.fis_fiscalmatric on y35_codnoti = y30_codnoti
                                  left join fiscalizacao.fis_fiscalsanitario on y37_codnoti = y30_codnoti
                                  left join fiscalizacao.fis_fiscalvistorias on  y21_codnoti = y30_codnoti
                                  left join iptubase as a on a.j01_matric = y35_matric
                                  left join issbase as b on y34_inscr = b.q02_inscr
                                  left join cgm on z01_numcgm = y36_numcgm
                                  left join fiscalizacao.fis_sanitario as c on c.y80_codsani = y37_codsani
                                  left join fiscalizacao.fis_vistorias on y70_codvist = y21_codvist
                                  left join fiscalizacao.fis_vistinscr on y71_codvist = y70_codvist
                                  left join fiscalizacao.fis_vistmatric on y72_codvist = y70_codvist
                                  left join fiscalizacao.fis_vistcgm on y73_codvist = y70_codvist
                                  left join fiscalizacao.fis_vistsanitario on y74_codvist = y70_codvist
                                  left join issbase as inscr on y71_inscr = inscr.q02_inscr
                                  left join iptubase as matric on matric.j01_matric = y72_matric
                                  left join fiscalizacao.fis_sanitario as sani on sani.y80_codsani = y74_codsani
                                  left join fiscalizacao.fis_fiscalusuario on y38_codnoti = y30_codnoti
                                  left join db_usuarios on id_usuario = y38_id_usuario
                                  inner join db_depart on coddepto = y30_setor
                            ) as x

                            inner join cgm on cgm.z01_numcgm=x.z01_numcgm";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y30_codnoti!=null) {
                $sql2 .= " where y30_codnoti = $y30_codnoti ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        return $sql;
    }
}
