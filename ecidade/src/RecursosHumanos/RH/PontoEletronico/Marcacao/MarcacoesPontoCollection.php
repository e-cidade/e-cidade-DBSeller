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

namespace ECidade\RecursosHumanos\RH\PontoEletronico\Marcacao;

use ECidade\RecursosHumanos\RH\PontoEletronico\Calculo\Horas\BaseHora;
use ECidade\RecursosHumanos\RH\PontoEletronico\Calculo\Model\DiaTrabalho;
use ECidade\RecursosHumanos\RH\PontoEletronico\Configuracao\Model\Justificativa;
use stdClass;

/**
 * Classe responsável por montar uma coleção de marcações
 * Class MarcacoesPontoCollection
 * @package ECidade\RecursosHumanos\RH\PontoEletronico\Marcacao
 * @author Renan Silva <renan.silva@dbseller.com.br>
 */
class MarcacoesPontoCollection
{

  /**
   * @var MarcacaoPonto[]
   */
    private $aMarcacoes = array();

    /**
     * @var array
     */
    private $aMarcacoesFaltantes = array(
        MarcacaoPonto::ENTRADA_1 => MarcacaoPonto::ENTRADA_1,
        MarcacaoPonto::SAIDA_1   => MarcacaoPonto::SAIDA_1,
        MarcacaoPonto::ENTRADA_2 => MarcacaoPonto::ENTRADA_2,
        MarcacaoPonto::SAIDA_2   => MarcacaoPonto::SAIDA_2,
        MarcacaoPonto::ENTRADA_3 => MarcacaoPonto::ENTRADA_3,
        MarcacaoPonto::SAIDA_3   => MarcacaoPonto::SAIDA_3
    );

  /**
   * @param array $aMarcacoes
   * @param string Y-m-d $dataDiaTrabalho
   * @return MarcacoesPontoCollection
   * @throws \ParameterException
   */
    public static function getCollectionMarcacoesFromArray(array $aMarcacoes, DiaTrabalho $dataDiaTrabalho)
    {
        $oCollection = new MarcacoesPontoCollection;
        $dataAtual = $dataDiaTrabalho->getData()->getDate();
        $jornada = $dataDiaTrabalho->getJornada();

        $marcacoesValidas = array_filter($aMarcacoes, function (stdClass $marcacao) {
            return !empty($marcacao->hora);
        });
        $possuiSomenteUmaMarcacao = count($marcacoesValidas) === 1;

        if ($possuiSomenteUmaMarcacao) {
            foreach ($aMarcacoes as $key => $dadosMarcacao) {
                if ($jornada->temHorarioNoturno($dadosMarcacao->data)) {
                    $dateTimeMarcacao = empty($dadosMarcacao->hora)
                      ? null
                      : new \DateTime("{$dadosMarcacao->data} {$dadosMarcacao->hora}");

                    $marcacao = MarcacoesPontoFactory::create($dateTimeMarcacao, 1, $dadosMarcacao->codigo);
                    $marcacao->setJornada($jornada);

                    if ($marcacao->getMarcacao()) {
                        $tipoMarcacao = $marcacao->getTipoMarcacaoPorProximidade();
                        $marcacao->setTipo($tipoMarcacao);
                        $dadosMaracaoAnterior = $aMarcacoes[$tipoMarcacao - 1];
                        $aMarcacoes[$tipoMarcacao - 1] = $dadosMarcacao;
                        $aMarcacoes[$key] = $dadosMaracaoAnterior;
                    }
                }
            }
        }

        for ($iMarcacoes=0; $iMarcacoes < count($aMarcacoes); $iMarcacoes++) {
            $oStdMarcacao = $aMarcacoes[$iMarcacoes];

            $oStdMarcacao->data = $iMarcacoes == 0 ? $dataAtual : $oStdMarcacao->data;
            $oStdMarcacao->data = empty($oStdMarcacao->hora) ? $dataAtual : $oStdMarcacao->data;

            if ($iMarcacoes > 0 &&
                !empty($oStdMarcacao->hora) &&
                !empty($aMarcacoes[$iMarcacoes - 1]->hora) &&
                !empty($dataAtual)) {
                if ($oStdMarcacao->data <= $dataAtual) {
                    $oStdMarcacao->data = $dataAtual;
                }

                if ($oStdMarcacao->hora < $aMarcacoes[$iMarcacoes - 1]->hora) {
                    $novaData = new \DBDate($dataAtual);
                    $novaData->adiantarPeriodo(1, 'd');

                    $oStdMarcacao->data = $novaData->getDate();
                    $dataAtual = $novaData->getDate();
                }
            }

            $oDadosMarcacao =!empty($oStdMarcacao->hora)
                ? new \DateTime($oStdMarcacao->data .' '. $oStdMarcacao->hora)
                : null;
            $oMarcacao    = MarcacoesPontoFactory::create($oDadosMarcacao, ($iMarcacoes+1), $oStdMarcacao->codigo);

            $origemMarcacaoGeradaRelogio = !empty($oStdMarcacao->origem_marcacao)
                ? $oStdMarcacao->origem_marcacao
                : 'R';

            if ($oMarcacao instanceof MarcacaoPontoSaida && $iMarcacoes % 2 != 0) {
                $oMarcacao->setMarcacaoEntrada($oCollection->getMarcacao($iMarcacoes)->getMarcacao());
            }

            if ($oMarcacao instanceof MarcacaoPonto) {
                $oMarcacao->setData(new \DBDate($oStdMarcacao->data));
                $oMarcacao->setOrigemMarcacao($origemMarcacaoGeradaRelogio);
                $oMarcacao->setManual((boolean)$oStdMarcacao->manual);

                if (!empty($oStdMarcacao->justificativa)) {
                    if ($oStdMarcacao->justificativa instanceof Justificativa) {
                        $oMarcacao->setJustificativa($oStdMarcacao->justificativa);
                    } else {
                        $justificativa = new Justificativa();
                        $justificativa->setCodigo($oStdMarcacao->justificativa->codigo);
                        $justificativa->setDescricao($oStdMarcacao->justificativa->descricao);
                        $justificativa->setAbreviacao($oStdMarcacao->justificativa->abreviacao);
                        $justificativa->setAbono($oStdMarcacao->justificativa->abono);

                        $oMarcacao->setJustificativa($justificativa);
                    }
                }
            }

            $oCollection->add($oMarcacao);
        }

        return $oCollection;
    }

