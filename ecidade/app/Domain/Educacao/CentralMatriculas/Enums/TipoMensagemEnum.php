<?php

namespace App\Domain\Educacao\CentralMatriculas\Enums;

use ECidade\Enum\Enum;

class TipoMensagemEnum extends Enum
{
    const CANDIDADO_LISTA_ESPERA = 1;
    const SISTEMA_FECHADO = 2;
    const CANDIDATO_POSSUI_MATRICULA = 3;
    const CONSULTA_CANDIDATURA = 4;
    const CRITERIO_CANDIDATURA_NAO_ALOCADA_NAO_POSSUI = 5;
    const CRITERIO_CANDIDATURA_NAO_ALOCADA_POSSUI = 6;
    const OBS_TELA_CONFIRMAR = 7;
    const CANDIATURA_EDITADA = 8;
    const MENSAGEM_VERACIDADE = 9;
    const ESCOLHA_ESCOLAS = 10;
    const DOCUMENTOS_PARA_MATRICULA = 11;
    const TERMO_DE_ACEITE = 12;
    const CANDIDATO_NAO_POSSUI_MATRICULA = 13;

    /**
     * @return string
     * @throws Exception
     */
    public function name()
    {
        $data = array(
            self::CANDIDADO_LISTA_ESPERA => "Inscrição Lista Espera",
            self::SISTEMA_FECHADO => "Sistema Fechado",
            self::CANDIDATO_POSSUI_MATRICULA => "Candidato possui matrícula",
            self::CONSULTA_CANDIDATURA => "Consulta Inscrição",
            self::CRITERIO_CANDIDATURA_NAO_ALOCADA_NAO_POSSUI =>
                "Critério Inscrição Anterior não alocada/não possui",
            self::CRITERIO_CANDIDATURA_NAO_ALOCADA_POSSUI => "Critério Inscrição Anterior não alocada/possui",
            self::OBS_TELA_CONFIRMAR => "Observação tela de confirmação",
            self::CANDIATURA_EDITADA => "Inscrição Editada",
            self::MENSAGEM_VERACIDADE => "Declaração de Veracidade",
            self::ESCOLHA_ESCOLAS => "Seleção de Escolas",
            self::DOCUMENTOS_PARA_MATRICULA => "Documentos para matrícula",
            self::TERMO_DE_ACEITE => "Mensagem Termo de Aceite",
            self::CANDIDATO_NAO_POSSUI_MATRICULA => "Candidato não possui matrícula"
        );

        if (empty($data[$this->getValue()])) {
            throw new Exception('Atendimento não encontrado.');
        }

        return $data[$this->getValue()];
    }
}
