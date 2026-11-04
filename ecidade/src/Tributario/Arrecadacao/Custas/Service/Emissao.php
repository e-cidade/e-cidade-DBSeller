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

namespace ECidade\Tributario\Arrecadacao\Custas\Service;

use BusinessException;
use CgmBase;
use CgmRepository;
use cl_iptubase;
use cl_issbase;
use convenio;
use DateTime;
use DBDate;
use ECidade\Tributario\Arrecadacao\CobrancaRegistrada\CobrancaRegistrada;
use ECidade\Tributario\Arrecadacao\Custas\Enum\TipoModelo as TipoModeloCustas;
use ECidade\Tributario\Arrecadacao\Custas\Interfaces\Service;
use ECidade\Tributario\Arrecadacao\Custas\Service\Recibo as ReciboCustasService;
use ECidade\Tributario\Arrecadacao\Proprietario;
use ECidade\Tributario\Arrecadacao\Repository\RegraEmissaoReciboCustaRepository;
use ECidade\Tributario\Cadastro\Model\Iptubase;
use ECidade\Tributario\Cadastro\Repository\IptubaseRepository;
use ECidade\Tributario\Caixa\Entity\Recibo;
use ECidade\Tributario\Caixa\Enum\Cadtipomod;
use ECidade\Tributario\Divida\Termo\Repository\Termo;
use ECidade\Tributario\Divida\Termo\Repository\TermoInicial;
use ECidade\Tributario\Issqn\Model\Issbase;
use ECidade\Tributario\Issqn\Repository\IssbaseRepository;
use ECidade\Tributario\Juridico\Parametro\Repository\ParametroRepository as ParametroJuridico;
use ECidade\Tributario\Library\DataBase;
use ECidade\V3\Extension\Registry;
use Exception;
use stdClass;
use ECidade\Tributario\Caixa\Entity\Collection\DebitoCollection;

final class Emissao implements Service
{
    /**
     * array de Regras de Emissao
     * @RegraEmissao
     */
    private $regrasEmissao = array();

    /**
     * @var array
     */
    private $iniciais = array();

    /**
     * Array de Numpres (indice do array) com array de numpar para cada numpre
     */
    private $numpres = array();

    /**
     * $carne
     * Define se é carne ou recibo
     * @var boolean
     */
    private $carne = false;
    /**
     * $minNumpar
     *
     * @var integer
     */
    private $minNumpar = 1;

    /**
     * $maxNumpar
     *
     * @var integer
     */
    private $maxNumpar = 1;

    /**
     * Int
     */
    private $codigoInstituicao;

    /**
     * $externo
     * Identifica se a geracao é externa ou nao
     * @var boolean
     */
    private $externo = false;

    /**
     * $modeloRecibo
     * @var int
     */
    private $modeloRecibo = TipoModeloCustas::RECIBO;

    /**
     * $modeloCarne
     * @var int
     */
    private $modeloCarne = TipoModeloCustas::CARNE;

    /**
     * $tipoDebito
     *
     * @var int
     */
    private $tipoDebito;

    /**
     * O objeto que foi usado para buscar os débitos do recibo.
     * Essencialmente, é ou o Cgm, ou a Inscrição, ou a Matrícula usada
     * na hora de pesquisar alguém na geral financeira.
     *
     * Seu valor é definido de acordo com os métodos:
     * @see Emissao::setCgm()
     * @see Emissao::setInscricao()
     * @see Emissao::setMatricula()
     *
     * @var CgmBase|Issbase|Iptubase
     */
    private $identificacao;

    /**
     * @var CgmBase
     */
    private $cgm;

    /**
     * $tipoModelo
     * @var string
     */
    private $tipoModelo = '19';

    /**
     * Diz se a emissão deve dar desconto por quitar todos
     * os débitos de um certo tipo
     *
     * @var bool
     */
    private $descontoQuitarTudo;

    private $container;

