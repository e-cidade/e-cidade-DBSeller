import StatusPaciente from "./Ambulatorial/Consultas/StatusPaciente.vue";
import UnidadeEncaminhadora from "./Ambulatorial/Cadastros/UnidadeEncaminhadora.vue";
import UnidadeOrigem from "./Ambulatorial/Cadastros/UnidadeOrigem.vue";
import UnificacaoCgs from "./Ambulatorial/Procedimentos/UnificacaoCgs.vue";
import ConsultaCgsUnificado from "./Ambulatorial/Consultas/ConsultaCgsUnificado.vue";
import GruposLiberacao from "./Laboratorio/Cadastros/GruposLiberacao.vue";

export default function (app) {
    app.component('status_paciente', StatusPaciente);
    app.component('unidade_encaminhadora', UnidadeEncaminhadora);
    app.component('unidade_origem', UnidadeOrigem);
    app.component('unificacao_cgs', UnificacaoCgs);
    app.component('consulta_cgs_unificado', ConsultaCgsUnificado);
    app.component('grupos_liberacao', GruposLiberacao);
}
