<?php

namespace App\Domain\Tributario\ITBI\Services;

use App\Domain\Tributario\Arrecadacao\Services\ArrecadacaoPixItbiService;
use App\Domain\Configuracao\Departamento\Repositories\DepartamentoRepository;
use App\Domain\Configuracao\Instituicao\Repository\InstituicaoRepository;
use App\Domain\Tributario\Arrecadacao\Repositories\ArretipoRepository;
use App\Domain\Tributario\Arrecadacao\Repositories\NumprefRepository;
use App\Domain\Tributario\Cadastro\Repositories\IptuantRepository;
use App\Domain\Tributario\Cadastro\Repositories\IptubaseRepository;
use App\Domain\Tributario\ITBI\Models\Itbinumpre;
use App\Domain\Tributario\ITBI\Reports\EmissaoGuia;
use App\Domain\Tributario\ITBI\Repositories\ItbiavaliaRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbiintermediadorRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbiconstrRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbidadosimovelRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbinomeRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbinumpreRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbiRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbiruralcaractRepository;
use App\Domain\Tributario\ITBI\Repositories\ParitbiRepository;
use App\Domain\Tributario\ITBI\Repositories\ParreciboitbiRepository;
use CgmRepository;
use ECidade\Lib\Session\DefaultSession;
use \ECidade\Tributario\Arrecadacao\CobrancaRegistrada\CobrancaRegistrada;
use ECidade\Tributario\ITBI\Model\Itbitaxasavalia;
use ECidade\Tributario\ITBI\Repository\ItbitaxasavaliaRepository;
use regraEmissao;
use convenio;
use cl_cgm;
use db_utils;

final class EmissaoGuiaService extends EmissaoGuia
{
    public function __construct()
    {
        $this->defaultSession = DefaultSession::getInstance();
        $this->instituicao = $this->defaultSession->get(DefaultSession::DB_INSTIT);
        $this->datausu = date("Y-m-d", $this->defaultSession->get(DefaultSession::DB_DATAUSU));
        $this->anousu = $this->defaultSession->get(DefaultSession::DB_ANOUSU);
    }

    /**
     * @param int $numeroGuia
     */
    public function setNumeroGuia($numeroGuia)
    {
        $this->numeroGuia = $numeroGuia;
    }