    public function processar()
    {
        $this->validaParametrosObrigatorios();

        $this->container = Registry::get('app.container')->get('tributario.container');
        $arretipoRepository = $this->container->get('ArretipoRepository');
        $debitoCollectionRepository = $this->container->get('DebitoCollectionRepository');
        $arrecadRepository = $this->container->get('Caixa\ArrecadRepository');

        $listaRecibos = array();
        $debitos = array();
        $this->setTipoModelo();

        $listaRecibos = $this->geraListaRecibos();
        $recibos = array();

        $whereLoteador = $this->getWhereLoteador();

        foreach ($listaRecibos as $dadosRecibo) {
            if (!empty($this->numpres)) {
                $debitos = $debitoCollectionRepository->findAllByNumpresNumpar($dadosRecibo->numpres);
                $dadosRecibo->iniciais = $this->getInicialByNumpres($dadosRecibo->numpres);
            } else {
                $debitos = $debitoCollectionRepository->findByIniciais($dadosRecibo->iniciais);
            }

            if (empty($dadosRecibo->iniciais)) {
                throw new BusinessException("É necessário informar ao menos uma inicial.");
            }

            if (empty($debitos)) {
                throw new BusinessException("Nenhum débito encontrado.");
            }

            // Buscando a regra de emissao
            $aRegraEmissao = $this->getRegraEmissao($debitos[0]);

            $reciboCustasService = new ReciboCustasService($aRegraEmissao[0]->tipoemissaocustas);
            $reciboCustasService->setIniciais($dadosRecibo->iniciais);
            $recibosTemCustas = $reciboCustasService->validaUsoDeCustas();

            if ($recibosTemCustas) {
                foreach ($aRegraEmissao as $regraEmissao) {
                    $recibo = new Recibo();

                    $arretipo = $arretipoRepository->find($debitos[0]->getTipo());
                    $recibo->setOrigem(Cadtipomod::RECIBO_DA_CGF);
                    $recibo->setTerceiroDigito($arretipo->getTercdigrecnormal());
                    $recibo->setTipo($this->tipoDebito);

                    $recibo->setVencimento($this->calculaDataDeVencimento($debitos, $regraEmissao));
                    $menorParcela = 1;
                    $quantidadeParcelasDebito = 1;

                    foreach ($debitos as $debito) {
                        $quantidadeParcelasDebito = $arrecadRepository->findMaxNumpar($debito->getNumpre());
                        $parcelas = $debito->getParcelas();
                        if ($parcelas->isEmpty()) {
                            throw new BusinessException("Não foi encontrada nenhum débito em aberto.");
                        }
                        foreach ($parcelas as $parcela) {
                            /**
                             * Adiciona os descontos nos débitos, levando em
                             * conta se a pessoa quitou todos os débitos ou não
                             */
                            $desconto = $recibo->reciboDesconto(
                                $debito->getNumpre(),
                                $parcela->getNumero(),
                                $recibo->getTipo(),
                                $whereLoteador,
                                count($debitos),
                                $this->descontoQuitarTudo ? count($debitos) : 0,
                                $recibo->getVencimento()->format('Y-m-d')
                            );
                            $menorParcela = $parcela->getNumero();
                            $recibo->setDesconto($desconto);
                        }
                        $recibo->addDebito($debito);
                    }
                    $recibo->setIdentificacao($this->identificacao);

                    $recibo = $this->processaCobrancaRegistrada($regraEmissao, $recibo);
                    $reciboCustasService->setRecibo($recibo);
                    $reciboCustasService->setRegraEmissao($regraEmissao);
                    $processamentoCustas = $reciboCustasService->processar();

                    if ($processamentoCustas) {
                        $recibo = $reciboCustasService->getRecibo();

                        $recibo->setConvenio($this->getConvenio($recibo, $regraEmissao, $arretipo));
                        $recibo->setCgmExibicao($this->cgm);
                        $recibo->setArretipo($arretipo);
                        $recibo->setCodigoInstituicao($this->codigoInstituicao);
                        $recibo->setCadTipoMod($this->tipoModelo);
                        $recibo->setMinNumpar($this->minNumpar);
                        $recibo->setMaxNumpar($this->maxNumpar);

                        if ($this->isCarne()) {
                            $recibo->setParcelaAtual($menorParcela);
                            $recibo->setQuantidadeParcelas($quantidadeParcelasDebito);
                        }
                        $recibo->tipoemissaocustas = $regraEmissao->tipoemissaocustas;
                        $recibos[] = $recibo;
                    }
                }
            } else {
                throw new BusinessException("Recibo/Carne não possui custas.");
            }
        }
        return $recibos;
    }

