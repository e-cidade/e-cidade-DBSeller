<?php

namespace App\Domain\Financeiro\Orcamento\Services\Relatorios;

use App\Domain\Financeiro\Orcamento\Relatorios\AcompanhamentoMetasVersusCotasPdf;
use App\Domain\Financeiro\Planejamento\Relatorios\MetasVersusCotasCsv;
use ECidade\Financeiro\Orcamento\Repository\AcompanhamentoCronogramaDespesaRepository;

class MetasVersusCotasService extends BaseCronograma
{
    /**
     * @var array
     */
    private $recursos = [];
    /**
     * @var array
     */
    private $cotasDespesa = [];
    /**
     * @var array
     */
    private $metasReceita = [];

    public function __construct(array $filtros)
    {
        parent::__construct($filtros);
        if ($filtros['agruparPor'] === 'geral') {
            $filtros['agruparPor'] = 'recurso';
        }
        $this->metasArrecadacaoService = new MetasArrecadacaoService($filtros);
    }

    public function emitir()
    {
        $this->processar();
        return array_merge($this->emitirPdf(), $this->emitirCSV());
    }

    private function emitirPdf()
    {
        $relatorio = new AcompanhamentoMetasVersusCotasPdf();
        $relatorio->setDados($this->dados);

        return $relatorio->emitir();
    }

    private function emitirCSV()
    {
        $relatorio = new MetasVersusCotasCsv();
        $relatorio->setDados($this->dados);
        return $relatorio->emitir();
    }

    protected function processaFiltros(array $filtros)
    {
        $this->exercicio = (int)$filtros['DB_anousu'];
        $this->instituicoes = $filtros['instituicoes'];
        $this->periodicidade = $filtros['periodicidade'];
        $this->agruparPor = $filtros['agruparPor'];

        $this->dados['periodosImpressao'] = $this->getPeriodos();
        $this->dados['filtros']['filtrouRecurso'] = $this->getPeriodos();
        if (!empty($filtros['recursos'])) {
            $this->recursos = $filtros['recursos'];
        }

        $this->dados['filtros']['exercicio'] = $this->exercicio;
        $this->dados['filtros']['agruparPor'] = $this->agruparPor;
        $this->dados['filtros']['periodicidade'] = $this->periodicidade;
        $this->buscarNotasExplicativas($filtros['DB_anousu'], $filtros['DB_instit'], 77);
    }

    private function processar()
    {
        $this->processarReceita();
        $this->processaDespesa();

        $this->organizaDados();
    }

    private function processarReceita()
    {
        $dadosMetas = $this->metasArrecadacaoService->processar();
        $this->metasReceita = $dadosMetas['dados'];
    }

    private function processaDespesa()
    {
        $repository = new AcompanhamentoCronogramaDespesaRepository();
        $repository->scopeInstituicoes($this->instituicoes);
        if (!empty($this->recursos)) {
            $repository->scopeRecursos($this->recursos);
        }
        $cotas = $repository->buscarDados($this->exercicio);
        $this->agruparDespesa($cotas);
    }

