<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace ECidade\RecursosHumanos\ESocial\Model\Formulario;

use Exception as ExceptionAlias;

/**
 * Tipos de Formulários do eSocial
 *
 * @package ECidade\RecursosHumanos\ESocial\Model\Formulario
 * @author  Andrio Costa - andrio.costa@dbseller.com.br
 */
class Tipo
{
    /**
     * EFD-Reinf
     */
    const EFD_REINF = 1;
    /**
     * eSocial
     */
    const ESOCIAL = 2;
    /**
     * Informações do Empregador/Contribuinte/Órgão Público - Tabela de Estabelecimentos, Obras ou Unidades
     * de Órgãos Públicos
     */
    const EMPREGADOR = 1;
    /**
     * Tabela de  Rubricas
     */
    const RUBRICA = 2;
    /**
     * Cadastramento Inicial do Vínculo e Admissão/Ingresso de Trabalhador
     */
    const SERVIDOR = 3;
    /**
     * Tabela de Lotações Tributárias
     */
    const LOTACAO_TRIBUTARIA = 4;
    /**
     * Tabela de Cargos/Empregos Públicos
     */
    const CARGO = 5;
    /**
     * Tabela de Funções/Cargos em Comissão
     */
    const FUNCAO = 6;
    /**
     * Tabela de Horários/Turnos de Trabalho
     */
    const HORARIO = 7;
    /**
     * Tabela de Processos Administrativos/Judiciais
     */
    const PROCESSOS = 8;
    /**
     * Admissão de Trabalhador - Registro Preliminar
     */
    const ADMISSAO_PRELIMINAR = 9;
    /**
     * Aviso Prévio
     */
    const AVISO_PREVIO = 11;
    /**
     * Afastamento Temporário
     */
    const AFASTAMENTO_TEMPORARIO = 12;
    /**
     * Exclusão de eventos
     */
    const EXCLUSAO_EVENTOS = 13;
    /**
     * Desligamento
     */
    const DESLIGAMENTO_SERVIDOR = 14;
    /**
     * Trabalhador Sem Vínculo de Emprego/Estatutário - Início
     */
    const TRABALHADOR_SEM_VINCULO = 15;
    /**
     * Convocação para Trabalho Intermitente
     */
    const TRABALHO_INTERMITENTE = 16;
    /**
     * Alteração de Dados Cadastrais do Trabalhador
     */
    const ALTERACAO_SERVIDOR = 17;
    /**
     * Alteração de Contrato de Trabalho
     */
    const ALTERACAO_CONTRATUAL = 18;
    /**
     * Reintegração
     */
    const REINTEGRACAO = 19;
    /**
     * Trabalhador Sem Vínculo de Emprego/Estatutário - Alteração Contratual
     */
    const ALTERACAO_TRABALHADOR_SEM_VINCULO = 20;
    /**
     * Trabalhador Sem Vínculo de Emprego/Estatutário - Término
     */
    const TERMINO_TRABALHADOR_SEM_VINCULO = 21;
    /**
     * Cadastro de Beneficiário - Entes Públicos - Início
     */
    const CADASTRO_BENEFICIARIO = 40;
    /**
     * Cadastro de Beneficiário - Entes Públicos - Alteração
     */
    const CADASTRO_BENEFICIARIO_ALTERACAO = 44;
    /**
     * Cadastro de Benefício - Entes Públicos - Término
     */
    const BENEFICIO_TERMINO = 46;
    /**
     * Cadastro de Benefício
     */
    const CADASTRO_BENEFICIO = 41;
    /**
     * Alteração de Benefício
     */
    const ALTERACAO_BENEFICIO = 48;

    /**
     * Processo Trabalhista
     */
    const PROCESSO_TRABALHISTA = 49;

    /**
     * Tributo Processo Trabalhista
     */
    const TRIBUTO_TRABALHISTA = 50;

    /**
     * Tributo Processo Trabalhista
     */
    const EXCLUSAO_PROCESSO_TRABALHISTA  = 51;


    /**
     *
     *  Informacoes Complementares Eventos Periodicos
     */
    const INFORMACOES_COMPLEMENTARES_EVENTOS_PERIODICOS = 56;

    const EXAME_TOXICOLOGICO_MOTORISTA_PROFISSIONAL_EMPREGADO = 57;

    /**
     * Cessão/Exercício em Outro Órgão
     */
    const CESSAO_EXERCICIO = 42;
    /**
     * Solicitação de Totalização para Pagamento em Contingência
     */
    const TOTALIZACAO_PAGAMENTOS_CONTINGENCIA = 35;

    /**
     * Constantes referente aos formulários existentes no e-cidade do EFD-Reinf
     */
    const CONTRIBUINTE = 22;
    /**
     * Tabela de processos do EFD
     */
    const EFD_PROCESSOS = 23;
    /**
     * Remuneração de Trabalhador Vinculado ao Regime Geral de Previdência Social
     */
    const REMUNERACAO_RGPS = 24;

    /**
     * Remuneração de Trabalhador Vinculado ao Regime Próprio de Previdência Social
     */
    const REMUNERACAO_RPPS = 43;
    /**
     * Remuneração de Benefícios - Entes Públicos
     */
    const REMUNERACAO_BENEFICIOS_ENTES_PUBLICOS = 45;
    /**
     * Exclusao de eventos do EFD
     */
    const EFD_EXCLUSAO_EVENTOS = 25;
    /**
     * Retenções sobre serviços tomados do EFD
     */
    const EFD_RETENCOES_SERVICOS_TOMADOS = 26;
    /**
     * Retenções sobre serviços prestados do EFD
     */
    const EFD_SERVICOS_PRESTADOS = 27;
    /**
     * Pagamentos de rendimentos
     */
    const PAGAMENTOS_RENDIMENTOS_TRABALHO = 28;
    /**
     * Contribuição Sindical Patronal
     */
    const CONTRIBUICAO_SINDICAL_PATRONAL = 30;
    /**
     * Fechamento dos Eventos Periódicos
     */
    const FECHAMENTO_EVENTOS_PERIODICOS = 31;
    /**
     *
     */
    const EFD_FECHAMENTO_PERIODICOS = 32;
    /**
     *
     */
    const EFD_REABERTURA_EVENTOS = 33;
    /**
     * Reabertura dos Eventos Periódicos
     */
    const REABERTURA_EVENTOS_PERIODICOS = 34;

    /**
     * Estabelecimentos e Obras
     */
    const OBRAS = 36;

    /**
     * Comunicação de Acidente de Trabalho
     */
    const CAT = 37;

    /**
     * Monitoramento da Saude do Trabalhador
     */
    const MONITORAMENTO_SAUDE = 38;

    /**
     * Condições Ambientais do Trabalho - Agentes Nocivos
     */
    const EXPOSICAO_RISCO = 39;

    /**
     * Aquisição de Produção Rural
     */
    const EFD_AQUISICAO_PRODUCAO_RURAL = 47;

    /**
     * R-4010: Pagamentos/créditos a beneficiário pessoa física
     */
    const EFD_PAGAMENTOS_CREDITOS_PF = 52;

    /**
     * R-4020: Pagamentos/créditos a beneficiário pessoa jurídica
     */
    const EFD_PAGAMENTOS_CREDITOS_PJ = 53;

    /**
     * R-4040: Pagamentos/créditos a beneficiários não identificados
     */
    const EFD_PAGAMENTOS_CREDITOS_NI = 54;

    /**
     * R-4099: Fechamento/reabertura dos eventos da série R-4000
     */
    const EFD_REABERTURA_FECH_R4000  = 55;

    /**
     * Codigo do Evento S-1200 na API
     */
    const S1200_API = 34;

    /**
     * Codigo do Evento S-1202 na API
     */
    const S1202_API = 35;

    /**
     * Codigo do Evento S-1207 na API
     */
    const S1207_API = 36;

    /**
     * Codigo do Evento S-2299 na API
     */
    const S2299_API = 24;

    /**
     * Codigo do Evento S-2399 na API
     */
    const S2399_API = 27;

    /**
     * S-1000
     */
    const S1000 = 1000;
    /**
     * S-1005
     */
    const S1005 = 1005;
    /**
     * S-1010
     */
    const S1010 = 1010;
    /**
     * S-1020
     */
    const S1020 = 1020;
    /**
     * S-1030
     */
    const S1030 = 1030;
    /**
     * S-1035
     */
    const S1035 = 1035;
    /**
     * S-1040
     */
    const S1040 = 1040;
    /**
     * S-1050
     */
    const S1050 = 1050;
    /**
     * S-1060
     */
    const S1060 = 1060;
    /**
     * S-1070
     */
    const S1070 = 1070;
    /**
     * S-1080
     */
    const S1080 = 1080;
    /**
     * S-1200
     */
    const S1200 = 1200;
    /**
     * S-1202
     */
    const S1202 = 1202;
    /**
     * S-1207
     */
    const S1207 = 1207;
    /**
     * S-1210
     */
    const S1210 = 1210;

    /**
     * S-1280
     */
    const S1280 = 1280;
    /**
     * S-1295
     */
    const S1295 = 1295;
    /**
     * S-1298
     */
    const S1298 = 1298;
    /**
     * S-1299
     */
    const S1299 = 1299;
    /**
     * S-1300
     */
    const S1300 = 1300;
    /**
     * S-2190
     */
    const S2190 = 2190;
    /**
     * S-2200
     */
    const S2200 = 2200;
    /**
     * S-2205
     */
    const S2205 = 2205;
    /**
     * S-2206
     */
    const S2206 = 2206;
    /**
     * S-2210
     */
    const S2210 = 2210;
    /**
     * S-2220
     */
    const S2220 = 2220;
    /**
     * S-2220
     */
    const S2221 = 2221;
    /**
     * S-2230
     */
    const S2230 = 2230;
    /**
     * S-2231
     */
    const S2231 = 2231;
    /**
     * S-2240
     */
    const S2240 = 2240;
    /**
     * S-2250
     */
    const S2250 = 2250;
    /**
     * S-2260
     */
    const S2260 = 2260;
    /**
     * S-2298
     */
    const S2298 = 2298;
    /**
     * S-2299
     */
    const S2299 = 2299;
    /**
     * S-2300
     */
    const S2300 = 2300;
    /**
     * S-2399
     */
    const S2399 = 2399;
    /**
     * S-2400
     */
    const S2400 = 2400;
    /**
     * S-2405
     */
    const S2405 = 2405;
    /**
     * S-2410
     */
    const S2410 = 2410;
    /**
     * S-2416
     */
    const S2416 = 2416;
    /**
     * S-2420
     */
    const S2420 = 2420;
    /**
     * S-2500
     */
    const S2500 = 2500;
    /**
     * S-2501
     */
    const S2501 = 2501;
    /**
     * S-3000
     */
    const S3000 = 3000;
    /**
     * S-3500
     */
    const S3500 = 3500;
    /**
     * S-2306
     */
    const S2306 = 2306;

