<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2014  DBSeller Servicos de Informatica
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

class cl_caiparametro
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
    public $k29_instit = 0;
    public $k29_boletimzerado = 'f';
    public $k29_modslipnormal = 0;
    public $k29_modsliptransf = 0;
    public $k29_chqduplicado = 'f';
    public $k29_chqemitidonaoautent_dia = null;
    public $k29_chqemitidonaoautent_mes = null;
    public $k29_chqemitidonaoautent_ano = null;
    public $k29_chqemitidonaoautent = null;
    public $k29_saldoemitechq = 0;
    public $k29_datasaldocontasextra_dia = null;
    public $k29_datasaldocontasextra_mes = null;
    public $k29_datasaldocontasextra_ano = null;
    public $k29_datasaldocontasextra = null;
    public $k29_trazdatacheque = 'f';
    public $k29_contassemmovimento = 'f';
    public $k29_orctiporecfundeb = 0;
    public $k29_contapadraoslip = 0;
    public $k29_gerarslipautomaticoreceitaretencao = 0;
    public $k29_filtrocontasdepartamento = 'f';
    public $k29_validadatacreditobaixabanco = 'f';
    public $k29_fr_contapagadora = 0;
    public $k29_utilizarcontaextra = 'f';
    public $k29_folhautilizarcontaextra = 'f';
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 k29_instit = int4 = Instituição
                 k29_boletimzerado = bool = Emissão de Boletim de caixa zerado
                 k29_modslipnormal = int4 = Modelo de impressao do slip
                 k29_modsliptransf = int4 = Modelo de impressao do slip de transferencia
                 k29_chqduplicado = bool = Agenda - Permitir cheques duplicados
                 k29_chqemitidonaoautent = date = Cheques emitidos e nao autenticados a partir de
                 k29_saldoemitechq = int4 = Controlar saldo da conta ao emitir cheque
                 k29_datasaldocontasextra = date = Data Implantação Saldo Extra
                 k29_trazdatacheque = bool = Trazer data cheques pagamentos agenda
                 k29_contassemmovimento = bool = Trazer Contas sem Movimento
                 k29_orctiporecfundeb = int4 = Recurso Fundeb
                 k29_contapadraoslip = int4 = Conta padrão do slip
                 k29_gerarslipautomaticoreceitaretencao = int4 = Slip Automat. receita retencao
                 k29_filtrocontasdepartamento = bool = Filtra contas por departamento
                 k29_validadatacreditobaixabanco = bool = Validar data credito da baixa de banco
                 k29_fr_contapagadora = int4 = Validar FR de conta pagadora :
                 k29_utilizarcontaextra = bool = Utiliza conta extra-orçamentária
                 k29_folhautilizarcontaextra = bool = Conta Extra - Planilha e Slip da folha
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("caiparametro");
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
    }

    public function erro($mostra, $retorna)
    {
        if (($this->erro_status == "0") || ($mostra == true && $this->erro_status != null)) {
            echo "<script>alert(\"" . $this->erro_msg . "\")</script>";
            if ($retorna == true) {
                echo "<script>location.href='" . $this->pagina_retorno . "'</script>";
            }
        }
    }

    public function atualizacampos($exclusao = false)
    {
        if ($exclusao == false) {
            $this->k29_instit = ($this->k29_instit == "" ? @$GLOBALS["HTTP_POST_VARS"]["k29_instit"] : $this->k29_instit);
            $this->k29_boletimzerado = ($this->k29_boletimzerado == "f" ? @$GLOBALS["HTTP_POST_VARS"]["k29_boletimzerado"] : $this->k29_boletimzerado);
            $this->k29_modslipnormal = ($this->k29_modslipnormal == "" ? @$GLOBALS["HTTP_POST_VARS"]["k29_modslipnormal"] : $this->k29_modslipnormal);
            $this->k29_modsliptransf = ($this->k29_modsliptransf == "" ? @$GLOBALS["HTTP_POST_VARS"]["k29_modsliptransf"] : $this->k29_modsliptransf);
            $this->k29_chqduplicado = ($this->k29_chqduplicado == "f" ? @$GLOBALS["HTTP_POST_VARS"]["k29_chqduplicado"] : $this->k29_chqduplicado);
            if ($this->k29_chqemitidonaoautent == "") {
                $this->k29_chqemitidonaoautent_dia = ($this->k29_chqemitidonaoautent_dia == "" ? @$GLOBALS["HTTP_POST_VARS"]["k29_chqemitidonaoautent_dia"] : $this->k29_chqemitidonaoautent_dia);
                $this->k29_chqemitidonaoautent_mes = ($this->k29_chqemitidonaoautent_mes == "" ? @$GLOBALS["HTTP_POST_VARS"]["k29_chqemitidonaoautent_mes"] : $this->k29_chqemitidonaoautent_mes);
                $this->k29_chqemitidonaoautent_ano = ($this->k29_chqemitidonaoautent_ano == "" ? @$GLOBALS["HTTP_POST_VARS"]["k29_chqemitidonaoautent_ano"] : $this->k29_chqemitidonaoautent_ano);
                if ($this->k29_chqemitidonaoautent_dia != "") {
                    $this->k29_chqemitidonaoautent = $this->k29_chqemitidonaoautent_ano . "-" . $this->k29_chqemitidonaoautent_mes . "-" . $this->k29_chqemitidonaoautent_dia;
                }
            }
            $this->k29_saldoemitechq = ($this->k29_saldoemitechq == "" ? @$GLOBALS["HTTP_POST_VARS"]["k29_saldoemitechq"] : $this->k29_saldoemitechq);
            if ($this->k29_datasaldocontasextra == "") {
                $this->k29_datasaldocontasextra_dia = ($this->k29_datasaldocontasextra_dia == "" ? @$GLOBALS["HTTP_POST_VARS"]["k29_datasaldocontasextra_dia"] : $this->k29_datasaldocontasextra_dia);
                $this->k29_datasaldocontasextra_mes = ($this->k29_datasaldocontasextra_mes == "" ? @$GLOBALS["HTTP_POST_VARS"]["k29_datasaldocontasextra_mes"] : $this->k29_datasaldocontasextra_mes);
                $this->k29_datasaldocontasextra_ano = ($this->k29_datasaldocontasextra_ano == "" ? @$GLOBALS["HTTP_POST_VARS"]["k29_datasaldocontasextra_ano"] : $this->k29_datasaldocontasextra_ano);
                if ($this->k29_datasaldocontasextra_dia != "") {
                    $this->k29_datasaldocontasextra = $this->k29_datasaldocontasextra_ano . "-" . $this->k29_datasaldocontasextra_mes . "-" . $this->k29_datasaldocontasextra_dia;
                }
            }
            $this->k29_trazdatacheque = ($this->k29_trazdatacheque == "f" ? @$GLOBALS["HTTP_POST_VARS"]["k29_trazdatacheque"] : $this->k29_trazdatacheque);
            $this->k29_contassemmovimento = ($this->k29_contassemmovimento == "f" ? @$GLOBALS["HTTP_POST_VARS"]["k29_contassemmovimento"] : $this->k29_contassemmovimento);
            $this->k29_orctiporecfundeb = ($this->k29_orctiporecfundeb == "" ? @$GLOBALS["HTTP_POST_VARS"]["k29_orctiporecfundeb"] : $this->k29_orctiporecfundeb);
            $this->k29_contapadraoslip = ($this->k29_contapadraoslip == "" ? @$GLOBALS["HTTP_POST_VARS"]["k29_contapadraoslip"] : $this->k29_contapadraoslip);
            $this->k29_gerarslipautomaticoreceitaretencao = ($this->k29_gerarslipautomaticoreceitaretencao == "" ? @$GLOBALS["HTTP_POST_VARS"]["k29_gerarslipautomaticoreceitaretencao"] : $this->k29_gerarslipautomaticoreceitaretencao);
            $this->k29_filtrocontasdepartamento = ($this->k29_filtrocontasdepartamento == "f" ? @$GLOBALS["HTTP_POST_VARS"]["k29_filtrocontasdepartamento"] : $this->k29_filtrocontasdepartamento);
            $this->k29_validadatacreditobaixabanco = ($this->k29_validadatacreditobaixabanco == "f" ? @$GLOBALS["HTTP_POST_VARS"]["k29_validadatacreditobaixabanco"] : $this->k29_validadatacreditobaixabanco);
            $this->k29_fr_contapagadora = ($this->k29_fr_contapagadora == "" ? @$GLOBALS["HTTP_POST_VARS"]["k29_fr_contapagadora"] : $this->k29_fr_contapagadora);
            $this->k29_utilizarcontaextra = ($this->k29_utilizarcontaextra == "f" ? @$GLOBALS["HTTP_POST_VARS"]["k29_utilizarcontaextra"] : $this->k29_utilizarcontaextra);
            $this->k29_folhautilizarcontaextra = ($this->k29_folhautilizarcontaextra == "f" ? @$GLOBALS["HTTP_POST_VARS"]["k29_folhautilizarcontaextra"] : $this->k29_folhautilizarcontaextra);
        } else {
            $this->k29_instit = ($this->k29_instit == "" ? @$GLOBALS["HTTP_POST_VARS"]["k29_instit"] : $this->k29_instit);
        }
    }

    public function incluir($k29_instit)
    {
        $this->atualizacampos();
        if ($this->k29_boletimzerado == null) {
            $this->erro_sql = " Campo Emissão de Boletim de caixa zerado não informado.";
            $this->erro_campo = "k29_boletimzerado";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k29_modslipnormal == null) {
            $this->erro_sql = " Campo Modelo de impressao do slip não informado.";
            $this->erro_campo = "k29_modslipnormal";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k29_modsliptransf == null) {
            $this->erro_sql = " Campo Modelo de impressao do slip de transferencia não informado.";
            $this->erro_campo = "k29_modsliptransf";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k29_chqduplicado == null) {
            $this->erro_sql = " Campo Agenda - Permitir cheques duplicados não informado.";
            $this->erro_campo = "k29_chqduplicado";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k29_chqemitidonaoautent == null) {
            $this->k29_chqemitidonaoautent = "null";
        }
        if ($this->k29_saldoemitechq == null) {
            $this->erro_sql = " Campo Controlar saldo da conta ao emitir cheque não informado.";
            $this->erro_campo = "k29_saldoemitechq";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k29_datasaldocontasextra == null) {
            $this->k29_datasaldocontasextra = "null";
        }
        if ($this->k29_trazdatacheque == null) {
            $this->erro_sql = " Campo Trazer data cheques pagamentos agenda não informado.";
            $this->erro_campo = "k29_trazdatacheque";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k29_contassemmovimento == null) {
            $this->erro_sql = " Campo Trazer Contas sem Movimento não informado.";
            $this->erro_campo = "k29_contassemmovimento";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k29_orctiporecfundeb == null) {
            $this->k29_orctiporecfundeb = "0";
        }
        if ($this->k29_contapadraoslip == null) {
            $this->erro_sql = " Campo Conta padrão do slip não informado.";
            $this->erro_campo = "k29_contapadraoslip";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k29_gerarslipautomaticoreceitaretencao == null) {
            $this->erro_sql = " Campo Slip Automat. receita retencao não informado.";
            $this->erro_campo = "k29_gerarslipautomaticoreceitaretencao";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k29_filtrocontasdepartamento == null) {
            $this->erro_sql = " Campo Filtra contas por departamento não informado.";
            $this->erro_campo = "k29_filtrocontasdepartamento";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k29_validadatacreditobaixabanco == null) {
            $this->erro_sql = " Campo Validar data credito da baixa de banco não informado.";
            $this->erro_campo = "k29_validadatacreditobaixabanco";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k29_fr_contapagadora == null) {
            $this->k29_fr_contapagadora = "0";
        }
        if ($this->k29_utilizarcontaextra == null) {
            $this->erro_sql = " Campo Utiliza conta extra-orçamentária não informado.";
            $this->erro_campo = "k29_utilizarcontaextra";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k29_folhautilizarcontaextra == null) {
            $this->erro_sql = " Campo Conta Extra - Planilha e Slip da folha não informado.";
            $this->erro_campo = "k29_folhautilizarcontaextra";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->k29_instit = $k29_instit;
        if (($this->k29_instit == null) || ($this->k29_instit == "")) {
            $this->erro_sql = " Campo k29_instit não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into caiparametro(
                                       k29_instit
                                      ,k29_boletimzerado
                                      ,k29_modslipnormal
                                      ,k29_modsliptransf
                                      ,k29_chqduplicado
                                      ,k29_chqemitidonaoautent
                                      ,k29_saldoemitechq
                                      ,k29_datasaldocontasextra
                                      ,k29_trazdatacheque
                                      ,k29_contassemmovimento
                                      ,k29_orctiporecfundeb
                                      ,k29_contapadraoslip
                                      ,k29_gerarslipautomaticoreceitaretencao
                                      ,k29_filtrocontasdepartamento
                                      ,k29_validadatacreditobaixabanco
                                      ,k29_fr_contapagadora
                                      ,k29_utilizarcontaextra
                                      ,k29_folhautilizarcontaextra
                       )
                values (
                                $this->k29_instit
                               ,'$this->k29_boletimzerado'
                               ,$this->k29_modslipnormal
                               ,$this->k29_modsliptransf
                               ,'$this->k29_chqduplicado'
                               ," . ($this->k29_chqemitidonaoautent == "null" || $this->k29_chqemitidonaoautent == "" ? "null" : "'" . $this->k29_chqemitidonaoautent . "'") . "
                               ,$this->k29_saldoemitechq
                               ," . ($this->k29_datasaldocontasextra == "null" || $this->k29_datasaldocontasextra == "" ? "null" : "'" . $this->k29_datasaldocontasextra . "'") . "
                               ,'$this->k29_trazdatacheque'
                               ,'$this->k29_contassemmovimento'
                               ,$this->k29_orctiporecfundeb
                               ,$this->k29_contapadraoslip
                               ,$this->k29_gerarslipautomaticoreceitaretencao
                               ,'$this->k29_filtrocontasdepartamento'
                               ,'$this->k29_validadatacreditobaixabanco'
                               ,$this->k29_fr_contapagadora
                               ,'$this->k29_utilizarcontaextra'
                               ,'$this->k29_folhautilizarcontaextra'
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "k29 ($this->k29_instit) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "k29 já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "k29 ($this->k29_instit) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->k29_instit;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->k29_instit));
            if (($resaco != false) || ($this->numrows != 0)) {

                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_fetch_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                $resac = db_query("insert into db_acountkey values($acount,8803,'$this->k29_instit','I')");
                $resac = db_query("insert into db_acount values($acount,1503,8803,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'k29_instit')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1503,8802,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'k29_boletimzerado')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1503,9188,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'k29_modslipnormal')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1503,9189,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'k29_modsliptransf')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1503,9555,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'k29_chqduplicado')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1503,10933,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'k29_chqemitidonaoautent')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1503,10932,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'k29_saldoemitechq')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1503,14540,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'k29_datasaldocontasextra')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1503,14618,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'k29_trazdatacheque')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1503,15311,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'k29_contassemmovimento')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1503,20050,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'k29_orctiporecfundeb')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1503,1010437,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'k29_contapadraoslip')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1503,1013980,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'k29_gerarslipautomaticoreceitaretencao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1503,1014411,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'k29_filtrocontasdepartamento')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1503,1014618,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'k29_validadatacreditobaixabanco')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1503,1014791,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'k29_fr_contapagadora')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1503,1014818,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'k29_utilizarcontaextra')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1503,1015517,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'k29_folhautilizarcontaextra')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            }
        }
        return true;
    }

    public function alterar($k29_instit = null)
    {
        $this->atualizacampos();
        $sql = " update caiparametro set ";
        $virgula = "";
        if (trim($this->k29_instit) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k29_instit"])) {
            $sql .= $virgula . " k29_instit = $this->k29_instit ";
            $virgula = ",";
            if (trim($this->k29_instit) == null) {
                $this->erro_sql = " Campo Instituição não informado.";
                $this->erro_campo = "k29_instit";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k29_boletimzerado) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k29_boletimzerado"])) {
            $sql .= $virgula . " k29_boletimzerado = '$this->k29_boletimzerado' ";
            $virgula = ",";
            if (trim($this->k29_boletimzerado) == null) {
                $this->erro_sql = " Campo Emissão de Boletim de caixa zerado não informado.";
                $this->erro_campo = "k29_boletimzerado";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k29_modslipnormal) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k29_modslipnormal"])) {
            $sql .= $virgula . " k29_modslipnormal = $this->k29_modslipnormal ";
            $virgula = ",";
            if (trim($this->k29_modslipnormal) == null) {
                $this->erro_sql = " Campo Modelo de impressao do slip não informado.";
                $this->erro_campo = "k29_modslipnormal";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k29_modsliptransf) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k29_modsliptransf"])) {
            $sql .= $virgula . " k29_modsliptransf = $this->k29_modsliptransf ";
            $virgula = ",";
            if (trim($this->k29_modsliptransf) == null) {
                $this->erro_sql = " Campo Modelo de impressao do slip de transferencia não informado.";
                $this->erro_campo = "k29_modsliptransf";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k29_chqduplicado) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k29_chqduplicado"])) {
            $sql .= $virgula . " k29_chqduplicado = '$this->k29_chqduplicado' ";
            $virgula = ",";
            if (trim($this->k29_chqduplicado) == null) {
                $this->erro_sql = " Campo Agenda - Permitir cheques duplicados não informado.";
                $this->erro_campo = "k29_chqduplicado";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k29_chqemitidonaoautent) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k29_chqemitidonaoautent_dia"]) && ($GLOBALS["HTTP_POST_VARS"]["k29_chqemitidonaoautent_dia"] != "")) {
            $sql .= $virgula . " k29_chqemitidonaoautent = '$this->k29_chqemitidonaoautent' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["k29_chqemitidonaoautent_dia"])) {
                $sql .= $virgula . " k29_chqemitidonaoautent = null ";
                $virgula = ",";
            }
        }
        if (trim($this->k29_saldoemitechq) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k29_saldoemitechq"])) {
            $sql .= $virgula . " k29_saldoemitechq = $this->k29_saldoemitechq ";
            $virgula = ",";
            if (trim($this->k29_saldoemitechq) == null) {
                $this->erro_sql = " Campo Controlar saldo da conta ao emitir cheque não informado.";
                $this->erro_campo = "k29_saldoemitechq";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k29_datasaldocontasextra) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k29_datasaldocontasextra_dia"]) && ($GLOBALS["HTTP_POST_VARS"]["k29_datasaldocontasextra_dia"] != "")) {
            $sql .= $virgula . " k29_datasaldocontasextra = '$this->k29_datasaldocontasextra' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["k29_datasaldocontasextra_dia"])) {
                $sql .= $virgula . " k29_datasaldocontasextra = null ";
                $virgula = ",";
            }
        }
        if (trim($this->k29_trazdatacheque) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k29_trazdatacheque"])) {
            $sql .= $virgula . " k29_trazdatacheque = '$this->k29_trazdatacheque' ";
            $virgula = ",";
            if (trim($this->k29_trazdatacheque) == null) {
                $this->erro_sql = " Campo Trazer data cheques pagamentos agenda não informado.";
                $this->erro_campo = "k29_trazdatacheque";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k29_contassemmovimento) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k29_contassemmovimento"])) {
            $sql .= $virgula . " k29_contassemmovimento = '$this->k29_contassemmovimento' ";
            $virgula = ",";
            if (trim($this->k29_contassemmovimento) == null) {
                $this->erro_sql = " Campo Trazer Contas sem Movimento não informado.";
                $this->erro_campo = "k29_contassemmovimento";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k29_orctiporecfundeb) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k29_orctiporecfundeb"])) {
            if (trim($this->k29_orctiporecfundeb) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k29_orctiporecfundeb"])) {
                $this->k29_orctiporecfundeb = "0";
            }
            $sql .= $virgula . " k29_orctiporecfundeb = $this->k29_orctiporecfundeb ";
            $virgula = ",";
        }
        if (trim($this->k29_contapadraoslip) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k29_contapadraoslip"])) {
            $sql .= $virgula . " k29_contapadraoslip = $this->k29_contapadraoslip ";
            $virgula = ",";
            if (trim($this->k29_contapadraoslip) == null) {
                $this->erro_sql = " Campo Conta padrão do slip não informado.";
                $this->erro_campo = "k29_contapadraoslip";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k29_gerarslipautomaticoreceitaretencao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k29_gerarslipautomaticoreceitaretencao"])) {
            $sql .= $virgula . " k29_gerarslipautomaticoreceitaretencao = $this->k29_gerarslipautomaticoreceitaretencao ";
            $virgula = ",";
            if (trim($this->k29_gerarslipautomaticoreceitaretencao) == null) {
                $this->erro_sql = " Campo Slip Automat. receita retencao não informado.";
                $this->erro_campo = "k29_gerarslipautomaticoreceitaretencao";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k29_filtrocontasdepartamento) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k29_filtrocontasdepartamento"])) {
            $sql .= $virgula . " k29_filtrocontasdepartamento = '$this->k29_filtrocontasdepartamento' ";
            $virgula = ",";
            if (trim($this->k29_filtrocontasdepartamento) == null) {
                $this->erro_sql = " Campo Filtra contas por departamento não informado.";
                $this->erro_campo = "k29_filtrocontasdepartamento";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k29_validadatacreditobaixabanco) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k29_validadatacreditobaixabanco"])) {
            $sql .= $virgula . " k29_validadatacreditobaixabanco = '$this->k29_validadatacreditobaixabanco' ";
            $virgula = ",";
            if (trim($this->k29_validadatacreditobaixabanco) == null) {
                $this->erro_sql = " Campo Validar data credito da baixa de banco não informado.";
                $this->erro_campo = "k29_validadatacreditobaixabanco";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k29_fr_contapagadora) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k29_fr_contapagadora"])) {
            if (trim($this->k29_fr_contapagadora) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k29_fr_contapagadora"])) {
                $this->k29_fr_contapagadora = "0";
            }
            $sql .= $virgula . " k29_fr_contapagadora = $this->k29_fr_contapagadora ";
            $virgula = ",";
        }
        if (trim($this->k29_utilizarcontaextra) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k29_utilizarcontaextra"])) {
            $sql .= $virgula . " k29_utilizarcontaextra = '$this->k29_utilizarcontaextra' ";
            $virgula = ",";
            if (trim($this->k29_utilizarcontaextra) == null) {
                $this->erro_sql = " Campo Utiliza conta extra-orçamentária não informado.";
                $this->erro_campo = "k29_utilizarcontaextra";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k29_folhautilizarcontaextra) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k29_folhautilizarcontaextra"])) {
            $sql .= $virgula . " k29_folhautilizarcontaextra = '$this->k29_folhautilizarcontaextra' ";
            $virgula = ",";
            if (trim($this->k29_folhautilizarcontaextra) == null) {
                $this->erro_sql = " Campo Conta Extra - Planilha e Slip da folha não informado.";
                $this->erro_campo = "k29_folhautilizarcontaextra";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($k29_instit != null) {
            $sql .= " k29_instit = $this->k29_instit";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->k29_instit));
            if ($this->numrows > 0) {

                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_fetch_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,8803,'$this->k29_instit','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k29_instit"]) || $this->k29_instit != "") {
                        $resac = db_query("insert into db_acount values($acount,1503,8803,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'k29_instit')) . "','$this->k29_instit'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k29_boletimzerado"]) || $this->k29_boletimzerado != "") {
                        $resac = db_query("insert into db_acount values($acount,1503,8802,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'k29_boletimzerado')) . "','$this->k29_boletimzerado'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k29_modslipnormal"]) || $this->k29_modslipnormal != "") {
                        $resac = db_query("insert into db_acount values($acount,1503,9188,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'k29_modslipnormal')) . "','$this->k29_modslipnormal'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k29_modsliptransf"]) || $this->k29_modsliptransf != "") {
                        $resac = db_query("insert into db_acount values($acount,1503,9189,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'k29_modsliptransf')) . "','$this->k29_modsliptransf'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k29_chqduplicado"]) || $this->k29_chqduplicado != "") {
                        $resac = db_query("insert into db_acount values($acount,1503,9555,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'k29_chqduplicado')) . "','$this->k29_chqduplicado'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k29_chqemitidonaoautent"]) || $this->k29_chqemitidonaoautent != "") {
                        $resac = db_query("insert into db_acount values($acount,1503,10933,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'k29_chqemitidonaoautent')) . "','$this->k29_chqemitidonaoautent'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k29_saldoemitechq"]) || $this->k29_saldoemitechq != "") {
                        $resac = db_query("insert into db_acount values($acount,1503,10932,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'k29_saldoemitechq')) . "','$this->k29_saldoemitechq'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k29_datasaldocontasextra"]) || $this->k29_datasaldocontasextra != "") {
                        $resac = db_query("insert into db_acount values($acount,1503,14540,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'k29_datasaldocontasextra')) . "','$this->k29_datasaldocontasextra'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k29_trazdatacheque"]) || $this->k29_trazdatacheque != "") {
                        $resac = db_query("insert into db_acount values($acount,1503,14618,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'k29_trazdatacheque')) . "','$this->k29_trazdatacheque'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k29_contassemmovimento"]) || $this->k29_contassemmovimento != "") {
                        $resac = db_query("insert into db_acount values($acount,1503,15311,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'k29_contassemmovimento')) . "','$this->k29_contassemmovimento'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k29_orctiporecfundeb"]) || $this->k29_orctiporecfundeb != "") {
                        $resac = db_query("insert into db_acount values($acount,1503,20050,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'k29_orctiporecfundeb')) . "','$this->k29_orctiporecfundeb'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k29_contapadraoslip"]) || $this->k29_contapadraoslip != "") {
                        $resac = db_query("insert into db_acount values($acount,1503,1010437,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'k29_contapadraoslip')) . "','$this->k29_contapadraoslip'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k29_gerarslipautomaticoreceitaretencao"]) || $this->k29_gerarslipautomaticoreceitaretencao != "") {
                        $resac = db_query("insert into db_acount values($acount,1503,1013980,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'k29_gerarslipautomaticoreceitaretencao')) . "','$this->k29_gerarslipautomaticoreceitaretencao'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k29_filtrocontasdepartamento"]) || $this->k29_filtrocontasdepartamento != "") {
                        $resac = db_query("insert into db_acount values($acount,1503,1014411,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'k29_filtrocontasdepartamento')) . "','$this->k29_filtrocontasdepartamento'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k29_validadatacreditobaixabanco"]) || $this->k29_validadatacreditobaixabanco != "") {
                        $resac = db_query("insert into db_acount values($acount,1503,1014618,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'k29_validadatacreditobaixabanco')) . "','$this->k29_validadatacreditobaixabanco'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k29_fr_contapagadora"]) || $this->k29_fr_contapagadora != "") {
                        $resac = db_query("insert into db_acount values($acount,1503,1014791,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'k29_fr_contapagadora')) . "','$this->k29_fr_contapagadora'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k29_utilizarcontaextra"]) || $this->k29_utilizarcontaextra != "") {
                        $resac = db_query("insert into db_acount values($acount,1503,1014818,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'k29_utilizarcontaextra')) . "','$this->k29_utilizarcontaextra'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k29_folhautilizarcontaextra"]) || $this->k29_folhautilizarcontaextra != "") {
                        $resac = db_query("insert into db_acount values($acount,1503,1015517,'" . AddSlashes(pg_result($resaco, $conresaco, 'k29_folhautilizarcontaextra')) . "','$this->k29_folhautilizarcontaextra'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "k29 não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->k29_instit;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "k29 não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->k29_instit;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $this->k29_instit;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($k29_instit = null, $dbwhere = null)
    {
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            if (empty($dbwhere)) {

                $resaco = $this->sql_record($this->sql_query_file($k29_instit));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
            }
            if (($resaco != false) || ($this->numrows != 0)) {

                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_fetch_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,8803,'$k29_instit','E')");
                    $resac = db_query("insert into db_acount values($acount,1503,8803,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'k29_instit')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1503,8802,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'k29_boletimzerado')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1503,9188,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'k29_modslipnormal')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1503,9189,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'k29_modsliptransf')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1503,9555,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'k29_chqduplicado')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1503,10933,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'k29_chqemitidonaoautent')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1503,10932,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'k29_saldoemitechq')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1503,14540,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'k29_datasaldocontasextra')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1503,14618,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'k29_trazdatacheque')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1503,15311,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'k29_contassemmovimento')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1503,20050,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'k29_orctiporecfundeb')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1503,1010437,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'k29_contapadraoslip')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1503,1013980,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'k29_gerarslipautomaticoreceitaretencao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1503,1014411,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'k29_filtrocontasdepartamento')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1503,1014618,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'k29_validadatacreditobaixabanco')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1503,1014791,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'k29_fr_contapagadora')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1503,1014818,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'k29_utilizarcontaextra')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1503,1015517,'','" . AddSlashes(pg_result($resaco, $iresaco, 'k29_folhautilizarcontaextra')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }
        $sql = " delete from caiparametro
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($k29_instit)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " k29_instit = $k29_instit ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "k29 não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $k29_instit;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "k29 não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $k29_instit;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $k29_instit;
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
            $this->erro_sql = "Record Vazio na Tabela:caiparametro";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($k29_instit = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos}";
        $sql .= "  from caiparametro ";
        $sql .= "      inner join db_config  on  db_config.codigo = caiparametro.k29_instit";
        $sql .= "      left  join saltes  on  saltes.k13_conta = caiparametro.k29_contapadraoslip";
        $sql .= "      left  join orctiporec  on  orctiporec.o15_codigo = caiparametro.k29_orctiporecfundeb";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
        $sql .= "      left  join db_depart  on  db_depart.coddepto = db_config.db21_departamento";
        $sql .= "      left  join db_tipoinstit  on  db_tipoinstit.db21_codtipo = db_config.db21_tipoinstit";
        $sql .= "      left  join db_estruturavalor  on  db_estruturavalor.db121_sequencial = orctiporec.o15_db_estruturavalor";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($k29_instit)) {
                $sql2 .= " where caiparametro.k29_instit = $k29_instit ";
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

    public function sql_query_file($k29_instit = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos} ";
        $sql .= "  from caiparametro ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($k29_instit)) {
                $sql2 .= " where caiparametro.k29_instit = $k29_instit ";
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

}
