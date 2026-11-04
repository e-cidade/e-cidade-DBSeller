<?php

namespace ECidade\RecursosHumanos\ESocial\Integracao;

use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use InvalidArgumentException;

/**
 * Class Recurso
 * @package ECidade\RecursosHumanos\ESocial\Integracao
 */
class Recurso
{
    /**
     * @var string
     */
    const CADASTRO_EMPREGADOR = '/empregador';
    /**
     * @var string
     */
    const CONSULTA_RECIBO = '/evento/recibo';
    /**
     * @var string
     */
    const EVENTO_EMPREGADOR = '/evento/empregador';
    /**
     * @var string
     */
    const EVENTO_ESTABELECIMENTO = '/evento/estabelecimento';
    /**
     * @var string
     */
    const EVENTO_RUBRICA = '/evento/rubrica';
    /**
     * @var string
     */
    const EVENTO_LOTACAO_TRIBUTARIA = '/evento/lotacao_tributaria';
    /**
     * @var string
     */
    const EVENTO_EMPREGO_PUBLICO = '/evento/emprego_publico';
    /**
     * @var string
     */
    const EVENTO_CARREIRA_PUBLICA = '/evento/carreira_publica';
    /**
     * @var string
     */
    const EVENTO_FUNCAO = '/evento/funcao';
    /**
     * @var string
     */
    const EVENTO_HORARIOS = '/evento/horarios';
    /**
     * @var string
     */
    const EVENTO_AMBIENTE_TRABALHO = '/evento/ambiente_trabalho';
    /**
     * @var string
     */
    const EVENTO_PROCESSO_ADMINISTRATIVO = '/evento/processo_administrativo';
    /**
     * @var string
     */
    const EVENTO_OPERACAO_PORTUARIA = '/evento/operacao_portuaria';
    /**
     * @var string
     */
    const EVENTO_ADMISSAO_SERVIDOR = '/evento/admissao_servidor';
    /**
     * @var string
     */
    const EVENTO_ADMISSAO_PRELIMINAR = '/evento/admissao_preliminar';
    /**
     * @var string
     */
    const EVENTO_AVISO_PREVIO = '/evento/aviso_previo';
    /**
     * @var string
     */
    const EVENTO_AFASTAMENTO_TEMPORARIO = '/evento/afastamento_temporario';
    /**
     * @var string
     */
    const CONDICAO_AMBIENTAL_TRABALHO = '/evento/condicao_ambiental_trabalho';

    /**
     * @var string
     */
    const EVENTO_DESLIGAMENTO_SERVIDOR = '/evento/desligamento';

    /**
     * @var string
     */
    const EVENTO_EXCLUSAO = '/evento/exclusao_eventos';
    /**
     * @var string
     */
    const EVENTO_TRABALHO_INTERMITENTE = '/evento/trabalho_intermitente';

    /**
     * @var string
     */
    const TRABALHADOR_SEM_VINCULO_INICIAL = '/evento/trabalhador_sem_vinculo_inicio';
    /**
     * @var string
     */
    const TRABALHADOR_SEM_VINCULO_ALTERACAO = '/evento/trabalhador_sem_vinculo_alteracao';

    /**
     * @var string
     */
    const ALTERACAO_CONTRATUAL = '/evento/alteracao_contratual';

    /**
     * @var string
     */
    const ALTERACAO_SERVIDOR = '/evento/alteracao_servidor';

    /**
     * @var string
     */
    const EVENTO_REINTEGRACAO = '/evento/reintegracao';

    /**
     * @var string
     */
    const TRABALHADOR_SEM_VINCULO_TERMINO = '/evento/trabalhador_sem_vinculo_termino';

    /**
     * @var string
     */
    const CONTRIBUINTE = '/evento/efd-reinf/contribuinte';

    /**
     * @var string
     */
    const EFD_PROCESSO = '/evento/efd-reinf/processos';

    /**
     * @var string
     */
    const EFD_RETENCOES_SERVICOS_TOMADOS = '/evento/efd-reinf/servicos_tomados';

    /**
     * @var string
     */
    const EFD_AQUISICAO_PRODUCAO_RURAL = '/evento/efd-reinf/aquisicao_producao_rural';

    /**
     * @var string
     */
    const EFD_SERVICOS_PRESTADOS = '/evento/efd-reinf/retencao_servicos_prestados';

    /**
     * @var string
     */
    const EFD_REABERTURA_EVENTOS = '/evento/efd-reinf/reabertura_periodicos';

    /**
     * @var string
     */
    const EFD_FECHAMENTO_PERIODICOS = '/evento/efd-reinf/fechamento_periodicos';

    /**
     * @var string
     */
    const EFD_PAGAMENTOS_CREDITOS_PF = '/evento/efd-reinf/pagamentos_creditos_pf';

    /**
     * @var string
     */
    const EFD_PAGAMENTOS_CREDITOS_PJ = '/evento/efd-reinf/pagamentos_creditos_pj';

