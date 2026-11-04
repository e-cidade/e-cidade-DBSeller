<?php

namespace App\Domain\Patrimonial\PNCP\Enum;

use ECidade\Enum\Enum;

class CategoriaItemPlanoContratacaoEnum extends Enum
{
    const MATERIAL = 1;
    const SERVICO = 2;
    const OBRAS = 3;
    const SERVICO_DE_ENGENHARIA = 4;
    const SOLUCOES_DE_TIC = 5;
    const LOCACAO_DE_IMOVEIS = 6;
    const ALIENCAO_CONCESSAO_PERMISSAO = 7;
    const OBRAS_E_SERVICOS_DE_ENGENHARIA = 8;

    /**
     * @return string
     * @throws \Exception
     */
    public function name()
    {
        $data = [
            self::MATERIAL => 'Material',
            self::SERVICO => 'Servico',
            self::OBRAS => 'Obras',
            self::SERVICO_DE_ENGENHARIA => 'Serviços de Engenharia',
            self::SOLUCOES_DE_TIC => 'Soluções de TIC',
            self::LOCACAO_DE_IMOVEIS => 'Locação de Imóveis',
            self::ALIENCAO_CONCESSAO_PERMISSAO => 'Alienção/Concessão/Permissão',
            self::OBRAS_E_SERVICOS_DE_ENGENHARIA => 'Obras e Serviços de Engenharia',
        ];
        if (empty($data[$this->getValue()])) {
            throw new \Exception('Opção inválida.');
        }

        return $data[$this->getValue()];
    }
}
