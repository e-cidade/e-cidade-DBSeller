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

use db_utils;
use ECidade\Tributario\Arrecadacao\Custas\Calculo\CalculoAdministrativa;
use ECidade\Tributario\Arrecadacao\Custas\Calculo\CalculoAdministrativaParcelamentoRecibo;
use ECidade\Tributario\Arrecadacao\Custas\Calculo\CalculoJuridica;
use ECidade\Tributario\Arrecadacao\Custas\Calculo\CalculoJuridicaParcelamentoRecibo;
use ECidade\Tributario\Arrecadacao\Custas\Enum\TipoModelo;
use ECidade\Tributario\Arrecadacao\Custas\Interfaces\PartilhaRecibo;
use ECidade\Tributario\Arrecadacao\Custas\Interfaces\Service;
use ECidade\Tributario\Arrecadacao\Custas\Partilha\PartilhaAdministrativaRecibo;
use ECidade\Tributario\Arrecadacao\Custas\Partilha\PartilhaJuridicaRecibo;
use ECidade\Tributario\Arrecadacao\Custas\Partilha\PartilhaAdministrativaParcelamentoRecibo;
use ECidade\Tributario\Arrecadacao\Custas\Partilha\PartilhaJuridicaParcelamentoRecibo;
use ECidade\Tributario\Arrecadacao\Custas\Validador\FixaJuridicaParcelamento;
use ECidade\Tributario\Arrecadacao\Custas\Validador\FixaJuridicaRecibo;
use ECidade\Tributario\Arrecadacao\Repository\ModeloCarnePadrao;
use ECidade\Tributario\Divida\Termo\Repository\Termo as TermoRepository;
use ECidade\Tributario\Juridico\Inicial\Inicial;
use ECidade\Tributario\Juridico\ProcessoForo\Repository\ProcessoForo;
use ECidade\Tributario\Juridico\ProcessoForoPartilha\Repository\ProcessoForoPartilha as ProcessoForoPartilhaRepository;
use ECidade\Tributario\Juridico\InicialPartilha\Repository\InicialPartilhaCustas as InicialPartilhaCustasRepository;
use ECidade\Tributario\Juridico\Repository\Partilha as PartilhaRepository;
use \Recibo as ReciboModel;
use \RegraEmissao;
use \Exception;

final class Recibo implements Service
{
    private $usaCustas;

    private $cadTipo;

    private $iniciais;

    /** @var ReciboModel */
    private $recibo;

    private $regraEmissao;

    private $modeloCarnePadraoRepository;

    private $processoForoRepository;

    private $termoRepository;

    /**
     * @var integer
     */
    private $numeroTotalParcelas;

    public function __construct($cadTipo)
    {
        $this->cadTipo = $cadTipo;

        $this->modeloCarnePadraoRepository = ModeloCarnePadrao::getInstance();
        $this->processoForoRepository = ProcessoForo::getInstance();
        $this->termoRepository = TermoRepository::getInstance();

        $this->termoRepository->setReturnFullItem(true);
        $this->processoForoRepository->setReturnFullItem(true);

        $this->usaCustas = false;
    }

    public function getIniciais()
    {
        return $this->iniciais;
    }

    public function setIniciais($iniciais)
    {
        $this->iniciais = $iniciais;
    }

    public function getRecibo()
    {
        return $this->recibo;
    }

    public function setRecibo(ReciboModel $recibo)
    {
        $this->recibo = $recibo;
    }

    public function getRegraEmissao()
    {
        return $this->regraEmissao;
    }

    public function setRegraEmissao($regraEmissao)
    {
        $this->regraEmissao = $regraEmissao;
    }

    /**
     * @param integer $numeroTotalParcelas
     * @return void
     */
    public function setTotalParcelas($numeroTotalParcelas)
    {
        $this->numeroTotalParcelas = $numeroTotalParcelas;
    }

    /**
     * @return integer
     */
    public function getTotalParcelas()
    {
        return $this->numeroTotalParcelas;
    }

    private function emissaoReciboParcelamento()
    {
        return ($this->cadTipo == 13);
    }

    private function emissaoReciboInicialForo()
    {
        return (!empty($this->iniciais));
    }

    private function emissaoReciboValido()
    {
        return ($this->emissaoReciboParcelamento() or $this->emissaoReciboInicialForo());
    }

    public function validaUsoDeCustas()
    {
        try {
            $existeRegraModelos = $this->modeloCarnePadraoRepository->existeRegraModelos(array(
                TipoModelo::RECIBO,
                TipoModelo::CARNE
            ));

            if ($existeRegraModelos and $this->emissaoReciboValido()) {
                $this->usaCustas = true;
            }

            return $this->usaCustas;
        } catch (Exception $erro) {
            throw new Exception($erro->getMessage());
        }
    }

