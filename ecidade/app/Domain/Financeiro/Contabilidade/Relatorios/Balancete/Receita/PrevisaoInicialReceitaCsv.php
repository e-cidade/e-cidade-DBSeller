<?php

namespace App\Domain\Financeiro\Contabilidade\Relatorios\Balancete\Receita;

use ECidade\File\Csv\Dumper\Dumper;

class PrevisaoInicialReceitaCsv extends Dumper
{
    private $dados = [];

    public function setDados(array $dados)
    {
        $this->dados = $dados;
    }

    public function emitir()
    {
        $filename = sprintf('tmp/balancete-receita-%s.csv', time());
        $this->dumpToFile($this->organizarDados(), $filename);
        return [
            'csv' => $filename,
            'csvLinkExterno' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    private function organizarDados()
    {
        $dadosImprimir = [$this->cabecalho()];
        foreach ($this->dados as $dado) {
            $dadosImprimir[] = [
                $dado->mascara,
                $dado->sintetico ? 'Não' : 'Sim',
                $dado->descricao,
                $dado->cp,
                $dado->reduzido,
                $dado->gestao,
                $dado->complemento,
                formataValorMonetario($dado->valor_inicial),
                formataValorMonetario($dado->previsao_adicional)
            ];
        }
        return $dadosImprimir;
    }

    private function cabecalho()
    {
        return [
            'Receita',
            'Valorizavel',
            'Descrição',
            'CP',
            'Reduzidos',
            'Recurso',
            'Complemento',
            'Previsto',
            'Prev. Adic.',
        ];
    }
}
