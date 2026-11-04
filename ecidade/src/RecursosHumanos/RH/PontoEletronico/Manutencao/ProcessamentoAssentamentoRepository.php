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

namespace ECidade\RecursosHumanos\RH\PontoEletronico\Manutencao;

use Assentamento;
use AssentamentoFactory;
use AssentamentoRepository;
use DBDate;
use ECidade\RecursosHumanos\RH\Assentamento\AssentamentoJustificativa;
use ECidade\RecursosHumanos\RH\Efetividade\Model\AssentamentoEncerramentoEfetividade;
use ECidade\RecursosHumanos\RH\Efetividade\Model\Periodo as Periodo;
use ECidade\RecursosHumanos\RH\Efetividade\Repository\AssentamentoEncerramentoEfetividadeRepository;
use ECidade\RecursosHumanos\RH\Efetividade\Repository\Jornada as JornadaRepository;
use ECidade\RecursosHumanos\RH\PontoEletronico\Calculo\Horas\BaseHora;
use ECidade\RecursosHumanos\RH\PontoEletronico\Calculo\Repository\DiaTrabalho as DiaTrabalhoRepository;
use ECidade\RecursosHumanos\RH\PontoEletronico\Configuracao\ParametrosRepository as ParametrosPontoRepository;
use ECidade\RecursosHumanos\RH\PontoEletronico\Manutencao\EspelhoPonto;
use Exception;
use Instituicao;
use ProgressBar;
use Servidor;

