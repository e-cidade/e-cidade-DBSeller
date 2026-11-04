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

/**
 * Class cl_rechumano
 */
class cl_rechumano
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
    public $ed20_i_codigo = 0;
    public $ed20_c_outroscursos = null;
    public $ed20_c_posgraduacao = null;
    public $ed20_i_escolaridade = 0;
    public $ed20_i_censomunicender = 0;
    public $ed20_i_censoufender = 0;
    public $ed20_c_passaporte = null;
    public $ed20_i_censoufcert = 0;
    public $ed20_c_certidaocart = null;
    public $ed20_c_certidaodata_dia = null;
    public $ed20_c_certidaodata_mes = null;
    public $ed20_c_certidaodata_ano = null;
    public $ed20_c_certidaodata = null;
    public $ed20_c_certidaolivro = null;
    public $ed20_c_certidaofolha = null;
    public $ed20_c_certidaonum = null;
    public $ed20_i_certidaotipo = 0;
    public $ed20_d_dataident_dia = null;
    public $ed20_d_dataident_mes = null;
    public $ed20_d_dataident_ano = null;
    public $ed20_d_dataident = null;
    public $ed20_i_censoufident = 0;
    public $ed20_i_censoorgemiss = 0;
    public $ed20_c_identcompl = null;
    public $ed20_i_censomunicnat = 0;
    public $ed20_i_censoufnat = 0;
    public $ed20_i_nacionalidade = 0;
    public $ed20_i_raca = 0;
    public $ed20_c_nis = null;
    public $ed20_i_codigoinep = 0;
    public $ed20_i_pais = 0;
    public $ed20_i_tiposervidor = 0;
    public $ed20_i_rhregime = 0;
    public $ed20_c_efetividade = null;
    public $ed20_i_censocartorio = 0;
    public $ed20_i_zonaresidencia = 0;
    public $ed20_tipoensinomedio = null;
    public $ed20_localizacaodiferenciada = null;
    public $ed20_paisresidencia = null;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 ed20_i_codigo = int8 = Código
                 ed20_c_outroscursos = char(15) = Outros Cursos
                 ed20_c_posgraduacao = char(10) = Pós - Graduação
                 ed20_i_escolaridade = int4 = Escolaridade
                 ed20_i_censomunicender = int4 = Município Endereço
                 ed20_i_censoufender = int4 = UF Endereço
                 ed20_c_passaporte = char(20) = N° Passaporte
                 ed20_i_censoufcert = int4 = UF Cartório
                 ed20_c_certidaocart = char(100) = Nome do Cartório
                 ed20_c_certidaodata = date = Data Emissão
                 ed20_c_certidaolivro = char(8) = Livro
                 ed20_c_certidaofolha = char(4) = Folha
                 ed20_c_certidaonum = char(8) = Número do Termo
                 ed20_i_certidaotipo = int4 = Certidão Tipo
                 ed20_d_dataident = date = Data de Expedição
                 ed20_i_censoufident = int4 = UF da Identidade
                 ed20_i_censoorgemiss = int4 = Órgão Emissor
                 ed20_c_identcompl = char(4) = Complemento
                 ed20_i_censomunicnat = int4 = Município de Nascimento
                 ed20_i_censoufnat = int4 = UF de nascimento
                 ed20_i_nacionalidade = int4 = Nacionalidade
                 ed20_i_raca = int4 = Cor/Raça
                 ed20_c_nis = char(11) = N° NIS
                 ed20_i_codigoinep = int8 = Código INEP
                 ed20_i_pais = int4 = País
                 ed20_i_tiposervidor = int4 = Servidor da Prefeitura
                 ed20_i_rhregime = int8 = Regime
                 ed20_c_efetividade = char(1) = Informar Efetividade
                 ed20_i_censocartorio = int4 = Cartório
                 ed20_i_zonaresidencia = int4 = Zona de Residência
                 ed20_tipoensinomedio = float4 = Tipo de Ensino Médio
                 ed20_localizacaodiferenciada = int4 = Localização Diferenciada
                 ed20_paisresidencia = int4 = País de Residencia
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("rechumano");
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
            $this->ed20_i_codigo = ($this->ed20_i_codigo == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_i_codigo"] : $this->ed20_i_codigo);
            $this->ed20_c_outroscursos = ($this->ed20_c_outroscursos == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_c_outroscursos"] : $this->ed20_c_outroscursos);
            $this->ed20_c_posgraduacao = ($this->ed20_c_posgraduacao == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_c_posgraduacao"] : $this->ed20_c_posgraduacao);
            $this->ed20_i_escolaridade = ($this->ed20_i_escolaridade == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_i_escolaridade"] : $this->ed20_i_escolaridade);
            $this->ed20_i_censomunicender = ($this->ed20_i_censomunicender == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_i_censomunicender"] : $this->ed20_i_censomunicender);
            $this->ed20_i_censoufender = ($this->ed20_i_censoufender == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_i_censoufender"] : $this->ed20_i_censoufender);
            $this->ed20_c_passaporte = ($this->ed20_c_passaporte == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_c_passaporte"] : $this->ed20_c_passaporte);
            $this->ed20_i_censoufcert = ($this->ed20_i_censoufcert == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_i_censoufcert"] : $this->ed20_i_censoufcert);
            $this->ed20_c_certidaocart = ($this->ed20_c_certidaocart == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_c_certidaocart"] : $this->ed20_c_certidaocart);
            if ($this->ed20_c_certidaodata == "") {
                $this->ed20_c_certidaodata_dia = ($this->ed20_c_certidaodata_dia == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_c_certidaodata_dia"] : $this->ed20_c_certidaodata_dia);
                $this->ed20_c_certidaodata_mes = ($this->ed20_c_certidaodata_mes == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_c_certidaodata_mes"] : $this->ed20_c_certidaodata_mes);
                $this->ed20_c_certidaodata_ano = ($this->ed20_c_certidaodata_ano == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_c_certidaodata_ano"] : $this->ed20_c_certidaodata_ano);
                if ($this->ed20_c_certidaodata_dia != "") {
                    $this->ed20_c_certidaodata = $this->ed20_c_certidaodata_ano . "-" . $this->ed20_c_certidaodata_mes . "-" . $this->ed20_c_certidaodata_dia;
                }
            }
            $this->ed20_c_certidaolivro = ($this->ed20_c_certidaolivro == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_c_certidaolivro"] : $this->ed20_c_certidaolivro);
            $this->ed20_c_certidaofolha = ($this->ed20_c_certidaofolha == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_c_certidaofolha"] : $this->ed20_c_certidaofolha);
            $this->ed20_c_certidaonum = ($this->ed20_c_certidaonum == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_c_certidaonum"] : $this->ed20_c_certidaonum);
            $this->ed20_i_certidaotipo = ($this->ed20_i_certidaotipo == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_i_certidaotipo"] : $this->ed20_i_certidaotipo);
            if ($this->ed20_d_dataident == "") {
                $this->ed20_d_dataident_dia = ($this->ed20_d_dataident_dia == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_d_dataident_dia"] : $this->ed20_d_dataident_dia);
                $this->ed20_d_dataident_mes = ($this->ed20_d_dataident_mes == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_d_dataident_mes"] : $this->ed20_d_dataident_mes);
                $this->ed20_d_dataident_ano = ($this->ed20_d_dataident_ano == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_d_dataident_ano"] : $this->ed20_d_dataident_ano);
                if ($this->ed20_d_dataident_dia != "") {
                    $this->ed20_d_dataident = $this->ed20_d_dataident_ano . "-" . $this->ed20_d_dataident_mes . "-" . $this->ed20_d_dataident_dia;
                }
            }
            $this->ed20_i_censoufident = ($this->ed20_i_censoufident == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_i_censoufident"] : $this->ed20_i_censoufident);
            $this->ed20_i_censoorgemiss = ($this->ed20_i_censoorgemiss == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_i_censoorgemiss"] : $this->ed20_i_censoorgemiss);
            $this->ed20_c_identcompl = ($this->ed20_c_identcompl == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_c_identcompl"] : $this->ed20_c_identcompl);
            $this->ed20_i_censomunicnat = ($this->ed20_i_censomunicnat == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_i_censomunicnat"] : $this->ed20_i_censomunicnat);
            $this->ed20_i_censoufnat = ($this->ed20_i_censoufnat == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_i_censoufnat"] : $this->ed20_i_censoufnat);
            $this->ed20_i_nacionalidade = ($this->ed20_i_nacionalidade == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_i_nacionalidade"] : $this->ed20_i_nacionalidade);
            $this->ed20_i_raca = ($this->ed20_i_raca == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_i_raca"] : $this->ed20_i_raca);
            $this->ed20_c_nis = ($this->ed20_c_nis == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_c_nis"] : $this->ed20_c_nis);
            $this->ed20_i_codigoinep = ($this->ed20_i_codigoinep == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_i_codigoinep"] : $this->ed20_i_codigoinep);
            $this->ed20_i_pais = ($this->ed20_i_pais == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_i_pais"] : $this->ed20_i_pais);
            $this->ed20_i_tiposervidor = ($this->ed20_i_tiposervidor == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_i_tiposervidor"] : $this->ed20_i_tiposervidor);
            $this->ed20_i_rhregime = ($this->ed20_i_rhregime == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_i_rhregime"] : $this->ed20_i_rhregime);
            $this->ed20_c_efetividade = ($this->ed20_c_efetividade == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_c_efetividade"] : $this->ed20_c_efetividade);
            $this->ed20_i_censocartorio = ($this->ed20_i_censocartorio == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_i_censocartorio"] : $this->ed20_i_censocartorio);
            $this->ed20_i_zonaresidencia = ($this->ed20_i_zonaresidencia == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_i_zonaresidencia"] : $this->ed20_i_zonaresidencia);
            $this->ed20_tipoensinomedio = (empty($this->ed20_tipoensinomedio) ? "null" : $this->ed20_tipoensinomedio);
            $this->ed20_localizacaodiferenciada = (empty($this->ed20_localizacaodiferenciada) ? "null" : $this->ed20_localizacaodiferenciada);
            $this->ed20_paisresidencia = (empty($this->ed20_paisresidencia) ? "null" : $this->ed20_paisresidencia);
        } else {
            $this->ed20_i_codigo = ($this->ed20_i_codigo == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed20_i_codigo"] : $this->ed20_i_codigo);
        }
    }

    public function incluir($ed20_i_codigo)
    {
        $this->atualizacampos();
        if ($this->ed20_c_outroscursos == null) {
            $this->erro_sql = " Campo Outros Cursos não informado.";
            $this->erro_campo = "ed20_c_outroscursos";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ed20_i_escolaridade == null) {
            $this->erro_sql = " Campo Escolaridade não informado.";
            $this->erro_campo = "ed20_i_escolaridade";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ed20_i_censomunicender == null || $this->ed20_i_censomunicender == " ") {
            $this->ed20_i_censomunicender = "null";
        }
        if ($this->ed20_i_censoufender == null || $this->ed20_i_censoufender == " ") {
            $this->ed20_i_censoufender = "null";
        }
        if ($this->ed20_i_censoufcert == null || $this->ed20_i_censoufcert == " ") {
            $this->ed20_i_censoufcert = "null";
        }
        if ($this->ed20_c_certidaodata == null) {
            $this->ed20_c_certidaodata = "null";
        }
        if ($this->ed20_i_certidaotipo == null) {
            $this->ed20_i_certidaotipo = "null";
        }
        if ($this->ed20_d_dataident == null) {
            $this->ed20_d_dataident = "null";
        }
        if ($this->ed20_i_censoufident == null || $this->ed20_i_censoufident == " ") {
            $this->ed20_i_censoufident = "null";
        }
        if ($this->ed20_i_censoorgemiss == null || $this->ed20_i_censoorgemiss == " ") {
            $this->ed20_i_censoorgemiss = "null";
        }
        if ($this->ed20_i_censomunicnat == null || $this->ed20_i_censomunicnat == " ") {
            $this->ed20_i_censomunicnat = "null";
        }
        if ($this->ed20_i_censoufnat == null || $this->ed20_i_censoufnat == " ") {
            $this->ed20_i_censoufnat = "null";
        }
        if ($this->ed20_i_nacionalidade == null) {
            $this->erro_sql = " Campo Nacionalidade não informado.";
            $this->erro_campo = "ed20_i_nacionalidade";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ed20_i_raca == null) {
            $this->erro_sql = " Campo Cor/Raça não informado.";
            $this->erro_campo = "ed20_i_raca";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ed20_i_codigoinep == null) {
            $this->ed20_i_codigoinep = "null";
        }
        if ($this->ed20_i_pais == null) {
            $this->erro_sql = " Campo País não informado.";
            $this->erro_campo = "ed20_i_pais";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ed20_i_tiposervidor == null) {
            $this->erro_sql = " Campo Servidor da Prefeitura não informado.";
            $this->erro_campo = "ed20_i_tiposervidor";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ed20_i_rhregime == null) {
            $this->ed20_i_rhregime = "null";
        }
        if ($this->ed20_c_efetividade == null) {
            $this->erro_sql = " Campo Informar Efetividade não informado.";
            $this->erro_campo = "ed20_c_efetividade";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ed20_i_censocartorio == null || $this->ed20_i_censocartorio == " ") {
            $this->ed20_i_censocartorio = "null";
        }
        if ($this->ed20_i_zonaresidencia == null) {
            $this->erro_sql = " Campo Zona de Residência não informado.";
            $this->erro_campo = "ed20_i_zonaresidencia";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ed20_tipoensinomedio == null) {
            $this->ed20_tipoensinomedio = "null";
        }
        if ($this->ed20_localizacaodiferenciada == null) {
            $this->ed20_localizacaodiferenciada = "null";
        }
        if ($this->ed20_paisresidencia == null) {
            $this->ed20_paisresidencia = "null";
        }
        if ($ed20_i_codigo == "" || $ed20_i_codigo == null) {
            $result = db_query("select nextval('rechumano_ed20_i_codigo_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: rechumano_ed20_i_codigo_seq do campo: ed20_i_codigo";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->ed20_i_codigo = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from rechumano_ed20_i_codigo_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $ed20_i_codigo)) {
                $this->erro_sql = " Campo ed20_i_codigo maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->ed20_i_codigo = $ed20_i_codigo;
            }
        }
        if (($this->ed20_i_codigo == null) || ($this->ed20_i_codigo == "")) {
            $this->erro_sql = " Campo ed20_i_codigo não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into rechumano(
                                       ed20_i_codigo
                                      ,ed20_c_outroscursos
                                      ,ed20_c_posgraduacao
                                      ,ed20_i_escolaridade
                                      ,ed20_i_censomunicender
                                      ,ed20_i_censoufender
                                      ,ed20_c_passaporte
                                      ,ed20_i_censoufcert
                                      ,ed20_c_certidaocart
                                      ,ed20_c_certidaodata
                                      ,ed20_c_certidaolivro
                                      ,ed20_c_certidaofolha
                                      ,ed20_c_certidaonum
                                      ,ed20_i_certidaotipo
                                      ,ed20_d_dataident
                                      ,ed20_i_censoufident
                                      ,ed20_i_censoorgemiss
                                      ,ed20_c_identcompl
                                      ,ed20_i_censomunicnat
                                      ,ed20_i_censoufnat
                                      ,ed20_i_nacionalidade
                                      ,ed20_i_raca
                                      ,ed20_c_nis
                                      ,ed20_i_codigoinep
                                      ,ed20_i_pais
                                      ,ed20_i_tiposervidor
                                      ,ed20_i_rhregime
                                      ,ed20_c_efetividade
                                      ,ed20_i_censocartorio
                                      ,ed20_i_zonaresidencia
                                      ,ed20_tipoensinomedio
                                      ,ed20_localizacaodiferenciada
                                      ,ed20_paisresidencia
                       )
                values (
                                $this->ed20_i_codigo
                               ,'$this->ed20_c_outroscursos'
                               ,'$this->ed20_c_posgraduacao'
                               ,$this->ed20_i_escolaridade
                               ,$this->ed20_i_censomunicender
                               ,$this->ed20_i_censoufender
                               ,'$this->ed20_c_passaporte'
                               ,$this->ed20_i_censoufcert
                               ,'$this->ed20_c_certidaocart'
                               ," . ($this->ed20_c_certidaodata == "null" || $this->ed20_c_certidaodata == "" ? "null" : "'" . $this->ed20_c_certidaodata . "'") . "
                               ,'$this->ed20_c_certidaolivro'
                               ,'$this->ed20_c_certidaofolha'
                               ,'$this->ed20_c_certidaonum'
                               ,$this->ed20_i_certidaotipo
                               ," . ($this->ed20_d_dataident == "null" || $this->ed20_d_dataident == "" ? "null" : "'" . $this->ed20_d_dataident . "'") . "
                               ,$this->ed20_i_censoufident
                               ,$this->ed20_i_censoorgemiss
                               ,'$this->ed20_c_identcompl'
                               ,$this->ed20_i_censomunicnat
                               ,$this->ed20_i_censoufnat
                               ,$this->ed20_i_nacionalidade
                               ,$this->ed20_i_raca
                               ,'$this->ed20_c_nis'
                               ,$this->ed20_i_codigoinep
                               ,$this->ed20_i_pais
                               ,$this->ed20_i_tiposervidor
                               ,$this->ed20_i_rhregime
                               ,'$this->ed20_c_efetividade'
                               ,$this->ed20_i_censocartorio
                               ,$this->ed20_i_zonaresidencia
                               ,$this->ed20_tipoensinomedio
                               ,$this->ed20_localizacaodiferenciada
                               ,$this->ed20_paisresidencia
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "Recursos Humanos ($this->ed20_i_codigo) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "Recursos Humanos já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "Recursos Humanos ($this->ed20_i_codigo) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->ed20_i_codigo;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->ed20_i_codigo));
            if (($resaco != false) || ($this->numrows != 0)) {

                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                $resac = db_query("insert into db_acountkey values($acount,1008513,'$this->ed20_i_codigo','I')");
                $resac = db_query("insert into db_acount values($acount,1010087,1008513,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_i_codigo')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,13819,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_c_outroscursos')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,13818,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_c_posgraduacao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,13817,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_i_escolaridade')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,13816,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_i_censomunicender')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,13815,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_i_censoufender')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,13814,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_c_passaporte')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,13813,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_i_censoufcert')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,13812,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_c_certidaocart')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,13811,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_c_certidaodata')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,13810,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_c_certidaolivro')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,13809,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_c_certidaofolha')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,13808,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_c_certidaonum')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,13807,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_i_certidaotipo')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,13806,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_d_dataident')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,13805,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_i_censoufident')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,13804,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_i_censoorgemiss')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,13803,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_c_identcompl')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,13802,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_i_censomunicnat')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,13801,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_i_censoufnat')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,13800,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_i_nacionalidade')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,13799,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_i_raca')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,13798,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_c_nis')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,13797,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_i_codigoinep')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,13823,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_i_pais')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,17288,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_i_tiposervidor')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,17336,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_i_rhregime')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,17444,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_c_efetividade')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,18011,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_i_censocartorio')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,18919,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_i_zonaresidencia')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,1010421,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_tipoensinomedio')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,1010420,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_localizacaodiferenciada')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010087,1010419,'','" . AddSlashes(pg_result($resaco, 0, 'ed20_paisresidencia')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            }
        }
        return true;
    }

    public function alterar($ed20_i_codigo = null)
    {
        $this->atualizacampos();
        $sql = " update rechumano set ";
        $virgula = "";
        if (trim($this->ed20_i_codigo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_codigo"])) {
            $sql .= $virgula . " ed20_i_codigo = $this->ed20_i_codigo ";
            $virgula = ",";
            if (trim($this->ed20_i_codigo) == null) {
                $this->erro_sql = " Campo Código não informado.";
                $this->erro_campo = "ed20_i_codigo";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->ed20_c_outroscursos) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_c_outroscursos"])) {
            $sql .= $virgula . " ed20_c_outroscursos = '$this->ed20_c_outroscursos' ";
            $virgula = ",";
            if (trim($this->ed20_c_outroscursos) == null) {
                $this->erro_sql = " Campo Outros Cursos não informado.";
                $this->erro_campo = "ed20_c_outroscursos";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->ed20_c_posgraduacao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_c_posgraduacao"])) {
            $sql .= $virgula . " ed20_c_posgraduacao = '$this->ed20_c_posgraduacao' ";
            $virgula = ",";
        }
        if (trim($this->ed20_i_escolaridade) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_escolaridade"])) {
            $sql .= $virgula . " ed20_i_escolaridade = $this->ed20_i_escolaridade ";
            $virgula = ",";
            if (trim($this->ed20_i_escolaridade) == null) {
                $this->erro_sql = " Campo Escolaridade não informado.";
                $this->erro_campo = "ed20_i_escolaridade";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->ed20_i_censomunicender) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_censomunicender"])) {
            if (trim($this->ed20_i_censomunicender) == "" && isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_censomunicender"])) {
                $this->ed20_i_censomunicender = "null";
            }
            $sql .= $virgula . " ed20_i_censomunicender = $this->ed20_i_censomunicender ";
            $virgula = ",";
        }
        if (trim($this->ed20_i_censoufender) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_censoufender"])) {
            if (trim($this->ed20_i_censoufender) == "" && isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_censoufender"])) {
                $this->ed20_i_censoufender = "null";
            }
            $sql .= $virgula . " ed20_i_censoufender = $this->ed20_i_censoufender ";
            $virgula = ",";
        }
        if (trim($this->ed20_c_passaporte) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_c_passaporte"])) {
            $sql .= $virgula . " ed20_c_passaporte = '$this->ed20_c_passaporte' ";
            $virgula = ",";
        }
        if (trim($this->ed20_i_censoufcert) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_censoufcert"])) {
            if (trim($this->ed20_i_censoufcert) == "" && isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_censoufcert"])) {
                $this->ed20_i_censoufcert = "null";
            }
            $sql .= $virgula . " ed20_i_censoufcert = $this->ed20_i_censoufcert ";
            $virgula = ",";
        }
        if (trim($this->ed20_c_certidaocart) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_c_certidaocart"])) {
            $sql .= $virgula . " ed20_c_certidaocart = '$this->ed20_c_certidaocart' ";
            $virgula = ",";
        }
        if (trim($this->ed20_c_certidaodata) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_c_certidaodata_dia"]) && ($GLOBALS["HTTP_POST_VARS"]["ed20_c_certidaodata_dia"] != "")) {
            $sql .= $virgula . " ed20_c_certidaodata = '$this->ed20_c_certidaodata' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_c_certidaodata_dia"])) {
                $sql .= $virgula . " ed20_c_certidaodata = null ";
                $virgula = ",";
            }
        }
        if (trim($this->ed20_c_certidaolivro) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_c_certidaolivro"])) {
            $sql .= $virgula . " ed20_c_certidaolivro = '$this->ed20_c_certidaolivro' ";
            $virgula = ",";
        }
        if (trim($this->ed20_c_certidaofolha) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_c_certidaofolha"])) {
            $sql .= $virgula . " ed20_c_certidaofolha = '$this->ed20_c_certidaofolha' ";
            $virgula = ",";
        }
        if (trim($this->ed20_c_certidaonum) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_c_certidaonum"])) {
            $sql .= $virgula . " ed20_c_certidaonum = '$this->ed20_c_certidaonum' ";
            $virgula = ",";
        }
        if (trim($this->ed20_i_certidaotipo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_certidaotipo"])) {
            if (trim($this->ed20_i_certidaotipo) == "" && isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_certidaotipo"])) {
                $this->ed20_i_certidaotipo = "null";
            }
            $sql .= $virgula . " ed20_i_certidaotipo = $this->ed20_i_certidaotipo ";
            $virgula = ",";
        }
        if (trim($this->ed20_d_dataident) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_d_dataident_dia"]) && ($GLOBALS["HTTP_POST_VARS"]["ed20_d_dataident_dia"] != "")) {
            $sql .= $virgula . " ed20_d_dataident = '$this->ed20_d_dataident' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_d_dataident_dia"])) {
                $sql .= $virgula . " ed20_d_dataident = null ";
                $virgula = ",";
            }
        }
        if (trim($this->ed20_i_censoufident) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_censoufident"])) {
            if (trim($this->ed20_i_censoufident) == "" && isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_censoufident"])) {
                $this->ed20_i_censoufident = "null";
            }
            $sql .= $virgula . " ed20_i_censoufident = $this->ed20_i_censoufident ";
            $virgula = ",";
        }
        if (trim($this->ed20_i_censoorgemiss) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_censoorgemiss"])) {
            if (trim($this->ed20_i_censoorgemiss) == "" && isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_censoorgemiss"])) {
                $this->ed20_i_censoorgemiss = "null";
            }
            $sql .= $virgula . " ed20_i_censoorgemiss = $this->ed20_i_censoorgemiss ";
            $virgula = ",";
        }
        if (trim($this->ed20_c_identcompl) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_c_identcompl"])) {
            $sql .= $virgula . " ed20_c_identcompl = '$this->ed20_c_identcompl' ";
            $virgula = ",";
        }
        if (trim($this->ed20_i_censomunicnat) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_censomunicnat"])) {
            if (trim($this->ed20_i_censomunicnat) == "" && isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_censomunicnat"])) {
                $this->ed20_i_censomunicnat = "null";
            }
            $sql .= $virgula . " ed20_i_censomunicnat = $this->ed20_i_censomunicnat ";
            $virgula = ",";
        }
        if (trim($this->ed20_i_censoufnat) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_censoufnat"])) {
            if (trim($this->ed20_i_censoufnat) == "" && isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_censoufnat"])) {
                $this->ed20_i_censoufnat = "null";
            }
            $sql .= $virgula . " ed20_i_censoufnat = $this->ed20_i_censoufnat ";
            $virgula = ",";
        }
        if (trim($this->ed20_i_nacionalidade) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_nacionalidade"])) {
            $sql .= $virgula . " ed20_i_nacionalidade = $this->ed20_i_nacionalidade ";
            $virgula = ",";
            if (trim($this->ed20_i_nacionalidade) == null) {
                $this->erro_sql = " Campo Nacionalidade não informado.";
                $this->erro_campo = "ed20_i_nacionalidade";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->ed20_i_raca) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_raca"])) {
            $sql .= $virgula . " ed20_i_raca = $this->ed20_i_raca ";
            $virgula = ",";
            if (trim($this->ed20_i_raca) == null) {
                $this->erro_sql = " Campo Cor/Raça não informado.";
                $this->erro_campo = "ed20_i_raca";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->ed20_c_nis) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_c_nis"])) {
            $sql .= $virgula . " ed20_c_nis = '$this->ed20_c_nis' ";
            $virgula = ",";
        }
        if (trim($this->ed20_i_codigoinep) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_codigoinep"])) {
            if (trim($this->ed20_i_codigoinep) == "" && isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_codigoinep"])) {
                $this->ed20_i_codigoinep = "null";
            }
            $sql .= $virgula . " ed20_i_codigoinep = $this->ed20_i_codigoinep ";
            $virgula = ",";
        }
        if (trim($this->ed20_i_pais) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_pais"])) {
            $sql .= $virgula . " ed20_i_pais = $this->ed20_i_pais ";
            $virgula = ",";
            if (trim($this->ed20_i_pais) == null) {
                $this->erro_sql = " Campo País não informado.";
                $this->erro_campo = "ed20_i_pais";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->ed20_i_tiposervidor) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_tiposervidor"])) {
            $sql .= $virgula . " ed20_i_tiposervidor = $this->ed20_i_tiposervidor ";
            $virgula = ",";
            if (trim($this->ed20_i_tiposervidor) == null) {
                $this->erro_sql = " Campo Servidor da Prefeitura não informado.";
                $this->erro_campo = "ed20_i_tiposervidor";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->ed20_i_rhregime) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_rhregime"])) {
            if (trim($this->ed20_i_rhregime) == "" && isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_rhregime"])) {
                $this->ed20_i_rhregime = "null";
            }
            $sql .= $virgula . " ed20_i_rhregime = $this->ed20_i_rhregime ";
            $virgula = ",";
        }
        if (trim($this->ed20_c_efetividade) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_c_efetividade"])) {
            $sql .= $virgula . " ed20_c_efetividade = '$this->ed20_c_efetividade' ";
            $virgula = ",";
            if (trim($this->ed20_c_efetividade) == null) {
                $this->erro_sql = " Campo Informar Efetividade não informado.";
                $this->erro_campo = "ed20_c_efetividade";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->ed20_i_censocartorio) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_censocartorio"])) {
            if (trim($this->ed20_i_censocartorio) == "" && isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_censocartorio"])) {
                $this->ed20_i_censocartorio = "null";
            }
            $sql .= $virgula . " ed20_i_censocartorio = $this->ed20_i_censocartorio ";
            $virgula = ",";
        }
        if (trim($this->ed20_i_zonaresidencia) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_zonaresidencia"])) {
            $sql .= $virgula . " ed20_i_zonaresidencia = $this->ed20_i_zonaresidencia ";
            $virgula = ",";
            if (trim($this->ed20_i_zonaresidencia) == null) {
                $this->erro_sql = " Campo Zona de Residência não informado.";
                $this->erro_campo = "ed20_i_zonaresidencia";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (empty($this->ed20_tipoensinomedio)) {
            $this->ed20_tipoensinomedio = "null";
        }
        $sql .= $virgula . " ed20_tipoensinomedio = $this->ed20_tipoensinomedio ";
        $virgula = ",";

        if (empty($this->ed20_localizacaodiferenciada)) {
            $this->ed20_localizacaodiferenciada = "null";
        }
        $sql .= $virgula . " ed20_localizacaodiferenciada = $this->ed20_localizacaodiferenciada ";
        $virgula = ",";

        if (empty($this->ed20_paisresidencia)) {
            $this->ed20_paisresidencia = "null";
        }
        $sql .= $virgula . " ed20_paisresidencia = $this->ed20_paisresidencia ";
        $virgula = ",";

        $sql .= " where ";
        if ($ed20_i_codigo != null) {
            $sql .= " ed20_i_codigo = $this->ed20_i_codigo";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->ed20_i_codigo));
            if ($this->numrows > 0) {

                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,1008513,'$this->ed20_i_codigo','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_codigo"]) || $this->ed20_i_codigo != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,1008513,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_i_codigo')) . "','$this->ed20_i_codigo'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_c_outroscursos"]) || $this->ed20_c_outroscursos != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,13819,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_c_outroscursos')) . "','$this->ed20_c_outroscursos'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_c_posgraduacao"]) || $this->ed20_c_posgraduacao != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,13818,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_c_posgraduacao')) . "','$this->ed20_c_posgraduacao'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_escolaridade"]) || $this->ed20_i_escolaridade != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,13817,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_i_escolaridade')) . "','$this->ed20_i_escolaridade'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_censomunicender"]) || $this->ed20_i_censomunicender != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,13816,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_i_censomunicender')) . "','$this->ed20_i_censomunicender'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_censoufender"]) || $this->ed20_i_censoufender != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,13815,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_i_censoufender')) . "','$this->ed20_i_censoufender'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_c_passaporte"]) || $this->ed20_c_passaporte != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,13814,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_c_passaporte')) . "','$this->ed20_c_passaporte'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_censoufcert"]) || $this->ed20_i_censoufcert != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,13813,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_i_censoufcert')) . "','$this->ed20_i_censoufcert'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_c_certidaocart"]) || $this->ed20_c_certidaocart != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,13812,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_c_certidaocart')) . "','$this->ed20_c_certidaocart'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_c_certidaodata"]) || $this->ed20_c_certidaodata != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,13811,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_c_certidaodata')) . "','$this->ed20_c_certidaodata'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_c_certidaolivro"]) || $this->ed20_c_certidaolivro != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,13810,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_c_certidaolivro')) . "','$this->ed20_c_certidaolivro'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_c_certidaofolha"]) || $this->ed20_c_certidaofolha != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,13809,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_c_certidaofolha')) . "','$this->ed20_c_certidaofolha'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_c_certidaonum"]) || $this->ed20_c_certidaonum != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,13808,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_c_certidaonum')) . "','$this->ed20_c_certidaonum'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_certidaotipo"]) || $this->ed20_i_certidaotipo != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,13807,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_i_certidaotipo')) . "','$this->ed20_i_certidaotipo'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_d_dataident"]) || $this->ed20_d_dataident != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,13806,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_d_dataident')) . "','$this->ed20_d_dataident'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_censoufident"]) || $this->ed20_i_censoufident != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,13805,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_i_censoufident')) . "','$this->ed20_i_censoufident'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_censoorgemiss"]) || $this->ed20_i_censoorgemiss != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,13804,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_i_censoorgemiss')) . "','$this->ed20_i_censoorgemiss'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_c_identcompl"]) || $this->ed20_c_identcompl != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,13803,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_c_identcompl')) . "','$this->ed20_c_identcompl'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_censomunicnat"]) || $this->ed20_i_censomunicnat != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,13802,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_i_censomunicnat')) . "','$this->ed20_i_censomunicnat'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_censoufnat"]) || $this->ed20_i_censoufnat != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,13801,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_i_censoufnat')) . "','$this->ed20_i_censoufnat'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_nacionalidade"]) || $this->ed20_i_nacionalidade != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,13800,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_i_nacionalidade')) . "','$this->ed20_i_nacionalidade'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_raca"]) || $this->ed20_i_raca != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,13799,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_i_raca')) . "','$this->ed20_i_raca'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_c_nis"]) || $this->ed20_c_nis != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,13798,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_c_nis')) . "','$this->ed20_c_nis'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_codigoinep"]) || $this->ed20_i_codigoinep != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,13797,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_i_codigoinep')) . "','$this->ed20_i_codigoinep'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_pais"]) || $this->ed20_i_pais != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,13823,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_i_pais')) . "','$this->ed20_i_pais'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_tiposervidor"]) || $this->ed20_i_tiposervidor != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,17288,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_i_tiposervidor')) . "','$this->ed20_i_tiposervidor'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_rhregime"]) || $this->ed20_i_rhregime != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,17336,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_i_rhregime')) . "','$this->ed20_i_rhregime'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_c_efetividade"]) || $this->ed20_c_efetividade != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,17444,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_c_efetividade')) . "','$this->ed20_c_efetividade'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_censocartorio"]) || $this->ed20_i_censocartorio != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,18011,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_i_censocartorio')) . "','$this->ed20_i_censocartorio'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_i_zonaresidencia"]) || $this->ed20_i_zonaresidencia != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,18919,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_i_zonaresidencia')) . "','$this->ed20_i_zonaresidencia'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_tipoensinomedio"]) || $this->ed20_tipoensinomedio != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,1010421,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_tipoensinomedio')) . "','$this->ed20_tipoensinomedio'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_localizacaodiferenciada"]) || $this->ed20_localizacaodiferenciada != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,1010420,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_localizacaodiferenciada')) . "','$this->ed20_localizacaodiferenciada'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["ed20_paisresidencia"]) || $this->ed20_paisresidencia != "")
                        $resac = db_query("insert into db_acount values($acount,1010087,1010419,'" . AddSlashes(pg_result($resaco, $conresaco, 'ed20_paisresidencia')) . "','$this->ed20_paisresidencia'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Recursos Humanos não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->ed20_i_codigo;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Recursos Humanos não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->ed20_i_codigo;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $this->ed20_i_codigo;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($ed20_i_codigo = null, $dbwhere = null)
    {
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            if (empty($dbwhere)) {

                $resaco = $this->sql_record($this->sql_query_file($ed20_i_codigo));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
            }
            if (($resaco != false) || ($this->numrows != 0)) {

                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,1008513,'$ed20_i_codigo','E')");
                    $resac = db_query("insert into db_acount values($acount,1010087,1008513,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_i_codigo')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,13819,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_c_outroscursos')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,13818,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_c_posgraduacao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,13817,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_i_escolaridade')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,13816,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_i_censomunicender')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,13815,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_i_censoufender')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,13814,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_c_passaporte')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,13813,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_i_censoufcert')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,13812,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_c_certidaocart')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,13811,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_c_certidaodata')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,13810,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_c_certidaolivro')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,13809,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_c_certidaofolha')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,13808,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_c_certidaonum')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,13807,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_i_certidaotipo')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,13806,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_d_dataident')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,13805,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_i_censoufident')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,13804,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_i_censoorgemiss')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,13803,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_c_identcompl')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,13802,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_i_censomunicnat')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,13801,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_i_censoufnat')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,13800,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_i_nacionalidade')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,13799,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_i_raca')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,13798,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_c_nis')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,13797,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_i_codigoinep')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,13823,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_i_pais')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,17288,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_i_tiposervidor')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,17336,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_i_rhregime')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,17444,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_c_efetividade')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,18011,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_i_censocartorio')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,18919,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_i_zonaresidencia')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,1010421,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_tipoensinomedio')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,1010420,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_localizacaodiferenciada')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010087,1010419,'','" . AddSlashes(pg_result($resaco, $iresaco, 'ed20_paisresidencia')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }
        $sql = " delete from rechumano
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($ed20_i_codigo)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " ed20_i_codigo = $ed20_i_codigo ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Recursos Humanos não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $ed20_i_codigo;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Recursos Humanos não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $ed20_i_codigo;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $ed20_i_codigo;
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
            $this->erro_sql = "Record Vazio na Tabela:rechumano";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($ed20_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos}";
        $instit = db_getsession("DB_instit");
        $ano = db_anofolha();
        $mes = db_mesfolha();
        $sql .= "  from rechumano ";
        //se rh vem da rhpessoal
        $sql .= "      left join rechumanopessoal  on  rechumanopessoal.ed284_i_rechumano = rechumano.ed20_i_codigo";
        $sql .= "      left join rhpessoal  on  rhpessoal.rh01_regist = rechumanopessoal.ed284_i_rhpessoal";
        $sql .= "      left join cgm as cgmrh on  cgmrh.z01_numcgm = rhpessoal.rh01_numcgm";
        $sql .= "      left join db_config  on  db_config.codigo = rhpessoal.rh01_instit";
        $sql .= "      left join rhpessoalmov on rhpessoalmov.rh02_anousu  = $ano
                                          and rhpessoalmov.rh02_mesusu  = $mes
                                          and rhpessoalmov.rh02_regist  = rhpessoal.rh01_regist
                                          and rhpessoalmov.rh02_instit  = $instit";
        $sql .= "      left join rhregime as regimerh on  regimerh.rh30_codreg = rhpessoalmov.rh02_codreg";
        $sql .= "      left join rhpesdoc  on  rhpesdoc.rh16_regist = rhpessoal.rh01_regist";
        $sql .= "      left join rhlota  on  rhlota.r70_codigo = rhpessoal.rh01_lotac";
        $sql .= "      left join rhestcivil  on  rhestcivil.rh08_estciv = rhpessoal.rh01_estciv";
        $sql .= "      left join rhraca  on  rhraca.rh18_raca = rhpessoal.rh01_raca";
        $sql .= "      left join rhfuncao  on  rhfuncao.rh37_funcao = rhpessoal.rh01_funcao and  rhfuncao.rh37_instit = rhpessoal.rh01_instit";
        $sql .= "      left join rhinstrucao  on  rhinstrucao.rh21_instru = rhpessoal.rh01_instru";
        $sql .= "      left join rhnacionalidade  on  rhnacionalidade.rh06_nacionalidade = rhpessoal.rh01_nacion";
        //se rh vem direto do cgm
        $sql .= "      left join rechumanocgm  on  rechumanocgm.ed285_i_rechumano = rechumano.ed20_i_codigo";
        $sql .= "      left join cgm as cgmcgm on  cgmcgm.z01_numcgm = rechumanocgm.ed285_i_cgm";
        $sql .= "      left join cgmfisico on cgmfisico.z04_numcgm = rhpessoal.rh01_numcgm or cgmfisico.z04_numcgm = cgmcgm.z01_numcgm";
        $sql .= "      left join cgmdoc on  cgmdoc.z02_i_cgm = cgmcgm.z01_numcgm";
        $sql .= "      left join rhregime as regimecgm on  regimecgm.rh30_codreg = rechumano.ed20_i_rhregime";
        //demais ligações da rechumano
        $sql .= "      inner join pais  on  pais.ed228_i_codigo = rechumano.ed20_i_pais";
        $sql .= "      left  join censouf as censoufident on  censoufident.ed260_i_codigo = rechumano.ed20_i_censoufident";
        $sql .= "      left  join censouf as censoufnat on  censoufnat.ed260_i_codigo = rechumano.ed20_i_censoufnat";
        $sql .= "      left  join censouf as censoufcert on  censoufcert.ed260_i_codigo = rechumano.ed20_i_censoufcert";
        $sql .= "      left  join censouf as censoufender on  censoufender.ed260_i_codigo = rechumano.ed20_i_censoufender";
        $sql .= "      left  join censomunic as censomunicnat on  censomunicnat.ed261_i_codigo = rechumano.ed20_i_censomunicnat";
        $sql .= "      left  join censomunic as censomunicender on  censomunicender.ed261_i_codigo = rechumano.ed20_i_censomunicender";
        $sql .= "      left  join censoorgemissrg  on  censoorgemissrg.ed132_i_codigo = rechumano.ed20_i_censoorgemiss";
        $sql .= "      left  join censocartorio  on  censocartorio.ed291_i_codigo = rechumano.ed20_i_censocartorio";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($ed20_i_codigo)) {
                $sql2 .= " where rechumano.ed20_i_codigo = $ed20_i_codigo ";
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

    public function sql_query_file($ed20_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos} ";
        $sql .= "  from rechumano ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($ed20_i_codigo)) {
                $sql2 .= " where rechumano.ed20_i_codigo = $ed20_i_codigo ";
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

    public function sql_query_escola($ed20_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $instit = db_getsession("DB_instit");
        $ano = db_anofolha();
        $mes = db_mesfolha();
        $sql .= " from rechumano ";
        //se rh vem da rhpessoal
        $sql .= "      left join rechumanopessoal  on  rechumanopessoal.ed284_i_rechumano = rechumano.ed20_i_codigo";
        $sql .= "      left join rhpessoal  on  rhpessoal.rh01_regist = rechumanopessoal.ed284_i_rhpessoal";
        $sql .= "      left join cgm as cgmrh on  cgmrh.z01_numcgm = rhpessoal.rh01_numcgm";
        $sql .= "      left join db_config  on  db_config.codigo = rhpessoal.rh01_instit";
        $sql .= "      left join rhpessoalmov on rhpessoalmov.rh02_anousu  = $ano
                                          and rhpessoalmov.rh02_mesusu  = $mes
                                          and rhpessoalmov.rh02_regist  = rhpessoal.rh01_regist
                                          and rhpessoalmov.rh02_instit  = $instit";
        $sql .= "      left join rhregime as regimerh on  regimerh.rh30_codreg = rhpessoalmov.rh02_codreg";
        $sql .= "      left join rhlota  on  rhlota.r70_codigo = rhpessoal.rh01_lotac";
        $sql .= "      left join rhpesdoc  on  rhpesdoc.rh16_regist = rhpessoal.rh01_regist";
        $sql .= "      left join rhestcivil  on  rhestcivil.rh08_estciv = rhpessoal.rh01_estciv";
        $sql .= "      left join rhraca  on  rhraca.rh18_raca = rhpessoal.rh01_raca";
        $sql .= "      left join rhfuncao  on  rhfuncao.rh37_funcao = rhpessoal.rh01_funcao and rh37_instit  = rh02_instit";
        $sql .= "      left join rhinstrucao  on  rhinstrucao.rh21_instru = rhpessoal.rh01_instru";
        $sql .= "      left join rhnacionalidade  on  rhnacionalidade.rh06_nacionalidade = rhpessoal.rh01_nacion";
        //se rh vem direto do cgm
        $sql .= "      left join rechumanocgm  on  rechumanocgm.ed285_i_rechumano = rechumano.ed20_i_codigo";
        $sql .= "      left join cgm as cgmcgm on  cgmcgm.z01_numcgm = rechumanocgm.ed285_i_cgm";
        $sql .= "      left join cgmdoc on  cgmdoc.z02_i_cgm = cgmcgm.z01_numcgm";
        $sql .= "      left join rhregime as regimecgm on  regimecgm.rh30_codreg = rechumano.ed20_i_rhregime";
        $sql .= "      left join cgmfisico on cgmfisico.z04_numcgm = rhpessoal.rh01_numcgm and cgmfisico.z04_nomesocial <> ''";
        //demais ligações da rechumano
        $sql .= "      inner join rechumanoescola  on  rechumanoescola.ed75_i_rechumano = rechumano.ed20_i_codigo";
        $sql .= "      inner join escola  on  escola.ed18_i_codigo = rechumanoescola.ed75_i_escola";
        $sql .= "      left join relacaotrabalho  on  relacaotrabalho.ed23_i_rechumanoescola = rechumanoescola.ed75_i_codigo";
        $sql .= "      left join tipohoratrabalho on  tipohoratrabalho.ed128_codigo = ed23_tipohoratrabalho";
        $sql .= "      left join rechumanoativ  on  rechumanoativ.ed22_i_rechumanoescola = rechumanoescola.ed75_i_codigo";
        $sql .= "      left join atividaderh  on  atividaderh.ed01_i_codigo = rechumanoativ.ed22_i_atividade";
        $sql .= "      left join disciplina  on  disciplina.ed12_i_codigo = relacaotrabalho.ed23_i_disciplina";
        $sql .= "      left join caddisciplina on ed232_i_codigo= ed12_i_caddisciplina";
        $sql .= "      left join ensino  on  ensino.ed10_i_codigo = disciplina.ed12_i_ensino";
        $sql .= "      inner join pais  on  pais.ed228_i_codigo = rechumano.ed20_i_pais";
        $sql .= "      left  join censouf as censoufident on  censoufident.ed260_i_codigo = rechumano.ed20_i_censoufident";
        $sql .= "      left  join censouf as censoufnat on  censoufnat.ed260_i_codigo = rechumano.ed20_i_censoufnat";
        $sql .= "      left  join censouf as censoufcert on  censoufcert.ed260_i_codigo = rechumano.ed20_i_censoufcert";
        $sql .= "      left  join censouf as censoufender on  censoufender.ed260_i_codigo = rechumano.ed20_i_censoufender";
        $sql .= "      left  join censomunic as censomunicnat on  censomunicnat.ed261_i_codigo = rechumano.ed20_i_censomunicnat";
        $sql .= "      left  join censomunic as censomunicender on  censomunicender.ed261_i_codigo = rechumano.ed20_i_censomunicender";
        $sql .= "      left  join censoorgemissrg  on  censoorgemissrg.ed132_i_codigo = rechumano.ed20_i_censoorgemiss";
        $sql .= "      left  join censocartorio  on  censocartorio.ed291_i_codigo = rechumano.ed20_i_censocartorio";
        $sql .= "      left  join regimetrabalho on  regimetrabalho.ed24_i_codigo = relacaotrabalho.ed23_i_regimetrabalho";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($ed20_i_codigo != null) {
                $sql2 .= " where rechumano.ed20_i_codigo = $ed20_i_codigo ";
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

    public function sql_query_escola_rechumano($ed20_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $instit = db_getsession("DB_instit");
        $ano = db_anofolha();
        $mes = db_mesfolha();
        $sql .= " from rechumano ";
        //se rh vem da rhpessoal
        $sql .= "      left join rechumanopessoal  on  rechumanopessoal.ed284_i_rechumano = rechumano.ed20_i_codigo";
        $sql .= "      left join rhpessoal  on  rhpessoal.rh01_regist = rechumanopessoal.ed284_i_rhpessoal";
        $sql .= "      left join cgm as cgmrh on  cgmrh.z01_numcgm = rhpessoal.rh01_numcgm";
        $sql .= "      left join cgmfisico ON cgmfisico.z04_numcgm = cgmrh.z01_numcgm";
        $sql .= "      left join db_config  on  db_config.codigo = rhpessoal.rh01_instit";
        $sql .= "      left join rhpessoalmov on rhpessoalmov.rh02_anousu  = $ano
                                          and rhpessoalmov.rh02_mesusu  = $mes
                                          and rhpessoalmov.rh02_regist  = rhpessoal.rh01_regist
                                          and rhpessoalmov.rh02_instit  = $instit";
        $sql .= "      left join rhregime as regimerh on  regimerh.rh30_codreg = rhpessoalmov.rh02_codreg";
        $sql .= "      left join rhlota  on  rhlota.r70_codigo = rhpessoal.rh01_lotac";
        $sql .= "      left join rhpesdoc  on  rhpesdoc.rh16_regist = rhpessoal.rh01_regist";
        $sql .= "      left join rhestcivil  on  rhestcivil.rh08_estciv = rhpessoal.rh01_estciv";
        $sql .= "      left join rhraca  on  rhraca.rh18_raca = rhpessoal.rh01_raca";
        $sql .= "      left join rhfuncao  on  rhfuncao.rh37_funcao = rhpessoal.rh01_funcao and rh37_instit  = rh02_instit";
        $sql .= "      left join rhinstrucao  on  rhinstrucao.rh21_instru = rhpessoal.rh01_instru";
        $sql .= "      left join rhnacionalidade  on  rhnacionalidade.rh06_nacionalidade = rhpessoal.rh01_nacion";
        //se rh vem direto do cgm
        $sql .= "      left join rechumanocgm  on  rechumanocgm.ed285_i_rechumano = rechumano.ed20_i_codigo";
        $sql .= "      left join cgm as cgmcgm on  cgmcgm.z01_numcgm = rechumanocgm.ed285_i_cgm";
        $sql .= "      left join cgmdoc on  cgmdoc.z02_i_cgm = cgmcgm.z01_numcgm";
        $sql .= "      left join rhregime as regimecgm on  regimecgm.rh30_codreg = rechumano.ed20_i_rhregime";
        // $sql .= "      inner join rhregime on rhregime.rh30_codreg = rechumano.ed20_i_rhregime ";
        //demais ligações da rechumano
        $sql .= "      inner join rechumanoescola  on  rechumanoescola.ed75_i_rechumano = rechumano.ed20_i_codigo";
        $sql .= "      inner join escola  on  escola.ed18_i_codigo = rechumanoescola.ed75_i_escola";
        $sql .= "      left join relacaotrabalho  on  relacaotrabalho.ed23_i_rechumanoescola = rechumanoescola.ed75_i_codigo";

        $sql .= "      left join regimetrabalho ON relacaotrabalho.ed23_i_regimetrabalho = regimetrabalho.ed24_i_codigo";
        $sql .= "      left join tipohoratrabalho ON relacaotrabalho.ed23_tipohoratrabalho = tipohoratrabalho.ed128_codigo";

        $sql .= "      left join rechumanoativ  on  rechumanoativ.ed22_i_rechumanoescola = rechumanoescola.ed75_i_codigo";
        $sql .= "      left join atividaderh  on  atividaderh.ed01_i_codigo = rechumanoativ.ed22_i_atividade";
        $sql .= "      left join disciplina  on  disciplina.ed12_i_codigo = relacaotrabalho.ed23_i_disciplina";
        $sql .= "      left join caddisciplina on ed232_i_codigo= ed12_i_caddisciplina";
        $sql .= "      left join ensino  on  ensino.ed10_i_codigo = disciplina.ed12_i_ensino";
        $sql .= "      inner join pais  on  pais.ed228_i_codigo = rechumano.ed20_i_pais";
        $sql .= "      left  join censouf as censoufident on  censoufident.ed260_i_codigo = rechumano.ed20_i_censoufident";
        $sql .= "      left  join censouf as censoufnat on  censoufnat.ed260_i_codigo = rechumano.ed20_i_censoufnat";
        $sql .= "      left  join censouf as censoufcert on  censoufcert.ed260_i_codigo = rechumano.ed20_i_censoufcert";
        $sql .= "      left  join censouf as censoufender on  censoufender.ed260_i_codigo = rechumano.ed20_i_censoufender";
        $sql .= "      left  join censomunic as censomunicnat on  censomunicnat.ed261_i_codigo = rechumano.ed20_i_censomunicnat";
        $sql .= "      left  join censomunic as censomunicender on  censomunicender.ed261_i_codigo = rechumano.ed20_i_censomunicender";
        $sql .= "      left  join censoorgemissrg  on  censoorgemissrg.ed132_i_codigo = rechumano.ed20_i_censoorgemiss";
        $sql .= "      left  join censocartorio  on  censocartorio.ed291_i_codigo = rechumano.ed20_i_censocartorio";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($ed20_i_codigo != null) {
                $sql2 .= " where rechumano.ed20_i_codigo = $ed20_i_codigo ";
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

    public function sql_query_censo($ed20_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $instit = db_getsession("DB_instit");
        $ano = db_anofolha();
        $mes = db_mesfolha();
        $sql .= " from rechumano ";
        //se rh vem da rhpessoal
        $sql .= "      left join rechumanopessoal  on  rechumanopessoal.ed284_i_rechumano = rechumano.ed20_i_codigo";
        $sql .= "      left join rhpessoal  on  rhpessoal.rh01_regist = rechumanopessoal.ed284_i_rhpessoal";
        $sql .= "      left join cgm as cgmrh on  cgmrh.z01_numcgm = rhpessoal.rh01_numcgm";
        $sql .= "      left join db_config  on  db_config.codigo = rhpessoal.rh01_instit";
        $sql .= "      left join rhpessoalmov on rhpessoalmov.rh02_anousu  = $ano
                                          and rhpessoalmov.rh02_mesusu  = $mes
                                          and rhpessoalmov.rh02_regist  = rhpessoal.rh01_regist
                                          and rhpessoalmov.rh02_instit  = $instit";
        $sql .= "      left join rhregime as regimerh on  regimerh.rh30_codreg = rhpessoalmov.rh02_codreg";
        $sql .= "      left join rhlota  on  rhlota.r70_codigo = rhpessoal.rh01_lotac";
        $sql .= "      left join rhpesdoc  on  rhpesdoc.rh16_regist = rhpessoal.rh01_regist";
        $sql .= "      left join rhestcivil  on  rhestcivil.rh08_estciv = rhpessoal.rh01_estciv";
        $sql .= "      left join rhraca  on  rhraca.rh18_raca = rhpessoal.rh01_raca";
        $sql .= "      left join rhfuncao  on  rhfuncao.rh37_funcao = rhpessoal.rh01_funcao and rh37_instit  = rh02_instit";
        $sql .= "      left join rhinstrucao  on  rhinstrucao.rh21_instru = rhpessoal.rh01_instru";
        $sql .= "      left join rhnacionalidade  on  rhnacionalidade.rh06_nacionalidade = rhpessoal.rh01_nacion";
        //se rh vem direto do cgm
        $sql .= "      left join rechumanocgm  on  rechumanocgm.ed285_i_rechumano = rechumano.ed20_i_codigo";
        $sql .= "      left join cgm as cgmcgm on  cgmcgm.z01_numcgm = rechumanocgm.ed285_i_cgm";
        $sql .= "      left join cgmdoc on  cgmdoc.z02_i_cgm = cgmcgm.z01_numcgm";
        $sql .= "      left join rhregime as regimecgm on  regimecgm.rh30_codreg = rechumano.ed20_i_rhregime";
        $sql .= "      left join rhregime on rhregime.rh30_codreg = rechumano.ed20_i_rhregime ";
        //demais ligações da rechumano
        $sql .= "      inner join rechumanoescola  on  rechumanoescola.ed75_i_rechumano = rechumano.ed20_i_codigo";
        $sql .= "      inner join escola  on  escola.ed18_i_codigo = rechumanoescola.ed75_i_escola";
        $sql .= "      left join relacaotrabalho  on  relacaotrabalho.ed23_i_rechumanoescola = rechumanoescola.ed75_i_codigo";
        $sql .= "      left join rechumanoativ  on  rechumanoativ.ed22_i_rechumanoescola = rechumanoescola.ed75_i_codigo";
        $sql .= "      left join atividaderh  on  atividaderh.ed01_i_codigo = rechumanoativ.ed22_i_atividade";
        $sql .= "      left join disciplina  on  disciplina.ed12_i_codigo = relacaotrabalho.ed23_i_disciplina";
        $sql .= "      left join caddisciplina on ed232_i_codigo= ed12_i_caddisciplina";
        $sql .= "      left join ensino  on  ensino.ed10_i_codigo = disciplina.ed12_i_ensino";
        $sql .= "      inner join pais  on  pais.ed228_i_codigo = rechumano.ed20_i_pais";
        $sql .= "      left  join censouf as censoufident on  censoufident.ed260_i_codigo = rechumano.ed20_i_censoufident";
        $sql .= "      left  join censouf as censoufnat on  censoufnat.ed260_i_codigo = rechumano.ed20_i_censoufnat";
        $sql .= "      left  join censouf as censoufcert on  censoufcert.ed260_i_codigo = rechumano.ed20_i_censoufcert";
        $sql .= "      left  join censouf as censoufender on  censoufender.ed260_i_codigo = rechumano.ed20_i_censoufender";
        $sql .= "      left  join censomunic as censomunicnat on  censomunicnat.ed261_i_codigo = rechumano.ed20_i_censomunicnat";
        $sql .= "      left  join censomunic as censomunicender on  censomunicender.ed261_i_codigo = rechumano.ed20_i_censomunicender";
        $sql .= "      left  join censoorgemissrg  on  censoorgemissrg.ed132_i_codigo = rechumano.ed20_i_censoorgemiss";
        $sql .= "      left  join censocartorio  on  censocartorio.ed291_i_codigo = rechumano.ed20_i_censocartorio";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($ed20_i_codigo != null) {
                $sql2 .= " where rechumano.ed20_i_codigo = $ed20_i_codigo ";
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

    public function sql_query_rechumano($iCodigo = null, $sCampos = '*', $sOrdem = null, $sDbWhere = '')
    {

        $sSql = 'select ';
        if ($sCampos != '*') {

            $sCamposSql = explode('#', $sCampos);
            $sVirgula = '';
            for ($iCont = 0; $iCont < sizeof($sCamposSql); $iCont++) {

                $sSql .= $sVirgula . $sCamposSql[$iCont];
                $virgula = ",";

            }

        } else {
            $sSql .= $sCampos;
        }
        $sSql .= ' from rechumano ';
        $sSql .= '   inner join ((select cgm.*, rhpesdoc.rh16_ctps_n::varchar as ctps_num, ';
        $sSql .= '                      rhpesdoc.rh16_ctps_s::varchar as ctps_serie, rhpesdoc.rh16_ctps_uf as ctps_uf, ';
        $sSql .= '                       rhpesdoc.rh16_pis as pis, rechumanopessoal.ed284_i_rechumano as rechumano,';
        $sSql .= '                       1 as tipo ';
        $sSql .= '                  from rechumano as a';
        $sSql .= '                    inner join rechumanopessoal on rechumanopessoal.ed284_i_rechumano = a.ed20_i_codigo ';
        $sSql .= '                    inner join rhpessoal on rhpessoal.rh01_regist = rechumanopessoal.ed284_i_rhpessoal';
        $sSql .= '                    inner join cgm on cgm.z01_numcgm = rhpessoal.rh01_numcgm';
        $sSql .= '                    left join rhpesdoc on rhpesdoc.rh16_regist = rhpessoal.rh01_regist ';
        $sSql .= '                      where rechumanopessoal.ed284_i_rechumano = a.ed20_i_codigo)';
        $sSql .= '                                      union ';
        $sSql .= '               (select cgm.*, cgmdoc.z02_c_ctpsnum::varchar, cgmdoc.z02_c_ctpsserie::varchar, ';
        $sSql .= '                       cgmdoc.z02_c_ctpsuf, cgmdoc.z02_i_pis::varchar,';
        $sSql .= '                       rechumanocgm.ed285_i_rechumano as rechumano, 2 as tipo ';
        $sSql .= '                 from rechumano as a';
        $sSql .= '                   inner join rechumanocgm on rechumanocgm.ed285_i_rechumano = a.ed20_i_codigo ';
        $sSql .= '                   inner join cgm on cgm.z01_numcgm = rechumanocgm.ed285_i_cgm';
        $sSql .= '                   left  join cgmdoc on cgmdoc.z02_i_cgm = cgm.z01_numcgm ';
        $sSql .= '                     where rechumanocgm.ed285_i_rechumano = a.ed20_i_codigo)) as reccgm';
        $sSql .= '     on reccgm.rechumano = rechumano.ed20_i_codigo ';
        $sSql .= '   inner join pais  on  pais.ed228_i_codigo = rechumano.ed20_i_pais';
        $sSql .= '   left join rhregime as regimecgm on regimecgm.rh30_codreg = rechumano.ed20_i_rhregime';
        $sSql .= '   left join censouf as censoufident on censoufident.ed260_i_codigo = rechumano.ed20_i_censoufident';
        $sSql .= '   left join censouf as censoufnat on censoufnat.ed260_i_codigo = rechumano.ed20_i_censoufnat';
        $sSql .= '   left join censouf as censoufcert on censoufcert.ed260_i_codigo = rechumano.ed20_i_censoufcert';
        $sSql .= '   left join censouf as censoufender on censoufender.ed260_i_codigo = rechumano.ed20_i_censoufender';
        $sSql .= '   left join censomunic as censomunicnat on censomunicnat.ed261_i_codigo = rechumano.ed20_i_censomunicnat';
        $sSql .= '   left join censomunic as censomunicender on censomunicender.ed261_i_codigo = rechumano.ed20_i_censomunicender';
        $sSql .= '   left join censoorgemissrg on censoorgemissrg.ed132_i_codigo = rechumano.ed20_i_censoorgemiss';
        $sSql .= '   left join censocartorio on censocartorio.ed291_i_codigo = rechumano.ed20_i_censocartorio';
        $sSql2 = '';
        if ($sDbWhere == '') {

            if ($iCodigo != null) {
                $sSql2 .= " where rechumano.ed20_i_codigo = $iCodigo ";
            }

        } elseif ($sDbWhere != '') {
            $sSql2 = " where $sDbWhere";
        }
        $sSql .= $sSql2;

        if ($sOrdem != null) {

            $sSql .= ' order by ';
            $sCamposSql = explode('#', $sOrdem);
            $sVirgula = '';
            for ($iCont = 0; $iCont < sizeof($sCamposSql); $iCont++) {

                $sSql .= $sVirgula . $sCamposSql[$iCont];
                $sVirgula = ',';

            }

        }

        return $sSql;

    }

    public function sql_query_censomodel($iCodigo = null, $sCampos = '*', $sOrdem = null, $sDbWhere = '')
    {

        $sSql = 'select ';
        if ($sCampos != '*') {

            $sCamposSql = explode('#', $sCampos);
            $sVirgula = '';
            for ($iCont = 0; $iCont < sizeof($sCamposSql); $iCont++) {

                $sSql .= $sVirgula . $sCamposSql[$iCont];
                $virgula = ",";

            }

        } else {
            $sSql .= $sCampos;
        }
        $sSql .= " from rechumano ";
        $sSql .= "      left join rechumanopessoal  on  rechumanopessoal.ed284_i_rechumano = rechumano.ed20_i_codigo ";
        $sSql .= "      left join rhpessoal  on  rhpessoal.rh01_regist = rechumanopessoal.ed284_i_rhpessoal ";
        $sSql .= "      left join cgm as cgmrh on  cgmrh.z01_numcgm = rhpessoal.rh01_numcgm ";
        $sSql .= "      left join rechumanocgm  on  rechumanocgm.ed285_i_rechumano = rechumano.ed20_i_codigo ";
        $sSql .= "      left join cgm as cgmcgm on  cgmcgm.z01_numcgm = rechumanocgm.ed285_i_cgm ";
        $sSql .= "      inner join rechumanoescola on ed75_i_rechumano = ed20_i_codigo ";
        $sSql .= "      inner join escola on ed18_i_codigo = ed75_i_escola ";
        $sSql .= "      inner join pais  on  pais.ed228_i_codigo = rechumano.ed20_i_pais";
        $sSql2 = '';
        if ($sDbWhere == '') {

            if ($iCodigo != null) {
                $sSql2 .= " where rechumano.ed20_i_codigo = $iCodigo ";
            }

        } elseif ($sDbWhere != '') {
            $sSql2 = " where $sDbWhere";
        }
        $sSql .= $sSql2;

        if ($sOrdem != null) {

            $sSql .= ' order by ';
            $sCamposSql = explode('#', $sOrdem);
            $sVirgula = '';
            for ($iCont = 0; $iCont < sizeof($sCamposSql); $iCont++) {

                $sSql .= $sVirgula . $sCamposSql[$iCont];
                $sVirgula = ',';

            }

        }

        return $sSql;

    }

    public function sql_query_atolegal($ed20_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $instit = db_getsession("DB_instit");
        $ano = db_anofolha();
        $mes = db_mesfolha();
        $sql .= " from rechumano ";
        //se rh vem da rhpessoal
        $sql .= "      left join rechumanopessoal  on  rechumanopessoal.ed284_i_rechumano = rechumano.ed20_i_codigo";
        $sql .= "      left join rhpessoal  on  rhpessoal.rh01_regist = rechumanopessoal.ed284_i_rhpessoal";
        $sql .= "      left join cgm as cgmrh on  cgmrh.z01_numcgm = rhpessoal.rh01_numcgm";
        $sql .= "      left join db_config  on  db_config.codigo = rhpessoal.rh01_instit";
        $sql .= "      left join rhpessoalmov on rhpessoalmov.rh02_anousu  = $ano
                                          and rhpessoalmov.rh02_mesusu  = $mes
                                          and rhpessoalmov.rh02_regist  = rhpessoal.rh01_regist
                                          and rhpessoalmov.rh02_instit  = $instit";
        $sql .= "      left join rhregime as regimerh on  regimerh.rh30_codreg = rhpessoalmov.rh02_codreg";
        $sql .= "      left join rhlota  on  rhlota.r70_codigo = rhpessoal.rh01_lotac";
        $sql .= "      left join rhpesdoc  on  rhpesdoc.rh16_regist = rhpessoal.rh01_regist";
        $sql .= "      left join rhestcivil  on  rhestcivil.rh08_estciv = rhpessoal.rh01_estciv";
        $sql .= "      left join rhraca  on  rhraca.rh18_raca = rhpessoal.rh01_raca";
        $sql .= "      left join rhfuncao  on  rhfuncao.rh37_funcao = rhpessoal.rh01_funcao and rh37_instit  = rh02_instit";
        $sql .= "      left join rhinstrucao  on  rhinstrucao.rh21_instru = rhpessoal.rh01_instru";
        $sql .= "      left join rhnacionalidade  on  rhnacionalidade.rh06_nacionalidade = rhpessoal.rh01_nacion";
        //se rh vem direto do cgm
        $sql .= "      left join rechumanocgm  on  rechumanocgm.ed285_i_rechumano = rechumano.ed20_i_codigo";
        $sql .= "      left join cgm as cgmcgm on  cgmcgm.z01_numcgm = rechumanocgm.ed285_i_cgm";
        $sql .= "      left join cgmdoc on  cgmdoc.z02_i_cgm = cgmcgm.z01_numcgm";
        $sql .= "      left join rhregime as regimecgm on  regimecgm.rh30_codreg = rechumano.ed20_i_rhregime";
        // $sql .= "      inner join rhregime on rhregime.rh30_codreg = rechumano.ed20_i_rhregime ";
        //demais ligações da rechumano
        $sql .= "      inner join rechumanoescola  on  rechumanoescola.ed75_i_rechumano = rechumano.ed20_i_codigo";
        $sql .= "      inner join escola  on  escola.ed18_i_codigo = rechumanoescola.ed75_i_escola";
        $sql .= "      left join relacaotrabalho  on  relacaotrabalho.ed23_i_rechumanoescola = rechumanoescola.ed75_i_codigo";
        $sql .= "      left join rechumanoativ  on  rechumanoativ.ed22_i_rechumanoescola = rechumanoescola.ed75_i_codigo";
        $sql .= "      left join atolegal on atolegal.ed05_i_codigo = rechumanoativ.ed22_i_atolegal";
        $sql .= "      left join atividaderh  on  atividaderh.ed01_i_codigo = rechumanoativ.ed22_i_atividade";
        $sql .= "      left join disciplina  on  disciplina.ed12_i_codigo = relacaotrabalho.ed23_i_disciplina";
        $sql .= "      left join caddisciplina on ed232_i_codigo= ed12_i_caddisciplina";
        $sql .= "      left join ensino  on  ensino.ed10_i_codigo = disciplina.ed12_i_ensino";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($ed20_i_codigo != null) {
                $sql2 .= " where rechumano.ed20_i_codigo = $ed20_i_codigo ";
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


    public function sql_query_relatorio($iCodigo = null, $sCampos = '*', $sOrdem = null, $sDbWhere = '')
    {

        $sSql = 'select ';
        if ($sCampos != '*') {

            $sCamposSql = explode('#', $sCampos);
            $sVirgula = '';
            for ($iCont = 0; $iCont < sizeof($sCamposSql); $iCont++) {

                $sSql .= $sVirgula . $sCamposSql[$iCont];
                $virgula = ",";

            }

        } else {
            $sSql .= $sCampos;
        }
        $instit = db_getsession("DB_instit");
        $ano = db_anofolha();
        $mes = db_mesfolha();
        $sSql .= " from rechumano ";
        $sSql .= "      inner join rechumanoescola on ed75_i_rechumano = ed20_i_codigo";
        $sSql .= "      left join rechumanoativ on ed22_i_rechumanoescola = ed75_i_codigo";
        $sSql .= "      left join atividaderh on ed01_i_codigo = ed22_i_atividade";
        $sSql .= "      left join rechumanopessoal  on  rechumanopessoal.ed284_i_rechumano = rechumano.ed20_i_codigo";
        $sSql .= "      left join rhpessoal  on  rhpessoal.rh01_regist = rechumanopessoal.ed284_i_rhpessoal";
        $sSql .= "      left join rhpessoalmov on rhpessoalmov.rh02_anousu  = $ano";
        $sSql .= "                              and rhpessoalmov.rh02_mesusu  = $mes";
        $sSql .= "                              and rhpessoalmov.rh02_regist  = rhpessoal.rh01_regist";
        $sSql .= "                              and rhpessoalmov.rh02_instit  = $instit";
        $sSql .= "      left join rhregime as regimerh on  regimerh.rh30_codreg = rhpessoalmov.rh02_codreg";
        $sSql .= "      left join cgm as cgmrh on  cgmrh.z01_numcgm = rhpessoal.rh01_numcgm";
        $sSql .= "      left join rechumanocgm  on  rechumanocgm.ed285_i_rechumano = rechumano.ed20_i_codigo";
        $sSql .= "      left join cgm as cgmcgm on  cgmcgm.z01_numcgm = rechumanocgm.ed285_i_cgm";
        $sSql .= "      left join rhregime as regimecgm on  regimecgm.rh30_codreg = rechumano.ed20_i_rhregime";
        $sSql2 = '';
        if ($sDbWhere == '') {

            if ($iCodigo != null) {
                $sSql2 .= " where rechumano.ed20_i_codigo = $iCodigo ";
            }

        } elseif ($sDbWhere != '') {
            $sSql2 = " where $sDbWhere";
        }
        $sSql .= $sSql2;

        if ($sOrdem != null) {

            $sSql .= ' order by ';
            $sCamposSql = explode('#', $sOrdem);
            $sVirgula = '';
            for ($iCont = 0; $iCont < sizeof($sCamposSql); $iCont++) {

                $sSql .= $sVirgula . $sCamposSql[$iCont];
                $sVirgula = ',';

            }

        }

        return $sSql;

    }

    /*
     *Query utilizada na rotina de solicitação de informações sem codigo inep.
     *@date 29/02/2012
     */

    public function sql_query_solicitaseminep($iCodigo = null, $sCampos = '*', $sOrdem = null, $sDbWhere = '')
    {
        $sSql = 'select ';
        if ($sCampos != '*') {

            $sCamposSql = explode('#', $sCampos);
            $sVirgula = '';
            for ($iCont = 0; $iCont < sizeof($sCamposSql); $iCont++) {

                $sSql .= $sVirgula . $sCamposSql[$iCont];
                $virgula = ",";

            }

        } else {
            $sSql .= $sCampos;
        }
        $sSql .= ' from rechumano ';
        $sSql .= '   inner join ((select cgm.*, rhpesdoc.rh16_ctps_n::varchar as ctps_num, ';
        $sSql .= '                      rhpesdoc.rh16_ctps_s::varchar as ctps_serie, rhpesdoc.rh16_ctps_uf as ctps_uf, ';
        $sSql .= '                       rhpesdoc.rh16_pis as pis, rechumanopessoal.ed284_i_rechumano as rechumano,';
        $sSql .= '                       1 as tipo ';
        $sSql .= '                  from rechumano as a';
        $sSql .= '                    inner join rechumanopessoal on rechumanopessoal.ed284_i_rechumano = a.ed20_i_codigo ';
        $sSql .= '                    inner join rhpessoal on rhpessoal.rh01_regist = rechumanopessoal.ed284_i_rhpessoal';
        $sSql .= '                    inner join cgm on cgm.z01_numcgm = rhpessoal.rh01_numcgm';
        $sSql .= '                    left join rhpesdoc on rhpesdoc.rh16_regist = rhpessoal.rh01_regist ';
        $sSql .= '                      where rechumanopessoal.ed284_i_rechumano = a.ed20_i_codigo)';
        $sSql .= '                                      union ';
        $sSql .= '               (select cgm.*, cgmdoc.z02_c_ctpsnum::varchar, cgmdoc.z02_c_ctpsserie::varchar, ';
        $sSql .= '                       cgmdoc.z02_c_ctpsuf, cgmdoc.z02_i_pis::varchar,';
        $sSql .= '                       rechumanocgm.ed285_i_rechumano as rechumano, 2 as tipo ';
        $sSql .= '                 from rechumano as a';
        $sSql .= '                   inner join rechumanocgm on rechumanocgm.ed285_i_rechumano = a.ed20_i_codigo ';
        $sSql .= '                   inner join cgm on cgm.z01_numcgm = rechumanocgm.ed285_i_cgm';
        $sSql .= '                   left  join cgmdoc on cgmdoc.z02_i_cgm = cgm.z01_numcgm ';
        $sSql .= '                     where rechumanocgm.ed285_i_rechumano = a.ed20_i_codigo';
        $sSql .= '                          )) as reccgm';
        $sSql .= '                             on reccgm.rechumano = rechumano.ed20_i_codigo ';
        $sSql .= '   inner join rechumanoescola on ed20_i_codigo = ed75_i_rechumano';
        $sSql .= '   left join pais  on  pais.ed228_i_codigo = rechumano.ed20_i_pais';
        $sSql .= '   left join rhregime as regimecgm on regimecgm.rh30_codreg = rechumano.ed20_i_rhregime';
        $sSql .= '   left join censouf as censoufident on censoufident.ed260_i_codigo = rechumano.ed20_i_censoufident';
        $sSql .= '   left join censouf as censoufnat on censoufnat.ed260_i_codigo = rechumano.ed20_i_censoufnat';
        $sSql .= '   left join censouf as censoufcert on censoufcert.ed260_i_codigo = rechumano.ed20_i_censoufcert';
        $sSql .= '   left join censouf as censoufender on censoufender.ed260_i_codigo = rechumano.ed20_i_censoufender';
        $sSql .= '   left join censomunic as censomunicnat on censomunicnat.ed261_i_codigo = rechumano.ed20_i_censomunicnat';
        $sSql .= '   left join censomunic as censomunicender on censomunicender.ed261_i_codigo = rechumano.ed20_i_censomunicender';
        $sSql .= '   inner join escola on escola.ed18_i_codigo =  rechumanoescola.ed75_i_escola';
        $sSql .= '   inner join calendarioescola on calendarioescola.ed38_i_escola = escola.ed18_i_codigo';
        $sSql .= '   inner join calendario on calendario.ed52_i_codigo = calendarioescola.ed38_i_calendario';
        $sSql .= '   left join censoorgemissrg on censoorgemissrg.ed132_i_codigo = rechumano.ed20_i_censoorgemiss';
        $sSql .= '   left join censocartorio on censocartorio.ed291_i_codigo = rechumano.ed20_i_censocartorio';
        $sSql .= '   inner join rechumanoativ   on ed22_i_rechumanoescola = ed75_i_codigo';
        $sSql .= '   inner join atividaderh     on ed22_i_atividade       = ed01_i_codigo';
        $sSql2 = '';

        if ($sDbWhere == '') {

            if ($iCodigo != null) {
                $sSql2 .= " where rechumano.ed20_i_codigo = $iCodigo ";
            }

        } elseif ($sDbWhere != '') {
            $sSql2 = " where $sDbWhere";
        }
        $sSql .= $sSql2;
        if ($sOrdem != null) {

            $sSql .= ' order by ';
            $sCamposSql = explode('#', $sOrdem);
            $sVirgula = '';
            for ($iCont = 0; $iCont < sizeof($sCamposSql); $iCont++) {

                $sSql .= $sVirgula . $sCamposSql[$iCont];
                $sVirgula = ',';

            }

        }

        return $sSql;
    }

    public function sql_query_rechumano_cgm($ed20_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= "  from rechumano ";
        $sql .= " inner join rechumanoescola on rechumanoescola.ed75_i_rechumano = rechumano.ed20_i_codigo ";
        /**
         * Busca identificar o cgm do professor
         */
        $sql .= " left join rechumanopessoal on rechumanopessoal.ed284_i_rechumano = rechumano.ed20_i_codigo            ";
        $sql .= " left join rhpessoal        on rhpessoal.rh01_regist              = rechumanopessoal.ed284_i_rhpessoal ";
        $sql .= " left join cgm as cgmrh     on cgmrh.z01_numcgm                   = rhpessoal.rh01_numcgm              ";
        $sql .= " left join rechumanocgm     on rechumanocgm.ed285_i_rechumano     = rechumano.ed20_i_codigo            ";
        $sql .= " left join cgm as cgmcgm    on cgmcgm.z01_numcgm                  = rechumanocgm.ed285_i_cgm           ";
        $sql2 = "";
        if ($dbwhere == "") {

            if ($ed20_i_codigo != null) {
                $sql2 .= " where rechumano.ed20_i_codigo = $ed20_i_codigo ";
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

    public function sql_query_consulta_rechumano($iCgm)
    {
        $sSql = " select ed20_i_codigo as rechumano                                                                       ";
        $sSql .= "   from rechumano                                                                                        ";
        $sSql .= "  inner join rechumanopessoal on rechumanopessoal.ed284_i_rechumano = rechumano.ed20_i_codigo            ";
        $sSql .= "  inner join rhpessoal        on rhpessoal.rh01_regist              = rechumanopessoal.ed284_i_rhpessoal ";
        $sSql .= "  inner join cgm              on cgm.z01_numcgm                     = rhpessoal.rh01_numcgm              ";
        $sSql .= "  where rhpessoal.rh01_numcgm = {$iCgm}                                                                  ";
        $sSql .= "                                                                                                         ";
        $sSql .= " union all                                                                                               ";
        $sSql .= "                                                                                                         ";
        $sSql .= " select ed20_i_codigo as rechumano                                                                       ";
        $sSql .= "   from rechumano                                                                                        ";
        $sSql .= "  inner join rechumanocgm  on rechumanocgm.ed285_i_rechumano     = rechumano.ed20_i_codigo               ";
        $sSql .= "  inner join cgm              on cgm.z01_numcgm                     = rechumanocgm.ed285_i_cgm           ";
        $sSql .= "  where rechumanocgm.ed285_i_cgm = {$iCgm}                                                               ";
        return $sSql;
    }


    public function sql_query_movimentacao_professor_cgm($iCgm)
    {

        $sSql = " select ed321_sequencial as codigo, ed20_i_codigo as rechumano, trim(z01_nome) as z01_nome , 'A'::varchar as tipo   ";
        $sSql .= "   from docenteausencia                                                                                  ";
        $sSql .= "  inner join rechumano        on rechumano.ed20_i_codigo            = docenteausencia.ed321_rechumano    ";
        $sSql .= "  inner join rechumanopessoal on rechumanopessoal.ed284_i_rechumano = rechumano.ed20_i_codigo            ";
        $sSql .= "  inner join rhpessoal        on rhpessoal.rh01_regist              = rechumanopessoal.ed284_i_rhpessoal ";
        $sSql .= "  inner join cgm              on cgm.z01_numcgm                     = rhpessoal.rh01_numcgm              ";
        $sSql .= "  where rhpessoal.rh01_numcgm = {$iCgm}                                                             ";
        $sSql .= "                                                                                                         ";
        $sSql .= " union all                                                                                               ";
        $sSql .= "                                                                                                         ";
        $sSql .= " select ed321_sequencial as codigo, ed20_i_codigo as rechumano, trim(z01_nome) as z01_nome, 'A'::varchar as tipo    ";
        $sSql .= "   from docenteausencia                                                                                  ";
        $sSql .= "  inner join rechumano     on rechumano.ed20_i_codigo            = docenteausencia.ed321_rechumano       ";
        $sSql .= "  inner join rechumanocgm  on rechumanocgm.ed285_i_rechumano     = rechumano.ed20_i_codigo               ";
        $sSql .= "  inner join cgm              on cgm.z01_numcgm                     = rechumanocgm.ed285_i_cgm           ";
        $sSql .= "  where rechumanocgm.ed285_i_cgm = {$iCgm}                                                          ";
        $sSql .= "                                                                                                         ";
        $sSql .= " union all                                                                                               ";
        $sSql .= "                                                                                                         ";
        $sSql .= " select ed322_sequencial as codigo, ed20_i_codigo as rechumano, trim(z01_nome) as z01_nome , 'S'::varchar as tipo   ";
        $sSql .= "   from docentesubstituto                                                                                ";
        $sSql .= "  inner join rechumano        on rechumano.ed20_i_codigo            = docentesubstituto.ed322_rechumano  ";
        $sSql .= "  inner join rechumanopessoal on rechumanopessoal.ed284_i_rechumano = rechumano.ed20_i_codigo            ";
        $sSql .= "  inner join rhpessoal        on rhpessoal.rh01_regist              = rechumanopessoal.ed284_i_rhpessoal ";
        $sSql .= "  inner join cgm              on cgm.z01_numcgm                     = rhpessoal.rh01_numcgm              ";
        $sSql .= "  where rhpessoal.rh01_numcgm = {$iCgm}                                                             ";
        $sSql .= "                                                                                                         ";
        $sSql .= " union all                                                                                               ";
        $sSql .= "                                                                                                         ";
        $sSql .= " select ed322_sequencial as codigo, ed20_i_codigo as rechumano, trim(z01_nome) as z01_nome, 'S'::varchar as tipo    ";
        $sSql .= "   from docentesubstituto                                                                                  ";
        $sSql .= "  inner join rechumano     on rechumano.ed20_i_codigo            = docentesubstituto.ed322_rechumano     ";
        $sSql .= "  inner join rechumanocgm  on rechumanocgm.ed285_i_rechumano     = rechumano.ed20_i_codigo               ";
        $sSql .= "  inner join cgm           on cgm.z01_numcgm                     = rechumanocgm.ed285_i_cgm              ";
        $sSql .= "  where rechumanocgm.ed285_i_cgm = {$iCgm}                                                          ";

        return $sSql;
    }

    public function sql_query_censo_2015($ed20_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
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

        $sql .= " from rechumano ";
        $sql .= "      left  join rechumanopessoal on rechumanopessoal.ed284_i_rechumano   = rechumano.ed20_i_codigo";
        $sql .= "      left  join rhpessoal        on rhpessoal.rh01_regist                = rechumanopessoal.ed284_i_rhpessoal";
        $sql .= "      left  join cgm as cgmrh     on cgmrh.z01_numcgm                     = rhpessoal.rh01_numcgm";
        $sql .= "      left  join rechumanocgm     on rechumanocgm.ed285_i_rechumano       = rechumano.ed20_i_codigo";
        $sql .= "      left  join cgm as cgmcgm    on cgmcgm.z01_numcgm                    = rechumanocgm.ed285_i_cgm";
        $sql .= "      inner join rechumanoescola  on rechumanoescola.ed75_i_rechumano     = rechumano.ed20_i_codigo";
        $sql .= "      inner join escola           on escola.ed18_i_codigo                 = rechumanoescola.ed75_i_escola";
        $sql .= "      left  join rechumanoativ    on rechumanoativ.ed22_i_rechumanoescola = rechumanoescola.ed75_i_codigo";
        $sql .= "      left  join atividaderh      on atividaderh.ed01_i_codigo            = rechumanoativ.ed22_i_atividade";
        $sql .= "      left  join docenteausencia  on docenteausencia.ed321_rechumano      = rechumano.ed20_i_codigo";
        $sql .= "                                 and docenteausencia.ed321_escola         = rechumanoescola.ed75_i_escola";
        $sql2 = "";

        if ($dbwhere == "") {

            if ($ed20_i_codigo != null) {
                $sql2 .= " where rechumano.ed20_i_codigo = $ed20_i_codigo ";
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

    public function sql_query_servidor($ed20_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
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

        $sql .= " from rechumano ";
        $sql .= "      inner join rechumanopessoal on rechumanopessoal.ed284_i_rechumano = rechumano.ed20_i_codigo";
        $sql .= "      inner join rhpessoal        on rhpessoal.rh01_regist              = rechumanopessoal.ed284_i_rhpessoal";
        $sql .= "      inner join rhpessoalmov     on rhpessoalmov.rh02_regist           = rhpessoal.rh01_regist";
        $sql .= "      inner join rhfuncao         on rhfuncao.rh37_funcao               = rhpessoalmov.rh02_funcao";
        $sql .= "                                 and rhfuncao.rh37_instit               = rhpessoalmov.rh02_instit";
        $sql .= "      left  join rhpescargo       on rhpescargo.rh20_seqpes             = rhpessoalmov.rh02_seqpes";
        $sql .= "      left  join rhcargo          on rhcargo.rh04_codigo                = rhpescargo.rh20_cargo";
        $sql2 = "";

        if ($dbwhere == "") {

            if ($ed20_i_codigo != null) {
                $sql2 .= " where rechumano.ed20_i_codigo = $ed20_i_codigo ";
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

    public function sqlProfissionaisEscola($campos = '*', $escola, $whereProfissionaisAtivos = array(), $where = array())
    {
        $whereProfissionaisAtivos[] = "ed75_i_escola = {$escola}";
        $whereProfissionaisAtivos = implode(" and ", $whereProfissionaisAtivos);

        $sql = "
        with profissionais_ativos as (
          select *
          from rechumano
          join rechumanoescola on rechumanoescola.ed75_i_rechumano = rechumano.ed20_i_codigo
          where {$whereProfissionaisAtivos}
        ), origem_profissinal as (
          select
                  case when ed285_i_cgm is null then rh01_numcgm else ed285_i_cgm end as cgm
                 ,rh01_regist as matricula
                 ,profissionais_ativos.*
          from profissionais_ativos
               left join rechumanocgm on rechumanocgm.ed285_i_rechumano = profissionais_ativos.ed20_i_codigo
               left join rechumanopessoal on rechumanopessoal.ed284_i_rechumano = ed20_i_codigo
               left join rhpessoal on rhpessoal.rh01_regist = rechumanopessoal.ed284_i_rhpessoal
        ) select *,
                 exists(select 1 from escolagestorcenso
                    where ed325_rechumano = ed20_i_codigo
                        and ed325_escola = ed75_i_escola) as gestor,
                 (select ed325_email
                    from escolagestorcenso
                   where ed325_rechumano = ed20_i_codigo and ed325_escola = ed75_i_escola
                 ) as gestor_email
            from origem_profissinal
        ";
        if ($where) {
            $sql .= "where " . implode(' and ', $where);
        }

        return $sql;
    }

    /**
     * @param array $where
     * @return string
     */
    public function sqlProfissionais(array $where)
    {
        $sql = "
        SELECT rechumano.*,
               rechumanoescola.*,
               case when ed285_i_cgm is null then rh01_numcgm else ed285_i_cgm end as cgm,
               rh01_regist as matricula,
               exists(
                select 1 from escolagestorcenso
                 where escolagestorcenso.ed325_rechumano = rechumanoescola.ed75_i_rechumano
                   and escolagestorcenso.ed325_escola = rechumanoescola.ed75_i_escola
                 limit 1
               ) as gestor,
               (select ed325_email
                  from escolagestorcenso
                 where escolagestorcenso.ed325_rechumano = rechumanoescola.ed75_i_rechumano
                   and escolagestorcenso.ed325_escola = rechumanoescola.ed75_i_escola
                 ) as gestor_email
          FROM rechumano
          join rechumanoescola on rechumanoescola.ed75_i_rechumano = rechumano.ed20_i_codigo
          left JOIN rechumanopessoal ON rechumanopessoal.ed284_i_rechumano = rechumano.ed20_i_codigo
          left JOIN rhpessoal ON rhpessoal.rh01_regist = rechumanopessoal.ed284_i_rhpessoal
          left JOIN cgm as rh_cgm ON rh_cgm.z01_numcgm = rhpessoal.rh01_numcgm
          left JOIN rechumanocgm ON rechumanocgm.ed285_i_rechumano = rechumano.ed20_i_codigo
          left JOIN cgm as rec_cgm ON rec_cgm.z01_numcgm = rechumanocgm.ed285_i_cgm
        ";

        if (count($where)) {
            $sql .= " where " . implode(' and ', $where);
        }

        return $sql;
    }

    /**
     * Retorna todas as formações acadêmicas a partir do número da matrícula
     * com 'codigo', 'descricao', 'tipoensinomedio', 'nomecursofaculdade', 'nomepos'.
     * 
     * Esse campo é usado no plugin Efetividade Relatório, por favor não apagar.
     * 
     * @param int $matricula número da matricula do profissional.
     * @return string sql
     */
    public function sql_query_formacao_professor_matricula($matricula)
    {
        $sql = "
        select distinct
            rh01_instru as codigo,
            rh21_descr as descricao,
            case
                when (ed20_tipoensinomedio is null) then 0
                else ed20_tipoensinomedio
            end	as tipoensinomedio,
            case
                when (ed94_c_descr is null) then ''
                else ed94_c_descr
            end as nomecursofaculdade,
            case 
                when (ed183_nomecurso is null) then ''
                else ed183_nomecurso
            end as nomepos
        from
            pessoal.rhpessoal
        join pessoal.rhinstrucao on 
            rh01_instru = rh21_instru
        left join escola.turmas_regentes on
            pessoal.rhpessoal.rh01_numcgm = escola.turmas_regentes.cgm
        left join escola.rechumano on
            escola.turmas_regentes.codigo_rechumano = escola.rechumano.ed20_i_codigo
        left join escola.formacao on
            escola.turmas_regentes.codigo_rechumano = ed27_i_rechumano
        left join escola.cursoformacao on
            ed27_i_cursoformacao = ed94_i_codigo
        left join escola.rhformacaosuperior on
            pessoal.rhpessoal.rh01_numcgm = ed183_cgm
        where
            rh01_regist = {$matricula};
        ";
        return $sql;
    }
}
