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

use DateTime;
use DBDate;
use ECidade\RecursosHumanos\RH\Efetividade\Model\Jornada;
use ECidade\RecursosHumanos\RH\PontoEletronico\Calculo\Horas\BaseHora;
use ECidade\RecursosHumanos\RH\PontoEletronico\Calculo\Model\DiaTrabalho;
use ECidade\RecursosHumanos\RH\PontoEletronico\Configuracao\Model\Justificativa as JustificativaModel;

/**
 * Classe que representa uma marcação de horário ponto
 * Class MarcacaoPonto
 * @package ECidade\RecursosHumanos\RH\PontoEletronico\Calculo\Model
 * @author Renan Silva <renan.silva@dbseller.com.br>
 */
class MarcacaoPonto
{
    const ENTRADA_1 = 1;
    const SAIDA_1 = 2;
    const ENTRADA_2 = 3;
    const SAIDA_2 = 4;
    const ENTRADA_3 = 5;
    const SAIDA_3 = 6;

    const MARCACAO_GERADA = 'G';
    const MARCACAO_RELOGIO = 'R';

    /**
     * @var array $tiposMarcacao
     */
    private static $tiposMarcacao = array(
        MarcacaoPonto::ENTRADA_1 => 'ENTRADA 1',
        MarcacaoPonto::SAIDA_1 => 'SAIDA 1',
        MarcacaoPonto::ENTRADA_2 => 'ENTRADA 2',
        MarcacaoPonto::SAIDA_2 => 'SAIDA 2',
        MarcacaoPonto::ENTRADA_3 => 'ENTRADA 3',
        MarcacaoPonto::SAIDA_3 => 'SAIDA 3'
    );
    /**
     * @var DateTime $oMarcacao
     */
    protected $oMarcacao;
    /**
     * @var Jornada $jornada
     */
    private $jornada = null;
    /**
     * @var integer $iTipo
     */
    private $iTipoMarcacao;
    /**
     * @var int
     */
    private $iCodigo;
    /**
     * #@var boolean
     */
    private $lManual = false;
    /**
     * @var DBDate
     */
    private $oData;
    /**
     * @var JustificativaModel
     */
    private $oJustificativa;
    /**
     * @var bool
     */
    private $lTemMarcacaoLancada = true;

    /**
     * @var string
     */
    private $sOrigemMarcacao = 'R';

    /**
     * Construtor da classe
     *
     * @param DateTime $oMarcacao
     * @param Integer $iTipoMarcacao
     */
    public function __construct($oMarcacao = null, $iTipoMarcacao = null)
    {
        if (!empty($oMarcacao)) {
            $this->oMarcacao = $oMarcacao;
            $this->lTemMarcacaoLancada = false;
        }

        if (!empty($iTipoMarcacao)) {
            $this->iTipoMarcacao = $iTipoMarcacao;
        }
    }

    public function __clone()
    {
        $this->oMarcacao = $this->oMarcacao ? clone $this->oMarcacao : null;
        $this->jornada = $this->jornada ? clone $this->jornada : null;
        $this->oJustificativa = $this->oJustificativa ? clone $this->oJustificativa : null;
    }

    /**
     * @param $tipo
     * @return string
     */
    public static function getDescricaoTipoMarcacao($tipo)
    {
        return isset(self::$tiposMarcacao[$tipo]) ? self::$tiposMarcacao[$tipo] : '';
    }

    /**
     * Define a hora da marcação
     *
     * @param DateTime $oMarcacao
     */
    public function setMarcacao(DateTime $oMarcacao)
    {
        $this->oMarcacao = $oMarcacao;
    }

    /**
     * Retorna a hora da marcação
     *
     * @return DateTime $oMarcacao
     */
    public function getMarcacao()
    {
        return $this->oMarcacao;
    }

    /**
     * Define o tipo de marcação
     *
     * @param Integer
     */
    public function setTipo($iTipoMarcacao)
    {
        $this->iTipoMarcacao = $iTipoMarcacao;
    }

    /**
     * Retorna o tipo de marcação
     *
     * @return Integer
     */
    public function getTipo()
    {
        return $this->iTipoMarcacao;
    }

    /**
     * @param int $iCodigo
     */
    public function setCodigo($iCodigo)
    {
        $this->iCodigo = $iCodigo;
    }

    /**
     * @return int
     */
    public function getCodigo()
    {
        return $this->iCodigo;
    }

    /**
     * Define se a marcação é ou não manual
     * @param boolean $lManual
     */
    public function setManual($lManual)
    {
        $this->lManual = $lManual;
    }

    /**
     * Retorna se é ou não manual a marcação
     * @return boolean
     */
    public function isManual()
    {
        return $this->lManual;
    }

    /**
     * @return DBDate
     */
    public function getData()
    {
        return $this->oData;
    }

