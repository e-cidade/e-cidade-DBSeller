<?php

namespace ECidade\RecursosHumanos\ESocial\Agendamento\Processamento;

/**
 * Class ProcessamentoAbstract
 * @package ECidade\RecursosHumanos\ESocial\Agendamento\Processamento
 */
abstract class ProcessamentoAbstract
{
    /**
     * @var Servidor[]
     */
    protected $servidores = array();

    /**
     * @var int
     */
    private $indicativoPeriodoApuracao;

    /**
     * @var
     * Variavel que permite o Formatter fazer envio
     * de matriculas que estejam rescindidas.
     */
    private $ignoraValidacao = false;

    /**
     * @var
     *  Variavel que valida se estamos forcando o reenvio de informacoes sem alteracoes
     */
    protected $envioForcado = false;

    /**
     * @var array
     * Variavel de armazenamento de inconsistencias durante os processamentos dos arquivos
     */
    protected $inconsistencias = [];

    protected $numeroRecibo = "";

    /**
     * @var int
     *  Variavel que identifica o codigo da selecao
     */
    protected $selecao;

    /**
     * @var int
     *  Variavel que identifica o ano da competencia
     */
    protected $anoCompetencia;

    /**
     * @var int
     *  Variavel que identifica o mes da competencia
     */
    protected $mesCompetencia;

    /**
     * @var int
     *  Variavel que identifica o ano do pagamento de caixa para eventos da folha
     */
    protected $anoCaixa;

    /**
     * @var int
     *  Variavel que identifica o mes de pagamento de caixa para eventos da folha
     */
    protected $mesCaixa;

    /**
     * @var string
     *  Variavel que identifica o tipo de data de pagamento
     *      "ultimoDia" -> Ultimo dia da competência
     *      "periodoAtual" -> Dia do pagamento preenchida no fechamento da folha
     */
    protected $tipoDataPagamento = '';

    /**
     * @var bool
     * Variavel responsavel por processamento de exatamente a matricula informada,
     * nos casos de varios vinculo de 1 cgm
     */
    protected $forcarMatricula = false;

    /**
     * @var Date
     */
    protected $dataInicial;

     /**
     * @var array
     *  Lista de rubricas
     */
    protected $listaRubricas = [];

    /**
     * @var DBCompetencia
     */
    protected $competenciaAnterior;

    /**
     * @return Date
     */
    public function getDataInicial()
    {
        return $this->dataInicial;
    }

    /**
     * @param Date $dataInicial
     */
    public function setDataInicial($dataInicial)
    {
        $this->dataInicial = $dataInicial;
    }

    /**
     * @return Date
     */
    public function getDataFinal()
    {
        return $this->dataFinal;
    }

    /**
     * @param Date $dataFinal
     */
    public function setDataFinal($dataFinal)
    {
        $this->dataFinal = $dataFinal;
    }

    /**
     * @var Date
     */
    protected $dataFinal;

    /**
     * @var Date
     */
    protected $dataPreenchidaInicio;

    /**
     * @return Date
     */
    public function getDataPreenchidaInicial()
    {
        return $this->dataPreenchidaInicio;
    }

    /**
     * @param Date $dataPreenchidaInicio
     */
    public function setDataPreenchidaInicial($dataPreenchidaInicio)
    {
        $this->dataPreenchidaInicio = $dataPreenchidaInicio;
    }

    /**
     * @return Date
     */
    public function getDataPreenchidaFinal()
    {
        return $this->dataPreenchidaFim;
    }

    /**
     * @param Date $dataPreenchidaFim
     */
    public function setDataPreenchidaFinal($dataPreenchidaFim)
    {
        $this->dataPreenchidaFim = $dataPreenchidaFim;
    }

     /**
     * @var Date
     */
    protected $dataPreenchidaFim;

    /**
     * @param Servidor[] $servidores
     */
    final public function setServidores(array $servidores)
    {
        $this->servidores = $servidores;
    }

    /**
     * @return bool
     */
    final public function filtraServidores()
    {
        return count($this->servidores) > 0;
    }

