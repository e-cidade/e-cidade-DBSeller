<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoUm;

class AnexoUm2023Service extends AnexoUmService
{
    protected $sections = [
        'receita_1' => [1, 78],
        'receita_2' => [104, 166],
        'despesa_1' => [79, 103],
        'despesa_2' => [167, 176],
    ];

    /**
     * Como o relatório usa as linhas do quadro de baixo nos quadros de cima, inverti o cálculo dos totalizadores
     * Na configuração que fiz abaixo, totalizador está somando dados de outros totalizadores.
     * Fiz isso pq as somas são em cima de muitas linhas.
     * @var array
     */
    protected $totalizarSoma = [
        // despesas 2 (intra)
        168 => [169, 170, 171],
        172 => [173, 174, 175],
        167 => [169, 170, 171, 173, 174, 175, 176],

        // receitas 2 (intra orçamentárias)
        106 => [107, 108, 109],
        110 => [111, 112, 113, 114],

        115 => [116, 117, 118, 119, 120, 121, 122],
        125 => [126, 127, 128, 129, 130],
        131 => [132, 133, 134, 135, 136, 137, 138],
        139 => [140, 141, 142, 143, 144],
        105 => [107, 108, 109, 110, 112, 113, 114, 116, 117, 118, 119, 120, 121, 122, 123, 124,
            126, 127, 128, 129, 130, 131, 133, 134, 135, 136, 137, 138, 140, 141, 142, 143, 144
        ],
        146 => [147, 148],
        149 => [150, 151, 152],
        154 => [155, 156, 157, 158, 159, 160, 161],
        162 => [163, 164, 165, 166],
        145 => [147, 148, 150, 151, 152, 153, 154, 156, 157, 158, 159, 160, 161, 163, 164, 165, 166],

        104 => [
            107, 108, 109, 111, 112, 113, 114, 116, 117, 118, 119, 120, 121, 122, 123, 124, 126, 127, 128, 129, 130,
            131, 133, 134, 135, 136, 137, 138, 140, 141, 142, 143, 144, 147, 148, 150, 151, 152, 153,
            155, 156, 157, 158, 159, 160, 161, 163, 165, 166
        ],


        // receitas 1 NÃO (intra orçamentárias)
        3 => [4, 5, 6],
        7 => [8, 9, 10, 11],
        12 => [13, 14, 15, 16, 17, 18, 19],
        22 => [23, 24, 25, 26, 27],
        28 => [29, 30, 31, 32, 33, 34, 35],
        36 => [37, 38, 39, 40, 41],
        2 => [4, 5, 6, 8, 9, 10, 11, 13, 14, 15, 16, 17, 18, 19, 20, 21, 23, 24, 25, 26, 27, 29, 30, 31, 32, 33, 34,
            35, 37, 38, 39, 40, 41,
        ],
        43 => [44, 45],
        46 => [47, 48, 49],
        51 => [52, 53, 54, 55, 56, 57, 58],
        59 => [60, 61, 62, 63],
        42 => [44, 45, 47, 48, 49, 50, 52, 53, 54, 55, 56, 57, 58, 60, 61, 62, 63],
        1 => [4, 5, 6, 8, 9, 10, 11, 13, 14, 15, 16, 17, 18, 19, 20, 21, 23, 24, 25, 26, 27, 29, 30, 31, 32, 33, 34,
            35, 37, 38, 39, 40, 41, 44, 45, 47, 48, 49, 50, 52, 53, 54, 55, 56, 57, 58, 60, 61, 62, 63
        ],

        64 => [104], // linha 65 é igual a linha 105 RECEITAS (INTRA-ORÇAMENTÁRIAS) (II)
        65 => [1, 64],
        67 => [68, 69],
        70 => [71, 72],
        66 => [68, 69, 71, 72],

        73 => [65, 66], // (V) = (III + IV)
        74 => [], // (VI)¹
        75 => [73, 74], // (VII) = (V + VI)
        76 => [77, 78],


        //despesa 1
        83 => [84, 85],
        86 => [87, 88, 89],
        80 => [81, 82, 84, 85],
        79 => [81, 82, 84, 85, 87, 88, 89, 90],
        91 => [167], // linha 92 é igual a linha 169 DESPESAS (INTRA-ORÇAMENTÁRIAS) (IX)
        92 => [79, 91], //   (X) = (VIII + IX)

        94 => [95, 96],
        97 => [98, 99],
        93 => [94, 97],
        100 => [92, 93], // (XII) = (X + XI)
        101 => [101], // SUPERÁVIT (XIII)
        102 => [100, 101], // TOTAL (XIV) = (XII + XIII)
    ];

    protected $linhasReceita = [[1, 78], [104, 166]];
    protected $linhasDespesa = [[79, 103], [167, 176]];

    protected $ordemLinhaTotalReceitas = 73;
    protected $ordemLinhaDefict = 74;
    protected $ordemLinhaSaldosExercíciosAnteriores = 78;
    protected $ordemLinhaTotalDespesas = 100;
    protected $ordemLinhaSuperavit = 101;
    protected $ordemLinhaTotalSuperavit = 102;

    /**
     * @return void
     */
    protected function linha79()
    {
        $this->linhas[78]->previsao_atualizada = $this->linhas[78]->arrecadado_acumulado;
    }

    protected function limparColunas()
    {
        $this->linhas[75]->saldo = ' - ';

        $this->linhas[76]->arrecadado_periodo = ' - ';
        $this->linhas[76]->percentual_no_bimestre = ' - ';
        $this->linhas[76]->percentual_acumulado = ' - ';
        $this->linhas[76]->saldo = ' - ';

        $this->linhas[77]->arrecadado_periodo = ' - ';
        $this->linhas[77]->percentual_no_bimestre = ' - ';
        $this->linhas[77]->arrecadado_acumulado = ' - ';
        $this->linhas[77]->percentual_acumulado = ' - ';
        $this->linhas[77]->saldo = ' - ';

        $this->linhas[78]->valor_inicial = ' - ';
        $this->linhas[78]->arrecadado_periodo = ' - ';
        $this->linhas[78]->percentual_no_bimestre = ' - ';
        $this->linhas[78]->percentual_acumulado = ' - ';
        $this->linhas[78]->saldo = ' - ';

        // reserva de contingência
        $this->linhas[90]->empenhado_liquido = '-';
        $this->linhas[90]->empenhado_liquido_acumulado = '-';
        $this->linhas[90]->liquidado = '-';
        $this->linhas[90]->liquidado_acumulado = '-';
        $this->linhas[90]->pago_acumulado = '-';

        // despesa
        $this->linhas[102]->saldo_empenhado = '-';
        $this->linhas[102]->saldo_liquidado = '-';

        $this->linhas[103]->empenhado_liquido = '-';
        $this->linhas[103]->empenhado_liquido_acumulado = '-';
        $this->linhas[103]->liquidado = '-';
        $this->linhas[103]->liquidado_acumulado = '-';
        $this->linhas[103]->pago_acumulado = '-';
        $this->linhas[103]->a_liquidar = '-';
    }
}
