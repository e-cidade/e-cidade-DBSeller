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
//CLASSE DA ENTIDADE levvalor
class cl_fis_levvalor
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
    public $y63_sequencia = 0;
    public $y63_codlev = 0;
    public $y63_ano = 0;
    public $y63_mes = 0;
    public $y63_dtvenc_dia = null;
    public $y63_dtvenc_mes = null;
    public $y63_dtvenc_ano = null;
    public $y63_dtvenc = null;
    public $y63_bruto = 0;
    public $y63_aliquota = 0;
    public $y63_pago = 0;
    public $y63_saldo = 0;
    public $y63_histor = null;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 y63_sequencia = int4 = Sequencial
                 y63_codlev = int4 = Levantamento
                 y63_ano = int4 = Exercicio
                 y63_mes = int4 = Competência
                 y63_dtvenc = date = Vencimento
                 y63_bruto = float8 = Valor Bruto
                 y63_aliquota = float8 = Alíquota(%)
                 y63_pago = float8 = Valor Pago
                 y63_saldo = float8 = Saldo a Pagar
                 y63_histor = text = Descrição
                 ";
   //funcao construtor da classe
    public function __construct()
    {
      //classes dos rotulos dos campos
        $this->rotulo = new rotulo("fis_levvalor");
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
            $this->y63_sequencia = ($this->y63_sequencia == ""?@$GLOBALS["HTTP_POST_VARS"]["y63_sequencia"]:$this->y63_sequencia);
            $this->y63_codlev = ($this->y63_codlev == ""?@$GLOBALS["HTTP_POST_VARS"]["y63_codlev"]:$this->y63_codlev);
            $this->y63_ano = ($this->y63_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y63_ano"]:$this->y63_ano);
            $this->y63_mes = ($this->y63_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y63_mes"]:$this->y63_mes);
            if ($this->y63_dtvenc == "") {
                $this->y63_dtvenc_dia = ($this->y63_dtvenc_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y63_dtvenc_dia"]:$this->y63_dtvenc_dia);
                $this->y63_dtvenc_mes = ($this->y63_dtvenc_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y63_dtvenc_mes"]:$this->y63_dtvenc_mes);
                $this->y63_dtvenc_ano = ($this->y63_dtvenc_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y63_dtvenc_ano"]:$this->y63_dtvenc_ano);
                if ($this->y63_dtvenc_dia != "") {
                     $this->y63_dtvenc = $this->y63_dtvenc_ano."-".$this->y63_dtvenc_mes."-".$this->y63_dtvenc_dia;
                }
            }
            $this->y63_bruto = ($this->y63_bruto == ""?@$GLOBALS["HTTP_POST_VARS"]["y63_bruto"]:$this->y63_bruto);
            $this->y63_aliquota = ($this->y63_aliquota == ""?@$GLOBALS["HTTP_POST_VARS"]["y63_aliquota"]:$this->y63_aliquota);
            $this->y63_pago = ($this->y63_pago == ""?@$GLOBALS["HTTP_POST_VARS"]["y63_pago"]:$this->y63_pago);
            $this->y63_saldo = ($this->y63_saldo == ""?@$GLOBALS["HTTP_POST_VARS"]["y63_saldo"]:$this->y63_saldo);
            $this->y63_histor = ($this->y63_histor == ""?@$GLOBALS["HTTP_POST_VARS"]["y63_histor"]:$this->y63_histor);
        } else {
            $this->y63_sequencia = ($this->y63_sequencia == ""?@$GLOBALS["HTTP_POST_VARS"]["y63_sequencia"]:$this->y63_sequencia);
        }
    }
   // funcao para inclusao
    public function incluir($y63_sequencia = null)
    {
        $this->atualizacampos();
        if ($this->y63_codlev == null) {
            $this->erro_sql = " Campo Levantamento nao Informado.";
            $this->erro_campo = "y63_codlev";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y63_ano == null) {
            $this->erro_sql = " Campo Exercicio nao Informado.";
            $this->erro_campo = "y63_ano";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y63_mes == null) {
            $this->erro_sql = " Campo Competência nao Informado.";
            $this->erro_campo = "y63_mes";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y63_dtvenc == null) {
            $this->erro_sql = " Campo Vencimento nao Informado.";
            $this->erro_campo = "y63_dtvenc_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y63_bruto == null) {
            $this->erro_sql = " Campo Valor Bruto nao Informado.";
            $this->erro_campo = "y63_bruto";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y63_aliquota == null) {
            $this->erro_sql = " Campo Alíquota(%) nao Informado.";
            $this->erro_campo = "y63_aliquota";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y63_pago == null) {
            $this->erro_sql = " Campo Valor Pago nao Informado.";
            $this->erro_campo = "y63_pago";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y63_saldo == null) {
            $this->erro_sql = " Campo Saldo a Pagar nao Informado.";
            $this->erro_campo = "y63_saldo";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into fiscalizacao.fis_levvalor(
                                       y63_codlev
                                      ,y63_ano
                                      ,y63_mes
                                      ,y63_dtvenc
                                      ,y63_bruto
                                      ,y63_aliquota
                                      ,y63_pago
                                      ,y63_saldo
                                      ,y63_histor
                       )
                values (
                                $this->y63_codlev
                               ,$this->y63_ano
                               ,$this->y63_mes
                               ,".($this->y63_dtvenc == "null" || $this->y63_dtvenc == ""?"null":"'".$this->y63_dtvenc."'")."
                               ,$this->y63_bruto
                               ,$this->y63_aliquota
                               ,$this->y63_pago
                               ,$this->y63_saldo
                               ,'$this->y63_histor'
                      ) returning y63_sequencia ";
        $result = db_query($sql);
        $this->y63_sequencia = db_utils::fieldsmemory($result, 0)->y63_sequencia;

        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "levvalor ($this->y63_sequencia) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "levvalor já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "levvalor ($this->y63_sequencia) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->y63_sequencia;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);


        return true;
    }
   // funcao para alteracao
    public function alterar($y63_sequencia = null)
    {
        $this->atualizacampos();
        $sql = " update fiscalizacao.fis_levvalor set ";
        $virgula = "";
        if (trim($this->y63_codlev)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y63_codlev"])) {
            $sql  .= $virgula." y63_codlev = $this->y63_codlev ";
            $virgula = ",";
            if (trim($this->y63_codlev) == null) {
                $this->erro_sql = " Campo Levantamento nao Informado.";
                $this->erro_campo = "y63_codlev";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y63_ano)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y63_ano"])) {
            $sql  .= $virgula." y63_ano = $this->y63_ano ";
            $virgula = ",";
            if (trim($this->y63_ano) == null) {
                $this->erro_sql = " Campo Exercicio nao Informado.";
                $this->erro_campo = "y63_ano";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y63_mes)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y63_mes"])) {
            $sql  .= $virgula." y63_mes = $this->y63_mes ";
            $virgula = ",";
            if (trim($this->y63_mes) == null) {
                $this->erro_sql = " Campo Competência nao Informado.";
                $this->erro_campo = "y63_mes";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y63_dtvenc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y63_dtvenc_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y63_dtvenc_dia"] !="")) {
            $sql  .= $virgula." y63_dtvenc = '$this->y63_dtvenc' ";
            $virgula = ",";
            if (trim($this->y63_dtvenc) == null) {
                $this->erro_sql = " Campo Vencimento nao Informado.";
                $this->erro_campo = "y63_dtvenc_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y63_dtvenc_dia"])) {
                $sql  .= $virgula." y63_dtvenc = null ";
                $virgula = ",";
                if (trim($this->y63_dtvenc) == null) {
                    $this->erro_sql = " Campo Vencimento nao Informado.";
                    $this->erro_campo = "y63_dtvenc_dia";
                    $this->erro_banco = "";
                    $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        if (trim($this->y63_bruto)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y63_bruto"])) {
            $sql  .= $virgula." y63_bruto = $this->y63_bruto ";
            $virgula = ",";
            if (trim($this->y63_bruto) == null) {
                $this->erro_sql = " Campo Valor Bruto nao Informado.";
                $this->erro_campo = "y63_bruto";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y63_aliquota)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y63_aliquota"])) {
            $sql  .= $virgula." y63_aliquota = $this->y63_aliquota ";
            $virgula = ",";
            if (trim($this->y63_aliquota) == null) {
                $this->erro_sql = " Campo Alíquota(%) nao Informado.";
                $this->erro_campo = "y63_aliquota";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y63_pago)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y63_pago"])) {
            $sql  .= $virgula." y63_pago = $this->y63_pago ";
            $virgula = ",";
            if (trim($this->y63_pago) == null) {
                $this->erro_sql = " Campo Valor Pago nao Informado.";
                $this->erro_campo = "y63_pago";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y63_saldo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y63_saldo"])) {
            $sql  .= $virgula." y63_saldo = $this->y63_saldo ";
            $virgula = ",";
            if (trim($this->y63_saldo) == null) {
                $this->erro_sql = " Campo Saldo a Pagar nao Informado.";
                $this->erro_campo = "y63_saldo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y63_histor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y63_histor"])) {
            $sql  .= $virgula." y63_histor = '$this->y63_histor' ";
            $virgula = ",";
        }
        $sql .= " where ";
        if ($y63_sequencia!=null) {
            $sql .= " y63_sequencia = $this->y63_sequencia";
        }


        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "levvalor nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->y63_sequencia;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "levvalor nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->y63_sequencia;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->y63_sequencia;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao para exclusao
    public function excluir($y63_sequencia = null, $dbwhere = null)
    {
        $sql = " delete from fiscalizacao.fis_levvalor where ";
        $sql2 = "";
        if ($dbwhere==null || $dbwhere =="") {
            if ($y63_sequencia != "") {
                if ($sql2!="") {
                    $sql2 .= " and ";
                }
                $sql2 .= " y63_sequencia = $y63_sequencia ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "levvalor nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y63_sequencia;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "levvalor nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y63_sequencia;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$y63_sequencia;
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
        if ($result==false) {
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
            $this->erro_sql   = "Record Vazio na Tabela:levvalor";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
    public function sql_compl($y63_sequencia = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_levvalor ";
        $sql .= "      inner join levisncr  on fis_levanta.y60_codlev = fis_levvalor.y63_codlev";
        $sql .= "      inner join fiscalizacao.fis_levanta  on  fis_levanta.y60_codlev = fis_levvalor.y63_codlev";
        $sql .= "      left outer join fiscalizacao.fis_levvalorpgtos on fis_levvalor.y63_sequencia = fis_levvalorpgtos.y68_sequencia";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y63_sequencia!=null) {
                $sql2 .= " where fis_levvalor.y63_sequencia = $y63_sequencia ";
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
    public function sql_query($y63_sequencia = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_levvalor ";
        $sql .= "      inner join fiscalizacao.fis_levanta  on  fis_levanta.y60_codlev = fis_levvalor.y63_codlev";
        $sql .= "      left outer join fiscalizacao.fis_levvalorpgtos on fis_levvalor.y63_sequencia = fis_levvalorpgtos.y68_sequencia";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y63_sequencia!=null) {
                $sql2 .= " where fis_levvalor.y63_sequencia = $y63_sequencia ";
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
    public function sql_query_file($y63_sequencia = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_levvalor ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y63_sequencia!=null) {
                $sql2 .= " where fis_levvalor.y63_sequencia = $y63_sequencia ";
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
    public function sql_query_inf($y63_sequencia = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from (select fis_levvalor.*,y62_inscr,y93_numcgm,
     		          case when q02_numcgm is not null then q02_numcgm else
						    (case when y93_numcgm is not null then y93_numcgm
					         end)
					  end as z01_numcgm from fiscalizacao.fis_levvalor ";
        $sql .= "  left join fiscalizacao.fis_levinscr on y62_codlev = y63_codlev";
        $sql .= "  left join fiscalizacao.fis_levcgm on y93_codlev = y63_codlev";
        $sql .= "	left join issbase on  q02_inscr = y62_inscr";
        $sql .= ")as x";
        $sql .= "	inner join cgm on x.z01_numcgm=cgm.z01_numcgm";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y63_sequencia!=null) {
                $sql2 .= " where fis_levvalor.y63_sequencia = $y63_sequencia ";
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
    public function sql_query_notas($y63_sequencia = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_levvalor ";
        $sql .= "      inner join fiscalizacao.fis_levanta  on  fis_levanta.y60_codlev = fis_levvalor.y63_codlev";
        $sql .= "      left outer join fiscalizacao.fis_levantanotas  on fis_levvalor.y63_sequencia = y79_sequencia";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y63_sequencia!=null) {
                $sql2 .= " where fis_levvalor.y63_sequencia = $y63_sequencia ";
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
