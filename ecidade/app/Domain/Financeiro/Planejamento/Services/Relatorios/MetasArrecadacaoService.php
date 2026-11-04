<?php

namespace App\Domain\Financeiro\Planejamento\Services\Relatorios;

use App\Domain\Financeiro\Planejamento\Models\CronogramaDesembolsoReceita;
use App\Domain\Financeiro\Planejamento\Models\EstimativaReceita;
use App\Domain\Financeiro\Planejamento\Models\Valor;
use App\Domain\Financeiro\Planejamento\Relatorios\MetasArrecadacaoCSV;
use App\Domain\Financeiro\Planejamento\Relatorios\MetasArrecadacaoPdf;
use Illuminate\Database\Eloquent\Collection;

class MetasArrecadacaoService extends BaseRelatoriosCronograma
{
    /**
     * @var array
     */
    protected $recursos = [];

    /**
     * @return array
     */
    public function emitirPdf()
    {
        if (empty($this->dados['dados'])) {
            $this->processar();
        }
        $relatorio = new MetasArrecadacaoPdf();
        $relatorio->setDados($this->dados);
        return $relatorio->emitir();
    }

    public function emitirCSV()
    {
        if (empty($this->dados['dados'])) {
            $this->processar();
        }
        $relatorio = new MetasArrecadacaoCSV();
        $relatorio->setDados($this->dados);
        return $relatorio->emitir();
    }

    public function emitir()
    {
        $this->processar();
        return array_merge($this->emitirPdf(), $this->emitirCSV());
    }

    /**
     * @return array
     */
    public function processar()
    {
        $estimativas = $this->getEstimativas();

        if ($this->agruparPor === 'receita') {
            $this->agruparPorReceita($estimativas);
        } else {
            $this->agruparPorRecurso($estimativas);
        }

        $this->totalizar();

        return $this->dados;
    }

    protected function processaFiltros(array $filtros)
    {
        parent::processaFiltros($filtros);

        if (!empty($filtros['recursos'])) {
            $this->recursos = $filtros['recursos'];
        }

        $this->buscarNotasExplicativas($filtros['DB_anousu'], $filtros['DB_instit'], 76);
    }

    /**
     * @return Collection[]
     */
    public function getEstimativas()
    {
        return EstimativaReceita::query()
            ->where('planejamento_id', '=', $this->planejamento->pl2_codigo)
            ->whereIn('instituicao_id', $this->instituicoes)
            ->when(!empty($this->recursos), function ($query) {
                $query->whereIn('recurso_id', $this->recursos);
            })
            ->get();
    }

    protected function agruparPorReceita(Collection $estimativas)
    {
        $this->dados['dados'] = $this->processaEstimativas($estimativas)->toArray();
        return $this->dados['dados'];
    }

    public function agruparPorRecurso(Collection $estimativas)
    {
        $agrupar = $this->agruparPor === 'recurso' ? 'codigo_recurso' : 'fonte_recurso';

        $this->dados['dados'] = [];
        $this->processaEstimativas($estimativas)->each(function ($estimativa) use ($agrupar) {
            if (!array_key_exists($estimativa->{$agrupar}, $this->dados['dados'])) {
                unset($estimativa->estrutural);
                unset($estimativa->estrutural_mascara);
                unset($estimativa->natureza);
                $this->dados['dados'][$estimativa->{$agrupar}] = $estimativa;
            } else {
                $this->dados['dados'][$estimativa->{$agrupar}]->valor += $estimativa->valor;
                if ($this->periodicidade === 'mensal') {
                    $this->dados['dados'][$estimativa->{$agrupar}]->janeiro += $estimativa->janeiro;
                    $this->dados['dados'][$estimativa->{$agrupar}]->fevereiro += $estimativa->fevereiro;
                    $this->dados['dados'][$estimativa->{$agrupar}]->marco += $estimativa->marco;
                    $this->dados['dados'][$estimativa->{$agrupar}]->abril += $estimativa->abril;
                    $this->dados['dados'][$estimativa->{$agrupar}]->maio += $estimativa->maio;
                    $this->dados['dados'][$estimativa->{$agrupar}]->junho += $estimativa->junho;
                    $this->dados['dados'][$estimativa->{$agrupar}]->julho += $estimativa->julho;
                    $this->dados['dados'][$estimativa->{$agrupar}]->agosto += $estimativa->agosto;
                    $this->dados['dados'][$estimativa->{$agrupar}]->setembro += $estimativa->setembro;
                    $this->dados['dados'][$estimativa->{$agrupar}]->outubro += $estimativa->outubro;
                    $this->dados['dados'][$estimativa->{$agrupar}]->novembro += $estimativa->novembro;
                    $this->dados['dados'][$estimativa->{$agrupar}]->dezembro += $estimativa->dezembro;
                } else {
                    $this->dados['dados'][$estimativa->{$agrupar}]->bimestre_1 += $estimativa->bimestre_1;
                    $this->dados['dados'][$estimativa->{$agrupar}]->bimestre_2 += $estimativa->bimestre_2;
                    $this->dados['dados'][$estimativa->{$agrupar}]->bimestre_3 += $estimativa->bimestre_3;
                    $this->dados['dados'][$estimativa->{$agrupar}]->bimestre_4 += $estimativa->bimestre_4;
                    $this->dados['dados'][$estimativa->{$agrupar}]->bimestre_5 += $estimativa->bimestre_5;
                    $this->dados['dados'][$estimativa->{$agrupar}]->bimestre_6 += $estimativa->bimestre_6;
                }
            }
        });

        $data = collect($this->dados['dados'])->sortBy('fonte_recurso');
        $this->dados['dados'] = $data->toArray();
        return $this->dados['dados'];
    }

