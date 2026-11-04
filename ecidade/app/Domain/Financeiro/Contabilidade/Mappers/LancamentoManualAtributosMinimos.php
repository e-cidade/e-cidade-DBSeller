<?php

namespace App\Domain\Financeiro\Contabilidade\Mappers;

use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalPcaspPadrao;

class LancamentoManualAtributosMinimos
{
    protected $tipos = [
        'RECEITA',
        'DOTACAO',
        'EMPENHO',
        'CGM'
    ];


    protected static $mapa = [
        1 => [
            '112' => 'CGM',
            '113' => 'CGM',
            '119' => 'CGM',
            '121' => 'CGM',
            '122' => 'CGM',
        ],
        2 => [
            '211' => 'CGM',
            '212' => 'CGM',
            '213' => 'CGM',
            '217' => 'CGM',
            '218' => 'CGM',
            '22' => 'CGM',
        ],
        5 => [
            '521' => 'RECEITA',
            '522' => 'DOTACAO',
            '53' => 'EMPENHO',
        ],
        6 => [
            '62213' => 'EMPENHO',
            '6221' => 'DOTACAO',
            '621' => 'RECEITA',
            '63' => 'EMPENHO',
        ],
        7 => [
            '711' => 'CGM',
            '712' => 'CGM',
            '73' => 'CGM',
            '74' => 'CGM',
            '75' => 'CGM',
        ],
        8 => [
            '81' => 'CGM',
            '83' => 'CGM',
            '84' => 'CGM',
            '85' => 'CGM'
        ]
    ];

    /*
        protected $all = [
            '111000000' => [Recurso],
            '112000000' => [Recurso, 'CGM'],
            '113000000' => [Recurso, 'CGM'],
            '115000000' => [Recurso],
            '119000000' => [Recurso, 'CGM'],
            '121000000' => [Recurso, 'CGM'],
            '122000000' => [Recurso, 'CGM'],
            '123000000' => [Recurso],
            '211000000' => [Recurso, 'CGM'],
            '212000000' => [Recurso, 'CGM'],
            '213000000' => [Recurso, 'CGM'],
            '217000000' => [Recurso, 'CGM'],
            '218000000' => [Recurso, 'CGM'],
            '220000000' => [Recurso, 'CGM'],
            '230000000' => [Recurso],
            '300000000' => [Recurso],
            '400000000' => [Recurso],
            '521000000' => ['RECEITA'],
            '522000000' => ['DOTACAO'],
            '530000000' => ['EMPENHO'],
            '621000000' => ['RECEITA'],
            '622100000' => ['DOTACAO'],
            '622130000' => ['EMPENHO'],
            '630000000' => ['EMPENHO'],
            '711000000' => [Recurso, 'CGM'],
            '712000000' => [Recurso, 'CGM'],
            '720000000' => [Recurso],
            '730000000' => [Recurso, 'CGM'],
            '740000000' => [Recurso, 'CGM'],
            '750000000' => [Recurso, 'CGM'],
            '790000000' => [Recurso],
            '810000000' => [Recurso, 'CGM'],
            '820000000' => [Recurso],
            '830000000' => [Recurso, 'CGM'],
            '840000000' => [Recurso, 'CGM'],
            '850000000' => [Recurso, 'CGM'],
            '890000000' => [Recurso],
        ];
    */


    /**
     * Retorna os vínculos minimos que cada estrutural deve possuir para um lançamento contábil.
     * @param $estrutural
     * @return array|string[]
     */
    public static function atributo($estrutural)
    {
        $atributo = self::buscaAtributo($estrutural);

        if (is_null($atributo)) {
            return ['RECURSO'];
        }
        if ($atributo === 'CGM') {
            return ['CGM', 'RECURSO'];
        }

        return [$atributo];
    }

    private static function identificaAtributoEstrutural($estrutural, $mapaEstruturais)
    {
        foreach ($mapaEstruturais as $estruturalMapeado => $atributo) {
            $parteStrutural = substr($estrutural, 0, strlen($estruturalMapeado));

            if ((int)$parteStrutural === (int)$estruturalMapeado) {
                return $atributo;
            }
        }
        return null;
    }

    /**
     * @param $estrutural
     * @return mixed|void|null
     */
    public static function buscaAtributo($estrutural)
    {
        foreach (self::$mapa as $classe => $mapaEstruturais) {
            if ((int)substr($estrutural, 0, 1) !== $classe) {
                continue;
            }

            return self::identificaAtributoEstrutural($estrutural, $mapaEstruturais);
        }
    }
}
