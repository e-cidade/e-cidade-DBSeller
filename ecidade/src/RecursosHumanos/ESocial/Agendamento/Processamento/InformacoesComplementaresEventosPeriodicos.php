<?php

namespace ECidade\RecursosHumanos\ESocial\Agendamento\Processamento;

use App\Domain\RecursosHumanos\ESocial\Models\InformacaoComplementar;
use ECidade\RecursosHumanos\ESocial\Agendamento\Evento;
use ECidade\RecursosHumanos\ESocial\Integracao\FormatterFactory;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use ParameterException;

class InformacoesComplementaresEventosPeriodicos extends ProcessamentoAbstract implements ProcessamentoInterface
{
    private $cgm;


    public function __construct($cgm)
    {
        $this->cgm = $cgm;
    }

    public function processar()
    {
        $alteracao = false;
        $periodo = $this->getPeriodo();
        $dados = $this->buscarDadosPeriodo($periodo);

        if ($dados) {
            $formatter = FormatterFactory::get(Tipo::S1280);

            if ($this->getIndicativoPeriodoApuracao() === '2') {
                $formatter->setDecimoTerceiro();
            }
            $dados->inscricao_empregador = \CgmRepository::buscarCNPJEmpregador($dados->eso40_nr_insc);

            $dadosFormatados = $formatter->formatar($dados);
            $validaMd5 = true;
            if ($this->envioForcado) {
                $validaMd5 = false;
            }
            $evento = new Evento(TIPO::S1280, $this->cgm, $dadosFormatados->referencia, $dadosFormatados);

            if ($evento->adicionarFila(false, $validaMd5)) {
                $alteracao = true;
            }
        }
        return $alteracao;
    }

    private function getPeriodo()
    {
        $periodo = "";
        if ($this->getIndicativoPeriodoApuracao() === null) {
            throw new ParameterException("Indicativo de Período de Apuração não informado.");
        }

        if ($this->getIndicativoPeriodoApuracao() === '1') {
            if (empty($this->getAnoCompetencia())) {
                throw new ParameterException("Ano do período de apuração não informado.");
            }
            if (empty($this->getMesCompetencia())) {
                throw new ParameterException("Mês do período de apuração não informado.");
            }
            $periodo = $this->getAnoCompetencia()
                . "-"
                . str_pad($this->getMesCompetencia(), 2, '0', STR_PAD_LEFT);
        }


        if ($this->getIndicativoPeriodoApuracao() === '2') {
            if (empty($this->getAnoCompetencia())) {
                throw new ParameterException("Ano do período de apuração não informados.");
            }
            $periodo = $this->getAnoCompetencia();
        }
        return $periodo;
    }

    private function buscarDadosPeriodo($periodo)
    {
        $dados = InformacaoComplementar::query()
            ->where('eso40_periodo', $periodo)
            ->where('eso40_nr_insc', $this->cgm)
            ->first();
        return $dados;
    }
}