    /**
     * Organiza os filtros de emissão
     */
    protected function organizaFiltrosEmissao()
    {
        parent::organizaFiltrosEmissao();
        $this->dados['filtros']['filtrouRecurso'] = !empty($this->recursos);
    }

    /**
     * @param CronogramaDesembolsoReceita $cronograma
     * @return array
     */
    protected function processaValores(CronogramaDesembolsoReceita $cronograma)
    {
        if ($this->periodicidade === 'mensal') {
            return $cronograma->toArray();
        }

        return [
            'bimestre_1' => $cronograma->janeiro + $cronograma->fevereiro,
            'bimestre_2' => $cronograma->marco + $cronograma->abril,
            'bimestre_3' => $cronograma->maio + $cronograma->junho,
            'bimestre_4' => $cronograma->julho + $cronograma->agosto,
            'bimestre_5' => $cronograma->setembro + $cronograma->outubro,
            'bimestre_6' => $cronograma->novembro + $cronograma->dezembro,
        ];
    }

    /**
     * @param EstimativaReceita $estimativaReceita
     * @return CronogramaDesembolsoReceita
     */
    protected function getCronograma(EstimativaReceita $estimativaReceita)
    {
        return $estimativaReceita->cronogramaDesembolso->filter(
            function (CronogramaDesembolsoReceita $cronograma) {
                return $cronograma->exercicio === $this->exercicio;
            }
        )->shift();
    }

    /**
     * @param EstimativaReceita $estimativaReceita
     * @return Valor
     */
    protected function getValorBase(EstimativaReceita $estimativaReceita)
    {
        return $estimativaReceita->getValores()->filter(function (Valor $valor) {
            return $valor->pl10_ano === $this->exercicio;
        })->shift();
    }

    /**
     * @param Collection $estimativas
     * @return Collection|\Illuminate\Support\Collection
     */
    protected function processaEstimativas(Collection $estimativas)
    {
        return $estimativas->map(function (EstimativaReceita $estimativaReceita) {
            $cronograma = $this->getCronograma($estimativaReceita);
            $valor = $this->getValorBase($estimativaReceita);
            $recurso = $estimativaReceita->recurso;
            $naturezaReceita = $estimativaReceita->getNaturezaOrcamento();
            $fonte = $recurso->fonteRecurso($this->planejamento->pl2_ano_inicial);
            if (is_null($fonte)) {
                throw new \Exception(sprintf(
                    'Não foi encontrada a fonte de recurso para %s. Acesse: %s ',
                    $this->planejamento->pl2_ano_inicial,
                    'Contabilidade > Procedimentos > Exercício Contábil > Inclusão'
                ));
            }
            $hash = "{$fonte->codigo_siconfi}#{$recurso->complemento->o200_sequencial}";
            $dado = [
                'estrutural' => $naturezaReceita->o57_fonte,
                'estrutural_mascara' => $naturezaReceita->getEstrutural()->getEstruturalComMascara(),
                'natureza' => $naturezaReceita->o57_descr,
                'codigo_recurso' => $hash,
                'fonte_recurso' => $fonte->codigo_siconfi,
                'descricao_recurso' => $fonte->descricao,
                'complemento' => $recurso->complemento->o200_sequencial,
                'descricao_complemento' => $recurso->complemento->o200_descricao,
                'valor' => $valor->pl10_valor,
            ];

            $dado = array_merge($dado, $this->processaValores($cronograma));

            return (object)$dado;
        })->sortBy('estrutural');
    }
}
