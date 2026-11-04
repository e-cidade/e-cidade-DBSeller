<?php

namespace App\Domain\Tributario\ITBI\Services;

use App\Domain\Patrimonial\Protocolo\Model\Processo\Processo;
use App\Domain\Patrimonial\Protocolo\Repository\Processo\ProcessoRepository;
use App\Domain\Tributario\Cadastro\Repositories\PropriRepository;
use App\Domain\Tributario\ITBI\Models\Itbi;
use App\Domain\Tributario\ITBI\Models\Itbiconstr;
use App\Domain\Tributario\ITBI\Models\Itbiconstrespecie;
use App\Domain\Tributario\ITBI\Models\Itbiconstrpadraoconstrutivo;
use App\Domain\Tributario\ITBI\Models\Itbiconstrtipo;
use App\Domain\Tributario\ITBI\Models\Itbidadosimovel;
use App\Domain\Tributario\ITBI\Models\Itbidadosimovelsetorloc;
use App\Domain\Tributario\ITBI\Models\Itbiformapagamentovalor;
use App\Domain\Tributario\ITBI\Models\Itbiintermediador;
use App\Domain\Tributario\ITBI\Models\Itbilocalidaderural;
use App\Domain\Tributario\ITBI\Models\Itbilogin;
use App\Domain\Tributario\ITBI\Models\Itbimatric;
use App\Domain\Tributario\ITBI\Models\Itbinome;
use App\Domain\Tributario\ITBI\Models\Itbinomecgm;
use App\Domain\Tributario\ITBI\Models\Itbipropriold;
use App\Domain\Tributario\ITBI\Models\Itbirural;
use App\Domain\Tributario\ITBI\Models\Itbiruralcaract;
use App\Domain\Tributario\ITBI\Models\Itburbano;
use App\Domain\Tributario\ITBI\Repositories\ItbiconstrespecieRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbiconstrpadraoconstrutivoRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbiconstrRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbiconstrtipoRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbidadosimovelRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbidadosimovelsetorlocRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbiformapagamentovalorRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbiintermediadorRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbilocalidaderuralRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbiloginRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbimatricRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbinomecgmRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbinomeRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbiproprioldRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbiRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbiruralcaractRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbiruralRepository;
use App\Domain\Tributario\ITBI\Repositories\ItburbanoRepository;
use ECidade\Lib\Session\DefaultSession;
use ECidade\Tributario\ITBI\Model\Itbitaxasitbi;
use ECidade\Tributario\ITBI\Repository\ItbitaxasitbiRepository;

class LancamentoItbiService
{
    private $secoes;

    private $numeroGuia;

    private $departamento;

    private $defaultSession;

    /**
     * @var Processo
     */
    private $processo;

    public function __construct()
    {
        $this->defaultSession = DefaultSession::getInstance();
    }

    public function setDepartamento($departamento)
    {
        $this->departamento = $departamento;
        return $this;
    }

