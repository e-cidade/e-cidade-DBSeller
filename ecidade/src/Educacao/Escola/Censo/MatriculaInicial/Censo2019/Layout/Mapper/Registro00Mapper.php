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

namespace ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Layout\Mapper;

class Registro00Mapper extends Mapper
{
    protected $dePara = array(
        "Tipo de registro" => "registro",
        "Código de escola - Inep" => "codigoInep",
        "Situação de funcionamento" => "situacaoFuncionamento",
        "Data de início do ano letivo" => "dataIncicioAnoLetivo",
        "Data de término do ano letivo" => "dataFinalAnoLetivo",
        "Nome da escola" => "nomeEscola",
        "CEP" => "cep",
        "Município" => "municipio",
        "Distrito" => "distrito",
        "Endereço" => "endereco",
        "Número" => "numero",
        "Complemento" => "complemento",
        "Bairro" => "bairro",
        "DDD" => "ddd",
        "Telefone" => "telefone",
        "Outro telefone de contato" => "outroTelefone",
        "Endereço eletrônico (e-mail) da escola" => "email",
        "Código do órgão regional de ensino" => "codigoOrgaoRegionalEnsino",
        "Localização/Zona da escola" => "zonaEscola",
        "Localização diferenciada da escola" => "localizacaoDiferenciada",
        "Dependência administrativa" => "dependenciaAdministrativa",
        "Secretaria de Educação/Ministério da Educação" => "secretariaEducacao",
        "Secretaria de Segurança Pública/Forças Armadas/Militar" => "secretariaSeguranca",
        "Secretaria da Saúde/Ministério da Saúde" => "secretariaSaude",
        "Outro órgão da administração pública" => "outroOrgaoAdministracaoPublica",
        "Empresa, grupos empresariais do setor privado ou pessoa física" => "mantenedoraPrivada",
        "Sindicatos de trabalhadores ou patronais, associações, cooperativas" => "mantenedoraSindicatos",
        "Organização não governamental (ONG) - nacional ou internacional" => "mantenedoraOng",
        "Instituição sem fins lucrativos" => "mantenedoraInstituicaoSemFimLucrativo",
        "Sistema S (Sesi, Senai, Sesc, outros)" => "mantenedoraSistemaS",
        "Organização da Sociedade Civil de Interesse Público (Oscip)" => "mantenedoraOscip",
        "Categoria da escola privada" => "categoriaEscolaPrivada",
        "Secretaria estadual" => "secretariaEstadual",
        "Secretaria Municipal" => "secretariaMunicipal",
        "Não possui parceria ou convênio" => "naoPossuiConvenio",

        "Termo de colaboração (Lei nº 13.019/2014)" => "termoColaboracao",
        "Termo de fomento (Lei nº 13.019/2014)" => "termoFormento",
        "Acordo de cooperação (Lei nº 13.019/2014)" => "acordoCooperacao",
        "Contrato de prestação de serviço" => "contratoPrestacaoServico",
        "Termo de cooperação técnica e financeira" => "termoCooperacaoTecnica",
        "Contrato de consórcio público/Convênio de cooperação" => "coonvenioCooperacao",

        "Termo de colaboração - (Lei nº 13.019/2014)" => "termoColaboracaoMunicipal",
        "Termo de fomento - (Lei nº 13.019/2014)" => "termoFormentoMunicipal",
        "Acordo de cooperação - (Lei nº 13.019/2014)" => "acordoCooperacaoMunicipal",
        "Contrato de prestação de serviço municipal" => "contratoPrestacaoServicoMunicipal",
        "Termo de cooperação técnica e financeira municipaç" => "termoCooperacaoTecnicaMunicipal",
        "Contrato de consórcio público/Convênio de cooperação municipal " => "coonvenioCooperacaoMunicipal",

//    "Atividade complementar" => "atividadeComplementar",
//    "Atendimento educacional especializado" => "atividadeEducacional",
//    "Ensino Regular - Creche - Parcial" => "crecheParcial",
//    "Ensino Regular - Creche - Integral" => "crecheIntegral",
//    "Ensino Regular - Pré-escola - Parcial" => "preEscolaParcial",
//    "Ensino Regular - Pré-escola - Integral" => "preEscolaIntegral",
//    "Ensino Regular - Ensino Fundamental - Anos Iniciais - Parcial" => "FundamentalInicialParcial",
//    "Ensino Regular - Ensino Fundamental - Anos Iniciais - Integral" => "FundamentalInicialIntegral",
//    "Ensino Regular - Ensino Fundamental - Anos Finais - Parcial" => "FundamentalFinalParcial",
//    "Ensino Regular - Ensino Fundamental - Anos Finais - Integral" => "FundamentalFinalIntegral",
//
//    "Ensino Regular - Ensino Médio - Parcial" => "medioParcial",
//    "Ensino Regular - Ensino Médio - Integral" => "medioIntegral",
//    "Educação Especial - Classe especial - Parcial" => "especialParcial",
//    "Educação Especial - Classe especial - Integral" => "especialIntegral",
//    "Educação de Jovens e Adultos (EJA) - Ensino fundamental" => "ejaFundamental",
//    "Educação de Jovens e Adultos (EJA) - Ensino médio" => "ejaMedio",
//
//    "Educação Profissional - Qualificação profissional - Integrada à educação de jovens e " .
//    "adultos no ensino fundamental - Parcial" => "ProfissionalQualificacaoEnsinoFundamentalParcial",
//    "Educação Profissional - Qualificação profissional - Integrada à educação de jovens e " .
//    "adultos no ensino fundamental - Integral" => "ProfissionalQualificacaoEnsinoFundamentalIntegral",
//
//    "Educação Profissional - Qualificação profissional técnica - Integrada à educação de jovens e " .
//    "adultos de nível médio - Parcial" => "ProfissionalQualificacaoNivelMedioParcial",
//    "Educação Profissional - Qualificação profissional técnica - Integrada à educação de jovens e " .
//    "adultos de nível médio - Integral" => "ProfissionalQualificacaoNivelMedioIntegral",
//    "Educação Profissional - Qualificação profissional técnica - Concomitante à educação de jovens e " .
//    "adultos de nível médio - Parcial" => "ProfissionalQualificacaoConcomitanteNivelMedioParcial",
//    "Educação Profissional - Qualificação profissional técnica - Concomitante à educação de jovens e" .
//    "adultos de nível médio - Integral" => "ProfissionalQualificacaoConcomitanteNivelMedioIntegral",
//    "Educação Profissional - Qualificação profissional técnica - Concomitante intercomplementar à educação de " .
//    "jovens e adultos de nível médio - Parcial" => "ProfissionalQualificacaoIntercomplementarNivelMedioParcial",
//    "Educação Profissional - Qualificação profissional técnica - Concomitante intercomplementar à educação de " .
//    "jovens e adultos de nível médio - Integral" => "ProfissionalQualificacaoIntercomplementarNivelMedioIntegral",
//
//    "Educação Profissional - Qualificação profissional técnica - Integrada ao ensino médio - Parcial" =>
//    "ProfissionalQualificacaoEnsinoMedioParcial",
//    "Educação Profissional - Qualificação profissional técnica - Integrada ao ensino médio - Integral" =>
//    "ProfissionalQualificacaoEnsinoIntegral",
//    "Educação Profissional - Qualificação profissional técnica - Concomitante ao ensino médio - Parcial" =>
//    "ProfissionalQualificacaoConcomitanteEnsinoMedioParcial",
//    "Educação Profissional - Qualificação profissional técnica - Concomitante ao ensino médio - Integral" =>
//    "ProfissionalQualificacaoConcomitanteEnsinoMedioIntegral",
//    "Educação Profissional - Qualificação profissional técnica - Concomitante intercomplementar ao ensino médio " .
//    "- Parcial" => "ProfissionalQualificacaoIntercomplementarEnsinoMedioParcial",
//    "Educação Profissional - Qualificação profissional técnica - Concomitante intercomplementar ao ensino médio " .
//    "- Integral" => "ProfissionalQualificacaoIntercomplementarEnsinoMedioIntegral",
//    "Educação Profissional - Educação profissional técnica de nível médio - Integrada ao ensino médio - Parcial" =>
//    "ProfissionalProfissionalEnsinoMedioParcial",
//    "Educação Profissional - Educação profissional técnica de nível médio - Integrada ao ensino médio - Integral" =>
//     "ProfissionalProfissionalEnsinoIntegral",
//    "Educação Profissional - Educação profissional técnica de nível médio - Concomitante ao ensino médio - Parcial"
//    => "ProfissionalProfissionalConcomitanteEnsinoMedioParcial",
//    "Educação Profissional - Educação profissional técnica de nível médio - Concomitante ao ensino médio - Integral"
//     => "ProfissionalProfissionalConcomitanteEnsinoMedioIntegral",
//    "Educação Profissional - Educação profissional técnica de nível médio - Concomitante intercomplementar ao " .
//    "ensino médio - Parcial" => "ProfissionalProfissionalIntercomplementarEnsinoMedioParcial",
//    "Educação Profissional - Educação profissional técnica de nível médio - Concomitante intercomplementar ao " .
//    "ensino médio - Integral" => "ProfissionalProfissionalIntercomplementarEnsinoMedioIntegral",
//    "Educação Profissional - Educação profissional técnica de nível médio - Subsequente ao ensino médio" =>
//    "ProfissionalProfissionalSubsequente",
//
//    "Educação Profissional - Educação profissional técnica de nível médio - Integrada à educação de jovens e " .
//    "adultos de nível médio - Parcial" => "ProfissionalProfissionalNivelMedioParcial",
//    "Educação Profissional - Educação profissional técnica de nível médio - Integrada à educação de jovens e " .
//    "adultos de nível médio - Integral" => "ProfissionalProfissionalNivelMedioIntegral",
//    "Educação Profissional - Educação profissional técnica de nível médio - Concomitante à educação de jovens e " .
//    "adultos de nível médio - Parcial" => "ProfissionalProfissionalConcomitanteNivelMedioParcial",
//    "Educação Profissional - Educação profissional técnica de nível médio - Concomitante à educação de jovens e " .
//    "adultos de nível médio - Integral" => "ProfissionalProfissionalConcomitanteNivelMedioIntegral",
//    "Educação Profissional - Educação profissional técnica de nível médio - Concomitante intercomplementar à " .
//    "educação de jovens e adultos de nível médio - Parcial"
//    => "ProfissionalProfissionalIntercomplementarNivelMedioParcial",
//    "Educação Profissional - Educação profissional técnica de nível médio - Concomitante intercomplementar à " .
//    "educação de jovens e adultos de nível médio - Integral"
//    => "ProfissionalProfissionalIntercomplementarNivelMedioIntegral",


        "CNPJ da mantenedora principal da escola privada" => "cnpjMantenedoraPrincipal",
        "Número do CNPJ da escola privada" => "cnpjEscolaPrivada",
        "Regulamentação/autorização no conselho ou órgão municipal,
        estadual ou federal de educação" => "regulamentacao",
        "Federal" => "esferaFederal",
        "Estadual" => "esferaEstadual",
        "Municipal" => "esferaMunicipal",
        "Unidade vinculada à escola de educação básica ou unidade ofertante de educação superior" => "unidadeVinculada",
        "Código da Escola Sede" => "codigoEscolaSede",
        "Código da IES" => "codigoIES",
    );
}
