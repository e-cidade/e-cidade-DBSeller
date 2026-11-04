<?php

namespace App\Domain\Financeiro\Contabilidade\Relatorios\Mapeamento;

use App\Domain\Financeiro\Contabilidade\Mappers\PlanoContas\PlanoContas;
use ECidade\File\Csv\Dumper\Dumper;

class MapeamentoPlanoContasCsv extends Dumper
{
    protected $contasSemVinculo = [];
    protected $contasComVinculo = [];
    protected $contasSemVinculoComMovimentacao = [];
    /**
     * @var integer
     */
    private $exercicio;
    /**
     * @var string
     */
    private $tipoPlano;

    protected $nomeArquivo = 'mapeamento_orcamentario';

    public function emitir()
    {
        $this->setCsvControl(';', '"');
        $filename = sprintf('tmp/%s-%s.csv', $this->nomeArquivo, time());
        $this->dumpToFile($this->organizarDados(), $filename);
        return [
            'csv' => $filename,
            'csvLinkExterno' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    public function setNomeArquivo($nome)
    {
        $this->nomeArquivo = $nome;
    }

    public function setExercicio($exercicio)
    {
        $this->exercicio = $exercicio;
    }

    public function setTipoPlano($tipoPlano)
    {
        $this->tipoPlano = 'Estadual / Regional';
        if ($tipoPlano === PlanoContas::PLANO_UNIAO) {
            $this->tipoPlano = 'União / Federação';
        }
    }

    public function setContas($contasSemVinculo, $contasComVinculo)
    {
        $this->contasSemVinculo = $contasSemVinculo;
        $this->contasComVinculo = $contasComVinculo;
    }

    private function organizarDados()
    {
        $dadosImprimir = [
            ['Exercício', $this->exercicio],
            ['Plano', $this->tipoPlano],
        ];

        $dadosImprimir[] = [];
        $dadosImprimir[] = ['', 'Contas do e-cidade com Movimentação não vinculadas ao plano de governo'];
        $dadosImprimir[] = ['Estrutural', 'Nome'];
        foreach ($this->contasSemVinculoComMovimentacao as $conta) {
            $dadosImprimir[] = [$conta->c60_estrut, $conta->c60_descr];
        }

        $dadosImprimir[] = [];
        $dadosImprimir[] = ['', 'Contas do e-cidade não vinculadas ao plano de governo'];
        $dadosImprimir[] = ['Estrutural', 'Nome'];
        foreach ($this->contasSemVinculo as $conta) {
            $dadosImprimir[] = [$conta->c60_estrut, $conta->c60_descr];
        }

        $dadosImprimir[] = [];
        $dadosImprimir[] = ['', 'Contas do e-cidade vinculadas ao plano de governo'];
        $dadosImprimir[] = ['Estrutural e-Cidade', 'Nome e-Cidade', 'Estrutural Governo', 'Nome Governo'];
        foreach ($this->contasComVinculo as $conta) {
            $dadosImprimir[] = [
                $conta->estrutural_ecidade,
                $conta->nome_ecidade,
                $conta->estrutural_governo,
                $conta->nome_governo
            ];
        }

        return $dadosImprimir;
    }

    public function setContasSemVinculoComMovimentacao($contasSemVinculoComMovimentacao)
    {
        $this->contasSemVinculoComMovimentacao = $contasSemVinculoComMovimentacao;
    }
}