    public function addInicial($inicial)
    {
        $this->iniciais[$inicial] = $inicial;
    }

    /**
     * @param mixed $debito
     * @return array
     */
    public function getRegraEmissao($debito)
    {
        $this->checkExterno();
        $this->setTipoDebito($debito->getTipo());
        $this->setTipoModelo();
        $regras = RegraEmissaoReciboCustaRepository::getRegraEmissao(
            date('Y-m-d', db_getsession("DB_datausu")),
            $this->codigoInstituicao,
            $this->tipoDebito,
            $this->minNumpar,
            $this->maxNumpar,
            $this->tipoModelo
        );
        return $regras;
    }

    public function setRegraEmissao(array $regrasEmissao)
    {
        $this->regrasEmissao = $regrasEmissao;
    }

    public function addRegraEmissao(RegraEmissao $regraEmissao)
    {
        $this->regrasEmissao[] = $regraEmissao;
    }

    /**
     * @param integer $codigoInstituicao
     * @return void
     */
    public function setCodigoInstituicao($codigoInstituicao)
    {
        $this->codigoInstituicao = $codigoInstituicao;
    }

    /**
     * setNumpres
     * Seta os Numpres e reseta o array de iniciais
     * Só podemos gerar 1 tipo de recibo por vez
     * @param array $numpres Array chaveado pelo numpre, e o valor é um array com os numpares
     * @return void
     */
    public function setNumpres($numpres)
    {
        $this->iniciais = array();
        $this->numpres = $numpres;

        /**
         * Calcula o numpar mínimo e máximo para a regra de
         * emissão
         */
        $numparesDeTodos = array();
        foreach ($this->numpres as $numparesDoNumpre) {
            $numparesDeTodos = array_merge($numparesDeTodos, $numparesDoNumpre);
        }

        $this->minNumpar = $numparesDeTodos[0];
        $this->maxNumpar = $numparesDeTodos[0];

        foreach ($numparesDeTodos as $numpar) {
            if ($numpar < $this->minNumpar) {
                $this->minNumpar = $numpar;
            }

            if ($numpar > $this->maxNumpar) {
                $this->maxNumpar = $numpar;
            }
        }
    }

    private function getInicialByNumpres($numpres)
    {
        $iniciais = array();
        foreach ($numpres as $numpre => $numpar) {
            $termoRepository = Termo::getInstance();
            $termo = $termoRepository->getByNumpre($numpre);
            $termoInicialRepository = TermoInicial::getInstance();
            foreach ($termoInicialRepository->setReturnFullItem(true)->getByTermo($termo->getCodigo()) as $termoIni) {
                $iniciais[] = $termoIni->getInicial()->getCodigo();
            }
        }
        return $iniciais;
    }

    public function setExterno($externo)
    {
        $this->externo = $externo;
    }

    /**
     * checkExterno
     * Verifica se a emissao vem de fora do sistema
     * Essa regra foi implementada devido aos recibos com cobrança registrada
     * @return void
     */
    private function checkExterno()
    {
        if ($this->externo) {
            $this->modeloRecibo = TipoModeloCustas::RECIBOEXTERNO;
            $this->modeloCarne = TipoModeloCustas::CARNEEXTERNO;
        } else {
            $this->modeloRecibo = TipoModeloCustas::RECIBO;
            $this->modeloCarne = TipoModeloCustas::CARNE;
        }
    }

    private function setTipoDebito($tipoDebito)
    {
        $this->tipoDebito = $tipoDebito;
    }

