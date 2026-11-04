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

require_once(modification('std/DBLargeObject.php'));

class ArquivoCobrancaRegistrada {

    var $iIdAtosDistribuidores             = "";
    var $iIdTaxaJudiciaria                 = "";
    var $iIdAtosEscrivaesDividaAtiva       = "";
    var $iIdAcrescimo                      = "";
    var $iIdAtosOficiaisJusticaAvaliadores = "";
    function __construct() {

    }

    /**
     * Metodo utilizado para reemitir arquivos de remessa ao Banco referentes a cobranca registrada
     *
     * @param int    $iCodigoArquivo  - Define o codigo do arquivo para reemissão
     *
     */
    public function reemiteArquivoRemessaBanco($iCodigoArquivo, $sNomeAuxiliar = null) {

        $oDaoPartilhaArquivo  = db_utils::getDao('partilhaarquivo');
        $sSql                 = $oDaoPartilhaArquivo->sql_query_file($iCodigoArquivo);
        $rsPartilhaArquivo    = $oDaoPartilhaArquivo->sql_record($sSql);
        $oPartilhaArquivo     = db_utils::fieldsMemory($rsPartilhaArquivo, 0);

        $sArquivo = "tmp/".$sNomeAuxiliar.$oPartilhaArquivo->v78_nomearq;

        $lReemitiuArquivo = DBLargeObject::leitura($oPartilhaArquivo->v78_arquivo, $sArquivo);

        if ($lReemitiuArquivo) {
            return $sArquivo;
        } else {
            throw new Exception("[ 0 ] - Erro ao reemitir o arquivo!");
        }

    }

