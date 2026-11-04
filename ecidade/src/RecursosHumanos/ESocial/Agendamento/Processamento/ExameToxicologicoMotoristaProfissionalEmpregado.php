<?php

namespace ECidade\RecursosHumanos\ESocial\Agendamento\Processamento;

use App\Domain\RecursosHumanos\ESocial\Models\ExameToxicologico;
use CgmRepository;
use ECidade\RecursosHumanos\ESocial\Agendamento\Evento;
use ECidade\RecursosHumanos\ESocial\Integracao\FormatterFactory;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;

class ExameToxicologicoMotoristaProfissionalEmpregado extends ProcessamentoAbstract implements ProcessamentoInterface
{

    private $instituicao;

    private $cgm;


    public function processar()
    {
        if (empty($this->dataInicial) && empty($this->dataFinal)) {
            $msg = "Não foi informado o período dos Acidentes de Trabalho. Por favor preencha as datas.";
            throw new \BusinessException($msg);
        }
        if (empty($this->dataInicial)) {
            $msg = "Não foi informada a data inicial dos Acidentes de Trabalho. Por favor preencha a data.";
            throw new \BusinessException($msg);
        }
        if (empty($this->dataFinal)) {
            $msg = "Não foi informada a data final dos Acidentes de Trabalho. Por favor preencha a data.";
            throw new \BusinessException($msg);
        }

        $dados = $this->buscarDados();
        $alteracao = false;
        if ($dados) {
            $formatter = FormatterFactory::get(Tipo::S2221);
            $formatter->setEmpregador(CgmRepository::getByCodigo($this->cgm));

            $dadosFormatados = $formatter->formatar($dados);

            $validaMd5 = true;
            if ($this->envioForcado) {
                $validaMd5 = false;
            }

            foreach ($dadosFormatados as $dadoFormatado) {
                $evento = new Evento(Tipo::S2221, $this->cgm, $dadoFormatado->referencia, $dadoFormatado);

                if ($evento->adicionarFila(false, $validaMd5)) {
                    $alteracao = true;
                }
            }
        }

        return $alteracao;
    }

    public function setInstituicao($instituicao)
    {
        $this->instituicao = $instituicao;
    }

    public function __construct($cgm)
    {
        $this->cgm = $cgm;
    }

    public function buscarDados()
    {
        $dados = ExameToxicologico::query()
            ->where('eso41_instit', '=', $this->instituicao)
            ->whereDate('eso41_dtexame', '>=', $this->dataInicial)
            ->whereDate('eso41_dtexame', '<=', $this->dataFinal)
            ->get();

        return $dados;
    }
}