    /**
     * @param DBDate $oData
     */
    public function setData(DBDate $oData)
    {
        $this->oData = $oData;
    }

    /**
     * @return bool
     */
    public function hasJustificativa()
    {
        return empty($this->oJustificativa) ? false : true;
    }

    /**
     * @return JustificativaModel
     */
    public function getJustificativa()
    {
        return $this->oJustificativa;
    }

    /**
     * @param JustificativaModel $oJustificativa
     */
    public function setJustificativa(JustificativaModel $oJustificativa)
    {
        $this->oJustificativa = $oJustificativa;
    }

    /**
     * @return mixed
     */
    public function getJornada()
    {
        return $this->jornada;
    }

    /**
     * @param mixed $jornada
     *
     * @return self
     */
    public function setJornada($jornada)
    {
        $this->jornada = $jornada;

        return $this;
    }

    /**
     * @param string $sOrigemMarcacao
     *
     * @return self
     */
    public function setOrigemMarcacao($sOrigemMarcacao)
    {
        $this->sOrigemMarcacao = $sOrigemMarcacao;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrigemMarcacao()
    {
        return $this->sOrigemMarcacao;
    }

    /**
     * @return bool
     */
    public function hasMarcacaoLancada()
    {
        return $this->lTemMarcacaoLancada;
    }

    /**
     * Limpa a hora da marcação
     */
    public function limparHoraMarcacao()
    {
        $this->oMarcacao = null;
    }

    /**
     * @return bool
     */
    public function isMarcacaoSaida()
    {
        return false;
    }

    /**
     * @return int|null|string
     */
    public function verificarProximidadeMarcacao()
    {
        return $this->iTipoMarcacao = $this->getTipoMarcacaoPorProximidade();
    }

    /**
     * Verifica se a marcação esta dentro da tolerancia informada
     *
     * @param DiaTrabalho $diaTrabalho
     * @param int|null $tipoMarcacaoComparar
     * @return bool
     */
    public function estaNaTolerancia(DiaTrabalho $diaTrabalho, $tipoMarcacaoComparar = null)
    {
        $tipoMarcacaoComparar = $tipoMarcacaoComparar !== null ? $tipoMarcacaoComparar : $this->getTipo();
        $horasDaJornada = $diaTrabalho->getJornada()->getHoras();
        $horaJornada = !empty($horasDaJornada[$tipoMarcacaoComparar - 1])
            ? $horasDaJornada[$tipoMarcacaoComparar - 1]
            : null;

        if (empty($horaJornada)) {
            return false;
        }

        if ($this->getMarcacao() == null) {
            return false;
        }

        $tolerancia = $diaTrabalho->getConfiguracoesLotacao()->getTolerancia();

        $horaJornadaComToleranciaParaMais = clone $horaJornada->oHora;
        $horaJornadaComToleranciaParaMais->modify("+ {$tolerancia} minutes");

        $horaJornadaComToleranciaParaMenos = clone $horaJornada->oHora;
        $horaJornadaComToleranciaParaMenos->modify(" - {$tolerancia} minutes");
        $estaNaTolerancia = BaseHora::verificaHoraEstaNoIntervalo(
            $this->getMarcacao(),
            $horaJornadaComToleranciaParaMenos,
            $horaJornadaComToleranciaParaMais
        );

        return $estaNaTolerancia;
    }

    /**
     * @return int|string|null
     */
    public function getTipoMarcacaoPorProximidade()
    {
        $horasJornada = $this->jornada->getHoras();
        $minutosDiferencaEntreMarcacaoJornadaMenor = null;
        $tipoMarcacao = null;

        foreach ($horasJornada as $tipoHoraJornada => $horaJornada) {
            if ($this->oMarcacao === null) {
                continue;
            }

            $diferencaEntreMarcacaoJornada = $horaJornada->oHora->diff($this->oMarcacao);
            $diferencaEntreMarcacaoJornada->invert = 0;
            $minutosDiferencaEntreMarcacaoJornada = BaseHora::converterIntervaloEmMinutos(
                $diferencaEntreMarcacaoJornada
            );

            if ($minutosDiferencaEntreMarcacaoJornadaMenor === null) {
                $tipoMarcacao = $tipoHoraJornada + 1;
                $minutosDiferencaEntreMarcacaoJornadaMenor = $minutosDiferencaEntreMarcacaoJornada;
            } elseif ((int)$minutosDiferencaEntreMarcacaoJornada < (int)$minutosDiferencaEntreMarcacaoJornadaMenor) {
                $tipoMarcacao = $tipoHoraJornada + 1;
                $minutosDiferencaEntreMarcacaoJornadaMenor = $minutosDiferencaEntreMarcacaoJornada;
            }
        }

        return $tipoMarcacao;
    }
}