class ProcessamentoAssentamentoRepository
{
    /**
     * @param Periodo $oPeriodo
     * @param Servidor[] $aServidores
     * @param array $aTiposAssentamentos
     * @param Instituicao $oInstituicao
     * @throws Exception
     */
    public static function processarAssentamentosNoPeriodo(
        Periodo $oPeriodo,
        array $aServidores,
        array $aTiposAssentamentos,
        Instituicao $oInstituicao
    ) {
        $aDatasEfetividade = DBDate::getDatasNoIntervalo($oPeriodo->getDataInicio(), $oPeriodo->getDataFim());
        $barraProgresso = new ProgressBar('barraProgresso');
        $barraProgresso->updateMaxProgress(count($aServidores));
        $oDiaTrabalhoRepository = new DiaTrabalhoRepository();
        $iInstituicao = $oInstituicao->getCodigo();
        $parametrosPontoRepository = ParametrosPontoRepository::create();
        $assentamentoNaoPerdeDSR = $parametrosPontoRepository->getConfiguracoesAssentamentosNaoPerdeDSR($iInstituicao);

        $tiposAssentamentosNaoDescontaDSR = array();
        if (!empty($assentamentoNaoPerdeDSR)) {
            if (is_array($assentamentoNaoPerdeDSR->getCodigosTiposAssentamento())
                && count($assentamentoNaoPerdeDSR->getCodigosTiposAssentamento()) > 0) {
                $tiposAssentamentosNaoDescontaDSR = $assentamentoNaoPerdeDSR->getCodigosTiposAssentamento();
            }
        }

        $i = 0;

        foreach ($aServidores as $oServidor) {
            $dadosPonto = array(
                'nTotalHorasExt50diurnas' => array('0:00'),
                'nTotalHorasExt75diurnas' => array('0:00'),
                'nTotalHorasExt100diurnas' => array('0:00'),
                'nTotalHorasExt50noturnas' => array('0:00'),
                'nTotalHorasExt75noturnas' => array('0:00'),
                'nTotalHorasExt100noturnas' => array('0:00'),
                'nTotalHorasAdicional' => array('0:00'),
                'nTotalHorasFaltas' => array('0:00'),
                'nTotalHorasAtrasos' => array('0:00'),
            );

            $dsrPerdido = array();
            $dsrNoMes = array();
            $jornadasDSR = JornadaRepository::getJornadasPorPeriodo($oServidor, $oPeriodo, array('D'));
            krsort($jornadasDSR);

            if (!empty($jornadasDSR)) {
                do {
                    $jornadaDSR = (object)current($jornadasDSR);
                    $dsrNoMes[$jornadaDSR->data->getTimestamp()] = $jornadaDSR->data;

                    if ((int)$jornadaDSR->data->getMes() < (int)$oPeriodo->getDataInicio()->getMes()
                        || $jornadaDSR->data->getTimestamp() < $oPeriodo->getDataInicio()->getTimestamp()) {
                        break;
                    }
                } while (next($jornadasDSR));
                ksort($dsrNoMes);

                $primeiroDSR = current($dsrNoMes);
            }

            do {
                $oDataEfetividade = current($aDatasEfetividade);
                $oDiaTrabalho = $oDiaTrabalhoRepository->getApenasHorasCalculadasPorServidorNaData(
                    $oServidor,
                    $oDataEfetividade
                );

                if (empty($oDiaTrabalho)) {
                    continue;
                }

                $dadosPonto['nTotalHorasExt50diurnas'][] = $oDiaTrabalho->getHorasExtra50();
                $dadosPonto['nTotalHorasExt75diurnas'][] = $oDiaTrabalho->getHorasExtra75();
                $dadosPonto['nTotalHorasExt100diurnas'][] = $oDiaTrabalho->getHorasExtra100();
                $dadosPonto['nTotalHorasExt50noturnas'][] = $oDiaTrabalho->getHorasExtra50Noturna();
                $dadosPonto['nTotalHorasExt75noturnas'][] = $oDiaTrabalho->getHorasExtra75Noturna();
                $dadosPonto['nTotalHorasExt100noturnas'][] = $oDiaTrabalho->getHorasExtra100Noturna();
                $dadosPonto['nTotalHorasAdicional'][] = $oDiaTrabalho->getHorasAdicionalNoturno();
                $dadosPonto['nTotalHorasFaltas'][] = $oDiaTrabalho->getHorasFalta();
                $dadosPonto['nTotalHorasAtrasos'][] = $oDiaTrabalho->getHorasAtraso();
                $dadosPonto['nTotalHorasAtrasos'][] = $oDiaTrabalho->getHorasSaidaAntecipada();
            } while (next($aDatasEfetividade));

            if (!empty($dsrNoMes)) {
                if (!in_array($oPeriodo->getDataInicio()->getTimestamp(), array_keys($dsrNoMes))) {
                    $aDatasEfetividade = DBDate::getDatasNoIntervalo($primeiroDSR, $oPeriodo->getDataFim());
                }

                end($aDatasEfetividade);
                do {
                    $oDataEfetividade = current($aDatasEfetividade);
                    $oDiaTrabalho = $oDiaTrabalhoRepository->getApenasHorasCalculadasPorServidorNaData(
                        $oServidor,
                        $oDataEfetividade
                    );

                    if ($oDiaTrabalho->getHorasFalta()) {
                        $horasFalta = BaseHora::converterStringHoraEmDateTime($oDiaTrabalho->getHorasFalta());

                        if (BaseHora::converterDateTimeEmMinutos($horasFalta) > 0) {
                            $assentamentosNaData = AssentamentoRepository::getAssentamentosServidorPorTipoENatureza(
                                $oServidor,
                                'S',
                                $oDiaTrabalho->getData(),
                                array(Assentamento::NATUREZA_JUSTIFICATIVA, Assentamento::NATUREZA_ABONO_FALTA)
                            );

                            if (!empty($assentamentosNaData) && is_array($assentamentosNaData)) {
                                array_walk($assentamentosNaData, function (
                                    $value,
                                    $key
                                ) use (
                                    &$assentamentosNaData,
                                    $tiposAssentamentosNaoDescontaDSR
                                ) {
                                    if (!in_array($value->getTipoAssentamento(), $tiposAssentamentosNaoDescontaDSR)) {
                                        unset($assentamentosNaData[$key]);
                                    }
                                });
                            }

                            if (empty($assentamentosNaData)) {
                                do {
                                    $dsrAtual = current($dsrNoMes);

                                    if ($oDataEfetividade->getTimestamp() < $dsrAtual->getTimestamp()) {
                                        $dsrPerdido[$dsrAtual->getTimestamp()] = $dsrAtual->getDate();
                                        break;
                                    }
                                } while (next($dsrNoMes));
                                reset($dsrNoMes);
                            }
                        }
                    }
                } while (prev($aDatasEfetividade));
                reset($aDatasEfetividade);
            }

            $aDatasEfetividade = DBDate::getDatasNoIntervalo($oPeriodo->getDataInicio(), $oPeriodo->getDataFim());

            $sHoras = null;
            foreach ($aTiposAssentamentos as $sTipoHora => $oTipoAssentamento) {
                $sHoras = null;

                switch ($sTipoHora) {
                    case 'extra50diurna':
                        $sHoras = EspelhoPonto::somarTotalizador($dadosPonto['nTotalHorasExt50diurnas']);
                        break;

                    case 'extra75diurna':
                        $sHoras = EspelhoPonto::somarTotalizador($dadosPonto['nTotalHorasExt75diurnas']);
                        break;

                    case 'extra100diurna':
                        $sHoras = EspelhoPonto::somarTotalizador($dadosPonto['nTotalHorasExt100diurnas']);
                        break;

                    case 'extra50noturna':
                        $sHoras = EspelhoPonto::somarTotalizador($dadosPonto['nTotalHorasExt50noturnas']);
                        break;

                    case 'extra75noturna':
                        $sHoras = EspelhoPonto::somarTotalizador($dadosPonto['nTotalHorasExt75noturnas']);
                        break;

                    case 'extra100noturna':
                        $sHoras = EspelhoPonto::somarTotalizador($dadosPonto['nTotalHorasExt100noturnas']);
                        break;

                    case 'adicionalnoturno':
                        $sHoras = EspelhoPonto::somarTotalizador($dadosPonto['nTotalHorasAdicional']);
                        break;

                    case 'falta':
                        $sHoras = EspelhoPonto::somarTotalizador($dadosPonto['nTotalHorasFaltas']);
                        break;

                    case 'atraso':
                        $sHoras = EspelhoPonto::somarTotalizador($dadosPonto['nTotalHorasAtrasos']);
                        break;

                    case 'faltas_dsr':
                        foreach ($dsrPerdido as $dataFaltasDSR) {
                            $dataConcessaoFaltasDSR = new DBDate($dataFaltasDSR);
                            $dataTerminoFaltasDSR = clone $dataConcessaoFaltasDSR;

                            $oAssentamento = AssentamentoFactory::getInstanciaPorNatureza(
                                $oTipoAssentamento->getSequencial()
                            );
                            $oAssentamento->setMatricula($oServidor->getMatricula());
                            $oAssentamento->setServidor($oServidor);
                            $oAssentamento->setTipoAssentamento($oTipoAssentamento->getSequencial());
                            $oAssentamento->setDataConcessao($dataConcessaoFaltasDSR);
                            $oAssentamento->setDataTermino($dataTerminoFaltasDSR);
                            $oAssentamento->setDias(1);
                            $oAssentamento->setDataLancamento(new DBDate(date('Y-m-d')));

                            if ($oAssentamento instanceof AssentamentoJustificativa) {
                                $oAssentamento->setTotal();
                            }

                            $assentamento = AssentamentoRepository::persist($oAssentamento);
                            self::adicionarAssentamentosEncerramentoEfetividade(
                                $assentamento,
                                $oPeriodo,
                                $oInstituicao
                            );
                        }
                        break;
                }

                if ($sTipoHora != 'faltas_dsr') {
                    if (empty($sHoras) || $sHoras == '00:00') {
                        continue;
                    }

                    $oAssentamento = AssentamentoFactory::getInstanciaPorNatureza($oTipoAssentamento->getSequencial());
                    $oAssentamento->setMatricula($oServidor->getMatricula());
                    $oAssentamento->setServidor($oServidor);
                    $oAssentamento->setTipoAssentamento($oTipoAssentamento->getSequencial());
                    $oAssentamento->setDataConcessao($oPeriodo->getDataFim());
                    $oAssentamento->setDataTermino($oPeriodo->getDataFim());
                    $oAssentamento->setDataLancamento(new DBDate(date('Y-m-d')));
                    $oAssentamento->setHora($sHoras);

                    if ($oAssentamento instanceof AssentamentoJustificativa) {
                        $oAssentamento->setTotal();
                    }

                    $assentamento = AssentamentoRepository::persist($oAssentamento);
                    self::adicionarAssentamentosEncerramentoEfetividade(
                        $assentamento,
                        $oPeriodo,
                        $oInstituicao
                    );
                }
            }

            $i++;
            $barraProgresso->updatePercentual($i);
        }
    }

    /**
     * @param Assentamento $assentamento
     * @param Periodo $periodo
     * @param Instituicao $instituicao
     * @throws Exception
     */
    private static function adicionarAssentamentosEncerramentoEfetividade(
        Assentamento $assentamento,
        Periodo $periodo,
        Instituicao $instituicao
    ) {
        $AssentamentosEncerramentoEfetividade = new AssentamentoEncerramentoEfetividade();
        $AssentamentosEncerramentoEfetividade->setAssentamento($assentamento);
        $AssentamentosEncerramentoEfetividade->setAno($periodo->getExercicio());
        $AssentamentosEncerramentoEfetividade->setMes($periodo->getCompetencia());
        $AssentamentosEncerramentoEfetividade->setInstituicao($instituicao);
        AssentamentoEncerramentoEfetividadeRepository::save($AssentamentosEncerramentoEfetividade);
    }
}
