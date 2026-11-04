<?php

namespace App\Domain\Patrimonial\PNCP\Enum;

use ECidade\Enum\Enum;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;

class ElementosDespesaEnum extends Enum
{
    const MATERIAL_CONSUMO = 30;
    const MATERIAL_BEM_SERVICO_DISTRIBUICAO_GRATUITA = 32;
    const SERVICOS_CONSULTORIA = 35;
    const OUTROS_SERVICOS_TERCEIROS_PESSOA_FISICA = 36;
    const LOCACAO_DE_MAO_DE_OBRA = 37;
    const ARRECADAMENTO_MERCANTIL = 38;
    const OUTROS_SERVICOS_DE_TERCEIROS_PESSOA_FISICA = 39;
    const SERVICOS_TECNOLOGIA_INFORMACAO_COMUNICACAO_PESSOA_JURIDICA = 40;
    const OBRAS_INSTALACOES = 51;
    protected static $dePara = [
        CategoriaItemPlanoContratacaoEnum::MATERIAL => [
            self::MATERIAL_CONSUMO,
            self::MATERIAL_BEM_SERVICO_DISTRIBUICAO_GRATUITA
        ],
        CategoriaItemPlanoContratacaoEnum::SERVICO => [
            self::SERVICOS_CONSULTORIA,
            self::OUTROS_SERVICOS_TERCEIROS_PESSOA_FISICA,
            self::LOCACAO_DE_MAO_DE_OBRA,
            self::OUTROS_SERVICOS_DE_TERCEIROS_PESSOA_FISICA
        ],
        CategoriaItemPlanoContratacaoEnum::OBRAS => [
            self::OBRAS_INSTALACOES
        ],
        CategoriaItemPlanoContratacaoEnum::SERVICO_DE_ENGENHARIA => [
            self::OUTROS_SERVICOS_DE_TERCEIROS_PESSOA_FISICA,
            self::OUTROS_SERVICOS_TERCEIROS_PESSOA_FISICA
        ],
        CategoriaItemPlanoContratacaoEnum::SOLUCOES_DE_TIC => [
            self::SERVICOS_TECNOLOGIA_INFORMACAO_COMUNICACAO_PESSOA_JURIDICA
        ],
        CategoriaItemPlanoContratacaoEnum::LOCACAO_DE_IMOVEIS => [
            self::OUTROS_SERVICOS_DE_TERCEIROS_PESSOA_FISICA,
            self::OUTROS_SERVICOS_TERCEIROS_PESSOA_FISICA
        ],
        CategoriaItemPlanoContratacaoEnum::ALIENCAO_CONCESSAO_PERMISSAO => [
            self::ARRECADAMENTO_MERCANTIL
        ],
        CategoriaItemPlanoContratacaoEnum::OBRAS_E_SERVICOS_DE_ENGENHARIA => [
            self::OBRAS_INSTALACOES
        ]
    ];

    public function name()
    {
        $data = [
            self::MATERIAL_CONSUMO => 'Material de Consumo',
            self::MATERIAL_BEM_SERVICO_DISTRIBUICAO_GRATUITA => 'Material, Bem ou Serviço para Distribuição Gratuita',
            self::SERVICOS_CONSULTORIA => 'Serviços de Consultoria',
            self::OUTROS_SERVICOS_TERCEIROS_PESSOA_FISICA => 'Outros Serviços de Terceiros - Pessoa Física',
            self::LOCACAO_DE_MAO_DE_OBRA => 'Locação de Mão-de-Obra',
            self::ARRECADAMENTO_MERCANTIL => 'Arrendamento Mercantil',
            self::OUTROS_SERVICOS_DE_TERCEIROS_PESSOA_FISICA => 'Outros Serviços de Terceiros - Pessoa Jurídica',
            self::SERVICOS_TECNOLOGIA_INFORMACAO_COMUNICACAO_PESSOA_JURIDICA
            => 'Serviços de Tecnologia da Informação e Comunicação - Pessoa Jurídica',
            self::OBRAS_INSTALACOES => 'Obras e Instalações',
        ];
        if (empty($data[$this->getValue()])) {
            throw new \Exception('Opção inválida.');
        }

        return $data[$this->getValue()];
    }

    /**
     * @param $categoriadoItemPncp
     * @return array
     * @throws \ReflectionException
     * @throws \Exception
     */
    public static function getElementoDespesa($categoriadoItemPncp)
    {
        $elementos = self::$dePara[$categoriadoItemPncp];
        $elementoDespesa = [];
        $elementoDespesa[] = (object)[
            'codigo' => 0,
            'descricao' => 'Selecione'
        ];

        foreach ($elementos as $elemento) {
            $elementoDespesaEnum = new self($elemento);
            $elementoDespesa[] = (object)[
                'codigo' => $elementoDespesaEnum->value,
                'descricao' => $elementoDespesaEnum->name(),
            ];
        }
        return $elementoDespesa;
    }
}
