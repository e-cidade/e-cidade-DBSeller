<?php

use ECidade\Tributario\Issqn\Simples\Entity\ISSArquivoSimplesRegistro;
use Tests\TestCase;

class ISSArquivoSimplesRegistroTest extends TestCase
{

    public function test_deve_lancar_exception_quando_houver_json_invalido_no_cnpj_vinculados()
    {
        $this->expectException(BusinessException::class);
        $this->expectExceptionMessage('Erro ao configurar cnpj vinculados [campo invalido]');

        $dados = new stdClass;
        $dados->cnpj_vinculados = 'jsoninvalido';
        new ISSArquivoSimplesRegistro($dados);
    }

    public function test_deve_retornar_array_vazio_quando_cnpj_vinculados_for_nulo()
    {
        $dados = new stdClass;
        $dados->cnpj_vinculados = null;
        $issArquivoSimplesRegistro = new ISSArquivoSimplesRegistro($dados);

        self::assertSame([], $issArquivoSimplesRegistro->cnpj_vinculados);
    }

    public function test_deve_setar_cnpj_do_arquivo_para_matriz_quando_cnpj_vinculados_for_nulo()
    {
        $dados = new stdClass;
        $dados->q23_cnpj = '12345678000139';
        $dados->cnpj_vinculados = null;
        $issArquivoSimplesRegistro = new ISSArquivoSimplesRegistro($dados);

        self::assertEquals('12345678000139', $issArquivoSimplesRegistro->cnpj_matriz);
    }

    public function test_deve_setar_filiais_e_matriz()
    {
        $dados = new stdClass;
        $dados->cnpj_vinculados = '[
            {"cnpj": "12345678000120", "cgm": 123, "inscr": null, "vinc": null},
            {"cnpj": "12345678000219", "cgm": 222, "inscr": null, "vinc": null},
            {"cnpj": "12345678000321", "cgm": 111, "inscr": 444, "vinc": null}
        ]';

        $issArquivoSimplesRegistro = new ISSArquivoSimplesRegistro($dados);
        $filiais = $issArquivoSimplesRegistro->getFiliais();

        self::assertCount(2, $filiais);
        self::assertEquals('12345678000321', $filiais[1]->cnpj);
        self::assertEquals('111', $filiais[1]->cgm);
        self::assertEquals('444', $filiais[1]->inscr);