    public function verificaGuiaPaga()
    {
        $itbinumpreRepository = ItbinumpreRepository::getInstance();

        $aPagamentos = $itbinumpreRepository->getPagamentosGuia($this->numeroGuia);

        if (count($aPagamentos) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Emite guia de ITBI
     * @throws \Exception
     */
    public function emitir()
    {
        $this->carregaRegraEmissao();
        $this->carregaDados();

        if ($this->dados->bLiberado) {
            $oRecibo = $this->geraRecibo();
            $this->geraCobrancaRegistrada($oRecibo);
            $this->ajustaItbinumpre();
            $this->carregaConvenio();
            $this->geraPix($oRecibo);
        }

        $this->gerar();
    }

    /**
     * Carrega os dados da regra de emissão
     * @throws \Exception
     */
    private function carregaRegraEmissao()
    {
        try {
            $this->regraEmissao = new regraEmissao(
                29,
                3,
                $this->instituicao,
                $this->datausu,
                $this->defaultSession->get(DefaultSession::DB_IP)
            );

            parent::__construct($this->regraEmissao);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * Carrega todos os dados necessários para a geração do recibo
     */
    private function carregaDados()
    {
        $instituicaoRepository = new InstituicaoRepository();
        $oInstituicao = $instituicaoRepository->find($this->instituicao);
        $this->setDados("oInstituicao", $oInstituicao);

        $itbiintermediadorRepository = new ItbiintermediadorRepository;
        $oItbiintermediador = $itbiintermediadorRepository->getByGuia($this->numeroGuia, true);

        $this->setDados("oItbiintermediador", $oItbiintermediador);

        $arretipoRepository = new ArretipoRepository();
        $oArretipo = $arretipoRepository->getByCadTipo(8, ["k00_descr", "k00_msgrecibo"]);

        $this->setDados("oArretipo", $oArretipo);

        $itbinumpreRepository = ItbinumpreRepository::getInstance();
        $aItbinumpre = $itbinumpreRepository->getByGuia($this->numeroGuia);

        $this->setDados("aItbinumpre", $aItbinumpre);

        $this->buscarParametrosItbi();
        $this->buscarDadosItbi();
        $this->buscarDadosImovelItbi();
        $this->buscarDadosImovelIptu();
        $this->buscarTransmitentesAdquirentes();
        $this->buscaAdquirentesETransmitentes();

        if ($this->dados->bLiberado) {
            $this->buscarAvaliacao();
        }

        $this->buscarBenfeitorias();
        $this->buscarCaracteristicaImovelRural();
        $this->ajustaHistorico();
        $this->buscarSituacaoImovel();

        if (!empty($this->dados->oItbi->it06_matric)) {
            $iptuantRepository = new IptuantRepository();
            $oIptuant = $iptuantRepository->getByMatric($this->dados->oItbi->it06_matric);

            $this->setDados("oIptuant", $oIptuant);

            $iFracaoIdeal = fc_iptu_fracionalote($this->dados->oItbi->it06_matric, $this->anousu);

            $this->setDados("iFracaoIdeal", $iFracaoIdeal);
        }

        $departamentoRepository = new DepartamentoRepository();
        $oDepart = $departamentoRepository->getByCodigo($this->dados->oItbi->it01_coddepto);

        $this->setDados("oDepart", $oDepart);
    }

    /**
     * Busca os parâmetros do ITBI
     * @throws \Exception
     */
    private function buscarParametrosItbi()
    {
        $paritbiRepository = new ParitbiRepository();
        $oParitbi = $paritbiRepository->getByAnousu($this->anousu);

        $this->setDados("oParitbi", $oParitbi);

        $parreciboitbiRepository = new ParreciboitbiRepository();
        $oParreciboitbi = $parreciboitbiRepository->get();

        $this->setDados("oParreciboitbi", $oParreciboitbi);
    }

    /**
     * Busca todos os dados necessários do ITBI
     * @throws \Exception
     */
    private function buscarDadosItbi()
    {
        $itbiRepository = new ItbiRepository();
        $oItbi = $itbiRepository->getAllByGuia($this->numeroGuia, [
            "it06_matric", "it22_numero", "it22_setor", "it22_quadra", "it22_lote", "it22_matricri",
            "it22_quadrari", "it22_loteri", "j13_descr", "it05_frente", "it05_fundos", "it05_direito", "it05_esquerdo",
            "it04_descr", "it14_obs", "it01_obs", "it14_guia", "it14_dtliber", "it14_dtvenc", "it05_guia", "it07_descr",
            "it01_valorterreno", "it01_valorconstr", "it14_valoravalconstr", "it01_valortransacao", "it14_valoraval",
            "it14_valorpaga", "it18_distcidade", "it18_localimovel", "it22_descrlograd", "it01_processo",
            \DB::raw("(CASE WHEN LENGTH(TRIM(it18_nomelograd)) > 0 THEN 'Sim' ELSE 'Não' END) AS lFrenteVia"),
            \DB::raw("TRIM(it18_nomelograd) AS it18_nomelograd"),
            "it01_tituprocesso", "it01_dtprocesso", "it01_areaterreno", "it01_areatrans", "it14_valoravalter",
            "it18_frente", "it18_fundos", "it18_prof", "it22_compl", "it01_data", "it01_hora",
            "it01_coddepto", "it29_setorloc"
        ]);

        $this->setDados("oItbi", $oItbi);
        $this->setDados("bLiberado", !empty($oItbi->it14_guia));
    }

    /**
     * Busca todos os dados necessarios do imovel da guia de itbi
     * @throws \Exception
     */
    private function buscarDadosImovelItbi()
    {
        $itbiDadosImovelRepository = new ItbidadosimovelRepository();
        $oDadosImovel = $itbiDadosImovelRepository->getByGuia($this->numeroGuia);
        $this->setDados("oDadosImovel", $oDadosImovel);
    }

    /**
     * Busca todos os dados necessarios do imovel da iptubase
     * @throws \Exception
     */
    private function buscarDadosImovelIptu()
    {
        $oInformacoesImovel = null;
        if ($this->dados->oItbi->it06_matric) {
            $iptuBaseRepository = new IptubaseRepository();
            $oInformacoesImovel = $iptuBaseRepository->getInformacoesImovel(
                $this->dados->oItbi->it06_matric
            );
        }
        $this->setDados("oInformacoesImovel", $oInformacoesImovel);
    }

    /**
     * Busca os transmitentes e adquirentes
     * @throws \Exception
     */
    private function buscarTransmitentesAdquirentes()
    {
        $itbinomeRepository = new ItbinomeRepository();
        $aItbinome = $itbinomeRepository->getByGuia($this->numeroGuia, false, [
            "itbinome.*",
            "cgm.*"
        ]);

        $aTransmitentesSecundarios = [];
        $aAdquirentesSecundarios = [];

        foreach ($aItbinome as $oItbinome) {
            if ($oItbinome->it03_tipo == "T") {
                if ($oItbinome->it03_princ == "t") {
                    $this->setDados("oTransmitente", $oItbinome);
                } else {
                    $aTransmitentesSecundarios[] = $oItbinome->it03_nome;
                }
            } else {
                if ($oItbinome->it03_princ == "t") {
                    $this->setDados("oAdquirente", $oItbinome);
                } else {
                    $aAdquirentesSecundarios[] = $oItbinome->it03_nome;
                }
            }
        }

        $sOutrosTransmitentes = "";
        $sOutrosAdquirentes = "";
        $sAdquirentesSecundarios = "";
        $sTransmitentesSecundarios = "";

        if (count($aAdquirentesSecundarios) > 0) {
            $sOutrosAdquirentes = " e outro(s)... ";
            $sAdquirentesSecundarios = "- ADQUIRENTES: ";
            $sAdquirentesSecundarios .= implode(" - ", $aAdquirentesSecundarios);
        }

        if (count($aTransmitentesSecundarios) > 0) {
            $sOutrosTransmitentes = " e outro(s)... ";
            $sTransmitentesSecundarios = "- OUTRO(S) TRANSMITENTE(S): ";
            $sTransmitentesSecundarios .= implode(" - ", $aTransmitentesSecundarios);
        }

        $this->setDados("aTransmitentesSecundarios", $aTransmitentesSecundarios);
        $this->setDados("aAdquirentesSecundarios", $aAdquirentesSecundarios);

        $this->setDados("sAdquirentesSecundarios", $sAdquirentesSecundarios);
        $this->setDados("sTransmitentesSecundarios", $sTransmitentesSecundarios);

        $this->setDados("sOutrosTransmitentes", $sOutrosTransmitentes);
        $this->setDados("sOutrosAdquirentes", $sOutrosAdquirentes);
    }

    private function buscaAdquirentesETransmitentes()
    {
        $arrayAdquirentes = [];
        $arrayTransmitentes = [];

        $mapArray = function ($item) {
            if (isset($item->z01_numcgm)) {
                return $this->buscarCgm($item->z01_numcgm);
            } elseif (isset($item->numcgm)) {
                return $this->buscarCgm($item->numcgm);
            }
        };

        if (isset($this->dados->oAdquirente)) {
            $arrayAdquirentes = array_merge($arrayAdquirentes, [$this->dados->oAdquirente]);
        }

        if (isset($this->dados->oTransmitente)) {
            $arrayTransmitentes = array_merge($arrayTransmitentes, [$this->dados->oTransmitente]);
        }

        $arrayAdquirentes = array_merge($arrayAdquirentes, $this->dados->aAdquirentesSecundarios);
        $arrayTransmitentes = array_merge($arrayTransmitentes, $this->dados->aTransmitentesSecundarios);
        
        $arrayAdquirentes = array_map($mapArray, $arrayAdquirentes);
        $arrayTransmitentes = array_map($mapArray, $arrayTransmitentes);

        $arrayAdquirentes = array_filter($arrayAdquirentes);
        $arrayTransmitentes = array_filter($arrayTransmitentes);

        $this->setDados("arrayAdquirentes", $arrayAdquirentes);
        $this->setDados("arrayTransmitentes", $arrayTransmitentes);
    }

    private function buscarCgm($cgm)
    {
        $oDaoCgm = new cl_cgm();
        $sSqlBuscaCgm = $oDaoCgm->sql_query_file($cgm, '*', null, "z01_numcgm = $cgm");
        $rsBuscaCgm = db_query($sSqlBuscaCgm);
        return db_utils::getCollectionByRecord($rsBuscaCgm)[0];
    }

    /**
     * Busca a avaliação do ITBI
     * @throws \Exception
     */
    private function buscarAvaliacao()
    {
        $itbiavaliaRepository = new ItbiavaliaRepository;

        $aItbiAvalia = $itbiavaliaRepository->getAllDadosByGuia($this->numeroGuia, [
            "it27_descricao",
            "it27_aliquota",
            "it28_descricao",
            "it24_valor",
            "it04_desconto"
        ]);

        $nTotalDesconto = 0;
        $sFormaPagamento = "I S E N T O";

        $aItbiAvalia = array_map(function ($oItbiAvalia) use (&$nTotalDesconto, &$sFormaPagamento) {
            $nValorImposto = $oItbiAvalia->it24_valor * ($oItbiAvalia->it27_aliquota / 100);
            $nDescImposto  = $nValorImposto * ($oItbiAvalia->it04_desconto / 100);
            $nTotalImposto = $nValorImposto - $nDescImposto;

            if ($nTotalImposto >= 0) {
                $nTotalDesconto += $nDescImposto;
                $sFormaPagamento = $oItbiAvalia->it28_descricao;

                return [
                    "Descricao" => $oItbiAvalia->it27_descricao,
                    "Aliquota" => $oItbiAvalia->it27_aliquota,
                    "Valor" => $oItbiAvalia->it24_valor,
                    "Imposto" => $nTotalImposto
                ];
            }
        }, $aItbiAvalia);

        if (count($aItbiAvalia) < 2) {
            $aItbiAvalia[] = [
                "Descricao" => "",
                "Aliquota" => "",
                "Valor" => "",
                "Imposto" => ""
            ];
        }

        $this->setDados("desconto_abatimento", $nTotalDesconto);
        $this->setDados("sFormaPagamento", $sFormaPagamento);
        $this->setDados("aDadosFormasPgto", $aItbiAvalia);
    }

    /**
     * Busca as benfeitorias do ITBI
     */
    private function buscarBenfeitorias()
    {
        $itbiconstrRepository = new ItbiconstrRepository;
        $aItbiconstr = $itbiconstrRepository->getByGuia($this->numeroGuia);

        $aConstrEspecie = [];
        $aConstrTipo = [];
        $aConstrArea = [];
        $aConstrAreatrans = [];
        $aConstrAno = [];
        $fAreaTrans = 0;
        $fAreaTotal = 0;

        foreach ($aItbiconstr as $key => $oItbiconstr) {
            $aConstrEspecie[$key] = $oItbiconstr->carconstrespecie;
            $aConstrTipo[$key] = $oItbiconstr->caritbiconstrtipo;
            $aConstrArea[$key] = $oItbiconstr->it08_area;
            $aConstrAreatrans[$key] = $oItbiconstr->it08_areatrans;
            $aConstrAno[$key] = $oItbiconstr->it08_ano;

            $fAreaTrans += $oItbiconstr->it08_areatrans;
            $fAreaTotal += $oItbiconstr->it08_area;
        }

        $oBenfeitorias = new \stdClass();
        $oBenfeitorias->aConstrEspecie = $aConstrEspecie;
        $oBenfeitorias->aConstrTipo = $aConstrTipo;
        $oBenfeitorias->aConstrArea = $aConstrArea;
        $oBenfeitorias->aConstrAreatrans = $aConstrAreatrans;
        $oBenfeitorias->aConstrAno = $aConstrAno;
        $oBenfeitorias->iQtdCons = count($aItbiconstr);
        $oBenfeitorias->fAreaTrans = $fAreaTrans;
        $oBenfeitorias->fAreaTotal = $fAreaTotal;

        $this->setDados("oBenfeitorias", $oBenfeitorias);
    }

    /**
     * Busca as caracteristicas do imóvel rural
     */
    private function buscarCaracteristicaImovelRural()
    {
        $itbiruralcaractRepository = new ItbiruralcaractRepository;
        $aItbiruralcaract = $itbiruralcaractRepository->getByGuia($this->numeroGuia);

        $aDadosRuralCaractUtil = [];
        $aDadosRuralCaractDist = [];

        foreach ($aItbiruralcaract as $oItbiruralcaract) {
            $oCaract = [
                "Descricao" => $oItbiruralcaract->j31_descr,
                "Valor" => $oItbiruralcaract->it19_valor
            ];

            if ($oItbiruralcaract->it19_tipocaract == 1) {
                $aDadosRuralCaractDist[] = $oCaract;
            } else {
                $aDadosRuralCaractUtil[] = $oCaract;
            }
        }

        $this->setDados("aDadosRuralCaractDist", $aDadosRuralCaractDist);
        $this->setDados("aDadosRuralCaractUtil", $aDadosRuralCaractUtil);
    }

    /**
     * Ajusta o texto do histórico do recibo
     */
    private function ajustaHistorico()
    {
        $oItbi = $this->dados->oItbi;

        $sHistorico = "\nITBI Nº {$this->numeroGuia}/{$this->anousu}";

        if (!empty($oItbi->it05_guia)) {
            $sHistorico .= "    Tipo: Urbano";

            $sSetQuaLot = "{$oItbi->it22_setor}/{$oItbi->it22_quadra}/{$oItbi->it22_lote}";
            $sHistorico .= "\nSetor/Quadra/Lote: {$sSetQuaLot}";
        } else {
            $sHistorico .= "\nTipo: Rural";
        }

        $sComplemento = (!empty($oItbi->it22_compl) ? "/{$oItbi->it22_compl}" : "");

        $sHistorico .= "\nEndereço: {$oItbi->it22_descrlograd} {$oItbi->it22_numero} {$sComplemento}";
        $sHistorico .= "\nBairro: {$oItbi->j13_descr}";
        $sHistorico .= "\nTransmitente: {$this->dados->oTransmitente->z01_nome}";
        $sHistorico .= "\n";

        $this->setDados("sHistorico", $sHistorico);
    }

    /**
     * Busca a situação do imóvel na data da geração do recibo
     */
    private function buscarSituacaoImovel()
    {
        $sMsgSituacaoImovel = "";

        if ($this->dados->oItbi->it06_matric) {
            $numprefRepository = new NumprefRepository();
            $oNumpref = $numprefRepository->getByAno($this->anousu, [
                "k03_regracnd"
            ]);

            $sCertidao = fc_tipocertidaomatricula(
                $this->dados->oItbi->it06_matric,
                "m",
                $this->datausu,
                $oNumpref->k03_regracnd
            );

            switch ($sCertidao) {
                case 'positiva':
                    $sMsgSituacaoImovel = 'IMÓVEL COM DÉBITOS PENDENTES NESTA DATA';
                    break;
                default:
                    $sMsgSituacaoImovel = 'IMÓVEL EM DIA NESTA DATA';
            }
        }

        $this->setDados("sMsgSituacaoImovel", $sMsgSituacaoImovel);
    }

    /**
     * Popula as tabelas de recibo
     * @return \Recibo
     * @throws \Exception
     */
    private function geraRecibo()
    {
        $oItbi = $this->dados->oItbi;

        $oRecibo = new \Recibo(1, $this->dados->oAdquirente->z01_numcgm);
        $oRecibo->setDataVencimentoRecibo($oItbi->it14_dtvenc);
        $oRecibo->setCodigoTipo(29);

        $aReceitas = $this->buscaReceitas();

        foreach ($aReceitas as $oReceita) {
            $oRecibo->adicionarReceita(
                $oReceita->receita,
                $oReceita->valor,
                0,
                null,
                $oReceita->historico,
                null,
                ($oReceita->historico == 707 && $oReceita->valor == 0)
            );
        }

        $oRecibo->setHistorico($this->dados->sHistorico);

        $iCgm = $this->dados->oAdquirente->z01_numcgm;

        if (empty($iCgm)) {
            $iCgm = $this->dados->oParreciboitbi->it17_numcgm;
        }

        $oRecibo->setVinculoCgm($iCgm);

        // Removido vilculo do boleto com a matricula.
        //
        // Arrenumcgm possui uma trigger que é acionada na insercao e
        // modifica a arrenumcgm, vinculando o boleto ao transmitente.
        //
        // if (!empty($oItbi->it06_matric)) {
        //     $oRecibo->setMatricula($oItbi->it06_matric);
        // }

        $oRecibo->emiteRecibo($this->regraEmissao->isCobranca(), true, $this->regraEmissao->getConvenio());

        $this->setDados("iNumpre", $oRecibo->getNumpreRecibo());
        $this->setDados("aTaxas2", $aReceitas);
        array_shift($aReceitas);
        $this->setDados("aTaxas", $aReceitas);

        return $oRecibo;
    }

    /**
     * Busca as receitas que devem ser adicionadas ao recibo
     * @return array
     * @throws \Exception
     */
    private function buscaReceitas()
    {
        $aReceitas[] = (object) [
            "descricao" => "ITBI",
            "receita" => $this->dados->oParreciboitbi->it17_codigo,
            "valor" => $this->dados->oItbi->it14_valorpaga,
            "historico" => 707
        ];

        $itbitaxasavaliaRepository = ItbitaxasavaliaRepository::getInstance();
        $itbitaxasavalia = new Itbitaxasavalia();
        $itbitaxasavalia->setGuia($this->numeroGuia);

        $aTaxas = $itbitaxasavaliaRepository->getDadosTaxas($itbitaxasavalia);

        foreach ($aTaxas as $oTaxa) {
            if ($oTaxa->it39_aliquota != "") {
                if ($oTaxa->it39_calculasobre == 1) {
                    $calculaSobre = "Terreno";
                } elseif ($oTaxa->it39_calculasobre == 2) {
                    $calculaSobre = "Construção";
                } elseif ($oTaxa->it39_calculasobre == 3) {
                    $calculaSobre = "Ambos";
                }
            } else {
                $calculaSobre = "Valor Fixo";
            }

            $aReceitas[] = (object) [
                "descricao" => $oTaxa->ar44_descricao,
                "calculaSobre" => $calculaSobre,
                "aliquota" => $oTaxa->it39_aliquota,
                "valor" => $oTaxa->it39_valor,
                "receita" => $oTaxa->ar44_receita,
                "historico" => 707
            ];

            $this->dados->oItbi->it14_valorpaga += $oTaxa->it39_valor;
        }

        return $aReceitas;
    }

    /**
     * Faz a cobrança registrada do recibo
     * @param $oRecibo
     * @throws \Exception
     */
    private function geraCobrancaRegistrada($oRecibo)
    {
        $oItbi = $this->dados->oItbi;

        try {
            $lConvenioCobrancaValido = CobrancaRegistrada::validaConvenioCobranca(
                $this->regraEmissao->getConvenio()
            );

            $lExisteValor = ($oItbi->it14_valorpaga != "0" || $oItbi->it14_valorpaga != 0);

            if ($lConvenioCobrancaValido
                &&
                !CobrancaRegistrada::utilizaIntegracaoWebService($this->regraEmissao->getConvenio())
                &&
                $lExisteValor
            ) {
                CobrancaRegistrada::adicionarRecibo($oRecibo, $this->regraEmissao->getConvenio());
            } else {
                if ($lConvenioCobrancaValido
                    &&
                    CobrancaRegistrada::utilizaIntegracaoWebService($this->regraEmissao->getConvenio())
                    &&
                    $lExisteValor
                ) {
                    CobrancaRegistrada::registrarReciboWebservice(
                        $this->dados->iNumpre,
                        $this->regraEmissao->getConvenio(),
                        $oItbi->it14_valorpaga
                    );
                }
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * Ajusta os dados da tabela itbinumpre para a ITBI selecionada
     */
    private function ajustaItbinumpre()
    {
        $itbinumpreRepository = ItbinumpreRepository::getInstance();
        $itbinumpre = new Itbinumpre();

        $itbinumpreRepository->atualizaUltimaGuia($this->numeroGuia);

        $itbinumpre->setGuia($this->numeroGuia);
        $itbinumpre->setNumpre($this->dados->iNumpre);
        $itbinumpre->setSequencial(null);
        $itbinumpre->setUltimaguia("t");
        $itbinumpreRepository->salvar($itbinumpre);
    }

    /**
     * Carrega os dados do convênio
     * @throws \Exception
     */
    private function carregaConvenio()
    {
        $oItbi = $this->dados->oItbi;

        try {
            $fValorCodBarras = valorCodigoBarras($oItbi->it14_valorpaga);

            $this->convenio = new convenio(
                $this->regraEmissao->getConvenio(),
                $this->dados->iNumpre,
                1,
                $oItbi->it14_valorpaga,
                $fValorCodBarras,
                $oItbi->it14_dtvenc,
                6
            );
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * Realiza a geracao do pix para a guia
     * @param Recibo $oRecibo
     * @throws \Exception
     */
    private function geraPix($oRecibo)
    {
        $servicePixItbiArrecadao = new ArrecadacaoPixItbiService();
        $servicePixItbiArrecadao->setConvenio($this->convenio);
        $servicePixItbiArrecadao->setRegraEmissao($this->regraEmissao);
        $servicePixItbiArrecadao->setVencimento($this->dados->oItbi->it14_dtvenc);
        $servicePixItbiArrecadao->setCodigoArrecadacao($this->dados->iNumpre);
        $servicePixItbiArrecadao->setRecibo($oRecibo);
        $servicePixItbiArrecadao->setTipoDebito(29);
        $servicePixItbiArrecadao->setValorRecibo($this->dados->oItbi->it14_valorpaga);
        $servicePixItbiArrecadao->setNumpreRecibo($this->dados->iNumpre);
        $servicePixItbiArrecadao->setCgm($this->dados->oAdquirente->z01_numcgm);
        $servicePixItbiArrecadao->gerarPix();

        $codigobarras = $servicePixItbiArrecadao->getCodigoBarras();
        $this->setDados("codigoDeBarrasPix", $codigobarras);
    }
}
