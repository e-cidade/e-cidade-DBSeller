import ManutencaoR4010 from './EFDReinf/Retencao/ManutencaoR4010'
import ManutencaoR4020 from './EFDReinf/Retencao/ManutencaoR4020'
import ManutencaoR4040 from './EFDReinf/Retencao/ManutencaoR4040'
import DadosRespR4099  from './EFDReinf/Forms/DadosRespR4099';

export default function (app) {
    app.component('manutencao_r4010', ManutencaoR4010);
    app.component('manutencao_r4020', ManutencaoR4020);
    app.component('manutencao_r4040', ManutencaoR4040);
    app.component('dadosresp_r4099', DadosRespR4099);
}
