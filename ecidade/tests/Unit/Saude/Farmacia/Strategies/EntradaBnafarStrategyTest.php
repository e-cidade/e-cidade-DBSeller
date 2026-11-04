<?php

namespace Tests\Unit\Saude\Farmacia\Strategies;

use App\Domain\Saude\Farmacia\Strategies\EntradaBnafarStrategy;
use App\Domain\Saude\Farmacia\Validators\EntradaBnafarValidator;

class EntradaBnafarStrategyTest extends ProcedimentoBnafarStrategyTest
{
    /**
     * @inheritDoc
     */
    protected function setUpConcreteStrategy($unidade, $repository)
    {
        $validator = new EntradaBnafarValidator();
        return new EntradaBnafarStrategy($unidade, $repository, $validator);
    }

    /**
     * @inheritDoc
     */
    protected function buildData($testValidator = false)
    {
        return collect([
            (object)[
                'm82_codigo' => 1,
                'id_produto' => 1,
                'numero_produto' => $testValidator ? '' : 'ANCBSHUS1215',
                'descricao_produto' => 'MEDICAMENTO TEST',
                'quantidade' => 50,
                'data_validade' => '2023-01-01',
                'lote' => $testValidator ? '' : 'ACS1548',
                'valor_unitario' => 25.94,
                'codigo_origem' => 12,
                'codigo_origem_item' => 254,
                'movimentacao' => 1,
                'cnpj_fabricante' => $testValidator ? '' : '29453641000183',
                'id_fabricante' => 1,
                'nome_fabricante' => $testValidator ? '' : 'Nome do Fabricante',
                'numero_documento' => $testValidator ? '' : '2545415151678',
                'data_documento' => '2022-08-18',
                'id_estabelecimento' => 124,
                'cnpj_estabelecimento' => $testValidator ? '' : '48521116000100',
                'nome_estabelecimento' => 'Nome do Distribuidor',
                'cnes_estabelecimento' => $testValidator ? '' : '25454151',
                'm80_obs' => 'teste',
                'fa01_i_codmater' => 1,
                'm60_descr' => 'MEDICAMENTO TEST',
                'tipo' => 'ENTRADA',
                'tipo_produto' => 'B',
                'data_entrada' => '2022-08-18',
                'fa01_i_codigo' => 1
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function buildExpected()
    {
        $expectedEstabelecimento = (object)[
            'cnes' => 12345,
            'tipo' => 'F',
        ];
        $expectedCaracterizacao = (object)[
            'cnesCnpjDistribuidor' => '25454151',
            'codigoOrigem' => 12,
            'dataEntrada' => '2022-08-18',
            'numeroDocumento' => '2545415151678',
            'tipoEntrada' => 'E-D'
        ];
        $expectedItens = [
            (object)[
                'cnpjFabricante' => '29453641000183',
                'dataValidade' => '2023-01-01',
                'lote' => 'ACS1548',
                'nomeFabricanteInternacional' => '',
                'numero' => 'ANCBSHUS1215',
                'tipoProduto' => 'B',
                'codigoOrigem' => 254,
                'siglaProgramaSaude' => '',
                'quantidade' => 50,
                'valorUnitario' => 25.94
            ]
        ];
        return [
            (object)[
                'codigo' => null,
                'estabelecimento' => $expectedEstabelecimento,
                'caracterizacao' => $expectedCaracterizacao,
                'itens' => $expectedItens
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    protected function buildExpectedErrors()
    {
        return [
            'id_estabelecimento' => utf8_decode(
                'É obrigatório informar o campo CNPJ ou CNES do estabelecimento origem.'
            ),
            'numero_documento' => utf8_decode('O campo nota fiscal é obrigatório.'),
            'numero_produto' => utf8_decode('O campo identificador do produto(CATMAT) é obrigatório.'),
            'lote' => utf8_decode('O campo lote é obrigatório.'),
            'id_fabricante' => utf8_decode('É obrigatório informar o CNPJ do fabricante ou fabricante internacional.')
        ];
    }
}
