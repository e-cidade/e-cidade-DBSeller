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

namespace ECidade\Tributario\Juridico\Service;

use ECidade\Tributario\Divida\Certidao\Certidao as CertidaoEntity;
use ECidade\Tributario\Divida\Certidao\Repository\Certidao as CertidaoRepository;
use ECidade\Tributario\Juridico\Inicial\Builder\HistoricoDesmembramentoBuilder;
use ECidade\Tributario\Juridico\Inicial\InicialNumpre;
use ECidade\Tributario\Juridico\Inicial\Repository\HistoricoDesmembramentoRepository;
use ECidade\Tributario\Juridico\Inicial\Repository\Inicial as InicialRepository;
use ECidade\Tributario\Juridico\Inicial\Repository\InicialNome as InicialNomeRepository;
use ECidade\Tributario\Juridico\Inicial\Repository\InicialNumpreRepository;
use ECidade\Tributario\Juridico\InicialPartilha\InicialPartilha as InicialPartilhaEntity;
use ECidade\Tributario\Juridico\ProcessoForo\ProcessoForoInicial as ProcessoForoInicialEntity;
use ECidade\Tributario\Juridico\ProcessoForo\Repository\ProcessoForo as ProcessoForoRepository;
use ECidade\Tributario\Juridico\ProcessoForo\Repository\ProcessoForoInicial as ProcessoForoInicialRepository;
use ECidade\Tributario\Juridico\Repository\Desmembramento as DesmembramentoRepository;
use ECidade\Tributario\Juridico\InicialPartilha\Repository\InicialPartilha as InicialPartilhaRepository;
use ECidade\Tributario\Arrecadacao\Repository\ArreforoRepository as ArreforoRepository;

/**
 * Class Desmembramento
 * @package ECidade\Tributario\Juridico\Service
 */
final class Desmembramento
{
    /**
     * @var DesmembramentoRepository
     */
    private $desmembramentoRepository;

    /**
     * @var HistoricoDesmembramentoRepository
     */
    private $historicoDesmembramentoRepositorio;
    /**
     * @var array
     */
    private $iniciaisCriadas;

    /**
     * Desmembramento constructor.
     * @param DesmembramentoRepository $desmembramentoRepository
     * @param HistoricoDesmembramentoRepository $historicoDesmembramentoRepositorio
     */
    public function __construct(
        DesmembramentoRepository $desmembramentoRepository,
        HistoricoDesmembramentoRepository $historicoDesmembramentoRepositorio
    ) {
        $this->desmembramentoRepository = $desmembramentoRepository;
        $this->historicoDesmembramentoRepositorio = $historicoDesmembramentoRepositorio;
    }

    /**
     * @param \stdClass $filtro
     * @return array
     */
    public function getDados(\stdClass $filtro)
    {
        $this->desmembramentoRepository->setFiltro($filtro);

        if (!empty($filtro->cgm)) {
            $dados = $this->desmembramentoRepository->getByCgm($filtro->cgm);
        }

        if (!empty($filtro->matric)) {
            $dados = $this->desmembramentoRepository->getByMatricula($filtro->matric);
        }

        if (!empty($filtro->inscr)) {
            $dados = $this->desmembramentoRepository->getByInscricao($filtro->inscr);
        }

        if (!empty($filtro->processoForo)) {
            $dados = $this->desmembramentoRepository->getByProcessoForo($filtro->processoForo);
        }

        if (empty($dados)) {
            return array();
        }

        $iniciaisAgrupadas = array();
        $cdasAgrupadas = array();

        foreach ($dados as $registro) {
            /* PLUGIN CRA 2 */

            $chave = "{$registro->sequencial_processo}#{$registro->codigo_inicial}#";
            $chave .= "{$registro->codigo_certidao}#{$registro->exercicio_divida}";

            if (empty($cdasAgrupadas[$registro->codigo_inicial])) {
                $cdasAgrupadas[$registro->codigo_inicial] = array(
                    $registro->codigo_certidao
                );
            } else {
                $cdasAgrupadas[$registro->codigo_inicial][] = $registro->codigo_certidao;
            }

            if (empty($iniciaisAgrupadas[$chave])) {
                $debito = new \stdClass();

                $debito->processo = $registro->codigo_processo;
                $debito->sequencial_processo = $registro->sequencial_processo;
                $debito->inicial = $registro->codigo_inicial;
                $debito->exercicio = $registro->exercicio_divida;
                $debito->cda = $registro->codigo_certidao;
                $debito->procedencias = array();
                $debito->valor = 0;
                $debito->dividas = array();

                $iniciaisAgrupadas[$chave] = $debito;
            }

            $debito = $iniciaisAgrupadas[$chave];
            $this->agrupaValoresDebito($debito, $registro);
        }

        // codigo abaixo comentado para permitir visualizar todos os casos desde que esteja em aberto, sem remover nada

        // remove iniciais que possui apenas 1 certidão e 1 divida.
        // foreach ($iniciaisAgrupadas as $key => $inicial) {
        //     $numpres = array();

        //     foreach ($inicial->dividas as $divida) {
        //         foreach ($divida->infos as $info) {
        //             $numpres[] = $info->numpre;
        //         }
        //     }

        //     $numpres = array_unique($numpres);

        //     if (count($numpres) > 1) {
        //         continue;
        //     }

        //     $cdasAgrupadas[$inicial->inicial] = array_unique($cdasAgrupadas[$inicial->inicial]);

        //     if (count($cdasAgrupadas[$inicial->inicial]) == 1) {
        //         unset($iniciaisAgrupadas[$key]);
        //     }
        // }

        sort($iniciaisAgrupadas);

        $iniciaisAgrupadas = array_reverse($iniciaisAgrupadas);

        return $iniciaisAgrupadas;
    }