    /**
     * Metodo utilizado para geracao dos arquivos de remessa ao Banco referentes a cobranca registrada
     *
     * @param date    $dtProcessamento  - Define a data dos registros de recibos que constarao no arquivo
     * @param string  $sNomeArq         - Define o nome do arquivo que será gerado
     *
     */
    public function geraArquivoRemessaBanco($dtProcessamento, $sNomeArq) {

        if ( !db_utils::inTransaction() ) {
            throw new Exception("[ 0 ] - Nenhuma transação encontrada!");
        }

        $oDaoConvenioCobranca     = db_utils::getDao('conveniocobranca');
        $oDaoPartilhaArquivo      = db_utils::getDao('partilhaarquivo');
        $oDaoPartilhaArquivoReg   = db_utils::getDao('partilhaarquivoreg');
        $oDaoDbConfig             = db_utils::getDao('db_config');
        $oLayoutTxt               = new db_layouttxt(100, "tmp/".$sNomeArq);

        $aPartilhaCustas          = array();
        $iRegistro = 1;


        /*
       * Buscamos as informações referentes ao convênio com o banco
       */
    $iConvenioCustaBoleto = regraEmissao::getConveioCustaBoleto();
    $rsConvenioCobranca = $oDaoConvenioCobranca->sql_record($oDaoConvenioCobranca->sql_query("","*",null,"ar13_carteira = '17' and ar11_cadtipoconvenio = $iConvenioCustaBoleto"));
        if ($oDaoConvenioCobranca->numrows == 0) {
            throw new Exception("[ 1 ] - Nenhum cadastro de convênio do tipo Cobrança Registrada com carteira 17 encontrado!");
        }
        $oConvenioCobranca = db_utils::fieldsMemory($rsConvenioCobranca,0);

        //Buscamos os dados da instituição
        $rsDbConfig = $oDaoDbConfig->sql_record($oDaoDbConfig->sql_query(db_getsession("DB_instit"), "*"));
        if ($oDaoDbConfig->numrows == 0) {
            throw new Exception("[ 2 ] - Erro ao buscar dados da instituição!\nErro: {$oDaoDbConfig->erro_msg}");
        }
        $oDbConfig = db_utils::fieldsMemory($rsDbConfig,0);

        /*
         * Gera o arquivo oid
         *
         */
        $iOid         = DBLargeObject::criaOID(true);

        /*
         * Cadastramos o registro de geração do arquivo
         */
        $oDaoPartilhaArquivo->v78_dtgeracao = $dtProcessamento;
        $oDaoPartilhaArquivo->v78_nomearq   = $sNomeArq;
        $oDaoPartilhaArquivo->v78_tipoarq   = 1;
        $oDaoPartilhaArquivo->v78_arquivo   = $iOid;
        $oDaoPartilhaArquivo->incluir(null);
        if ($oDaoPartilhaArquivo->erro_status == "0") {
            throw new Exception("[ 3 ] - Erro ao inserir dados em Partilha Arquivo\n {$oDaoPartilhaArquivo->erro_msg}");
        }

        $aDtProcessamento = explode("-",$dtProcessamento);
        $sDtProcessamento = $aDtProcessamento[2].$aDtProcessamento[1].substr($aDtProcessamento[0],2,2);

        /*
         * Montanos o Header do Arquivo com as informações do convênio e sequencial de geração do arquivo
         */
        $oHeader = new stdClass();
        $oHeader->id                                  = "0";
        $oHeader->fixo1                               = "1";
        $oHeader->fixo2                               = str_pad("CBR653",                             7, " ", STR_PAD_RIGHT);
        $oHeader->fixo3                               = "01";
        $oHeader->fixo4                               = "COBRANCA";
        $oHeader->fixo5                               = str_repeat(" ",7); //brancos
        $oHeader->prefixo_agencia                     = str_pad(substr($oConvenioCobranca->db89_codagencia,-4),  4, "0", STR_PAD_LEFT);
        $oHeader->dv_prefixo_agencia                  = str_pad($oConvenioCobranca->db89_digito     ,  1, "0", STR_PAD_LEFT);
        $oHeader->codigo_cedente                      = str_pad($oConvenioCobranca->ar13_cedente    ,  8, "0", STR_PAD_LEFT);
        $oHeader->dv_codigo_cedente                   = str_pad($oConvenioCobranca->ar13_digcedente ,  1, "0", STR_PAD_LEFT);;
        $oHeader->fixo6                               = "000000";
        $oHeader->nome_cliente                        = str_pad($oDbConfig->nomeinst                , 30, " ", STR_PAD_RIGHT);
        $oHeader->banco                               = "001BANCO DO BRASIL"; //fixo
        $oHeader->data_gravacao                       = $sDtProcessamento;
        $oHeader->sequencial_remessa                  = str_pad($oDaoPartilhaArquivo->v78_sequencial, 7, "0", STR_PAD_LEFT);
        $oHeader->fixo7                               = str_repeat(" ",22); //brancos
        $oHeader->numero_convenio_banco               = str_pad($oConvenioCobranca->ar13_convenio,    7, "0", STR_PAD_LEFT);
        $oHeader->fixo8                               = str_repeat(" ",258); //brancos
        $oHeader->sequencial_registro                 = "000001";
        if ( $oLayoutTxt->setByLineOfDBUtils($oHeader,1,"0") == false ) {
            throw new Exception ("[ 4 ] - Erro ao gerar Header do Arquivo");
        }

        /*
         * Montamos os dados do detalhe do arquivo
         *
         * Primeiramente buscamos as informações sque irão compor os campos dessas linhas
         * Após percorremos os registros encontrados e montamos a stdClass $oDadosDetalhe para geração das linhas do Detalhe
         *
         */
        $oRegistros = $this->getDadosReciboPartilha(1,$dtProcessamento);
        if (count($oRegistros) == 0) {
            throw new Exception("[ 3 ] - Nenhum registro de emissão com cobranca encontrado para a data informada!");
        }

        foreach ( $oRegistros as $aDados ) {

            $iRegistro++;

            $sNumeroControleParticipante = $aDados->z01_numcgm.$aDados->numbanco.str_replace("-","",$aDados->k00_dtpaga);

            $aDataEmissao    = explode("-",$aDados->k00_dtoper);
            $sDataEmissao    = $aDataEmissao[2].$aDataEmissao[1].substr($aDataEmissao[0],2,2);

            $buscaDataEmissao = db_query("select to_char(k138_data, 'ddmmYY') as k138_data from recibopagaboleto where k138_numnov = {$aDados->k00_numnov} limit 1;");
            $sDataEmissao = db_utils::fieldsMemory($buscaDataEmissao, 0)->k138_data;

            $aDataVencimento = explode("-",$aDados->k00_dtpaga);
            $sDataVencimento = $aDataVencimento[2].$aDataVencimento[1].substr($aDataVencimento[0],2,2);

            $nValor    = number_format(round($aDados->valor_total_recibo,2), 2, '', '');

            $oDetalhe = new stdClass();
            $oDetalhe->id                                   = "7";
            $oDetalhe->tipo_inscricao_empresa               = "02";
            $oDetalhe->inscricao_empresa                    = str_pad($oDbConfig->z01_cgccpf                    , 14, "0", STR_PAD_LEFT);
            $oDetalhe->prefixo_agencia                      = str_pad(substr($oConvenioCobranca->db89_codagencia,-4),  4, "0", STR_PAD_LEFT);
            $oDetalhe->dv_prefixo_agencia                   = str_pad($oConvenioCobranca->db89_digito           ,  1, "0", STR_PAD_LEFT);
            $oDetalhe->codigo_cedente                       = str_pad($oConvenioCobranca->ar13_cedente          ,  8, "0", STR_PAD_LEFT);
            $oDetalhe->dv_codigo_cedente                    = str_pad($oConvenioCobranca->ar13_digcedente       ,  1, "0", STR_PAD_LEFT);
            $oDetalhe->numero_convenio                      = str_pad($oConvenioCobranca->ar13_convenio         ,  7, "0", STR_PAD_LEFT);
            $oDetalhe->numero_controle_participante         = str_pad($sNumeroControleParticipante              , 25, "0", STR_PAD_LEFT);
            $oDetalhe->nosso_numero                         = str_pad($aDados->numbanco                         , 17, "0", STR_PAD_RIGHT);
            $oDetalhe->fixo1                                = str_repeat("0",2);  //00
            $oDetalhe->fixo2                                = str_repeat("0",2);  //00
            $oDetalhe->fixo3                                = str_repeat(" ",3);  //brancos
            $oDetalhe->indicativo_sacador                   = str_repeat(" ",1);
            $oDetalhe->prefixo_titulo                       = str_repeat(" ",3);  //brancos
            $oDetalhe->variacao_carteira                    = "019";
            $oDetalhe->fixo4                                = str_repeat("0",1);  //0
            $oDetalhe->fixo5                                = str_repeat("0",6);  //000000
            $oDetalhe->fixo6                                = str_repeat(" ",5);  //brancos
            $oDetalhe->carteira                             = $oConvenioCobranca->ar13_carteira;
            $oDetalhe->comando                              = "01";
            $oDetalhe->seu_numero                           = str_pad($aDados->k00_numnov."000"                 , 10, "0", STR_PAD_LEFT);
            $oDetalhe->data_vencimento                      = $sDataVencimento;
            $oDetalhe->valor_titulo                         = str_pad($nValor, 13, "0", STR_PAD_LEFT);
            $oDetalhe->numero_banco                         = "001";
            $oDetalhe->prefixo_agencia_cobradora            = str_repeat("0",4);  //0000
            $oDetalhe->dv_prefixo_agencia_cobradora         = str_repeat(" ",1);
            $oDetalhe->especie_titulo                       = "27";
            $oDetalhe->aceite                               = "N";
            $oDetalhe->data_emissao                         = $sDataEmissao;
            $oDetalhe->instrucao_codificada1                = str_repeat("0",2);  //00
            $oDetalhe->instrucao_codificada2                = str_repeat("0",2);  //00
            $oDetalhe->juros_mora_dia                       = str_repeat("0",13); //00000000000
            $oDetalhe->data_limite_concessao_desconto       = str_repeat("0",6);  //000000
            $oDetalhe->valor_desconto                       = str_repeat("0",13); //00000000000
            $oDetalhe->valor_IOF                            = str_repeat("0",13); //00000000000
            $oDetalhe->valor_abatimento                     = str_repeat("0",13); //00000000000

            $sTipoInscricaoSacado = "01";

            if (strlen($aDados->z01_cgccpf) == 14)  {
                $sTipoInscricaoSacado = "02";
            }

            $aDados->z01_cgccpf = trim($aDados->z01_cgccpf);

            if (empty($aDados->z01_cgccpf) || $aDados->z01_cgccpf == '00000000000' || $aDados->z01_cgccpf == '00000000000000') {

                $aDados->z01_cgccpf = "39756648000128";
                $sTipoInscricaoSacado = "02";
            }

            $oDetalhe->tipo_inscricao_sacado                = $sTipoInscricaoSacado; //str_repeat("0",2);  //00

            $oDetalhe->documento_sacado                     = str_pad($aDados->z01_cgccpf,14,"0",STR_PAD_LEFT); //00000000000000
            $oDetalhe->nome_sacado                          = str_pad($aDados->z01_nome, 37, " ", STR_PAD_RIGHT);
            $oDetalhe->fixo7                                = str_repeat(" ",3);  //brancos
            $oDetalhe->fixo8                                = str_repeat(" ",15); //brancos

            //Caso o endereço do sacado seja nulo, utilizamos o enderço da prefeitura
            if (empty($aDados->z01_bairro) || empty($aDados->z01_munic) || empty($aDados->z01_ender)) {

                $oDetalhe->endereco_sacado                      = $oDbConfig->ender;
                $oDetalhe->cep_endereco_sacado                  = $oDbConfig->cep;
                $oDetalhe->cidade_endereco_sacado               = $oDbConfig->munic;
                $oDetalhe->uf_cidade_sacado                     = $oDbConfig->uf;

            } else {

                $oDetalhe->endereco_sacado                      = $aDados->z01_ender;
                $oDetalhe->cep_endereco_sacado                  = $aDados->z01_cep;
                $oDetalhe->cidade_endereco_sacado               = $aDados->z01_munic;
                $oDetalhe->uf_cidade_sacado                     = $aDados->z01_uf;

            }

            $oDetalhe->observacoes                          = str_repeat(" ",40); //brancos
            $oDetalhe->numero_dias_protesto                 = str_repeat("0",2);  //00
            $oDetalhe->fixo9                                = str_repeat(" ",1);
            $oDetalhe->sequencial_registro                  = str_pad($iRegistro, 6, "0", STR_PAD_LEFT);
            if ( $oLayoutTxt->setByLineOfDBUtils($oDetalhe,3,7) == false ) {
                throw new Exception ("[ 5 ] - Erro ao gerar Detalhe do Arquivo");
            }

            /**
             * Dados ref aos campos da linha Detalhe Auxiliar / Remessa / Registro 2
             * Com os dados dos Favorecidos
             */
            // dd($aDados);
            // dd($aDados);
            $partilhasEncontradas = array();
            if ( !empty($aDados->banco_TJ) ) {

                $partilhaOrigem = new stdClass();
                $partilhaOrigem->banco_credito               = $aDados->banco_TJ;
                $partilhaOrigem->prefixo_agencia_credito     = $aDados->codagencia_TJ;
                $partilhaOrigem->dv_prefixo_agencia_credito  = $aDados->dvagencia_TJ;
                $partilhaOrigem->conta_credito               = $aDados->conta_TJ;
                $partilhaOrigem->dv_conta_credito            = $aDados->dvconta_TJ;
                $partilhaOrigem->nome_favorecido             = $aDados->nome_TJ;
                $partilhaOrigem->valor_partilha              = $aDados->valor_TJ;
                $partilhaOrigem->numero_documento_favorecido = $aDados->cgccpf_TJ;
                $partilhasEncontradas[] = $partilhaOrigem;
            }

            if ( !empty($aDados->banco_FUNPERJ) ) {

                $partilhaOrigem = new stdClass();
                $partilhaOrigem->banco_credito               = $aDados->banco_FUNPERJ;
                $partilhaOrigem->prefixo_agencia_credito     = $aDados->codagencia_FUNPERJ;
                $partilhaOrigem->dv_prefixo_agencia_credito  = $aDados->dvagencia_FUNPERJ;
                $partilhaOrigem->conta_credito               = $aDados->conta_FUNPERJ;
                $partilhaOrigem->dv_conta_credito            = $aDados->dvconta_FUNPERJ;
                $partilhaOrigem->nome_favorecido             = $aDados->nome_FUNPERJ;
                $partilhaOrigem->valor_partilha              = $aDados->valor_FUNPERJ;
                $partilhaOrigem->numero_documento_favorecido = $aDados->cgccpf_FUNPERJ;
                $partilhasEncontradas[] = $partilhaOrigem;
            }

            if ( !empty($aDados->banco_CAARJ) ) {

                $partilhaOrigem = new stdClass();
                $partilhaOrigem->banco_credito               = $aDados->banco_CAARJ;
                $partilhaOrigem->prefixo_agencia_credito     = $aDados->codagencia_CAARJ;
                $partilhaOrigem->dv_prefixo_agencia_credito  = $aDados->dvagencia_CAARJ;
                $partilhaOrigem->conta_credito               = $aDados->conta_CAARJ;
                $partilhaOrigem->dv_conta_credito            = $aDados->dvconta_CAARJ;
                $partilhaOrigem->nome_favorecido             = $aDados->nome_CAARJ;
                $partilhaOrigem->valor_partilha              = $aDados->valor_CAARJ;
                $partilhaOrigem->numero_documento_favorecido = $aDados->cgccpf_CAARJ;
                $partilhasEncontradas[] = $partilhaOrigem;
            }

            if ( !empty($aDados->banco_FUNDPERJ) ) {

                $partilhaOrigem = new stdClass();
                $partilhaOrigem->banco_credito               = $aDados->banco_FUNDPERJ;
                $partilhaOrigem->prefixo_agencia_credito     = $aDados->codagencia_FUNDPERJ;
                $partilhaOrigem->dv_prefixo_agencia_credito  = $aDados->dvagencia_FUNDPERJ;
                $partilhaOrigem->conta_credito               = $aDados->conta_FUNDPERJ;
                $partilhaOrigem->dv_conta_credito            = $aDados->dvconta_FUNDPERJ;
                $partilhaOrigem->nome_favorecido             = $aDados->nome_FUNDPERJ;
                $partilhaOrigem->valor_partilha              = $aDados->valor_FUNDPERJ;
                $partilhaOrigem->numero_documento_favorecido = $aDados->cgccpf_FUNDPERJ;
                $partilhasEncontradas[] = $partilhaOrigem;
            }

            if ( !empty($aDados->banco_INSTITUICAO) ) {

                $partilhaOrigem = new stdClass();
                $partilhaOrigem->banco_credito               = $aDados->banco_INSTITUICAO;
                $partilhaOrigem->prefixo_agencia_credito     = $aDados->codagencia_INSTITUICAO;
                $partilhaOrigem->dv_prefixo_agencia_credito  = $aDados->dvagencia_INSTITUICAO;
                $partilhaOrigem->conta_credito               = $aDados->conta_INSTITUICAO;
                $partilhaOrigem->dv_conta_credito            = $aDados->dvconta_INSTITUICAO;
                $partilhaOrigem->nome_favorecido             = $aDados->nome_INSTITUICAO;
                $partilhaOrigem->valor_partilha              = $aDados->valor_INSTITUICAO;
                $partilhaOrigem->numero_documento_favorecido = $oDbConfig->z01_cgccpf;
                $partilhasEncontradas[] = $partilhaOrigem;
            }

            if ( !empty($aDados->banco_HONORARIOS) ) {

                $partilhaOrigem = new stdClass();
                $partilhaOrigem->banco_credito               = $aDados->banco_HONORARIOS;
                $partilhaOrigem->prefixo_agencia_credito     = $aDados->codagencia_HONORARIOS;
                $partilhaOrigem->dv_prefixo_agencia_credito  = $aDados->dvagencia_HONORARIOS;
                $partilhaOrigem->conta_credito               = $aDados->conta_HONORARIOS;
                $partilhaOrigem->dv_conta_credito            = $aDados->dvconta_HONORARIOS;
                $partilhaOrigem->nome_favorecido             = $aDados->nome_HONORARIOS;
                $partilhaOrigem->valor_partilha              = $aDados->valor_HONORARIOS;
                $partilhaOrigem->numero_documento_favorecido = $aDados->cgccpf_HONORARIOS;
                $partilhasEncontradas[] = $partilhaOrigem;
            }

            if ( !empty($aDados->banco_DISTRIBUIDOR) ) {

                $partilhaOrigem = new stdClass();
                $partilhaOrigem->banco_credito               = $aDados->banco_DISTRIBUIDOR;
                $partilhaOrigem->prefixo_agencia_credito     = $aDados->codagencia_DISTRIBUIDOR;
                $partilhaOrigem->dv_prefixo_agencia_credito  = $aDados->dvagencia_DISTRIBUIDOR;
                $partilhaOrigem->conta_credito               = $aDados->conta_DISTRIBUIDOR;
                $partilhaOrigem->dv_conta_credito            = $aDados->dvconta_DISTRIBUIDOR;
                $partilhaOrigem->nome_favorecido             = $aDados->nome_DISTRIBUIDOR;
                $partilhaOrigem->valor_partilha              = $aDados->valor_DISTRIBUIDOR;
                $partilhaOrigem->numero_documento_favorecido = $aDados->cgccpf_DISTRIBUIDOR;
                $partilhasEncontradas[] = $partilhaOrigem;
            }
            if ( !empty($aDados->banco_FUNARPEN) ) {

                $partilhaOrigem = new stdClass();
                $partilhaOrigem->banco_credito               = $aDados->banco_FUNARPEN;
                $partilhaOrigem->prefixo_agencia_credito     = $aDados->codagencia_FUNARPEN;
                $partilhaOrigem->dv_prefixo_agencia_credito  = $aDados->dvagencia_FUNARPEN;
                $partilhaOrigem->conta_credito               = $aDados->conta_FUNARPEN;
                $partilhaOrigem->dv_conta_credito            = $aDados->dvconta_FUNARPEN;
                $partilhaOrigem->nome_favorecido             = $aDados->nome_FUNARPEN;
                $partilhaOrigem->valor_partilha              = $aDados->valor_FUNARPEN;
                $partilhaOrigem->numero_documento_favorecido = $aDados->cgccpf_FUNARPEN;
                $partilhasEncontradas[] = $partilhaOrigem;
            }

            $camposPartilha = array(
                'banco_credito',
                'prefixo_agencia_credito',
                'dv_prefixo_agencia_credito',
                'conta_credito',
                'dv_conta_credito',
                'nome_favorecido',
                'valor_partilha',
                'numero_documento_favorecido'
            );

            $oDetalheAuxiliar = new stdClass();
            $oDetalheAuxiliarPref = new stdClass();
            // dd($partilhasEncontradas);
            $inicio = 1;
            $imprimeSegundaLinha = false;
            foreach ($partilhasEncontradas as $indiceDadoPartilha => $stdDadosPartilha) {

                if ($inicio == 5) {
                    $inicio = 1;
                    $imprimeSegundaLinha = true;
                }

                foreach ($camposPartilha as $indiceCampo => $campo) {

                    $nomePropriedade = "{$campo}{$inicio}";
                    if ($inicio <= 4 && $imprimeSegundaLinha === false) {
                        $oDetalheAuxiliar->{$nomePropriedade} = $stdDadosPartilha->{$campo};
                    }
                    /* imprime a segunda linha */
                    if ( $imprimeSegundaLinha === true ) {
                        $oDetalheAuxiliarPref->{$nomePropriedade} = $stdDadosPartilha->{$campo};
                    }
                }
                $inicio++;
            }

            $oDetalheAuxiliar->id                           = "2";
            $oDetalheAuxiliar->nosso_numero                 = str_pad($aDados->numbanco                         , 17, "0", STR_PAD_RIGHT);

            $oDetalheAuxiliar->banco_credito1               = str_pad($oDetalheAuxiliar->banco_credito1                         ,  3, "0", STR_PAD_LEFT);
            $oDetalheAuxiliar->camara_compensacao1          = str_repeat("0",3);
            $oDetalheAuxiliar->prefixo_agencia_credito1     = str_pad(substr($oDetalheAuxiliar->prefixo_agencia_credito1,-4)         ,  4, "0", STR_PAD_LEFT);
            $oDetalheAuxiliar->dv_prefixo_agencia_credito1  = str_pad($oDetalheAuxiliar->dv_prefixo_agencia_credito1                     ,  1, "0", STR_PAD_LEFT);
            $oDetalheAuxiliar->conta_credito1               = str_pad($oDetalheAuxiliar->conta_credito1                         , 11, "0", STR_PAD_LEFT);
            $oDetalheAuxiliar->dv_conta_credito1            = str_pad($oDetalheAuxiliar->dv_conta_credito1                       ,  1, "0", STR_PAD_LEFT);
            $oDetalheAuxiliar->nome_favorecido1             = str_pad($oDetalheAuxiliar->nome_favorecido1                          , 30, " ", STR_PAD_RIGHT);
            $oDetalheAuxiliar->valor_partilha1              = str_pad(number_format(round($oDetalheAuxiliar->valor_partilha1,2), 2, '', ''), 13, "0", STR_PAD_LEFT);
            $oDetalheAuxiliar->brancos1                     = str_repeat(" ",13);

            $oDetalheAuxiliar->banco_credito2               = str_pad($oDetalheAuxiliar->banco_credito2                     ,  3, "0", STR_PAD_LEFT);
            $oDetalheAuxiliar->camara_compensacao2          = str_repeat("0",3);
            $oDetalheAuxiliar->prefixo_agencia_credito2     = str_pad(substr($oDetalheAuxiliar->prefixo_agencia_credito2,-4)     ,  4, "0", STR_PAD_LEFT);
            $oDetalheAuxiliar->dv_prefixo_agencia_credito2  = str_pad($oDetalheAuxiliar->dv_prefixo_agencia_credito2                 ,  1, "0", STR_PAD_LEFT);
            $oDetalheAuxiliar->conta_credito2               = str_pad($oDetalheAuxiliar->conta_credito2                     , 11, "0", STR_PAD_LEFT);
            $oDetalheAuxiliar->dv_conta_credito2            = str_pad($oDetalheAuxiliar->dv_conta_credito2                   ,  1, "0", STR_PAD_LEFT);
            $oDetalheAuxiliar->nome_favorecido2             = str_pad($oDetalheAuxiliar->nome_favorecido2                      , 30, " ", STR_PAD_RIGHT);
            $oDetalheAuxiliar->valor_partilha2              = str_pad(number_format(round($oDetalheAuxiliar->valor_partilha2,2), 2, '', ''), 13, "0", STR_PAD_LEFT);
            $oDetalheAuxiliar->brancos2                     = str_repeat(" ",13);

            $oDetalheAuxiliar->banco_credito3               = str_pad($oDetalheAuxiliar->banco_credito3                       ,  3, "0", STR_PAD_LEFT);
            $oDetalheAuxiliar->camara_compensacao3          = str_repeat("0",3);
            $oDetalheAuxiliar->prefixo_agencia_credito3     = str_pad(substr($oDetalheAuxiliar->prefixo_agencia_credito3,-4)       ,  4, "0", STR_PAD_LEFT);
            $oDetalheAuxiliar->dv_prefixo_agencia_credito3  = str_pad($oDetalheAuxiliar->dv_prefixo_agencia_credito3                   ,  1, "0", STR_PAD_LEFT);
            $oDetalheAuxiliar->conta_credito3               = str_pad($oDetalheAuxiliar->conta_credito3                       , 11, "0", STR_PAD_LEFT);
            $oDetalheAuxiliar->dv_conta_credito3            = str_pad($oDetalheAuxiliar->dv_conta_credito3                     ,  1, "0", STR_PAD_LEFT);
            $oDetalheAuxiliar->nome_favorecido3             = str_pad($oDetalheAuxiliar->nome_favorecido3                        , 30, " ", STR_PAD_RIGHT);
            $oDetalheAuxiliar->valor_partilha3              = str_pad(number_format(round($oDetalheAuxiliar->valor_partilha3,2), 2, '', ''), 13, "0", STR_PAD_LEFT);
            $oDetalheAuxiliar->brancos3                     = str_repeat(" ",13);

            $oDetalheAuxiliar->banco_credito4               = str_pad($oDetalheAuxiliar->banco_credito4                    ,  3, "0", STR_PAD_LEFT);
            $oDetalheAuxiliar->camara_compensacao4          = str_repeat("0",3);
            $oDetalheAuxiliar->prefixo_agencia_credito4     = str_pad(substr($oDetalheAuxiliar->prefixo_agencia_credito4,-4)    ,  4, "0", STR_PAD_LEFT);
            $oDetalheAuxiliar->dv_prefixo_agencia_credito4  = str_pad($oDetalheAuxiliar->dv_prefixo_agencia_credito4                ,  1, "0", STR_PAD_LEFT);
            $oDetalheAuxiliar->conta_credito4               = str_pad($oDetalheAuxiliar->conta_credito4                    , 11, "0", STR_PAD_LEFT);
            $oDetalheAuxiliar->dv_conta_credito4            = str_pad($oDetalheAuxiliar->dv_conta_credito4                  ,  1, "0", STR_PAD_LEFT);
            $oDetalheAuxiliar->nome_favorecido4             = str_pad($oDetalheAuxiliar->nome_favorecido4                     , 30, " ", STR_PAD_RIGHT);
            $oDetalheAuxiliar->valor_partilha4              = str_pad(number_format(round($oDetalheAuxiliar->valor_partilha4,2), 2, '', ''), 13, "0", STR_PAD_LEFT);
            $oDetalheAuxiliar->brancos4                     = str_repeat(" ",13);

            $oDetalheAuxiliar->tipo_documento_favorecido1   = "4";
            $oDetalheAuxiliar->numero_documento_favorecido1 = str_pad($oDetalheAuxiliar->numero_documento_favorecido1, 14, "0", STR_PAD_LEFT);

            $oDetalheAuxiliar->tipo_documento_favorecido2   = "4";
            $oDetalheAuxiliar->numero_documento_favorecido2 = str_pad($oDetalheAuxiliar->numero_documento_favorecido2, 14, "0", STR_PAD_LEFT);

            $oDetalheAuxiliar->tipo_documento_favorecido3   = "4";
            $oDetalheAuxiliar->numero_documento_favorecido3 = str_pad($oDetalheAuxiliar->numero_documento_favorecido3, 14, "0", STR_PAD_LEFT);

            $oDetalheAuxiliar->tipo_documento_favorecido4   = "4";
            $oDetalheAuxiliar->numero_documento_favorecido4 = str_pad($oDetalheAuxiliar->numero_documento_favorecido4, 14, "0", STR_PAD_LEFT);


            $iRegistro++;
            $oDetalheAuxiliar->sequencial                   = str_pad($iRegistro ,  6, "0", STR_PAD_LEFT);
            if ( $oLayoutTxt->setByLineOfDBUtils($oDetalheAuxiliar,3,2) == false ) {
                throw new Exception ("[ 6 ] - Erro ao gerar Detalhe Auxiliar do Arquivo");
            }


            /*
             * Dados ref aos campos da linha Detalhe Auxiliar / Remessa / Registro 2
             * Prefeitura e Honorários - Somente com os dados da prefeitura
             */

            $oDetalheAuxiliarPref->id                           = "2";
            $oDetalheAuxiliarPref->nosso_numero                 = str_pad($aDados->numbanco                       , 17, "0", STR_PAD_RIGHT);

            $oDetalheAuxiliarPref->banco_credito1               = str_pad($oDetalheAuxiliarPref->banco_credito1                     ,  3, "0", STR_PAD_LEFT);
            $oDetalheAuxiliarPref->camara_compensacao1          = str_repeat("0",3);
            $oDetalheAuxiliarPref->prefixo_agencia_credito1     = str_pad(substr($oDetalheAuxiliarPref->prefixo_agencia_credito1,-4)     ,  4, "0", STR_PAD_LEFT);
            $oDetalheAuxiliarPref->dv_prefixo_agencia_credito1  = str_pad($oDetalheAuxiliarPref->dv_prefixo_agencia_credito1                 ,  1, "0", STR_PAD_LEFT);
            $oDetalheAuxiliarPref->conta_credito1               = str_pad($oDetalheAuxiliarPref->conta_credito1                     , 11, "0", STR_PAD_LEFT);
            $oDetalheAuxiliarPref->dv_conta_credito1            = str_pad($oDetalheAuxiliarPref->dv_conta_credito1                   ,  1, "0", STR_PAD_LEFT);
            $oDetalheAuxiliarPref->nome_favorecido1             = str_pad($oDetalheAuxiliarPref->nome_favorecido1                      , 30, " ", STR_PAD_RIGHT);
            $oDetalheAuxiliarPref->valor_partilha1              = str_pad(number_format(round($oDetalheAuxiliarPref->valor_partilha1,2), 2, '', ''), 13, "0", STR_PAD_LEFT);
            $oDetalheAuxiliarPref->brancos1                     = str_repeat(" ",13);

            $oDetalheAuxiliarPref->banco_credito2               = str_pad($oDetalheAuxiliarPref->banco_credito2                  ,  3, "0", STR_PAD_LEFT);
            $oDetalheAuxiliarPref->camara_compensacao2          = str_repeat("0",3);
            $oDetalheAuxiliarPref->prefixo_agencia_credito2     = str_pad(substr($oDetalheAuxiliarPref->prefixo_agencia_credito2,-4)  ,  4, "0", STR_PAD_LEFT);
            $oDetalheAuxiliarPref->dv_prefixo_agencia_credito2  = str_pad($oDetalheAuxiliarPref->dv_prefixo_agencia_credito2              ,  1, "0", STR_PAD_LEFT);
            $oDetalheAuxiliarPref->conta_credito2               = str_pad($oDetalheAuxiliarPref->conta_credito2                  , 11, "0", STR_PAD_LEFT);
            $oDetalheAuxiliarPref->dv_conta_credito2            = str_pad($oDetalheAuxiliarPref->dv_conta_credito2                ,  1, "0", STR_PAD_LEFT);
            $oDetalheAuxiliarPref->nome_favorecido2             = str_pad($oDetalheAuxiliarPref->nome_favorecido2             , 30, " ", STR_PAD_RIGHT);
            $oDetalheAuxiliarPref->valor_partilha2              = str_pad(number_format(round($oDetalheAuxiliarPref->valor_partilha2,2), 2, '', ''), 13, "0", STR_PAD_LEFT);
            $oDetalheAuxiliarPref->brancos2                     = str_repeat(" ",13);

            $oDetalheAuxiliarPref->banco_credito3               = str_pad($oDetalheAuxiliarPref->banco_credito3                ,  3, "0", STR_PAD_LEFT);
            $oDetalheAuxiliarPref->camara_compensacao3          = str_repeat("0",3);
            $oDetalheAuxiliarPref->prefixo_agencia_credito3     = str_pad(substr($oDetalheAuxiliarPref->prefixo_agencia_credito3,-4)  ,  4, "0", STR_PAD_LEFT);
            $oDetalheAuxiliarPref->dv_prefixo_agencia_credito3  = str_pad($oDetalheAuxiliarPref->dv_prefixo_agencia_credito3            ,  1, "0", STR_PAD_LEFT);
            $oDetalheAuxiliarPref->conta_credito3               = str_pad($oDetalheAuxiliarPref->conta_credito3                , 11, "0", STR_PAD_LEFT);
            $oDetalheAuxiliarPref->dv_conta_credito3            = str_pad($oDetalheAuxiliarPref->dv_conta_credito3              ,  1, "0", STR_PAD_LEFT);
            $oDetalheAuxiliarPref->nome_favorecido3             = str_pad($oDetalheAuxiliarPref->nome_favorecido3                 , 30, " ", STR_PAD_RIGHT);
            $oDetalheAuxiliarPref->valor_partilha3              = str_pad(number_format(round($oDetalheAuxiliarPref->valor_partilha3,2), 2, '', ''), 13, "0", STR_PAD_LEFT);
            $oDetalheAuxiliarPref->brancos3                     = str_repeat(" ",13);

            $oDetalheAuxiliarPref->banco_credito4               = str_pad($oDetalheAuxiliarPref->banco_credito4             ,  3, "0", STR_PAD_LEFT);
            $oDetalheAuxiliarPref->camara_compensacao4          = str_repeat("0",3);
            $oDetalheAuxiliarPref->prefixo_agencia_credito4     = str_pad(substr($oDetalheAuxiliarPref->prefixo_agencia_credito4,-4)  ,  4, "0", STR_PAD_LEFT);
            $oDetalheAuxiliarPref->dv_prefixo_agencia_credito4  = str_pad($oDetalheAuxiliarPref->dv_prefixo_agencia_credito4         ,  1, "0", STR_PAD_LEFT);
            $oDetalheAuxiliarPref->conta_credito4               = str_pad($oDetalheAuxiliarPref->conta_credito4             , 11, "0", STR_PAD_LEFT);
            $oDetalheAuxiliarPref->dv_conta_credito4            = str_pad($oDetalheAuxiliarPref->dv_conta_credito4           ,  1, "0", STR_PAD_LEFT);
            $oDetalheAuxiliarPref->nome_favorecido4             = str_pad($oDetalheAuxiliarPref->nome_favorecido4              , 30, " ", STR_PAD_RIGHT);
            $oDetalheAuxiliarPref->valor_partilha4              = str_pad(number_format(round($oDetalheAuxiliarPref->valor_partilha4,2), 2, '', ''), 13, "0", STR_PAD_LEFT);
            $oDetalheAuxiliarPref->brancos4                     = str_repeat(" ",13);


            $oDetalheAuxiliarPref->tipo_documento_favorecido1   = "4";
            $oDetalheAuxiliarPref->numero_documento_favorecido1 = str_pad($oDetalheAuxiliarPref->numero_documento_favorecido1, 14, "0", STR_PAD_LEFT);

            $oDetalheAuxiliarPref->tipo_documento_favorecido2   = "4";
            $oDetalheAuxiliarPref->numero_documento_favorecido2 = str_pad($oDetalheAuxiliarPref->numero_documento_favorecido2, 14, "0", STR_PAD_LEFT);

            $oDetalheAuxiliarPref->tipo_documento_favorecido3   = "4";
            $oDetalheAuxiliarPref->numero_documento_favorecido3 = str_pad($oDetalheAuxiliarPref->numero_documento_favorecido3, 14, "0", STR_PAD_LEFT);

            $oDetalheAuxiliarPref->tipo_documento_favorecido4   = "4";
            $oDetalheAuxiliarPref->numero_documento_favorecido4 = str_pad($oDetalheAuxiliarPref->numero_documento_favorecido4, 14, "0", STR_PAD_LEFT);

            if ($imprimeSegundaLinha === true) {

                $iRegistro++;
                $oDetalheAuxiliarPref->sequencial                   = str_pad($iRegistro, 6, "0", STR_PAD_LEFT);
                if ( $oLayoutTxt->setByLineOfDBUtils($oDetalheAuxiliarPref,3,2) == false ) {
                    throw new Exception ("[ 6 ] - Erro ao gerar Detalhe Auxiliar referente aos dados da Prefeitura do Arquivo");
                }
            }


        }
        $oTrailer = new stdClass();
        $oTrailer->id      = "9";
        $oTrailer->brancos = str_repeat(" ",393); //fixo
        $oTrailer->sequencial_ultimo_registro = str_pad($iRegistro+1, 6, "0", STR_PAD_LEFT);
        if ( $oLayoutTxt->setByLineOfDBUtils($oTrailer,5,9) == false ) {
            throw new Exception("[ 7 ] Erro ao gerar trailer do Arquivo");
        }

        $iArquivo   = DBLargeObject::escrita("tmp/".$sNomeArq, $iOid);

    }

