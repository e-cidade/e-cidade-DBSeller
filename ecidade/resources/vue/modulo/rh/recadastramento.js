import { createApp } from 'vue'
import FormularioAtendimentoJson from '../components/FormularioAtendimentoJson';
import VueTheMask, { mask } from 'vue-the-mask'

const recadastramento = createApp();

recadastramento.use(VueTheMask);
recadastramento.directive('maska', mask);
recadastramento.component('formulario-atendimento-json', FormularioAtendimentoJson);
recadastramento.mount('#app-recadastramento');
