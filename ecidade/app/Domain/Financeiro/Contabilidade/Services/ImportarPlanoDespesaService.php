<?php

namespace App\Domain\Financeiro\Contabilidade\Services;

use App\Domain\Financeiro\Contabilidade\Builder\PlanoOrcamentarioDespesaBuilder;
use App\Domain\Financeiro\Contabilidade\Builder\PlanoOrcamentarioDespesaRsBuilder;
use App\Domain\Financeiro\Contabilidade\Factories\PlanoContasOrcamentarioDespesaFactory;
use App\Domain\Financeiro\Contabilidade\Mappers\PlanoContas\Orcamentario\E2022\DespesaUniaoMapper;
use App\Domain\Financeiro\Contabilidade\Mappers\PlanoContas\PlanoContas;
use App\Domain\Financeiro\Contabilidade\Models\PlanoDespesa;
use ECidade\File\Csv\LerCsv;
use Exception;

class ImportarPlanoDespesaService
{
    /**
     * Tipo do plano selecionado
     * @var string
     */
    private $plano;
    /**
     * @var integer
     */
    private $exercicio;
    private $filepath;
    private $uf;
    /**
     * @var bool
     */
    private $uniao;

    public function setFiltrosFromRequest(array $filtros)
    {
        $this->uf = getEstadoInstituicao();
        $this->plano = $filtros['plano'];
        $this->layout = new DespesaUniaoMapper();
        $this->uniao = PlanoContas::PLANO_UNIAO === $this->plano;

        $this->exercicio = $filtros['exercicio'];
        $file = \JSON::create()->parse(str_replace('\"', '"', $filtros['file']));
        if ($file->extension !== 'csv') {
            throw new Exception('O arquivo deve vir no formato csv.');
        }
        $this->filepath = $file->path;
    }

    public function processar()
    {
        $linha = $this->getLinha();

        try {
            $dados = [];
            foreach ($linha as $key => $dadosLinha) {
                if (!$this->validaLinhaCsv($key, $dadosLinha)) {
                    continue;
                }

                $dados[] = $this->buildDados($dadosLinha);

                // remove do array as contas importadas
                $dados = array_filter($dados, function ($contaImportar) {
                    return PlanoDespesa::query()
                            ->where('uniao', $this->uniao)
                            ->where('exercicio', $this->exercicio)
                            ->where('conta', $contaImportar['conta'])
                            ->get()
                            ->count() === 0;
                });

                if (count($dados) === 100) {
                    $this->inserir($dados);
                    $dados = [];
                }
            }

            if (!empty($dados)) {
                $this->inserir($dados);
            }
        } catch (Exception $exception) {
            throw new Exception('Erro ao importar planilha. Contate o Suporte. ' . $exception->getMessage());
        }
    }

    private function inserir($dados)
    {
        $model = new PlanoDespesa();
        $model->insert($dados);
    }

    public function atualizar()
    {
        $linha = $this->getLinha();

        try {
            foreach ($linha as $key => $dadosLinha) {
                if (!$this->validaLinhaCsv($key, $dadosLinha)) {
                    continue;
                }

                $dados = $this->buildDados($dadosLinha);
                $campos = ['nome' => $dados['nome'], 'funcao' => $dados['funcao'], 'sintetica' => $dados['sintetica']];
                PlanoDespesa::query()
                    ->where('uniao', $this->uniao)
                    ->where('exercicio', $this->exercicio)
                    ->where('conta', $dados['conta'])
                    ->update($campos);
            }
        } catch (Exception $exception) {
            throw new Exception('Erro ao importar planilha. Contate o Suporte. ' . $exception->getMessage());
        }
    }

    /**
     * @return \Generator
     */
    public function getLinha()
    {
        $csv = new LerCsv($this->filepath);
        $csv->setCsvControl(';');
        $linha = $csv->read();
        return $linha;
    }

    /**
     * Valida se a linha do csv é uma linha valida para impressão
     * @param integer $numeroLinha
     * @param array $dadosLinha
     * @return bool
     */
    protected function validaLinhaCsv($numeroLinha, $dadosLinha)
    {
        if ($numeroLinha < $this->layout->linhaInicio()) {
            return false;
        }
        // valida se tem dados na coluna em que deveria conter o código da conta
        $conta = $dadosLinha[$this->layout->indexColunaConta()];
        if (empty($conta)) {
            return false;
        }
        return true;
    }

    /**
     * Recebe a linha do csv e retorna um arrary estruturado chave valor para manutenção na model
     * @param array $dadosLinha
     * @return array
     */
    public function buildDados($dadosLinha)
    {
        return (new PlanoOrcamentarioDespesaBuilder())
            ->addExercicio($this->exercicio)
            ->addTipoPlano($this->plano)
            ->addLayout($this->layout)
            ->addLinha($dadosLinha)
            ->build();
    }
}
