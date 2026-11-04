<?php

namespace App\Domain\Patrimonial\PNCP\Enum;

use ECidade\Enum\Enum;

class ModoDisputaEnum extends Enum
{
    const ABERTO = 1;
    const FECHADO = 2;
    const ABERTO_FECHADO = 3;
    const DISPENSA_COM_DISPUTA = 4;
    const NAO_SE_APLICA = 5;
    const FECHADO_ABERTO = 6;
    protected static $dePara = [
        InstrumentoConvocatorioEnum::EDITAL => [
            self::ABERTO,
            self::FECHADO,
            self::ABERTO_FECHADO,
            self::FECHADO_ABERTO
        ],
        InstrumentoConvocatorioEnum::AVISO_DE_CONTRATACOES_DIRETA => [
            self::DISPENSA_COM_DISPUTA,
        ],
        InstrumentoConvocatorioEnum::ATO_QUE_AUTORIZA_CONTRATACAO_DIRETA => [
            self::NAO_SE_APLICA,
        ],
        InstrumentoConvocatorioEnum::EDITAL_DE_CHAMAMENTO_PUBLICO => [
            self::NAO_SE_APLICA
        ]

    ];
    public function name()
    {
        $data = [
          self::ABERTO => 'Aberto',
          self::FECHADO => 'Fechado',
          self::ABERTO_FECHADO => 'Aberto-Fechado',
          self::DISPENSA_COM_DISPUTA => 'Dispensa com disputa',
          self::NAO_SE_APLICA => 'Não se aplica',
          self::FECHADO_ABERTO => 'Fechado-Aberto',
        ];

        if (empty($data[$this->getValue()])) {
            throw new \Exception('Opção inválida.');
        }

        return $data[$this->getValue()];
    }

    public static function getModoDisputa($instrumentoConvocatorio)
    {
        $modosDisputa = self::$dePara[$instrumentoConvocatorio];
        $disputas = [];
        if (count($modosDisputa) > 1) {
            $disputas[] = (object)[
                'codigo' => 0,
                'descricao' => 'Selecione'
            ];
        }
        foreach ($modosDisputa as $modoDisputa) {
            $modoDisputaEnum = new self($modoDisputa);
            $disputas[] = (object)[
                'codigo' => $modoDisputaEnum->value,
                'descricao' => $modoDisputaEnum->name(),
            ];
        }

        return $disputas;
    }
}
