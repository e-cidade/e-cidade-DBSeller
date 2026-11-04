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

namespace ECidade\Tributario\Arrecadacao\Custas\Relatorio;

use ECidade\Tributario\Arrecadacao\Custas\Enum\TipoLancamento;
use ECidade\Tributario\Arrecadacao\Relatorio\Contract;
use ECidade\Tributario\Divida\Certidao\Certidao;
use ECidade\Tributario\Divida\Termo\Repository\Termo;
use ECidade\Tributario\Juridico\Inicial\Inicial;
use ECidade\Tributario\Juridico\InicialPartilha\InicialPartilha as InicialPartilhaEntity;
use ECidade\Tributario\Juridico\InicialPartilha\InicialPartilhaCustas as InicialPartilhaCustasEntity;
use ECidade\Tributario\Juridico\Interfaces\Partilha;
use ECidade\Tributario\Juridico\ProcessoForo\ProcessoForo as ProcessoForoEntity;
use ECidade\Tributario\Juridico\Inicial\Inicial as InicialEntity;
use ECidade\Tributario\Juridico\ProcessoForo\ProcessoForo;
use ECidade\Tributario\Arrecadacao\Custas\Service;
use ECidade\Tributario\Juridico\ProcessoForoPartilha\Repository\ProcessoForoPartilha as ProcessoForoPartilhaRepository;

/**
 * Relatório para exibir custas de um processo ou inicial.
 *
 * @author Leonardo Oliveira <leonardo.malia@dbseller.com.br>
 */
class Custas extends Contract
{
    const ALTURA_LINHA = 4;

    /** @var ProcessoForoEntity[] */
    private $processos = array();

    /** @var InicialEntity[] */
    private $iniciais = array();

    /** @var array */
    private $recibos = null;

    /** @var integer */
    private $cpfCnpj;

    /** @var string */
    private $contribuinte;

    /** @var array */
    private $datasVencimentos;

    /** @var float */
    private $valorTotalGeral = 0;

    /**
     * @throws \Exception
     */
    protected function montar()
    {
        if (empty($this->processos) && empty($this->iniciais) && empty($this->recibos)) {
            throw new \Exception('Nenhum processo, inicial ou recibo informados.');
        }

        foreach ($this->recibos as $recibo) {
            $this->montarQuadroParcelamento($recibo);
        }

        foreach ($this->processos as $indice => $processo) {
            $this->montarQuadroProcesso(
                $processo,
                $indice
            );
        }

        foreach ($this->iniciais as $indice => $inicial) {
            $this->montarQuadroInicial(
                $inicial,
                $indice
            );
        }

        $this->montarTotalGeral();
    }

    /**
     * @param $datasVencimentos
     * @return $this
     */
    public function setDatasVencimentos($datasVencimentos)
    {
        $this->datasVencimentos = $datasVencimentos;
        return $this;
    }

    /**
     * @param ProcessoForoEntity[] $processos
     * @return Custas
     */
    public function setProcessos($processos)
    {
        $this->processos = $processos;
        return $this;
    }

    /**
     * @param InicialEntity[] $iniciais
     * @return Custas
     */
    public function setIniciais($iniciais)
    {
        $this->iniciais = $iniciais;
        return $this;
    }

    /**
     * @param array $recibos
     * @return Custas
     */
    public function setRecibos(array $recibos)
    {
        $this->recibos = $recibos;
        return $this;
    }

    /**
     * @param int $cpfCnpj
     * @return Custas
     */
    public function setCpfCnpj($cpfCnpj)
    {
        $this->cpfCnpj = $cpfCnpj;
        return $this;
    }