    /**
     * @param bool $comCalculoCustas
     */
    public function processar($comCalculoCustas = true)
    {
        if (!$this->usaCustas) {
            return false;
        }

        if (empty($this->regraEmissao)) {
            throw new Exception("Não foi definida a regra de emissão.");
        }

        if ($this->regraEmissao instanceof RegraEmissao) {
            if (!in_array($this->cadTipo, array(18, 12, 13)) ||
                  $this->regraEmissao->getCadTipoConvenio() != RegraEmissao::getConveioCustaBoleto()) {
                return false;
            }
        } else {
            if (!in_array($this->regraEmissao->k03_tipo, array(18, 12, 13))
                || $this->regraEmissao->ar11_cadtipoconvenio != RegraEmissao::getConveioCustaBoleto()
            ) {
                return false;
            }
        }

        if (empty($this->recibo)) {
            throw new Exception("Não foi definido o recibo que recebera as custas.");
        }

        /** @var PartilhaRecibo[] $partilhas */
        $partilhas = array();

        if ($this->emissaoReciboParcelamento()) {
            $numpres = array();

            $debitos = $this->recibo->getDebitosRecibo();

            foreach ($debitos as $debito) {
                $numpres[] = $debito->k00_numpre;
            }

            foreach (array_unique($numpres) as $numpre) {
                $termo = $this->termoRepository->getByNumpre($numpre);

                $termoIniciais = $termo->getTermoIniciais();

                $iniciais = array();
                $processos = array();

                foreach ($termoIniciais as $termoInicial) {
                    $inicial = $termoInicial->getInicial();

                    $processo = $this->processoForoRepository->getByInicial($inicial->getCodigo());

                    if (empty($processo)) {
                        $iniciais[] = $inicial;
                    } else {
                        $processos[] = $processo;
                    }
                }

                foreach ($iniciais as $inicial) {
                    $calculoAdministrativa = new CalculoAdministrativaParcelamentoRecibo(
                        $this->recibo,
                        $inicial,
                        $termo
                    );

                    $partilhas[] = new PartilhaAdministrativaParcelamentoRecibo(
                        $calculoAdministrativa,
                        $inicial,
                        $termo
                    );
                }

                $processos = array_unique($processos);

                foreach ($processos as $processo) {
                    $calculoJuridica = new CalculoJuridicaParcelamentoRecibo($this->recibo, $processo, $termo);

                    $fixaJuridicaParcelamento = new FixaJuridicaParcelamento($termo, $processo);

                    $partilhas[] = new PartilhaJuridicaParcelamentoRecibo(
                        $calculoJuridica,
                        $fixaJuridicaParcelamento,
                        $processo,
                        $termo
                    );
                }
            }
        } else {
            if ($this->emissaoReciboInicialForo()) {
                $iniciais = array();
                $processos = array();

                foreach ($this->iniciais as $codigoInicial) {
                    $processo = $this->processoForoRepository->getByInicial($codigoInicial);

                    if (empty($processo)) {
                        $inicial = new Inicial();
                        $inicial->setCodigo($codigoInicial);

                        $iniciais[] = $inicial;
                    } else {
                        $processos[] = $processo;
                    }
                }

                foreach ($iniciais as $inicial) {
                    $calculoAdministrativa = new CalculoAdministrativa($inicial, $this->recibo->getNumpreRecibo());

                    $partilhas[] = new PartilhaAdministrativaRecibo($calculoAdministrativa, $inicial);
                }

                $processos = array_unique($processos);

                foreach ($processos as $processo) {
                    $calculoJuridica = new CalculoJuridica(
                        $processo,
                        $this->recibo->getNumpreRecibo(),
                        $this->recibo->getDataVencimento()
                    );

                    $fixaJuridicaRecibo = new FixaJuridicaRecibo($this->recibo, $processo);

                    $partilhas[] = new PartilhaJuridicaRecibo($calculoJuridica, $fixaJuridicaRecibo, $processo);
                }
            }
        }

        if ($comCalculoCustas && !db_utils::ativaParcelamentoCustas()) {
            foreach ($partilhas as $partilha) {
                $partilha->setRecibo($this->recibo);
                
                $this->recibo = $partilha->processar();
            }
        } else {
            foreach ($partilhas as $partilha) {
                $partilha->setRecibo($this->recibo);
                
                $this->recibo = $partilha->processar(false);
            }
        }

        $this->recibo->processaReceitaCusta();

        return $this->recibo;
    }

    /**
     * Retorna as custas de um recibo filtrando por numnov.
     *
     * @param integer $numnov
     *
     * @return array
     */
    public function getCustas($numnov)
    {
        if ($this->cadTipo == 13) {
            $repository = new PartilhaRepository();

            return $repository->getCustasParcelamentoForo($numnov);
        }

        $oProcessoForoPartilhaRepository = ProcessoForoPartilhaRepository::getInstance();
        $custasProcesso = $oProcessoForoPartilhaRepository->getDadosRecibo($numnov);

        $oPartilhaCustasRepository = InicialPartilhaCustasRepository::getInstance();
        $custasInicial = $oPartilhaCustasRepository->getDadosRecibo($numnov);

        $custas = array_merge($custasProcesso, $custasInicial);

        return $custas;
    }
}
