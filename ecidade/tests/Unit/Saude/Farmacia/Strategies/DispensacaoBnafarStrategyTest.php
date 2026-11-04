<?php

namespace Tests\Unit\Saude\Farmacia\Strategies;

use App\Domain\Saude\Farmacia\Strategies\DispensacaoBnafarStrategy;
use App\Domain\Saude\Farmacia\Validators\DispensacaoBnafarValidator;

class DispensacaoBnafarStrategyTest extends ProcedimentoBnafarStrategyTest
{
    /**
     * @inheritDoc
     */
    protected function setUpConcreteStrategy($unidade, $repository)
    {
        $validator = new DispensacaoBnafarValidator();
        return new DispensacaoBnafarStrategy($unidade, $repository, $validator);
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
                'movimentacao' => '',
                'cnpj_fabricante' => $testValidator ? '' : '29453641000183',
                'id_fabricante' => 1,
                'nome_fabricante' => $testValidator ? '' : 'Nome do Fabricante',
                'numero_documento' => $testValidator ? '' : '2545415151678',
                'data_documento' => '2022-08-18',
                'cgm_distribuidor' => 124,
                'cnpj_distribuidor' => $testValidator ? '' : '48521116000100',
                'nome_distribuidor' => 'Nome do Distribuidor',
                'm80_obs' => 'teste',
                'fa01_i_codmater' => 1,
                'm60_descr' => 'MEDICAMENTO TEST',
                'tipo' => 'DISPENSAÇÃO',
                'm70_coddepto' => 1,
                'tipo_produto' => 'B',
                'data_dispensacao' => '2022-09-18',
                'id_paciente' => 1,
                'nome_paciente' => 'Nome do Paciente',
                'cns_paciente' => $testValidator ? '' : '784888852000001',
                'cpf_paciente' => $testValidator ? '' : '11791060080',
                'fa01_i_codigo' => 1
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function buildExpected()
    {
        $expectedUsuarioSus = (object)['cpf' => '11791060080'];
        $expectedEstabelecimento = (object)['cnes' => 12345];
        $expectedCaracterizacao = (object)[
            'codigoOrigem' => 12,
            'dataDispensacao' => '2022-09-18',
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
                'quantidade' => 50
            ]
        ];
        return [
            (object)[
                'codigo' => null,
                'usuarioSus' => $expectedUsuarioSus,
                'estabelecimentoDispensador' => $expectedEstabelecimento,
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
            'cns_paciente' => utf8_decode('Usuário Nome do Paciente com CNS inválido.'),
            'cpf_paciente' => utf8_decode('Usuário Nome do Paciente com CPF inválido.'),
            'numero_produto' => utf8_decode('O campo identificador do produto(CATMAT) é obrigatório.'),
            'lote' => utf8_decode('O campo lote é obrigatório.'),
            'id_fabricante' => utf8_decode('É obrigatório informar o CNPJ do fabricante ou fabricante internacional.')
        ];
    }
}