    /**
     * Constantes referentes aos tipos de formatação aplicada em cima dos dados do formulário do EFD-Reinf
     */

    /**
     *  R-1000
     */
    const R1000 = 'R-1000';
    /**
     * R-1070
     */
    const R1070 = 'R-1070';
    /**
     * R-2010
     */
    const R2010 = 'R-2010';
    /**
     * R-2020
     */
    const R2020 = 'R-2020';
    /**
     * R-2055
     */
    const R2055 = 'R-2055';
    /**
     * R-2098
     */
    const R2098 = 'R-2098';
    /**
     * R-2099
     */
    const R2099 = 'R-2099';
    /**
     * R-9000
     */
    const R9000 = 'R-9000';

    /**
     * R-4010
     */
    const R4010 = 'R-4010';

    /**
     * R-4020
     */
    const R4020 = 'R-4020';

    /**
     * R-4040
     */
    const R4040 = 'R-4040';

    /**
     * R-4099
     */
    const R4099 = 'R-4099';

    /**
     * @param  $layout
     * @return string|array
     */
    public static function getDescricoes($layout = null)
    {
        $descricoesArquivos = array(
            Tipo::S1000 => "S1000 - Empregador",
            Tipo::S1005 => "S1005 - Estabelecimentos e Obras",
            Tipo::S1010 => "S1010 - Tabela de Rubricas",
            Tipo::S1020 => "S1020 - Tabela de Lotações Tributárias",
            // Tipo::S1030 => "S1030 - Tabela de Cargos/Empregos Públicos",
            // Tipo::S1040 => "S1040 - Tabela de Funções/Cargos em Comissão",
            // Tipo::S1050 => "S1050 - Tabela de Horários/Turnos de Trabalho",
            Tipo::S1070 => "S1070 - Tabela de Processos Administrativos/Judiciais",
            Tipo::S1200 => "S1200 - Remuneração RGPS",
            Tipo::S1202 => "S1202 - Remuneração RPPS",
            Tipo::S1207 => "S1207 - Benefícios - Entes Públicos",
            Tipo::S1210 => "S1210 - Pagamentos de Rendimentos do Trabalho",
            Tipo::S1280 => "S1280 - Informações Complementares aos Eventos Períodicos",
            // Tipo::S1295 => "S1295 - Solicitação de Totalização para Pagamento em Contingência",

            Tipo::S1298 => "S1298 - Reabetura dos Eventos Periódicos",
            Tipo::S1299 => "S1299 - Fechamento dos Eventos Periódicos",
            // Tipo::S1300 => "S1300 - Contribuição Sindical Patronal",
            Tipo::S2190 => "S2190 - Admissão de Trabalhador - Registro Preliminar",
            Tipo::S2200 => "S2200 - Cadastramento Inicial do Vínculo e Admissão/Ingresso de Trabalhador",
            Tipo::S2205 => "S2205 - Alteração de Dados Cadastrais do Trabalhador",
            Tipo::S2206 => "S2206 - Alteração de Contrato de Trabalho",
            Tipo::S2210 => "S2210 - Comunicação de Acidente de Trabalho",
            Tipo::S2220 => "S2220 - Monitoramento da Saúde do Trabalhador",
            Tipo::S2221 => "S2221 - Exame Toxicológico do Motorista Profissional Empregado",
            Tipo::S2230 => "S2230 - Afastamento Temporário",
            Tipo::S2231 => "S2231 - Cessão/Exercício em Outro Órgão",
            Tipo::S2240 => "S2240 - Condições Ambientais do Trabalho - Agentes Nocivos",
            // Tipo::S2250 => "S2250 - Aviso Prévio",
            // Tipo::S2260 => "S2260 - Convocação para Trabalho Intermitente",
            Tipo::S2298 => "S2298 - Reintegração",
            Tipo::S2299 => "S2299 - Desligamento",
            Tipo::S2300 => "S2300 - Trabalhador Sem Vínculo de Emprego/Estatutário - Início",
            Tipo::S2306 => "S2306 - Trabalhador Sem Vínculo de Emprego/Estatutário - Alteração Contratual",
            Tipo::S2399 => "S2399 - Trabalhador Sem Vínculo de Emprego/Estatutário - Término",
            Tipo::S2400 => "S2400 - Cadastro de Beneficiário - Entes Públicos - Início",
            Tipo::S2405 => "S2405 - Cadastro de Beneficiário - Entes Públicos - Alteração",
            Tipo::S2410 => "S2410 - Cadastro de Benefício - Entes Públicos - Início",
            Tipo::S2416 => "S2416 - Cadastro de Benefício - Entes Públicos - Alteração",
            Tipo::S2420 => "S2420 - Cadastro de Benefício - Entes Públicos - Término",
            Tipo::S2500 => "S2500 - Processo Trabalhista",
            Tipo::S2501 => "S2501 - Informações de Tributos Decorrentes de Processo Trabalhista",
            Tipo::S3000 => "S3000 - Exclusão de eventos",
            Tipo::S3500 => "S3500 - Exclusão de Eventos - Processo Trabalhista",
            Tipo::R1000 => "R1000 - Informações do Contribuinte",
            Tipo::R1070 => "R1070 - Tabela de Processos Administrativos/Judiciais",
            Tipo::R2010 => "R2010 - Retenção Contribuição Previdenciária - Serviços Tomados",
            Tipo::R2020 => "R2020 - Retenção Contribuição Previdenciária - Serviços Prestados",
            Tipo::R2055 => "R2055 - Aquisição de Produção Rural",
            Tipo::R2098 => "R2098 - Reabertura de Eventos Periódicos",
            Tipo::R2099 => "R2099 - Fechamento dos Eventos Periódicos",
            Tipo::R9000 => "R9000 - Exclusão de Eventos",
            Tipo::R4010 => "R4010 - Pagamentos/créditos a beneficiário pessoa física",
            Tipo::R4020 => "R4020 - Pagamentos/créditos a beneficiário pessoa jurídica",
            Tipo::R4040 => "R4040 - Pagamentos/créditos a beneficiários não identificados",
            Tipo::R4099 => "R4099 - Fechamento/reabertura dos eventos da série R-4000"
        );

        if (!empty($layout) && !empty($descricoesArquivos[$layout])) {
            $descricoesArquivos = $descricoesArquivos[$layout];
        }

        return $descricoesArquivos;
    }

    /**
     * @param  $layout
     * @return string|array
     */
    public static function getDescricoesForcadas($layout = null)
    {
        $descricoesArquivos = self::getDescricoes();
        $arquivos = [
            Tipo::S1010,
            Tipo::S1200,
            Tipo::S1202,
            Tipo::S1207,
            Tipo::S1210,
            Tipo::S1280,
            Tipo::S1298,
            Tipo::S1299,
            Tipo::S2200,
            Tipo::S2205,
            Tipo::S2210,
            Tipo::S2220,
            Tipo::S2221,
            Tipo::S2230,
            Tipo::S2240,
            Tipo::S2298,
            Tipo::S2299,
            Tipo::S2300,
            Tipo::S2306,
            Tipo::S2399,
            Tipo::S2400,
            Tipo::S2405,
            Tipo::S2410,
            Tipo::S2416,
            Tipo::S2420,
            Tipo::S2500,
            Tipo::S2501,
            Tipo::S3000,
            Tipo::S3500,
            Tipo::R2010,
            Tipo::R2055,
            Tipo::R4010,
            Tipo::R4020,
            Tipo::R4040
        ];

        $retorno = [];
        foreach ($arquivos as $arquivo) {
            if (!empty($descricoesArquivos[$arquivo])) {
                $retorno[$arquivo] = $descricoesArquivos[$arquivo];
            }
        }

        return $retorno;
    }

