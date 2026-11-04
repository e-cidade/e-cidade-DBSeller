<?php

namespace App\Domain\Financeiro\Orcamento\Services\Relatorios;

use App\Domain\Financeiro\Orcamento\Relatorios\AcompanhamentoMetasArrecadacaoPdf;
use App\Domain\Financeiro\Planejamento\Relatorios\MetasArrecadacaoCSV;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceita;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceitaFormatter;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceitaPadrao;
use ECidade\Financeiro\Orcamento\Repository\AcompanhamentoCronogramaReceitaRepository;
use Illuminate\Support\Collection;

class MetasArrecadacaoService extends BaseCronograma
{
    /**
     * @var mixed
     */
    private $recursos = [];

    protected $ementario = 'ecidade';

    /**
     * @param string $natureza
     * @return EstruturalReceitaFormatter
     */
    protected function getFormatterReceita($natureza)
    {
        if ($this->ementario === 'ecidade') {
            return new EstruturalReceita($natureza);
        }

        return new EstruturalReceitaPadrao($natureza);
    }

    protected function processaFiltros(array $filtros)
    {
        $this->exercicio = (int)$filtros['DB_anousu'];
        $this->agruparPor = $filtros['agruparPor'];
        $this->periodicidade = $filtros['periodicidade'];
        $this->instituicoes = $filtros['instituicoes'];
        if (!empty($filtros['recursos'])) {
            $this->recursos = $filtros['recursos'];
        }

        if (!empty($filtros['ementario'])) {
            $this->ementario = $filtros['ementario'];
        }

        $this->organizaFiltrosEmissao();
        $this->inicializaTotalizadores();
    }

    /**
     * Organiza os filtros de emissão
     */
    protected function organizaFiltrosEmissao()
    {
        $this->dados['filtros']['exercicio'] = $this->exercicio;
        $this->dados['filtros']['agruparPor'] = $this->agruparPor;
        $this->dados['filtros']['periodicidade'] = $this->periodicidade;
        $this->dados['filtros']['filtrouRecurso'] = !empty($this->recursos);
        $this->dados['ementario'] = $this->ementario;
    }

    /**
     * @return array
     */
    public function processar()
    {
        $estimativas = $this->getEstimativas();

        $estimativas = collect($estimativas)->sortBy('natureza');
        if ($this->agruparPor === 'receita') {
            $this->agruparPorReceita($estimativas);
        } else {
            $this->agruparPorRecurso($estimativas);
        }

        $this->totalizar();

        return $this->dados;
    }

    /**
     * @return array
     */
    public function emitirPdf()
    {
        if (empty($this->dados['dados'])) {
            $this->processar();
        }
        $relatorio = new AcompanhamentoMetasArrecadacaoPdf();
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

    public function getEstimativas()
    {
        $repository = new AcompanhamentoCronogramaReceitaRepository();

        if (!empty($this->recursos)) {
            $repository->scopeRecursos($this->recursos);
        }
        $repository->scopeInstituicoes($this->instituicoes)
            ->scopeExercicio($this->exercicio);
        if ($this->ementario === 'ecidade') {
            return $repository->getDadosRelatorio();
        }

        return $repository->getDadosRelatorioPlanoPadrao($this->ementario);
    }

    private function agruparPorReceita(Collection $estimativas)
    {
        $this->dados['dados'] = $this->processaEstimativas($estimativas);
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

    private function processaEstimativas(Collection $estimativas)
    {
        return $estimativas->map(function ($estimativa) {
            $estrutural = $this->getFormatterReceita($estimativa->natureza);
            $hash = "{$estimativa->fonte_recurso}#{$estimativa->complemento}";
            $dado = [
                'estrutural' => $estrutural->getEstrutural(),
                'estrutural_mascara' => $estrutural->getEstruturalComMascara(),
                'natureza' => $estimativa->descricao,
                'codigo_recurso' => $hash,
                'fonte_recurso' => $estimativa->fonte_recurso,
                'descricao_recurso' => $estimativa->descricao_recurso,
                'complemento' => $estimativa->complemento,
                'descricao_complemento' => $estimativa->descricao_complemento,
            ];
            $dado = array_merge($dado, $this->processaValores($estimativa));
            return (object)$dado;
        });
    }

    protected function processaValores($cronograma)
    {
        if ($this->periodicidade === 'mensal') {
            return [
                "valor" => $this->totalizaLinha($cronograma),
                "janeiro" => $cronograma->janeiro,
                "fevereiro" => $cronograma->fevereiro,
                "marco" => $cronograma->marco,
                "abril" => $cronograma->abril,
                "maio" => $cronograma->maio,
                "junho" => $cronograma->junho,
                "julho" => $cronograma->julho,
                "agosto" => $cronograma->agosto,
                "setembro" => $cronograma->setembro,
                "outubro" => $cronograma->outubro,
                "novembro" => $cronograma->novembro,
                "dezembro" => $cronograma->dezembro,
            ];
        }

        return [
            "valor" => $this->totalizaLinha($cronograma),
            'bimestre_1' => $cronograma->janeiro + $cronograma->fevereiro,
            'bimestre_2' => $cronograma->marco + $cronograma->abril,
            'bimestre_3' => $cronograma->maio + $cronograma->junho,
            'bimestre_4' => $cronograma->julho + $cronograma->agosto,
            'bimestre_5' => $cronograma->setembro + $cronograma->outubro,
            'bimestre_6' => $cronograma->novembro + $cronograma->dezembro,
        ];
    }

    private function totalizaLinha($cronograma)
    {
        return $cronograma->janeiro + $cronograma->fevereiro + $cronograma->marco + $cronograma->abril +
            $cronograma->maio + $cronograma->junho + $cronograma->julho + $cronograma->agosto +
            $cronograma->setembro + $cronograma->outubro + $cronograma->novembro + $cronograma->dezembro;
    }
}
