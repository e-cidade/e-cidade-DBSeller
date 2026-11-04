import ManutencaoParametrosFundeb from './Pessoal/ManutencaoParametrosFundeb'
import ProcessamentoRubricaSalario from './Pessoal/ProcessamentoRubricaSalario'
import ConfigAssentPeriodo from './RH/Relatorios/AssentamentoPorPeriodo/ConfigAssentPeriodo';
import ConfiguracaoAjuda from './Pessoal/AjudaCusto/ConfiguracaoAjuda';
import LancamentoAjuda from './Pessoal/AjudaCusto/LancamentoAjuda';
import RelatorioAjuda from './Pessoal/AjudaCusto/RelatorioAjuda';
import LiberacaoContracheque from './Pessoal/Contracheque/LiberacaoContracheque';
import TipoGuiaPrevidencia from './Pessoal/Relatorios/TipoGuiaPrevidencia';
import EncargosTributariosMensais from './Pessoal/Relatorios/EncargosTributariosMensais/EncargosTributariosMensais.vue';
import PrevidenciaComplementar from './Pessoal/ServidorPrevidenciaComplementar';
import HistoricoRubrica from './Pessoal/Rubricas/HistoricoRubrica';
import CertidaoTempoContribuicao from './RH/Relatorios/CertidaoTempoContribuicao';
import EmissaoContraChequeFalhas from './Pessoal/EmissaoContraChequeApp/Falhas.vue';
import EventosPeriodicos from './ESocial/EventosPeriodicos/EventoS1280';
import ExameToxicologicoMotoristaProfissional from './ESocial/ExameToxicologicoMotoristaProfissional/EventoS2221';

export default function (app) {
    app.component('fundeb', ManutencaoParametrosFundeb);
    app.component('fundeb-processamento', ProcessamentoRubricaSalario);
    app.component('config_assent_periodo', ConfigAssentPeriodo);
    app.component('configuracao_ajuda', ConfiguracaoAjuda);
    app.component('lancamento_ajuda', LancamentoAjuda);
    app.component('relatorio_ajuda', RelatorioAjuda);
    app.component('liberacao_contracheque', LiberacaoContracheque);
    app.component('tipo_guia_previdencia', TipoGuiaPrevidencia);
    app.component('encargos_tributarios_mensais', EncargosTributariosMensais);
    app.component('servidor_previdencia_complementar', PrevidenciaComplementar);
    app.component('historico_rubrica', HistoricoRubrica);
    app.component('certidao_tempo_contribuicao', CertidaoTempoContribuicao);
    app.component('emissao_contracheque_app_falhas',EmissaoContraChequeFalhas);
    app.component('evento_s1280',EventosPeriodicos);
    app.component('evento_s2221',ExameToxicologicoMotoristaProfissional)
}