    /**
     * Retorna a lista com todos os tipos de arquivos ou apenas o titulo do arquivo selecionado
     *
     * @param      null $tipo
     * @return     array|mixed
     * @deprecated
     * @see        Tipo::getDescricoes()
     */
    public static function getTitulos($tipo = null)
    {
        $s1295 = "S1295 - Solicitação de Totalização para Pagamento em Contingência";
        $s2306 = "S2306 - Trabalhador Sem Vínculo de Emprego/Estatutário - Alteração Contratual";
        $tituloArquivos = array(
            Tipo::EMPREGADOR => "S1000/S1005 - Empregador/Obras",
            Tipo::RUBRICA => "S1010 - Tabela de Rubricas",
            Tipo::LOTACAO_TRIBUTARIA => "S1020 - Tabela de Lotações Tributárias",
            // Tipo::CARGO => "S1030 - Tabela de Cargos/Empregos Públicos",
            // Tipo::FUNCAO => "S1040 - Tabela de Funções/Cargos em Comissão",
            // Tipo::HORARIO => "S1050 - Tabela de Horários/Turnos de Trabalho",
            Tipo::PROCESSOS => "S1070 - Tabela de Processos Administrativos/Judiciais",
            Tipo::REMUNERACAO_RGPS => "S1200 - Remuneração RGPS",
            Tipo::REMUNERACAO_RPPS => "S1202 - Remuneração RPPS",
            Tipo::REMUNERACAO_BENEFICIOS_ENTES_PUBLICOS => "S1207 - Benefícios - Entes Públicos",
            Tipo::PAGAMENTOS_RENDIMENTOS_TRABALHO => "S1210 - Pagamentos de Rendimentos do Trabalho",
            // Tipo::TOTALIZACAO_PAGAMENTOS_CONTINGENCIA => $s1295,
            Tipo::REABERTURA_EVENTOS_PERIODICOS => "S1298 - Reabetura dos Eventos Periódicos",
            Tipo::FECHAMENTO_EVENTOS_PERIODICOS => "S1299 - Fechamento dos Eventos Periódicos",
            // Tipo::CONTRIBUICAO_SINDICAL_PATRONAL => "S1300 - Contribuição Sindical Patronal",
            Tipo::ADMISSAO_PRELIMINAR => "S2190 - Admissão de Trabalhador - Registro Preliminar",
            Tipo::SERVIDOR => "S2200 - Cadastramento Inicial do Vínculo e Admissão/Ingresso de Trabalhador",
            Tipo::ALTERACAO_SERVIDOR => "S2205 - Alteração de Dados Cadastrais do Trabalhador",
            Tipo::ALTERACAO_CONTRATUAL => "S2206 - Alteração de Contrato de Trabalho",
            Tipo::CAT => "S2210 - Comunicação de Acidente de Trabalho",
            Tipo::MONITORAMENTO_SAUDE => "S2220 - Monitoramento da Saúde do Trabalhador",
            Tipo::EXAME_TOXICOLOGICO_MOTORISTA_PROFISSIONAL_EMPREGADO =>
                "S2221 - Exame Toxicológico do Motorista Profissional Empregado",
            Tipo::AFASTAMENTO_TEMPORARIO => "S2230 - Afastamento Temporário",
            Tipo::CESSAO_EXERCICIO => "S2231 - Cessão/Exercício em Outro Órgão",
            Tipo::EXPOSICAO_RISCO => "S2240 - Condições Ambientais do Trabalho - Agentes Nocivos",
            // Tipo::AVISO_PREVIO => "S2250 - Aviso Prévio",
            // Tipo::TRABALHO_INTERMITENTE => "S2260 - Convocação para Trabalho Intermitente",
            Tipo::REINTEGRACAO => "S2298 - Reintegração",
            Tipo::DESLIGAMENTO_SERVIDOR => "S2299 - Desligamento",
            Tipo::TRABALHADOR_SEM_VINCULO => "S2300 - Trabalhador Sem Vínculo de Emprego/Estatutário - Início",
            Tipo::ALTERACAO_TRABALHADOR_SEM_VINCULO => $s2306,
            Tipo::TERMINO_TRABALHADOR_SEM_VINCULO => "S2399 - Trabalhador Sem Vínculo de Emprego/Estatutário - Término",
            Tipo::CADASTRO_BENEFICIARIO => "S2400 - Cadastro de Beneficiário - Entes Públicos - Início",
            Tipo::CADASTRO_BENEFICIARIO_ALTERACAO => "S2405 - Cadastro de Beneficiário - Entes Públicos - Alteração",
            Tipo::CADASTRO_BENEFICIO => "S2410 - Cadastro de Benefício - Entes Públicos - Início",
            Tipo::PROCESSO_TRABALHISTA =>"S2500 - Processo Trabalhista",
            Tipo::TRIBUTO_TRABALHISTA =>"S2501 - Tributo Processo Trabalhista",
            Tipo::EXCLUSAO_EVENTOS => "S3000 - Exclusão de eventos",
            Tipo::EXCLUSAO_PROCESSO_TRABALHISTA =>"S3500 - Tributo Processo Trabalhista",
            Tipo::CONTRIBUINTE => "R1000 - Informações do Contribuinte",
            Tipo::EFD_PROCESSOS => "R1070 - Tabela de Processos Administrativos/Judiciais",
            Tipo::EFD_RETENCOES_SERVICOS_TOMADOS => "R2010 - Retenção Contribuição Previdenciária - Serviços Tomados",
            Tipo::EFD_SERVICOS_PRESTADOS => "R2020 - Retenção Contribuição Previdenciária - Serviços Prestados",
            Tipo::EFD_AQUISICAO_PRODUCAO_RURAL => "R2055 - Aquisição de Produção Rural",
            Tipo::EFD_REABERTURA_EVENTOS => "R2098 - Reabertura de Eventos Periódicos",
            Tipo::EFD_FECHAMENTO_PERIODICOS => "R2099 - Fechamento dos Eventos Periódicos",
            Tipo::EFD_EXCLUSAO_EVENTOS => "R9000 - Exclusão de Eventos",
            Tipo::EFD_PAGAMENTOS_CREDITOS_PF => "R-4010 - Pagamentos/créditos a beneficiário pessoa física",
            Tipo::EFD_PAGAMENTOS_CREDITOS_PJ => "R-4020 - Pagamentos/créditos a beneficiário pessoa jurídica",
            Tipo::EFD_PAGAMENTOS_CREDITOS_NI => "R-4040 - Pagamentos/créditos a beneficiários não identificados",
            Tipo::EFD_REABERTURA_FECH_R4000 => "R-4099 - Fechamento/reabertura dos eventos da série R-4000"
        );

        if (!empty($tipo) && !empty($tituloArquivos[$tipo])) {
            $tituloArquivos = $tituloArquivos[$tipo];
        }

        return $tituloArquivos;
    }

    /**
     * Retorna a constante da formatacao pelo tipo de arquivo selecionado
     *
     * @param  $tipo
     * @return mixed
     */
    public static function getLayout($tipo)
    {
        $tipoArquivos = array(
            Tipo::EMPREGADOR => array(Tipo::S1000),
            Tipo::OBRAS => array(Tipo::S1005),
            Tipo::RUBRICA => array(Tipo::S1010),
            Tipo::LOTACAO_TRIBUTARIA => array(Tipo::S1020),
            Tipo::CARGO => array(Tipo::S1030),
            Tipo::FUNCAO => array(Tipo::S1040),
            Tipo::HORARIO => array(Tipo::S1050),
            Tipo::PROCESSOS => array(Tipo::S1070),
            Tipo::TOTALIZACAO_PAGAMENTOS_CONTINGENCIA => array(Tipo::S1295),
            Tipo::REABERTURA_EVENTOS_PERIODICOS => array(Tipo::S1298),
            Tipo::FECHAMENTO_EVENTOS_PERIODICOS => array(Tipo::S1299),
            Tipo::REMUNERACAO_RGPS => array(Tipo::S1200),
            Tipo::REMUNERACAO_RPPS => array(Tipo::S1202),
            Tipo::REMUNERACAO_BENEFICIOS_ENTES_PUBLICOS => [TIPO::S1207],
            Tipo::PAGAMENTOS_RENDIMENTOS_TRABALHO => array(Tipo::S1210),
            Tipo::CONTRIBUICAO_SINDICAL_PATRONAL => array(Tipo::S1300),
            Tipo::ADMISSAO_PRELIMINAR => array(Tipo::S2190),
            Tipo::SERVIDOR => array(Tipo::S2200),
            Tipo::ALTERACAO_SERVIDOR => array(Tipo::S2205),
            Tipo::ALTERACAO_CONTRATUAL => array(Tipo::S2206),
            Tipo::MONITORAMENTO_SAUDE => array(Tipo::S2220),
            Tipo::AFASTAMENTO_TEMPORARIO => array(Tipo::S2230),
            Tipo::CESSAO_EXERCICIO => array(Tipo::S2231),
            Tipo::EXPOSICAO_RISCO => array(Tipo::S2240),
            Tipo::AVISO_PREVIO => array(Tipo::S2250),
            Tipo::TRABALHO_INTERMITENTE => array(Tipo::S2260),
            Tipo::REINTEGRACAO => array(Tipo::S2298),
            Tipo::DESLIGAMENTO_SERVIDOR => array(Tipo::S2299),
            Tipo::TRABALHADOR_SEM_VINCULO => array(Tipo::S2300),
            Tipo::ALTERACAO_TRABALHADOR_SEM_VINCULO => array(Tipo::S2306),
            Tipo::TERMINO_TRABALHADOR_SEM_VINCULO => array(Tipo::S2399),
            Tipo::CADASTRO_BENEFICIARIO => array(Tipo::S2400),
            Tipo::CADASTRO_BENEFICIARIO_ALTERACAO => array(Tipo::S2405),
            Tipo::CADASTRO_BENEFICIO => array(Tipo::S2410),
            Tipo::ALTERACAO_BENEFICIO => [Tipo::S2416],
            Tipo::BENEFICIO_TERMINO => [Tipo::S2420],
            Tipo::PROCESSO_TRABALHISTA => [Tipo::S2500],
            Tipo::TRIBUTO_TRABALHISTA => [Tipo::S2501],
            Tipo::EXCLUSAO_EVENTOS => array(Tipo::S3000),
            Tipo::EXCLUSAO_PROCESSO_TRABALHISTA => [Tipo::S3500],
            Tipo::CONTRIBUINTE => array(Tipo::R1000),
            Tipo::EFD_PROCESSOS => array(Tipo::R1070),
            Tipo::EFD_RETENCOES_SERVICOS_TOMADOS => array(Tipo::R2010),
            Tipo::EFD_SERVICOS_PRESTADOS => array(Tipo::R2020),
            Tipo::EFD_AQUISICAO_PRODUCAO_RURAL => array(Tipo::R2055),
            Tipo::EFD_REABERTURA_EVENTOS => array(Tipo::R2098),
            Tipo::EFD_FECHAMENTO_PERIODICOS => array(Tipo::R2099),
            Tipo::EFD_EXCLUSAO_EVENTOS => array(Tipo::R9000),
            Tipo::EFD_PAGAMENTOS_CREDITOS_PF => array(Tipo::R4010),
            Tipo::EFD_PAGAMENTOS_CREDITOS_PJ => array(Tipo::R4020),
            Tipo::EFD_PAGAMENTOS_CREDITOS_NI => array(Tipo::R4040),
            Tipo::EFD_REABERTURA_FECH_R4000  => array(Tipo::R4099)
        );

        return $tipoArquivos[$tipo];
    }

