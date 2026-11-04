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

class Registro30Mapper extends Mapper
{
    protected $dePara = array(
        "Tipo de registro" => "tipoRegistro ",
        "Código de escola - Inep" => "codigoInepEscola",
        "Código da pessoa física no sistema próprio" => "codigoPessoa",
        "Identificação única (Inep)" => "codigoInep",
        "Número do CPF" => "cpf",
        "Nome completo" => "nome",
        "Data de nascimento" => "dataNascimento",
        "Filiação" => "filiacao",
        "Filiação 1 (preferencialmente o nome da mãe)" => "filiacao1",
        "Filiação 2 (preferencialmente o nome do pai)" => "filiacao2",
        "Sexo" => "sexo",
        "Cor/Raça" => "corRaca",
        "Nacionalidade" => "nacionalidade",
        "País de nacionalidade" => "paisNacionalidade",
        "Município de nascimento" => "municipioNascimento",
        "Pessoa física com deficiência, transtorno do espectro autista ou altas habilidades/ superdotação"
        => "deficienciaOuAltismoOuSuperdotacao",
        "Cegueira" => "cegueira",
        "Visão monocular" => "visaoMonocular",
        "Baixa visão" => "baixaVisao",
        "Surdez" => "surdez",
        "Deficiência auditiva" => "deficienciaAuditiva",
        "Surdocegueira" => "surdocegueira",
        "Deficiência física" => "deficienciaFisica",
        "Deficiência intelectual" => "deficienciaintelectual",
        "Deficiência múltipla" => "deficienciaMultipla",
        "Transtorno do espectro autista" => "transtornoAutista",
        "Altas habilidades/ superdotação" => "superdotacao",
        "Auxílio ledor" => "auxilioLedor",
        "Auxílio transcrição" => "auxilioTranscricao",
        "Guia-Intérprete" => "guiaInterprete",
        "Tradutor-Intérprete de Libras" => "tradutorInterpreteLibras",
        "Leitura Labial" => "leituraLabial",
        "Prova Ampliada (Fonte 18)" => "provaAmpliada",
        "Prova superampliada (Fonte 24)" => "provaSuperampliada",
        "CD com áudio para deficiente visual " => "audioDeficienteVisual",
        "Prova de Língua Portuguesa como Segunda Língua para surdos e deficientes auditivos"
        => "provaLinguaPortuguesaSegundaLingua",
        "Prova em Vídeo em Libras" => "provaVideoLibras",
        "Material didático e prova em Braille" => "provaBraille",
        "Nenhum Recurso" => "nenhumRecurso",
        "Número da matrícula da certidão de nascimento (certidão nova)" => "certidaoNascimento",
        "País de residência" => "paisResidencia",
        "CEP" => "cep",
        "Município de residência" => "municipioResidencia",
        "Município de residência" => "municipioResidencia",
        "Localização/ Zona de residência" => "zonaResidencia",
        "Localização diferenciada" => "localizacaoDiferenciada",
        "Maior nível de escolaridade concluída" => "escolaridade",
        "Tipo de ensino médio cursado" => "tipoEnsinoMedio",
        "Código do Curso 1" => "codigoCurso1",
        "Ano de Conclusão  1" => "anoConclusao1",
        "Instituição de educação superior 1" => "instituicaoSuperior1",
        "Código do Curso 2" => "codigoCurso2",
        "Ano de Conclusão  2" => "anoConclusao2",
        "Instituição de educação superior 2" => "instituicaoSuperior2",
        "Código do Curso 3" => "codigoCurso3",
        "Ano de Conclusão  3" => "anoConclusao3",
        "Instituição de educação superior 3" => "instituicaoSuperior3",
        "Área do conhecimento/ componentes curriculares 1" => "componenteCurricular1",
        "Área do conhecimento/ componentes curriculares 2" => "componenteCurricular2",
        "Área do conhecimento/ componentes curriculares 3" => "componenteCurricular3",
        "Tipo de pós-graduação 1" => "tipoPos1",
        "Área da pós-graduação 1" => "areaPos1",
        "Ano de conclusão da pós-graduação 1" => "anoConclusaoPos1",
        "Tipo de pós-graduação 2" => "tipoPos2",
        "Área da pós-graduação 2" => "areaPos2",
        "Ano de conclusão da pós-graduação 2" => "anoConclusaoPos2",
        "Tipo de pós-graduação 3" => "tipoPos3",
        "Área da pós-graduação 3" => "areaPos3",
        "Ano de conclusão da pós-graduação 3" => "anoConclusaoPos3",
        "Tipo de pós-graduação 4" => "tipoPos4",
        "Área da pós-graduação 4" => "areaPos4",
        "Ano de conclusão da pós-graduação 4" => "anoConclusaoPos4",
        "Tipo de pós-graduação 5" => "tipoPos5",
        "Área da pós-graduação 5" => "areaPos5",
        "Ano de conclusão da pós-graduação 5" => "anoConclusaoPos5",
        "Tipo de pós-graduação 6" => "tipoPos6",
        "Área da pós-graduação 6" => "areaPos6",
        "Ano de conclusão da pós-graduação 6" => "anoConclusaoPos6",
        "Não tem pós-graduação concluída" => "nenhumaPos",
        "Creche (0 a 3 anos)" => "creche",
        "Pré-escola (4 e 5 anos)" => "preEscola",
        "Anos iniciais do ensino fundamental" => "anosIniciais",
        "Anos finais do ensino fundamental" => "anosFinais",
        "Ensino médio" => "ensinoMedio",
        "Educação de jovens e adultos" => "eja",
        "Educação especial" => "educacaoEspecial",
        "Educação Indígena" => "educacaoIndigena",
        "Educação do campo" => "educacaoCampo",
        "Educação ambiental" => "educacaoAmbiental",
        "Educação em direitos humanos" => "educacaoDireitosHumanos",
        "Educação bilíngue de surdos" => "educacaoBilingueSurdos",
        "Educação e Tecnologia de Informação e Comunicação (TIC)" => "educacaoTIC",
        "Gênero e diversidade sexual" => "generoDiversidadeSexual",
        "Direitos de criança e adolescente" => "direitosCriancaAdolescente",
        "Educação para as relações étnico-raciais e História e cultura Afro-Brasileira e Africana"
        => "educacaoEtnicoRaciais",
        "Gestão Escolar" => "gestaoEscolar",
        "Outros" => "outros",
        "Nenhum Curso" => "nenhumCurso",
        "Endereço Eletrônico (e-mail)" => "email"
    );
}
