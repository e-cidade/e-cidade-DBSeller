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

/**
 * Class Registro20Mapper
 * @package ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Layout\Mapper
 */
class Registro20Mapper extends Mapper
{
    protected $dePara = array(
        "Tipo de registro" => "tipoRegistro",
        "Código de escola - Inep" => "codigoInepEscola",
        "Código da Turma na Entidade/Escola" => "codigoTurma",
        "Código da Turma - Inep" => "codigoInep",
        "Nome da Turma" => "nomeTurma",
        "Tipo de mediação didático-pedagógica" => "tipoMediacaoDidaticoPedagogica",
        "Hora Inicial - Hora" => "horaInicio",
        "Hora Inicial - Minuto" => "minutoInicio",
        "Hora Final - Hora" => "horaFim",
        "Hora Final - Minuto" => "minutoFim",
        "Domingo" => "domingo",
        "Segunda-feira" => "segundaFeira",
        "Terça-feira" => "tercaFeira",
        "Quarta-feira" => "quartaFeira",
        "Quinta-feira" => "quintaFeira",
        "Sexta-feira" => "sextaFeira",
        "Sábado" => "sabado",
        "Escolarização" => "escolarizacao",
        "Atividade complementar" => "atividadeComplementar",
        "Atendimento educacional especializado - AEE" => "atendimentoAEE",
        "Formação geral básica" => "formacaoGeralBasica",
        "Itinerário formativo" => "itinerarioFormativo",
        "Não se aplica" => "naoAplica",
        "Código 1" => "codigo1",
        "Código 2" => "codigo2",
        "Código 3" => "codigo3",
        "Código 4" => "codigo4",
        "Código 5" => "codigo5",
        "Código 6" => "codigo6",
        "Local de funcionamento diferenciado" => "localFuncionamentoDiferenciado",
        "Modalidade" => "modalidade",
        "Etapa" => "etapaCenso",
        "Código Curso" => "codigoCurso",
        "Série/ano (séries anuais)" => "serieAno",
        "Períodos semestrais" => "periodosSemestrais",
        "Ciclo(s)" => "ciclos",
        "Grupos não seriados com base na idade ou competência" => "gruposNaoSeriados",
        "Módulos" => "modulos",
        "Alternância regular de períodos de estudos
        (proposta pedagógica de formação por alternância: tempo-escola e tempo-comunidade)"
            => "alternanciaRegular",
        "Eletivas Un" => "eletivas",
        "Libras Un" => "librasUnidadeCurricular",
        "Língua indígena Un" => "linguaIndigenaUnidade",
        "Língua/Literatura estrangeira - Espanhol Un" => "linguaEspanhol",
        "Língua/Literatura estrangeira - Francês Un" => "linguaFrances",
        "Língua/Literatura estrangeira - outra Un" => "linguaOutra",
        "Projeto de vida Un" => "projetoVidaUnidade",
        "Trilhas de aprofundamento/aprendizagens" => "trilhaAprofundamento",
        "Outra(s) unidade(s) curricular(es) obrigatória(s)" => "outrasUnidades",
        "1. Química" => "quimica",
        "2. Física" => "fisica",
        "3. Matemática" => "matematica",
        "4. Biologia" => "biologia",
        "5. Ciências" => "ciencias",
        "6. Língua/Literatura Portuguesa" => "literaturaPortuguesa",
        "7. Língua/Literatura Estrangeira - Inglês" => "literaturaEstrangeiraIngles",
        "8. Língua/Literatura Estrangeira - Espanhol" => "literaturaEstrangeiraEspanhol",
        "9. Língua/Literatura Estrangeira - outra" => "literaturaEstrangeiraOutra",
        "10. Arte (Educação Artística, Teatro, Dança, Música, Artes Plásticas e outras)" => "artes",
        "11. Educação Física" => "educacaoFisica",
        "12. História" => "historia",
        "13. Geografia" => "geografia",
        "14. Filosofia" => "filosofia",
        "16. Informática/ Computação" => "informatica",
        "17. Disciplinas dos Cursos Técnicos Profissionais" => "cursosTecnicosProfissionais",
        "23. Libras" => "libras",
        "25. Disciplinas Pedagógicas" => "disciplinasPedagogicas",
        "26. Ensino Religioso" => "ensinoReligioso",
        "27. Língua Indígena" => "linguaIndigena",
        "28. Estudos Sociais" => "estudosSociais",
        "29. Sociologia" => "sociologia",
        "30. Língua/Literatura Estrangeira - Francês" => "literaturaEstrangeiraFrances",
        "31. Língua Portuguesa como Segunda Língua" => "portuguesComoSegundaLingua",
        "32. Estágio Curricular Supervisionado" => "estagioSupervisionado",
        "33. Projeto de vida" => "projetoVida",
        "99. Outras disciplinas" => "outrasDisciplinas",
        "Classe bilíngue de surdos tendo a Libras (Língua Brasileira de Sinais) como língua de instrução, ensino, 
            comunicação e interação e a língua portuguesa escrita como segunda 
            língua" => "librasPrimeiraLingua"
    );
}
