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

class Registro10Mapper extends Mapper
{
    protected $dePara = array(
        "Tipo de registro" => "tipoRegistro",
        "Código de escola - Inep" => "codigoInep",
        "Prédio escolar" => "predioEscolar",
        "Sala(s) em outra escola" => "salaOutraEscola",
        "Galpão/ rancho/ paiol/ barracão" => "galpaoRanchoPaiolBarracao",
        "Unidade de atendimento Socioeducativa" => "unidadeAtendimentoSocioeducativa",
        "Unidade Prisional" => "unidadePrisional",
        "Local Outros" => "outroLocal",
        "Forma de ocupação do prédio" => "formaOcupacaoPredio",
        "Prédio escolar compartilhado com outra escola" => "predioEscolarCompartilhado",
        "Código da escola com a qual compartilha (1)" => "codigoEscolaCompartilha1",
        "Código da escola com a qual compartilha (2)" => "codigoEscolaCompartilha2",
        "Código da escola com a qual compartilha (3)" => "codigoEscolaCompartilha3",
        "Código da escola com a qual compartilha (4)" => "codigoEscolaCompartilha4",
        "Código da escola com a qual compartilha (5)" => "codigoEscolaCompartilha5",
        "Código da escola com a qual compartilha (6)" => "codigoEscolaCompartilha6",
        "Fornece água potável para o consumo humano" => "forneceAguaPotavel",
        "Agua Rede pública" => "aguaRedePublica",
        "Poço artesiano" => "pocoArtesiano",
        "Cacimba/ cisterna / poço" => "cacimbaCisternaPoco",
        "Fonte/ rio / igarapé/ riacho/ córrego" => "fonteRio",
        "Carro-pipa" => "carroPipa",
        "Não há abastecimento de água" => "semAgua",
        "Luz Rede pública" => "luzRedePublica",
        "Gerador movido a combustível fóssil" => "geradorCombustivelFossil",
        "Fontes de energia renováveis ou alternativas (gerador a biocombustível e/ou biodigestores, eólica, solar, ".
        "outras)" => "energiaRenovavel",
        "Não há energia elétrica" => "semEnergiaEletrica",
        "Esgoto Rede pública" => "esgotoRedePublica",
        "Fossa séptica" => "fossaSeptica",
        "Fossa rudimentar/comum" => "fossaRudimentar",
        "Não há esgotamento sanitário" => "semEsgotamentoSanitario",
        "Serviço de coleta" => "servicoColeta",
        "Queima" => "queimaLixo",
        "Enterra" => "enterraLixo",
        "Leva a uma destinação final licenciada pelo poder público" => "levaLixo",
        "Descarta em outra área" => "descartaLixo",
        "Separação do lixo/resíduos" => "separacaoLixo",
        "Reaproveitamento/reutilização" => "reaproveitamentoLixo",
        "Reciclagem" => "reciclagemLixo",
        "Não faz tratamento" => "naoTrataLixo",
        "Almoxarifado" => "almoxarifado",
        "Área de vegetação ou gramado" => "areaVegetacaoGramado",
        "Auditório" => "auditorio",
        "Banheiro" => "banheiro",
        "Banheiro acessível adequado ao uso de pessoas com deficiência ou mobilidade ".
        "reduzida" => "banheiroAcessivelPessoasDeficiencia",
        "Banheiro adequado à educação infantil" => "banheiroEducacaoInfantil",
        "Banheiro exclusivo para os funcionários" => "banheiroExclusivoFuncionarios",
        "Banheiro ou vestiário com chuveiro" => "banheiroComChuveiro",
        "Biblioteca" => "biblioteca",
        "Cozinha" => "cozinha",
        "Despensa" => "despensa",
        "Dormitório de aluno(a)" => "dormitorioAluno",
        "Dormitório de professor(a)" => "dormitorioProfessor",
        "Laboratório de ciências" => "laboratorioCiencias",
        "Laboratório de informática" => "laboratorioInformatica",
        "Laboratório específico para a educação
        profissional" => "laboratorioEducacaoProfissional",
        "Parque infantil" => "parqueInfantil",
        "Pátio coberto" => "patiocoberto",
        "Pátio descoberto" => "patiodescoberto",
        "Piscina" => "piscina",
        "Quadra de esportes coberta" => "quadraEsportesCoberta",
        "Quadra de esportes descoberta" => "quadraEsportesDescoberta",
        "Refeitório" => "refeitorio",
        "Sala de repouso para aluno(a)" => "salaRepousoAluno",
        "Sala/ateliê de artes" => "atelieArtes",
        "Sala de música/coral" => "salaMusica",
        "Sala/estúdio de dança" => "salaDanca",
        "Sala multiuso (música, dança e artes)" => "salaMultiuso",
        "Terreirão (área para prática desportiva e recreação sem cobertura, sem piso e sem edificações)" => "terreirao",
        "Viveiro/criação de animais" => "viveiro",
        "Sala de diretoria" => "salaDiretoria",
        "Sala de Leitura" => "salaLeitura",
        "Sala de professores" => "salaProfessores",
        "Sala de recursos multifuncionais para atendimento educacional especializado " .
        "(AEE)" => "salaRecursosMultifuncionaisAEE",
        "Sala de Secretaria" => "salaSecretaria",
        "Salas de oficinas da educação profissional" => "salaEducacaoProfissional",
        "Estúdio de gravação e edição" => "salasGravacaoEdicao",
        "Área de horta, plantio e/ou produção agrícola" => "areaHortaPlantioProducaoAgricola",
        "Nenhuma das dependências relacionadas" => "nenhumaDependencias",
        "Corrimão e guarda-corpos" => "corrimao",
        "Elevador" => "elevador",
        "Pisos táteis" => "pisoTatil",
        "Portas com vão livre de no mínimo 80 cm" => "portasComVao80Cm",
        "Rampas" => "rampas",
        "Sinalização/alarme luminoso" => "sinalizacaoAlarmeLuminoso",
        "Sinalização sonora" => "sinalizacaoSonora",
        "Sinalização tátil" => "sinalizacaoTatil",
        "Sinalização visual (piso/paredes)" => "sinalizacaoVisual",
        "Nenhum dos recursos de acessibilidade listados" => "nenhumRecursosAcessibilidade",
        "Número de salas de aula utilizadas na escola dentro do prédio escolar" => "numeroSalasDentroPredioEscolar",
        "Número de salas de aula utilizadas na escola fora do prédio escolar" => "numeroSalasForaPredioEscolar",
        "Número de salas de aula climatizadas (ar condicionado, aquecedor ou climatizador)" => "numeroSalasClimatizada",
        "Número de salas de aula com acessibilidade para pessoas com deficiência ou mobilidade" .
        " reduzida" => "numeroSalasComAcessibilidade",
        "Antena parabólica" => "antenaParabolica",
        "Computadores" => "computador",
        "Copiadora" => "copiadora",
        "Impressora" => "impressora",
        "Impressora Multifuncional" => "impressoraMultifuncional",
        "Scanner" => "scanner",
        "Nenhum dos equipamentos listados" => "nenhumEquipamentosListados",
        "Aparelho de DVD/Blu-ray" => "aparelhoDVDBluray",
        "Aparelho de som" => "aparelhoSom",
        "Aparelho de Televisão" => "aparelhoTelevisao",
        "Lousa digital" => "lousaDigital",
        "Projetor Multimídia (Data show)" => "projetorMultimidia",
        "Computadores de mesa (desktop)" => "computadorDesktop",
        "Computadores portáteis" => "computadorPortateis",
        "Tablets" => "tablets",
        "Para uso administrativo" => "internetParaAdministrativo",
        "Para uso no processo de ensino e aprendizagem" => "internetParaEnsino",
        "Para uso dos aluno(a)s" => "internetParaAluno",
        "Para uso da comunidade" => "internetParaComunidade",
        "Não possui acesso à internet" => "naoPossuiInternet",
        "Computadores de mesa, portáteis e tablets da escola (no laboratório de informática, biblioteca, sala de aula" .
        " etc.)" => "computadoresDisponiveis",
        "Dispositivos pessoais (computadores portáteis, celulares, tablets etc.)" => "dispositivosPessoais",
        "Internet banda larga" => "internetBandaLarga",
        "A cabo" => "redeCabo",
        "Wireless" => "redeWireless",
        "Não há rede local interligando computadores" => "naoExisteRede",
        "Agrônomos(as), horticultores(as), técnicos ou monitores(as) responsáveis pela".
            " gestão da área de horta, plantio e/ou ".
            "produção agrícola" => "agronomosTecnicosResponsaveisProducaoAgricola",
        "Auxiliares de secretaria ou auxiliares administrativos, atendentes" => "auxiliarSecretariaAdministrativos",
        "Auxiliar de serviços gerais, porteiro(a), zelador(a), faxineiro(a), horticultor(a)," .
        " jardineiro(a)" => "auxiliarServicosGerais",
        "Bibliotecário(a), auxiliar de biblioteca ou monitor(a) da sala de leitura" => "bibliotecario",
        "Bombeiro(a) brigadista, profissionais de assistência a saúde (urgência e emergência), enfermeiro(a)," .
        " técnico(a) de enfermagem e socorrista" => "bombeiro",
        "Coordenador(a) de turno/disciplinar" => "coordenador",
        "Fonoaudiólogo(a)" => "fonoaudiologo",
        "Nutricionista" => "nutricionista",
        "Psicólogo(a) escolar" => "psicologo",
        "Profissionais de preparação e segurança alimentar, cozinheiro(a), merendeira e auxiliar de" .
        " cozinha" => "profissionaisPreparacaoSeguraca",
        "Profissionais de apoio e supervisão pedagógica" => "profissionaisApoio",
        "Secretário(a) escolar" => "secretario",
        "Segurança, guarda ou segurança patrimonial" => "seguranca",
        "Técnicos(as), monitores(as) ou auxiliares de laboratório(s)" => "tecnicosMonitores",
        "Vice-diretor(a) ou diretor(a) adjunto(a), profissionais responsáveis pela gestão administrativa" .
        "e/ou financeira" => "gestoresEscola",
        "Orientador(a) comunitário(a) ou assistente social" => "orientadorComunitario",
        "Tradutor e Intérprete de Libras para atendimento em outros ambientes da escola que não seja sala de aula"
            => "tradutorInterpreteLibras",
        "Revisor de texto Braille, assistente vidente (assistente de revisão do texto ".
            "em Braille)" => "revisorTextoBrailleAssistenteVidente",
        "Não há funcionários para as funções listadas" => "naoHaFuncionarios",
        "Alimentação escolar para os aluno(a)s" => "alimentacaoEscolar",
        "Acervo multimídia" => "acervoMultimidia",
        "Brinquedos para educação infantil" => "brinquedosEducacaoInfantil",
        "Conjunto de materiais científicos" => "materiaisCientificos",
        "Equipamento para amplificação e difusão de som/áudio" => "EquipamentoAmplificacaoOuDifusaoAudio",
        "Equipamentos e instrumentos para atividades em área de horta, ".
            "plantio e/ou produção agrícola" => "equipamentosAtividadesHortaProducaoAgricola",
        "Instrumentos musicais para conjunto, banda/fanfarra e/ou aulas de música" => "instrumentosMusicais",
        "Jogos educativos" => "jogosEducativos",
        "Materiais para atividades culturais e artísticas" => "materialAtividadeCultural",
        "Materiais para educação profissional" => "materialEducacaoProfissional",
        "Materiais para prática desportiva e recreação" => "materialDesportivRecreacao",
        "Materiais pedagógicos para a educação bilíngue de surdos" => "materialEducacaoBilingue",
        "Materiais pedagógicos para a educação escolar indígena" => "materialEducacaoIndigena",
        "Materiais pedagógicos para a educação das relações étnicos raciais" => "materialEducacaoEtnicoRacial",
        "Materiais pedagógicos para a educação do campo" => "materialEducacaoCampo",
        "Materiais pedagógicos para a educação escolar quilombola" => "materialEducacaoQuilombola",
        "Materiais pedagógicos para a educação especial" => "materialEducacaoEspecial",
        "Nenhum dos instrumentos listados" => "nenhumInstrumentoListado",
        "Escola indígena" => "educacaoEscolarIndigena",
        "Língua indígena" => "linguaIndigena",
        "Língua portuguesa" => "linguaPortuguesa",
        "Código da língua indígena 1" => "codigoLinguaIndigena1",
        "Código da língua indígena 2" => "codigoLinguaIndigena2",
        "Código da língua indígena 3" => "codigoLinguaIndigena3",
        "A escola faz exame de seleção para ingresso de seus aluno(a)s (avaliação por prova e /ou analise" .
        " curricular)" => "exameSelecao",
        "Autodeclarado preto, pardo ou indígena (PPI)" => "reservaVagaPretoPardoIndigena",
        "Condição de renda" => "reservaVagaRenda",
        "Oriundo de escola pública" => "reservaVagaEscolaPublica",
        "Pessoa com deficiência (PCD)" => "reservaVagaDeficiencia",
        "Outros grupos que não os listados" => "reservaVagaOutro",
        "Sem reservas de vagas para sistema de cotas (ampla concorrência)" => "semReservaVagas",
        "A escola possui site ou blog ou página em redes sociais para comunicação institucional" => "possuiSiteBlog",
        "A escola compartilha espaços para atividades de integração escola-comunidade" =>
            "escolaCompartilhaEspacoComunidade",
        "A escola usa espaços e equipamentos do entorno escolar para atividades regulares com os aluno(a)s" =>
            "escolaUsaEquipamentosParaAtividade",
        "Associação de Pais" => "associacaoPais",
        "Associação de pais e mestres" => "associacaoPaisMestres",
        "Conselho escolar" => "conselhoEscolar",
        "Grêmio estudantil" => "gremioEstudantil",
        "Orgaos Outros" => "orgaosColegiadosOutros",
        "Não há órgãos colegiados em funcionamento" => "orgaosColegiadosNenhum",
        "Projeto político pedagógico ou a proposta pedagógica da escola atualizado nos últimos 12 meses até a data de" .
        " referência" => "projetoPedagogicoAtualizado",
        "A escola desenvolve ações na área de educação ambiental" => "desenvolveAcoesEducacaoAmbiental",
        "Como conteúdo dos componentes/campos de experiências presentes no currículo"
            => "comoConteudoComponentesCamposExperiencias",
        "Como um componente curricular especial, específico, flexível ou eletivo"
            => "comoComponenteCurricularEspecialEspecifico",
        "Como um eixo estruturante do currículo" => "comoEixoEstruturanteCurriculo",
        "Em eventos" => "emEventos",
        "Em projetos transversais ou interdisciplinares" => "emProjetosTransversaisInterdisciplinares",
        "Nenhuma das opções listadas" => "nenhumaDasOpcoesListadas"
    );
}
