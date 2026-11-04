<?php

namespace App\Domain\Patrimonial\PNCP\Enum;

use ECidade\Enum\Enum;

class InstrumentoConvocatorioEnum extends Enum
{
    const EDITAL = 1;
    const AVISO_DE_CONTRATACOES_DIRETA = 2;
    const ATO_QUE_AUTORIZA_CONTRATACAO_DIRETA = 3;
    const EDITAL_DE_CHAMAMENTO_PUBLICO = 4;
    protected static $dePara = [
        ModalidadeCompraEnum::LEILAO_ELETRONICO => [
            InstrumentoConvocatorioEnum::EDITAL,
            InstrumentoConvocatorioEnum::AVISO_DE_CONTRATACOES_DIRETA,
            InstrumentoConvocatorioEnum::ATO_QUE_AUTORIZA_CONTRATACAO_DIRETA
        ],
        ModalidadeCompraEnum::DIALOGO_COMPETITIVO => [
            InstrumentoConvocatorioEnum::EDITAL,
            InstrumentoConvocatorioEnum::AVISO_DE_CONTRATACOES_DIRETA,
            InstrumentoConvocatorioEnum::ATO_QUE_AUTORIZA_CONTRATACAO_DIRETA
        ],
        ModalidadeCompraEnum::CONCURSO => [
            InstrumentoConvocatorioEnum::EDITAL,
            InstrumentoConvocatorioEnum::AVISO_DE_CONTRATACOES_DIRETA,
            InstrumentoConvocatorioEnum::ATO_QUE_AUTORIZA_CONTRATACAO_DIRETA
        ],
        ModalidadeCompraEnum::CONCORRENCIA_ELETRONICA => [
            InstrumentoConvocatorioEnum::EDITAL,
            InstrumentoConvocatorioEnum::AVISO_DE_CONTRATACOES_DIRETA,
            InstrumentoConvocatorioEnum::ATO_QUE_AUTORIZA_CONTRATACAO_DIRETA
        ],
        ModalidadeCompraEnum::CONCORRENCIA_PRESENCIAL => [
            InstrumentoConvocatorioEnum::EDITAL,
            InstrumentoConvocatorioEnum::AVISO_DE_CONTRATACOES_DIRETA,
            InstrumentoConvocatorioEnum::ATO_QUE_AUTORIZA_CONTRATACAO_DIRETA
        ],
        ModalidadeCompraEnum::PREGAO_ELETRONICO => [
            InstrumentoConvocatorioEnum::EDITAL,
            InstrumentoConvocatorioEnum::AVISO_DE_CONTRATACOES_DIRETA,
            InstrumentoConvocatorioEnum::ATO_QUE_AUTORIZA_CONTRATACAO_DIRETA
        ],
        ModalidadeCompraEnum::PREGAO_PRESENCIAL => [
            InstrumentoConvocatorioEnum::EDITAL,
            InstrumentoConvocatorioEnum::AVISO_DE_CONTRATACOES_DIRETA,
            InstrumentoConvocatorioEnum::ATO_QUE_AUTORIZA_CONTRATACAO_DIRETA
        ],
        ModalidadeCompraEnum::DISPENSA_DE_LICITACAO => [
            InstrumentoConvocatorioEnum::AVISO_DE_CONTRATACOES_DIRETA,
            InstrumentoConvocatorioEnum::ATO_QUE_AUTORIZA_CONTRATACAO_DIRETA,
        ],
        ModalidadeCompraEnum::INEXIGIBILIDADE => [
            InstrumentoConvocatorioEnum::ATO_QUE_AUTORIZA_CONTRATACAO_DIRETA
        ],
        ModalidadeCompraEnum::MANIFESTACAO_DE_INTERESSE => [
            InstrumentoConvocatorioEnum::AVISO_DE_CONTRATACOES_DIRETA,
            InstrumentoConvocatorioEnum::ATO_QUE_AUTORIZA_CONTRATACAO_DIRETA,
            InstrumentoConvocatorioEnum::EDITAL_DE_CHAMAMENTO_PUBLICO
        ],
        ModalidadeCompraEnum::PRE_QUALIFICACAO => [
            InstrumentoConvocatorioEnum::AVISO_DE_CONTRATACOES_DIRETA,
            InstrumentoConvocatorioEnum::ATO_QUE_AUTORIZA_CONTRATACAO_DIRETA,
            InstrumentoConvocatorioEnum::EDITAL_DE_CHAMAMENTO_PUBLICO,
        ],
        ModalidadeCompraEnum::CREDENCIAMENTO => [
            InstrumentoConvocatorioEnum::AVISO_DE_CONTRATACOES_DIRETA,
            InstrumentoConvocatorioEnum::ATO_QUE_AUTORIZA_CONTRATACAO_DIRETA,
            InstrumentoConvocatorioEnum::EDITAL_DE_CHAMAMENTO_PUBLICO
        ],
        ModalidadeCompraEnum::LEILAO_PRESENCIAL => [
            InstrumentoConvocatorioEnum::EDITAL,
            InstrumentoConvocatorioEnum::AVISO_DE_CONTRATACOES_DIRETA,
            InstrumentoConvocatorioEnum::ATO_QUE_AUTORIZA_CONTRATACAO_DIRETA,
        ],
        ModalidadeCompraEnum::INAPLICABILIDADE_DA_LICITACAO => [
            InstrumentoConvocatorioEnum::AVISO_DE_CONTRATACOES_DIRETA,
            InstrumentoConvocatorioEnum::ATO_QUE_AUTORIZA_CONTRATACAO_DIRETA,
        ]
    ];

    public function name()
    {
        $data = [
          self::EDITAL => 'Edital',
          self::AVISO_DE_CONTRATACOES_DIRETA => 'Aviso de Contratação Direta',
          self::ATO_QUE_AUTORIZA_CONTRATACAO_DIRETA => 'Ato que Autoriza a Contratação Direta',
          self::EDITAL_DE_CHAMAMENTO_PUBLICO => 'Edital de Chamamento Público'
        ];

        if (empty($data[$this->getValue()])) {
            throw new \Exception('Opção inválida.');
        }

        return $data[$this->getValue()];
    }

    public static function getInstrumentoConvocatorio($modalidadeCompra)
    {
        $instrumentoConvocatorio = [];
        $instrumentoConvocatorio[] = (object)[
            'codigo' => 0,
            'descricao' => 'Selecione'
        ];

        if (empty($modalidadeCompra)) {
            return $instrumentoConvocatorio;
        }

        $instrumentos = self::$dePara[$modalidadeCompra];
        foreach ($instrumentos as $instrumento) {
            $instrumentoConvocatorioEnum = new self($instrumento);
            $instrumentoConvocatorio[] = (object)[
                'codigo' => $instrumentoConvocatorioEnum->value,
                'descricao' => $instrumentoConvocatorioEnum->name(),
            ];
        }
        return $instrumentoConvocatorio;
    }
}
