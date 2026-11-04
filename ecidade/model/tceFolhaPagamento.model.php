<?php
/**
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

require_once(modification('model/tceEstruturaBasica.php'));

class tceFolhaPagamento extends tceEstruturaBasica
{
    const  NOME_ARQUIVO = 'TCE_4810.TXT';

    public $iInstit = "";
    public $sInstituicoes = "";
    public $sDataIni = "";
    public $sDataFim = "";
    public $sCodRemessa = "";
    public $iDiaPagamento = "";

    private $oLeiaute = null;

    function __construct($iInstit, $sCodRemessa, $sDataIni, $sDataFim, $oData, $oLeiaute = null, $sInstituicoes, $iCodigoArquivo = 31)
    {
        try {
            parent::__construct($iCodigoArquivo, self::NOME_ARQUIVO);
        } catch (Exception $e) {
            //throw $e->getMessage();
        }

        $this->oLeiaute = $oLeiaute;
        $this->iInstit = $iInstit;
        $this->sInstituicoes = $sInstituicoes;
        $this->sDataIni = $sDataIni;
        $this->sDataFim = $sDataFim;
        $this->sCodRemessa = $sCodRemessa;
        $this->iDiaPagamento = $oData->diapagfolha;

        if ($oLeiaute != null) {
            $this->oLeiaute = $oLeiaute;
        }
    }

    function getNomeArquivo()
    {
        return self::NOME_ARQUIVO;
    }

    /**
     * @throws Exception
     */
    function geraArquivo()
    {
        db_criatermometro('terTCE4810', 'Arquivo TCE4810...', 'blue', 1);

        $this->oTxtLayout->setByLineOfDBUtils($this->cabecalhoPadrao($this->iInstit,
          $this->sDataIni,
          $this->sDataFim,
          $this->sCodRemessa), 1);

        $sSqlFolhaPagamento = $this->sqlFolhaPagamento($this->sInstituicoes,
          $this->sDataIni,
          $this->sDataFim,
          $this->iDiaPagamento);

        $rsFolhaPagamento = db_query($sSqlFolhaPagamento);
        $iNumRows = pg_num_rows($rsFolhaPagamento);
        $iTotalRegistros = 0;

        if ($this->oLeiaute) {
            /**
             * Setando as propriedades do campo a ser inserido no leiaute
             */
            $this->oLeiaute->setNomeArquivo($this->getNomeArquivo());
            $this->oLeiaute->setNomeArqTce($this->getNomeArquivo());
            $this->oLeiaute->setNomeCampo("RUBRICA");
            $this->oLeiaute->setNumCasasDecimais(0);
            $this->oLeiaute->setVersaoLeiaute("1");
            $this->oLeiaute->setObs("CODIGO DA VANTAGEM DESCONTO TOTALIZADOR");
            $this->oLeiaute->setTamanho(4);
            $this->oLeiaute->setTipo("C");
            /**
             * Metodo que adiciona uma linha no leiaute,
             * com base nas propriedades setadas
             */
            $this->oLeiaute->addLinha();
        }

        $dataCompetencia = explode("-", $this->sDataIni);
        $anoCompetencia = $dataCompetencia[0];
        $mesCompetencia = $dataCompetencia[1];

        $dataFimCompetencia = explode("-", $this->sDataFim);
        $mesFimCompetencia = $dataFimCompetencia[1];

        $daoVerificaDuplicidade = new cl_padpagamentoposterior();
        $instituicao = $this->iInstit;
        $where = "rh237_instituicao in ({$this->sInstituicoes}) and rh237_ano = {$anoCompetencia} and rh237_mes between {$mesCompetencia} and {$mesFimCompetencia} ";
        $daoVerificaDuplicidade->excluir(null, $where);

        $iQuant = 0;
        $contadorIdentificadores = array();
        $chaveControleDuplicidade = array();

        for ($i = 0; $i < $iNumRows; $i++) {
            $iNew = intval($i * 100 / $iNumRows);

            if ($iNew > $iQuant) {
                $iQuant = $iNew;
                db_atutermometro($i, $iNumRows, "terTCE4810");
            }

            $oFolhaPagamento = db_utils::fieldsMemory($rsFolhaPagamento, $i);

            if (empty($oFolhaPagamento->codigo_recurso)) {
                $mensagem = "Recurso não configurado para a lotação vinculada a matrícula {$oFolhaPagamento->codigoregistrofuncionario}.";
                throw new Exception($mensagem);
            }

            if (empty($oFolhaPagamento->codigobancodepositofolhapagentidad)
              || empty($oFolhaPagamento->codigoagencdepositofolhapagentidad)
              || empty($oFolhaPagamento->codcontacorrbancodepfolhapagent)
            ) {
                $instituicao = InstituicaoRepository::getInstituicaoByCodigo($oFolhaPagamento->r14_instit);
                $mensagem = "Recurso {$oFolhaPagamento->codigo_recurso} - {$oFolhaPagamento->descricao_recurso} ";
                $mensagem .= "sem conta configurada para a instituição {$instituicao->getCodigo()} - {$instituicao->getDescricao()}. ";
                $mensagem .= "Para configurá-la, acesse:";
                $mensagem .= "\nDB:RECURSOSHUMANOS > Pessoal > Cadastros > Contas por Recurso > Inclusão";

                throw new Exception($mensagem);
            }

            if (!array_key_exists($oFolhaPagamento->codigotipofolha, $contadorIdentificadores)) {
                $contadorIdentificadores[$oFolhaPagamento->codigotipofolha] = str_pad($oFolhaPagamento->codigotipofolha, 4, '0', STR_PAD_LEFT);
            }

            $oFolhaPagamento->identificadorfolhapagamento .= $contadorIdentificadores[$oFolhaPagamento->codigotipofolha];

            $chave = "{$oFolhaPagamento->identificadorfolhapagamento}#{$oFolhaPagamento->codigoregistrofuncionario}#{$oFolhaPagamento->r14_instit}";
            $chave .= "#{$oFolhaPagamento->r14_anousu}#{$oFolhaPagamento->r14_mesusu}#{$oFolhaPagamento->r14_rubric}";

            if (array_key_exists($chave, $chaveControleDuplicidade)) {
                continue;
            }

            $chaveControleDuplicidade[$chave] = $chave;

            if ($oFolhaPagamento->pagamentoaposvigencia == 'S' ) {
                if ($oFolhaPagamento->identificacaooperacao == 'V' || $oFolhaPagamento->identificacaooperacao == 'D') {
                    if (strlen($oFolhaPagamento->codigoagencdepositofolhapagentidad) > 5) {
                        $mensagem = "Erro ao gerar o arquivo. Tamanho do número da Agência + Dígito Verificador";
                        $mensagem .= " referente a conta vinculada ao recurso {$oFolhaPagamento->codigo_recurso} - {$oFolhaPagamento->descricao_recurso} ";
                        $mensagem .= "(DB:RECURSOSHUMANOS > Pessoal > Cadastros > Contas por Recurso), ";
                        $mensagem .= " é superior a 5 dígitos. Verifique o cadastro da conta";
                        $mensagem .= " (DB:CONFIGURAÇÃO > Configuração > Cadastros > Cadastro de Agências).";

                        throw new Exception($mensagem);
                    }

                    $daoPadPagamentoPosterior = new cl_padpagamentoposterior();
                    $daoPadPagamentoPosterior->rh237_identificador = "{$oFolhaPagamento->identificadorfolhapagamento}";
                    $daoPadPagamentoPosterior->rh237_identificacaovalor = "{$oFolhaPagamento->identificacaooperacao}";
                    $daoPadPagamentoPosterior->rh237_matricula = $oFolhaPagamento->codigoregistrofuncionario;
                    $daoPadPagamentoPosterior->rh237_instituicao = $oFolhaPagamento->r14_instit;
                    $daoPadPagamentoPosterior->rh237_ano = $oFolhaPagamento->r14_anousu;
                    $daoPadPagamentoPosterior->rh237_mes = $oFolhaPagamento->r14_mesusu;
                    $daoPadPagamentoPosterior->rh237_tipofolha = $oFolhaPagamento->codigotipofolha;

                    $daoPadPagamentoPosterior->rh237_valor = $oFolhaPagamento->valorvantagemdescontototalizador;
                    $daoPadPagamentoPosterior->rh237_identificacaovalor = $oFolhaPagamento->identificacaooperacao;
                    $daoPadPagamentoPosterior->rh237_banco = $oFolhaPagamento->codigobancodepositofolhapagentidad;
                    $daoPadPagamentoPosterior->rh237_agencia = $oFolhaPagamento->codigoagencdepositofolhapagentidad;
                    $daoPadPagamentoPosterior->rh237_contacorrente = $oFolhaPagamento->codcontacorrbancodepfolhapagent;
                    $daoPadPagamentoPosterior->rh237_datapagamento = $oFolhaPagamento->rh225_datapagamento;

                    $daoPadPagamentoPosterior->incluir(null);

                    if ($daoPadPagamentoPosterior->erro_status == '0') {
                        throw new Exception('Erro ao salvar os registros para geração do arquivo de Pagamento Após a Competência.');
                    }
                }

                $oFolhaPagamento->datapagamentofolha = '0000-00-00';
            }

            // <!-- PADRS Arquivo 4810 -->

            $this->oTxtLayout->setByLineOfDBUtils($oFolhaPagamento, 3);
            $iTotalRegistros++;
        }

        $this->oTxtLayout->setByLineOfDBUtils($this->rodapePadrao($iTotalRegistros), 5);
        unset($rsFolhaPagamento);
    }

    function sqlFolhaPagamento($iInstit, $sDataini, $sDatafim, $iDiaPagamento)
    {
        list ($iAnoUsuFim, $iMesUsuFim, $iDiaUsuFim) = explode("-", $sDatafim);
        list ($iAnoUsuIni, $iMesUsuIni, $iDiaUsuIni) = explode("-", $sDataini);

        // Estrutura da versao nova do PADRS

        $sqlcampos12 = <<<SQL
            'X' as indicadorincidenciasaude,
            case when rhrubricas.rh27_pd = 3 then 'X' when eso26_codinccp in ('11', '12') then 'S' else 'N' end as indicadorincidenciainss,
            case when rhrubricas.rh27_pd = 3 then 'X' when eso26_codinccprp in ('11', '12') then 'S' else 'N' end as indicadorincidenciarpps,
SQL;
        $sql12 = <<<SQL
        inner join esocial.esocialrubricas on esocialrubricas.eso26_rubrica = rhrubricas.rh27_rubric
            and esocialrubricas.eso26_instituicao = rhrubricas.rh27_instit
SQL;

        $sSqlFolhaPagamento  = " select qry.*, rh225_datapagamento, ";
        $sSqlFolhaPagamento .= " case ";
        $sSqlFolhaPagamento .= " WHEN rh225_datapagamento BETWEEN (qry.r14_anousu::text || '-' ||  LPAD(qry.r14_mesusu::text, 2, '0') || '-' || '01' )::date  ";
        $sSqlFolhaPagamento .= " AND (date_trunc('MONTH', ( qry.r14_anousu::text ||  LPAD(qry.r14_mesusu::text, 2, '0') ||'01')::date) + INTERVAL '1 MONTH - 1 day')::DATE ";
        $sSqlFolhaPagamento .= " then 'N' ";
        $sSqlFolhaPagamento .= " else 'S' ";
        $sSqlFolhaPagamento .= " END AS pagamentoaposvigencia, ";
        $sSqlFolhaPagamento .= " qry.r14_anousu::text || LPAD(qry.r14_mesusu::text, 2, '0') || LPAD({$this->iInstit}, 2, '0') AS identificadorfolhapagamento ";
        $sSqlFolhaPagamento .= " from ( ";

        $sSqlFolhaPagamento .= " select 1 as codigotipofolha, ";
        $sSqlFolhaPagamento .= "        gerfsal.r14_anousu, ";
        $sSqlFolhaPagamento .= "        gerfsal.r14_mesusu, ";
        $sSqlFolhaPagamento .= "        gerfsal.r14_rubric, ";
        $sSqlFolhaPagamento .= "        gerfsal.r14_instit, ";
        $sSqlFolhaPagamento .= "        rhrubricas.rh27_pd, ";
        $sSqlFolhaPagamento .= "        r14_quant as quantidade_rubrica, ";
        $sSqlFolhaPagamento .= "        r14_regist as codigoregistrofuncionario, ";
        $sSqlFolhaPagamento .= "        r14_regist || '00' as matriculafuncionario, ";
        $sSqlFolhaPagamento .= "        (cast(r14_anousu::varchar||'-'||r14_mesusu::varchar||'-'||(select fc_ultimodiames(r14_anousu,r14_mesusu))::varchar as date)) as datacompetenciafolha, ";
        $sSqlFolhaPagamento .= "        (cast(r14_anousu::varchar||'-'||r14_mesusu::varchar||'-'||(select fc_ultimodiames(r14_anousu,r14_mesusu))::varchar as date)) as datapagamentofolha, ";
        $sSqlFolhaPagamento .= "        '000'      as codigovantagemdescontototalizador, ";
        $sSqlFolhaPagamento .= "        null      as percentualdescontoirpf, ";
        $sSqlFolhaPagamento .= "        null      as percentualdescontoprevidencia, ";
        $sSqlFolhaPagamento .= "        null      as percentualdescontosaude, ";
        $sSqlFolhaPagamento .= "        null      as indicadordesconto, ";
        $sSqlFolhaPagamento .= "        null      as indicadordescontosaude, ";
        $sSqlFolhaPagamento .= "        r14_valor  as valorvantagemdescontototalizador, ";
        $sSqlFolhaPagamento .= "        case ";
        $sSqlFolhaPagamento .= "          when r14_pd = 1 then 'V' ";
        $sSqlFolhaPagamento .= "          when r14_pd = 2 then 'D'  ";
        $sSqlFolhaPagamento .= "          else 'O'  ";
        $sSqlFolhaPagamento .= "        end as identificacaooperacao, ";
        $sSqlFolhaPagamento .= "        case  ";
        $sSqlFolhaPagamento .= "          when ( select r09_rubric  ";
        $sSqlFolhaPagamento .= "                   from basesr ";
        $sSqlFolhaPagamento .= "                  where r09_anousu = gerfsal.r14_anousu  ";
        $sSqlFolhaPagamento .= "                    and r09_mesusu = gerfsal.r14_mesusu  ";
        $sSqlFolhaPagamento .= "                    and r09_rubric = gerfsal.r14_rubric ";
        $sSqlFolhaPagamento .= "                    and r09_instit = gerfsal.r14_instit ";
        $sSqlFolhaPagamento .= "                    and r09_base in ('B004','B005') limit 1 ) is not null  ";
        $sSqlFolhaPagamento .= "            then 'S' ";
        $sSqlFolhaPagamento .= "          else 'N' ";
        $sSqlFolhaPagamento .= "        end                      as indicadorincidenciairrf, ";
        $sSqlFolhaPagamento .= "        conplanoconta.c63_banco   as codigobancodepositofolhapagentidad, ";
        $sSqlFolhaPagamento .= "        case when (conplanoconta.c63_banco = '104') ";
        $sSqlFolhaPagamento .= "                then conplanoconta.c63_agencia ";
        $sSqlFolhaPagamento .= "                else conplanoconta.c63_agencia || conplanoconta.c63_dvagencia ";
        $sSqlFolhaPagamento .= "        end  as codigoagencdepositofolhapagentidad, ";

        $sSqlFolhaPagamento .= "        case when (conplanoconta.c63_banco = '104') then ";
        $sSqlFolhaPagamento .= "             coalesce(conplanoconta.c63_codigooperacao, '') || coalesce(conplanoconta.c63_conta, '') || coalesce(conplanoconta.c63_dvconta, '') ";
        $sSqlFolhaPagamento .= "          else ";
        $sSqlFolhaPagamento .= "             conplanoconta.c63_conta || conplanoconta.c63_dvconta ";
        $sSqlFolhaPagamento .= "        end as codcontacorrbancodepfolhapagent, ";

        $sSqlFolhaPagamento .= "        coalesce(rh44_codban,'') as codigobancofuncionario, ";
        $sSqlFolhaPagamento .= "        coalesce(rh44_agencia,'') as codigoagenciabancofuncionario, ";
        $sSqlFolhaPagamento .= "        coalesce(rh44_conta,'')||coalesce(rh44_dvconta,'') as codigocontacorrentebancofuncionario, ";
        $sSqlFolhaPagamento .= "        case when r14_pd = 3 then rh27_descr else '' end as observacoes, ";
        $sSqlFolhaPagamento .= "        o15_codigo as codigo_recurso, ";
        $sSqlFolhaPagamento .= "        o15_descr as descricao_recurso, ";
        $sSqlFolhaPagamento .= $sqlcampos12;
        $sSqlFolhaPagamento .= "        r14_instit||r14_rubric    as rubrica ";
        $sSqlFolhaPagamento .= "   from gerfsal ";
        $sSqlFolhaPagamento .= "        inner join rhpessoalmov on rhpessoalmov.rh02_anousu = gerfsal.r14_anousu ";
        $sSqlFolhaPagamento .= "                               and rhpessoalmov.rh02_mesusu = gerfsal.r14_mesusu ";
        $sSqlFolhaPagamento .= "                               and rhpessoalmov.rh02_regist = gerfsal.r14_regist ";
        $sSqlFolhaPagamento .= "                               and rhpessoalmov.rh02_instit = gerfsal.r14_instit ";
        $sSqlFolhaPagamento .= "        left  join rhpesbanco   on rhpesbanco.rh44_seqpes   = rhpessoalmov.rh02_seqpes ";
        $sSqlFolhaPagamento .= "        inner join rhrubricas   on rhrubricas.rh27_rubric   = gerfsal.r14_rubric ";
        $sSqlFolhaPagamento .= "                               and rhrubricas.rh27_instit   = gerfsal.r14_instit ";
        $sSqlFolhaPagamento .= $sql12;
        $sSqlFolhaPagamento .= "        left  join rhlota on rhlota.r70_codigo = rhpessoalmov.rh02_lota ";
        $sSqlFolhaPagamento .= "        left  join rhlotavinc on rhlotavinc.rh25_codigo = rhlota.r70_codigo and rh25_anousu = {$iAnoUsuFim}";
        $sSqlFolhaPagamento .= "        left  join orctiporec on orctiporec.o15_codigo = rhlotavinc.rh25_recurso ";
        $sSqlFolhaPagamento .= "        left  join rhcontasrec on rhcontasrec.rh41_codigo = orctiporec.o15_codigo ";
        $sSqlFolhaPagamento .= "                              and rhcontasrec.rh41_instit = gerfsal.r14_instit ";
        $sSqlFolhaPagamento .= "                              and rhcontasrec.rh41_anousu = gerfsal.r14_anousu ";
        $sSqlFolhaPagamento .= "        left  join saltes on saltes.k13_conta = rhcontasrec.rh41_conta ";
        $sSqlFolhaPagamento .= "        left  join conplanoreduz on conplanoreduz.c61_reduz  = saltes.k13_reduz ";
        $sSqlFolhaPagamento .= "                                and conplanoreduz.c61_anousu = gerfsal.r14_anousu ";
        $sSqlFolhaPagamento .= "        left  join conplanoexe on conplanoexe.c62_reduz    = conplanoreduz.c61_reduz ";
        $sSqlFolhaPagamento .= "                              and conplanoreduz.c61_anousu = conplanoexe.c62_anousu ";
        $sSqlFolhaPagamento .= "        left  join conplano on conplanoreduz.c61_codcon = conplano.c60_codcon ";
        $sSqlFolhaPagamento .= "                           and conplanoreduz.c61_anousu = conplano.c60_anousu ";
        $sSqlFolhaPagamento .= "        left  join conplanoconta on conplanoconta.c63_codcon = conplanoreduz.c61_codcon ";
        $sSqlFolhaPagamento .= "                                and conplanoconta.c63_anousu = conplanoreduz.c61_anousu ";
        $sSqlFolhaPagamento .= "                                and conplanoconta.c63_reduz  = conplanoreduz.c61_reduz ";
        $sSqlFolhaPagamento .= "  where gerfsal.r14_instit in ({$this->sInstituicoes}) ";
        $sSqlFolhaPagamento .= "    and gerfsal.r14_anousu = {$iAnoUsuFim} ";
        $sSqlFolhaPagamento .= "    and gerfsal.r14_mesusu between {$iMesUsuIni} and {$iMesUsuFim} ";
        $sSqlFolhaPagamento .= "    and not exists(select 1 ";
        $sSqlFolhaPagamento .= "                     from rhfolhapagamento ";
        $sSqlFolhaPagamento .= "                          inner join rhhistoricocalculo on rhhistoricocalculo.rh143_folhapagamento = rhfolhapagamento.rh141_sequencial ";
        $sSqlFolhaPagamento .= "                    where rhfolhapagamento.rh141_anousu = gerfsal.r14_anousu ";
        $sSqlFolhaPagamento .= "                      and rhfolhapagamento.rh141_mesusu = gerfsal.r14_mesusu ";
        $sSqlFolhaPagamento .= "                      and rhfolhapagamento.rh141_instit = gerfsal.r14_instit ";
        $sSqlFolhaPagamento .= "                      and rhhistoricocalculo.rh143_regist = gerfsal.r14_regist ";
        $sSqlFolhaPagamento .= "                      and rhhistoricocalculo.rh143_rubrica = gerfsal.r14_rubric ";
        $sSqlFolhaPagamento .= "                      and rhfolhapagamento.rh141_tipofolha = " . FolhaPagamento::TIPO_FOLHA_SUPLEMENTAR .  " ) ";

        $sSqlFolhaPagamento .= " union all";

        $sSqlFolhaPagamento .= " select 2 as codigotipofolha, ";
        $sSqlFolhaPagamento .= "        gerfs13.r35_anousu, ";
        $sSqlFolhaPagamento .= "        gerfs13.r35_mesusu, ";
        $sSqlFolhaPagamento .= "        gerfs13.r35_rubric, ";
        $sSqlFolhaPagamento .= "        gerfs13.r35_instit, ";
        $sSqlFolhaPagamento .= "        rhrubricas.rh27_pd, ";
        $sSqlFolhaPagamento .= "        r35_quant as quantidade_rubrica, ";
        $sSqlFolhaPagamento .= "        r35_regist as codigoregistrofuncionario, ";
        $sSqlFolhaPagamento .= "        r35_regist || '00' as matriculafuncionario, ";        
        $sSqlFolhaPagamento .= "        (cast(r35_anousu::varchar||'-'||r35_mesusu::varchar||'-'||(select fc_ultimodiames(r35_anousu,r35_mesusu))::varchar as date)) as datacompetenciafolha, ";
        $sSqlFolhaPagamento .= "        (cast(r35_anousu::varchar||'-'||r35_mesusu::varchar||'-'||(select fc_ultimodiames(r35_anousu,r35_mesusu))::varchar as date)) as datapagamentofolha, ";
        $sSqlFolhaPagamento .= "        '000'      as codigovantagemdescontototalizador, ";
        $sSqlFolhaPagamento .= "        null      as percentualdescontoirpf, ";
        $sSqlFolhaPagamento .= "        null      as percentualdescontoprevidencia, ";
        $sSqlFolhaPagamento .= "        null      as percentualdescontosaude, ";
        $sSqlFolhaPagamento .= "        null      as indicadordesconto, ";
        $sSqlFolhaPagamento .= "        null      as indicadordescontosaude, ";
        $sSqlFolhaPagamento .= "        r35_valor  as valorvantagemdescontototalizador, ";
        $sSqlFolhaPagamento .= "        case ";
        $sSqlFolhaPagamento .= "          when r35_pd = 1 then 'V' ";
        $sSqlFolhaPagamento .= "          when r35_pd = 2 then 'D' "; //-- os codigos  > R950 especificar como outros nas observaçoes
        $sSqlFolhaPagamento .= "          else 'O' ";
        $sSqlFolhaPagamento .= "        end as identificacaooperacao, ";
        $sSqlFolhaPagamento .= "        case  ";
        $sSqlFolhaPagamento .= "          when ( select r09_rubric  ";
        $sSqlFolhaPagamento .= "                   from basesr ";
        $sSqlFolhaPagamento .= "                  where r09_anousu = gerfs13.r35_anousu ";
        $sSqlFolhaPagamento .= "                    and r09_mesusu = gerfs13.r35_mesusu ";
        $sSqlFolhaPagamento .= "                    and r09_rubric = gerfs13.r35_rubric ";
        $sSqlFolhaPagamento .= "                    and r09_instit = gerfs13.r35_instit ";
        $sSqlFolhaPagamento .= "                    and r09_base in ('B004','B005') limit 1 ) is not null  ";
        $sSqlFolhaPagamento .= "            then 'S' ";
        $sSqlFolhaPagamento .= "          else 'N' ";
        $sSqlFolhaPagamento .= "        end                      as indicadorincidenciairrf, ";
        $sSqlFolhaPagamento .= "        conplanoconta.c63_banco   as codigobancodepositofolhapagentidad, ";
        $sSqlFolhaPagamento .= "        case when (conplanoconta.c63_banco = '104') ";
        $sSqlFolhaPagamento .= "                then conplanoconta.c63_agencia ";
        $sSqlFolhaPagamento .= "                else conplanoconta.c63_agencia || conplanoconta.c63_dvagencia ";
        $sSqlFolhaPagamento .= "        end  as codigoagencdepositofolhapagentidad, " ;
        //$sSqlFolhaPagamento .= "        conplanoconta.c63_conta || conplanoconta.c63_dvconta   as codcontacorrbancodepfolhapagent, ";

        $sSqlFolhaPagamento .= "        case when (conplanoconta.c63_banco = '104') then ";
        $sSqlFolhaPagamento .= "             coalesce(conplanoconta.c63_codigooperacao, '') || coalesce(conplanoconta.c63_conta, '') || coalesce(conplanoconta.c63_dvconta, '') ";
        $sSqlFolhaPagamento .= "          else ";
        $sSqlFolhaPagamento .= "             conplanoconta.c63_conta || conplanoconta.c63_dvconta ";
        $sSqlFolhaPagamento .= "        end as codcontacorrbancodepfolhapagent, ";

        $sSqlFolhaPagamento .= "        coalesce(rh44_codban,'') as codigobancofuncionario, ";
        $sSqlFolhaPagamento .= "        coalesce(rh44_agencia,'') as codigoagenciabancofuncionario, ";
        $sSqlFolhaPagamento .= "        coalesce(rh44_conta,'')||coalesce(rh44_dvconta,'') as codigocontacorrentebancofuncionario, ";
        $sSqlFolhaPagamento .= "        case when r35_pd = 3 then rh27_descr else '' end as observacoes, ";
        $sSqlFolhaPagamento .= "        o15_codigo as codigo_recurso, ";
        $sSqlFolhaPagamento .= "        o15_descr as descricao_recurso, ";
        $sSqlFolhaPagamento .= $sqlcampos12;
        $sSqlFolhaPagamento .= "        gerfs13.r35_instit||r35_rubric  as rubrica ";
        $sSqlFolhaPagamento .= "   from gerfs13 ";
        $sSqlFolhaPagamento .= "        inner join rhpessoalmov on rhpessoalmov.rh02_anousu = gerfs13.r35_anousu ";
        $sSqlFolhaPagamento .= "                               and rhpessoalmov.rh02_mesusu = gerfs13.r35_mesusu ";
        $sSqlFolhaPagamento .= "                               and rhpessoalmov.rh02_regist = gerfs13.r35_regist ";
        $sSqlFolhaPagamento .= "                               and rhpessoalmov.rh02_instit = gerfs13.r35_instit ";
        $sSqlFolhaPagamento .= "        left  join rhpesbanco   on rhpesbanco.rh44_seqpes   = rhpessoalmov.rh02_seqpes ";
        $sSqlFolhaPagamento .= "        inner join rhrubricas   on rhrubricas.rh27_rubric   = gerfs13.r35_rubric ";
        $sSqlFolhaPagamento .= "                               and rhrubricas.rh27_instit   = gerfs13.r35_instit ";
        $sSqlFolhaPagamento .= $sql12;
        $sSqlFolhaPagamento .= "        left  join rhlota on rhlota.r70_codigo = rhpessoalmov.rh02_lota ";
        $sSqlFolhaPagamento .= "        left  join rhlotavinc on rhlotavinc.rh25_codigo = rhlota.r70_codigo and rh25_anousu = {$iAnoUsuFim}";
        $sSqlFolhaPagamento .= "        left  join orctiporec on orctiporec.o15_codigo = rhlotavinc.rh25_recurso ";
        $sSqlFolhaPagamento .= "        left  join rhcontasrec on rhcontasrec.rh41_codigo = orctiporec.o15_codigo ";
        $sSqlFolhaPagamento .= "                              and rhcontasrec.rh41_instit = gerfs13.r35_instit ";
        $sSqlFolhaPagamento .= "                              and rhcontasrec.rh41_anousu = gerfs13.r35_anousu ";
        $sSqlFolhaPagamento .= "        left  join saltes on saltes.k13_conta = rhcontasrec.rh41_conta ";
        $sSqlFolhaPagamento .= "        left  join conplanoreduz on conplanoreduz.c61_reduz  = saltes.k13_reduz ";
        $sSqlFolhaPagamento .= "                                and conplanoreduz.c61_anousu = gerfs13.r35_anousu ";
        $sSqlFolhaPagamento .= "        left  join conplanoexe on conplanoexe.c62_reduz    = conplanoreduz.c61_reduz ";
        $sSqlFolhaPagamento .= "                              and conplanoreduz.c61_anousu = conplanoexe.c62_anousu ";
        $sSqlFolhaPagamento .= "        left  join conplano on conplanoreduz.c61_codcon = conplano.c60_codcon ";
        $sSqlFolhaPagamento .= "                           and conplanoreduz.c61_anousu = conplano.c60_anousu ";
        $sSqlFolhaPagamento .= "        left  join conplanoconta on conplanoconta.c63_codcon = conplanoreduz.c61_codcon ";
        $sSqlFolhaPagamento .= "                                and conplanoconta.c63_anousu = conplanoreduz.c61_anousu ";
        $sSqlFolhaPagamento .= "  where gerfs13.r35_instit in ({$this->sInstituicoes}) ";
        $sSqlFolhaPagamento .= "    and gerfs13.r35_anousu = {$iAnoUsuFim} ";
        $sSqlFolhaPagamento .= "    and gerfs13.r35_mesusu between {$iMesUsuIni} and {$iMesUsuFim} ";

        $sSqlFolhaPagamento .= " union all ";

        $sSqlFolhaPagamento .= " select 4 as codigotipofolha, ";
        $sSqlFolhaPagamento .= "        gerfres.r20_anousu, ";
        $sSqlFolhaPagamento .= "        gerfres.r20_mesusu, ";
        $sSqlFolhaPagamento .= "        gerfres.r20_rubric, ";
        $sSqlFolhaPagamento .= "        gerfres.r20_instit, ";
        $sSqlFolhaPagamento .= "        rhrubricas.rh27_pd, ";
        $sSqlFolhaPagamento .= "        r20_quant as quantidade_rubrica, ";
        $sSqlFolhaPagamento .= "        r20_regist as codigoregistrofuncionario, ";
        $sSqlFolhaPagamento .= "        r20_regist || '00' as matriculafuncionario, ";        
        $sSqlFolhaPagamento .= "        (cast(r20_anousu::varchar||'-'||r20_mesusu::varchar||'-'||(select fc_ultimodiames(r20_anousu,r20_mesusu))::varchar as date)) as datacompetenciafolha, ";
        $sSqlFolhaPagamento .= "        (cast(r20_anousu::varchar||'-'||r20_mesusu::varchar||'-'||(select fc_ultimodiames(r20_anousu,r20_mesusu))::varchar as date)) as datapagamentofolha, ";
        $sSqlFolhaPagamento .= "        '000'      as codigovantagemdescontototalizador, ";
        $sSqlFolhaPagamento .= "        null      as percentualdescontoirpf, ";
        $sSqlFolhaPagamento .= "        null      as percentualdescontoprevidencia, ";
        $sSqlFolhaPagamento .= "        null      as percentualdescontosaude, ";
        $sSqlFolhaPagamento .= "        null      as indicadordesconto, ";
        $sSqlFolhaPagamento .= "        null      as indicadordescontosaude, ";

        /**
         *  Soma pela chave da gerfres tirando apenas o r20_tpp
         *  pois pode ter por exemplo a rubrica de férias vencida / proporcional
         *  e na geração um registro seria descartado na validação da chave do arquivo
         */

        $sSqlFolhaPagamento .= "        (   select sum(r20_valor)                     ";
        $sSqlFolhaPagamento .= "              from gerfres sq                         ";
        $sSqlFolhaPagamento .= "             where sq.r20_anousu = gerfres.r20_anousu ";
        $sSqlFolhaPagamento .= "               and sq.r20_mesusu = gerfres.r20_mesusu ";
        $sSqlFolhaPagamento .= "               and sq.r20_regist = gerfres.r20_regist ";
        $sSqlFolhaPagamento .= "               and sq.r20_rubric = gerfres.r20_rubric ";
        $sSqlFolhaPagamento .= "          group by sq.r20_anousu,                     ";
        $sSqlFolhaPagamento .= "                   sq.r20_mesusu,                     ";
        $sSqlFolhaPagamento .= "                   sq.r20_rubric,                     ";
        $sSqlFolhaPagamento .= "                   sq.r20_pd,                         ";
        $sSqlFolhaPagamento .= "                   sq.r20_regist                      ";
        $sSqlFolhaPagamento .= "        ) as valorvantagemdescontototalizador,        ";

        $sSqlFolhaPagamento .= "        case ";
        $sSqlFolhaPagamento .= "          when r20_pd = 1 then 'V' ";
        $sSqlFolhaPagamento .= "          when r20_pd = 2 then 'D' ";
        $sSqlFolhaPagamento .= "          else 'O'";
        $sSqlFolhaPagamento .= "        end as identificacaooperacao, ";
        $sSqlFolhaPagamento .= "        case  ";
        $sSqlFolhaPagamento .= "          when ( select r09_rubric  ";
        $sSqlFolhaPagamento .= "                   from basesr ";
        $sSqlFolhaPagamento .= "                  where r09_anousu = gerfres.r20_anousu ";
        $sSqlFolhaPagamento .= "                    and r09_mesusu = gerfres.r20_mesusu ";
        $sSqlFolhaPagamento .= "                    and r09_rubric = gerfres.r20_rubric ";
        $sSqlFolhaPagamento .= "                    and r09_instit = gerfres.r20_instit ";
        $sSqlFolhaPagamento .= "                    and r09_base in ('B004','B005') limit 1 ) is not null  ";
        $sSqlFolhaPagamento .= "            then 'S' ";
        $sSqlFolhaPagamento .= "          else 'N' ";
        $sSqlFolhaPagamento .= "        end                      as indicadorincidenciairrf, ";
        $sSqlFolhaPagamento .= "        conplanoconta.c63_banco   as codigobancodepositofolhapagentidad, ";
        $sSqlFolhaPagamento .= "        case when (conplanoconta.c63_banco = '104') ";
        $sSqlFolhaPagamento .= "                then conplanoconta.c63_agencia ";
        $sSqlFolhaPagamento .= "                else conplanoconta.c63_agencia || conplanoconta.c63_dvagencia ";
        $sSqlFolhaPagamento .= "        end  as codigoagencdepositofolhapagentidad, " ;

        //$sSqlFolhaPagamento .= "        conplanoconta.c63_conta || conplanoconta.c63_dvconta   as codcontacorrbancodepfolhapagent, ";
        $sSqlFolhaPagamento .= "        case when (conplanoconta.c63_banco = '104') then ";
        $sSqlFolhaPagamento .= "             coalesce(conplanoconta.c63_codigooperacao, '') || coalesce(conplanoconta.c63_conta, '') || coalesce(conplanoconta.c63_dvconta, '') ";
        $sSqlFolhaPagamento .= "          else ";
        $sSqlFolhaPagamento .= "             conplanoconta.c63_conta || conplanoconta.c63_dvconta ";
        $sSqlFolhaPagamento .= "        end as codcontacorrbancodepfolhapagent, ";

        $sSqlFolhaPagamento .= "        coalesce(rh44_codban,'') as codigobancofuncionario, ";
        $sSqlFolhaPagamento .= "        coalesce(rh44_agencia,'') as codigoagenciabancofuncionario, ";
        $sSqlFolhaPagamento .= "        coalesce(rh44_conta,'')||coalesce(rh44_dvconta,'') as codigocontacorrentebancofuncionario, ";
        $sSqlFolhaPagamento .= "        case when r20_pd = 3 then rh27_descr else '' end as observacoes, ";
        $sSqlFolhaPagamento .= "        o15_codigo as codigo_recurso, ";
        $sSqlFolhaPagamento .= "        o15_descr as descricao_recurso, ";
        $sSqlFolhaPagamento .= $sqlcampos12;
        $sSqlFolhaPagamento .= "        r20_instit||r20_rubric as rubrica ";
        $sSqlFolhaPagamento .= "   from gerfres ";
        $sSqlFolhaPagamento .= "        inner join rhpessoalmov on rhpessoalmov.rh02_anousu = gerfres.r20_anousu ";
        $sSqlFolhaPagamento .= "                               and rhpessoalmov.rh02_mesusu = gerfres.r20_mesusu ";
        $sSqlFolhaPagamento .= "                               and rhpessoalmov.rh02_regist = gerfres.r20_regist ";
        $sSqlFolhaPagamento .= "                               and rhpessoalmov.rh02_instit = gerfres.r20_instit ";
        $sSqlFolhaPagamento .= "        left  join rhpesbanco   on rhpesbanco.rh44_seqpes   = rhpessoalmov.rh02_seqpes ";
        $sSqlFolhaPagamento .= "        inner join rhrubricas   on rhrubricas.rh27_rubric   = gerfres.r20_rubric ";
        $sSqlFolhaPagamento .= "                               and rhrubricas.rh27_instit   = gerfres.r20_instit ";
        $sSqlFolhaPagamento .= $sql12;
        $sSqlFolhaPagamento .= "        left  join rhlota on rhlota.r70_codigo = rhpessoalmov.rh02_lota ";
        $sSqlFolhaPagamento .= "        left  join rhlotavinc on rhlotavinc.rh25_codigo = rhlota.r70_codigo and rh25_anousu = {$iAnoUsuFim}";
        $sSqlFolhaPagamento .= "        left  join orctiporec on orctiporec.o15_codigo = rhlotavinc.rh25_recurso ";
        $sSqlFolhaPagamento .= "        left  join rhcontasrec on rhcontasrec.rh41_codigo = orctiporec.o15_codigo ";
        $sSqlFolhaPagamento .= "                              and rhcontasrec.rh41_instit          = gerfres.r20_instit ";
        $sSqlFolhaPagamento .= "                              and rhcontasrec.rh41_anousu          = gerfres.r20_anousu ";
        $sSqlFolhaPagamento .= "        left  join saltes on saltes.k13_conta = rhcontasrec.rh41_conta ";
        $sSqlFolhaPagamento .= "        left  join conplanoreduz on conplanoreduz.c61_reduz  = saltes.k13_reduz ";
        $sSqlFolhaPagamento .= "                                and conplanoreduz.c61_anousu = gerfres.r20_anousu ";
        $sSqlFolhaPagamento .= "        left  join conplanoexe on conplanoexe.c62_reduz    = conplanoreduz.c61_reduz ";
        $sSqlFolhaPagamento .= "                              and conplanoreduz.c61_anousu = conplanoexe.c62_anousu ";
        $sSqlFolhaPagamento .= "        left  join conplano on conplanoreduz.c61_codcon = conplano.c60_codcon ";
        $sSqlFolhaPagamento .= "                           and conplanoreduz.c61_anousu = conplano.c60_anousu ";
        $sSqlFolhaPagamento .= "        left  join conplanoconta on conplanoconta.c63_codcon = conplanoreduz.c61_codcon ";
        $sSqlFolhaPagamento .= "                                and conplanoconta.c63_anousu = conplanoreduz.c61_anousu ";
        $sSqlFolhaPagamento .= "  where gerfres.r20_instit in ({$this->sInstituicoes}) ";
        $sSqlFolhaPagamento .= "    and gerfres.r20_anousu = {$iAnoUsuFim} ";
        $sSqlFolhaPagamento .= "    and gerfres.r20_mesusu between {$iMesUsuIni} and {$iMesUsuFim} ";

        $sSqlFolhaPagamento .= " union all ";

        $sSqlFolhaPagamento .= " select 5 as codigotipofolha, ";
        $sSqlFolhaPagamento .= "        gerfcom.r48_anousu, ";
        $sSqlFolhaPagamento .= "        gerfcom.r48_mesusu, ";
        $sSqlFolhaPagamento .= "        gerfcom.r48_rubric, ";
        $sSqlFolhaPagamento .= "        gerfcom.r48_instit, ";
        $sSqlFolhaPagamento .= "        rhrubricas.rh27_pd, ";
        $sSqlFolhaPagamento .= "        r48_quant as quantidade_rubrica, ";
        $sSqlFolhaPagamento .= "        r48_regist as codigoregistrofuncionario, ";
        $sSqlFolhaPagamento .= "        r48_regist || '00' as matriculafuncionario, ";
        $sSqlFolhaPagamento .= "        (cast(r48_anousu::varchar||'-'||r48_mesusu::varchar||'-'||(select fc_ultimodiames(r48_anousu,r48_mesusu))::varchar as date)) as datacompetenciafolha, ";
        $sSqlFolhaPagamento .= "        (cast(r48_anousu::varchar||'-'||r48_mesusu::varchar||'-'||(select fc_ultimodiames(r48_anousu,r48_mesusu))::varchar as date)) as datapagamentofolha, ";
        $sSqlFolhaPagamento .= "        '000'      as codigovantagemdescontototalizador, ";
        $sSqlFolhaPagamento .= "        null      as percentualdescontoirpf, ";
        $sSqlFolhaPagamento .= "        null      as percentualdescontoprevidencia, ";
        $sSqlFolhaPagamento .= "        null      as percentualdescontosaude, ";
        $sSqlFolhaPagamento .= "        null      as indicadordesconto, ";
        $sSqlFolhaPagamento .= "        null      as indicadordescontosaude, ";
        $sSqlFolhaPagamento .= "        r48_valor  as valorvantagemdescontototalizador, ";
        $sSqlFolhaPagamento .= "        case ";
        $sSqlFolhaPagamento .= "          when r48_pd = 1 then 'V' ";
        $sSqlFolhaPagamento .= "          when r48_pd = 2 then 'D' ";
        $sSqlFolhaPagamento .= "          else 'O'";
        $sSqlFolhaPagamento .= "        end as identificacaooperacao, ";
        $sSqlFolhaPagamento .= "        case  ";
        $sSqlFolhaPagamento .= "          when ( select r09_rubric  ";
        $sSqlFolhaPagamento .= "                   from basesr ";
        $sSqlFolhaPagamento .= "                  where r09_anousu = gerfcom.r48_anousu ";
        $sSqlFolhaPagamento .= "                    and r09_mesusu = gerfcom.r48_mesusu ";
        $sSqlFolhaPagamento .= "                    and r09_rubric = gerfcom.r48_rubric ";
        $sSqlFolhaPagamento .= "                    and r09_instit = gerfcom.r48_instit ";
        $sSqlFolhaPagamento .= "                    and r09_base in ('B004','B005') limit 1 ) is not null  ";
        $sSqlFolhaPagamento .= "            then 'S' ";
        $sSqlFolhaPagamento .= "          else 'N' ";
        $sSqlFolhaPagamento .= "        end                      as indicadorincidenciairrf, ";
        $sSqlFolhaPagamento .= "        conplanoconta.c63_banco   as codigobancodepositofolhapagentidad, ";
        $sSqlFolhaPagamento .= "        case when (conplanoconta.c63_banco = '104') ";
        $sSqlFolhaPagamento .= "                then conplanoconta.c63_agencia ";
        $sSqlFolhaPagamento .= "                else conplanoconta.c63_agencia || conplanoconta.c63_dvagencia ";
        $sSqlFolhaPagamento .= "        end  as codigoagencdepositofolhapagentidad, " ;
        //$sSqlFolhaPagamento .= "        conplanoconta.c63_conta || conplanoconta.c63_dvconta   as codcontacorrbancodepfolhapagent, ";
        $sSqlFolhaPagamento .= "        case when (conplanoconta.c63_banco = '104') then ";
        $sSqlFolhaPagamento .= "             coalesce(conplanoconta.c63_codigooperacao, '') || coalesce(conplanoconta.c63_conta, '') || coalesce(conplanoconta.c63_dvconta, '') ";
        $sSqlFolhaPagamento .= "          else ";
        $sSqlFolhaPagamento .= "             conplanoconta.c63_conta || conplanoconta.c63_dvconta ";
        $sSqlFolhaPagamento .= "        end as codcontacorrbancodepfolhapagent, ";


        $sSqlFolhaPagamento .= "        coalesce(rh44_codban,'') as codigobancofuncionario, ";
        $sSqlFolhaPagamento .= "        coalesce(rh44_agencia,'') as codigoagenciabancofuncionario, ";
        $sSqlFolhaPagamento .= "        coalesce(rh44_conta,'')||coalesce(rh44_dvconta,'') as codigocontacorrentebancofuncionario, ";
        $sSqlFolhaPagamento .= "        case when r48_pd = 3 then rh27_descr else '' end as observacoes, ";
        $sSqlFolhaPagamento .= "        o15_codigo as codigo_recurso, ";
        $sSqlFolhaPagamento .= "        o15_descr as descricao_recurso, ";
        $sSqlFolhaPagamento .= $sqlcampos12;
        $sSqlFolhaPagamento .= "        r48_instit||r48_rubric  as rubrica ";
        $sSqlFolhaPagamento .= "   from gerfcom ";
        $sSqlFolhaPagamento .= "        inner join rhpessoalmov on rhpessoalmov.rh02_anousu = gerfcom.r48_anousu ";
        $sSqlFolhaPagamento .= "                               and rhpessoalmov.rh02_mesusu = gerfcom.r48_mesusu ";
        $sSqlFolhaPagamento .= "                               and rhpessoalmov.rh02_regist = gerfcom.r48_regist ";
        $sSqlFolhaPagamento .= "                               and rhpessoalmov.rh02_instit = gerfcom.r48_instit ";
        $sSqlFolhaPagamento .= "        left  join rhpesbanco   on rhpesbanco.rh44_seqpes   = rhpessoalmov.rh02_seqpes ";
        $sSqlFolhaPagamento .= "        inner join rhrubricas   on rhrubricas.rh27_rubric   = gerfcom.r48_rubric ";
        $sSqlFolhaPagamento .= "                               and rhrubricas.rh27_instit   = gerfcom.r48_instit ";
        $sSqlFolhaPagamento .= $sql12;
        $sSqlFolhaPagamento .= "        left  join rhlota on rhlota.r70_codigo = rhpessoalmov.rh02_lota ";
        $sSqlFolhaPagamento .= "        left  join rhlotavinc on rhlotavinc.rh25_codigo = rhlota.r70_codigo and rh25_anousu = {$iAnoUsuFim}";
        $sSqlFolhaPagamento .= "        left  join orctiporec on orctiporec.o15_codigo = rhlotavinc.rh25_recurso ";
        $sSqlFolhaPagamento .= "        left  join rhcontasrec on rhcontasrec.rh41_codigo = orctiporec.o15_codigo ";
        $sSqlFolhaPagamento .= "                              and rhcontasrec.rh41_instit = gerfcom.r48_instit ";
        $sSqlFolhaPagamento .= "                              and rhcontasrec.rh41_anousu = gerfcom.r48_anousu ";
        $sSqlFolhaPagamento .= "        left  join saltes on saltes.k13_conta = rhcontasrec.rh41_conta ";
        $sSqlFolhaPagamento .= "        left  join conplanoreduz on conplanoreduz.c61_reduz  = saltes.k13_reduz ";
        $sSqlFolhaPagamento .= "                                and conplanoreduz.c61_anousu = gerfcom.r48_anousu ";
        $sSqlFolhaPagamento .= "        left  join conplanoexe on conplanoexe.c62_reduz    = conplanoreduz.c61_reduz ";
        $sSqlFolhaPagamento .= "                              and conplanoreduz.c61_anousu = conplanoexe.c62_anousu ";
        $sSqlFolhaPagamento .= "        left  join conplano on conplanoreduz.c61_codcon = conplano.c60_codcon ";
        $sSqlFolhaPagamento .= "                           and conplanoreduz.c61_anousu = conplano.c60_anousu ";
        $sSqlFolhaPagamento .= "        left  join conplanoconta on conplanoconta.c63_codcon = conplanoreduz.c61_codcon ";
        $sSqlFolhaPagamento .= "                                and conplanoconta.c63_anousu = conplanoreduz.c61_anousu ";
        $sSqlFolhaPagamento .= "  where gerfcom.r48_instit in ({$this->sInstituicoes}) ";
        $sSqlFolhaPagamento .= "    and gerfcom.r48_anousu = {$iAnoUsuFim} ";
        $sSqlFolhaPagamento .= "    and gerfcom.r48_mesusu between {$iMesUsuIni} and {$iMesUsuFim} ";

        $sSqlFolhaPagamento .= " union all ";

        $sSqlFolhaPagamento .= " select 5 as codigotipofolha, ";
        $sSqlFolhaPagamento .= "        rhfolhapagamento.rh141_anousu, ";
        $sSqlFolhaPagamento .= "        rhfolhapagamento.rh141_mesusu, ";
        $sSqlFolhaPagamento .= "        rhhistoricocalculo.rh143_rubrica, ";
        $sSqlFolhaPagamento .= "        rhfolhapagamento.rh141_instit, ";
        $sSqlFolhaPagamento .= "        rhhistoricocalculo.rh143_tipoevento, ";
        $sSqlFolhaPagamento .= "        rh143_quantidade as quantidade_rubrica, ";
        $sSqlFolhaPagamento .= "        rh143_regist as codigoregistrofuncionario, ";
        $sSqlFolhaPagamento .= "        rh143_regist || '00' as matriculafuncionario, ";
        $sSqlFolhaPagamento .= "        (cast(rh141_anousu::varchar||'-'||rh141_mesusu::varchar||'-'||(select fc_ultimodiames(rh141_anousu,rh141_mesusu))::varchar as date)) as datacompetenciafolha, ";
        $sSqlFolhaPagamento .= "        (cast(rh141_anousu::varchar||'-'||rh141_mesusu::varchar||'-'||(select fc_ultimodiames(rh141_anousu,rh141_mesusu))::varchar as date)) as datapagamentofolha, ";
        $sSqlFolhaPagamento .= "        '000'      as codigovantagemdescontototalizador, ";
        $sSqlFolhaPagamento .= "        null      as percentualdescontoirpf, ";
        $sSqlFolhaPagamento .= "        null      as percentualdescontoprevidencia, ";
        $sSqlFolhaPagamento .= "        null      as percentualdescontosaude, ";
        $sSqlFolhaPagamento .= "        null      as indicadordesconto, ";
        $sSqlFolhaPagamento .= "        null      as indicadordescontosaude, ";
        $sSqlFolhaPagamento .= "        rh143_valor  as valorvantagemdescontototalizador, ";
        $sSqlFolhaPagamento .= "        case ";
        $sSqlFolhaPagamento .= "          when rh143_tipoevento = 1 then 'V' ";
        $sSqlFolhaPagamento .= "          when rh143_tipoevento = 2 then 'D' ";
        $sSqlFolhaPagamento .= "          else 'O'";
        $sSqlFolhaPagamento .= "        end as identificacaooperacao, ";
        $sSqlFolhaPagamento .= "        case  ";
        $sSqlFolhaPagamento .= "          when ( select r09_rubric  ";
        $sSqlFolhaPagamento .= "                   from basesr ";
        $sSqlFolhaPagamento .= "                  where r09_anousu = rhfolhapagamento.rh141_anousu ";
        $sSqlFolhaPagamento .= "                    and r09_mesusu = rhfolhapagamento.rh141_mesusu ";
        $sSqlFolhaPagamento .= "                    and r09_rubric = rhhistoricocalculo.rh143_rubrica ";
        $sSqlFolhaPagamento .= "                    and r09_instit = rhfolhapagamento.rh141_instit ";
        $sSqlFolhaPagamento .= "                    and r09_base in ('B004','B005') limit 1 ) is not null  ";
        $sSqlFolhaPagamento .= "            then 'S' ";
        $sSqlFolhaPagamento .= "          else 'N' ";
        $sSqlFolhaPagamento .= "        end                      as indicadorincidenciairrf, ";
        $sSqlFolhaPagamento .= "        conplanoconta.c63_banco   as codigobancodepositofolhapagentidad, ";
        $sSqlFolhaPagamento .= "        case when (conplanoconta.c63_banco = '104') ";
        $sSqlFolhaPagamento .= "                then conplanoconta.c63_agencia ";
        $sSqlFolhaPagamento .= "                else conplanoconta.c63_agencia || conplanoconta.c63_dvagencia ";
        $sSqlFolhaPagamento .= "        end  as codigoagencdepositofolhapagentidad, " ;
        //$sSqlFolhaPagamento .= "        conplanoconta.c63_conta || conplanoconta.c63_dvconta   as codcontacorrbancodepfolhapagent, ";

        $sSqlFolhaPagamento .= "        case when (conplanoconta.c63_banco = '104') then ";
        $sSqlFolhaPagamento .= "             coalesce(conplanoconta.c63_codigooperacao, '') || coalesce(conplanoconta.c63_conta, '') || coalesce(conplanoconta.c63_dvconta, '') ";
        $sSqlFolhaPagamento .= "          else ";
        $sSqlFolhaPagamento .= "             conplanoconta.c63_conta || conplanoconta.c63_dvconta ";
        $sSqlFolhaPagamento .= "        end as codcontacorrbancodepfolhapagent, ";

        $sSqlFolhaPagamento .= "        coalesce(rh44_codban,'') as codigobancofuncionario, ";
        $sSqlFolhaPagamento .= "        coalesce(rh44_agencia,'') as codigoagenciabancofuncionario, ";
        $sSqlFolhaPagamento .= "        coalesce(rh44_conta,'')||coalesce(rh44_dvconta,'') as codigocontacorrentebancofuncionario, ";
        $sSqlFolhaPagamento .= "        case when rh143_tipoevento = 3 then rh27_descr else '' end as observacoes, ";
        $sSqlFolhaPagamento .= "        o15_codigo as codigo_recurso, ";
        $sSqlFolhaPagamento .= "        o15_descr as descricao_recurso, ";
        $sSqlFolhaPagamento .= $sqlcampos12;
        $sSqlFolhaPagamento .= "        rh141_instit||rh143_rubrica  as rubrica ";
        $sSqlFolhaPagamento .= "   from rhfolhapagamento ";
        $sSqlFolhaPagamento .= "        inner join rhhistoricocalculo on rhhistoricocalculo.rh143_folhapagamento = rhfolhapagamento.rh141_sequencial ";
        $sSqlFolhaPagamento .= "        inner join rhpessoalmov on rhpessoalmov.rh02_anousu = rhfolhapagamento.rh141_anousu ";
        $sSqlFolhaPagamento .= "                               and rhpessoalmov.rh02_mesusu = rhfolhapagamento.rh141_mesusu ";
        $sSqlFolhaPagamento .= "                               and rhpessoalmov.rh02_regist = rhhistoricocalculo.rh143_regist ";
        $sSqlFolhaPagamento .= "                               and rhpessoalmov.rh02_instit = rhfolhapagamento.rh141_instit ";
        $sSqlFolhaPagamento .= "        left  join rhpesbanco   on rhpesbanco.rh44_seqpes   = rhpessoalmov.rh02_seqpes ";
        $sSqlFolhaPagamento .= "        inner join rhrubricas   on rhrubricas.rh27_rubric   = rhhistoricocalculo.rh143_rubrica ";
        $sSqlFolhaPagamento .= "                               and rhrubricas.rh27_instit   = rhfolhapagamento.rh141_instit ";
        $sSqlFolhaPagamento .= $sql12;
        $sSqlFolhaPagamento .= "        left  join rhlota on rhlota.r70_codigo = rhpessoalmov.rh02_lota ";
        $sSqlFolhaPagamento .= "        left  join rhlotavinc on rhlotavinc.rh25_codigo = rhlota.r70_codigo and rh25_anousu = {$iAnoUsuFim}";
        $sSqlFolhaPagamento .= "        left  join orctiporec on orctiporec.o15_codigo = rhlotavinc.rh25_recurso ";
        $sSqlFolhaPagamento .= "        left  join rhcontasrec on rhcontasrec.rh41_codigo = orctiporec.o15_codigo ";
        $sSqlFolhaPagamento .= "                              and rhcontasrec.rh41_instit = rhfolhapagamento.rh141_instit ";
        $sSqlFolhaPagamento .= "                              and rhcontasrec.rh41_anousu = rhfolhapagamento.rh141_anousu ";
        $sSqlFolhaPagamento .= "        left  join saltes on saltes.k13_conta = rhcontasrec.rh41_conta ";
        $sSqlFolhaPagamento .= "        left  join conplanoreduz on conplanoreduz.c61_reduz  = saltes.k13_reduz ";
        $sSqlFolhaPagamento .= "                                and conplanoreduz.c61_anousu = rhfolhapagamento.rh141_anousu ";
        $sSqlFolhaPagamento .= "        left  join conplanoexe on conplanoexe.c62_reduz    = conplanoreduz.c61_reduz ";
        $sSqlFolhaPagamento .= "                              and conplanoreduz.c61_anousu = conplanoexe.c62_anousu ";
        $sSqlFolhaPagamento .= "        left  join conplano on conplanoreduz.c61_codcon = conplano.c60_codcon ";
        $sSqlFolhaPagamento .= "                           and conplanoreduz.c61_anousu = conplano.c60_anousu ";
        $sSqlFolhaPagamento .= "        left  join conplanoconta on conplanoconta.c63_codcon = conplanoreduz.c61_codcon ";
        $sSqlFolhaPagamento .= "                                and conplanoconta.c63_anousu = conplanoreduz.c61_anousu ";
        $sSqlFolhaPagamento .= "  where rhfolhapagamento.rh141_instit in ({$this->sInstituicoes}) ";
        $sSqlFolhaPagamento .= "    and rhfolhapagamento.rh141_anousu = {$iAnoUsuFim} ";
        $sSqlFolhaPagamento .= "    and rhfolhapagamento.rh141_mesusu between {$iMesUsuIni} and {$iMesUsuFim} ";
        $sSqlFolhaPagamento .= "    and rhfolhapagamento.rh141_tipofolha = " . FolhaPagamento::TIPO_FOLHA_SUPLEMENTAR ;

        $sSqlFolhaPagamento .= " ) as qry ";

        $sSqlFolhaPagamento .= " INNER JOIN rhdatapagamentofolha ON rh225_instituicao = qry.r14_instit ";
        $sSqlFolhaPagamento .= "                                AND rh225_ano = qry.r14_anousu ";
        $sSqlFolhaPagamento .= "                                AND rh225_mes = qry.r14_mesusu ";
        $sSqlFolhaPagamento .= " order by codigoregistrofuncionario, datacompetenciafolha, codigotipofolha, rubrica";

        return $sSqlFolhaPagamento;
    }
}
