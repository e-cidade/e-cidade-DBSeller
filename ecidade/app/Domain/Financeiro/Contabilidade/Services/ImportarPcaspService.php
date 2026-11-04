<?php

namespace App\Domain\Financeiro\Contabilidade\Services;

use App\Domain\Financeiro\Contabilidade\Builder\PcaspBuilder;
use App\Domain\Financeiro\Contabilidade\Contracts\PlanoContasPcaspInterface;
use App\Domain\Financeiro\Contabilidade\Mappers\PlanoContas\Pcasp\E2022\UniaoMapper;
use App\Domain\Financeiro\Contabilidade\Models\Pcasp;
use App\Domain\Financeiro\Contabilidade\Models\PlanoDespesa;
use ECidade\File\Csv\LerCsv;
use Exception;

class ImportarPcaspService
{
    /**
     * @var PlanoContasPcaspInterface
     */
    private $layout;
    /**
     * @var integer
     */
    private $exercicio;
    /**
     * @var string
     */
    private $filepath;
    /**
     * Tipo do plano selecionado
     * @var string
     */
    private $plano;
    /**
     * @var string
     */
    private $uf;
    /**
     * @var bool
     */
    private $uniao;

    /**
     * @param array $filtros
     * @throws Exception
     */
    public function setFiltrosFromRequest(array $filtros)
    {
        $this->plano = $filtros['plano'];
        $this->uf = getEstadoInstituicao();
        $this->uniao = PcaspBuilder::UNIAO == $this->plano;
        $this->layout = new UniaoMapper();
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

        $dados = [];
        foreach ($linha as $key => $dadosLinha) {
            if (!$this->validaLinhaImportar($key, $dadosLinha)) {
                continue;
            }

            // usando BULK inserts
            $dados[] = $this->buildDados($dadosLinha);

            // remove do array as contas importadas
            $dados = array_filter($dados, function ($contaImportar) {
                return Pcasp::query()
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
    }

    private function inserir($dados)
    {
        $model = new Pcasp();
        $model->insert($dados);
    }

    public function atualizar()
    {
        $linha = $this->getLinha();

        foreach ($linha as $key => $dadosLinha) {
            if (!$this->validaLinhaImportar($key, $dadosLinha)) {
                continue;
            }

            $dados = $this->buildDados($dadosLinha);

            Pcasp::query()
                ->where('uniao', PcaspBuilder::UNIAO == $this->plano)
                ->where('exercicio', $this->exercicio)
                ->where('conta', $dados['conta'])
                ->update(['nome' => $dados['nome'], 'funcao' => $dados['funcao']]);
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
     * Recebe a linha do csv e retorna um arrary estruturado chave valor para manutenção na model PCASP
     * @param array $dadosLinha dados da linha do csv
     * @return array
     */
    public function buildDados($dadosLinha)
    {
        return (new PcaspBuilder())
            ->addExercicio($this->exercicio)
            ->addTipoPlano($this->plano)
            ->addLayout($this->layout)
            ->addLinha($dadosLinha)
            ->build();
    }

    private function validaImportacao()
    {
        $uniao = PcaspBuilder::UNIAO == $this->plano;
        $importado = Pcasp::query()
                ->where('uniao', $uniao)
                ->where('exercicio', $this->exercicio)
                ->get()
                ->count() != 0;

        if ($importado) {
            throw new Exception(sprintf(
                'Já foi importado o plano de contas do exercício %s %s',
                $this->exercicio,
                $uniao ? 'da união' : 'do tribunal regional'
            ));
        }
    }

    /**
     * @param $key
     * @param $dadosLinha
     * @return bool
     */
    private function validaLinhaImportar($key, $dadosLinha)
    {
        if ($key < $this->layout->linhaInicio()) {
            return false;
        }
        // valida se tem dados na coluna em que deveria conter o código da conta
        $conta = $dadosLinha[$this->layout->indexColunaConta()];
        if (empty($conta)) {
            return false;
        }

        // Valida se a conta esta ativa... Só importa contas ativas.
        $status = $dadosLinha[$this->layout->colunaStatus()];
        if (!$this->layout->importar($status)) {
            return false;
        }
        return true;
    }
}