    private function agruparDespesa(array $cotas)
    {
        $dadosAgrupados = [];
        $agruparPor = $this->agruparPor;
        if ($this->agruparPor === 'geral') {
            $agruparPor = 'recurso';
        }

        foreach ($cotas as $cota) {
            $agrupar = $cota->{$agruparPor};

            if (!array_key_exists($agrupar, $dadosAgrupados)) {
                $codigo = $cota->recurso;
                $descricao = sprintf(
                    '%s - %s | %s - %s',
                    $cota->fonte_recurso,
                    $cota->descricao_recurso,
                    $cota->complemento,
                    $cota->descricao_complemento
                );

                if ($this->agruparPor === 'fonte_recurso') {
                    $descricao = sprintf('%s - %s', $cota->fonte_recurso, $cota->descricao_recurso);
                    $codigo = $cota->fonte_recurso;
                }
                $dadosAgrupados[$agrupar] = $this->criaObjeto();
                $dadosAgrupados[$agrupar]->codigo = $codigo;
                $dadosAgrupados[$agrupar]->descricao = $descricao;
            }

            $dadosAgrupados[$agrupar]->valor += $this->totalizaLinha($cota);
            if ($this->periodicidade === 'mensal') {
                $dadosAgrupados[$agrupar]->janeiro += $cota->janeiro;
                $dadosAgrupados[$agrupar]->fevereiro += $cota->fevereiro;
                $dadosAgrupados[$agrupar]->marco += $cota->marco;
                $dadosAgrupados[$agrupar]->abril += $cota->abril;
                $dadosAgrupados[$agrupar]->maio += $cota->maio;
                $dadosAgrupados[$agrupar]->junho += $cota->junho;
                $dadosAgrupados[$agrupar]->julho += $cota->julho;
                $dadosAgrupados[$agrupar]->agosto += $cota->agosto;
                $dadosAgrupados[$agrupar]->setembro += $cota->setembro;
                $dadosAgrupados[$agrupar]->outubro += $cota->outubro;
                $dadosAgrupados[$agrupar]->novembro += $cota->novembro;
                $dadosAgrupados[$agrupar]->dezembro += $cota->dezembro;
            } else {
                $dadosAgrupados[$agrupar]->bimestre_1 += $cota->janeiro;
                $dadosAgrupados[$agrupar]->bimestre_1 += $cota->fevereiro;
                $dadosAgrupados[$agrupar]->bimestre_2 += $cota->marco;
                $dadosAgrupados[$agrupar]->bimestre_2 += $cota->abril;
                $dadosAgrupados[$agrupar]->bimestre_3 += $cota->maio;
                $dadosAgrupados[$agrupar]->bimestre_3 += $cota->junho;
                $dadosAgrupados[$agrupar]->bimestre_4 += $cota->julho;
                $dadosAgrupados[$agrupar]->bimestre_4 += $cota->agosto;
                $dadosAgrupados[$agrupar]->bimestre_5 += $cota->setembro;
                $dadosAgrupados[$agrupar]->bimestre_5 += $cota->outubro;
                $dadosAgrupados[$agrupar]->bimestre_6 += $cota->novembro;
                $dadosAgrupados[$agrupar]->bimestre_6 += $cota->dezembro;
            }
        }
        ksort($dadosAgrupados);
        $this->cotasDespesa = $dadosAgrupados;
    }

    private function criaObjeto()
    {
        $objeto = new \stdClass();
        $objeto->valor = 0;

        if ($this->periodicidade === 'mensal') {
            $objeto->janeiro = 0;
            $objeto->fevereiro = 0;
            $objeto->marco = 0;
            $objeto->abril = 0;
            $objeto->maio = 0;
            $objeto->junho = 0;
            $objeto->julho = 0;
            $objeto->agosto = 0;
            $objeto->setembro = 0;
            $objeto->outubro = 0;
            $objeto->novembro = 0;
            $objeto->dezembro = 0;
        } else {
            $objeto->bimestre_1 = 0;
            $objeto->bimestre_2 = 0;
            $objeto->bimestre_3 = 0;
            $objeto->bimestre_4 = 0;
            $objeto->bimestre_5 = 0;
            $objeto->bimestre_6 = 0;
        }

        return $objeto;
    }

    private function totalizaLinha($cronograma)
    {
        return $cronograma->janeiro + $cronograma->fevereiro + $cronograma->marco + $cronograma->abril +
            $cronograma->maio + $cronograma->junho + $cronograma->julho + $cronograma->agosto +
            $cronograma->setembro + $cronograma->outubro + $cronograma->novembro + $cronograma->dezembro;
    }

    private function organizaDados()
    {
        switch ($this->agruparPor) {
            case 'recurso':
            case 'fonte_recurso':
                $this->totalizaPorRecurso();
                break;
            case 'geral':
                $this->totalizaGeral();
                break;
        }
    }

