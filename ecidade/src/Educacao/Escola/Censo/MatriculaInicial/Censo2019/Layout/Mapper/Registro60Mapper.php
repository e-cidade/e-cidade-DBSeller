<?php
/**
 * Created by PhpStorm.
 * User: andri
 * Date: 03/05/2019
 * Time: 16:40
 */

namespace ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Layout\Mapper;

/**
 * Class Registro60Mapper
 * @package ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Layout\Mapper
 */
class Registro60Mapper extends Mapper
{
    protected $dePara = array(
        "Tipo de registro" => "tipoRegistro",
        "Código de escola - Inep" => "codigoInepEscola",
        "Código da pessoa física no sistema próprio" => "codigoPessoa",
        "Identificação única (Inep)" => "codigoInep",
        "Código da Turma na Entidade/Escola" => "codigoTurma",
        "Código da turma no INEP" => "codigoTurmaInep",
        "Código da Matrícula do(a) aluno(a)" => "codigoMatricula",
        "Turma multi" => "etapaTurmaMultietapa",
        "Linguagens e suas tecnologias" => "liguagensTecnlogias",
        "Matemática e suas tecnologias" => "matematicaTecnlogias",
        "Ciências da natureza e suas tecnologias" => "cienciasNaturezaTecnlogias",
        "Ciências humanas e sociais aplicadas" => "cienciasHumanas",
        "Formação técnica e profissional" => "formacaoTecnicaProfissional",
        "Itinerário formativo integrado" => "itenarioFormativoIntegrado",
        "Composição itinerário formativo integrado - Linguagens e suas tecnologias" => "liguagensTecnlogiasIntegrado",
        "Composição itinerário formativo integrado - Matemática e suas tecnologias" => "matematicaTecnlogiasIntegrado",
        "Composição itinerário formativo integrado - Ciências da natureza e suas tecnologias"
            => "cienciasNaturezaTecnlogiasIntegrado",
        "Composição itinerário formativo integrado - Ciências humanas e sociais aplicadas"
            => "cienciasHumanasIntegrado",
        "Composição itinerário formativo integrado - Formação técnica e profissional"
            => "formacaoTecnicaProfissionalIntegrado",
        "Tipo do curso do itinerário de formação técnica e profissional" => "cursoFormacaoTecnicaProfissionalIntegrado",
        "Código do curso técnico" => "codigoCursoTecnico",
        "Itinerário concomitante intercomplementar à matrícula de formação geral básica"
            => "itenarioConcomitanteIntegrado",
        "Desenvolvimento de funções cognitivas" => "desenvolvimentoFuncoesCognitivas",
        "Desenvolvimento de vida autônoma" => "desenvolvimentoVidaAutonoma",
        "Enriquecimento curricular" => "enriquecimentoCurricular",
        "Ensino da informática acessível" => "EnsinoInformaticaAcessivel",
        "Ensino da Língua Brasileira de Sinais (Libras)" => "ensinoLibras",
        "Ensino da Língua Portuguesa como Segunda Língua" => "ensinoPortuguesaSegundaLingua",
        "Ensino das técnicas do cálculo no Soroban" => "ensinoCalculoSoroban",
        "Ensino de Sistema Braille" => "ensinoSistemaBraille",
        "Ensino de técnicas para orientação e mobilidade" => "ensinoTecnicasOrientacaoMobilidade",
        "Ensino de uso da Comunicação Alternativa e Aumentativa (CAA)" => "ensinoCAA",
        "Ensino de uso de recursos ópticos e não ópticos" => "ensinoRecursosOpticosNaoOpticos",
        "Recebe escolarização em outro espaço (diferente da escola)" => "recebeEscolarizacaoEspecial",
        "Transporte escolar público" => "transporteEscolarPublico",
        "Poder Público responsável pelo transporte escolar" => "responsavelTransporteEscolar",
        "Rodoviário - Bicicleta" => "bicicleta",
        "Rodoviário - Microônibus" => "microonibus",
        "Rodoviário - Ônibus" => "onibus",
        "Rodoviário - Tração Animal" => "tracaoAnimal",
        "Rodoviário - Vans/Kombis" => "vansKombis",
        "Rodoviário - Outro" => "outro",
        "Aquaviário - Capacidade de até 5 aluno(a)s" => "aquaviárioAte5",
        "Aquaviário - Capacidade entre 5 a 15 aluno(a)s" => "aquaviárioEntre5A15",
        "Aquaviário - Capacidade entre 15 a 35 aluno(a)s" => "aquaviárioEntre15A35",
        "Aquaviário - Capacidade acima de 35 aluno(a)s" => "aquaviárioAcima35"
    );
}