    /**
     * Agrupa os valores do debito
     * @param \stdClass $debito
     * @param \stdClass $registro
     * @return \stdClass
     */
    private function agrupaValoresDebito(\stdClass $debito, \stdClass $registro)
    {
        if (!in_array($registro->nome_procedencia, $debito->procedencias)) {
            $debito->procedencias[] = $registro->nome_procedencia;
        }

        $valores = explode(',', str_replace(array('(', ')'), '', $registro->valores));

        $divida = $this->getRegistroDivida($registro->codigo_divida, $debito);

        if (empty($divida)) {
            $divida = new \stdClass();
            $divida->divida = $registro->codigo_divida;
            $divida->infos = array();
            $debito->dividas[] = $divida;
        }

        $numpre = new \stdClass();
        $numpre->numpre = $registro->numpre;
        $numpre->p = $registro->numpar;
        $numpre->t = $registro->total_parcelas;
        $numpre->dt_oper = $registro->data_operacao;
        $numpre->dt_venc = $registro->data_vencimento;
        $numpre->receita = $registro->receita_descricao;
        $numpre->val_historico = $valores[0];
        $numpre->val_corrigido = $valores[1];
        $numpre->val_juros = $valores[2];
        $numpre->val_multa = $valores[3];
        $numpre->val_desconto = $valores[4];
        $numpre->total = $valores[5];

        $divida->infos[] = $numpre;
        $debito->valor += $numpre->total;

        return $debito;
    }

    /**
     * @param $codigoDivida
     * @param $debito
     * @return null
     */
    private function getRegistroDivida($codigoDivida, $debito)
    {
        foreach ($debito->dividas as $divida) {
            if ($divida->divida == $codigoDivida) {
                return $divida;
            }
        }

        return null;
    }

