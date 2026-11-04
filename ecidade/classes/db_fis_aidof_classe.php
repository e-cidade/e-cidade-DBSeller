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
//CLASSE DA ENTIDADE aidof
class cl_fis_aidof
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
    public $y08_codigo = 0;
    public $y08_nota = 0;
    public $y08_inscr = 0;
    public $y08_dtlanc_dia = null;
    public $y08_dtlanc_mes = null;
    public $y08_dtlanc_ano = null;
    public $y08_dtlanc = null;
    public $y08_notain = 0;
    public $y08_notafi = 0;
    public $y08_numcgm = 0;
    public $y08_obs = null;
    public $y08_login = 0;
    public $y08_quantsol = 0;
    public $y08_quantlib = 0;
    public $y08_cancel = 'f';
    public $y08_dataliberacaografica_dia = null;
    public $y08_dataliberacaografica_mes = null;
    public $y08_dataliberacaografica_ano = null;
    public $y08_dataliberacaografica = null;
    public $y08_datarecebimentocontribuinte_dia = null;
    public $y08_datarecebimentocontribuinte_mes = null;
    public $y08_datarecebimentocontribuinte_ano = null;
    public $y08_datarecebimentocontribuinte = null;
    public $y08_datalimitesolicitada_dia = null;
    public $y08_datalimitesolicitada_mes = null;
    public $y08_datalimitesolicitada_ano = null;
    public $y08_datalimitesolicitada = null;
    public $y08_datalimiteliberada_dia = null;
    public $y08_datalimiteliberada_mes = null;
    public $y08_datalimiteliberada_ano = null;
    public $y08_datalimiteliberada = null;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 y08_codigo = int4 = Código
                 y08_nota = int8 = Nota
                 y08_inscr = int4 = Inscrição
                 y08_dtlanc = date = Data de Lançamento
                 y08_notain = int4 = Nota Inicial
                 y08_notafi = int4 = Nota Final
                 y08_numcgm = int4 = Gráfica
                 y08_obs = text = Observação
                 y08_login = int8 = Login
                 y08_quantsol = int8 = Quantidade Solicitada
                 y08_quantlib = int8 = Quantidade Liberada
                 y08_cancel = bool = Cancelada
                 y08_dataliberacaografica = date = Data de liberação da gráfica
                 y08_datarecebimentocontribuinte = date = Data de recebimento do contribuinte
                 y08_datalimitesolicitada = date = Data limite solicitada
                 y08_datalimiteliberada = date = Data limite liberada
                 ";
   //funcao construtor da classe
    public function __construct()
    {
         //classes dos rotulos dos campos
         $this->rotulo = new rotulo("fis_aidof");
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
            $this->y08_codigo = ($this->y08_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["y08_codigo"]:$this->y08_codigo);
            $this->y08_nota = ($this->y08_nota == ""?@$GLOBALS["HTTP_POST_VARS"]["y08_nota"]:$this->y08_nota);
            $this->y08_inscr = ($this->y08_inscr == ""?@$GLOBALS["HTTP_POST_VARS"]["y08_inscr"]:$this->y08_inscr);
            if ($this->y08_dtlanc == "") {
                $this->y08_dtlanc_dia = ($this->y08_dtlanc_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y08_dtlanc_dia"]:$this->y08_dtlanc_dia);
                $this->y08_dtlanc_mes = ($this->y08_dtlanc_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y08_dtlanc_mes"]:$this->y08_dtlanc_mes);
                $this->y08_dtlanc_ano = ($this->y08_dtlanc_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y08_dtlanc_ano"]:$this->y08_dtlanc_ano);
                if ($this->y08_dtlanc_dia != "") {
                    $this->y08_dtlanc = $this->y08_dtlanc_ano."-".$this->y08_dtlanc_mes."-".$this->y08_dtlanc_dia;
                }
            }
            $this->y08_notain = ($this->y08_notain == ""?@$GLOBALS["HTTP_POST_VARS"]["y08_notain"]:$this->y08_notain);
            $this->y08_notafi = ($this->y08_notafi == ""?@$GLOBALS["HTTP_POST_VARS"]["y08_notafi"]:$this->y08_notafi);
            $this->y08_numcgm = ($this->y08_numcgm == ""?@$GLOBALS["HTTP_POST_VARS"]["y08_numcgm"]:$this->y08_numcgm);
            $this->y08_obs = ($this->y08_obs == ""?@$GLOBALS["HTTP_POST_VARS"]["y08_obs"]:$this->y08_obs);
            $this->y08_login = ($this->y08_login == ""?@$GLOBALS["HTTP_POST_VARS"]["y08_login"]:$this->y08_login);
            $this->y08_quantsol = ($this->y08_quantsol == ""?@$GLOBALS["HTTP_POST_VARS"]["y08_quantsol"]:$this->y08_quantsol);
            $this->y08_quantlib = ($this->y08_quantlib == ""?@$GLOBALS["HTTP_POST_VARS"]["y08_quantlib"]:$this->y08_quantlib);
            $this->y08_cancel = ($this->y08_cancel == "f"?@$GLOBALS["HTTP_POST_VARS"]["y08_cancel"]:$this->y08_cancel);
            if ($this->y08_dataliberacaografica == "") {
                $this->y08_dataliberacaografica_dia = ($this->y08_dataliberacaografica_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y08_dataliberacaografica_dia"]:$this->y08_dataliberacaografica_dia);
                $this->y08_dataliberacaografica_mes = ($this->y08_dataliberacaografica_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y08_dataliberacaografica_mes"]:$this->y08_dataliberacaografica_mes);
                $this->y08_dataliberacaografica_ano = ($this->y08_dataliberacaografica_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y08_dataliberacaografica_ano"]:$this->y08_dataliberacaografica_ano);
                if ($this->y08_dataliberacaografica_dia != "") {
                    $this->y08_dataliberacaografica = $this->y08_dataliberacaografica_ano."-".$this->y08_dataliberacaografica_mes."-".$this->y08_dataliberacaografica_dia;
                }
            }
            if ($this->y08_datarecebimentocontribuinte == "") {
                $this->y08_datarecebimentocontribuinte_dia = ($this->y08_datarecebimentocontribuinte_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y08_datarecebimentocontribuinte_dia"]:$this->y08_datarecebimentocontribuinte_dia);
                $this->y08_datarecebimentocontribuinte_mes = ($this->y08_datarecebimentocontribuinte_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y08_datarecebimentocontribuinte_mes"]:$this->y08_datarecebimentocontribuinte_mes);
                $this->y08_datarecebimentocontribuinte_ano = ($this->y08_datarecebimentocontribuinte_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y08_datarecebimentocontribuinte_ano"]:$this->y08_datarecebimentocontribuinte_ano);
                if ($this->y08_datarecebimentocontribuinte_dia != "") {
                    $this->y08_datarecebimentocontribuinte = $this->y08_datarecebimentocontribuinte_ano."-".$this->y08_datarecebimentocontribuinte_mes."-".$this->y08_datarecebimentocontribuinte_dia;
                }
            }
            if ($this->y08_datalimitesolicitada == "") {
                $this->y08_datalimitesolicitada_dia = ($this->y08_datalimitesolicitada_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y08_datalimitesolicitada_dia"]:$this->y08_datalimitesolicitada_dia);
                $this->y08_datalimitesolicitada_mes = ($this->y08_datalimitesolicitada_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y08_datalimitesolicitada_mes"]:$this->y08_datalimitesolicitada_mes);
                $this->y08_datalimitesolicitada_ano = ($this->y08_datalimitesolicitada_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y08_datalimitesolicitada_ano"]:$this->y08_datalimitesolicitada_ano);
                if ($this->y08_datalimitesolicitada_dia != "") {
                    $this->y08_datalimitesolicitada = $this->y08_datalimitesolicitada_ano."-".$this->y08_datalimitesolicitada_mes."-".$this->y08_datalimitesolicitada_dia;
                }
            }
            if ($this->y08_datalimiteliberada == "") {
                $this->y08_datalimiteliberada_dia = ($this->y08_datalimiteliberada_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y08_datalimiteliberada_dia"]:$this->y08_datalimiteliberada_dia);
                $this->y08_datalimiteliberada_mes = ($this->y08_datalimiteliberada_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y08_datalimiteliberada_mes"]:$this->y08_datalimiteliberada_mes);
                $this->y08_datalimiteliberada_ano = ($this->y08_datalimiteliberada_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y08_datalimiteliberada_ano"]:$this->y08_datalimiteliberada_ano);
                if ($this->y08_datalimiteliberada_dia != "") {
                    $this->y08_datalimiteliberada = $this->y08_datalimiteliberada_ano."-".$this->y08_datalimiteliberada_mes."-".$this->y08_datalimiteliberada_dia;
                }
            }
        } else {
            $this->y08_codigo = ($this->y08_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["y08_codigo"]:$this->y08_codigo);
        }
    }
   // funcao para inclusao
    public function incluir($y08_codigo = null)
    {
          $this->atualizacampos();
        if ($this->y08_nota == null) {
            $this->erro_sql = " Campo Nota nao Informado.";
            $this->erro_campo = "y08_nota";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y08_inscr == null) {
            $this->erro_sql = " Campo Inscrição nao Informado.";
            $this->erro_campo = "y08_inscr";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y08_dtlanc == null) {
            $this->erro_sql = " Campo Data de Lançamento nao Informado.";
            $this->erro_campo = "y08_dtlanc_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y08_notain == null) {
            $this->erro_sql = " Campo Nota Inicial nao Informado.";
            $this->erro_campo = "y08_notain";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y08_notafi == null) {
            $this->erro_sql = " Campo Nota Final nao Informado.";
            $this->erro_campo = "y08_notafi";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y08_numcgm == null) {
            $this->y08_numcgm = "null";
        }
        if ($this->y08_login == null) {
            $this->erro_sql = " Campo Login nao Informado.";
            $this->erro_campo = "y08_login";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y08_quantsol == null) {
            $this->erro_sql = " Campo Quantidade Solicitada nao Informado.";
            $this->erro_campo = "y08_quantsol";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y08_quantlib == null) {
            $this->erro_sql = " Campo Quantidade Liberada nao Informado.";
            $this->erro_campo = "y08_quantlib";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y08_cancel == null) {
            $this->erro_sql = " Campo Cancelada nao Informado.";
            $this->erro_campo = "y08_cancel";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y08_dataliberacaografica == null) {
            $this->y08_dataliberacaografica = "null";
        }
        if ($this->y08_datarecebimentocontribuinte == null) {
            $this->y08_datarecebimentocontribuinte = "null";
        }
        if ($this->y08_datalimitesolicitada == null) {
            $this->y08_datalimitesolicitada = "null";
        }
        if ($this->y08_datalimiteliberada == null) {
            $this->y08_datalimiteliberada = "null";
        }
        if (($this->y08_codigo == null) || ($this->y08_codigo == "")) {
            $this->erro_sql = " Campo y08_codigo nao declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into fiscalizacao.fis_aidof(
                                       y08_nota
                                      ,y08_inscr
                                      ,y08_dtlanc
                                      ,y08_notain
                                      ,y08_notafi
                                      ,y08_numcgm
                                      ,y08_obs
                                      ,y08_login
                                      ,y08_quantsol
                                      ,y08_quantlib
                                      ,y08_cancel
                                      ,y08_dataliberacaografica
                                      ,y08_datarecebimentocontribuinte
                                      ,y08_datalimitesolicitada
                                      ,y08_datalimiteliberada
                       )
                values (
                                $this->y08_nota
                               ,$this->y08_inscr
                               ,".($this->y08_dtlanc == "null" || $this->y08_dtlanc == ""?"null":"'".$this->y08_dtlanc."'")."
                               ,$this->y08_notain
                               ,$this->y08_notafi
                               ,$this->y08_numcgm
                               ,'$this->y08_obs'
                               ,$this->y08_login
                               ,$this->y08_quantsol
                               ,$this->y08_quantlib
                               ,'$this->y08_cancel'
                               ,".($this->y08_dataliberacaografica == "null" || $this->y08_dataliberacaografica == ""?"null":"'".$this->y08_dataliberacaografica."'")."
                               ,".($this->y08_datarecebimentocontribuinte == "null" || $this->y08_datarecebimentocontribuinte == ""?"null":"'".$this->y08_datarecebimentocontribuinte."'")."
                               ,".($this->y08_datalimitesolicitada == "null" || $this->y08_datalimitesolicitada == ""?"null":"'".$this->y08_datalimitesolicitada."'")."
                               ,".($this->y08_datalimiteliberada == "null" || $this->y08_datalimiteliberada == ""?"null":"'".$this->y08_datalimiteliberada."'")."
                      ) returning y08_codigo ";
        $result = db_query($sql);
        $this->y08_codigo = db_utils::fieldsmemory($result, 0)->y08_codigo;
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Autorização Fiscal ($this->y08_codigo) nao Incluída. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Autorização Fiscal já Cadastrada";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "Autorização Fiscal ($this->y08_codigo) não Incluída. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->y08_codigo;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);

        return true;
    }
   // funcao para alteracao
    public function alterar($y08_codigo = null)
    {
          $this->atualizacampos();
         $sql = " update fiscalizacao.fis_aidof set ";
         $virgula = "";
        if (trim($this->y08_nota)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y08_nota"])) {
            $sql  .= $virgula." y08_nota = $this->y08_nota ";
            $virgula = ",";
            if (trim($this->y08_nota) == null) {
                $this->erro_sql = " Campo Nota nao Informado.";
                $this->erro_campo = "y08_nota";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y08_inscr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y08_inscr"])) {
            $sql  .= $virgula." y08_inscr = $this->y08_inscr ";
            $virgula = ",";
            if (trim($this->y08_inscr) == null) {
                $this->erro_sql = " Campo Inscrição nao Informado.";
                $this->erro_campo = "y08_inscr";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y08_dtlanc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y08_dtlanc_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y08_dtlanc_dia"] !="")) {
            $sql  .= $virgula." y08_dtlanc = '$this->y08_dtlanc' ";
            $virgula = ",";
            if (trim($this->y08_dtlanc) == null) {
                $this->erro_sql = " Campo Data de Lançamento nao Informado.";
                $this->erro_campo = "y08_dtlanc_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y08_dtlanc_dia"])) {
                $sql  .= $virgula." y08_dtlanc = null ";
                $virgula = ",";
                if (trim($this->y08_dtlanc) == null) {
                    $this->erro_sql = " Campo Data de Lançamento nao Informado.";
                    $this->erro_campo = "y08_dtlanc_dia";
                    $this->erro_banco = "";
                    $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        if (trim($this->y08_notain)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y08_notain"])) {
            $sql  .= $virgula." y08_notain = $this->y08_notain ";
            $virgula = ",";
            if (trim($this->y08_notain) == null) {
                $this->erro_sql = " Campo Nota Inicial nao Informado.";
                $this->erro_campo = "y08_notain";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y08_notafi)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y08_notafi"])) {
            $sql  .= $virgula." y08_notafi = $this->y08_notafi ";
            $virgula = ",";
            if (trim($this->y08_notafi) == null) {
                $this->erro_sql = " Campo Nota Final nao Informado.";
                $this->erro_campo = "y08_notafi";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y08_numcgm)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y08_numcgm"])) {
            if (trim($this->y08_numcgm)=="" && isset($GLOBALS["HTTP_POST_VARS"]["y08_numcgm"])) {
                $this->y08_numcgm = "0" ;
            }
            $sql  .= $virgula." y08_numcgm = $this->y08_numcgm ";
            $virgula = ",";
        }
        if (trim($this->y08_obs)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y08_obs"])) {
            $sql  .= $virgula." y08_obs = '$this->y08_obs' ";
            $virgula = ",";
        }
        if (trim($this->y08_login)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y08_login"])) {
            $sql  .= $virgula." y08_login = $this->y08_login ";
            $virgula = ",";
            if (trim($this->y08_login) == null) {
                $this->erro_sql = " Campo Login nao Informado.";
                $this->erro_campo = "y08_login";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y08_quantsol)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y08_quantsol"])) {
            $sql  .= $virgula." y08_quantsol = $this->y08_quantsol ";
            $virgula = ",";
            if (trim($this->y08_quantsol) == null) {
                $this->erro_sql = " Campo Quantidade Solicitada nao Informado.";
                $this->erro_campo = "y08_quantsol";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y08_quantlib)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y08_quantlib"])) {
            $sql  .= $virgula." y08_quantlib = $this->y08_quantlib ";
            $virgula = ",";
            if (trim($this->y08_quantlib) == null) {
                $this->erro_sql = " Campo Quantidade Liberada nao Informado.";
                $this->erro_campo = "y08_quantlib";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y08_cancel)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y08_cancel"])) {
            $sql  .= $virgula." y08_cancel = '$this->y08_cancel' ";
            $virgula = ",";
            if (trim($this->y08_cancel) == null) {
                $this->erro_sql = " Campo Cancelada nao Informado.";
                $this->erro_campo = "y08_cancel";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y08_dataliberacaografica)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y08_dataliberacaografica_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y08_dataliberacaografica_dia"] !="")) {
            $sql  .= $virgula." y08_dataliberacaografica = '$this->y08_dataliberacaografica' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y08_dataliberacaografica_dia"])) {
                $sql  .= $virgula." y08_dataliberacaografica = null ";
                $virgula = ",";
            }
        }
        if (trim($this->y08_datarecebimentocontribuinte)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y08_datarecebimentocontribuinte_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y08_datarecebimentocontribuinte_dia"] !="")) {
            $sql  .= $virgula." y08_datarecebimentocontribuinte = '$this->y08_datarecebimentocontribuinte' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y08_datarecebimentocontribuinte_dia"])) {
                $sql  .= $virgula." y08_datarecebimentocontribuinte = null ";
                $virgula = ",";
            }
        }
        if (trim($this->y08_datalimitesolicitada)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y08_datalimitesolicitada_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y08_datalimitesolicitada_dia"] !="")) {
            $sql  .= $virgula." y08_datalimitesolicitada = '$this->y08_datalimitesolicitada' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y08_datalimitesolicitada_dia"])) {
                $sql  .= $virgula." y08_datalimitesolicitada = null ";
                $virgula = ",";
            }
        }
        if (trim($this->y08_datalimiteliberada)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y08_datalimiteliberada_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y08_datalimiteliberada_dia"] !="")) {
            $sql  .= $virgula." y08_datalimiteliberada = '$this->y08_datalimiteliberada' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y08_datalimiteliberada_dia"])) {
                $sql  .= $virgula." y08_datalimiteliberada = null ";
                $virgula = ",";
            }
        }
        $sql .= " where ";
        if ($y08_codigo!=null) {
            $sql .= " y08_codigo = $this->y08_codigo";
        }

        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Autorização Fiscal nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->y08_codigo;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "Autorização Fiscal nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->y08_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->y08_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao para exclusao
    public function excluir($y08_codigo = null, $dbwhere = null)
    {

         $sql = " delete from fiscalizacao.fis_aidof where ";
         $sql2 = "";
        if ($dbwhere==null || $dbwhere =="") {
            if ($y08_codigo != "") {
                if ($sql2!="") {
                    $sql2 .= " and ";
                }
                $sql2 .= " y08_codigo = $y08_codigo ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Autorização Fiscal nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y08_codigo;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Autorização Fiscal nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y08_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$y08_codigo;
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
            $this->erro_sql   = "Record Vazio na Tabela:aidof";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
   // funcao do sql
    public function sql_query($y08_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_aidof ";
        $sql .= "      inner join issbase  on  issbase.q02_inscr = fis_aidof.y08_inscr";
        $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = fis_aidof.y08_login";
        $sql .= "      inner join notasiss  on  notasiss.q09_codigo = fis_aidof.y08_nota";
        $sql .= "      left  join fiscalizacao.fis_graficas  on  fis_graficas.y20_grafica = fis_aidof.y08_numcgm";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = issbase.q02_numcgm";
        $sql .= "      inner join gruponotaiss  on  gruponotaiss.q139_sequencial = notasiss.q09_gruponotaiss";
        $sql .= "      inner join cgm  as a on   a.z01_numcgm = fis_graficas.y20_grafica";
        $sql .= "      inner join db_usuarios  as b on   b.id_usuario = fis_graficas.y20_id_usuario";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y08_codigo!=null) {
                $sql2 .= " where fis_aidof.y08_codigo = $y08_codigo ";
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
    public function sql_query_file($y08_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_aidof ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y08_codigo!=null) {
                $sql2 .= " where fis_aidof.y08_codigo = $y08_codigo ";
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
    public function sql_query_aidof($y08_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_aidof ";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = fis_aidof.y08_numcgm";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y08_codigo!=null) {
                $sql2 .= " where fis_aidof.y08_codigo = $y08_codigo ";
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
