<?php

namespace App\Domain\Financeiro\Contabilidade\Relatorios;

use ECidade\File\Csv\Dumper\Dumper;
use stdClass;

class ConferenciaPorRecursosCSV extends Dumper
{
    private $dados = [];

    private $filtros;

    public function __construct(stdClass $filtros)
    {
        $this->filtros = $filtros;
    }

    public function setDados(array $dados)
    {
        $this->dados = $dados;
    }

    public function emitir()
    {
        $filename = sprintf('tmp/conferencia_por_recursos_%s.csv', time());
        $this->dumpToFile($this->organizarDados(), $filename);
        return $filename;
    }

    private function organizarDados()
    {
        $dadosImprimir = [$this->cabecalho()];
        foreach ($this->dados as $dado) {
            $dados = [];
            switch ($this->filtros->modeloEmissao) {
                case 2:
                    $dados[] = $dado->siconfi;
                    break;
                case 3:
                    $dados[] = $dado->siconfi;
                    $dados[] = $dado->complemento;
                    break;
                case 1:
                default:
                    $dados[] = $dado->gestao;
                    $dados[] = $dado->subrecurso;
                    $dados[] = $dado->complemento;
                    break;
            }

            $dadosImprimir[] = array_merge(
                $dados,
                [
                formataValorMonetario($dado->saldo_ativo_at_f),
                formataValorMonetario($dado->saldo_extra_orcamentario),
                formataValorMonetario($dado->valor_a_liquidar),
                formataValorMonetario($dado->valor_a_pagar),
                formataValorMonetario($dado->valor_a_liquidar_rp),
                formataValorMonetario($dado->valor_a_pagar_rp),
                formataValorMonetario($dado->total),
                formataValorMonetario($dado->valor_disponibilidade),
                formataValorMonetario($dado->diferenca),
                ]
            );
        }
        return $dadosImprimir;
    }

    private function cabecalho()
    {
        switch ($this->filtros->modeloEmissao) {
            case 2:
                $header = ['Siconfi'];
                break;
            case 3:
                $header = ['Siconfi', 'Complemento'];
                break;
            case 1:
            default:
                $header = ['Gestão', 'Subrecurso', 'Complemento'];
                break;
        }

        return array_merge(
            $header,
            [
            'Saldo Ativo',
            'Saldo Extra',
            'Vlr. Liquidar',
            'Vlr. Pagar',
            'Vlr. Liq. RP',
            'Vlr. Pagar RP',
            'Total',
            'Vlr. Disp.',
            'Diferença',
            ]
        );
    }
}
