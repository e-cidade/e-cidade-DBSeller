<?php

namespace App\Domain\Educacao\CentralMatriculas\Enums;

use ECidade\Enum\Enum;

class TipoCorEnum extends Enum
{
    const CABECALHO = 1;
    const INSCRICAO = 2;
    const CONSULTA = 3;
    const ESCOLAS = 4;
    const DUVIDAS = 5;
    const LEGISLACAO = 6;
    const LISTA = 7;
    const EDICAO = 8;

    public function name()
    {
        $data = array(
            self::CABECALHO => "Cabeçalho e Rodapé",
            self::INSCRICAO => "Menu Inscrição",
            self::CONSULTA => "Menu Consulta",
            self::ESCOLAS => "Menu Escolas",
            self::DUVIDAS => "Menu Dúvidas Frequentes",
            self::LEGISLACAO => "Menu Legislação",
            self::LISTA => "Menu Lista de Espera",
            self::EDICAO => "Menu Edição ou Exclusão",
        );

        if (empty($data[$this->getValue()])) {
            throw new Exception('Tipo não encontrado.');
        }

        return $data[$this->getValue()];
    }
}