    /**
     * Metodo utilizado para geracao dos arquivos de remessa ao TJ referentes a cobranca registrada
     *
     * @param date    $dtProcessamento  - Define a data dos registros de recibos que constarao no arquivo
     * @param string  $sNomeArq         - Define o nome do arquivo que será gerado
     *
     */
    public function geraArquivoRemessaTj($dtProcessamento,$sNomeArq) {

        $oDaoPartilhaArquivo     = db_utils::getDao('partilhaarquivo');
        $oDaoPartilhaArquivoReg  = db_utils::getDao('partilhaarquivoreg');
        $oDaoDbConfig            = db_utils::getDao('db_config');
        $oLayoutTxt              = new db_layouttxt(101, "tmp/".$sNomeArq);

        $aPartilhaCustas         = array();
        $iRegistro = 1;

        //Buscamos os dados da instituição
        $rsDbConfig = $oDaoDbConfig->sql_record($oDaoDbConfig->sql_query(db_getsession("DB_instit"), "*"));
        if ($oDaoDbConfig->numrows == 0) {
            throw new Exception("[ 2 ] - Erro ao buscar dados da instituição!\nErro: {$oDaoDbConfig->erro_msg}");
        }
        $oDbConfig = db_utils::fieldsMemory($rsDbConfig,0);
        /*
         * Cadastramos o registro de geração do arquivo
         */
        $oDaoPartilhaArquivo->v78_dtgeracao = $dtProcessamento;
        $oDaoPartilhaArquivo->v78_nomearq   = $sNomeArq;
        $oDaoPartilhaArquivo->v78_tipoarq   = 2;
        $oDaoPartilhaArquivo->incluir(null);
        if ($oDaoPartilhaArquivo->erro_status == "0") {
            throw new Exception("[ 3 ] - Erro ao inserir dados em Partilha Arquivo\n {$oDaoPartilhaArquivo->erro_msg}");
        }

        $aDtProcessamento = explode("-",$dtProcessamento);
        $sDtProcessamento = $aDtProcessamento[2].$aDtProcessamento[1].$aDtProcessamento[0];

        $oHeader = new stdClass();
        $oHeader->id                         = "0";
        $oHeader->data_geracao_arquivo       = date("dmY",db_getsession("DB_datausu"));
        $oHeader->codigo_identificador_TJERJ = str_pad($oDbConfig->db21_codtj, 5, "0",STR_PAD_LEFT) ;
        $oHeader->nome_municipio             = str_pad($oDbConfig->munic, 30, " ", STR_PAD_RIGHT) ;
        $oHeader->versao_layout              = "0300";
        $oHeader->data_movimento             = $sDtProcessamento;
        $oHeader->sequencial_arquivo         = str_pad($oDaoPartilhaArquivo->v78_sequencial, 3, "0", STR_PAD_LEFT);
        $oHeader->brancos                    = str_repeat(" ",236);
        $oHeader->sequencial_registro        = "00001";
        if ( $oLayoutTxt->setByLineOfDBUtils($oHeader,1,"0") == false ) {
            throw new Exception ("[ 4 ] - Erro ao gerar Header do Arquivo");
        }

        $oRegistros = $this->getDadosReciboPartilha(2,$dtProcessamento);
        if (count($oRegistros) == 0) {
            throw new Exception("[ 5 ] - Nenhum registro de emissão com cobranca encontrado para a data informada!");
        }

        foreach ( $oRegistros as $aDados ) {

            $iRegistro++;

            $aDataVencimento         = explode("-",$aDados->k00_dtpaga);
            $sDataVencimento         = $aDataVencimento[2].$aDataVencimento[1].$aDataVencimento[0];

            $aDataUltimaDistribuicao = explode("-",$aDados->v70_data);
            $sDataUltimaDistribuicao = $aDataUltimaDistribuicao[2].$aDataUltimaDistribuicao[1].$aDataUltimaDistribuicao[0];

            $oDetalheTipo1 = new stdClass();
            $oDetalheTipo1->id                              = "1";
            $oDetalheTipo1->nosso_numero                    = str_pad($aDados->numbanco                                             , 17, "0", STR_PAD_RIGHT);
            $oDetalheTipo1->data_vencimento_boleto_bancario = $sDataVencimento;
            $oDetalheTipo1->valor_total_boleto_bancario     = str_pad(number_format(round($aDados->valor_total_recibo,2), 2, '', ''), 14, "0", STR_PAD_LEFT);

            $oDetalheTipo1->conta_corrente_TJERJ            = str_pad($aDados->conta_TJ                                             , 10, "0", STR_PAD_LEFT);
            $oDetalheTipo1->valor_devido_TJERJ              = str_pad(number_format(round($aDados->valor_TJ,2), 2, '', '')          ,  9, "0", STR_PAD_LEFT);

            $oDetalheTipo1->conta_corrente_CAARJ            = str_pad($aDados->conta_CAARJ                                          , 10, "0", STR_PAD_LEFT);
            $oDetalheTipo1->valor_devido_CAARJ              = str_pad(number_format(round($aDados->valor_CAARJ,2), 2, '', '')       ,  9, "0", STR_PAD_LEFT);

            $oDetalheTipo1->conta_corrente_FUNDPERJ         = str_pad($aDados->conta_FUNDPERJ                                       , 10, "0", STR_PAD_LEFT);
            $oDetalheTipo1->valor_devido_FUNDPERJ           = str_pad(number_format(round($aDados->valor_FUNDPERJ,2), 2, '', '')    ,  9, "0", STR_PAD_LEFT);

            $oDetalheTipo1->conta_corrente_FUNPERJ          = str_pad($aDados->conta_FUNPERJ                                        , 10, "0", STR_PAD_LEFT);
            $oDetalheTipo1->valor_devido_FUNPERJ            = str_pad(number_format(round($aDados->valor_FUNPERJ,2), 2, '', '')     ,  9, "0", STR_PAD_LEFT);

            $oDetalheTipo1->reservado_TJERJ                 = str_repeat(" ",19);
            $oDetalheTipo1->brancos                         = str_repeat(" ",160);
            $oDetalheTipo1->sequencial_registro             = str_pad($iRegistro, 5, "0", STR_PAD_LEFT);
            if ( $oLayoutTxt->setByLineOfDBUtils($oDetalheTipo1,3,1) == false ) {
                throw new Exception ("[ 7 ] - Erro ao gerar Detalhe Tipo 1 do Arquivo");
            }


            $iRegistro++;
            $oDetalheTipo3 = new stdClass();
            $oDetalheTipo3->id                                                = "3";
            $oDetalheTipo3->numero_certidao                                   = str_pad($aDados->v51_certidao                           , 10, "0", STR_PAD_LEFT);
            $oDetalheTipo3->numero_processo_tj                                = str_pad($aDados->codforo_antigo                         , 14, " ", STR_PAD_LEFT); //NUMERO ANTIGO DO PROCESSO DO TJ
            $oDetalheTipo3->data_ultima_distribuicao_processo                 = "$sDataUltimaDistribuicao";
            $oDetalheTipo3->valor_total_tributo_devido_processo               = str_pad(number_format(round($aDados->v70_valorinicial,2), 2, '', ''), 12, "0", STR_PAD_LEFT);
            $oDetalheTipo3->numero_parcela                                    = "999";
            $oDetalheTipo3->total_parcelas                                    = "001";

            $oDetalheTipo3->codigo_receita_CAARJ                              = str_pad($aDados->codigo_receita_CAARJ              , 5, "0", STR_PAD_LEFT);
            $oDetalheTipo3->valor_CAARJ                                       = str_pad(number_format(round($aDados->valor_CAARJ,2), 2, '', ''), 9, "0", STR_PAD_LEFT) ;

            $oDetalheTipo3->codigo_receita_atos_oficiais_justica_avaliadores  = str_pad($aDados->codigo_receita_atos_oficiais_justica_avaliadores                      , 5, "0", STR_PAD_LEFT) ;
            $oDetalheTipo3->valor_receita_atos_oficiais_justica_avaliadores   = str_pad(number_format(round($aDados->valor_receita_atos_oficiais_justica_avaliadores,2), 2, '', ''), 9, "0", STR_PAD_LEFT) ;

            $oDetalheTipo3->codigo_receita_atos_escrivaes_divida_ativa        = str_pad($aDados->codigo_receita_atos_escrivaes_divida_ativa                             , 5, "0", STR_PAD_LEFT);
            $oDetalheTipo3->valor_receita_atos_escrivaes_divida_ativa         = str_pad(number_format(round($aDados->valor_receita_atos_escrivaes_divida_ativa,2), 2, '', ''), 9, "0", STR_PAD_LEFT);

            $oDetalheTipo3->codigo_receita_taxa_judiciaria                    = str_pad($aDados->codigo_receita_taxa_judiciaria                                        , 5, "0", STR_PAD_LEFT);
            $oDetalheTipo3->valor_receita_taxa_judiciaria                     = str_pad(number_format(round($aDados->valor_receita_taxa_judiciaria,2), 2, '', ''), 9, "0", STR_PAD_LEFT);

            $oDetalheTipo3->codigo_receita_atos_distribuidores                = str_pad($aDados->codigo_receita_atos_distribuidores                                    , 10, "0", STR_PAD_LEFT);
            $oDetalheTipo3->valor_receita_atos_distribuidores                 = str_pad(number_format(round($aDados->valor_receita_atos_distribuidores,2), 2, '', ''), 9, "0", STR_PAD_LEFT);

            $oDetalheTipo3->conta_corrente_acrescimo_20                       = "";
            $oDetalheTipo3->valor_corrente_acrescimo_20                       = str_pad(number_format(round($aDados->valor_corrente_acrescimo_20,2), 2, '', ''),  9, "0", STR_PAD_LEFT);

            $oDetalheTipo3->conta_FUNPERJ                                     = str_pad($aDados->conta_FUNPERJ                                                         , 10, "0", STR_PAD_LEFT);
            $oDetalheTipo3->valor_FUNPERJ                                     = str_pad(number_format(round($aDados->valor_FUNPERJ,2), 2, '', ''),  9, "0", STR_PAD_LEFT);

            $oDetalheTipo3->conta_FUNDPERJ                                    = str_pad($aDados->conta_FUNDPERJ                                                        , 10, "0", STR_PAD_LEFT);
            $oDetalheTipo3->valor_FUNDPERJ                                    = str_pad(number_format(round($aDados->valor_FUNDPERJ,2), 2, '', ''),  9, "0", STR_PAD_LEFT);

            $oDetalheTipo3->codigo_receita_HONORARIOS                         = str_pad($aDados->codigo_receita_HONORARIOS                 , 5, "0", STR_PAD_LEFT);
            $oDetalheTipo3->valor_HONORARIOS                                  = str_pad(number_format(round($aDados->valor_HONORARIOS,2), 2, '', ''), 9, "0",STR_PAD_LEFT);

            $oDetalheTipo3->codigo_receita_DISTRIBUIDOR                       = str_pad($aDados->codigo_receita_DISTRIBUIDOR                , 5, "0", STR_PAD_LEFT);

            $oDetalheTipo3->codigo_receita_DISTRIBUIDOR                       = str_pad($aDados->codigo_receita_DISTRIBUIDOR               , 5, "0", STR_PAD_LEFT);


            $oDetalheTipo3->reservado_TJERJ                                   = str_pad($aDados->codforo_novo                                                          , 25, " ", STR_PAD_RIGHT) ; //NUMERO DO PROCESSO DO FORO NOVO
            $oDetalheTipo3->brancos                                           = str_repeat(" ",87);
            $oDetalheTipo3->sequencial_registro                               = str_pad($iRegistro, 5, "0", STR_PAD_LEFT);
            if ( $oLayoutTxt->setByLineOfDBUtils($oDetalheTipo3,3,3) == false ) {
                throw new Exception ("[ 8 ] - Erro ao gerar Detalhe Tipo 2 do Arquivo");
            }

        }

        $oTrailer = new stdClass();
        $oTrailer->id                        = "9";
        $oTrailer->brancos                   = str_repeat(" ",294);
        $oTrailer->sequencial_registro       = str_pad($iRegistro+1, 5, "0", STR_PAD_LEFT);
        if ( $oLayoutTxt->setByLineOfDBUtils($oTrailer,5,9) == false ) {
            throw new Exception("[ 9 ] Erro ao gerar trailer do Arquivo");
        }

        /*
         * Incluímos o registro da custa para o processo do foro gerado no arquivo
         * na tabela partilhaarquivoreg
         *
         */
        for ( $iInd=0; $iInd < count($aDados->PartilhaCustas); $iInd++) {

            $oDaoPartilhaArquivoReg->v79_partilhaarquivo           = $oDaoPartilhaArquivo->v78_sequencial;
            $oDaoPartilhaArquivoReg->v79_processoforopartilhacusta = $aDados->PartilhaCustas[$iInd];
            $oDaoPartilhaArquivoReg->incluir(null);
            if ( $oDaoPartilhaArquivoReg->erro_status == "0") {
                throw new Exception("[ 6 ] - Erro ao incluir dados em partilhaarquivoreg\n Erro: {$oDaoPartilhaArquivoReg->erro_msg} ");
            }
        }

    }