    /**
     * @param  $layout
     * @return mixed
     */
    public static function getByLayout($layout)
    {
        $data = array(
            Tipo::S1000 => Tipo::EMPREGADOR,
            Tipo::S1005 => Tipo::OBRAS,
            Tipo::S1010 => Tipo::RUBRICA,
            Tipo::S1020 => Tipo::LOTACAO_TRIBUTARIA,
            Tipo::S1030 => Tipo::CARGO,
            Tipo::S1040 => Tipo::FUNCAO,
            Tipo::S1050 => Tipo::HORARIO,
            Tipo::S1070 => Tipo::PROCESSOS,
            Tipo::S1200 => Tipo::REMUNERACAO_RGPS,
            Tipo::S1202 => Tipo::REMUNERACAO_RPPS,
            Tipo::S1207 => Tipo::REMUNERACAO_BENEFICIOS_ENTES_PUBLICOS,
            Tipo::S1210 => Tipo::PAGAMENTOS_RENDIMENTOS_TRABALHO,
            Tipo::S1295 => Tipo::TOTALIZACAO_PAGAMENTOS_CONTINGENCIA,
            Tipo::S1280 => Tipo::INFORMACOES_COMPLEMENTARES_EVENTOS_PERIODICOS,
            Tipo::S1298 => Tipo::REABERTURA_EVENTOS_PERIODICOS,
            Tipo::S1299 => Tipo::FECHAMENTO_EVENTOS_PERIODICOS,
            Tipo::S1300 => Tipo::CONTRIBUICAO_SINDICAL_PATRONAL,
            Tipo::S2190 => Tipo::ADMISSAO_PRELIMINAR,
            Tipo::S2200 => Tipo::SERVIDOR,
            Tipo::S2205 => Tipo::ALTERACAO_SERVIDOR,
            Tipo::S2206 => Tipo::ALTERACAO_CONTRATUAL,
            Tipo::S2210 => Tipo::CAT,
            Tipo::S2220 => Tipo::MONITORAMENTO_SAUDE,
            Tipo::S2221 => Tipo::EXAME_TOXICOLOGICO_MOTORISTA_PROFISSIONAL_EMPREGADO,
            Tipo::S2230 => Tipo::AFASTAMENTO_TEMPORARIO,
            Tipo::S2231 => Tipo::CESSAO_EXERCICIO,
            Tipo::S2240 => Tipo::EXPOSICAO_RISCO,
            Tipo::S2250 => Tipo::AVISO_PREVIO,
            Tipo::S2260 => Tipo::TRABALHO_INTERMITENTE,
            Tipo::S2298 => Tipo::REINTEGRACAO,
            Tipo::S2299 => Tipo::DESLIGAMENTO_SERVIDOR,
            Tipo::S2300 => Tipo::TRABALHADOR_SEM_VINCULO,
            Tipo::S2306 => Tipo::ALTERACAO_TRABALHADOR_SEM_VINCULO,
            Tipo::S2399 => Tipo::TERMINO_TRABALHADOR_SEM_VINCULO,
            Tipo::S2400 => Tipo::CADASTRO_BENEFICIARIO,
            Tipo::S2405 => Tipo::CADASTRO_BENEFICIARIO_ALTERACAO,
            Tipo::S2410 => Tipo::CADASTRO_BENEFICIO,
            Tipo::S2416 => Tipo::ALTERACAO_BENEFICIO,
            Tipo::S2420 => Tipo::BENEFICIO_TERMINO,
            Tipo::S2500 => Tipo::PROCESSO_TRABALHISTA,
            Tipo::S2501 => Tipo::TRIBUTO_TRABALHISTA,
            Tipo::S3000 => Tipo::EXCLUSAO_EVENTOS,
            Tipo::S3500 => Tipo::EXCLUSAO_PROCESSO_TRABALHISTA,
            Tipo::R1000 => Tipo::CONTRIBUINTE,
            Tipo::R1070 => Tipo::EFD_PROCESSOS,
            Tipo::R2010 => Tipo::EFD_RETENCOES_SERVICOS_TOMADOS,
            Tipo::R2020 => Tipo::EFD_SERVICOS_PRESTADOS,
            Tipo::R2055 => Tipo::EFD_AQUISICAO_PRODUCAO_RURAL,
            Tipo::R2098 => Tipo::EFD_REABERTURA_EVENTOS,
            Tipo::R2099 => Tipo::EFD_FECHAMENTO_PERIODICOS,
            Tipo::R9000 => Tipo::EFD_EXCLUSAO_EVENTOS,
            Tipo::R4010 => Tipo::EFD_PAGAMENTOS_CREDITOS_PF,
            Tipo::R4020 => Tipo::EFD_PAGAMENTOS_CREDITOS_PJ,
            Tipo::R4040 => Tipo::EFD_PAGAMENTOS_CREDITOS_NI,
            Tipo::R4099 => Tipo::EFD_REABERTURA_FECH_R4000
        );

        return $data[$layout];
    }

    /**
     * @param  $tipo
     * @return bool
     */
    public static function existe($tipo)
    {
        $tipoArquivos = array(
            Tipo::EMPREGADOR => array(Tipo::S1000, Tipo::S1005),
            Tipo::RUBRICA => array(Tipo::S1010),
            Tipo::LOTACAO_TRIBUTARIA => array(Tipo::S1020),
            Tipo::CARGO => array(Tipo::S1030),
            Tipo::FUNCAO => array(Tipo::S1040),
            Tipo::HORARIO => array(Tipo::S1050),
            Tipo::PROCESSOS => array(Tipo::S1070),
            Tipo::REMUNERACAO_RGPS => array(Tipo::S1200),
            Tipo::REMUNERACAO_RPPS => array(Tipo::S1202),
            Tipo::REMUNERACAO_BENEFICIOS_ENTES_PUBLICOS => [Tipo::S1207],
            Tipo::PAGAMENTOS_RENDIMENTOS_TRABALHO => array(Tipo::S1210),
            Tipo::INFORMACOES_COMPLEMENTARES_EVENTOS_PERIODICOS => array(Tipo::S1280),
            Tipo::TOTALIZACAO_PAGAMENTOS_CONTINGENCIA => array(Tipo::S1295),
            Tipo::REABERTURA_EVENTOS_PERIODICOS => array(Tipo::S1298),
            Tipo::FECHAMENTO_EVENTOS_PERIODICOS => array(Tipo::S1299),
            Tipo::CONTRIBUICAO_SINDICAL_PATRONAL => array(Tipo::S1300),
            Tipo::ADMISSAO_PRELIMINAR => array(Tipo::S2190),
            Tipo::SERVIDOR => array(Tipo::S2200),
            Tipo::ALTERACAO_SERVIDOR => array(Tipo::S2205),
            Tipo::ALTERACAO_CONTRATUAL => array(Tipo::S2206),
            Tipo::MONITORAMENTO_SAUDE => array(Tipo::S2220),
            Tipo::AFASTAMENTO_TEMPORARIO => array(Tipo::S2230),
            Tipo::CESSAO_EXERCICIO => array(Tipo::S2231),
            Tipo::EXPOSICAO_RISCO => array(Tipo::S2240),
            Tipo::AVISO_PREVIO => array(Tipo::S2250),
            Tipo::TRABALHO_INTERMITENTE => array(Tipo::S2260),
            Tipo::REINTEGRACAO => array(Tipo::S2298),
            Tipo::DESLIGAMENTO_SERVIDOR => array(Tipo::S2299),
            Tipo::TRABALHADOR_SEM_VINCULO => array(Tipo::S2300),
            Tipo::ALTERACAO_TRABALHADOR_SEM_VINCULO => array(Tipo::S2306),
            Tipo::TERMINO_TRABALHADOR_SEM_VINCULO => array(Tipo::S2399),
            Tipo::CADASTRO_BENEFICIARIO => array(Tipo::S2400),
            Tipo::CADASTRO_BENEFICIARIO_ALTERACAO => array(Tipo::S2405),
            Tipo::CADASTRO_BENEFICIO => [Tipo::S2410],
            Tipo::ALTERACAO_BENEFICIO => [Tipo::S2416],
            Tipo::BENEFICIO_TERMINO => [Tipo::S2420],
            Tipo::PROCESSO_TRABALHISTA => [Tipo::S2500],
            Tipo::TRIBUTO_TRABALHISTA => [Tipo::S2501],
            Tipo::EXCLUSAO_EVENTOS => array(Tipo::S3000),
            Tipo::EXCLUSAO_PROCESSO_TRABALHISTA => [Tipo::S3500],
            Tipo::CONTRIBUINTE => array(Tipo::R1000),
            Tipo::EFD_PROCESSOS => array(Tipo::R1070),
            Tipo::EFD_RETENCOES_SERVICOS_TOMADOS => array(Tipo::R2010),
            Tipo::EFD_AQUISICAO_PRODUCAO_RURAL => array(Tipo::R2055),
            Tipo::EFD_SERVICOS_PRESTADOS => array(Tipo::R2020),
            Tipo::EFD_REABERTURA_EVENTOS => array(Tipo::R2098),
            Tipo::EFD_FECHAMENTO_PERIODICOS => array(Tipo::R2099),
            Tipo::EFD_EXCLUSAO_EVENTOS => array(Tipo::R9000),
            Tipo::EFD_PAGAMENTOS_CREDITOS_PF => array(Tipo::R4010),
            Tipo::EFD_PAGAMENTOS_CREDITOS_PJ => array(Tipo::R4020),
            Tipo::EFD_PAGAMENTOS_CREDITOS_NI => array(Tipo::R4040),
            Tipo::EFD_REABERTURA_FECH_R4000  => array(Tipo::R4099)
        );

        return array_key_exists($tipo, $tipoArquivos);
    }

