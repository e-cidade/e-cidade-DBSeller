<?php

namespace App\Domain\Financeiro\Contabilidade\Relatorios\Deliberacao;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use ECidade\File\Csv\Dumper\Dumper;

class ModeloCincoCsv extends Dumper
{
    private $filtros = [];

    private $dadosMde = [];
    private $dadosFundeb = [];

    private $dadosImprimir = [];
    /**
     * @var DBConfig
     */
    private $instituicao;

    public function setFiltros(array $filtros)
    {
        $this->filtros = $filtros;
    }

    public function setDadosMde(array $dadosMde)
    {
        $this->dadosMde = $dadosMde;
    }

    public function setDadosFundeb(array $dadosFundeb)
    {
        $this->dadosFundeb = $dadosFundeb;
    }

    public function setInstituicao(DBConfig $instituicao)
    {
        $this->instituicao = $instituicao;
    }

    public function emitir()
    {
        $this->setCsvControl();
        $filename = sprintf('tmp/modelo-5-%s.csv', time());
        $this->dumpToFile($this->organizarDados(), $filename);
        return [
            'csv' => $filename,
            'csvLinkExterno' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    private function getDadosOrgao()
    {
        return [
            'Órgão',
            $this->instituicao->nomeinst,
            'Município',
            $this->instituicao->munic,
            'Exercício',
            $this->filtros['exercicio']
        ];
    }

    private function organizarDados()
    {
        $this->dadosImprimir[] = '';
        $this->dadosImprimir[] = 'Modelo 5';
        $this->dadosImprimir[] = '';
        $this->dadosImprimir[] = $this->getDadosOrgao();
        $this->dadosImprimir[] = '';
        $this->dadosImprimir[] = '';
        foreach ($this->filtros['tipoConta'] as $tipoConta) {
            if ($tipoConta === 'fundeb') {
                $this->imprimeFundeb();
            }
            if ($tipoConta === 'mde') {
                $this->imprimeMde();
            }
        }

        return $this->dadosImprimir;
    }

    private function imprimeFundeb()
    {
        $this->imprimeDadosDisponibilidade($this->dadosFundeb['disponibilidade']);
        $this->imprimeDebitos($this->dadosFundeb['debitos']);
        $this->imprimeCreditos($this->dadosFundeb['creditos']);
    }

    private function imprimeMde()
    {
        $this->imprimeDadosDisponibilidade($this->dadosMde['disponibilidade']);
        $this->imprimeDebitos($this->dadosMde['debitos']);
        $this->imprimeCreditos($this->dadosMde['creditos']);
    }

    private function imprimeDadosDisponibilidade($dados)
    {
        $this->dadosImprimir[] = [
            'Banco', 'Conta Bancária', 'Saldo em 31/12', 'Débitos (R$) (B)', 'Débitos (R$) (B)', 'Créditos (R$) (C)',
            'Créditos (R$) (C)', 'Saldo Contábil em 31/12'
        ];
        $this->dadosImprimir[] = [
            '', '', 'conforme extrato(R$) (A)', 'Anteriores ao Exercício de Referência',
            'Relativos ao Exercício de Referência',
            'Anteriores ao Exercício de Referência', 'Relativos ao Exercício de Referência', '(R$) (D = A + B - C)'
        ];

        $valorTotal = 0;
        foreach ($dados as $dado) {
            $dado->saldo_contabil = (
                    $dado->saldo_31_12 + abs($dado->debito_ex_anterior) + abs($dado->debito_ex_atual)) -
                (abs($dado->credito_ex_anterior) + abs($dado->credito_ex_atual)
                );
            $valorTotal += $dado->saldo_contabil;
            $this->dadosImprimir[] = [
                $dado->banco,
                $dado->agencia_conta,
                formataValorMonetario($dado->saldo_31_12),
                formataValorMonetario($dado->debito_ex_anterior),
                formataValorMonetario($dado->debito_ex_atual),
                formataValorMonetario($dado->credito_ex_anterior),
                formataValorMonetario($dado->credito_ex_atual),
                formataValorMonetario($dado->saldo_contabil)
            ];
        }

        $this->dadosImprimir[] = [
            'Total Bancos em 31/12 (I)', '', '', '', '', '', '', formataValorMonetario($valorTotal)
        ];
        $this->dadosImprimir[] = ['Total Bancos em 31/12 (I)', '', '', '', '', '', '', formataValorMonetario(0)];
        $this->dadosImprimir[] = [
            'Total das Disponibilidades Financeiras  (III = I + II)', '', '', '', '', '', '',
            formataValorMonetario($valorTotal)
        ];
    }

    private function imprimeDebitos($debitos)
    {
        $this->dadosImprimir[] = '';
        $this->dadosImprimir[] = '';
        $this->dadosImprimir[] = 'MODELO 5 - QUADRO I - DÉBITOS';
        $this->imprimeDadosBancarios($debitos);
    }

    private function imprimeCreditos($creditos)
    {
        $this->dadosImprimir[] = '';
        $this->dadosImprimir[] = '';
        $this->dadosImprimir[] = 'MODELO 5 - QUADRO II - CRÉDITOS';
        $this->imprimeDadosBancarios($creditos);
    }

    public function imprimeDadosBancarios($valores)
    {
        $this->dadosImprimir[] = $this->getDadosOrgao();

        foreach ($valores as $banco) {
            $labelHistorico = $banco->tipo === 'D' ? 'Débito' : 'Crédito';
            $cabecalhoBanco = [
                "Data do Evento",
                "Número do Documento",
                "Natureza/Histórico do" . $labelHistorico,
                "Valor (R$)",
                "Nota Explicativa para Regularização",
            ];

            $this->dadosImprimir[] = ['Banco', $banco->banco, 'Conta Bancária ', $banco->conta];
            $this->dadosImprimir[] = 'Anteriores ao Exercício de Referência';
            $this->dadosImprimir[] = $cabecalhoBanco;

            $totalAnterior = $this->imprimeValoresQuadroBanco($banco->anteriorExercicio);
            $this->dadosImprimir[] = ['Subtotal (A)', '', '', formataValorMonetario($totalAnterior)];

            $this->dadosImprimir[] = 'Relativos ao Exercício de Referência';
            $this->dadosImprimir[] = $cabecalhoBanco;

            $totalExercicio = $this->imprimeValoresQuadroBanco($banco->doExercicio);
            $this->dadosImprimir[] = ['Subtotal (B)', '', '', formataValorMonetario($totalExercicio)];

            $total = $totalAnterior + $totalExercicio;
            $this->dadosImprimir[] = ['Total (A+B)', '', '', formataValorMonetario($total)];
        }
    }

    private function imprimeValoresQuadroBanco($valores)
    {
        $total = 0;
        foreach ($valores as $dado) {
            $total += $dado->valor;

            $this->dadosImprimir[] = [
                db_formatar($dado->data_evento, 'd'),
                $dado->numero_documento,
                substr($dado->natureza, 0, 25),
                formataValorMonetario(abs($dado->valor)),
                substr($dado->nota_explicativa, 0, 50),
                $dado->data_regularizacao
            ];
        }

        return $total;
    }
}
