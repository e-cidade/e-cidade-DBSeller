<?php
namespace ECidade\RecursosHumanos\ESocial\Enum;

/**
 * Tipos de rescições do Esocial
 * Class TipoRescisao
 * @package Ecidade\RecursosHumanos\ESocial\Enum
 */

class TipoRescisao
{

    private $tipos = array();

    /**
     *
     * TipoRescisao constructor.
     */
    public function __construct()
    {
        $cd8 = "08 Rescisão do contrato de trabalho por interesse do(a) empregado(a), nas hipóteses previstas nos arts."
            . " 394 e 483, § 1º, da CLT -Todos";
        $cd9 = "09 Rescisão por opção do empregado em virtude de falecimento do empreg. ind.ou empreg.doméstico -Todos";
        $cd11 = "11 Transferência de empregado para empresa do mesmo grupo empresarial que tenha assumido os encargos "
            . "trabalhistas, sem que tenha havido rescisão do contrato de trabalho -Todos, exceto [104]";
        $cd12 = "12 Transferência de empregado da empresa consorciada para o consórcio que tenha assumido os encargos "
            . "trabalhistas, e vice-versa, sem que tenha havido rescisão do contrato de trabalho -Todos, exceto [104]";
        $cd13 = "13 Transferência de empregado de empresa ou consórcio, para outra empresa ou consórcio que tenha "
            . "assumido os encargos trabalhistas por motivo de sucessão (fusão,cisão ou incorporação), sem que tenha "
            . "havido resc. do contrato de trabalho -Todos, exceto [104]";
        $cd14 = "14 Rescisão do contrato de trabalho por encerramento da empresa, de seus estabelecimentos ou "
            . "supressão de parte de suas atividades ou falecimento do empregador individual ou empregador doméstico "
            . "sem continuação da atividade -Todos";
        $cd16 = "16 Declaração de nulidade do contrato de trabalho por infringência ao inciso II do art. 37 da "
            . "Constituição Federal, quando mantido o direito ao salário -Todos, exceto [104]";
        $cd26 = "26 Rescisão do contrato de trabalho por paralisação temporária ou definitiva da empresa, "
            . "estabelecimento ou parte das atividades motivada por atos de autoridade municipal, estadual ou federal "
            . "- Todos, exceto [104]";
        $cd34 = "34 Transferência de titularidade do empregado doméstico para outro representante da mesma unidade "
            . "familiar - [104]";
        $cd41 = "41 Rescisão do contrato de aprendizagem por desempenho insuficiente ou inadaptação do aprendiz -[103]";
        $cd42 = "42 Rescisão do contrato de aprendizagem por ausência injustificada do aprendiz à escola que implique"
            . " perda do ano letivo -[103]";
        $cd43 = "43 Transferência de empregado de empresa considerada inapta por inexistência de fato";
        $cd44 = "44 Agrupamento contratual";
        $cd45 = "45  Exclusão de militar das Forças Armadas do serviço ativo, com efeitos financeiros";
        $cd46 = "46  Exclusão de militar das Forças Armadas do serviço ativo, sem efeitos financeiros";
        $cd47 = "47 - Rescisão do contrato de trabalho por encerramento da empresa, de seus estabelecimentos ou "
            . "supressão de parte de suas atividades - Todos, exceto [104]";

        $cd48 = "48 - Falecimento do empregador individual sem continuação da atividade - "
            . "Todos, exceto [104]";
        $cd49 = "49 - Falecimento do empregador doméstico sem continuação da atividade - [104]";

        $this->tipos = array(
            1  => "01 Rescisão com justa causa, por iniciativa do empregador -Todos",
            2  => "02 Rescisão sem justa causa, por iniciativa do empregador -Todos",
            3  => "03 Rescisão antecipada do contrato a termo por iniciativa do empregador -Todos",
            4  => "04 Rescisão antecipada do contrato a termo por iniciativa do empregado -Todos",
            5  => "05 Rescisão por culpa recíproca -Todos",
            6  => "06 Rescisão por término do contrato a termo -Todos",
            7  => "07 Rescisão do contrato de trabalho por iniciativa do empregado -Todos",
            8  => $cd8,
            9  => $cd9,
            10 => "10 Rescisão por falecimento do empregado -Todos",
            11 => $cd11,
            12 => $cd12,
            13 => $cd13,
            14 => $cd14,
            16 => $cd16,
            17 => "17 Rescisão indireta do contrato de trabalho -Todos",
            21 => "21 Reforma militar - [307]",
            22 => "22 Reserva militar - [307]",
            23 => "23 Exoneração - [301, 302, 303, 306, 307, 309, 310, 312]",
            24 => "24 Demissão - [301, 302, 303, 306, 307, 309, 310, 312]",
            25 => "25 Vacância de cargo efetivo - [301, 307]",
            26 => $cd26,
            27 => "27 Rescisão por motivo de força maior -Todos",
            29 => "29 Redistribuição - [301, 303, 306, 307, 309]",
            30 => "30 Mudança de regime trabalhista - Todos, exceto [104]",
            31 => "31 Reversão de reintegração - Todos, exceto [104]",
            32 => "32 Extravio de militar - [307]",
            33 => "33 Rescisão por acordo entre as partes (art. 484-A da CLT)-Todos",
            34 => $cd34,
            36 => "36 Mudança de CPF - Todos",
            37 => "37 Remoção, em caso de alteração do órgão declarante -[301, 306, 307, 309]",
            38 => "38 Aposentadoria, exceto por invalidez -[101, 301, 302, 312]",
            39 => "39 Aposentadoria de servidor estatutário, por invalidez -[301, 306, 309]",
            40 => "40 Término de exercício de mandato [301, 302, 303, 306, 307, 309, 310, 312, 314]",
            41 => $cd41,
            42 => $cd42,
            43 => $cd43,
            44 => $cd44,
            45 => $cd45,
            46 => $cd46,
            47 => $cd47,
            48 => $cd48,
            49 => $cd49
        );
    }

    /**
     * Retorna todos os tipos de rescisão
     * @return array
     */
    public function getTipos()
    {

        return $this->tipos;
    }
}
