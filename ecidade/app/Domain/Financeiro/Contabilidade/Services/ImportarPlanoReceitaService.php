<?php

namespace App\Domain\Financeiro\Contabilidade\Services;

use App\Domain\Financeiro\Contabilidade\Builder\PlanoOrcamentarioReceitaBuilder;
use App\Domain\Financeiro\Contabilidade\Builder\PlanoOrcamentarioReceitaROBuilder;
use App\Domain\Financeiro\Contabilidade\Builder\PlanoOrcamentarioReceitaRSBuilder;
use App\Domain\Financeiro\Contabilidade\Mappers\PlanoContas\Orcamentario\E2022\ReceitaUniaoMapper;
use App\Domain\Financeiro\Contabilidade\Mappers\PlanoContas\PlanoContas;
use App\Domain\Financeiro\Contabilidade\Models\PlanoReceita;
use ECidade\File\Csv\LerCsv;
use Exception;

class ImportarPlanoReceitaService
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
    /**
     * @var string
     */
    private $filepath;
    /**
     * @var string
     */
    private $uf;
    /**
     * @var bool
     */
    private $uniao;

    public function setFiltrosFromRequest(array $filtros)
    {
        $this->uf = getEstadoInstituicao();
        $this->plano = $filtros['plano'];
        $this->layout = new ReceitaUniaoMapper();

        $this->uniao = PlanoContas::PLANO_UNIAO == $this->plano;

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

                $dadoBuild = $this->buildaDados($dadosLinha);

                // remove do array as contas importadas
                $dadoBuild = array_filter($dadoBuild, function ($contaImportar) {
                    return PlanoReceita::query()
                            ->where('uniao', $this->uniao)
                            ->where('exercicio', $this->exercicio)
                            ->where('conta', $contaImportar['conta'])
                            ->get()
                            ->count() === 0;
                });

                $dados = array_merge($dados, $dadoBuild);
                if (count($dados) >= 100) {
                    $this->inserir($dados);
                    $dados = [];
                }
            }

            if (!empty($dados)) {
                $this->inserir($dados);
            }
        } catch (Exception $exception) {
            throw new Exception('Erro ao importar planilha. Contate o Suporte.' . $exception->getMessage());
        }
    }

    public function atualizar()
    {
        $linha = $this->getLinha();
        try {
            foreach ($linha as $key => $dadosLinha) {
                if (!$this->validaLinhaCsv($key, $dadosLinha)) {
                    continue;
                }

                $dadoBuild = $this->buildaDados($dadosLinha);
                foreach ($dadoBuild as $dados) {
                    $this->update($dados);
                }
            }
        } catch (Exception $exception) {
            throw new Exception('Erro ao importar planilha. Contate o Suporte.' . $exception->getMessage());
        }
    }

    private function inserir($dados)
    {
        $model = new PlanoReceita();
        $model->insert($dados);
    }

    private function update($dados)
    {
        PlanoReceita::query()
            ->where('uniao', $this->uniao)
            ->where('exercicio', $this->exercicio)
            ->where('conta', $dados['conta'])
            ->update(['nome' => $dados['nome'], 'funcao' => $dados['funcao'], 'sintetica' => $dados['sintetica']]);
    }

    /**
     * @param $builder
     * @param array $dadosLinha
     * @return mixed
     */
    protected function preparaDados($builder, array $dadosLinha)
    {
        return $builder
            ->addExercicio($this->exercicio)
            ->addTipoPlano($this->plano)
            ->addLayout($this->layout)
            ->addLinha($dadosLinha)
            ->build();
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
     * @param $dadosLinha
     * @return mixed
     */
    public function buildaDados($dadosLinha)
    {
        $builder = new PlanoOrcamentarioReceitaBuilder();
        if ($this->uf === 'RS' && PlanoContas::PLANO_UNIAO !== $this->plano) {
            $builder = new PlanoOrcamentarioReceitaRSBuilder();
        }
        if ($this->uf === 'RO' && PlanoContas::PLANO_UNIAO !== $this->plano) {
            $builder = new PlanoOrcamentarioReceitaROBuilder();
        }

        // usando BULK inserts
        $dadoBuild = $this->preparaDados($builder, $dadosLinha);
        return $dadoBuild;
    }
}
