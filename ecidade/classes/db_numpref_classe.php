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

//MODULO: caixa
//CLASSE DA ENTIDADE numpref
class cl_numpref
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
    // cria variaveis do arquivo
    public $k03_anousu = 0;
    public $k03_instit = 0;
    public $k03_numpre = 0;
    public $k03_defope = 0;
    public $k03_recjur = 0;
    public $k03_numsli = 0;
    public $k03_impend = 'f';
    public $k03_unipri = 'f';
    public $k03_codbco = 0;
    public $k03_codage = null;
    public $k03_recmul = 0;
    public $k03_calrec = 'f';
    public $k03_msg = null;
    public $k03_msgcarne = null;
    public $k03_msgbanco = null;
    public $k03_certissvar = 'f';
    public $k03_diasjust = 0;
    public $k03_reccert = 'f';
    public $k03_taxagrupo = 0;
    public $k03_tipocodcert = 0;
    public $k03_reciboprot = 0;
    public $k03_regracnd = 0;
    public $k03_reciboprotretencao = 0;
    public $k03_tipocertidao = 0;
    public $k03_separajurmulparc = 0;
    public $k03_respcgm = 0;
    public $k03_respcargo = 0;
    public $k03_msgautent = null;
    public $k03_toleranciapgtoparc = 0;
    public $k03_pgtoparcial = 'f';
    public $k03_reemissaorecibo = 'f';
    public $k03_opcaoemissparcela = null;
    public $k03_numprepgtoparcial = 0;
    public $k03_agrupadorarquivotxtbaixabanco = 0;
    public $k03_receitapadraocredito = 0;
    public $k03_diasreemissaocertidao = 0;
    public $k03_toleranciacredito = 0;
    public $k03_templatecertidao_cgm = 0;
    public $k03_templatecertidao_matric = 0;
    public $k03_templatecertidao_inscr = 0;
    public $k03_templatecertidaopositiva_cgm = 0;
    public $k03_templatecertidaopositiva_matric = 0;
    public $k03_templatecertidaopositiva_inscr = 0;
    public $k03_templatecertidaonegativa_cgm = 0;
    public $k03_templatecertidaonegativa_matric = 0;
    public $k03_templatecertidaonegativa_inscr = 0;
    public $k03_templatecertidaoweb_cgm = 0;
    public $k03_templatecertidaoweb_matric = 0;
    public $k03_templatecertidaoweb_inscr = 0;
    public $k03_templatecertidaopositivaweb_cgm = 0;
    public $k03_templatecertidaopositivaweb_matric = 0;
    public $k03_templatecertidaopositivaweb_inscr = 0;
    public $k03_templatecertidaonegativaweb_cgm = 0;
    public $k03_templatecertidaonegativaweb_matric = 0;
    public $k03_templatecertidaonegativaweb_inscr = 0;
    public $k03_diascertidregular_cgm = 0;
    public $k03_diascertidregular_matric = 0;
    public $k03_diascertidregular_inscr = 0;
    public $k03_diascertidpositiva_cgm = 0;
    public $k03_diascertidpositiva_matric = 0;
    public $k03_diascertidpositiva_inscr = 0;
    public $k03_diascertidnegativa_cgm = 0;
    public $k03_diascertidnegativa_matric = 0;
    public $k03_diascertidnegativa_inscr = 0;
    public $k03_diascertidregularweb_cgm = 0;
    public $k03_diascertidregularweb_matric = 0;
    public $k03_diascertidregularweb_inscr = 0;
    public $k03_diascertidpositivaweb_cgm = 0;
    public $k03_diascertidpositivaweb_matric = 0;
    public $k03_diascertidpositivaweb_inscr = 0;
    public $k03_diascertidnegativaweb_cgm = 0;
    public $k03_diascertidnegativaweb_matric = 0;
    public $k03_diascertidnegativaweb_inscr = 0;
    public $k03_filtrardepart = '';
    public $k03_apenastiporenuncia = '';
    public $k03_filtrarreceita = '';
    public $k03_imprimecancdebitos = 'f';
    public $k03_usagrupotaxa = 'f';
    public $k03_encargoscarneunica = '';
    public $k03_processoparcelamento = 'f';
    public $k03_custashonorarios = 'f';
    public $k03_tipoinscricao = 'null';
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                k03_anousu = int4 = Exercício
                k03_instit = int4 = Cód. Instituição
                k03_numpre = int4 = Numeração
                k03_defope = int4 = Operação
                k03_recjur = int4 = Receita Juros
                k03_numsli = int4 = Slip
                k03_impend = bool = Imprime Endereço
                k03_unipri = bool = Única/Primeira
                k03_codbco = int4 = Banco
                k03_codage = char(5) = Agência
                k03_recmul = int4 = Receita Multa
                k03_calrec = bool = Receita Cálculo
                k03_msg = text = Mensagem
                k03_msgcarne = text = Mensagem exibida no carne
                k03_msgbanco = text = Mensagem do local de pagamento exibida no carne
                k03_certissvar = bool = Libera Variável
                k03_diasjust = int4 = Dias Justif.
                k03_reccert = bool = Recibo na certidão
                k03_taxagrupo = int4 = Código do grupo de taxas
                k03_tipocodcert = int4 = Tipo de Codificação
                k03_reciboprot = int4 = Tipo do Recibo do Protocolo
                k03_regracnd = int4 = Regra paraEmissão CND
                k03_reciboprotretencao = int4 = Tipo Recibo Retenção
                k03_tipocertidao = int4 = Forma Emissão Certidão de Débitos
                k03_separajurmulparc = int4 = Separar jur e mul no parcelamento
                k03_respcgm = int4 = Numcgm
                k03_respcargo = int4 = Cargo
                k03_msgautent = text = Mensagem Impressora Térmica
                k03_toleranciapgtoparc = numeric(15,2) = Valor Tolerância Diferença Pagamento
                k03_pgtoparcial = bool = Ativa Pagamento Parcial
                k03_reemissaorecibo = bool = Permite Reemissão de Recibo
                k03_opcaoemissparcela = char(1) = Parcela de Outros Exercicios
                k03_numprepgtoparcial = int8 = Numpre inicio pagamento parcial
                k03_agrupadorarquivotxtbaixabanco = int4 = Forma de Processamento de Arquivo TXT
                k03_receitapadraocredito = int4 = Receita
                k03_diasvalidadecertidao = int4 = Certidões Regulares
                k03_diasreemissaocertidao = int4 = Dias para reemissão das Certidões
                k03_toleranciacredito = float8 = Tolerância para Crédito
                k03_templatecertidao_cgm = int4 = Template de documento de Certidão Regular do E-Cidade filtrado por CGM
                k03_templatecertidao_matric = int4 = Template de documento de Certidão Regular do E-Cidade filtrado por Matricula
                k03_templatecertidao_inscr = int4 = Template de documento de Certidão Regular do E-Cidade filtrado por Inscrição
                k03_templatecertidaopositiva_cgm = int4 = Template de documento de Certidão Positiva do E-Cidade filtrado por CGM
                k03_templatecertidaopositiva_matric = int4 = Template de documento de Certidão Positiva do E-Cidade filtrado por Matricula
                k03_templatecertidaopositiva_inscr = int4 = Template de documento de Certidão Positiva do E-Cidade filtrado por Inscrição
                k03_templatecertidaonegativa_cgm = int4 = Template de documento de Certidão Negativa do E-Cidade filtrado por CGM
                k03_templatecertidaonegativa_matric = int4 = Template de documento de Certidão Negativa do E-Cidade filtrado por Matricula
                k03_templatecertidaonegativa_inscr = int4 = Template de documento de Certidão Negativa do E-Cidade filtrado por Inscrição
                k03_templatecertidaoweb_cgm = int4 = Template de documento de Certidão Regular do DBPref filtrado por CGM
                k03_templatecertidaoweb_matric = int4 = Template de documento de Certidão Regular do DBPref filtrado por Matricula
                k03_templatecertidaoweb_inscr = int4 = Template de documento de Certidão Regular do DBPref filtrado por Inscrição
                k03_templatecertidaopositivaweb_cgm = int4 = Template de documento de Certidão Positiva do DBPref filtrado por CGM
                k03_templatecertidaopositivaweb_matric = int4 = Template de documento de Certidão Positiva do DBPref filtrado por Matricula
                k03_templatecertidaopositivaweb_inscr = int4 = Template de documento de Certidão Positiva do DBPref filtrado por Inscrição
                k03_templatecertidaonegativaweb_cgm = int4 = Template de documento de Certidão Negativa do DBPref filtrado por CGM
                k03_templatecertidaonegativaweb_matric = int4 = Template de documento de Certidão Negativa do DBPref filtrado por Matricula
                k03_templatecertidaonegativaweb_inscr = int4 = Template de documento de Certidão Negativa do DBPref filtrado por Inscrição
                k03_diascertidregular_cgm = int4 = Certidão Regular do E-Cidade filtrado por CGM
                k03_diascertidregular_matric = int4 = Certidão Regular do E-Cidade filtrado por Matricula
                k03_diascertidregular_inscr = int4 = Certidão Regular do E-Cidade filtrado por Inscrição
                k03_diascertidpositiva_cgm = int4 = Certidão Positiva do E-Cidade filtrado por CGM
                k03_diascertidpositiva_matric = int4 = Certidão Positiva do E-Cidade filtrado por Matricula
                k03_diascertidpositiva_inscr = int4 = Certidão Positiva do E-Cidade filtrado por Inscrição
                k03_diascertidnegativa_cgm = int4 = Certidão Negativa do E-Cidade filtrado por CGM
                k03_diascertidnegativa_matric = int4 = Certidão Negativa do E-Cidade filtrado por Matricula
                k03_diascertidnegativa_inscr = int4 = Certidão Negativa do E-Cidade filtrado por Inscrição
                k03_diascertidregularweb_cgm = int4 = Certidão Regular do DBPref filtrado por CGM
                k03_diascertidregularweb_matric = int4 = Certidão Regular do DBPref filtrado por Matricula
                k03_diascertidregularweb_inscr = int4 = Certidão Regular do DBPref filtrado por Inscrição
                k03_diascertidpositivaweb_cgm = int4 = Certidão Positiva do DBPref filtrado por CGM
                k03_diascertidpositivaweb_matric = int4 = Certidão Positiva do DBPref filtrado por Matricula
                k03_diascertidpositivaweb_inscr = int4 = Certidão Positiva do DBPref filtrado por Inscrição
                k03_diascertidnegativaweb_cgm = int4 = Certidão Negativa do DBPref filtrado por CGM
                k03_diascertidnegativaweb_matric = int4 = Certidão Negativa do DBPref filtrado por Matricula
                k03_diascertidnegativaweb_inscr = int4 = Certidão Negativa do DBPref filtrado por Inscrição
                k03_filtrardepart = bool = Filtrar processamento de cancelamentos por departamento
                k03_apenastiporenuncia = bool = Habilitar apenas tipo de renuncia para cancelamentos na cgf
                k03_filtrarreceita = bool = Filtrar receitas ao buscar regras de parcelamento
                k03_imprimecancdebitos = bool = Permite Impressão Cancelamento Débitos
                k03_usagrupotaxa = bool = Usa Grupo de Taxa
                k03_processoparcelamento = bool = Processo Obrigatório Parcelamento
                k03_custashonorarios = bool = Habilita custas e honorários
                k03_tipoinscricao = int4 = Vinculo nas procedencias nas inscricoes em cob ou divida
                ";

    public function __construct()
    {
        $this->rotulo = new rotulo("numpref");
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
    }

    public function erro($mostra, $retorna)
    {
     if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
        echo "<script>alert(\"".$this->erro_msg."\")</script>";
        if($retorna==true){
           echo "<script>location.href='".$this->pagina_retorno."'</script>";
        }
     }
   }

    public function atualizacampos($exclusao = false)
    {
        if ($exclusao == false) {
            $this->k03_anousu = ($this->k03_anousu == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_anousu"] : $this->k03_anousu);
            $this->k03_instit = ($this->k03_instit == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_instit"] : $this->k03_instit);
            $this->k03_numpre = ($this->k03_numpre == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_numpre"] : $this->k03_numpre);
            $this->k03_defope = ($this->k03_defope == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_defope"] : $this->k03_defope);
            $this->k03_recjur = ($this->k03_recjur == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_recjur"] : $this->k03_recjur);
            $this->k03_numsli = ($this->k03_numsli == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_numsli"] : $this->k03_numsli);
            $this->k03_impend = ($this->k03_impend == "f" ? @$GLOBALS["HTTP_POST_VARS"]["k03_impend"] : $this->k03_impend);
            $this->k03_unipri = ($this->k03_unipri == "f" ? @$GLOBALS["HTTP_POST_VARS"]["k03_unipri"] : $this->k03_unipri);
            $this->k03_codbco = ($this->k03_codbco == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_codbco"] : $this->k03_codbco);
            $this->k03_codage = ($this->k03_codage == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_codage"] : $this->k03_codage);
            $this->k03_recmul = ($this->k03_recmul == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_recmul"] : $this->k03_recmul);
            $this->k03_calrec = ($this->k03_calrec == "f" ? @$GLOBALS["HTTP_POST_VARS"]["k03_calrec"] : $this->k03_calrec);
            $this->k03_msg = ($this->k03_msg == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_msg"] : $this->k03_msg);
            $this->k03_msgcarne = ($this->k03_msgcarne == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_msgcarne"] : $this->k03_msgcarne);
            $this->k03_msgbanco = ($this->k03_msgbanco == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_msgbanco"] : $this->k03_msgbanco);
            $this->k03_certissvar = ($this->k03_certissvar == "f" ? @$GLOBALS["HTTP_POST_VARS"]["k03_certissvar"] : $this->k03_certissvar);
            $this->k03_diasjust = ($this->k03_diasjust == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_diasjust"] : $this->k03_diasjust);
            $this->k03_reccert = ($this->k03_reccert == "f" ? @$GLOBALS["HTTP_POST_VARS"]["k03_reccert"] : $this->k03_reccert);
            $this->k03_taxagrupo = ($this->k03_taxagrupo == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_taxagrupo"] : $this->k03_taxagrupo);
            $this->k03_tipocodcert = ($this->k03_tipocodcert == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_tipocodcert"] : $this->k03_tipocodcert);
            $this->k03_reciboprot = ($this->k03_reciboprot == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_reciboprot"] : $this->k03_reciboprot);
            $this->k03_regracnd = ($this->k03_regracnd == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_regracnd"] : $this->k03_regracnd);
            $this->k03_reciboprotretencao = ($this->k03_reciboprotretencao == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_reciboprotretencao"] : $this->k03_reciboprotretencao);
            $this->k03_tipocertidao = ($this->k03_tipocertidao == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_tipocertidao"] : $this->k03_tipocertidao);
            $this->k03_separajurmulparc = ($this->k03_separajurmulparc == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_separajurmulparc"] : $this->k03_separajurmulparc);
            $this->k03_respcgm = ($this->k03_respcgm == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_respcgm"] : $this->k03_respcgm);
            $this->k03_respcargo = ($this->k03_respcargo == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_respcargo"] : $this->k03_respcargo);
            $this->k03_msgautent = ($this->k03_msgautent == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_msgautent"] : $this->k03_msgautent);
            $this->k03_toleranciapgtoparc = ($this->k03_toleranciapgtoparc == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_toleranciapgtoparc"] : $this->k03_toleranciapgtoparc);
            $this->k03_pgtoparcial = ($this->k03_pgtoparcial == "f" ? @$GLOBALS["HTTP_POST_VARS"]["k03_pgtoparcial"] : $this->k03_pgtoparcial);
            $this->k03_reemissaorecibo = ($this->k03_reemissaorecibo == "f" ? @$GLOBALS["HTTP_POST_VARS"]["k03_reemissaorecibo"] : $this->k03_reemissaorecibo);
            $this->k03_opcaoemissparcela = ($this->k03_opcaoemissparcela == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_opcaoemissparcela"] : $this->k03_opcaoemissparcela);
            $this->k03_numprepgtoparcial = ($this->k03_numprepgtoparcial == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_numprepgtoparcial"] : $this->k03_numprepgtoparcial);
            $this->k03_agrupadorarquivotxtbaixabanco = ($this->k03_agrupadorarquivotxtbaixabanco == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_agrupadorarquivotxtbaixabanco"] : $this->k03_agrupadorarquivotxtbaixabanco);
            $this->k03_receitapadraocredito = ($this->k03_receitapadraocredito == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_receitapadraocredito"] : $this->k03_receitapadraocredito);
            $this->k03_templatecertidao_cgm = ($this->k03_templatecertidao_cgm == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_templatecertidao_cgm"] : $this->k03_templatecertidao_cgm);
            $this->k03_templatecertidao_matric = ($this->k03_templatecertidao_matric == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_templatecertidao_matric"] : $this->k03_templatecertidao_matric);
            $this->k03_templatecertidao_inscr = ($this->k03_templatecertidao_inscr == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_templatecertidao_inscr"] : $this->k03_templatecertidao_inscr);
            $this->k03_templatecertidaopositiva_cgm = ($this->k03_templatecertidaopositiva_cgm == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaopositiva_cgm"] : $this->k03_templatecertidaopositiva_cgm);
            $this->k03_templatecertidaopositiva_matric = ($this->k03_templatecertidaopositiva_matric == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaopositiva_matric"] : $this->k03_templatecertidaopositiva_matric);
            $this->k03_templatecertidaopositiva_inscr = ($this->k03_templatecertidaopositiva_inscr == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaopositiva_inscr"] : $this->k03_templatecertidaopositiva_inscr);
            $this->k03_templatecertidaonegativa_cgm = ($this->k03_templatecertidaonegativa_cgm == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaonegativa_cgm"] : $this->k03_templatecertidaonegativa_cgm);
            $this->k03_templatecertidaonegativa_matric = ($this->k03_templatecertidaonegativa_matric == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaonegativa_matric"] : $this->k03_templatecertidaonegativa_matric);
            $this->k03_templatecertidaonegativa_inscr = ($this->k03_templatecertidaonegativa_inscr == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaonegativa_inscr"] : $this->k03_templatecertidaonegativa_inscr);
            $this->k03_templatecertidaoweb_cgm = ($this->k03_templatecertidaoweb_cgm == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaoweb_cgm"] : $this->k03_templatecertidaoweb_cgm);
            $this->k03_templatecertidaoweb_matric = ($this->k03_templatecertidaoweb_matric == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaoweb_matric"] : $this->k03_templatecertidaoweb_matric);
            $this->k03_templatecertidaoweb_inscr = ($this->k03_templatecertidaoweb_inscr == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaoweb_inscr"] : $this->k03_templatecertidaoweb_inscr);
            $this->k03_templatecertidaopositivaweb_cgm = ($this->k03_templatecertidaopositivaweb_cgm == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaopositivaweb_cgm"] : $this->k03_templatecertidaopositivaweb_cgm);
            $this->k03_templatecertidaopositivaweb_matric = ($this->k03_templatecertidaopositivaweb_matric == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaopositivaweb_matric"] : $this->k03_templatecertidaopositivaweb_matric);
            $this->k03_templatecertidaopositivaweb_inscr = ($this->k03_templatecertidaopositivaweb_inscr == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaopositivaweb_inscr"] : $this->k03_templatecertidaopositivaweb_inscr);
            $this->k03_templatecertidaonegativaweb_cgm = ($this->k03_templatecertidaonegativaweb_cgm == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaonegativaweb_cgm"] : $this->k03_templatecertidaonegativaweb_cgm);
            $this->k03_templatecertidaonegativaweb_matric = ($this->k03_templatecertidaonegativaweb_matric == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaonegativaweb_matric"] : $this->k03_templatecertidaonegativaweb_matric);
            $this->k03_templatecertidaonegativaweb_inscr = ($this->k03_templatecertidaonegativaweb_inscr == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaonegativaweb_inscr"] : $this->k03_templatecertidaonegativaweb_inscr);
            $this->k03_diascertidregular_cgm = ($this->k03_diascertidregular_cgm == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_diascertidregular_cgm"] : $this->k03_diascertidregular_cgm);
            $this->k03_diascertidregular_matric = ($this->k03_diascertidregular_matric == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_diascertidregular_matric"] : $this->k03_diascertidregular_matric);
            $this->k03_diascertidregular_inscr = ($this->k03_diascertidregular_inscr == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_diascertidregular_inscr"] : $this->k03_diascertidregular_inscr);
            $this->k03_diascertidpositiva_cgm = ($this->k03_diascertidpositiva_cgm == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_diascertidpositiva_cgm"] : $this->k03_diascertidpositiva_cgm);
            $this->k03_diascertidpositiva_matric = ($this->k03_diascertidpositiva_matric == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_diascertidpositiva_matric"] : $this->k03_diascertidpositiva_matric);
            $this->k03_diascertidpositiva_inscr = ($this->k03_diascertidpositiva_inscr == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_diascertidpositiva_inscr"] : $this->k03_diascertidpositiva_inscr);
            $this->k03_diascertidnegativa_cgm = ($this->k03_diascertidnegativa_cgm == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_diascertidnegativa_cgm"] : $this->k03_diascertidnegativa_cgm);
            $this->k03_diascertidnegativa_matric = ($this->k03_diascertidnegativa_matric == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_diascertidnegativa_matric"] : $this->k03_diascertidnegativa_matric);
            $this->k03_diascertidnegativa_inscr = ($this->k03_diascertidnegativa_inscr == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_diascertidnegativa_inscr"] : $this->k03_diascertidnegativa_inscr);
            $this->k03_diascertidregularweb_cgm = ($this->k03_diascertidregularweb_cgm == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_diascertidregularweb_cgm"] : $this->k03_diascertidregularweb_cgm);
            $this->k03_diascertidregularweb_matric = ($this->k03_diascertidregularweb_matric == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_diascertidregularweb_matric"] : $this->k03_diascertidregularweb_matric);
            $this->k03_diascertidregularweb_inscr = ($this->k03_diascertidregularweb_inscr == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_diascertidregularweb_inscr"] : $this->k03_diascertidregularweb_inscr);
            $this->k03_diascertidpositivaweb_cgm = ($this->k03_diascertidpositivaweb_cgm == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_diascertidpositivaweb_cgm"] : $this->k03_diascertidpositivaweb_cgm);
            $this->k03_diascertidpositivaweb_matric = ($this->k03_diascertidpositivaweb_matric == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_diascertidpositivaweb_matric"] : $this->k03_diascertidpositivaweb_matric);
            $this->k03_diascertidpositivaweb_inscr = ($this->k03_diascertidpositivaweb_inscr == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_diascertidpositivaweb_inscr"] : $this->k03_diascertidpositivaweb_inscr);
            $this->k03_diascertidnegativaweb_cgm = ($this->k03_diascertidnegativaweb_cgm == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_diascertidnegativaweb_cgm"] : $this->k03_diascertidnegativaweb_cgm);
            $this->k03_diascertidnegativaweb_matric = ($this->k03_diascertidnegativaweb_matric == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_diascertidnegativaweb_matric"] : $this->k03_diascertidnegativaweb_matric);
            $this->k03_diascertidnegativaweb_inscr = ($this->k03_diascertidnegativaweb_inscr == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_diascertidnegativaweb_inscr"] : $this->k03_diascertidnegativaweb_inscr);
            $this->k03_diasreemissaocertidao = ($this->k03_diasreemissaocertidao == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_diasreemissaocertidao"] : $this->k03_diasreemissaocertidao);
            $this->k03_filtrardepart = ($this->k03_filtrardepart == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_filtrardepart"] : 'f');
            $this->k03_apenastiporenuncia = ($this->k03_apenastiporenuncia == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_apenastiporenuncia"] : 't');
            $this->k03_filtrarreceita = ($this->k03_filtrarreceita == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_filtrarreceita"] : 'f');
            $this->k03_imprimecancdebitos = ($this->k03_imprimecancdebitos == "f"?@$GLOBALS["HTTP_POST_VARS"]["k03_imprimecancdebitos"]:$this->k03_imprimecancdebitos);
            $this->k03_usagrupotaxa = ($this->k03_usagrupotaxa == "f"?@$GLOBALS["HTTP_POST_VARS"]["k03_usagrupotaxa"]:$this->k03_usagrupotaxa);
            $this->k03_processoparcelamento = ($this->k03_processoparcelamento == "f"?@$GLOBALS["HTTP_POST_VARS"]["k03_processoparcelamento"]:$this->k03_processoparcelamento);
            $this->k03_encargoscarneunica = ($this->k03_encargoscarneunica == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_encargoscarneunica"] : 'f');
            $this->k03_custashonorarios = ($this->k03_custashonorarios == "f"?@$GLOBALS["HTTP_POST_VARS"]["k03_custashonorarios"]:$this->k03_custashonorarios);
            $this->k03_tipoinscricao = ($this->k03_tipoinscricao == "null"?@$GLOBALS["HTTP_POST_VARS"]["k03_tipoinscricao"]:$this->k03_tipoinscricao);
        } else {
            $this->k03_anousu = ($this->k03_anousu == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_anousu"] : $this->k03_anousu);
            $this->k03_instit = ($this->k03_instit == "" ? @$GLOBALS["HTTP_POST_VARS"]["k03_instit"] : $this->k03_instit);
        }
    }

    public function incluir($k03_anousu,$k03_instit)
    {
        $this->atualizacampos();
        if ($this->k03_numpre == null) {
            $this->k03_numpre = "0";
        }
        if ($this->k03_defope == null) {
            $this->erro_sql = " Campo Operação não informado.";
            $this->erro_campo = "k03_defope";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k03_recjur == null) {
            $this->erro_sql = " Campo Receita Juros não informado.";
            $this->erro_campo = "k03_recjur";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k03_numsli == null) {
            $this->erro_sql = " Campo Slip não informado.";
            $this->erro_campo = "k03_numsli";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k03_impend == null) {
            $this->erro_sql = " Campo Imprime Endereço não informado.";
            $this->erro_campo = "k03_impend";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k03_unipri == null) {
            $this->erro_sql = " Campo Única/Primeira não informado.";
            $this->erro_campo = "k03_unipri";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k03_codbco == null) {
            $this->erro_sql = " Campo Banco não informado.";
            $this->erro_campo = "k03_codbco";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k03_codage == null) {
            $this->erro_sql = " Campo Agência não informado.";
            $this->erro_campo = "k03_codage";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k03_recmul == null) {
            $this->erro_sql = " Campo Receita Multa não informado.";
            $this->erro_campo = "k03_recmul";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k03_calrec == null) {
            $this->erro_sql = " Campo Receita Cálculo não informado.";
            $this->erro_campo = "k03_calrec";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k03_certissvar == null) {
            $this->erro_sql = " Campo Libera Variável não informado.";
            $this->erro_campo = "k03_certissvar";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k03_diasjust == null) {
            $this->erro_sql = " Campo Dias Justif. não informado.";
            $this->erro_campo = "k03_diasjust";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k03_reccert == null) {
            $this->erro_sql = " Campo Recibo na certidão não informado.";
            $this->erro_campo = "k03_reccert";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k03_taxagrupo == null) {
            $this->erro_sql = " Campo Código do grupo de taxas não informado.";
            $this->erro_campo = "k03_taxagrupo";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k03_tipocodcert == null) {
            $this->erro_sql = " Campo Tipo de Codificação não informado.";
            $this->erro_campo = "k03_tipocodcert";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k03_reciboprot == null) {
            $this->erro_sql = " Campo Tipo do Recibo do Protocolo não informado.";
            $this->erro_campo = "k03_reciboprot";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k03_regracnd == null) {
            $this->erro_sql = " Campo Regra paraEmissão CND não informado.";
            $this->erro_campo = "k03_regracnd";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k03_reciboprotretencao == null) {
            $this->erro_sql = " Campo Tipo Recibo Retenção não informado.";
            $this->erro_campo = "k03_reciboprotretencao";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k03_tipocertidao == null) {
            $this->erro_sql = " Campo Forma Emissão Certidão de Débitos não informado.";
            $this->erro_campo = "k03_tipocertidao";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k03_separajurmulparc == null) {
            $this->erro_sql = " Campo Separar jur e mul no parcelamento não informado.";
            $this->erro_campo = "k03_separajurmulparc";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k03_respcgm == null) {
            $this->k03_respcgm = "null";
        }
        if ($this->k03_respcargo == null) {
            $this->k03_respcargo = "null";
        }
        if ($this->k03_toleranciapgtoparc == null) {
            $this->erro_sql = " Campo Valor Tolerância Diferença Pagamento não informado.";
            $this->erro_campo = "k03_toleranciapgtoparc";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k03_pgtoparcial == null) {
            $this->erro_sql = " Campo Ativa Pagamento Parcial não informado.";
            $this->erro_campo = "k03_pgtoparcial";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k03_reemissaorecibo == null) {
            $this->erro_sql = " Campo Permite Reemissão de Recibo não informado.";
            $this->erro_campo = "k03_reemissaorecibo";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k03_numprepgtoparcial == null) {
            $this->erro_sql = " Campo Numpre inicio pagamento parcial não informado.";
            $this->erro_campo = "k03_numprepgtoparcial";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k03_agrupadorarquivotxtbaixabanco == null) {
            $this->erro_sql = " Campo Forma de Processamento de Arquivo TXT não informado.";
            $this->erro_campo = "k03_agrupadorarquivotxtbaixabanco";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k03_diasreemissaocertidao == null) {
            $this->erro_sql = " Campo Dias para reemissão das Certidões não informado.";
            $this->erro_campo = "k03_diasreemissaocertidao";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k03_diasvalidadecertidao == null) {
            $this->erro_sql = " Campo Dias para vencimento das Certidões Regulares não informado.";
            $this->erro_campo = "k03_diasvalidadecertidao";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if($this->k03_toleranciacredito == null ){
            $this->erro_sql = " Campo Tolerãncia para Crédito não informado.";
            $this->erro_campo = "k03_toleranciacredito";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k03_receitapadraocredito == null) {
            $this->k03_receitapadraocredito = 'null';
        }
        if($this->k03_filtrardepart == null){
            $this->k03_filtrardepart = 'f';
        }
        if($this->k03_apenastiporenuncia == null){
            $this->k03_apenastiporenuncia = 't';
        }
        if($this->k03_filtrarreceita == null){
            $this->k03_filtrarreceita = 'f';
        }

        if ($k03_numpre == "" || $k03_numpre == null) {
            $result = db_query("select nextval('numpref_k03_numpre_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: numpref_k03_numpre_seq do campo: k03_numpre";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->k03_numpre = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from numpref_k03_numpre_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $k03_numpre)) {
                $this->erro_sql = " Campo k03_numpre maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->k03_numpre = $k03_numpre;
            }
        }
        if (($this->k03_anousu == null) || ($this->k03_anousu == "")) {
            $this->erro_sql = " Campo k03_anousu nao declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if (($this->k03_instit == null) || ($this->k03_instit == "")) {
            $this->erro_sql = " Campo k03_instit nao declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }

        #################################### CERTIDÕES ####################################

                            ################ CERTIDÕES POSITIVAS >>> ################
        if($this->k03_diascertidpositiva_cgm == null) {
            $this->erro_sql = " Campo Dias de Validade da Certidão Positiva por CGM via E-Cidade não informado.";
            $this->erro_campo = "k03_diascertidpositiva_cgm";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }

        if($this->k03_diascertidpositiva_matric == null) {
            $this->erro_sql = " Campo Dias de Validade da Certidão Positiva por Matricula via E-Cidade não informado.";
            $this->erro_campo = "k03_diascertidpositiva_matric";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }

        if($this->k03_diascertidpositiva_inscr == null) {
            $this->erro_sql = " Campo Dias de Validade da Certidão Positiva por Inscrição via E-Cidade não informado.";
            $this->erro_campo = "k03_diascertidpositiva_inscr";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }

        if($this->k03_diascertidpositivaweb_cgm == null) {
            $this->erro_sql = " Campo Dias de Validade da Certidão Positiva por CGM via DBPref não informado.";
            $this->erro_campo = "k03_diascertidpositivaweb_cgm";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }

        if($this->k03_diascertidpositivaweb_matric == null) {
            $this->erro_sql = " Campo Dias de Validade da Certidão Positiva por Matricula via DBPref não informado.";
            $this->erro_campo = "k03_diascertidpositivaweb_matric";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }

        if($this->k03_diascertidpositivaweb_inscr == null) {
            $this->erro_sql = " Campo Dias de Validade da Certidão Positiva por Inscrição via DBPref não informado.";
            $this->erro_campo = "k03_diascertidpositivaweb_inscr";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
                            ################ <<< CERTIDÕES POSITIVAS ################

                            ################ CERTIDÕES REGULAR >>> ################
        if($this->k03_diascertidregular_cgm == null) {
            $this->erro_sql = " Campo Dias de Validade da Certidão Regular por CGM via E-Cidade não informado.";
            $this->erro_campo = "k03_diascertidregular_cgm";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }

        if($this->k03_diascertidregular_matric == null) {
            $this->erro_sql = " Campo Dias de Validade da Certidão Regular por Matricula via E-Cidade não informado.";
            $this->erro_campo = "k03_diascertidregular_matric";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }

        if($this->k03_diascertidregular_inscr == null) {
            $this->erro_sql = " Campo Dias de Validade da Certidão Regular por Inscrição via E-Cidade não informado.";
            $this->erro_campo = "k03_diascertidregular_inscr";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }

        if($this->k03_diascertidregularweb_cgm == null) {
            $this->erro_sql = " Campo Dias de Validade da Certidão Regular por CGM via DBPref não informado.";
            $this->erro_campo = "k03_diascertidregularweb_cgm";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }

        if($this->k03_diascertidregularweb_matric == null) {
            $this->erro_sql = " Campo Dias de Validade da Certidão Regular por Matricula via DBPref não informado.";
            $this->erro_campo = "k03_diascertidregularweb_matric";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }

        if($this->k03_diascertidregularweb_inscr == null) {
            $this->erro_sql = " Campo Dias de Validade da Certidão regular por Inscrição via DBPref não informado.";
            $this->erro_campo = "k03_diascertidregularweb_inscr";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
                            ################ <<< CERTIDÕES REGULAR ################

                            ################ CERTIDÕES NEGATIVA >>> ################
        if($this->k03_diascertidnegativa_cgm == null) {
            $this->erro_sql = " Campo Dias de Validade da Certidão Negativa por CGM via E-Cidade não informado.";
            $this->erro_campo = "k03_diascertidnegativa_cgm";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }

        if($this->k03_diascertidnegativa_matric == null) {
            $this->erro_sql = " Campo Dias de Validade da Certidão Negativa por Matricula via E-Cidade não informado.";
            $this->erro_campo = "k03_diascertidnegativa_matric";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }

        if($this->k03_diascertidnegativa_inscr == null) {
            $this->erro_sql = " Campo Dias de Validade da Certidão Negativa por Inscrição via E-Cidade não informado.";
            $this->erro_campo = "k03_diascertidnegativa_inscr";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }

        if($this->k03_diascertidnegativaweb_cgm == null) {
            $this->erro_sql = " Campo Dias de Validade da Certidão Negativa por CGM via DBPref não informado.";
            $this->erro_campo = "k03_diascertidnegativaweb_cgm";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }

        if($this->k03_diascertidnegativaweb_matric == null) {
            $this->erro_sql = " Campo Dias de Validade da Certidão Negativa por Matricula via DBPref não informado.";
            $this->erro_campo = "k03_diascertidnegativaweb_matric";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }

        if($this->k03_diascertidnegativaweb_inscr == null) {
            $this->erro_sql = " Campo Dias de Validade da Certidão Negativa por Inscrição via DBPref não informado.";
            $this->erro_campo = "k03_diascertidnegativaweb_inscr";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
                            ################ <<< CERTIDÕES NEGATIVA ################
        #################################### CERTIDÕES ####################################
        if($this->k03_imprimecancdebitos == null ){
         $this->erro_sql = " Campo Permite Impressão Cancelamento Débitos não informado.";
         $this->erro_campo = "k03_imprimecancdebitos";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }

        if($this->k03_usagrupotaxa == null ){
            $this->erro_sql = " Campo Usa Grupo de Taxa não informado.";
            $this->erro_campo = "k03_usagrupotaxa";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }

        if($this->k03_processoparcelamento == null ){
            $this->erro_sql = " Campo Processo Obrigatório Parcelamento não informado.";
            $this->erro_campo = "k03_processoparcelamento";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }

        if($this->k03_custashonorarios == null ){
            $this->erro_sql = " Campo habilita custas e honorários não informado.";
            $this->erro_campo = "k03_custashonorarios";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }

        if($this->k03_encargoscarneunica == null ){
            $this->erro_sql = " Campo Emissão de carnê CGF não informado.";
            $this->erro_campo = "k03_encargoscarneunica";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }

        $sql = "insert into numpref(
                                       k03_anousu
                                      ,k03_instit
                                      ,k03_numpre
                                      ,k03_defope
                                      ,k03_recjur
                                      ,k03_numsli
                                      ,k03_impend
                                      ,k03_unipri
                                      ,k03_codbco
                                      ,k03_codage
                                      ,k03_recmul
                                      ,k03_calrec
                                      ,k03_msg
                                      ,k03_msgcarne
                                      ,k03_msgbanco
                                      ,k03_certissvar
                                      ,k03_diasjust
                                      ,k03_reccert
                                      ,k03_taxagrupo
                                      ,k03_tipocodcert
                                      ,k03_reciboprot
                                      ,k03_regracnd
                                      ,k03_reciboprotretencao
                                      ,k03_tipocertidao
                                      ,k03_separajurmulparc
                                      ,k03_respcgm
                                      ,k03_respcargo
                                      ,k03_msgautent
                                      ,k03_toleranciapgtoparc
                                      ,k03_pgtoparcial
                                      ,k03_reemissaorecibo
                                      ,k03_opcaoemissparcela
                                      ,k03_numprepgtoparcial
                                      ,k03_agrupadorarquivotxtbaixabanco
                                      ,k03_receitapadraocredito
                                      ,k03_diasreemissaocertidao
                                      ,k03_toleranciacredito
                                      ,k03_diascertidpositiva_cgm
                                      ,k03_diascertidpositiva_matric
                                      ,k03_diascertidpositiva_inscr
                                      ,k03_diascertidpositivaweb_cgm
                                      ,k03_diascertidpositivaweb_matric
                                      ,k03_diascertidpositivaweb_inscr
                                      ,k03_templatecertidaopositiva_cgm
                                      ,k03_templatecertidaopositiva_matric
                                      ,k03_templatecertidaopositiva_inscr
                                      ,k03_templatecertidaopositivaweb_cgm
                                      ,k03_templatecertidaopositivaweb_matric
                                      ,k03_templatecertidaopositivaweb_inscr
                                      ,k03_templatecertidao_cgm
                                      ,k03_templatecertidao_matric
                                      ,k03_templatecertidao_inscr
                                      ,k03_diascertidregular_cgm
                                      ,k03_diascertidregular_matric
                                      ,k03_diascertidregular_inscr
                                      ,k03_templatecertidaoweb_cgm
                                      ,k03_templatecertidaoweb_matric
                                      ,k03_templatecertidaoweb_inscr
                                      ,k03_diascertidregularweb_cgm
                                      ,k03_diascertidregularweb_matric
                                      ,k03_diascertidregularweb_inscr
                                      ,k03_diascertidnegativa_cgm
                                      ,k03_diascertidnegativa_matric
                                      ,k03_diascertidnegativa_inscr
                                      ,k03_templatecertidaonegativa_cgm
                                      ,k03_templatecertidaonegativa_matric
                                      ,k03_templatecertidaonegativa_inscr
                                      ,k03_templatecertidaonegativaweb_cgm
                                      ,k03_templatecertidaonegativaweb_matric
                                      ,k03_templatecertidaonegativaweb_inscr
                                      ,k03_diascertidnegativaweb_cgm
                                      ,k03_diascertidnegativaweb_matric
                                      ,k03_diascertidnegativaweb_inscr
                                      ,k03_filtrardepart
                                      ,k03_apenastiporenuncia
                                      ,k03_filtrarreceita
                                      ,k03_imprimecancdebitos
                                      ,k03_usagrupotaxa
                                      ,k03_encargoscarneunica
                                      ,k03_processoparcelamento
                                      ,k03_custashonorarios
                                      ,k03_tipoinscricao
                       )
                values (
                                $this->k03_anousu
                               ,$this->k03_instit
                               ,$this->k03_numpre
                               ,$this->k03_defope
                               ,$this->k03_recjur
                               ,$this->k03_numsli
                               ,'$this->k03_impend'
                               ,'$this->k03_unipri'
                               ,$this->k03_codbco
                               ,'$this->k03_codage'
                               ,$this->k03_recmul
                               ,'$this->k03_calrec'
                               ,'$this->k03_msg'
                               ,'$this->k03_msgcarne'
                               ,'$this->k03_msgbanco'
                               ,'$this->k03_certissvar'
                               ,$this->k03_diasjust
                               ,'$this->k03_reccert'
                               ,$this->k03_taxagrupo
                               ,$this->k03_tipocodcert
                               ,$this->k03_reciboprot
                               ,$this->k03_regracnd
                               ,$this->k03_reciboprotretencao
                               ,$this->k03_tipocertidao
                               ,$this->k03_separajurmulparc
                               ,$this->k03_respcgm
                               ,$this->k03_respcargo
                               ,'$this->k03_msgautent'
                               ,$this->k03_toleranciapgtoparc
                               ,'$this->k03_pgtoparcial'
                               ,'$this->k03_reemissaorecibo'
                               ,'$this->k03_opcaoemissparcela'
                               ,$this->k03_numprepgtoparcial
                               ,$this->k03_agrupadorarquivotxtbaixabanco
                               ,$this->k03_receitapadraocredito
                               ,$this->k03_diasreemissaocertidao
                               ,$this->k03_toleranciacredito
                               ,$this->k03_diascertidpositiva_cgm
                               ,$this->k03_diascertidpositiva_matric
                               ,$this->k03_diascertidpositiva_inscr
                               ,$this->k03_diascertidpositivaweb_cgm
                               ,$this->k03_diascertidpositivaweb_matric
                               ,$this->k03_diascertidpositivaweb_inscr
                               ,$this->k03_templatecertidaopositiva_cgm
                               ,$this->k03_templatecertidaopositiva_matric
                               ,$this->k03_templatecertidaopositiva_inscr
                               ,$this->k03_templatecertidaopositivaweb_cgm
                               ,$this->k03_templatecertidaopositivaweb_matric
                               ,$this->k03_templatecertidaopositivaweb_inscr
                               ,$this->k03_templatecertidao_cgm
                               ,$this->k03_templatecertidao_matric
                               ,$this->k03_templatecertidao_inscr
                               ,$this->k03_diascertidregular_cgm
                               ,$this->k03_diascertidregular_matric
                               ,$this->k03_diascertidregular_inscr
                               ,$this->k03_templatecertidaoweb_cgm
                               ,$this->k03_templatecertidaoweb_matric
                               ,$this->k03_templatecertidaoweb_inscr
                               ,$this->k03_diascertidregularweb_cgm
                               ,$this->k03_diascertidregularweb_matric
                               ,$this->k03_diascertidregularweb_inscr
                               ,$this->k03_diascertidnegativa_cgm
                               ,$this->k03_diascertidnegativa_matric
                               ,$this->k03_diascertidnegativa_inscr
                               ,$this->k03_templatecertidaonegativa_cgm
                               ,$this->k03_templatecertidaonegativa_matric
                               ,$this->k03_templatecertidaonegativa_inscr
                               ,$this->k03_templatecertidaonegativaweb_cgm
                               ,$this->k03_templatecertidaonegativaweb_matric
                               ,$this->k03_templatecertidaonegativaweb_inscr
                               ,$this->k03_diascertidnegativaweb_cgm
                               ,$this->k03_diascertidnegativaweb_matric
                               ,$this->k03_diascertidnegativaweb_inscr
                               ,$this->k03_filtrardepart
                               ,$this->k03_apenastiporenuncia
                               ,$this->k03_filtrarreceita
                               ,'$this->k03_imprimecancdebitos'
                               ,'$this->k03_usagrupotaxa'
                               ,'$this->k03_encargoscarneunica'
                               ,'$this->k03_processoparcelamento'
                               ,'$this->k03_custashonorarios'
                               ,$this->k03_tipoinscricao
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "Numerações ($this->k03_anousu." - ".$this->k03_instit) nao Incluído. Inclusao Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "Numerações já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "Numerações ($this->k03_anousu." - ".$this->k03_instit) nao Incluído. Inclusao Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : " . $this->k03_anousu . "-" . $this->k03_instit;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        return true;
    }

    public function alterar($k03_anousu = null, $k03_instit = null)
    {

        $this->atualizacampos();
        $sql = " update numpref set ";
        $virgula = "";
        if (trim($this->k03_anousu) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_anousu"])) {
            $sql .= $virgula . " k03_anousu = $this->k03_anousu ";
            $virgula = ",";
            if (trim($this->k03_anousu) == null) {
                $this->erro_sql = " Campo Exercício não informado.";
                $this->erro_campo = "k03_anousu";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_instit) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_instit"])) {
            $sql .= $virgula . " k03_instit = $this->k03_instit ";
            $virgula = ",";
            if (trim($this->k03_instit) == null) {
                $this->erro_sql = " Campo Cód. Instituição não informado.";
                $this->erro_campo = "k03_instit";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_numpre) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_numpre"])) {
            if (trim($this->k03_numpre) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k03_numpre"])) {
                $this->k03_numpre = "0";
            }
            $sql .= $virgula . " k03_numpre = $this->k03_numpre ";
            $virgula = ",";
        }
        if (trim($this->k03_defope) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_defope"])) {
            $sql .= $virgula . " k03_defope = $this->k03_defope ";
            $virgula = ",";
            if (trim($this->k03_defope) == null) {
                $this->erro_sql = " Campo Operação não informado.";
                $this->erro_campo = "k03_defope";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_recjur) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_recjur"])) {
            $sql .= $virgula . " k03_recjur = $this->k03_recjur ";
            $virgula = ",";
            if (trim($this->k03_recjur) == null) {
                $this->erro_sql = " Campo Receita Juros não informado.";
                $this->erro_campo = "k03_recjur";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_numsli) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_numsli"])) {
            $sql .= $virgula . " k03_numsli = $this->k03_numsli ";
            $virgula = ",";
            if (trim($this->k03_numsli) == null) {
                $this->erro_sql = " Campo Slip não informado.";
                $this->erro_campo = "k03_numsli";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_impend) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_impend"])) {
            $sql .= $virgula . " k03_impend = '$this->k03_impend' ";
            $virgula = ",";
            if (trim($this->k03_impend) == null) {
                $this->erro_sql = " Campo Imprime Endereço não informado.";
                $this->erro_campo = "k03_impend";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_unipri) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_unipri"])) {
            $sql .= $virgula . " k03_unipri = '$this->k03_unipri' ";
            $virgula = ",";
            if (trim($this->k03_unipri) == null) {
                $this->erro_sql = " Campo Única/Primeira não informado.";
                $this->erro_campo = "k03_unipri";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_codbco) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_codbco"])) {
            $sql .= $virgula . " k03_codbco = $this->k03_codbco ";
            $virgula = ",";
            if (trim($this->k03_codbco) == null) {
                $this->erro_sql = " Campo Banco não informado.";
                $this->erro_campo = "k03_codbco";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_codage) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_codage"])) {
            $sql .= $virgula . " k03_codage = '$this->k03_codage' ";
            $virgula = ",";
            if (trim($this->k03_codage) == null) {
                $this->erro_sql = " Campo Agência não informado.";
                $this->erro_campo = "k03_codage";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_recmul) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_recmul"])) {
            $sql .= $virgula . " k03_recmul = $this->k03_recmul ";
            $virgula = ",";
            if (trim($this->k03_recmul) == null) {
                $this->erro_sql = " Campo Receita Multa não informado.";
                $this->erro_campo = "k03_recmul";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_calrec) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_calrec"])) {
            $sql .= $virgula . " k03_calrec = '$this->k03_calrec' ";
            $virgula = ",";
            if (trim($this->k03_calrec) == null) {
                $this->erro_sql = " Campo Receita Cálculo não informado.";
                $this->erro_campo = "k03_calrec";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_msg) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_msg"])) {
            $sql .= $virgula . " k03_msg = '$this->k03_msg' ";
            $virgula = ",";
        }
        if (trim($this->k03_msgcarne) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_msgcarne"])) {
            $sql .= $virgula . " k03_msgcarne = '$this->k03_msgcarne' ";
            $virgula = ",";
        }
        if (trim($this->k03_msgbanco) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_msgbanco"])) {
            $sql .= $virgula . " k03_msgbanco = '$this->k03_msgbanco' ";
            $virgula = ",";
        }
        if (trim($this->k03_certissvar) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_certissvar"])) {
            $sql .= $virgula . " k03_certissvar = '$this->k03_certissvar' ";
            $virgula = ",";
            if (trim($this->k03_certissvar) == null) {
                $this->erro_sql = " Campo Libera Variável não informado.";
                $this->erro_campo = "k03_certissvar";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_diasjust) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_diasjust"])) {
            $sql .= $virgula . " k03_diasjust = $this->k03_diasjust ";
            $virgula = ",";
            if (trim($this->k03_diasjust) == null) {
                $this->erro_sql = " Campo Dias Justif. não informado.";
                $this->erro_campo = "k03_diasjust";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_reccert) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_reccert"])) {
            $sql .= $virgula . " k03_reccert = '$this->k03_reccert' ";
            $virgula = ",";
            if (trim($this->k03_reccert) == null) {
                $this->erro_sql = " Campo Recibo na certidão não informado.";
                $this->erro_campo = "k03_reccert";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_taxagrupo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_taxagrupo"])) {
            $sql .= $virgula . " k03_taxagrupo = $this->k03_taxagrupo ";
            $virgula = ",";
            if (trim($this->k03_taxagrupo) == null) {
                $this->erro_sql = " Campo Código do grupo de taxas não informado.";
                $this->erro_campo = "k03_taxagrupo";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_tipocodcert) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_tipocodcert"])) {
            $sql .= $virgula . " k03_tipocodcert = $this->k03_tipocodcert ";
            $virgula = ",";
            if (trim($this->k03_tipocodcert) == null) {
                $this->erro_sql = " Campo Tipo de Codificação não informado.";
                $this->erro_campo = "k03_tipocodcert";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_reciboprot) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_reciboprot"])) {
            $sql .= $virgula . " k03_reciboprot = $this->k03_reciboprot ";
            $virgula = ",";
            if (trim($this->k03_reciboprot) == null) {
                $this->erro_sql = " Campo Tipo do Recibo do Protocolo não informado.";
                $this->erro_campo = "k03_reciboprot";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_regracnd) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_regracnd"])) {
            $sql .= $virgula . " k03_regracnd = $this->k03_regracnd ";
            $virgula = ",";
            if (trim($this->k03_regracnd) == null) {
                $this->erro_sql = " Campo Regra paraEmissão CND não informado.";
                $this->erro_campo = "k03_regracnd";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_reciboprotretencao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_reciboprotretencao"])) {
            $sql .= $virgula . " k03_reciboprotretencao = $this->k03_reciboprotretencao ";
            $virgula = ",";
            if (trim($this->k03_reciboprotretencao) == null) {
                $this->erro_sql = " Campo Tipo Recibo Retenção não informado.";
                $this->erro_campo = "k03_reciboprotretencao";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_tipocertidao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_tipocertidao"])) {
            $sql .= $virgula . " k03_tipocertidao = $this->k03_tipocertidao ";
            $virgula = ",";
            if (trim($this->k03_tipocertidao) == null) {
                $this->erro_sql = " Campo Forma Emissão Certidão de Débitos não informado.";
                $this->erro_campo = "k03_tipocertidao";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_separajurmulparc) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_separajurmulparc"])) {
            $sql .= $virgula . " k03_separajurmulparc = '$this->k03_separajurmulparc' ";
            $virgula = ",";
            if (trim($this->k03_separajurmulparc) == null) {
                $this->erro_sql = " Campo Separar jur e mul no parcelamento não informado.";
                $this->erro_campo = "k03_separajurmulparc";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_respcgm) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_respcgm"])) {
            if (trim($this->k03_respcgm) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k03_respcgm"])) {
                $this->k03_respcgm = "null";
            }
            $sql .= $virgula . " k03_respcgm = $this->k03_respcgm ";
            $virgula = ",";
        }
        if (trim($this->k03_respcargo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_respcargo"])) {
            if (trim($this->k03_respcargo) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k03_respcargo"])) {
                $this->k03_respcargo = "null";
            }
            $sql .= $virgula . " k03_respcargo = $this->k03_respcargo ";
            $virgula = ",";
        }
        if (trim($this->k03_msgautent) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_msgautent"])) {
            $sql .= $virgula . " k03_msgautent = '$this->k03_msgautent' ";
            $virgula = ",";
        }
        if (trim($this->k03_toleranciapgtoparc) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_toleranciapgtoparc"])) {
            $sql .= $virgula . " k03_toleranciapgtoparc = $this->k03_toleranciapgtoparc ";
            $virgula = ",";
            if (trim($this->k03_toleranciapgtoparc) == null) {
                $this->erro_sql = " Campo Valor Tolerância Diferença Pagamento não informado.";
                $this->erro_campo = "k03_toleranciapgtoparc";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_pgtoparcial) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_pgtoparcial"])) {
            $sql .= $virgula . " k03_pgtoparcial = '$this->k03_pgtoparcial' ";
            $virgula = ",";
            if (trim($this->k03_pgtoparcial) == null) {
                $this->erro_sql = " Campo Ativa Pagamento Parcial não informado.";
                $this->erro_campo = "k03_pgtoparcial";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_reemissaorecibo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_reemissaorecibo"])) {
            $sql .= $virgula . " k03_reemissaorecibo = '$this->k03_reemissaorecibo' ";
            $virgula = ",";
            if (trim($this->k03_reemissaorecibo) == null) {
                $this->erro_sql = " Campo Permite Reemissão de Recibo não informado.";
                $this->erro_campo = "k03_reemissaorecibo";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_opcaoemissparcela) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_opcaoemissparcela"])) {
            $sql .= $virgula . " k03_opcaoemissparcela = '$this->k03_opcaoemissparcela' ";
            $virgula = ",";
        }
        if (trim($this->k03_numprepgtoparcial) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_numprepgtoparcial"])) {
            $sql .= $virgula . " k03_numprepgtoparcial = $this->k03_numprepgtoparcial ";
            $virgula = ",";
            if (trim($this->k03_numprepgtoparcial) == null) {
                $this->erro_sql = " Campo Numpre inicio pagamento parcial não informado.";
                $this->erro_campo = "k03_numprepgtoparcial";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_agrupadorarquivotxtbaixabanco) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_agrupadorarquivotxtbaixabanco"])) {
            $sql .= $virgula . " k03_agrupadorarquivotxtbaixabanco = $this->k03_agrupadorarquivotxtbaixabanco ";
            $virgula = ",";
            if (trim($this->k03_agrupadorarquivotxtbaixabanco) == null) {
                $this->erro_sql = " Campo Forma de Processamento de Arquivo TXT não informado.";
                $this->erro_campo = "k03_agrupadorarquivotxtbaixabanco";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_receitapadraocredito) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_receitapadraocredito"])) {
            if (trim($this->k03_receitapadraocredito) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k03_receitapadraocredito"])) {
                $this->k03_receitapadraocredito = "null";
            }
            $sql .= $virgula . " k03_receitapadraocredito = $this->k03_receitapadraocredito ";
            $virgula = ",";
        }

        if (trim($this->k03_diascertidregular_cgm) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_diascertidregular_cgm"])) {
            $sql .= $virgula . " k03_diascertidregular_cgm = $this->k03_diascertidregular_cgm ";
            $virgula = ",";
            if (trim($this->k03_diascertidregular_cgm) == null) {
                $this->erro_sql = " Campo Dias Venc. Certidão Regular por CGM não informado.";
                $this->erro_campo = "k03_diascertidregular_cgm";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            if (!DBNumber::isInteger(trim($this->k03_diascertidregular_cgm))) {
                $this->k03_diascertidregular_cgm = '';
                $this->erro_sql = " Campo Dias para vencimento das Certidões deve ser preenchido somente com números!";
                $this->erro_campo = "k03_diascertidregular_cgm";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_diascertidregular_matric) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_diascertidregular_matric"])) {
            $sql .= $virgula . " k03_diascertidregular_matric = $this->k03_diascertidregular_matric ";
            $virgula = ",";
            if (trim($this->k03_diascertidregular_matric) == null) {
                $this->erro_sql = " Campo Dias Venc. Certidão Regular por Matricula não informado.";
                $this->erro_campo = "k03_diascertidregular_matric";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_diascertidregular_inscr) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_diascertidregular_inscr"])) {
            $sql .= $virgula . " k03_diascertidregular_inscr = $this->k03_diascertidregular_inscr ";
            $virgula = ",";
            if (trim($this->k03_diascertidregular_inscr) == null) {
                $this->erro_sql = " Campo Dias Venc. Certidão Regular por Inscrição não informado.";
                $this->erro_campo = "k03_diascertidregular_inscr";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }

        if (trim($this->k03_diascertidpositiva_cgm) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_diascertidpositiva_cgm"])) {
            $sql .= $virgula . " k03_diascertidpositiva_cgm = $this->k03_diascertidpositiva_cgm ";
            $virgula = ",";
            if (trim($this->k03_diascertidpositiva_cgm) == null) {
                $this->erro_sql = " Campo Dias Venc. Certidão Positiva por CGM não informado.";
                $this->erro_campo = "k03_diascertidpositiva_cgm";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_diascertidpositiva_matric) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_diascertidpositiva_matric"])) {
            $sql .= $virgula . " k03_diascertidpositiva_matric = $this->k03_diascertidpositiva_matric ";
            $virgula = ",";
            if (trim($this->k03_diascertidpositiva_matric) == null) {
                $this->erro_sql = " Campo Dias Venc. Certidão Positiva por Matricula não informado.";
                $this->erro_campo = "k03_diascertidpositiva_matric";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_diascertidpositiva_inscr) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_diascertidpositiva_inscr"])) {
            $sql .= $virgula . " k03_diascertidpositiva_inscr = $this->k03_diascertidpositiva_inscr ";
            $virgula = ",";
            if (trim($this->k03_diascertidpositiva_inscr) == null) {
                $this->erro_sql = " Campo Dias Venc. Certidão Positiva por Inscrição não informado.";
                $this->erro_campo = "k03_diascertidpositiva_inscr";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }

        if (trim($this->k03_diascertidnegativa_cgm) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_diascertidnegativa_cgm"])) {
            $sql .= $virgula . " k03_diascertidnegativa_cgm = $this->k03_diascertidnegativa_cgm ";
            $virgula = ",";
            if (trim($this->k03_diascertidnegativa_cgm) == null) {
                $this->erro_sql = " Campo Dias Venc. Certidão Negativa por CGM não informado.";
                $this->erro_campo = "k03_diascertidnegativa_cgm";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_diascertidnegativa_matric) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_diascertidnegativa_matric"])) {
            $sql .= $virgula . " k03_diascertidnegativa_matric = $this->k03_diascertidnegativa_matric ";
            $virgula = ",";
            if (trim($this->k03_diascertidnegativa_matric) == null) {
                $this->erro_sql = " Campo Dias Venc. Certidão Negativa por Matricula não informado.";
                $this->erro_campo = "k03_diascertidnegativa_matric";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_diascertidnegativa_inscr) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_diascertidnegativa_inscr"])) {
            $sql .= $virgula . " k03_diascertidnegativa_inscr = $this->k03_diascertidnegativa_inscr ";
            $virgula = ",";
            if (trim($this->k03_diascertidnegativa_inscr) == null) {
                $this->erro_sql = " Campo Dias Venc. Certidão Negativa por Inscrição não informado.";
                $this->erro_campo = "k03_diascertidnegativa_inscr";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }

        if (trim($this->k03_diascertidregularweb_cgm) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_diascertidregularweb_cgm"])) {
            $sql .= $virgula . " k03_diascertidregularweb_cgm = $this->k03_diascertidregularweb_cgm ";
            $virgula = ",";
            if (trim($this->k03_diascertidregularweb_cgm) == null) {
                $this->erro_sql = " Campo Dias Venc. Certidão Regular por CGM não informado.";
                $this->erro_campo = "k03_diascertidregularweb_cgm";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_diascertidregularweb_matric) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_diascertidregularweb_matric"])) {
            $sql .= $virgula . " k03_diascertidregularweb_matric = $this->k03_diascertidregularweb_matric ";
            $virgula = ",";
            if (trim($this->k03_diascertidregularweb_matric) == null) {
                $this->erro_sql = " Campo Dias Venc. Certidão Regular por Matricula não informado.";
                $this->erro_campo = "k03_diascertidregularweb_matric";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_diascertidregularweb_inscr) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_diascertidregularweb_inscr"])) {
            $sql .= $virgula . " k03_diascertidregularweb_inscr = $this->k03_diascertidregularweb_inscr ";
            $virgula = ",";
            if (trim($this->k03_diascertidregularweb_inscr) == null) {
                $this->erro_sql = " Campo Dias Venc. Certidão Regular por Inscrição não informado.";
                $this->erro_campo = "k03_diascertidregularweb_inscr";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }

        if (trim($this->k03_diascertidpositivaweb_cgm) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_diascertidpositivaweb_cgm"])) {
            $sql .= $virgula . " k03_diascertidpositivaweb_cgm = $this->k03_diascertidpositivaweb_cgm ";
            $virgula = ",";
            if (trim($this->k03_diascertidpositivaweb_cgm) == null) {
                $this->erro_sql = " Campo Dias Venc. Certidão Positiva por CGM não informado.";
                $this->erro_campo = "k03_diascertidpositivaweb_cgm";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_diascertidpositivaweb_matric) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_diascertidpositivaweb_matric"])) {
            $sql .= $virgula . " k03_diascertidpositivaweb_matric = $this->k03_diascertidpositivaweb_matric ";
            $virgula = ",";
            if (trim($this->k03_diascertidpositivaweb_matric) == null) {
                $this->erro_sql = " Campo Dias Venc. Certidão Positiva por Matricula não informado.";
                $this->erro_campo = "k03_diascertidpositivaweb_matric";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_diascertidpositivaweb_inscr) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_diascertidpositivaweb_inscr"])) {
            $sql .= $virgula . " k03_diascertidpositivaweb_inscr = $this->k03_diascertidpositivaweb_inscr ";
            $virgula = ",";
            if (trim($this->k03_diascertidpositivaweb_inscr) == null) {
                $this->erro_sql = " Campo Dias Venc. Certidão Positiva por Inscrição não informado.";
                $this->erro_campo = "k03_diascertidpositivaweb_inscr";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }

        if (trim($this->k03_diascertidnegativaweb_cgm) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_diascertidnegativaweb_cgm"])) {
            $sql .= $virgula . " k03_diascertidnegativaweb_cgm = $this->k03_diascertidnegativaweb_cgm ";
            $virgula = ",";
            if (trim($this->k03_diascertidnegativaweb_cgm) == null) {
                $this->erro_sql = " Campo Dias Venc. Certidão Negativa por CGM não informado.";
                $this->erro_campo = "k03_diascertidnegativaweb_cgm";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_diascertidnegativaweb_matric) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_diascertidnegativaweb_matric"])) {
            $sql .= $virgula . " k03_diascertidnegativaweb_matric = $this->k03_diascertidnegativaweb_matric ";
            $virgula = ",";
            if (trim($this->k03_diascertidnegativaweb_matric) == null) {
                $this->erro_sql = " Campo Dias Venc. Certidão Negativa por Matricula não informado.";
                $this->erro_campo = "k03_diascertidnegativaweb_matric";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k03_diascertidnegativaweb_inscr) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_diascertidnegativaweb_inscr"])) {
            $sql .= $virgula . " k03_diascertidnegativaweb_inscr = $this->k03_diascertidnegativaweb_inscr ";
            $virgula = ",";
            if (trim($this->k03_diascertidnegativaweb_inscr) == null) {
                $this->erro_sql = " Campo Dias Venc. Certidão Negativa por Inscrição não informado.";
                $this->erro_campo = "k03_diascertidnegativaweb_inscr";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }

        if (trim($this->k03_templatecertidao_cgm) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidao_cgm"])) {
            if (trim($this->k03_templatecertidao_cgm) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidao_cgm"])) {
                $this->k03_templatecertidao_cgm = "null";
            }
            $sql .= $virgula . " k03_templatecertidao_cgm = $this->k03_templatecertidao_cgm ";
            $virgula = ",";

        }
        if (trim($this->k03_templatecertidao_matric) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidao_matric"])) {
            if (trim($this->k03_templatecertidao_matric) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidao_matric"])) {
                $this->k03_templatecertidao_matric = "null";
            }
            $sql .= $virgula . " k03_templatecertidao_matric = $this->k03_templatecertidao_matric ";
            $virgula = ",";

        }
        if (trim($this->k03_templatecertidao_inscr) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidao_inscr"])) {
            if (trim($this->k03_templatecertidao_inscr) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidao_inscr"])) {
                $this->k03_templatecertidao_inscr = "null";
            }
            $sql .= $virgula . " k03_templatecertidao_inscr = $this->k03_templatecertidao_inscr ";
            $virgula = ",";

        }

        if (trim($this->k03_templatecertidaopositiva_cgm) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaopositiva_cgm"])) {
            if (trim($this->k03_templatecertidaopositiva_cgm) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaopositiva_cgm"])) {
                $this->k03_templatecertidaopositiva_cgm = "null";
            }
            $sql .= $virgula . " k03_templatecertidaopositiva_cgm = $this->k03_templatecertidaopositiva_cgm ";
            $virgula = ",";

        }
        if (trim($this->k03_templatecertidaopositiva_matric) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaopositiva_matric"])) {
            if (trim($this->k03_templatecertidaopositiva_matric) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaopositiva_matric"])) {
                $this->k03_templatecertidaopositiva_matric = "null";
            }
            $sql .= $virgula . " k03_templatecertidaopositiva_matric = $this->k03_templatecertidaopositiva_matric ";
            $virgula = ",";

        }
        if (trim($this->k03_templatecertidaopositiva_inscr) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaopositiva_inscr"])) {
            if (trim($this->k03_templatecertidaopositiva_inscr) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaopositiva_inscr"])) {
                $this->k03_templatecertidaopositiva_inscr = "null";
            }
            $sql .= $virgula . " k03_templatecertidaopositiva_inscr = $this->k03_templatecertidaopositiva_inscr ";
            $virgula = ",";

        }

        if (trim($this->k03_templatecertidaonegativa_cgm) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaonegativa_cgm"])) {
            if (trim($this->k03_templatecertidaonegativa_cgm) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaonegativa_cgm"])) {
                $this->k03_templatecertidaonegativa_cgm = "null";
            }
            $sql .= $virgula . " k03_templatecertidaonegativa_cgm = $this->k03_templatecertidaonegativa_cgm ";
            $virgula = ",";

        }
        if (trim($this->k03_templatecertidaonegativa_matric) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaonegativa_matric"])) {
            if (trim($this->k03_templatecertidaonegativa_matric) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaonegativa_matric"])) {
                $this->k03_templatecertidaonegativa_matric = "null";
            }
            $sql .= $virgula . " k03_templatecertidaonegativa_matric = $this->k03_templatecertidaonegativa_matric ";
            $virgula = ",";

        }
        if (trim($this->k03_templatecertidaonegativa_inscr) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaonegativa_inscr"])) {
            if (trim($this->k03_templatecertidaonegativa_inscr) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaonegativa_inscr"])) {
                $this->k03_templatecertidaonegativa_inscr = "null";
            }
            $sql .= $virgula . " k03_templatecertidaonegativa_inscr = $this->k03_templatecertidaonegativa_inscr ";
            $virgula = ",";

        }

        if (trim($this->k03_templatecertidaoweb_cgm) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaoweb_cgm"])) {
            if (trim($this->k03_templatecertidaoweb_cgm) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaoweb_cgm"])) {
                $this->k03_templatecertidaoweb_cgm = "null";
            }
            $sql .= $virgula . " k03_templatecertidaoweb_cgm = $this->k03_templatecertidaoweb_cgm ";
            $virgula = ",";

        }
        if (trim($this->k03_templatecertidaoweb_matric) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaoweb_matric"])) {
            if (trim($this->k03_templatecertidaoweb_matric) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaoweb_matric"])) {
                $this->k03_templatecertidaoweb_matric = "null";
            }
            $sql .= $virgula . " k03_templatecertidaoweb_matric = $this->k03_templatecertidaoweb_matric ";
            $virgula = ",";

        }
        if (trim($this->k03_templatecertidaoweb_inscr) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaoweb_inscr"])) {
            if (trim($this->k03_templatecertidaoweb_inscr) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaoweb_inscr"])) {
                $this->k03_templatecertidaoweb_inscr = "null";
            }
            $sql .= $virgula . " k03_templatecertidaoweb_inscr = $this->k03_templatecertidaoweb_inscr ";
            $virgula = ",";

        }

        if (trim($this->k03_templatecertidaopositivaweb_cgm) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaopositivaweb_cgm"])) {
            if (trim($this->k03_templatecertidaopositivaweb_cgm) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaopositivaweb_cgm"])) {
                $this->k03_templatecertidaopositivaweb_cgm = "null";
            }
            $sql .= $virgula . " k03_templatecertidaopositivaweb_cgm = $this->k03_templatecertidaopositivaweb_cgm ";
            $virgula = ",";

        }
        if (trim($this->k03_templatecertidaopositivaweb_matric) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaopositivaweb_matric"])) {
            if (trim($this->k03_templatecertidaopositivaweb_matric) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaopositivaweb_matric"])) {
                $this->k03_templatecertidaopositivaweb_matric = "null";
            }
            $sql .= $virgula . " k03_templatecertidaopositivaweb_matric = $this->k03_templatecertidaopositivaweb_matric ";
            $virgula = ",";

        }
        if (trim($this->k03_templatecertidaopositivaweb_inscr) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaopositivaweb_inscr"])) {
            if (trim($this->k03_templatecertidaopositivaweb_inscr) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaopositivaweb_inscr"])) {
                $this->k03_templatecertidaopositivaweb_inscr = "null";
            }
            $sql .= $virgula . " k03_templatecertidaopositivaweb_inscr = $this->k03_templatecertidaopositivaweb_inscr ";
            $virgula = ",";

        }

        if (trim($this->k03_templatecertidaonegativaweb_cgm) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaonegativaweb_cgm"])) {
            if (trim($this->k03_templatecertidaonegativaweb_cgm) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaonegativaweb_cgm"])) {
                $this->k03_templatecertidaonegativaweb_cgm = "null";
            }
            $sql .= $virgula . " k03_templatecertidaonegativaweb_cgm = $this->k03_templatecertidaonegativaweb_cgm ";
            $virgula = ",";

        }
        if (trim($this->k03_templatecertidaonegativaweb_matric) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaonegativaweb_matric"])) {
            if (trim($this->k03_templatecertidaonegativaweb_matric) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaonegativaweb_matric"])) {
                $this->k03_templatecertidaonegativaweb_matric = "null";
            }
            $sql .= $virgula . " k03_templatecertidaonegativaweb_matric = $this->k03_templatecertidaonegativaweb_matric ";
            $virgula = ",";

        }
        if (trim($this->k03_templatecertidaonegativaweb_inscr) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaonegativaweb_inscr"])) {
            if (trim($this->k03_templatecertidaonegativaweb_inscr) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k03_templatecertidaonegativaweb_inscr"])) {
                $this->k03_templatecertidaonegativaweb_inscr = "null";
            }
            $sql .= $virgula . " k03_templatecertidaonegativaweb_inscr = $this->k03_templatecertidaonegativaweb_inscr ";
            $virgula = ",";

        }

        if (trim($this->k03_filtrardepart) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_filtrardepart"])) {
            $sql .= $virgula . " k03_filtrardepart = '$this->k03_filtrardepart' ";
            $virgula = ",";
        }

        if (trim($this->k03_apenastiporenuncia) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_apenastiporenuncia"])) {
            $sql .= $virgula . " k03_apenastiporenuncia = '$this->k03_apenastiporenuncia' ";
            $virgula = ",";
        }

        if (trim($this->k03_filtrarreceita) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_filtrarreceita"])) {
            $sql .= $virgula . " k03_filtrarreceita = '$this->k03_filtrarreceita' ";
            $virgula = ",";
        }

        if (trim($this->k03_diasreemissaocertidao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_diasreemissaocertidao"])) {
            $sql .= $virgula . " k03_diasreemissaocertidao = $this->k03_diasreemissaocertidao ";
            $virgula = ",";
            if (trim($this->k03_diasreemissaocertidao) == null) {
                $this->erro_sql = " Campo Dias para reemissão das Certidões não informado.";
                $this->erro_campo = "k03_diasvalidadecertidao";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }

            if (!DBNumber::isInteger(trim($this->k03_diasreemissaocertidao))) {
                $this->k03_diasreemissaocertidao = '';
                $this->erro_sql = " Campo Dias para reemissão das Certidões deve ser preenchido somente com números!";
                $this->erro_campo = "k03_diasvalidadecertidao";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }

        if (trim($this->k03_toleranciacredito) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k03_toleranciacredito"])) {

            if($GLOBALS["HTTP_POST_VARS"]["k03_toleranciacredito"] > 0){
                $sql .= $virgula . " k03_toleranciacredito = ".$GLOBALS["HTTP_POST_VARS"]["k03_toleranciacredito"];
            } else {
                $sql .= $virgula . " k03_toleranciacredito = $this->k03_toleranciacredito ";
            }
            $virgula = ",";
            if (trim($this->k03_toleranciacredito) == null) {
                $this->erro_sql = " Campo Tolerãncia para Crédito não informado.";
                $this->erro_campo = "k03_toleranciacredito";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }

     if(trim($this->k03_imprimecancdebitos)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k03_imprimecancdebitos"])){
       $sql  .= $virgula." k03_imprimecancdebitos = '$this->k03_imprimecancdebitos' ";
       $virgula = ",";
       if(trim($this->k03_imprimecancdebitos) == null ){
         $this->erro_sql = " Campo Permite Impressão Cancelamento Débitos não informado.";
         $this->erro_campo = "k03_imprimecancdebitos";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }

        if(trim($this->k03_usagrupotaxa)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k03_usagrupotaxa"])){
            $sql  .= $virgula." k03_usagrupotaxa = '$this->k03_usagrupotaxa' ";
            $virgula = ",";
            if(trim($this->k03_usagrupotaxa) == null ){
                $this->erro_sql = " Campo Usa Grupo de Taxa não informado.";
                $this->erro_campo = "k03_usagrupotaxa";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }

        if(trim($this->k03_processoparcelamento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k03_processoparcelamento"])){
            $sql  .= $virgula." k03_processoparcelamento = '$this->k03_processoparcelamento' ";
            $virgula = ",";
            if(trim($this->k03_processoparcelamento) == null ){
                $this->erro_sql = " Campo Processo Obrigatório Parcelamento não informado.";
                $this->erro_campo = "k03_processoparcelamento";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }

        if(trim($this->k03_custashonorarios)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k03_custashonorarios"])){
            $sql  .= $virgula." k03_custashonorarios = '$this->k03_custashonorarios' ";
            $virgula = ",";
            if(trim($this->k03_custashonorarios) == null ){
                $this->erro_sql = " Campo habilita custas e honorários não informado.";
                $this->erro_campo = "k03_custashonorarios";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }

        if(trim($this->k03_encargoscarneunica)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k03_encargoscarneunica"])){
            $sql  .= $virgula." k03_encargoscarneunica = '$this->k03_encargoscarneunica' ";
            $virgula = ",";
            if(trim($this->k03_encargoscarneunica) == null ){
                $this->erro_sql = " Campo Emissão de carnê CGF não informado.";
                $this->erro_campo = "k03_encargoscarneunica";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }

        if(trim($this->k03_tipoinscricao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k03_tipoinscricao"])){
            $sql  .= $virgula." k03_tipoinscricao = $this->k03_tipoinscricao ";
            $virgula = ",";
        }

        $sql .= " where ";
        if ($k03_anousu != null) {
            $sql .= " k03_anousu = $this->k03_anousu";
        }
        if ($k03_instit != null) {
            $sql .= " and  k03_instit = $this->k03_instit";
        }
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Numerações nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->k03_anousu . "-" . $this->k03_instit;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Numerações nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->k03_anousu . "-" . $this->k03_instit;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : " . $this->k03_anousu . "-" . $this->k03_instit;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    // funcao para exclusao
    public function excluir($k03_anousu = null, $k03_instit = null, $dbwhere = null)
    {
        $sql = " delete from numpref
                    where ";
        $sql2 = "";
        if ($dbwhere == null || $dbwhere == "") {
            if ($k03_anousu != "") {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                }
                $sql2 .= " k03_anousu = $k03_anousu ";
            }
            if ($k03_instit != "") {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                }
                $sql2 .= " k03_instit = $k03_instit ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Numerações nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $k03_anousu . "-" . $k03_instit;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Numerações nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $k03_anousu . "-" . $k03_instit;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : " . $k03_anousu . "-" . $k03_instit;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
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
        if ($result == false) {
            $this->numrows = 0;
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Erro ao selecionar os registros.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->numrows = pg_numrows($result);
        if ($this->numrows == 0) {
            $this->erro_banco = "";
            $this->erro_sql = "Record Vazio na Tabela:numpref";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    // funcao do sql
    public function sql_query($k03_anousu = null, $k03_instit = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from numpref ";
        $sql .= "      left  join cgm as cgm2     on cgm2.z01_numcgm            = numpref.k03_respcgm";
        $sql .= "      inner join arretipo  on  arretipo.k00_tipo = numpref.k03_reciboprotretencao";
        $sql .= "      inner join db_config  on  db_config.codigo = numpref.k03_instit";
        $sql .= "      left  join rhfuncao  on  rhfuncao.rh37_funcao = numpref.k03_respcargo";
        $sql .= "                                and rhfuncao.rh37_instit       = numpref.k03_instit";
        $sql .= "      inner join taxagrupo  on  taxagrupo.k06_taxagrupo = numpref.k03_taxagrupo";
        $sql .= "      inner join db_config  as a on   a.codigo = arretipo.k00_instit";
        $sql .= "      inner join cadtipo  on  cadtipo.k03_tipo = arretipo.k03_tipo";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
        $sql .= "      inner join db_tipoinstit  on  db_tipoinstit.db21_codtipo = db_config.db21_tipoinstit";
        $sql .= "      left  join db_config  as b on b.codigo                   = rhfuncao.rh37_instit";
        $sql .= "      left  join tabrec          on tabrec.k02_codigo          = numpref.k03_receitapadraocredito";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($k03_anousu != null) {
                $sql2 .= " where numpref.k03_anousu = $k03_anousu ";
            }
            if ($k03_instit != null) {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " numpref.k03_instit = $k03_instit ";
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

    // funcao do sql
    public function sql_query_file($k03_anousu = null, $k03_instit = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from numpref ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($k03_anousu != null) {
                $sql2 .= " where numpref.k03_anousu = $k03_anousu ";
            }
            if ($k03_instit != null) {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " numpref.k03_instit = $k03_instit ";
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

    /**
     * @return integer
     */
    public function sql_numpre()
    {
        $result = @db_query("select nextval('numpref_k03_numpre_seq')");
        return pg_result($result, 0, 0);
    }

    /**
     * @return int
     */
    public static function getNumpre()
    {
        $dao = new cl_numpref();
        return $dao->sql_numpre();
    }
}