    private function setTipoModelo()
    {
        $parametroJuridico = ParametroJuridico::getParametroAtual($this->codigoInstituicao, db_getsession("DB_anousu"));
        if ($parametroJuridico->partilha == "t" && $this->carne && $this->tipoDebito == 18) {
            $this->tipoModelo = "{$this->modeloCarne}, {$this->modeloRecibo}";
        } elseif ($this->carne) {
            $this->tipoModelo = "{$this->modeloCarne}";
        } else {
            $this->tipoModelo = "{$this->modeloRecibo}";
        }
    }

    public function setCarne($carne)
    {
        $this->carne = $carne;
    }

    public function isCarne()
    {
        return $this->carne;
    }

    private function processaCobrancaRegistrada($regraEmissao, $recibo)
    {
        $reciboService = $this->container->get('ReciboService');
        if ($regraEmissao->tipoconvenio == 7) {
            $recibo = $reciboService->execute($recibo);
            $recibo = $recibo->toModel();
            if ($this->verificaCobrancaRegistrada($regraEmissao)) {
                CobrancaRegistrada::adicionarRecibo($recibo, $regraEmissao->convenio);
            }
        } else {
            $recibo = $reciboService->execute($recibo);
        }
        return $recibo;
    }

    /**
     * @param mixed $recibo
     * @param mixed $regraEmissao
     * @param mixed $arretipo
     * @return convenio
     */
    private function getConvenio($recibo, $regraEmissao, $arretipo)
    {
        $valor = db_formatar(
            str_replace(
                '.',
                '',
                str_pad(number_format($recibo->getTotalRecibo(), 2, "", "."), 11, "0", STR_PAD_LEFT)
            ),
            's',
            '0',
            11,
            'e'
        );
        return new convenio(
            $regraEmissao->convenio,
            $recibo->getNumpreRecibo(),
            0,
            $recibo->getTotalRecibo(),
            $valor,
            $recibo->getDataVencimentoRecibo(),
            $arretipo->getTercdigrecnormal()
        );
    }

    /**
     * Formata os recibos para o processamento
     * @return array
     */
    private function geraListaRecibos()
    {
        $recibos = array();

        if (empty($this->numpres) && empty($this->iniciais)) {
            throw new BusinessException("Não houveram iniciais/numpres informados.");
        }

        if ($this->carne) {
            if (!empty($this->numpres)) {
                foreach ($this->numpres as $numpre => $numpares) {
                    // Cada parcela se torna 1 recibo
                    foreach ($numpares as $numpar) {
                        $recibo = new stdClass;
                        $recibo->numpres = array($numpre => array($numpar));
                        $recibos[] = $recibo;
                    }
                }
            } elseif (!empty($this->iniciais)) {
                // Cada inicial se torna 1 recibo
                foreach ($this->iniciais as $inicial) {
                    $recibo = new stdClass;
                    $recibo->iniciais = array($inicial);
                    $recibos[] = $recibo;
                }
            }
        } else {
            if (!empty($this->numpres)) {
                $recibo = new stdClass;
                $recibo->numpres = $this->numpres;
                $recibos[] = $recibo;
            } elseif (!empty($this->iniciais)) {
                $recibo = new stdClass;
                $recibo->iniciais = $this->iniciais;
                $recibos[] = $recibo;
            }
        }
        return $recibos;
    }

    /**
     * @param array $iniciais
     */
    public function setIniciais(array $iniciais)
    {
        $this->iniciais = $iniciais;
    }

    public function setCgm($codigoCgm)
    {
        $this->setCodigoCgm($codigoCgm);
        $this->identificacao = $this->cgm;
    }

    public function setMatricula($codigoImovel)
    {
        $iptubaseRepository = new IptubaseRepository(DataBase::getInstance(), new cl_iptubase());
        $iptubase = $iptubaseRepository->find($codigoImovel);
        $this->identificacao = $iptubase;

        $cgm = Proprietario::getProprietarioByMatricula($codigoImovel);
        $this->setCodigoCgm($cgm->rinumcgm);
    }