    /**
     *
     * Método utilizado para buscar as informações necessárias para a geração dos arquivos
     * @param integer $iTipoArq        - Tipo de busca de informações, 1: Banco 2: TJ
     * @param string    $dtProcessamento - Data da emissão dos recibos
     */
    public function getDadosReciboPartilha($iTipoArq,$dtProcessamento) {

        $oProcessoForoPartilhaCusta = new cl_processoforopartilhacusta;

        $aRegistrosAgrupados        = array();
        $aPatrilhaCustas            = array();
        $sCampos  = " on (recibopaga.k00_numnov, cgmrecibopaga.z01_nome, cgmfavorecido.z01_nome,        \n";
        $sCampos .= "             processoforopartilhacusta.v77_taxa,cgmfavorecido.z01_cgccpf,          \n";
        $sCampos .= "             processoforopartilhacusta.v77_processoforopartilha)                   \n";
        $sCampos .= " recibopaga.k00_numpre,                                                            \n";
        $sCampos .= " recibopaga.k00_numnov,                                                            \n";
        $sCampos .= " recibopaga.k00_dtvenc,                                                            \n";
        $sCampos .= " recibopagaboleto.k138_data                          as k00_dtoper,                \n";
        $sCampos .= " recibopaga.k00_dtpaga,                                                            \n";
        $sCampos .= " cgmrecibopaga.z01_numcgm,                                                         \n";
        $sCampos .= " cgmrecibopaga.z01_nome,                                                           \n";
        $sCampos .= " cgmrecibopaga.z01_ender,                                                          \n";
        $sCampos .= " cgmrecibopaga.z01_numero,                                                         \n";
        $sCampos .= " cgmrecibopaga.z01_bairro,                                                         \n";
        $sCampos .= " cgmrecibopaga.z01_cep,                                                            \n";
        $sCampos .= " cgmrecibopaga.z01_munic,                                                          \n";
        $sCampos .= " cgmrecibopaga.z01_cgccpf,                                                         \n";
        $sCampos .= " cgmrecibopaga.z01_uf,                                                             \n";
        $sCampos .= " inicialcert.v51_inicial,                                                          \n";
        $sCampos .= " inicialcert.v51_certidao,                                                         \n";
        $sCampos .= " processoforo.v70_sequencial,                                                      \n";
        $sCampos .= " processoforo.v70_data,                                                            \n";
        $sCampos .= " processoforo.v70_valorinicial,                                                    \n";
        $sCampos .= " processoforopartilhacusta.v77_sequencial,                                         \n";
        $sCampos .= " processoforopartilhacusta.v77_processoforopartilha,                               \n";
        $sCampos .= " processoforopartilhacusta.v77_taxa                  as taxa_codigo,               \n";
        $sCampos .= " taxa.ar36_receita                                   as taxa_receita,              \n";
        $sCampos .= " round(processoforopartilhacusta.v77_valor, 2)       as taxa_valor,                \n";
        $sCampos .= " favorecido.v86_numcgm                               as favorecido_numcgm,         \n";
        $sCampos .= " favorecido.v86_containterna                         as favorecido_containterna,   \n";
        $sCampos .= " cgmfavorecido.z01_nome                              as favorecido_nome,           \n";
        $sCampos .= " cgmfavorecido.z01_cgccpf                            as favorecido_cgccpf,         \n";
        $sCampos .= " bancoagencia.db89_db_bancos                         as favorecido_banco,          \n";
        $sCampos .= " bancoagencia.db89_codagencia                        as favorecido_agencia,        \n";
        $sCampos .= " bancoagencia.db89_digito                            as favorecido_agenciadv,      \n";
        $sCampos .= " contabancaria.db83_conta                            as favorecido_conta,          \n";
        $sCampos .= " coalesce(contabancaria.db83_dvconta,'0')            as favorecido_contadv         \n";


        /*
         * Filtramos os registros:
         *  - somente retornados os recibos gerados na data informada
         *  - somente retornados os registros que não estiverem na tabela partilhaarquivoreg, ou seja, que não foram enviados
         *  para o mesmo tipo de arquivo.
         */
        $sWhere    = " recibopagaboleto.k138_data <= '{$dtProcessamento}'                                                      \n";
        $sWhere   .= " and processoforopartilha.v76_dtpagamento is null                                                       \n";
        $sWhere   .= " and processoforopartilha.v76_tipolancamento = 1                                                        \n";
        $sWhere   .= " and cancrecibopaga.k134_sequencial is null                                                             \n";
        $sWhere   .= " and not exists ( select 1                                                                              \n";
        $sWhere   .= "                    from partilhaarquivoreg as p                                                        \n";
        $sWhere   .= "                   inner join partilhaarquivo on partilhaarquivo.v78_sequencial = p.v79_partilhaarquivo \n";
        $sWhere   .= "                   where partilhaarquivo.v78_tipoarq = {$iTipoArq}                                      \n";
        $sWhere   .= "                     and p.v79_processoforopartilhacusta = processoforopartilhacusta.v77_sequencial)    \n";

        $sOrderBy  = " recibopaga.k00_numnov, cgmrecibopaga.z01_nome, cgmfavorecido.z01_nome";
        $sSqlDados = $oProcessoForoPartilhaCusta->sql_query_recibo_banco(null, "distinct {$sCampos}", $sOrderBy, $sWhere);
        if ($iTipoArq == 1) {
            $sSql = "
            SELECT *
            FROM
              (SELECT DISTINCT ON (recibopaga.k00_numnov,
                                   cgmrecibopaga.z01_nome,
                                   cgmfavorecido.z01_nome,
                                   processoforopartilhacusta.v77_taxa,
                                   cgmfavorecido.z01_cgccpf,
                      processoforopartilhacusta.v77_processoforopartilha)
                      recibopaga.k00_numpre,
                      recibopaga.k00_numnov,
                      recibopaga.k00_dtvenc,
                      recibopagaboleto.k138_data AS k00_dtoper,
                      recibopaga.k00_dtpaga,
                      cgmrecibopaga.z01_numcgm,
                      cgmrecibopaga.z01_nome,
                      cgmrecibopaga.z01_ender,
                      cgmrecibopaga.z01_numero,
                      cgmrecibopaga.z01_bairro,
                      cgmrecibopaga.z01_cep,
                      cgmrecibopaga.z01_munic,
                      cgmrecibopaga.z01_cgccpf,
                      cgmrecibopaga.z01_uf,
                      inicialcert.v51_inicial,
                      inicialcert.v51_certidao,
                      processoforo.v70_sequencial,
                      processoforo.v70_data,
                      processoforo.v70_valorinicial,
                      processoforopartilhacusta.v77_sequencial,
                      processoforopartilhacusta.v77_processoforopartilha,
                      processoforopartilhacusta.v77_taxa AS taxa_codigo,
                      taxa.ar36_receita AS taxa_receita,
                      round(processoforopartilhacusta.v77_valor, 2) AS taxa_valor,
                      favorecido.v86_numcgm AS favorecido_numcgm,
                      favorecido.v86_containterna AS favorecido_containterna,
                      cgmfavorecido.z01_nome AS favorecido_nome,
                      cgmfavorecido.z01_cgccpf AS favorecido_cgccpf,
                      bancoagencia.db89_db_bancos AS favorecido_banco,
                      bancoagencia.db89_codagencia AS favorecido_agencia,
                      bancoagencia.db89_digito AS favorecido_agenciadv,
                      contabancaria.db83_conta AS favorecido_conta,
                      coalesce(contabancaria.db83_dvconta, '0') AS favorecido_contadv
                 FROM processoforopartilha
           INNER JOIN processoforopartilhacusta ON v77_processoforopartilha = v76_sequencial
           INNER JOIN taxa ON taxa.ar36_sequencial = processoforopartilhacusta.v77_taxa
           INNER JOIN favorecidotaxa ON favorecidotaxa.v87_taxa = taxa.ar36_sequencial
           INNER JOIN favorecido ON favorecido.v86_sequencial = favorecidotaxa.v87_favorecido
           INNER JOIN cgm AS cgmfavorecido ON cgmfavorecido.z01_numcgm = favorecido.v86_numcgm
           INNER JOIN contabancaria ON contabancaria.db83_sequencial = favorecido.v86_contabancaria
           INNER JOIN bancoagencia ON contabancaria.db83_bancoagencia = bancoagencia.db89_sequencial
           INNER JOIN processoforo ON processoforo.v70_sequencial = processoforopartilha.v76_processoforo
           INNER JOIN processoforoinicial ON processoforoinicial.v71_processoforo = processoforo.v70_sequencial
           INNER JOIN inicial ON processoforoinicial.v71_inicial = inicial.v50_inicial
           INNER JOIN inicialcert ON inicialcert.v51_inicial = inicial.v50_inicial
           INNER JOIN arrebanco ON arrebanco.k00_numpre = processoforopartilhacusta.v77_numnov
                  AND cast(trim(k00_numbco) AS numeric) <> 0
           INNER JOIN recibopagaboleto ON recibopagaboleto.k138_numnov = processoforopartilhacusta.v77_numnov
           INNER JOIN recibopaga ON recibopaga.k00_numnov = recibopagaboleto.k138_numnov
           LEFT  JOIN cancrecibopaga ON cancrecibopaga.k134_numnov = recibopagaboleto.k138_numnov
           INNER JOIN cgm AS cgmrecibopaga ON cgmrecibopaga.z01_numcgm = recibopaga.k00_numcgm
                WHERE recibopagaboleto.k138_data = '{$dtProcessamento}'
                  AND processoforopartilha.v76_dtpagamento IS NULL
                  AND processoforopartilha.v76_tipolancamento = 1
                  AND cancrecibopaga.k134_sequencial IS NULL
                  AND NOT EXISTS
                   (SELECT 1
                      FROM partilhaarquivoreg AS p
                INNER JOIN partilhaarquivo ON partilhaarquivo.v78_sequencial = p.v79_partilhaarquivo
                     WHERE partilhaarquivo.v78_tipoarq = 1
                       AND p.v79_processoforopartilhacusta = processoforopartilhacusta.v77_sequencial)

        UNION ALL

               SELECT DISTINCT ON (recibopaga.k00_numnov,
                                   cgmrecibopaga.z01_nome,
                                   cgmfavorecido.z01_nome,
                                   inicialpartilhacustas.v36_taxa,
                                   cgmfavorecido.z01_cgccpf,
                      inicialpartilhacustas.v36_inicialpartilha)
                      recibopaga.k00_numpre,
                      recibopaga.k00_numnov,
                      recibopaga.k00_dtvenc,
                      recibopagaboleto.k138_data AS k00_dtoper,
                      recibopaga.k00_dtpaga,
                      cgmrecibopaga.z01_numcgm,
                      cgmrecibopaga.z01_nome,
                      cgmrecibopaga.z01_ender,
                      cgmrecibopaga.z01_numero,
                      cgmrecibopaga.z01_bairro,
                      cgmrecibopaga.z01_cep,
                      cgmrecibopaga.z01_munic,
                      cgmrecibopaga.z01_cgccpf,
                      cgmrecibopaga.z01_uf,
                      inicialcert.v51_inicial,
                      inicialcert.v51_certidao,
                      NULL::integer AS v70_sequencial,
                      NULL::date AS v70_data,
                      NULL::numeric asv70_valorinicial,
                      inicialpartilhacustas.v36_sequencial,
                      inicialpartilhacustas.v36_inicialpartilha,
                      inicialpartilhacustas.v36_taxa AS taxa_codigo,
                      taxa.ar36_receita AS taxa_receita,
                      round(inicialpartilhacustas.v36_valor, 2) AS taxa_valor,
                      favorecido.v86_numcgm AS favorecido_numcgm,
                      favorecido.v86_containterna AS favorecido_containterna,
                      cgmfavorecido.z01_nome AS favorecido_nome,
                      cgmfavorecido.z01_cgccpf AS favorecido_cgccpf,
                      bancoagencia.db89_db_bancos AS favorecido_banco,
                      bancoagencia.db89_codagencia AS favorecido_agencia,
                      bancoagencia.db89_digito AS favorecido_agenciadv,
                      contabancaria.db83_conta AS favorecido_conta,
                      coalesce(contabancaria.db83_dvconta, '0') AS favorecido_contadv
                 FROM inicialpartilha
           INNER JOIN inicialpartilhacustas ON v36_inicialpartilha = v35_sequencial
           INNER JOIN taxa ON taxa.ar36_sequencial = inicialpartilhacustas.v36_taxa
           INNER JOIN favorecidotaxa ON favorecidotaxa.v87_taxa = taxa.ar36_sequencial
           INNER JOIN favorecido ON favorecido.v86_sequencial = favorecidotaxa.v87_favorecido
           INNER JOIN cgm AS cgmfavorecido ON cgmfavorecido.z01_numcgm = favorecido.v86_numcgm
           INNER JOIN contabancaria ON contabancaria.db83_sequencial = favorecido.v86_contabancaria
           INNER JOIN bancoagencia ON contabancaria.db83_bancoagencia = bancoagencia.db89_sequencial
           INNER JOIN inicial ON inicialpartilha.v35_inicial = inicial.v50_inicial
           INNER JOIN inicialcert ON inicialcert.v51_inicial = inicial.v50_inicial
           INNER JOIN arrebanco ON arrebanco.k00_numpre = inicialpartilhacustas.v36_numnov
                  AND cast(trim(k00_numbco) AS numeric) <> 0
           INNER JOIN recibopagaboleto ON recibopagaboleto.k138_numnov = inicialpartilhacustas.v36_numnov
           INNER JOIN recibopaga ON recibopaga.k00_numnov = recibopagaboleto.k138_numnov
            LEFT JOIN cancrecibopaga ON cancrecibopaga.k134_numnov = recibopagaboleto.k138_numnov
           INNER JOIN cgm AS cgmrecibopaga ON cgmrecibopaga.z01_numcgm = recibopaga.k00_numcgm
                WHERE recibopagaboleto.k138_data = '{$dtProcessamento}'
                  AND inicialpartilha.v35_dtpagamento IS NULL
                  AND inicialpartilha.v35_tipolancamento = 1
                  AND cancrecibopaga.k134_sequencial IS NULL

        UNION ALL

                 SELECT DISTINCT
                        recibopaga.k00_numpre,
                        recibopaga.k00_numnov,
                        recibopaga.k00_dtvenc,
                        recibopagaboleto.k138_data AS k00_dtoper,
                        recibopaga.k00_dtpaga,
                        cgmrecibopaga.z01_numcgm,
                        cgmrecibopaga.z01_nome,
                        cgmrecibopaga.z01_ender,
                        cgmrecibopaga.z01_numero,
                        cgmrecibopaga.z01_bairro,
                        cgmrecibopaga.z01_cep,
                        cgmrecibopaga.z01_munic,
                        cgmrecibopaga.z01_cgccpf,
                        cgmrecibopaga.z01_uf,
                        NULL::integer AS v51_inicial,
                        NULL::integer AS v51_certidao,
                        NULL::integer AS v70_sequencial,
                        NULL::date AS v70_data,
                        NULL::numeric AS v70_valorinicial,
                        NULL::integer AS v77_sequencial,
                        NULL::integer AS v77_processoforopartilha,
                        NULL::integer AS taxa_codigo,
                        NULL::integer AS taxa_receita,
                        NULL::float8 AS taxa_valor,
                        NULL::integer AS favorecido_numcgm,
                        ''::varchar AS favorecido_containterna,
                        ''::varchar AS favorecido_nome,
                        ''::varchar AS favorecido_cgccpf,
                        ''::varchar AS favorecido_banco,
                        ''::varchar AS favorecido_agencia,
                        ''::varchar AS favorecido_agenciadv,
                        ''::varchar AS favorecido_conta,
                        ''::varchar AS favorecido_contadv
               FROM recibopagaboleto
         INNER JOIN recibopaga ON k138_numnov = k00_numnov
         INNER JOIN reciboregistra ON k138_numnov = k146_numpre
         INNER JOIN cgm cgmrecibopaga ON k00_numcgm = z01_numcgm
              WHERE k138_data = '{$dtProcessamento}'
                AND k138_numnov not in (SELECT DISTINCT coalesce(v77_numnov, 0) AS numnov
                                          FROM processoforopartilhacusta
                                     UNION ALL
                                        SELECT DISTINCT coalesce(v36_numnov, 0) AS numnov
                                          FROM inicialpartilhacustas)
        ) AS x
          ORDER BY k00_numnov,
                   z01_nome,
                   favorecido_nome;";

            $sSqlDados = $sSql;
        }
        //die($sSqlDados);
        $rsDados   = $oProcessoForoPartilhaCusta->sql_record($sSqlDados);
        if ( $rsDados && pg_num_rows($rsDados) > 0 ) {

            //Buscamos os dados da instituição
            $oDaoDbConfig         = db_utils::getDao('db_config');
            $rsDbConfig = $oDaoDbConfig->sql_record($oDaoDbConfig->sql_query(db_getsession("DB_instit"), " z01_numcgm, z01_cgccpf ", null, "prefeitura is true"));
            if ($oDaoDbConfig->numrows == 0) {
                throw new Exception("[ 1 ] - Erro ao buscar dados da instituição!\nErro: {$oDaoDbConfig->erro_msg}");
            }
            $oDbConfig = db_utils::fieldsMemory($rsDbConfig,0);

            $oDaoFavorecido       = db_utils::getDao('favorecido');
            $rsFavorecido = $oDaoFavorecido->sql_record($oDaoFavorecido->sql_query_dados(null,"*",null,"v86_numcgm = {$oDbConfig->z01_numcgm}"));
            if ($oDaoFavorecido->numrows == 0) {
                throw new Exception("[ 2 ] - Não encontrado cadastro de Favorecido para a instituição! ");
            }
            $oDadosInstituicao = db_utils::fieldsMemory($rsFavorecido,0);

            if ($iTipoArq == 2 ) {

                $this->setIdTaxasEspecificasTJ();

            }

            $aRecibo    = array();
            $aRegistros = array();

            $aTaxas     = array();
            for ($iInd = 0; $iInd < pg_num_rows($rsDados); $iInd ++) {

                $oRegistro = db_utils::fieldsMemory($rsDados, $iInd);

                if (!isset($aRegistros[$oRegistro->k00_numnov])) {

                    $oDados     = new stdClass();
                    $oDados->valor_FUNDPERJ = 0;
                    $oDados->valor_HONORARIOS = 0;
                    $oDados->valor_FUNPERJ = 0;
                    $oDados->valor_CAARJ = 0;
                    $oDados->valor_TJ = 0;
                    $oDados->valor_DISTRIBUIDOR = 0;
                    $oDados->valor_FUNARPEN = 0;
                    $aTaxas     = array();
                } else {
                    $oDados = $aRegistros[$oRegistro->k00_numnov];
                }

                $sSqlArrebanco   = "select k00_numbco                                       ";
                $sSqlArrebanco  .= "  from arrebanco                                        ";
                $sSqlArrebanco  .= " where arrebanco.k00_numpre = {$oRegistro->k00_numnov}  ";
                $sSqlArrebanco  .= "   and round(cast(trim(k00_numbco) as numeric)) <> 0    ";
                $sSqlArrebanco  .= " order by round(cast(trim(k00_numbco) as numeric)) desc ";
                $sSqlArrebanco  .= " limit 1                                                ";

                $rsSqlArrebanco  = db_query($sSqlArrebanco);
                if ( $rsSqlArrebanco && pg_num_rows($rsSqlArrebanco) > 0 ){
                    $oRegistro->numbanco  = db_utils::fieldsMemory($rsSqlArrebanco, 0)->k00_numbco;
                }else{
                    throw new Exception("[ 99 ] - Erro ao buscar dados do banco!\nErro: Numpre do recibo não encontrado na tabela arrebanco");
                }

                $sSqlRecibopaga  = "select coalesce(sum(k00_valor),0) as n_valor_recibo     ";
                $sSqlRecibopaga .= " from recibopaga as r                                   ";
                $sSqlRecibopaga .= "where r.k00_numnov ={$oRegistro->k00_numnov} and r.k00_hist not in (11403, 11404)";
                $rsSqlRecibopaga = db_query($sSqlRecibopaga);
                if ( $rsSqlRecibopaga && pg_num_rows($rsSqlRecibopaga) > 0 ){
                    $oRegistro->valor_recibo = db_utils::fieldsMemory($rsSqlRecibopaga,0)->n_valor_recibo;
                }else{
                    throw new Exception("[ 98 ] - Erro ao buscar valor do recibo!\nErro: ");
                }

                //Busca por taxas especificas da instituição

                $sSqlCustasPref      = "select coalesce(sum(k00_valor),0) as soma";
                $sSqlCustasPref     .= "  from recibopaga ";
                $sSqlCustasPref     .= "  INNER JOIN taxa ON taxa.ar36_receita = recibopaga.k00_receit ";
                $sSqlCustasPref     .= "  inner join favorecidotaxa on favorecidotaxa.v87_taxa = taxa.ar36_sequencial ";
                $sSqlCustasPref     .= "  inner join favorecido on favorecido.v86_sequencial = favorecidotaxa.v87_favorecido ";
                $sSqlCustasPref     .= "  inner join cgm as cgmfavorecido on cgmfavorecido.z01_numcgm = favorecido.v86_numcgm ";
                $sSqlCustasPref     .= " where recibopaga.k00_numnov = {$oRegistro->k00_numnov} and cgmfavorecido.z01_cgccpf = ( select cgc from db_config where prefeitura is true and codigo = ".db_getsession("DB_instit").")";
                $rsSqlCustasPref     = db_query($sSqlCustasPref);

                $oRegistro->valor_total_taxas_pref = 0;
                if ( $rsSqlCustasPref && pg_num_rows($rsSqlCustasPref) > 0 ) {
                    $oRegistro->valor_total_taxas_pref = db_utils::fieldsMemory($rsSqlCustasPref, 0)->soma;
                }else{
                    throw new Exception("[ 98.aseda5 ] - Erro ao buscar valor da Tsdafasdfsdfaxa Bancária\nErro: ".pg_last_error());
                }

                $oRegistro->valor_total_taxas = 0;
                $sSqlCustas  = "select coalesce(sum(k00_valor),0) as n_valor_recibo     ";
                $sSqlCustas .= " from recibopaga as r                                   ";
                $sSqlCustas .= "where r.k00_numnov ={$oRegistro->k00_numnov}  and r.k00_hist in (11403, 11404)";
                $rsSqlCustas     = db_query($sSqlCustas);
                if ( $rsSqlCustas && pg_num_rows($rsSqlCustas) > 0 ){
                    $oRegistro->valor_total_taxas = db_utils::fieldsMemory($rsSqlCustas, 0)->n_valor_recibo;
                }else {
                    throw new Exception("[ 98.5 ] - Erro ao buscar valor da Total das taxas)");
                }

                //Dados do Recibo

                $sSqlValorTaxaBancaria = "select k00_txban
                                    from recibopaga
                                         inner join arrecad   on recibopaga.k00_numpre = arrecad.k00_numpre
                                                             and recibopaga.k00_numpar = arrecad.k00_numpar
                                         inner join arretipo  on arrecad.k00_tipo      = arretipo.k00_tipo
                                   where recibopaga.k00_numnov = {$oRegistro->k00_numnov}
                                group by arretipo.k00_tipo;";
                $rsValorTaxa           = db_query($sSqlValorTaxaBancaria);


                if ( !$rsValorTaxa ) {
                    throw new Exception("[ 98.5 ] - Erro ao buscar valor da Taxa Bancária\nErro: ".pg_last_error());
                }

                $nValorTaxaEmissao = 0;
                if ( pg_num_rows($rsValorTaxa) >= 0 ) {
                    $nValorTaxaEmissao = db_utils::fieldsMemory($rsValorTaxa,0)->k00_txban;
                }



                $oDados->k00_numpre               = $oRegistro->k00_numpre;
                $oDados->k00_numnov               = $oRegistro->k00_numnov;
                $oDados->k00_dtvenc               = $oRegistro->k00_dtvenc;
                $oDados->k00_dtoper               = $oRegistro->k00_dtoper;
                $oDados->k00_dtpaga               = $oRegistro->k00_dtpaga;
                $oDados->v77_processoforopartilha = '';
                $oDados->v51_inicial              = $oRegistro->v51_inicial;
                $oDados->v51_certidao             = $oRegistro->v51_certidao;
                $oDados->v70_data                 = $oRegistro->v70_data;
                $oDados->numbanco                 = $oRegistro->numbanco;
                $oDados->valor_recibo             = $oRegistro->valor_recibo + $nValorTaxaEmissao;
                $oDados->valor_total_taxa         = $oRegistro->valor_total_taxas;
                $oDados->valor_total_recibo       = $oDados->valor_recibo + $oRegistro->valor_total_taxas;
                $oDados->valor_taxa_pref          = $oRegistro->valor_total_taxas_pref;

                //Endereço do sacado por cgm
                $oDados->z01_numcgm               = $oRegistro->z01_numcgm;
                $oDados->z01_nome                 = $oRegistro->z01_nome;
                $oDados->z01_ender                = $oRegistro->z01_ender.",".$oRegistro->z01_numero;
                $oDados->z01_cep                  = $oRegistro->z01_cep;
                $oDados->z01_cgccpf               = $oRegistro->z01_cgccpf;
                $oDados->z01_munic                = $oRegistro->z01_munic;
                $oDados->z01_uf                   = $oRegistro->z01_uf;

                /*
                 *
                 * Se o tipo de geração for de arquivos de remessa ao Banco
                 * Buscamos o endereço do sacado de acordo com a Origem do Numpre.
                 *
                 * Por Default essa informação já é preenchida com os dados do CGM do recibo
                 * Verificamos se o numpre possui matricula ou inscrição e utilizamos as informações de acordo essa vinculação
                 *
                 */

                if ( $iTipoArq == 1 ) {

                    $sSqlOrigemNumpre  = " select fc_origem_numpre as retorno                ";
                    $sSqlOrigemNumpre .= "   from fc_origem_numpre({$oRegistro->k00_numpre},1,'') ";
                    $rsOrigemNumpre    = db_query($sSqlOrigemNumpre);
                    $sOrigemNumpre     = db_utils::fieldsMemory($rsOrigemNumpre,0)->retorno;
                    $aOrigemNumpre     = [];
                    if (count($sOrigemNumpre) > 0) {

                        $aOpcoes = ['M', 'I'];
                        foreach ($aOpcoes as $sOpcao) {
                            preg_match("/$sOpcao:\s*([^I|M|C]*)/", $sOrigemNumpre, $aMatches);
                            if (!empty($aMatches) && !empty($aMatches[1])) {
                                $aOrigemNumpre[$sOpcao] = trim($aMatches[1]);
                            }
                        }

                        if (isset($aOrigemNumpre['M'])) {

                            $iMatric = $aOrigemNumpre['M'];

                            $sSqlProprietario  = " select z01_numcgm,                 ";
                            $sSqlProprietario .= "        z01_nome,                   ";
                            $sSqlProprietario .= "        z01_ender,                  ";
                            $sSqlProprietario .= "        z01_numero,                 ";
                            $sSqlProprietario .= "        z01_cep,                    ";
                            $sSqlProprietario .= "        z01_munic,                  ";
                            $sSqlProprietario .= "        z01_uf,                      ";
                            $sSqlProprietario .= "        z01_cgccpf                  ";
                            $sSqlProprietario .= "   from proprietario                ";
                            $sSqlProprietario .= "  where j01_matric = {$iMatric}  ";
                            $rsProprietario    = db_query($sSqlProprietario);
                            if (pg_num_rows($rsProprietario) > 0) {
                                $oDadosProprietario = db_utils::fieldsMemory($rsProprietario,0);

                                $oDados->z01_numcgm = $oDadosProprietario->z01_numcgm;
                                $oDados->z01_nome   = $oDadosProprietario->z01_nome;
                                $oDados->z01_ender  = $oDadosProprietario->z01_ender.",".$oDadosProprietario->z01_numero;
                                $oDados->z01_cep    = $oDadosProprietario->z01_cep;
                                $oDados->z01_munic  = $oDadosProprietario->z01_munic;
                                $oDados->z01_uf     = $oDadosProprietario->z01_uf;
                                $oDados->z01_cgccpf = $oDadosProprietario->z01_cgccpf;


                            }

                        }

                        if (isset($aOrigemNumpre['I']) && !isset($aOrigemNumpre['M'])) {

                            $iInscr = $aOrigemNumpre['I'];

                            $sSqlEmpresa  = " select q02_numcgm as z01_numcgm,   ";
                            $sSqlEmpresa .= "        z01_nome,                   ";
                            $sSqlEmpresa .= "        z01_ender,                  ";
                            $sSqlEmpresa .= "        z01_numero,                 ";
                            $sSqlEmpresa .= "        z01_cep,                    ";
                            $sSqlEmpresa .= "        z01_munic,                  ";
                            $sSqlEmpresa .= "        z01_uf,                      ";
                            $sSqlEmpresa .= "        z01_cgccpf                  ";
                            $sSqlEmpresa .= "  from empresa                      ";
                            $sSqlEmpresa .= " where q02_inscr = {$iInscr}        ";
                            $rsEmpresa    = db_query($sSqlEmpresa);
                            if (pg_num_rows($rsEmpresa) > 0) {
                                $oDadosEmpresa = db_utils::fieldsMemory($rsEmpresa,0);

                                $oDados->z01_numcgm = $oDadosEmpresa->z01_numcgm;
                                $oDados->z01_nome   = $oDadosEmpresa->z01_nome;
                                $oDados->z01_ender  = $oDadosEmpresa->z01_ender.",".$oDadosEmpresa->z01_numero;
                                $oDados->z01_cep    = $oDadosEmpresa->z01_cep;
                                $oDados->z01_munic  = $oDadosEmpresa->z01_munic;
                                $oDados->z01_uf     = $oDadosEmpresa->z01_uf;
                                $oDados->z01_cgccpf = $oDadosEmpresa->z01_cgccpf;

                            }

                        }

                    }

                }

                /*
                 *
                 * Se o tipo de geração for de arquivos de remessa ao TJ
                 * Buscamos os numeros dos processos do foro antigo e atual
                 *
                 * A lógica é a seguinte:
                 * 1 - Numero do processo do foro Atualizado (NOVO):
                 *     - Se o campo v85_codforo da tabela processoforocodforoant for maior ou igual a 0(zero)
                 *         v85_codforo = 0 : Processo do foro cadastrado novo (Já com o numero no novo formato)
                 *         v85_codforo > 0 : Processo do foro alterado (Em tese, alterado para o novo numero com o novo formato)
                 * 2 - Numero do processo do foro Desatualizado (ANTIGO):
                 *     - Se o campo v85_codforo da tabela processoforocodforoant estiver nulo, significa que o processo é
                 *     antigo e não teve seu numero alterado.
                 *
                 */
                if ($iTipoArq == 2) {

                    $sSqlProcesso  = "select case                                                                 ";
                    $sSqlProcesso .= "         when v85_codforo > 0                                               ";
                    $sSqlProcesso .= "           then v85_codforo                                                 ";
                    $sSqlProcesso .= "         else                                                               ";
                    $sSqlProcesso .= "           case                                                             ";
                    $sSqlProcesso .= "             when v85_codforo is null                                       ";
                    $sSqlProcesso .= "               then v70_codforo                                             ";
                    $sSqlProcesso .= "             else ''                                                        ";
                    $sSqlProcesso .= "           end                                                              ";
                    $sSqlProcesso .= "       end as codforo_antigo,                                               ";
                    $sSqlProcesso .= "       case                                                                 ";
                    $sSqlProcesso .= "         when v85_codforo >= 0                                              ";
                    $sSqlProcesso .= "           then v70_codforo                                                 ";
                    $sSqlProcesso .= "         else ''                                                            ";
                    $sSqlProcesso .= "       end as codforo_novo                                                  ";
                    $sSqlProcesso .= "  from processoforo                                                         ";
                    $sSqlProcesso .= "  left join processoforocodforoant on v70_sequencial = v85_processoforo     ";
                    $sSqlProcesso .= " where v70_sequencial = {$oRegistro->v70_sequencial}                        ";
                    $rsProcesso = db_query($sSqlProcesso) ;
                    $aProcesso = db_utils::fieldsmemory($rsProcesso,0);
                    $oDados->codforo_antigo            = $aProcesso->codforo_antigo;
                    $oDados->codforo_novo              = $aProcesso->codforo_novo;

                    /*
                     * Quando o campo v70_valorinicial for nulo, buscamos o valor dessa inicial dando um "sum()" no campo k00_valor
                     * da tabela arreforo das certidões que compõe essa inicial
                     */
                    $oDados->v70_valorinicial         = $oRegistro->v70_valorinicial;
                    if ( empty($oRegistro->v70_valorinicial)) {

                        $sSqlArreforo    = " select sum(k00_valor) as valor_inicial                                     ";
                        $sSqlArreforo   .= "   from arreforo                                                            ";
                        $sSqlArreforo   .= " inner join inicialcert on inicialcert.v51_certidao = arreforo.k00_certidao ";
                        $sSqlArreforo   .= " where v51_inicial = {$oRegistro->v51_inicial}                              ";
                        $rsValorArreforo = db_query($sSqlArreforo);
                        $oDados->v70_valorinicial = db_utils::fieldsMemory($rsValorArreforo,0)->valor_inicial;

                    }

                }

                //Dados dos Favorecidos
                /*
                 * Separamos as informações dos favorecidos através do seu CPF/CNPJ
                 * Serão 5 favorecidos:
                 * 1 - TJ         - 28538734000148
                 * 2 - FUNPERJ    - 08778206000159
                 * 3 - CAARJ....  - 33755174000113
                 * 4 - FUNDPERJ   - 31443526000170
                 * 5 - INSTITUICAO - Dados do cadastro da instituição
                 * 6 - FUNARPEN RJ  - 34552805000160
                 */

                switch (trim($oRegistro->favorecido_cgccpf)) {

                    //TJ
                    case "28538734000148":

                        $oDados->taxa_TJ            = $oRegistro->taxa_codigo;
                        $oDados->numcgm_TJ          = $oRegistro->favorecido_numcgm;
                        $oDados->containterna_TJ    = $oRegistro->favorecido_containterna;
                        $oDados->nome_TJ            = $oRegistro->favorecido_nome;
                        $oDados->cgccpf_TJ          = $oRegistro->favorecido_cgccpf;
                        $oDados->banco_TJ           = $oRegistro->favorecido_banco;
                        $oDados->codagencia_TJ      = substr($oRegistro->favorecido_agencia,-4);
                        $oDados->dvagencia_TJ       = $oRegistro->favorecido_agenciadv;
                        $oDados->conta_TJ           = $oRegistro->favorecido_conta;
                        $oDados->dvconta_TJ         = $oRegistro->favorecido_contadv;
                        $oDados->codigo_receita_TJ  = $oRegistro->taxa_receita;
                        $oDados->valor_TJ          += $oRegistro->taxa_valor;

                        break;

                    //FUNPERJ
                    case "08778206000159":

                        $oDados->taxa_FUNPERJ             = $oRegistro->taxa_codigo;
                        $oDados->numcgm_FUNPERJ           = $oRegistro->favorecido_numcgm;
                        $oDados->containterna_FUNPERJ     = $oRegistro->favorecido_containterna;
                        $oDados->nome_FUNPERJ             = $oRegistro->favorecido_nome;
                        $oDados->cgccpf_FUNPERJ           = $oRegistro->favorecido_cgccpf;
                        $oDados->banco_FUNPERJ            = $oRegistro->favorecido_banco;
                        $oDados->codagencia_FUNPERJ       = substr($oRegistro->favorecido_agencia,-4);
                        $oDados->dvagencia_FUNPERJ        = $oRegistro->favorecido_agenciadv;
                        $oDados->conta_FUNPERJ            = $oRegistro->favorecido_conta;
                        $oDados->dvconta_FUNPERJ          = $oRegistro->favorecido_contadv;
                        $oDados->codigo_receita_FUNPERJ   = $oRegistro->taxa_receita;
                        $oDados->valor_FUNPERJ           += $oRegistro->taxa_valor;

                        break;

                    //CAARJ
                    case  "33755174000113":

                        $oDados->taxa_CAARJ               = $oRegistro->taxa_codigo;
                        $oDados->numcgm_CAARJ             = $oRegistro->favorecido_numcgm;
                        $oDados->containterna_CAARJ       = $oRegistro->favorecido_containterna;
                        $oDados->nome_CAARJ               = $oRegistro->favorecido_nome;
                        $oDados->cgccpf_CAARJ             = $oRegistro->favorecido_cgccpf;
                        $oDados->banco_CAARJ              = $oRegistro->favorecido_banco;
                        $oDados->codagencia_CAARJ         = substr($oRegistro->favorecido_agencia,-4);
                        $oDados->dvagencia_CAARJ          = $oRegistro->favorecido_agenciadv;
                        $oDados->conta_CAARJ              = $oRegistro->favorecido_conta;
                        $oDados->dvconta_CAARJ            = $oRegistro->favorecido_contadv;
                        $oDados->codigo_receita_CAARJ     = $oRegistro->taxa_receita;
                        $oDados->valor_CAARJ             += $oRegistro->taxa_valor;

                        break;


                    //FUNDPERJ
                    case "31443526000170":

                        $oDados->taxa_FUNDPERJ            = $oRegistro->taxa_codigo;
                        $oDados->numcgm_FUNDPERJ          = $oRegistro->favorecido_numcgm;
                        $oDados->containterna_FUNDPERJ    = $oRegistro->favorecido_containterna;
                        $oDados->nome_FUNDPERJ            = $oRegistro->favorecido_nome;
                        $oDados->cgccpf_FUNDPERJ          = $oRegistro->favorecido_cgccpf;
                        $oDados->banco_FUNDPERJ           = $oRegistro->favorecido_banco;
                        $oDados->codagencia_FUNDPERJ      = substr($oRegistro->favorecido_agencia,-4);
                        $oDados->dvagencia_FUNDPERJ       = $oRegistro->favorecido_agenciadv;
                        $oDados->conta_FUNDPERJ           = $oRegistro->favorecido_conta;
                        $oDados->dvconta_FUNDPERJ         = $oRegistro->favorecido_contadv;
                        $oDados->codigo_receita_FUNDPERJ  = $oRegistro->taxa_receita;
                        $oDados->valor_FUNDPERJ          += $oRegistro->taxa_valor;

                        break;

                    //FUNARPEN
                    case "34552805000160":

                        $oDados->taxa_FUNARPEN            = $oRegistro->taxa_codigo;
                        $oDados->numcgm_FUNARPEN          = $oRegistro->favorecido_numcgm;
                        $oDados->containterna_FUNARPEN    = $oRegistro->favorecido_containterna;
                        $oDados->nome_FUNARPEN            = $oRegistro->favorecido_nome;
                        $oDados->cgccpf_FUNARPEN          = $oRegistro->favorecido_cgccpf;
                        $oDados->banco_FUNARPEN           = $oRegistro->favorecido_banco;
                        $oDados->codagencia_FUNARPEN      = substr($oRegistro->favorecido_agencia,-4);
                        $oDados->dvagencia_FUNARPEN       = $oRegistro->favorecido_agenciadv;
                        $oDados->conta_FUNARPEN           = $oRegistro->favorecido_conta;
                        $oDados->dvconta_FUNARPEN         = $oRegistro->favorecido_contadv;
                        $oDados->codigo_receita_FUNARPEN  = $oRegistro->taxa_receita;
                        $oDados->valor_FUNARPEN          += $oRegistro->taxa_valor;

                        break;

                }

                /*
                 * Montamos as informações referentes a instituição
                 */
                $oDados->taxa_INSTITUICAO            = "";
                $oDados->numcgm_INSTITUICAO          = $oDadosInstituicao->v86_numcgm;
                $oDados->containterna_INSTITUICAO    = $oDadosInstituicao->v86_containterna;
                $oDados->nome_INSTITUICAO            = $oDadosInstituicao->z01_nome;
                $oDados->cgccpf_INSTITUICAO          = $oDadosInstituicao->z01_cgccpf;
                $oDados->banco_INSTITUICAO           = $oDadosInstituicao->db90_codban;
                $oDados->codagencia_INSTITUICAO      = substr($oDadosInstituicao->db89_codagencia,-4);
                $oDados->dvagencia_INSTITUICAO       = $oDadosInstituicao->db89_digito;
                $oDados->conta_INSTITUICAO           = $oDadosInstituicao->db83_conta;
                $oDados->dvconta_INSTITUICAO         = $oDadosInstituicao->db83_dvconta;
                $oDados->codigo_receita_INSTITUICAO  = "";
                $valorInstituicao = $oDados->valor_total_recibo - $oDados->valor_TJ - $oDados->valor_FUNPERJ - $oDados->valor_CAARJ - $oDados->valor_FUNDPERJ - $oDados->valor_FUNARPEN;
                $oDados->valor_INSTITUICAO           = $valorInstituicao;

                if (!in_array($oRegistro->v77_sequencial,$aPatrilhaCustas)) {
                    $aPatrilhaCustas[] = $oRegistro->v77_sequencial;
                    $oDados->PartilhaCustas = $aPatrilhaCustas;
                }
                $aRegistrosAgrupados[$oDados->k00_numnov] = $oDados;
                if (!in_array($oRegistro->v77_sequencial,$aPatrilhaCustas)) {
                    $aPatrilhaCustas[] = $oRegistro->v77_sequencial;
                    $oDados->PartilhaCustas = $aPatrilhaCustas;
                }

                $aRegistros[$oRegistro->k00_numnov] = $oDados;
            }

        }
        //dump(  $aRegistros);
        return $aRegistrosAgrupados;
    }