  /**
   * @param $oMarcacao
   */
    public function add($oMarcacao)
    {

        if ($oMarcacao instanceof MarcacaoPonto) {
            $this->aMarcacoes[$oMarcacao->getTipo()] = $oMarcacao;
            unset($this->aMarcacoesFaltantes[$oMarcacao->getTipo()]);
        }
    }

    public function getMarcacaoEntrada1()
    {
        return $this->getMarcacao(MarcacaoPonto::ENTRADA_1);
    }

    public function getMarcacaoSaida1()
    {
        return $this->getMarcacao(MarcacaoPonto::SAIDA_1);
    }

    public function getMarcacaoEntrada2()
    {
        return $this->getMarcacao(MarcacaoPonto::ENTRADA_2);
    }

    public function getMarcacaoSaida2()
    {
        return $this->getMarcacao(MarcacaoPonto::SAIDA_2);
    }

    public function getMarcacaoEntrada3()
    {
        return $this->getMarcacao(MarcacaoPonto::ENTRADA_3);
    }

    public function getMarcacaoSaida3()
    {
        return $this->getMarcacao(MarcacaoPonto::SAIDA_3);
    }

    public function getMarcacao($iTipo)
    {
        return isset($this->aMarcacoes[$iTipo]) ? $this->aMarcacoes[$iTipo] : null;
    }

    public function getMarcacoesEntrada()
    {

        $aMarcacoes   = array();

        if ($this->getMarcacaoEntrada1() !== null) {
            $aMarcacoes[] = $this->getMarcacaoEntrada1();
        }

        if ($this->getMarcacaoEntrada2() !== null) {
            $aMarcacoes[] = $this->getMarcacaoEntrada2();
        }

        return $aMarcacoes;
    }

    public function getMarcacoesSaida()
    {

        $aMarcacoes   = array();

        if ($this->getMarcacaoSaida1() !== null) {
            $aMarcacoes[] = $this->getMarcacaoSaida1();
        }

        if ($this->getMarcacaoSaida2() !== null) {
            $aMarcacoes[] = $this->getMarcacaoSaida2();
        }

        return $aMarcacoes;
    }

  /**
   * @return MarcacaoPonto[]
   */
    public function getMarcacoes()
    {
        return $this->aMarcacoes;
    }

  /**
   * @return \ECidade\RecursosHumanos\RH\PontoEletronico\Marcacao\MarcacaoPonto
   */
    public function getUltimaMarcacao()
    {
        return $this->aMarcacoes[count($this->aMarcacoes)];
    }

  /**
   * @return \ECidade\RecursosHumanos\RH\PontoEletronico\Marcacao\MarcacaoPonto|null
   */
    public function getUltimaMarcacaoComRegistro()
    {

        $oUltimaMarcacao = null;

        foreach ($this->aMarcacoes as $oMarcacao) {
            if ($oMarcacao->getMarcacao() != null) {
                $oUltimaMarcacao = $oMarcacao;
            }
        }

        return $oUltimaMarcacao;
    }

  /**
   * Verifica se a coleção está vazia
   */
    public function isEmpty()
    {

        if (empty($this->aMarcacoes)) {
            return true;
        }

        foreach ($this->aMarcacoes as $oMarcacao) {
            if ($oMarcacao->getMarcacao() != null) {
                return false;
            }
        }

        return true;
    }

  /**
   * @return bool
   */
    public function temTodasMarcacoes()
    {

        if (count($this->aMarcacoes) < 6) {
            return false;
        }

        return true;
    }

    public function atualizaMarcacao(MarcacaoPonto $oMarcacao)
    {
        $this->aMarcacoes[$oMarcacao->getTipo()]->setMarcacao($oMarcacao->getMarcacao());
    }

