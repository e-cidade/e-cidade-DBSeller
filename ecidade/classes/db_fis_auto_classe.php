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

//MODULO: fiscal
//CLASSE DA ENTIDADE auto
class cl_fis_auto
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
    public $y50_codauto = 0;
    public $y50_data_dia = null;
    public $y50_data_mes = null;
    public $y50_data_ano = null;
    public $y50_data = null;
    public $y50_hora = null;
    public $y50_obs = null;
    public $y50_setor = 0;
    public $y50_nome = null;
    public $y50_dtvenc_dia = null;
    public $y50_dtvenc_mes = null;
    public $y50_dtvenc_ano = null;
    public $y50_dtvenc = null;
    public $y50_numbloco = null;
    public $y50_prazorec_dia = null;
    public $y50_prazorec_mes = null;
    public $y50_prazorec_ano = null;
    public $y50_prazorec = null;
    public $y50_codtipo = 0;
    public $y50_instit = 0;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 y50_codauto = int4 = Código do Auto de Infração
                 y50_data = date = Data do Auto de Infração
                 y50_hora = char(5) = Hora do Auto
                 y50_obs = text = Observação do auto
                 y50_setor = int4 = Código do Departamento
                 y50_nome = varchar(50) = Nome da Pessoa Autuada
                 y50_dtvenc = date = Data do Vencimento Atualizada
                 y50_numbloco = varchar(20) = Número do Bloco
                 y50_prazorec = date = Prazo p/ Recurso
                 y50_codtipo = int4 = Cod. Tipo
                 y50_instit = int4 = Cod. Instituição
                 ";
   //funcao construtor da classe
    public function __construct()
    {
      //classes dos rotulos dos campos
        $this->rotulo = new rotulo("fis_auto");
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
            $this->y50_codauto = ($this->y50_codauto == ""?@$GLOBALS["HTTP_POST_VARS"]["y50_codauto"]:$this->y50_codauto);
            if ($this->y50_data == "") {
                $this->y50_data_dia = ($this->y50_data_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y50_data_dia"]:$this->y50_data_dia);
                $this->y50_data_mes = ($this->y50_data_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y50_data_mes"]:$this->y50_data_mes);
                $this->y50_data_ano = ($this->y50_data_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y50_data_ano"]:$this->y50_data_ano);
                if ($this->y50_data_dia != "") {
                     $this->y50_data = $this->y50_data_ano."-".$this->y50_data_mes."-".$this->y50_data_dia;
                }
            }
            $this->y50_hora = ($this->y50_hora == ""?@$GLOBALS["HTTP_POST_VARS"]["y50_hora"]:$this->y50_hora);
            $this->y50_obs = ($this->y50_obs == ""?@$GLOBALS["HTTP_POST_VARS"]["y50_obs"]:$this->y50_obs);
            $this->y50_setor = ($this->y50_setor == ""?@$GLOBALS["HTTP_POST_VARS"]["y50_setor"]:$this->y50_setor);
            $this->y50_nome = ($this->y50_nome == ""?@$GLOBALS["HTTP_POST_VARS"]["y50_nome"]:$this->y50_nome);
            if ($this->y50_dtvenc == "") {
                $this->y50_dtvenc_dia = ($this->y50_dtvenc_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y50_dtvenc_dia"]:$this->y50_dtvenc_dia);
                $this->y50_dtvenc_mes = ($this->y50_dtvenc_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y50_dtvenc_mes"]:$this->y50_dtvenc_mes);
                $this->y50_dtvenc_ano = ($this->y50_dtvenc_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y50_dtvenc_ano"]:$this->y50_dtvenc_ano);
                if ($this->y50_dtvenc_dia != "") {
                    $this->y50_dtvenc = $this->y50_dtvenc_ano."-".$this->y50_dtvenc_mes."-".$this->y50_dtvenc_dia;
                }
            }
            $this->y50_numbloco = ($this->y50_numbloco == ""?@$GLOBALS["HTTP_POST_VARS"]["y50_numbloco"]:$this->y50_numbloco);
            if ($this->y50_prazorec == "") {
                $this->y50_prazorec_dia = ($this->y50_prazorec_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y50_prazorec_dia"]:$this->y50_prazorec_dia);
                $this->y50_prazorec_mes = ($this->y50_prazorec_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y50_prazorec_mes"]:$this->y50_prazorec_mes);
                $this->y50_prazorec_ano = ($this->y50_prazorec_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y50_prazorec_ano"]:$this->y50_prazorec_ano);
                if ($this->y50_prazorec_dia != "") {
                    $this->y50_prazorec = $this->y50_prazorec_ano."-".$this->y50_prazorec_mes."-".$this->y50_prazorec_dia;
                }
            }
            $this->y50_codtipo = ($this->y50_codtipo == ""?@$GLOBALS["HTTP_POST_VARS"]["y50_codtipo"]:$this->y50_codtipo);
            $this->y50_instit = ($this->y50_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["y50_instit"]:$this->y50_instit);
        } else {
            $this->y50_codauto = ($this->y50_codauto == ""?@$GLOBALS["HTTP_POST_VARS"]["y50_codauto"]:$this->y50_codauto);
        }
    }
   // funcao para inclusao
    public function incluir($y50_codauto = null)
    {
        $this->atualizacampos();
        if ($this->y50_data == null) {
            $this->erro_sql = " Campo Data do Auto de Infração nao Informado.";
            $this->erro_campo = "y50_data_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y50_setor == null) {
            $this->erro_sql = " Campo Código do Departamento nao Informado.";
            $this->erro_campo = "y50_setor";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y50_prazorec == null) {
            $this->y50_prazorec = "null";
        }
        if ($this->y50_codtipo == null) {
            $this->erro_sql = " Campo Cod. Tipo nao Informado.";
            $this->erro_campo = "y50_codtipo";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y50_instit == null) {
            $this->erro_sql = " Campo Cod. Instituição nao Informado.";
            $this->erro_campo = "y50_instit";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into fiscalizacao.fis_auto(
                                       y50_data
                                      ,y50_hora
                                      ,y50_obs
                                      ,y50_setor
                                      ,y50_nome
                                      ,y50_dtvenc
                                      ,y50_numbloco
                                      ,y50_prazorec
                                      ,y50_codtipo
                                      ,y50_instit
                       )
                values (
                                ".($this->y50_data == "null" || $this->y50_data == ""?"null":"'".$this->y50_data."'")."
                               ,'$this->y50_hora'
                               ,'$this->y50_obs'
                               ,$this->y50_setor
                               ,'$this->y50_nome'
                               ,".($this->y50_dtvenc == "null" || $this->y50_dtvenc == ""?"null":"'".$this->y50_dtvenc."'")."
                               ,'$this->y50_numbloco'
                               ,".($this->y50_prazorec == "null" || $this->y50_prazorec == ""?"null":"'".$this->y50_prazorec."'")."
                               ,$this->y50_codtipo
                               ,$this->y50_instit
                      ) returning y50_codauto ";
        $result = db_query($sql);
        $this->y50_codauto = db_utils::fieldsmemory($result, 0)->y50_codauto;

        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "auto ($this->y50_codauto) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "auto já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "auto ($this->y50_codauto) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->y50_codauto;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);

        return true;
    }
   // funcao para alteracao
    public function alterar($y50_codauto = null)
    {
        $this->atualizacampos();
        $sql = " update fiscalizacao.fis_auto set ";
        $virgula = "";
        if (trim($this->y50_data)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y50_data_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y50_data_dia"] !="")) {
            $sql  .= $virgula." y50_data = '$this->y50_data' ";
            $virgula = ",";
            if (trim($this->y50_data) == null) {
                $this->erro_sql = " Campo Data do Auto de Infração nao Informado.";
                $this->erro_campo = "y50_data_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y50_data_dia"])) {
                $sql  .= $virgula." y50_data = null ";
                $virgula = ",";
                if (trim($this->y50_data) == null) {
                    $this->erro_sql = " Campo Data do Auto de Infração nao Informado.";
                    $this->erro_campo = "y50_data_dia";
                    $this->erro_banco = "";
                    $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        if (trim($this->y50_hora)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y50_hora"])) {
            $sql  .= $virgula." y50_hora = '$this->y50_hora' ";
            $virgula = ",";
        }
        if (trim($this->y50_obs)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y50_obs"])) {
            $sql  .= $virgula." y50_obs = '$this->y50_obs' ";
            $virgula = ",";
        }
        if (trim($this->y50_setor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y50_setor"])) {
            $sql  .= $virgula." y50_setor = $this->y50_setor ";
            $virgula = ",";
            if (trim($this->y50_setor) == null) {
                $this->erro_sql = " Campo Código do Departamento nao Informado.";
                $this->erro_campo = "y50_setor";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y50_nome)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y50_nome"])) {
            $sql  .= $virgula." y50_nome = '$this->y50_nome' ";
            $virgula = ",";
        }
        if (trim($this->y50_dtvenc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y50_dtvenc_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y50_dtvenc_dia"] !="")) {
            $sql  .= $virgula." y50_dtvenc = '$this->y50_dtvenc' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y50_dtvenc_dia"])) {
                $sql  .= $virgula." y50_dtvenc = null ";
                $virgula = ",";
            }
        }
        if (trim($this->y50_numbloco)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y50_numbloco"])) {
            $sql  .= $virgula." y50_numbloco = '$this->y50_numbloco' ";
            $virgula = ",";
        }
        if (trim($this->y50_prazorec)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y50_prazorec_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y50_prazorec_dia"] !="")) {
            $sql  .= $virgula." y50_prazorec = '$this->y50_prazorec' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y50_prazorec_dia"])) {
                $sql  .= $virgula." y50_prazorec = null ";
                $virgula = ",";
            }
        }
        if (trim($this->y50_codtipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y50_codtipo"])) {
            $sql  .= $virgula." y50_codtipo = $this->y50_codtipo ";
            $virgula = ",";
            if (trim($this->y50_codtipo) == null) {
                $this->erro_sql = " Campo Cod. Tipo nao Informado.";
                $this->erro_campo = "y50_codtipo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y50_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y50_instit"])) {
            $sql  .= $virgula." y50_instit = $this->y50_instit ";
            $virgula = ",";
            if (trim($this->y50_instit) == null) {
                $this->erro_sql = " Campo Cod. Instituição nao Informado.";
                $this->erro_campo = "y50_instit";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($y50_codauto!=null) {
            $sql .= " y50_codauto = $this->y50_codauto";
        }


        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "auto nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->y50_codauto;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "auto nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->y50_codauto;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->y50_codauto;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao para exclusao
    public function excluir($y50_codauto = null, $dbwhere = null)
    {
        $sql = " delete from fiscalizacao.fis_auto where ";
        $sql2 = "";
        if ($dbwhere == null || $dbwhere == "") {
            if ($y50_codauto != "") {
                if ($sql2!="") {
                    $sql2 .= " and ";
                }
                $sql2 .= " y50_codauto = $y50_codauto ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if ($result==false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "auto nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y50_codauto;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "auto nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y50_codauto;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$y50_codauto;
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
            $this->erro_sql   = "Record Vazio na Tabela:auto";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
    public function sql_calculo($y50_codauto = "")
    {
        $result =  db_query("select fc_autodeinfracao($y50_codauto)");
        return $result;
    }
    public function sql_query($y50_codauto = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_auto ";
        $sql .= "      inner join db_config  on  db_config.codigo = fis_auto.y50_instit";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_auto.y50_setor";
        $sql .= "      inner join fiscalizacao.fis_tipofiscaliza  on  fis_tipofiscaliza.y27_codtipo = fis_auto.y50_codtipo";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y50_codauto!=null) {
                $sql2 .= " where fis_auto.y50_codauto = $y50_codauto ";
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
    public function sql_query_busca($y50_codauto = null, $dbwhere = "", $dbwhere2 = "")
    {
        $sql = "select dl_Auto,dl_identificacao,dl_codigo,z01_nome,tipo,y50_instit,y50_numbloco,dl_Andamento from
                        (select distinct
                                y50_numbloco,
                                y50_instit,
                                y50_setor,
                                y50_codauto as dl_Auto,
                                case when q02_numcgm is not null then 'Inscrição'  else
                                      (case when j01_numcgm is not null then 'Matrícula' else
                                             (case when y80_numcgm is not null then 'Sanitário '  else
                                                     (case when z01_numcgm is not null then 'Cgm' else
                                                          (case when y30_codnoti is not null then 'Notificação' else 'Nenhum'
                                                           end)
                                                     end)
                                             end )
                                     end )
                                end as dl_identificacao,
                                case when y52_inscr is not null then y52_inscr else
                                        (case when y53_matric is not null then y53_matric else
                                                (case when y55_codsani is not null then y55_codsani else
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
                                end as z01_numcgm ,
                                y27_descr as tipo,
			                    y41_descr as dl_Andamento
                         from fiscalizacao.fis_auto
                              left join fiscalizacao.fis_tipofiscaliza on y50_codtipo=y27_codtipo
                              left join fiscalizacao.fis_autocgm on y54_codauto = y50_codauto
                              left join fiscalizacao.fis_autoinscr on y52_codauto = y50_codauto
                              left join fiscalizacao.fis_automatric on y53_codauto = y50_codauto
                              left join fiscalizacao.fis_autosanitario on y55_codauto = y50_codauto
                              left join iptubase on j01_matric = y53_matric
                              left join issbase on y52_inscr = q02_inscr
                              left join cgm on z01_numcgm = y54_numcgm
                              left join fiscalizacao.fis_sanitario on y80_codsani = y55_codsani
                              left join fiscalizacao.fis_autofiscal on y51_codauto = y50_codauto
                              left join fiscalizacao.fis_fiscal on y51_codnoti = y30_codnoti
                              left join fiscalizacao.fis_fiscalusuario on y38_codnoti = y30_codnoti
                              left join fiscalizacao.fis_procfiscalauto on y111_auto = y50_codauto
                              left join fiscalizacao.fis_procfiscal on y111_procfiscal = y100_sequencial
                              left join fiscalizacao.fis_procfiscalfiscais  on fis_procfiscalfiscais.y106_procfiscal = y100_sequencial
                              left join fiscalizacao.fis_cadfiscais on fis_procfiscalfiscais.y106_cadfiscais = fis_cadfiscais.id_usuario
                              left join db_usuarios on db_usuarios.id_usuario = y38_id_usuario
                              left join fiscalizacao.fis_processofiscalativo on fis_processofiscalativo.processo_fiscal = y100_sequencial
                              left join fiscalizacao.fis_datalimitefiscal on fis_datalimitefiscal.fiscal = fis_cadfiscais.id_usuario
                              left join fiscalizacao.fis_fandam on y39_codandam = (select max(y58_codandam) from fiscalizacao.fis_autoandam where y58_codauto = y50_codauto)
                              left join fiscalizacao.fis_tipoandam on y41_codtipo = y39_codtipo
                              left join fiscalizacao.fis_grupotipoandamento_tipoandam on y41_codtipo = fi30_tipoandam
                              left join fiscalizacao.fis_grupotipoandamento on fi30_grupo = fis_grupotipoandamento.sequencial
                              left join fiscalizacao.fis_autousu on y56_codauto   = y50_codauto
			             $dbwhere2
                        ) as x inner join cgm on cgm.z01_numcgm = x.z01_numcgm";

        $sql2 = "";
        if ($dbwhere=="") {
            if ($y50_codauto != null) {
                $sql2 .= " where dl_auto = $y50_codauto ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;

        return $sql;
    }

    public function sql_query_busca_tipo($y50_codauto = null, $dbwhere = "")
    {
        $sql = "select dl_Auto,dl_identificacao,dl_codigo,z01_nome,tipo,y50_instit,y50_numbloco, dl_identificacaotipo  from
                        (select
                           y50_numbloco,
                           y50_instit,
                           y50_setor,
                           y50_codauto as dl_Auto,
                           case when q02_numcgm is not null then 'Inscrição'  else
                                 (case when j01_numcgm is not null then 'Matrícula' else
                                        (case when y80_numcgm is not null then 'Sanitário '  else
                                                (case when z01_numcgm is not null then 'Cgm' else
                                                     (case when y30_codnoti is not null then 'Notificação' else 'Nenhum'
                                                      end)
                                                end)
                                        end )
                                end )
                        end as dl_identificacao,
                        case when q02_numcgm is not null then '1'  else
                                 (case when j01_numcgm is not null then '2' else
                                        (case when y80_numcgm is not null then '3 '  else
                                                (case when z01_numcgm is not null then '4' else
                                                     (case when y30_codnoti is not null then '5' else '6'
                                                      end)
                                                end)
                                        end )
                                end )
                        end as dl_identificacaotipo,
                        case when y52_inscr is not null then y52_inscr else
                                (case when y53_matric is not null then y53_matric else
                                        (case when y55_codsani is not null then y55_codsani else
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
                        end as z01_numcgm ,
                        y27_descr as tipo
                from fiscalizacao.fis_auto
                        left join fiscalizacao.fis_tipofiscaliza on y50_codtipo=y27_codtipo
                        left join fiscalizacao.fis_autocgm on y54_codauto = y50_codauto
                        left join fiscalizacao.fis_autoinscr on y52_codauto = y50_codauto
                        left join fiscalizacao.fis_automatric on y53_codauto = y50_codauto
                        left join fiscalizacao.fis_autosanitario on y55_codauto = y50_codauto
                        left join iptubase on j01_matric = y53_matric
                        left join issbase on y52_inscr = q02_inscr
                        left join cgm on z01_numcgm = y54_numcgm
                        left join fiscalizacao.fis_sanitario on y80_codsani = y55_codsani
                        left join fiscalizacao.fis_autofiscal on y51_codauto = y50_codauto
                        left join fiscalizacao.fis_fiscal on y51_codnoti = y30_codnoti
                        ) as x
                    inner join cgm on cgm.z01_numcgm = x.z01_numcgm";


        $sql2 = "";
        if ($dbwhere=="") {
            if ($y50_codauto!=null) {
                $sql2 .= " where dl_auto = $y50_codauto ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;

        return $sql;
    }

    public function sql_query_busca2($y50_codauto = null, $dbwhere = "")
    {

        $sql = "select dl_Auto,dl_identifica,dl_codigo,z01_nome,tipo,y50_setor from
                        (select
			   y50_setor,
                           y50_codauto as dl_Auto,
			   case when q02_numcgm is not null then 'Inscrição'  else
                                 (case when j01_numcgm is not null then 'Matrícula' else
                                        (case when y80_numcgm is not null then 'Sanitário '  else
                                                (case when z01_numcgm is not null then 'Cgm' else
						     (case when y30_codnoti is not null then 'Notificação' else 'Nenhum'
						      end)
                                                end)
                                        end )
                                end )
                        end as dl_identifica,
                        case when y52_inscr is not null then y52_inscr else
                                (case when y53_matric is not null then y53_matric else
                                        (case when y55_codsani is not null then y55_codsani else
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
                        end as z01_numcgm ,
			y27_descr as tipo,
      y50_instit
                from fiscalizacao.fis_auto
		        left join fiscalizacao.fis_tipofiscaliza on y50_codtipo=y27_codtipo
                        left join fiscalizacao.fis_autocgm on y54_codauto = y50_codauto
                        left join fiscalizacao.fis_autoinscr on y52_codauto = y50_codauto
                        left join fiscalizacao.fis_automatric on y53_codauto = y50_codauto
                        left join fiscalizacao.fis_autosanitario on y55_codauto = y50_codauto
                        left join iptubase on j01_matric = y53_matric
                        left join issbase on y52_inscr = q02_inscr
                        left join cgm on z01_numcgm = y54_numcgm
                        left join fiscalizacao.fis_sanitario on y80_codsani = y55_codsani
                        left join fiscalizacao.fis_autofiscal on y51_codauto = y50_codauto
                        left join fiscalizacao.fis_fiscal on y51_codnoti = y30_codnoti
	        ) as x
		    inner join cgm on cgm.z01_numcgm=x.z01_numcgm";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y50_codauto!=null) {
                $sql2 .= " where dl_auto = $y50_codauto ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;

        return $sql;
    }
    public function sql_querycgm($y50_codauto = null)
    {
        $sql = "select
                        case when q02_numcgm is not null then q02_numcgm else
                                (case when j01_numcgm is not null then j01_numcgm else
                                        (case when y80_numcgm is not null then y80_numcgm else
                                                (case when z01_numcgm is not null then z01_numcgm else q02_numcgm
                                                end)
                                        end )
                                end )
                        end as z01_numcgm ,
                        case when y30_codnoti is not null then y30_codnoti
                        end as y30_codnoti
                from fiscalizacao.fis_auto
                        left join fiscalizacao.fis_autocgm on y54_codauto = y50_codauto
                        left join fiscalizacao.fis_autoinscr on y52_codauto = y50_codauto
                        left join fiscalizacao.fis_automatric on y53_codauto = y50_codauto
                        left join fiscalizacao.fis_autosanitario on y55_codauto = y50_codauto
                        left join iptubase on j01_matric = y53_matric
                        left join issbase on y52_inscr = q02_inscr
                        left join cgm on z01_numcgm = y54_numcgm
                        left join fiscalizacao.fis_sanitario on y80_codsani = y55_codsani
                        left join fiscalizacao.fis_autofiscal on y51_codauto = y50_codauto
                        left join fiscalizacao.fis_fiscal on y51_codnoti = y30_codnoti ";
        $sql2 = "";
        if ($y50_codauto!=null) {
            $sql2 .= " where fis_auto.y50_codauto = $y50_codauto ";
        }
        $sql .= $sql2;
        return $sql;
    }
    public function sql_query_cgm($y50_codauto = null, $dbwhere = "")
    {

        $sql = "select * from (select
                        case when q02_numcgm is not null then q02_numcgm else
                                (case when j01_numcgm is not null then j01_numcgm else
                                        (case when y80_numcgm is not null then y80_numcgm else
                                                (case when z01_numcgm is not null then z01_numcgm else q02_numcgm
                                                end)
                                        end )
                                end )
                        end as z01_numcgm ,
                        case when y30_codnoti is not null then y30_codnoti
                        end as y30_codnoti,
                        y50_codauto
                from fiscalizacao.fis_auto
                        left join fiscalizacao.fis_autocgm on y54_codauto = y50_codauto
                        left join fiscalizacao.fis_autoinscr on y52_codauto = y50_codauto
                        left join fiscalizacao.fis_automatric on y53_codauto = y50_codauto
                        left join fiscalizacao.fis_autosanitario on y55_codauto = y50_codauto
                        left join iptubase on j01_matric = y53_matric
                        left join issbase on y52_inscr = q02_inscr
                        left join cgm on z01_numcgm = y54_numcgm
                        left join fiscalizacao.fis_sanitario on y80_codsani = y55_codsani
                        left join fiscalizacao.fis_autofiscal on y51_codauto = y50_codauto
                        left join fiscalizacao.fis_fiscal on y51_codnoti = y30_codnoti) as x ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y50_codauto!=null) {
                $sql2 .= " where fis_auto.y50_codauto = $y50_codauto ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        return $sql;
    }
    public function sql_query_file($y50_codauto = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_auto ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y50_codauto!=null) {
                $sql2 .= " where fis_auto.y50_codauto = $y50_codauto ";
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
    public function sql_query_info($y50_codauto = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_auto ";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_auto.y50_setor";
        $sql .= "      inner join fiscalizacao.fis_tipofiscaliza  on  fis_tipofiscaliza.y27_codtipo = fis_auto.y50_codtipo";
        $sql .= "      left join fiscalizacao.fis_autocgm on y54_codauto = y50_codauto";
        $sql .= "      left join fiscalizacao.fis_autoinscr on y52_codauto = y50_codauto";
        $sql .= "      left join issbase on y52_inscr = q02_inscr";
        $sql .= "      left join fiscalizacao.fis_automatric on y53_codauto = y50_codauto";
        $sql .= "      left join fiscalizacao.fis_autosanitario on y55_codauto = y50_codauto";
        $sql .= "      left join fiscalizacao.fis_sanitario on y80_codsani = y55_codsani";
        $sql .= "      left join fiscalizacao.fis_autofiscal on y51_codauto = y50_codauto";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y50_codauto!=null) {
                $sql2 .= " where fis_auto.y50_codauto = $y50_codauto ";
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
    public function sql_query_infoautos($y50_codauto = null, $dbwhere = "")
    {

        $sql = "select auto,
                    identificacao,
                    codigo,
                    z01_nome as nome,
                    tipo,y50_data as data,
                    y50_setor as setor,
                    descrdepto,
                    y50_hora as hora,
                    z01_cgccpf as cpf,
                    j13_descr as bairro,
                    j14_nome as rua,
                    y14_numero as numero,
                    y14_compl as complemento,
                    y50_prazorec as prazo_recurso,
                    z01_incest as inscest,
                    y50_obs as obs
             from
                    (select y14_numero,
                            y14_compl,
                            j13_descr,
                            j14_nome,
                            y50_hora,
                            y50_prazorec,
                            y50_data,
                            y50_setor,
                            y50_obs,
                            y50_codauto as auto,
                            case when q02_numcgm is not null then 'Inscrição'  else
                                  (case when j01_numcgm is not null then 'Matrícula' else
                                         (case when y80_numcgm is not null then 'Sanitário '  else
                                                 (case when z01_numcgm is not null then 'Cgm' else
                                                      (case when y30_codnoti is not null then 'Notificação' else 'Nenhum'
                                                       end)
                                                 end)
                            end )
                                   end )
                            end as identificacao,
                            case when y52_inscr is not null then y52_inscr else
                                    (case when y53_matric is not null then y53_matric else
                                            (case when y55_codsani is not null then y55_codsani else
                                                    (case when z01_numcgm is not null then z01_numcgm else
                                                         (case when y51_codnoti is not null then y51_codnoti
                                                          end)
                                                    end)
                                            end )
                                    end )
                            end as codigo,
                            case when q02_numcgm is not null then q02_numcgm else
                                    (case when j01_numcgm is not null then j01_numcgm else
                                            (case when y80_numcgm is not null then y80_numcgm else
                                                    (case when z01_numcgm is not null then z01_numcgm else q02_numcgm
                                                    end)
                                            end )
                                    end )
                            end as z01_numcgm ,
                            y27_descr as tipo
                     from fiscalizacao.fis_auto
                            inner join fiscalizacao.fis_tipofiscaliza on y50_codtipo=y27_codtipo
                            left join fiscalizacao.fis_autocgm on y54_codauto = y50_codauto
                            left join fiscalizacao.fis_autoinscr on y52_codauto = y50_codauto
                            left join fiscalizacao.fis_automatric on y53_codauto = y50_codauto
                            left join fiscalizacao.fis_autosanitario on y55_codauto = y50_codauto
                            left join iptubase on j01_matric = y53_matric
                            left join issbase on y52_inscr = q02_inscr
                            left join cgm on z01_numcgm = y54_numcgm
                            left join fiscalizacao.fis_sanitario on y80_codsani = y55_codsani
                            left join fiscalizacao.fis_autofiscal on y51_codauto = y50_codauto
                            left join fiscalizacao.fis_fiscal on y51_codnoti = y30_codnoti    inner join fiscalizacao.fis_autolocal on y14_codauto = y50_codauto
                            left join ruas on j14_codigo = y14_codigo
                            left join bairro on j13_codi = y14_codi
                    ) as x
                    inner join db_depart on db_depart.coddepto = x.y50_setor
                    inner join cgm on cgm.z01_numcgm=x.z01_numcgm";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y50_codauto!=null) {
                $sql2 .= " where auto = $y50_codauto ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;

        return $sql;
    }

    public function sql_query_cgm_inscricao($y50_codauto = null)
    {

        $sSql  = "select   ";
        $sSql .= "    y52_inscr as q02_inscr, ";
        $sSql .= "    case when y54_numcgm is not null then y54_numcgm ";
        $sSql .= "         when y80_numcgm is not null then y80_numcgm ";
        $sSql .= "         when j01_numcgm is not null then j01_numcgm ";
        $sSql .= "         else null      ";
        $sSql .= "      end as z01_numcgm, ";
        $sSql .= "    case when y54_numcgm is not null then cgmauto.z01_nome ";
        $sSql .= "         when y80_numcgm is not null then cgmsan.z01_nome  ";
        $sSql .= "         when j01_numcgm is not null then cgmiptu.z01_nome ";
        $sSql .= "         when q02_numcgm is not null then cgmiss.z01_nome  ";
        $sSql .= "         else null            ";
        $sSql .= "      end as z01_nomecgminscr ";
        $sSql .= "from fiscalizacao.fis_auto                     ";
        $sSql .= "    left join fiscalizacao.fis_autocgm        on y54_codauto         = y50_codauto ";
        $sSql .= "    left join cgm as cgmauto on cgmauto.z01_numcgm  = y54_numcgm  ";
        $sSql .= "    left join fiscalizacao.fis_autoinscr      on y52_codauto         = y50_codauto ";
        $sSql .= "    left join issbase        on q02_inscr           = y52_inscr   ";
        $sSql .= "    left join cgm as cgmiss  on cgmiss.z01_numcgm   = q02_numcgm  ";
        $sSql .= "    left join fiscalizacao.fis_autosanitario  on y55_codauto         = y50_codauto ";
        $sSql .= "    left join fiscalizacao.fis_sanitario      on y80_codsani         = y55_codsani ";
        $sSql .= "    left join cgm as cgmsan  on cgmsan.z01_numcgm   = y80_numcgm  ";
        $sSql .= "    left join fiscalizacao.fis_automatric     on y53_codauto         = y50_codauto ";
        $sSql .= "    left join iptubase       on j01_matric          = y53_matric  ";
        $sSql .= "    left join cgm as cgmiptu on cgmiptu.z01_numcgm  = j01_numcgm  ";

        if (!is_null($y50_codauto)) {
            $sSql .= " where y50_codauto = $y50_codauto ";
        }

        return $sSql;
    }

    public function sql_precalculo($y50_codauto = "")
    {
        $result =  db_query("select fc_fis_calculoautodeinfracao($y50_codauto)");
        return $result;
    }

    public function excluir_precalculo($y50_codauto = null, $dbwhere = null)
    {
        $sql = " delete from fiscalizacao.fis_calculoauto where ";
        $sql2 = "";
        if ($dbwhere==null || $dbwhere =="") {
            if ($y50_codauto != "") {
                if ($sql2!="") {
                    $sql2 .= " and ";
                }
                $sql2 .= " y50_codauto = $y50_codauto ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "auto nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y50_codauto;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "auto nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y50_codauto;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$y50_codauto;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = pg_affected_rows($result);
                return true;
            }
        }
    }
}