    /**
     * Desmembramento de inicial do foro.
     * @param array $dividas
     * @throws \Exception
     */
    public function desmembrarInicial(array $dividas)
    {
        if (!$this->desmembramentoRepository->validaNumpresSelecionados($dividas)) {
            throw new \Exception('Numpre selecionado parcialmente, não pode ser desmembrado.');
        }

        $iniciais = $this->desmembramentoRepository->getIniciaisPorDividas($dividas);

        $certidaoRepository = CertidaoRepository::getInstance();
        $certidaoRepository->setReturnFullItem(true);
        $certidaoRepository->setPersistPropagation(true);

        $inicialRepository = InicialRepository::getInstance();
        $processoForoRepository = ProcessoForoRepository::getInstance();
        $usuario = db_getsession('DB_id_usuario');

        foreach ($iniciais as $inicial => $certidoes) {
            $inicialAntiga = $inicialRepository->getByCode($inicial);

            $processoForo = $processoForoRepository->getByInicial($inicialAntiga->getCodigo());

            $inicialEntity = clone $inicialAntiga;
            $inicialEntity->setCodigo(null)
                ->setData(new \DateTime())
                ->setLogin($usuario);

            $inicialRepository->persist($inicialEntity);

            if (empty($processoForo)) {
                $this->persistInicialPartilha($inicialAntiga->getCodigo(), $inicialEntity->getCodigo());
            }

            foreach ($certidoes as $codigoCertidao) {
                $certidaoEntity = $certidaoRepository->getByCode($codigoCertidao);
                $this->removerDividasCertidao($certidaoEntity, $dividas);

                $cdaOld = $certidaoEntity->getCodigo();

                if ($this->desmembramentoRepository->cdaParcialmenteSelecionada(
                    $certidaoEntity->getCodigo(),
                    $dividas
                )) {
                    $certidaoEntity->setCodigo(null);

                    foreach ($certidaoEntity->getCertidaoDividas() as $certidaoDivida) {
                        $this->desmembramentoRepository->removeCertDiv(
                            $codigoCertidao,
                            $certidaoDivida->getDivida()->getCodigoDivida()
                        );

                        $certidaoDivida->setCodigoCertidao(null);
                    }

                    $certidaoRepository->persist($certidaoEntity);

                    /* PLUGIN CRA 3 */
                } else {
                    $this->desmembramentoRepository->removeInicialCert(
                        $inicialAntiga->getCodigo(),
                        $certidaoEntity->getCodigo()
                    );
                }

                $daoInicialCert = new \cl_inicialcert();
                $daoInicialCert->incluir($inicialEntity->getCodigo(), $certidaoEntity->getCodigo());

                if ($daoInicialCert->erro_status == 0) {
                    throw new \Exception($daoInicialCert->erro_msg);
                }

                foreach ($certidaoEntity->getNumpres() as $numpre) {
                    $inicialNumpre = new InicialNumpre();
                    $inicialNumpre->setInicial($inicialEntity->getCodigo());

                    $inicialNumpreRepository = new InicialNumpreRepository();
                    $inicialNumpreRepository->where(array('v59_inicial', '=', $inicialAntiga->getCodigo()))
                        ->where(array('v59_numpre', '=', $numpre))
                        ->save($inicialNumpre);
                }

                $historicoDesmembramentoBuilder = new HistoricoDesmembramentoBuilder();
                $historicoDesmembramento = $historicoDesmembramentoBuilder->criarHistoricoDesmembramento()
                    ->addInicial($inicialEntity->getCodigo())
                    ->addInicialOld($inicialAntiga->getCodigo())
                    ->addCda($certidaoEntity->getCodigo())
                    ->addCdaOld($cdaOld)
                    ->addUsuario($usuario)
                    ->getHistoricoDesmembramento();

                $this->historicoDesmembramentoRepositorio->inserir($historicoDesmembramento);
            }

            $this->iniciaisCriadas[] = $inicialEntity->getCodigo();

            $initials = array(
                $inicialAntiga->getCodigo(),
                $inicialEntity->getCodigo()
            );

            $inicialNomeRepository = InicialNomeRepository::getInstance();

            foreach ($initials as $initial) {
                $inicialNomeRepository->deleteByInitial($initial);
                $inicialNomeRepository->persistByInitial($initial);
            }

            if (!empty($processoForo)) {
                $processForoInicial = new ProcessoForoInicialEntity();
                $processForoInicial->setUsuario($usuario)
                    ->setData(new \DateTime())
                    ->setInicial($inicialEntity->getCodigo())
                    ->setProcessoForo($processoForo->getCodigo())
                    ->setAnulado(false);

                ProcessoForoInicialRepository::getInstance()->persist($processForoInicial);
            }

            foreach ($certidaoEntity->getNumpres() as $numpre) {
                ArreforoRepository::atualizaCertidao($numpre, $cdaOld, $certidaoEntity->getCodigo());
            }
        }

        /* PLUGIN CRA 1 */
    }

    /**
     * Remove as dividas que não foram selecionadas na rotina.
     * @param CertidaoEntity $certidao
     * @param $dividas
     */
    private function removerDividasCertidao(CertidaoEntity $certidao, $dividas)
    {
        $dividas = array_flip($dividas);

        foreach ($certidao->getCertidaoDividas() as $certidaoDivida) {
            $codigoDivida = $certidaoDivida->getDivida()->getCodigoDivida();

            if (!isset($dividas[$codigoDivida])) {
                $certidao->removeCertidaoDivida($codigoDivida);
            }
        }
    }

    /**
     * @param array $iniciais
     * @return array
     */
    public function getQuantidadeDeDividasPorInicial(array $iniciais)
    {
        $quantidadeDividasPorInicial = array();

        array_map(function ($inicial) use (&$quantidadeDividasPorInicial) {
            $quantidadeDividasPorInicial[$inicial->inicial] = array_key_exists(
                $inicial->inicial,
                $quantidadeDividasPorInicial
            )
                ? $quantidadeDividasPorInicial[$inicial->inicial] + count($inicial->dividas)
                : count($inicial->dividas);
        }, $iniciais);

        return $quantidadeDividasPorInicial;
    }

    /**
     * Retorna iniciais criadas no desmembramento.
     *
     * @return array
     */
    public function getIniciaisCriadas()
    {
        return $this->iniciaisCriadas;
    }

    /**
     * @param integer $inicialOld
     * @param integer $inicial
     */
    private function persistInicialPartilha($inicialOld, $inicial)
    {
        $inicialPartilhaRepository = InicialPartilhaRepository::getInstance();

        /** @var InicialPartilhaEntity[] $inicialPartilhas */
        $inicialPartilhas = array_merge(
            $inicialPartilhaRepository->getInicialPartilhaIsencao($inicialOld),
            $inicialPartilhaRepository->getInicialPartilhaPago($inicialOld)
        );

        if (empty($inicialPartilhas)) {
            return;
        }

        foreach ($inicialPartilhas as $inicialPartilha) {
            $inicialPartilha->setCodigo(null);
            $inicialPartilha->setCodigoInicial($inicial);

            foreach ($inicialPartilha->getCustas() as $custa) {
                $custa->setCodigo(null);
            }

            $inicialPartilhaRepository->persist($inicialPartilha);
        }
    }
}
