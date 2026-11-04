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

class cl_cfiptu
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
    public $j18_anousu = 0;
    public $j18_vlrref = 0;
    public $j18_dtoper_dia = null;
    public $j18_dtoper_mes = null;
    public $j18_dtoper_ano = null;
    public $j18_dtoper = null;
    public $j18_rterri = 0;
    public $j18_rpredi = 0;
    public $j18_vencim = 0;
    public $j18_logradauto = 'f';
    public $j18_segundavia = 0;
    public $j18_infla = null;
    public $j18_utilizasetfisc = 'f';
    public $j18_testadanumero = 'f';
    public $j18_excconscalc = 'f';
    public $j18_textoprom = null;
    public $j18_calcvenc = 0;
    public $j18_utilizaloc = 'f';
    public $j18_permvenc = 0;
    public $j18_utidadosdiver = 'f';
    public $j18_dadoscertisen = 0;
    public $j18_formatsetor = 0;
    public $j18_formatquadra = 0;
    public $j18_formatlote = 0;
    public $j18_utilpontos = 0;
    public $j18_ordendent = 0;
    public $j18_iptuhistisen = 0;
    public $j18_db_sysfuncoes = 0;
    public $j18_tipoisen = 0;
    public $j18_perccorrepadrao = 0;
    public $j18_templatecertidaoexitencia = 0;
    public $j18_templatecertidaoisencao = 0;
    public $j18_receitacreditorecalculo = 0;
    public $j18_tipodebitorecalculo = 0;
    public $j18_taxaseparada = 0;
    public $j18_permitectmcgf = 'f';
    public $j18_bicmarcasigilo = 'f';
    public $j18_utilizaareaprivativa = 'f';
    public $j18_fracionaidbql = 'f';
    public $j18_validarano = 'f';
    public $j18_validaproprietario = 't';
    public $j18_desvinculadebitosaverbacao = 'f';
    public $j18_mostraareairregular = 'f';
    public $j18_caracterirregular = null;
    public $j18_simulacaocalculo = 'f';

    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 j18_anousu = int4 = Exercício 
                 j18_vlrref = float8 = Valor Referência 
                 j18_dtoper = date = Data Operação 
                 j18_rterri = int4 = Receita Territorial 
                 j18_rpredi = int4 = Receita Predial 
                 j18_vencim = int4 = Tabela de Vencimento 
                 j18_logradauto = bool = Código do Logradouro Automático 
                 j18_segundavia = int4 = Segunda Via 
                 j18_infla = varchar(5) = Código do Inflator 
                 j18_utilizasetfisc = bool = Utiliza Setor FIscal 
                 j18_testadanumero = bool = Utiliza Número na Testada 
                 j18_excconscalc = bool = Excluir Construção do Cálculo 
                 j18_textoprom = varchar(20) = Texto Promitente 
                 j18_calcvenc = int4 = Perguntar Vencimentos Durante Cálculo 
                 j18_utilizaloc = bool = Utilizar Informações de Localização 
                 j18_permvenc = int4 = Permite Escolher Vencimentos Durante Cálculo 
                 j18_utidadosdiver = bool = Utiliza Dados Diversos no Cálculo 
                 j18_dadoscertisen = int4 = Dados da Certidão de Isenção 
                 j18_formatsetor = int4 = Permite Digitar para Setor 
                 j18_formatquadra = int4 = Permite Digitar para Quadra 
                 j18_formatlote = int4 = Permite Digitar para o Lote 
                 j18_utilpontos = int4 = Utilizar Pontuação por Construção 
                 j18_ordendent = int4 = Ordem no Endereço de Entrega 
                 j18_iptuhistisen = int8 = Código do histórico de isenção 
                 j18_db_sysfuncoes = int4 = Código Função 
                 j18_tipoisen = int4 = Código da Isenção 
                 j18_perccorrepadrao = float8 = Percentual de Correção 
                 j18_templatecertidaoexitencia = int4 = Documento Template 
                 j18_templatecertidaoisencao = int4 = Template Certidão Isenção 
                 j18_receitacreditorecalculo = int4 = Receita de Crédito 
                 j18_tipodebitorecalculo = int4 = Tipo de Débito 
                 j18_taxaseparada = int4 = Calcular Taxas Separadas 
                 j18_permitectmcgf = bool = Permite consultar CTM com Matric na CGF 
                 j18_bicmarcasigilo = bool = BIC Marca Emissão Dados Sigilosos 
                 j18_utilizaareaprivativa = bool = Utiliza Área Privativa 
                 j18_fracionaidbql = bool = Fraciona por Idbql
                 j18_validarano = bool = Permite construção sem ano
                 j18_validaproprietario = bool = Valida proprietário
                 j18_desvinculadebitosaverbacao = bool = Desvincula débitos com averbação
                 j18_mostraareairregular = bool = Mostra área irregular no CTM
                 j18_caracterirregular = varchar(40) = Características de área irregular
                 j18_simulacaocalculo = bool = Habilita Simulação do calculo de IPTU
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("cfiptu");
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
    }

    public function erro($mostra, $retorna)
    {
        if (($this->erro_status == "0") || ($mostra && $this->erro_status != null )) {
            echo "<script>alert(\"".$this->erro_msg."\")</script>";
            if ($retorna) {
                echo "<script>location.href='".$this->pagina_retorno."'</script>";
            }
        }
    }

    public function atualizacampos($exclusao = false)
    {
        if (!$exclusao) {
            $this->j18_anousu = ($this->j18_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_anousu"]:$this->j18_anousu);
            $this->j18_vlrref = ($this->j18_vlrref == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_vlrref"]:$this->j18_vlrref);
            if ($this->j18_dtoper == "") {
                $this->j18_dtoper_dia = ($this->j18_dtoper_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_dtoper_dia"]:$this->j18_dtoper_dia);
                $this->j18_dtoper_mes = ($this->j18_dtoper_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_dtoper_mes"]:$this->j18_dtoper_mes);
                $this->j18_dtoper_ano = ($this->j18_dtoper_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_dtoper_ano"]:$this->j18_dtoper_ano);
                if ($this->j18_dtoper_dia != "") {
                    $this->j18_dtoper = $this->j18_dtoper_ano."-".$this->j18_dtoper_mes."-".$this->j18_dtoper_dia;
                }
            }
            $this->j18_rterri = ($this->j18_rterri == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_rterri"]:$this->j18_rterri);
            $this->j18_rpredi = ($this->j18_rpredi == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_rpredi"]:$this->j18_rpredi);
            $this->j18_vencim = ($this->j18_vencim == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_vencim"]:$this->j18_vencim);
            $this->j18_logradauto = ($this->j18_logradauto == "f"?@$GLOBALS["HTTP_POST_VARS"]["j18_logradauto"]:$this->j18_logradauto);
            $this->j18_segundavia = ($this->j18_segundavia == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_segundavia"]:$this->j18_segundavia);
            $this->j18_infla = ($this->j18_infla == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_infla"]:$this->j18_infla);
            $this->j18_utilizasetfisc = ($this->j18_utilizasetfisc == "f"?@$GLOBALS["HTTP_POST_VARS"]["j18_utilizasetfisc"]:$this->j18_utilizasetfisc);
            $this->j18_testadanumero = ($this->j18_testadanumero == "f"?@$GLOBALS["HTTP_POST_VARS"]["j18_testadanumero"]:$this->j18_testadanumero);
            $this->j18_excconscalc = ($this->j18_excconscalc == "f"?@$GLOBALS["HTTP_POST_VARS"]["j18_excconscalc"]:$this->j18_excconscalc);
            $this->j18_textoprom = ($this->j18_textoprom == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_textoprom"]:$this->j18_textoprom);
            $this->j18_calcvenc = ($this->j18_calcvenc == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_calcvenc"]:$this->j18_calcvenc);
            $this->j18_utilizaloc = ($this->j18_utilizaloc == "f"?@$GLOBALS["HTTP_POST_VARS"]["j18_utilizaloc"]:$this->j18_utilizaloc);
            $this->j18_permvenc = ($this->j18_permvenc == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_permvenc"]:$this->j18_permvenc);
            $this->j18_utidadosdiver = ($this->j18_utidadosdiver == "f"?@$GLOBALS["HTTP_POST_VARS"]["j18_utidadosdiver"]:$this->j18_utidadosdiver);
            $this->j18_dadoscertisen = ($this->j18_dadoscertisen == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_dadoscertisen"]:$this->j18_dadoscertisen);
            $this->j18_formatsetor = ($this->j18_formatsetor == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_formatsetor"]:$this->j18_formatsetor);
            $this->j18_formatquadra = ($this->j18_formatquadra == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_formatquadra"]:$this->j18_formatquadra);
            $this->j18_formatlote = ($this->j18_formatlote == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_formatlote"]:$this->j18_formatlote);
            $this->j18_utilpontos = ($this->j18_utilpontos == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_utilpontos"]:$this->j18_utilpontos);
            $this->j18_ordendent = ($this->j18_ordendent == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_ordendent"]:$this->j18_ordendent);
            $this->j18_iptuhistisen = ($this->j18_iptuhistisen == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_iptuhistisen"]:$this->j18_iptuhistisen);
            $this->j18_db_sysfuncoes = ($this->j18_db_sysfuncoes == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_db_sysfuncoes"]:$this->j18_db_sysfuncoes);
            $this->j18_tipoisen = ($this->j18_tipoisen == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_tipoisen"]:$this->j18_tipoisen);
            $this->j18_perccorrepadrao = ($this->j18_perccorrepadrao == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_perccorrepadrao"]:$this->j18_perccorrepadrao);
            $this->j18_templatecertidaoexitencia = ($this->j18_templatecertidaoexitencia == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_templatecertidaoexitencia"]:$this->j18_templatecertidaoexitencia);
            $this->j18_templatecertidaoisencao = ($this->j18_templatecertidaoisencao == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_templatecertidaoisencao"]:$this->j18_templatecertidaoisencao);
            $this->j18_receitacreditorecalculo = ($this->j18_receitacreditorecalculo == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_receitacreditorecalculo"]:$this->j18_receitacreditorecalculo);
            $this->j18_tipodebitorecalculo = ($this->j18_tipodebitorecalculo == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_tipodebitorecalculo"]:$this->j18_tipodebitorecalculo);
            $this->j18_taxaseparada = ($this->j18_taxaseparada == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_taxaseparada"]:$this->j18_taxaseparada);
            $this->j18_permitectmcgf = ($this->j18_permitectmcgf == "f"?@$GLOBALS["HTTP_POST_VARS"]["j18_permitectmcgf"]:$this->j18_permitectmcgf);
            $this->j18_bicmarcasigilo = ($this->j18_bicmarcasigilo == "f"?@$GLOBALS["HTTP_POST_VARS"]["j18_bicmarcasigilo"]:$this->j18_bicmarcasigilo);
            $this->j18_utilizaareaprivativa = ($this->j18_utilizaareaprivativa == "f"?@$GLOBALS["HTTP_POST_VARS"]["j18_utilizaareaprivativa"]:$this->j18_utilizaareaprivativa);
            $this->j18_fracionaidbql = ($this->j18_fracionaidbql == "f"?@$GLOBALS["HTTP_POST_VARS"]["j18_fracionaidbql"]:$this->j18_fracionaidbql);
            $this->j18_validarano = ($this->j18_validarano == "f" ? @$GLOBALS["HTTP_POST_VARS"]["j18_validarano"] : $this->j18_validarano);
            $this->j18_validaproprietario = ($this->j18_validaproprietario == "f" ? @$GLOBALS["HTTP_POST_VARS"]["j18_validaproprietario"] : $this->j18_validaproprietario);
            $this->j18_desvinculadebitosaverbacao = ($this->j18_desvinculadebitosaverbacao == "f" ? @$GLOBALS["HTTP_POST_VARS"]["j18_desvinculadebitosaverbacao"] : $this->j18_desvinculadebitosaverbacao);
            $this->j18_mostraareairregular = ($this->j18_mostraareairregular == "f" ? @$GLOBALS["HTTP_POST_VARS"]["j18_mostraareairregular"] : $this->j18_mostraareairregular);
            $this->j18_caracterirregular = ($this->j18_caracterirregular == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_caracterirregular"]:$this->j18_caracterirregular);
            $this->j18_simulacaocalculo = ($this->j18_simulacaocalculo == "f" ? @$GLOBALS["HTTP_POST_VARS"]["j18_simulacaocalculo"] : $this->j18_simulacaocalculo);
        } else {
            $this->j18_anousu = ($this->j18_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["j18_anousu"]:$this->j18_anousu);
        }
    }

    public function incluir($j18_anousu)
    {
        $this->atualizacampos();

        if ($this->j18_validarano == null) {
            $this->j18_validarano = 'f';
        }

        if ($this->j18_validaproprietario == null) {
            $this->j18_validaproprietario = 't';
        }

        if ($this->j18_desvinculadebitosaverbacao == null) {
            $this->j18_desvinculadebitosaverbacao = 'f';
        }

        if ($this->j18_mostraareairregular == null) {
            $this->j18_mostraareairregular = 'f';
        }

        if ($this->j18_simulacaocalculo == null) {
            $this->j18_simulacaocalculo = 'f';
        }

        if ($this->j18_vlrref == null) {
            $this->erro_sql = " Campo Valor Referência não informado.";
            $this->erro_campo = "j18_vlrref";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j18_dtoper == null) {
            $this->erro_sql = " Campo Data Operação não informado.";
            $this->erro_campo = "j18_dtoper_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j18_rterri == null) {
            $this->erro_sql = " Campo Receita Territorial não informado.";
            $this->erro_campo = "j18_rterri";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j18_rpredi == null) {
            $this->erro_sql = " Campo Receita Predial não informado.";
            $this->erro_campo = "j18_rpredi";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j18_vencim == null) {
            $this->erro_sql = " Campo Tabela de Vencimento não informado.";
            $this->erro_campo = "j18_vencim";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j18_logradauto == null) {
            $this->erro_sql = " Campo Código do Logradouro Automático não informado.";
            $this->erro_campo = "j18_logradauto";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j18_segundavia == null) {
            $this->erro_sql = " Campo Segunda Via não informado.";
            $this->erro_campo = "j18_segundavia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j18_infla == null) {
            $this->erro_sql = " Campo Código do Inflator não informado.";
            $this->erro_campo = "j18_infla";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j18_utilizasetfisc == null) {
            $this->erro_sql = " Campo Utiliza Setor FIscal não informado.";
            $this->erro_campo = "j18_utilizasetfisc";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j18_testadanumero == null) {
            $this->erro_sql = " Campo Utiliza Número na Testada não informado.";
            $this->erro_campo = "j18_testadanumero";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j18_excconscalc == null) {
            $this->erro_sql = " Campo Excluir Construção do Cálculo não informado.";
            $this->erro_campo = "j18_excconscalc";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j18_calcvenc == null) {
            $this->erro_sql = " Campo Perguntar Vencimentos Durante Cálculo não informado.";
            $this->erro_campo = "j18_calcvenc";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j18_utilizaloc == null) {
            $this->erro_sql = " Campo Utilizar Informações de Localização não informado.";
            $this->erro_campo = "j18_utilizaloc";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j18_permvenc == null) {
            $this->erro_sql = " Campo Permite Escolher Vencimentos Durante Cálculo não informado.";
            $this->erro_campo = "j18_permvenc";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j18_utidadosdiver == null) {
            $this->erro_sql = " Campo Utiliza Dados Diversos no Cálculo não informado.";
            $this->erro_campo = "j18_utidadosdiver";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j18_dadoscertisen == null) {
            $this->erro_sql = " Campo Dados da Certidão de Isenção não informado.";
            $this->erro_campo = "j18_dadoscertisen";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j18_formatsetor == null) {
            $this->erro_sql = " Campo Permite Digitar para Setor não informado.";
            $this->erro_campo = "j18_formatsetor";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j18_formatquadra == null) {
            $this->erro_sql = " Campo Permite Digitar para Quadra não informado.";
            $this->erro_campo = "j18_formatquadra";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j18_formatlote == null) {
            $this->erro_sql = " Campo Permite Digitar para o Lote não informado.";
            $this->erro_campo = "j18_formatlote";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j18_utilpontos == null) {
            $this->erro_sql = " Campo Utilizar Pontuação por Construção não informado.";
            $this->erro_campo = "j18_utilpontos";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j18_ordendent == null) {
            $this->erro_sql = " Campo Ordem no Endereço de Entrega não informado.";
            $this->erro_campo = "j18_ordendent";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j18_iptuhistisen == null) {
            $this->erro_sql = " Campo Código do histórico de isenção não informado.";
            $this->erro_campo = "j18_iptuhistisen";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j18_db_sysfuncoes == null) {
            $this->erro_sql = " Campo Código Função não informado.";
            $this->erro_campo = "j18_db_sysfuncoes";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j18_tipoisen == null) {
            $this->erro_sql = " Campo Código da Isenção não informado.";
            $this->erro_campo = "j18_tipoisen";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j18_templatecertidaoexitencia == null) {
            $this->j18_templatecertidaoexitencia = "null";
        }
        if ($this->j18_perccorrepadrao == null) {
            $this->j18_perccorrepadrao = "0";
        }
        if ($this->j18_templatecertidaoisencao == null) {
            $this->j18_templatecertidaoisencao = "null";
        }

        if ($this->j18_receitacreditorecalculo == null) {
            $this->j18_receitacreditorecalculo = "null";
        }

        if ($this->j18_tipodebitorecalculo == null) {
            $this->j18_tipodebitorecalculo = "null";
        }

        if ($this->j18_taxaseparada == null) {
            $this->erro_sql = " Campo Calcular Taxas Separadas não informado.";
            $this->erro_campo = "j18_taxaseparada";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->j18_anousu = $j18_anousu;
        if (($this->j18_anousu == null) || ($this->j18_anousu == "")) {
            $this->erro_sql = " Campo j18_anousu não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j18_bicmarcasigilo == null) {
            $this->erro_sql = " Campo BIC Marca Emissão Dados Sigilosos não informado.";
            $this->erro_campo = "j18_bicmarcasigilo";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j18_permitectmcgf == null) {
            $this->erro_sql = " Campo Permite consultar CTM com Matric na CGF não informado.";
            $this->erro_campo = "j18_permitectmcgf";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }

        if ($this->j18_utilizaareaprivativa == null) {
            $this->erro_sql = " Campo Utiliza Área Privativa não informado.";
            $this->erro_campo = "j18_utilizaareaprivativa";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->j18_fracionaidbql == null) {
            $this->j18_fracionaidbql = "false";
        }

        if ($this->j18_caracterirregular == null) {
            $this->j18_caracterirregular = '';
        }

        $sql = "insert into cfiptu(
                                       j18_anousu
                                      ,j18_vlrref
                                      ,j18_dtoper
                                      ,j18_rterri
                                      ,j18_rpredi
                                      ,j18_vencim
                                      ,j18_logradauto
                                      ,j18_segundavia
                                      ,j18_infla
                                      ,j18_utilizasetfisc
                                      ,j18_testadanumero
                                      ,j18_excconscalc
                                      ,j18_textoprom
                                      ,j18_calcvenc
                                      ,j18_utilizaloc
                                      ,j18_permvenc
                                      ,j18_utidadosdiver
                                      ,j18_dadoscertisen
                                      ,j18_formatsetor
                                      ,j18_formatquadra
                                      ,j18_formatlote
                                      ,j18_utilpontos
                                      ,j18_ordendent
                                      ,j18_iptuhistisen
                                      ,j18_db_sysfuncoes
                                      ,j18_tipoisen
                                      ,j18_perccorrepadrao
                                      ,j18_templatecertidaoexitencia
                                      ,j18_templatecertidaoisencao
                                      ,j18_receitacreditorecalculo
                                      ,j18_tipodebitorecalculo
                                      ,j18_taxaseparada 
                                      ,j18_permitectmcgf 
                                      ,j18_bicmarcasigilo 
                                      ,j18_utilizaareaprivativa 
                                      ,j18_fracionaidbql
                                      ,j18_validarano
                                      ,j18_validaproprietario
                                      ,j18_desvinculadebitosaverbacao
                                      ,j18_mostraareairregular
                                      ,j18_caracterirregular
                                      ,j18_simulacaocalculo
                       )
                values (
                                $this->j18_anousu
                               ,$this->j18_vlrref
                               ,".($this->j18_dtoper == "null" || $this->j18_dtoper == ""?"null":"'".$this->j18_dtoper."'")."
                               ,$this->j18_rterri
                               ,$this->j18_rpredi
                               ,$this->j18_vencim
                               ,'$this->j18_logradauto'
                               ,$this->j18_segundavia
                               ,'$this->j18_infla'
                               ,'$this->j18_utilizasetfisc'
                               ,'$this->j18_testadanumero'
                               ,'$this->j18_excconscalc'
                               ,'$this->j18_textoprom'
                               ,$this->j18_calcvenc
                               ,'$this->j18_utilizaloc'
                               ,$this->j18_permvenc
                               ,'$this->j18_utidadosdiver'
                               ,$this->j18_dadoscertisen
                               ,$this->j18_formatsetor
                               ,$this->j18_formatquadra
                               ,$this->j18_formatlote
                               ,$this->j18_utilpontos
                               ,$this->j18_ordendent
                               ,$this->j18_iptuhistisen
                               ,$this->j18_db_sysfuncoes
                               ,$this->j18_tipoisen
                               ,$this->j18_perccorrepadrao
                               ,$this->j18_templatecertidaoexitencia
                               ,$this->j18_templatecertidaoisencao
                               ,$this->j18_receitacreditorecalculo
                               ,$this->j18_tipodebitorecalculo
                               ,$this->j18_taxaseparada 
                               ,'$this->j18_permitectmcgf' 
                               ,'$this->j18_bicmarcasigilo' 
                               ,'$this->j18_utilizaareaprivativa'
                               ,'$this->j18_fracionaidbql'
                               ,'$this->j18_validarano'
                               ,'$this->j18_validaproprietario'
                               ,'$this->j18_desvinculadebitosaverbacao'
                               ,'$this->j18_mostraareairregular'
                               ,'$this->j18_caracterirregular'
                               ,'$this->j18_simulacaocalculo'
                      )";
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Parametros ($this->j18_anousu) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Parametros já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "Parametros ($this->j18_anousu) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->j18_anousu;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        return true;
    }

    public function alterar($j18_anousu = null)
    {
        $this->atualizacampos();
        $sql = " update cfiptu set ";
        $virgula = "";
        if (trim($this->j18_vlrref)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_vlrref"])) {
            $sql  .= $virgula." j18_vlrref = $this->j18_vlrref ";
            $virgula = ",";
            if (trim($this->j18_vlrref) == null) {
                $this->erro_sql = " Campo Valor Referência não informado.";
                $this->erro_campo = "j18_vlrref";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j18_dtoper)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_dtoper_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["j18_dtoper_dia"] !="")) {
            $sql  .= $virgula." j18_dtoper = '$this->j18_dtoper' ";
            $virgula = ",";
            if (trim($this->j18_dtoper) == null) {
                $this->erro_sql = " Campo Data Operação não informado.";
                $this->erro_campo = "j18_dtoper_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["j18_dtoper_dia"])) {
                $sql  .= $virgula." j18_dtoper = null ";
                $virgula = ",";
                if (trim($this->j18_dtoper) == null) {
                      $this->erro_sql = " Campo Data Operação não informado.";
                      $this->erro_campo = "j18_dtoper_dia";
                      $this->erro_banco = "";
                      $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                      $this->erro_status = "0";
                      return false;
                }
            }
        }
        if (trim($this->j18_rterri)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_rterri"])) {
            $sql  .= $virgula." j18_rterri = $this->j18_rterri ";
            $virgula = ",";
            if (trim($this->j18_rterri) == null) {
                $this->erro_sql = " Campo Receita Territorial não informado.";
                $this->erro_campo = "j18_rterri";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j18_rpredi)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_rpredi"])) {
            $sql  .= $virgula." j18_rpredi = $this->j18_rpredi ";
            $virgula = ",";
            if (trim($this->j18_rpredi) == null) {
                $this->erro_sql = " Campo Receita Predial não informado.";
                $this->erro_campo = "j18_rpredi";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j18_vencim)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_vencim"])) {
            $sql  .= $virgula." j18_vencim = $this->j18_vencim ";
            $virgula = ",";
            if (trim($this->j18_vencim) == null) {
                $this->erro_sql = " Campo Tabela de Vencimento não informado.";
                $this->erro_campo = "j18_vencim";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j18_logradauto)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_logradauto"])) {
            $sql  .= $virgula." j18_logradauto = '$this->j18_logradauto' ";
            $virgula = ",";
            if (trim($this->j18_logradauto) == null) {
                $this->erro_sql = " Campo Código do Logradouro Automático não informado.";
                $this->erro_campo = "j18_logradauto";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j18_segundavia)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_segundavia"])) {
            $sql  .= $virgula." j18_segundavia = $this->j18_segundavia ";
            $virgula = ",";
            if (trim($this->j18_segundavia) == null) {
                $this->erro_sql = " Campo Segunda Via não informado.";
                $this->erro_campo = "j18_segundavia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j18_infla)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_infla"])) {
            $sql  .= $virgula." j18_infla = '$this->j18_infla' ";
            $virgula = ",";
            if (trim($this->j18_infla) == null) {
                $this->erro_sql = " Campo Código do Inflator não informado.";
                $this->erro_campo = "j18_infla";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j18_utilizasetfisc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_utilizasetfisc"])) {
            $sql  .= $virgula." j18_utilizasetfisc = '$this->j18_utilizasetfisc' ";
            $virgula = ",";
            if (trim($this->j18_utilizasetfisc) == null) {
                $this->erro_sql = " Campo Utiliza Setor FIscal não informado.";
                $this->erro_campo = "j18_utilizasetfisc";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j18_testadanumero)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_testadanumero"])) {
            $sql  .= $virgula." j18_testadanumero = '$this->j18_testadanumero' ";
            $virgula = ",";
            if (trim($this->j18_testadanumero) == null) {
                $this->erro_sql = " Campo Utiliza Número na Testada não informado.";
                $this->erro_campo = "j18_testadanumero";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j18_excconscalc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_excconscalc"])) {
            $sql  .= $virgula." j18_excconscalc = '$this->j18_excconscalc' ";
            $virgula = ",";
            if (trim($this->j18_excconscalc) == null) {
                $this->erro_sql = " Campo Excluir Construção do Cálculo não informado.";
                $this->erro_campo = "j18_excconscalc";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j18_textoprom)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_textoprom"])) {
            $sql  .= $virgula." j18_textoprom = '$this->j18_textoprom' ";
            $virgula = ",";
        }
        if (trim($this->j18_calcvenc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_calcvenc"])) {
            $sql  .= $virgula." j18_calcvenc = $this->j18_calcvenc ";
            $virgula = ",";
            if (trim($this->j18_calcvenc) == null) {
                $this->erro_sql = " Campo Perguntar Vencimentos Durante Cálculo não informado.";
                $this->erro_campo = "j18_calcvenc";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j18_utilizaloc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_utilizaloc"])) {
            $sql  .= $virgula." j18_utilizaloc = '$this->j18_utilizaloc' ";
            $virgula = ",";
            if (trim($this->j18_utilizaloc) == null) {
                $this->erro_sql = " Campo Utilizar Informações de Localização não informado.";
                $this->erro_campo = "j18_utilizaloc";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j18_permvenc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_permvenc"])) {
            $sql  .= $virgula." j18_permvenc = $this->j18_permvenc ";
            $virgula = ",";
            if (trim($this->j18_permvenc) == null) {
                $this->erro_sql = " Campo Permite Escolher Vencimentos Durante Cálculo não informado.";
                $this->erro_campo = "j18_permvenc";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j18_utidadosdiver)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_utidadosdiver"])) {
            $sql  .= $virgula." j18_utidadosdiver = '$this->j18_utidadosdiver' ";
            $virgula = ",";
            if (trim($this->j18_utidadosdiver) == null) {
                $this->erro_sql = " Campo Utiliza Dados Diversos no Cálculo não informado.";
                $this->erro_campo = "j18_utidadosdiver";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j18_dadoscertisen)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_dadoscertisen"])) {
            $sql  .= $virgula." j18_dadoscertisen = $this->j18_dadoscertisen ";
            $virgula = ",";
            if (trim($this->j18_dadoscertisen) == null) {
                $this->erro_sql = " Campo Dados da Certidão de Isenção não informado.";
                $this->erro_campo = "j18_dadoscertisen";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j18_formatsetor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_formatsetor"])) {
            $sql  .= $virgula." j18_formatsetor = $this->j18_formatsetor ";
            $virgula = ",";
            if (trim($this->j18_formatsetor) == null) {
                $this->erro_sql = " Campo Permite Digitar para Setor não informado.";
                $this->erro_campo = "j18_formatsetor";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j18_formatquadra)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_formatquadra"])) {
            $sql  .= $virgula." j18_formatquadra = $this->j18_formatquadra ";
            $virgula = ",";
            if (trim($this->j18_formatquadra) == null) {
                $this->erro_sql = " Campo Permite Digitar para Quadra não informado.";
                $this->erro_campo = "j18_formatquadra";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j18_formatlote)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_formatlote"])) {
            $sql  .= $virgula." j18_formatlote = $this->j18_formatlote ";
            $virgula = ",";
            if (trim($this->j18_formatlote) == null) {
                $this->erro_sql = " Campo Permite Digitar para o Lote não informado.";
                $this->erro_campo = "j18_formatlote";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j18_utilpontos)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_utilpontos"])) {
            $sql  .= $virgula." j18_utilpontos = $this->j18_utilpontos ";
            $virgula = ",";
            if (trim($this->j18_utilpontos) == null) {
                $this->erro_sql = " Campo Utilizar Pontuação por Construção não informado.";
                $this->erro_campo = "j18_utilpontos";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j18_ordendent)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_ordendent"])) {
            $sql  .= $virgula." j18_ordendent = $this->j18_ordendent ";
            $virgula = ",";
            if (trim($this->j18_ordendent) == null) {
                $this->erro_sql = " Campo Ordem no Endereço de Entrega não informado.";
                $this->erro_campo = "j18_ordendent";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j18_iptuhistisen)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_iptuhistisen"])) {
            $sql  .= $virgula." j18_iptuhistisen = $this->j18_iptuhistisen ";
            $virgula = ",";
            if (trim($this->j18_iptuhistisen) == null) {
                $this->erro_sql = " Campo Código do histórico de isenção não informado.";
                $this->erro_campo = "j18_iptuhistisen";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j18_db_sysfuncoes)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_db_sysfuncoes"])) {
            $sql  .= $virgula." j18_db_sysfuncoes = $this->j18_db_sysfuncoes ";
            $virgula = ",";
            if (trim($this->j18_db_sysfuncoes) == null) {
                $this->erro_sql = " Campo Código Função não informado.";
                $this->erro_campo = "j18_db_sysfuncoes";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j18_tipoisen)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_tipoisen"])) {
            $sql  .= $virgula." j18_tipoisen = $this->j18_tipoisen ";
            $virgula = ",";
            if (trim($this->j18_tipoisen) == null) {
                $this->erro_sql = " Campo Código da Isenção não informado.";
                $this->erro_campo = "j18_tipoisen";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j18_templatecertidaoexitencia)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_templatecertidaoexitencia"])) {
            if (trim($this->j18_templatecertidaoexitencia)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j18_templatecertidaoisencao"])) {
                $this->j18_templatecertidaoexitencia = "null" ;
            }
            $sql  .= $virgula." j18_templatecertidaoexitencia = $this->j18_templatecertidaoexitencia ";
            $virgula = ",";
        }
        if (trim($this->j18_perccorrepadrao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_perccorrepadrao"])) {
            if (trim($this->j18_perccorrepadrao)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j18_perccorrepadrao"])) {
                $this->j18_perccorrepadrao = "0" ;
            }
            $sql  .= $virgula." j18_perccorrepadrao = $this->j18_perccorrepadrao ";
            $virgula = ",";
        }
        if (trim($this->j18_templatecertidaoisencao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_templatecertidaoisencao"])) {
            if (trim($this->j18_templatecertidaoisencao)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j18_templatecertidaoisencao"])) {
                $this->j18_templatecertidaoisencao = "null" ;
            }
            $sql  .= $virgula." j18_templatecertidaoisencao = $this->j18_templatecertidaoisencao ";
            $virgula = ",";
        }
        if (trim($this->j18_receitacreditorecalculo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_receitacreditorecalculo"])) {
            if (trim($this->j18_receitacreditorecalculo)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j18_receitacreditorecalculo"])) {
                $this->j18_receitacreditorecalculo = "null" ;
            }
            $sql  .= $virgula." j18_receitacreditorecalculo = $this->j18_receitacreditorecalculo ";
            $virgula = ",";
        }
        if (trim($this->j18_tipodebitorecalculo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_tipodebitorecalculo"])) {
            if (trim($this->j18_tipodebitorecalculo)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j18_tipodebitorecalculo"])) {
                $this->j18_tipodebitorecalculo = "null" ;
            }
            $sql  .= $virgula." j18_tipodebitorecalculo = $this->j18_tipodebitorecalculo ";
            $virgula = ",";
        }
        if (trim($this->j18_taxaseparada)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_taxaseparada"])) {
            $sql  .= $virgula." j18_taxaseparada = $this->j18_taxaseparada ";
            $virgula = ",";
            if (trim($this->j18_taxaseparada) == null) {
                $this->erro_sql = " Campo Calcular Taxas Separadas não informado.";
                $this->erro_campo = "j18_taxaseparada";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j18_permitectmcgf)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_permitectmcgf"])) {
            $sql  .= $virgula." j18_permitectmcgf = '$this->j18_permitectmcgf' ";
            $virgula = ",";
            if (trim($this->j18_permitectmcgf) == null) {
                $this->erro_sql = " Campo Permite consultar CTM com Matric na CGF não informado.";
                $this->erro_campo = "j18_permitectmcgf";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j18_bicmarcasigilo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_bicmarcasigilo"])) {
            $sql  .= $virgula." j18_bicmarcasigilo = '$this->j18_bicmarcasigilo' ";
            $virgula = ",";
            if (trim($this->j18_bicmarcasigilo) == null) {
                $this->erro_sql = " Campo BIC Marca Emissão Dados Sigilosos não informado.";
                $this->erro_campo = "j18_bicmarcasigilo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j18_utilizaareaprivativa)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_utilizaareaprivativa"])) {
            $sql  .= $virgula." j18_utilizaareaprivativa = '$this->j18_utilizaareaprivativa' ";
            $virgula = ",";
            if (trim($this->j18_utilizaareaprivativa) == null) {
                $this->erro_sql = " Campo Utiliza Área Privativa não informado.";
                $this->erro_campo = "j18_utilizaareaprivativa";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->j18_fracionaidbql)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_fracionaidbql"])) {
            $sql  .= $virgula." j18_fracionaidbql = '$this->j18_fracionaidbql' ";
            $virgula = ",";
        }

        if (!empty($this->j18_validarano)) {
            if ($this->j18_validarano != 'f' and $this->j18_validarano != 't') {
                $this->j18_validarano = 'f';
            }

            $sql .= $virgula . " j18_validarano = '$this->j18_validarano' ";
            $virgula = ",";
        }

        if (isset($_POST["j18_validaproprietario"])) {
            $this->j18_validaproprietario = $_POST["j18_validaproprietario"];
        }
        
        if (trim($this->j18_validaproprietario)!="") {
            $sql .= $virgula . " j18_validaproprietario = '$this->j18_validaproprietario' ";
            $virgula = ",";
        }

        if (isset($_POST["j18_simulacaocalculo"])) {
            $this->j18_simulacaocalculo = $_POST["j18_simulacaocalculo"];
        }
        
        if (trim($this->j18_simulacaocalculo)!="") {
            $sql .= $virgula . " j18_simulacaocalculo = '$this->j18_simulacaocalculo' ";
            $virgula = ",";
        }

        if (isset($_POST["j18_desvinculadebitosaverbacao"])) {
            $this->j18_desvinculadebitosaverbacao =  $_POST["j18_desvinculadebitosaverbacao"];
        }
        
        if (trim($this->j18_desvinculadebitosaverbacao)!="") {
            $sql .= $virgula . " j18_desvinculadebitosaverbacao = '$this->j18_desvinculadebitosaverbacao' ";
            $virgula = ",";
        }
        if (!empty($this->j18_mostraareairregular)) {
            if ($this->j18_mostraareairregular != 'f' and $this->j18_mostraareairregular != 't') {
                $this->j18_mostraareairregular = 'f';
            }

            $sql .= $virgula . " j18_mostraareairregular = '$this->j18_mostraareairregular' ";
            $virgula = ",";
        }

        if (trim($this->j18_caracterirregular)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j18_caracterirregular"])) {
            $sql  .= $virgula." j18_caracterirregular = '$this->j18_caracterirregular' ";
            $virgula = ",";
            if (trim($this->j18_caracterirregular) == null) {
                $this->j18_caracterirregular = '';
            }
        }

        $sql .= " where ";
        if ($j18_anousu!=null) {
            $sql .= " j18_anousu = $this->j18_anousu";
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Parametros não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->j18_anousu;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Parametros não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->j18_anousu;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$this->j18_anousu;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($j18_anousu = null, $dbwhere = null)
    {
        $sql = " delete from cfiptu where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($j18_anousu)) {
                if (!empty($sql2)) {
                     $sql2 .= " and ";
                }
                $sql2 .= " j18_anousu = $j18_anousu ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Parametros não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$j18_anousu;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Parametros não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$j18_anousu;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$j18_anousu;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
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
            $this->numrows    = 0;
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Erro ao selecionar os registros.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->numrows = pg_num_rows($result);
        if ($this->numrows == 0) {
            $this->erro_banco = "";
            $this->erro_sql   = "Record Vazio na Tabela:cfiptu";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($j18_anousu = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos}";
        $sql .= "  from cfiptu ";
        $sql .= "      inner join tipoisen  on  tipoisen.j45_tipo = cfiptu.j18_tipoisen";
        $sql .= "      left  join tabrec  on  tabrec.k02_codigo = cfiptu.j18_receitacreditorecalculo";
        $sql .= "      inner join inflan  on  inflan.i01_codigo = cfiptu.j18_infla";
        $sql .= "      left  join arretipo  on  arretipo.k00_tipo = cfiptu.j18_tipodebitorecalculo";
        $sql .= "      inner join db_sysfuncoes  on  db_sysfuncoes.codfuncao = cfiptu.j18_db_sysfuncoes";
        $sql .= "      left  join iptucalh  on  iptucalh.j17_codhis = cfiptu.j18_iptuhistisen";
        $sql .= "      left  join db_documentotemplate  on  db_documentotemplate.db82_sequencial = cfiptu.j18_templatecertidaoexitencia";
        $sql .= "      inner join tabrecjm  on  tabrecjm.k02_codjm = tabrec.k02_codjm";
        $sql .= "      inner join tabrectipo  on  tabrectipo.k116_sequencial = tabrec.k02_tabrectipo";
        $sql .= "      left  join tabdesc  on  tabdesc.codsubrec = arretipo.k00_taxaespecifica";
        $sql .= "      inner join db_config  on  db_config.codigo = arretipo.k00_instit";
        $sql .= "      inner join cadtipo  on  cadtipo.k03_tipo = arretipo.k03_tipo";
        $sql .= "      left join db_config  as a on   a.codigo = db_documentotemplate.db82_instit";
        $sql .= "      left join db_documentotemplatetipo  on  db_documentotemplatetipo.db80_sequencial = db_documentotemplate.db82_templatetipo";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($j18_anousu)) {
                $sql2 .= " where cfiptu.j18_anousu = $j18_anousu ";
            }
        } elseif (!empty($dbwhere)) {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }
        return $sql;
    }

    public function sql_query_file($j18_anousu = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos} ";
        $sql .= "  from cfiptu ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($j18_anousu)) {
                $sql2 .= " where cfiptu.j18_anousu = $j18_anousu ";
            }
        } elseif (!empty($dbwhere)) {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }
        return $sql;
    }

   /**
   * Método que retorna objeto com os paramêtros do cadastro imobiliário
   * @param $iAnousu Ano base para consulta
   */
    function getParametrosCadastroImobiliario($iAnoUsu = null)
    {

        $sSql = "select * from cfiptu ";

        if (!empty($iAnoUsu)) {
            $sSql .= " where j18_anousu = " . $iAnoUsu;
        }
        $rsSql = db_query($sSql);

        if ($rsSql && pg_num_rows($rsSql) > 0) {
            return db_utils::getCollectionByRecord($rsSql);
        }
        return false;
    }

    function sql_query_param($j18_anousu = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from cfiptu ";
        $sql .= "      left join tipoisen      on tipoisen.j45_tipo = cfiptu.j18_tipoisen";
        $sql .= "      left join inflan        on inflan.i01_codigo = cfiptu.j18_infla";
        $sql .= "      left join db_sysfuncoes on db_sysfuncoes.codfuncao = cfiptu.j18_db_sysfuncoes";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($j18_anousu!=null) {
                $sql2 .= " where cfiptu.j18_anousu = $j18_anousu ";
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
   * Método que retorna receitas com data limite válida
   * @todo refatorar a logica para retornar a descricao da receita invalida
   * @param  integer $iAnousu      Ano de exercicio do calculo
   * @return string
   */
    function verificaReceitasInvalidas($iAnousu)
    {

        $sSql  = " select tabrec.k02_codigo,                                                      ";
        $sSql .= "        tabrec.k02_descr,                                                       ";
        $sSql .= "        tabrec.k02_limite as limite_receita_principal,                          ";
        $sSql .= "        juros.k02_limite  as limite_receita_juros,                              ";
        $sSql .= "        multa.k02_limite  as limite_receita_multa                               ";
        $sSql .= "   from tabrec                                                                  ";
        $sSql .= "        left join tabrec juros  on juros.k02_codigo = tabrec.k02_recjur         ";
        $sSql .= "        left join tabrec multa  on multa.k02_codigo = tabrec.k02_recmul         ";
        $sSql .= "  where tabrec.k02_codigo in (select j08_tabrec as codigo_receita               ";
        $sSql .= "                                from iptucadtaxaexe                             ";
        $sSql .= "                               where j08_anousu = {$iAnousu}                    ";
        $sSql .= "                               union                                            ";
        $sSql .= "                              select j18_rterri as codigo_receita               ";
        $sSql .= "                                from cfiptu                                     ";
        $sSql .= "                               where j18_anousu = {$iAnousu}                    ";
        $sSql .= "                               union                                            ";
        $sSql .= "                              select j18_rpredi as codigo_receita               ";
        $sSql .= "                                from cfiptu                                     ";
        $sSql .= "                               where j18_anousu = {$iAnousu} )                  ";
        $sSql .= "    and (    tabrec.k02_limite < '{$iAnousu}-01-01'::date                       ";
        $sSql .= "          or juros.k02_limite  < '{$iAnousu}-01-01'::date                       ";
        $sSql .= "          or multa.k02_limite  < '{$iAnousu}-01-01'::date )                     ";

        $rsVerificaReceitas = db_query($sSql);

        $sMensagem        = '';
        $sMensagemRetorno = null;

        if ($rsVerificaReceitas) {
            if (pg_num_rows($rsVerificaReceitas) > 0) {
                $oReceitasInvalidas = db_utils::getCollectionByRecord($rsVerificaReceitas);
                foreach ($oReceitasInvalidas as $oReceita) {
                    $sMensagem = "Verifique o cadastro da Receita {$oReceita->k02_codigo}, data limite informada para a receita";

                    if (!empty($oReceita->limite_receita_principal)) {
                        $sMensagem .=  " principal";
                    }

                    if (!empty($oReceita->limite_receita_juros) && !empty($oReceita->limite_receita_multa)) {
                        if (!empty($oReceita->limite_receita_principal)) {
                              $sMensagem .=  " e receita de juros e multa";
                        } else {
                              $sMensagem .=  " de juros e multa";
                        }
                    } else {
                        if (!empty($oReceita->limite_receita_juros)) {
                            if (!empty($oReceita->limite_receita_principal)) {
                                $sMensagem .=  " e de juros";
                            } else {
                                $sMensagem .=  " de juros";
                            }
                        }

                        if (!empty($oReceita->limite_receita_multa)) {
                            if (!empty($oReceita->limite_receita_principal)) {
                                $sMensagem .=  " e de multa";
                            } else {
                                $sMensagem .=  " de multa";
                            }
                        }
                    }

                    $sMensagem .= " inválida para o exercício. \n";
                    $sMensagemRetorno .= $sMensagem;
                }
            }
        }

        return $sMensagemRetorno;
    }

  /**
   * Montamos a query que consulta a receita de crédito configurada para o recalculo
   * @param  integer  $iAnousu
   * @param  string   $sCampos
   * @return string   query pronta
   */
    public function verificaReceitaCreditoRecalculo($iAnousu, $sCampos = "*")
    {

        $sSql  = " select {$sCampos}                                                    ";
        $sSql .= "   from cfiptu                                                        ";
        $sSql .= "        inner join tabrec on j18_receitacreditorecalculo = k02_codigo ";
        $sSql .= "  where j18_anousu = {$iAnousu}                                       ";

        return $sSql;
    }

  /**
   * Montamos a query que consulta o tipo de débito configurado para o recalculo
   * @param  integer  $iAnousu
   * @param  string   $sCampos
   * @param  integer  $iCadTipo
   * @return string   query pronta
   */
    public function verificaTipoDebitoRecalculo($iAnousu, $sCampos = "*", $iCadTipo = 7)
    {

        $sSql  = " select {$sCampos}                                                 ";
        $sSql .= "   from cfiptu                                                     ";
        $sSql .= "        inner join arretipo on j18_tipodebitorecalculo = k00_tipo  ";
        $sSql .= "        inner join cadtipo on arretipo.k03_tipo = cadtipo.k03_tipo ";
        $sSql .= "  where j18_anousu       = {$iAnousu}                              ";
        $sSql .= "    and cadtipo.k03_tipo = {$iCadTipo}                             ";

        return $sSql;
    }

  /**
   * Montamos a query que consulta a procedência do tipo de débito configurado para o recálculo
   * @param  integer  $iAnousu
   * @param  string   $sCampos
   * @return string   query pronta
   */
    public function verificaProcedenciaDebitoRecalculo($iAnousu, $sCampos = "*")
    {

        $sSql  = " select {$sCampos}                                                   ";
        $sSql .= "   from cfiptu                                                       ";
        $sSql .= "        inner join arretipo  on j18_tipodebitorecalculo = k00_tipo   ";
        $sSql .= "        inner join procdiver on dv09_tipo               = k00_tipo   ";
        $sSql .= "        inner join proced    on dv09_proced             = v03_codigo ";
        $sSql .= "        inner join tabrec    on dv09_receit             = k02_codigo ";
        $sSql .= "        inner join histcalc  on dv09_hist               = k01_codigo ";
        $sSql .= "  where j18_anousu    = {$iAnousu}                                   ";
        $sSql .= "    and (dv09_dtlimite >= now() or dv09_dtlimite is null)            ";

        return $sSql;
    }
}
