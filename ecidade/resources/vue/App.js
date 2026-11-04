import { createApp } from 'vue'

import VueTheMask, { mask } from 'vue-the-mask'
import Toast from "primevue/toast";
import PrimeVue from 'primevue/config'
import ToastService from 'primevue/toastservice'
import ConfirmationService from 'primevue/confirmationservice'

import Tooltip from 'primevue/tooltip'
import BadgeDirective from 'primevue/badgedirective'
import Ripple from 'primevue/ripple'
import StyleClass from 'primevue/styleclass'

// Import modules
import Assinador from './modules/Assinador'
import AssistenciaSocial from './modules/AssistenciaSocial'
import Cidadao from './modules/Cidadao'
import Client from './modules/Client'
import Configuracao from './modules/Configuracao'
import Core from './modules/Core'
import Educacao from './modules/Educacao'
import Financeiro from './modules/Financeiro'
import Gestor from './modules/Gestor'
import Integracoes from './modules/Integracoes'
import Patrimonial from './modules/Patrimonial'
import RecursosHumanos from './modules/RecursosHumanos'
import Saude from './modules/Saude'
import SIM from './modules/SIM'
import Tributario from './modules/Tributario'

// Providers
import './providers/AxiosProvider'
import 'file-saver'

// Uuid
import './providers/UuidProvider'

// Locale
import pt_BR from './lang/pt-BR'

import VueShepherdPlugin from 'vue-shepherd';

const app = createApp()

app.use(PrimeVue, {ripple: true, locale: pt_BR })
app.use(ToastService)
app.use(ConfirmationService)
app.use(VueTheMask)

app.directive('tooltip', Tooltip);
app.directive('badge', BadgeDirective);
app.directive('ripple', Ripple);
app.directive('styleclass', StyleClass);

app.directive('maska', mask)
app.component('toast', Toast)

app.use(VueShepherdPlugin)
// Modules
Assinador(app)
AssistenciaSocial(app)
Cidadao(app)
Client(app)
Configuracao(app)
Core(app)
Educacao(app)
Financeiro(app)
Gestor(app)
Integracoes(app)
Patrimonial(app)
RecursosHumanos(app)
Saude(app)
SIM(app)
Tributario(app)

app.mount('#app')
