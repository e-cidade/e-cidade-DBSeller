<?php

namespace App\Domain\Educacao\Escola\Enums;

use ECidade\Enum\Enum;
use Exception;

class AtendimentosEspecialEnum extends Enum
{
    const ENSINO_BRAILE = 0;
    const VAZIO = 1;
    const ENSINO_OPTICO_NAO_OPTICO = 2;
    const PROCESSOS_MENTAIS = 3;
    const TECNICA_ORIENTCAO = 4;
    const ENSINO_SINAIS = 5;
    const ENSINO_CAA = 6;
    const ENRIQUECIMENTO_CURRICULAR = 7;
    const CALCULO_SOROBAN = 8;
    const INFORMATICA_ACESSIVEL = 9;
    const PORTUGUES_ESCRITA = 10;
    const AUTONOMIA_ESCOLAR = 11;
    const FUNCOES_COGNITIVAS = 12;
    const VIDA_AUTONOMA = 13;
    const PORTUGUES_SEGUNDA_LINGUA = 14;

    /**
     * @return string
     * @throws Exception
     */
    public function descricao()
    {
        $data = array(
            self::ENSINO_BRAILE => "Ensino do Sistema Braille",
            self::VAZIO => "",
            self::ENSINO_OPTICO_NAO_OPTICO => "Ensino do uso de recursos ópticos e não ópticos",
            self::PROCESSOS_MENTAIS => "Estratégias para o desenvolvimento de processos mentais",
            self::TECNICA_ORIENTCAO => "Técnicas de orientação e mobilidade",
            self::ENSINO_SINAIS => "Ensino de Língua Brasileira de Sinais - Libras",
            self::ENSINO_CAA => "Ensino de uso da Comunicação Alternativa e Aumentativa - CAA",
            self::ENRIQUECIMENTO_CURRICULAR => "Estratégias para enriquecimento curricular",
            self::CALCULO_SOROBAN => "Ensino das técnicas de cálculo no Soroban",
            self::INFORMATICA_ACESSIVEL => "Ensino da usabilidade e das funcionalidades da informática acessível",
            self::PORTUGUES_ESCRITA => "Ensino da Língua Portuguesa na modalidade escrita",
            self::AUTONOMIA_ESCOLAR => "Estratégias para autonomia no ambiente escolar",
            self::FUNCOES_COGNITIVAS => "Desenvolvimento de funçðes cognitivas",
            self::VIDA_AUTONOMA => "Desenvolvimento de vida autônoma",
            self::PORTUGUES_SEGUNDA_LINGUA => "Ensino da Língua Portuguesa como Segunda Língua"
        );

        if (empty($data[$this->getValue()])) {
            throw new Exception('Atendimento não encontrado.');
        }

        return $data[$this->getValue()];
    }

    public static function all()
    {
        return array(
            self::ENSINO_BRAILE => "Ensino do Sistema Braille",
            self::VAZIO => "",
            self::ENSINO_OPTICO_NAO_OPTICO => "Ensino do uso de recursos ópticos e não ópticos",
            self::PROCESSOS_MENTAIS => "Estratégias para o desenvolvimento de processos mentais",
            self::TECNICA_ORIENTCAO => "Técnicas de orientação e mobilidade",
            self::ENSINO_SINAIS => "Ensino de Língua Brasileira de Sinais - Libras",
            self::ENSINO_CAA => "Ensino de uso da Comunicação Alternativa e Aumentativa - CAA",
            self::ENRIQUECIMENTO_CURRICULAR => "Estratégias para enriquecimento curricular",
            self::CALCULO_SOROBAN => "Ensino das técnicas de cálculo no Soroban",
            self::INFORMATICA_ACESSIVEL => "Ensino da usabilidade e das funcionalidades da informática acessível",
            self::PORTUGUES_ESCRITA => "Ensino da Língua Portuguesa na modalidade escrita",
            self::AUTONOMIA_ESCOLAR => "Estratégias para autonomia no ambiente escolar",
            self::FUNCOES_COGNITIVAS => "Desenvolvimento de funçðes cognitivas",
            self::VIDA_AUTONOMA => "Desenvolvimento de vida autônoma",
            self::PORTUGUES_SEGUNDA_LINGUA => "Ensino da Língua Portuguesa como Segunda Língua"
        );
    }
}