    public function setInscricao($codigoInscricao)
    {
        $issbaseRepository = new IssbaseRepository(DataBase::getInstance(), new cl_issbase());
        $issbase = $issbaseRepository->find($codigoInscricao);
        $this->identificacao = $issbase;
        $cgm = Proprietario::getProprietarioByInscricao($codigoInscricao);
        $this->setCodigoCgm($cgm->rinumcgm);
    }

    private function setCodigoCgm($codigoCgm)
    {
        $this->cgm = CgmRepository::getByCodigo($codigoCgm);
    }

    private function validaParametrosObrigatorios()
    {
        if (empty($this->cgm)) {
            throw new BusinessException('Matrícula/CGM não informado!');
        }

        if (empty($this->numpres) && empty($this->iniciais)) {
            throw new BusinessException('Não foi informado nenhum parcelamento ou inicial para a geração de Boleto!');
        }
    }

    private function verificaCobrancaRegistrada($regraEmissao)
    {
        if ($regraEmissao->tipoconvenio == 7) {
            $cobranca = CobrancaRegistrada::validaConvenioCobranca($regraEmissao->convenio);
            if ($cobranca && !CobrancaRegistrada::utilizaIntegracaoWebService($regraEmissao->convenio)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return string
     * @throws Exception
     */
    private function getWhereLoteador()
    {
        if (!empty($this->cgm) && !$this->identificacao instanceof Iptubase) {
            $sql = "select * from loteam
                    left join loteamcgm  on loteamcgm.j120_loteam = loteam.j34_loteam
                    where j120_cgm = {$this->cgm->getCodigo()}
            ";

            $rs = db_query($sql);

            if (!$rs) {
                throw new Exception('Não foi possível verificar o loteador.');
            }

            if (pg_numrows($rs) > 0) {
                return "and k40_forma = 3";
            }
        }

        return " and k40_forma <> 3";
    }

    /**
     * @param bool $descontoQuitarTudo
     */
    public function setDescontoQuitarTudo($descontoQuitarTudo)
    {
        $this->descontoQuitarTudo = $descontoQuitarTudo;
    }

    /**
     * @return bool
     */
    public function getDescontoQuitarTudo()
    {
        return $this->descontoQuitarTudo;
    }

    /**
     * @param DebitoCollection $debitos
     * @param stdClass $regraEmissao A regra de emissão
     * @return DateTime
     */
    private function calculaDataDeVencimento(DebitoCollection $debitos, $regraEmissao)
    {
        $dataVencimento = null;
        $dataAtual = new DBDate(date('Y-m-d', db_getsession("DB_datausu")));

        foreach ($debitos as $debito) {
            $parcelas = $debito->getParcelas();
            if (empty($dataVencimento)) {
                $dataVencimento = new DBDate(date('Y-m-d', $parcelas[0]->getVencimento()->getTimeStamp()));
            }

            foreach ($parcelas as $parcela) {
                $dataParcela = $parcela->getVencimento();
                if ($dataParcela->getTimeStamp() <= $dataVencimento->getTimeStamp()) {
                    $dataVencimento = new DBDate(date('Y-m-d', $dataParcela->getTimeStamp()));
                }
            }
        }

        /**
         * Verifica se possui cobranca Registrada
         * caso seja, a data de vencimento nao pode ser a data atual e sim o proximo dia util
         */
        if ($this->verificaCobrancaRegistrada($regraEmissao)) {
            $dataAtual = $dataAtual->getProximoDia();
        }
        /**
         * Caso a data de vencimento seja menor que a data atual, ela vira a data atual
         */
        if ($dataAtual->getTimeStamp() >= $dataVencimento->getTimeStamp()) {
            $dataVencimento = $dataAtual;
        }
        $calendarioService = new Calendario();

        while (!$calendarioService->isUtil($dataVencimento->getDate())) {
            $dataVencimento = $dataVencimento->getProximoDia();
        }

        return new DateTime($dataVencimento->getDate());
    }
}
