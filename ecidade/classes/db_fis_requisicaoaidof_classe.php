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
//CLASSE DA ENTIDADE requisicaoaidof
class cl_fis_requisicaoaidof
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
    public $y116_id = 0;
    public $y116_tipodocumento = 0;
    public $y116_codigografica = 0;
    public $y116_idusuario = 0;
    public $y116_inscricaomunicipal = 0;
    public $y116_datalancamento_dia = null;
    public $y116_datalancamento_mes = null;
    public $y116_datalancamento_ano = null;
    public $y116_datalancamento = null;
    public $y116_quantidadesolicitada = 0;
    public $y116_quantidadeLiberada = 0;
    public $y116_status = null;
    public $y116_observacao = null;
    public $y116_codigoaidof = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 y116_id = int4 = Código
                 y116_tipodocumento = int4 = Tipo de Documento
                 y116_codigografica = int4 = Código da Gráfica
                 y116_idusuario = int4 = Código Usuário
                 y116_inscricaomunicipal = int4 = Inscrição Municipal
                 y116_datalancamento = date = Data do Lançamento
                 y116_quantidadesolicitada = int4 = Quantidade Solicitada
                 y116_quantidadeLiberada = int4 = Quantidade Liberada
                 y116_status = char(1) = Status
                 y116_observacao = text = Observação
                 y116_codigoaidof = int4 = Código Aidof
                 ";
    //funcao construtor da classe
    public function __construct()
    {
         //classes dos rotulos dos campos
         $this->rotulo = new rotulo("fis_requisicaoaidof");
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
            $this->y116_id = ($this->y116_id == ""?@$GLOBALS["HTTP_POST_VARS"]["y116_id"]:$this->y116_id);
            $this->y116_tipodocumento = ($this->y116_tipodocumento == ""?@$GLOBALS["HTTP_POST_VARS"]["y116_tipodocumento"]:$this->y116_tipodocumento);
            $this->y116_codigografica = ($this->y116_codigografica == ""?@$GLOBALS["HTTP_POST_VARS"]["y116_codigografica"]:$this->y116_codigografica);
            $this->y116_idusuario = ($this->y116_idusuario == ""?@$GLOBALS["HTTP_POST_VARS"]["y116_idusuario"]:$this->y116_idusuario);
            $this->y116_inscricaomunicipal = ($this->y116_inscricaomunicipal == ""?@$GLOBALS["HTTP_POST_VARS"]["y116_inscricaomunicipal"]:$this->y116_inscricaomunicipal);
            if ($this->y116_datalancamento == "") {
                $this->y116_datalancamento_dia = ($this->y116_datalancamento_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y116_datalancamento_dia"]:$this->y116_datalancamento_dia);
                $this->y116_datalancamento_mes = ($this->y116_datalancamento_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y116_datalancamento_mes"]:$this->y116_datalancamento_mes);
                $this->y116_datalancamento_ano = ($this->y116_datalancamento_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y116_datalancamento_ano"]:$this->y116_datalancamento_ano);
                if ($this->y116_datalancamento_dia != "") {
                    $this->y116_datalancamento = $this->y116_datalancamento_ano."-".$this->y116_datalancamento_mes."-".$this->y116_datalancamento_dia;
                }
            }
            $this->y116_quantidadesolicitada = ($this->y116_quantidadesolicitada == ""?@$GLOBALS["HTTP_POST_VARS"]["y116_quantidadesolicitada"]:$this->y116_quantidadesolicitada);
            $this->y116_quantidadeLiberada = ($this->y116_quantidadeLiberada == ""?@$GLOBALS["HTTP_POST_VARS"]["y116_quantidadeLiberada"]:$this->y116_quantidadeLiberada);
            $this->y116_status = ($this->y116_status == ""?@$GLOBALS["HTTP_POST_VARS"]["y116_status"]:$this->y116_status);
            $this->y116_observacao = ($this->y116_observacao == ""?@$GLOBALS["HTTP_POST_VARS"]["y116_observacao"]:$this->y116_observacao);
            $this->y116_codigoaidof = ($this->y116_codigoaidof == ""?@$GLOBALS["HTTP_POST_VARS"]["y116_codigoaidof"]:$this->y116_codigoaidof);
        } else {
            $this->y116_id = ($this->y116_id == ""?@$GLOBALS["HTTP_POST_VARS"]["y116_id"]:$this->y116_id);
        }
    }
    // funcao para inclusao
    public function incluir($y116_id = null)
    {
        $this->atualizacampos();
        if ($this->y116_tipodocumento == null) {
            $this->erro_sql = " Campo Tipo de Documento não informado.";
            $this->erro_campo = "y116_tipodocumento";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y116_codigografica == null) {
            $this->erro_sql = " Campo Código da Gráfica não informado.";
            $this->erro_campo = "y116_codigografica";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y116_idusuario == null) {
            $this->y116_idusuario = "NULL";
        }
        if ($this->y116_inscricaomunicipal == null) {
            $this->erro_sql = " Campo Inscrição Municipal não informado.";
            $this->erro_campo = "y116_inscricaomunicipal";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y116_datalancamento == null) {
            $this->erro_sql = " Campo Data do Lançamento não informado.";
            $this->erro_campo = "y116_datalancamento_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y116_quantidadesolicitada == null) {
            $this->erro_sql = " Campo Quantidade Solicitada não informado.";
            $this->erro_campo = "y116_quantidadesolicitada";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y116_quantidadeLiberada == null) {
            $this->y116_quantidadeLiberada = "0";
        }
        if ($this->y116_status == null) {
            $this->erro_sql = " Campo Status não informado.";
            $this->erro_campo = "y116_status";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y116_codigoaidof == null) {
            $this->y116_codigoaidof = "0";
        }
        $sql = "insert into fiscalizacao.fis_requisicaoaidof (
                                       y116_tipodocumento
                                      ,y116_codigografica
                                      ,y116_idusuario
                                      ,y116_inscricaomunicipal
                                      ,y116_datalancamento
                                      ,y116_quantidadesolicitada
                                      ,y116_quantidadeLiberada
                                      ,y116_status
                                      ,y116_observacao
                                      ,y116_codigoaidof
                       )
                values (
                                $this->y116_tipodocumento
                               ,$this->y116_codigografica
                               ,$this->y116_idusuario
                               ,$this->y116_inscricaomunicipal
                               ,".($this->y116_datalancamento == "null" || $this->y116_datalancamento == ""?"null":"'".$this->y116_datalancamento."'")."
                               ,$this->y116_quantidadesolicitada
                               ,$this->y116_quantidadeLiberada
                               ,'$this->y116_status'
                               ,'$this->y116_observacao'
                               ,$this->y116_codigoaidof
                      ) returning y116_id ";
        $result = db_query($sql);
        $this->y116_id = db_utils::fieldsmemory($result, 0)->y116_id;

        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Requisição de AIDOF ($this->y116_id) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Requisição de AIDOF já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "Requisição de AIDOF ($this->y116_id) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y116_id;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);


        return true;
    }
   // funcao para alteracao
    public function alterar($y116_id = null)
    {
          $this->atualizacampos();
         $sql = " update fiscalizacao.fis_requisicaoaidof set ";
         $virgula = "";
        if (trim($this->y116_tipodocumento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y116_tipodocumento"])) {
            $sql  .= $virgula." y116_tipodocumento = $this->y116_tipodocumento ";
            $virgula = ",";
            if (trim($this->y116_tipodocumento) == null) {
                $this->erro_sql = " Campo Tipo de Documento não informado.";
                $this->erro_campo = "y116_tipodocumento";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y116_codigografica)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y116_codigografica"])) {
            $sql  .= $virgula." y116_codigografica = $this->y116_codigografica ";
            $virgula = ",";
            if (trim($this->y116_codigografica) == null) {
                $this->erro_sql = " Campo Código da Gráfica não informado.";
                $this->erro_campo = "y116_codigografica";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y116_idusuario)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y116_idusuario"])) {
            if (trim($this->y116_idusuario)=="" && isset($GLOBALS["HTTP_POST_VARS"]["y116_idusuario"])) {
                $this->y116_idusuario = "0" ;
            }
            $sql  .= $virgula." y116_idusuario = $this->y116_idusuario ";
            $virgula = ",";
        }
        if (trim($this->y116_inscricaomunicipal)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y116_inscricaomunicipal"])) {
            $sql  .= $virgula." y116_inscricaomunicipal = $this->y116_inscricaomunicipal ";
            $virgula = ",";
            if (trim($this->y116_inscricaomunicipal) == null) {
                $this->erro_sql = " Campo Inscrição Municipal não informado.";
                $this->erro_campo = "y116_inscricaomunicipal";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y116_datalancamento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y116_datalancamento_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y116_datalancamento_dia"] !="")) {
            $sql  .= $virgula." y116_datalancamento = '$this->y116_datalancamento' ";
            $virgula = ",";
            if (trim($this->y116_datalancamento) == null) {
                $this->erro_sql = " Campo Data do Lançamento não informado.";
                $this->erro_campo = "y116_datalancamento_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y116_datalancamento_dia"])) {
                $sql  .= $virgula." y116_datalancamento = null ";
                $virgula = ",";
                if (trim($this->y116_datalancamento) == null) {
                    $this->erro_sql = " Campo Data do Lançamento não informado.";
                    $this->erro_campo = "y116_datalancamento_dia";
                    $this->erro_banco = "";
                    $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        if (trim($this->y116_quantidadesolicitada)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y116_quantidadesolicitada"])) {
            $sql  .= $virgula." y116_quantidadesolicitada = $this->y116_quantidadesolicitada ";
            $virgula = ",";
            if (trim($this->y116_quantidadesolicitada) == null) {
                $this->erro_sql = " Campo Quantidade Solicitada não informado.";
                $this->erro_campo = "y116_quantidadesolicitada";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y116_quantidadeLiberada)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y116_quantidadeLiberada"])) {
            if (trim($this->y116_quantidadeLiberada)=="" && isset($GLOBALS["HTTP_POST_VARS"]["y116_quantidadeLiberada"])) {
                $this->y116_quantidadeLiberada = "0" ;
            }
            $sql  .= $virgula." y116_quantidadeLiberada = $this->y116_quantidadeLiberada ";
            $virgula = ",";
        }
        if (trim($this->y116_status)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y116_status"])) {
            $sql  .= $virgula." y116_status = '$this->y116_status' ";
            $virgula = ",";
            if (trim($this->y116_status) == null) {
                $this->erro_sql = " Campo Status não informado.";
                $this->erro_campo = "y116_status";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y116_observacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y116_observacao"])) {
            $sql  .= $virgula." y116_observacao = '$this->y116_observacao' ";
            $virgula = ",";
        }
        if (trim($this->y116_codigoaidof)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y116_codigoaidof"])) {
            if (trim($this->y116_codigoaidof)=="" && isset($GLOBALS["HTTP_POST_VARS"]["y116_codigoaidof"])) {
                $this->y116_codigoaidof = "0" ;
            }
            $sql  .= $virgula." y116_codigoaidof = $this->y116_codigoaidof ";
            $virgula = ",";
        }
        $sql .= " where ";
        if ($y116_id!=null) {
            $sql .= " y116_id = $this->y116_id";
        }


        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Requisição de AIDOF nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->y116_id;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "Requisição de AIDOF nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->y116_id;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->y116_id;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao para exclusao
    public function excluir($y116_id = null, $dbwhere = null)
    {
         $sql = " delete from fiscalizacao.fis_requisicaoaidof where ";
         $sql2 = "";
        if ($dbwhere==null || $dbwhere =="") {
            if ($y116_id != "") {
                if ($sql2!="") {
                    $sql2 .= " and ";
                }
                $sql2 .= " y116_id = $y116_id ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Requisição de AIDOF nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y116_id;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "Requisição de AIDOF nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y116_id;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$y116_id;
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
            $this->erro_sql   = "Record Vazio na Tabela:requisicaoaidof";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
   // funcao do sql
    public function sql_query($y116_id = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_requisicaoaidof ";
        $sql .= "      inner join issbase  on  issbase.q02_inscr = fis_requisicaoaidof.y116_inscricaomunicipal";
        $sql .= "      left  join db_usuarios  on  db_usuarios.id_usuario = fis_requisicaoaidof.y116_idusuario";
        $sql .= "      inner join notasiss  on  notasiss.q09_codigo = fis_requisicaoaidof.y116_tipodocumento";
        $sql .= "      inner join fiscalizacao.fis_graficas  on  fis_graficas.y20_grafica = fis_requisicaoaidof.y116_codigografica";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = issbase.q02_numcgm";
        $sql .= "      inner join gruponotaiss  on  gruponotaiss.q139_sequencial = notasiss.q09_gruponotaiss";
        $sql .= "      inner join cgm  as a on   a.z01_numcgm = fis_graficas.y20_grafica";
        $sql .= "      inner join db_usuarios  as b on   b.id_usuario = fis_graficas.y20_id_usuario";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y116_id!=null) {
                $sql2 .= " where fis_requisicaoaidof.y116_id = $y116_id ";
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
   // funcao do sql
    public function sql_query_file($y116_id = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_requisicaoaidof ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y116_id!=null) {
                $sql2 .= " where fis_requisicaoaidof.y116_id = $y116_id ";
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
   /**
   * Sql com dados da requisicação de aidof
   *
   * @param string $y116_id
   * @param string $campos
   * @param string $ordem
   * @param string $dbwhere
   * @return string
   */
    public function sql_query_dadosRequisicao($y116_id = null, $campos = '*', $ordem = null, $dbwhere = '')
    {
        $sSql = 'select ';

        if ($campos != '*') {
            $campos_sql = explode('#', $campos);
            $virgula    = '';

            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sSql .= $virgula.$campos_sql[$i];
                $virgula = ',';
            }
        } else {
            $sSql .= $campos;
        }

        $sSql .= ' from fiscalizacao.fis_requisicaoaidof ';
        $sSql .= '      inner join notasiss     on  notasiss.q09_codigo          = fis_requisicaoaidof.y116_tipodocumento';
        $sSql .= '      inner join gruponotaiss on  gruponotaiss.q139_sequencial = notasiss.q09_gruponotaiss';
        $sSql .= '      inner join issbase      on  issbase.q02_inscr            = fis_requisicaoaidof.y116_inscricaomunicipal';
        $sSql .= '      inner join cgm          on  cgm.z01_numcgm               = issbase.q02_numcgm';
        $sSql .= '      left  join db_usuarios  on  db_usuarios.id_usuario       = fis_requisicaoaidof.y116_idusuario';

        $sSql2 = '';

        if ($dbwhere == '') {
            if ($y116_id != null) {
                $sSql2 .= " where fis_requisicaoaidof.y116_id = {$y116_id} ";
            }
        } elseif ($dbwhere != '') {
            $sSql2 = " where {$dbwhere} ";
        }

        $sSql .= $sSql2;

        if ($ordem != null) {
            $sSql       .= ' order by ';
            $campos_sql = explode("#", $ordem);
            $virgula    = '';

            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sSql     .= $virgula.$campos_sql[$i];
                $virgula  = ',';
            }
        }

        return $sSql;
    }
   /**
   * Retorna Sql para pesquisa das requisições realizadas dos seus clientes
   * pesquisa atravez do Cgm do Escritório
   *
   * @param integer $iCgm
   * @param string $sCampos
   * @param string $sOrdem
   * @return string
   */
    public function sql_query_RequisicoesPorEscritorio($iCgm = null, $iInscricao = null, $sCampos = "*", $sOrdem = null)
    {

        $sSql = "select ";

        if ($sCampos != "*") {
            $sCamposSql = explode("#", $sCampos);
            $sVirgula   = "";

            for ($i = 0; $i < sizeof($sCamposSql); $i++) {
                $sSql     .= $sVirgula. $sCamposSql[$i];
                $sVirgula  = ",";
            }
        } else {
            $sSql .= $sCampos;
        }


        if ((trim($iCgm) > 0) || (trim($iInscricao) > 0)) {
            $sWhere = " where ";

            if (trim($iCgm) > 0) {
                $sWhere .= ' q10_numcgm = ' . $iCgm;
            }

            if (trim($iInscricao) > 0) {
                if (trim($iCgm) > 0) {
                    $sWhere .= ' and ';
                }

                $sWhere .= ' q02_inscr = ' . $iInscricao;
            }
        }

        $sSql .= "  from escrito                                                            ";
        $sSql .= "       inner join issbase         on q02_inscr               = q10_inscr  ";
        $sSql .= "       inner join cgm             on z01_numcgm              = q02_numcgm ";
        $sSql .= "       inner join fiscalizacao.fis_requisicaoaidof on y116_inscricaomunicipal = q02_inscr  ";
        $sSql .= "       {$sWhere}                                                          ";
        $sSql .= "   and q10_dtfim  is null                                                 ";
        $sSql .= "   and q02_dtbaix is null                                                 ";

        if ($sOrdem != null) {
            $sSql       .= " order by ";
            $sCamposSql  = explode("#", $sOrdem);
            $sVirgula    = "";

            for ($i = 0; $i < sizeof($sCamposSql); $i++) {
                $sSql     .= $sVirgula . $sCamposSql[$i];
                $sVirgula  = ",";
            }
        }



        return $sSql;
    }
}
