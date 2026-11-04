<?php

namespace App\Domain\Educacao\CentralMatriculas\Enums;

use ECidade\Enum\Enum;

class TipoParametroEnum extends Enum
{
    const VALIDADE_ALOCACAO = "validade_alocacao";
    const HABILITA_LISTA_ESPERA = "habilita_lista_espera";
    const TIPO_CONSULTA = "habilita_edicao";
    const HABILITA_REEMISSAO = "habilita_reemissao";
    const HABILITA_VALIDAR_CPF_CAND = "habilita_validar_cpf_candidato";
    const APENAS_ESCOLAS_COM_VAGAS = "apenas_escolas_com_vagas";
    const EXIBE_CLASSIFICA_LISTA_ESPERA = "exibe_classificacao_lista_espera";
    const LI_CONCORDO_LEGISLACAO = "li_concordo_legislacao";
    const VALIDA_LIMITE_INSCRICOES = "valida_limite_inscricoes";
    const AUTOMATIZA_SITUACAO_ALOCADO = "automatiza_situacao_alocado";
    const HABILITA_EDICAO_EXCLUSAO = "habilita_edicao_exclusao";
    const TITULO_CPMPROVANTE = "titulo_comprovante";
    public function name()
    {
        $data = array(
            self::VALIDADE_ALOCACAO => "Prazo de Validade da Alocação (em dias úteis)",
            self::AUTOMATIZA_SITUACAO_ALOCADO => "Automatizar alteração da Situação do Alocado",
            self::HABILITA_LISTA_ESPERA => "Habilitar Consulta Lista de Espera no Matrícula Online",
            self::TIPO_CONSULTA => "Habilitar Busca Candidato Por",
            self::HABILITA_REEMISSAO => "Habilitar Reemissão do Comprovante no Matrícula Online",
            self::HABILITA_VALIDAR_CPF_CAND => "Habilitar 2ª Validação (consulta por CPF)",
            self::APENAS_ESCOLAS_COM_VAGAS => "Apresentar apenas escolas com vagas",
            self::EXIBE_CLASSIFICA_LISTA_ESPERA => "Exibir classificação do candidato na lista de espera",
            self::LI_CONCORDO_LEGISLACAO => "Exibir Li e Concordo com a Legislação",
            self::VALIDA_LIMITE_INSCRICOES => " Validar Limite de Inscrições por Escola",
            self::HABILITA_EDICAO_EXCLUSAO => "Habilitar menu Edição/Exclusão",
            self::TITULO_CPMPROVANTE => "Titulo do Compprovate"
        );

        if (empty($data[$this->getValue()])) {
            throw new Exception('Tipo não encontrado.');
        }

        return $data[$this->getValue()];
    }

    public function componente()
    {
        $data = array(
            self::VALIDADE_ALOCACAO => "InputNumber",
            self::AUTOMATIZA_SITUACAO_ALOCADO => "Checkbox",
            self::HABILITA_LISTA_ESPERA => "Checkbox",
            self::TIPO_CONSULTA => "Dropdown",
            self::HABILITA_REEMISSAO => "Dropdown",
            self::HABILITA_VALIDAR_CPF_CAND => "Checkbox",
            self::APENAS_ESCOLAS_COM_VAGAS => "Checkbox",
            self::EXIBE_CLASSIFICA_LISTA_ESPERA => "Checkbox",
            self::LI_CONCORDO_LEGISLACAO => "Checkbox",
            self::VALIDA_LIMITE_INSCRICOES => "Checkbox",
            self::HABILITA_EDICAO_EXCLUSAO => "Dropdown",
            self::TITULO_CPMPROVANTE => "InputText"
        );

        if (empty($data[$this->getValue()])) {
            throw new Exception('Tipo não encontrado.');
        }

        return $data[$this->getValue()];
    }

    public function options()
    {
        $data = array(
            self::HABILITA_REEMISSAO => [
                (object) [
                    "value" => 0,
                    "label" => "TODOS"
                ],
                (object) [
                    "value" => 1,
                    "label" => "PROTOCOLO"
                ],
                (object) [
                    "value" => 2,
                    "label" => "CPF"
                ],
                (object) [
                    "value" => 99,
                    "label" => "NÃO LIBERAR"
                ]
            ],
            self::TIPO_CONSULTA => [
                (object) [
                    "value" => 0,
                    "label" => "TODOS"
                ],
                (object) [
                    "value" => 1,
                    "label" => "PROTOCOLO"
                ],
                (object) [
                    "value" => 2,
                    "label" => "CPF | VISTO | RNM/RNE"
                ],
                (object) [
                    "value" => 99,
                    "label" => "NÃO LIBERAR"
                ]
            ],
            self::HABILITA_EDICAO_EXCLUSAO => [
                (object) [
                    "value" => 0,
                    "label" => "AMBOS"
                ],
                (object) [
                    "value" => 1,
                    "label" => "EDIÇÃO"
                ],
                (object) [
                    "value" => 2,
                    "label" => "EXCLUSÃO"
                ],
                (object) [
                    "value" => 99,
                    "label" => "NÃO LIBERAR"
                ]
            ]
        );

        if (empty($data[$this->getValue()])) {
            throw new Exception('Tipo não encontrado.');
        }

        return $data[$this->getValue()];
    }
}
