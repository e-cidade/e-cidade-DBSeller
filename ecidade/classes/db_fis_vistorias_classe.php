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
//CLASSE DA ENTIDADE vistorias
class cl_fis_vistorias
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
    public $y70_codvist = 0;
    public $y70_data_dia = null;
    public $y70_data_mes = null;
    public $y70_data_ano = null;
    public $y70_data = null;
    public $y70_hora = null;
    public $y70_obs = null;
    public $y70_contato = null;
    public $y70_tipovist = 0;
    public $y70_ultandam = 0;
    public $y70_id_usuario = 0;
    public $y70_coddepto = 0;
    public $y70_numbloco = null;
    public $y70_parcial = 'f';
    public $y70_ativo = 'f';
    public $y70_instit = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 y70_codvist = int4 = Código da Vistoria
                 y70_data = date = Data da Vistorias
                 y70_hora = char(5) = Hora da Vistoria
                 y70_obs = text = Observações
                 y70_contato = varchar(50) = Contato
                 y70_tipovist = int4 = Código do Tipo
                 y70_ultandam = int8 = Codigo do Último Andamento Gerado
                 y70_id_usuario = int4 = Cod. Usuário
                 y70_coddepto = int4 = Código
                 y70_numbloco = varchar(20) = Número do Bloco
                 y70_parcial = bool = Vistoria parcial
                 y70_ativo = bool = Ativo
                 y70_instit = int4 = Cod. Instituição
                 ";
    //funcao construtor da classe
    public function __construct()
    {
        //classes dos rotulos dos campos
        $this->rotulo = new rotulo("fis_vistorias");
        $this->pagina_retorno =  basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
    }
    //funcao erro
    public function erro($mostra, $retorna)
    {
        if (($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )) {
            echo "<script>alert(\"".$this->erro_msg."\");</script>";
            if ($retorna) {
                echo "<script>location.href='".$this->pagina_retorno."'</script>";
            }
        }
    }
   // funcao para atualizar campos
    public function atualizacampos($exclusao = false)
    {
        if (!$exclusao) {
            $this->y70_codvist = ($this->y70_codvist == ""?@$GLOBALS["HTTP_POST_VARS"]["y70_codvist"]:$this->y70_codvist);
            if ($this->y70_data == "") {
                $this->y70_data_dia = ($this->y70_data_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y70_data_dia"]:$this->y70_data_dia);
                $this->y70_data_mes = ($this->y70_data_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y70_data_mes"]:$this->y70_data_mes);
                $this->y70_data_ano = ($this->y70_data_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y70_data_ano"]:$this->y70_data_ano);
                if ($this->y70_data_dia != "") {
                     $this->y70_data = $this->y70_data_ano."-".$this->y70_data_mes."-".$this->y70_data_dia;
                }
            }
            $this->y70_hora = ($this->y70_hora == ""?@$GLOBALS["HTTP_POST_VARS"]["y70_hora"]:$this->y70_hora);
            $this->y70_obs = ($this->y70_obs == ""?@$GLOBALS["HTTP_POST_VARS"]["y70_obs"]:$this->y70_obs);
            $this->y70_contato = ($this->y70_contato == ""?@$GLOBALS["HTTP_POST_VARS"]["y70_contato"]:$this->y70_contato);
            $this->y70_tipovist = ($this->y70_tipovist == ""?@$GLOBALS["HTTP_POST_VARS"]["y70_tipovist"]:$this->y70_tipovist);
            $this->y70_ultandam = ($this->y70_ultandam == ""?@$GLOBALS["HTTP_POST_VARS"]["y70_ultandam"]:$this->y70_ultandam);
            $this->y70_id_usuario = ($this->y70_id_usuario == ""?@$GLOBALS["HTTP_POST_VARS"]["y70_id_usuario"]:$this->y70_id_usuario);
            $this->y70_coddepto = ($this->y70_coddepto == ""?@$GLOBALS["HTTP_POST_VARS"]["y70_coddepto"]:$this->y70_coddepto);
            $this->y70_numbloco = ($this->y70_numbloco == ""?@$GLOBALS["HTTP_POST_VARS"]["y70_numbloco"]:$this->y70_numbloco);
            $this->y70_parcial = ($this->y70_parcial == "f"?@$GLOBALS["HTTP_POST_VARS"]["y70_parcial"]:$this->y70_parcial);
            $this->y70_ativo = ($this->y70_ativo == "f"?@$GLOBALS["HTTP_POST_VARS"]["y70_ativo"]:$this->y70_ativo);
            $this->y70_instit = ($this->y70_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["y70_instit"]:$this->y70_instit);
        } else {
            $this->y70_codvist = ($this->y70_codvist == ""?@$GLOBALS["HTTP_POST_VARS"]["y70_codvist"]:$this->y70_codvist);
        }
    }
   // funcao para inclusao
    public function incluir($y70_codvist = null)
    {
        $this->atualizacampos();
        if ($this->y70_data == null) {
            $this->erro_sql = " Campo Data da Vistorias nao Informado.";
            $this->erro_campo = "y70_data_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y70_hora == null) {
            $this->erro_sql = " Campo Hora da Vistoria nao Informado.";
            $this->erro_campo = "y70_hora";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y70_tipovist == null) {
            $this->erro_sql = " Campo Código do Tipo nao Informado.";
            $this->erro_campo = "y70_tipovist";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y70_ultandam == null) {
            $this->erro_sql = " Campo Codigo do Último Andamento Gerado nao Informado.";
            $this->erro_campo = "y70_ultandam";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y70_id_usuario == null) {
            $this->erro_sql = " Campo Cod. Usuário nao Informado.";
            $this->erro_campo = "y70_id_usuario";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y70_coddepto == null) {
            $this->erro_sql = " Campo Código nao Informado.";
            $this->erro_campo = "y70_coddepto";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y70_numbloco == null) {
            $this->y70_numbloco = "0";
        }
        if ($this->y70_parcial == null) {
            $this->erro_sql = " Campo Vistoria parcial nao Informado.";
            $this->erro_campo = "y70_parcial";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y70_ativo == null) {
            $this->erro_sql = " Campo Ativo nao Informado.";
            $this->erro_campo = "y70_ativo";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y70_instit == null) {
            $this->erro_sql = " Campo Cod. Instituição nao Informado.";
            $this->erro_campo = "y70_instit";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into fiscalizacao.fis_vistorias(
                                       y70_data
                                      ,y70_hora
                                      ,y70_obs
                                      ,y70_contato
                                      ,y70_tipovist
                                      ,y70_ultandam
                                      ,y70_id_usuario
                                      ,y70_coddepto
                                      ,y70_numbloco
                                      ,y70_parcial
                                      ,y70_ativo
                                      ,y70_instit
                       )
                values (
                                ".($this->y70_data == "null" || $this->y70_data == ""?"null":"'".$this->y70_data."'")."
                               ,'$this->y70_hora'
                               ,'$this->y70_obs'
                               ,'$this->y70_contato'
                               ,$this->y70_tipovist
                               ,$this->y70_ultandam
                               ,$this->y70_id_usuario
                               ,$this->y70_coddepto
                               ,'$this->y70_numbloco'
                               ,'$this->y70_parcial'
                               ,'$this->y70_ativo'
                               ,$this->y70_instit
                      ) returning y70_codvist ";
        $result = db_query($sql);
        $this->y70_codvist = db_utils::fieldsmemory($result, 0)->y70_codvist;

        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "vistorias ($this->y70_codvist) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "vistorias já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "vistorias ($this->y70_codvist) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->y70_codvist;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);


        return true;
    }
   // funcao para alteracao
    public function alterar($y70_codvist = null)
    {
        $this->atualizacampos();
        $sql = " update fiscalizacao.fis_vistorias set ";
        $virgula = "";
        if (trim($this->y70_data)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y70_data_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y70_data_dia"] !="")) {
            $sql  .= $virgula." y70_data = '$this->y70_data' ";
            $virgula = ",";
            if (trim($this->y70_data) == null) {
                $this->erro_sql = " Campo Data da Vistorias nao Informado.";
                $this->erro_campo = "y70_data_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y70_data_dia"])) {
                $sql  .= $virgula." y70_data = null ";
                $virgula = ",";
                if (trim($this->y70_data) == null) {
                    $this->erro_sql = " Campo Data da Vistorias nao Informado.";
                    $this->erro_campo = "y70_data_dia";
                    $this->erro_banco = "";
                    $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        if (trim($this->y70_hora)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y70_hora"])) {
            $sql  .= $virgula." y70_hora = '$this->y70_hora' ";
            $virgula = ",";
            if (trim($this->y70_hora) == null) {
                $this->erro_sql = " Campo Hora da Vistoria nao Informado.";
                $this->erro_campo = "y70_hora";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y70_obs)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y70_obs"])) {
            $sql  .= $virgula." y70_obs = '$this->y70_obs' ";
            $virgula = ",";
        }
        if (trim($this->y70_contato)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y70_contato"])) {
            $sql  .= $virgula." y70_contato = '$this->y70_contato' ";
            $virgula = ",";
        }
        if (trim($this->y70_tipovist)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y70_tipovist"])) {
            $sql  .= $virgula." y70_tipovist = $this->y70_tipovist ";
            $virgula = ",";
            if (trim($this->y70_tipovist) == null) {
                $this->erro_sql = " Campo Código do Tipo nao Informado.";
                $this->erro_campo = "y70_tipovist";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y70_ultandam)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y70_ultandam"])) {
            $sql  .= $virgula." y70_ultandam = $this->y70_ultandam ";
            $virgula = ",";
            if (trim($this->y70_ultandam) == null) {
                $this->erro_sql = " Campo Codigo do Último Andamento Gerado nao Informado.";
                $this->erro_campo = "y70_ultandam";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y70_id_usuario)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y70_id_usuario"])) {
            $sql  .= $virgula." y70_id_usuario = $this->y70_id_usuario ";
            $virgula = ",";
            if (trim($this->y70_id_usuario) == null) {
                $this->erro_sql = " Campo Cod. Usuário nao Informado.";
                $this->erro_campo = "y70_id_usuario";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y70_coddepto)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y70_coddepto"])) {
            $sql  .= $virgula." y70_coddepto = $this->y70_coddepto ";
            $virgula = ",";
            if (trim($this->y70_coddepto) == null) {
                $this->erro_sql = " Campo Código nao Informado.";
                $this->erro_campo = "y70_coddepto";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y70_numbloco)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y70_numbloco"])) {
            $sql  .= $virgula." y70_numbloco = '$this->y70_numbloco' ";
            $virgula = ",";
        }
        if (trim($this->y70_parcial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y70_parcial"])) {
            $sql  .= $virgula." y70_parcial = '$this->y70_parcial' ";
            $virgula = ",";
            if (trim($this->y70_parcial) == null) {
                $this->erro_sql = " Campo Vistoria parcial nao Informado.";
                $this->erro_campo = "y70_parcial";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y70_ativo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y70_ativo"])) {
            $sql  .= $virgula." y70_ativo = '$this->y70_ativo' ";
            $virgula = ",";
            if (trim($this->y70_ativo) == null) {
                $this->erro_sql = " Campo Ativo nao Informado.";
                $this->erro_campo = "y70_ativo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y70_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y70_instit"])) {
            $sql  .= $virgula." y70_instit = $this->y70_instit ";
            $virgula = ",";
            if (trim($this->y70_instit) == null) {
                $this->erro_sql = " Campo Cod. Instituição nao Informado.";
                $this->erro_campo = "y70_instit";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($y70_codvist!=null) {
            $sql .= " y70_codvist = $this->y70_codvist";
        }


        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "vistorias nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->y70_codvist;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "vistorias nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->y70_codvist;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->y70_codvist;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao para exclusao
    public function excluir($y70_codvist = null, $dbwhere = null)
    {
        $sql = " delete from fiscalizacao.fis_vistorias where ";
        $sql2 = "";
        if ($dbwhere==null || $dbwhere =="") {
            if ($y70_codvist != "") {
                if ($sql2!="") {
                    $sql2 .= " and ";
                }
                $sql2 .= " y70_codvist = $y70_codvist ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "vistorias nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y70_codvist;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "vistorias nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y70_codvist;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$y70_codvist;
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
            $this->erro_sql   = "Record Vazio na Tabela:vistorias";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
    public function sql_calculo($y70_codvist = "")
    {

        $sSqlCalculo = "select fiscalizacao.fc_fis_vistorias($y70_codvist)";
        $result      =  db_query($sSqlCalculo) or die($sSqlCalculo);
        return $result;
    }

    public function sql_query($y70_codvist = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_vistorias ";
        $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = fis_vistorias.y70_id_usuario";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_vistorias.y70_coddepto";
        $sql .= "      inner join fiscalizacao.fis_fandam  on  fis_fandam.y39_codandam = fis_vistorias.y70_ultandam";
        $sql .= "      inner join fiscalizacao.fis_tipovistorias  on  fis_tipovistorias.y77_codtipo = fis_vistorias.y70_tipovist";
        $sql .= "      inner join db_config  on  db_config.codigo = db_depart.instit";
        $sql .= "      inner join db_usuarios  as a on   a.id_usuario = fis_fandam.y39_id_usuario";
        $sql .= "      inner join fiscalizacao.fis_tipoandam  on  fis_tipoandam.y41_codtipo = fis_fandam.y39_codtipo";
        $sql .= "      inner join db_depart  as b on   b.coddepto = fis_tipovistorias.y77_coddepto";
        $sql .= "      inner join fiscalizacao.fis_tipoandam  as c on   c.y41_codtipo = fis_tipovistorias.y77_tipoandam";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y70_codvist!=null) {
                $sql2 .= " where fis_vistorias.y70_codvist = $y70_codvist ";
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
    public function sql_query_busca($y70_codvist = null, $db_where = "")
    {
        $sql = "select y70_codvist,dl_Identificação,dl_Código,x.z01_numcgm,cgm.z01_nome,y77_descricao,y70_data,y70_hora,y39_codandam,y70_coddepto
		           from (
             select y70_codvist,y70_data,y70_hora,y70_obs,y77_descricao,y39_codandam,y70_coddepto,y70_id_usuario,y70_instit,y70_ativo,
                    case when q02_numcgm is not null then 'Inscrição' else
                            (case when j01_numcgm is not null then 'Matrícula' else
                                    (case when y73_numcgm is not null then 'CGM' else
                                            (case when y80_numcgm is not null then 'Sanitário' else 'Nenhum'
                                            end)
                                    end)
                            end)
                    end as dl_Identificação ,
                    case when y71_inscr is not null then y71_inscr else
                            (case when y72_matric is not null then y72_matric else
                                    (case when y73_numcgm is not null then y73_numcgm else
                                            (case when y74_codsani is not null then y74_codsani
                                            end)
                                    end)
                            end)
                    end as dl_Código,
                    case when q02_numcgm is not null then q02_numcgm else
                            (case when j01_numcgm is not null then j01_numcgm else
                                    (case when y73_numcgm is not null then y73_numcgm else
                                            (case when y80_numcgm is not null then y80_numcgm else q02_numcgm
                                            end)
                                    end)
                            end)
                    end as z01_numcgm
              from fiscalizacao.fis_vistorias
                    left outer join fiscalizacao.fis_vistinscr on y71_codvist = y70_codvist
                    left join fiscalizacao.fis_vistmatric on y72_codvist = y70_codvist
                    left join fiscalizacao.fis_vistcgm on y73_codvist = y70_codvist
                    left join fiscalizacao.fis_vistsanitario on y74_codvist = y70_codvist
                    left join issbase on y71_inscr = q02_inscr
										left join iptubase on j01_matric = y72_matric
                    left join fiscalizacao.fis_sanitario on y80_codsani = y74_codsani
                    inner join fiscalizacao.fis_tipovistorias  on  fis_tipovistorias.y77_codtipo = fis_vistorias.y70_tipovist
                    inner join fiscalizacao.fis_fandam  on  fis_fandam.y39_codandam = fis_vistorias.y70_ultandam
              ) as x
							inner join cgm on cgm.z01_numcgm = x.z01_numcgm ";
        $sql2 = "";
        if ($db_where=="") {
            if ($y70_codvist!=null) {
                $sql2 .= " where fis_vistorias.y70_codvist = $y70_codvist ";
            }
        } elseif ($db_where != "") {
            $sql2 = " where $db_where";
        }
        $sql .= $sql2;

        return $sql;
    }
    public function sql_querycgm($y70_codvist = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        $sql .= " case when q02_numcgm is not null then q02_numcgm else
                        (case when j01_numcgm is not null then j01_numcgm else
                                (case when y73_numcgm is not null then y73_numcgm else
                                        (case when y80_numcgm is not null then y80_numcgm else q02_numcgm
                                        end)
                                end)
                        end)
                end as z01_numcgm from fiscalizacao.fis_vistorias left outer join fiscalizacao.fis_vistinscr on y71_codvist = y70_codvist left join vistmatri
                ";
        $sql2 = "";
        if ($y70_codvist!=null) {
            $sql2 .= " where fis_vistorias.y70_codvist = $y70_codvist ";
        }
        $sql .= $sql2;
        return $sql;
    }
    public function sql_query_cons($y70_codvist = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_vistorias ";
        $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = fis_vistorias.y70_id_usuario";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_vistorias.y70_coddepto";
        $sql .= "      inner join fiscalizacao.fis_fandam  on  fis_fandam.y39_codandam = fis_vistorias.y70_ultandam";
        $sql .= "      inner join fiscalizacao.fis_tipovistorias  on  fis_tipovistorias.y77_codtipo = fis_vistorias.y70_tipovist";
        $sql .= "      inner join db_usuarios x  on  x.id_usuario = fis_fandam.y39_id_usuario";
        $sql .= "      inner join fiscalizacao.fis_tipoandam  on  fis_tipoandam.y41_codtipo = fis_fandam.y39_codtipo";
        $sql .= "      inner join db_depart  as a on   a.coddepto = fis_tipovistorias.y77_coddepto";
        $sql .= "      inner join fiscalizacao.fis_tipoandam  as b on   b.y41_codtipo = fis_tipovistorias.y77_tipoandam";
        $sql .= "      left join fiscalizacao.fis_vistcgm on fis_vistcgm.y73_codvist = fis_vistorias.y70_codvist";
        $sql .= "      left join fiscalizacao.fis_vistmatric on fis_vistmatric.y72_codvist = fis_vistorias.y70_codvist";
        $sql .= "      left join fiscalizacao.fis_vistinscr on fis_vistinscr.y71_codvist = fis_vistorias.y70_codvist";
        $sql .= "      left join issbase on y71_inscr = q02_inscr ";
        $sql .= "      left join fiscalizacao.fis_vistsanitario on fis_vistsanitario.y74_codvist = fis_vistorias.y70_codvist";
        $sql .= "      left join fiscalizacao.fis_sanitario on y80_codsani = y74_codsani";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y70_codvist!=null) {
                $sql2 .= " where fis_vistorias.y70_codvist = $y70_codvist ";
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
    public function sql_query_deb($y70_codvist = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_vistorias ";
        $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = fis_vistorias.y70_id_usuario";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_vistorias.y70_coddepto";
        $sql .= "      inner join fiscalizacao.fis_fandam  on  fis_fandam.y39_codandam = fis_vistorias.y70_ultandam";
        $sql .= "      inner join fiscalizacao.fis_tipovistorias  on  fis_tipovistorias.y77_codtipo = fis_vistorias.y70_tipovist";
        $sql .= "      inner join db_usuarios x on  x.id_usuario = fis_fandam.y39_id_usuario";
        $sql .= "      inner join fiscalizacao.fis_tipoandam  on  fis_tipoandam.y41_codtipo = fis_fandam.y39_codtipo";
        $sql .= "      inner join db_depart  as a on   a.coddepto = fis_tipovistorias.y77_coddepto";
        $sql .= "      inner join fiscalizacao.fis_tipoandam  as b on   b.y41_codtipo = fis_tipovistorias.y77_tipoandam";
        $sql .= "      left join fiscalizacao.fis_vistorianumpre on  fis_vistorias.y70_codvist = fis_vistorianumpre.y69_codvist";
        $sql .= "      left join arrecad on arrecad.k00_numpre = fis_vistorianumpre.y69_numpre";
        $sql .= "      left join arrecant on arrecant.k00_numpre = fis_vistorianumpre.y69_numpre";
        $sql .= "      left join arrepaga on arrepaga.k00_numpre = fis_vistorianumpre.y69_numpre";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y70_codvist!=null) {
                $sql2 .= " where fis_vistorias.y70_codvist = $y70_codvist ";
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
    public function sql_query_file($y70_codvist = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_vistorias ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y70_codvist!=null) {
                $sql2 .= " where fis_vistorias.y70_codvist = $y70_codvist ";
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
    public function sql_query_info($y70_codvist = null, $campos = "*", $ordem = null, $db_where = "")
    {
        $sql = "select $campos from (
             select fis_vistorias.*,
                    case when q02_numcgm is not null then 'Inscrição' else
			    		(case when j01_numcgm is not null then 'Matrícula' else
				    		(case when y73_numcgm is not null then 'CGM' else
					    		(case when y80_numcgm is not null then 'Sanitário' else 'Nenhum'
					    		end)
				    		end)
			    		end)
		    		end as identificacao ,
                    case when y71_inscr is not null then y71_inscr else
			    		(case when y72_matric is not null then y72_matric else
				    		(case when y73_numcgm is not null then y73_numcgm else
					    		(case when y74_codsani is not null then y74_codsani
					    		end)
				    		end)
			    		end)
		    		end as codigo,
                    case when q02_numcgm is not null then q02_numcgm else
			    		(case when j01_numcgm is not null then j01_numcgm else
				    		(case when y73_numcgm is not null then y73_numcgm else
					    		(case when y80_numcgm is not null then y80_numcgm else q02_numcgm
					    		end)
				    		end)
			    		end)
		    		end as z01_numcgm
	      	from fiscalizacao.fis_vistorias
		    	left outer join fiscalizacao.fis_vistinscr on y71_codvist = y70_codvist
		    	left join fiscalizacao.fis_vistmatric on y72_codvist = y70_codvist
		    	left join fiscalizacao.fis_vistcgm on y73_codvist = y70_codvist
		    	left join fiscalizacao.fis_vistsanitario on y74_codvist = y70_codvist
		    	left join issbase on y71_inscr = q02_inscr
		    	left join iptubase on j01_matric = y72_matric
		    	left join fiscalizacao.fis_sanitario on y80_codsani = y74_codsani ) as x

				inner join cgm on cgm.z01_numcgm = x.z01_numcgm
   			inner join db_usuarios  on  db_usuarios.id_usuario = x.y70_id_usuario
				inner join db_depart  on  db_depart.coddepto = x.y70_coddepto
   			inner join fiscalizacao.fis_fandam  on  fis_fandam.y39_codandam = x.y70_ultandam
 				inner join fiscalizacao.fis_tipovistorias  on  fis_tipovistorias.y77_codtipo = x.y70_tipovist
       	left join fiscalizacao.fis_vistlocal on y10_codvist = y70_codvist
     		left join ruas on y10_codigo = j14_codigo
     		left join bairro on y10_codi = j13_codi";
        $sql2 = "";
        if ($db_where=="") {
            if ($y70_codvist!=null) {
                $sql2 .= " where y70_codvist = $y70_codvist ";
            }
        } elseif ($db_where != "") {
            $sql2 = " where $db_where";
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
    public function sql_querylocal($y70_codvist = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_vistorias ";
        $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = fis_vistorias.y70_id_usuario";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_vistorias.y70_coddepto";
        $sql .= "      inner join fiscalizacao.fis_fandam  on  fis_fandam.y39_codandam = fis_vistorias.y70_ultandam";
        $sql .= "      inner join fiscalizacao.fis_tipovistorias  on  fis_tipovistorias.y77_codtipo = fis_vistorias.y70_tipovist";
        $sql .= "      inner join db_usuarios x  on  x.id_usuario = fis_fandam.y39_id_usuario";
        $sql .= "      inner join fiscalizacao.fis_tipoandam  on  fis_tipoandam.y41_codtipo = fis_fandam.y39_codtipo";
        $sql .= "      inner join db_depart  as a on   a.coddepto = fis_tipovistorias.y77_coddepto";
        $sql .= "      inner join fiscalizacao.fis_tipoandam  as b on   b.y41_codtipo = fis_tipovistorias.y77_tipoandam";
        $sql .= "      inner join fiscalizacao.fis_vistlocal on y10_codvist = y70_codvist";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y70_codvist!=null) {
                $sql2 .= " where fis_vistorias.y70_codvist = $y70_codvist ";
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

    public function sql_verifica_cgm_possui_calculo($cgm)
    {
        $schema = 'fiscalizacao';
        $sql = "
            with inscricao_cgm as (
                select y71_codvist from issbase
                inner join {$schema}.fis_vistinscr on y71_inscr = q02_inscr
                where q02_numcgm = {$cgm}
            ),
            sanitario_cgm as (
                select y74_codvist from {$schema}.fis_sanitario
                inner join {$schema}.fis_vistsanitario on y74_codsani = y80_codsani
                where y80_numcgm = {$cgm}
            ),
            vistorias_cgm AS (
                select y71_codvist as codvist from inscricao_cgm
                union
                select y74_codvist from sanitario_cgm
            )
            select fis_vistoriacalculo.y123_codvist
            from {$schema}.fis_vistoriacalculo
            inner join vistorias_cgm on fis_vistoriacalculo.y123_codvist = vistorias_cgm.codvist
            limit 1
        ";
        return $sql;
    }
}