    /**
     * @var string
     */
    const EFD_PAGAMENTOS_CREDITOS_NI = '/evento/efd-reinf/pagamentos_creditos_ni';

    /**
     * @var string
     */
    const EFD_REABERTURA_FECH_R4000  = '/evento/efd-reinf/reabertura_fech_r4000';

    /**
     * @var string
     */
    const REMUNERACAO_RGPS = '/evento/remuneracao_rgps';

    /**
     * @var string
     */
    const REMUNERACAO_RPPS = '/evento/remuneracao_rpps';

    /**
     * @var string
     */
    const REMUNERACAO_BENEFICIO_ENTE_PUBLICO = '/evento/remuneracao_beneficio_ente_publico';
    /**
     * @var string
     */
    const EFD_EXCLUSAO_EVENTOS = '/evento/efd-reinf/exclusao_eventos';
    /**
     * @var string
     */
    const ESOCIAL_PAGAMENTOS_RENDIMENTOS_TRABALHO = '/evento/pagamentos_rendimentos_trabalho';
    /**
     * @var string
     */
    const CONTRIBUICAO_SINDICAL_PATRONAL = '/evento/contribuicao_sindical_patronal';
    /**
     * @var string
     */
    const FECHAMENTO_EVENTOS_PERIODICOS = '/evento/fechamento_eventos_periodicos';

    /**
     * @var string
     */
    const REABERTURA_EVENTOS_PERIODICOS = '/evento/reabertura_eventos_periodicos';

    /**
     * @var string
     */
    const CADASTRO_BENEFICIARIO = '/evento/cadastro_beneficiario';

    /**
     * @var string
     */
    const CADASTRO_BENEFICIARIO_ALTERACAO = '/evento/cadastro_beneficiario_alteracao';

    /**
     * @var string
     */
    const CADASTRO_BENEFICIO = '/evento/cadastro_beneficio';

    /**
     * @var string
     */
    const ALTERACAO_BENEFICIO = '/evento/alteracao_beneficio';


    /**
     * @var string
     */
    const BENEFICIO_TERMINO = '/evento/beneficio_termino';

    /**
     * @var string
     */
    const CONSULTA_TRABALHADOR_INTERMITENTE = '/evento/consultar_trabalhador_intermitente';

    /**
     * @var string
     */
    const TOTALIZACAO_PAGAMENTOS_CONTINGENCIA = '/evento/totalizador_pagamentos_contingencia';
    /**
     * @var string
     */
    const CONSULTA_REFERENCIA_PARA_PAGAMENTOS_RENDIMENTOS_TRABALHO =
        '/evento/consultar_referencia_para_pagamentos_rendimentos_trabalho';

    /**
     * @var string
     */
    const CONSULTA_REFERENCIA_PARA_PAGAMENTOS_RENDIMENTOS_TRABALHO_DESLIGAMENTO =
    '/evento/consultar_referencia_para_pagamentos_rendimentos_trabalho_desligamento';

    const MONITORAMENTO_SAUDE = '/evento/monitoramento_saude';

    const CESSAO_EXERCICIO = '/evento/cessao';

    /**
     * @var string
     */
    const CAT = '/evento/cat';

    /**
     * @var string
     */
    const CERTIFICADO_VALIDADE = '/evento/data_certificado';

    /**
     * @var string
     */
    const LIMPA_CACHE = '/evento/limpa_cache';

    /**
     * @var string
     */
    const PROCESSO_TRABALHISTA = '/evento/processo_trabalhista';

    /**
     * @var string
     */
    const TRIBUTO_TRABALHISTA = '/evento/tributo_trabalhista';

    /**
     * @var string
     */
    const EXCLUSAO_PROCESSO_TRABALHISTA = '/evento/exclusao_processo_trabalhista';

    const INFORMACOES_COMPLEMENTARES_EVENTOS_PERIODICOS = '/evento/informacoes_complementares_eventos_periodicos';