    /**
     * @param mixed $secoes
     * @return LancamentoItbiService
     */
    public function setSecoes($secoes)
    {
        $this->secoes = $secoes;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumeroGuia()
    {
        return $this->numeroGuia;
    }

    /**
     * @param Processo $processo
     * @return LancamentoItbiService
     */
    public function setProcesso(Processo $processo)
    {
        $this->processo = $processo;
        return $this;
    }

    /**
     * @throws \Exception
     */
    public function lancar()
    {
        $this->lancarDadosImovel($this->secoes->dadosImovel);
        $this->lancarIntermediadores($this->secoes->intermediadores);
        $aTransmitentes = $this->ajustaTransmitentes($this->secoes->transmitentes);
        $aAdquirentes = $this->ajustaAdquirentes($this->secoes->adquirentes);
        $this->lancarTransmitenteAdquirente($aTransmitentes, $aAdquirentes);
        $this->lancarBenfeitorias($this->secoes->benfeitorias);
    }

    private function removeMascaraValorMonetario($valor)
    {
        $valor = str_replace(".", "", $valor);
        return str_replace(",", ".", $valor);
    }

    private function lancarDadosImovel($dadosImovel)
    {
        /**
         * @todo verificar usuário
         * */
        $this->inserirGuiaItbi($dadosImovel);

        /**
         * Insere qual tipo de taxa foi usada
         * */
        if (!empty($dadosImovel->tipoTaxa)) {
            $itbitaxasitbiRepository = ItbitaxasitbiRepository::getInstance();
            $itbitaxasitbi = new Itbitaxasitbi();

            $itbitaxasitbi->setSequencial(null);
            $itbitaxasitbi->setItbi($this->numeroGuia);
            $itbitaxasitbi->setTaxasitbi($dadosImovel->tipoTaxa);

            $itbitaxasitbiRepository->persist($itbitaxasitbi);
        }

        if (isset($dadosImovel->matricula)) {
            $this->inserirDadosItbiUrbano($dadosImovel);
        } else {
            $this->inserirDadosItbiRural($dadosImovel);
        }

        /**
         * @todo verificar usuário
         * Insere o usuário que gerou a guia
         * */
        $itbiloginRepository = new ItbiloginRepository();
        $itbilogin = new Itbilogin();

        $itbilogin->setGuia($this->numeroGuia);
        $itbilogin->setIdUsuario(1);

        $itbiloginRepository->inserir($itbilogin);

        $this->inserirDadosRegistroImovel($dadosImovel);
        $this->inserirFormasPagamento($dadosImovel);
    }

    /**
     * @throws \Exception
     */
    private function inserirGuiaItbi($dadosImovel)
    {
        $itbi = new Itbi();
        $itbiRepository = new ItbiRepository();

        $itbi->setGuia(null)
             ->setData(date("Y-m-d", $this->defaultSession->get(DefaultSession::DB_DATAUSU)))
             ->setHora(db_hora())
             ->setTipotransacao($dadosImovel->tipoTransacao)
             ->setAreaterreno($dadosImovel->areaTotal)
             ->setAreaedificada("0")
             ->setObs(isset($dadosImovel->observacoes) ? $dadosImovel->observacoes : "")
             ->setValortransacao($this->removeMascaraValorMonetario($dadosImovel->valorTotal))
             ->setAreatrans($dadosImovel->areaTrans)
             ->setMail($dadosImovel->emailContato)
             ->setFinalizado("f")
             ->setOrigem(2)
             ->setIdUsuario(1)
             ->setCoddepto($this->departamento)
             ->setValorterreno($this->removeMascaraValorMonetario($dadosImovel->valorTerreno))
             ->setValorconstr($this->removeMascaraValorMonetario($dadosImovel->valorBenfeitorias))
             ->setEnvia("t")
             ->setPercentualareatransmitida($dadosImovel->percAreaTrans)
             ->setNotificado("t")
             ->setProcesso("{$this->processo->getNumero()}/{$this->processo->getAno()}")
             ->setProcessoSequencial($this->processo->getCodigoProcesso())
             ->setTituprocesso(null)
             ->setDtprocesso(null)
             ->setCartorioextra((isset($dadosImovel->cartorio) ? $dadosImovel->cartorio : null));

        $this->numeroGuia = $itbiRepository->salvar($itbi);
    }

    private function inserirDadosItbiUrbano($dadosImovel)
    {
        $itburbanoRepository = new ItburbanoRepository();
        $itburbano = new Itburbano();

        $itburbano->setGuia($this->numeroGuia);
        $itburbano->setFrente($dadosImovel->frente);
        $itburbano->setFundos($dadosImovel->fundos);
        $itburbano->setDireito($dadosImovel->ladoDireito);
        $itburbano->setEsquerdo($dadosImovel->ladoEsquerdo);
        $itburbano->setItbisituacao($dadosImovel->situacao);

        $itburbanoRepository->incluir($itburbano);

        $itbimatricRepository = new ItbimatricRepository();
        $itbimatric = new Itbimatric();

        $itbimatric->setGuia($this->numeroGuia);
        $itbimatric->setMatric($dadosImovel->matricula);

        $itbimatricRepository->incluir($itbimatric);

        /**
         * Salva os proprietários anteriores
         * */
        $propriRepository = new PropriRepository();
        $aPropri = $propriRepository->getAllByMatricula($dadosImovel->matricula);

        if (count($aPropri) > 0) {
            $itbipropriold = new Itbipropriold();
            $itbiproprioldRepository = new ItbiproprioldRepository();

            $itbipropriold->setGuia($this->numeroGuia);
            $itbipropriold->setPri("f");

            foreach ($aPropri as $oPropri) {
                $itbipropriold->setNumcgm($oPropri->j42_numcgm);
                $itbiproprioldRepository->inserir($itbipropriold);
            }

            $itbipropriold->setPri("t");
            $itbipropriold->setNumcgm($oPropri->j01_numcgm);
            $itbiproprioldRepository->inserir($itbipropriold);
        }
    }

    private function inserirDadosRegistroImovel($dadosImovel)
    {
        $itbidadosimovel = new Itbidadosimovel();
        $itbidadosimovelRepository = new ItbidadosimovelRepository();

        $itbidadosimovel->setSequencial(null);
        $itbidadosimovel->setItbi($this->numeroGuia);
        $itbidadosimovel->setSetor($dadosImovel->setorBairro);
        $itbidadosimovel->setQuadra($dadosImovel->quadra);
        $itbidadosimovel->setLote($dadosImovel->lote);
        $itbidadosimovel->setDescrlograd($dadosImovel->logradouro);
        $itbidadosimovel->setNumero($dadosImovel->numero);
        $itbidadosimovel->setCompl((!empty($dadosImovel->complemento)) ? $dadosImovel->complemento : "");
        $itbidadosimovel->setMatricri($dadosImovel->matriculaRi);
        $itbidadosimovel->setQuadrari($dadosImovel->quadraRi);
        $itbidadosimovel->setLoteri($dadosImovel->loteRi);

        $sequencial = $itbidadosimovelRepository->salvar($itbidadosimovel);

        if (isset($dadosImovel->setorRi) && !empty($dadosImovel->setorRi)) {
            $itbidadosimovelsetorloc = new Itbidadosimovelsetorloc();
            $itbidadosimovelsetorlocRepository = new ItbidadosimovelsetorlocRepository();

            $itbidadosimovelsetorloc->setSequencial(null);
            $itbidadosimovelsetorloc->setItbidadosimovel($sequencial);
            $itbidadosimovelsetorloc->setSetorloc($dadosImovel->setorRi);

            $itbidadosimovelsetorlocRepository->salvar($itbidadosimovelsetorloc);
        }
    }

    private function inserirFormasPagamento($dadosImovel)
    {
        $itbiformapagamentovalorRepository = new ItbiformapagamentovalorRepository();
        $itbiformapagamentovalor = new Itbiformapagamentovalor();

        $itbiformapagamentovalor->setSequencial(null);
        $itbiformapagamentovalor->setGuia($this->numeroGuia);

        foreach ($dadosImovel->formasPagamento as $formaPagamento) {
            $itbiformapagamentovalor->setItbitransacaoformapag($formaPagamento->codigo);
            $itbiformapagamentovalor->setValor($this->removeMascaraValorMonetario($formaPagamento->valor));

            $itbiformapagamentovalorRepository->salvar($itbiformapagamentovalor);
        }
    }

    private function inserirDadosItbiRural($dadosImovel)
    {
        $itbiruralRepository = new ItbiruralRepository();
        $itbirural = new Itbirural();

        $itbirural->setGuia($this->numeroGuia);
        $itbirural->setFrente($dadosImovel->frente);
        $itbirural->setFundos($dadosImovel->fundos);
        $itbirural->setProf($dadosImovel->profundidade);
        $itbirural->setLocalimovel($dadosImovel->localizacao);
        $itbirural->setDistcidade($dadosImovel->distanciaCidade);
        $itbirural->setNomelograd(isset($dadosImovel->nomeLograd) ? $dadosImovel->nomeLograd : "");
        $itbirural->setArea($dadosImovel->areaTotal);
        $itbirural->setCoordenadas($dadosImovel->coordenadas);

        $itbiruralRepository->inserir($itbirural);

        $itbilocalidaderuralRepository = new ItbilocalidaderuralRepository();
        $itbilocalidaderural = new Itbilocalidaderural();

        if (!empty($dadosImovel->localidade)) {
            $itbilocalidaderural->setSequencial(null);
            $itbilocalidaderural->setGuia($this->numeroGuia);
            $itbilocalidaderural->setLocalidaderural($dadosImovel->localidade);

            $itbilocalidaderuralRepository->salvar($itbilocalidaderural);
        }

        $itbiruralcaractRepository = new ItbiruralcaractRepository();
        $itbiruralcaract = new Itbiruralcaract();

        $itbiruralcaract->setGuia($this->numeroGuia);

        $aCaracter = array_merge($dadosImovel->caractImovel, $dadosImovel->caractUtilImovel);

        foreach ($aCaracter as $caracter) {
            if (!isset($caracter->value)) {
                continue;
            }

            $itbiruralcaract->setCodigo($caracter->codigo);
            $itbiruralcaract->setValor($caracter->value);
            $itbiruralcaract->setTipocaract($caracter->tipo);

            $itbiruralcaractRepository->inserir($itbiruralcaract);
        }
    }

    private function ajustaTransmitentes($transmitentes)
    {
        if (count($transmitentes) == 0) {
            throw new \Exception("Nenhum transmitente informado.");
        }

        return array_map(function ($oTransmitente) {
            $oTransmitente->tipo = "T";
            return $oTransmitente;
        }, $transmitentes);
    }

    private function lancarIntermediadores($intermediadores)
    {
        $itbiintermediadorRepository = new ItbiintermediadorRepository();
        $itbiintermediador = new Itbiintermediador();

        foreach ($intermediadores as $intermediador) {
            $itbiintermediador->setSequencial(null);
            $itbiintermediador->setItbi($this->numeroGuia);
            $itbiintermediador->setCgm((isset($intermediador->numcgm) ? $intermediador->numcgm : null));
            $itbiintermediador->setNome($intermediador->nome);
            $itbiintermediador->setCnpjCpf($intermediador->cpfCnpj);
            $itbiintermediador->setCreci((isset($intermediador->creci) ? $intermediador->creci : null));
            $itbiintermediador->setPrincipal(($intermediador->principal ? "t" : "f"));

            $itbiintermediadorRepository->salvar($itbiintermediador);
        }
    }

    private function ajustaAdquirentes($adquirentes)
    {
        if (count($adquirentes) == 0) {
            throw new \Exception("Nenhum adquirente informado.");
        }

        return array_map(function ($oAdquirente) {
            $oAdquirente->tipo = "C";
            return $oAdquirente;
        }, $adquirentes);
    }

    private function lancarTransmitenteAdquirente($aTransmitentes, $aAdquirentes)
    {
        $itbinomeRepository = new ItbinomeRepository();
        $itbinome = new Itbinome();

        $itbinomecgmRepository = new ItbinomecgmRepository();
        $itbinomecgm = new Itbinomecgm();

        $aTransmitentesAdquirentes = array_merge($aTransmitentes, $aAdquirentes);

        foreach ($aTransmitentesAdquirentes as $oDados) {
            $itbinome->setSeq(null);
            $itbinome->setGuia($this->numeroGuia);
            $itbinome->setTipo(substr($oDados->tipo, 0, 1));
            $itbinome->setPrinc(($oDados->principal == 1 ? "t" : "f"));
            $itbinome->setNome(substr($oDados->nome, 0, 40));
            $itbinome->setSexo((!empty(trim($oDados->sexo)) ? $oDados->sexo : "F"));
            $itbinome->setCpfcnpj(substr($oDados->cpfCnpj, 0, 14));
            $itbinome->setEndereco(substr($oDados->endereco, 0, 100));
            $itbinome->setNumero($oDados->numero);
            $itbinome->setCompl((!empty($oDados->complemento)) ? substr($oDados->complemento, 0, 100) : "");
            $itbinome->setCxpostal((!empty($oDados->caixaPostal)) ? substr($oDados->caixaPostal, 0, 20) : "");
            $itbinome->setBairro(substr($oDados->bairro, 0, 40));
            $itbinome->setMunic(substr($oDados->municipio, 0, 40));
            $itbinome->setUf(substr($oDados->uf, 0, 2));
            $itbinome->setCep(substr($oDados->cep, 0, 8));
            $itbinome->setMail(substr($oDados->email, 0, 50));

            $sequencial = $itbinomeRepository->salvar($itbinome);

            if (!empty($oDados->numcgm)) {
                $oCgm = \CgmFactory::getInstanceByCgm($oDados->numcgm);
            } else {
                $oCgm = \CgmFactory::getInstanceByCnpjCpf(trim(
                    str_replace([".", "-", ""], "", $oDados->cpfCnpj)
                ));
            }

            if (empty($oCgm)) {
                if (strlen(trim($oDados->cpfCnpj)) == '11') {
                    $oCgm = \CgmFactory::getInstanceByType(\CgmFactory::FISICO);
                    $oCgm->setCpf($oDados->cpfCnpj);
                } else {
                    if (strlen(trim($oDados->cpfCnpj)) == '14') {
                        $oCgm = \CgmFactory::getInstanceByType(\CgmFactory::JURIDICO);
                        $oCgm->setCnpj($oDados->cpfCnpj);
                    }
                }
            }

            $oCgm->setNome(mb_strtoupper($oDados->nome));
            $oCgm->setNomeCompleto(mb_strtoupper($oDados->nome));
            $oCgm->setUf(mb_strtoupper($oDados->uf));
            $oCgm->setMunicipio(mb_strtoupper($oDados->municipio));
            $oCgm->setCep(mb_strtoupper($oDados->cep));
            $oCgm->setBairro(mb_strtoupper($oDados->bairro));
            $oCgm->setNumero(mb_strtoupper($oDados->numero));
            $oCgm->setLogradouro(mb_strtoupper($oDados->endereco));
            $oCgm->setComplemento(mb_strtoupper((!empty($oDados->complemento)) ? $oDados->complemento : ""));
            $oCgm->save();

            $itbinomecgm->setSequencial(null);
            $itbinomecgm->setItbinome($sequencial);
            $itbinomecgm->setNumcgm($oCgm->getCodigo());

            $itbinomecgmRepository->salvar($itbinomecgm);

            if ($oDados->tipo == "C" && $oDados->principal == 1) {
                $processo = new Processo();
                $processo->setCodigoProcesso($this->processo->getCodigoProcesso());
                $processo->setInterno(false);
                $processo->setPublico(true);
                $processo->setCgm($oCgm->getCodigo());

                $processoRepository = new ProcessoRepository(new \cl_protprocesso);
                $processoRepository->persist($processo);
            }
        }
    }

    private function lancarBenfeitorias($benfeitorias)
    {
        $nTotalAreaEdificada = 0;

        if (count($benfeitorias) > 0) {
            $itbiconstrRepository = new ItbiconstrRepository();
            $itbiconstr = new Itbiconstr();

            $itbiconstrtipoRepository = new ItbiconstrtipoRepository();
            $itbiconstrtipo = new Itbiconstrtipo();

            $itbiconstrespecieRepository = new ItbiconstrespecieRepository();
            $itbiconstrespecie = new Itbiconstrespecie();

            $itbiconstrpadraoconstrutivoRepository = new ItbiconstrpadraoconstrutivoRepository();
            $itbiconstrpadraoconstrutivo = new Itbiconstrpadraoconstrutivo();
        }

        foreach ($benfeitorias as $benfeitoria) {
            $nTotalAreaEdificada += floatval($benfeitoria->areaTrans);

            $itbiconstr->setCodigo(null);
            $itbiconstr->setGuia($this->numeroGuia);
            $itbiconstr->setArea($benfeitoria->area);
            $itbiconstr->setAreatrans($benfeitoria->areaTrans);
            $itbiconstr->setAno($benfeitoria->ano);
            $itbiconstr->setObs(isset($benfeitoria->observacoes) ? $benfeitoria->observacoes : "");
            $itbiconstr->setCoordenadas((isset($benfeitoria->coordenadas) ? $benfeitoria->coordenadas : null));
            $sequencial = $itbiconstrRepository->salvar($itbiconstr);

            if (isset($benfeitoria->tipo) && !empty($benfeitoria->tipo)) {
                $itbiconstrtipo->setCodigo($sequencial);
                $itbiconstrtipo->setCaract($benfeitoria->tipo);
                $itbiconstrtipoRepository->inserir($itbiconstrtipo);
            }

            if (isset($benfeitoria->especie) && !empty($benfeitoria->especie)) {
                $itbiconstrespecie->setCodigo($sequencial);
                $itbiconstrespecie->setCaract($benfeitoria->especie);
                $itbiconstrespecieRepository->inserir($itbiconstrespecie);
            }

            if (isset($benfeitoria->padraoConstrutivo) && !empty($benfeitoria->padraoConstrutivo)) {
                $itbiconstrpadraoconstrutivo->setCodigo($sequencial);
                $itbiconstrpadraoconstrutivo->setCaract($benfeitoria->padraoConstrutivo);
                $itbiconstrpadraoconstrutivoRepository->inserir($itbiconstrpadraoconstrutivo);
            }
        }

        if ($nTotalAreaEdificada > 0) {
            $itbiRepository = new ItbiRepository();
            $itbi = new Itbi();

            $itbi->setGuia($this->numeroGuia);
            $itbi->setAreaedificada($nTotalAreaEdificada);

            $itbiRepository->salvar($itbi);
        }
    }
}