  /**
   * Retorna quantidade de marcações existentes.
   *
   * @return integer
   */
    public function getQuantidadeMarcacoes()
    {

        $iContador = 0;

        if (empty($this->aMarcacoes)) {
            return $iContador;
        }

        foreach ($this->aMarcacoes as $oMarcacao) {
            if ($oMarcacao->getMarcacao() != null) {
                $iContador++;
            }
        }

        return $iContador;
    }

    /**
     * @return array
     */
    public function getMarcacoesFaltantes()
    {
        return $this->aMarcacoesFaltantes;
    }

    /**
     * @param array $aMarcacoesFaltantes
     *
     * @return self
     */
    public function setMarcacoesFaltantes(array $aMarcacoesFaltantes)
    {
        $this->aMarcacoesFaltantes = $aMarcacoesFaltantes;
        return $this;
    }

    /**
     * @param array $aMarcacoesFaltantes
     *
     * @return self
     */
    public function addMarcacoesFaltantes(MarcacaoPonto $marcacaoFaltante)
    {
        $this->aMarcacoesFaltantes[$marcacaoFaltante->getTipo()] = $marcacaoFaltante;
        return $this;
    }

    /**
     * @param int $tipoMarcacao
     * @param bool $forcar
     *
     * @throws \BusinessException
     */
    public function moverMarcacaoParaAnterior($tipoMarcacao, $forcar = true)
    {
        if (!$forcar) {
            $atual    = MarcacaoPonto::getDescricaoTipoMarcacao($tipoMarcacao);
            $anterior = MarcacaoPonto::getDescricaoTipoMarcacao($tipoMarcacao - 1);

            if (!empty($this->aMarcacoes[$tipoMarcacao - 1])) {
                throw new \BusinessException(
                    "Não foi possível mover a marcação ({$atual}) para anterior ({$anterior}) pois já está ocupada"
                );
            }
        }

        $this->aMarcacoes[$tipoMarcacao - 1] = $this->aMarcacoes[$tipoMarcacao];
        $this->aMarcacoes[$tipoMarcacao - 1]->setTipo($tipoMarcacao - 1);
        unset($this->aMarcacoes[$tipoMarcacao]);
    }

    public function toArray()
    {
        $marcacoes = array();

        foreach ($this->aMarcacoes as $marcacao) {
            $m = null;

            if ($marcacao->getMarcacao() instanceof \DateTime) {
                $m = $marcacao->getMarcacao()->format('H:i');

                if ($marcacao->isManual()) {
                    $m .= '-M';
                }
            }

            $marcacoes[MarcacaoPonto::getDescricaoTipoMarcacao($marcacao->getTipo())] = $m;
        }

        return $marcacoes;
    }

    /**
     * Retorna a primeira marcação do ponto
     * @return MarcacaoPonto|null
     */
    public function getPrimeiraMarcacaoComRegistro()
    {

        $oUltimaMarcacao = null;

        foreach ($this->aMarcacoes as $oMarcacao) {
            if ($oMarcacao->getMarcacao() != null) {
                return $oMarcacao;
            }
        }

        return $oUltimaMarcacao;
    }

    /**
     * Verifica se existe o primeiro intervalo nas marcações
     *
     * @return bool
     */
    public function temIntervalo1()
    {
        return count($this->aMarcacoes) > 2 ? true : false;
    }

    /**
     * Verifica se existe o segundo intervalo nas marcações
     *
     * @return bool
     */
    public function temIntervalo2()
    {
        return count($this->aMarcacoes) > 4 ? true : false;
    }

    /**
     * Verifica se há intervalo nas marcações
     *
     * @return bool
     */
    public function temIntervalo()
    {
        if ($this->temIntervalo1() || $this->temIntervalo2()) {
            return true;
        }

        return false;
    }

    /**
     * Verifica um horário informado se está em algum intervalo de marcações
     *
     * @return bool
     */
    public function estaNoIntervalo(\DateTime $horaVerificar)
    {
        if (!$this->temIntervalo()) {
            return false;
        }

        $horaInicio = $this->aMarcacoes[MarcacaoPonto::SAIDA_1]->getMarcacao();
        $horaFim    = $this->aMarcacoes[MarcacaoPonto::ENTRADA_2]->getMarcacao();

        if (empty($horaInicio) || empty($horaFim)) {
            return false;
        }

        if (BaseHora::verificaHoraEstaNoIntervalo($horaVerificar, $horaInicio, $horaFim)) {
            return true;
        } else {
            if (!$this->temIntervalo2()) {
                $horaInicio = $this->aMarcacoes[MarcacaoPonto::SAIDA_2]->getMarcacao();
                $horaFim    = $this->aMarcacoes[MarcacaoPonto::ENTRADA_3]->getMarcacao();

                if (empty($horaInicio) || empty($horaFim)) {
                    return false;
                }

                return BaseHora::verificaHoraEstaNoIntervalo($horaVerificar, $horaInicio, $horaFim);
            }
        }

        return false;
    }

    public function __clone()
    {
        foreach ($this->aMarcacoes as $marcacao) {
            if ($marcacao !== null) {
                $this->aMarcacoes[$marcacao->getTipo()] = clone $marcacao;
            }
        }
    }
}