    /**
     * @param  $tipo
     * @return bool
     */
    public static function isEFDReinf($tipo)
    {
        return strpos(self::getTitulos($tipo), 'R') === 0;
    }

    /**
     * @return array
     */
    public static function getExibeCompetencias($reenvio = false, $exclusao = false)
    {
        $default = "Ao não informar a competência, serão processados todos os servidores da competencia atual.";

        $mensagemS2200 = "Ao preencher a competência, serão processados todos os servidores admitidos na competência "
            . "\r\ninformada, caso contrario, serão processados todos o servidores ativos cadastrados no sistema.";

        $mensagemS2205 = "Ao preencher a competência, serão processados todos os servidores alterados na competência "
        . "\r\ninformada, caso contrario, serão processados todos o servidores alterados na competência atual.";

        $msgS2231 = "Ao preencher a competência, serão processados todos os servidores cedidos na competência "
        ."\r\ninformada, caso contrario, serão processados todos os servidores cedidos na competência atual.";

        $mensagemS2299 = "Ao preencher a competência, serão processados todos os servidores rescindidos na competência "
            . "\r\ninformada, caso contrario, serão processados todos o servidores rescindidos na competência atual.";

        $mensagemS2300 = "Ao preencher a competência, serão processados todos os servidores sem vínculo na competência "
            . "\r\ninformada, caso contrario, serão processados todos o servidores sem vínculo ativos cadastrados no "
            . "sistema.";

        $msgS2399 = "Ao preencher a competência, serão processados todos os servidores sem vínculo e rescindidos "
            . "\r\nna competência informada, caso contrario, serão processados todos o servidores sem vínculo "
            . "\r\ne rescindidos na competência atual.";

        $mensagemS2410 = "Ao não informar a competência, serão processados todos os servidores da competencia atual.";

        $mensagemS2416 = "Ao não informar a competência, serão processados todos os servidores atualizados "
            . "na competencia atual.";

        $msg2410Validadacao = "Deseja realmente processar todos os servidores ";
        $msg2410Validadacao .= "inativos e pensionistas da competência atual?";

        $msgS2420 = "Ao preencher a competência, serão processados todos os servidores rescindidos "
            . "\r\nna competência informada, caso contrario, serão processados todos o servidores "
            . "\r\nrescindidos na competência atual.";

        $defaultExclusao = "Ao não informar os filtros abaixo, serão ";
        $defaultExclusao .= "processados todos os servidores da competencia informada. ";

        if ($exclusao) {
            return [
                ["layout" => Tipo::R2010],
                ["layout" => Tipo::R2098],
                ["layout" => Tipo::R2055],
                ["layout" => Tipo::R2099],
                ["layout" => Tipo::R4010],
                ["layout" => Tipo::R4020],
                ["layout" => Tipo::R4040],
                ["layout" => Tipo::R4099],
                [
                    "layout" => Tipo::S1200,
                    "mensagem" => $defaultExclusao,
                    "forcar" => true
                ],
                [
                    "layout" => Tipo::S1202,
                    "mensagem" => $defaultExclusao,
                    "forcar" => true
                ],
                [
                    "layout" => Tipo::S1207,
                    "mensagem" => $defaultExclusao,
                    "forcar" => true
                ],
                ["layout" => Tipo::S1298],
                [
                    "layout" => Tipo::S2220,
                    "mensagem" => $defaultExclusao,
                    "forcar" => true
                ],
                [
                    "layout" => Tipo::S2230,
                    "mensagem" => $defaultExclusao,
                    "forcar" => true
                ],
                ["layout" => Tipo::S2400],
                [
                    "layout" => Tipo::S2410,
                    "mensagem" => $defaultExclusao,
                    "forcar" => true
                ],
                ["layout" => Tipo::S2420]
            ];
        }
        if ($reenvio) {
            return [
                [
                    "layout" => Tipo::S1200,
                    "forcar" => true
                ],
                [
                    "layout" => Tipo::S1202,
                    "forcar" => true
                ],
                [
                    "layout" => Tipo::S1207,
                    "forcar" => true
                ],
                [
                    "layout" => Tipo::S1280,
                    "forcar" => false
                ],
                [
                    "layout" => Tipo::S1298,
                    "forcar" => false
                ],
                [
                    "layout" => Tipo::S1299,
                    "forcar" => false
                ],
                [
                    "layout" => Tipo::S2200,
                    "mensagem" => $mensagemS2200,
                    "validar" => "Deseja realmente processar todos os servidores ativos?",
                    "forcar" => false
                ],
                [
                    "layout" => Tipo::S2205,
                    "mensagem" => $mensagemS2205,
                    "validar" => "Deseja realmente processar todos os servidores ativos?",
                    "forcar" => false
                ],
                [
                    "layout" => Tipo::S2220,
                    "forcar" => false
                ],
                [
                    "layout" => Tipo::S2230,
                    "forcar" => true
                ],
                [   "layout" => Tipo::S2240,
                    "forcar" => false
                ],
                [
                    "layout" => Tipo::S2298,
                    "validar" => "Deseja realmente processar todos os servidores ativos?",
                    "forcar" => false
                ],
                [
                    "layout" => Tipo::S2299,
                    "mensagem" => $mensagemS2299,
                    "validar" => "Deseja realmente processar todos os servidores rescindidos na competência atual?",
                    "forcar" => false
                ],
                [
                    "layout" => Tipo::S2300,
                    "mensagem" => $mensagemS2300,
                    "validar" => "Deseja realmente processar todos os servidores ativos?",
                    "forcar" => false
                ],
                [
                    "layout" => Tipo::S2399,
                    "mensagem" => $msgS2399,
                    "validar" => "Deseja realmente processar todos os servidores
                    rescindidos e sem vínculo na competência atual?",
                    "forcar" => false
                ],
                [
                    "layout" => Tipo::S2410,
                    "mensagem" => $mensagemS2410,
                    "validar" => "Deseja realmente processar todos os servidores
                    rescindidos e sem vínculo na competência atual?",
                    "forcar" => false
                ],
                [
                    "layout" => Tipo::S2416,
                    "mensagem" => $default,
                    "validar" => "Deseja realmente processar todos os servidores
                    aposentados e pensionistas na competência atual?",
                    "forcar" => false
                ],
                [
                    "layout" => Tipo::R2010,
                    "forcar" => false
                ],
                [
                    "layout" => Tipo::R2055,
                    "forcar" => false
                ],
                ["layout" => Tipo::R4010, "forcar" => false],
                ["layout" => Tipo::R4020, "forcar" => false],
                ["layout" => Tipo::R4040, "forcar" => false]
            ];
        }
        return array(
            [
                "layout" => Tipo::R2010,
                "forcar" => false
            ],
            [
                "layout" => Tipo::R2055,
                "forcar" => false
            ],
            [
                "layout" => Tipo::R2098,
                "forcar" => false
            ],
            [
                "layout" => Tipo::R2099,
                "forcar" => false
            ],
            [
                "layout" => Tipo::R4010,
                "forcar" => false
            ],
            [
                "layout" => Tipo::R4020,
                "forcar" => false
            ],
            [
                "layout" => Tipo::R4040,
                "forcar" => false
            ],
            [
                "layout" => Tipo::R4099,
                "forcar" => false
            ],
            [
                "layout" => Tipo::S1200,
                "forcar" => false
            ],
            [
                "layout" => Tipo::S1202,
                "forcar" => false
            ],
            [
                "layout" => Tipo::S1207,
                "forcar" => false
            ],
            [
                "layout" => Tipo::S1280,
                "forcar" => false
            ],
            [
                "layout" => Tipo::S1298,
                "forcar" => false
            ],
            [
                "layout" => Tipo::S1299,
                "forcar" => false
            ],
            [
                "layout" => Tipo::S2200,
                "mensagem" => $mensagemS2200,
                "validar" => "Deseja realmente processar todos os servidores ativos?",
                "forcar" => false
            ],
            [
                "layout" => Tipo::S2205,
                "mensagem" => $mensagemS2200,
                "validar" => "Deseja realmente processar todos os servidores ativos?",
                "forcar" => false
            ],
            [
                "layout" => Tipo::S2220,
                "forcar" => false
            ],
            [
                "layout" => Tipo::S2230,
                "forcar" => true
            ],
            [
                "layout" => Tipo::S2231,
                "mensagem" => $msgS2231,
                "validar" => "Deseja realmente processar todos os servidores cedidos e ativos na competência atual?",
                "forcar" => false
            ],
            [   "layout" => Tipo::S2240,
                "forcar" => false
            ],
            [
                "layout" => Tipo::S2298,
                "mensagem" => $default,
                "validar" => "Deseja realmente processar todos os servidores ativos?",
                "forcar" => false
            ],
            [
                "layout" => Tipo::S2299,
                "mensagem" => $mensagemS2299,
                "validar" => "Deseja realmente processar todos os servidores rescindidos na competência atual?",
                "forcar" => false
            ],
            [
                "layout" => Tipo::S2300,
                "mensagem" => $mensagemS2300,
                "validar" => "Deseja realmente processar todos os servidores ativos?",
                "forcar" => false
            ],
            [
                "layout" => Tipo::S2399,
                "mensagem" => $msgS2399,
                "validar" => "Deseja realmente processar todos os servidores
                rescindidos e sem vínculo na competência atual?",
                "forcar" => false
            ],
            [
                "layout" => Tipo::S2200,
                "mensagem" => $mensagemS2200,
                "validar" => "Deseja realmente processar todos os servidores ativos?",
                "forcar" => false
            ],
            [
                "layout" => Tipo::S2205,
                "mensagem" => $mensagemS2200,
                "validar" => "Deseja realmente processar todos os servidores ativos?",
                "forcar" => false
            ],
            [
                "layout" => Tipo::S2420,
                "mensagem" => $msgS2420,
                "validar" => "Deseja realmente processar todos os servidores rescindidos na competência atual?",
                "forcar" => false
            ],
            [
                "layout" => Tipo::S2400,
                "forcar" => false
            ],
            [
                "layout" => Tipo::S2410,
                "mensagem" => $mensagemS2410,
                "validar" =>  $msg2410Validadacao,
                "forcar" => false
            ],
            [
                "layout" => Tipo::S2416,
                "mensagem" => $mensagemS2416,
                "validar" => $default,
                "forcar" => false
            ],
            [
                "layout" => Tipo::S2420,
                "mensagem" => $msgS2420,
                "validar" => "Deseja realmente processar todos os servidores rescindidos na competência atual?",
                "forcar" => false
            ],
            ["layout" => Tipo::S2500],
            ["layout" => Tipo::S2501]
        );
    }

