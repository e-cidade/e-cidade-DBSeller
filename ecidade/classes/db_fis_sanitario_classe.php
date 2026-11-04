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
//CLASSE DA ENTIDADE sanitario
class cl_fis_sanitario
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
    public $y80_codsani = 0;
    public $y80_numbloco = null;
    public $y80_numcgm = 0;
    public $y80_data_dia = null;
    public $y80_data_mes = null;
    public $y80_data_ano = null;
    public $y80_data = null;
    public $y80_obs = null;
    public $y80_texto = null;
    public $y80_area = 0;
    public $y80_codrua = 0;
    public $y80_codbairro = 0;
    public $y80_numero = 0;
    public $y80_compl = null;
    public $y80_dtbaixa_dia = null;
    public $y80_dtbaixa_mes = null;
    public $y80_dtbaixa_ano = null;
    public $y80_dtbaixa = null;
    public $y80_depto = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 y80_codsani = int4 = Alvará Sanitário
                 y80_numbloco = varchar(20) = Código próprio
                 y80_numcgm = int4 = CGM da Empresa
                 y80_data = date = Data da Liberação
                 y80_obs = text = Observação do Lançamento
                 y80_texto = text = Texto
                 y80_area = float8 = Área
                 y80_codrua = int4 = Rua
                 y80_codbairro = int4 = Bairro
                 y80_numero = int4 = Número
                 y80_compl = varchar(20) = Complemento
                 y80_dtbaixa = date = Data da Baixa
                 y80_depto = int4 = Depart.
                 ";
    //funcao construtor da classe
    public function __construct()
    {
      //classes dos rotulos dos campos
        $this->rotulo = new rotulo("fis_sanitario");
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
            $this->y80_codsani = ($this->y80_codsani == ""?@$GLOBALS["HTTP_POST_VARS"]["y80_codsani"]:$this->y80_codsani);
            $this->y80_numbloco = ($this->y80_numbloco == ""?@$GLOBALS["HTTP_POST_VARS"]["y80_numbloco"]:$this->y80_numbloco);
            $this->y80_numcgm = ($this->y80_numcgm == ""?@$GLOBALS["HTTP_POST_VARS"]["y80_numcgm"]:$this->y80_numcgm);
            if ($this->y80_data == "") {
                $this->y80_data_dia = ($this->y80_data_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y80_data_dia"]:$this->y80_data_dia);
                $this->y80_data_mes = ($this->y80_data_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y80_data_mes"]:$this->y80_data_mes);
                $this->y80_data_ano = ($this->y80_data_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y80_data_ano"]:$this->y80_data_ano);
                if ($this->y80_data_dia != "") {
                     $this->y80_data = $this->y80_data_ano."-".$this->y80_data_mes."-".$this->y80_data_dia;
                }
            }
            $this->y80_obs = ($this->y80_obs == ""?@$GLOBALS["HTTP_POST_VARS"]["y80_obs"]:$this->y80_obs);
            $this->y80_texto = ($this->y80_texto == ""?@$GLOBALS["HTTP_POST_VARS"]["y80_texto"]:$this->y80_texto);
            $this->y80_area = ($this->y80_area == ""?@$GLOBALS["HTTP_POST_VARS"]["y80_area"]:$this->y80_area);
            $this->y80_codrua = ($this->y80_codrua == ""?@$GLOBALS["HTTP_POST_VARS"]["y80_codrua"]:$this->y80_codrua);
            $this->y80_codbairro = ($this->y80_codbairro == ""?@$GLOBALS["HTTP_POST_VARS"]["y80_codbairro"]:$this->y80_codbairro);
            $this->y80_numero = ($this->y80_numero == ""?@$GLOBALS["HTTP_POST_VARS"]["y80_numero"]:$this->y80_numero);
            $this->y80_compl = ($this->y80_compl == ""?@$GLOBALS["HTTP_POST_VARS"]["y80_compl"]:$this->y80_compl);
            if ($this->y80_dtbaixa == "") {
                $this->y80_dtbaixa_dia = ($this->y80_dtbaixa_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y80_dtbaixa_dia"]:$this->y80_dtbaixa_dia);
                $this->y80_dtbaixa_mes = ($this->y80_dtbaixa_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y80_dtbaixa_mes"]:$this->y80_dtbaixa_mes);
                $this->y80_dtbaixa_ano = ($this->y80_dtbaixa_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y80_dtbaixa_ano"]:$this->y80_dtbaixa_ano);
                if ($this->y80_dtbaixa_dia != "") {
                    $this->y80_dtbaixa = $this->y80_dtbaixa_ano."-".$this->y80_dtbaixa_mes."-".$this->y80_dtbaixa_dia;
                }
            }
            $this->y80_depto = ($this->y80_depto == ""?@$GLOBALS["HTTP_POST_VARS"]["y80_depto"]:$this->y80_depto);
        } else {
            $this->y80_codsani = ($this->y80_codsani == ""?@$GLOBALS["HTTP_POST_VARS"]["y80_codsani"]:$this->y80_codsani);
        }
    }
   // funcao para inclusao
    public function incluir($y80_codsani = null)
    {
        $this->atualizacampos();
        if ($this->y80_numcgm == null) {
            $this->erro_sql = " Campo CGM da Empresa nao Informado.";
            $this->erro_campo = "y80_numcgm";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y80_data == null) {
            $this->erro_sql = " Campo Data da Liberação nao Informado.";
            $this->erro_campo = "y80_data_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y80_area == null) {
            $this->y80_area = "0";
        }
        if ($this->y80_codrua == null) {
            $this->erro_sql = " Campo Rua nao Informado.";
            $this->erro_campo = "y80_codrua";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y80_codbairro == null) {
            $this->erro_sql = " Campo Bairro nao Informado.";
            $this->erro_campo = "y80_codbairro";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y80_numero == null) {
            $this->erro_sql = " Campo Número nao Informado.";
            $this->erro_campo = "y80_numero";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y80_dtbaixa == null) {
            $this->y80_dtbaixa = "null";
        }
        if ($this->y80_depto == null) {
            $this->erro_sql = " Campo Depart. nao Informado.";
            $this->erro_campo = "y80_depto";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into fiscalizacao.fis_sanitario(
                                       y80_numbloco
                                      ,y80_numcgm
                                      ,y80_data
                                      ,y80_obs
                                      ,y80_texto
                                      ,y80_area
                                      ,y80_codrua
                                      ,y80_codbairro
                                      ,y80_numero
                                      ,y80_compl
                                      ,y80_dtbaixa
                                      ,y80_depto
                       )
                values (
                                '$this->y80_numbloco'
                               ,$this->y80_numcgm
                               ,".($this->y80_data == "null" || $this->y80_data == ""?"null":"'".$this->y80_data."'")."
                               ,'$this->y80_obs'
                               ,'$this->y80_texto'
                               ,$this->y80_area
                               ,$this->y80_codrua
                               ,$this->y80_codbairro
                               ,$this->y80_numero
                               ,'$this->y80_compl'
                               ,".($this->y80_dtbaixa == "null" || $this->y80_dtbaixa == ""?"null":"'".$this->y80_dtbaixa."'")."
                               ,$this->y80_depto
                      ) returning y80_codsani ";
        $result = db_query($sql);
        $this->y80_codsani = db_utils::fieldsmemory($result, 0)->y80_codsani;

        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "sanitario ($this->y80_codsani) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "sanitario já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "sanitario ($this->y80_codsani) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->y80_codsani;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);


        return true;
    }
   // funcao para alteracao
    public function alterar($y80_codsani = null)
    {
        $this->atualizacampos();
        $sql = " update fiscalizacao.fis_sanitario set ";
        $virgula = "";
        if (trim($this->y80_numbloco)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y80_numbloco"])) {
            $sql  .= $virgula." y80_numbloco = '$this->y80_numbloco' ";
            $virgula = ",";
        }
        if (trim($this->y80_numcgm)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y80_numcgm"])) {
            $sql  .= $virgula." y80_numcgm = $this->y80_numcgm ";
            $virgula = ",";
            if (trim($this->y80_numcgm) == null) {
                $this->erro_sql = " Campo CGM da Empresa nao Informado.";
                $this->erro_campo = "y80_numcgm";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y80_data)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y80_data_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y80_data_dia"] !="")) {
            $sql  .= $virgula." y80_data = '$this->y80_data' ";
            $virgula = ",";
            if (trim($this->y80_data) == null) {
                $this->erro_sql = " Campo Data da Liberação nao Informado.";
                $this->erro_campo = "y80_data_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y80_data_dia"])) {
                $sql  .= $virgula." y80_data = null ";
                $virgula = ",";
                if (trim($this->y80_data) == null) {
                    $this->erro_sql = " Campo Data da Liberação nao Informado.";
                    $this->erro_campo = "y80_data_dia";
                    $this->erro_banco = "";
                    $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        if (trim($this->y80_obs)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y80_obs"])) {
            $sql  .= $virgula." y80_obs = '$this->y80_obs' ";
            $virgula = ",";
        }
        if (trim($this->y80_texto)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y80_texto"])) {
            $sql  .= $virgula." y80_texto = '$this->y80_texto' ";
            $virgula = ",";
        }
        if (trim($this->y80_area)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y80_area"])) {
            if (trim($this->y80_area)=="" && isset($GLOBALS["HTTP_POST_VARS"]["y80_area"])) {
                $this->y80_area = "0" ;
            }
            $sql  .= $virgula." y80_area = $this->y80_area ";
            $virgula = ",";
        }
        if (trim($this->y80_codrua)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y80_codrua"])) {
            $sql  .= $virgula." y80_codrua = $this->y80_codrua ";
            $virgula = ",";
            if (trim($this->y80_codrua) == null) {
                $this->erro_sql = " Campo Rua nao Informado.";
                $this->erro_campo = "y80_codrua";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y80_codbairro)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y80_codbairro"])) {
            $sql  .= $virgula." y80_codbairro = $this->y80_codbairro ";
            $virgula = ",";
            if (trim($this->y80_codbairro) == null) {
                $this->erro_sql = " Campo Bairro nao Informado.";
                $this->erro_campo = "y80_codbairro";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y80_numero)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y80_numero"])) {
            $sql  .= $virgula." y80_numero = $this->y80_numero ";
            $virgula = ",";
            if (trim($this->y80_numero) == null) {
                $this->erro_sql = " Campo Número nao Informado.";
                $this->erro_campo = "y80_numero";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y80_compl)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y80_compl"])) {
            $sql  .= $virgula." y80_compl = '$this->y80_compl' ";
            $virgula = ",";
        }
        if (trim($this->y80_dtbaixa)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y80_dtbaixa_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y80_dtbaixa_dia"] !="")) {
            $sql  .= $virgula." y80_dtbaixa = '$this->y80_dtbaixa' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y80_dtbaixa_dia"])) {
                $sql  .= $virgula." y80_dtbaixa = null ";
                $virgula = ",";
            }
        }
        if (trim($this->y80_depto)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y80_depto"])) {
            $sql  .= $virgula." y80_depto = $this->y80_depto ";
            $virgula = ",";
            if (trim($this->y80_depto) == null) {
                $this->erro_sql = " Campo Depart. nao Informado.";
                $this->erro_campo = "y80_depto";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($y80_codsani!=null) {
            $sql .= " y80_codsani = $this->y80_codsani";
        }


        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "sanitario nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->y80_codsani;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "sanitario nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->y80_codsani;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->y80_codsani;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao para exclusao
    public function excluir($y80_codsani = null, $dbwhere = null)
    {
        $sql = " delete from fiscalizacao.fis_sanitario where ";
        $sql2 = "";
        if ($dbwhere==null || $dbwhere =="") {
            if ($y80_codsani != "") {
                if ($sql2!="") {
                    $sql2 .= " and ";
                }
                $sql2 .= " y80_codsani = $y80_codsani ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "sanitario nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y80_codsani;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "sanitario nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y80_codsani;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$y80_codsani;
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
            $this->erro_sql   = "Record Vazio na Tabela:sanitario";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
    public function incluir_sem_seq($y80_codsani = null)
    {
        $this->atualizacampos();
        if ($this->y80_numcgm == null) {
            $this->erro_sql = " Campo CGM da Empresa nao Informado.";
            $this->erro_campo = "y80_numcgm";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y80_data == null) {
            $this->erro_sql = " Campo Data da Liberação do Alvará nao Informado.";
            $this->erro_campo = "y80_data_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y80_area == null) {
            $this->y80_area = "0";
        }
        if ($this->y80_codrua == null) {
            $this->erro_sql = " Campo Rua nao Informado.";
            $this->erro_campo = "y80_codrua";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y80_codbairro == null) {
            $this->erro_sql = " Campo Bairro nao Informado.";
            $this->erro_campo = "y80_codbairro";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y80_numero == null) {
            $this->erro_sql = " Campo Número nao Informado.";
            $this->erro_campo = "y80_numero";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y80_depto == null) {
            $this->erro_sql = " Campo Depart. nao Informado.";
            $this->erro_campo = "y80_depto";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y80_dtbaixa == null) {
            $this->y80_dtbaixa = "null";
        }

        $sql = "insert into fiscalizacao.fis_sanitario(
                                       y80_numbloco
                                      ,y80_numcgm
                                      ,y80_data
                                      ,y80_obs
                                      ,y80_area
                                      ,y80_codrua
                                      ,y80_codbairro
                                      ,y80_numero
                                      ,y80_compl
                                      ,y80_dtbaixa
						 				,y80_depto
                       )
                values (
                                '$this->y80_numbloco'
                               ,$this->y80_numcgm
                               ,".($this->y80_data == "null" || $this->y80_data == ""?"null":"'".$this->y80_data."'")."
                               ,'$this->y80_obs'
                               ,$this->y80_area
                               ,$this->y80_codrua
                               ,$this->y80_codbairro
                               ,$this->y80_numero
                               ,'$this->y80_compl'
                               ,".($this->y80_dtbaixa == "null" || $this->y80_dtbaixa == ""?"null":"'".$this->y80_dtbaixa."'")."
                                                 ,$this->y80_depto
                      ) returning y80_codsani ";
        $result = @db_query($sql);
        $this->y80_codsani = db_utils::fieldsmemory($result, 0)->y80_codsani;

        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "sanitario ($this->y80_codsani) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "sanitario já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "sanitario ($this->y80_codsani) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->y80_codsani;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);

        return true;
    }
    public function sql_query($y80_codsani = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_sanitario ";
        $sql .= "      inner join bairro  on  bairro.j13_codi = fis_sanitario.y80_codbairro";
        $sql .= "      inner join ruas  on  ruas.j14_codigo = fis_sanitario.y80_codrua";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = fis_sanitario.y80_numcgm";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_sanitario.y80_depto";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y80_codsani!=null) {
                $sql2 .= " where fis_sanitario.y80_codsani = $y80_codsani ";
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
    public function sql_query_file($y80_codsani = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_sanitario ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y80_codsani!=null) {
                $sql2 .= " where fis_sanitario.y80_codsani = $y80_codsani ";
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
    public function sql_query_ativ($y80_codsani = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_sanitario ";
        $sql .= "      inner join ruas  on  ruas.j14_codigo = fis_sanitario.y80_codrua";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = fis_sanitario.y80_numcgm";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_sanitario.y80_depto";
        $sql .= "      inner join fiscalizacao.fis_saniatividade on y80_codsani = y83_codsani";
        $sql .= "      left  join fiscalizacao.fis_sanitarioinscr on  y18_codsani        = y80_codsani";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y80_codsani!=null) {
                $sql2 .= " where fis_sanitario.y80_codsani = $y80_codsani ";
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
    public function sql_querybaix($y80_codsani = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_sanitario ";
        $sql .= "      inner join bairro  on  bairro.j13_codi = fis_sanitario.y80_codbairro";
        $sql .= "      inner join ruas  on  ruas.j14_codigo = fis_sanitario.y80_codrua";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = fis_sanitario.y80_numcgm";
        $sql .= "      left join fiscalizacao.fis_sanibaixa  on  fis_sanibaixa.y81_codsani = fis_sanitario.y80_codsani";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y80_codsani!=null) {
                $sql2 .= " where fis_sanitario.y80_codsani = $y80_codsani ";
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
    public function sql_querysani($y80_codsani = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_sanitario";
        $sql .= "      inner join bairro  on  bairro.j13_codi = fis_sanitario.y80_codbairro";
        $sql .= "      inner join ruas  on  ruas.j14_codigo = fis_sanitario.y80_codrua";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = fis_sanitario.y80_numcgm";
        $sql .= "      inner join fiscalizacao.fis_saniatividade  on  fis_saniatividade.y83_codsani = fis_sanitario.y80_codsani";
        $sql .= "      inner join ativid on fis_saniatividade.y83_ativ = ativid.q03_ativ";
        $sql .= "      left join fiscalizacao.fis_sanibaixa  on  fis_sanibaixa.y81_codsani = fis_sanitario.y80_codsani";
        $sql .= "      left join fiscalizacao.fis_sanibaixaproc  on  fis_sanibaixaproc.y82_codsani = fis_sanitario.y80_codsani";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y80_codsani!=null) {
                $sql2 .= " where fis_sanitario.y80_codsani = $y80_codsani ";
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

    public function sql_query_sem_ativ($y80_codsani = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_sanitario ";
        $sql .= "      inner join ruas           on  ruas.j14_codigo    = fis_sanitario.y80_codrua";
        $sql .= "      inner join cgm            on  cgm.z01_numcgm     = fis_sanitario.y80_numcgm";
        $sql .= "      inner join db_depart      on  db_depart.coddepto = fis_sanitario.y80_depto";
        $sql .= "      left  join fiscalizacao.fis_saniatividade  on  y80_codsani        = y83_codsani";
        $sql .= "      left  join fiscalizacao.fis_sanitarioinscr on  y18_codsani        = y80_codsani";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y80_codsani!=null) {
                $sql2 .= " where fis_sanitario.y80_codsani = $y80_codsani ";
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
