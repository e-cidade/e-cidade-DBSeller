<?php

namespace App\Domain\Educacao\Censo\Enums;

use ECidade\Enum\Enum;

class TipoCursoProfissionalizanteEnum extends Enum
{
    const AMBIENTE_SAUDE = 1;
    const APOIO_EDUCACIONAL = 2;
    const PROCESSOS_INSDUSTRIAIS = 3;
    const GESTAO_NEGOCIOS = 4;
    const HOSPITALIDADE_LAZER = 5;
    const INFORMACAO_COMUNICACAO = 6;
    const INFRA_ESTRUTURA = 7;
    const MILITAR = 8;
    const PRODUCAO_ALIMENTICIA = 9;
    const PRODUCAO_CULTURAL = 10;
    const PRODUCAO_INDUSTRIAL = 11;
    const RECURSOS_NATURAIS = 12;
    const SEGURANCA = 13;

    /**
     * @return string
     * @throws Exception
     */

    public function descricao()
    {
        $data = array(
            self::AMBIENTE_SAUDE => "Ambiente e saúde",
            self::APOIO_EDUCACIONAL => "Desenvolvimento educacional e social",
            self::PROCESSOS_INSDUSTRIAIS => "Controle e processos industriais",
            self::GESTAO_NEGOCIOS => "Gestão e negócios",
            self::HOSPITALIDADE_LAZER => "Turismo, hospitalidade e lazer",
            self::INFORMACAO_COMUNICACAO => " Informação e comunicação",
            self::INFRA_ESTRUTURA => "Infraestrutura",
            self::MILITAR => "Militar",
            self::PRODUCAO_ALIMENTICIA => "Produção alimentícia",
            self::PRODUCAO_CULTURAL => "Produção cultural e design",
            self::PRODUCAO_INDUSTRIAL => "Produção industrial",
            self::RECURSOS_NATURAIS => "Recursos naturais",
            self::SEGURANCA => "Segurança "
        );

        if (empty($data[$this->getValue()])) {
            throw new Exception('Curso não encontrado.');
        }

        return $data[$this->getValue()];
    }
}
