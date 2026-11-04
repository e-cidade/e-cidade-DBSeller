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

namespace ECidade\Tributario\Arrecadacao\Custas\Service\Relatorio;

use ECidade\Tributario\Arrecadacao\Custas;
use ECidade\Tributario\Arrecadacao\CobrancaRegistrada\CobrancaRegistrada;
use ECidade\Tributario\Juridico\Inicial\Repository\Inicial as InicialRepository;
use ECidade\Tributario\Juridico\ProcessoForo\Repository\ProcessoForo as ProcessoForoRepository;
use ECidade\Tributario\Juridico\ProcessoForoPartilha\ProcessoForoPartilha as ProcessoForoPartilhaEntity;
use ECidade\Tributario\Juridico\ProcessoForoPartilha\ProcessoForoPartilhaCusta as ProcessoForoPartilhaCustaEntity;
use ECidade\Tributario\Juridico\ProcessoForoPartilha\Repository\ProcessoForoPartilha as ProcessoForoPartilhaRepository;
use ECidade\Tributario\Juridico\InicialPartilha\InicialPartilha as InicialPartilhaEntity;
use ECidade\Tributario\Juridico\InicialPartilha\InicialPartilhaCustas as InicialPartilhaCustasEntity;
use ECidade\Tributario\Juridico\ProcessoForo\ProcessoForo as ProcessoForoEntity;
use ECidade\Tributario\Juridico\Inicial\Inicial as InicialEntity;

/**
 * Serviço responsavel por calcular custas a partir da emissao de recibos.
 *
 * @author Leonardo Oliveira <leonardo.malia@dbseller.com.br>
 */
class Inicial implements Custas\Interfaces\Service
{
    /** @var integer */
    private $tipoDebito;

    /** @var integer */
    private $cadTipo;

    /** @var array */
    private $iniciais;

    /** @var \DBDate */
    private $dataUsuario;

    /** @var array  */
    private $datasVencimentos = array();

    public function __construct($tipoDebito, $cadTipo, $iniciais)
    {
        if (!\db_utils::inTransaction()) {
            throw new \Exception('Transação não iniciada');
        }

        $this->tipoDebito = $tipoDebito;
        $this->cadTipo = $cadTipo;
        $this->iniciais = array_unique($iniciais);
        $this->dataUsuario = \DBDate::createFromTimestamp(db_getsession("DB_datausu"));
    }

    /**
     * Processamento do serviço
     * @return array
     * @throws \Exception
     * @throws \DBException
     * @throws \ParameterException
     */
    public function processar()
    {
        $processoForoRepository = ProcessoForoRepository::getInstance()
            ->setReturnFullItem(true);

        $inicialRepository = InicialRepository::getInstance()
            ->setReturnFullItem(true);

        /** @var ProcessoForoEntity[] $processos */
        $processos = array();

        /** @var InicialEntity[] $iniciais */
        $iniciais = array();

        foreach ($this->iniciais as $inicial) {
            if (!isset($inicial) || empty($inicial)) {
                continue;
            }

            $processo = $processoForoRepository->getByInicial($inicial);

            if (!$processo) {
                $iniciais[] = $inicialRepository->getByCode($inicial);
            } else {
                // Remove iniciais do processo que não foram selecionadas
                $processoIniciais = $processo->getIniciais();
                foreach ($processoIniciais as $key => $value) {
                    if (!in_array($value->getCodigo(), $this->iniciais)) {
                        unset($processoIniciais[$key]);
                    }
                }
                $processo->setIniciais($processoIniciais);

                $processos[$processo->getCodigo()] = $processo;
            }
        }

        $this->validarIniciais($processos, $iniciais);

        $regraEmissao = new \regraEmissao(
            $this->tipoDebito,
            19,
            db_getsession('DB_instit'),
            $this->dataUsuario->getDate(),
            db_getsession('DB_ip'),
            true,
            false
        );
            
        $debitos = array_merge($processos, $iniciais);

        foreach ($debitos as $debito) {
            $listaIniciais = $this->getIniciaisDebito($debito);

            $custas = $this->processarCustas($listaIniciais, $regraEmissao);

            $this->buildPartilha($debito, $custas);
        }

        return $debitos;
    }

    /**
     * Retorna as iniciais de acordo com o tipo de débito.
     *
     * @param $debito
     *
     * @return array
     */
    private function getIniciaisDebito($debito)
    {
        $iniciais = array();

        if ($debito instanceof ProcessoForoEntity) {
            foreach ($debito->getIniciais() as $inicial) {
                $iniciais[] = $inicial->getCodigo();
            }
        }

        if ($debito instanceof InicialEntity) {
            $iniciais = array($debito->getCodigo());
        }

        return $iniciais;
    }

