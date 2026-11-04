<?php

namespace App\Domain\Financeiro\Contabilidade\Relatorios\Balancete\Verificacao;

use ECidade\File\Csv\Dumper\Dumper;

class BalanceteVerificacaoInformacaoComplementar extends Dumper
{

    private $dados = [];

    public function setDados(array $dados)
    {
        $this->dados = $dados;
    }

    public function emitir()
    {
        $this->setCsvControl(";");
        $filename = sprintf('tmp/balancete-verificacao-informacao-complementar-%s.csv', time());
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
            'União',
            'e-Cidade',
            'Instituicao',
            'reduzido',
            'PO',
            'FP',
            'DC',
            'FS',
            'FR',
            'CO',
            'NR',
            'ND',
            'AI',
            'saldo_anterior',
            'saldo_debito',
            'saldo_credito',
            'saldo_final',
        ];
    }

    private function linha($dado)
    {
        return [
            $dado->estrutural_padrao,
            $dado->estrutural,
            $dado->instituicao,
            $dado->reduzido,
            $dado->poder_ordao,
            $dado->indicador_superavit,
            $dado->divida_consolidada,
            "{$dado->funcao}{$dado->subfuncao}",
            $dado->siconfi,
            $dado->complemento,
            $dado->nr,
            $dado->nd,
            $dado->ai,
            number_format($dado->saldo_anterior, 2, ',', '.'),
            number_format($dado->saldo_debito, 2, ',', '.'),
            number_format($dado->saldo_credito, 2, ',', '.'),
            number_format($dado->saldo_final, 2, ',', '.'),
        ];
    }
}
