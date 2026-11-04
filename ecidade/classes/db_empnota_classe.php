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

class cl_empnota
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
    public $e69_codnota = 0;
    public $e69_numero = null;
    public $e69_numemp = 0;
    public $e69_id_usuario = 0;
    public $e69_dtnota_dia = null;
    public $e69_dtnota_mes = null;
    public $e69_dtnota_ano = null;
    public $e69_dtnota = null;
    public $e69_dtrecebe_dia = null;
    public $e69_dtrecebe_mes = null;
    public $e69_dtrecebe_ano = null;
    public $e69_dtrecebe = null;
    public $e69_anousu = 0;
    public $e69_tipodocumentosfiscal = 0;
    public $e69_dtservidor_dia = null;
    public $e69_dtservidor_mes = null;
    public $e69_dtservidor_ano = null;
    public $e69_dtservidor = null;
    public $e69_dtinclusao_dia = null;
    public $e69_dtinclusao_mes = null;
    public $e69_dtinclusao_ano = null;
    public $e69_dtinclusao = null;
    public $e69_dtvencimento_dia = null;
    public $e69_dtvencimento_mes = null;
    public $e69_dtvencimento_ano = null;
    public $e69_dtvencimento = null;
    public $e69_localrecebimento = null;
    public $e69_outrosdados = null;
    public $e69_serienota = null;

    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 e69_codnota = int4 = Nota
                 e69_numero = varchar(20) = Número da NF
                 e69_numemp = int4 = Empenho
                 e69_id_usuario = int4 = Cod. Usuário
                 e69_dtnota = date = Data nota
                 e69_dtrecebe = date = Data do Recebimento
                 e69_anousu = int4 = Ano da Nota
                 e69_tipodocumentosfiscal = int4 = Tipo de Documento Fiscal
                 e69_dtservidor = date = Data do Servidor
                 e69_dtinclusao = date = Data da Inclusão
                 e69_dtvencimento = date = Data de Vencimento
                 e69_localrecebimento = text = Local de Recebimento
                 e69_outrosdados = text = Outros Dados
                 e69_serienota = varchar(15) = Série da NF
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("empnota");
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
    }

    public function erro($mostra, $retorna)
    {
     if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null)){
        echo "<script>alert(\"".$this->erro_msg."\")</script>";
        if($retorna==true){
           echo "<script>location.href='".$this->pagina_retorno."'</script>";
        }
     }
   }

    public function atualizacampos($exclusao = false)
    {
        if ($exclusao == false) {
            $this->e69_codnota = ($this->e69_codnota == "" ? @$GLOBALS["HTTP_POST_VARS"]["e69_codnota"] : $this->e69_codnota);
            $this->e69_numero = ($this->e69_numero == "" ? @$GLOBALS["HTTP_POST_VARS"]["e69_numero"] : $this->e69_numero);
            $this->e69_numemp = ($this->e69_numemp == "" ? @$GLOBALS["HTTP_POST_VARS"]["e69_numemp"] : $this->e69_numemp);
            $this->e69_id_usuario = ($this->e69_id_usuario == "" ? @$GLOBALS["HTTP_POST_VARS"]["e69_id_usuario"] : $this->e69_id_usuario);
            if ($this->e69_dtnota == "") {
                $this->e69_dtnota_dia = ($this->e69_dtnota_dia == "" ? @$GLOBALS["HTTP_POST_VARS"]["e69_dtnota_dia"] : $this->e69_dtnota_dia);
                $this->e69_dtnota_mes = ($this->e69_dtnota_mes == "" ? @$GLOBALS["HTTP_POST_VARS"]["e69_dtnota_mes"] : $this->e69_dtnota_mes);
                $this->e69_dtnota_ano = ($this->e69_dtnota_ano == "" ? @$GLOBALS["HTTP_POST_VARS"]["e69_dtnota_ano"] : $this->e69_dtnota_ano);
                if ($this->e69_dtnota_dia != "") {
                    $this->e69_dtnota = $this->e69_dtnota_ano . "-" . $this->e69_dtnota_mes . "-" . $this->e69_dtnota_dia;
                }
            }
            if ($this->e69_dtrecebe == "") {
                $this->e69_dtrecebe_dia = ($this->e69_dtrecebe_dia == "" ? @$GLOBALS["HTTP_POST_VARS"]["e69_dtrecebe_dia"] : $this->e69_dtrecebe_dia);
                $this->e69_dtrecebe_mes = ($this->e69_dtrecebe_mes == "" ? @$GLOBALS["HTTP_POST_VARS"]["e69_dtrecebe_mes"] : $this->e69_dtrecebe_mes);
                $this->e69_dtrecebe_ano = ($this->e69_dtrecebe_ano == "" ? @$GLOBALS["HTTP_POST_VARS"]["e69_dtrecebe_ano"] : $this->e69_dtrecebe_ano);
                if ($this->e69_dtrecebe_dia != "") {
                    $this->e69_dtrecebe = $this->e69_dtrecebe_ano . "-" . $this->e69_dtrecebe_mes . "-" . $this->e69_dtrecebe_dia;
                }
            }
            $this->e69_anousu = ($this->e69_anousu == "" ? @$GLOBALS["HTTP_POST_VARS"]["e69_anousu"] : $this->e69_anousu);
            $this->e69_tipodocumentosfiscal = ($this->e69_tipodocumentosfiscal == "" ? @$GLOBALS["HTTP_POST_VARS"]["e69_tipodocumentosfiscal"] : $this->e69_tipodocumentosfiscal);
            if ($this->e69_dtservidor == "") {
                $this->e69_dtservidor_dia = ($this->e69_dtservidor_dia == "" ? @$GLOBALS["HTTP_POST_VARS"]["e69_dtservidor_dia"] : $this->e69_dtservidor_dia);
                $this->e69_dtservidor_mes = ($this->e69_dtservidor_mes == "" ? @$GLOBALS["HTTP_POST_VARS"]["e69_dtservidor_mes"] : $this->e69_dtservidor_mes);
                $this->e69_dtservidor_ano = ($this->e69_dtservidor_ano == "" ? @$GLOBALS["HTTP_POST_VARS"]["e69_dtservidor_ano"] : $this->e69_dtservidor_ano);
                if ($this->e69_dtservidor_dia != "") {
                    $this->e69_dtservidor = $this->e69_dtservidor_ano . "-" . $this->e69_dtservidor_mes . "-" . $this->e69_dtservidor_dia;
                }
            }
            if ($this->e69_dtinclusao == "") {
                $this->e69_dtinclusao_dia = ($this->e69_dtinclusao_dia == "" ? @$GLOBALS["HTTP_POST_VARS"]["e69_dtinclusao_dia"] : $this->e69_dtinclusao_dia);
                $this->e69_dtinclusao_mes = ($this->e69_dtinclusao_mes == "" ? @$GLOBALS["HTTP_POST_VARS"]["e69_dtinclusao_mes"] : $this->e69_dtinclusao_mes);
                $this->e69_dtinclusao_ano = ($this->e69_dtinclusao_ano == "" ? @$GLOBALS["HTTP_POST_VARS"]["e69_dtinclusao_ano"] : $this->e69_dtinclusao_ano);
                if ($this->e69_dtinclusao_dia != "") {
                    $this->e69_dtinclusao = $this->e69_dtinclusao_ano . "-" . $this->e69_dtinclusao_mes . "-" . $this->e69_dtinclusao_dia;
                }
            }
            if ($this->e69_dtvencimento == "") {
                $this->e69_dtvencimento_dia = @$GLOBALS["HTTP_POST_VARS"]["e69_dtvencimento_dia"];
                $this->e69_dtvencimento_mes = @$GLOBALS["HTTP_POST_VARS"]["e69_dtvencimento_mes"];
                $this->e69_dtvencimento_ano = @$GLOBALS["HTTP_POST_VARS"]["e69_dtvencimento_ano"];
                if ($this->e69_dtvencimento_dia != "") {
                    $this->e69_dtvencimento = $this->e69_dtvencimento_ano . "-" . $this->e69_dtvencimento_mes . "-" . $this->e69_dtvencimento_dia;
                }
            }
            $this->e69_localrecebimento = ($this->e69_localrecebimento == "" ? @$GLOBALS["HTTP_POST_VARS"]["e69_localrecebimento"] : $this->e69_localrecebimento);
            $this->e69_outrosdados = ($this->e69_outrosdados == "" ? @$GLOBALS["HTTP_POST_VARS"]["e69_outrosdados"] : $this->e69_outrosdados);
            $this->e69_serienota = ($this->e69_serienota == "" ? @$GLOBALS["HTTP_POST_VARS"]["e69_serienota"] : $this->e69_serienota);
        } else {
            $this->e69_codnota = ($this->e69_codnota == "" ? @$GLOBALS["HTTP_POST_VARS"]["e69_codnota"] : $this->e69_codnota);
        }
    }

    public function incluir($e69_codnota)
    {
        $this->atualizacampos();
        if ($this->e69_numero == null) {
            $this->erro_sql = " Campo Número da NF não informado.";
            $this->erro_campo = "e69_numero";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e69_numemp == null) {
            $this->erro_sql = " Campo Empenho não informado.";
            $this->erro_campo = "e69_numemp";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e69_id_usuario == null) {
            $this->erro_sql = " Campo Cod. Usuário não informado.";
            $this->erro_campo = "e69_id_usuario";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e69_dtnota == null) {
            $this->erro_sql = " Campo Data nota não informado.";
            $this->erro_campo = "e69_dtnota_dia";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e69_dtrecebe == null) {
            $this->erro_sql = " Campo Data do Recebimento não informado.";
            $this->erro_campo = "e69_dtrecebe_dia";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e69_anousu == null) {
            $this->erro_sql = " Campo Ano da Nota não informado.";
            $this->erro_campo = "e69_anousu";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e69_tipodocumentosfiscal == null) {
            $this->erro_sql = " Campo Tipo de Documento Fiscal não informado.";
            $this->erro_campo = "e69_tipodocumentosfiscal";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e69_dtservidor == null) {
            $this->erro_sql = " Campo Data do Servidor não informado.";
            $this->erro_campo = "e69_dtservidor_dia";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e69_dtinclusao == null) {
            $this->erro_sql = " Campo Data da Inclusão não informado.";
            $this->erro_campo = "e69_dtinclusao_dia";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }

        if (empty($this->e69_outrosdados)) {
            $this->e69_outrosdados = "null";
        } else {
            $this->e69_outrosdados = "'{$this->e69_outrosdados}'";
        }
        if ($e69_codnota == "" || $e69_codnota == null) {
            $result = db_query("select nextval('empnota_e69_codnota_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: empnota_e69_codnota_seq do campo: e69_codnota";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->e69_codnota = pg_fetch_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from empnota_e69_codnota_seq");
            if (($result != false) && (pg_fetch_result($result, 0, 0) < $e69_codnota)) {
                $this->erro_sql = " Campo e69_codnota maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->e69_codnota = $e69_codnota;
            }
        }
        if (($this->e69_codnota == null) || ($this->e69_codnota == "")) {
            $this->erro_sql = " Campo e69_codnota não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into empnota(
                                       e69_codnota
                                      ,e69_numero
                                      ,e69_numemp
                                      ,e69_id_usuario
                                      ,e69_dtnota
                                      ,e69_dtrecebe
                                      ,e69_anousu
                                      ,e69_tipodocumentosfiscal
                                      ,e69_dtservidor
                                      ,e69_dtinclusao
                                      ,e69_dtvencimento
                                      ,e69_localrecebimento
                                      ,e69_outrosdados
                                      ,e69_serienota
                       )
                values (
                                $this->e69_codnota
                               ,'$this->e69_numero'
                               ,$this->e69_numemp
                               ,$this->e69_id_usuario
                               ," . ($this->e69_dtnota == "null" || $this->e69_dtnota == "" ? "null" : "'" . $this->e69_dtnota . "'") . "
                               ," . ($this->e69_dtrecebe == "null" || $this->e69_dtrecebe == "" ? "null" : "'" . $this->e69_dtrecebe . "'") . "
                               ,$this->e69_anousu
                               ,$this->e69_tipodocumentosfiscal
                               ," . ($this->e69_dtservidor == "null" || $this->e69_dtservidor == "" ? "null" : "'" . $this->e69_dtservidor . "'") . "
                               ," . ($this->e69_dtinclusao == "null" || $this->e69_dtinclusao == "" ? "null" : "'" . $this->e69_dtinclusao . "'") . "
                               ," . ($this->e69_dtvencimento == "null" || $this->e69_dtvencimento == "" ? "null" : "'" . $this->e69_dtvencimento . "'") . "
                               ,'$this->e69_localrecebimento'
                               , $this->e69_outrosdados
                               ,". ($this->e69_serienota == "null" || $this->e69_serienota == "" ? "null" : "'" . $this->e69_serienota. "'") . "
                      )";

        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "Notas empenho ($this->e69_codnota) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "Notas empenho já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "Notas empenho ($this->e69_codnota) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->e69_codnota;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->e69_codnota));
            if (($resaco != false) || ($this->numrows != 0)) {

                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_fetch_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                $resac = db_query("insert into db_acountkey values($acount,6044,'$this->e69_codnota','I')");
                $resac = db_query("insert into db_acount values($acount,971,6044,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'e69_codnota')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,971,6045,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'e69_numero')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,971,6047,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'e69_numemp')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,971,6048,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'e69_id_usuario')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,971,6046,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'e69_dtnota')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,971,6049,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'e69_dtrecebe')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,971,11062,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'e69_anousu')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,971,14669,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'e69_tipodocumentosfiscal')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,971,15655,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'e69_dtservidor')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,971,15656,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'e69_dtinclusao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,971,21595,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'e69_dtvencimento')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,971,21596,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'e69_localrecebimento')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,971,1014009,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'e69_outrosdados')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,971,1014135,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'e69_serienota')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            }
        }
        return true;
    }

    public function alterar($e69_codnota = null)
    {
        $this->atualizacampos();
        $sql = " update empnota set ";
        $virgula = "";
        if (trim($this->e69_codnota) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e69_codnota"])) {
            $sql .= $virgula . " e69_codnota = $this->e69_codnota ";
            $virgula = ",";
            if (trim($this->e69_codnota) == null) {
                $this->erro_sql = " Campo Seq da Nota não informado.";
                $this->erro_campo = "e69_codnota";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e69_numero) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e69_numero"])) {
            $sql .= $virgula . " e69_numero = '$this->e69_numero' ";
            $virgula = ",";
            if (trim($this->e69_numero) == null) {
                $this->erro_sql = " Campo Número da NF não informado.";
                $this->erro_campo = "e69_numero";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e69_numemp) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e69_numemp"])) {
            $sql .= $virgula . " e69_numemp = $this->e69_numemp ";
            $virgula = ",";
            if (trim($this->e69_numemp) == null) {
                $this->erro_sql = " Campo Empenho não informado.";
                $this->erro_campo = "e69_numemp";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e69_id_usuario) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e69_id_usuario"])) {
            $sql .= $virgula . " e69_id_usuario = $this->e69_id_usuario ";
            $virgula = ",";
            if (trim($this->e69_id_usuario) == null) {
                $this->erro_sql = " Campo Cod. Usuário não informado.";
                $this->erro_campo = "e69_id_usuario";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e69_dtnota) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e69_dtnota_dia"]) && ($GLOBALS["HTTP_POST_VARS"]["e69_dtnota_dia"] != "")) {
            $sql .= $virgula . " e69_dtnota = '$this->e69_dtnota' ";
            $virgula = ",";
            if (trim($this->e69_dtnota) == null) {
                $this->erro_sql = " Campo Data nota não informado.";
                $this->erro_campo = "e69_dtnota_dia";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["e69_dtnota_dia"])) {
                $sql .= $virgula . " e69_dtnota = null ";
                $virgula = ",";
                if (trim($this->e69_dtnota) == null) {
                    $this->erro_sql = " Campo Data nota não informado.";
                    $this->erro_campo = "e69_dtnota_dia";
                    $this->erro_banco = "";
                    $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                    $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        if (trim($this->e69_dtrecebe) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e69_dtrecebe_dia"]) && ($GLOBALS["HTTP_POST_VARS"]["e69_dtrecebe_dia"] != "")) {
            $sql .= $virgula . " e69_dtrecebe = '$this->e69_dtrecebe' ";
            $virgula = ",";
            if (trim($this->e69_dtrecebe) == null) {
                $this->erro_sql = " Campo Data do Recebimento não informado.";
                $this->erro_campo = "e69_dtrecebe_dia";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["e69_dtrecebe_dia"])) {
                $sql .= $virgula . " e69_dtrecebe = null ";
                $virgula = ",";
                if (trim($this->e69_dtrecebe) == null) {
                    $this->erro_sql = " Campo Data do Recebimento não informado.";
                    $this->erro_campo = "e69_dtrecebe_dia";
                    $this->erro_banco = "";
                    $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                    $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        if (trim($this->e69_anousu) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e69_anousu"])) {
            $sql .= $virgula . " e69_anousu = $this->e69_anousu ";
            $virgula = ",";
            if (trim($this->e69_anousu) == null) {
                $this->erro_sql = " Campo Ano da Nota não informado.";
                $this->erro_campo = "e69_anousu";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e69_tipodocumentosfiscal) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e69_tipodocumentosfiscal"])) {
            $sql .= $virgula . " e69_tipodocumentosfiscal = $this->e69_tipodocumentosfiscal ";
            $virgula = ",";
            if (trim($this->e69_tipodocumentosfiscal) == null) {
                $this->erro_sql = " Campo Tipo de Documento Fiscal não informado.";
                $this->erro_campo = "e69_tipodocumentosfiscal";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e69_dtservidor) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e69_dtservidor_dia"]) && ($GLOBALS["HTTP_POST_VARS"]["e69_dtservidor_dia"] != "")) {
            $sql .= $virgula . " e69_dtservidor = '$this->e69_dtservidor' ";
            $virgula = ",";
            if (trim($this->e69_dtservidor) == null) {
                $this->erro_sql = " Campo Data do Servidor não informado.";
                $this->erro_campo = "e69_dtservidor_dia";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["e69_dtservidor_dia"])) {
                $sql .= $virgula . " e69_dtservidor = null ";
                $virgula = ",";
                if (trim($this->e69_dtservidor) == null) {
                    $this->erro_sql = " Campo Data do Servidor não informado.";
                    $this->erro_campo = "e69_dtservidor_dia";
                    $this->erro_banco = "";
                    $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                    $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        if (trim($this->e69_dtinclusao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e69_dtinclusao_dia"]) && ($GLOBALS["HTTP_POST_VARS"]["e69_dtinclusao_dia"] != "")) {
            $sql .= $virgula . " e69_dtinclusao = '$this->e69_dtinclusao' ";
            $virgula = ",";
            if (trim($this->e69_dtinclusao) == null) {
                $this->erro_sql = " Campo Data da Inclusão não informado.";
                $this->erro_campo = "e69_dtinclusao_dia";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["e69_dtinclusao_dia"])) {
                $sql .= $virgula . " e69_dtinclusao = null ";
                $virgula = ",";
                if (trim($this->e69_dtinclusao) == null) {
                    $this->erro_sql = " Campo Data da Inclusão não informado.";
                    $this->erro_campo = "e69_dtinclusao_dia";
                    $this->erro_banco = "";
                    $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                    $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        if (trim($this->e69_dtvencimento) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e69_dtvencimento_dia"]) && ($GLOBALS["HTTP_POST_VARS"]["e69_dtvencimento_dia"] != "")) {
            $sql .= $virgula . " e69_dtvencimento = '$this->e69_dtvencimento' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["e69_dtvencimento_dia"])) {
                $sql .= $virgula . " e69_dtvencimento = null ";
                $virgula = ",";
            }
        }
        if (trim($this->e69_localrecebimento) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e69_localrecebimento"])) {
            $sql .= $virgula . " e69_localrecebimento = '$this->e69_localrecebimento' ";
            $virgula = ",";
        }

        if (empty($this->e69_outrosdados)) {
            $sql .= $virgula . " e69_outrosdados = null";
        } else {
            $sql .= $virgula . " e69_outrosdados = '{$this->e69_outrosdados}'";
        }

        if (empty($this->e69_serienota)) {
            $sql .= $virgula . " e69_serienota = null";
        } else {
            $sql .= $virgula . " e69_serienota = '{$this->e69_serienota}'";
        }

        $sql .= " where ";
        if ($e69_codnota != null) {
            $sql .= " e69_codnota = $this->e69_codnota";
        }

        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->e69_codnota));
            if ($this->numrows > 0) {

                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_fetch_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,6044,'$this->e69_codnota','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e69_codnota"]) || $this->e69_codnota != "")
                        $resac = db_query("insert into db_acount values($acount,971,6044,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'e69_codnota')) . "','$this->e69_codnota'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e69_numero"]) || $this->e69_numero != "")
                        $resac = db_query("insert into db_acount values($acount,971,6045,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'e69_numero')) . "','$this->e69_numero'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e69_numemp"]) || $this->e69_numemp != "")
                        $resac = db_query("insert into db_acount values($acount,971,6047,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'e69_numemp')) . "','$this->e69_numemp'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e69_id_usuario"]) || $this->e69_id_usuario != "")
                        $resac = db_query("insert into db_acount values($acount,971,6048,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'e69_id_usuario')) . "','$this->e69_id_usuario'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e69_dtnota"]) || $this->e69_dtnota != "")
                        $resac = db_query("insert into db_acount values($acount,971,6046,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'e69_dtnota')) . "','$this->e69_dtnota'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e69_dtrecebe"]) || $this->e69_dtrecebe != "")
                        $resac = db_query("insert into db_acount values($acount,971,6049,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'e69_dtrecebe')) . "','$this->e69_dtrecebe'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e69_anousu"]) || $this->e69_anousu != "")
                        $resac = db_query("insert into db_acount values($acount,971,11062,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'e69_anousu')) . "','$this->e69_anousu'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e69_tipodocumentosfiscal"]) || $this->e69_tipodocumentosfiscal != "")
                        $resac = db_query("insert into db_acount values($acount,971,14669,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'e69_tipodocumentosfiscal')) . "','$this->e69_tipodocumentosfiscal'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e69_dtservidor"]) || $this->e69_dtservidor != "")
                        $resac = db_query("insert into db_acount values($acount,971,15655,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'e69_dtservidor')) . "','$this->e69_dtservidor'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e69_dtinclusao"]) || $this->e69_dtinclusao != "")
                        $resac = db_query("insert into db_acount values($acount,971,15656,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'e69_dtinclusao')) . "','$this->e69_dtinclusao'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e69_dtvencimento"]) || $this->e69_dtvencimento != "")
                        $resac = db_query("insert into db_acount values($acount,971,21595,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'e69_dtvencimento')) . "','$this->e69_dtvencimento'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e69_localrecebimento"]) || $this->e69_localrecebimento != "")
                        $resac = db_query("insert into db_acount values($acount,971,21596,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'e69_localrecebimento')) . "','$this->e69_localrecebimento'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e69_outrosdados"]) || $this->e69_outrosdados != "")
                        $resac = db_query("insert into db_acount values($acount,971,1014009,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'e69_outrosdados')) . "','$this->e69_outrosdados'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["e69_serienota"]) || $this->e69_serienota != "")
                        $resac = db_query("insert into db_acount values($acount,971,1014135,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'e69_serienota')) . "','$this->e69_serienota'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }

        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Notas empenho não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->e69_codnota;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Notas empenho não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->e69_codnota;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $this->e69_codnota;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($e69_codnota = null, $dbwhere = null)
    {
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            if (empty($dbwhere)) {

                $resaco = $this->sql_record($this->sql_query_file($e69_codnota));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
            }
            if (($resaco != false) || ($this->numrows != 0)) {

                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_fetch_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,6044,'$e69_codnota','E')");
                    $resac = db_query("insert into db_acount values($acount,971,6044,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'e69_codnota')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,971,6045,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'e69_numero')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,971,6047,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'e69_numemp')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,971,6048,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'e69_id_usuario')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,971,6046,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'e69_dtnota')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,971,6049,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'e69_dtrecebe')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,971,11062,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'e69_anousu')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,971,14669,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'e69_tipodocumentosfiscal')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,971,15655,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'e69_dtservidor')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,971,15656,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'e69_dtinclusao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,971,21595,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'e69_dtvencimento')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,971,21596,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'e69_localrecebimento')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,971,1014009,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'e69_outrosdados')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,971,1014135,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'e69_serienota')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }
        $sql = " delete from empnota
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($e69_codnota)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " e69_codnota = $e69_codnota ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Notas empenho não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $e69_codnota;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Notas empenho não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $e69_codnota;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $e69_codnota;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
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
            $this->numrows = 0;
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Erro ao selecionar os registros.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->numrows = pg_num_rows($result);
        if ($this->numrows == 0) {
            $this->erro_banco = "";
            $this->erro_sql = "Record Vazio na Tabela:empnota";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($e69_codnota = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos}";
        $sql .= "  from empnota ";
        $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = empnota.e69_id_usuario";
        $sql .= "      inner join empempenho  on  empempenho.e60_numemp = empnota.e69_numemp";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = empempenho.e60_numcgm";
        $sql .= "      inner join db_config  on  db_config.codigo = empempenho.e60_instit";
        $sql .= "      inner join orcdotacao  on  orcdotacao.o58_anousu = empempenho.e60_anousu and  orcdotacao.o58_coddot = empempenho.e60_coddot";
        $sql .= "      inner join pctipocompra  on  pctipocompra.pc50_codcom = empempenho.e60_codcom";
        $sql .= "      inner join emptipo  on  emptipo.e41_codtipo = empempenho.e60_codtipo";
        $sql .= "      inner join concarpeculiar  on  concarpeculiar.c58_sequencial = empempenho.e60_concarpeculiar";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($e69_codnota)) {
                $sql2 .= " where empnota.e69_codnota = $e69_codnota ";
            }
        } else if (!empty($dbwhere)) {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }
        return $sql;
    }

    public function sql_query_file($e69_codnota = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos} ";
        $sql .= "  from empnota ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($e69_codnota)) {
                $sql2 .= " where empnota.e69_codnota = $e69_codnota ";
            }
        } else if (!empty($dbwhere)) {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }
        return $sql;
    }

    public function sql_query_emp($e69_codnota = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from empnota ";
        $sql .= "      inner join empnotaele  on  empnotaele.e70_codnota = empnota.e69_codnota";
        $sql .= "      inner join empempenho  on  empempenho.e60_numemp = empnota.e69_numemp and e60_instit=" . db_getsession("DB_instit");
        $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario   = empnota.e69_id_usuario";
        $sql .= "      inner join cgm          on  cgm.z01_numcgm           = empempenho.e60_numcgm";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($e69_codnota != null) {
                $sql2 .= " where empnota.e69_codnota = $e69_codnota ";
            }
        } else if ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
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

    public function sql_query_nota($e69_codnota = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from empnota ";
        $sql .= "      inner join empnotaele   on  e69_codnota              = e70_codnota";
        $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = empnota.e69_id_usuario";
        $sql .= "      inner join empempenho   on  empempenho.e60_numemp    = empnota.e69_numemp";
        $sql .= "      inner join cgm          on  cgm.z01_numcgm           = empempenho.e60_numcgm";
        $sql .= "      inner join db_config    on  db_config.codigo         = empempenho.e60_instit";
        $sql .= "                             and  e60_instit               =" . db_getsession('DB_instit');
        $sql .= "      inner join orcdotacao   on  orcdotacao.o58_anousu    = empempenho.e60_anousu";
        $sql .= "                             and  orcdotacao.o58_coddot    = empempenho.e60_coddot";
        $sql .= "      inner join pctipocompra on  pctipocompra.pc50_codcom = empempenho.e60_codcom";
        $sql .= "      inner join emptipo      on  emptipo.e41_codtipo      = empempenho.e60_codtipo";
        $sql .= "      left join pagordemnota  on  e71_codnota              = empnota.e69_codnota";
        $sql .= "      left join pagordem      on  e71_codord               = e50_codord";
        $sql .= "      left join pagordemele   on  e53_codord               = pagordemnota.e71_codord";
        $sql .= "      left join empnotaord    on  m72_codnota              = e69_codnota";
        $sql .= "      left join matordem      on  m72_codordem             = m51_codordem";
        $sql .= "      left join matordemanu   on  m51_codordem             = m53_codordem";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($e69_codnota != null) {
                $sql2 .= " where empnota.e69_codnota = $e69_codnota ";
            }
        } else if ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
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

    public function sql_query_usuarios($e69_codnota = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from empnota ";
        $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = empnota.e69_id_usuario";
        $sql .= "      inner join empempenho  on  empempenho.e60_numemp = empnota.e69_numemp and e60_instit=" . db_getsession("DB_instit");

        $sql2 = "";
        if ($dbwhere == "") {
            if ($e69_codnota != null) {
                $sql2 .= " where empnota.e69_codnota = $e69_codnota ";
            }
        } else if ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
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

    public function sql_query_notas($e69_codnota = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= "       from empnota                                                                                             ";
        $sql .= " inner join empempenho           on empempenho.e60_numemp            = empnota.e69_numemp                       ";
        $sql .= " inner join empnotaele           on empnotaele.e70_codnota           = empnota.e69_codnota                      ";
        $sql .= " inner join pagordemnota         on pagordemnota.e71_codnota         = empnota.e69_codnota                      ";
        $sql .= " inner join pagordem             on pagordem.e50_codord              = pagordemnota.e71_codord                  ";
        $sql .= " inner join pagordemele          on pagordemele.e53_codord           = pagordem.e50_codord                      ";
        $sql .= " inner join cgm                  on cgm.z01_numcgm                   = empempenho.e60_numcgm                    ";
        $sql .= " inner join conlancamord         on conlancamord.c80_codord          = pagordem.e50_codord                      ";
        $sql .= " inner join conlancam            on conlancam.c70_codlan             = conlancamord.c80_codlan                  ";
        $sql .= " inner join conlancamdoc         on conlancamdoc.c71_codlan          = conlancam.c70_codlan                     ";
        $sql .= " inner join conhistdoc           on conhistdoc.c53_coddoc            = conlancamdoc.c71_coddoc                  ";
        $sql .= " inner join db_config            on db_config.codigo                 = empempenho.e60_instit                    ";
        $sql .= "  left join empnotadadospitnotas on empnotadadospitnotas.e13_empnota = empnota.e69_codnota                      ";
        $sql .= "  left join empnotadadospit      on empnotadadospit.e11_sequencial   = empnotadadospitnotas.e13_empnotadadospit ";
        $sql .= " inner join orcdotacao           on orcdotacao.o58_anousu            = empempenho.e60_anousu                    ";
        $sql .= "                                and orcdotacao.o58_coddot            = empempenho.e60_coddot                    ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($e69_codnota != null) {
                $sql2 .= " where empnota.e69_codnota = $e69_codnota ";
            }
        } else if ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
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

    public function sql_query_elemento_patrimonio($e69_codnota = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from empnota ";
        $sql .= "      inner join empnotaele                           on  empnotaele.e70_codnota = empnota.e69_codnota";
        $sql .= "      inner join empempenho                           on  empempenho.e60_numemp  = empnota.e69_numemp and e60_instit=" . db_getsession("DB_instit");
        $sql .= "      inner join db_usuarios                          on  db_usuarios.id_usuario = empnota.e69_id_usuario";
        $sql .= "      inner join cgm                                  on  cgm.z01_numcgm         = empempenho.e60_numcgm";
        $sql .= "      inner join orcelemento                          on orcelemento.o56_codele  = empnotaele.e70_codele";
        $sql .= "                                                     and orcelemento.o56_anousu  = " . db_getsession('DB_anousu');
        if (!USE_PCASP) {
            $sql .= " inner join configuracaodesdobramentopatrimonio  on configuracaodesdobramentopatrimonio.e135_desdobramento = orcelemento.o56_elemento";
        } else {

            /**
             * Quando for PCASP devemos apenas retornar as notas que tenham o seu elemento
             * configurado no grupo 9 - Material permanente
             */
            $sql .= "  inner join conplanoorcamentogrupo on e70_codele   = c21_codcon ";
            $sql .= "                                   and c21_anousu   = " . db_getsession('DB_anousu');
            $sql .= "                                   and c21_congrupo = 9";
            $sql .= "                                   and c21_instit   = " . db_getsession("DB_instit");
        }

        $sql2 = "";
        if ($dbwhere == "") {
            if ($e69_codnota != null) {
                $sql2 .= " where empnota.e69_codnota = $e69_codnota ";
            }
        } else if ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
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

    public function sql_queryMovimentacaoPatrimonial($e69_codnota = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from empnota ";
        $sql .= "      inner join empnotaele   on  e69_codnota              = e70_codnota";
        $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario   = empnota.e69_id_usuario";
        $sql .= "      inner join empempenho   on  empempenho.e60_numemp    = empnota.e69_numemp";
        $sql .= "      inner join cgm          on  cgm.z01_numcgm           = empempenho.e60_numcgm";
        $sql .= "      inner join db_config    on  db_config.codigo         = empempenho.e60_instit";
        $sql .= "                             and  e60_instit               =" . db_getsession('DB_instit');
        $sql .= "      inner join orcdotacao   on  orcdotacao.o58_anousu    = empempenho.e60_anousu";
        $sql .= "                             and  orcdotacao.o58_coddot    = empempenho.e60_coddot";
        $sql .= "      inner join pctipocompra on  pctipocompra.pc50_codcom = empempenho.e60_codcom";
        $sql .= "      inner join emptipo      on  emptipo.e41_codtipo      = empempenho.e60_codtipo";
        $sql .= "      left join pagordemnota  on  e71_codnota              = empnota.e69_codnota";
        $sql .= "      left join pagordem      on  e71_codord               = e50_codord";
        $sql .= "      left join pagordemele   on  e53_codord               = pagordemnota.e71_codord";
        $sql .= "      left join empnotaord    on  m72_codnota              = e69_codnota";
        $sql .= "      left join matordem      on  m72_codordem             = m51_codordem";
        $sql .= "      left join matordemanu   on  m51_codordem             = m53_codordem";
        $sql .= "     inner join conlancamnota on  empnota.e69_codnota      = conlancamnota.c66_codnota ";
        $sql .= "     inner join conlancam     on  conlancamnota.c66_codlan = conlancam.c70_codlan ";
        $sql .= "     inner join conlancamdoc  on  conlancam.c70_codlan     = conlancamdoc.c71_codlan ";

        $sql2 = "";
        if ($dbwhere == "") {
            if ($e69_codnota != null) {
                $sql2 .= " where empnota.e69_codnota = $e69_codnota ";
            }
        } else if ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
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

    public function sql_query_valor_financeiro($iCodigoNota = null, $sCampos = "*", $sOrdem = null, $sWhere = "")
    {
        $sql = "select {$sCampos} ";
        $sql .= " from empnota ";
        $sql .= "      inner join empnotaele  on empnotaele.e70_codnota = empnota.e69_codnota ";
        $sql .= "      inner join empempenho  on empempenho.e60_numemp  = empnota.e69_numemp ";
        $sql .= "      inner join empelemento on empelemento.e64_numemp = empempenho.e60_numemp ";
        $sql .= "      inner join orcelemento on orcelemento.o56_codele = empelemento.e64_codele ";
        $sql .= "                            and orcelemento.o56_anousu = empempenho.e60_anousu ";

        if ($sWhere == "" && $iCodigoNota != null) {
            $sql .= " where empnota.e69_codnota = {$iCodigoNota} ";
        } else if ($sWhere != "") {
            $sql .= " where {$sWhere} ";
        }

        if ($sOrdem) {
            $sql .= " order by {$sOrdem} ";
        }

        return $sql;
    }

    public function sql_query_classificacaocredores($sCampos = '*', $sWhere = null, $sOrder = null)
    {

        $sSql = "select {$sCampos} ";
        $sSql .= " from empnota ";
        $sSql .= "      inner join empempenho                   on e60_numemp      = e69_numemp ";
        $sSql .= "      inner join orcdotacao                   on (o58_coddot , o58_anousu) = (e60_coddot , e60_anousu) ";
        $sSql .= "      inner join orcunidade                   on (o58_orgao, o58_unidade, o58_anousu) = (o41_orgao, o41_unidade, o41_anousu) ";
        $sSql .= "      inner join orctiporec                   on o58_codigo = o15_codigo ";
        $sSql .= "      inner join empnotaele                   on e70_codnota     = e69_codnota ";
        $sSql .= "      inner join pagordemnota                 on e71_codnota     = e69_codnota ";
        $sSql .= "      inner join pagordem                     on e71_codord      = e50_codord ";
        $sSql .= "      inner join pagordemele                  on e53_codord      = e50_codord ";
        $sSql .= "      inner join empord                       on e82_codord      = e50_codord ";
        $sSql .= "      inner join empagemov                    on e82_codmov      = e81_codmov ";
        $sSql .= "      inner join cgm                          on z01_numcgm      = e60_numcgm ";
        $sSql .= "      left  join classificacaocredoresempenho on cc31_empempenho = e69_numemp ";
        $sSql .= "      left  join classificacaocredores        on cc30_codigo     = cc31_classificacaocredores ";
        $sSql .= "      left  join empagepag                    on e85_codmov      = e81_codmov ";

        if (!empty($sWhere)) {
            $sSql .= " where {$sWhere} ";
        }

        if (!empty($sOrder)) {
            $sSql .= " order by {$sOrder} ";
        }

        return $sSql;
    }
}
