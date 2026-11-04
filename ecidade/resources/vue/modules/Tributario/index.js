import RequisicaoAPIPIXGeral from './Arrecadacao/RequisicaoAPIPIXGeral'
import GeracaoPDFEcartaGeral from './Cadastro/GeracaoPDFEcartaGeral'
import GeracaoPDFEcartaIndividual from './Cadastro/GeracaoPDFEcartaIndividual'
import CalculoGeralIPTU from './Cadastro/CalculoGeralIPTU'
import DecadDesconto from './Cadastro/DecadDesconto'
import DecadConsultaCgm from './Cadastro/DecadConsultaCgm'
import DecadConsultaEspelho from './Cadastro/DecadConsultaEspelho'
import ArquivoXMLCorreios from './Cadastro/ArquivoXMLCorreios'
import DataDeLancamento from './Arrecadacao/DataDeLancamento'
import NovosEstabelecimentos from "./Issqn/NovosEstabelecimentos"
import AtualizacaoDeCadastros from "./Issqn/AtualizacaoDeCadastros"
import ParametrosCemiterio from "./Cemiterio/ParametrosCemiterio"
import CancelaPacelamentoLista from "./Arrecadacao/CancelaPacelamentoLista"
import GrupoDeTaxas from "./Arrecadacao/GrupoDeTaxas"
import ConfigOrdenacaoCfg from "./Arrecadacao/ConfigOrdenacaoCfg"
import DemostrativoCalculo from './Fiscalizacao/Vistoria/DemostrativoCalculo.vue'
import LancamentoHistorico from './DividaAtiva/LancamentoHistorico'
import CadastroMacroZonas from './Cadastro/CadastroMacroZonas.vue'
import CadastroZonas from './Cadastro/CadastroZonas.vue'
import AlteracaoZonas from './Cadastro/AlteracaoZonas.vue'
import AlteracaoMacrozonas from './Cadastro/AlteracaoMacrozonas.vue'
import InclusaoSalaoParceiro from './Issqn/InclusaoSalaoParceiro'
import AlterarSalaoParceiro from './Issqn/AlterarSalaoParceiro.vue'
import InclusaoDeducaoSalao from './Issqn/InclusaoDeducaoSalao.vue'

export default function (app) {
    app.component('requisicao_api_pix_geral', RequisicaoAPIPIXGeral)
    app.component('geracao_pdf_ecarta_geral', GeracaoPDFEcartaGeral)
    app.component('geracao_pdf_ecarta_individual', GeracaoPDFEcartaIndividual)
    app.component('calculo_geral_iptu', CalculoGeralIPTU)
    app.component('decad_desconto', DecadDesconto)
    app.component('decad_consulta_cgm', DecadConsultaCgm)
    app.component('decad_consulta_espelho', DecadConsultaEspelho)
    app.component('arquivo_xml_correios', ArquivoXMLCorreios)
    app.component('data_de_lancamento', DataDeLancamento)
    app.component('novos_estabelecimentos', NovosEstabelecimentos)
    app.component("atualizacao_de_cadastros_simples_nacional", AtualizacaoDeCadastros);
    app.component("parametros_cemiterio", ParametrosCemiterio);
    app.component('cancelparcelista', CancelaPacelamentoLista);
    app.component("grupo_de_taxas", GrupoDeTaxas);
    app.component("config_ordenacao_cgf", ConfigOrdenacaoCfg);
    app.component('demostrativo_calculo', DemostrativoCalculo);
    app.component("lancamento_historico", LancamentoHistorico);
    app.component("cadastro_macrozonas", CadastroMacroZonas);
    app.component("cadastro_zonas", CadastroZonas);
    app.component("alteracao_zonas", AlteracaoZonas);
    app.component("alteracao_macrozonas", AlteracaoMacrozonas);
    app.component("inclusao_salao_parceiro", InclusaoSalaoParceiro);
    app.component("alterar_salao_parceiro", AlterarSalaoParceiro);
    app.component("inclusao_deducao_salao", InclusaoDeducaoSalao);
}