    private function totalizaPorRecurso()
    {
        // percorre as cotas de depesa e monta a tabela de valores
        // através da despesa busca os dados da receita pelo recurso
        foreach ($this->cotasDespesa as $cota) {
            $periodos = $this->getPeriodos();

            $metaReceita = $this->getMetaRecurso($cota->codigo);
            $valorReceita = is_null($metaReceita) ? 0 : $metaReceita->valor;

            $objeto = (object)[
                "codigo" => $cota->codigo,
                "descricao" => $cota->descricao,
                "total_despesa" => $cota->valor,
                "total_receita" => $valorReceita,
                "diferenca" => round($valorReceita - $cota->valor, 2),
            ];

            foreach ($periodos as $periodo) {
                $despesa = $this->criaObjetoComValorPercentual();
                $despesa->valor = (float)$cota->{$periodo};
                if ($cota->valor != 0) {
                    $despesa->percentual = round(($cota->{$periodo} * 100) / $cota->valor, 2);
                }
                $receita = $this->criaObjetoComValorPercentual();
                if (!is_null($metaReceita)) {
                    $receita->valor = $metaReceita->{$periodo};
                    if ($metaReceita->valor != 0) {
                        $receita->percentual = round(($metaReceita->{$periodo} * 100) / $metaReceita->valor, 2);
                    }
                }
                $objeto->{$periodo} = new \stdClass();
                $objeto->{$periodo}->despesa = $despesa;
                $objeto->{$periodo}->receita = $receita;
                $objeto->{$periodo}->diferenca = round($receita->valor - $despesa->valor, 2);
            }

            $this->dados['dados'][$cota->codigo] = $objeto;
        }

        // como foi identificado receitas de recursos que não haviam projeção da despesa
        // nesse loop identificamos as receitas que ficaram de fora.
        foreach ($this->metasReceita as $metaReceita) {
            $propriedade = 'fonte_recurso';
            if (in_array($this->agruparPor, ['recurso', 'geral'])) {
                $propriedade = 'codigo_recurso';
            }

            $codigo = $metaReceita->{$propriedade};
            if (array_key_exists($codigo, $this->dados['dados'])) {
                continue;
            }

            $descricao = "{$metaReceita->fonte_recurso} - {$metaReceita->descricao_recurso}";
            if ($propriedade === 'codigo_recurso') {
                $descricao .= " | $metaReceita->complemento - {$metaReceita->descricao_complemento}";
            }

            $objeto = (object)[
                "codigo" => $codigo,
                "descricao" => $descricao,
                "total_despesa" => 0,
                "total_receita" => $metaReceita->valor,
                "diferenca" => round($metaReceita->valor - 0, 2),
            ];

            foreach ($periodos as $periodo) {
                $receita = $this->criaObjetoComValorPercentual();

                $receita->valor = (float) $metaReceita->{$periodo};
                if ($metaReceita->valor != 0) {
                    $receita->percentual = round(($metaReceita->{$periodo} * 100) / $metaReceita->valor, 2);
                }

                $objeto->{$periodo} = new \stdClass();
                $objeto->{$periodo}->despesa = $this->criaObjetoComValorPercentual();
                $objeto->{$periodo}->receita = $receita;
                $objeto->{$periodo}->diferenca = round($receita->valor - 0, 2);
            }
            $this->dados['dados'][$codigo] = $objeto;
        }

        ksort($this->dados['dados']);
    }

    public function criaObjetoComValorPercentual()
    {
        return (object)[
            'valor' => 0,
            'percentual' => 0
        ];
    }

    /**
     * @param $codigo
     * @return mixed
     */
    private function getMetaRecurso($codigo)
    {
        if (!array_key_exists($codigo, $this->metasReceita)) {
            return null;
        }
        return $this->metasReceita[$codigo];
    }

    private function totalizaGeral()
    {
        $this->totalizaPorRecurso();
        $objeto = (object)[
            "codigo" => null,
            "descricao" => 'Total Geral',
            "total_despesa" => 0,
            "total_receita" => 0,
            "diferenca" => 0,
        ];
        $periodos = $this->getPeriodos();

        // cria os objetos zerados
        foreach ($periodos as $periodo) {
            $objeto->{$periodo} = new \stdClass();
            $objeto->{$periodo}->despesa = $this->criaObjetoComValorPercentual();
            $objeto->{$periodo}->receita = $this->criaObjetoComValorPercentual();
            $objeto->{$periodo}->diferenca = 0;
        }

        // totaliza os valores
        foreach ($this->dados['dados'] as $dado) {
            $objeto->total_despesa += $dado->total_despesa;
            $objeto->total_receita += $dado->total_receita;

            foreach ($periodos as $periodo) {
                $objeto->{$periodo}->despesa->valor += $dado->{$periodo}->despesa->valor;
                $objeto->{$periodo}->receita->valor += $dado->{$periodo}->receita->valor;
            }
        }

        // calcula percentual e diferenças
        foreach ($periodos as $periodo) {
            $valorDespesa = $objeto->{$periodo}->despesa->valor;
            $valorReceita = $objeto->{$periodo}->receita->valor;

            $objeto->{$periodo}->despesa->percentual = round(($valorDespesa * 100) / $objeto->total_despesa, 2);
            $objeto->{$periodo}->receita->percentual = round(($valorReceita * 100) / $objeto->total_receita, 2);
            $objeto->{$periodo}->diferenca = round($valorReceita - $valorDespesa, 2);
        }

        $objeto->diferenca = $objeto->total_receita - $objeto->total_despesa;
        $this->dados['totalGeral'] = $objeto;
    }
}