    /**
     * Monta as partilhas de acordo com o tipo de débito.
     * @param \ECidade\Tributario\Juridico\Inicial\Inicial $debito
     * @param array $custas
     * @throws \Exception
     */
    private function buildPartilha($debito, $custas)
    {
        $partilhas = array();

        foreach ($custas as $custa) {
            if ($debito instanceof ProcessoForoEntity) {
                $partilhas[$custa->tipolancamento] = new ProcessoForoPartilhaEntity();
            }

            if ($debito instanceof InicialEntity) {
                $partilhas[$custa->tipolancamento] = new InicialPartilhaEntity();
            }
        }

        foreach ($partilhas as $tipoLancamento => $partilha) {
            foreach ($custas as $custa) {
                if ($custa->tipolancamento != $tipoLancamento) {
                    continue;
                }

                $taxa = new \taxa($custa->taxa);

                if ($debito instanceof ProcessoForoEntity) {
                    $partilhaCusta = new ProcessoForoPartilhaCustaEntity();
                    $partilhaCusta->setTaxa($taxa);
                    $partilhaCusta->setValor($custa->valor);

                    $partilha->addCustas($partilhaCusta);
                }

                if ($debito instanceof InicialEntity) {
                    $partilhaCusta = new InicialPartilhaCustasEntity();
                    $partilhaCusta->setTaxa($taxa);
                    $partilhaCusta->setValor($custa->valor);

                    $partilha->addCustas($partilhaCusta);
                }

                $partilha->setTipoLancamento($custa->tipolancamento);
            }

            if ($debito instanceof ProcessoForoEntity) {
                $debito->addProcessoForoPartilha($partilha);
            }

            if ($debito instanceof InicialEntity) {
                $debito->addInicialPartilha($partilha);
            }
        }
    }

    /**
     * @param ProcessoForoEntity[] $processos
     * @param InicialEntity[] $iniciais
     *
     * @throws \Exception
     */
    private function validarIniciais($processos, $iniciais)
    {
        $exists = array();
        foreach ($processos as $processo) {
            foreach ($processo->getIniciais() as $inicial) {
                if (!empty($exists[$inicial->getCodigo()])) {
                    throw new \Exception('Foi encontrado uma mesma inicial no array de processos');
                }

                $exists[$inicial->getCodigo()] = $inicial->getCodigo();
            }
        }

        foreach ($iniciais as $inicial) {
            if (!empty($exists[$inicial->getCodigo()])) {
                throw new \Exception('Foi encontrado uma mesma inicial no array de iniciais');
            }

            $exists[$inicial->getCodigo()] = $inicial->getCodigo();
        }
    }

    /**
     * @param $iniciais
     * @param \regraEmissao $regraEmissao
     * @return array
     * @throws \Exception
     */
    private function processarCustas($iniciais, \regraEmissao $regraEmissao)
    {
        $recibo = $this->emissaoRecibo($iniciais, $regraEmissao);
        $this->datasVencimentos[] = $recibo->getDataVencimento();

        $reciboService = new Custas\Service\Recibo($this->cadTipo);
        $reciboService->setIniciais($iniciais);
        $reciboService->validaUsoDeCustas();
        $reciboService->setRecibo($recibo);
        $reciboService->setRegraEmissao($regraEmissao);

        $processamentoCustas = $reciboService->processar();

        $processoForoPartilhaRepository = ProcessoForoPartilhaRepository::getInstance();
        $custasPagas = $processoForoPartilhaRepository->getPagoManualByNumnov($recibo->getNumpreRecibo());

        $custas = array();
        if ($processamentoCustas) {
            $custas = $reciboService->getCustas($recibo->getNumpreRecibo());
        }

        if (!empty($custasPagas)) {
            $custas = array_merge($custas, $custasPagas);
        }

        return $custas;
    }

    /**
     * @param $iniciais
     * @param \regraEmissao $regraEmissao
     * @return \recibo
     * @throws \Exception
     */
    private function emissaoRecibo($iniciais, \regraEmissao $regraEmissao)
    {
        $recibo = new \recibo(2, null, 1);

        $dataVencimento = $this->getDataVencimento($recibo, $iniciais);

        $recibo->setNumBco($regraEmissao->getCodConvenioCobranca());
        $recibo->setDataRecibo($dataVencimento->getDate());
        $recibo->setDataVencimentoRecibo($dataVencimento->getDate());
        $recibo->setExercicioRecibo(substr($dataVencimento->getDate(), 0, 4));
        $lConvenioCobrancaValido = CobrancaRegistrada::validaConvenioCobranca($regraEmissao->getConvenio());
        $recibo->emiteRecibo($lConvenioCobrancaValido, true, $regraEmissao->getConvenio());

        return $recibo;
    }

    /**
     * @param \recibo $recibo
     * @param $iniciais
     * @return \DBDate|null
     * @throws \Exception
     * @throws \ParameterException
     */
    private function getDataVencimento(\recibo $recibo, $iniciais)
    {
        $dataVencimento = null;

        foreach ($iniciais as $inicial) {
            $sSqlinicial = " select distinct arrecad.k00_numpre as numpre,                              ";
            $sSqlinicial .= "        arrecad.k00_numpar as numpar,                                        ";
            $sSqlinicial .= "        arrecad.k00_dtvenc as data_vencimento";
            $sSqlinicial .= "   from inicialnumpre                                                       ";
            $sSqlinicial .= "        inner join arrecad on arrecad.k00_numpre = inicialnumpre.v59_numpre ";
            $sSqlinicial .= "  where v59_inicial = " . $inicial;
            $rsInicial = db_query($sSqlinicial);

            $aInicial = \db_utils::getCollectionByRecord($rsInicial);

            foreach ($aInicial as $inicialNumpre) {
                $recibo->addNumpre($inicialNumpre->numpre, $inicialNumpre->numpar);
                $novaData = new \DBDate($inicialNumpre->data_vencimento);

                if ($dataVencimento === null || $dataVencimento->getTimeStamp() > $novaData->getTimeStamp()) {
                    $dataVencimento = $novaData;
                }
            }

            if ($this->dataUsuario->getTimeStamp() > $dataVencimento->getTimeStamp()) {
                $dataVencimento = $this->dataUsuario;
            }
        }

        return $dataVencimento;
    }

    /**
     * @return array
     */
    public function getDatasVencimentos()
    {
        return $this->datasVencimentos;
    }
}