    /**
     * @return array
     */
    public static function getExibeRubrica($reenvio = false)
    {
        if ($reenvio) {
            return [
                [
                    "layout" => Tipo::S1010,
                    'funcao' => 'func_rhrubricas.php',
                    "validar" => "Nenhuma rubrica informada."
                ]
            ];
        }
        return [
            [
                "layout" => Tipo::S1010,
                'funcao' => 'func_rhrubricas.php',
                "validar" => "Nenhuma rubrica informada."
            ]
        ];
    }
    /**
     * @return array
     */
    public static function getExibeMatricula($reenvio = false, $exclusao = false)
    {
        if ($reenvio || $exclusao) {
            return [
                [
                    "layout" => Tipo::S1200,
                    'funcao' => 'func_rhpessoal.php'
                ],
                [
                    "layout" => Tipo::S1202,
                    'funcao' => 'func_rhpessoal.php'
                ],
                [
                    "layout" => Tipo::S1207,
                    'funcao' => 'func_rhpessoal.php'
                ],
                [
                    "layout" => Tipo::S1210,
                    'funcao' => 'func_rhpessoal.php'

                ],
                [
                    "layout" => Tipo::S2200,
                    'funcao' => 'func_rhpessoal.php',
                    'parametros' => [
                        'vinculados' => true
                    ]
                ],
                [
                    "layout" => Tipo::S2205,
                    'funcao' => 'func_rhpessoal.php'
                ],
                [
                    "layout" => Tipo::S2220,
                    'funcao' => 'func_rhpessoal.php',
                    'parametros' => [
                        'filtro_lotacao' => true
                    ]
                ],
                [
                    "layout" => Tipo::S2240,
                    'funcao' => 'func_rhpessoal.php',
                     'parametros' => [
                         'vinculados' => true
                    ]
                ],
                [
                    "layout" => Tipo::S2298,
                    'funcao' => 'func_rhpessoal.php'
                ],
                [
                    "layout" => Tipo::S2299,
                    'funcao' => 'func_rhpessoal.php',
                    'parametros' => [
                        'vinculados' => true,
                        'sRescindidos' => true
                    ]
                ],
                [
                    "layout" => Tipo::S2300,
                    'funcao' => 'func_rhpessoal.php'
                ],
                [
                    "layout" => Tipo::S2306,
                    'funcao' => 'func_rhpessoal.php'
                ],
                [
                    "layout" => Tipo::S2230,
                    'funcao' => 'func_rhpessoal.php',
                    'parametros' => [
                        'lFormularioAfastamento' => true,
                        'filtro_lotacao' => true
                    ]
                ],
                [
                    "layout" => Tipo::S2399,
                    'funcao' => 'func_rhpessoal.php',
                    'parametros' => [
                        'vinculados' => false,
                        'sRescindidos' => true
                    ]
                ],
                [
                    "layout" => Tipo::S2400,
                    'funcao' => 'func_rhpessoal.php',
                    'parametros' => [
                        'filtro_lotacao' => true
                    ]
                ],
                [
                    "layout" => Tipo::S2405,
                    'funcao' => 'func_rhpessoal.php',
                    'parametros' => [
                        'filtro_lotacao' => true
                    ]
                ],
                [
                    "layout" => Tipo::S2410,
                    'funcao' => 'func_rhpessoal.php'
                ],
                [
                    "layout" => Tipo::S2416,
                    'funcao' => 'func_rhpessoal.php'
                ],
                [
                    "layout" => Tipo::S2420,
                    'funcao' => 'func_rhpessoal.php'
                ],
                [
                    "layout" => Tipo::S2500,
                    'funcao' => 'func_rhpessoal.php'
                ],
                [
                    "layout" => Tipo::S2501,
                    'funcao' => 'func_rhpessoal.php'
                ]
            ];
        }
        return [
            [
                "layout" => Tipo::S2190,
                'funcao' => 'func_rhpessoal.php',
                'parametros' => [
                    'vinculados' => true
                ]
            ],
            [
                "layout" => Tipo::S2205,
                'funcao' => 'func_rhpessoal.php',
                'parametros' => []
            ],
            [
                "layout" => Tipo::S2206,
                'funcao' => 'func_rhpessoal.php',
                'parametros' => [
                    'vinculados' => true
                ]
            ],
            [
                "layout" => Tipo::S2200,
                'funcao' => 'func_rhpessoal.php',
                'parametros' => [
                    'vinculados' => true
                ],
                "validar" => "Nenhuma matrícula informada."
            ],
            [
                "layout" => Tipo::S2205,
                'funcao' => 'func_rhpessoal.php'
            ],
            [
                "layout" => Tipo::S2220,
                'funcao' => 'func_rhpessoal.php',
                'parametros' => [
                     'filtro_lotacao' => true
                 ]
            ],
            [
                "layout" => Tipo::S2300,
                'funcao' => 'func_rhpessoal.php'
            ],
            [
                "layout" => Tipo::S2306,
                'funcao' => 'func_rhpessoal.php'
            ],
            [
                "layout" => Tipo::S2230,
                'funcao' => 'func_rhpessoal.php',
                'parametros' => [
                    'lFormularioAfastamento' => true,
                    'filtro_lotacao' => true
                ]
            ],
            [
                "layout" => Tipo::S2231,
                'funcao' => 'func_rhpessoal.php',
                'parametros' => [
                    'vinculados' => true
                ],
                "validar" => "Nenhuma matrícula informada."
            ],
            [
                "layout" => Tipo::S2240,
                'funcao' => 'func_rhpessoal.php',
                 'parametros' => [
                     'vinculados' => true
                ]
            ],
            [
                "layout" => Tipo::S2298,
                'funcao' => 'func_rhpessoal.php',
                 'parametros' => [
                     'vinculados' => true
                ]
            ],
            [
                "layout" => Tipo::S2299,
                'funcao' => 'func_rhpessoal.php',
                'parametros' => [
                    'vinculados' => true,
                    'sRescindidos' => true
                ]
            ],
            [
                "layout" => Tipo::S2399,
                'funcao' => 'func_rhpessoal.php',
                'parametros' => [
                    'vinculados' => false,
                    'sRescindidos' => true
                ]
            ],
            [
                "layout" => Tipo::S2400,
                'funcao' => 'func_rhpessoal.php',
                'parametros' => [
                   'filtro_lotacao' => true
                ]
            ],
            [
                "layout" => Tipo::S2405,
                'funcao' => 'func_rhpessoal.php',
                'parametros' => [
                    'filtro_lotacao' => true
                ]
            ],
            [
                "layout" => Tipo::S2410,
                'funcao' => 'func_rhpessoal.php'
            ],
            [
                "layout" => Tipo::S2416,
                'funcao' => 'func_rhpessoal.php'
            ],
            [
                "layout" => Tipo::S2420,
                'funcao' => 'func_rhpessoal.php',
                'parametros' => [
                    'sRescindidos' => true
                ]

            ],
            [
                "layout" => Tipo::S2500,
                'funcao' => 'func_rhpessoal.php'
            ],
            [
                "layout" => Tipo::S2501,
                'funcao' => 'func_rhpessoal.php'
            ]
        ];
    }

    /**
    * @return array
    */
    public static function getExibeDataDePreenchimento($reenvio = false)
    {
        if ($reenvio) {
            return [
                ["layout" => Tipo::S2190],
                ["layout" => Tipo::S3000],
                ["layout" => Tipo::S3500]
            ];
        }
        return [
            ["layout" => Tipo::S2190],
            ["layout" => Tipo::S3000],
            ["layout" => Tipo::S3500]
        ];
    }

    /**
     * @return array
     */
    public static function getExibeIndicativoPeriodoApuracao($reenvio = false)
    {
        if ($reenvio) {
            return [
                ["layout" => Tipo::S1200],
                ["layout" => Tipo::S1202],
                ["layout" => Tipo::S1207],
                ["layout" => Tipo::S1280],
                ["layout" => Tipo::S1298],
                ["layout" => Tipo::S1299]
            ];
        }
        return [
            ["layout" => Tipo::S1200],
            ["layout" => Tipo::S1202],
            ["layout" => Tipo::S1207],
            ["layout" => Tipo::S1280],
            ["layout" => Tipo::S1298],
            ["layout" => Tipo::S1299]
        ];
    }

    /**
     * @param  $layout
     * @return string|array
     */
    public static function getDescricoesExclusaoLote($layout = null)
    {
        $descricoesArquivos = self::getDescricoes();
        $arquivos = [
            Tipo::S1200,
            Tipo::S1202,
            Tipo::S1210,
            Tipo::S2220,
            Tipo::S2230,
            Tipo::S2410,
        ];

        $retorno = [];
        foreach ($arquivos as $arquivo) {
            if (!empty($descricoesArquivos[$arquivo])) {
                $retorno[$arquivo] = $descricoesArquivos[$arquivo];
            }
        }

        return $retorno;
    }

