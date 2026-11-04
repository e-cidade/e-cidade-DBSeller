import ConfigArquivoXMLCorreios from './Procedimentos/ArquivoXMLCorreios';
import GeradorRelatorios from "./Relatorios/Gerador";
import TelaDinamica from "./Relatorios/Gerador/Components/DialogTelaDinamica";
import Jobs from "@modules/Configuracao/Jobs/Jobs.vue";
import Favoritos from "@modules/Configuracao/favoritos/Favoritos.vue";

export default function (app) {
    app.component('config_arquivo_xml_correios', ConfigArquivoXMLCorreios);
    app.component('gerador_relatorios', GeradorRelatorios);
    app.component('gerador_tela_dinamica', TelaDinamica);
    app.component('jobs', Jobs);
    app.component('favoritos', Favoritos);
}