    /**
     * @return int
     */
    public function getIndicativoPeriodoApuracao()
    {
        return $this->indicativoPeriodoApuracao;
    }

    /**
     * @param $indicativoPeriodoApuracao
     */
    public function setIndicativoPeriodoApuracao($indicativoPeriodoApuracao)
    {
        $this->indicativoPeriodoApuracao = $indicativoPeriodoApuracao;
    }

    /**
     * @param boolean $envioForcado
     */
    public function setEnvioForcado($envioForcado)
    {
        $this->envioForcado = $envioForcado;
    }

    /**
     * @param array $inconsistencias
     */
    public function setInconsitencias(array $inconsistencias)
    {
        $this->inconsistencias = $inconsistencias;
    }

    /**
     * @return arrays
     */
    public function getInconsistencia()
    {
        return $this->inconsistencias;
    }

    /**
     * @return bool
     */
    public function possuiInconsistencia()
    {
        if (sizeof($this->inconsistencias) > 0) {
            return true;
        }
        return false;
    }

    public function setNumeroRecibo($recibo)
    {
        $this->numeroRecibo = $recibo;
    }


    /**
     * @return bool
     */
    public function isForcarMatricula()
    {
        return $this->forcarMatricula;
    }

    /**
     * @param bool $forcarMatricula
     */
    public function setForcarMatricula($forcarMatricula)
    {
        $this->forcarMatricula = $forcarMatricula;
    }

    /**
     * @return int
     */
    public function getSelecao()
    {
        return $this->selecao;
    }

    /**
     * @param int $selecao
     */
    public function setSelecao($selecao)
    {
        $this->selecao = $selecao;
    }

    /**
     * @return int
     */
    public function getAnoCompetencia()
    {
        return $this->anoCompetencia;
    }

    /**
     * @param int $anoCompetencia
     */
    public function setAnoCompetencia($anoCompetencia)
    {
        $this->anoCompetencia = $anoCompetencia;
    }

    /**
     * @return int
     */
    public function getMesCompetencia()
    {
        return $this->mesCompetencia;
    }

    /**
     * @param int $mesCompetencia
     */
    public function setMesCompetencia($mesCompetencia)
    {
        $this->mesCompetencia = $mesCompetencia;
    }

    /**
     * @return int
     */
    public function getAnoCaixa()
    {
        return $this->anoCaixa;
    }

    /**
     * @param int $anoCaixa
     */
    public function setAnoCaixa($anoCaixa)
    {
        $this->anoCaixa = $anoCaixa;
    }

    /**
     * @return int
     */
    public function getMesCaixa()
    {
        return $this->mesCaixa;
    }

    /**
     * @param int $mesCaixa
     */
    public function setMesCaixa($mesCaixa)
    {
        $this->mesCaixa = $mesCaixa;
    }
    /**
     * @return string
     */
    public function getTipoDataPagamento()
    {
        return $this->tipoDataPagamento;
    }

    /**
     * @param string $tipoDataPagamento
     */
    public function setTipoDataPagamento($tipoDataPagamento)
    {
        $this->tipoDataPagamento = $tipoDataPagamento;
    }

    /**
     * Retorna o valor da variavel ignoraValidacao
     */
    public function getIgnoraValidacao()
    {
        return $this->ignoraValidacao;
    }

    /**
     * Seta o valor da variavel ignoraValidacao
     *
     * @return  self
     */
    public function setIgnoraValidacao($ignoraValidacao)
    {
        $this->ignoraValidacao = $ignoraValidacao;

        return $this;
    }


    /**
     * Get lista de rubricas
     *
     * @return  array
     */
    public function getListaRubricas()
    {
        return $this->listaRubricas;
    }

    /**
     * Set lista de rubricas
     *
     * @param  array  $listaRubricas  Lista de rubricas
     *
     * @return  self
     */
    public function setListaRubricas(array $listaRubricas)
    {
        $this->listaRubricas = $listaRubricas;
    }
}