    /**
     * @return array
     */
    public static function getExibeFiltro($reenvio = false, $exclusao = false)
    {
        if ($exclusao) {
            return [
                Tipo::S1200 => [
                    "opcoes" => [
                        [
                            'tipo' => 'matricula',
                            'titulo' => "Matrículas"
                        ],
                        [
                            'tipo' => 'selecao',
                            'titulo' => "Seleção"
                        ]
                    ]
                ],
                Tipo::S1202 => [
                    "opcoes" => [
                        [
                            'tipo' => 'matricula',
                            'titulo' => "Matrículas"
                        ],
                        [
                            'tipo' => 'selecao',
                            'titulo' => "Seleção"
                        ]
                    ]
                ],
                Tipo::S1210 => [
                    "opcoes" => [
                        [
                            'tipo' => 'matricula',
                            'titulo' => "Matrículas"
                        ],
                        [
                            'tipo' => 'selecao',
                            'titulo' => "Seleção"
                        ]
                    ]
                ],
                Tipo::S2220 => [
                    "opcoes" => [
                        [
                            'tipo' => 'matricula',
                            'titulo' => "Matrículas"
                        ],
                        [
                            'tipo' => 'selecao',
                            'titulo' => "Seleção"
                        ]
                    ]
                ],
                Tipo::S2230 => [
                    "opcoes" => [
                        [
                            'tipo' => 'matricula',
                            'titulo' => "Matrículas"
                        ],
                        [
                            'tipo' => 'selecao',
                            'titulo' => "Seleção"
                        ]
                    ]
                ],
                Tipo::S2410 => [
                    "opcoes" => [
                        [
                            'tipo' => 'matricula',
                            'titulo' => "Matrículas"
                        ],
                        [
                            'tipo' => 'selecao',
                            'titulo' => "Seleção"
                        ]
                    ]
                ],
                Tipo::S2500 => [
                    "opcoes" => [
                        [
                            'tipo' => 'competencia',
                            'titulo' => "Ano/Mês Decisão"
                        ],
                        [
                            'tipo' => 'matricula',
                            'titulo' => "Matrículas"
                        ],
                        [
                            'tipo' => 'selecao',
                            'titulo' => "Seleção"
                        ]
                    ]
                ],
                Tipo::S2501 => [
                    "opcoes" => [
                        [
                            'tipo' => 'competencia',
                            'titulo' => "Ano/Mês Contemplado"
                        ],
                        [
                            'tipo' => 'matricula',
                            'titulo' => "Matrículas"
                        ],
                        [
                            'tipo' => 'selecao',
                            'titulo' => "Seleção"
                        ]
                    ]
                ]
            ];
        }
        if ($reenvio) {
            return [
                Tipo::S1200 => [
                    "opcoes" => [
                        [
                            'tipo' => 'matricula',
                            'titulo' => "Matrículas"
                        ],
                        [
                            'tipo' => 'selecao',
                            'titulo' => "Seleção"
                        ]
                    ]
                ],
                Tipo::S1202 => [
                    "opcoes" => [
                        [
                            'tipo' => 'matricula',
                            'titulo' => "Matrículas"
                        ],
                        [
                            'tipo' => 'selecao',
                            'titulo' => "Seleção"
                        ]
                    ]
                ],
                Tipo::S1207 => [
                    "opcoes" => [
                        [
                            'tipo' => 'matricula',
                            'titulo' => "Matrículas"
                        ],
                        [
                            'tipo' => 'selecao',
                            'titulo' => "Seleção"
                        ]
                    ]
                ],
                Tipo::S1210 => [
                    "opcoes" => [
                        [
                            'tipo' => 'matricula',
                            'titulo' => "Matrículas"
                        ],
                        [
                            'tipo' => 'selecao',
                            'titulo' => "Seleção"
                        ]
                    ]
                ],
                Tipo::S2200 => [
                    "opcoes" => [
                        [
                            'tipo' => 'matricula',
                            'titulo' => "Matrículas"
                        ],
                        [
                            'tipo' => 'selecao',
                            'titulo' => "Seleção"
                        ]
                    ]
                ],
                Tipo::S2205 => [
                    "opcoes" => [
                        [
                            'tipo' => 'matricula',
                            'titulo' => "Matrículas"
                        ],
                        [
                            'tipo' => 'selecao',
                            'titulo' => "Seleção"
                        ]
                    ]
                ],
                Tipo::S2230 => [
                    "opcoes" => [
                        [
                            'tipo' => 'matricula',
                            'titulo' => "Matrículas"
                        ],
                        [
                            'tipo' => 'selecao',
                            'titulo' => "Seleção"
                        ]
                    ]
                ],
                Tipo::S2231 => [
                    "opcoes" => [
                        [
                            'tipo' => 'matricula',
                            'titulo' => "Matrículas"
                        ],
                        [
                            'tipo' => 'selecao',
                            'titulo' => "Seleção"
                        ]
                    ]
                ],
                Tipo::S2240 => [
                    "opcoes" => [
                        [
                            'tipo' => 'competencia',
                            'titulo' => "Competência"
                        ],
                        [
                            'tipo' => 'matricula',
                            'titulo' => "Matrículas"
                        ],
                        [
                            'tipo' => 'selecao',
                            'titulo' => "Seleção"
                        ]
                    ]
                ],
                Tipo::S2298 => [
                    "opcoes" => [
                        [
                            'tipo' => 'competencia',
                            'titulo' => "Competência"
                        ],
                        [
                            'tipo' => 'matricula',
                            'titulo' => "Matrículas"
                        ],
                        [
                            'tipo' => 'selecao',
                            'titulo' => "Seleção"
                        ]
                    ]
                ],
                Tipo::S2299 => [
                    "opcoes" => [
                        [
                            'tipo' => 'matricula',
                            'titulo' => "Matrículas"
                        ],
                        [
                            'tipo' => 'selecao',
                            'titulo' => "Seleção"
                        ]
                    ]
                ],
                Tipo::S2300 => [
                    "opcoes" => [
                        [
                            'tipo' => 'competencia',
                            'titulo' => "Competência"
                        ],
                        [
                            'tipo' => 'matricula',
                            'titulo' => "Matrículas"
                        ],
                        [
                            'tipo' => 'selecao',
                            'titulo' => "Seleção"
                        ]
                    ]
                ],
                Tipo::S2306 => [
                    "opcoes" => [
                        [
                            'tipo' => 'matricula',
                            'titulo' => "Matrículas"
                        ],
                        [
                            'tipo' => 'selecao',
                            'titulo' => "Seleção"
                        ]
                    ]
                ],
                Tipo::S2399 => [
                    "opcoes" => [
                        [
                            'tipo' => 'matricula',
                            'titulo' => "Matrículas"
                        ],
                        [
                            'tipo' => 'selecao',
                            'titulo' => "Seleção"
                        ]
                    ]
                ],
                Tipo::S2410 => [
                    "opcoes" => [
                        [
                            'tipo' => 'competencia',
                            'titulo' => "Competência"
                        ],
                        [
                            'tipo' => 'matricula',
                            'titulo' => "Matrículas"
                        ],
                        [
                            'tipo' => 'selecao',
                            'titulo' => "Seleção"
                        ]
                    ]
                ],
                Tipo::S2416 => [
                    "opcoes" => [
                        [
                            'tipo' => 'competencia',
                            'titulo' => "Competência"
                        ],
                        [
                            'tipo' => 'matricula',
                            'titulo' => "Matrículas"
                        ],
                        [
                            'tipo' => 'selecao',
                            'titulo' => "Seleção"
                        ]
                    ]
                ],
                Tipo::S2420 => [
                    "opcoes" => [
                        [
                            'tipo' => 'matricula',
                            'titulo' => "Matrículas"
                        ],
                        [
                            'tipo' => 'selecao',
                            'titulo' => "Seleção"
                        ]
                    ]
                ],
                Tipo::S2500 => [
                    "opcoes" => [
                        [
                            'tipo' => 'competencia',
                            'titulo' => "Ano/Mês Decisão"
                        ],
                        [
                            'tipo' => 'matricula',
                            'titulo' => "Matrículas"
                        ],
                        [
                            'tipo' => 'selecao',
                            'titulo' => "Seleção"
                        ]
                    ]
                ],
                Tipo::S2501 => [
                    "opcoes" => [
                        [
                            'tipo' => 'competencia',
                            'titulo' => "Ano/Mês Contemplado"
                        ],
                        [
                            'tipo' => 'matricula',
                            'titulo' => "Matrículas"
                        ],
                        [
                            'tipo' => 'selecao',
                            'titulo' => "Seleção"
                        ]
                    ]
                ]
            ];
        }
        return [
            Tipo::S1010 => [
                "opcoes" => [
                    [
                        'tipo' => '',
                        'titulo' => "Selecione..."
                    ],
                    [
                        'tipo' => 'rubrica',
                        'titulo' => "Rubrica"
                    ]
                ]
            ],
            Tipo::S2200 => [
                "opcoes" => [
                    [
                        'tipo' => 'competencia',
                        'titulo' => "Competência"
                    ],
                    [
                        'tipo' => 'matricula',
                        'titulo' => "Matrículas"
                    ],
                    [
                        'tipo' => 'selecao',
                        'titulo' => "Seleção"
                    ]
                ]
            ],
            Tipo::S2205 => [
                "opcoes" => [
                    [
                        'tipo' => 'competencia',
                        'titulo' => "Competência"
                    ],
                    [
                        'tipo' => 'matricula',
                        'titulo' => "Matrículas"
                    ],
                    [
                        'tipo' => 'selecao',
                        'titulo' => "Seleção"
                    ]
                ]
            ],
            Tipo::S2230 => [
                "opcoes" => [
                    [
                        'tipo' => 'matricula',
                        'titulo' => "Matrículas"
                    ],
                    [
                        'tipo' => 'selecao',
                        'titulo' => "Seleção"
                    ]
                ]
            ],
            Tipo::S2231 => [
                "opcoes" => [
                    [
                        'tipo' => 'competencia',
                        'titulo' => "Competência"
                    ],
                    [
                        'tipo' => 'matricula',
                        'titulo' => "Matrículas"
                    ],
                    [
                        'tipo' => 'selecao',
                        'titulo' => "Seleção"
                    ]
                ]
            ],
            Tipo::S2240 => [
                "opcoes" => [
                    [
                        'tipo' => 'competencia',
                        'titulo' => "Competência"
                    ],
                    [
                        'tipo' => 'matricula',
                        'titulo' => "Matrículas"
                    ],
                    [
                        'tipo' => 'selecao',
                        'titulo' => "Seleção"
                    ]
                ]
            ],
            Tipo::S2298 => [
                "opcoes" => [
                    [
                        'tipo' => 'competencia',
                        'titulo' => "Competência"
                    ],
                    [
                        'tipo' => 'matricula',
                        'titulo' => "Matrículas"
                    ],
                    [
                        'tipo' => 'selecao',
                        'titulo' => "Seleção"
                    ]
                ]
            ],
            Tipo::S2299 => [
                "opcoes" => [
                    [
                        'tipo' => 'competencia',
                        'titulo' => "Competência"
                    ],
                    [
                        'tipo' => 'matricula',
                        'titulo' => "Matrículas"
                    ],
                    [
                        'tipo' => 'selecao',
                        'titulo' => "Seleção"
                    ]
                ]
            ],
            Tipo::S2300 => [
                "opcoes" => [
                    [
                        'tipo' => 'competencia',
                        'titulo' => "Competência"
                    ],
                    [
                        'tipo' => 'matricula',
                        'titulo' => "Matrículas"
                    ],
                    [
                        'tipo' => 'selecao',
                        'titulo' => "Seleção"
                    ]
                ]
            ],
            Tipo::S2306 => [
                "opcoes" => [
                    [
                        'tipo' => 'matricula',
                        'titulo' => "Matrículas"
                    ],
                    [
                        'tipo' => 'selecao',
                        'titulo' => "Seleção"
                    ]
                ]
            ],
            Tipo::S2206 => [
                "opcoes" => [
                    [
                        'tipo' => 'matricula',
                        'titulo' => "Matrículas"
                    ],
                    [
                        'tipo' => 'selecao',
                        'titulo' => "Seleção"
                    ]
                ]
            ],
            Tipo::S2399 => [
                "opcoes" => [
                    [
                        'tipo' => 'competencia',
                        'titulo' => "Competência"
                    ],
                    [
                        'tipo' => 'matricula',
                        'titulo' => "Matrículas"
                    ],
                    [
                        'tipo' => 'selecao',
                        'titulo' => "Seleção"
                    ]
                ]
            ],
            Tipo::S2400 => [
                "opcoes" => [
                    [
                        'tipo' => 'competencia',
                        'titulo' => "Competência"
                    ],
                    [
                        'tipo' => 'matricula',
                        'titulo' => "Matrículas"
                    ],
                    [
                        'tipo' => 'selecao',
                        'titulo' => "Seleção"
                    ]
                ]
            ],
            Tipo::S2405 => [
                "opcoes" => [
                    [
                        'tipo' => 'matricula',
                        'titulo' => "Matrículas"
                    ],
                    [
                        'tipo' => 'selecao',
                        'titulo' => "Seleção"
                    ]
                ]
            ],
            Tipo::S2410 => [
                "opcoes" => [
                    [
                        'tipo' => 'competencia',
                        'titulo' => "Competência"
                    ],
                    [
                        'tipo' => 'matricula',
                        'titulo' => "Matrículas"
                    ],
                    [
                        'tipo' => 'selecao',
                        'titulo' => "Seleção"
                    ]
                ]
            ],
            Tipo::S2416 => [
                "opcoes" => [
                    [
                        'tipo' => 'competencia',
                        'titulo' => "Competência"
                    ],
                    [
                        'tipo' => 'matricula',
                        'titulo' => "Matrículas"
                    ],
                    [
                        'tipo' => 'selecao',
                        'titulo' => "Seleção"
                    ]
                ]
            ],
            Tipo::S2420 => [
                "opcoes" => [
                    [
                        'tipo' => 'competencia',
                        'titulo' => "Competência"
                    ],
                    [
                        'tipo' => 'matricula',
                        'titulo' => "Matrículas"
                    ],
                    [
                        'tipo' => 'selecao',
                        'titulo' => "Seleção"
                    ]
                ]
            ],
            Tipo::S2500 => [
                "opcoes" => [
                    [
                        'tipo' => 'competencia',
                        'titulo' => "Ano/Mês Decisão"
                    ],
                    [
                        'tipo' => 'matricula',
                        'titulo' => "Matrículas"
                    ],
                    [
                        'tipo' => 'selecao',
                        'titulo' => "Seleção"
                    ]
                ]
            ],
            Tipo::S2501 => [
                "opcoes" => [
                    [
                        'tipo' => 'competencia',
                        'titulo' => "Ano/Mês Contemplado"
                    ],
                    [
                        'tipo' => 'matricula',
                        'titulo' => "Matrículas"
                    ],
                    [
                        'tipo' => 'selecao',
                        'titulo' => "Seleção"
                    ]
                ]
            ]
        ];
    }
    /**
     * @return array
     */
    public static function getExibeSelecao($reenvio = false, $exclusao = false)
    {
        $default = "Seleção não informada.";
        if ($exclusao) {
            return [
                [
                    "layout" => Tipo::S1200,
                    "validar" => $default
                ],
                [
                    "layout" => Tipo::S1202,
                    "validar" => $default
                ],
                [
                    "layout" => Tipo::S1210,
                    "validar" => $default
                ],
                [
                    "layout" => Tipo::S2220,
                    "validar" => $default
                ],
                [
                    "layout" => Tipo::S2230,
                    "validar" => $default
                ],
                [
                    "layout" => Tipo::S2410,
                    "validar" => $default
                ],
                [
                    "layout" => Tipo::S2500,
                    "validar" => $default
                ],
                [
                    "layout" => Tipo::S2501,
                    "validar" => $default
                ]
            ];
        }
        if ($reenvio) {
            return [
                [
                    "layout" => Tipo::S1200,
                    "validar" => $default
                ],
                [
                    "layout" => Tipo::S1202,
                    "validar" => $default
                ],
                [
                    "layout" => Tipo::S1207,
                    "validar" => $default
                ],
                [
                    "layout" => Tipo::S1210,
                    "validar" => $default
                ],
                [
                    "layout" => Tipo::S2200,
                    "validar" => $default
                ],
                [
                    "layout" => Tipo::S2205,
                    "validar" => $default
                ],
                [
                    "layout" => Tipo::S2230,
                    "validar" => $default
                ],
                [
                    "layout" => Tipo::S2240,
                    "validar" => $default
                ],
                [
                    "layout" => Tipo::S2298,
                    "validar" => $default
                ],
                [
                    "layout" => Tipo::S2299,
                    "validar" => $default
                ],
                [
                    "layout" => Tipo::S2300,
                    "validar" => $default
                ],
                [
                    "layout" => Tipo::S2306,
                    "validar" => $default
                ],
                [
                    "layout" => Tipo::S2399,
                    "validar" => $default
                ],
                [
                    "layout" => Tipo::S2410,
                    "validar" => $default
                ],
                [
                    "layout" => Tipo::S2416,
                    "validar" => $default
                ],
                [
                    "layout" => Tipo::S2420,
                    "validar" => $default
                ],
                [
                    "layout" => Tipo::S2500,
                    "validar" => $default
                ]
            ];
        }
        return [
            [
                "layout" => Tipo::S1200
            ],
            [
                "layout" => Tipo::S1202
            ],
            [
                "layout" => Tipo::S1207
            ],
            [
                "layout" => Tipo::S1210
            ],
            [
                "layout" => Tipo::S2200,
                "validar" => $default
            ],
            [
                "layout" => Tipo::S2205,
                "validar" => $default
            ],
            [
                "layout" => Tipo::S2206,
                "validar" => $default
            ],
            [
                "layout" => Tipo::S2230,
                "validar" => $default
            ],
            [
                "layout" => Tipo::S2231,
                "validar" => $default
            ],
            [
                "layout" => Tipo::S2240,
                "validar" => $default
            ],
            [
                "layout" => Tipo::S2298,
                "validar" => $default
            ],
            [
                "layout" => Tipo::S2299,
                "validar" => $default
            ],
            [
                "layout" => Tipo::S2300,
                "validar" => $default
            ],
            [
                "layout" => Tipo::S2306,
                "validar" => $default
            ],
            [
                "layout" => Tipo::S2399,
                "validar" => $default
            ],
            [
                "layout" => Tipo::S2400,
                "validar" => $default
            ],
            [
                "layout" => Tipo::S2405,
                "validar" => $default
            ],
            [
                "layout" => Tipo::S2410,
                "validar" => $default
            ],
            [
                "layout" => Tipo::S2416,
                "validar" => $default
            ],
            [
                "layout" => Tipo::S2420,
                "validar" => $default
            ],
            [
                "layout" => Tipo::S2500,
                "validar" => $default
            ]
        ];
    }

