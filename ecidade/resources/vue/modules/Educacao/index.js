import HistoricoEscolar from "./Escola/Relatorios/Alunos/HistoricoEscolar";
import FichaIndividualAluno from "./Escola/Relatorios/Alunos/FichaIndividualAluno";
import BoletimDesempenhoPcd from "./Secretaria/Relatorios/Turmas/BoletimDesempenhoPcd";
import FichaMatricula from "./Escola/Relatorios/Alunos/FichaMatricula";
import AlunosEstrangeiros from "./Secretaria/Relatorios/Alunos/AlunosEstrangeiros";
import AtestadoFrequencia from "./Escola/Relatorios/Alunos/AtestadoFrequencia";
import AcompanhamentoAlunosPCD from "./Escola/Procedimentos/DiarioClasse/AcompanhamentoAlunosPCD";
import RecursosUtilizadosAEE from "./Secretaria/Cadastros/Tabelas/RecursosUtilizadosAEE";
import CursosCRUD from "./Secretaria/Cadastros/Cursos/CursosCRUD.vue";
import BasesCurricularesCRUD from "./Escola/Cadastros/BasesCurriculares/BasesCurricularesCRUD.vue";
import UnidadeCurricularCRUD from "./Secretaria/Cadastros/ComposicaoCurricular/UnidadeCurricularCRUD.vue";
import TiposIntrumentosAvaliativosCRUD
    from "./Secretaria/Cadastros/TiposIntrumentosAvaliativos/TiposIntrumentosAvaliativosCRUD.vue";
import RegistroAula from "./Escola/Procedimentos/DiarioClasse/RegistroAula.vue";
import FasesCRUD from "./MatriculaOnline/Cadastros/FasesCRUD.vue";
import CiclosCRUD from "./MatriculaOnline/Cadastros/CiclosCRUD.vue";
import FormularioInscricao from "./MatriculaOnline/Cadastros/FormularioInscricao.vue";
import DuvidasFrequentes from "./MatriculaOnline/Configuracao/DuvidasFrequentes.vue";
import MensagensPersonalizadas from "./MatriculaOnline/Configuracao/MensagensPersonalizadas.vue";
import Documentos from "./MatriculaOnline/Configuracao/Documentos.vue";
import ImagensPersonalizadas from "./MatriculaOnline/Configuracao/ImagensPersonalizadas.vue";
import Noticias from "./MatriculaOnline/Configuracao/Noticias.vue";
import AtualizadorAulasDadas from "./Escola/Procedimentos/DiarioClasse/AtualizadorAulasDadas.vue";
import Atividades from "./Escola/Relatorios/RecursosHumanos/Atividades.vue"
import CoresPersonalizadas from "./MatriculaOnline/Configuracao/CoresPersonalizadas.vue";
import CamposOpcionais from "./MatriculaOnline/Configuracao/CamposOpcionais.vue";
import Parametros from "./MatriculaOnline/Configuracao/Parametros.vue";
import EmissaoCarteiraEstudante from "./TransporteEscolar/Procedimentos/EmissaoCarteiraEstudante.vue";
import ParametrosConfigEnvioNotificacoes from "./MatriculaOnline/Configuracao/ParametrosEnvioNotificacoes.vue";
import PrevisaoVagasParciais from "@modules/Educacao/MatriculaOnline/Relatorios/PrevisaoVagasParciais.vue";
import GeralInscricoes from "@modules/Educacao/MatriculaOnline/Relatorios/GeralInscricoes.vue";
import DemandaReprimida from "@modules/Educacao/MatriculaOnline/Relatorios/DemandaReprimida.vue";
import EmissaoAtas from "./Secretaria/Relatorios/Turmas/EmissaoAtas";

export default function (app) {
    app.component('demanda_reprimida', DemandaReprimida);
    app.component('geral_inscricoes', GeralInscricoes);
    app.component('previsao_vagas_parciais', PrevisaoVagasParciais);
    app.component('emissao_carteira_estudante', EmissaoCarteiraEstudante);
    app.component('historico_escolar', HistoricoEscolar);
    app.component('ficha_individual_aluno', FichaIndividualAluno);
    app.component('boletim_desempenho_pcd', BoletimDesempenhoPcd);
    app.component('emissao_atas', EmissaoAtas);
    app.component('ficha_matricula', FichaMatricula);
    app.component('alunos_estrangeiros', AlunosEstrangeiros);
    app.component('atestado_frequencia', AtestadoFrequencia);
    app.component('acompanhamento_alunos_pcd', AcompanhamentoAlunosPCD);
    app.component('recursos_utilizados_aee', RecursosUtilizadosAEE);
    app.component('cursos_crud', CursosCRUD);
    app.component('bases_curriculares_crud', BasesCurricularesCRUD);
    app.component('unidade_curricular_crud', UnidadeCurricularCRUD)
    app.component('tipos_instrumentos_avaliativos_crud', TiposIntrumentosAvaliativosCRUD);
    app.component('registro_aula', RegistroAula);
    app.component('fases_crud', FasesCRUD);
    app.component('ciclos_crud', CiclosCRUD);
    app.component('formulario_inscricao', FormularioInscricao);
    app.component('duvidas_frequentes', DuvidasFrequentes);
    app.component('mensagens_personalizadas', MensagensPersonalizadas);
    app.component('documentos', Documentos);
    app.component('imagens_personalizadas', ImagensPersonalizadas);
    app.component('noticias', Noticias);
    app.component('atualizador_aulas_dadas', AtualizadorAulasDadas);
    app.component('atividades', Atividades);
    app.component('cores_personalizadas', CoresPersonalizadas);
    app.component('campos_opcionais', CamposOpcionais);
    app.component('parametros', Parametros);
    app.component('parametros_config_envio_notificacoes', ParametrosConfigEnvioNotificacoes);
}