    /**
     *
     * Método chamado para setar o código das taxas específicas para a geração do arquivo do TJ
     *
     * O código dessas taxas é informado no valor default dos campos que utilizarao essas informações.
     * Layout 101
     * Código dos campos: 5900,5902,5904,5906,5909
     *
     */
    function setIdTaxasEspecificasTJ() {

        try {

            $oDBLayoutCampos      = db_utils::getDao("db_layoutcampos");
            $rsAtosDistribuidores = $oDBLayoutCampos->sql_record($oDBLayoutCampos->sql_query_file(5906,"db52_default"));
            if ($oDBLayoutCampos->numrows == 0 ) {
                throw new Exception("Nenhum registro da Linha 5906 encontrado no Layout 101!");
            }

            $rsTaxaJudiciaria  = $oDBLayoutCampos->sql_record($oDBLayoutCampos->sql_query_file(5904,"db52_default"));
            if ($oDBLayoutCampos->numrows == 0 ) {
                throw new Exception("Nenhum registro da Linha 5904 encontrado no Layout 101!");
            }

            $rsAtosEscrivaesDividaAtiva = $oDBLayoutCampos->sql_record($oDBLayoutCampos->sql_query_file(5902,"db52_default"));
            if ($oDBLayoutCampos->numrows == 0 ) {
                throw new Exception("Nenhum registro da Linha 5902 encontrado no Layout 101!");
            }

            $rsAcrescimo = $oDBLayoutCampos->sql_record($oDBLayoutCampos->sql_query_file(5909,"db52_default"));
            if ($oDBLayoutCampos->numrows == 0 ) {
                throw new Exception("Nenhum registro da Linha 5909 encontrado no Layout 101!");
            }

            $rsAtosOficiaisJusticaAvaliadores = $oDBLayoutCampos->sql_record($oDBLayoutCampos->sql_query_file(5900,"db52_default"));
            if ($oDBLayoutCampos->numrows == 0 ) {
                throw new Exception("Nenhum registro da Linha 5900 encontrado no Layout 101!");
            }

            $this->iIdAtosDistribuidores = db_utils::fieldsMemory($rsAtosDistribuidores,0)->db52_default;
            if (empty($this->iIdAtosDistribuidores)){
                throw new Exception("ERRO: Valor Default do campo codigo_receita_atos_distribuidores não configurado no Layout 101 !\n\n".
                    "O valor Default para este campo deve ser o código da taxa referenciada pelo campo");
            }

            $this->iIdTaxaJudiciaria = db_utils::fieldsMemory($rsTaxaJudiciaria,0)->db52_default;
            if (empty($this->iIdTaxaJudiciaria)){
                throw new Exception("ERRO: Valor Default do campo codigo_receita_taxa_judiciaria não configurado no Layout 101 !\n\n".
                    "O valor Default para este campo deve ser o código da taxa referenciada pelo campo");
            }

            $this->iIdAtosEscrivaesDividaAtiva = db_utils::fieldsMemory($rsAtosEscrivaesDividaAtiva,0)->db52_default;
            if (empty($this->iIdAtosEscrivaesDividaAtiva)){
                throw new Exception("ERRO: Valor Default do campo codigo_receita_atos_escrivaes_divida_ativa não configurado no Layout 101 !\n\n".
                    "O valor Default para este campo deve ser o código da taxa referenciada pelo campo");
            }

            $this->iIdAcrescimo = db_utils::fieldsMemory($rsAcrescimo,0)->db52_default;
            if (empty($this->iIdAcrescimo)){
                throw new Exception("ERRO: Valor Default do campo conta_corrente_acrescimo_20 não configurado no Layout 101 !\n\n".
                    "O valor Default para este campo deve ser o código da taxa referenciada pelo campo");
            }

            $this->iIdAtosOficiaisJusticaAvaliadores = db_utils::fieldsMemory($rsAtosOficiaisJusticaAvaliadores,0)->db52_default;
            if (empty($this->iIdAtosOficiaisJusticaAvaliadores)){
                throw new Exception("ERRO: Valor Default do campo codigo_receita_atos_oficiais_justica_avaliadores não configurado no Layout 101 !\n\n".
                    "O valor Default para este campo deve ser o código da taxa referenciada pelo campo");
            }

        } catch (Exception $eException) {
            throw new Exception($eException->getMessage());
        }

    }

}
