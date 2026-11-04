<?php

namespace App\Domain\Financeiro\Contabilidade\Services;

use cl_conplanoatributosaldo;
use ECidade\File\Csv\LerCsv;
use Exception;

class ImportarSaldosMatrizService
{
    const LINHA_HEADER = 1;
    const LINHA_IMPORTAR = 'ending_balance';

    /**
     * @var integer
     */
    private $exercicio;

    /**
     * @var integer
     */
    private $mes;

    /**
     * @var \int[][]
     */
    private $quantidadeColunas = [
        2022 => ['quantidade_tipos' => 6],
        2023 => ['quantidade_tipos' => 6],
    ];

    /**
     * mapeia a posição das colunas
     * @var array
     */
    protected $mapaColunas = [
        'conta' => 0,
        'valor' => null,
        'tipo_valor' => null,
        'natureza_valor' => null,
    ];

    /**
     *
     * @param array $dados
     * @throws Exception
     */
    public function importar(array $dados)
    {
        $this->exercicio = $dados['exercicio'];
        $this->mes = (int)$dados['mes'];

        $this->mapeiaPosicaoColunas();
        $this->removerSaldos();

        $dado = \JSON::create()->parse(str_replace('\"', '"', $dados['file']));
        $this->processarArquivo($dado->path);
    }

    /**
     * Remove os saldos salvos
     * @throws Exception
     */
    private function removerSaldos()
    {
        $exercicio = date('Y');
        if ($this->exercicio < $exercicio) {
            $this->removerSaldoCompetencia($exercicio);
        }
        $this->removerSaldoCompetencia($this->exercicio, $this->mes);
    }

    /**
     * @param integer $exercicio
     * @param integer|null $mes
     * @throws Exception
     */
    private function removerSaldoCompetencia($exercicio, $mes = null)
    {
        $where = [
            "c125_tiposaldo = 1",
            "c125_conplanosistema = 1",
            "c125_anousu = {$exercicio}"
        ];
        if (!is_null($mes)) {
            $where[] = "c125_mesusu >= {$mes}";
        }

        $sql = "delete from contabilidade.conplanoatributosaldo where " . implode(' and ', $where);
        $rs = db_query($sql);
        if (!$rs) {
            throw new Exception("Erro ao remover saldos.");
        }
    }

    /**
     * Lê o arquivo salvando o ending_balance
     * @param $filepath
     * @throws Exception
     */
    private function processarArquivo($filepath)
    {
        $csv = new LerCsv($filepath);
        $csv->setCsvControl(';');
        $linha = $csv->read();

        foreach ($linha as $key => $dadosLinha) {
            if ($key <= self::LINHA_HEADER) {
                continue;
            }

            if ($dadosLinha[$this->mapaColunas['tipo_valor']] !== self::LINHA_IMPORTAR) {
                continue;
            }

            $this->salvaDadosLinha($dadosLinha);
        }
    }

    /**
     *
     */
    private function mapeiaPosicaoColunas()
    {
        if (!array_key_exists($this->exercicio, $this->quantidadeColunas)) {
            throw new Exception("Não foi mapeado a quantidade de colunas da MSC no exercício {$this->exercicio}");
        }

        $quantidade = $this->quantidadeColunas[$this->exercicio]['quantidade_tipos'];
        $totalTipos = $quantidade * 2;

        $this->mapaColunas['valor'] = $totalTipos + 1;
        $this->mapaColunas['tipo_valor'] = $totalTipos + 2;
        $this->mapaColunas['natureza_valor'] = $totalTipos + 3;
    }

    /**
     * @param $dadosLinha
     * @throws Exception
     */
    private function salvaDadosLinha($dadosLinha)
    {
        $valor = $dadosLinha[$this->mapaColunas['valor']];
        if ($valor == 0) {
            return;
        }
        $hash = $this->montaHash($dadosLinha);
        $natureza = $dadosLinha[$this->mapaColunas['natureza_valor']];

        $dao = new cl_conplanoatributosaldo();
        $dao->c125_anousu = $this->exercicio;
        $dao->c125_mesusu = $this->mes;
        $dao->c125_hashcontaatributos = $hash;
        $dao->c125_valor = $valor;
        $dao->c125_natureza = $natureza;
        $dao->c125_tipo = 3;
        $dao->c125_conplanosistema = 1;
        $dao->c125_instit = null;
        $dao->c125_tiposaldo = 1;
        $dao->incluir(null);

        if ($dao->erro_status == 0) {
            throw new Exception($dao->erro_msg, 403);
        }
    }

    private function montaHash($dadosLinha)
    {
        $dados = [$dadosLinha[$this->mapaColunas['conta']]];

        $posicaoValor = 1;
        $posicaoTipo = 2;

        $quantidade = $this->quantidadeColunas[$this->exercicio]['quantidade_tipos'];
        while ($quantidade > 0) {
            $valor = $dadosLinha[$posicaoValor];
            $tipo = $dadosLinha[$posicaoTipo];
            if (!empty($valor) && !empty($tipo)) {
                $dados[] = "{$valor}#{$tipo}";
            }
            $posicaoValor += 2;
            $posicaoTipo += 2;
            $quantidade --;
        }

        return implode('|', $dados);
    }
}