    /**
     * @param string $contribuinte
     * @return Custas
     */
    public function setContribuinte($contribuinte)
    {
        $this->contribuinte = $contribuinte;
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function montarEnteFederativo()
    {
        parent::montarEnteFederativo();

        $this->pdf->addHeaderDescription('CPF/CNPJ: ' . $this->cpfCnpj);
        $this->pdf->addHeaderDescription('Contribuinte: ' . $this->contribuinte);
    }

    /**
     * @param Inicial $inicial
     * @param integer $indice
     */
    private function montarQuadroInicial($inicial, $indice)
    {
        $certidoes = $inicial->getCertidoes();
        $title = "Inicial: " . $inicial->getCodigo();
        $height = $this->getHeight($certidoes, $inicial->getInicialPartilhas());

        $this->addPage($height);
        $this->montarTituloQuadro($title);

        $x = $this->montarCabecalhoColuna(27, self::ALTURA_LINHA, 'CDA');
        $x = $this->montarCabecalhoColuna(27, self::ALTURA_LINHA, 'Exercício', $x);
        $x = $this->montarCabecalhoColuna(27, self::ALTURA_LINHA, 'Valor Histórico', $x);
        $x = $this->montarCabecalhoColuna(27, self::ALTURA_LINHA, 'Valor Corrigido', $x);
        $x = $this->montarCabecalhoColuna(27, self::ALTURA_LINHA, 'Juros', $x);
        $x = $this->montarCabecalhoColuna(27, self::ALTURA_LINHA, 'Multa', $x);
        $this->montarCabecalhoColuna(27, self::ALTURA_LINHA, 'Total', $x);

        $this->pdf->Ln();

        $total = $this->montarDadosCdas($certidoes, $indice);

        $this->pdf->Ln(2);
        $this->montarQuadroCustas($inicial->getInicialPartilhas(), $total);
        $this->pdf->Ln(5);
    }

    /**
     * @param ProcessoForo $processo
     * @param string $title
     */
    private function montarQuadroProcesso($processo, $indice)
    {
        $certidoes = array();
        $iniciais = $processo->getIniciais();
        $title = 'Processo: ' . $processo->getCodigoForo();

        foreach ($iniciais as $inicial) {
            $certidoes = array_merge($certidoes, $inicial->getCertidoes());
        }

        $height = $this->getHeight($certidoes, $processo->getProcessoForoPartilhas());

        $this->addPage($height);

        $this->montarTituloQuadro($title);

        $x = $this->montarCabecalhoColuna(23.625, self::ALTURA_LINHA, 'Inicial');
        $x = $this->montarCabecalhoColuna(23.625, self::ALTURA_LINHA, 'CDA', $x);
        $x = $this->montarCabecalhoColuna(23.625, self::ALTURA_LINHA, 'Exercício', $x);
        $x = $this->montarCabecalhoColuna(23.625, self::ALTURA_LINHA, 'Valor Histórico', $x);
        $x = $this->montarCabecalhoColuna(23.625, self::ALTURA_LINHA, 'Valor Corrigido', $x);
        $x = $this->montarCabecalhoColuna(23.625, self::ALTURA_LINHA, 'Juros', $x);
        $x = $this->montarCabecalhoColuna(23.625, self::ALTURA_LINHA, 'Multa', $x);
        $this->montarCabecalhoColuna(23.625, self::ALTURA_LINHA, 'Total', $x);

        $this->pdf->Ln();

        $total = $this->montarDadosCdasProcesso($iniciais, $indice);

        $this->pdf->Ln(2);
        $this->montarQuadroCustas($processo->getProcessoForoPartilhas(), $total);
        $this->pdf->Ln(5);
    }

    /**
     * @param $title
     */
    private function montarTituloQuadro($title)
    {
        $this->pdf->SetFontSize(10);
        $this->pdf->setBold(1);
        $this->pdf->MultiCell(189, 8, $title, 1, 'C', 1);
        $this->pdf->setBold(0);
        $this->pdf->SetFontSize(7);
    }

    /**
     * @param Partilha[] $partilhas
     * @param float $subtotal
     */
    private function montarQuadroCustas($partilhas, $subtotal)
    {
        $this->montarCabecalhoColuna(80, self::ALTURA_LINHA, 'Taxas/Custas');
        $this->pdf->Ln();
        $total = 0;

        foreach ($partilhas as $partilha) {
            foreach ($partilha->getCustas() as $custa) {
                if (($this->pdf->getAvailHeight() - 15) < static::ALTURA_LINHA) {
                    $this->pdf->AddPage();
                }

                $this->montarLinha(60, $custa->getTaxa()->getDescricao(), false, false, false, false, 'L');

                if ($partilha->getTipoLancamento() == TipoLancamento::PAGAMENTO
                    and !$partilha->getPartilhaPagaMigracao()
                ) {
                    $this->montarLinha(20, $custa->getValor(), true);
                    $total += $custa->getValor();
                }

                if ($partilha->getTipoLancamento() == TipoLancamento::ISENCAO) {
                    $this->montarLinha(20, "ISENTO", false);
                }

                if ($partilha->getTipoLancamento() == TipoLancamento::PAGAMENTO_MANUAL
                    or $partilha->getPartilhaPagaMigracao()
                ) {
                    $this->montarLinha(20, "PAGO", false);
                }

                $this->pdf->Ln();
            }
        }

        $this->montarLinha(60, 'Total Taxas/Custas', false, true, true);
        $this->montarLinha(20, $total, true, true, true);
        $this->pdf->Ln();

        $this->montarLinha(60, 'Total', false, true, true);
        $this->montarLinha(20, $subtotal + $total, true, true, true);
        $this->pdf->Ln();
        $this->valorTotalGeral += $subtotal + $total;
    }

    /**
     * @param Certidao[] $certidoes
     * @param integer $indice
     * @return float
     */
    private function montarDadosCdas($certidoes, $indice)
    {
        require_once(modification("libs/db_sql.php"));
        $totalValorHist = 0;
        $totalValorCorrigido = 0;
        $totalJuros = 0;
        $totalMulta = 0;
        $total = 0;

        foreach ($certidoes as $certidao) {
            $exercicio = null;
            $valorHist = 0;
            $valorCorrigido = 0;
            $juros = 0;
            $multa = 0;
            $valorTotal = 0;

            if (($this->pdf->getAvailHeight() - 15) < static::ALTURA_LINHA) {
                $this->pdf->AddPage();
            }

            $numpres = (Object)[];
            $listaExercicios = (Object)[];
            foreach ($certidao->getCertidaoDividas() as $certidaoDivida) {
                $numpres->{$certidaoDivida->getDivida()->getNumpre()}
                ->{'listaNumpar'}[] = $certidaoDivida->getDivida()->getNumpar();

                $numpres->{$certidaoDivida->getDivida()->getNumpre()}
                ->{'exercicio'} = $certidaoDivida->getDivida()->getExercicio();

                $listaExercicios->{$certidaoDivida->getDivida()->getExercicio()} = (Object)[];
            }


            foreach ($numpres as $numpre => $numpares) {
                $sWhere  = " and y.k00_numpre = {$numpre} ";
                $sWhere .= " and y.k00_numpar in (" . implode(', ', $numpares->listaNumpar) . ") ";

                $rsDebitosNumpre = \debitos_numpre(
                    $numpre,
                    0,
                    0,
                    strtotime($this->datasVencimentos[$indice]),
                    db_getsession("DB_anousu"),
                    0,
                    "",
                    "",
                    " and y.k00_hist <> 918 $sWhere "
                );

                foreach (pg_fetch_all($rsDebitosNumpre) as $debitoNumpre) {
                    $listaExercicios->{$numpares->exercicio}->valorHist += $debitoNumpre['vlrhis'];
                    $listaExercicios->{$numpares->exercicio}->valorCorrigido += $debitoNumpre['vlrcor'];
                    $listaExercicios->{$numpares->exercicio}->juros += $debitoNumpre['vlrjuros'];
                    $listaExercicios->{$numpares->exercicio}->multa += $debitoNumpre['vlrmulta'];
                    $listaExercicios->{$numpares->exercicio}->valorTotal += $debitoNumpre['total'];
                    $listaExercicios->{$numpares->exercicio}->exercicio = $numpares->exercicio;
                }
            }


            foreach ($listaExercicios as $exercicio) {
                $this->montarLinha(27, $certidao->getCodigo());
                $this->montarLinha(27, $exercicio->exercicio);
                $this->montarLinha(27, $exercicio->valorHist, true);
                $this->montarLinha(27, $exercicio->valorCorrigido, true);
                $this->montarLinha(27, $exercicio->juros, true);
                $this->montarLinha(27, $exercicio->multa, true);
                $this->montarLinha(27, $exercicio->valorTotal, true);

                $totalValorHist += $exercicio->valorHist;
                $totalValorCorrigido += $exercicio->valorCorrigido;
                $totalJuros += $exercicio->juros;
                $totalMulta += $exercicio->multa;
                $total += $exercicio->valorTotal;

                $this->pdf->Ln();
            }
        }

        $this->montarLinha(27 * 2, 'Subtotal', false, true, true);
        $this->montarLinha(27, $totalValorHist, true, true, true);
        $this->montarLinha(27, $totalValorCorrigido, true, true, true);
        $this->montarLinha(27, $totalJuros, true, true, true);
        $this->montarLinha(27, $totalMulta, true, true, true);
        $this->montarLinha(27, $total, true, true, true);

        $this->pdf->Ln();

        return $total;
    }

    /**
     * @param InicialEntity[] $iniciais
     * @return float
     */
    private function montarDadosCdasProcesso($iniciais, $indice)
    {
        require_once(modification("libs/db_sql.php"));
        $totalValorHist = 0;
        $totalValorCorrigido = 0;
        $totalJuros = 0;
        $totalMulta = 0;
        $total = 0;

        foreach ($iniciais as $inicial) {
            foreach ($inicial->getCertidoes() as $certidao) {
                $exercicio = null;
                $valorHist = 0;
                $valorCorrigido = 0;
                $juros = 0;
                $multa = 0;
                $valorTotal = 0;

                if (($this->pdf->getAvailHeight() - 15) < static::ALTURA_LINHA) {
                    $this->pdf->AddPage();
                }

                $numpres = (Object)[];
                $listaExercicios = (Object)[];
                foreach ($certidao->getCertidaoDividas() as $certidaoDivida) {
                    $numpres->{$certidaoDivida->getDivida()->getNumpre()}
                    ->{'listaNumpar'}[] = $certidaoDivida->getDivida()->getNumpar();

                    $numpres->{$certidaoDivida->getDivida()->getNumpre()}
                    ->{'exercicio'} = $certidaoDivida->getDivida()->getExercicio();

                    $listaExercicios->{$certidaoDivida->getDivida()->getExercicio()} = (Object)[];
                }


                foreach ($numpres as $numpre => $numpares) {
                    $sWhere  = " and y.k00_numpre = {$numpre} ";
                    $sWhere .= " and y.k00_numpar in (" . implode(', ', $numpares->listaNumpar) . ") ";

                    $rsDebitosNumpre = \debitos_numpre(
                        $numpre,
                        0,
                        0,
                        strtotime($this->datasVencimentos[$indice]),
                        db_getsession("DB_anousu"),
                        0,
                        "",
                        "",
                        " and y.k00_hist <> 918 $sWhere "
                    );

                    foreach (pg_fetch_all($rsDebitosNumpre) as $debitoNumpre) {
                        $listaExercicios->{$numpares->exercicio}->valorHist += $debitoNumpre['vlrhis'];
                        $listaExercicios->{$numpares->exercicio}->valorCorrigido += $debitoNumpre['vlrcor'];
                        $listaExercicios->{$numpares->exercicio}->juros += $debitoNumpre['vlrjuros'];
                        $listaExercicios->{$numpares->exercicio}->multa += $debitoNumpre['vlrmulta'];
                        $listaExercicios->{$numpares->exercicio}->valorTotal += $debitoNumpre['total'];
                        $listaExercicios->{$numpares->exercicio}->exercicio = $numpares->exercicio;
                    }
                }


                foreach ($listaExercicios as $exercicio) {
                    $this->montarLinha(23.625, $inicial->getCodigo());
                    $this->montarLinha(23.625, $certidao->getCodigo());
                    $this->montarLinha(23.625, $exercicio->exercicio);
                    $this->montarLinha(23.625, $exercicio->valorHist, true);
                    $this->montarLinha(23.625, $exercicio->valorCorrigido, true);
                    $this->montarLinha(23.625, $exercicio->juros, true);
                    $this->montarLinha(23.625, $exercicio->multa, true);
                    $this->montarLinha(23.625, $exercicio->valorTotal, true);

                    $totalValorHist += $exercicio->valorHist;
                    $totalValorCorrigido += $exercicio->valorCorrigido;
                    $totalJuros += $exercicio->juros;
                    $totalMulta += $exercicio->multa;
                    $total += $exercicio->valorTotal;

                    $this->pdf->Ln();
                }
            }
        }

        $this->montarLinha(23.625 * 3, 'Subtotal', false, true, true);
        $this->montarLinha(23.625, $totalValorHist, true, true, true);
        $this->montarLinha(23.625, $totalValorCorrigido, true, true, true);
        $this->montarLinha(23.625, $totalJuros, true, true, true);
        $this->montarLinha(23.625, $totalMulta, true, true, true);
        $this->montarLinha(23.625, $total, true, true, true);

        $this->pdf->Ln();

        return $total;
    }

    /**
     * @param int $height
     */
    private function addPage($height)
    {
        if ($height > ($this->pdf->getAvailHeight() - 5)) {
            $this->pdf->AddPage();
        }
    }

    /**
     * @param array $certidoes
     * @param array $partilhas
     * @return int
     */
    private function getHeight($certidoes, $partilhas)
    {
        $custas = 0;

        foreach ($partilhas as $partilha) {
            $custas += count($partilha->getCustas());
        }

        $height = 3;
        $height += (self::ALTURA_LINHA * 2);
        $height += (self::ALTURA_LINHA * count($certidoes));
        $height += 2;
        $height += (self::ALTURA_LINHA * 2) + ($custas * self::ALTURA_LINHA) + self::ALTURA_LINHA;
        $height += 5;

        return $height;
    }

    /**
     * @param \recibo $recibo
     * @throws \ParameterException
     */
    private function montarQuadroParcelamento(\recibo $recibo)
    {
        require_once(modification("libs/db_sql.php"));

        if (!$recibo instanceof \recibo) {
            throw new \ParameterException("O recibo informado para imprimir as custas não é um recibo válido.");
        }

        $numpres = $recibo->getDebitosRecibo();
        $numpares = array();

        foreach ($numpres as $numpre) {
            $numpares[] = $numpre->k00_numpar;
        }

        $numpre = $numpres[0]->k00_numpre;
        $termoRepository = Termo::getInstance();
        $termo = $termoRepository->getByNumpre($numpre);

        $this->montarTituloQuadro('Parcelamento: ' . $termo->getCodigo());

        $x = $this->montarCabecalhoColuna(37.8, self::ALTURA_LINHA, 'Valor Histórico');
        $x = $this->montarCabecalhoColuna(37.8, self::ALTURA_LINHA, 'Valor Corrigido', $x);
        $x = $this->montarCabecalhoColuna(37.8, self::ALTURA_LINHA, 'Juros', $x);
        $x = $this->montarCabecalhoColuna(37.8, self::ALTURA_LINHA, 'Multa', $x);
        $this->montarCabecalhoColuna(37.8, self::ALTURA_LINHA, 'Total', $x);

        $sWhere  = " and y.k00_numpre = {$numpre} and y.k00_numpar in (" . implode(', ', $numpares) . ") ";

        $rsDebitosNumpre = \debitos_numpre(
            $numpre,
            0,
            0,
            strtotime($recibo->getDataVencimento()),
            db_getsession("DB_anousu"),
            0,
            "",
            "",
            " and y.k00_hist <> 918 $sWhere "
        );

        $valorHist  = 0;
        $valorCorrigido = 0;
        $juros = 0;
        $multa = 0;
        $valorTotal = 0;

        foreach (pg_fetch_all($rsDebitosNumpre) as $debitoNumpre) {
            $valorHist += $debitoNumpre['vlrhis'];
            $valorCorrigido += $debitoNumpre['vlrcor'];
            $juros += $debitoNumpre['vlrjuros'];
            $multa += $debitoNumpre['vlrmulta'];
            $valorTotal += $debitoNumpre['total'];
        }

        $this->pdf->Ln();

        $this->montarLinha(37.8, $valorHist, true);
        $this->montarLinha(37.8, $valorCorrigido, true);
        $this->montarLinha(37.8, $juros, true);
        $this->montarLinha(37.8, $multa, true);
        $this->montarLinha(37.8, $valorTotal, true);

        $this->pdf->Ln();

        $reciboService = new Service\Recibo(null);
        $custas = $reciboService->getCustas($recibo->getNumpreRecibo());

        $processoForoPartilhaRepository = ProcessoForoPartilhaRepository::getInstance();
        $custasPagas = $processoForoPartilhaRepository->getPagoManualByNumnov($recibo->getNumpreRecibo());

        $custas = array_merge($custas, $custasPagas);

        $partilhas = array();

        foreach ($custas as $custa) {
            $taxa = new \taxa($custa->taxa);

            $inicialPartilha = new InicialPartilhaEntity();
            $inicialPartilha->setTipoLancamento($custa->tipolancamento);

            $inicialPartilhaCusta = new InicialPartilhaCustasEntity();
            $inicialPartilhaCusta->setTaxa($taxa);
            $inicialPartilhaCusta->setValor($custa->valor);

            $inicialPartilha->addCustas($inicialPartilhaCusta);

            $partilhas[] = $inicialPartilha;
        }

        $this->pdf->Ln(2);
        $this->montarQuadroCustas($partilhas, $valorTotal);
        $this->pdf->Ln(5);
    }

    private function montarTotalGeral()
    {
        if (($this->pdf->getAvailHeight() - 15) < static::ALTURA_LINHA) {
            $this->pdf->AddPage();
        }

        $this->pdf->Ln(3);
        $this->montarLinha(151.2, "Total Geral", false, true, true, false, "R");
        $this->montarLinha(37.8, $this->valorTotalGeral, true, true, true, true, "R");
    }
}
