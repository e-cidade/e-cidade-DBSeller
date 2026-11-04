<?php

namespace App\Domain\Financeiro\Contabilidade\Relatorios\Balancete\Verificacao;

use ECidade\File\Csv\Dumper\Dumper;

class BalanceteVerificacaoCsv extends Dumper
{
    private $dados = [];

    public function setDados(array $dados)
    {
        $this->dados = $dados;
    }

    public function emitir()
    {
        $filename = sprintf('tmp/balancete-verificacao-%s.csv', time());
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
            $dadosImprimir[] = $this->linha($dado);
        }
        return $dadosImprimir;
    }

    private function cabecalho()
    {
        return [
            'estrutural',
            'reduzido',
            'Sintetica',
            'nome',
            'instituicao',
            'siconfi',
            'gestao',
            'subrecurso',
            'complemento',
            'saldo_anterior',
            'saldo_debito',
            'saldo_credito',
            'saldo_final',
        ];
    }

    private function linha($dado)
    {
        return [
            $dado->estrutural,
            implode(', ', $dado->reduzidos),
            $dado->sintetica ? 'Sim' : 'Não',
            $dado->nome,
            $dado->instituicao,
            $dado->siconfi,
            $dado->gestao,
            $dado->subrecurso,
            $dado->complemento,
            $dado->saldo_anterior,
            $dado->saldo_debito,
            $dado->saldo_credito,
            $dado->saldo_final
        ];
    }
}
