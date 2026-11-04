<?php

namespace ECidade\Enum\Saude\ESF;

use ECidade\Enum\Enum;

class EstrategiaVacinacaoEnum extends Enum
{
    const ROTINA = 1;
    const ESPECIAL = 2;
    const BLOQUEIO = 3;
    const INTENSIFICACAO = 4;
    const CAMPANHA_INDISCRIMINADA = 5;
    const CAMPANHA_SELETIVA = 6;
    const SOROTERAPIA = 7;
    const SERVICO_PRIVADO = 8;
    const MONITORAMENTO_RAPIDO_DE_COBERTURA_VACINAL = 9;
    const MULTIVACINACAO = 10;
    const PESQUISA = 11;
    const PRE_EXPOSICAO = 12;
    const POS_EXPOSICAO = 13;
    const REEXPOSICAO = 14;


    public function name()
    {
        $data = [
            self::ROTINA => 'Rotina',
            self::ESPECIAL => 'Especial',
            self::BLOQUEIO => 'Bloqueio',
            self::INTENSIFICACAO => 'Intensificação',
            self::CAMPANHA_INDISCRIMINADA => 'Campanha indiscriminada',
            self::CAMPANHA_SELETIVA => 'Campanha seletiva',
            self::SOROTERAPIA => 'Soroterapia',
            self::SERVICO_PRIVADO => 'Serviço privado',
            self::MONITORAMENTO_RAPIDO_DE_COBERTURA_VACINAL => 'Monitoramento rápido de cobertura vacinal',
            self::MULTIVACINACAO => 'Multivacinação',
            self::PESQUISA => 'Pesquisa',
            self::PRE_EXPOSICAO => 'Pré-exposição',
            self::POS_EXPOSICAO => 'Pós-exposição',
            self::REEXPOSICAO => 'Reexposição'
        ];

        if (empty($data[$this->getValue()])) {
            throw new \Exception('Opção inválida.');
        }

        return $data[$this->getValue()];
    }
}