    /**
     * @return array
     */
    public static function getExibeTipoDataPagamento()
    {
        return [];
    }

    /**
     * @return array
     */
    public static function getExibeForcarMatricula($reenvio = false)
    {
        if ($reenvio) {
            return [
                Tipo::S1200,
                Tipo::S1202,
                Tipo::S1210
            ];
        }
        return [];
    }

    /**
     * @return array
     */
    public static function getExibePeriodoData($reenvio = false, $exclusao = false)
    {
        if ($exclusao) {
            return [];
        }
        if ($reenvio) {
            return [['layout' => Tipo::S2210 ], ['layout' => Tipo::S2221 ]];
        }
        return [['layout' => Tipo::S2210 ], ['layout' => Tipo::S2221 ]];
    }

    /**
     * @return array
     */
    public static function getExibeCaixa($reenvio = false, $exclusao = false)
    {
        $default = "Ao não informar a competência, serão processados todos os servidores da competencia atual.";


        if ($exclusao) {
            return [
                Tipo::S1210
            ];
        }
        if ($reenvio) {
            return [
                [
                    "layout" => Tipo::S1210,
                    "forcar" => true
                ]
            ];
        }
        return [
            [
                "layout" => Tipo::S1210,
                "forcar" => false
            ]
            ];
    }
}