        self::assertEquals('12345678000120', $issArquivoSimplesRegistro->cnpj_matriz);
        self::assertEquals('123', $issArquivoSimplesRegistro->cgm_matriz);
        self::assertEquals(null, $issArquivoSimplesRegistro->inscr_matriz);
    }

    public function test_deve_setar_pagamento_para_matriz_quando_nao_houver_filial()
    {
        $dados = new stdClass;
        $dados->cnpj_vinculados = '[{"cnpj": 12345678000139, "cgm": 123, "inscr": 321, "vinc": 1}]';
        $issArquivoSimplesRegistro = new ISSArquivoSimplesRegistro($dados);
        $dadosParaPagamento = $issArquivoSimplesRegistro->getDadosParaPagamento();

        self::assertTrue($dadosParaPagamento->matriz);
    }

    public function test_deve_sempre_setar_pagamento_para_matriz_quando_matriz_possui_inscricao()
    {
        $dados = new stdClass;
        $dados->cnpj_vinculados = '[
            {"cnpj": 12345678000139, "cgm": 123, "inscr": 321, "vinc": 2},
            {"cnpj": 12345678000239, "cgm": 222, "inscr": 321, "vinc": null}
        ]';

        $issArquivoSimplesRegistro = new ISSArquivoSimplesRegistro($dados);
        $dadosParaPagamento = $issArquivoSimplesRegistro->getDadosParaPagamento();

        self::assertTrue($dadosParaPagamento->matriz);
        self::assertEquals('12345678000139', $dadosParaPagamento->cnpj);
    }

    public function test_deve_setar_pagamento_para_filial_quando_matriz_nao_possui_inscricao()
    {
        $dados = new stdClass;
        $dados->cnpj_vinculados = '[
            {"cnpj": "12345678000120", "cgm": 123, "inscr": null, "vinc": null},
            {"cnpj": "12345678000219", "cgm": 222, "inscr": null, "vinc": null},
            {"cnpj": "12345678000321", "cgm": 111, "inscr": 444, "vinc": null}
        ]';

        $issArquivoSimplesRegistro = new ISSArquivoSimplesRegistro($dados);
        $dadosParaPagamento = $issArquivoSimplesRegistro->getDadosParaPagamento();

        self::assertFalse($dadosParaPagamento->matriz);
        self::assertEquals('12345678000321', $dadosParaPagamento->cnpj);
    }

    public function test_deve_setar_pagamento_para_primeira_filial_quando_houver_mais_de_uma_inscricao()
    {
        $dados = new stdClass;
        $dados->cnpj_vinculados = '[
            {"cnpj": "12345678000120", "cgm": 123, "inscr": null, "vinc": null},
            {"cnpj": "12345678000219", "cgm": 222, "inscr": 222, "vinc": 1},
            {"cnpj": "12345678000321", "cgm": 111, "inscr": 444, "vinc": null}
        ]';

        $issArquivoSimplesRegistro = new ISSArquivoSimplesRegistro($dados);
        $dadosParaPagamento = $issArquivoSimplesRegistro->getDadosParaPagamento();

        self::assertFalse($dadosParaPagamento->matriz);
        self::assertEquals('12345678000219', $dadosParaPagamento->cnpj);
    }

    public function test_deve_setar_cancelamento_para_demais_inscricoes_quando_filial()
    {
        $dados = new stdClass;
        $dados->cnpj_vinculados = '[
            {"cnpj": "12345678000120", "cgm": 123, "inscr": null, "vinc": null},
            {"cnpj": "12345678000219", "cgm": 222, "inscr": 222, "vinc": null},
            {"cnpj": "12345678000530", "cgm": 555, "inscr": 333, "vinc": null},
            {"cnpj": "12345678000321", "cgm": 111, "inscr": 444, "vinc": null}
        ]';

        $issArquivoSimplesRegistro = new ISSArquivoSimplesRegistro($dados);
        $dadosParaPagamento = $issArquivoSimplesRegistro->getDadosParaPagamento();
        $inscricoesParaCancelamento = $issArquivoSimplesRegistro->getInscricoesParaCancelamento();

        self::assertFalse($dadosParaPagamento->matriz);
        self::assertEquals('12345678000219', $dadosParaPagamento->cnpj);
        self::assertCount(2, $inscricoesParaCancelamento);
        self::assertEquals(333, $inscricoesParaCancelamento[0]->inscr);
        self::assertEquals(444, $inscricoesParaCancelamento[1]->inscr);
    }

    public function test_deve_setar_cancelamento_para_demais_inscricoes_quando_matriz()
    {
        $dados = new stdClass;
        $dados->cnpj_vinculados = '[
            {"cnpj": "12345678000120", "cgm": 123, "inscr": 123, "vinc": 1},
            {"cnpj": "12345678000219", "cgm": 222, "inscr": 222, "vinc": null},
            {"cnpj": "12345678000530", "cgm": 555, "inscr": 333, "vinc": null},
            {"cnpj": "12345678000321", "cgm": 111, "inscr": 444, "vinc": null}
        ]';

        $issArquivoSimplesRegistro = new ISSArquivoSimplesRegistro($dados);
        $dadosParaPagamento = $issArquivoSimplesRegistro->getDadosParaPagamento();
        $inscricoesParaCancelamento = $issArquivoSimplesRegistro->getInscricoesParaCancelamento();

        self::assertTrue($dadosParaPagamento->matriz);
        self::assertEquals('12345678000120', $dadosParaPagamento->cnpj);
        self::assertCount(3, $inscricoesParaCancelamento);
        self::assertEquals(222, $inscricoesParaCancelamento[0]->inscr);
        self::assertEquals(333, $inscricoesParaCancelamento[1]->inscr);
        self::assertEquals(444, $inscricoesParaCancelamento[2]->inscr);
    }


    public function test_deve_remover_inscr_sem_vinculo_quando_houver_apenas_uma_com_vinculo()
    {
        $dados = new stdClass;
        $dados->cnpj_vinculados = '[
            {"cnpj": "12345678000120", "cgm": 123, "inscr": 123, "vinc": 1},
            {"cnpj": "12345678000120", "cgm": 222, "inscr": 222, "vinc": null},
            {"cnpj": "12345678000120", "cgm": 111, "inscr": 444, "vinc": null},
            {"cnpj": "12345678000220", "cgm": 333, "inscr": 333, "vinc": 2},
            {"cnpj": "12345678000220", "cgm": 555, "inscr": 555, "vinc": null}
        ]';

        $issArquivoSimplesRegistro = new ISSArquivoSimplesRegistro($dados);
        $dadosParaPagamento = $issArquivoSimplesRegistro->getDadosParaPagamento();

        self::assertEquals(123, $dadosParaPagamento->inscricao);
        self::assertCount(1, $issArquivoSimplesRegistro->getInscricaoVinculos());
        self::assertEquals(123, $issArquivoSimplesRegistro->getInscricaoVinculos()[0]);
    }

    public function test_seguir_quando_possuir_zero_duas_ou_mais_inscr_com_vinculo()
    {
        $dados = new stdClass;
        $dados->cnpj_vinculados = '[
            {"cnpj": "12345678000120", "cgm": 123, "inscr": 123, "vinc": 1},
            {"cnpj": "12345678000120", "cgm": 222, "inscr": 222, "vinc": 2},
            {"cnpj": "12345678000120", "cgm": 111, "inscr": 444, "vinc": null},
            {"cnpj": "12345678000220", "cgm": 333, "inscr": 333, "vinc": 2}
        ]';

        $issArquivoSimplesRegistro = new ISSArquivoSimplesRegistro($dados);
        self::assertCount(3, $issArquivoSimplesRegistro->getInscricaoVinculos());
    }

    public function test_deve_remover_inscr_sem_vinculo_quando_houver_apenas_uma_com_vinculo_filial()
    {
        $dados = new stdClass;
        $dados->cnpj_vinculados = '[
            {"cnpj": "12345678000120", "cgm": 123, "inscr": null, "vinc": null},
            {"cnpj": "12345678000220", "cgm": 333, "inscr": 333, "vinc": 2},
            {"cnpj": "12345678000220", "cgm": 555, "inscr": 555, "vinc": null}
        ]';

        $issArquivoSimplesRegistro = new ISSArquivoSimplesRegistro($dados);
        $dadosParaPagamento = $issArquivoSimplesRegistro->getDadosParaPagamento();

        self::assertEquals(333, $dadosParaPagamento->inscricao);
        self::assertCount(1, $issArquivoSimplesRegistro->getInscricaoVinculos());
        self::assertEquals(333, $issArquivoSimplesRegistro->getInscricaoVinculos()[0]);
    }
}
