import AtendimentoJson from "./Ouvidoria/AtendimentoJson";
import IncluirOrgao from "./PNCP/IncluirOrgao";
import IncluirDocumentoLicitacao from "./PNCP/IncluirDocumentoLicitacao.vue";
import ExclusaoDocumento from "@modules/Patrimonial/PNCP/procedimentos/contrato/ExclusaoDocumento.vue";
import AlterarDotacao from "./Contratos/AlterarDotacao"
import IncluirObra from "./Licitacao/Licitacon/Obras/Incluir.vue";
import ParametrosLicitacon from "./Licitacao/Licitacon/Parametros.vue";
import ExclusaoDocumentoContratacao from "./PNCP/Procedimentos/ContratacaoEditalAviso/ExclusaoDocumentoContratacao.vue";

import ImplantacaoSaldoPlanilha from "./Material/ImplantacaoSaldoPlanilha.vue";
import MensageriaAcordosConfig from "@modules/Patrimonial/Contratos/MensageriaAcordosConfig.vue";

export default function (app) {
    app.component('atendimento_json',AtendimentoJson);
    app.component('incluir_orgao',IncluirOrgao);
    app.component('exclusao-documento',ExclusaoDocumento);
    app.component('alterar_dotacao',AlterarDotacao);
    app.component('parametros_licitacon', ParametrosLicitacon);
    app.component('incluir_obra_licitacon', IncluirObra);
    app.component('implantacao_saldo_planilha', ImplantacaoSaldoPlanilha);
    app.component('incluir-documento-licitacao', IncluirDocumentoLicitacao);
    app.component('exclusao-documento-contratacao', ExclusaoDocumentoContratacao);
    app.component("mensageria-acordos-config", MensageriaAcordosConfig);
}