    const EXAME_TOXICOLOGICO_MOTORISTA_PROFISSIONAL_EMPREGADO =
        '/evento/exame_toxicologico_motorista_profissional_empregado';
    /**
     * @param $codigoEvento
     * @return string
     */
    public static function getRecursoByEvento($codigoEvento)
    {
        switch ($codigoEvento) {
            case Tipo::S1000:
                return self::EVENTO_EMPREGADOR;
            case Tipo::S1005:
                return self::EVENTO_ESTABELECIMENTO;
            case Tipo::S1010:
                return self::EVENTO_RUBRICA;
            case Tipo::S1020:
                return self::EVENTO_LOTACAO_TRIBUTARIA;
            case Tipo::S1030:
                return self::EVENTO_EMPREGO_PUBLICO;
            case Tipo::S1035:
                return self::EVENTO_CARREIRA_PUBLICA;
            case Tipo::S1040:
                return self::EVENTO_FUNCAO;
            case Tipo::S1050:
                return self::EVENTO_HORARIOS;
            case Tipo::S1060:
                return self::EVENTO_AMBIENTE_TRABALHO;
            case Tipo::S1070:
                return self::EVENTO_PROCESSO_ADMINISTRATIVO;
            case Tipo::S1080:
                return self::EVENTO_OPERACAO_PORTUARIA;
            case Tipo::S1200:
                return self::REMUNERACAO_RGPS;
            case Tipo::S1202:
                return self::REMUNERACAO_RPPS;
            case Tipo::S1207:
                return self::REMUNERACAO_BENEFICIO_ENTE_PUBLICO;
            case Tipo::S1280:
                return self::INFORMACOES_COMPLEMENTARES_EVENTOS_PERIODICOS;
            case Tipo::S1298:
                return self::REABERTURA_EVENTOS_PERIODICOS;
            case Tipo::S1210:
                return self::ESOCIAL_PAGAMENTOS_RENDIMENTOS_TRABALHO;
            case Tipo::S1295:
                return self::TOTALIZACAO_PAGAMENTOS_CONTINGENCIA;
            case Tipo::S1299:
                return self::FECHAMENTO_EVENTOS_PERIODICOS;
            case Tipo::S1300:
                return self::CONTRIBUICAO_SINDICAL_PATRONAL;
            case Tipo::S2190:
                return self::EVENTO_ADMISSAO_PRELIMINAR;
            case Tipo::S2200:
                return self::EVENTO_ADMISSAO_SERVIDOR;
            case Tipo::S2205:
                return self::ALTERACAO_SERVIDOR;
            case Tipo::S2206:
                return self::ALTERACAO_CONTRATUAL;
            case Tipo::S2210:
                return self::CAT;
            case Tipo::S2220:
                return self::MONITORAMENTO_SAUDE;
            case Tipo::S2221:
                return self::EXAME_TOXICOLOGICO_MOTORISTA_PROFISSIONAL_EMPREGADO;
            case Tipo::S2230:
                return self::EVENTO_AFASTAMENTO_TEMPORARIO;
            case Tipo::S2231:
                return self::CESSAO_EXERCICIO;
            case Tipo::S2240:
                return self::CONDICAO_AMBIENTAL_TRABALHO;
            case Tipo::S2250:
                return self::EVENTO_AVISO_PREVIO;
            case Tipo::S2260:
                return self::EVENTO_TRABALHO_INTERMITENTE;
            case Tipo::S2298:
                return self::EVENTO_REINTEGRACAO;
            case Tipo::S2299:
                return self::EVENTO_DESLIGAMENTO_SERVIDOR;
            case Tipo::S2300:
                return self::TRABALHADOR_SEM_VINCULO_INICIAL;
            case Tipo::S2306:
                return self::TRABALHADOR_SEM_VINCULO_ALTERACAO;
            case Tipo::S2399:
                return self::TRABALHADOR_SEM_VINCULO_TERMINO;
            case Tipo::S2400:
                return self::CADASTRO_BENEFICIARIO;
            case Tipo::S2405:
                return self::CADASTRO_BENEFICIARIO_ALTERACAO;
            case Tipo::S2410:
                return self::CADASTRO_BENEFICIO;
            case Tipo::S2416:
                return self::ALTERACAO_BENEFICIO;
            case Tipo::S2420:
                return self::BENEFICIO_TERMINO;
            case Tipo::S2500:
                return self::PROCESSO_TRABALHISTA;
            case Tipo::S2501:
                return self::TRIBUTO_TRABALHISTA;
            case Tipo::S3000:
                return self::EVENTO_EXCLUSAO;
            case Tipo::S3500:
                return self::EXCLUSAO_PROCESSO_TRABALHISTA;
            case Tipo::R1000:
                return self::CONTRIBUINTE;
            case Tipo::R1070:
                return self::EFD_PROCESSO;
            case Tipo::R2010:
                return self::EFD_RETENCOES_SERVICOS_TOMADOS;
            case Tipo::R2055:
                return self::EFD_AQUISICAO_PRODUCAO_RURAL;
            case Tipo::R2020:
                return self::EFD_SERVICOS_PRESTADOS;
            case Tipo::R2098:
                return self::EFD_REABERTURA_EVENTOS;
            case Tipo::R2099:
                return self::EFD_FECHAMENTO_PERIODICOS;
            case Tipo::R9000:
                return self::EFD_EXCLUSAO_EVENTOS;
            case Tipo::R4010:
                return self::EFD_PAGAMENTOS_CREDITOS_PF;
            case Tipo::R4020:
                return self::EFD_PAGAMENTOS_CREDITOS_PJ;
            case Tipo::R4040:
                return self::EFD_PAGAMENTOS_CREDITOS_NI;
            case Tipo::R4099:
                return self::EFD_REABERTURA_FECH_R4000;
            default:
                throw new InvalidArgumentException("O evento {$codigoEvento} não existe ou não está mapeado.");
        }
    }
}
